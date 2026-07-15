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

echo "Seeding demo matches (every player vs every other player × every metric)...\n";

$playerOrder = ['iain', 'mike', 'sarah', 'jordan', 'elena', 'marcus'];
$playerTz = [
    'iain' => 'America/Los_Angeles',
    'mike' => 'America/Los_Angeles',
    'sarah' => 'America/New_York',
    'jordan' => 'America/Chicago',
    'elena' => 'Europe/London',
    'marcus' => 'America/Denver',
];
$playerSource = [
    'iain' => 'apple_watch',
    'mike' => 'apple_watch',
    'sarah' => 'fitbit',
    'jordan' => 'garmin',
    'elena' => 'apple_watch',
    'marcus' => 'android_phone',
];

/** Stable deterministic int from string key. */
function seed_hash(string $key): int
{
    return (int) sprintf('%u', crc32($key));
}

/**
 * Generate believable daily values for a metric series.
 *
 * @return array<int, array{0:int,1:int}>
 */
function seed_day_values(string $metricSlug, int $lengthDays, string $pairKey, string $pattern): array
{
    $h = seed_hash($metricSlug . '|' . $pairKey . '|' . $pattern);
    $out = [];

    for ($d = 1; $d <= $lengthDays; $d++) {
        $wave = (($h + $d * 17) % 100) / 100.0;
        $swing = (($h + $d * 31) % 7) - 3;

        switch ($metricSlug) {
            case 'steps':
                $baseA = 9000 + (($h + $d * 97) % 5000);
                $baseB = 8800 + (($h + $d * 53) % 4800);
                if ($pattern === 'showcase') {
                    $showcase = [
                        1 => [12450, 9800], 2 => [10200, 9100], 3 => [8700, 11200], 4 => [11800, 10500],
                        5 => [9400, 10100], 6 => [15200, 11000], 7 => [9900, 12100], 8 => [11248, 9731],
                        9 => [8421, 7984], 10 => [10100, 9800], 11 => [9200, 11000], 12 => [12500, 10000],
                        13 => [8800, 9500], 14 => [11300, 10200],
                    ];
                    $out[$d] = $showcase[$d] ?? [$baseA, $baseB];
                    break;
                }
                if ($pattern === 'a_dominant' && $d <= (int) ceil($lengthDays * 0.65)) {
                    $baseA = max($baseA, $baseB + 400 + $d * 20);
                } elseif ($pattern === 'b_dominant' && $d <= (int) ceil($lengthDays * 0.65)) {
                    $baseB = max($baseB, $baseA + 400 + $d * 20);
                } elseif ($pattern === 'close' && $d === $lengthDays) {
                    $baseA = 10050;
                    $baseB = 10020;
                }
                $out[$d] = [$baseA, $baseB];
                break;

            case 'active_minutes':
                $a = 25 + (($h + $d * 11) % 140);
                $b = 20 + (($h + $d * 19) % 145);
                if ($pattern === 'a_dominant') {
                    $a = max($a, $b + 8);
                } elseif ($pattern === 'b_dominant') {
                    $b = max($b, $a + 8);
                }
                $out[$d] = [$a, $b];
                break;

            case 'resting_heart_rate':
                // Realistic RHR ~48–72
                $a = 52 + (($h + $d * 3) % 14) + (int) round($swing * 0.4);
                $b = 54 + (($h + $d * 5) % 13) + (int) round((3 - $swing) * 0.4);
                if ($pattern === 'draw') {
                    $a = 60 + ($d % 2);
                    $b = 61 - ($d % 2);
                    if ($d >= 5) {
                        $a = $b = 60;
                    }
                } elseif ($pattern === 'a_dominant') {
                    $a = min($a, $b - 2); // lower wins
                } elseif ($pattern === 'b_dominant') {
                    $b = min($b, $a - 2);
                }
                $out[$d] = [max(45, min(75, $a)), max(45, min(75, $b))];
                break;

            case 'hrv':
                // Realistic HRV ~35–85 ms
                $a = 45 + (($h + $d * 7) % 28) + $swing;
                $b = 42 + (($h + $d * 13) % 30) + (int) round($wave * 4);
                if ($pattern === 'a_dominant') {
                    $a = max($a, $b + 4);
                } elseif ($pattern === 'b_dominant') {
                    $b = max($b, $a + 4);
                }
                $out[$d] = [max(30, min(90, $a)), max(30, min(90, $b))];
                break;

            case 'sleep_duration':
                // 5h30–8h30 in minutes
                $a = 360 + (($h + $d * 23) % 140);
                $b = 350 + (($h + $d * 29) % 150);
                if ($pattern === 'a_dominant') {
                    $a = max($a, $b + 25);
                } elseif ($pattern === 'b_dominant') {
                    $b = max($b, $a + 25);
                } elseif ($pattern === 'close' && $d === 5) {
                    $a = 420;
                    $b = 418;
                }
                $out[$d] = [max(300, min(540, $a)), max(300, min(540, $b))];
                break;

            default:
                $out[$d] = [100, 90];
        }
    }

    return $out;
}

