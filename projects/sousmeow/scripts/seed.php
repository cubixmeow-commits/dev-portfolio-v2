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
 *   php scripts/seed.php --status                 Print cookbook catalog health
 *                                                 (no writes). Use after deploy
 *                                                 to confirm sync worked.
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
use SousMeow\Services\Accent;

$args = $argv;
array_shift($args);

$options = [
    'fresh'          => false,
    'status'         => false,
    'admin_email'    => 'chef@sousmeow.example',
    'reset_password' => null,
];
for ($i = 0; $i < count($args); $i++) {
    switch ($args[$i]) {
        case '--fresh':
            $options['fresh'] = true;
            break;
        case '--status':
            $options['status'] = true;
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
    try {
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        echo "Migrated {$table}.{$column}\n";
    } catch (\PDOException $e) {
        fwrite(STDERR, "Migration failed for {$table}.{$column}: {$e->getMessage()}\n");
        throw $e;
    }
}

/** Column DDL matched to schema.mysql.sql / schema.sqlite.sql per driver. */
function column_definition(string $driver, string $column): string
{
    if ($driver === 'mysql') {
        return match ($column) {
            'primary_category_id'      => 'INT UNSIGNED NULL',
            'difficulty'               => "VARCHAR(20) NOT NULL DEFAULT 'Intermediate'",
            'demo_completed_runs'        => 'INT UNSIGNED NOT NULL DEFAULT 0',
            'demo_avg_rating'          => 'DECIMAL(2,1) NULL',
            'stage_position'           => 'INT UNSIGNED NULL',
            'simulation'               => 'TINYINT(1) NOT NULL DEFAULT 0',
            'output_contract'          => 'MEDIUMTEXT NULL',
            'evidence_keys'            => 'TEXT NULL',
            'before_you_begin'         => 'TEXT NULL',
            'common_problems'          => 'TEXT NULL',
            'recovery_guidance'        => 'TEXT NULL',
            'email_verified_at'        => 'DATETIME NULL',
            'verification_token_hash'  => 'VARCHAR(64) NULL',
            'verification_expires_at'  => 'DATETIME NULL',
            'verification_sent_at'     => 'DATETIME NULL',
            'pending_email'            => 'VARCHAR(190) NULL',
            'pending_email_token_hash' => 'VARCHAR(64) NULL',
            'pending_email_expires_at' => 'DATETIME NULL',
            'password_changed_at'      => 'DATETIME NULL',
            'onboarding_completed_at'  => 'DATETIME NULL',
            'bio'                      => 'VARCHAR(280) NULL',
            'website'                  => 'VARCHAR(255) NULL',
            'avatar_url'               => 'VARCHAR(255) NULL',
            'preferred_ai'             => 'VARCHAR(30) NULL',
            'ai_experience_level'      => 'VARCHAR(20) NULL',
            'timezone'                 => 'VARCHAR(64) NULL',
            'theme_preference'         => "VARCHAR(10) NULL DEFAULT 'system'",
            default                    => throw new \InvalidArgumentException("Unknown column: {$column}"),
        };
    }

    return match ($column) {
        'primary_category_id'      => 'INTEGER NULL',
        'difficulty'               => "TEXT NOT NULL DEFAULT 'Intermediate'",
        'demo_completed_runs'      => 'INTEGER NOT NULL DEFAULT 0',
        'demo_avg_rating'          => 'REAL',
        'stage_position'           => 'INTEGER',
        'simulation'               => 'INTEGER NOT NULL DEFAULT 0',
            'output_contract'          => 'TEXT',
            'evidence_keys'            => 'TEXT',
            'before_you_begin'         => "TEXT NOT NULL DEFAULT ''",
            'common_problems'          => "TEXT NOT NULL DEFAULT ''",
            'recovery_guidance'        => "TEXT NOT NULL DEFAULT ''",
            'email_verified_at'        => 'TEXT NULL',
        'verification_token_hash'  => 'TEXT NULL',
        'verification_expires_at'  => 'TEXT NULL',
        'verification_sent_at'     => 'TEXT NULL',
        'pending_email'            => 'TEXT NULL',
        'pending_email_token_hash' => 'TEXT NULL',
        'pending_email_expires_at' => 'TEXT NULL',
        'password_changed_at'      => 'TEXT NULL',
        'onboarding_completed_at'  => 'TEXT NULL',
        'bio'                      => 'TEXT NULL',
        'website'                  => 'TEXT NULL',
        'avatar_url'               => 'TEXT NULL',
        'preferred_ai'             => 'TEXT NULL',
        'ai_experience_level'      => 'TEXT NULL',
        'timezone'                 => 'TEXT NULL',
        'theme_preference'         => "TEXT NULL DEFAULT 'system'",
        default                    => throw new \InvalidArgumentException("Unknown column: {$column}"),
    };
}

/** Create an index if it does not already exist, in either dialect. */
function ensure_index(PDO $pdo, string $driver, string $index, string $table, string $columns): void
{
    if ($driver === 'mysql') {
        $exists = (int) Database::fetchValue(
            "SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$table, $index]
        ) > 0;
        if (!$exists) {
            $pdo->exec("CREATE INDEX {$index} ON {$table} {$columns}");
            echo "Migrated index {$index}\n";
        }
        return;
    }
    $pdo->exec("CREATE INDEX IF NOT EXISTS {$index} ON {$table} {$columns}");
}

/** Apply additive schema changes on existing databases. */
function migrate_schema(PDO $pdo, string $driver): void
{
    // Discovery taxonomy: the sousmeow_categories/sousmeow_collections/
    // sousmeow_cookbook_collections tables are created by the schema
    // file's CREATE TABLE IF NOT EXISTS on every run, so they already
    // exist by the time we get here. What the schema file cannot retrofit
    // onto an existing cookbooks table is the primary_category_id column,
    // its index, and (MySQL) its foreign key.
    ensure_column($pdo, $driver, 'cookbooks', 'primary_category_id', column_definition($driver, 'primary_category_id'));
    ensure_index($pdo, $driver, 'idx_cookbooks_primary_category', 'cookbooks', '(primary_category_id)');
    // SQLite cannot ALTER-add a foreign key, so an upgraded SQLite dev DB
    // gets the column + index only; fresh installs get the full FK. On
    // MySQL (the production target) we add the FK to existing tables too.
    if ($driver === 'mysql') {
        // A database shared with other apps can already have a table
        // literally named "categories" that predates this feature and
        // belongs to something else entirely. If fk_cookbooks_category
        // was ever added pointing at that table (same constraint name,
        // wrong referenced table), repoint it at sousmeow_categories
        // rather than leaving it silently wrong.
        $refTable = Database::fetchValue(
            "SELECT REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cookbooks'
               AND CONSTRAINT_NAME = 'fk_cookbooks_category'"
        );
        if ($refTable !== null && $refTable !== 'sousmeow_categories') {
            $pdo->exec('ALTER TABLE cookbooks DROP FOREIGN KEY fk_cookbooks_category');
            echo "Repointed cookbooks.primary_category_id foreign key (was referencing {$refTable})\n";
            $refTable = null;
        }
        if ($refTable === null) {
            $pdo->exec(
                "ALTER TABLE cookbooks ADD CONSTRAINT fk_cookbooks_category
                 FOREIGN KEY (primary_category_id) REFERENCES sousmeow_categories(id) ON DELETE SET NULL"
            );
            echo "Migrated cookbooks.primary_category_id foreign key\n";
        }
    }

    ensure_column($pdo, $driver, 'cookbooks', 'difficulty', column_definition($driver, 'difficulty'));
    ensure_column($pdo, $driver, 'cookbooks', 'demo_completed_runs', column_definition($driver, 'demo_completed_runs'));
    ensure_column($pdo, $driver, 'cookbooks', 'demo_avg_rating', column_definition($driver, 'demo_avg_rating'));
    ensure_column($pdo, $driver, 'recipes', 'stage_position', column_definition($driver, 'stage_position'));
    ensure_column($pdo, $driver, 'users', 'simulation', column_definition($driver, 'simulation'));
    ensure_column($pdo, $driver, 'recipes', 'output_contract', column_definition($driver, 'output_contract'));
    ensure_column($pdo, $driver, 'recipe_checks', 'evidence_keys', column_definition($driver, 'evidence_keys'));
    ensure_column($pdo, $driver, 'recipes', 'before_you_begin', column_definition($driver, 'before_you_begin'));
    ensure_column($pdo, $driver, 'recipes', 'common_problems', column_definition($driver, 'common_problems'));
    ensure_column($pdo, $driver, 'recipes', 'recovery_guidance', column_definition($driver, 'recovery_guidance'));

    $userAccountColumns = [
        'email_verified_at', 'verification_token_hash', 'verification_expires_at', 'verification_sent_at',
        'pending_email', 'pending_email_token_hash', 'pending_email_expires_at',
        'password_changed_at', 'onboarding_completed_at',
        'bio', 'website', 'avatar_url', 'preferred_ai', 'ai_experience_level', 'timezone', 'theme_preference',
    ];
    foreach ($userAccountColumns as $col) {
        ensure_column($pdo, $driver, 'users', $col, column_definition($driver, $col));
    }

    $hasResetTokens = (int) Database::fetchValue(
        $driver === 'mysql'
            ? "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'password_reset_tokens'"
            : "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = 'password_reset_tokens'"
    ) > 0;
    if (!$hasResetTokens) {
        if ($driver === 'mysql') {
            $pdo->exec(<<<'SQL'
CREATE TABLE password_reset_tokens (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at    DATETIME NULL,
    created_at DATETIME NOT NULL,
    KEY idx_reset_user (user_id),
    KEY idx_reset_hash (token_hash),
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        } else {
            $pdo->exec(<<<'SQL'
CREATE TABLE password_reset_tokens (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    used_at    TEXT NULL,
    created_at TEXT NOT NULL
)
SQL);
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_reset_user ON password_reset_tokens (user_id)');
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_reset_hash ON password_reset_tokens (token_hash)');
        }
        echo "Migrated password_reset_tokens table\n";
    }

    // Legacy users (pre-verification deploy) keep access. New signups stay unverified
    // until they verify — they always have verification_token_hash set at registration.
    Database::run(
        'UPDATE users SET email_verified_at = created_at, onboarding_completed_at = COALESCE(onboarding_completed_at, created_at)
         WHERE email_verified_at IS NULL AND (simulation = 1 OR role = ?)',
        ['admin']
    );
    Database::run(
        'UPDATE users SET email_verified_at = created_at, onboarding_completed_at = COALESCE(onboarding_completed_at, created_at)
         WHERE email_verified_at IS NULL AND simulation = 0 AND role = ? AND verification_token_hash IS NULL',
        ['user']
    );

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

    $hasSimRuns = (int) Database::fetchValue(
        $driver === 'mysql'
            ? "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'simulation_runs'"
            : "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = 'simulation_runs'"
    ) > 0;
    if (!$hasSimRuns) {
        if ($driver === 'mysql') {
            $pdo->exec(<<<'SQL'
CREATE TABLE simulation_runs (
    pacific_date   DATE NOT NULL PRIMARY KEY,
    users_active   INT UNSIGNED NOT NULL DEFAULT 0,
    actions_count  INT UNSIGNED NOT NULL DEFAULT 0,
    executed_at    DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL);
        } else {
            $pdo->exec(<<<'SQL'
CREATE TABLE simulation_runs (
    pacific_date   TEXT NOT NULL PRIMARY KEY,
    users_active   INTEGER NOT NULL DEFAULT 0,
    actions_count  INTEGER NOT NULL DEFAULT 0,
    executed_at    TEXT NOT NULL
)
SQL);
        }
        echo "Migrated simulation_runs table\n";
    }
}

/**
 * Upsert the discovery taxonomy (categories, then collections) and return
 * lookup maps keyed by slug. Both are upserted by slug and safe to re-run;
 * orphans removed from the seed are pruned (a category only when no
 * Cookbook still points at it; a collection freely, its join rows cascade).
 *
 * @param array{categories: list<array<string, mixed>>, collections: list<array<string, mixed>>} $content
 * @return array{categories: array<string, array{id: int, name: string}>, collections: array<string, array{id: int, type: string, name: string}>}
 */
function sync_taxonomy(array $content): array
{
    $categories = [];
    $collections = [];
    $now = now_utc();

    Database::transaction(function () use ($content, $now, &$categories, &$collections): void {
        $catSlugs = [];
        foreach ($content['categories'] as $sortIndex => $cat) {
            $slug = (string) $cat['slug'];
            $catSlugs[] = $slug;
            $outcomes = json_encode(array_values($cat['outcomes']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $sortOrder = (int) ($cat['sort_order'] ?? ($sortIndex + 1));
            $existing = Database::fetch('SELECT id FROM sousmeow_categories WHERE slug = ?', [$slug]);
            if ($existing !== null) {
                Database::run(
                    'UPDATE sousmeow_categories SET name = ?, short_name = ?, tagline = ?, description = ?, outcomes_json = ?,
                                           accent = ?, icon_key = ?, sort_order = ?, is_visible = ?, updated_at = ?
                     WHERE id = ?',
                    [$cat['name'], $cat['short_name'] ?? null, $cat['tagline'], $cat['description'], $outcomes,
                     $cat['accent'], $cat['icon_key'] ?? null, $sortOrder, (int) ($cat['is_visible'] ?? 1), $now, $existing['id']]
                );
                $id = (int) $existing['id'];
            } else {
                Database::run(
                    'INSERT INTO sousmeow_categories (slug, name, short_name, tagline, description, outcomes_json, accent,
                                             icon_key, sort_order, is_visible, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$slug, $cat['name'], $cat['short_name'] ?? null, $cat['tagline'], $cat['description'], $outcomes,
                     $cat['accent'], $cat['icon_key'] ?? null, $sortOrder, (int) ($cat['is_visible'] ?? 1), $now, $now]
                );
                $id = Database::lastInsertId();
            }
            $categories[$slug] = ['id' => $id, 'name' => (string) $cat['name']];
        }
        prune_orphan_categories($catSlugs);

        $colSlugs = [];
        foreach ($content['collections'] as $sortIndex => $col) {
            $slug = (string) $col['slug'];
            $colSlugs[] = $slug;
            $sortOrder = (int) ($col['sort_order'] ?? ($sortIndex + 1));
            $existing = Database::fetch('SELECT id FROM sousmeow_collections WHERE slug = ?', [$slug]);
            if ($existing !== null) {
                Database::run(
                    'UPDATE sousmeow_collections SET name = ?, tagline = ?, description = ?, accent = ?, collection_type = ?,
                                            min_display_count = ?, sort_order = ?, is_visible = ?, updated_at = ?
                     WHERE id = ?',
                    [$col['name'], $col['tagline'], $col['description'], $col['accent'], $col['collection_type'],
                     (int) ($col['min_display_count'] ?? 1), $sortOrder, (int) ($col['is_visible'] ?? 1), $now, $existing['id']]
                );
                $id = (int) $existing['id'];
            } else {
                Database::run(
                    'INSERT INTO sousmeow_collections (slug, name, tagline, description, accent, collection_type,
                                              min_display_count, sort_order, is_visible, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$slug, $col['name'], $col['tagline'], $col['description'], $col['accent'], $col['collection_type'],
                     (int) ($col['min_display_count'] ?? 1), $sortOrder, (int) ($col['is_visible'] ?? 1), $now, $now]
                );
                $id = Database::lastInsertId();
            }
            $collections[$slug] = ['id' => $id, 'type' => (string) $col['collection_type'], 'name' => (string) $col['name']];
        }
        prune_orphan_collections($colSlugs);
    });

    return ['categories' => $categories, 'collections' => $collections];
}

/**
 * Reconcile a Cookbook's editorial Collection membership from its seed
 * `collections` slug list. Rows are rebuilt from the seed each run; only
 * editorial Collections carry join rows (dynamic/attribute resolve by
 * query). Position mirrors the Cookbook's global sort_order for a stable
 * order inside every Collection.
 *
 * @param list<string>                                                     $collectionSlugs
 * @param array<string, array{id: int, type: string, name: string}>        $colBySlug
 */
function sync_cookbook_memberships(int $cookbookId, array $collectionSlugs, int $position, array $colBySlug): void
{
    Database::run('DELETE FROM sousmeow_cookbook_collections WHERE cookbook_id = ?', [$cookbookId]);
    foreach ($collectionSlugs as $slug) {
        $collection = $colBySlug[(string) $slug];   // pre-validated as an editorial slug
        Database::run(
            'INSERT INTO sousmeow_cookbook_collections (cookbook_id, collection_id, position, is_featured, created_at)
             VALUES (?, ?, ?, ?, ?)',
            [$cookbookId, $collection['id'], $position, 0, now_utc()]
        );
    }
}

/**
 * Upsert first-party Cookbook content from seed files. Preserves user
 * projects and artifact history; only prunes unused catalog rows.
 *
 * @param array{cookbooks: list<array<string, mixed>>} $content
 * @param array<string, array{id: int, name: string}>  $catBySlug
 * @param array<string, array{id: int, type: string, name: string}> $colBySlug
 * @return array{inserted: int, updated: int, removed: int, executable: int, preview: int}
 */
function sync_content(array $content, array $catBySlug, array $colBySlug): array
{
    $stats = ['inserted' => 0, 'updated' => 0, 'removed' => 0, 'executable' => 0, 'preview' => 0];
    $seedSlugs = [];

    Database::transaction(function () use ($content, $catBySlug, $colBySlug, &$stats, &$seedSlugs): void {
        foreach ($content['cookbooks'] as $book) {
            $slug = (string) $book['slug'];
            $seedSlugs[] = $slug;
            $executable = !empty($book['is_executable']);

            // Resolve the primary category by slug. The legacy `category`
            // string is derived from the category name (rollback only).
            $category = $catBySlug[(string) $book['primary_category']];  // pre-validated
            $categoryId = $category['id'];
            $legacyCategory = $category['name'];

            $existing = Database::fetch('SELECT id, created_at FROM cookbooks WHERE slug = ?', [$slug]);
            if ($existing !== null) {
                $cookbookId = (int) $existing['id'];
                Database::run(
                    'UPDATE cookbooks SET title = ?, tagline = ?, description = ?, category = ?, primary_category_id = ?,
                                          audience = ?, outcome = ?, price_cents = ?, is_executable = ?, status = ?,
                                          accent = ?, difficulty = ?, est_minutes = ?, demo_completed_runs = ?,
                                          demo_avg_rating = ?, sort_order = ?
                     WHERE id = ?',
                    [
                        $book['title'], $book['tagline'], $book['description'],
                        $legacyCategory, $categoryId, $book['audience'], $book['outcome'],
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
                    'INSERT INTO cookbooks (slug, title, tagline, description, category, primary_category_id, audience,
                                            outcome, price_cents, is_executable, status, accent, difficulty, est_minutes,
                                            demo_completed_runs, demo_avg_rating, sort_order, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $slug, $book['title'], $book['tagline'], $book['description'],
                        $legacyCategory, $categoryId, $book['audience'], $book['outcome'],
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
            sync_cookbook_memberships($cookbookId, $book['collections'] ?? [], (int) ($book['sort_order'] ?? 100), $colBySlug);
            echo "  Synced {$slug} (" . ($executable ? 'executable' : 'preview') . ", " . count($book['recipes']) . " recipes)\n";
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
    // Clear the unique (cookbook_id, position) collision window before
    // upserting. Cookbook revisions may rename Pantry fields while reusing
    // the same positions, just as recipe revisions do below.
    Database::run(
        'UPDATE pantry_fields SET position = position + 1000 WHERE cookbook_id = ?',
        [$cookbookId]
    );

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
    // Clear the unique (cookbook_id, position) collision window before
    // upserting. Renames and shrinks otherwise fail when an INSERT claims a
    // position still held by a soon-to-be-orphaned slug.
    Database::run(
        'UPDATE recipes SET position = position + 1000 WHERE cookbook_id = ?',
        [$cookbookId]
    );

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
            'stage_position'      => $recipe['stage_position'] ?? null,
            'position'            => $position + 1,
            'title'               => $recipe['title'],
            'summary'             => $recipe['summary'],
            'why_it_matters'      => $recipe['why_it_matters'] ?? '',
            'unlocks_text'        => $recipe['unlocks_text'] ?? '',
            'before_you_begin'    => $recipe['before_you_begin'] ?? '',
            'common_problems'     => $recipe['common_problems'] ?? '',
            'recovery_guidance'   => $recipe['recovery_guidance'] ?? '',
            'prompt_template'     => $prompt,
            'example_response'    => $example,
            'output_contract'     => \SousMeow\Services\OutputContract::encode($recipe['output_sections'] ?? null),
            'est_minutes'         => (int) ($recipe['est_minutes'] ?? 5),
        ];
        if ($existing !== null) {
            $recipeId = (int) $existing['id'];
            Database::run(
                'UPDATE recipes SET stage_position = ?, position = ?, title = ?, summary = ?,
                                    why_it_matters = ?, unlocks_text = ?, before_you_begin = ?,
                                    common_problems = ?, recovery_guidance = ?, prompt_template = ?,
                                    example_response = ?, output_contract = ?, est_minutes = ?
                 WHERE id = ?',
                [
                    $row['stage_position'], $row['position'], $row['title'], $row['summary'],
                    $row['why_it_matters'], $row['unlocks_text'], $row['before_you_begin'],
                    $row['common_problems'], $row['recovery_guidance'], $row['prompt_template'],
                    $row['example_response'], $row['output_contract'], $row['est_minutes'], $recipeId,
                ]
            );
        } else {
            Database::run(
                'INSERT INTO recipes (cookbook_id, stage_position, position, slug, title, summary,
                                      why_it_matters, unlocks_text, before_you_begin, common_problems,
                                      recovery_guidance, prompt_template, example_response,
                                      output_contract, est_minutes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $cookbookId, $row['stage_position'], $row['position'], $slug,
                    $row['title'], $row['summary'], $row['why_it_matters'], $row['unlocks_text'],
                    $row['before_you_begin'], $row['common_problems'], $row['recovery_guidance'],
                    $row['prompt_template'], $row['example_response'], $row['output_contract'],
                    $row['est_minutes'],
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

/** @param list<array{label: string, help?: string, evidence_sections?: list<string>}> $checks */
function sync_checks(int $recipeId, array $checks): void
{
    $keepPositions = [];
    foreach ($checks as $checkPos => $check) {
        $pos = $checkPos + 1;
        $keepPositions[] = $pos;
        $evidence = isset($check['evidence_sections']) && $check['evidence_sections'] !== []
            ? json_encode(array_values($check['evidence_sections']), JSON_UNESCAPED_SLASHES)
            : null;
        $existing = Database::fetch(
            'SELECT id FROM recipe_checks WHERE recipe_id = ? AND position = ?',
            [$recipeId, $pos]
        );
        if ($existing !== null) {
            Database::run(
                'UPDATE recipe_checks SET label = ?, help = ?, evidence_keys = ? WHERE id = ?',
                [$check['label'], $check['help'] ?? '', $evidence, $existing['id']]
            );
        } else {
            Database::run(
                'INSERT INTO recipe_checks (recipe_id, position, label, help, evidence_keys) VALUES (?, ?, ?, ?, ?)',
                [$recipeId, $pos, $check['label'], $check['help'] ?? '', $evidence]
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

/**
 * Remove categories dropped from the seed, but only when no Cookbook still
 * points at them (the FK is ON DELETE SET NULL, so a live pointer would be
 * silently nulled otherwise).
 *
 * @param list<string> $seedSlugs
 */
function prune_orphan_categories(array $seedSlugs): void
{
    if ($seedSlugs === []) {
        return;
    }
    $placeholders = implode(',', array_fill(0, count($seedSlugs), '?'));
    $orphans = Database::fetchAll(
        "SELECT c.id, c.slug FROM sousmeow_categories c
         WHERE c.slug NOT IN ({$placeholders})
           AND NOT EXISTS (SELECT 1 FROM cookbooks b WHERE b.primary_category_id = c.id)",
        $seedSlugs
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM sousmeow_categories WHERE id = ?', [$row['id']]);
        echo "Removed orphan category: {$row['slug']}\n";
    }
}

/**
 * Remove collections dropped from the seed. Membership rows cascade, so no
 * guard is needed.
 *
 * @param list<string> $seedSlugs
 */
function prune_orphan_collections(array $seedSlugs): void
{
    if ($seedSlugs === []) {
        return;
    }
    $placeholders = implode(',', array_fill(0, count($seedSlugs), '?'));
    $orphans = Database::fetchAll(
        "SELECT id, slug FROM sousmeow_collections WHERE slug NOT IN ({$placeholders})",
        $seedSlugs
    );
    foreach ($orphans as $row) {
        Database::run('DELETE FROM sousmeow_collections WHERE id = ?', [$row['id']]);
        echo "Removed orphan collection: {$row['slug']}\n";
    }
}

/** @return list<string> Expected cookbook seed filenames from content.php. */
function expected_seed_files(): array
{
    return [
        'launch-day-kit.php',
        'validate-saas-idea.php',
        'professional-portfolio.php',
        'plan-youtube-video.php',
        'plan-a-novel.php',
        'build-a-study-plan.php',
        'write-email-that-gets-answered.php',
        'write-a-feature-spec.php',
        'name-your-brand-voice.php',
        'compare-three-competitors.php',
        'make-a-criteria-decision.php',
        'finish-a-personal-project.php',
        'price-your-offer.php',
        'write-a-campaign-brief.php',
        'pack-a-release-checklist.php',
        'plan-a-newsletter-issue.php',
        'outline-an-article.php',
        'critique-a-screen.php',
        'prep-for-an-interview.php',
        'synthesize-interview-notes.php',
        'plan-a-lesson.php',
        'document-a-simple-process.php',
        'design-a-game-loop.php',
        'set-a-thirty-day-goal.php',
    ];
}

/** Abort early when deploy is missing seed files (common partial-upload mistake). */
function verify_seed_files(): void
{
    $dir = __DIR__ . '/../database/seeds/cookbooks';
    $missing = [];
    foreach (expected_seed_files() as $file) {
        if (!is_file($dir . '/' . $file)) {
            $missing[] = $file;
        }
    }
    if ($missing !== []) {
        fwrite(STDERR, "Missing seed files in database/seeds/cookbooks/:\n");
        foreach ($missing as $file) {
            fwrite(STDERR, "  - {$file}\n");
        }
        fwrite(STDERR, "Deploy the full projects/sousmeow folder, then re-run seed.\n");
        exit(1);
    }
}

/**
 * Validate every recipe's output contract and evidence mappings before
 * any database write. Malformed contracts abort the seed run so they can
 * never surface as runtime surprises in the Runner.
 *
 * @param array{cookbooks: list<array<string, mixed>>} $content
 * @return list<string> Problem lines; empty means valid.
 */
function output_contract_issues(array $content): array
{
    $issues = [];
    foreach ($content['cookbooks'] as $book) {
        foreach ($book['recipes'] as $recipe) {
            $errors = \SousMeow\Services\OutputContract::validate(
                $recipe['output_sections'] ?? null,
                $recipe['checks'] ?? []
            );
            foreach ($errors as $error) {
                $issues[] = "{$book['slug']}/{$recipe['slug']}: {$error}";
            }
        }
    }
    return $issues;
}

/**
 * Validate the discovery taxonomy before any write. Every accent is an
 * allowlisted key, every collection_type is known, every category carries
 * exactly three outcomes, every Cookbook's primary_category resolves to a
 * known category slug, and every declared Collection membership resolves
 * to a known *editorial* Collection. Any failure aborts the seed loudly.
 *
 * @param array{cookbooks: list<array<string, mixed>>, categories: list<array<string, mixed>>, collections: list<array<string, mixed>>} $content
 * @return list<string> Problem lines; empty means valid.
 */
function taxonomy_issues(array $content): array
{
    $issues = [];
    $validTypes = ['editorial', 'dynamic', 'attribute'];

    $categorySlugs = [];
    foreach ($content['categories'] as $cat) {
        $slug = (string) ($cat['slug'] ?? '');
        if ($slug === '') {
            $issues[] = 'category with no slug';
            continue;
        }
        $categorySlugs[$slug] = true;
        if (!Accent::isValid((string) ($cat['accent'] ?? ''))) {
            $issues[] = "category {$slug}: accent '{$cat['accent']}' is not in the allowlist";
        }
        $outcomes = $cat['outcomes'] ?? null;
        if (!is_array($outcomes) || count($outcomes) !== 3) {
            $issues[] = "category {$slug}: outcomes must be exactly three";
        }
    }

    $collectionSlugs = [];
    $editorialSlugs = [];
    foreach ($content['collections'] as $col) {
        $slug = (string) ($col['slug'] ?? '');
        if ($slug === '') {
            $issues[] = 'collection with no slug';
            continue;
        }
        $collectionSlugs[$slug] = true;
        $type = (string) ($col['collection_type'] ?? '');
        if (!in_array($type, $validTypes, true)) {
            $issues[] = "collection {$slug}: collection_type '{$type}' is not one of editorial/dynamic/attribute";
        }
        if ($type === 'editorial') {
            $editorialSlugs[$slug] = true;
        }
        if (!Accent::isValid((string) ($col['accent'] ?? ''))) {
            $issues[] = "collection {$slug}: accent '{$col['accent']}' is not in the allowlist";
        }
    }

    foreach ($content['cookbooks'] as $book) {
        $slug = (string) $book['slug'];
        $pc = (string) ($book['primary_category'] ?? '');
        if ($pc === '' || !isset($categorySlugs[$pc])) {
            $issues[] = "cookbook {$slug}: unknown primary_category slug '{$pc}'";
        }
        if (!Accent::isValid((string) ($book['accent'] ?? ''))) {
            $issues[] = "cookbook {$slug}: accent '{$book['accent']}' is not in the allowlist";
        }
        foreach ((array) ($book['collections'] ?? []) as $cs) {
            $cs = (string) $cs;
            if (!isset($collectionSlugs[$cs])) {
                $issues[] = "cookbook {$slug}: unknown collection slug '{$cs}'";
            } elseif (!isset($editorialSlugs[$cs])) {
                $issues[] = "cookbook {$slug}: collection '{$cs}' is not editorial (only editorial Collections take membership rows)";
            }
        }
    }

    return $issues;
}

/**
 * @param array{cookbooks: list<array<string, mixed>>} $content
 * @return list<string> Problem lines; empty means healthy.
 */
function cookbook_health_issues(array $content): array
{
    $issues = [];
    foreach ($content['cookbooks'] as $book) {
        $slug = (string) $book['slug'];
        $shouldRun = !empty($book['is_executable']);
        $row = Database::fetch(
            'SELECT id, is_executable, status FROM cookbooks WHERE slug = ?',
            [$slug]
        );
        if ($row === null) {
            $issues[] = "{$slug}: missing from database (sync did not insert it)";
            continue;
        }
        $cookbookId = (int) $row['id'];
        $recipeTotal = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM recipes WHERE cookbook_id = ?',
            [$cookbookId]
        );
        $promptCount = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM recipes WHERE cookbook_id = ? AND prompt_template IS NOT NULL AND LENGTH(prompt_template) > 0',
            [$cookbookId]
        );
        $expectedRecipes = count($book['recipes']);
        $isExec = (int) $row['is_executable'] === 1;

        if ($shouldRun && !$isExec) {
            $issues[] = "{$slug}: seed expects executable but database has is_executable=0 (status={$row['status']})";
        }
        if ($recipeTotal !== $expectedRecipes) {
            $issues[] = "{$slug}: expected {$expectedRecipes} recipes, database has {$recipeTotal}";
        }
        if ($shouldRun && $promptCount < $expectedRecipes) {
            $issues[] = "{$slug}: expected {$expectedRecipes} prompts, database has {$promptCount}";
        }
    }

    $dbCount = (int) Database::fetchValue('SELECT COUNT(*) FROM cookbooks');
    $seedCount = count($content['cookbooks']);
    if ($dbCount !== $seedCount) {
        $issues[] = "catalog count mismatch: seed defines {$seedCount} cookbooks, database has {$dbCount}";
    }

    return $issues;
}

/**
 * @param array{cookbooks: list<array<string, mixed>>} $content
 */
function print_cookbook_status(array $content): void
{
    echo "Cookbook catalog (driver: " . Database::driver() . ")\n";
    foreach ($content['cookbooks'] as $book) {
        $slug = (string) $book['slug'];
        $shouldRun = !empty($book['is_executable']);
        $row = Database::fetch(
            'SELECT id, is_executable, status FROM cookbooks WHERE slug = ?',
            [$slug]
        );
        if ($row === null) {
            echo "  [MISSING] {$slug} (seed wants " . ($shouldRun ? 'executable' : 'preview') . ")\n";
            continue;
        }
        $promptCount = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM recipes WHERE cookbook_id = ? AND prompt_template IS NOT NULL AND prompt_template != \'\'',
            [(int) $row['id']]
        );
        $recipeTotal = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM recipes WHERE cookbook_id = ?',
            [(int) $row['id']]
        );
        $flag = (int) $row['is_executable'] === 1 ? 'executable' : 'preview';
        $ok = (!$shouldRun || ((int) $row['is_executable'] === 1 && $promptCount === count($book['recipes'])));
        echo '  ' . ($ok ? '[OK]' : '[!!]') . " {$slug}: db={$flag}, status={$row['status']}, recipes={$recipeTotal}, prompts={$promptCount}\n";
    }
    $extra = Database::fetchAll(
        'SELECT slug FROM cookbooks WHERE slug NOT IN (' . implode(',', array_fill(0, count($content['cookbooks']), '?')) . ')',
        array_map(static fn(array $b): string => (string) $b['slug'], $content['cookbooks'])
    );
    foreach ($extra as $row) {
        echo "  [EXTRA] {$row['slug']} (not in current seed; prune runs when it has no projects)\n";
    }
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
               'projects', 'pantry_fields', 'recipe_checks', 'recipes', 'cookbook_stages',
               'sousmeow_cookbook_collections', 'cookbooks', 'sousmeow_collections', 'sousmeow_categories',
               'simulation_runs', 'rate_events', 'users'];
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

verify_seed_files();
/** @var array{cookbooks: list<array<string, mixed>>, categories: list<array<string, mixed>>, collections: list<array<string, mixed>>} $content */
$content = require __DIR__ . '/../database/seeds/content.php';

$contractIssues = output_contract_issues($content);
if ($contractIssues !== []) {
    fwrite(STDERR, "Invalid output contracts in seed content; nothing was synced:\n");
    foreach ($contractIssues as $issue) {
        fwrite(STDERR, "  - {$issue}\n");
    }
    exit(1);
}

$taxonomyIssues = taxonomy_issues($content);
if ($taxonomyIssues !== []) {
    fwrite(STDERR, "Invalid discovery taxonomy in seed content; nothing was synced:\n");
    foreach ($taxonomyIssues as $issue) {
        fwrite(STDERR, "  - {$issue}\n");
    }
    exit(1);
}

if ($options['status']) {
    print_cookbook_status($content);
    $issues = cookbook_health_issues($content);
    if ($issues !== []) {
        echo "\nProblems found:\n";
        foreach ($issues as $issue) {
            echo "  - {$issue}\n";
        }
        exit(1);
    }
    echo "\nCatalog health: OK\n";
    exit(0);
}

// 2. Taxonomy sync (categories, then collections), then content sync.
// Both upsert by slug and are safe to re-run.
$taxonomy = sync_taxonomy($content);
echo sprintf(
    "Taxonomy synced: %d categories, %d collections.\n",
    count($taxonomy['categories']),
    count($taxonomy['collections'])
);

$stats = sync_content($content, $taxonomy['categories'], $taxonomy['collections']);
echo sprintf(
    "Content synced: %d inserted, %d updated, %d orphan cookbooks removed (%d executable, %d preview in seed).\n",
    $stats['inserted'],
    $stats['updated'],
    $stats['removed'],
    $stats['executable'],
    $stats['preview']
);

print_cookbook_status($content);
$issues = cookbook_health_issues($content);
if ($issues !== []) {
    echo "\nSeed sync finished but catalog health checks failed:\n";
    foreach ($issues as $issue) {
        echo "  - {$issue}\n";
    }
    fwrite(STDERR, "\nRe-run from the projects/sousmeow directory after confirming deploy includes database/seeds/cookbooks/.\n");
    exit(1);
}
echo "Catalog health: OK\n";

// 3. Admin account, created once. The temporary password is printed
// exactly once and never stored in plain text.
$adminCount = (int) Database::fetchValue("SELECT COUNT(*) FROM users WHERE role = 'admin'");
if ($adminCount > 0) {
    echo "Admin account already exists, skipping.\n";
} else {
    $email = strtolower(trim($options['admin_email']));
    $password = temp_password();
    Database::run(
        'INSERT INTO users (name, email, password_hash, role, email_verified_at, onboarding_completed_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
        ['Head Chef', $email, password_hash($password, PASSWORD_DEFAULT), 'admin', now_utc(), now_utc(), now_utc()]
    );
    echo "\nAdmin account created.\n";
    echo "  Email:              {$email}\n";
    echo "  Temporary password: {$password}\n";
    echo "Sign in and change this password. It is shown only this once.\n";
}

echo "\nDone. Start the app with:\n  php -S localhost:8090 -t public public/index.php\n";
