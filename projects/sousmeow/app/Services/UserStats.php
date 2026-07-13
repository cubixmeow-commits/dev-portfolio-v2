<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;

/**
 * Lightweight account statistics from existing records. Simulated activity
 * is excluded when the user is a real account.
 */
final class UserStats
{
    /** @return array<string, mixed> */
    public static function forUser(int $userId, bool $isSimulation): array
    {
        $memberSince = Database::fetchValue('SELECT created_at FROM users WHERE id = ?', [$userId]);

        $activeProjects = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects WHERE user_id = ? AND completed_at IS NULL',
            [$userId]
        );

        $completedProjects = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects WHERE user_id = ? AND completed_at IS NOT NULL',
            [$userId]
        );

        $stepsCompleted = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM artifacts a
             INNER JOIN projects p ON p.id = a.project_id
             WHERE p.user_id = ? AND a.status = ?',
            [$userId, 'approved']
        );

        $kitsExported = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM exports e
             INNER JOIN projects p ON p.id = e.project_id
             WHERE p.user_id = ?',
            [$userId]
        );

        $mostUsed = Database::fetch(
            'SELECT c.title, COUNT(*) AS cnt FROM projects p
             INNER JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.user_id = ?
             GROUP BY p.cookbook_id ORDER BY cnt DESC LIMIT 1',
            [$userId]
        );

        $totalRecipes = (int) Database::fetchValue(
            'SELECT COALESCE(SUM(rc.cnt), 0) FROM (
                SELECT COUNT(*) AS cnt FROM recipes r
                INNER JOIN projects p ON p.cookbook_id = r.cookbook_id
                WHERE p.user_id = ?
             ) rc',
            [$userId]
        );

        $completionRate = null;
        if ($totalRecipes > 0 && $stepsCompleted > 0) {
            $completionRate = min(100, (int) round(($stepsCompleted / $totalRecipes) * 100));
        }

        return [
            'member_since'       => $memberSince,
            'active_projects'    => $activeProjects,
            'completed_projects' => $completedProjects,
            'steps_completed'    => $stepsCompleted,
            'kits_exported'      => $kitsExported,
            'most_used_workflow' => $mostUsed['title'] ?? null,
            'completion_rate'    => $completionRate,
            'is_simulation'      => $isSimulation,
        ];
    }
}
