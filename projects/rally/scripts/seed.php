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
use Rally\Services\BaselineService;
use Rally\Services\Clock;
use Rally\Services\MatchService;
use Rally\Services\SettlementService;
use Rally\Services\UserMetricDayService;

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

require __DIR__ . '/migrate_columns.php';

function exec_schema(PDO $pdo, string $path): void
{
    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Cannot read schema: {$path}");
    }
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

/** Stable deterministic int from string key. */
function seed_hash(string $key): int
{
    return (int) sprintf('%u', crc32($key));
}

/** @return list<string> */
function date_range_inclusive(string $start, string $end): array
{
    $dates = [];
    $cursor = new DateTimeImmutable($start);
    $last = new DateTimeImmutable($end);
    while ($cursor <= $last) {
        $dates[] = $cursor->format('Y-m-d');
        $cursor = $cursor->modify('+1 day');
    }
    return $dates;
}

/**
 * @param array<string, array<string, int>> $userMetricMeans
 */
function canonical_daily_value(string $username, string $metricSlug, string $date, array $userMetricMeans): int
{
    $mean = $userMetricMeans[$username][$metricSlug] ?? 100;
    $h = seed_hash('canonical|' . $username . '|' . $metricSlug . '|' . $date);

    return match ($metricSlug) {
        'steps' => max(500, min(25000, $mean + ($h % 2400) - 1200 + (($h >> 4) % 600) - 300)),
        'active_minutes' => max(5, min(300, $mean + ($h % 40) - 20)),
        'resting_heart_rate' => max(48, min(72, $mean + ($h % 9) - 4)),
        'hrv' => max(35, min(85, $mean + ($h % 25) - 12)),
        'sleep_duration' => max(300, min(540, $mean + ($h % 80) - 40)),
        default => max(0, $mean + ($h % 50) - 25),
    };
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
    string $sourceASlug,
    ?string $sourceBSlug,
    string $startDate,
    string $timezone,
    int $tieThreshold,
    string $invitationStatus,
    string $initialStatus,
    array $dayValues,
    int $lengthDays = 14,
    array $forceStatus = [],
    string $metricSlug = 'steps',
    string $competitionType = 'classic',
    ?float $baselineTieThreshold = null,
    bool $freezeBaselines = false
): int {
    $now = Clock::nowUtcString();
    $competitionType = $competitionType === 'baseline' ? 'baseline' : 'classic';
    if ($competitionType === 'baseline' && $baselineTieThreshold === null) {
        $baselineTieThreshold = BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD;
    }

    Database::run(
        'INSERT INTO rly_matches
         (metric_type_id, player_a_user_id, player_b_user_id, player_a_source_id, player_b_source_id,
          created_by_user_id, start_date, length_days, timezone, tie_threshold, competition_type,
          baseline_tie_threshold, status, invitation_status, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            $metricId, $playerA, $playerB, $sourceA, $sourceB, $playerA,
            $startDate, $lengthDays, $timezone, $tieThreshold, $competitionType,
            $baselineTieThreshold, $initialStatus, $invitationStatus, $now, $now,
        ]
    );
    $matchId = Database::lastInsertId();
    MatchService::createMatchDays($matchId, $startDate, $lengthDays, $timezone, $now);

    if (
        $freezeBaselines
        && $competitionType === 'baseline'
        && $invitationStatus === 'accepted'
        && $sourceB !== null
    ) {
        BaselineService::freezeForMatch($matchId, true);
    }

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
        $compDate = (string) $day['competition_date'];
        if ($valA !== null) {
            UserMetricDayService::ingest([
                'user_id' => $playerA,
                'metric_type' => $metricSlug,
                'observation_date' => $compDate,
                'value' => $valA,
                'data_source' => $sourceASlug,
                'source_record_key' => "seed-match-{$matchId}-{$dayNum}-a",
                'is_manual' => false,
                'ingested_at' => $compDate . 'T20:00:00Z',
                'project' => true,
            ], true);
        }
        if ($valB !== null && $sourceBSlug !== null) {
            UserMetricDayService::ingest([
                'user_id' => $playerB,
                'metric_type' => $metricSlug,
                'observation_date' => $compDate,
                'value' => $valB,
                'data_source' => $sourceBSlug,
                'source_record_key' => "seed-match-{$matchId}-{$dayNum}-b",
                'is_manual' => false,
                'ingested_at' => $compDate . 'T20:30:00Z',
                'project' => true,
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
                $a = 52 + (($h + $d * 3) % 14) + (int) round($swing * 0.4);
                $b = 54 + (($h + $d * 5) % 13) + (int) round((3 - $swing) * 0.4);
                if ($pattern === 'draw') {
                    $a = 60 + ($d % 2);
                    $b = 61 - ($d % 2);
                    if ($d >= 5) {
                        $a = $b = 60;
                    }
                } elseif ($pattern === 'a_dominant') {
                    $a = min($a, $b - 2);
                } elseif ($pattern === 'b_dominant') {
                    $b = min($b, $a - 2);
                }
                $out[$d] = [max(45, min(75, $a)), max(45, min(75, $b))];
                break;

            case 'hrv':
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
 * Baseline Steps showcase: Iain lower raw on several days but higher % vs ~8400 baseline.
 *
 * @return array<int, array{0:int,1:int}>
 */
function baseline_steps_showcase_values(): array
{
    return [
        1 => [12000, 16000],
        2 => [11800, 15800],
        3 => [12200, 16200],
        4 => [10500, 14500],
        5 => [13000, 16800],
        6 => [9500, 13200],
        7 => [11200, 15000],
        8 => [10800, 14800],
        9 => [10100, 13800],
        10 => [9900, 13500],
        11 => [11500, 15200],
        12 => [12800, 16500],
        13 => [10400, 14200],
        14 => [11900, 15700],
    ];
}

/**
 * Baseline Active Minutes: Iain vs Sarah with distinct improvement arcs.
 *
 * @return array<int, array{0:int,1:int}>
 */
function baseline_active_minutes_values(): array
{
    return [
        1 => [72, 58],
        2 => [68, 55],
        3 => [75, 52],
        4 => [70, 60],
        5 => [78, 54],
        6 => [65, 57],
        7 => [74, 53],
        8 => [71, 59],
        9 => [69, 56],
        10 => [76, 51],
        11 => [73, 58],
        12 => [80, 55],
        13 => [67, 54],
        14 => [77, 57],
    ];
}

/**
 * Short completed Baseline match with a percentage-point tie on day 1.
 *
 * @return array<int, array{0:int,1:int}>
 */
function baseline_percentage_tie_values(): array
{
    return [
        1 => [7500, 10480],
        2 => [7200, 10100],
        3 => [7800, 10650],
        4 => [7100, 10020],
        5 => [7600, 10510],
        6 => [7300, 10280],
        7 => [7450, 10420],
    ];
}

/**
 * @return array{start:string,status:string,force:array<int,string>,values:?array<int,array{0:?int,1:?int}>,invitation:string,pattern:string}
 */
function seed_lifecycle(
    string $metricSlug,
    int $lengthDays,
    int $pairIndex,
    int $metricIndex,
    string $pairKey
): array {
    $mode = ($pairIndex + $metricIndex * 2) % 5;

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

/**
 * @param list<int> $acceptedBaselineMatchIds
 */
function verify_baseline_matches(array $acceptedBaselineMatchIds, ?int $exemptMatchId = null): void
{
    foreach ($acceptedBaselineMatchIds as $matchId) {
        if ($exemptMatchId !== null && $matchId === $exemptMatchId) {
            continue;
        }

        $match = Database::fetch(
            'SELECT m.*, mt.scoring_strategy, mt.slug AS metric_slug
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE m.id = ?',
            [$matchId]
        );
        if ($match === null) {
            throw new RuntimeException("Verification failed: match #{$matchId} not found.");
        }
        if ((string) $match['competition_type'] !== 'baseline') {
            throw new RuntimeException("Verification failed: match #{$matchId} competition_type is not baseline.");
        }
        if ((string) $match['scoring_strategy'] !== 'daily_wins') {
            throw new RuntimeException("Verification failed: match #{$matchId} scoring_strategy is not daily_wins.");
        }
        if ((string) $match['invitation_status'] !== 'accepted') {
            continue;
        }

        $startDate = (string) $match['start_date'];
        foreach ([(int) $match['player_a_user_id'], (int) $match['player_b_user_id']] as $userId) {
            $baseline = Database::fetch(
                'SELECT * FROM rly_match_baselines WHERE match_id = ? AND user_id = ?',
                [$matchId, $userId]
            );
            if ($baseline === null) {
                throw new RuntimeException("Verification failed: match #{$matchId} missing frozen baseline for user #{$userId}.");
            }
            $sampleCount = (int) $baseline['sample_count'];
            if ($sampleCount < 30) {
                throw new RuntimeException(
                    "Verification failed: match #{$matchId} user #{$userId} sample_count={$sampleCount} (need >= 30)."
                );
            }
            $windowEnd = (string) $baseline['window_end_date'];
            if ($windowEnd >= $startDate) {
                throw new RuntimeException(
                    "Verification failed: match #{$matchId} user #{$userId} window_end_date {$windowEnd} must be < start_date {$startDate}."
                );
            }
        }
    }
}

if ($options['status']) {
    $tables = list_tables($pdo, $driver);
    $required = [
        'rly_users',
        'rly_metric_types',
        'rly_data_sources',
        'rly_matches',
        'rly_match_days',
        'rly_match_day_results',
        'rly_user_metric_days',
        'rly_match_baselines',
    ];
    echo "Driver: {$driver}\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    foreach ($required as $t) {
        $ok = in_array($t, $tables, true) ? 'OK' : 'MISSING';
        echo "  {$t}: {$ok}\n";
    }
    if (in_array('rly_matches', $tables, true)) {
        $hasCompetitionType = rally_column_exists($pdo, $driver, 'rly_matches', 'competition_type');
        echo '  rly_matches.competition_type: ' . ($hasCompetitionType ? 'OK' : 'MISSING') . "\n";
    }
    if (in_array('rly_users', $tables, true)) {
        echo 'Users: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_users') . "\n";
        echo 'Matches: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_matches') . "\n";
        echo 'Match days: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_days') . "\n";
        echo 'Results: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_day_results') . "\n";
        if (in_array('rly_user_metric_days', $tables, true)) {
            echo 'Canonical observations: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_user_metric_days') . "\n";
        }
        if (in_array('rly_match_baselines', $tables, true)) {
            echo 'Frozen baselines: ' . Database::fetchValue('SELECT COUNT(*) FROM rly_match_baselines') . "\n";
        }
        if (rally_column_exists($pdo, $driver, 'rly_matches', 'competition_type')) {
            echo 'Baseline matches: ' . Database::fetchValue("SELECT COUNT(*) FROM rly_matches WHERE competition_type = 'baseline'") . "\n";
        }
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

migrate_rly_metric_types_columns($pdo, $driver);
migrate_rly_competition_and_history($pdo, $driver);

$demoPassword = Config::string('demo.password', 'rally-demo-2026');
$passwordHash = password_hash($demoPassword, PASSWORD_DEFAULT);
$now = '2026-07-21 12:00:00';

Clock::setOverride(new DateTimeImmutable('2026-07-21T19:00:00Z'));

echo "Seeding reference data...\n";

$pdo->exec('DELETE FROM rly_match_baselines');
$pdo->exec('DELETE FROM rly_match_day_results');
$pdo->exec('DELETE FROM rly_match_days');
$pdo->exec('DELETE FROM rly_matches');
$pdo->exec('DELETE FROM rly_user_metric_days');
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

/** @var array<string, array<string, int>> */
$userMetricMeans = [
    'iain' => ['steps' => 8400, 'active_minutes' => 52, 'resting_heart_rate' => 58, 'hrv' => 62, 'sleep_duration' => 430],
    'mike' => ['steps' => 11500, 'active_minutes' => 68, 'resting_heart_rate' => 54, 'hrv' => 48, 'sleep_duration' => 405],
    'sarah' => ['steps' => 7200, 'active_minutes' => 44, 'resting_heart_rate' => 61, 'hrv' => 55, 'sleep_duration' => 445],
    'jordan' => ['steps' => 9800, 'active_minutes' => 58, 'resting_heart_rate' => 56, 'hrv' => 50, 'sleep_duration' => 415],
    'elena' => ['steps' => 9500, 'active_minutes' => 49, 'resting_heart_rate' => 63, 'hrv' => 58, 'sleep_duration' => 435],
    'marcus' => ['steps' => 6800, 'active_minutes' => 38, 'resting_heart_rate' => 66, 'hrv' => 42, 'sleep_duration' => 455],
];

$historyStart = '2026-05-20';
$historyEnd = '2026-07-19';
$historyDates = date_range_inclusive($historyStart, $historyEnd);
$metricSlugs = array_keys($metricIds);

echo "Seeding canonical health history ({$historyStart} → {$historyEnd})...\n";

$historyIngestCount = 0;
foreach ($playerOrder as $username) {
    $userId = $userIds[$username];
    $sourcesForUser = [$playerSource[$username]];
    if (in_array($username, ['iain', 'mike'], true)) {
        $sourcesForUser[] = 'iphone';
    }

    foreach ($metricSlugs as $metricSlug) {
        foreach ($sourcesForUser as $sourceSlug) {
            foreach ($historyDates as $date) {
                $value = canonical_daily_value($username, $metricSlug, $date, $userMetricMeans);
                UserMetricDayService::ingest([
                    'user_id' => $userId,
                    'metric_type' => $metricSlug,
                    'observation_date' => $date,
                    'value' => $value,
                    'data_source' => $sourceSlug,
                    'source_record_key' => "seed-history-{$username}-{$metricSlug}-{$sourceSlug}-{$date}",
                    'is_manual' => false,
                    'ingested_at' => $date . 'T08:00:00Z',
                    'project' => false,
                ], true);
                $historyIngestCount++;
            }
        }
    }
}

echo "  Ingested {$historyIngestCount} canonical history rows (project=false).\n";

echo "Seeding Jordan short simulated_watch history (5 days, insufficient baseline)...\n";
$jordanShortDates = date_range_inclusive('2026-07-10', '2026-07-14');
foreach ($metricSlugs as $metricSlug) {
    foreach ($jordanShortDates as $date) {
        $value = canonical_daily_value('jordan', $metricSlug, $date, $userMetricMeans);
        UserMetricDayService::ingest([
            'user_id' => $userIds['jordan'],
            'metric_type' => $metricSlug,
            'observation_date' => $date,
            'value' => $value,
            'data_source' => 'simulated_watch',
            'source_record_key' => "seed-jordan-sim-{$metricSlug}-{$date}",
            'is_manual' => false,
            'ingested_at' => $date . 'T09:00:00Z',
            'project' => false,
        ], true);
    }
}

echo "Seeding demo matches (every player vs every other player × every metric, classic)...\n";

$pairs = [];
for ($i = 0; $i < count($playerOrder); $i++) {
    for ($j = $i + 1; $j < count($playerOrder); $j++) {
        $pairs[] = [$playerOrder[$i], $playerOrder[$j]];
    }
}

$metricList = array_values(array_keys($metricIds));
$matchCount = 0;
$classicShowcaseId = null;
$byMetric = array_fill_keys($metricList, 0);
/** @var list<int> */
$acceptedBaselineMatchIds = [];

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
            if (($life['force'][5] ?? null) === 'void' && isset($values[5])) {
                $values[5] = [$values[5][0], null];
            }
        }

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
            $srcA,
            $srcB,
            $life['start'],
            $playerTz[$userA],
            $tieThreshold,
            $life['invitation'],
            $life['status'],
            $values,
            $lengthDays,
            $life['force'],
            $metricSlug,
            'classic',
            null,
            false
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
            $classicShowcaseId = $matchId;
        }

        $matchCount++;
        $byMetric[$metricSlug]++;
        echo "  #{$matchCount} classic {$metricSlug}: {$userA} vs {$userB} ({$life['status']})\n";
    }
}

