<?php

declare(strict_types=1);

/**
 * Shared in-place column and table upgrades for Rally.
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

/** @return list<string> */
function rally_list_tables(PDO $pdo, string $driver): array
{
    if ($driver === 'mysql') {
        $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
        return array_map(static fn(array $r): string => (string) $r[0], $rows);
    }
    $rows = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_ASSOC);
    return array_map(static fn(array $r): string => (string) $r['name'], $rows);
}

function rally_table_exists(PDO $pdo, string $driver, string $table): bool
{
    return in_array($table, rally_list_tables($pdo, $driver), true);
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

function rally_drop_column(PDO $pdo, string $driver, string $table, string $column, bool $quiet = false): void
{
    if (!rally_column_exists($pdo, $driver, $table, $column)) {
        if (!$quiet) {
            echo "  skip  drop {$table}.{$column} (missing)\n";
        }
        return;
    }
    if ($driver === 'mysql') {
        $pdo->exec(
            'ALTER TABLE `' . str_replace('`', '``', $table) . '` DROP COLUMN `' . str_replace('`', '``', $column) . '`'
        );
    } else {
        // SQLite 3.35+ supports DROP COLUMN.
        try {
            $pdo->exec(
                'ALTER TABLE "' . str_replace('"', '""', $table) . '" DROP COLUMN "' . str_replace('"', '""', $column) . '"'
            );
        } catch (Throwable $e) {
            if (!$quiet) {
                echo "  warn  could not drop {$table}.{$column}: " . $e->getMessage() . "\n";
            }
            return;
        }
    }
    if (!$quiet) {
        echo "  dropped {$table}.{$column}\n";
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

/**
 * Ensure Classic/Baseline competition columns and history tables exist (idempotent).
 */
function migrate_rly_competition_and_history(PDO $pdo, string $driver, bool $quiet = false): void
{
    if (!$quiet) {
        echo "Ensuring Classic/Baseline competition schema...\n";
    }

    // Handle unfinished competition_mode column if present.
    if (rally_column_exists($pdo, $driver, 'rly_matches', 'competition_mode')
        && !rally_column_exists($pdo, $driver, 'rly_matches', 'competition_type')) {
        rally_add_column(
            $pdo,
            $driver,
            'rly_matches',
            'competition_type',
            $driver === 'mysql'
                ? "VARCHAR(40) NOT NULL DEFAULT 'classic'"
                : "TEXT NOT NULL DEFAULT 'classic'",
            $quiet
        );
        $pdo->exec("UPDATE rly_matches SET competition_type = 'classic' WHERE competition_mode IN ('direct', 'classic', '') OR competition_mode IS NULL");
        $pdo->exec("UPDATE rly_matches SET competition_type = 'baseline' WHERE competition_mode IN ('baseline', 'baseline_improvement')");
        rally_drop_column($pdo, $driver, 'rly_matches', 'competition_mode', $quiet);
    }

    rally_add_column(
        $pdo,
        $driver,
        'rly_matches',
        'competition_type',
        $driver === 'mysql'
            ? "VARCHAR(40) NOT NULL DEFAULT 'classic'"
            : "TEXT NOT NULL DEFAULT 'classic'",
        $quiet
    );
    rally_add_column(
        $pdo,
        $driver,
        'rly_matches',
        'baseline_tie_threshold',
        $driver === 'mysql' ? 'DECIMAL(6,2) NULL' : 'REAL NULL',
        $quiet
    );

    // Normalize legacy labels if any slipped in.
    if (rally_column_exists($pdo, $driver, 'rly_matches', 'competition_type')) {
        $pdo->exec("UPDATE rly_matches SET competition_type = 'classic' WHERE competition_type IN ('direct', '') OR competition_type IS NULL");
        $pdo->exec("UPDATE rly_matches SET competition_type = 'baseline' WHERE competition_type = 'baseline_improvement'");
        $pdo->exec("UPDATE rly_matches SET competition_type = 'classic' WHERE competition_type NOT IN ('classic', 'baseline')");
    }

    // Drop leftover competition_mode if both columns somehow exist.
    if (rally_column_exists($pdo, $driver, 'rly_matches', 'competition_mode')
        && rally_column_exists($pdo, $driver, 'rly_matches', 'competition_type')) {
        rally_drop_column($pdo, $driver, 'rly_matches', 'competition_mode', $quiet);
    }

    if (!rally_table_exists($pdo, $driver, 'rly_user_metric_days')) {
        if ($driver === 'mysql') {
            $pdo->exec(
                "CREATE TABLE rly_user_metric_days (
                    id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    user_id           INT UNSIGNED NOT NULL,
                    metric_type_id    INT UNSIGNED NOT NULL,
                    data_source_id    INT UNSIGNED NOT NULL,
                    observation_date  DATE NOT NULL,
                    metric_value      INT NOT NULL,
                    is_manual         TINYINT(1) NOT NULL DEFAULT 0,
                    source_record_key VARCHAR(190) NULL,
                    source_timezone   VARCHAR(64) NULL,
                    observed_at       DATETIME NULL,
                    ingested_at       DATETIME NOT NULL,
                    created_at        DATETIME NOT NULL,
                    updated_at        DATETIME NOT NULL,
                    UNIQUE KEY uq_rly_umd_user_metric_source_date (user_id, metric_type_id, data_source_id, observation_date),
                    KEY idx_rly_umd_user_metric_date (user_id, metric_type_id, observation_date),
                    KEY idx_rly_umd_source_key (source_record_key),
                    CONSTRAINT fk_rly_umd_user FOREIGN KEY (user_id) REFERENCES rly_users(id),
                    CONSTRAINT fk_rly_umd_metric FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id),
                    CONSTRAINT fk_rly_umd_source FOREIGN KEY (data_source_id) REFERENCES rly_data_sources(id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } else {
            $pdo->exec(
                "CREATE TABLE rly_user_metric_days (
                    id                INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id           INTEGER NOT NULL,
                    metric_type_id    INTEGER NOT NULL,
                    data_source_id    INTEGER NOT NULL,
                    observation_date  TEXT NOT NULL,
                    metric_value      INTEGER NOT NULL,
                    is_manual         INTEGER NOT NULL DEFAULT 0,
                    source_record_key TEXT NULL,
                    source_timezone   TEXT NULL,
                    observed_at       TEXT NULL,
                    ingested_at       TEXT NOT NULL,
                    created_at        TEXT NOT NULL,
                    updated_at        TEXT NOT NULL,
                    UNIQUE (user_id, metric_type_id, data_source_id, observation_date),
                    FOREIGN KEY (user_id) REFERENCES rly_users(id),
                    FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id),
                    FOREIGN KEY (data_source_id) REFERENCES rly_data_sources(id)
                )"
            );
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_rly_umd_user_metric_date ON rly_user_metric_days (user_id, metric_type_id, observation_date)');
            $pdo->exec('CREATE INDEX IF NOT EXISTS idx_rly_umd_source_key ON rly_user_metric_days (source_record_key)');
        }
        if (!$quiet) {
            echo "  created rly_user_metric_days\n";
        }
    } elseif (!$quiet) {
        echo "  skip  rly_user_metric_days (exists)\n";
    }

    if (!rally_table_exists($pdo, $driver, 'rly_match_baselines')) {
        if ($driver === 'mysql') {
            $pdo->exec(
                "CREATE TABLE rly_match_baselines (
                    id                           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    match_id                     INT UNSIGNED NOT NULL,
                    user_id                      INT UNSIGNED NOT NULL,
                    metric_type_id               INT UNSIGNED NOT NULL,
                    baseline_mean                DECIMAL(14,4) NOT NULL,
                    baseline_median              DECIMAL(14,4) NOT NULL,
                    baseline_standard_deviation  DECIMAL(14,4) NOT NULL,
                    baseline_minimum             INT NOT NULL,
                    baseline_maximum             INT NOT NULL,
                    sample_count                 INT UNSIGNED NOT NULL,
                    window_start_date            DATE NOT NULL,
                    window_end_date              DATE NOT NULL,
                    calculated_at                DATETIME NOT NULL,
                    created_at                   DATETIME NOT NULL,
                    UNIQUE KEY uq_rly_match_baselines_match_user (match_id, user_id),
                    CONSTRAINT fk_rly_baselines_match FOREIGN KEY (match_id) REFERENCES rly_matches(id) ON DELETE CASCADE,
                    CONSTRAINT fk_rly_baselines_user FOREIGN KEY (user_id) REFERENCES rly_users(id),
                    CONSTRAINT fk_rly_baselines_metric FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } else {
            $pdo->exec(
                "CREATE TABLE rly_match_baselines (
                    id                           INTEGER PRIMARY KEY AUTOINCREMENT,
                    match_id                     INTEGER NOT NULL,
                    user_id                      INTEGER NOT NULL,
                    metric_type_id               INTEGER NOT NULL,
                    baseline_mean                REAL NOT NULL,
                    baseline_median              REAL NOT NULL,
                    baseline_standard_deviation  REAL NOT NULL,
                    baseline_minimum             INTEGER NOT NULL,
                    baseline_maximum             INTEGER NOT NULL,
                    sample_count                 INTEGER NOT NULL,
                    window_start_date            TEXT NOT NULL,
                    window_end_date              TEXT NOT NULL,
                    calculated_at                TEXT NOT NULL,
                    created_at                   TEXT NOT NULL,
                    UNIQUE (match_id, user_id),
                    FOREIGN KEY (match_id) REFERENCES rly_matches(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES rly_users(id),
                    FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id)
                )"
            );
        }
        if (!$quiet) {
            echo "  created rly_match_baselines\n";
        }
    } elseif (!$quiet) {
        echo "  skip  rly_match_baselines (exists)\n";
    }
}
