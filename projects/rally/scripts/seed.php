<?php

declare(strict_types=1);

/**
 * Rally database setup and seeding. CLI only.
 *
 * Usage:
 *   php scripts/seed.php              Apply schema + seed demo data
 *   php scripts/seed.php --fresh      Drop tables first (destructive)
 *   php scripts/seed.php --status     Print health checks (read-only)
 *
 * Existing installs missing multi-metric columns should run:
 *   php scripts/migrate.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use Rally\Core\Config;
use Rally\Core\Database;
use Rally\Services\Clock;
use Rally\Services\MatchService;
use Rally\Services\ResultIngestionService;
use Rally\Services\SettlementService;

$args = $argv;
array_shift($args);
$options = ['fresh' => false, 'status' => false];
foreach ($args as $arg) {
    if ($arg === '--fresh') {
        $options['fresh'] = true;
    } elseif ($arg === '--status') {
        $options['status'] = true;
    } else {
        fwrite(STDERR, "Unknown option: {$arg}\n");
        exit(1);
    }
}

$pdo = Database::pdo();
$driver = Database::driver();

function exec_schema(PDO $pdo, string $path): void
{
    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Cannot read schema: {$path}");
    }
    // Strip line comments
    $lines = explode("\n", $sql);
    $cleaned = [];
    foreach ($lines as $line) {
        $trim = ltrim($line);
        if (str_starts_with($trim, '--')) {
            continue;
        }
        $cleaned[] = $line;
    }
    $sql = implode("\n", $cleaned);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}

/** @return list<string> */
function list_tables(PDO $pdo, string $driver): array
{
    if ($driver === 'mysql') {
        $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
        return array_map(static fn(array $r): string => (string) $r[0], $rows);
    }
    $rows = Database::fetchAll("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    return array_map(static fn(array $r): string => (string) $r['name'], $rows);
}

function drop_all(PDO $pdo, string $driver): void
{
    $tables = list_tables($pdo, $driver);
    if ($driver === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            $pdo->exec('DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`');
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        return;
    }
    $pdo->exec('PRAGMA foreign_keys = OFF');
    foreach ($tables as $table) {
        $pdo->exec('DROP TABLE IF EXISTS "' . str_replace('"', '""', $table) . '"');
    }
    $pdo->exec('PRAGMA foreign_keys = ON');
}

if ($options['status']) {
    $tables = list_tables($pdo, $driver);
    $required = ['rly_users', 'rly_metric_types', 'rly_data_sources', 'rly_matches', 'rly_match_days', 'rly_match_day_results'];
    echo "Driver: {$driver}\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    foreach ($required as $t) {
        $ok = in_array($t, $tables, true) ? 'OK' : 'MISSING';
        echo "  {$t}: {$ok}\n";
    }
    if (in_array('rly_users', $tables, true)) {
        echo 'Users: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_users') . "\n";
        echo 'Matches: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_matches') . "\n";
        echo 'Match days: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_days') . "\n";
        echo 'Results: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_day_results') . "\n";
    }
    exit(0);
}

if ($options['fresh']) {
    echo "This will DROP all Rally tables. Type 'yes' to continue: ";
    $confirm = trim((string) fgets(STDIN));
    if ($confirm !== 'yes') {
        echo "Aborted.\n";
        exit(1);
    }
    drop_all($pdo, $driver);
    echo "Dropped tables.\n";
}

$schemaFile = $driver === 'mysql'
    ? __DIR__ . '/../database/schema.mysql.sql'
    : __DIR__ . '/../database/schema.sqlite.sql';

echo "Applying schema ({$driver})...\n";
exec_schema($pdo, $schemaFile);

// Existing DBs keep old rly_metric_types shape under CREATE TABLE IF NOT EXISTS.
// Ensure multi-metric columns exist before seeding into them.
require __DIR__ . '/migrate_columns.php';
migrate_rly_metric_types_columns($pdo, $driver);

$demoPassword = Config::string('demo.password', 'rally-demo-2026');
$passwordHash = password_hash($demoPassword, PASSWORD_DEFAULT);
$now = '2026-07-21 12:00:00';

// Fixed demonstration "now": mid-day on 2026-07-21 UTC.
Clock::setOverride(new DateTimeImmutable('2026-07-21T19:00:00Z')); // 12:00 PDT