echo "Seeding additional demonstration matches...\n";

$baselineForce = array_replace(array_fill(1, 8, 'official'), [9 => 'live']);
$baselineShowcaseId = seed_match(
    $metricIds['steps'],
    $userIds['iain'],
    $userIds['mike'],
    $sourceIds['apple_watch'],
    $sourceIds['apple_watch'],
    'apple_watch',
    'apple_watch',
    '2026-07-13',
    'America/Los_Angeles',
    100,
    'accepted',
    'active',
    baseline_steps_showcase_values(),
    14,
    $baselineForce,
    'steps',
    'baseline',
    BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
    true
);
$acceptedBaselineMatchIds[] = $baselineShowcaseId;
$matchCount++;
echo "  #{$matchCount} baseline steps: iain vs mike (active, % wins despite lower raw)\n";

$baselineActiveId = seed_match(
    $metricIds['active_minutes'],
    $userIds['iain'],
    $userIds['sarah'],
    $sourceIds['apple_watch'],
    $sourceIds['fitbit'],
    'apple_watch',
    'fitbit',
    '2026-07-13',
    'America/Los_Angeles',
    2,
    'accepted',
    'active',
    baseline_active_minutes_values(),
    14,
    array_replace(array_fill(1, 7, 'official'), [8 => 'live']),
    'active_minutes',
    'baseline',
    BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
    true
);
$acceptedBaselineMatchIds[] = $baselineActiveId;
$matchCount++;
echo "  #{$matchCount} baseline active_minutes: iain vs sarah (active)\n";

