<?php

declare(strict_types=1);

namespace SousMeow\Core;

use PDO;

/**
 * Single shared PDO connection with two supported drivers: SQLite for
 * zero-setup local runs and MySQL for Hostinger deployment. Every query
 * in the application goes through prepared statements on this handle;
 * there is no other database access path.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ];

        if (Config::string('db.driver', 'sqlite') === 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                Config::string('db.host'),
                Config::int('db.port', 3306),
                Config::string('db.name'),
                Config::string('db.charset', 'utf8mb4')
            );
            $options[PDO::ATTR_EMULATE_PREPARES] = false;
            self::$pdo = new PDO($dsn, Config::string('db.user'), Config::string('db.password'), $options);
            return self::$pdo;
        }

        $path = Config::string('db.sqlite_path');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        self::$pdo = new PDO('sqlite:' . $path, null, null, $options);
        self::$pdo->exec('PRAGMA foreign_keys = ON');
        self::$pdo->exec('PRAGMA busy_timeout = 5000');
        self::$pdo->exec('PRAGMA journal_mode = WAL');
        return self::$pdo;
    }

    public static function driver(): string
    {
        return Config::string('db.driver', 'sqlite');
    }

    /**
     * Prepare, execute, and return the statement in one call.
     *
     * @param array<int|string, mixed> $params
     */
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|null
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return list<array<string, mixed>>
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** @param array<int|string, mixed> $params */
    public static function fetchValue(string $sql, array $params = []): mixed
    {
        $value = self::run($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    public static function lastInsertId(): int
    {
        return (int) self::pdo()->lastInsertId();
    }

    /**
     * Run a closure inside a transaction, committing on success and
     * rolling back on any throwable. Nested calls reuse the outer
     * transaction; callers keep transactional units small.
     *
     * @template T
     * @param callable(): T $work
     * @return T
     */
    public static function transaction(callable $work): mixed
    {
        $pdo = self::pdo();
        if ($pdo->inTransaction()) {
            return $work();
        }
        $pdo->beginTransaction();
        try {
            $result = $work();
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
