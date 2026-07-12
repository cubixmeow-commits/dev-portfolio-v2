<?php

declare(strict_types=1);

/**
 * SousMeow database setup and seeding. CLI only; there is deliberately
 * no web-accessible installer.
 *
 * Usage:
 *   php scripts/seed.php                          Apply schema, sync seed
 *                                                 content (upsert by slug),
 *                                                 create the admin account if
 *                                                 missing (prints a temporary
 *                                                 password once).
 *   php scripts/seed.php --admin-email you@x.com  Choose the admin email on
 *                                                 first creation.
 *   php scripts/seed.php --reset-password EMAIL   Generate and print a new
 *                                                 temporary password for an
 *                                                 existing account.
 *   php scripts/seed.php --fresh                  Drop all tables first.
 *                                                 Destroys data; asks for
 *                                                 confirmation.
 *
 * Content sync is safe to re-run: cookbooks, stages, pantry fields, recipes,
 * and quality checks are upserted from database/seeds/content.php. User
 * projects, artifacts, and exports are never deleted. Orphan catalog rows
 * (removed from seed, with no projects or artifact references) are pruned.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;

$args = $argv;
array_shift($args);

$options = [
    'fresh'          => false,
    'admin_email'    => 'chef@sousmeow.example',
    'reset_password' => null,
];
for ($i = 0; $i < count($args); $i++) {
    switch ($args[$i]) {
        case '--fresh':
            $options['fresh'] = true;
            break;
        case '--admin-email':
            $options['admin_email'] = $args[++$i] ?? $options['admin_email'];
            break;
        case '--reset-password':
            $options['reset_password'] = $args[++$i] ?? null;
            break;
        default:
            fwrite(STDERR, "Unknown option: {$args[$i]}\n");
            exit(1);
    }
}

$pdo = Database::pdo();
$driver = Database::driver();

/** Generate a readable temporary password: 4 words + 2 digits. */
function temp_password(): string
{
    $words = ['maple', 'thyme', 'ember', 'clove', 'birch', 'honey', 'cedar', 'plume',
              'saffron', 'juniper', 'cocoa', 'basil', 'amber', 'willow', 'pepper', 'sage'];
    $picks = [];
    foreach (array_rand($words, 4) as $idx) {
        $picks[] = $words[$idx];
    }
    return implode('-', $picks) . '-' . random_int(10, 99);
}

/** @return list<string> */
function table_columns(PDO $pdo, string $driver, string $table): array
{
    if ($driver === 'mysql') {
        $rows = Database::fetchAll(
            'SELECT COLUMN_NAME AS name FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            [$table]
        );
        return array_map(static fn(array $r): string => (string) $r['name'], $rows);
    }
    $rows = Database::fetchAll('PRAGMA table_info(' . $table . ')');
    return array_map(static fn(array $r): string => (string) $r['name'], $rows);
}

function ensure_column(PDO $pdo, string $driver, string $table, string $column, string $definition): void
{
    if (in_array($column, table_columns($pdo, $driver, $table), true)) {
        return;
    }
    $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "Migrated {$table}.{$column}\n";
}

