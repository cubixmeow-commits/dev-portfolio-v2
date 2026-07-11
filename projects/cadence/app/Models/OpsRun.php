<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

final class OpsRun
{
    public const TOOLS = ['seed', 'reset', 'report'];

    /** @param array<string, mixed> $params */
    public static function create(string $tool, array $params, int $userId): int
    {
        Database::run(
            "INSERT INTO ops_runs (tool, params, triggered_by, triggered_by_user, status)
             VALUES (?, ?, 'web', ?, 'running')",
            [$tool, json_encode($params, JSON_UNESCAPED_SLASHES), $userId]
        );
        return Database::lastInsertId();
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM ops_runs WHERE id = ?', [$id]);
    }

    /** @return list<array<string, mixed>> */
    public static function recent(int $limit = 15): array
    {
        $limit = max(1, min(100, $limit));
        return Database::fetchAll(
            "SELECT r.*, u.display_name AS runner_name
             FROM ops_runs r
             LEFT JOIN users u ON u.id = r.triggered_by_user
             ORDER BY r.id DESC LIMIT $limit"
        );
    }

    /** Mark a run failed (used when the process could not be launched). */
    public static function fail(int $id, string $summary): void
    {
        Database::run(
            "UPDATE ops_runs SET status = 'failed', output_summary = ?, finished_at = NOW() WHERE id = ?",
            [$summary, $id]
        );
    }

    /** Human duration for a finished run row. */
    public static function duration(array $run): string
    {
        if ($run['finished_at'] === null) {
            return 'running';
        }
        $seconds = strtotime((string) $run['finished_at']) - strtotime((string) $run['started_at']);
        if ($seconds < 1) {
            return '<1s';
        }
        return $seconds < 60 ? $seconds . 's' : intdiv($seconds, 60) . 'm ' . ($seconds % 60) . 's';
    }
}
