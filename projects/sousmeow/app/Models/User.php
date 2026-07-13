<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class User
{
    public const PREFERRED_AI_OPTIONS = [
        'ChatGPT', 'Claude', 'Gemini', 'Grok', 'Perplexity', 'Other', 'No preference',
    ];

    public const EXPERIENCE_LEVELS = ['New to AI', 'Comfortable', 'Advanced'];

    public const THEME_OPTIONS = ['system', 'light', 'dark'];

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [self::normalizeEmail($email)]);
    }

    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public static function create(string $name, string $email, string $password): int
    {
        $now = now_utc();
        Database::run(
            'INSERT INTO users (name, email, password_hash, role, simulation, created_at) VALUES (?, ?, ?, ?, 0, ?)',
            [trim($name), self::normalizeEmail($email), password_hash($password, PASSWORD_DEFAULT), 'user', $now]
        );
        return Database::lastInsertId();
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::run('UPDATE users SET password_hash = ?, password_changed_at = ? WHERE id = ?', [
            password_hash($password, PASSWORD_DEFAULT),
            now_utc(),
            $id,
        ]);
    }

    /** Issue a verification token; returns the raw token for the email link. */
    public static function issueVerificationToken(int $userId): string
    {
        $raw = bin2hex(random_bytes(32));
        $expires = gmdate('Y-m-d H:i:s', time() + 86400);
        Database::run(
            'UPDATE users SET verification_token_hash = ?, verification_expires_at = ?, verification_sent_at = ? WHERE id = ?',
            [hash('sha256', $raw), $expires, now_utc(), $userId]
        );
        return $raw;
    }

    /** @return array<string, mixed>|null */
    public static function findByVerificationToken(string $rawToken): ?array
    {
        return Database::fetch(
            'SELECT * FROM users WHERE verification_token_hash = ? AND verification_expires_at > ?',
            [hash('sha256', $rawToken), now_utc()]
        );
    }

    public static function markEmailVerified(int $userId): void
    {
        Database::run(
            'UPDATE users SET email_verified_at = ?, verification_token_hash = NULL, verification_expires_at = NULL WHERE id = ?',
            [now_utc(), $userId]
        );
    }

    public static function clearVerificationToken(int $userId): void
    {
        Database::run(
            'UPDATE users SET verification_token_hash = NULL, verification_expires_at = NULL WHERE id = ?',
            [$userId]
        );
    }

    public static function issuePendingEmailChange(int $userId, string $newEmail): string
    {
        $raw = bin2hex(random_bytes(32));
        $expires = gmdate('Y-m-d H:i:s', time() + 86400);
        Database::run(
            'UPDATE users SET pending_email = ?, pending_email_token_hash = ?, pending_email_expires_at = ? WHERE id = ?',
            [self::normalizeEmail($newEmail), hash('sha256', $raw), $expires, $userId]
        );
        return $raw;
    }

    /** @return array<string, mixed>|null */
    public static function findByPendingEmailToken(string $rawToken): ?array
    {
        return Database::fetch(
            'SELECT * FROM users WHERE pending_email_token_hash = ? AND pending_email_expires_at > ? AND pending_email IS NOT NULL',
            [hash('sha256', $rawToken), now_utc()]
        );
    }

    public static function confirmPendingEmail(int $userId): void
    {
        $user = self::find($userId);
        if ($user === null || $user['pending_email'] === null) {
            return;
        }
        Database::run(
            'UPDATE users SET email = ?, pending_email = NULL, pending_email_token_hash = NULL, pending_email_expires_at = NULL WHERE id = ?',
            [$user['pending_email'], $userId]
        );
    }

    public static function clearPendingEmail(int $userId): void
    {
        Database::run(
            'UPDATE users SET pending_email = NULL, pending_email_token_hash = NULL, pending_email_expires_at = NULL WHERE id = ?',
            [$userId]
        );
    }

    /** @param array<string, mixed> $fields */
    public static function updateProfile(int $userId, array $fields): void
    {
        $allowed = ['name', 'bio', 'website', 'avatar_url'];
        $sets = [];
        $params = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $fields)) {
                $sets[] = $key . ' = ?';
                $params[] = $fields[$key];
            }
        }
        if ($sets === []) {
            return;
        }
        $params[] = $userId;
        Database::run('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    /** @param array<string, mixed> $fields */
    public static function updatePreferences(int $userId, array $fields): void
    {
        $allowed = ['preferred_ai', 'ai_experience_level', 'timezone', 'theme_preference'];
        $sets = [];
        $params = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $fields)) {
                $sets[] = $key . ' = ?';
                $params[] = $fields[$key];
            }
        }
        if ($sets === []) {
            return;
        }
        $params[] = $userId;
        Database::run('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    public static function completeOnboarding(int $userId): void
    {
        Database::run('UPDATE users SET onboarding_completed_at = ? WHERE id = ?', [now_utc(), $userId]);
    }

    public static function deleteAccount(int $userId): void
    {
        Database::run('DELETE FROM users WHERE id = ?', [$userId]);
    }

    public static function isRealUser(array $user): bool
    {
        return (int) ($user['simulation'] ?? 0) === 0 && ($user['role'] ?? 'user') !== 'admin';
    }

    public static function canSelfDelete(array $user): bool
    {
        return self::isRealUser($user) && ($user['role'] ?? 'user') !== 'admin';
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        if ($parts === []) {
            return '?';
        }
        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    }

    public static function count(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM users');
    }

    /** @return list<array<string, mixed>> */
    public static function recent(int $limit = 8): array
    {
        return Database::fetchAll(
            'SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT ' . max(1, $limit)
        );
    }
}