echo "Seeding reference data...\n";

$pdo->exec('DELETE FROM rly_match_day_results');
$pdo->exec('DELETE FROM rly_match_days');
$pdo->exec('DELETE FROM rly_matches');
$pdo->exec('DELETE FROM rly_rate_events');
$pdo->exec('DELETE FROM rly_users');
$pdo->exec('DELETE FROM rly_metric_types');
$pdo->exec('DELETE FROM rly_data_sources');

$metrics = [
    [
        'steps', 'Daily Steps', 'steps', null, 'performance', 'daily_wins', 1, 14, 100,
        'Daily step totals contested as a multi-day series of head-to-head games.',
        null,
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
$metricIds = [];
foreach ($metrics as $row) {
    Database::run(
        'INSERT INTO rly_metric_types
         (slug, name, unit, display_unit, classification, scoring_strategy, higher_wins,
          default_length_days, default_tie_threshold, description, context_note, is_active, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)',
        [...$row, $now]
    );
    $metricIds[$row[0]] = (int) Database::fetchValue('SELECT id FROM rly_metric_types WHERE slug = ?', [$row[0]]);
}

$sources = [
    ['apple_watch', 'Apple Watch', 'watch'],
    ['iphone', 'iPhone', 'phone'],
    ['android_phone', 'Android Phone', 'phone'],
    ['fitbit', 'Fitbit', 'wearable'],
    ['garmin', 'Garmin', 'wearable'],
    ['simulated_watch', 'Simulated Watch', 'watch'],
    ['simulated_phone', 'Simulated Phone', 'phone'],
];
$sourceIds = [];
foreach ($sources as [$slug, $name, $class]) {
    Database::run(
        'INSERT INTO rly_data_sources (slug, name, source_class, is_active, created_at) VALUES (?, ?, ?, 1, ?)',
        [$slug, $name, $class, $now]
    );
    $sourceIds[$slug] = (int) Database::fetchValue('SELECT id FROM rly_data_sources WHERE slug = ?', [$slug]);
}

$players = [
    ['Iain', 'iain', 'iain@rally.demo', 'America/Los_Angeles', 'admin', 1],
    ['Mike', 'mike', 'mike@rally.demo', 'America/Los_Angeles', 'user', 1],
    ['Sarah', 'sarah', 'sarah@rally.demo', 'America/New_York', 'user', 1],
    ['Jordan', 'jordan', 'jordan@rally.demo', 'America/Chicago', 'user', 1],
    ['Elena', 'elena', 'elena@rally.demo', 'Europe/London', 'user', 1],
    ['Marcus', 'marcus', 'marcus@rally.demo', 'America/Denver', 'user', 1],
];
$userIds = [];
foreach ($players as [$name, $username, $email, $tz, $role, $sim]) {
    Database::run(
        'INSERT INTO rly_users (name, username, email, password_hash, role, simulation, timezone, status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$name, $username, $email, $passwordHash, $role, $sim, $tz, 'active', $now, $now]
    );
    $userIds[$username] = (int) Database::fetchValue('SELECT id FROM rly_users WHERE username = ?', [$username]);
}

/**
 * @param array<int, array{0:?int,1:?int}> $dayValues
 * @param array<int, string> $forceStatus
 */
function seed_match(
    int $metricId,
    int $playerA,
    int $playerB,
    int $sourceA,
    ?int $sourceB,
    string $startDate,
    string $timezone,
    int $tieThreshold,
    string $invitationStatus,
    string $initialStatus,
    array $dayValues,
    int $lengthDays = 14,
    array $forceStatus = [],
    string $metricSlug = 'steps'
): int {
    $now = Clock::nowUtcString();
    Database::run(
        'INSERT INTO rly_matches
         (metric_type_id, player_a_user_id, player_b_user_id, player_a_source_id, player_b_source_id,
          created_by_user_id, start_date, length_days, timezone, tie_threshold, status, invitation_status,
          created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            $metricId, $playerA, $playerB, $sourceA, $sourceB, $playerA,
            $startDate, $lengthDays, $timezone, $tieThreshold,
            $initialStatus, $invitationStatus, $now, $now,
        ]
    );
    $matchId = Database::lastInsertId();
    MatchService::createMatchDays($matchId, $startDate, $lengthDays, $timezone, $now);

    $days = Database::fetchAll(
        'SELECT * FROM rly_match_days WHERE match_id = ? ORDER BY day_number',
        [$matchId]
    );
    $dayByNum = [];
    foreach ($days as $d) {
        $dayByNum[(int) $d['day_number']] = $d;
    }

    foreach ($dayValues as $dayNum => [$valA, $valB]) {
        $day = $dayByNum[$dayNum] ?? null;
        if ($day === null) {
            continue;
        }
        if ($valA !== null) {
            ResultIngestionService::ingest([
                'user_id' => $playerA,
                'match_day_id' => (int) $day['id'],
                'metric_type' => $metricSlug,
                'value' => $valA,
                'data_source' => $sourceA,
                'source_record_key' => "seed-{$matchId}-{$dayNum}-a",
                'is_manual' => false,
                'ingested_at' => $day['competition_date'] . 'T20:00:00Z',
            ], true);
        }
        if ($valB !== null && $sourceB !== null) {
            ResultIngestionService::ingest([
                'user_id' => $playerB,
                'match_day_id' => (int) $day['id'],
                'metric_type' => $metricSlug,
                'value' => $valB,
                'data_source' => $sourceB,
                'source_record_key' => "seed-{$matchId}-{$dayNum}-b",
                'is_manual' => false,
                'ingested_at' => $day['competition_date'] . 'T20:30:00Z',
            ], true);
        }
    }

    SettlementService::refreshMatch($matchId);

    foreach ($forceStatus as $dayNum => $status) {
        $day = $dayByNum[$dayNum] ?? null;
        if ($day === null) {
            continue;
        }
        $officialAt = in_array($status, ['official', 'void'], true) ? $now : null;
        Database::run(
            'UPDATE rly_match_days SET status = ?, official_at = ?, updated_at = ? WHERE id = ?',
            [$status, $officialAt, $now, (int) $day['id']]
        );
    }

    SettlementService::refreshMatch($matchId);
    return $matchId;
}

echo "Seeding demo matches...\n";
$tzLA = 'America/Los_Angeles';

// ---- STEPS ----
$showcaseValues = [
    1 => [12450, 9800],
    2 => [10200, 9100],
    3 => [8700, 11200],
    4 => [11800, 10500],
    5 => [9400, 10100],
    6 => [15200, 11000],
    7 => [9900, 12100],
    8 => [11248, 9731],
    9 => [8421, 7984],
];
$forceOfficial = [];
for ($d = 1; $d <= 8; $d++) {
    $forceOfficial[$d] = 'official';
}
$forceOfficial[9] = 'live';
$showcaseId = seed_match(
    $metricIds['steps'], $userIds['iain'], $userIds['mike'],
    $sourceIds['apple_watch'], $sourceIds['apple_watch'],
    '2026-07-13', $tzLA, 100, 'accepted', 'active', $showcaseValues, 14, $forceOfficial, 'steps'
);
Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9", [$showcaseId]);
Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$showcaseId]);

