package com.cadence.engine.cli;

import com.cadence.engine.Db;
import com.cadence.engine.OpsRun;
import com.cadence.engine.ReportEngine;
import com.cadence.engine.ResetEngine;
import com.cadence.engine.SeedEngine;

import java.sql.Connection;
import java.util.HashMap;
import java.util.Map;

/**
 * CLI for the Cadence ops engine. Thin by design: parse arguments,
 * open the connection, delegate to an engine class, record the run.
 *
 * Exit codes: 0 success, 1 usage error, 2 runtime failure.
 */
public final class Main {

    private static final String USAGE = """
        Cadence ops engine

        Usage:
          java -jar cadence-engine.jar <tool> [options]

        Tools:
          seed      Generate demo data.
                      --users=N          users to create (default 500)
                      --challenges=N     challenges to create (default 12)
                      --history-days=N   days of history (default 120)
          reset     Restore the known-good demo state: wipe demo data,
                    reseed at standard scale, rebuild the curated demo
                    member and admin account.
                      --confirm          required; reset refuses without it
          report    Print an operational report.
                      --type=T           engagement | retention | challenge-health

        Common options:
          --db-config=PATH   engine.properties location (default:
                             config/engine.properties, then ../config
                             relative to the jar)
          --seed=N           RNG seed (default 42; same seed, same data)
          --run-id=N         finish an existing ops_runs row instead of
                             creating one (used by the web admin)
          --triggered-by=X   cli | web (default cli)
          --help             this text

        Exit codes: 0 success, 1 usage error, 2 runtime failure.
        """;

    public static void main(String[] args) {
        if (args.length == 0 || hasFlag(args, "--help")) {
            System.out.println(USAGE);
            System.exit(args.length == 0 ? 1 : 0);
        }

        String tool = args[0];
        Map<String, String> opts = parseOptions(args);
        if (!tool.equals("seed") && !tool.equals("reset") && !tool.equals("report")) {
            System.err.println("Unknown tool: " + tool);
            System.out.println(USAGE);
            System.exit(1);
        }

        long rngSeed = parseLong(opts.getOrDefault("seed", "42"), "--seed");
        Long runId = opts.containsKey("run-id") ? parseLong(opts.get("run-id"), "--run-id") : null;
        String triggeredBy = opts.getOrDefault("triggered-by", "cli");
        if (!triggeredBy.equals("cli") && !triggeredBy.equals("web")) {
            fail(1, "--triggered-by must be cli or web");
        }

        OpsRun run = null;
        try {
            Db db = Db.fromConfig(opts.get("db-config"));
            try (Connection conn = db.connect()) {
                switch (tool) {
                    case "seed" -> {
                        int users = (int) parseLong(opts.getOrDefault("users", "500"), "--users");
                        int challenges = (int) parseLong(opts.getOrDefault("challenges", "12"), "--challenges");
                        int historyDays = (int) parseLong(opts.getOrDefault("history-days", "120"), "--history-days");
                        if (users < 1 || users > 20000 || challenges < 1 || challenges > 200
                                || historyDays < 1 || historyDays > 730) {
                            fail(1, "Out of range: users 1-20000, challenges 1-200, history-days 1-730.");
                        }
                        String params = String.format(
                            "{\"users\":%d,\"challenges\":%d,\"historyDays\":%d,\"rngSeed\":%d}",
                            users, challenges, historyDays, rngSeed);
                        run = OpsRun.start(conn, "seed", params, triggeredBy, runId);
                        String summary = new SeedEngine(conn, rngSeed).run(users, challenges, historyDays);
                        run.finish(true, summary);
                    }
                    case "reset" -> {
                        if (!hasFlag(args, "--confirm")) {
                            fail(1, "reset wipes and rebuilds all demo data. Re-run with --confirm.");
                        }
                        String params = String.format("{\"rngSeed\":%d}", rngSeed);
                        run = OpsRun.start(conn, "reset", params, triggeredBy, runId);
                        String summary = new ResetEngine(conn, rngSeed).run();
                        run.finish(true, summary);
                    }
                    case "report" -> {
                        String type = opts.getOrDefault("type", "engagement");
                        String params = String.format("{\"type\":\"%s\"}", type);
                        run = OpsRun.start(conn, "report", params, triggeredBy, runId);
                        String output = new ReportEngine(conn).run(type);
                        run.finish(true, output);
                    }
                    default -> throw new IllegalStateException(tool);
                }
                if (!conn.getAutoCommit()) {
                    conn.commit();
                }
            }
        } catch (IllegalArgumentException e) {
            finishQuietly(run, e.getMessage());
            fail(1, e.getMessage());
        } catch (Exception e) {
            finishQuietly(run, e.toString());
            System.err.println("Runtime failure: " + e);
            System.exit(2);
        }
    }

    private static void finishQuietly(OpsRun run, String message) {
        if (run != null) {
            try {
                run.finish(false, message);
            } catch (Exception ignored) {
                // The primary error is already being reported.
            }
        }
    }

    private static Map<String, String> parseOptions(String[] args) {
        Map<String, String> opts = new HashMap<>();
        for (int i = 1; i < args.length; i++) {
            String arg = args[i];
            if (!arg.startsWith("--")) {
                fail(1, "Unexpected argument: " + arg);
            }
            String body = arg.substring(2);
            int eq = body.indexOf('=');
            if (eq >= 0) {
                opts.put(body.substring(0, eq), body.substring(eq + 1));
            } else {
                opts.put(body, "true");
            }
        }
        return opts;
    }

    private static boolean hasFlag(String[] args, String flag) {
        for (String arg : args) {
            if (arg.equals(flag)) {
                return true;
            }
        }
        return false;
    }

    private static long parseLong(String value, String name) {
        try {
            return Long.parseLong(value);
        } catch (NumberFormatException e) {
            fail(1, name + " expects a number, got: " + value);
            return 0; // unreachable
        }
    }

    private static void fail(int code, String message) {
        System.err.println(message);
        System.exit(code);
    }
}
