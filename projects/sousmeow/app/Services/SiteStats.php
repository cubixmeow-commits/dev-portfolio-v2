<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;

/**
 * Public-facing aggregates for the marketing dashboard. Blends live
 * database counts with seeded cookbook demo metrics for portfolio impact.
 */
final class SiteStats
{
    /** @return array{chefs:int, kits:int, runs:int, rating:float, recipes:int, cookbooks:int} */
    public static function hero(): array
    {
        $chefs = (int) Database::fetchValue("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $kits = (int) Database::fetchValue('SELECT COUNT(*) FROM exports');
        $liveCompleted = (int) Database::fetchValue('SELECT COUNT(*) FROM projects WHERE completed_at IS NOT NULL');
        $seedRuns = (int) Database::fetchValue('SELECT COALESCE(SUM(demo_completed_runs), 0) FROM cookbooks');
        $ratingRow = Database::fetch(
            'SELECT
                SUM(demo_completed_runs * demo_avg_rating) AS weighted,
                SUM(demo_completed_runs) AS weight
             FROM cookbooks
             WHERE demo_avg_rating IS NOT NULL AND demo_completed_runs > 0'
        );
        $weight = (int) ($ratingRow['weight'] ?? 0);
        $rating = $weight > 0
            ? round((float) $ratingRow['weighted'] / $weight, 1)
            : 4.7;
        $recipes = (int) Database::fetchValue('SELECT COUNT(*) FROM recipes');
        $cookbooks = (int) Database::fetchValue('SELECT COUNT(*) FROM cookbooks');

        return [
            'chefs'     => $chefs,
            'kits'      => $kits,
            'runs'      => $liveCompleted + $seedRuns,
            'rating'    => $rating,
            'recipes'   => $recipes,
            'cookbooks' => $cookbooks,
        ];
    }

    /**
     * Top cookbooks by live project count plus seed demo runs.
     *
     * @return list<array<string, mixed>>
     */
    public static function popularCookbooks(int $limit = 3): array
    {
        $limit = max(1, $limit);
        return Database::fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count,
                    (SELECT COUNT(*) FROM projects p WHERE p.cookbook_id = c.id) AS live_projects,
                    (COALESCE(c.demo_completed_runs, 0) + (SELECT COUNT(*) FROM projects p WHERE p.cookbook_id = c.id AND p.completed_at IS NOT NULL)) AS display_runs
             FROM cookbooks c
             ORDER BY display_runs DESC, c.sort_order
             LIMIT {$limit}"
        );
    }

    /**
     * Mixed activity feed for the kitchen ticker.
     *
     * @return list<array{kind:string, at:string, name:string, cookbook_title?:string, detail?:string}>
     */
    public static function recentActivity(int $limit = 10): array
    {
        $events = [];

        $completions = Database::fetchAll(
            "SELECT p.completed_at AS at, u.name, c.title AS cookbook_title
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.completed_at IS NOT NULL
             ORDER BY p.completed_at DESC
             LIMIT 6"
        );
        foreach ($completions as $row) {
            $events[] = [
                'kind'           => 'completed',
                'at'             => (string) $row['at'],
                'name'           => (string) $row['name'],
                'cookbook_title' => (string) $row['cookbook_title'],
            ];
        }

        $cooking = Database::fetchAll(
            "SELECT p.updated_at AS at, u.name, c.title AS cookbook_title,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS approved_count,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id) AS recipe_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL
             ORDER BY p.updated_at DESC
             LIMIT 6"
        );
        foreach ($cooking as $row) {
            $approved = (int) $row['approved_count'];
            $total = (int) $row['recipe_count'];
            if ($approved === 0 || $total === 0) {
                continue;
            }
            $events[] = [
                'kind'           => 'cooking',
                'at'             => (string) $row['at'],
                'name'           => (string) $row['name'],
                'cookbook_title' => (string) $row['cookbook_title'],
                'detail'         => 'Recipe ' . min($approved + 1, $total) . ' of ' . $total,
            ];
        }

        $pantries = Database::fetchAll(
            "SELECT p.pantry_saved_at AS at, u.name, c.title AS cookbook_title
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.pantry_saved_at IS NOT NULL
             ORDER BY p.pantry_saved_at DESC
             LIMIT 4"
        );
        foreach ($pantries as $row) {
            $events[] = [
                'kind'           => 'pantry',
                'at'             => (string) $row['at'],
                'name'           => (string) $row['name'],
                'cookbook_title' => (string) $row['cookbook_title'],
            ];
        }

        $joins = Database::fetchAll(
            "SELECT created_at AS at, name
             FROM users
             WHERE role = 'user'
             ORDER BY created_at DESC
             LIMIT 4"
        );
        foreach ($joins as $row) {
            $events[] = [
                'kind' => 'joined',
                'at'   => (string) $row['at'],
                'name' => (string) $row['name'],
            ];
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

    /** @param array{kind:string, name:string, cookbook_title?:string, detail?:string} $event */
    public static function activityMessage(array $event): array
    {
        $who = self::firstName($event['name']);
        return match ($event['kind']) {
            'completed' => [
                'prefix'   => $who . ' completed',
                'emphasis' => (string) ($event['cookbook_title'] ?? ''),
                'suffix'   => '',
            ],
            'cooking' => [
                'prefix'   => $who . ' is cooking',
                'emphasis' => (string) ($event['cookbook_title'] ?? ''),
                'suffix'   => (string) ($event['detail'] ?? ''),
            ],
            'pantry' => [
                'prefix'   => $who . ' stocked a Pantry for',
                'emphasis' => (string) ($event['cookbook_title'] ?? ''),
                'suffix'   => '',
            ],
            'joined' => [
                'prefix'   => $who,
                'emphasis' => 'joined the kitchen',
                'suffix'   => '',
            ],
            default => [
                'prefix'   => $who,
                'emphasis' => 'is active in the kitchen',
                'suffix'   => '',
            ],
        };
    }
}