/** Apply additive schema changes on existing databases. */
function migrate_schema(PDO $pdo, string $driver): void
{
    ensure_column($pdo, $driver, 'cookbooks', 'difficulty', "TEXT NOT NULL DEFAULT 'Intermediate'");
    ensure_column($pdo, $driver, 'cookbooks', 'demo_completed_runs', 'INTEGER NOT NULL DEFAULT 0');
    ensure_column($pdo, $driver, 'cookbooks', 'demo_avg_rating', 'REAL');
    ensure_column($pdo, $driver, 'recipes', 'stage_position', 'INTEGER');

    $hasStages = (int) Database::fetchValue(
        $driver === 'mysql'
            ? "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cookbook_stages'"
            : "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = 'cookbook_stages'"
    ) > 0;
    if (!$hasStages) {
        if ($driver === 'mysql') {
            $pdo->exec(<<<'SQL'
CREATE TABLE cookbook_stages (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cookbook_id INT UNSIGNED NOT NULL,
    position    INT UNSIGNED NOT NULL,
    title       VARCHAR(190) NOT NULL,
    summary     VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY uq_stages_pos (cookbook_id, position),
    CONSTRAINT fk_stages_cookbook FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        } else {
            $pdo->exec(<<<'SQL'
CREATE TABLE cookbook_stages (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    cookbook_id INTEGER NOT NULL REFERENCES cookbooks(id) ON DELETE CASCADE,
    position    INTEGER NOT NULL,
    title       TEXT NOT NULL,
    summary     TEXT NOT NULL DEFAULT '',
    UNIQUE (cookbook_id, position)
)
SQL);
        }
        echo "Migrated cookbook_stages table\n";
    }
}

/**
 * Upsert first-party Cookbook content from seed files. Preserves user
 * projects and artifact history; only prunes unused catalog rows.
 *
 * @param array{cookbooks: list<array<string, mixed>>} $content
 * @return array{inserted: int, updated: int, removed: int, executable: int, preview: int}
 */
function sync_content(array $content): array
{
    $stats = ['inserted' => 0, 'updated' => 0, 'removed' => 0, 'executable' => 0, 'preview' => 0];
    $seedSlugs = [];

    Database::transaction(function () use ($content, &$stats, &$seedSlugs): void {
        foreach ($content['cookbooks'] as $book) {
            $slug = (string) $book['slug'];
            $seedSlugs[] = $slug;
            $executable = !empty($book['is_executable']);

            $existing = Database::fetch('SELECT id, created_at FROM cookbooks WHERE slug = ?', [$slug]);
            if ($existing !== null) {
                $cookbookId = (int) $existing['id'];
                Database::run(
                    'UPDATE cookbooks SET title = ?, tagline = ?, description = ?, category = ?, audience = ?,
                                          outcome = ?, price_cents = ?, is_executable = ?, status = ?, accent = ?,
                                          difficulty = ?, est_minutes = ?, demo_completed_runs = ?,
                                          demo_avg_rating = ?, sort_order = ?
                     WHERE id = ?',
                    [
                        $book['title'], $book['tagline'], $book['description'],
                        $book['category'], $book['audience'], $book['outcome'],
                        $book['price_cents'] ?? null,
                        $executable ? 1 : 0,
                        $executable ? 'available' : 'coming_soon',
                        $book['accent'],
                        $book['difficulty'] ?? 'Intermediate',
                        (int) ($book['est_minutes'] ?? 20),
                        (int) ($book['demo_completed_runs'] ?? 0),
                        $book['demo_avg_rating'] ?? null,
                        (int) ($book['sort_order'] ?? 100),
                        $cookbookId,
                    ]
                );
                $stats['updated']++;
            } else {
                $now = now_utc();
                Database::run(
                    'INSERT INTO cookbooks (slug, title, tagline, description, category, audience, outcome,
                                            price_cents, is_executable, status, accent, difficulty, est_minutes,
                                            demo_completed_runs, demo_avg_rating, sort_order, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $slug, $book['title'], $book['tagline'], $book['description'],
                        $book['category'], $book['audience'], $book['outcome'],
                        $book['price_cents'] ?? null,
                        $executable ? 1 : 0,
                        $executable ? 'available' : 'coming_soon',
                        $book['accent'],
                        $book['difficulty'] ?? 'Intermediate',
                        (int) ($book['est_minutes'] ?? 20),
                        (int) ($book['demo_completed_runs'] ?? 0),
                        $book['demo_avg_rating'] ?? null,
                        (int) ($book['sort_order'] ?? 100),
                        $now,
                    ]
                );
                $cookbookId = Database::lastInsertId();
                $stats['inserted']++;
            }

            if ($executable) {
                $stats['executable']++;
            } else {
                $stats['preview']++;
            }

            sync_stages($cookbookId, $book['stages'] ?? []);
            sync_pantry_fields($cookbookId, $book['fields'] ?? []);
            sync_recipes($cookbookId, $book['recipes'], $executable);
        }

        $stats['removed'] = prune_orphan_cookbooks($seedSlugs);
    });

    return $stats;
}

/** @param list<array{title: string, summary?: string}> $stages */
function sync_stages(int $cookbookId, array $stages): void
{
    $keep = [];
    foreach ($stages as $position => $stage) {
        $pos = $position + 1;
        $keep[] = $pos;
        $existing = Database::fetch(
            'SELECT id FROM cookbook_stages WHERE cookbook_id = ? AND position = ?',
            [$cookbookId, $pos]
        );
        if ($existing !== null) {
            Database::run(
                'UPDATE cookbook_stages SET title = ?, summary = ? WHERE id = ?',
                [$stage['title'], $stage['summary'] ?? '', $existing['id']]
            );
        } else {
            Database::run(
                'INSERT INTO cookbook_stages (cookbook_id, position, title, summary) VALUES (?, ?, ?, ?)',
                [$cookbookId, $pos, $stage['title'], $stage['summary'] ?? '']
            );
        }
    }
    if ($keep === []) {
        Database::run('DELETE FROM cookbook_stages WHERE cookbook_id = ?', [$cookbookId]);
        return;
    }
    $placeholders = implode(',', array_fill(0, count($keep), '?'));
    Database::run(
        "DELETE FROM cookbook_stages WHERE cookbook_id = ? AND position NOT IN ({$placeholders})",
        array_merge([$cookbookId], $keep)
    );
}

/** @param list<array<string, mixed>> $fields */
function sync_pantry_fields(int $cookbookId, array $fields): void
{
    $keepKeys = [];
    foreach ($fields as $position => $field) {
        $key = (string) $field['field_key'];
        $keepKeys[] = $key;
        $existing = Database::fetch(
            'SELECT id FROM pantry_fields WHERE cookbook_id = ? AND field_key = ?',
            [$cookbookId, $key]
        );
        if ($existing !== null) {
            Database::run(
                'UPDATE pantry_fields SET position = ?, label = ?, type = ?, help = ?, placeholder = ?,
                                          options = ?, required = ?, sample_value = ?
                 WHERE id = ?',
                [
                    $position + 1,
                    $field['label'],
                    $field['type'],
                    $field['help'] ?? '',
                    $field['placeholder'] ?? '',
                    isset($field['options']) ? json_encode($field['options']) : null,
                    $field['required'] ?? 1,
                    $field['sample_value'] ?? '',
                    $existing['id'],
                ]
            );
        } else {
            Database::run(
                'INSERT INTO pantry_fields (cookbook_id, position, field_key, label, type, help,
                                            placeholder, options, required, sample_value)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $cookbookId, $position + 1, $key, $field['label'], $field['type'],
                    $field['help'] ?? '', $field['placeholder'] ?? '',
                    isset($field['options']) ? json_encode($field['options']) : null,
                    $field['required'] ?? 1, $field['sample_value'] ?? '',
                ]
            );
        }
    }
    if ($keepKeys === []) {
        return;
    }
    $orphans = Database::fetchAll(
        'SELECT pf.id FROM pantry_fields pf
         WHERE pf.cookbook_id = ?
           AND pf.field_key NOT IN (' . implode(',', array_fill(0, count($keepKeys), '?')) . ')
           AND NOT EXISTS (SELECT 1 FROM pantry_values pv WHERE pv.field_id = pf.id)',
        array_merge([$cookbookId], $keepKeys)
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM pantry_fields WHERE id = ?', [$row['id']]);
    }
}

