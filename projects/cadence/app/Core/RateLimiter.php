<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Fixed-window rate limiter backed by the rate_limits table. Keys are
 * hashed so raw IPs and emails never sit in the table. The upsert is
 * atomic, so concurrent requests cannot double-count a window reset.
 */
final class RateLimiter
{
    /**
     * Record a hit and report whether the caller is within the limit.
     * Returns true when the action is allowed.
     */
    public static function hit(string $bucket, string $key, int $maxHits, int $windowSeconds): bool
    {
        $keyHash = hash('sha256', $key);

        Database::run(
            'INSERT INTO rate_limits (bucket, key_hash, hits, window_start)
             VALUES (?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE
               hits = IF(window_start < DATE_SUB(NOW(), INTERVAL ? SECOND), 1, hits + 1),
               window_start = IF(window_start < DATE_SUB(NOW(), INTERVAL ? SECOND), NOW(), window_start)',
            [$bucket, $keyHash, $windowSeconds, $windowSeconds]
        );

        $hits = (int) Database::fetchValue(
            'SELECT hits FROM rate_limits WHERE bucket = ? AND key_hash = ?',
            [$bucket, $keyHash]
        );

        return $hits <= $maxHits;
    }

    /**
     * Convenience guard for auth endpoints: limit by IP within a bucket.
     * On breach, flashes a friendly message and redirects back.
     */
    public static function guard(string $bucket, int $maxHits = 5, int $windowSeconds = 900, ?string $redirectTo = null): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!self::hit($bucket, 'ip:' . $ip, $maxHits, $windowSeconds)) {
            Flash::set('error', 'Too many attempts. Wait a few minutes and try again.');
            redirect($redirectTo ?? ($_SERVER['REQUEST_URI'] ?? '/'));
        }
    }
}
