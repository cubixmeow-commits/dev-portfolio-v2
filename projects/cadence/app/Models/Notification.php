<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

/**
 * In-app notifications. Cadence has no cron on shared hosting, so
 * time-based notifications (challenge starting tomorrow, challenge
 * ended with final rank) are synthesized lazily when the user loads
 * their dashboard, deduplicated by (user, type, link). Event-based
 * ones (badge, milestone) are written inside the transaction that
 * caused them.
 */
final class Notification
{
    public static function push(int $userId, string $type, string $title, string $body = '', ?string $link = null): void
    {
        Database::run(
            'INSERT INTO notifications (user_id, type, title, body, link) VALUES (?, ?, ?, ?, ?)',
            [$userId, $type, mb_substr($title, 0, 120), mb_substr($body, 0, 255), $link]
        );
    }

    /** Push unless an identical (type, link) notification already exists. */
    public static function pushOnce(int $userId, string $type, string $title, string $body = '', ?string $link = null): void
    {
        $exists = Database::fetchValue(
            'SELECT 1 FROM notifications WHERE user_id = ? AND type = ? AND (link <=> ?)',
            [$userId, $type, $link]
        );
        if ($exists === null) {
            self::push($userId, $type, $title, $body, $link);
        }
    }

    public static function unreadCount(int $userId): int
    {
        return (int) Database::fetchValue(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND read_at IS NULL',
            [$userId]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function forUser(int $userId, int $limit = 30): array
    {
        $limit = max(1, min(100, $limit));
        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT $limit",
            [$userId]
        );
    }

    public static function markAllRead(int $userId): void
    {
        Database::run('UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL', [$userId]);
    }

    /**
     * Lazy time-based notifications, called on dashboard load.
     *
     * @param array<string, mixed> $user
     */
    public static function sync(array $user): void
    {
        $userId = (int) $user['id'];

        // Challenges I joined that start tomorrow.
        $starting = Database::fetchAll(
            'SELECT c.title, c.slug FROM challenge_participants cp
             JOIN challenges c ON c.id = cp.challenge_id
             WHERE cp.user_id = ? AND c.start_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)',
            [$userId]
        );
        foreach ($starting as $c) {
            self::pushOnce(
                $userId,
                'challenge_starting',
                $c['title'] . ' starts tomorrow',
                'First check-in opens at midnight in your timezone.',
                url('/challenges/' . $c['slug'])
            );
        }

        // Challenges that ended while I was a member: final rank.
        $ended = Database::fetchAll(
            'SELECT c.id, c.title, c.slug, cp.points FROM challenge_participants cp
             JOIN challenges c ON c.id = cp.challenge_id
             WHERE cp.user_id = ? AND c.end_date < CURDATE() AND c.end_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)',
            [$userId]
        );
        foreach ($ended as $c) {
            $rank = 1 + (int) Database::fetchValue(
                'SELECT COUNT(*) FROM challenge_participants WHERE challenge_id = ? AND points > ?',
                [(int) $c['id'], (int) $c['points']]
            );
            $of = (int) Database::fetchValue(
                'SELECT COUNT(*) FROM challenge_participants WHERE challenge_id = ?',
                [(int) $c['id']]
            );
            self::pushOnce(
                $userId,
                'challenge_ended',
                $c['title'] . ' has ended',
                "You finished #{$rank} of {$of}. Nice work showing up.",
                url('/challenges/' . $c['slug'])
            );
            Badge::evaluateCompletion($userId, (int) $c['id']);
        }
    }
}
