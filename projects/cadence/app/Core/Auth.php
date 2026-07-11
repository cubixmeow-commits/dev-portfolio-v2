<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Current-user resolution and login state transitions. The session holds
 * only the user id; the row is fetched once per request and cached here.
 */
final class Auth
{
    /** @var array<string, mixed>|null|false false = not yet resolved */
    private static array|null|false $user = false;

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        if (self::$user === false) {
            $id = $_SESSION['user_id'] ?? null;
            self::$user = is_int($id)
                ? Database::fetch('SELECT * FROM users WHERE id = ?', [$id])
                : null;
        }
        return self::$user;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user === null ? null : (int) $user['id'];
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    /** Log a user in: rotate the session id, then bind the user to it. */
    public static function login(int $userId): void
    {
        Session::regenerate();
        $_SESSION['user_id'] = $userId;
        self::$user = false;
        Database::run('UPDATE users SET last_active_at = NOW() WHERE id = ?', [$userId]);
    }

    /** Destroy the session server-side and clear the cookie. */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 86400,
                'path'     => $p['path'],
                'secure'   => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'],
            ]);
        }
        session_destroy();
        self::$user = false;
    }

    /** Require a signed-in user or redirect to login, remembering the target. */
    public static function requireUser(): array
    {
        $user = self::user();
        if ($user === null) {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? url('/');
            Flash::set('info', 'Sign in to continue.');
            redirect('/login');
        }
        return $user;
    }

    /** Require an admin or return 404 (admin existence is not advertised). */
    public static function requireAdmin(): array
    {
        $user = self::requireUser();
        if ($user['role'] !== 'admin') {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Page not found']);
            exit;
        }
        return $user;
    }

    /** Refresh the request-cached user row after a profile update. */
    public static function refresh(): void
    {
        self::$user = false;
    }
}
