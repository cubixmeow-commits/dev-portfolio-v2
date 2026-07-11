<?php

declare(strict_types=1);

/**
 * Streak logic acceptance tests: the written table of cases from the
 * build spec, executable. Run from the project root:
 *
 *   php scripts/streak-tests.php
 *
 * Creates throwaway rows (prefixed streaktest_), verifies each case,
 * and removes them again. Exit code 0 = all cases pass.
 *
 * Case table:
 *   1. First check-in                       -> streak 1, base points
 *   2. Same-day duplicate                   -> rejected, state unchanged
 *   3. Next-day check-in                    -> streak increments
 *   4. Milestone day (7)                    -> +5 bonus, event, badge
 *   5. Gap of 2+ days                       -> streak resets to 1
 *   6. Timezone: "today" follows the user's zone, not the server's
 */

require __DIR__ . '/../app/Core/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'Cadence\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

use Cadence\Core\Config;
use Cadence\Core\Database;
use Cadence\Models\CheckIn;
use Cadence\Models\Participation;

Config::load(require __DIR__ . '/../config/config.php');

$failures = 0;
$caseNo = 0;

function check(string $label, bool $condition): void
{
    global $failures, $caseNo;
    $caseNo++;
    if ($condition) {
        echo "  ok  $caseNo. $label\n";
    } else {
        $failures++;
        echo "FAIL  $caseNo. $label\n";
    }
}

/** Make a user + challenge + participation for one isolated scenario. */
function scenario(string $key, string $timezone = 'UTC'): array
{
    Database::run(
        "INSERT INTO users (email, password_hash, display_name, handle, avatar_seed, timezone)
         VALUES (?, 'x', 'Streak Tester', ?, 'seed', ?)",
        ["streaktest_$key@invalid.test", "streaktest_$key", $timezone]
    );
    $userId = Database::lastInsertId();
    Database::run(
        "INSERT INTO challenges (title, slug, description, category, points_per_checkin, start_date, end_date)
         VALUES (?, ?, 'test', 'fitness', 10, DATE_SUB(CURDATE(), INTERVAL 200 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY))",
        ["Streak test $key", "streaktest-$key"]
    );
    $challengeId = Database::lastInsertId();
    Database::run('INSERT INTO challenge_participants (challenge_id, user_id) VALUES (?, ?)', [$challengeId, $userId]);

    return [
        'user'      => Database::fetch('SELECT * FROM users WHERE id = ?', [$userId]),
        'challenge' => Database::fetch('SELECT * FROM challenges WHERE id = ?', [$challengeId]),
    ];
}

/** Force the participation into a given prior state. */
function primeState(array $s, int $streak, ?string $lastDate): void
{
    Database::run(
        'UPDATE challenge_participants SET current_streak = ?, longest_streak = ?, last_checkin_date = ?
         WHERE challenge_id = ? AND user_id = ?',
        [$streak, $streak, $lastDate, (int) $s['challenge']['id'], (int) $s['user']['id']]
    );
}

function participantState(array $s): array
{
    return Participation::find((int) $s['challenge']['id'], (int) $s['user']['id']);
}

function cleanup(): void
{
    Database::run("DELETE FROM users WHERE email LIKE 'streaktest_%'");
    Database::run("DELETE FROM challenges WHERE slug LIKE 'streaktest-%'");
}

cleanup();
echo "Streak logic cases:\n";

// Case 1: first check-in.
$s = scenario('first');
$r = CheckIn::perform($s['user'], $s['challenge'], 'first one');
$p = participantState($s);
check('first check-in starts a streak of 1', $r['ok'] && $r['streak'] === 1 && (int) $p['current_streak'] === 1);
check('base points awarded (10, no bonus)', ($r['points'] ?? 0) === 10 && (int) $p['points'] === 10);

// Case 2: same-day duplicate is rejected and changes nothing.
$before = participantState($s);
$r2 = CheckIn::perform($s['user'], $s['challenge'], null);
$after = participantState($s);
check('same-day duplicate is rejected with a friendly message', !$r2['ok'] && str_contains($r2['error'] ?? '', 'Already checked in'));
check('duplicate leaves streak and points untouched', $before['current_streak'] === $after['current_streak'] && $before['points'] === $after['points']);

