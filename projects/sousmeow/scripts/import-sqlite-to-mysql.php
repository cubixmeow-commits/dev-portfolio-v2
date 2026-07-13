#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Import data from a SQLite database into the configured MySQL database.
 *
 * Typical production cutover (catalog already seeded on MySQL):
 *   1. config.php: db.driver = mysql, plus MySQL credentials
 *   2. php scripts/seed.php --status          # confirm catalog is healthy
 *   3. php scripts/import-sqlite-to-mysql.php --sqlite-path storage/sousmeow.sqlite
 *
 * Full replace (empty MySQL or you want SQLite to be the source of truth):
 *   php scripts/import-sqlite-to-mysql.php --full --sqlite-path storage/sousmeow.sqlite
 *
 * Options:
 *   --sqlite-path PATH   SQLite file (default: config db.sqlite_path)
 *   --full               Truncate MySQL tables and import everything from SQLite
 *   --data-only          Import users + projects + artifacts only (default)
 *   --replace-users      With --data-only: remove all MySQL users before import
 *   --dry-run            Print plan without writing
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Config;

$args = array_slice($argv, 1);
$options = [
    'sqlite_path'    => null,
    'full'           => false,
    'data_only'      => false,
    'replace_users'  => false,
    'dry_run'        => false,
];

for ($i = 0; $i < count($args); $i++) {
    $arg = $args[$i];
    if ($arg === '--full') {
        $options['full'] = true;
    } elseif ($arg === '--data-only') {
        $options['data_only'] = true;
    } elseif ($arg === '--dry-run') {
        $options['dry_run'] = true;
    } elseif ($arg === '--replace-users') {
        $options['replace_users'] = true;
    } elseif ($arg === '--sqlite-path') {
        $options['sqlite_path'] = $args[++$i] ?? null;
    } elseif (str_starts_with($arg, '--sqlite-path=')) {
        $options['sqlite_path'] = substr($arg, strlen('--sqlite-path='));
    } else {
        fwrite(STDERR, "Unknown option: {$arg}\n");
        exit(1);
    }
}

if (!$options['full'] && !$options['data_only']) {
    $options['data_only'] = true;
}
if ($options['full'] && $options['data_only']) {
    fwrite(STDERR, "Use either --full or --data-only, not both.\n");
    exit(1);
}

if (Config::string('db.driver', 'sqlite') !== 'mysql') {
    fwrite(STDERR, "config.php must use db.driver = 'mysql' for the destination database.\n");
    exit(1);
}

$projectRoot = dirname(__DIR__);

/** Resolve a path relative to the project root when it is not absolute. */
function resolve_sqlite_path(string $path, string $projectRoot): string
{
    if ($path === '') {
        return $path;
    }
    if ($path[0] === '/') {
        return $path;
    }
    return $projectRoot . '/' . ltrim($path, '/');
}

$sqlitePath = $options['sqlite_path'] ?? Config::string('db.sqlite_path');
$sqlitePath = resolve_sqlite_path($sqlitePath, $projectRoot);

if (!is_file($sqlitePath)) {
    $defaultPath = resolve_sqlite_path(Config::string('db.sqlite_path'), $projectRoot);
    fwrite(STDERR, "SQLite file not found: {$sqlitePath}\n");
    if ($defaultPath !== $sqlitePath && is_file($defaultPath)) {
        fwrite(STDERR, "Try: php scripts/import-sqlite-to-mysql.php --sqlite-path {$defaultPath}\n");
    } else {
        fwrite(STDERR, "Upload your local database to storage/sousmeow.sqlite, then run from the project root:\n");
        fwrite(STDERR, "  cd ..   # parent of scripts/\n");
        fwrite(STDERR, "  php scripts/import-sqlite-to-mysql.php --dry-run --sqlite-path storage/sousmeow.sqlite\n");
    }
    exit(1);
}

