package com.cadence.engine;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.charset.StandardCharsets;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.DayOfWeek;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.LocalTime;
import java.time.ZoneId;
import java.time.ZoneOffset;
import java.time.ZonedDateTime;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Random;
import java.util.Set;

/**
 * Demo data generator. Produces a population that reads as a living
 * product: growth-curved signups, power-law challenge membership,
 * archetype-driven check-in histories, and fully derived streaks,
 * points, badges, events, notifications, and denormalized counters.
 *
 * The streak and points rules mirror app/Models/CheckIn.php exactly
 * (scripts/streak-tests.php is the executable spec for both).
 *
 * Deterministic: a fixed RNG seed means the same parameters produce
 * the same population on every run (dates shift with the clock).
 */
public final class SeedEngine {

    /** Matches CheckIn::MILESTONES and MILESTONE_BONUS in PHP. */
    private static final int[] MILESTONES = {7, 30, 100};
    private static final int MILESTONE_BONUS = 5;

    /** bcrypt of "cadence-demo": every seeded member shares it. */
    static final String DEMO_PASSWORD_HASH = "$2y$10$P1MDcOKW.0cyhskanRTsR.fLk0KX9L24S.0l8fplzDUtzZyz4RtAG";

    private static final String[] TIMEZONES = {
        "America/Los_Angeles", "America/Denver", "America/Chicago", "America/New_York",
        "America/Sao_Paulo", "Europe/London", "Europe/Berlin", "Europe/Madrid",
        "Africa/Lagos", "Asia/Kolkata", "Asia/Tokyo", "Australia/Sydney",
    };

    private static final String[] COVER_STYLES = {
        "spruce", "dawn", "dusk", "ember", "field", "tide", "bloom", "moss",
    };

    private static final String[] BIOS = {
        "One day at a time.", "Building better mornings.", "Slow and steady, on purpose.",
        "Here for the streaks.", "Small habits, big years.", "Show up daily. That is the whole plan.",
        "Recovering night owl.", "Consistency over intensity.", "Trying to out-walk my desk job.",
        "Coffee, checklists, check-ins.", "Learning to keep promises to myself.", "Progress, not perfection.",
        "Marathon in training, patience in progress.", "Quiet mind, full ring.", "Fresh start, same me.",
        "Doing the boring work daily.", "Habit nerd. Spreadsheet enjoyer.", "Every ring closed is a good day.",
    };

    /** title | category | description */
    private static final String[][] CHALLENGE_TEMPLATES = {
        {"Morning Run Club", "fitness", "Lace up and get moving before 9am. Any distance counts; consistency is the whole game."},
        {"10k Steps a Day", "fitness", "Ten thousand steps, every day. Walk the dog, take the stairs, pace on calls. It all adds up."},
        {"Daily Mobility Reset", "fitness", "Fifteen minutes of stretching or mobility work. Your future back says thank you."},
        {"Strength Before Breakfast", "fitness", "A short strength session before your first meal. Bodyweight counts."},
        {"Ten Minutes of Stillness", "mindfulness", "Ten quiet minutes a day. Sit, breathe, notice. No apps required."},
        {"Evening Wind-Down", "mindfulness", "Screens off 30 minutes before bed. Read, stretch, or just be bored on purpose."},
        {"Gratitude Threes", "mindfulness", "Write down three things that did not suck today. Rereading them later is the secret level."},
        {"One Mindful Meal", "mindfulness", "Eat one meal a day with no screens and no rush. Taste the thing."},
        {"Cook Dinner at Home", "nutrition", "One home-cooked dinner a day. Cereal does not count, but breakfast-for-dinner does."},
        {"Two Liters of Water", "nutrition", "Drink two liters of water a day. Your one job: keep the bottle nearby."},
        {"Five a Day, Actually", "nutrition", "Five servings of fruit or vegetables, actually eaten, actually daily."},
        {"Sugar-Light September", "nutrition", "Skip the added-sugar snacks. Fruit is fair game; pastries are a weekend guest."},
        {"Read Twenty Pages", "learning", "Twenty pages of any book, every day. That is roughly 25 books a year."},
        {"Daily Language Reps", "learning", "Fifteen minutes of language practice. Streaks are the only fluency hack that works."},
        {"Learn One Thing Out Loud", "learning", "Write or say one thing you learned today in your own words. Teaching is remembering."},
        {"Code Kata Daily", "learning", "One small programming exercise a day. Reps make the hard parts boring, in a good way."},
        {"Sketch Something Daily", "creativity", "One sketch a day, five minutes minimum. Quantity breeds quality."},
        {"100 Words a Day", "creativity", "Write one hundred words. Fiction, journal, nonsense. The habit is the point."},
        {"One Photo Walk", "creativity", "Take one deliberate photograph a day. Look up more than you look down."},
        {"Make Before You Scroll", "creativity", "Create something small before your first scroll of the day. Doodle, riff, hum, build."},
        {"Inbox Zero-ish", "lifestyle", "Ten minutes of inbox and desk triage a day. Zero-ish is close enough."},
        {"Lights Out by Eleven", "lifestyle", "In bed, lights out, by 11pm. Sleep is the habit all the others stand on."},
        {"Daily Tidy Ten", "lifestyle", "Ten minutes of tidying one surface, shelf, or drawer. Entropy never rests; neither do we."},
        {"No-Spend Weekdays", "lifestyle", "Weekdays without impulse purchases. Groceries and bills are fine; the third hobby haul is not."},
    };

