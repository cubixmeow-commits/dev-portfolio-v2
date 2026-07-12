<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;
use SousMeow\Models\Artifact;
use SousMeow\Models\Cookbook;
use SousMeow\Models\Export;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;
use SousMeow\Models\User;

/**
 * Executes simulated kitchen actions through the same models as the web app.
 */
final class SimulationKitchen
{
    /** @var array<string, array<string, mixed>> */
    private static array $catalog = [];

    /** @return array<string, array<string, mixed>> */
    public static function catalog(): array
    {
        if (self::$catalog !== []) {
            return self::$catalog;
        }
        foreach (['launch-day-kit', 'plan-youtube-video', 'validate-saas-idea'] as $slug) {
            $book = Cookbook::findBySlug($slug);
            if ($book === null || (int) $book['is_executable'] !== 1) {
                continue;
            }
            $recipes = Recipe::forCookbook((int) $book['id']);
            foreach ($recipes as &$recipe) {
                $recipe['checks'] = Recipe::checks((int) $recipe['id']);
            }
            unset($recipe);
            self::$catalog[$slug] = [
                'id'      => (int) $book['id'],
                'slug'    => $slug,
                'title'   => (string) $book['title'],
                'recipes' => $recipes,
                'fields'  => PantryField::forCookbook((int) $book['id']),
            ];
        }
        if (self::$catalog === []) {
            throw new \RuntimeException('No executable cookbooks. Run php scripts/seed.php first.');
        }
        return self::$catalog;
    }

    /** @param array<string, int> $weights */
    public static function pickCookbookSlug(array $weights): string
    {
        $total = array_sum($weights);
        $roll = random_int(1, $total);
        $cursor = 0;
        foreach ($weights as $slug => $weight) {
            $cursor += $weight;
            if ($roll <= $cursor) {
                return $slug;
            }
        }
        return array_key_first($weights);
    }

    public static function createProject(int $userId, string $title, string $slug, string $createdUtc): int
    {
        $book = self::catalog()[$slug];
        $projectId = Project::create($userId, $book['id'], $title);
        Database::run(
            'UPDATE projects SET created_at = ?, updated_at = ? WHERE id = ?',
            [$createdUtc, $createdUtc, $projectId]
        );
        return $projectId;
    }

    public static function stockPantry(int $projectId, string $slug, string $savedUtc): void
    {
        $fields = self::catalog()[$slug]['fields'];
        $values = [];
        foreach ($fields as $field) {
            $sample = (string) ($field['sample_value'] ?? '');
            if ($sample !== '') {
                $values[(int) $field['id']] = $sample;
            }
        }
        Project::savePantry($projectId, $values);
        Project::markPantrySaved($projectId);
        Database::run(
            'UPDATE projects SET pantry_saved_at = ?, updated_at = ? WHERE id = ?',
            [$savedUtc, $savedUtc, $projectId]
        );
    }

    public static function approveRecipes(int $projectId, string $slug, int $count, string $lastUtc): string
    {
        $book = self::catalog()[$slug];
        $cursor = 0;
        $touch = $lastUtc;
        foreach ($book['recipes'] as $recipe) {
            $cursor++;
            if ($cursor > $count) {
                break;
            }
            $content = self::truncate((string) ($recipe['example_response'] ?? 'Simulated response.'));
            $artifactId = Artifact::addVersion($projectId, (int) $recipe['id'], $content, 'example');
            $latest = Artifact::latestVersion($artifactId);
            if ($latest === null) {
                continue;
            }
            $versionId = (int) $latest['id'];
            foreach ($recipe['checks'] as $check) {
                Artifact::setCheck($versionId, (int) $check['id'], true);
            }
            Artifact::approve($artifactId, $versionId);
            Database::run(
                'UPDATE artifacts SET created_at = ?, updated_at = ? WHERE id = ?',
                [$touch, $touch, $artifactId]
            );
            Database::run('UPDATE artifact_versions SET created_at = ? WHERE id = ?', [$touch, $versionId]);
            Database::run('UPDATE artifact_checks SET created_at = ? WHERE version_id = ?', [$touch, $versionId]);
        }
        Database::run('UPDATE projects SET updated_at = ? WHERE id = ?', [$touch, $projectId]);
        return $touch;
    }

    public static function completeProject(int $projectId, string $slug, string $completedUtc): void
    {
        $book = self::catalog()[$slug];
        $total = count($book['recipes']);
        self::approveRecipes($projectId, $slug, $total, $completedUtc);
        Project::markCompleteIfDone($projectId);
        Database::run(
            'UPDATE projects SET completed_at = ?, updated_at = ? WHERE id = ?',
            [$completedUtc, $completedUtc, $projectId]
        );
        $project = Database::fetch('SELECT title FROM projects WHERE id = ?', [$projectId]);
        $title = (string) ($project['title'] ?? 'project');
        $filename = self::slugify($title) . '-' . $slug . '-' . substr($completedUtc, 0, 10) . '.zip';
        Export::record($projectId, $filename, random_int(48_000, 420_000), $total + 2);
        $exportId = Database::lastInsertId();
        Database::run('UPDATE exports SET created_at = ? WHERE id = ?', [$completedUtc, $exportId]);
    }

    /** @return array<string, mixed>|null */
    public static function randomOpenProject(int $userId): ?array
    {
        $order = Database::driver() === 'mysql' ? 'RAND()' : 'RANDOM()';
        return Database::fetch(
            "SELECT p.*, c.slug AS cookbook_slug
             FROM projects p JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE p.user_id = ? AND p.completed_at IS NULL
             ORDER BY {$order} LIMIT 1",
            [$userId]
        );
    }

    public static function approvedCount(int $projectId): int
    {
        return (int) Database::fetchValue(
            "SELECT COUNT(*) FROM artifacts WHERE project_id = ? AND status = 'approved'",
            [$projectId]
        );
    }

    public static function recipeTotal(string $slug): int
    {
        return count(self::catalog()[$slug]['recipes']);
    }

    private static function truncate(string $content, int $max = 12_000): string
    {
        if (strlen($content) <= $max) {
            return $content;
        }
        return substr($content, 0, $max) . "\n\n…";
    }

    private static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? 'project';
        return trim($text, '-') ?: 'project';
    }
}