/** @param list<array<string, mixed>> $recipes */
function sync_recipes(int $cookbookId, array $recipes, bool $executable): void
{
    $keepSlugs = [];
    foreach ($recipes as $position => $recipe) {
        $slug = (string) $recipe['slug'];
        $keepSlugs[] = $slug;
        $prompt = $executable ? ($recipe['prompt_template'] ?? null) : null;
        $example = $recipe['example_response'] ?? null;

        $existing = Database::fetch(
            'SELECT id FROM recipes WHERE cookbook_id = ? AND slug = ?',
            [$cookbookId, $slug]
        );
        $row = [
            'stage_position'   => $recipe['stage_position'] ?? null,
            'position'         => $position + 1,
            'title'            => $recipe['title'],
            'summary'          => $recipe['summary'],
            'why_it_matters'   => $recipe['why_it_matters'] ?? '',
            'unlocks_text'     => $recipe['unlocks_text'] ?? '',
            'prompt_template'  => $prompt,
            'example_response' => $example,
            'est_minutes'      => (int) ($recipe['est_minutes'] ?? 5),
        ];
        if ($existing !== null) {
            $recipeId = (int) $existing['id'];
            Database::run(
                'UPDATE recipes SET stage_position = ?, position = ?, title = ?, summary = ?,
                                    why_it_matters = ?, unlocks_text = ?, prompt_template = ?,
                                    example_response = ?, est_minutes = ?
                 WHERE id = ?',
                [
                    $row['stage_position'], $row['position'], $row['title'], $row['summary'],
                    $row['why_it_matters'], $row['unlocks_text'], $row['prompt_template'],
                    $row['example_response'], $row['est_minutes'], $recipeId,
                ]
            );
        } else {
            Database::run(
                'INSERT INTO recipes (cookbook_id, stage_position, position, slug, title, summary,
                                      why_it_matters, unlocks_text, prompt_template, example_response,
                                      est_minutes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $cookbookId, $row['stage_position'], $row['position'], $slug,
                    $row['title'], $row['summary'], $row['why_it_matters'], $row['unlocks_text'],
                    $row['prompt_template'], $row['example_response'], $row['est_minutes'],
                ]
            );
            $recipeId = Database::lastInsertId();
        }
        sync_checks($recipeId, $recipe['checks'] ?? []);
    }

    if ($keepSlugs === []) {
        return;
    }
    $orphans = Database::fetchAll(
        'SELECT r.id FROM recipes r
         WHERE r.cookbook_id = ?
           AND r.slug NOT IN (' . implode(',', array_fill(0, count($keepSlugs), '?')) . ')
           AND NOT EXISTS (SELECT 1 FROM artifacts a WHERE a.recipe_id = r.id)',
        array_merge([$cookbookId], $keepSlugs)
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM recipes WHERE id = ?', [$row['id']]);
    }
}