    private final Connection conn;
    private final Random rng;
    private final LocalDate today;

    /** Per-user running state, keyed by user id. */
    private final Map<Long, UserState> userStates = new HashMap<>();

    private static final class UserState {
        String timezone;
        LocalDateTime createdAt;
        int totalPoints = 0;
        int totalCheckins = 0;
        int joinCount = 0;
        LocalDateTime lastActive;
        boolean hasFirstStep = false;
        boolean hasCollector = false;
        boolean hasPointMachine = false;
        boolean hasFinisher = false;
    }

    public SeedEngine(Connection conn, long rngSeed) {
        this.conn = conn;
        this.rng = new Random(rngSeed);
        this.today = LocalDate.now(ZoneOffset.UTC);
    }

    public String run(int userCount, int challengeCount, int historyDays) throws SQLException, IOException {
        long startedAt = System.currentTimeMillis();
        conn.setAutoCommit(false);

        Map<String, Long> badgeIds = loadBadgeIds();
        List<long[]> challengeWindows = seedChallenges(challengeCount, historyDays);
        List<Long> userIds = seedUsers(userCount, historyDays);
        int checkins = seedParticipationAndCheckins(userIds, challengeWindows, badgeIds);
        finalizeUsers(userIds);
        conn.commit();

        long seconds = (System.currentTimeMillis() - startedAt) / 1000;
        String summary = String.format(
            "Seeded %d users, %d challenges, %d days of history: %,d check-ins written in %ds.",
            userCount, challengeCount, historyDays, checkins, seconds);
        System.out.println(summary);
        return summary;
    }

    /* ---------------- challenges ---------------- */

