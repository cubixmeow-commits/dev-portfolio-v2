<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Database-backed session handler. PHP's session machinery drives the
 * lifecycle; this class stores the payload in the sessions table so
 * sessions survive across web heads, are inspectable, and can be revoked
 * server-side (logout deletes the row).
 *
 * Idle expiry: rows older than session.idle_ttl (default 30 days) are
 * treated as missing on read and purged by gc.
 */
final class Session implements \SessionHandlerInterface
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.sid_length', '64');
        ini_set('session.sid_bits_per_character', '5');
        ini_set('session.use_strict_mode', '1');

        session_set_save_handler(new self(), true);
        session_name(Config::string('session.cookie_name', 'cadence_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => (Config::string('app.base_path') ?: '') . '/',
            'secure'   => Config::bool('session.secure'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    /** Rotate the session id, keeping data. Called on login and privilege change. */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $ttl = Config::int('session.idle_ttl', 2592000);
        $row = Database::fetch(
            'SELECT data FROM sessions WHERE id = ? AND last_seen_at > DATE_SUB(NOW(), INTERVAL ? SECOND)',
            [$id, $ttl]
        );
        return $row === null ? '' : (string) ($row['data'] ?? '');
    }

    public function write(string $id, string $data): bool
    {
        $userId = null;
        // The serialized payload carries user_id; mirror it into its own
        // column so admin tooling and revocation can query by user.
        if (preg_match('/user_id\|i:(\d+);/', $data, $m)) {
            $userId = (int) $m[1];
        }
        Database::run(
            'INSERT INTO sessions (id, user_id, ip, user_agent, data, last_seen_at)
             VALUES (?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), data = VALUES(data), last_seen_at = NOW()',
            [
                $id,
                $userId,
                substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                $data,
            ]
        );
        return true;
    }

    public function destroy(string $id): bool
    {
        Database::run('DELETE FROM sessions WHERE id = ?', [$id]);
        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $ttl = Config::int('session.idle_ttl', 2592000);
        return Database::run(
            'DELETE FROM sessions WHERE last_seen_at < DATE_SUB(NOW(), INTERVAL ? SECOND)',
            [$ttl]
        )->rowCount();
    }
}