$baselineTieId = seed_match(
    $metricIds['steps'],
    $userIds['marcus'],
    $userIds['elena'],
    $sourceIds['android_phone'],
    $sourceIds['apple_watch'],
    'android_phone',
    'apple_watch',
    '2026-06-22',
    'America/Denver',
    100,
    'accepted',
    'completed',
    baseline_percentage_tie_values(),
    7,
    array_fill(1, 7, 'official'),
    'steps',
    'baseline',
    BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
    true
);
Database::run(
    "UPDATE rly_matches SET status = 'completed', completed_at = ?, updated_at = ? WHERE id = ?",
    ['2026-06-29 18:00:00', Clock::nowUtcString(), $baselineTieId]
);
$acceptedBaselineMatchIds[] = $baselineTieId;
$matchCount++;
echo "  #{$matchCount} baseline steps tie: marcus vs elena (completed, day-1 % within 1.00 pp)\n";

$invalidBaselineInvitationId = seed_match(
    $metricIds['steps'],
    $userIds['iain'],
    $userIds['jordan'],
    $sourceIds['apple_watch'],
    null,
    'apple_watch',
    null,
    '2026-07-25',
    'America/Los_Angeles',
    100,
    'pending',
    'invited',
    [],
    14,
    [],
    'steps',
    'baseline',
    BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
    false
);
$matchCount++;
echo "  #{$matchCount} baseline steps invited: iain vs jordan (pending — intentionally invalid: Jordan has no apple_watch history)\n";

