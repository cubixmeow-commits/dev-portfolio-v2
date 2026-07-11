<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

/**
 * Leaderboard queries. Week and month windows are computed live from
 * check_ins by date, never stored; all-time reads the denormalized
 * users.total_points. Deleted (anonymized) accounts are excluded.
 */
final class Leaderboard
{
    public const WINDOWS = ['week', 'month', 'all'];

    /** Start date (inclusive) for a window, or null for all time. */
    public static function windowStart(string $window): ?string
    {
        return match ($window) {
            'week'  => date('Y-m-d', strtotime('monday this week')),
            'month' => date('Y-m-01'),
            default => null,
        };
    }

    /**
     * Global top N for a window.
     * @return list<array<string, mixed>> rows with user fields + points
     */
    public static function global(string $window, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $start = self::windowStart($window);

        if ($start === null) {
            return Database::fetchAll(
                "SELECT u.id, u.display_name, u.handle, u.avatar_seed, u.total_points AS points
                 FROM users u
                 WHERE u.total_points > 0 AND u.handle NOT LIKE 'deleted\\_%'
                 ORDER BY u.total_points DESC, u.id ASC
                 LIMIT $limit"
            );
        }

        return Database::fetchAll(
            "SELECT u.id, u.display_name, u.handle, u.avatar_seed, SUM(ci.points_awarded) AS points
             FROM check_ins ci
             JOIN users u ON u.id = ci.user_id
             WHERE ci.checkin_date >= ? AND u.handle NOT LIKE 'deleted\\_%'
             GROUP BY u.id, u.display_name, u.handle, u.avatar_seed
             ORDER BY points DESC, u.id ASC
             LIMIT $limit",
            [$start]
        );
    }

    /**
     * The signed-in user's rank and points in a window, plus the field
     * size, for the pinned own-rank bar.
     *
     * @return array{rank: int, points: int, of: int}
     */
    public static function rankFor(int $userId, string $window): array
    {
        $start = self::windowStart($window);

        if ($start === null) {
            $points = (int) Database::fetchValue('SELECT total_points FROM users WHERE id = ?', [$userId]);
            $rank = 1 + (int) Database::fetchValue(
                "SELECT COUNT(*) FROM users
                 WHERE total_points > ? AND handle NOT LIKE 'deleted\\_%'",
                [$points]
            );
            $of = (int) Database::fetchValue(
                "SELECT COUNT(*) FROM users WHERE total_points > 0 AND handle NOT LIKE 'deleted\\_%'"
            );
            return ['rank' => $rank, 'points' => $points, 'of' => max($of, 1)];
        }

        $points = (int) Database::fetchValue(
            'SELECT COALESCE(SUM(points_awarded), 0) FROM check_ins WHERE user_id = ? AND checkin_date >= ?',
            [$userId, $start]
        );
        $rank = 1 + (int) Database::fetchValue(
            "SELECT COUNT(*) FROM (
                SELECT ci.user_id
                FROM check_ins ci
                JOIN users u ON u.id = ci.user_id
                WHERE ci.checkin_date >= ? AND u.handle NOT LIKE 'deleted\\_%'
                GROUP BY ci.user_id
                HAVING SUM(ci.points_awarded) > ?
             ) ranked",
            [$start, $points]
        );
        $of = (int) Database::fetchValue(
            "SELECT COUNT(DISTINCT ci.user_id)
             FROM check_ins ci
             JOIN users u ON u.id = ci.user_id
             WHERE ci.checkin_date >= ? AND u.handle NOT LIKE 'deleted\\_%'",
            [$start]
        );
        return ['rank' => $rank, 'points' => $points, 'of' => max($of, 1)];
    }
}
