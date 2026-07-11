<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

final class Participation
{
    /** @return array<string, mixed>|null */
    public static function find(int $challengeId, int $userId): ?array
    {
        return Database::fetch(
            'SELECT * FROM challenge_participants WHERE challenge_id = ? AND user_id = ?',
            [$challengeId, $userId]
        );
    }

    /**
     * Join a challenge: create the participation, bump the denormalized
     * counter, and record the activity event. Idempotent at the DB level
     * via the unique key; a double join is reported as already joined.
     *
     * @return bool true if newly joined, false when already a member
     */
    public static function join(int $challengeId, int $userId): bool
    {
        return Database::transaction(static function () use ($challengeId, $userId): bool {
            try {
                Database::run(
                    'INSERT INTO challenge_participants (challenge_id, user_id) VALUES (?, ?)',
                    [$challengeId, $userId]
                );
            } catch (\PDOException $e) {
                if ($e->getCode() === '23000') {
                    return false;
                }
                throw $e;
            }
            Database::run('UPDATE challenges SET participant_count = participant_count + 1 WHERE id = ?', [$challengeId]);
            ActivityEvent::record($userId, 'joined', $challengeId);
            Badge::evaluateAfterJoin($userId);
            return true;
        });
    }

    /**
     * Leave a challenge. Progress in that challenge is forfeited: the
     * participation row and its check-ins go, and the user's total
     * points drop by what the challenge had contributed. Documented as
     * a product rule on the challenge page.
     */
    public static function leave(int $challengeId, int $userId): bool
    {
        return Database::transaction(static function () use ($challengeId, $userId): bool {
            $participation = self::find($challengeId, $userId);
            if ($participation === null) {
                return false;
            }
            Database::run(
                'UPDATE users SET total_points = GREATEST(0, total_points - ?) WHERE id = ?',
                [(int) $participation['points'], $userId]
            );
            Database::run('DELETE FROM challenge_participants WHERE id = ?', [(int) $participation['id']]);
            Database::run(
                'UPDATE challenges SET participant_count = GREATEST(0, participant_count - 1) WHERE id = ?',
                [$challengeId]
            );
            return true;
        });
    }

    /** Active-challenge cards for a user's dashboard and nav ring. @return list<array<string, mixed>> */
    public static function activeForUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT cp.*, c.title, c.slug, c.category, c.cover_style, c.points_per_checkin,
                    c.start_date, c.end_date, c.participant_count
             FROM challenge_participants cp
             JOIN challenges c ON c.id = cp.challenge_id
             WHERE cp.user_id = ? AND c.start_date <= CURDATE() AND c.end_date >= CURDATE()
             ORDER BY cp.joined_at ASC',
            [$userId]
        );
    }

    /** Count of challenges the user has ever joined (for the Collector badge). */
    public static function joinedCount(int $userId): int
    {
        return (int) Database::fetchValue(
            'SELECT COUNT(*) FROM challenge_participants WHERE user_id = ?',
            [$userId]
        );
    }

    /**
     * Fraction of today's check-ins done across active challenges, for
     * the streak ring: 1.0 when every active challenge is checked in
     * today, null when the user has no active challenges.
     */
    public static function ringToday(int $userId, string $userTimezone): ?float
    {
        $today = CheckIn::todayFor($userTimezone);
        $row = Database::fetch(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(cp.last_checkin_date = ?), 0) AS done
             FROM challenge_participants cp
             JOIN challenges c ON c.id = cp.challenge_id
             WHERE cp.user_id = ? AND c.start_date <= CURDATE() AND c.end_date >= CURDATE()',
            [$today, $userId]
        );
        $total = (int) ($row['total'] ?? 0);
        if ($total === 0) {
            return null;
        }
        return ((int) $row['done']) / $total;
    }
}
