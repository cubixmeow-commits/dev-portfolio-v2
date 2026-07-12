<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class Export
{
    public static function record(int $projectId, string $filename, int $fileSize, int $artifactCount): int
    {
        Database::run(
            'INSERT INTO exports (project_id, filename, file_size, artifact_count, created_at) VALUES (?, ?, ?, ?, ?)',
            [$projectId, $filename, $fileSize, $artifactCount, now_utc()]
        );
        return Database::lastInsertId();
    }

    /** @return list<array<string, mixed>> */
    public static function forProject(int $projectId): array
    {
        return Database::fetchAll(
            'SELECT * FROM exports WHERE project_id = ? ORDER BY id DESC',
            [$projectId]
        );
    }

    /**
     * Fetch an export only when the requesting user owns its project.
     *
     * @return array<string, mixed>|null
     */
    public static function findForUser(int $id, int $userId): ?array
    {
        return Database::fetch(
            'SELECT e.*, p.title AS project_title
             FROM exports e JOIN projects p ON p.id = e.project_id
             WHERE e.id = ? AND p.user_id = ?',
            [$id, $userId]
        );
    }

    public static function count(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM exports');
    }
}
