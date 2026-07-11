<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

final class ActivityEvent
{
    /**
     * Append an event to the global feed.
     *
     * @param array<string, mixed>|null $meta
     */
    public static function record(int $userId, string $type, ?int $challengeId = null, ?int $badgeId = null, ?array $meta = null): void
    {
        Database::run(
            'INSERT INTO activity_events (user_id, type, challenge_id, badge_id, meta) VALUES (?, ?, ?, ?, ?)',
            [$userId, $type, $challengeId, $badgeId, $meta === null ? null : json_encode($meta, JSON_UNESCAPED_UNICODE)]
        );
    }

    /**
     * Cursor-paginated feed page, newest first. The cursor is the last
     * seen event id; ids are monotonic so this stays correct while new
     * events arrive (no page drift, unlike OFFSET).
     *
     * @return list<array<string, mixed>>
     */
    public static function feed(?int $beforeId, int $limit, ?int $forUserId = null): array
    {
        $params = [];
        $where = [];

        if ($beforeId !== null) {
            $where[] = 'ae.id < ?';
            $params[] = $beforeId;
        }
        if ($forUserId !== null) {
            // "Challenges I'm in": only events from those challenges.
            $where[] = 'ae.challenge_id IN (SELECT challenge_id FROM challenge_participants WHERE user_id = ?)';
            $params[] = $forUserId;
        }

        $whereSql = $where === [] ? '' : 'WHERE ' . implode(' AND ', $where);
        $limit = max(1, min(50, $limit));

        return Database::fetchAll(
            "SELECT ae.*, u.display_name, u.handle, u.avatar_seed, u.timezone AS user_timezone,
                    c.title AS challenge_title, c.slug AS challenge_slug,
                    b.name AS badge_name, b.icon AS badge_icon
             FROM activity_events ae
             JOIN users u ON u.id = ae.user_id
             LEFT JOIN challenges c ON c.id = ae.challenge_id
             LEFT JOIN badges b ON b.id = ae.badge_id
             $whereSql
             ORDER BY ae.id DESC
             LIMIT $limit",
            $params
        );
    }

    /** Recent events within one challenge, for its detail page. @return list<array<string, mixed>> */
    public static function forChallenge(int $challengeId, int $limit = 10): array
    {
        $limit = max(1, min(50, $limit));
        return Database::fetchAll(
            "SELECT ae.*, u.display_name, u.handle, u.avatar_seed,
                    b.name AS badge_name, b.icon AS badge_icon
             FROM activity_events ae
             JOIN users u ON u.id = ae.user_id
             LEFT JOIN badges b ON b.id = ae.badge_id
             WHERE ae.challenge_id = ?
             ORDER BY ae.id DESC
             LIMIT $limit",
            [$challengeId]
        );
    }

    /** Recent events by one user, for profiles. @return list<array<string, mixed>> */
    public static function forUser(int $userId, int $limit = 15): array
    {
        $limit = max(1, min(50, $limit));
        return Database::fetchAll(
            "SELECT ae.*, u.display_name, u.handle, u.avatar_seed,
                    c.title AS challenge_title, c.slug AS challenge_slug,
                    b.name AS badge_name, b.icon AS badge_icon
             FROM activity_events ae
             JOIN users u ON u.id = ae.user_id
             LEFT JOIN challenges c ON c.id = ae.challenge_id
             LEFT JOIN badges b ON b.id = ae.badge_id
             WHERE ae.user_id = ?
             ORDER BY ae.id DESC
             LIMIT $limit",
            [$userId]
        );
    }

    /**
     * Human sentence for an event row, HTML-escaped, with links.
     * The one place feed copy lives.
     */
    public static function sentence(array $event): string
    {
        $name = '<a href="' . e(url('/u/' . $event['handle'])) . '">' . e($event['display_name']) . '</a>';
        $challenge = $event['challenge_title'] !== null
            ? '<a href="' . e(url('/challenges/' . $event['challenge_slug'])) . '">' . e($event['challenge_title']) . '</a>'
            : 'a challenge';
        $meta = [];
        if (!empty($event['meta'])) {
            $decoded = json_decode((string) $event['meta'], true);
            if (is_array($decoded)) {
                $meta = $decoded;
            }
        }

        return match ($event['type']) {
            'checkin' => $name . ' checked in to ' . $challenge
                . (isset($meta['streak']) && (int) $meta['streak'] > 1
                    ? ' <span class="muted">(day ' . e((string) (int) $meta['streak']) . ')</span>'
                    : ''),
            'joined' => $name . ' joined ' . $challenge,
            'badge' => $name . ' earned the <strong>' . e($event['badge_name'] ?? 'badge') . '</strong> badge',
            'streak_milestone' => $name . ' hit a <strong>' . e((string) (int) ($meta['streak'] ?? 0)) . ' day streak</strong> in ' . $challenge,
            'challenge_completed' => $name . ' completed ' . $challenge,
            default => $name . ' did something',
        };
    }
}
