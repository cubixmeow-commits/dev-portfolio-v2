package com.cadence.engine;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.ZoneOffset;
import java.util.ArrayList;
import java.util.List;

/**
 * Returns the platform to a known-good demo state in one command:
 * deletes demo data (and only demo data) in FK-safe order, reseeds at
 * the standard demo scale, then rebuilds the curated demo member with
 * a hand-tuned history and makes sure the admin account exists.
 *
 * Non-demo accounts survive with their identity intact; their
 * challenge progress necessarily resets because all challenges are
 * demo content (the app has no user-created challenges).
 */
public final class ResetEngine {

    public static final int DEMO_USERS = 500;
    public static final int DEMO_CHALLENGES = 12;
    public static final int DEMO_HISTORY_DAYS = 120;

    /** bcrypt of "cadence-admin". RUNBOOK says: change it after first login. */
    private static final String ADMIN_PASSWORD_HASH = "$2y$10$YnXTiHClDTsFphEevMTKJ.PKlIAJoqbuT86RFf.eAVzrYb3LPOlQa";

    private final Connection conn;
    private final long rngSeed;

    public ResetEngine(Connection conn, long rngSeed) {
        this.conn = conn;
        this.rngSeed = rngSeed;
    }

    public String run() throws SQLException, IOException {
        conn.setAutoCommit(false);

        System.out.println("Clearing demo data...");
        int removedUsers = clearDemoData();
        conn.commit();
        System.out.println("Removed " + removedUsers + " demo users and all challenge content.");

        String seedSummary = new SeedEngine(conn, rngSeed).run(DEMO_USERS, DEMO_CHALLENGES, DEMO_HISTORY_DAYS);

        System.out.println("Building the curated demo member...");
        buildDemoMember();
        ensureAdmin();
        conn.commit();

        String summary = "Reset complete. " + seedSummary
            + " Demo member: demo@cadence.demo / cadence-demo. Admin: admin@cadence.demo.";
        System.out.println(summary);
        return summary;
    }

    /**
     * FK-safe demo wipe. Deleting users cascades to sessions,
     * participations, check-ins, events, user_badges, notifications,
     * and token tables. Challenges are all seed content, so they are
     * cleared outright, which cascades any residue owned by non-demo
     * users; their aggregate totals are then zeroed to stay consistent.
     */
    private int clearDemoData() throws SQLException {
        int users;
        try (Statement st = conn.createStatement()) {
            users = st.executeUpdate("DELETE FROM users WHERE is_demo = 1");
            st.executeUpdate("DELETE FROM challenges");
            // Whatever events and badges non-demo users earned pointed at
            // demo challenges or platform badges from demo activity.
            st.executeUpdate("DELETE FROM activity_events");
            st.executeUpdate("DELETE FROM user_badges");
            st.executeUpdate("DELETE FROM check_ins");
            st.executeUpdate("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE is_demo = 0)");
            st.executeUpdate("UPDATE users SET total_points = 0 WHERE is_demo = 0");
            st.executeUpdate("DELETE FROM rate_limits");
        }
        return users;
    }

