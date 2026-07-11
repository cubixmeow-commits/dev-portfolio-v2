package com.cadence.engine;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

/**
 * Records engine executions in ops_runs so both the CLI and the web
 * admin share one run history. When the web admin triggers a run it
 * pre-creates the row (status running) and passes --run-id; the engine
 * then finishes that row instead of inserting a second one.
 */
public final class OpsRun {

    private final Connection conn;
    private final long id;

    private OpsRun(Connection conn, long id) {
        this.conn = conn;
        this.id = id;
    }

    public static OpsRun start(Connection conn, String tool, String paramsJson, String triggeredBy, Long existingRunId)
            throws SQLException {
        if (existingRunId != null) {
            return new OpsRun(conn, existingRunId);
        }
        try (PreparedStatement ps = conn.prepareStatement(
                "INSERT INTO ops_runs (tool, params, triggered_by, status) VALUES (?, ?, ?, 'running')",
                Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, tool);
            ps.setString(2, paramsJson);
            ps.setString(3, triggeredBy);
            ps.executeUpdate();
            try (ResultSet keys = ps.getGeneratedKeys()) {
                keys.next();
                return new OpsRun(conn, keys.getLong(1));
            }
        }
    }

    public long id() {
        return id;
    }

    public void finish(boolean success, String summary) throws SQLException {
        try (PreparedStatement ps = conn.prepareStatement(
                "UPDATE ops_runs SET status = ?, output_summary = ?, finished_at = NOW() WHERE id = ?")) {
            ps.setString(1, success ? "success" : "failed");
            ps.setString(2, summary != null && summary.length() > 60000 ? summary.substring(summary.length() - 60000) : summary);
            ps.setLong(3, id);
            ps.executeUpdate();
        }
    }
}
