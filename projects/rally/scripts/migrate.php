<?php

declare(strict_types=1);

/**
 * In-place Rally schema upgrades for existing installs (Hostinger-safe).
 * Does NOT drop data. Idempotent — safe to re-run.
 *
 * Usage:
 *   php scripts/migrate.php
 *   php scripts/migrate.php --status
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/migrate_columns.php';

use Rally\Core\Database;

$args = $argv;
array_shift($args);
$statusOnly = in_array('--status', $args, true);

$pdo = Database::pdo();
$driver = Database::driver();

echo "Rally migrate ({$driver})\n";

$tables = rally_list_tables($pdo, $driver);

if (!in_array('rly_metric_types', $tables, true)) {
    fwrite(STDERR, "rly_metric_types missing. Run: php scripts/seed.php\n");
    exit(1);
}

$requiredMetricCols = [
    'display_unit',
    'classification',
    'scoring_strategy',
    'default_length_days',
    'default_tie_threshold',
    'description',
    'context_note',
];

$requiredMatchCols = [
    'competition_type',
    'baseline_tie_threshold',
];

$requiredTables = [
    'rly_user_metric_days',
    'rly_match_baselines',
];

if ($statusOnly) {
    echo "rly_metric_types columns:\n";
    $missing = 0;
    foreach ($requiredMetricCols as $col) {
        $ok = rally_column_exists($pdo, $driver, 'rly_metric_types', $col);
        if (!$ok) {
            $missing++;
        }
        echo '  ' . $col . ': ' . ($ok ? 'OK' : 'MISSING') . "\n";
    }
    echo "rly_matches columns:\n";
    foreach ($requiredMatchCols as $col) {
        $ok = rally_column_exists($pdo, $driver, 'rly_matches', $col);
        if (!$ok) {
            $missing++;
        }
        echo '  ' . $col . ': ' . ($ok ? 'OK' : 'MISSING') . "\n";
    }
    if (rally_column_exists($pdo, $driver, 'rly_matches', 'competition_mode')) {
        echo "  competition_mode: PRESENT (should be migrated away)\n";
        $missing++;
    } else {
        echo "  competition_mode: absent (OK)\n";
    }
    echo "History tables:\n";
    foreach ($requiredTables as $table) {
        $ok = rally_table_exists($pdo, $driver, $table);
        if (!$ok) {
            $missing++;
        }
        echo '  ' . $table . ': ' . ($ok ? 'OK' : 'MISSING') . "\n";
    }
    $metricCount = (int) Database::fetchValue('SELECT COUNT(*) FROM rly_metric_types');
    echo "Metric rows: {$metricCount}\n";
    if (rally_table_exists($pdo, $driver, 'rly_user_metric_days')) {
        echo 'Canonical observations: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_user_metric_days') . "\n";
    }
    if (rally_table_exists($pdo, $driver, 'rly_match_baselines')) {
        echo 'Frozen baselines: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_baselines') . "\n";
    }
    exit($missing > 0 ? 1 : 0);
}

migrate_rly_metric_types_columns($pdo, $driver);
migrate_rly_competition_and_history($pdo, $driver);

$now = date('Y-m-d H:i:s');
$metrics = [
    [
        'steps', 'Daily Steps', 'steps', null, 'performance', 'daily_wins', 1, 14, 100,
        'Daily step totals contested as a multi-day series of head-to-head games.', null,
    ],
    [
        'active_minutes', 'Active Minutes', 'min', null, 'performance', 'daily_wins', 1, 14, 2,
        'Minutes of elevated activity contested day by day. Platforms may classify activity differently.',
        'Active-minute definitions vary by device. Rally compares the declared recorded values.',
    ],
    [
        'resting_heart_rate', 'Resting Heart Rate', 'bpm', null, 'health_comparison', 'series_average', 0, 7, 1,
        'Compare recorded resting heart-rate averages across the series. Lower official average wins.',
        'Resting heart rate varies by person and context. Rally compares recorded values for this series and does not interpret them medically.',
    ],
    [
        'hrv', 'Heart Rate Variability', 'ms', null, 'health_comparison', 'series_average', 1, 7, 2,
        'Compare recorded HRV averages across the series. Higher official average wins.',
        'HRV varies by person and device. Rally compares the recorded values for this series and does not interpret them medically.',
    ],
    [
        'sleep_duration', 'Sleep Duration', 'min', 'hours_minutes', 'health_comparison', 'series_average', 1, 7, 15,
        'Compare recorded sleep duration averages. Higher official average wins. This is duration, not sleep quality.',
        'Sleep needs vary. Rally compares recorded duration for this series without judging sleep quality or overall health.',
    ],
];

echo "Upserting metric definitions...\n";
foreach ($metrics as $row) {
    [$slug, $name, $unit, $displayUnit, $classification, $strategy, $higherWins, $length, $threshold, $description, $contextNote] = $row;
    $existing = Database::fetch('SELECT id FROM rly_metric_types WHERE slug = ?', [$slug]);
    if ($existing === null) {
        Database::run(
            'INSERT INTO rly_metric_types
             (slug, name, unit, display_unit, classification, scoring_strategy, higher_wins,
              default_length_days, default_tie_threshold, description, context_note, is_active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)',
            [$slug, $name, $unit, $displayUnit, $classification, $strategy, $higherWins, $length, $threshold, $description, $contextNote, $now]
        );
        echo "  inserted {$slug}\n";
        continue;
    }
    Database::run(
        'UPDATE rly_metric_types SET
            name = ?, unit = ?, display_unit = ?, classification = ?, scoring_strategy = ?,
            higher_wins = ?, default_length_days = ?, default_tie_threshold = ?,
            description = ?, context_note = ?, is_active = 1
         WHERE slug = ?',
        [$name, $unit, $displayUnit, $classification, $strategy, $higherWins, $length, $threshold, $description, $contextNote, $slug]
    );
    echo "  updated {$slug}\n";
}

echo "\nMigrate complete. Classic/Baseline competition schema is ready.\n";
echo "Verify: php scripts/migrate.php --status\n";
echo "Optional full demo reseed: php scripts/seed.php\n";
