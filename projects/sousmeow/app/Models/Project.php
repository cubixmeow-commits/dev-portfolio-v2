<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class Project
{
    public static function create(int $userId, int $cookbookId, string $title): int
    {
        $now = now_utc();
        Database::run(
            'INSERT INTO projects (user_id, cookbook_id, title, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
            [$userId, $cookbookId, trim($title), $now, $now]
        );
        return Database::lastInsertId();
    }

    /**
     * Fetch a project only if it belongs to the given user. Every
     * project route goes through this, so ownership is enforced at the
     * data access layer, not in templates.
     *
     * @return array<string, mixed>|null
     */
    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT p.*, c.slug AS cookbook_slug, c.title AS cookbook_title, c.accent AS cookbook_accent
             FROM projects p JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.id = ? AND p.user_id = ?',
            [$id, $userId]
        );
    }

    /**
     * All projects for the Kitchen dashboard, with recipe and approval
     * counts so progress renders without N+1 queries.
     *
     * @return list<array<string, mixed>>
     */
    public static function listForUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.title AS cookbook_title, c.accent AS cookbook_accent,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id) AS recipe_count,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS approved_count
             FROM projects p JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.user_id = ?
             ORDER BY p.updated_at DESC",
            [$userId]
        );
    }

    public static function touch(int $id): void
    {
        Database::run('UPDATE projects SET updated_at = ? WHERE id = ?', [now_utc(), $id]);
    }

    public static function markPantrySaved(int $id): void
    {
        Database::run(
            'UPDATE projects SET pantry_saved_at = ?, updated_at = ? WHERE id = ?',
            [now_utc(), now_utc(), $id]
        );
    }

    public static function markCompleteIfDone(int $id): bool
    {
        $project = Database::fetch('SELECT cookbook_id, completed_at FROM projects WHERE id = ?', [$id]);
        if ($project === null || $project['completed_at'] !== null) {
            return false;
        }
        $total = Recipe::countForCookbook((int) $project['cookbook_id']);
        $approved = Artifact::approvedCount($id);
        if ($total > 0 && $approved >= $total) {
            Database::run(
                'UPDATE projects SET completed_at = ?, updated_at = ? WHERE id = ?',
                [now_utc(), now_utc(), $id]
            );
            return true;
        }
        return false;
    }

    public static function reopen(int $id): void
    {
        Database::run('UPDATE projects SET completed_at = NULL, updated_at = ? WHERE id = ?', [now_utc(), $id]);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM projects WHERE id = ?', [$id]);
    }

    /**
     * Pantry values keyed by field id.
     *
     * @return array<int, string>
     */
    public static function pantryValues(int $projectId): array
    {
        $rows = Database::fetchAll(
            'SELECT field_id, value FROM pantry_values WHERE project_id = ?',
            [$projectId]
        );
        $values = [];
        foreach ($rows as $row) {
            $values[(int) $row['field_id']] = (string) $row['value'];
        }
        return $values;
    }

    /** @param array<int, string> $values Field id to value. */
    public static function savePantry(int $projectId, array $values): void
    {
        Database::transaction(static function () use ($projectId, $values): void {
            foreach ($values as $fieldId => $value) {
                $existing = Database::fetchValue(
                    'SELECT id FROM pantry_values WHERE project_id = ? AND field_id = ?',
                    [$projectId, $fieldId]
                );
                if ($existing !== null) {
                    Database::run('UPDATE pantry_values SET value = ? WHERE id = ?', [$value, $existing]);
                } else {
                    Database::run(
                        'INSERT INTO pantry_values (project_id, field_id, value) VALUES (?, ?, ?)',
                        [$projectId, $fieldId, $value]
                    );
                }
            }
        });
    }

    public static function count(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM projects');
    }

    public static function completedCount(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM projects WHERE completed_at IS NOT NULL');
    }
}
