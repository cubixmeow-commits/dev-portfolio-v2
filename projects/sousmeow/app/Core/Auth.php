<?php

declare(strict_types=1);

namespace SousMeow\Core;

use SousMeow\Models\User;

/**
 * Session-backed authentication. The user row is loaded once per request
 * and cached. Authorization checks (requireLogin, requireAdmin) redirect
 * or 403 server side; nothing relies on hidden UI.
 */
final class Auth
{
    /** @var array<string, mixed>|null|false false = not yet loaded */
    private static array|null|false $user = false;

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        if (self::$user === false) {
            $id = $_SESSION['user_id'] ?? null;
            self::$user = is_int($id) ? User::find($id) : null;
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

    /** @param array<string, mixed> $user */
    public static function login(array $user): void
    {
        Session::regenerate();
        unset($_SESSION['csrf_token']);
        $_SESSION['user_id'] = (int) $user['id'];
        self::$user = false;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$user = false;
    }

    /** Redirect guests to login, remembering where they were headed. */
    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? null;
        Flash::set('notice', 'Sign in to open your kitchen.');
        redirect('/login');
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            exit;
        }
    }
}
