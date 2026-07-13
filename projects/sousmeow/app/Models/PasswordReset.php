<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class PasswordReset
{
    /** Issue a reset token (60 minute expiry). Returns raw token for the email. */
    public static function issue(int $userId): string
    {
        $raw = bin2hex(random_bytes(32));
        $now = now_utc();
        $expires = gmdate('Y-m-d H:i:s', time() + 3600);

        Database::run(
            'UPDATE password_reset_tokens SET used_at = ? WHERE user_id = ? AND used_at IS NULL',
            [$now, $userId]
        );
        Database::run(
            'INSERT INTO password_reset_tokens (user_id, token_hash, expires_at, created_at) VALUES (?, ?, ?, ?)',
            [$userId, hash('sha256', $raw), $expires, $now]
        );

        return $raw;
    }

    /** @return array<string, mixed>|null */
    public static function findValid(string $rawToken): ?array
    {
        return Database::fetch(
            'SELECT * FROM password_reset_tokens WHERE token_hash = ? AND used_at IS NULL AND expires_at > ?',
            [hash('sha256', $rawToken), now_utc()]
        );
    }

    public static function consume(string $rawToken): ?int
    {
        $row = self::findValid($rawToken);
        if ($row === null) {
            return null;
        }
        Database::run('UPDATE password_reset_tokens SET used_at = ? WHERE id = ?', [now_utc(), $row['id']]);
        return (int) $row['user_id'];
    }

    public static function invalidateForUser(int $userId): void
    {
        Database::run(
            'UPDATE password_reset_tokens SET used_at = ? WHERE user_id = ? AND used_at IS NULL',
            [now_utc(), $userId]
        );
    }
}