$sarahElenaPendingId = seed_match(
    $metricIds['steps'],
    $userIds['sarah'],
    $userIds['elena'],
    $sourceIds['fitbit'],
    $sourceIds['apple_watch'],
    'fitbit',
    'apple_watch',
    '2026-07-28',
    'America/New_York',
    100,
    'pending',
    'invited',
    [],
    14,
    [],
    'steps',
    'baseline',
    BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
    false
);
$matchCount++;
echo "  #{$matchCount} baseline steps invited: sarah vs elena (pending — distinct baselines ~7200 vs ~9500)\n";

$mikeJordanInvitedId = seed_match(
    $metricIds['steps'],
    $userIds['mike'],
    $userIds['jordan'],
    $sourceIds['apple_watch'],
    null,
    'apple_watch',
    null,
    '2026-07-25',
    'America/Los_Angeles',
    100,
    'pending',
    'invited',
    [],
    14,
    [],
    'steps',
    'classic',
    null,
    false
);
$matchCount++;
echo "  #{$matchCount} classic steps invited: mike vs jordan (pending)\n";

if ($classicShowcaseId !== null) {
    SettlementService::refreshMatch($classicShowcaseId);
    for ($d = 1; $d <= 8; $d++) {
        Database::run(
            "UPDATE rly_match_days SET status = 'official', official_at = COALESCE(official_at, ?), updated_at = ? WHERE match_id = ? AND day_number = ?",
            [Clock::nowUtcString(), Clock::nowUtcString(), $classicShowcaseId, $d]
        );
    }
    Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9", [$classicShowcaseId]);
    Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$classicShowcaseId]);
}

