<?php

declare(strict_types=1);

namespace SousMeow\Models;

use SousMeow\Core\Database;

final class User
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
    }

    public static function create(string $name, string $email, string $password): int
    {
        Database::run(
            'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)',
            [trim($name), strtolower(trim($email)), password_hash($password, PASSWORD_DEFAULT), 'user', now_utc()]
        );
        return Database::lastInsertId();
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [
            password_hash($password, PASSWORD_DEFAULT),
            $id,
        ]);
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
