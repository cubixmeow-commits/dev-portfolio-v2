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

    public static function isSimulated(): bool
    {
        $user = self::user();
        return $user !== null && (int) ($user['simulation'] ?? 0) === 1;
    }

    public static function isVerified(): bool
    {
        $user = self::user();
        if ($user === null) {
            return false;
        }
        if (self::isAdmin() || self::isSimulated()) {
            return true;
        }
        return $user['email_verified_at'] !== null && $user['email_verified_at'] !== '';
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

    public static function refresh(): void
    {
        self::$user = false;
    }

    /** Redirect guests to login, remembering where they were headed. */
    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? null;
        Flash::set('notice', 'Sign in to access your projects.');
        redirect('/login');
    }

    /**
     * Block unverified real users from privileged actions. Admins and
     * simulated demo accounts bypass this check.
     */
    public static function requireVerified(): void
    {
        self::requireLogin();
        if (self::isVerified()) {
            return;
        }
        $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? null;
        Flash::set('notice', 'Verify your email to use this feature.');
        redirect('/verify-email/pending');
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

    /** Real users only — simulated and admin accounts are excluded. */
    public static function requireRealUser(): void
    {
        self::requireLogin();
        $user = self::user();
        if ($user === null || !User::isRealUser($user)) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            exit;
        }
    }

    public static function redirectIntended(string $fallback = '/kitchen'): never
    {
        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);
        if (is_string($intended) && str_starts_with($intended, '/') && !str_starts_with($intended, '//')) {
            header('Location: ' . $intended, true, 303);
            exit;
        }
        redirect($fallback);
    }
}
