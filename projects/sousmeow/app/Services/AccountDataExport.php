<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Config;
use SousMeow\Core\Database;
use SousMeow\Models\Export;
use SousMeow\Models\Project;
use SousMeow\Models\User;
use ZipArchive;

/**
 * Export a user's personal data as a ZIP of JSON and Markdown files.
 */
final class AccountDataExport
{
    public static function createZip(int $userId): ?string
    {
        $user = User::find($userId);
        if ($user === null) {
            return null;
        }

        $exportDir = Config::string('exports.dir') . '/account-exports';
        if (!is_dir($exportDir) && !mkdir($exportDir, 0775, true) && !is_dir($exportDir)) {
            return null;
        }

        $tmpDir = sys_get_temp_dir() . '/sousmeow-export-' . $userId . '-' . bin2hex(random_bytes(4));
        if (!mkdir($tmpDir, 0700) && !is_dir($tmpDir)) {
            return null;
        }

        try {
            $profile = self::sanitizeUser($user);
            file_put_contents($tmpDir . '/profile.json', json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            file_put_contents($tmpDir . '/profile.md', self::profileMarkdown($profile));

            $projects = self::projectsData($userId);
            file_put_contents($tmpDir . '/projects.json', json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $zipPath = $exportDir . '/sousmeow-data-' . $userId . '-' . gmdate('Ymd-His') . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return null;
            }
            foreach (glob($tmpDir . '/*') ?: [] as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            return $zipPath;
        } finally {
            foreach (glob($tmpDir . '/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($tmpDir);
        }
    }

    /** @param array<string, mixed> $user */
    private static function sanitizeUser(array $user): array
    {
        unset(
            $user['password_hash'],
            $user['verification_token_hash'],
            $user['verification_expires_at'],
            $user['pending_email_token_hash'],
            $user['pending_email_expires_at']
        );
        return $user;
    }

    /** @param array<string, mixed> $profile */
    private static function profileMarkdown(array $profile): string
    {
        $lines = ["# SousMeow account export", '', '## Profile', ''];
        foreach (['name', 'email', 'bio', 'website', 'preferred_ai', 'ai_experience_level', 'timezone', 'created_at'] as $key) {
            if (!empty($profile[$key])) {
                $lines[] = '- **' . ucfirst(str_replace('_', ' ', $key)) . ':** ' . $profile[$key];
            }
        }
        return implode("\n", $lines) . "\n";
    }

    /** @return list<array<string, mixed>> */
    private static function projectsData(int $userId): array
    {
        $projects = Database::fetchAll(
            'SELECT p.*, c.title AS cookbook_title FROM projects p
             INNER JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.user_id = ? ORDER BY p.id',
            [$userId]
        );

        $result = [];
        foreach ($projects as $project) {
            $projectId = (int) $project['id'];
            $pantry = Project::pantryValues($projectId);
            $exports = Export::forProject($projectId);

            $artifacts = Database::fetchAll(
                'SELECT a.*, r.title AS recipe_title FROM artifacts a
                 INNER JOIN recipes r ON r.id = a.recipe_id
                 WHERE a.project_id = ?',
                [$projectId]
            );

            foreach ($artifacts as &$artifact) {
                $versions = Database::fetchAll(
                    'SELECT id, version_no, content, source, created_at FROM artifact_versions WHERE artifact_id = ? ORDER BY version_no',
                    [$artifact['id']]
                );
                foreach ($versions as &$version) {
                    $checks = Database::fetchAll(
                        'SELECT rc.label FROM artifact_checks ac
                         INNER JOIN recipe_checks rc ON rc.id = ac.check_id
                         WHERE ac.version_id = ?',
                        [$version['id']]
                    );
                    $version['quality_confirmations'] = array_column($checks, 'label');
                }
                $artifact['versions'] = $versions;
            }

            $result[] = [
                'project'   => $project,
                'pantry'    => $pantry,
                'artifacts' => $artifacts,
                'exports'   => $exports,
            ];
        }

        return $result;
    }
}
