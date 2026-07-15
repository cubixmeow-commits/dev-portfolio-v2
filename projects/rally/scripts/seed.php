<?php

declare(strict_types=1);

/**
 * Rally database setup and seeding. CLI only.
 *
 * Usage:
 *   php scripts/seed.php              Apply schema + seed demo data
 *   php scripts/seed.php --fresh      Drop tables first (destructive)
 *   php scripts/seed.php --status     Print health checks (read-only)
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

Database::run(
    'INSERT INTO rly_metric_types (slug, name, unit, higher_wins, is_active, created_at) VALUES (?, ?, ?, 1, 1, ?)',
    ['steps', 'Daily Steps', 'steps', $now]
);
$metricId = (int) Database::fetchValue('SELECT id FROM rly_metric_types WHERE slug = ?', ['steps']);

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
    // name, username, email, timezone, role, simulation
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
 * Bootstrap match shell (users/days), then ingest results through the service.
 *
 * @param array<int, array{0:?int,1:?int}> $dayValues Map day_number => [a, b] null = missing
 * @param array<int, string> $forceStatus Optional day_number => status override after lifecycle
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
    array $forceStatus = []
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
                'metric_type' => 'steps',
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
                'metric_type' => 'steps',
                'value' => $valB,
                'data_source' => $sourceB,
                'source_record_key' => "seed-{$matchId}-{$dayNum}-b",
                'is_manual' => false,
                'ingested_at' => $day['competition_date'] . 'T20:30:00Z',
            ], true);
        }
    }

    SettlementService::refreshMatch($matchId);

    // Force-settle days that should already be official/void for the demo timeline.
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

// 1) Primary showcase: Iain vs Mike — dramatic active, days 1-8 official (5–3), day 9 live, includes close & blowout.
// Pattern days 1-8: A A B A B A A B → 5–3. Day 4 margin within drama; include a near-tie elsewhere.
$showcaseValues = [
    1 => [12450, 9800],   // A blowout
    2 => [10200, 9100],   // A
    3 => [8700, 11200],   // B
    4 => [11800, 10500],  // A
    5 => [9400, 10100],   // B close
    6 => [15200, 11000],  // A
    7 => [13100, 12000],  // A
    8 => [9731, 11248],   // B (Mike) — showcase "game 8" flipped for history variety; wait, example has Iain winning game 8
];
// Re-align to example GAME 8: Iain 11248 vs Mike 9731
$showcaseValues[8] = [11248, 9731]; // A → series 5–3 going into game 9? Days 1-8: A A B A B A A A = 6-2
// Spec scoreboard shows 5–3 before game 9. Adjust day 7 to B:
$showcaseValues[7] = [9900, 12100]; // B → A A B A B A B A = 5–3 ✓
$showcaseValues[9] = [8421, 7984];  // LIVE today
// Day 10+ empty

$forceOfficial = [];
for ($d = 1; $d <= 8; $d++) {
    $forceOfficial[$d] = 'official';
}
$forceOfficial[9] = 'live';

$showcaseId = seed_match(
    $metricId,
    $userIds['iain'],
    $userIds['mike'],
    $sourceIds['apple_watch'],
    $sourceIds['apple_watch'],
    '2026-07-13',
    $tzLA,
    100,
    'accepted',
    'active',
    $showcaseValues,
    14,
    $forceOfficial
);

// Force day 9 live explicitly (refresh may already set it).
$day9 = Database::fetch('SELECT id FROM rly_match_days WHERE match_id = ? AND day_number = 9', [$showcaseId]);
if ($day9) {
    Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL, updated_at = ? WHERE id = ?", [Clock::nowUtcString(), (int) $day9['id']]);
}

// 2) Upcoming match: Sarah vs Jordan starting tomorrow
seed_match(
    $metricId,
    $userIds['sarah'],
    $userIds['jordan'],
    $sourceIds['fitbit'],
    $sourceIds['garmin'],
    '2026-07-22',
    'America/New_York',
    100,
    'accepted',
    'scheduled',
    [],
    14,
    []
);

// 3) Newly active: Elena vs Marcus — started yesterday, day 1 pending-ish / day 2 live
$newActiveValues = [
    1 => [7800, 8100],
    2 => [3200, 2900], // live partial day
];
$newForce = [1 => 'pending', 2 => 'live'];
seed_match(
    $metricId,
    $userIds['elena'],
    $userIds['marcus'],
    $sourceIds['apple_watch'],
    $sourceIds['apple_watch'],
    '2026-07-20',
    'Europe/London',
    100,
    'accepted',
    'active',
    $newActiveValues,
    14,
    $newForce
);

// 4) Completed decisive match: Sarah vs Mike — Sarah wins 8–5 with 1 tie (14 days)
$completed = [];
// Build 14 days of results. Start 2026-06-20.
$aWins = [1, 2, 4, 5, 7, 9, 11, 13]; // 8 wins for A
$bWins = [3, 6, 8, 10, 12]; // 5 wins
// day 14 tie
for ($d = 1; $d <= 14; $d++) {
    if ($d === 14) {
        $completed[$d] = [10050, 10020]; // diff 30 < 100 tie
    } elseif (in_array($d, $aWins, true)) {
        $completed[$d] = [11000 + $d * 50, 9000 + $d * 40];
    } else {
        $completed[$d] = [8500 + $d * 30, 10500 + $d * 45];
    }
}
$compForce = array_fill(1, 14, 'official');
seed_match(
    $metricId,
    $userIds['sarah'],
    $userIds['mike'],
    $sourceIds['fitbit'],
    $sourceIds['fitbit'],
    '2026-06-20',
    'America/New_York',
    100,
    'accepted',
    'completed',
    $completed,
    14,
    $compForce
);

// 5) Drawn 7–7 exhibition: Jordan vs Elena
$draw = [];
for ($d = 1; $d <= 14; $d++) {
    if ($d % 2 === 1) {
        $draw[$d] = [12000, 10000]; // A
    } else {
        $draw[$d] = [9500, 11500]; // B
    }
}
$drawForce = array_fill(1, 14, 'official');
$drawId = seed_match(
    $metricId,
    $userIds['jordan'],
    $userIds['elena'],
    $sourceIds['garmin'],
    $sourceIds['garmin'],
    '2026-06-01',
    'America/Chicago',
    100,
    'accepted',
    'completed',
    $draw,
    14,
    $drawForce
);
Database::run(
    "UPDATE rly_matches SET status = 'completed', completed_at = ?, updated_at = ? WHERE id = ?",
    ['2026-06-17 13:00:00', Clock::nowUtcString(), $drawId]
);

// 6) Source mismatch active: Iain (watch) vs Sarah (phone)
$mmValues = [
    1 => [14000, 12500],
    2 => [9000, 11000],
    3 => [10500, 10200],
    4 => [8000, 8700],
    5 => [11500, 9800],
    6 => [10000, 10050], // very close but A loses? diff 50 tie
    7 => [12200, 10100],
    8 => [9500, 9900],
];
$mmForce = [];
for ($d = 1; $d <= 7; $d++) {
    $mmForce[$d] = 'official';
}
$mmForce[8] = 'pending';
seed_match(
    $metricId,
    $userIds['iain'],
    $userIds['sarah'],
    $sourceIds['apple_watch'],
    $sourceIds['iphone'],
    '2026-07-14',
    $tzLA,
    100,
    'accepted',
    'active',
    $mmValues + [9 => [5000, 4800]],
    14,
    $mmForce + [9 => 'live']
);

// 7) Match with void day + settling state-ish: Marcus vs Mike, nearly done
$voidValues = [];
for ($d = 1; $d <= 12; $d++) {
    if ($d === 5) {
        $voidValues[$d] = [9000, null]; // one missing → void at settle
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
// Start so day 14 competition date already passed relative to clock: start 2026-07-07 → day 14 = 2026-07-20
seed_match(
    $metricId,
    $userIds['marcus'],
    $userIds['mike'],
    $sourceIds['android_phone'],
    $sourceIds['android_phone'],
    '2026-07-07',
    'America/Denver',
    100,
    'accepted',
    'settling',
    $voidValues,
    14,
    $voidForce
);

// 8) Pending invitation: Jordan challenged by Mike (not yet accepted)
seed_match(
    $metricId,
    $userIds['mike'],
    $userIds['jordan'],
    $sourceIds['apple_watch'],
    null,
    '2026-07-25',
    $tzLA,
    100,
    'pending',
    'invited',
    [],
    14,
    []
);

// Re-affirm showcase statuses after all refreshes
SettlementService::refreshMatch($showcaseId);
for ($d = 1; $d <= 8; $d++) {
    Database::run(
        "UPDATE rly_match_days SET status = 'official', official_at = COALESCE(official_at, ?), updated_at = ? WHERE match_id = ? AND day_number = ?",
        [Clock::nowUtcString(), Clock::nowUtcString(), $showcaseId, $d]
    );
}
Database::run(
    "UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9",
    [$showcaseId]
);
Database::run(
    "UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?",
    [$showcaseId]
);

echo "\nRally seed complete.\n";
echo "Demo password for all seeded players: {$demoPassword}\n";
echo "Accounts:\n";
foreach ($players as [$name, $username, $email]) {
    echo "  {$name} <{$email}> (@{$username})\n";
}
echo "Simulated clock override: 2026-07-21T19:00:00Z (noon PDT)\n";
echo "Showcase match id: {$showcaseId} (Iain vs Mike)\n";
echo "Run: php -S localhost:8091 -t public public/index.php\n";