    /** Returns [challengeId, startEpochDay, endEpochDay, pointsPerCheckin, featured] per challenge. */
    private List<long[]> seedChallenges(int count, int historyDays) throws SQLException {
        System.out.println("Seeding challenges: 0/" + count);
        List<long[]> out = new ArrayList<>();
        Set<String> usedSlugs = new HashSet<>(existingSlugs());

        int ended = Math.max(count >= 4 ? 1 : 0, (int) Math.round(count * 0.25));
        int upcoming = Math.max(count >= 4 ? 1 : 0, (int) Math.round(count * 0.15));
        int active = Math.max(1, count - ended - upcoming);

        List<String[]> templates = new ArrayList<>(List.of(CHALLENGE_TEMPLATES));
        java.util.Collections.shuffle(templates, rng);

        String sql = "INSERT INTO challenges (title, slug, description, category, points_per_checkin, "
            + "start_date, end_date, cover_style, is_featured, participant_count, created_at, updated_at) "
            + "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
        try (PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            for (int i = 0; i < count; i++) {
                String[] tpl = templates.get(i % templates.size());
                String title = tpl[0];
                String slug = slugify(title);
                while (usedSlugs.contains(slug)) {
                    slug = slugify(title) + "-" + (rng.nextInt(90) + 10);
                }
                usedSlugs.add(slug);

                LocalDate start;
                LocalDate end;
                if (i < ended) {
                    // Ended: ran inside the history window.
                    int duration = 21 + rng.nextInt(30);
                    int endedAgo = 5 + rng.nextInt(Math.max(5, historyDays / 4));
                    end = today.minusDays(endedAgo);
                    start = end.minusDays(duration - 1);
                } else if (i < ended + active) {
                    // Active: started inside the window, ends ahead.
                    int startedAgo = 7 + rng.nextInt(Math.max(7, historyDays - 14));
                    start = today.minusDays(startedAgo);
                    end = today.plusDays(14 + rng.nextInt(76));
                } else {
                    // Upcoming.
                    start = today.plusDays(2 + rng.nextInt(12));
                    end = start.plusDays(29 + rng.nextInt(62));
                }

                boolean featured = i >= ended && i < ended + Math.min(2, active);
                int pts = rng.nextInt(10) < 8 ? 10 : 15;
                LocalDateTime createdAt = start.minusDays(3 + rng.nextInt(10)).atTime(randomTime());

                ps.setString(1, title);
                ps.setString(2, slug);
                ps.setString(3, tpl[2]);
                ps.setString(4, tpl[1]);
                ps.setInt(5, pts);
                ps.setString(6, start.toString());
                ps.setString(7, end.toString());
                ps.setString(8, COVER_STYLES[rng.nextInt(COVER_STYLES.length)]);
                ps.setInt(9, featured ? 1 : 0);
                ps.setTimestamp(10, Timestamp.valueOf(createdAt));
                ps.setTimestamp(11, Timestamp.valueOf(createdAt));
                ps.executeUpdate();
                try (ResultSet keys = ps.getGeneratedKeys()) {
                    keys.next();
                    out.add(new long[] {keys.getLong(1), start.toEpochDay(), end.toEpochDay(), pts, featured ? 1 : 0});
                }
            }
        }
        System.out.println("Seeding challenges: " + count + "/" + count);
        return out;
    }

    private Set<String> existingSlugs() throws SQLException {
        Set<String> slugs = new HashSet<>();
        try (Statement st = conn.createStatement(); ResultSet rs = st.executeQuery("SELECT slug FROM challenges")) {
            while (rs.next()) {
                slugs.add(rs.getString(1));
            }
        }
        return slugs;
    }

    /* ---------------- users ---------------- */

    private List<Long> seedUsers(int count, int historyDays) throws SQLException, IOException {
        List<String> firstNames = resourceLines("/names_first.txt");
        List<String> lastNames = resourceLines("/names_last.txt");
        Set<String> usedHandles = new HashSet<>(existingHandles());
        List<Long> ids = new ArrayList<>(count);

        String sql = "INSERT INTO users (email, password_hash, display_name, handle, avatar_seed, bio, timezone, "
            + "total_points, role, email_verified_at, is_demo, last_active_at, created_at, updated_at) "
            + "VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'member', ?, 1, NULL, ?, ?)";
        try (PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            for (int i = 0; i < count; i++) {
                String first = firstNames.get(rng.nextInt(firstNames.size()));
                String last = lastNames.get(rng.nextInt(lastNames.size()));
                String display = first + " " + last;

                String base = slugify(first + "_" + last).replace('-', '_');
                String handle = base;
                while (usedHandles.contains(handle) || handle.length() > 30) {
                    handle = (base.length() > 26 ? base.substring(0, 26) : base) + (rng.nextInt(900) + 100);
                }
                usedHandles.add(handle);

                // Growth curve: power weighting pulls signups toward the
                // recent end of the window without starving histories.
                double r = rng.nextDouble();
                int daysAgo = (int) Math.floor(Math.pow(r, 1.5) * historyDays);
                LocalDateTime createdAt = today.minusDays(daysAgo).atTime(randomTime());

                String timezone = TIMEZONES[weightedTimezoneIndex()];
                String bio = rng.nextInt(100) < 40 ? BIOS[rng.nextInt(BIOS.length)] : null;

                ps.setString(1, handle + "@demo.cadence.invalid");
                ps.setString(2, DEMO_PASSWORD_HASH);
                ps.setString(3, display);
                ps.setString(4, handle);
                ps.setString(5, randomHex(24));
                ps.setString(6, bio);
                ps.setString(7, timezone);
                if (rng.nextInt(100) < 85) {
                    ps.setTimestamp(8, Timestamp.valueOf(createdAt.plusMinutes(5 + rng.nextInt(600))));
                } else {
                    ps.setNull(8, java.sql.Types.TIMESTAMP);
                }
                ps.setTimestamp(9, Timestamp.valueOf(createdAt));
                ps.setTimestamp(10, Timestamp.valueOf(createdAt));
                ps.executeUpdate();
                try (ResultSet keys = ps.getGeneratedKeys()) {
                    keys.next();
                    long id = keys.getLong(1);
                    ids.add(id);
                    UserState state = new UserState();
                    state.timezone = timezone;
                    state.createdAt = createdAt;
                    userStates.put(id, state);
                }

                if ((i + 1) % 100 == 0 || i + 1 == count) {
                    System.out.println("Seeding users: " + (i + 1) + "/" + count);
                }
            }
        }
        return ids;
    }