/**
 * @return array{start:string,status:string,force:array<int,string>,values:?array<int,array{0:?int,1:?int}>,invitation:string}
 */
function seed_lifecycle(
    string $metricSlug,
    int $lengthDays,
    int $pairIndex,
    int $metricIndex,
    string $pairKey
): array {
    $mode = ($pairIndex + $metricIndex * 2) % 5;
    // 0 active, 1 completed, 2 completed close/draw-ish, 3 scheduled, 4 settling/void-flavored active

    if ($pairKey === 'iain|mike' && $metricSlug === 'steps') {
        $force = array_replace(array_fill(1, 8, 'official'), [9 => 'live']);
        return [
            'start' => '2026-07-13',
            'status' => 'active',
            'force' => $force,
            'values' => seed_day_values('steps', 14, $pairKey, 'showcase'),
            'invitation' => 'accepted',
            'pattern' => 'showcase',
        ];
    }

    if ($lengthDays === 14) {
        return match ($mode) {
            0 => [
                'start' => '2026-07-13',
                'status' => 'active',
                'force' => array_replace(array_fill(1, 8, 'official'), [9 => 'live']),
                'values' => null,
                'invitation' => 'accepted',
                'pattern' => ($pairIndex % 2 === 0) ? 'a_dominant' : 'b_dominant',
            ],
            1 => [
                'start' => '2026-06-' . str_pad((string) (1 + ($pairIndex % 20)), 2, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'force' => array_fill(1, 14, 'official'),
                'values' => null,
                'invitation' => 'accepted',
                'pattern' => 'a_dominant',
            ],
            2 => [
                'start' => '2026-06-' . str_pad((string) (5 + ($metricIndex * 3) % 20), 2, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'force' => array_fill(1, 14, 'official'),
                'values' => null,
                'invitation' => 'accepted',
                'pattern' => 'close',
            ],
            3 => [
                'start' => '2026-07-22',
                'status' => 'scheduled',
                'force' => [],
                'values' => [],
                'invitation' => 'accepted',
                'pattern' => 'neutral',
            ],
            default => [
                'start' => '2026-07-07',
                'status' => 'settling',
                'force' => array_replace(array_fill(1, 12, 'official'), [5 => 'void', 13 => 'pending', 14 => 'pending']),
                'values' => null,
                'invitation' => 'accepted',
                'pattern' => ($pairIndex % 2 === 0) ? 'a_dominant' : 'b_dominant',
            ],
        };
    }

    // 7-day series-average metrics
    return match ($mode) {
        0 => [
            'start' => '2026-07-15',
            'status' => 'active',
            'force' => array_replace(array_fill(1, 5, 'official'), [6 => 'pending', 7 => 'live']),
            'values' => null,
            'invitation' => 'accepted',
            'pattern' => ($pairIndex % 2 === 0) ? 'a_dominant' : 'b_dominant',
        ],
        1 => [
            'start' => '2026-06-' . str_pad((string) (1 + ($pairIndex % 18)), 2, '0', STR_PAD_LEFT),
            'status' => 'completed',
            'force' => array_fill(1, 7, 'official'),
            'values' => null,
            'invitation' => 'accepted',
            'pattern' => 'a_dominant',
        ],
        2 => [
            'start' => '2026-06-' . str_pad((string) (8 + ($metricIndex * 2) % 15), 2, '0', STR_PAD_LEFT),
            'status' => 'completed',
            'force' => array_fill(1, 7, 'official'),
            'values' => null,
            'invitation' => 'accepted',
            'pattern' => $metricSlug === 'resting_heart_rate' ? 'draw' : 'close',
        ],
        3 => [
            'start' => '2026-07-22',
            'status' => 'scheduled',
            'force' => [],
            'values' => [],
            'invitation' => 'accepted',
            'pattern' => 'neutral',
        ],
        default => [
            'start' => '2026-07-15',
            'status' => 'active',
            'force' => array_replace(array_fill(1, 4, 'official'), [5 => 'void', 6 => 'pending', 7 => 'live']),
            'values' => null,
            'invitation' => 'accepted',
            'pattern' => ($pairIndex % 2 === 0) ? 'a_dominant' : 'b_dominant',
        ],
    };
}

$pairs = [];
for ($i = 0; $i < count($playerOrder); $i++) {
    for ($j = $i + 1; $j < count($playerOrder); $j++) {
        $pairs[] = [$playerOrder[$i], $playerOrder[$j]];
    }
}

$metricList = array_values(array_keys($metricIds));
$matchCount = 0;
$showcaseId = null;
$byMetric = array_fill_keys($metricList, 0);

foreach ($metricList as $metricIndex => $metricSlug) {
    $metricId = $metricIds[$metricSlug];
    $meta = Database::fetch('SELECT * FROM rly_metric_types WHERE id = ?', [$metricId]);
    $lengthDays = (int) ($meta['default_length_days'] ?? 14);
    $tieThreshold = (int) ($meta['default_tie_threshold'] ?? 100);

    foreach ($pairs as $pairIndex => [$userA, $userB]) {
        $pairKey = $userA . '|' . $userB;
        $life = seed_lifecycle($metricSlug, $lengthDays, $pairIndex, $metricIndex, $pairKey);
        $pattern = (string) $life['pattern'];
        $values = $life['values'];
        if ($values === null) {
            $values = seed_day_values($metricSlug, $lengthDays, $pairKey, $pattern);
            // Settling/void sample: omit B on void day
            if (($life['force'][5] ?? null) === 'void' && isset($values[5])) {
                $values[5] = [$values[5][0], null];
            }
        }

        // Occasional source mismatch for variety (watch vs phone / different wearables)
        $srcA = $playerSource[$userA];
        $srcB = $playerSource[$userB];
        if (($pairIndex + $metricIndex) % 7 === 3) {
            $srcB = $srcB === 'apple_watch' ? 'iphone' : ($srcB === 'fitbit' ? 'garmin' : 'iphone');
        }

        $matchId = seed_match(
            $metricId,
            $userIds[$userA],
            $userIds[$userB],
            $sourceIds[$srcA],
            $sourceIds[$srcB],
            $life['start'],
            $playerTz[$userA],
            $tieThreshold,
            $life['invitation'],
            $life['status'],
            $values,
            $lengthDays,
            $life['force'],
            $metricSlug
        );

        if ($life['status'] === 'completed') {
            Database::run(
                "UPDATE rly_matches SET status = 'completed', completed_at = ?, updated_at = ? WHERE id = ?",
                ['2026-06-20 18:00:00', Clock::nowUtcString(), $matchId]
            );
        } elseif ($life['status'] === 'active') {
            Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$matchId]);
        } elseif ($life['status'] === 'settling') {
            Database::run("UPDATE rly_matches SET status = 'settling', completed_at = NULL WHERE id = ?", [$matchId]);
        } elseif ($life['status'] === 'scheduled') {
            Database::run("UPDATE rly_matches SET status = 'scheduled', completed_at = NULL WHERE id = ?", [$matchId]);
        }

        if ($pairKey === 'iain|mike' && $metricSlug === 'steps') {
            $showcaseId = $matchId;
        }

        $matchCount++;
        $byMetric[$metricSlug]++;
        echo "  #{$matchCount} {$metricSlug}: {$userA} vs {$userB} ({$life['status']})\n";
    }
}

