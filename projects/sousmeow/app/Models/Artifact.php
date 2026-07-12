<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

/**
 * An Artifact is the reviewed output of one Recipe within one Project.
 * Its content lives in immutable artifact_versions rows; edits and
 * restores always append a new version. Quality Check confirmations are
 * recorded against a specific version, so changing the content
 * automatically requires re-review.
 */
final class Artifact
{
    /** @return array<string, mixed>|null */
    public static function find(int $projectId, int $recipeId): ?array
    {
        return Database::fetch(
            'SELECT * FROM artifacts WHERE project_id = ? AND recipe_id = ?',
            [$projectId, $recipeId]
        );
    }

    /**
     * Store a pasted/edited/restored response. Creates the artifact on
     * first paste, then appends an immutable version. Any approval is
     * withdrawn because the reviewed content changed.
     */
    public static function addVersion(int $projectId, int $recipeId, string $content, string $source): int
    {
        return Database::transaction(static function () use ($projectId, $recipeId, $content, $source): int {
            $now = now_utc();
            $artifact = self::find($projectId, $recipeId);
            if ($artifact === null) {
                Database::run(
                    'INSERT INTO artifacts (project_id, recipe_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
                    [$projectId, $recipeId, 'review', $now, $now]
                );
                $artifactId = Database::lastInsertId();
                $versionNo = 1;
            } else {
                $artifactId = (int) $artifact['id'];
                $versionNo = 1 + (int) Database::fetchValue(
                    'SELECT COALESCE(MAX(version_no), 0) FROM artifact_versions WHERE artifact_id = ?',
                    [$artifactId]
                );
                Database::run(
                    'UPDATE artifacts SET status = ?, approved_version_id = NULL, updated_at = ? WHERE id = ?',
                    ['review', $now, $artifactId]
                );
            }
            Database::run(
                'INSERT INTO artifact_versions (artifact_id, version_no, content, source, created_at) VALUES (?, ?, ?, ?, ?)',
                [$artifactId, $versionNo, $content, $source, $now]
            );
            return $artifactId;
        });
    }

    /** @return list<array<string, mixed>> Newest first. */
    public static function versions(int $artifactId): array
    {
        return Database::fetchAll(
            'SELECT id, artifact_id, version_no, source, created_at, LENGTH(content) AS content_length
             FROM artifact_versions WHERE artifact_id = ? ORDER BY version_no DESC',
            [$artifactId]
        );
    }

    /** @return array<string, mixed>|null */
    public static function version(int $artifactId, int $versionNo): ?array
    {
        return Database::fetch(
            'SELECT * FROM artifact_versions WHERE artifact_id = ? AND version_no = ?',
            [$artifactId, $versionNo]
        );
    }

    /** @return array<string, mixed>|null */
    public static function latestVersion(int $artifactId): ?array
    {
        return Database::fetch(
            'SELECT * FROM artifact_versions WHERE artifact_id = ? ORDER BY version_no DESC LIMIT 1',
            [$artifactId]
        );
    }

    /** @return list<int> Confirmed check ids for a version. */
    public static function confirmedCheckIds(int $versionId): array
    {
        $rows = Database::fetchAll(
            'SELECT check_id FROM artifact_checks WHERE version_id = ?',
            [$versionId]
        );
        return array_map(static fn(array $row): int => (int) $row['check_id'], $rows);
    }

    public static function setCheck(int $versionId, int $checkId, bool $confirmed): void
    {
        if ($confirmed) {
            $exists = Database::fetchValue(
                'SELECT id FROM artifact_checks WHERE version_id = ? AND check_id = ?',
                [$versionId, $checkId]
            );
            if ($exists === null) {
                Database::run(
                    'INSERT INTO artifact_checks (version_id, check_id, created_at) VALUES (?, ?, ?)',
                    [$versionId, $checkId, now_utc()]
                );
            }
        } else {
            Database::run(
                'DELETE FROM artifact_checks WHERE version_id = ? AND check_id = ?',
                [$versionId, $checkId]
            );
        }
    }

    public static function approve(int $artifactId, int $versionId): void
    {
        Database::run(
            'UPDATE artifacts SET status = ?, approved_version_id = ?, updated_at = ? WHERE id = ?',
            ['approved', $versionId, now_utc(), $artifactId]
        );
    }

    public static function reopen(int $artifactId): void
    {
        Database::run(
            'UPDATE artifacts SET status = ?, approved_version_id = NULL, updated_at = ? WHERE id = ?',
            ['review', now_utc(), $artifactId]
        );
    }

    public static function approvedCount(int $projectId): int
    {
        return (int) Database::fetchValue(
            "SELECT COUNT(*) FROM artifacts WHERE project_id = ? AND status = 'approved'",
            [$projectId]
        );
    }

    /**
     * Approved artifacts for a project keyed by recipe id, with the
     * approved version's content. Used by prompt chaining and export.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function approvedByRecipe(int $projectId): array
    {
        $rows = Database::fetchAll(
            "SELECT a.recipe_id, a.updated_at AS approved_at, v.content, v.version_no, v.source,
                    r.slug AS recipe_slug, r.title AS recipe_title, r.position AS recipe_position
             FROM artifacts a
             JOIN artifact_versions v ON v.id = a.approved_version_id
             JOIN recipes r ON r.id = a.recipe_id
             WHERE a.project_id = ? AND a.status = 'approved'
             ORDER BY r.position",
            [$projectId]
        );
        $byRecipe = [];
        foreach ($rows as $row) {
            $byRecipe[(int) $row['recipe_id']] = $row;
        }
        return $byRecipe;
    }

    /**
     * Artifact status per recipe id for a project (for the step rail).
     *
     * @return array<int, string> recipe_id to 'review'|'approved'
     */
    public static function statusByRecipe(int $projectId): array
    {
        $rows = Database::fetchAll(
            'SELECT recipe_id, status FROM artifacts WHERE project_id = ?',
            [$projectId]
        );
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['recipe_id']] = (string) $row['status'];
        }
        return $map;
    }

    public static function totalApproved(): int
    {
        return (int) Database::fetchValue("SELECT COUNT(*) FROM artifacts WHERE status = 'approved'");
    }
}