$sqlite = new PDO('sqlite:' . $sqlitePath, null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$sqlite->exec('PRAGMA foreign_keys = OFF');

$mysqlDsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    Config::string('db.host'),
    Config::int('db.port', 3306),
    Config::string('db.name'),
    Config::string('db.charset', 'utf8mb4')
);
$mysql = new PDO($mysqlDsn, Config::string('db.user'), Config::string('db.password'), [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

echo "SQLite source: {$sqlitePath}\n";
echo "MySQL dest:    " . Config::string('db.host') . '/' . Config::string('db.name') . "\n";
echo 'Mode:          ' . ($options['full'] ? 'full' : 'data-only') . ($options['dry_run'] ? ' (dry run)' : '') . "\n\n";

echo "Row counts (SQLite → MySQL):\n";
foreach ($allTables as $table) {
    if (!table_exists($sqlite, 'sqlite', $table)) {
        continue;
    }
    $s = count_rows($sqlite, $table);
    $m = table_exists($mysql, 'mysql', $table) ? count_rows($mysql, $table) : 0;
    if ($s > 0 || $m > 0) {
        echo sprintf("  %-22s %6d → %d\n", $table . ':', $s, $m);
    }
}
echo "\n";

/** Tables in parent-first order for import. */
$allTables = [
    'users',
    'password_reset_tokens',
    'rate_events',
    'cookbooks',
    'cookbook_stages',
    'recipes',
    'recipe_checks',
    'pantry_fields',
    'projects',
    'pantry_values',
    'artifacts',
    'artifact_versions',
    'artifact_checks',
    'exports',
    'simulation_runs',
];

$dataOnlyTables = [
    'users',
    'password_reset_tokens',
    'rate_events',
    'projects',
    'pantry_values',
    'artifacts',
    'artifact_versions',
    'artifact_checks',
    'exports',
    'simulation_runs',
];

$tables = $options['full'] ? $allTables : $dataOnlyTables;

/** @return bool */
function table_exists(PDO $pdo, string $driver, string $table): bool
{
    if ($driver === 'mysql') {
        $row = $pdo->query(
            "SELECT COUNT(*) FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = " . $pdo->quote($table)
        )->fetchColumn();
        return (int) $row > 0;
    }
    $row = $pdo->query(
        "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = " . $pdo->quote($table)
    )->fetchColumn();
    return (int) $row > 0;
}

/** @return list<string> */
function column_names(PDO $pdo, string $driver, string $table): array
{
    if ($driver === 'mysql') {
        $stmt = $pdo->query(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ' . $pdo->quote($table) . '
             ORDER BY ORDINAL_POSITION'
        );
        $cols = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        return array_map('strval', $cols ?: []);
    }
    $rows = $pdo->query('PRAGMA table_info(' . $table . ')')->fetchAll();
    if ($rows === false) {
        return [];
    }
    return array_map(static fn(array $r): string => (string) $r['name'], $rows);
}

/** @return list<array<string, mixed>> */
function fetch_rows(PDO $pdo, string $table): array
{
    return $pdo->query('SELECT * FROM ' . $table)->fetchAll();
}

function count_rows(PDO $pdo, string $table): int
{
    return (int) $pdo->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
}

/** @param array<string, mixed> $row */
function insert_row(PDO $mysql, string $table, array $columns, array $row): void
{
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $colList = implode(', ', array_map(static fn(string $c): string => '`' . $c . '`', $columns));
    $sql = "INSERT INTO `{$table}` ({$colList}) VALUES ({$placeholders})";
    $values = [];
    foreach ($columns as $col) {
        $values[] = $row[$col] ?? null;
    }
    $stmt = $mysql->prepare($sql);
    $stmt->execute($values);
}

/** @return array<int, int> */
function build_cookbook_id_map(PDO $sqlite, PDO $mysql): array
{
    $src = $sqlite->query('SELECT id, slug FROM cookbooks')->fetchAll();
    $dest = $mysql->query('SELECT id, slug FROM cookbooks')->fetchAll();
    $destBySlug = [];
    foreach ($dest as $row) {
        $destBySlug[(string) $row['slug']] = (int) $row['id'];
    }
    $map = [];
    foreach ($src as $row) {
        $slug = (string) $row['slug'];
        if (!isset($destBySlug[$slug])) {
            throw new RuntimeException("Cookbook slug missing on MySQL: {$slug}");
        }
        $map[(int) $row['id']] = $destBySlug[$slug];
    }
    return $map;
}

/** @return array<int, int> */
function build_recipe_id_map(PDO $sqlite, PDO $mysql, array $cookbookMap): array
{
    $src = $sqlite->query(
        'SELECT r.id, r.cookbook_id, r.position, c.slug AS cookbook_slug
         FROM recipes r INNER JOIN cookbooks c ON c.id = r.cookbook_id'
    )->fetchAll();
    $dest = $mysql->query(
        'SELECT r.id, r.cookbook_id, r.position, c.slug AS cookbook_slug
         FROM recipes r INNER JOIN cookbooks c ON c.id = r.cookbook_id'
    )->fetchAll();
    $destKey = [];
    foreach ($dest as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (int) $row['position'];
        $destKey[$key] = (int) $row['id'];
    }
    $map = [];
    foreach ($src as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (int) $row['position'];
        if (!isset($destKey[$key])) {
            throw new RuntimeException("Recipe missing on MySQL: {$key}");
        }
        $map[(int) $row['id']] = $destKey[$key];
    }
    return $map;
}

/** @return array<int, int> */
function build_pantry_field_id_map(PDO $sqlite, PDO $mysql): array
{
    $src = $sqlite->query(
        'SELECT pf.id, pf.field_key, c.slug AS cookbook_slug
         FROM pantry_fields pf INNER JOIN cookbooks c ON c.id = pf.cookbook_id'
    )->fetchAll();
    $dest = $mysql->query(
        'SELECT pf.id, pf.field_key, c.slug AS cookbook_slug
         FROM pantry_fields pf INNER JOIN cookbooks c ON c.id = pf.cookbook_id'
    )->fetchAll();
    $destKey = [];
    foreach ($dest as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (string) $row['field_key'];
        $destKey[$key] = (int) $row['id'];
    }
    $map = [];
    foreach ($src as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (string) $row['field_key'];
        if (!isset($destKey[$key])) {
            throw new RuntimeException("Pantry field missing on MySQL: {$key}");
        }
        $map[(int) $row['id']] = $destKey[$key];
    }
    return $map;
}

/** @param array<string, mixed> $row */
function remap_row(string $table, array $row, array $maps): array
{
    if ($table === 'projects' && isset($row['cookbook_id'], $maps['cookbooks'][$row['cookbook_id']])) {
        $row['cookbook_id'] = $maps['cookbooks'][(int) $row['cookbook_id']];
    }
    if ($table === 'pantry_values' && isset($row['field_id'], $maps['pantry_fields'][$row['field_id']])) {
        $row['field_id'] = $maps['pantry_fields'][(int) $row['field_id']];
    }
    if ($table === 'artifacts' && isset($row['recipe_id'], $maps['recipes'][$row['recipe_id']])) {
        $row['recipe_id'] = $maps['recipes'][(int) $row['recipe_id']];
    }
    if ($table === 'artifact_checks' && isset($row['check_id'], $maps['recipe_checks'][$row['check_id']])) {
        $row['check_id'] = $maps['recipe_checks'][(int) $row['check_id']];
    }
    return $row;
}

/** @return array<int, int> */
function build_recipe_check_id_map(PDO $sqlite, PDO $mysql): array
{
    $src = $sqlite->query(
        'SELECT rc.id, rc.position, r.position AS recipe_position, c.slug AS cookbook_slug
         FROM recipe_checks rc
         INNER JOIN recipes r ON r.id = rc.recipe_id
         INNER JOIN cookbooks c ON c.id = r.cookbook_id'
    )->fetchAll();
    $dest = $mysql->query(
        'SELECT rc.id, rc.position, r.position AS recipe_position, c.slug AS cookbook_slug
         FROM recipe_checks rc
         INNER JOIN recipes r ON r.id = rc.recipe_id
         INNER JOIN cookbooks c ON c.id = r.cookbook_id'
    )->fetchAll();
    $destKey = [];
    foreach ($dest as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (int) $row['recipe_position'] . '#' . (int) $row['position'];
        $destKey[$key] = (int) $row['id'];
    }
    $map = [];
    foreach ($src as $row) {
        $key = (string) $row['cookbook_slug'] . '#' . (int) $row['recipe_position'] . '#' . (int) $row['position'];
        if (!isset($destKey[$key])) {
            throw new RuntimeException("Recipe check missing on MySQL: {$key}");
        }
        $map[(int) $row['id']] = $destKey[$key];
    }
    return $map;
}

if ($options['data_only']) {
    $sqliteCookbooks = count_rows($sqlite, 'cookbooks');
    $mysqlCookbooks = count_rows($mysql, 'cookbooks');
    if ($sqliteCookbooks !== $mysqlCookbooks) {
        fwrite(STDERR, "Catalog mismatch: SQLite has {$sqliteCookbooks} cookbooks, MySQL has {$mysqlCookbooks}.\n");
        fwrite(STDERR, "Run php scripts/seed.php on MySQL first, or use --full to import everything.\n");
        exit(1);
    }
    echo "Catalog counts match ({$mysqlCookbooks} cookbooks). Importing user data…\n";
    if (!$options['replace_users']) {
        $mysqlUsers = count_rows($mysql, 'users');
        if ($mysqlUsers > 0) {
            echo "Note: MySQL already has {$mysqlUsers} user(s). Use --replace-users to replace them from SQLite,\n";
            echo "      or --full to import everything. Overlapping user IDs may be skipped.\n";
        }
    }
    echo "\n";
}

$maps = [];
if ($options['data_only']) {
    try {
        $maps['cookbooks'] = build_cookbook_id_map($sqlite, $mysql);
        $maps['recipes'] = build_recipe_id_map($sqlite, $mysql, $maps['cookbooks']);
        $maps['pantry_fields'] = build_pantry_field_id_map($sqlite, $mysql);
        $maps['recipe_checks'] = build_recipe_check_id_map($sqlite, $mysql);
    } catch (RuntimeException $e) {
        fwrite(STDERR, 'Catalog mapping failed: ' . $e->getMessage() . "\n");
        fwrite(STDERR, "Re-run php scripts/seed.php on MySQL, or use --full.\n");
        exit(1);
    }
}

if ($options['full']) {
    $truncateOrder = array_reverse($allTables);
    echo "Full import will truncate MySQL tables:\n";
    foreach ($truncateOrder as $table) {
        if (table_exists($mysql, 'mysql', $table)) {
            echo "  - {$table} (" . count_rows($mysql, $table) . " rows)\n";
        }
    }
    echo "\n";
}

$totalImported = 0;

if (!$options['dry_run']) {
    $mysql->exec('SET FOREIGN_KEY_CHECKS = 0');
}

if ($options['full'] && !$options['dry_run']) {
    foreach (array_reverse($allTables) as $table) {
        if (table_exists($mysql, 'mysql', $table)) {
            $mysql->exec('TRUNCATE TABLE `' . $table . '`');
        }
    }
}

if ($options['data_only'] && !$options['dry_run']) {
    foreach (array_reverse($dataOnlyTables) as $table) {
        if ($table === 'users') {
            if ($options['replace_users']) {
                $mysql->exec('DELETE FROM `users`');
            }
            continue;
        }
        if (table_exists($mysql, 'mysql', $table) && table_exists($sqlite, 'sqlite', $table)) {
            $mysql->exec('DELETE FROM `' . $table . '`');
        }
    }
}

foreach ($tables as $table) {
    if (!table_exists($sqlite, 'sqlite', $table)) {
        echo "  skip {$table} (not in SQLite)\n";
        continue;
    }
    if (!table_exists($mysql, 'mysql', $table)) {
        echo "  skip {$table} (not in MySQL — run seed.php first)\n";
        continue;
    }

    $rows = fetch_rows($sqlite, $table);
    $sqliteCols = column_names($sqlite, 'sqlite', $table);
    $mysqlCols = column_names($mysql, 'mysql', $table);
    $columns = array_values(array_intersect($sqliteCols, $mysqlCols));

    if ($columns === []) {
        echo "  skip {$table} (no shared columns)\n";
        continue;
    }

    $imported = 0;
    $skipped = 0;

    foreach ($rows as $row) {
        if ($options['data_only']) {
            $row = remap_row($table, $row, $maps);
        }

        if ($table === 'users' && $options['data_only']) {
            $email = (string) ($row['email'] ?? '');
            $existing = $mysql->prepare('SELECT id FROM users WHERE email = ?');
            $existing->execute([$email]);
            $existingId = $existing->fetchColumn();
            if ($existingId !== false && (int) $existingId !== (int) $row['id']) {
                echo "  warn users: email {$email} exists with different id (MySQL {$existingId}, SQLite {$row['id']}) — skipping\n";
                $skipped++;
                continue;
            }
        }

        if ($options['dry_run']) {
            $imported++;
            continue;
        }

        try {
            if ($table === 'users' && $options['data_only']) {
                $email = (string) ($row['email'] ?? '');
                $check = $mysql->prepare('SELECT id FROM users WHERE email = ?');
                $check->execute([$email]);
                if ($check->fetchColumn() !== false) {
                    $sets = [];
                    $vals = [];
                    foreach ($columns as $col) {
                        if ($col === 'id' || $col === 'email') {
                            continue;
                        }
                        $sets[] = '`' . $col . '` = ?';
                        $vals[] = $row[$col] ?? null;
                    }
                    $vals[] = $email;
                    $mysql->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE email = ?')->execute($vals);
                    $imported++;
                    continue;
                }
            }
            insert_row($mysql, $table, $columns, $row);
            $imported++;
        } catch (PDOException $e) {
            if ((int) $e->getCode() === 23000) {
                echo "  warn {$table} id {$row['id']}: duplicate — " . $e->getMessage() . "\n";
                $skipped++;
                continue;
            }
            throw $e;
        }
    }

    if (!$options['dry_run'] && $imported > 0 && in_array('id', $columns, true)) {
        $maxId = (int) $mysql->query('SELECT COALESCE(MAX(id), 0) FROM `' . $table . '`')->fetchColumn();
        $mysql->exec('ALTER TABLE `' . $table . '` AUTO_INCREMENT = ' . ($maxId + 1));
    }

    $suffix = $skipped > 0 ? ", {$skipped} skipped" : '';
    echo sprintf("  %s: %d row(s)%s\n", $table, $imported, $suffix);
    $totalImported += $imported;
}

if (!$options['dry_run']) {
    $mysql->exec('SET FOREIGN_KEY_CHECKS = 1');
}

echo "\nDone. Imported {$totalImported} row(s).\n";
if ($options['data_only']) {
    echo "Verify: php scripts/seed.php --status\n";
}
