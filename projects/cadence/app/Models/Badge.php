<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

/**
 * Badge catalog and evaluation. Badges are defined as rows (code +
 * criteria); evaluation runs inside the same transaction as the action
 * that might earn them, so a badge, its activity event, and its
 * notification appear exactly once or not at all.
 */
final class Badge
{
    /** @return list<array<string, mixed>> */
    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM badges ORDER BY criteria_value ASC, id ASC');
    }

    /** @return list<array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT b.*, ub.earned_at, ub.challenge_id
             FROM user_badges ub
             JOIN badges b ON b.id = ub.badge_id
             WHERE ub.user_id = ?
             ORDER BY ub.earned_at DESC',
            [$userId]
        );
    }

    /** Called inside the check-in transaction. */
    public static function evaluateAfterCheckIn(int $userId, int $challengeId, int $streak): void
    {
        // First step: very first check-in anywhere.
        $totalCheckins = (int) Database::fetchValue('SELECT COUNT(*) FROM check_ins WHERE user_id = ?', [$userId]);
        if ($totalCheckins === 1) {
            self::award($userId, 'first_step', null);
        }

        // Streak badges are per challenge: the unique key
        // (user, badge, challenge) lets Week One be earned in each
        // challenge while the feed still reads naturally.
        foreach ([7 => 'week_one', 30 => 'iron_month', 100 => 'century'] as $days => $code) {
            if ($streak === $days) {
                self::award($userId, $code, $challengeId);
            }
        }

        // Point machine: total points crossing the threshold.
        $points = (int) Database::fetchValue('SELECT total_points FROM users WHERE id = ?', [$userId]);
        if ($points >= 5000) {
            self::award($userId, 'point_machine', null);
        }
    }

    /** Called inside the join transaction. */
    public static function evaluateAfterJoin(int $userId): void
    {
        if (Participation::joinedCount($userId) >= 5) {
            self::award($userId, 'collector', null);
        }
    }

    /** Called when a challenge a user stayed in reaches its end. */
    public static function evaluateCompletion(int $userId, int $challengeId): void
    {
        self::award($userId, 'finisher', $challengeId);
    }

    /**
     * Idempotent award: the unique key on user_badges absorbs repeats,
     * and the event plus notification are only written on a fresh award.
     */
    public static function award(int $userId, string $code, ?int $challengeId): bool
    {
        $badge = Database::fetch('SELECT * FROM badges WHERE code = ?', [$code]);
        if ($badge === null) {
            return false;
        }

        // MySQL unique keys treat NULLs as distinct, so platform-wide
        // badges (challenge_id NULL) need an explicit existence check;
        // the unique key still backstops challenge-scoped races.
        $exists = Database::fetchValue(
            'SELECT 1 FROM user_badges WHERE user_id = ? AND badge_id = ? AND (challenge_id <=> ?)',
            [$userId, (int) $badge['id'], $challengeId]
        );
        if ($exists !== null) {
            return false;
        }

        try {
            Database::run(
                'INSERT INTO user_badges (user_id, badge_id, challenge_id) VALUES (?, ?, ?)',
                [$userId, (int) $badge['id'], $challengeId]
            );
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }

        ActivityEvent::record($userId, 'badge', $challengeId, (int) $badge['id']);
        Database::run(
            'INSERT INTO notifications (user_id, type, title, body, link) VALUES (?, ?, ?, ?, ?)',
            [
                $userId,
                'badge',
                'You earned ' . $badge['name'],
                (string) $badge['description'],
                url('/dashboard'),
            ]
        );
        return true;
    }
}
