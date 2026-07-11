package com.cadence.engine;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

/**
 * Operational reports as formatted text tables. Output goes to stdout
 * so the CLI and the web admin (which captures stdout) share one
 * implementation and one format.
 */
public final class ReportEngine {

    private final Connection conn;
    private final StringBuilder out = new StringBuilder();

    public ReportEngine(Connection conn) {
        this.conn = conn;
    }

    public String run(String type) throws SQLException {
        switch (type) {
            case "engagement" -> engagement();
            case "retention" -> retention();
            case "challenge-health" -> challengeHealth();
            default -> throw new IllegalArgumentException(
                "Unknown report type: " + type + " (expected engagement, retention, or challenge-health)");
        }
        System.out.print(out);
        return out.toString();
    }

    /* ---------------- engagement ---------------- */

    private void engagement() throws SQLException {
        title("Engagement summary");

        long dau = scalar("SELECT COUNT(DISTINCT user_id) FROM check_ins WHERE checkin_date = CURDATE()");
        long wau = scalar("SELECT COUNT(DISTINCT user_id) FROM check_ins WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)");
        long mau = scalar("SELECT COUNT(DISTINCT user_id) FROM check_ins WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)");
        long members = scalar("SELECT COUNT(*) FROM users WHERE handle NOT LIKE 'deleted\\_%'");
        long weekCheckins = scalar("SELECT COUNT(*) FROM check_ins WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)");
        double perActive = wau == 0 ? 0 : (double) weekCheckins / wau;

        table(
            new String[] {"Metric", "Value"},
            List.of(
                new String[] {"Members", String.format("%,d", members)},
                new String[] {"DAU (checked in today)", String.format("%,d", dau)},
                new String[] {"WAU (last 7 days)", String.format("%,d", wau)},
                new String[] {"MAU (last 30 days)", String.format("%,d", mau)},
                new String[] {"Check-ins, last 7 days", String.format("%,d", weekCheckins)},
                new String[] {"Check-ins per weekly active", String.format("%.1f", perActive)},
                new String[] {"WAU / MAU stickiness", mau == 0 ? "n/a" : String.format("%.0f%%", 100.0 * wau / mau)}
            ));

        title("Current streak distribution (active participations)");
        List<String[]> rows = new ArrayList<>();
        try (Statement st = conn.createStatement();
             ResultSet rs = st.executeQuery(
                 "SELECT CASE"
                 + " WHEN current_streak >= 100 THEN '100+'"
                 + " WHEN current_streak >= 30 THEN '30-99'"
                 + " WHEN current_streak >= 7 THEN '7-29'"
                 + " WHEN current_streak >= 2 THEN '2-6'"
                 + " WHEN current_streak = 1 THEN '1'"
                 + " ELSE '0' END AS bucket, COUNT(*) AS n "
                 + "FROM challenge_participants cp "
                 + "JOIN challenges c ON c.id = cp.challenge_id "
                 + "WHERE c.start_date <= CURDATE() AND c.end_date >= CURDATE() "
                 + "GROUP BY bucket "
                 + "ORDER BY FIELD(bucket, '0','1','2-6','7-29','30-99','100+')")) {
            while (rs.next()) {
                rows.add(new String[] {rs.getString(1) + " days", String.format("%,d", rs.getLong(2))});
            }
        }
        table(new String[] {"Streak", "Participations"}, rows);
    }

    /* ---------------- retention ---------------- */

    private void retention() throws SQLException {
        title("Retention snapshot: of members who joined N weeks ago, % active in the last 7 days");

        List<String[]> rows = new ArrayList<>();
        try (Statement st = conn.createStatement();
             ResultSet rs = st.executeQuery(
                 "SELECT FLOOR(DATEDIFF(CURDATE(), DATE(u.created_at)) / 7) AS weeks_ago, "
                 + "COUNT(*) AS cohort, "
                 + "SUM(EXISTS (SELECT 1 FROM check_ins ci WHERE ci.user_id = u.id "
                 + "            AND ci.checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY))) AS active "
                 + "FROM users u "
                 + "WHERE u.handle NOT LIKE 'deleted\\_%' "
                 + "GROUP BY weeks_ago "
                 + "HAVING weeks_ago BETWEEN 0 AND 16 "
                 + "ORDER BY weeks_ago")) {
            while (rs.next()) {
                long cohort = rs.getLong("cohort");
                long active = rs.getLong("active");
                double pct = cohort == 0 ? 0 : 100.0 * active / cohort;
                rows.add(new String[] {
                    rs.getLong("weeks_ago") + "w",
                    String.format("%,d", cohort),
                    String.format("%,d", active),
                    String.format("%.0f%%", pct),
                    bar(pct),
                });
            }
        }
        table(new String[] {"Cohort", "Joined", "Active", "Retention", ""}, rows);
    }