// Case 3: yesterday -> today increments.
$s = scenario('nextday');
$yesterday = (new DateTimeImmutable('yesterday', new DateTimeZone('UTC')))->format('Y-m-d');
primeState($s, 3, $yesterday);
$r = CheckIn::perform($s['user'], $s['challenge'], null);
check('next-day check-in increments the streak (3 -> 4)', $r['ok'] && $r['streak'] === 4);

// Case 4: milestone day 7 pays the bonus and writes the milestone event.
$s = scenario('milestone');
primeState($s, 6, $yesterday);
$r = CheckIn::perform($s['user'], $s['challenge'], null);
$eventCount = (int) Database::fetchValue(
    "SELECT COUNT(*) FROM activity_events WHERE user_id = ? AND type = 'streak_milestone'",
    [(int) $s['user']['id']]
);
$badgeCount = (int) Database::fetchValue(
    "SELECT COUNT(*) FROM user_badges ub JOIN badges b ON b.id = ub.badge_id
     WHERE ub.user_id = ? AND b.code = 'week_one'",
    [(int) $s['user']['id']]
);
check('day 7 pays the +5 milestone bonus (15 total)', $r['ok'] && $r['streak'] === 7 && $r['points'] === 15);
check('day 7 writes exactly one streak_milestone event', $eventCount === 1);
check('day 7 awards the Week One badge exactly once', $badgeCount === 1);
$milestoneNotifs = (int) Database::fetchValue(
    "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND type = 'streak_milestone'",
    [(int) $s['user']['id']]
);
$badgeNotifs = (int) Database::fetchValue(
    "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND type = 'badge' AND title LIKE '%Week one%'",
    [(int) $s['user']['id']]
);
check('day 7 sends exactly one milestone notification', $milestoneNotifs === 1);
check('day 7 sends exactly one Week One badge notification', $badgeNotifs === 1);

// Case 5: a gap resets the streak to 1.
$s = scenario('gap');
$threeDaysAgo = (new DateTimeImmutable('-3 days', new DateTimeZone('UTC')))->format('Y-m-d');
primeState($s, 12, $threeDaysAgo);
$r = CheckIn::perform($s['user'], $s['challenge'], null);
$p = participantState($s);
check('a 2+ day gap resets the streak to 1', $r['ok'] && $r['streak'] === 1);
check('longest streak survives the reset', (int) $p['longest_streak'] === 12);

// Case 6: timezone edge. Pick zones on both sides of the date line;
// whenever their local dates differ, todayFor must reflect each.
$kiritimati = CheckIn::todayFor('Pacific/Kiritimati'); // UTC+14
$midway = CheckIn::todayFor('Pacific/Midway');         // UTC-11
$expectedKiritimati = (new DateTimeImmutable('now', new DateTimeZone('Pacific/Kiritimati')))->format('Y-m-d');
$expectedMidway = (new DateTimeImmutable('now', new DateTimeZone('Pacific/Midway')))->format('Y-m-d');
check('todayFor follows the user timezone across the date line', $kiritimati === $expectedKiritimati && $midway === $expectedMidway && $kiritimati > $midway);

// And the check-in row lands on the user's local date, not the server's.
$s = scenario('tzrow', 'Pacific/Kiritimati');
$r = CheckIn::perform($s['user'], $s['challenge'], null);
$rowDate = Database::fetchValue(
    'SELECT checkin_date FROM check_ins WHERE user_id = ?',
    [(int) $s['user']['id']]
);
check('check-in row is stamped with the user-local date', $r['ok'] && $rowDate === $expectedKiritimati);

// Unknown timezone falls back to UTC instead of erroring.
check('unknown timezone falls back to UTC', CheckIn::todayFor('Not/AZone') === (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d'));

cleanup();

echo $failures === 0 ? "\nAll cases pass.\n" : "\n$failures case(s) FAILED.\n";
exit($failures === 0 ? 0 : 1);
