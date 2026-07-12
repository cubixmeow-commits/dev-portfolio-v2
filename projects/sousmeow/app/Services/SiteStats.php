<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;

/**
 * Public and admin metrics for the portfolio kitchen dashboard.
 */
final class SiteStats
{
    public static function pacificTodayRange(): array
    {
        $today = (new \DateTimeImmutable('now', Simulation::pacific()))->format('Y-m-d');
        return Simulation::pacificDayUtcRange($today);
    }

    /** @return array{chefs:int, kits_today:int, kits_total:int, approved_today:int, rating:float, cookbooks:int, recipes:int, active_today:int} */
    public static function hero(): array
    {
        [$dayStart, $dayEnd] = self::pacificTodayRange();

        $chefs = (int) Database::fetchValue('SELECT COUNT(*) FROM users WHERE simulation = 1');
        $kitsToday = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM exports e
             JOIN projects p ON p.id = e.project_id
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND e.created_at >= ? AND e.created_at <= ?',
            [$dayStart, $dayEnd]
        );
        $kitsTotal = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM exports e
             JOIN projects p ON p.id = e.project_id
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1'
        );
        $approvedToday = (int) Database::fetchValue(
            "SELECT COUNT(*) FROM artifacts a
             JOIN projects p ON p.id = a.project_id
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND a.status = 'approved'
               AND a.updated_at >= ? AND a.updated_at <= ?",
            [$dayStart, $dayEnd]
        );
        $activeToday = (int) Database::fetchValue(
            'SELECT COUNT(DISTINCT p.user_id) FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.updated_at >= ? AND p.updated_at <= ?',
            [$dayStart, $dayEnd]
        );

        $ratingRow = Database::fetch(
            'SELECT SUM(demo_completed_runs * demo_avg_rating) AS weighted, SUM(demo_completed_runs) AS weight
             FROM cookbooks WHERE demo_avg_rating IS NOT NULL AND demo_completed_runs > 0'
        );
        $weight = (int) ($ratingRow['weight'] ?? 0);
        $rating = $weight > 0 ? round((float) $ratingRow['weighted'] / $weight, 1) : 4.7;

        return [
            'chefs'          => $chefs,
            'kits_today'     => $kitsToday,
            'kits_total'     => $kitsTotal,
            'approved_today' => $approvedToday,
            'rating'         => $rating,
            'cookbooks'      => (int) Database::fetchValue('SELECT COUNT(*) FROM cookbooks'),
            'recipes'        => (int) Database::fetchValue('SELECT COUNT(*) FROM recipes'),
            'active_today'   => $activeToday,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function popularCookbooks(int $limit = 3): array
    {
        $limit = max(1, $limit);
        [$dayStart, $dayEnd] = self::pacificTodayRange();
        return Database::fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count,
                    (SELECT COUNT(*) FROM projects p
                     JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.updated_at >= ? AND p.updated_at <= ?) AS activity_today
             FROM cookbooks c
             ORDER BY activity_today DESC, c.demo_completed_runs DESC, c.sort_order
             LIMIT {$limit}",
            [$dayStart, $dayEnd]
        );
    }

    /**
     * @return list<array{kind:string, at:string, name:string, cookbook_title?:string, detail?:string}>
     */
    public static function recentActivity(int $limit = 10): array
    {
        $events = [];

        $completions = Database::fetchAll(
            "SELECT p.completed_at AS at, u.name, c.title AS cookbook_title
             FROM projects p JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
             ORDER BY p.completed_at DESC LIMIT 5"
        );
        foreach ($completions as $row) {
            $events[] = ['kind' => 'completed', 'at' => (string) $row['at'], 'name' => (string) $row['name'], 'cookbook_title' => (string) $row['cookbook_title']];
        }

        $cooking = Database::fetchAll(
            "SELECT p.updated_at AS at, u.name, c.title AS cookbook_title,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS approved_count,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id) AS recipe_count
             FROM projects p JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL
             ORDER BY p.updated_at DESC LIMIT 5"
        );
        foreach ($cooking as $row) {
            $approved = (int) $row['approved_count'];
            $total = (int) $row['recipe_count'];
            if ($approved === 0 || $total === 0) {
                continue;
            }
            $events[] = [
                'kind' => 'cooking', 'at' => (string) $row['at'], 'name' => (string) $row['name'],
                'cookbook_title' => (string) $row['cookbook_title'],
                'detail' => 'Recipe ' . min($approved + 1, $total) . ' of ' . $total,
            ];
        }

        $pantries = Database::fetchAll(
            "SELECT p.pantry_saved_at AS at, u.name, c.title AS cookbook_title
             FROM projects p JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.pantry_saved_at IS NOT NULL
             ORDER BY p.pantry_saved_at DESC LIMIT 4"
        );
        foreach ($pantries as $row) {
            $events[] = ['kind' => 'pantry', 'at' => (string) $row['at'], 'name' => (string) $row['name'], 'cookbook_title' => (string) $row['cookbook_title']];
        }

        $joins = Database::fetchAll(
            'SELECT created_at AS at, name FROM users WHERE simulation = 1 ORDER BY created_at DESC LIMIT 4'
        );
        foreach ($joins as $row) {
            $events[] = ['kind' => 'joined', 'at' => (string) $row['at'], 'name' => (string) $row['name']];
        }

        usort($events, static fn(array $a, array $b): int => strcmp($b['at'], $a['at']));
        return array_slice($events, 0, max(1, $limit));
    }

    public static function formatCompact(int $n): string
    {
        if ($n >= 1_000_000) {
            return number_format($n / 1_000_000, 1) . 'M';
        }
        if ($n >= 1000) {
            return number_format($n / 1000, 1) . 'k';
        }
        return number_format($n);
    }

    public static function firstName(string $fullName): string
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        return $parts[0] !== '' ? $parts[0] : 'Someone';
    }

    public static function initials(string $fullName): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($fullName)) ?: [], static fn(string $p): bool => $p !== ''));
        if ($parts === []) {
            return 'SM';
        }
        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
        }
        return strtoupper(mb_substr($parts[0], 0, 2));
    }

    /** Warm avatar hues consistent with the kitchen palette. */
    public static function avatarHue(string $fullName): int
    {
        $hues = [18, 32, 42, 145, 168, 198, 268, 312];
        return $hues[crc32($fullName) % count($hues)];
    }

    /**
     * GitHub-style contribution grid: columns are weeks, rows are Sun–Sat.
     *
     * @return array{
     *   weeks: list<list<array{date:string,count:int,level:int}|null>>,
     *   max: int,
     *   total: int,
     *   end: string,
     *   month_labels: list<array{label:string,col:int}>
     * }
     */
    public static function activityHeatmap(int $weeks = 12): array
    {
        $weeks = max(4, min(26, $weeks));
        $totalDays = $weeks * 7;
        $pacific = Simulation::pacific();
        $endDay = new \DateTimeImmutable('today', $pacific);
        $startDay = $endDay->modify('-' . ($totalDays - 1) . ' days');

        $counts = [];
        for ($i = 0; $i < $totalDays; $i++) {
            $counts[$startDay->modify('+' . $i . ' days')->format('Y-m-d')] = 0;
        }

        $runs = Database::fetchAll(
            'SELECT pacific_date, actions_count FROM simulation_runs
             WHERE pacific_date >= ? AND pacific_date <= ?',
            [$startDay->format('Y-m-d'), $endDay->format('Y-m-d')]
        );
        $hasRun = [];
        foreach ($runs as $row) {
            $date = (string) $row['pacific_date'];
            if (isset($counts[$date])) {
                $counts[$date] = (int) $row['actions_count'];
                $hasRun[$date] = true;
            }
        }

        [$utcStart] = Simulation::pacificDayUtcRange($startDay->format('Y-m-d'));
        $bucket = static function (string $at) use ($pacific, &$counts, $hasRun, $startDay, $endDay): void {
            if ($at === '') {
                return;
            }
            $date = (new \DateTimeImmutable($at, Simulation::utc()))
                ->setTimezone($pacific)
                ->format('Y-m-d');
            if ($date < $startDay->format('Y-m-d') || $date > $endDay->format('Y-m-d') || isset($hasRun[$date])) {
                return;
            }
            $counts[$date] = ($counts[$date] ?? 0) + 1;
        };

        $timestampQueries = [
            'SELECT p.updated_at AS at FROM projects p JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.updated_at >= ?',
            'SELECT e.created_at AS at FROM exports e
             JOIN projects p ON p.id = e.project_id JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND e.created_at >= ?',
            'SELECT p.pantry_saved_at AS at FROM projects p JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.pantry_saved_at IS NOT NULL AND p.pantry_saved_at >= ?',
            'SELECT u.created_at AS at FROM users u WHERE u.simulation = 1 AND u.created_at >= ?',
        ];
        foreach ($timestampQueries as $sql) {
            foreach (Database::fetchAll($sql, [$utcStart]) as $row) {
                $bucket((string) ($row['at'] ?? ''));
            }
        }

        $max = max($counts) ?: 1;
        $total = array_sum($counts);
        $levelFor = static fn(int $count): int => match (true) {
            $count <= 0           => 0,
            $count <= $max * 0.25 => 1,
            $count <= $max * 0.5  => 2,
            $count <= $max * 0.75 => 3,
            default               => 4,
        };

        $gridStart = $startDay->modify('-' . (int) $startDay->format('w') . ' days');

        $weekGrid = [];
        $monthLabels = [];
        $lastMonth = '';
        $cursor = $gridStart;
        $col = 0;

        while ($cursor <= $endDay) {
            $week = [];
            for ($dow = 0; $dow < 7; $dow++) {
                $dateStr = $cursor->format('Y-m-d');
                if ($cursor < $startDay || $cursor > $endDay) {
                    $week[] = null;
                } else {
                    $count = $counts[$dateStr] ?? 0;
                    $week[] = ['date' => $dateStr, 'count' => $count, 'level' => $levelFor($count)];
                }
                if ($dow === 0 && $cursor >= $startDay && $cursor <= $endDay) {
                    $month = $cursor->format('M');
                    if ($month !== $lastMonth) {
                        $monthLabels[] = ['label' => $month, 'col' => $col];
                        $lastMonth = $month;
                    }
                }
                $cursor = $cursor->modify('+1 day');
            }
            $weekGrid[] = $week;
            $col++;
        }

        return [
            'weeks'         => $weekGrid,
            'max'           => $max,
            'total'         => $total,
            'end'           => $endDay->format('Y-m-d'),
            'month_labels'  => $monthLabels,
        ];
    }

    /** @param array{kind:string, name:string, cookbook_title?:string, detail?:string} $event */
    public static function activityMessage(array $event): array
    {
        $who = self::firstName($event['name']);
        return match ($event['kind']) {
            'completed' => ['prefix' => $who . ' completed', 'emphasis' => (string) ($event['cookbook_title'] ?? ''), 'suffix' => ''],
            'cooking'   => ['prefix' => $who . ' is cooking', 'emphasis' => (string) ($event['cookbook_title'] ?? ''), 'suffix' => (string) ($event['detail'] ?? '')],
            'pantry'    => ['prefix' => $who . ' stocked a Pantry for', 'emphasis' => (string) ($event['cookbook_title'] ?? ''), 'suffix' => ''],
            'joined'    => ['prefix' => $who, 'emphasis' => 'joined the kitchen', 'suffix' => ''],
            default     => ['prefix' => $who, 'emphasis' => 'is active in the kitchen', 'suffix' => ''],
        };
    }

    /** @return array{sim_users:int, projects:int, completed:int, exports:int, approved:int, active_today_pt:int, last_run:?array<string,mixed>} */
    public static function adminBundle(): array
    {
        [$dayStart, $dayEnd] = self::pacificTodayRange();
        $lastRun = Database::fetch('SELECT * FROM simulation_runs ORDER BY pacific_date DESC LIMIT 1');

        return [
            'sim_users'      => (int) Database::fetchValue('SELECT COUNT(*) FROM users WHERE simulation = 1'),
            'projects'       => (int) Database::fetchValue(
                'SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id WHERE u.simulation = 1'
            ),
            'completed'      => (int) Database::fetchValue(
                'SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id WHERE u.simulation = 1 AND p.completed_at IS NOT NULL'
            ),
            'exports'        => (int) Database::fetchValue(
                'SELECT COUNT(*) FROM exports e JOIN projects p ON p.id = e.project_id JOIN users u ON u.id = p.user_id WHERE u.simulation = 1'
            ),
            'approved'       => (int) Database::fetchValue(
                "SELECT COUNT(*) FROM artifacts a JOIN projects p ON p.id = a.project_id JOIN users u ON u.id = p.user_id WHERE u.simulation = 1 AND a.status = 'approved'"
            ),
            'active_today_pt' => (int) Database::fetchValue(
                'SELECT COUNT(DISTINCT p.user_id) FROM projects p JOIN users u ON u.id = p.user_id WHERE u.simulation = 1 AND p.updated_at >= ? AND p.updated_at <= ?',
                [$dayStart, $dayEnd]
            ),
            'last_run'       => $lastRun,
        ];
    }
}
