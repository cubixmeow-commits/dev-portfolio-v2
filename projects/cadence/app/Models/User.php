<?php

declare(strict_types=1);

namespace Cadence\Models;

use Cadence\Core\Config;
use Cadence\Core\Database;

final class User
{
    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [mb_strtolower(trim($email))]);
    }

    /** @return array<string, mixed>|null */
    public static function findByHandle(string $handle): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE handle = ?', [mb_strtolower(trim($handle))]);
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function handleTaken(string $handle): bool
    {
        return Database::fetchValue('SELECT 1 FROM users WHERE handle = ?', [mb_strtolower(trim($handle))]) !== null;
    }

    public static function emailTaken(string $email): bool
    {
        return Database::fetchValue('SELECT 1 FROM users WHERE email = ?', [mb_strtolower(trim($email))]) !== null;
    }

    public static function create(string $email, string $password, string $displayName, string $handle, string $timezone): int
    {
        Database::run(
            'INSERT INTO users (email, password_hash, display_name, handle, avatar_seed, timezone)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                mb_strtolower(trim($email)),
                self::hashPassword($password),
                trim($displayName),
                mb_strtolower(trim($handle)),
                bin2hex(random_bytes(12)),
                $timezone,
            ]
        );
        return Database::lastInsertId();
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => Config::int('security.bcrypt_cost', 12)]);
    }

    public static function updatePassword(int $userId, string $password): void
    {
        Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [self::hashPassword($password), $userId]);
    }

    public static function updateProfile(int $userId, string $displayName, ?string $bio, string $timezone): void
    {
        Database::run(
            'UPDATE users SET display_name = ?, bio = ?, timezone = ? WHERE id = ?',
            [trim($displayName), $bio !== null && trim($bio) !== '' ? trim($bio) : null, $timezone, $userId]
        );
    }

    public static function markEmailVerified(int $userId): void
    {
        Database::run('UPDATE users SET email_verified_at = NOW() WHERE id = ? AND email_verified_at IS NULL', [$userId]);
    }

    public static function touchLastActive(int $userId): void
    {
        Database::run('UPDATE users SET last_active_at = NOW() WHERE id = ?', [$userId]);
    }

    /**
     * Soft delete: anonymize identity fields, revoke sessions, keep
     * aggregate rows (check-ins, events) consistent under a neutral name.
     */
    public static function softDelete(int $userId): void
    {
        Database::transaction(static function () use ($userId): void {
            Database::run(
                "UPDATE users SET
                   email = CONCAT('deleted-', id, '@invalid.cadence'),
                   handle = CONCAT('deleted_', id),
                   display_name = 'Deleted member',
                   bio = NULL,
                   password_hash = ?,
                   email_verified_at = NULL
                 WHERE id = ?",
                [self::hashPassword(bin2hex(random_bytes(24))), $userId]
            );
            Database::run('DELETE FROM sessions WHERE user_id = ?', [$userId]);
            Database::run('DELETE FROM email_verifications WHERE user_id = ?', [$userId]);
            Database::run('DELETE FROM password_resets WHERE user_id = ?', [$userId]);
            Database::run('DELETE FROM notifications WHERE user_id = ?', [$userId]);
        });
    }

    /**
     * Create an email verification token and return the raw token for
     * the mail link. Only the SHA-256 hash is stored.
     */
    public static function createEmailVerification(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        Database::run(
            'INSERT INTO email_verifications (user_id, token_hash, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))',
            [$userId, hash('sha256', $token)]
        );
        return $token;
    }

    /**
     * Consume a verification token. Returns the user id on success,
     * null for unknown, expired, or already-consumed tokens.
     */
    public static function consumeEmailVerification(string $token): ?int
    {
        $row = Database::fetch(
            'SELECT id, user_id FROM email_verifications
             WHERE token_hash = ? AND consumed_at IS NULL AND expires_at > NOW()',
            [hash('sha256', $token)]
        );
        if ($row === null) {
            return null;
        }
        Database::run('UPDATE email_verifications SET consumed_at = NOW() WHERE id = ?', [$row['id']]);
        return (int) $row['user_id'];
    }
}
