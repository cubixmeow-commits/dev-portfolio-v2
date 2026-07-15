<?php

declare(strict_types=1);

/**
 * Shared in-place column upgrades for rly_metric_types.
 * Included by scripts/migrate.php and scripts/seed.php.
 */

/** @return list<string> */
function rally_table_columns(PDO $pdo, string $driver, string $table): array
{
    if ($driver === 'mysql') {
        $rows = $pdo->query('SHOW COLUMNS FROM `' . str_replace('`', '``', $table) . '`')->fetchAll(PDO::FETCH_ASSOC);
        return array_map(static fn(array $r): string => (string) $r['Field'], $rows);
    }
    $rows = $pdo->query('PRAGMA table_info("' . str_replace('"', '""', $table) . '")')->fetchAll(PDO::FETCH_ASSOC);
    return array_map(static fn(array $r): string => (string) $r['name'], $rows);
}

function rally_column_exists(PDO $pdo, string $driver, string $table, string $column): bool
{
    return in_array($column, rally_table_columns($pdo, $driver, $table), true);
}

function rally_add_column(PDO $pdo, string $driver, string $table, string $column, string $definition, bool $quiet = false): void
{
    if (rally_column_exists($pdo, $driver, $table, $column)) {
        if (!$quiet) {
            echo "  skip  {$table}.{$column} (exists)\n";
        }
        return;
    }
    $sql = $driver === 'mysql'
        ? 'ALTER TABLE `' . str_replace('`', '``', $table) . '` ADD COLUMN `' . str_replace('`', '``', $column) . '` ' . $definition
        : 'ALTER TABLE "' . str_replace('"', '""', $table) . '" ADD COLUMN "' . str_replace('"', '""', $column) . '" ' . $definition;
    $pdo->exec($sql);
    if (!$quiet) {
        echo "  added {$table}.{$column}\n";
    }
}

/**
 * Ensure multi-metric columns exist on rly_metric_types (idempotent).
 */
function migrate_rly_metric_types_columns(PDO $pdo, string $driver, bool $quiet = false): void
{
    $required = [
        'display_unit' => $driver === 'mysql' ? 'VARCHAR(40) NULL' : 'TEXT NULL',
        'classification' => $driver === 'mysql'
            ? "VARCHAR(40) NOT NULL DEFAULT 'performance'"
            : "TEXT NOT NULL DEFAULT 'performance'",
        'scoring_strategy' => $driver === 'mysql'
            ? "VARCHAR(40) NOT NULL DEFAULT 'daily_wins'"
            : "TEXT NOT NULL DEFAULT 'daily_wins'",
        'default_length_days' => $driver === 'mysql'
            ? 'INT UNSIGNED NOT NULL DEFAULT 14'
            : 'INTEGER NOT NULL DEFAULT 14',
        'default_tie_threshold' => $driver === 'mysql'
            ? 'INT UNSIGNED NOT NULL DEFAULT 100'
            : 'INTEGER NOT NULL DEFAULT 100',
        'description' => $driver === 'mysql'
            ? "TEXT NOT NULL"
            : "TEXT NOT NULL DEFAULT ''",
        'context_note' => $driver === 'mysql' ? 'TEXT NULL' : 'TEXT NULL',
    ];

    if (!$quiet) {
        echo "Ensuring rly_metric_types columns...\n";
    }
    foreach ($required as $column => $definition) {
        rally_add_column($pdo, $driver, 'rly_metric_types', $column, $definition, $quiet);
    }

    if ($driver === 'mysql' && rally_column_exists($pdo, $driver, 'rly_metric_types', 'description')) {
        $pdo->exec("UPDATE rly_metric_types SET description = '' WHERE description IS NULL");
    }
}
