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
use Rally\Services\Clock;
use Rally\Services\MatchService;
use Rally\Services\MatchScoringService;
use Rally\Services\MetricComparisonService;
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
Database::run('INSERT INTO rly_metric_types (slug, name, unit, higher_wins, is_active, created_at) VALUES (?,?,?,1,1,?)', ['steps', 'Daily Steps', 'steps', $now]);
$metricId = (int) Database::fetchValue('SELECT id FROM rly_metric_types WHERE slug=?', ['steps']);
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

Clock::resetForTests();
@unlink($testDb);

echo "\nResults: {$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);
