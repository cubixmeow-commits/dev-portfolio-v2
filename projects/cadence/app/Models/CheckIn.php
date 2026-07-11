<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

/**
 * Check-in domain logic: the single source of truth for streaks and
 * points (mirrored by the Java seed engine; documented in
 * ARCHITECTURE.md).
 *
 * Rules:
 *  - "Today" is the calendar date in the user's timezone.
 *  - One check-in per participant per day, enforced by a DB unique key.
 *  - Streak increments when today equals last_checkin_date + 1 day;
 *    stays a streak of 1 after any larger gap.
 *  - Points: challenge points_per_checkin, plus a +5 bonus when the new
 *    streak hits exactly 7, 30, or 100. Milestones also write a
 *    streak_milestone activity event.
 */
final class CheckIn
{
    public const MILESTONES = [7, 30, 100];
    public const MILESTONE_BONUS = 5;

    /** Calendar date string for "today" in the given timezone. */
    public static function todayFor(string $timezone): string
    {
        try {
            $tz = new \DateTimeZone($timezone);
        } catch (\Exception) {
            $tz = new \DateTimeZone('UTC');
        }
        return (new \DateTimeImmutable('now', $tz))->format('Y-m-d');
    }

    /**
     * Perform a check-in for today.
     *
     * @param array<string, mixed> $user       users row
     * @param array<string, mixed> $challenge  challenges row
     * @return array{ok: bool, error?: string, streak?: int, points?: int, milestone?: int|null}
     */
    public static function perform(array $user, array $challenge, ?string $note): array
    {
        $userId = (int) $user['id'];
        $challengeId = (int) $challenge['id'];
        $today = self::todayFor((string) $user['timezone']);

        if ($today < $challenge['start_date']) {
            return ['ok' => false, 'error' => 'This challenge has not started yet. It begins ' . date('M j', strtotime((string) $challenge['start_date'])) . '.'];
        }
        if ($today > $challenge['end_date']) {
            return ['ok' => false, 'error' => 'This challenge has ended. Check-ins are closed.'];
        }

        $participation = Participation::find($challengeId, $userId);
        if ($participation === null) {
            return ['ok' => false, 'error' => 'Join the challenge first, then check in.'];
        }

        $note = $note !== null ? mb_substr(trim($note), 0, 200) : null;
        if ($note === '') {
            $note = null;
        }

        try {
            return Database::transaction(static function () use ($user, $challenge, $participation, $userId, $challengeId, $today, $note): array {
                // Streak math against the previous check-in date.
                $last = $participation['last_checkin_date'];
                $yesterday = (new \DateTimeImmutable($today))->modify('-1 day')->format('Y-m-d');
                $streak = ($last === $yesterday) ? ((int) $participation['current_streak']) + 1 : 1;

                $milestone = in_array($streak, self::MILESTONES, true) ? $streak : null;
                $points = (int) $challenge['points_per_checkin'] + ($milestone !== null ? self::MILESTONE_BONUS : 0);

                // The unique key (participant_id, checkin_date) is the
                // duplicate guard; a second submit for today throws 23000.
                Database::run(
                    'INSERT INTO check_ins (participant_id, user_id, challenge_id, checkin_date, note, points_awarded)
                     VALUES (?, ?, ?, ?, ?, ?)',
                    [(int) $participation['id'], $userId, $challengeId, $today, $note, $points]
                );

                Database::run(
                    'UPDATE challenge_participants
                     SET current_streak = ?, longest_streak = GREATEST(longest_streak, ?),
                         points = points + ?, last_checkin_date = ?
                     WHERE id = ?',
                    [$streak, $streak, $points, $today, (int) $participation['id']]
                );

                Database::run(
                    'UPDATE users SET total_points = total_points + ?, last_active_at = NOW() WHERE id = ?',
                    [$points, $userId]
                );

                $eventMeta = ['streak' => $streak];
                if ($note !== null) {
                    $eventMeta['note'] = $note;
                }
                ActivityEvent::record($userId, 'checkin', $challengeId, null, $eventMeta);
                if ($milestone !== null) {
                    ActivityEvent::record($userId, 'streak_milestone', $challengeId, null, ['streak' => $streak]);
                    Notification::push(
                        $userId,
                        'streak_milestone',
                        $streak . ' day streak in ' . $challenge['title'],
                        'Milestone bonus: +' . self::MILESTONE_BONUS . ' points on top of today\'s check-in.',
                        url('/challenges/' . $challenge['slug'])
                    );
                }

                Badge::evaluateAfterCheckIn($userId, $challengeId, $streak);

                return ['ok' => true, 'streak' => $streak, 'points' => $points, 'milestone' => $milestone];
            });
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return ['ok' => false, 'error' => 'Already checked in today. Come back tomorrow.'];
            }
            throw $e;
        }
    }

    /** The user's check-in dates in one challenge, newest first. @return list<array<string, mixed>> */
    public static function historyFor(int $participantId, int $limit = 30): array
    {
        $limit = max(1, min(365, $limit));
        return Database::fetchAll(
            "SELECT checkin_date, note, points_awarded, created_at
             FROM check_ins WHERE participant_id = ?
             ORDER BY checkin_date DESC LIMIT $limit",
            [$participantId]
        );
    }
}
