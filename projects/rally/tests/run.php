<?php

declare(strict_types=1);

/**
 * Rally deterministic domain tests. CLI only.
 *
 *   php tests/run.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use Rally\Core\Database;
use Rally\Services\ActivityFeedService;
use Rally\Services\Clock;
use Rally\Services\MatchService;
use Rally\Services\MatchScoringService;
use Rally\Services\MetricComparisonService;
use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;
use Rally\Services\PersonalRecordsService;
use Rally\Services\ResultIngestionService;
use Rally\Services\SettlementService;

$passed = 0;
$failed = 0;

function assert_true(bool $cond, string $msg): void
{
    global $passed, $failed;
    if ($cond) {
        echo "  PASS  {$msg}\n";
        $passed++;
    } else {
        echo "  FAIL  {$msg}\n";
        $failed++;
    }
}

function assert_eq(mixed $expected, mixed $actual, string $msg): void
{
    assert_true($expected === $actual, $msg . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
}

echo "Rally tests\n";

// --- Metric comparison (no DB) ---
echo "\nMetricComparisonService\n";
$r = MetricComparisonService::compare(10042, 9978, 100, true);
assert_true($r['is_tie'], '99 difference is a tie when threshold is 100');
assert_eq(MetricComparisonService::OUTCOME_TIE, $r['outcome'], '99 → tie outcome');

$r = MetricComparisonService::compare(10100, 10000, 100, true);
assert_true(!$r['is_tie'], '100 difference is NOT a tie');
assert_eq('a', $r['winner_side'], '100 difference higher wins → A');

$r = MetricComparisonService::compare(10101, 10000, 100, true);
assert_true(!$r['is_tie'], '101 difference is a win');
assert_eq('a', $r['winner_side'], '101 → A wins');

$r = MetricComparisonService::compare(10000, 10100, 100, true);
assert_eq('b', $r['winner_side'], 'higher B wins at margin 100');

$r = MetricComparisonService::compare(100, 200, 50, false);
assert_eq('a', $r['winner_side'], 'lower-wins metric: lower value wins when margin >= threshold');

$r = MetricComparisonService::compare(100, 120, 50, false);
assert_true($r['is_tie'], 'lower-wins: below threshold still ties');

// Use isolated sqlite for integration tests
$testDb = sys_get_temp_dir() . '/rally_test_' . getmypid() . '.sqlite';
@unlink($testDb);

// Reload config by writing a temp approach: override via redefining is hard.
// Instead manipulate Database by setting config through a fresh Process...
// Simpler: use the project's sqlite but in a transaction rollback — SQLite DDL commits.
// We'll create a dedicated test DB by replacing the PDO.

$ref = new ReflectionClass(Database::class);
$prop = $ref->getProperty('pdo');
$prop->setAccessible(true);
$prop->setValue(null, null);

// Patch config by loading a custom config file
$cfgDir = sys_get_temp_dir() . '/rally_cfg_' . getmypid();
@mkdir($cfgDir);
file_put_contents($cfgDir . '/config.php', '<?php return ' . var_export([
    'app' => [
        'env' => 'development',
        'url' => 'http://localhost',
        'base_url' => 'http://localhost',
        'base_path' => '',
        'timezone' => 'UTC',
        'settlement_hour' => 6,
        'settlement_lag_days' => 2,
        'metric_value_min' => 0,
        'metric_value_max' => 100000,
    ],
    'db' => [
        'driver' => 'sqlite',
        'sqlite_path' => $testDb,
        'host' => '', 'port' => 3306, 'name' => '', 'user' => '', 'password' => '', 'charset' => 'utf8mb4',
    ],
    'session' => ['cookie_name' => 'rally_test', 'idle_ttl' => 3600, 'secure' => false],
    'demo' => ['password' => 'test'],
], true) . ';');

\Rally\Core\Config::load($cfgDir);
$prop->setValue(null, null);
$pdo = Database::pdo();

$schema = file_get_contents(__DIR__ . '/../database/schema.sqlite.sql');
$lines = [];
foreach (explode("\n", (string) $schema) as $line) {
    if (!str_starts_with(ltrim($line), '--')) {
        $lines[] = $line;
    }
}
foreach (array_filter(array_map('trim', explode(';', implode("\n", $lines)))) as $stmt) {
    $pdo->exec($stmt);
}

Clock::freezeForTests(new DateTimeImmutable('2026-07-21T19:00:00Z'));

$now = '2026-07-21 12:00:00';
$insertMetric = static function (
    string $slug,
    string $name,
    string $unit,
    ?string $displayUnit,
    string $classification,
    string $strategy,
    int $higherWins,
    int $defaultLength,
    int $defaultThreshold
) use ($now): int {
    Database::run(
        'INSERT INTO rly_metric_types
         (slug, name, unit, display_unit, classification, scoring_strategy, higher_wins,
          default_length_days, default_tie_threshold, description, context_note, is_active, created_at)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,1,?)',
        [
            $slug, $name, $unit, $displayUnit, $classification, $strategy, $higherWins,
            $defaultLength, $defaultThreshold, $name . ' competition', null, $now,
        ]
    );
    return (int) Database::fetchValue('SELECT id FROM rly_metric_types WHERE slug=?', [$slug]);
};

$metricId = $insertMetric('steps', 'Daily Steps', 'steps', null, 'performance', 'daily_wins', 1, 14, 100);
$metricActive = $insertMetric('active_minutes', 'Active Minutes', 'min', null, 'performance', 'daily_wins', 1, 14, 2);
$metricRhr = $insertMetric('resting_heart_rate', 'Resting Heart Rate', 'bpm', null, 'health_comparison', 'series_average', 0, 7, 1);
$metricHrv = $insertMetric('hrv', 'Heart Rate Variability', 'ms', null, 'health_comparison', 'series_average', 1, 7, 2);
$metricSleep = $insertMetric('sleep_duration', 'Sleep Duration', 'min', 'hours_minutes', 'health_comparison', 'series_average', 1, 7, 15);

Database::run('INSERT INTO rly_data_sources (slug, name, source_class, is_active, created_at) VALUES (?,?,?,1,?)', ['apple_watch', 'Apple Watch', 'watch', $now]);
Database::run('INSERT INTO rly_data_sources (slug, name, source_class, is_active, created_at) VALUES (?,?,?,1,?)', ['iphone', 'iPhone', 'phone', $now]);
$srcWatch = (int) Database::fetchValue('SELECT id FROM rly_data_sources WHERE slug=?', ['apple_watch']);
$srcPhone = (int) Database::fetchValue('SELECT id FROM rly_data_sources WHERE slug=?', ['iphone']);

$hash = password_hash('x', PASSWORD_DEFAULT);
Database::run('INSERT INTO rly_users (name, username, email, password_hash, role, simulation, timezone, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)',
    ['Alice', 'alice', 'a@test.local', $hash, 'user', 0, 'America/New_York', 'active', $now, $now]);
Database::run('INSERT INTO rly_users (name, username, email, password_hash, role, simulation, timezone, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)',
    ['Bob', 'bob', 'b@test.local', $hash, 'user', 0, 'Europe/London', 'active', $now, $now]);
$alice = (int) Database::fetchValue('SELECT id FROM rly_users WHERE username=?', ['alice']);
$bob = (int) Database::fetchValue('SELECT id FROM rly_users WHERE username=?', ['bob']);

echo "\nMetric definitions\n";
$defs = Database::fetchAll('SELECT * FROM rly_metric_types ORDER BY slug');
assert_eq(5, count($defs), 'All five metrics seed correctly');
$bySlug = [];
foreach ($defs as $d) {
    $bySlug[$d['slug']] = $d;
}
assert_eq('daily_wins', $bySlug['steps']['scoring_strategy'], 'Steps strategy daily_wins');
assert_eq('daily_wins', $bySlug['active_minutes']['scoring_strategy'], 'Active minutes strategy');
assert_eq('series_average', $bySlug['hrv']['scoring_strategy'], 'HRV strategy series_average');
assert_eq('series_average', $bySlug['resting_heart_rate']['scoring_strategy'], 'RHR strategy');
assert_eq('series_average', $bySlug['sleep_duration']['scoring_strategy'], 'Sleep strategy');
assert_eq(1, (int) $bySlug['steps']['higher_wins'], 'Steps higher wins');
assert_eq(0, (int) $bySlug['resting_heart_rate']['higher_wins'], 'RHR lower wins');
assert_eq(1, (int) $bySlug['hrv']['higher_wins'], 'HRV higher wins');
assert_eq(100, (int) $bySlug['steps']['default_tie_threshold'], 'Steps default threshold');
assert_eq(2, (int) $bySlug['active_minutes']['default_tie_threshold'], 'Active minutes threshold');
assert_eq(1, (int) $bySlug['resting_heart_rate']['default_tie_threshold'], 'RHR threshold');
assert_eq(2, (int) $bySlug['hrv']['default_tie_threshold'], 'HRV threshold');
assert_eq(15, (int) $bySlug['sleep_duration']['default_tie_threshold'], 'Sleep threshold');
assert_eq('7h 47m', MetricFormatter::format(467, $bySlug['sleep_duration']), 'Sleep values format correctly');
assert_eq('11,248 steps', MetricFormatter::format(11248, $bySlug['steps']), 'Steps formatting');
assert_eq('84 min', MetricFormatter::format(84, $bySlug['active_minutes']), 'Active minutes formatting');
assert_eq('58 bpm', MetricFormatter::format(58, $bySlug['resting_heart_rate']), 'RHR formatting');
assert_eq('62 ms', MetricFormatter::format(62, $bySlug['hrv']), 'HRV formatting');

echo "\nMatch timezone authority\n";
$match = MatchService::create([
    'creator_id' => $alice,
    'opponent_id' => $bob,
    'metric_type_id' => $metricId,
    'start_date' => '2026-07-20',
    'length_days' => 3,
    'timezone' => 'America/Los_Angeles',
    'tie_threshold' => 100,
    'player_a_source_id' => $srcWatch,
    'player_b_source_id' => $srcWatch,
    'auto_accept' => true,
]);
$matchId = (int) $match['id'];
$day1 = Database::fetch('SELECT * FROM rly_match_days WHERE match_id=? AND day_number=1', [$matchId]);
assert_eq('2026-07-20', $day1['competition_date'], 'Match day uses match timezone calendar, not user TZ');
// Settlement: day 1 settles at 6am on day+2 in LA = 2026-07-22 06:00 PDT = 2026-07-22 13:00 UTC
$expectedSettles = SettlementService::computeSettlesAt('2026-07-20', 'America/Los_Angeles');
assert_eq($expectedSettles, $day1['settles_at'], 'settles_at derived from match timezone');
assert_true(!str_contains($expectedSettles, 'America'), 'settles_at stored as UTC string');

echo "\nIngestion idempotency & locks\n";
ResultIngestionService::ingest([
    'user_id' => $alice,
    'match_day_id' => (int) $day1['id'],
    'metric_type' => 'steps',
    'value' => 5000,
    'data_source' => 'apple_watch',
    'source_record_key' => 'rec-1',
]);
ResultIngestionService::ingest([
    'user_id' => $alice,
    'match_day_id' => (int) $day1['id'],
    'metric_type' => 'steps',
    'value' => 5500,
    'data_source' => 'apple_watch',
    'source_record_key' => 'rec-1',
]);
$count = (int) Database::fetchValue('SELECT COUNT(*) FROM rly_match_day_results WHERE match_day_id=? AND user_id=?', [(int) $day1['id'], $alice]);
assert_eq(1, $count, 'One result row per player per day');
$val = (int) Database::fetchValue('SELECT metric_value FROM rly_match_day_results WHERE match_day_id=? AND user_id=?', [(int) $day1['id'], $alice]);
assert_eq(5500, $val, 'Re-ingest same key updates in place');

ResultIngestionService::ingest([
    'user_id' => $alice,
    'match_day_id' => (int) $day1['id'],
    'value' => 6000,
    'data_source' => 'apple_watch',
    'source_record_key' => 'rec-2',
]);
$val = (int) Database::fetchValue('SELECT metric_value FROM rly_match_day_results WHERE match_day_id=? AND user_id=?', [(int) $day1['id'], $alice]);
assert_eq(6000, $val, 'Provisional result updates in place with new key');

ResultIngestionService::ingest([
    'user_id' => $bob,
    'match_day_id' => (int) $day1['id'],
    'value' => 5900,
    'data_source' => 'apple_watch',
    'source_record_key' => 'rec-b',
]);

echo "\nSettlement & scoring\n";
Clock::freezeForTests(new DateTimeImmutable('2026-07-22T14:00:00Z')); // past settles_at
SettlementService::refreshMatch($matchId);
$day1 = Database::fetch('SELECT * FROM rly_match_days WHERE id=?', [(int) $day1['id']]);
assert_eq('official', $day1['status'], 'Day settles to official when both values present');

$locked = false;
try {
    ResultIngestionService::ingest([
        'user_id' => $alice,
        'match_day_id' => (int) $day1['id'],
        'value' => 9999,
        'data_source' => 'apple_watch',
        'source_record_key' => 'rec-3',
    ]);
} catch (RuntimeException) {
    $locked = true;
}
assert_true($locked, 'Official day rejects ordinary ingestion');

$pack = MatchScoringService::forMatchId($matchId);
assert_eq(1, $pack['summary']['player_a_wins'], 'Official day counts toward score');
assert_eq(0, $pack['summary']['player_b_wins'], 'Bob did not win day 1');
assert_eq(0, $pack['summary']['ties'], 'Not a tie (margin 100 exactly → win)');

// Pending does not affect score: create day 2 results but leave pending
$day2 = Database::fetch('SELECT * FROM rly_match_days WHERE match_id=? AND day_number=2', [$matchId]);
Database::run("UPDATE rly_match_days SET status='pending', updated_at=? WHERE id=?", [Clock::nowUtcString(), (int) $day2['id']]);
ResultIngestionService::ingest([
    'user_id' => $alice, 'match_day_id' => (int) $day2['id'], 'value' => 10000, 'data_source' => 'apple_watch', 'source_record_key' => 'd2a',
], true);
ResultIngestionService::ingest([
    'user_id' => $bob, 'match_day_id' => (int) $day2['id'], 'value' => 2000, 'data_source' => 'apple_watch', 'source_record_key' => 'd2b',
], true);
Database::run("UPDATE rly_match_days SET status='pending' WHERE id=?", [(int) $day2['id']]);
$pack = MatchScoringService::forMatchId($matchId);
assert_eq(1, $pack['summary']['player_a_wins'], 'Pending days do not affect official score');
assert_eq(1, $pack['summary']['pending_days'], 'Pending day counted as pending');

echo "\nVoid vs tie\n";
$day3 = Database::fetch('SELECT * FROM rly_match_days WHERE match_id=? AND day_number=3', [$matchId]);
// Only Alice synced
ResultIngestionService::ingest([
    'user_id' => $alice, 'match_day_id' => (int) $day3['id'], 'value' => 8000, 'data_source' => 'apple_watch', 'source_record_key' => 'd3a',
], true);
$status = SettlementService::settleDayNow((int) $day3['id']);
assert_eq('void', $status, 'Missing opponent at settlement → void');
$pack = MatchScoringService::forMatchId($matchId);
assert_eq(1, $pack['summary']['voids'], 'Voids counted separately');
assert_eq(1, $pack['summary']['player_a_wins'], 'Void awards no win');

// Tie day on a fresh mini match — clock during the series so refresh won't void remaining days.
Clock::freezeForTests(new DateTimeImmutable('2026-08-01T12:00:00Z'));
$match2 = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricId,
    'start_date' => '2026-08-01', 'length_days' => 2, 'timezone' => 'UTC', 'tie_threshold' => 100,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcPhone, 'auto_accept' => true,
]);
$m2 = (int) $match2['id'];
$d = Database::fetch('SELECT * FROM rly_match_days WHERE match_id=? AND day_number=1', [$m2]);
ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $d['id'], 'value' => 10050, 'data_source' => $srcWatch, 'source_record_key' => 't1'], true);
ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $d['id'], 'value' => 10020, 'data_source' => $srcPhone, 'source_record_key' => 't2'], true);
SettlementService::settleDayNow((int) $d['id']);
// Prevent refresh from auto-settling day 2 by reasserting day 2 stays scheduled/live.
Database::run("UPDATE rly_match_days SET status='scheduled', official_at=NULL WHERE match_id=? AND day_number=2", [$m2]);
$pack = MatchScoringService::forMatchId($m2);
assert_eq(1, $pack['summary']['ties'], 'Tie counted');
assert_eq(0, $pack['summary']['player_a_wins'], 'Tie awards no win');
assert_eq(0, $pack['summary']['voids'], 'Ties and voids remain distinct');

$comp = MatchService::sourceComparability($pack['match']);
assert_true($comp['mismatch'], 'Watch vs phone triggers source mismatch');


echo "\nMatch completion rules\n";
// 7-7 with one pending must not be final draw
// 15-day series: days 1–14 alternate to 7–7 official, day 15 still pending.
$match3 = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricId,
    'start_date' => '2026-06-01', 'length_days' => 15, 'timezone' => 'UTC', 'tie_threshold' => 100,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$m3 = (int) $match3['id'];
$days = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$m3]);
foreach ($days as $i => $day) {
    $n = $i + 1;
    if ($n <= 14) {
        $a = $n % 2 === 1 ? 12000 : 9000;
        $b = $n % 2 === 1 ? 9000 : 12000;
        ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $day['id'], 'value' => $a, 'data_source' => $srcWatch, 'source_record_key' => "m3a{$n}"], true);
        ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $day['id'], 'value' => $b, 'data_source' => $srcWatch, 'source_record_key' => "m3b{$n}"], true);
        Database::run("UPDATE rly_match_days SET status='official', official_at=?, updated_at=? WHERE id=?", [Clock::nowUtcString(), Clock::nowUtcString(), (int) $day['id']]);
    } else {
        ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $day['id'], 'value' => 11000, 'data_source' => $srcWatch, 'source_record_key' => 'm3a15'], true);
        ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $day['id'], 'value' => 10000, 'data_source' => $srcWatch, 'source_record_key' => 'm3b15'], true);
        Database::run("UPDATE rly_match_days SET status='pending' WHERE id=?", [(int) $day['id']]);
    }
}
Database::run("UPDATE rly_matches SET status='settling' WHERE id=?", [$m3]);
$pack = MatchScoringService::forMatchId($m3);
assert_eq(7, $pack['summary']['player_a_wins'], '7–7 on official days while one remains pending');
assert_eq(7, $pack['summary']['player_b_wins'], '7–7 on official days while one remains pending (B)');
assert_true(!$pack['summary']['is_complete'], 'Not complete while a day is pending');
assert_true(!$pack['summary']['is_draw'], '7–7 is not a final draw while a day remains pending');
assert_true($pack['summary']['is_provisional'], 'Provisional while incomplete');

SettlementService::settleDayNow((int) $days[14]['id']);
$pack = MatchScoringService::forMatchId($m3);
assert_true($pack['summary']['is_complete'], 'Complete when every day terminal');
assert_eq(8, $pack['summary']['player_a_wins'], 'Pending day becomes official win for A');
Database::run("UPDATE rly_matches SET status='completed', completed_at=? WHERE id=?", [Clock::nowUtcString(), $m3]);
$pack = MatchScoringService::forMatchId($m3);
assert_true($pack['summary']['is_complete'], 'Completed match reports complete');
assert_true(!$pack['summary']['is_draw'], 'Completed decisive match is not a draw');
// Settling after final competition date, but before settlement lag voids/officials everything.
// Days Jul 23–24; clock Jul 25 12:00 UTC → day 23 past settles_at (void), day 24 pending (settles Jul 26).
Clock::freezeForTests(new DateTimeImmutable('2026-07-25T12:00:00Z'));
$match4 = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricId,
    'start_date' => '2026-07-23', 'length_days' => 2, 'timezone' => 'UTC', 'tie_threshold' => 100,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$m4 = (int) $match4['id'];
SettlementService::refreshMatch($m4);
$dayStates = Database::fetchAll('SELECT day_number, status, settles_at FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$m4]);
$status = (string) Database::fetchValue('SELECT status FROM rly_matches WHERE id=?', [$m4]);
assert_eq('settling', $status, 'Match enters settling after final competition date with unresolved days');
$pending = (int) Database::fetchValue("SELECT COUNT(*) FROM rly_match_days WHERE match_id=? AND status='pending'", [$m4]);
assert_true($pending >= 1, 'At least one day remains pending during settling');

echo "\nMissing data is not zero\n";
$outcome = MatchScoringService::dayOutcome(
    ['player_a_user_id' => 1, 'player_b_user_id' => 2, 'tie_threshold' => 100, 'higher_wins' => 1],
    ['status' => 'live', 'results' => []]
);
assert_eq('awaiting', $outcome['kind'], 'No results → awaiting, not zero');
assert_eq(null, $outcome['value_a'], 'Missing value stays null');

echo "\nDaily-wins: active minutes\n";
Clock::freezeForTests(new DateTimeImmutable('2026-09-01T12:00:00Z'));
$amMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricActive,
    'start_date' => '2026-09-01', 'length_days' => 3, 'timezone' => 'UTC', 'tie_threshold' => 2,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$amId = (int) $amMatch['id'];
$amDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$amId]);
ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $amDays[0]['id'], 'metric_type' => 'active_minutes', 'value' => 80, 'data_source' => $srcWatch, 'source_record_key' => 'am1a'], true);
ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $amDays[0]['id'], 'metric_type' => 'active_minutes', 'value' => 70, 'data_source' => $srcWatch, 'source_record_key' => 'am1b'], true);
SettlementService::settleDayNow((int) $amDays[0]['id']);
$pack = MatchScoringService::forMatchId($amId);
assert_eq('daily_wins', $pack['summary']['scoring_strategy'], 'Active minutes score by daily victories');
assert_eq(1, $pack['summary']['player_a_wins'], 'Active minutes day 1 win for A');

echo "\nSeries-average: HRV / RHR / Sleep\n";
Clock::freezeForTests(new DateTimeImmutable('2026-09-10T12:00:00Z'));
$hrvMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricHrv,
    'start_date' => '2026-09-10', 'length_days' => 3, 'timezone' => 'UTC', 'tie_threshold' => 2,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$hrvId = (int) $hrvMatch['id'];
$hrvDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$hrvId]);
// A averages higher: 60,62,64 vs 50,52,54
foreach ([[60, 50], [62, 52], [64, 54]] as $i => [$va, $vb]) {
    ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $hrvDays[$i]['id'], 'metric_type' => 'hrv', 'value' => $va, 'data_source' => $srcWatch, 'source_record_key' => "hrva{$i}"], true);
    ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $hrvDays[$i]['id'], 'metric_type' => 'hrv', 'value' => $vb, 'data_source' => $srcWatch, 'source_record_key' => "hrvb{$i}"], true);
    SettlementService::settleDayNow((int) $hrvDays[$i]['id']);
}
$pack = MatchScoringService::forMatchId($hrvId);
assert_eq('series_average', $pack['summary']['scoring_strategy'], 'Correct series summary type');
assert_eq($alice, $pack['summary']['leader_user_id'], 'HRV winner uses highest official average');
assert_true(abs(($pack['summary']['player_a_average'] ?? 0) - 62.0) < 0.01, 'HRV A average 62');
assert_true($pack['summary']['daily_comparison_a_leads'] >= 1, 'Daily comparison ownership recorded');
assert_true($pack['summary']['is_complete'], 'All days settled → complete');

// Pending observation does not affect official average
Database::run("UPDATE rly_match_days SET status='pending', official_at=NULL WHERE id=?", [(int) $hrvDays[2]['id']]);
$pack = MatchScoringService::forMatchId($hrvId);
assert_true(abs(($pack['summary']['player_a_average'] ?? 0) - 61.0) < 0.01, 'Pending observations do not affect official averages');
assert_true($pack['summary']['is_provisional'], 'Series-average match provisional until all days settle');
assert_true(!$pack['summary']['is_complete'], 'Incomplete while pending');
// Restore official for later
Database::run("UPDATE rly_match_days SET status='official', official_at=? WHERE id=?", [Clock::nowUtcString(), (int) $hrvDays[2]['id']]);

$rhrMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricRhr,
    'start_date' => '2026-09-20', 'length_days' => 2, 'timezone' => 'UTC', 'tie_threshold' => 1,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$rhrId = (int) $rhrMatch['id'];
$rhrDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$rhrId]);
foreach ([[55, 62], [57, 60]] as $i => [$va, $vb]) {
    ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $rhrDays[$i]['id'], 'metric_type' => 'resting_heart_rate', 'value' => $va, 'data_source' => $srcWatch, 'source_record_key' => "rhra{$i}"], true);
    ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $rhrDays[$i]['id'], 'metric_type' => 'resting_heart_rate', 'value' => $vb, 'data_source' => $srcWatch, 'source_record_key' => "rhrb{$i}"], true);
    SettlementService::settleDayNow((int) $rhrDays[$i]['id']);
}
$pack = MatchScoringService::forMatchId($rhrId);
assert_eq($alice, $pack['summary']['leader_user_id'], 'Resting heart rate winner uses lowest official average');

$sleepMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricSleep,
    'start_date' => '2026-09-25', 'length_days' => 2, 'timezone' => 'UTC', 'tie_threshold' => 15,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$sleepId = (int) $sleepMatch['id'];
$sleepDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$sleepId]);
foreach ([[450, 400], [460, 410]] as $i => [$va, $vb]) {
    ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $sleepDays[$i]['id'], 'metric_type' => 'sleep_duration', 'value' => $va, 'data_source' => $srcWatch, 'source_record_key' => "sla{$i}"], true);
    ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $sleepDays[$i]['id'], 'metric_type' => 'sleep_duration', 'value' => $vb, 'data_source' => $srcWatch, 'source_record_key' => "slb{$i}"], true);
    SettlementService::settleDayNow((int) $sleepDays[$i]['id']);
}
$pack = MatchScoringService::forMatchId($sleepId);
assert_eq($alice, $pack['summary']['leader_user_id'], 'Sleep winner uses highest official average');

echo "\nSeries-average: voids, draws, thresholds, daily ownership\n";
$avgMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricHrv,
    'start_date' => '2026-10-01', 'length_days' => 3, 'timezone' => 'UTC', 'tie_threshold' => 2,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$avgId = (int) $avgMatch['id'];
$avgDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$avgId]);
// Day 1: A 60 vs B 50 (A daily lead)
ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $avgDays[0]['id'], 'value' => 60, 'data_source' => $srcWatch, 'source_record_key' => 'avg1a'], true);
ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $avgDays[0]['id'], 'value' => 50, 'data_source' => $srcWatch, 'source_record_key' => 'avg1b'], true);
SettlementService::settleDayNow((int) $avgDays[0]['id']);
// Day 2: void (only A)
ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $avgDays[1]['id'], 'value' => 200, 'data_source' => $srcWatch, 'source_record_key' => 'avg2a'], true);
SettlementService::settleDayNow((int) $avgDays[1]['id']);
assert_eq('void', Database::fetchValue('SELECT status FROM rly_match_days WHERE id=?', [(int) $avgDays[1]['id']]), 'Void days excluded from averages');
// Day 3: A 50 vs B 60 (B daily lead) — A avg (60+50)/2=55, B (50+60)/2=55 → draw if complete
ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $avgDays[2]['id'], 'value' => 50, 'data_source' => $srcWatch, 'source_record_key' => 'avg3a'], true);
ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $avgDays[2]['id'], 'value' => 60, 'data_source' => $srcWatch, 'source_record_key' => 'avg3b'], true);
SettlementService::settleDayNow((int) $avgDays[2]['id']);
$pack = MatchScoringService::forMatchId($avgId);
assert_true($pack['summary']['voids'] >= 1, 'Void days remain separate');
assert_true(abs(($pack['summary']['player_a_average'] ?? 0) - 55.0) < 0.01, 'Void excluded; average uses official comparable days only');
assert_true(!isset($pack['summary']['player_a_average']) || $pack['summary']['player_a_average'] !== 0.0 || $pack['summary']['official_days'] > 0, 'Missing data is not treated as zero');
assert_eq(1, (int) $pack['summary']['daily_comparison_a_leads'], 'Daily comparison A lead count');
assert_eq(1, (int) $pack['summary']['daily_comparison_b_leads'], 'Daily comparison B lead count');
assert_true($pack['summary']['is_draw'], 'Final average below threshold difference is a draw');
assert_eq(null, $pack['summary']['leader_user_id'], 'Daily comparison ownership does not determine the final winner when averages draw');

// Equal-to-threshold decisive
$thrMatch = MatchService::create([
    'creator_id' => $alice, 'opponent_id' => $bob, 'metric_type_id' => $metricHrv,
    'start_date' => '2026-10-10', 'length_days' => 2, 'timezone' => 'UTC', 'tie_threshold' => 2,
    'player_a_source_id' => $srcWatch, 'player_b_source_id' => $srcWatch, 'auto_accept' => true,
]);
$thrId = (int) $thrMatch['id'];
$thrDays = Database::fetchAll('SELECT * FROM rly_match_days WHERE match_id=? ORDER BY day_number', [$thrId]);
foreach ([[62, 60], [62, 60]] as $i => [$va, $vb]) {
    ResultIngestionService::ingest(['user_id' => $alice, 'match_day_id' => (int) $thrDays[$i]['id'], 'value' => $va, 'data_source' => $srcWatch, 'source_record_key' => "thra{$i}"], true);
    ResultIngestionService::ingest(['user_id' => $bob, 'match_day_id' => (int) $thrDays[$i]['id'], 'value' => $vb, 'data_source' => $srcWatch, 'source_record_key' => "thrb{$i}"], true);
    SettlementService::settleDayNow((int) $thrDays[$i]['id']);
}
$pack = MatchScoringService::forMatchId($thrId);
assert_eq($alice, $pack['summary']['leader_user_id'], 'Final difference equal to threshold is decisive');
assert_true(!$pack['summary']['is_draw'], 'Equal threshold is not a draw');

echo "\nPresentation: feed + personal records + step compatibility\n";
$events = ActivityFeedService::eventsFromPack(MatchScoringService::forMatchId($thrId));
assert_true($events !== [], 'Correct social event generation');
$records = PersonalRecordsService::forUser($alice);
assert_true(isset($records['by_metric']['hrv']), 'Correct personal-record derivation');
$stepsPack = MatchScoringService::forMatchId($matchId);
assert_eq('daily_wins', $stepsPack['summary']['scoring_strategy'], 'Current step-only matches remain compatible');
$scoreline = MetricCompetitionService::scoreline($stepsPack['match'], $stepsPack['summary']);
assert_true(str_contains($scoreline['primary'], '–'), 'Daily-wins scoreline uses win tally');

Clock::resetForTests();
@unlink($testDb);

echo "\nResults: {$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);