if ($baselineShowcaseId !== null) {
    SettlementService::refreshMatch($baselineShowcaseId);
    for ($d = 1; $d <= 8; $d++) {
        Database::run(
            "UPDATE rly_match_days SET status = 'official', official_at = COALESCE(official_at, ?), updated_at = ? WHERE match_id = ? AND day_number = ?",
            [Clock::nowUtcString(), Clock::nowUtcString(), $baselineShowcaseId, $d]
        );
    }
    Database::run("UPDATE rly_match_days SET status = 'live', official_at = NULL WHERE match_id = ? AND day_number = 9", [$baselineShowcaseId]);
    Database::run("UPDATE rly_matches SET status = 'active', completed_at = NULL WHERE id = ?", [$baselineShowcaseId]);
}

echo "Verifying accepted Baseline matches...\n";
try {
    verify_baseline_matches($acceptedBaselineMatchIds, $invalidBaselineInvitationId);
    echo "  Baseline verification: PASS\n";
} catch (RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}

$totalObservations = (int) Database::fetchValue('SELECT COUNT(*) FROM rly_user_metric_days');
$observationSummary = Database::fetchAll(
    'SELECT u.username, COUNT(*) AS observation_count
     FROM rly_user_metric_days o
     JOIN rly_users u ON u.id = o.user_id
     GROUP BY u.username
     ORDER BY u.username'
);
$baselineMatchCount = (int) Database::fetchValue("SELECT COUNT(*) FROM rly_matches WHERE competition_type = 'baseline'");
$frozenBaselineCount = (int) Database::fetchValue('SELECT COUNT(*) FROM rly_match_baselines');

