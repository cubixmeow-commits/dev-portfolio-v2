package com.cubixmeow.helpdesk;

import java.io.IOException;
import java.io.PrintWriter;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.LinkedHashMap;
import java.util.Map;

/**
 * TicketReportGenerator — a small standalone weekly ops report tool for the
 * Helpdesk Ops Toolkit. It connects to the same MySQL database the PHP app
 * uses (the {@code hot_tickets} table), prints a summary to the console, and
 * writes a timestamped report file to {@code reports/}.
 *
 * <p>This mirrors the real-world pattern of "a support script someone in IT
 * wrote to automate a recurring report," and demonstrates Java/JDBC alongside
 * the PHP web layer. It is intentionally a single readable class.</p>
 *
 * <p>Usage:</p>
 * <pre>
 *   java -jar ticket-report.jar --range weekly
 * </pre>
 *
 * <p>Database connection is taken from environment variables (with sensible
 * defaults) so no credentials are hard-coded:</p>
 * <ul>
 *   <li>{@code HOT_DB_URL}  (default {@code jdbc:mysql://127.0.0.1:3306/helpdesk_ops})</li>
 *   <li>{@code HOT_DB_USER} (default {@code root})</li>
 *   <li>{@code HOT_DB_PASS} (default empty)</li>
 * </ul>
 */
public final class TicketReportGenerator {

    private static final DateTimeFormatter LOG_TS =
            DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
    private static final DateTimeFormatter FILE_TS =
            DateTimeFormatter.ofPattern("yyyyMMdd-HHmmss");

    public static void main(String[] args) {
        String range = argValue(args, "--range", "weekly");
        if (!"weekly".equals(range)) {
            log("WARN", "Only --range weekly is supported; defaulting to weekly.");
            range = "weekly";
        }

        String url  = env("HOT_DB_URL", "jdbc:mysql://127.0.0.1:3306/helpdesk_ops");
        String user = env("HOT_DB_USER", "root");
        String pass = env("HOT_DB_PASS", "");

        log("INFO", "TicketReportGenerator starting (range=" + range + ").");
        log("INFO", "Connecting to " + url);

        try (Connection conn = DriverManager.getConnection(url, user, pass)) {
            Report report = buildReport(conn);
            log("INFO", "Pulled " + report.totalRows + " ticket rows.");

            String text = render(report, range);
            System.out.println();
            System.out.println(text);

            Path out = writeReportFile(text);
            log("INFO", "Report written to " + out.toAbsolutePath());
            log("INFO", "Completed successfully.");
        } catch (SQLException e) {
            log("ERROR", "Database error: " + e.getMessage());
            System.err.println("\nCould not generate the report. Check that MySQL is running, the "
                    + "schema is imported, and the MySQL JDBC driver (mysql-connector-j) is on the "
                    + "classpath. See README.md.");
            System.exit(2);
        } catch (IOException e) {
            log("ERROR", "Could not write report file: " + e.getMessage());
            System.exit(3);
        }
    }

    /** Immutable-ish holder for the aggregated numbers. */
    private static final class Report {
        int totalRows;
        int last7Days;
        final Map<String, Integer> byCategory = new LinkedHashMap<>();
        final Map<String, Integer> byStatus   = new LinkedHashMap<>();
        final Map<String, Integer> byPriority = new LinkedHashMap<>();
        double avgResolutionHours;
    }

    /** Runs the aggregate queries against hot_tickets. */
    private static Report buildReport(Connection conn) throws SQLException {
        Report r = new Report();

        r.totalRows  = scalarInt(conn, "SELECT COUNT(*) FROM hot_tickets");
        r.last7Days  = scalarInt(conn,
                "SELECT COUNT(*) FROM hot_tickets WHERE created_at >= NOW() - INTERVAL 7 DAY");

        countInto(conn, "SELECT category, COUNT(*) FROM hot_tickets GROUP BY category", r.byCategory);
        countInto(conn, "SELECT status,   COUNT(*) FROM hot_tickets GROUP BY status",   r.byStatus);
        countInto(conn, "SELECT priority, COUNT(*) FROM hot_tickets GROUP BY priority", r.byPriority);

        // Average resolution time in hours for tickets that have been resolved.
        try (PreparedStatement ps = conn.prepareStatement(
                "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) "
                        + "FROM hot_tickets WHERE resolved_at IS NOT NULL");
             ResultSet rs = ps.executeQuery()) {
            if (rs.next()) {
                r.avgResolutionHours = rs.getDouble(1);
            }
        }
        return r;
    }

    private static int scalarInt(Connection conn, String sql) throws SQLException {
        try (PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            return rs.next() ? rs.getInt(1) : 0;
        }
    }

    private static void countInto(Connection conn, String sql, Map<String, Integer> into)
            throws SQLException {
        try (PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                into.put(rs.getString(1), rs.getInt(2));
            }
        }
    }

    /** Renders the report as a fixed-width text block for console + file. */
    private static String render(Report r, String range) {
        StringBuilder sb = new StringBuilder();
        String stamp = LocalDateTime.now().format(LOG_TS);
        sb.append("========================================================\n");
        sb.append(" HELPDESK OPS TOOLKIT — WEEKLY TICKET REPORT\n");
        sb.append(" Generated: ").append(stamp).append("   Range: ").append(range).append('\n');
        sb.append("========================================================\n\n");

        sb.append(String.format("Total tickets ............. %d%n", r.totalRows));
        sb.append(String.format("Opened in last 7 days ..... %d%n", r.last7Days));
        sb.append(String.format("Avg. resolution time ...... %.1f hours%n%n", r.avgResolutionHours));

        appendTable(sb, "BY CATEGORY", r.byCategory);
        appendTable(sb, "BY STATUS",   r.byStatus);
        appendTable(sb, "BY PRIORITY", r.byPriority);

        sb.append("--------------------------------------------------------\n");
        sb.append("End of report.\n");
        return sb.toString();
    }

    private static void appendTable(StringBuilder sb, String title, Map<String, Integer> rows) {
        sb.append(title).append('\n');
        sb.append("--------------------------------------------------------\n");
        if (rows.isEmpty()) {
            sb.append("  (no data)\n\n");
            return;
        }
        for (Map.Entry<String, Integer> e : rows.entrySet()) {
            String bar = "#".repeat(Math.min(e.getValue(), 40));
            sb.append(String.format("  %-16s %3d  %s%n", e.getKey(), e.getValue(), bar));
        }
        sb.append('\n');
    }

    private static Path writeReportFile(String text) throws IOException {
        Path dir = Paths.get("reports");
        Files.createDirectories(dir);
        String name = "ticket-report-" + LocalDateTime.now().format(FILE_TS) + ".txt";
        Path file = dir.resolve(name);
        try (PrintWriter pw = new PrintWriter(Files.newBufferedWriter(file))) {
            pw.print(text);
        }
        return file;
    }

    /* ---- tiny helpers ---- */

    private static String env(String key, String fallback) {
        String v = System.getenv(key);
        return (v == null || v.isEmpty()) ? fallback : v;
    }

    private static String argValue(String[] args, String flag, String fallback) {
        for (int i = 0; i < args.length - 1; i++) {
            if (flag.equals(args[i])) {
                return args[i + 1];
            }
        }
        return fallback;
    }

    private static void log(String level, String msg) {
        System.err.println("[" + LocalDateTime.now().format(LOG_TS) + "] " + level + " " + msg);
    }

    private TicketReportGenerator() { /* no instances */ }
}