/** @param list<array{label: string, help?: string}> $checks */
function sync_checks(int $recipeId, array $checks): void
{
    $keepPositions = [];
    foreach ($checks as $checkPos => $check) {
        $pos = $checkPos + 1;
        $keepPositions[] = $pos;
        $existing = Database::fetch(
            'SELECT id FROM recipe_checks WHERE recipe_id = ? AND position = ?',
            [$recipeId, $pos]
        );
        if ($existing !== null) {
            Database::run(
                'UPDATE recipe_checks SET label = ?, help = ? WHERE id = ?',
                [$check['label'], $check['help'] ?? '', $existing['id']]
            );
        } else {
            Database::run(
                'INSERT INTO recipe_checks (recipe_id, position, label, help) VALUES (?, ?, ?, ?)',
                [$recipeId, $pos, $check['label'], $check['help'] ?? '']
            );
        }
    }
    if ($keepPositions === []) {
        return;
    }
    $orphans = Database::fetchAll(
        'SELECT rc.id FROM recipe_checks rc
         WHERE rc.recipe_id = ?
           AND rc.position NOT IN (' . implode(',', array_fill(0, count($keepPositions), '?')) . ')
           AND NOT EXISTS (SELECT 1 FROM artifact_checks ac WHERE ac.check_id = rc.id)',
        array_merge([$recipeId], $keepPositions)
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM recipe_checks WHERE id = ?', [$row['id']]);
    }
}

