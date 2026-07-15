<?php

declare(strict_types=1);

namespace Rally\Core;

/**
 * Native PHP session with hardened cookie settings and idle expiry.
 * Login and logout rotate the session id.
 */
final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name(Config::string('session.cookie_name', 'rally_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => Config::bool('session.secure'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        $now = time();
        $idleTtl = Config::int('session.idle_ttl', 1209600);
        $lastSeen = $_SESSION['_last_seen'] ?? null;
        if (is_int($lastSeen) && ($now - $lastSeen) > $idleTtl) {
            self::destroy();
            session_start();
        }
        $_SESSION['_last_seen'] = $now;
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'secure'   => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
