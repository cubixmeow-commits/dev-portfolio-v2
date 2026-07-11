<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Database;
use Cadence\Core\View;

/**
 * Analytics dashboard. Every number is computed live from the DB;
 * nothing here is cached or stored, which is affordable at demo scale
 * and honest for a portfolio piece.
 */
final class AdminController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $cards = [
            'members' => (int) Database::fetchValue(
                "SELECT COUNT(*) FROM users WHERE handle NOT LIKE 'deleted\\_%'"
            ),
            'wau' => (int) Database::fetchValue(
                'SELECT COUNT(DISTINCT user_id) FROM check_ins WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)'
            ),
            'checkins_today' => (int) Database::fetchValue(
                'SELECT COUNT(*) FROM check_ins WHERE checkin_date = CURDATE()'
            ),
            'avg_streak' => (float) Database::fetchValue(
                'SELECT COALESCE(AVG(current_streak), 0)
                 FROM challenge_participants cp
                 JOIN challenges c ON c.id = cp.challenge_id
                 WHERE c.start_date <= CURDATE() AND c.end_date >= CURDATE()'
            ),
        ];

        // Signups per day, last 90 days, zero-filled.
        $signups = self::zeroFilledSeries(
            Database::fetchAll(
                "SELECT DATE(created_at) AS d, COUNT(*) AS n FROM users
                 WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 89 DAY)
                   AND handle NOT LIKE 'deleted\\_%'
                 GROUP BY DATE(created_at)"
            ),
            90
        );

        // Check-ins per day, last 30 days, zero-filled.
        $checkins = self::zeroFilledSeries(
            Database::fetchAll(
                'SELECT checkin_date AS d, COUNT(*) AS n FROM check_ins
                 WHERE checkin_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                 GROUP BY checkin_date'
            ),
            30
        );

        // How many challenges each member belongs to (participation
        // distribution: the power-law shape is the story).
        $distribution = Database::fetchAll(
            "SELECT joined, COUNT(*) AS members FROM (
                SELECT COUNT(*) AS joined FROM challenge_participants GROUP BY user_id
             ) t
             GROUP BY joined ORDER BY joined"
        );

        $newestUsers = Database::fetchAll(
            "SELECT display_name, handle, avatar_seed, created_at, total_points FROM users
             WHERE handle NOT LIKE 'deleted\\_%'
             ORDER BY id DESC LIMIT 8"
        );

        $topChallenges = Database::fetchAll(
            'SELECT c.title, c.slug, c.participant_count,
                    (SELECT COUNT(*) FROM check_ins ci WHERE ci.challenge_id = c.id
                      AND ci.checkin_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)) AS week_checkins
             FROM challenges c
             ORDER BY week_checkins DESC LIMIT 8'
        );

        View::render('admin/index', [
            'title'        => 'Admin',
            'page_css'     => 'admin',
            'cards'        => $cards,
            'signups'      => $signups,
            'checkins'     => $checkins,
            'distribution' => $distribution,
            'newestUsers'  => $newestUsers,
            'topChallenges' => $topChallenges,
        ]);
    }

    /**
     * @param list<array<string, mixed>> $rows with d (date) and n keys
     * @return list<array{date: string, value: int}>
     */
    private static function zeroFilledSeries(array $rows, int $days): array
    {
        $byDay = [];
        foreach ($rows as $row) {
            $byDay[(string) $row['d']] = (int) $row['n'];
        }
        $series = [];
        $start = new \DateTimeImmutable('-' . ($days - 1) . ' days');
        for ($i = 0; $i < $days; $i++) {
            $date = $start->modify("+$i days")->format('Y-m-d');
            $series[] = ['date' => $date, 'value' => $byDay[$date] ?? 0];
        }
        return $series;
    }
}