    private Set<String> existingHandles() throws SQLException {
        Set<String> handles = new HashSet<>();
        try (Statement st = conn.createStatement(); ResultSet rs = st.executeQuery("SELECT handle FROM users")) {
            while (rs.next()) {
                handles.add(rs.getString(1));
            }
        }
        return handles;
    }

    /* ---------------- membership + check-ins ---------------- */

    private int seedParticipationAndCheckins(List<Long> userIds, List<long[]> challenges, Map<String, Long> badgeIds)
            throws SQLException {
        int totalCheckins = 0;
        int processed = 0;

        Map<Long, Integer> participantCounts = new HashMap<>();
        Batcher batch = new Batcher(conn);

        for (long userId : userIds) {
            UserState state = userStates.get(userId);

            int memberships = powerLawMemberships(challenges.size());
            List<long[]> chosen = pickChallenges(challenges, memberships);
            state.joinCount = chosen.size();

            LocalDateTime collectorAt = null;
            int joinedSoFar = 0;

            for (long[] ch : chosen) {
                long challengeId = ch[0];
                LocalDate chStart = LocalDate.ofEpochDay(ch[1]);
                LocalDate chEnd = LocalDate.ofEpochDay(ch[2]);
                int pointsPer = (int) ch[3];

                LocalDate userLocalToday = LocalDate.now(zoneOf(state.timezone));
                LocalDate joinEarliest = maxDate(state.createdAt.toLocalDate(), chStart.minusDays(7));
                LocalDate joinLatest = minDate(userLocalToday, chEnd);
                if (joinEarliest.isAfter(joinLatest)) {
                    state.joinCount--;
                    continue;
                }

                String archetype = pickArchetype();
                LocalDate joinDate;
                if ("fresh".equals(archetype)) {
                    joinDate = userLocalToday.minusDays(rng.nextInt(4));
                    if (joinDate.isBefore(joinEarliest)) {
                        joinDate = joinEarliest;
                    }
                    if (joinDate.isAfter(joinLatest)) {
                        joinDate = joinLatest;
                    }
                } else {
                    // Quadratic bias toward the challenge start: most
                    // members join early, stragglers trickle in, which
                    // is both realistic and gives histories room to
                    // develop real streaks.
                    int span = (int) (joinLatest.toEpochDay() - joinEarliest.toEpochDay());
                    double r = rng.nextDouble();
                    joinDate = joinEarliest.plusDays(span == 0 ? 0 : (long) Math.floor(r * r * (span + 1)));
                }
                LocalDateTime joinedAt = joinDate.atTime(randomTime());
                joinedSoFar++;

                // Generate the check-in date list for this participation.
                List<LocalDate> dates = checkinDates(archetype, maxDate(chStart, joinDate), minDate(userLocalToday, chEnd));

                // Derive streaks and points exactly like CheckIn::perform.
                int streak = 0;
                int longest = 0;
                int points = 0;
                LocalDate prev = null;
                long participantId = batch.insertParticipant(challengeId, userId, joinedAt);

                for (LocalDate date : dates) {
                    streak = (prev != null && date.equals(prev.plusDays(1))) ? streak + 1 : 1;
                    longest = Math.max(longest, streak);

                    boolean milestone = streak == MILESTONES[0] || streak == MILESTONES[1] || streak == MILESTONES[2];
                    int awarded = pointsPer + (milestone ? MILESTONE_BONUS : 0);
                    points += awarded;

                    LocalDateTime localMoment = date.atTime(checkinTime(date));
                    LocalDateTime utcMoment = toUtc(localMoment, state.timezone);

                    batch.insertCheckin(participantId, userId, challengeId, date, awarded, utcMoment,
                        rng.nextInt(100) < 8 ? BIOS[rng.nextInt(BIOS.length)] : null);
                    batch.insertEvent(userId, "checkin", challengeId, null,
                        "{\"streak\":" + streak + "}", utcMoment);

                    if (milestone) {
                        batch.insertEvent(userId, "streak_milestone", challengeId, null,
                            "{\"streak\":" + streak + "}", utcMoment);
                        batch.insertNotification(userId, "streak_milestone",
                            streak + " day streak", "Milestone bonus: +" + MILESTONE_BONUS + " points.",
                            utcMoment, isRecent(utcMoment) ? null : utcMoment);
                        String code = streak == 7 ? "week_one" : streak == 30 ? "iron_month" : "century";
                        batch.insertBadge(userId, badgeIds.get(code), challengeId, utcMoment);
                        batch.insertEvent(userId, "badge", challengeId, badgeIds.get(code), null, utcMoment);
                    }

                    state.totalCheckins++;
                    if (!state.hasFirstStep) {
                        state.hasFirstStep = true;
                        batch.insertBadge(userId, badgeIds.get("first_step"), null, utcMoment);
                        batch.insertEvent(userId, "badge", null, badgeIds.get("first_step"), null, utcMoment);
                    }
                    state.totalPoints += awarded;
                    if (!state.hasPointMachine && state.totalPoints >= 5000) {
                        state.hasPointMachine = true;
                        batch.insertBadge(userId, badgeIds.get("point_machine"), null, utcMoment);
                        batch.insertEvent(userId, "badge", null, badgeIds.get("point_machine"), null, utcMoment);
                    }
                    if (state.lastActive == null || utcMoment.isAfter(state.lastActive)) {
                        state.lastActive = utcMoment;
                    }
                    prev = date;
                    totalCheckins++;
                }

                // Current streak keeps its stored semantics: the value as
                // of the last check-in (matches the PHP write path).
                LocalDate lastDate = dates.isEmpty() ? null : dates.get(dates.size() - 1);
                batch.updateParticipant(participantId, streak, longest, points, lastDate);

                batch.insertEvent(userId, "joined", challengeId, null, null, toUtc(joinedAt, state.timezone));
                participantCounts.merge(challengeId, 1, Integer::sum);

                if (!state.hasCollector && joinedSoFar >= 5) {
                    state.hasCollector = true;
                    LocalDateTime at = toUtc(joinedAt, state.timezone);
                    batch.insertBadge(userId, badgeIds.get("collector"), null, at);
                    batch.insertEvent(userId, "badge", null, badgeIds.get("collector"), null, at);
                    collectorAt = at;
                }

                // Finisher: challenge ended and the member stayed through
                // the final week.
                if (chEnd.isBefore(userLocalToday) && lastDate != null && !lastDate.isBefore(chEnd.minusDays(6))) {
                    LocalDateTime at = chEnd.plusDays(1).atTime(9, 0);
                    if (!state.hasFinisher) {
                        state.hasFinisher = true;
                        batch.insertBadge(userId, badgeIds.get("finisher"), challengeId, at);
                        batch.insertEvent(userId, "badge", challengeId, badgeIds.get("finisher"), null, at);
                    }
                    batch.insertEvent(userId, "challenge_completed", challengeId, null, null, at);
                    batch.insertNotification(userId, "challenge_ended", "A challenge you joined has ended",
                        "Check the final leaderboard.", at, isRecent(at) ? null : at);
                }
            }

            processed++;
            if (processed % 100 == 0 || processed == userIds.size()) {
                batch.flush();
                conn.commit();
                System.out.println("Building histories: " + processed + "/" + userIds.size()
                    + " users, check-ins written: " + String.format("%,d", totalCheckins));
            }
        }

        batch.flush();

        // Denormalized participant counts.
        try (PreparedStatement ps = conn.prepareStatement(
                "UPDATE challenges SET participant_count = ? WHERE id = ?")) {
            for (Map.Entry<Long, Integer> entry : participantCounts.entrySet()) {
                ps.setInt(1, entry.getValue());
                ps.setLong(2, entry.getKey());
                ps.addBatch();
            }
            ps.executeBatch();
        }
        conn.commit();
        return totalCheckins;
    }

