<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\Config;
use Rally\Core\Csrf;
use Rally\Core\Flash;
use Rally\Core\RateLimiter;
use Rally\Core\View;
use Rally\Models\User;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('auth/login', [
            'title' => 'Sign in',
            'pageCss' => 'auth',
            'errors' => [],
            'email' => '',
        ]);
    }

    public function login(): void
    {
        $email = User::normalizeEmail((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (RateLimiter::tooMany('login', $ip, 20, 900) || RateLimiter::tooMany('login', $email, 10, 900)) {
            $errors['form'] = 'Too many sign-in attempts. Try again in a few minutes.';
            View::render('auth/login', [
                'title' => 'Sign in',
                'pageCss' => 'auth',
                'errors' => $errors,
                'email' => $email,
            ]);
            http_response_code(429);
            return;
        }

        $user = User::findByEmail($email);
        RateLimiter::hit('login', $ip);
        RateLimiter::hit('login', $email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            $errors['form'] = 'Email or password is incorrect.';
            View::render('auth/login', [
                'title' => 'Sign in',
                'pageCss' => 'auth',
                'errors' => $errors,
                'email' => $email,
            ]);
            http_response_code(422);
            return;
        }

        if (($user['status'] ?? 'active') !== 'active') {
            $errors['form'] = 'This account is not active.';
            View::render('auth/login', [
                'title' => 'Sign in',
                'pageCss' => 'auth',
                'errors' => $errors,
                'email' => $email,
            ]);
            http_response_code(403);
            return;
        }

        if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
            \Rally\Core\Database::run(
                'UPDATE rly_users SET password_hash = ?, updated_at = ? WHERE id = ?',
                [password_hash($password, PASSWORD_DEFAULT), \Rally\Services\Clock::nowUtcString(), (int) $user['id']]
            );
        }

        Auth::login($user);
        Flash::set('success', 'Welcome back, ' . $user['name'] . '.');
        Auth::redirectIntended('/dashboard');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('auth/register', [
            'title' => 'Create account',
            'pageCss' => 'auth',
            'errors' => [],
            'old' => [],
        ]);
    }

    public function register(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $username = strtolower(trim((string) ($_POST['username'] ?? '')));
        $email = User::normalizeEmail((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');
        $timezone = trim((string) ($_POST['timezone'] ?? 'UTC'));
        $errors = [];
        $old = compact('name', 'username', 'email', 'timezone');

        if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
            $errors['name'] = 'Enter a display name between 2 and 120 characters.';
        }
        if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
            $errors['username'] = 'Username must be 3–30 characters: lowercase letters, numbers, underscore.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (mb_strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }
        try {
            new \DateTimeZone($timezone);
        } catch (\Exception) {
            $errors['timezone'] = 'Choose a valid timezone.';
            $timezone = 'UTC';
        }
        if (User::findByEmail($email) !== null) {
            $errors['email'] = 'That email is already registered.';
        }
        if (User::findByUsername($username) !== null) {
            $errors['username'] = 'That username is taken.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/register', [
                'title' => 'Create account',
                'pageCss' => 'auth',
                'errors' => $errors,
                'old' => $old,
            ]);
            return;
        }

        $user = User::create($name, $username, $email, $password, $timezone);
        Auth::login($user);
        Flash::set('success', 'Account created. Time to start a series.');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        Flash::set('notice', 'Signed out.');
        redirect('/');
    }
}