// Completed close steps
$closeSteps = [];
for ($d = 1; $d <= 14; $d++) {
    if ($d === 14) {
        $closeSteps[$d] = [10050, 10020];
    } elseif (in_array($d, [1, 2, 4, 5, 7, 9, 11, 13], true)) {
        $closeSteps[$d] = [11000 + $d * 50, 9000 + $d * 40];
    } else {
        $closeSteps[$d] = [8500 + $d * 30, 10500 + $d * 45];
    }
}
seed_match(
    $metricIds['steps'], $userIds['sarah'], $userIds['mike'],
    $sourceIds['fitbit'], $sourceIds['fitbit'],
    '2026-06-20', 'America/New_York', 100, 'accepted', 'completed', $closeSteps, 14, array_fill(1, 14, 'official'), 'steps'
);

// Completed decisive steps
$decisive = [];
for ($d = 1; $d <= 14; $d++) {
    if ($d <= 9) {
        $decisive[$d] = [12500 + $d * 20, 9000 + $d * 15];
    } else {
        $decisive[$d] = [8800 + $d, 11000 + $d * 10];
    }
}
seed_match(
    $metricIds['steps'], $userIds['jordan'], $userIds['elena'],
    $sourceIds['garmin'], $sourceIds['garmin'],
    '2026-06-01', 'America/Chicago', 100, 'accepted', 'completed', $decisive, 14, array_fill(1, 14, 'official'), 'steps'
);