    private void finalizeUsers(List<Long> userIds) throws SQLException {
        try (PreparedStatement ps = conn.prepareStatement(
                "UPDATE users SET total_points = ?, last_active_at = ? WHERE id = ?")) {
            for (long userId : userIds) {
                UserState state = userStates.get(userId);
                ps.setInt(1, state.totalPoints);
                if (state.lastActive != null) {
                    ps.setTimestamp(2, Timestamp.valueOf(state.lastActive));
                } else {
                    ps.setTimestamp(2, Timestamp.valueOf(state.createdAt));
                }
                ps.setLong(3, userId);
                ps.addBatch();
            }
            ps.executeBatch();
        }
        System.out.println("Totals and activity timestamps written for " + userIds.size() + " users.");
    }

    /* ---------------- distributions ---------------- */

    private int powerLawMemberships(int challengeCount) {
        int roll = rng.nextInt(100);
        int n = roll < 35 ? 1 : roll < 60 ? 2 : roll < 77 ? 3 : roll < 89 ? 4 : roll < 96 ? 5 : 6 + rng.nextInt(2);
        return Math.min(n, challengeCount);
    }

    private List<long[]> pickChallenges(List<long[]> challenges, int count) {
        // Featured challenges carry triple weight.
        List<long[]> pool = new ArrayList<>();
        for (long[] ch : challenges) {
            pool.add(ch);
            if (ch[4] == 1) {
                pool.add(ch);
                pool.add(ch);
            }
        }
        List<long[]> chosen = new ArrayList<>();
        Set<Long> seen = new HashSet<>();
        int guard = 0;
        while (chosen.size() < count && guard++ < 200) {
            long[] ch = pool.get(rng.nextInt(pool.size()));
            if (seen.add(ch[0])) {
                chosen.add(ch);
            }
        }
        return chosen;
    }

