<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Config;
use Cadence\Core\Database;
use Cadence\Core\Flash;
use Cadence\Core\Mailer;
use Cadence\Core\RateLimiter;
use Cadence\Core\View;
use Cadence\Models\PasswordReset;
use Cadence\Models\User;

final class AuthController
{
    /* ---- Registration ---- */

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('auth/register', [
            'page_css' => 'auth',
            'title'     => 'Create your account',
            'errors'    => [],
            'old'       => [],
            'timezones' => self::timezones(),
        ]);
    }

    public function register(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        RateLimiter::guard('register', 5, 900, '/register');

        $email       = trim((string) ($_POST['email'] ?? ''));
        $displayName = trim((string) ($_POST['display_name'] ?? ''));
        $handle      = mb_strtolower(trim((string) ($_POST['handle'] ?? '')));
        $password    = (string) ($_POST['password'] ?? '');
        $timezone    = (string) ($_POST['timezone'] ?? 'America/Los_Angeles');

        $errors = [];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (User::emailTaken($email)) {
            $errors['email'] = 'That email already has an account. Try signing in.';
        }

        if (mb_strlen($displayName) < 2 || mb_strlen($displayName) > 60) {
            $errors['display_name'] = 'Display name needs 2 to 60 characters.';
        }

        if (!preg_match('/^[a-z0-9_]{3,30}$/', $handle)) {
            $errors['handle'] = 'Handles are 3 to 30 characters: lowercase letters, numbers, underscores.';
        } elseif (User::handleTaken($handle)) {
            $errors['handle'] = 'That handle is taken. Try another.';
        }

        if (($passwordError = self::passwordError($password)) !== null) {
            $errors['password'] = $passwordError;
        }

        if (!in_array($timezone, self::timezones(), true)) {
            $timezone = 'America/Los_Angeles';
        }

        if ($errors !== []) {
            View::render('auth/register', [
                'page_css' => 'auth',
                'title'     => 'Create your account',
                'errors'    => $errors,
                'old'       => ['email' => $email, 'display_name' => $displayName, 'handle' => $handle, 'timezone' => $timezone],
                'timezones' => self::timezones(),
            ]);
            return;
        }

        $userId = User::create($email, $password, $displayName, $handle, $timezone);
        $this->sendVerification($userId, $email, $displayName);

        Auth::login($userId);
        Flash::set('success', 'Welcome to Cadence. We sent a verification link to your email.');
        redirect('/dashboard');
    }

    /** JSON endpoint behind the register form's handle field. */
    public function checkHandle(): void
    {
        $handle = mb_strtolower(trim((string) ($_GET['handle'] ?? '')));
        if (!preg_match('/^[a-z0-9_]{3,30}$/', $handle)) {
            json_response(['available' => false, 'reason' => 'invalid']);
        }
        json_response(['available' => !User::handleTaken($handle)]);
    }

    /* ---- Email verification ---- */

    public function verify(string $token): void
    {
        $userId = User::consumeEmailVerification($token);
        if ($userId === null) {
            Flash::set('error', 'That verification link is expired or already used. Request a new one from your settings.');
            redirect(Auth::check() ? '/dashboard' : '/login');
        }
        User::markEmailVerified($userId);
        Auth::refresh();
        Flash::set('success', 'Email verified. You are all set.');
        redirect(Auth::check() ? '/dashboard' : '/login');
    }

    public function resendVerification(): void
    {
        $user = Auth::requireUser();
        RateLimiter::guard('verify-resend', 3, 900, '/dashboard');
        if ($user['email_verified_at'] === null) {
            $this->sendVerification((int) $user['id'], (string) $user['email'], (string) $user['display_name']);
        }
        Flash::set('success', 'Verification email sent. Check your inbox.');
        redirect('/dashboard');
    }

    /* ---- Login and logout ---- */

    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('auth/login', ['page_css' => 'auth', 'title' => 'Sign in', 'old' => []]);
    }

    public function login(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        RateLimiter::guard('login', 5, 900, '/login');

        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        // Second limiter keyed by the account so a distributed attack on
        // one mailbox is still throttled.
        if ($email !== '' && !RateLimiter::hit('login-account', 'email:' . mb_strtolower($email), 10, 900)) {
            Flash::set('error', 'Too many attempts. Wait a few minutes and try again.');
            redirect('/login');
        }

        $user = $email !== '' ? User::findByEmail($email) : null;

        // One generic failure path: no user enumeration via message or
        // timing shortcut.
        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            if ($user === null) {
                // Burn comparable time when the account does not exist.
                password_verify($password, '$2y$12$C6UzMDM.H6dfunLW6nkQdOMYS4b3f9tYjKn8bqRdOP1B3S1Qxhy2a');
            }
            View::render('auth/login', [
                'page_css' => 'auth',
                'title' => 'Sign in',
                'error' => 'Email or password is incorrect.',
                'old'   => ['email' => $email],
            ]);
            return;
        }

        if (str_starts_with((string) $user['handle'], 'deleted_')) {
            View::render('auth/login', [
                'page_css' => 'auth',
                'title' => 'Sign in',
                'error' => 'Email or password is incorrect.',
                'old'   => ['email' => $email],
            ]);
            return;
        }

        Auth::login((int) $user['id']);
        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);
        header('Location: ' . (is_string($intended) && str_starts_with($intended, '/') ? $intended : url('/dashboard')), true, 303);
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    /* ---- Password reset ---- */

    public function showForgot(): void
    {
        View::render('auth/forgot', ['page_css' => 'auth', 'title' => 'Reset your password']);
    }

    public function sendReset(): void
    {
        RateLimiter::guard('password-reset', 5, 900, '/forgot-password');

        $email = trim((string) ($_POST['email'] ?? ''));
        $user = $email !== '' ? User::findByEmail($email) : null;

        if ($user !== null && !str_starts_with((string) $user['handle'], 'deleted_')) {
            $token = PasswordReset::issue((int) $user['id']);
            $link = Config::string('app.base_url') . url('/reset-password/' . $token);
            Mailer::send(
                (string) $user['email'],
                'Reset your Cadence password',
                "Hi {$user['display_name']},\n\n"
                . "Someone asked to reset the password for this account. If it was you, open the link below within 30 minutes:\n\n"
                . $link . "\n\n"
                . "If it was not you, ignore this email. Your password is unchanged.\n\n"
                . "Cadence"
            );
        }

        // Same response either way: the form never confirms whether an
        // email is registered.
        Flash::set('success', 'If that email has an account, a reset link is on its way.');
        redirect('/forgot-password');
    }

    public function showReset(string $token): void
    {
        if (PasswordReset::peek($token) === null) {
            Flash::set('error', 'That reset link is expired or already used. Request a new one.');
            redirect('/forgot-password');
        }
        View::render('auth/reset', ['page_css' => 'auth', 'title' => 'Choose a new password', 'token' => $token]);
    }

    public function reset(string $token): void
    {
        RateLimiter::guard('password-reset-submit', 5, 900, '/forgot-password');

        $password = (string) ($_POST['password'] ?? '');
        if (($passwordError = self::passwordError($password)) !== null) {
            View::render('auth/reset', [
                'page_css' => 'auth',
                'title' => 'Choose a new password',
                'token' => $token,
                'error' => $passwordError,
            ]);
            return;
        }

        $userId = PasswordReset::consume($token);
        if ($userId === null) {
            Flash::set('error', 'That reset link is expired or already used. Request a new one.');
            redirect('/forgot-password');
        }

        User::updatePassword($userId, $password);
        // New password revokes every existing session for the account.
        Database::run('DELETE FROM sessions WHERE user_id = ?', [$userId]);

        Flash::set('success', 'Password updated. Sign in with the new one.');
        redirect('/login');
    }

    /* ---- Shared helpers ---- */

    private function sendVerification(int $userId, string $email, string $displayName): void
    {
        $token = User::createEmailVerification($userId);
        $link = Config::string('app.base_url') . url('/verify/' . $token);
        Mailer::send(
            $email,
            'Verify your Cadence email',
            "Hi {$displayName},\n\n"
            . "Confirm this email address to finish setting up your Cadence account:\n\n"
            . $link . "\n\n"
            . "The link works for 24 hours.\n\n"
            . "Cadence"
        );
    }

    /** Returns a user-facing error string, or null when the password passes. */
    public static function passwordError(string $password): ?string
    {
        $min = Config::int('security.min_password_length', 10);
        if (mb_strlen($password) < $min) {
            return "Passwords need at least {$min} characters.";
        }
        $common = require __DIR__ . '/../Data/common_passwords.php';
        if (in_array(mb_strtolower($password), $common, true)) {
            return 'That password is too common. Pick something more unusual.';
        }
        return null;
    }

    /** @return list<string> */
    private static function timezones(): array
    {
        return \DateTimeZone::listIdentifiers() ?: ['UTC'];
    }
}
