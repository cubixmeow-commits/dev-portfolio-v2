<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Database;

final class PasswordReset
{
    /**
     * Issue a single-use reset token (30 minute expiry) and return the
     * raw token for the mail link. Outstanding tokens for the user are
     * invalidated first so only the newest link works.
     */
    public static function issue(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        Database::run('UPDATE password_resets SET consumed_at = NOW() WHERE user_id = ? AND consumed_at IS NULL', [$userId]);
        Database::run(
            'INSERT INTO password_resets (user_id, token_hash, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))',
            [$userId, hash('sha256', $token)]
        );
        return $token;
    }

    /** Look up a live token without consuming it (for showing the form). */
    public static function peek(string $token): ?array
    {
        return Database::fetch(
            'SELECT id, user_id FROM password_resets
             WHERE token_hash = ? AND consumed_at IS NULL AND expires_at > NOW()',
            [hash('sha256', $token)]
        );
    }

    /** Consume a live token. Returns the user id or null. */
    public static function consume(string $token): ?int
    {
        $row = self::peek($token);
        if ($row === null) {
            return null;
        }
        Database::run('UPDATE password_resets SET consumed_at = NOW() WHERE id = ?', [$row['id']]);
        return (int) $row['user_id'];
    }
}