    private String pickArchetype() {
        int roll = rng.nextInt(100);
        if (roll < 15) return "committed";
        if (roll < 50) return "consistent";
        if (roll < 80) return "struggling";
        if (roll < 95) return "lapsed";
        return "fresh";
    }

    private List<LocalDate> checkinDates(String archetype, LocalDate from, LocalDate to) {
        List<LocalDate> dates = new ArrayList<>();
        if (from.isAfter(to)) {
            return dates;
        }
        double rate;
        LocalDate cutoff = null;
        switch (archetype) {
            // Committed people genuinely do not miss: high nineties so
            // the platform carries real 30 and 60 day streaks.
            case "committed" -> rate = 0.97 + rng.nextDouble() * 0.03;
            case "consistent" -> rate = 0.70 + rng.nextDouble() * 0.20;
            case "struggling" -> rate = 0.40 + rng.nextDouble() * 0.20;
            case "lapsed" -> {
                rate = 0.65 + rng.nextDouble() * 0.15;
                cutoff = to.minusDays(14 + rng.nextInt(22));
            }
            default -> rate = 0.85 + rng.nextDouble() * 0.15; // fresh
        }
        for (LocalDate d = from; !d.isAfter(to); d = d.plusDays(1)) {
            if (cutoff != null && d.isAfter(cutoff)) {
                break;
            }
            double p = rate;
            DayOfWeek dow = d.getDayOfWeek();
            // Weekends dip for everyone except the committed core.
            if ((dow == DayOfWeek.SATURDAY || dow == DayOfWeek.SUNDAY) && !"committed".equals(archetype)) {
                p *= 0.85;
            }
            if (rng.nextDouble() < p) {
                dates.add(d);
            }
        }
        return dates;
    }

