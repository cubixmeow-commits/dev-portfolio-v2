<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Config;
use Cadence\Core\View;
use Cadence\Models\OpsRun;

/**
 * Web face of the Java ops engine. Thin on purpose: the page builds a
 * command, the engine does the work, ops_runs and a log file carry the
 * result back. When shell_exec is unavailable (some shared hosts), the
 * page degrades to showing the exact SSH command instead of dead-ending.
 */
final class AdminToolsController
{
    public function index(): void
    {
        Auth::requireAdmin();
        View::render('admin/tools', [
            'title'        => 'Ops tools',
            'page_css'     => 'admin',
            'shellEnabled' => self::shellEnabled(),
            'runs'         => OpsRun::recent(15),
            'jarPath'      => Config::string('engine.jar_path'),
        ]);
    }

    /** POST /admin/tools/run: launch a tool in the background. */
    public function run(): void
    {
        $admin = Auth::requireAdmin();

        $tool = (string) ($_POST['tool'] ?? '');
        if (!in_array($tool, OpsRun::TOOLS, true)) {
            json_response(['ok' => false, 'error' => 'Unknown tool.'], 422);
        }

        // Numeric params are cast before they get anywhere near a shell.
        $params = [];
        $args = [];
        if ($tool === 'seed') {
            $users = max(1, min(20000, (int) ($_POST['users'] ?? 500)));
            $challenges = max(1, min(200, (int) ($_POST['challenges'] ?? 12)));
            $historyDays = max(1, min(730, (int) ($_POST['history_days'] ?? 120)));
            $params = ['users' => $users, 'challenges' => $challenges, 'historyDays' => $historyDays];
            $args = ["--users=$users", "--challenges=$challenges", "--history-days=$historyDays"];
        } elseif ($tool === 'reset') {
            if (($_POST['confirm_text'] ?? '') !== 'RESET') {
                json_response(['ok' => false, 'error' => 'Type RESET in the confirm box to proceed.'], 422);
            }
            $args = ['--confirm'];
        } elseif ($tool === 'report') {
            $type = (string) ($_POST['type'] ?? 'engagement');
            if (!in_array($type, ['engagement', 'retention', 'challenge-health'], true)) {
                json_response(['ok' => false, 'error' => 'Unknown report type.'], 422);
            }
            $params = ['type' => $type];
            $args = ["--type=$type"];
        }

        if (!self::shellEnabled()) {
            json_response([
                'ok'      => false,
                'error'   => 'shell_exec is disabled on this host.',
                'command' => self::cliCommand($tool, $args),
            ], 409);
        }

        $runId = OpsRun::create($tool, $params, (int) $admin['id']);
        $logFile = self::logPath($runId);
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0775, true);
        }

        // Fixed jar path from config; every argument shell-escaped; the
        // tool name came from an allowlist above.
        $command = self::engineCommand($tool, $args, $runId)
            . ' >> ' . escapeshellarg($logFile) . ' 2>&1 & echo $!';
        $pid = shell_exec($command);

        if ($pid === null || trim((string) $pid) === '') {
            OpsRun::fail($runId, 'Failed to launch the engine process.');
            json_response(['ok' => false, 'error' => 'Failed to launch the engine process.'], 500);
        }

        json_response(['ok' => true, 'runId' => $runId]);
    }

    /** GET /admin/tools/status/{id}: poll a run's state and output. */
    public function status(string $id): void
    {
        Auth::requireAdmin();

        $run = OpsRun::find((int) $id);
        if ($run === null) {
            json_response(['ok' => false, 'error' => 'No such run.'], 404);
        }

        $log = '';
        $logFile = self::logPath((int) $run['id']);
        if (is_file($logFile)) {
            $log = (string) file_get_contents($logFile, false, null, 0, 65536);
        } elseif ($run['output_summary'] !== null) {
            $log = (string) $run['output_summary'];
        }

        // JVM environment banners (e.g. JAVA_TOOL_OPTIONS pickup notes)
        // are noise, not engine output.
        $log = implode("\n", array_filter(
            explode("\n", $log),
            static fn(string $line): bool => !str_starts_with($line, 'Picked up JAVA_TOOL_OPTIONS')
        ));

        json_response([
            'ok'       => true,
            'status'   => $run['status'],
            'log'      => $log,
            'duration' => OpsRun::duration($run),
        ]);
    }

    /** Full engine invocation for web-triggered runs. */
    private static function engineCommand(string $tool, array $args, int $runId): string
    {
        $parts = [
            escapeshellarg(Config::string('engine.java_bin', 'java')),
            '-jar',
            escapeshellarg(Config::string('engine.jar_path')),
            escapeshellarg($tool),
        ];
        foreach ($args as $arg) {
            $parts[] = escapeshellarg($arg);
        }
        $parts[] = escapeshellarg('--db-config=' . Config::string('engine.properties_path'));
        $parts[] = escapeshellarg('--run-id=' . $runId);
        $parts[] = escapeshellarg('--triggered-by=web');
        return implode(' ', $parts);
    }

    /** The copy-paste SSH equivalent, shown when shell_exec is off. */
    public static function cliCommand(string $tool, array $args): string
    {
        return 'java -jar ' . Config::string('engine.jar_path') . ' ' . $tool
            . ($args !== [] ? ' ' . implode(' ', $args) : '')
            . ' --db-config=' . Config::string('engine.properties_path');
    }

    public static function shellEnabled(): bool
    {
        if (!function_exists('shell_exec')) {
            return false;
        }
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        return !in_array('shell_exec', $disabled, true);
    }

    private static function logPath(int $runId): string
    {
        return __DIR__ . '/../../storage/logs/run-' . $runId . '.log';
    }
}
