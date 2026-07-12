<?php

declare(strict_types=1);

/**
 * Portfolio demo activity generator. Creates simulated chefs, projects,
 * artifacts, and exports for screenshots and the marketing dashboard.
 * Safe to re-run with --fresh-demo to replace demo users only.
 *
 * Usage:
 *   php scripts/seed-demo.php                    350 demo users (default)
 *   php scripts/seed-demo.php --users=500
 *   php scripts/seed-demo.php --fresh-demo       Remove demo+*.local users first
 *   php scripts/seed-demo.php --status             Print aggregate counts (no writes)
 *
 * Prerequisite: php scripts/seed.php (catalog + admin).
 *
 * Demo accounts use email demo+{n}@kitchen.local and password demo-kitchen-2026
 * (documented in README; not printed per user).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;
use SousMeow\Models\Cookbook;
use SousMeow\Models\PantryField;
use SousMeow\Models\Recipe;

const DEMO_EMAIL_DOMAIN = 'kitchen.local';
const DEMO_PASSWORD = 'demo-kitchen-2026';

$options = [
    'users'      => 350,
    'fresh_demo' => false,
    'status'     => false,
];

$args = $argv;
array_shift($args);
for ($i = 0; $i < count($args); $i++) {
    $arg = $args[$i];
    if ($arg === '--fresh-demo') {
        $options['fresh_demo'] = true;
        continue;
    }
    if ($arg === '--status') {
        $options['status'] = true;
        continue;
    }
    if (str_starts_with($arg, '--users=')) {
        $options['users'] = max(1, (int) substr($arg, 8));
        continue;
    }
    if ($arg === '--users') {
        $options['users'] = max(1, (int) ($args[++$i] ?? 350));
        continue;
    }
    fwrite(STDERR, "Unknown option: {$arg}\n");
    exit(1);
}

if ($options['status']) {
    print_status();
    exit(0);
}

$executableSlugs = ['launch-day-kit', 'plan-youtube-video', 'validate-saas-idea'];
$catalog = load_catalog($executableSlugs);
if ($catalog === []) {
    fwrite(STDERR, "No executable cookbooks found. Run php scripts/seed.php first.\n");
    exit(1);
}

if ($options['fresh_demo']) {
    $removed = purge_demo_users();
    fwrite(STDOUT, "Removed {$removed} demo user(s) and cascaded data.\n");
}

$passwordHash = password_hash(DEMO_PASSWORD, PASSWORD_DEFAULT);
$names = demo_names();
$productTitles = demo_product_titles();
$slugWeights = [
    'launch-day-kit'       => 40,
    'plan-youtube-video'   => 30,
    'validate-saas-idea'   => 30,
];

$userCount = $options['users'];
$projectTarget = (int) round($userCount * 1.48);
$stats = [
    'users'     => 0,
    'projects'  => 0,
    'exports'   => 0,
    'artifacts' => 0,
];

fwrite(STDOUT, "Seeding {$userCount} demo chefs (~{$projectTarget} projects)...\n");

Database::transaction(static function () use (
    $userCount,
    $projectTarget,
    $passwordHash,
    $names,
    $productTitles,
    $slugWeights,
    $catalog,
    &$stats
): void {
    $now = time();
    $usersCreated = [];

    for ($i = 1; $i <= $userCount; $i++) {
        $email = 'demo+' . $i . '@' . DEMO_EMAIL_DOMAIN;
        $existing = Database::fetchValue('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing !== null) {
            continue;
        }

        $name = $names[($i - 1) % count($names)];
        if ($i % 4 === 0) {
            $name .= ' ' . chr(65 + ($i % 26)) . '.';
        }
        $createdAt = utc_offset($now, -random_int(1, 90) * 86400 - random_int(0, 43200));

        Database::run(
            'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)',
            [$name, $email, $passwordHash, 'user', $createdAt]
        );
        $usersCreated[] = [
            'id'         => Database::lastInsertId(),
            'name'       => $name,
            'created_at' => $createdAt,
        ];
        $stats['users']++;
    }

    if ($usersCreated === []) {
        fwrite(STDOUT, "No new demo users inserted (all exist). Use --fresh-demo to replace.\n");
        return;
    }

    $projectsPerUser = distribute_projects($usersCreated, $projectTarget);
    $projectIndex = 0;

    foreach ($usersCreated as $user) {
        $count = $projectsPerUser[$projectIndex++] ?? 1;
        for ($p = 0; $p < $count; $p++) {
            $slug = weighted_slug($slugWeights);
            $book = $catalog[$slug];
            $title = $productTitles[($stats['projects'] + $p) % count($productTitles)];
            $userTs = strtotime($user['created_at']);
            $createdAt = clamp_past(utc_offset($userTs, random_int(3600, 45 * 86400)), $now);
            $funnel = pick_funnel();

            Database::run(
                'INSERT INTO projects (user_id, cookbook_id, title, pantry_saved_at, completed_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $user['id'],
                    $book['id'],
                    $title,
                    null,
                    null,
                    $createdAt,
                    $createdAt,
                ]
            );
            $projectId = Database::lastInsertId();
            $stats['projects']++;

            $recipeTotal = count($book['recipes']);
            $approvedTarget = funnel_approved_count($funnel, $recipeTotal);
            $isComplete = $funnel === 'complete';

            if ($funnel === 'draft') {
                if (random_int(1, 100) <= 15) {
                    Database::run(
                        'UPDATE projects SET updated_at = ? WHERE id = ?',
                        [utc_offset($now, -random_int(300, 21600)), $projectId]
                    );
                }
                continue;
            }

            $pantryAt = clamp_past(utc_offset(strtotime($createdAt), random_int(600, 86400)), $now);
            Database::run(
                'UPDATE projects SET pantry_saved_at = ?, updated_at = ? WHERE id = ?',
                [$pantryAt, $pantryAt, $projectId]
            );
            seed_pantry($projectId, $book['fields'], $pantryAt);

            if ($approvedTarget === 0) {
                Database::run(
                    'UPDATE projects SET updated_at = ? WHERE id = ?',
                    [utc_offset($now, -random_int(1800, 86400)), $projectId]
                );
                continue;
            }

            $recipeCursor = 0;
            foreach ($book['recipes'] as $recipe) {
                $recipeCursor++;
                if ($recipeCursor > $approvedTarget) {
                    break;
                }

                $content = truncate_content((string) ($recipe['example_response'] ?? 'Demo response.'));
                $versionAt = clamp_past(utc_offset(strtotime($pantryAt), $recipeCursor * random_int(1800, 14400)), $now);

                Database::run(
                    'INSERT INTO artifacts (project_id, recipe_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
                    [$projectId, $recipe['id'], 'review', $versionAt, $versionAt]
                );
                $artifactId = Database::lastInsertId();

                Database::run(
                    'INSERT INTO artifact_versions (artifact_id, version_no, content, source, created_at) VALUES (?, ?, ?, ?, ?)',
                    [$artifactId, 1, $content, 'example', $versionAt]
                );
                $versionId = Database::lastInsertId();
                $stats['artifacts']++;

                foreach ($recipe['checks'] as $check) {
                    Database::run(
                        'INSERT INTO artifact_checks (version_id, check_id, created_at) VALUES (?, ?, ?)',
                        [$versionId, $check['id'], $versionAt]
                    );
                }

                Database::run(
                    'UPDATE artifacts SET status = ?, approved_version_id = ?, updated_at = ? WHERE id = ?',
                    ['approved', $versionId, $versionAt, $artifactId]
                );
            }

            $lastTouch = clamp_past(utc_offset(strtotime($pantryAt), $approvedTarget * random_int(3600, 28800)), $now);
            if ($isComplete) {
                $completedAt = clamp_past(utc_offset(strtotime($lastTouch), random_int(600, 7200)), $now);
                Database::run(
                    'UPDATE projects SET completed_at = ?, updated_at = ? WHERE id = ?',
                    [$completedAt, $completedAt, $projectId]
                );

                $filename = slugify($title) . '-' . $book['slug'] . '-' . date('Y-m-d', strtotime($completedAt)) . '.zip';
                $fileSize = random_int(48_000, 420_000);
                Database::run(
                    'INSERT INTO exports (project_id, filename, file_size, artifact_count, created_at) VALUES (?, ?, ?, ?, ?)',
                    [$projectId, $filename, $fileSize, $recipeTotal + 2, $completedAt]
                );
                $stats['exports']++;
            } elseif (random_int(1, 100) <= 15) {
                Database::run(
                    'UPDATE projects SET updated_at = ? WHERE id = ?',
                    [utc_offset($now, -random_int(300, 21600)), $projectId]
                );
            } else {
                Database::run(
                    'UPDATE projects SET updated_at = ? WHERE id = ?',
                    [$lastTouch, $projectId]
                );
            }
        }
    }
});

fwrite(STDOUT, sprintf(
    "Demo seed complete: %d users, %d projects, %d approved artifacts, %d exports.\n",
    $stats['users'],
    $stats['projects'],
    $stats['artifacts'],
    $stats['exports']
));
print_status();

/** @param list<string> $slugs @return array<string, array<string, mixed>> */
function load_catalog(array $slugs): array
{
    $catalog = [];
    foreach ($slugs as $slug) {
        $book = Cookbook::findBySlug($slug);
        if ($book === null || (int) $book['is_executable'] !== 1) {
            continue;
        }
        $recipes = Recipe::forCookbook((int) $book['id']);
        foreach ($recipes as &$recipe) {
            $recipe['checks'] = Recipe::checks((int) $recipe['id']);
        }
        unset($recipe);
        $catalog[$slug] = [
            'id'      => (int) $book['id'],
            'slug'    => $slug,
            'title'   => (string) $book['title'],
            'recipes' => $recipes,
            'fields'  => PantryField::forCookbook((int) $book['id']),
        ];
    }
    return $catalog;
}