    /* ---------------- challenge health ---------------- */

    private void challengeHealth() throws SQLException {
        title("Challenge health: members, 7-day check-in rate, trend vs prior week");

        List<String[]> rows = new ArrayList<>();
        try (Statement st = conn.createStatement();
             ResultSet rs = st.executeQuery(
                 "SELECT c.title, c.participant_count, "
                 + "(c.end_date < CURDATE()) AS ended, (c.start_date > CURDATE()) AS upcoming, "
                 + "(SELECT COUNT(*) FROM check_ins ci WHERE ci.challenge_id = c.id "
                 + "  AND ci.checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)) AS week_now, "
                 + "(SELECT COUNT(*) FROM check_ins ci WHERE ci.challenge_id = c.id "
                 + "  AND ci.checkin_date >= DATE_SUB(CURDATE(), INTERVAL 13 DAY) "
                 + "  AND ci.checkin_date < DATE_SUB(CURDATE(), INTERVAL 6 DAY)) AS week_prior "
                 + "FROM challenges c "
                 + "ORDER BY c.participant_count DESC")) {
            while (rs.next()) {
                boolean ended = rs.getInt("ended") == 1;
                boolean upcoming = rs.getInt("upcoming") == 1;
                long members = rs.getLong("participant_count");
                long now = rs.getLong("week_now");
                long prior = rs.getLong("week_prior");
                // Rate: check-ins per member per day over the last week.
                double rate = members == 0 ? 0 : now / (members * 7.0);
                String status = ended ? "ended" : upcoming ? "upcoming" : "active";
                String trend = ended || upcoming ? "-"
                    : now > prior * 1.05 ? "up"
                    : now * 1.05 < prior ? "down"
                    : "flat";
                rows.add(new String[] {
                    truncate(rs.getString("title"), 28),
                    status,
                    String.format("%,d", members),
                    ended || upcoming ? "-" : String.format("%.0f%%", rate * 100),
                    ended || upcoming ? "-" : String.format("%,d", now),
                    trend,
                });
            }
        }
        table(new String[] {"Challenge", "Status", "Members", "7d rate", "7d check-ins", "Trend"}, rows);
    }

    /* ---------------- formatting ---------------- */

    private void title(String text) {
        out.append('\n').append(text).append('\n');
        out.append("=".repeat(text.length())).append('\n');
    }

    private void table(String[] headers, List<String[]> rows) {
        int[] widths = new int[headers.length];
        for (int i = 0; i < headers.length; i++) {
            widths[i] = headers[i].length();
        }
        for (String[] row : rows) {
            for (int i = 0; i < row.length; i++) {
                widths[i] = Math.max(widths[i], row[i].length());
            }
        }
        StringBuilder line = new StringBuilder();
        for (int i = 0; i < headers.length; i++) {
            line.append(pad(headers[i], widths[i])).append(i < headers.length - 1 ? "  " : "");
        }
        out.append(line).append('\n');
        out.append("-".repeat(line.length())).append('\n');
        for (String[] row : rows) {
            StringBuilder r = new StringBuilder();
            for (int i = 0; i < row.length; i++) {
                r.append(pad(row[i], widths[i])).append(i < row.length - 1 ? "  " : "");
            }
            out.append(r).append('\n');
        }
        if (rows.isEmpty()) {
            out.append("(no rows)\n");
        }
    }

    private static String pad(String s, int width) {
        return s + " ".repeat(Math.max(0, width - s.length()));
    }

    private static String bar(double pct) {
        int blocks = (int) Math.round(pct / 5.0);
        return "#".repeat(Math.max(0, blocks));
    }

    private static String truncate(String s, int max) {
        return s.length() <= max ? s : s.substring(0, max - 3) + "...";
    }

    private long scalar(String sql) throws SQLException {
        try (Statement st = conn.createStatement(); ResultSet rs = st.executeQuery(sql)) {
            rs.next();
            return rs.getLong(1);
        }
    }
}