// Source mismatch steps
$mmValues = [
    1 => [14000, 12500], 2 => [9000, 11000], 3 => [10500, 10200], 4 => [8000, 8700],
    5 => [11500, 9800], 6 => [10000, 10050], 7 => [12200, 10100], 8 => [9500, 9900], 9 => [5000, 4800],
];
$mmForce = array_replace(array_fill(1, 7, 'official'), [8 => 'pending', 9 => 'live']);
seed_match(
    $metricIds['steps'], $userIds['iain'], $userIds['sarah'],
    $sourceIds['apple_watch'], $sourceIds['iphone'],
    '2026-07-14', $tzLA, 100, 'accepted', 'active', $mmValues, 14, $mmForce, 'steps'
);

// Void / settling steps
$voidValues = [];
for ($d = 1; $d <= 12; $d++) {
    if ($d === 5) {
        $voidValues[$d] = [9000, null];
    } elseif ($d % 2 === 0) {
        $voidValues[$d] = [10000, 12000];
    } else {
        $voidValues[$d] = [13000, 11000];
    }
}
$voidForce = array_fill(1, 12, 'official');
$voidForce[5] = 'void';
$voidForce[13] = 'pending';
$voidForce[14] = 'pending';
seed_match(
    $metricIds['steps'], $userIds['marcus'], $userIds['mike'],
    $sourceIds['android_phone'], $sourceIds['android_phone'],
    '2026-07-07', 'America/Denver', 100, 'accepted', 'settling', $voidValues, 14, $voidForce, 'steps'
);

seed_match(
    $metricIds['steps'], $userIds['mike'], $userIds['jordan'],
    $sourceIds['apple_watch'], null,
    '2026-07-25', $tzLA, 100, 'pending', 'invited', [], 14, [], 'steps'
);

// ---- ACTIVE MINUTES ----
$amActive = [
    1 => [62, 55], 2 => [48, 71], 3 => [90, 84], 4 => [40, 38],
    5 => [105, 92], 6 => [55, 60], 7 => [72, 68], 8 => [88, 95], 9 => [33, 29],
];
$amForce = array_replace(array_fill(1, 8, 'official'), [9 => 'live']);
seed_match(
    $metricIds['active_minutes'], $userIds['elena'], $userIds['marcus'],
    $sourceIds['apple_watch'], $sourceIds['garmin'],
    '2026-07-13', 'Europe/London', 2, 'accepted', 'active', $amActive, 14, $amForce, 'active_minutes'
);

$amDone = [];
for ($d = 1; $d <= 14; $d++) {
    $amDone[$d] = $d % 3 === 0 ? [45, 70] : [80 + $d, 55 + $d];
}
seed_match(
    $metricIds['active_minutes'], $userIds['sarah'], $userIds['jordan'],
    $sourceIds['fitbit'], $sourceIds['fitbit'],
    '2026-06-10', 'America/New_York', 2, 'accepted', 'completed', $amDone, 14, array_fill(1, 14, 'official'), 'active_minutes'
);

// ---- RHR (series average, 7 days) ----
// Start 2026-07-15 → day 7 = Jul 21 live
$rhr = [
    1 => [58, 61], 2 => [57, 60], 3 => [59, 59], // daily tie / within
    4 => [56, 62], 5 => [58, 60], 6 => [57, 58], 7 => [55, 59],
];
$rhrForce = array_replace(array_fill(1, 6, 'official'), [7 => 'live']);
seed_match(
    $metricIds['resting_heart_rate'], $userIds['iain'], $userIds['elena'],
    $sourceIds['apple_watch'], $sourceIds['apple_watch'],
    '2026-07-15', $tzLA, 1, 'accepted', 'active', $rhr, 7, $rhrForce, 'resting_heart_rate'
);

