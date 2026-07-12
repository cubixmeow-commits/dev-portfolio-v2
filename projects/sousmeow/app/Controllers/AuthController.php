<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Flash;
use SousMeow\Core\RateLimiter;
use SousMeow\Core\View;
use SousMeow\Models\User;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }
        View::render('auth/login', [
            'title'    => 'Sign in',
            'pageCss'  => 'auth',
            'errors'   => [],
            'old'      => ['email' => ''],
        ]);
    }

    public function login(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter the email address you signed up with.';
        }
        if ($password === '') {
            $errors['password'] = 'Enter your password.';
        }

        if ($errors === []) {
            if (RateLimiter::tooMany('login', $ip, 20, 900) || RateLimiter::tooMany('login', $email, 8, 900)) {
                $errors['email'] = 'Too many attempts. Wait a few minutes and try again.';
            } else {
                RateLimiter::hit('login', $ip);
                RateLimiter::hit('login', $email);
                $user = User::findByEmail($email);
                if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
                    $errors['email'] = 'That email and password do not match an account.';
                }
            }
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/login', [
                'title'   => 'Sign in',
                'pageCss' => 'auth',
                'errors'  => $errors,
                'old'     => ['email' => $email],
            ]);
            return;
        }

        /** @var array<string, mixed> $user */
        Auth::login($user);
        if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
            User::updatePassword((int) $user['id'], $password);
        }

        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);
        Flash::set('success', 'Welcome back to your kitchen.');
        // Only follow same-app relative paths, never absolute URLs.
        if (is_string($intended) && str_starts_with($intended, '/') && !str_starts_with($intended, '//')) {
            header('Location: ' . $intended, true, 303);
            exit;
        }
        redirect('/kitchen');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }
        View::render('auth/register', [
            'title'   => 'Create your account',
            'pageCss' => 'auth',
            'errors'  => [],
            'old'     => ['name' => '', 'email' => ''],
        ]);
    }

    public function register(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $errors = [];
        if ($name === '' || mb_strlen($name) > 80) {
            $errors['name'] = 'Tell us what to call you (up to 80 characters).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (mb_strlen($password) < 8) {
            $errors['password'] = 'Use at least 8 characters. A short sentence works well.';
        }
        if ($errors === [] && RateLimiter::tooMany('register', $ip, 10, 3600)) {
            $errors['email'] = 'Too many new accounts from this connection. Try again later.';
        }
        if ($errors === [] && User::findByEmail($email) !== null) {
            $errors['email'] = 'An account with this email already exists. Try signing in instead.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/register', [
                'title'   => 'Create your account',
                'pageCss' => 'auth',
                'errors'  => $errors,
                'old'     => ['name' => $name, 'email' => $email],
            ]);
            return;
        }

        RateLimiter::hit('register', $ip);
        $id = User::create($name, $email, $password);
        $user = User::find($id);
        if ($user !== null) {
            Auth::login($user);
        }
        Flash::set('success', 'Your kitchen is ready. Start your first Cookbook whenever you like.');
        redirect('/kitchen');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    public function showChangePassword(): void
    {
        Auth::requireLogin();
        View::render('auth/password', [
            'title'   => 'Change password',
            'pageCss' => 'auth',
            'errors'  => [],
        ]);
    }

    public function changePassword(): void
    {
        Auth::requireLogin();
        $user = Auth::user();

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');

        $errors = [];
        if (!password_verify($current, (string) $user['password_hash'])) {
            $errors['current_password'] = 'Your current password does not match.';
        }
        if (mb_strlen($new) < 8) {
            $errors['new_password'] = 'Use at least 8 characters. A short sentence works well.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/password', [
                'title'   => 'Change password',
                'pageCss' => 'auth',
                'errors'  => $errors,
            ]);
            return;
        }

        User::updatePassword((int) $user['id'], $new);
        Flash::set('success', 'Password updated.');
        redirect('/kitchen');
    }
}