function purge_demo_users(): int
{
    $count = (int) Database::fetchValue(
        "SELECT COUNT(*) FROM users WHERE email LIKE 'demo+%@" . DEMO_EMAIL_DOMAIN . "'"
    );
    if ($count === 0) {
        return 0;
    }
    Database::run(
        "DELETE FROM users WHERE email LIKE 'demo+%@" . DEMO_EMAIL_DOMAIN . "'"
    );
    return $count;
}

function print_status(): void
{
    $demoUsers = (int) Database::fetchValue(
        "SELECT COUNT(*) FROM users WHERE email LIKE 'demo+%@" . DEMO_EMAIL_DOMAIN . "' AND role = 'user'"
    );
    $allUsers = (int) Database::fetchValue("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $projects = (int) Database::fetchValue('SELECT COUNT(*) FROM projects');
    $completed = (int) Database::fetchValue('SELECT COUNT(*) FROM projects WHERE completed_at IS NOT NULL');
    $exports = (int) Database::fetchValue('SELECT COUNT(*) FROM exports');
    $approved = (int) Database::fetchValue("SELECT COUNT(*) FROM artifacts WHERE status = 'approved'");
    $seedRuns = (int) Database::fetchValue('SELECT COALESCE(SUM(demo_completed_runs), 0) FROM cookbooks');

    fwrite(STDOUT, "\n--- Kitchen activity ---\n");
    fwrite(STDOUT, "Demo chefs:        {$demoUsers}\n");
    fwrite(STDOUT, "All chefs:         {$allUsers}\n");
    fwrite(STDOUT, "Projects:          {$projects}\n");
    fwrite(STDOUT, "Completed:         {$completed}\n");
    fwrite(STDOUT, "Approved recipes:  {$approved}\n");
    fwrite(STDOUT, "Kits exported:     {$exports}\n");
    fwrite(STDOUT, "Seed run baseline: {$seedRuns}\n");
    fwrite(STDOUT, "Display runs:      " . ($completed + $seedRuns) . " (live completed + seed baseline)\n");
}

/** @param list<array<string, mixed>> $users @return list<int> */
function distribute_projects(array $users, int $target): array
{
    $n = count($users);
    if ($n === 0) {
        return [];
    }
    $counts = array_fill(0, $n, 1);
    $remaining = max(0, $target - $n);
    for ($i = 0; $i < $remaining; $i++) {
        $counts[random_int(0, $n - 1)]++;
    }
    return $counts;
}

/** @param array<string, int> $weights */
function weighted_slug(array $weights): string
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

function pick_funnel(): string
{
    $roll = random_int(1, 100);
    if ($roll <= 15) {
        return 'draft';
    }
    if ($roll <= 40) {
        return 'early';
    }
    if ($roll <= 60) {
        return 'mid';
    }
    if ($roll <= 80) {
        return 'late';
    }
    return 'complete';
}

function funnel_approved_count(string $funnel, int $recipeTotal): int
{
    if ($recipeTotal === 0) {
        return 0;
    }
    return match ($funnel) {
        'draft'    => 0,
        'early'    => max(1, (int) ceil($recipeTotal * random_int(5, 30) / 100)),
        'mid'      => max(1, (int) ceil($recipeTotal * random_int(31, 70) / 100)),
        'late'     => max(1, (int) ceil($recipeTotal * random_int(71, 99) / 100)),
        'complete' => $recipeTotal,
        default    => 0,
    };
}

/** @param list<array<string, mixed>> $fields */
function seed_pantry(int $projectId, array $fields, string $savedAt): void
{
    foreach ($fields as $field) {
        $value = (string) ($field['sample_value'] ?? '');
        if ($value === '') {
            continue;
        }
        Database::run(
            'INSERT INTO pantry_values (project_id, field_id, value) VALUES (?, ?, ?)',
            [$projectId, $field['id'], $value]
        );
    }
}

function truncate_content(string $content, int $max = 12_000): string
{
    if (strlen($content) <= $max) {
        return $content;
    }
    return substr($content, 0, $max) . "\n\n…";
}

function clamp_past(string $datetime, int $maxTs): string
{
    $ts = strtotime($datetime);
    if ($ts === false || $ts > $maxTs) {
        return gmdate('Y-m-d H:i:s', $maxTs - random_int(300, 86400));
    }
    return $datetime;
}

function utc_offset(int $baseTs, int $seconds): string
{
    return gmdate('Y-m-d H:i:s', $baseTs + $seconds);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? 'project';
    return trim($text, '-') ?: 'project';
}

/** @return list<string> */
function demo_names(): array
{
    return [
        'Mira', 'James', 'Priya', 'Elena', 'Marcus', 'Sofia', 'Chen', 'Aisha', 'Noah', 'Lily',
        'Omar', 'Greta', 'Felix', 'Yuki', 'Amara', 'Leo', 'Nina', 'Diego', 'Hannah', 'Kwame',
        'Rosa', 'Theo', 'Ingrid', 'Raj', 'Clara', 'Mateo', 'Zoe', 'Elliot', 'Mei', 'Jonas',
        'Ava', 'Samir', 'Freya', 'Luca', 'Nadia', 'Owen', 'Sana', 'Viktor', 'Isla', 'Andre',
        'Keiko', 'Rafael', 'Tessa', 'Malik', 'Bronwyn', 'Henrik', 'Paloma', 'Darius', 'Linnea', 'Caleb',
    ];
}

/** @return list<string> */
function demo_product_titles(): array
{
    return [
        'Driftlog', 'Taskloom', 'Clearcast', 'Northline', 'Patchwork', 'Harborlight', 'Sidekick OS',
        'Meridian', 'Papertrail', 'Brightloom', 'Fieldnote', 'Stackwell', 'Openframe', 'Rivermark',
        'Signalhouse', 'Craftlane', 'Bluehour', 'Nestform', 'Waypoint', 'Threadline', 'Launchpad X',
        'Studio Nine', 'Copperline', 'Daybreak', 'Foxglove', 'Kitehouse', 'Moonjar', 'Pinecode',
        'Quillbox', 'Relaykit', 'Sunroom', 'Tidewalk', 'Uplift', 'Vellum', 'Wildgrain',
    ];
}
