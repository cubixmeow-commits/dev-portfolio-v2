<?php

declare(strict_types=1);

namespace Rally\Core;

/**
 * Small fixed-window rate limiter backed by the database, used to slow
 * credential guessing on the auth endpoints. Windows are keyed by
 * action plus client identifier (IP or email).
 */
final class RateLimiter
{
    public static function tooMany(string $action, string $identifier, int $max, int $windowSeconds): bool
    {
        $key = $action . ':' . strtolower(trim($identifier));
        $windowStart = gmdate('Y-m-d H:i:s', time() - $windowSeconds);

        Database::run(
            'DELETE FROM rly_rate_events WHERE created_at < ?',
            [gmdate('Y-m-d H:i:s', time() - 86400)]
        );

        $count = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM rly_rate_events WHERE event_key = ? AND created_at >= ?',
            [$key, $windowStart]
        );
        return $count >= $max;
    }

    public static function hit(string $action, string $identifier): void
    {
        $key = $action . ':' . strtolower(trim($identifier));
        Database::run(
            'INSERT INTO rly_rate_events (event_key, created_at) VALUES (?, ?)',
            [$key, now_utc()]
        );
    }
}