$pairCount = count($pairs);
$metricCount = count($metricList);
$expectedPairs = $pairCount * $metricCount;
$extraMatches = 5;

echo "\nRally Classic/Baseline seed complete.\n";
echo "Demo password for all seeded players: {$demoPassword}\n";
echo "Accounts:\n";
foreach ($players as [$name, $username, $email]) {
    echo "  {$name} <{$email}> (@{$username})\n";
}
echo "Canonical observations: {$totalObservations} total\n";
foreach ($observationSummary as $row) {
    echo "  {$row['username']}: {$row['observation_count']}\n";
}
echo "Baseline matches: {$baselineMatchCount} (frozen baseline rows: {$frozenBaselineCount})\n";
echo "Accepted Baseline matches verified: " . count($acceptedBaselineMatchIds) . " — PASS\n";
echo "Players: {$pairCount} unique pairs × {$metricCount} metrics = {$expectedPairs} classic matrix matches";
echo " (+ {$extraMatches} demonstration invitations/specials) → {$matchCount} total\n";
foreach ($byMetric as $slug => $n) {
    echo "  classic matrix {$slug}: {$n} pairings\n";
}
echo "Simulated clock override: 2026-07-21T19:00:00Z (noon PDT)\n";
if ($classicShowcaseId !== null) {
    echo "Classic steps showcase match id: {$classicShowcaseId} (Iain vs Mike)\n";
}
if ($baselineShowcaseId !== null) {
    echo "Baseline steps showcase match id: {$baselineShowcaseId} (Iain vs Mike, % improvement)\n";
}
echo "Intentionally invalid Baseline invitation id: {$invalidBaselineInvitationId} (Iain → Jordan, apple_watch — Jordan lacks apple_watch history)\n";
echo "Sarah vs Elena pending Baseline invitation id: {$sarahElenaPendingId}\n";
echo "Note: iPhone observations for Iain/Mike do not project into apple_watch matches (source mismatch by design).\n";
echo "Run: php -S localhost:8091 -t public public/index.php\n";