    /** Morning and evening weighted, like real people. */
    private LocalTime checkinTime(LocalDate date) {
        int roll = rng.nextInt(100);
        int hour;
        if (roll < 40) {
            hour = 6 + rng.nextInt(4);          // 6-9 morning
        } else if (roll < 60) {
            hour = 10 + rng.nextInt(7);         // 10-16 midday
        } else if (roll < 95) {
            hour = 17 + rng.nextInt(6);         // 17-22 evening
        } else {
            hour = rng.nextInt(24);             // stragglers
        }
        return LocalTime.of(hour, rng.nextInt(60), rng.nextInt(60));
    }

    /* ---------------- small helpers ---------------- */

    private Map<String, Long> loadBadgeIds() throws SQLException {
        Map<String, Long> ids = new HashMap<>();
        try (Statement st = conn.createStatement(); ResultSet rs = st.executeQuery("SELECT id, code FROM badges")) {
            while (rs.next()) {
                ids.put(rs.getString(2), rs.getLong(1));
            }
        }
        if (!ids.containsKey("first_step")) {
            throw new SQLException("badges table is empty; apply database/schema.sql first.");
        }
        return ids;
    }

    private int weightedTimezoneIndex() {
        // US zones carry extra weight so the demo reads coherent without
        // being monocultural.
        int roll = rng.nextInt(100);
        if (roll < 14) return 0;
        if (roll < 22) return 1;
        if (roll < 34) return 2;
        if (roll < 52) return 3;
        return 4 + rng.nextInt(TIMEZONES.length - 4);
    }

    private LocalTime randomTime() {
        return LocalTime.of(rng.nextInt(24), rng.nextInt(60), rng.nextInt(60));
    }

    private boolean isRecent(LocalDateTime utc) {
        return utc.isAfter(LocalDateTime.now(ZoneOffset.UTC).minusDays(7));
    }

    private static ZoneId zoneOf(String tz) {
        try {
            return ZoneId.of(tz);
        } catch (Exception e) {
            return ZoneOffset.UTC;
        }
    }

    private static LocalDateTime toUtc(LocalDateTime local, String tz) {
        ZonedDateTime zoned = local.atZone(zoneOf(tz));
        return zoned.withZoneSameInstant(ZoneOffset.UTC).toLocalDateTime();
    }

    private static LocalDate maxDate(LocalDate a, LocalDate b) {
        return a.isAfter(b) ? a : b;
    }

    private static LocalDate minDate(LocalDate a, LocalDate b) {
        return a.isBefore(b) ? a : b;
    }

    private String randomHex(int chars) {
        StringBuilder sb = new StringBuilder(chars);
        for (int i = 0; i < chars; i++) {
            sb.append(Character.forDigit(rng.nextInt(16), 16));
        }
        return sb.toString();
    }

    static String slugify(String input) {
        String slug = input.toLowerCase()
            .replaceAll("[^a-z0-9]+", "-")
            .replaceAll("(^-|-$)", "");
        return slug.isEmpty() ? "x" : slug;
    }

    private List<String> resourceLines(String resource) throws IOException {
        List<String> lines = new ArrayList<>();
        try (InputStream in = SeedEngine.class.getResourceAsStream(resource)) {
            if (in == null) {
                throw new IOException("Missing bundled resource: " + resource);
            }
            BufferedReader reader = new BufferedReader(new InputStreamReader(in, StandardCharsets.UTF_8));
            String line;
            while ((line = reader.readLine()) != null) {
                if (!line.isBlank()) {
                    lines.add(line.trim());
                }
            }
        }
        return lines;
    }
}