    /**
     * The curated account a hiring manager lands in from the one-click
     * demo button. Hand-tuned history: one long committed streak, one
     * consistent challenge, one struggling, one finished (ended)
     * challenge, so every surface of the product has something to show.
     */
    private void buildDemoMember() throws SQLException {
        // Recreate from scratch; the wipe removed it (is_demo = 1).
        LocalDateTime createdAt = LocalDateTime.now(ZoneOffset.UTC).minusDays(95);
        long userId;
        try (PreparedStatement ps = conn.prepareStatement(
                "INSERT INTO users (email, password_hash, display_name, handle, avatar_seed, bio, timezone, "
                + "role, email_verified_at, is_demo, created_at, updated_at) "
                + "VALUES (?, ?, ?, ?, ?, ?, ?, 'member', ?, 1, ?, ?)",
                Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, "demo@cadence.demo");
            ps.setString(2, SeedEngine.DEMO_PASSWORD_HASH);
            ps.setString(3, "Sam Harper");
            ps.setString(4, "sam");
            ps.setString(5, "demo-member-seed-0001");
            ps.setString(6, "Exploring what sticks. Currently: running, reading, and one very stubborn meditation habit.");
            ps.setString(7, "America/Los_Angeles");
            ps.setTimestamp(8, Timestamp.valueOf(createdAt.plusHours(2)));
            ps.setTimestamp(9, Timestamp.valueOf(createdAt));
            ps.setTimestamp(10, Timestamp.valueOf(createdAt));
            ps.executeUpdate();
            try (ResultSet keys = ps.getGeneratedKeys()) {
                keys.next();
                userId = keys.getLong(1);
            }
        }

        // Four live challenges with distinct textures plus one ended.
        List<long[]> targets = new ArrayList<>();
        try (Statement st = conn.createStatement();
             ResultSet rs = st.executeQuery(
                 "SELECT id, start_date, end_date, points_per_checkin, "
                 + "(end_date < CURDATE()) AS ended "
                 + "FROM challenges "
                 + "ORDER BY ended DESC, participant_count DESC")) {
            int live = 0;
            boolean tookEnded = false;
            while (rs.next() && targets.size() < 5) {
                boolean ended = rs.getInt("ended") == 1;
                if (ended && tookEnded) {
                    continue;
                }
                if (!ended && live >= 4) {
                    continue;
                }
                targets.add(new long[] {
                    rs.getLong("id"),
                    LocalDate.parse(rs.getString("start_date")).toEpochDay(),
                    LocalDate.parse(rs.getString("end_date")).toEpochDay(),
                    rs.getInt("points_per_checkin"),
                    ended ? 1 : 0,
                });
                if (ended) {
                    tookEnded = true;
                } else {
                    live++;
                }
            }
        }

        // Deterministic personal RNG so Sam's story is stable.
        java.util.Random rng = new java.util.Random(7);
        Batcher batch = new Batcher(conn);
        java.util.Map<String, Long> badgeIds = new java.util.HashMap<>();
        try (Statement st = conn.createStatement(); ResultSet rs = st.executeQuery("SELECT id, code FROM badges")) {
            while (rs.next()) {
                badgeIds.put(rs.getString(2), rs.getLong(1));
            }
        }

        LocalDate today = LocalDate.now(ZoneOffset.UTC).minusDays(0);
        int totalPoints = 0;
        boolean firstStep = false;
        int liveIndex = 0;
        LocalDateTime lastActive = createdAt;

        for (long[] target : targets) {
            long challengeId = target[0];
            LocalDate start = LocalDate.ofEpochDay(target[1]);
            LocalDate end = LocalDate.ofEpochDay(target[2]);
            int pointsPer = (int) target[3];
            boolean ended = target[4] == 1;

            LocalDate from = start.isAfter(createdAt.toLocalDate()) ? start : createdAt.toLocalDate();
            LocalDate to = ended ? end : today.minusDays(liveIndex == 3 ? 0 : 1);
            if (!ended && to.isAfter(today)) {
                to = today;
            }

            // Texture per slot: 0 committed (long streak, checked in
            // today), 1 consistent, 2 struggling, 3 fresh join.
            double rate;
            LocalDate effectiveFrom = from;
            switch (ended ? 9 : liveIndex) {
                case 0 -> rate = 1.0;
                case 1 -> rate = 0.82;
                case 2 -> rate = 0.55;
                case 3 -> {
                    rate = 1.0;
                    effectiveFrom = today.minusDays(2);
                    if (effectiveFrom.isBefore(from)) {
                        effectiveFrom = from;
                    }
                }
                default -> rate = 0.85; // the ended, finished challenge
            }

            LocalDateTime joinedAt = effectiveFrom.atTime(8, 30 + rng.nextInt(20), rng.nextInt(60));
            long participantId = batch.insertParticipant(challengeId, userId, joinedAt);
            batch.insertEvent(userId, "joined", challengeId, null, null, joinedAt);

            int streak = 0;
            int longest = 0;
            int points = 0;
            LocalDate prev = null;
            LocalDate lastDate = null;

            for (LocalDate d = effectiveFrom; !d.isAfter(to); d = d.plusDays(1)) {
                boolean doIt = liveIndex == 0 && !ended ? true : rng.nextDouble() < rate;
                // The committed slot never misses; guarantee today's
                // check-in is present so the demo ring starts partial.
                if (!doIt) {
                    continue;
                }
                streak = (prev != null && d.equals(prev.plusDays(1))) ? streak + 1 : 1;
                longest = Math.max(longest, streak);
                boolean milestone = streak == 7 || streak == 30 || streak == 100;
                int awarded = pointsPer + (milestone ? 5 : 0);
                points += awarded;
                totalPoints += awarded;

                LocalDateTime moment = d.atTime(7 + rng.nextInt(2), rng.nextInt(60), rng.nextInt(60));
                batch.insertCheckin(participantId, userId, challengeId, d, awarded, moment,
                    rng.nextInt(100) < 12 ? "Good one today." : null);
                batch.insertEvent(userId, "checkin", challengeId, null, "{\"streak\":" + streak + "}", moment);

                if (!firstStep) {
                    firstStep = true;
                    batch.insertBadge(userId, badgeIds.get("first_step"), null, moment);
                    batch.insertEvent(userId, "badge", null, badgeIds.get("first_step"), null, moment);
                }
                if (milestone) {
                    String code = streak == 7 ? "week_one" : streak == 30 ? "iron_month" : "century";
                    batch.insertBadge(userId, badgeIds.get(code), challengeId, moment);
                    batch.insertEvent(userId, "streak_milestone", challengeId, null, "{\"streak\":" + streak + "}", moment);
                    batch.insertEvent(userId, "badge", challengeId, badgeIds.get(code), null, moment);
                    batch.insertNotification(userId, "streak_milestone", streak + " day streak",
                        "Milestone bonus: +5 points.", moment, moment.plusHours(1));
                }
                if (moment.isAfter(lastActive)) {
                    lastActive = moment;
                }
                prev = d;
                lastDate = d;
            }

            batch.updateParticipant(participantId, streak, longest, points, lastDate);

            if (ended) {
                LocalDateTime at = end.plusDays(1).atTime(9, 0);
                batch.insertBadge(userId, badgeIds.get("finisher"), challengeId, at);
                batch.insertEvent(userId, "badge", challengeId, badgeIds.get("finisher"), null, at);
                batch.insertEvent(userId, "challenge_completed", challengeId, null, null, at);
                batch.insertNotification(userId, "challenge_ended", "A challenge you joined has ended",
                    "Check the final leaderboard.", at, null);
            } else {
                liveIndex++;
            }
        }

        if (targets.size() >= 5) {
            LocalDateTime at = LocalDateTime.now(ZoneOffset.UTC).minusDays(1);
            batch.insertBadge(userId, badgeIds.get("collector"), null, at);
            batch.insertEvent(userId, "badge", null, badgeIds.get("collector"), null, at);
        }
        batch.flush();

        try (PreparedStatement ps = conn.prepareStatement(
                "UPDATE users SET total_points = ?, last_active_at = ? WHERE id = ?")) {
            ps.setInt(1, totalPoints);
            ps.setTimestamp(2, Timestamp.valueOf(lastActive));
            ps.setLong(3, userId);
            ps.executeUpdate();
        }
        try (PreparedStatement ps = conn.prepareStatement(
                "UPDATE challenges SET participant_count = participant_count + 1 WHERE id IN "
                + "(SELECT challenge_id FROM challenge_participants WHERE user_id = ?)")) {
            ps.setLong(1, userId);
            ps.executeUpdate();
        }
        System.out.println("Demo member ready: sam (demo@cadence.demo), " + totalPoints + " points.");
    }

    /** Admin survives resets (is_demo = 0); created only when missing. */
    private void ensureAdmin() throws SQLException {
        try (PreparedStatement check = conn.prepareStatement("SELECT id FROM users WHERE email = ?")) {
            check.setString(1, "admin@cadence.demo");
            try (ResultSet rs = check.executeQuery()) {
                if (rs.next()) {
                    System.out.println("Admin account already present; leaving it untouched.");
                    return;
                }
            }
        }
        try (PreparedStatement ps = conn.prepareStatement(
                "INSERT INTO users (email, password_hash, display_name, handle, avatar_seed, timezone, "
                + "role, email_verified_at, is_demo) "
                + "VALUES (?, ?, 'Cadence Ops', 'ops', 'admin-seed-0001', 'America/Los_Angeles', 'admin', NOW(), 0)")) {
            ps.setString(1, "admin@cadence.demo");
            ps.setString(2, ADMIN_PASSWORD_HASH);
            ps.executeUpdate();
        }
        System.out.println("Admin created: admin@cadence.demo / cadence-admin (change it after first login).");
    }
}