// Preserve one pending invitation outside the complete matrix (Mike → Jordan steps)
seed_match(
    $metricIds['steps'],
    $userIds['mike'],
    $userIds['jordan'],
    $sourceIds['apple_watch'],
    null,
    '2026-07-25',
    'America/Los_Angeles',
    100,
    'pending',
    'invited',
    [],
    14,
    [],
    'steps'
);
$matchCount++;
echo "  #{$matchCount} steps: mike vs jordan (invited)\n";

if ($showcaseId !== null) {
    SettlementService::refreshMatch($showcaseId);
    for ($d = 1; $d <= 8; $d++) {
        Database::run(
            "UPDATE rly_match_days SET status = 'official', official_at = COALESCE(official_at, ?), updated_at = ? WHERE match_id = ? AND day_number = ?",
            [Clock::nowUtcString(), Clock::nowUtcString(), $showcaseId, $d]
        );
    }
    Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9", [$showcaseId]);
    Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$showcaseId]);
}

$pairCount = count($pairs);
$metricCount = count($metricList);
$expectedPairs = $pairCount * $metricCount;

echo "\nRally full-pairing seed complete.\n";
echo "Demo password for all seeded players: {$demoPassword}\n";
echo "Accounts:\n";
foreach ($players as [$name, $username, $email]) {
    echo "  {$name} <{$email}> (@{$username})\n";
}
echo "Players: {$pairCount} unique pairs × {$metricCount} metrics = {$expectedPairs} accepted matches";
echo " (+ 1 invited) → {$matchCount} total\n";
foreach ($byMetric as $slug => $n) {
    echo "  {$slug}: {$n} pairings\n";
}
echo "Simulated clock override: 2026-07-21T19:00:00Z (noon PDT)\n";
if ($showcaseId !== null) {
    echo "Showcase steps match id: {$showcaseId} (Iain vs Mike)\n";
}
echo "Run: php -S localhost:8091 -t public public/index.php\n";