// Completed near-draw RHR (avg differ by < 1 → draw) — values designed for avg tie
$rhrDraw = [
    1 => [60, 61], 2 => [61, 60], 3 => [59, 60], 4 => [60, 59], 5 => [60, 60], 6 => [61, 61], 7 => [60, 60],
];
seed_match(
    $metricIds['resting_heart_rate'], $userIds['mike'], $userIds['marcus'],
    $sourceIds['garmin'], $sourceIds['garmin'],
    '2026-06-01', 'America/Denver', 1, 'accepted', 'completed', $rhrDraw, 7, array_fill(1, 7, 'official'), 'resting_heart_rate'
);

// ---- HRV ----
$hrv = [
    1 => [58, 52], 2 => [61, 55], 3 => [54, 57], 4 => [63, 50],
    5 => [59, 58], 6 => [62, 53], 7 => [60, 56],
];
$hrvForce = array_replace(array_fill(1, 5, 'official'), [6 => 'pending', 7 => 'live']);
seed_match(
    $metricIds['hrv'], $userIds['sarah'], $userIds['elena'],
    $sourceIds['apple_watch'], $sourceIds['fitbit'],
    '2026-07-15', 'America/New_York', 2, 'accepted', 'active', $hrv, 7, $hrvForce, 'hrv'
);

$hrvDone = [
    1 => [48, 55], 2 => [52, 51], 3 => [60, 47], 4 => [55, 58], 5 => [50, 49], 6 => [57, 53], 7 => [54, 56],
];
seed_match(
    $metricIds['hrv'], $userIds['jordan'], $userIds['mike'],
    $sourceIds['garmin'], $sourceIds['apple_watch'],
    '2026-06-05', 'America/Chicago', 2, 'accepted', 'completed', $hrvDone, 7, array_fill(1, 7, 'official'), 'hrv'
);

// ---- SLEEP DURATION (minutes) ----
$sleep = [
    1 => [430, 401], // 7h10 / 6h41
    2 => [455, 470],
    3 => [390, 405],
    4 => [480, 445],
    5 => [420, 418], // within 15 threshold daily
    6 => [440, 460],
    7 => [410, 395],
];
$sleepForce = array_replace(array_fill(1, 5, 'official'), [6 => 'pending', 7 => 'live']);
seed_match(
    $metricIds['sleep_duration'], $userIds['marcus'], $userIds['jordan'],
    $sourceIds['fitbit'], $sourceIds['garmin'],
    '2026-07-15', 'America/Denver', 15, 'accepted', 'active', $sleep, 7, $sleepForce, 'sleep_duration'
);

$sleepDone = [
    1 => [400, 420], 2 => [450, 430], 3 => [380, 410], 4 => [470, 440],
    5 => [425, 425], 6 => [460, 405], 7 => [415, 450],
];
seed_match(
    $metricIds['sleep_duration'], $userIds['iain'], $userIds['sarah'],
    $sourceIds['apple_watch'], $sourceIds['apple_watch'],
    '2026-06-08', $tzLA, 15, 'accepted', 'completed', $sleepDone, 7, array_fill(1, 7, 'official'), 'sleep_duration'
);

// Upcoming scheduled steps
seed_match(
    $metricIds['steps'], $userIds['sarah'], $userIds['jordan'],
    $sourceIds['fitbit'], $sourceIds['garmin'],
    '2026-07-22', 'America/New_York', 100, 'accepted', 'scheduled', [], 14, [], 'steps'
);

SettlementService::refreshMatch($showcaseId);
for ($d = 1; $d <= 8; $d++) {
    Database::run(
        "UPDATE rly_match_days SET status = 'official', official_at = COALESCE(official_at, ?), updated_at = ? WHERE match_id = ? AND day_number = ?",
        [Clock::nowUtcString(), Clock::nowUtcString(), $showcaseId, $d]
    );
}
Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9", [$showcaseId]);
Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$showcaseId]);

echo "\nRally multi-metric seed complete.\n";
echo "Demo password for all seeded players: {$demoPassword}\n";
echo "Accounts:\n";
foreach ($players as [$name, $username, $email]) {
    echo "  {$name} <{$email}> (@{$username})\n";
}
echo "Metrics: " . implode(', ', array_keys($metricIds)) . "\n";
echo "Simulated clock override: 2026-07-21T19:00:00Z (noon PDT)\n";
echo "Showcase steps match id: {$showcaseId} (Iain vs Mike)\n";
echo "Run: php -S localhost:8091 -t public public/index.php\n";