/** @param list<string> $seedSlugs */
function prune_orphan_cookbooks(array $seedSlugs): int
{
    if ($seedSlugs === []) {
        return 0;
    }
    $orphans = Database::fetchAll(
        'SELECT c.id, c.slug FROM cookbooks c
         WHERE c.slug NOT IN (' . implode(',', array_fill(0, count($seedSlugs), '?')) . ')
           AND NOT EXISTS (SELECT 1 FROM projects p WHERE p.cookbook_id = c.id)',
        $seedSlugs
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM cookbooks WHERE id = ?', [$row['id']]);
        echo "Removed orphan cookbook: {$row['slug']}\n";
    }
    return count($orphans);
}

// --reset-password: standalone maintenance action.
if ($options['reset_password'] !== null) {
    $email = strtolower(trim($options['reset_password']));
    $user = Database::fetch('SELECT id, email FROM users WHERE email = ?', [$email]);
    if ($user === null) {
        fwrite(STDERR, "No account found for {$email}.\n");
        exit(1);
    }
    $password = temp_password();
    Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [
        password_hash($password, PASSWORD_DEFAULT),
        $user['id'],
    ]);
    echo "Temporary password for {$user['email']}: {$password}\n";
    echo "Share it over a safe channel and change it after signing in.\n";
    exit(0);
}

// --fresh: drop everything after an explicit confirmation.
if ($options['fresh']) {
    echo "This drops ALL SousMeow tables and data ({$driver}). Type 'yes' to continue: ";
    $answer = trim((string) fgets(STDIN));
    if ($answer !== 'yes') {
        echo "Aborted.\n";
        exit(0);
    }
    $tables = ['artifact_checks', 'artifact_versions', 'artifacts', 'exports', 'pantry_values',
               'projects', 'pantry_fields', 'recipe_checks', 'recipes', 'cookbook_stages', 'cookbooks',
               'rate_events', 'users'];
    if ($driver === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    }
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS {$table}");
    }
    if ($driver === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
    echo "Dropped existing tables.\n";
}

// 1. Schema.
$schemaFile = __DIR__ . '/../database/schema.' . ($driver === 'mysql' ? 'mysql' : 'sqlite') . '.sql';
$sql = (string) file_get_contents($schemaFile);
$sql = implode("\n", array_filter(
    explode("\n", $sql),
    static fn(string $line): bool => !str_starts_with(ltrim($line), '--')
));
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}
echo "Schema ready ({$driver}).\n";

migrate_schema($pdo, $driver);

// 2. Content sync (upsert; safe to re-run).
/** @var array{cookbooks: list<array<string, mixed>>} $content */
$content = require __DIR__ . '/../database/seeds/content.php';
$stats = sync_content($content);
echo sprintf(
    "Content synced: %d inserted, %d updated, %d orphan cookbooks removed (%d executable, %d preview in seed).\n",
    $stats['inserted'],
    $stats['updated'],
    $stats['removed'],
    $stats['executable'],
    $stats['preview']
);

// 3. Admin account, created once. The temporary password is printed
// exactly once and never stored in plain text.
$adminCount = (int) Database::fetchValue("SELECT COUNT(*) FROM users WHERE role = 'admin'");
if ($adminCount > 0) {
    echo "Admin account already exists, skipping.\n";
} else {
    $email = strtolower(trim($options['admin_email']));
    $password = temp_password();
    Database::run(
        'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)',
        ['Head Chef', $email, password_hash($password, PASSWORD_DEFAULT), 'admin', now_utc()]
    );
    echo "\nAdmin account created.\n";
    echo "  Email:              {$email}\n";
    echo "  Temporary password: {$password}\n";
    echo "Sign in and change this password. It is shown only this once.\n";
}

echo "\nDone. Start the app with:\n  php -S localhost:8090 -t public public/index.php\n";
