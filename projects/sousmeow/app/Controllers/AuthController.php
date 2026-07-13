<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Flash;
use SousMeow\Core\RateLimiter;
use SousMeow\Core\View;
use SousMeow\Models\PasswordReset;
use SousMeow\Models\User;
use SousMeow\Services\AccountMailer;

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

        $email = User::normalizeEmail((string) ($_POST['email'] ?? ''));
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

        Flash::set('success', 'Welcome back.');
        Auth::redirectIntended('/kitchen');
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
        $email = User::normalizeEmail((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
        $terms = isset($_POST['terms']);
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
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }
        if (!$terms) {
            $errors['terms'] = 'Please accept the Terms and Privacy Policy.';
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
        if ($user === null) {
            Flash::set('error', 'Something went wrong. Please try again.');
            redirect('/register');
        }

        Auth::login($user);
        $token = User::issueVerificationToken($id);
        $sent = AccountMailer::sendVerification($id, $email, $name, $token);

        if ($sent) {
            Flash::set('success', 'Account created. Check your email to verify your address.');
        } else {
            Flash::set('notice', 'Account created, but we could not send the verification email. You can resend it from the next screen.');
            error_log('SousMeow: verification email failed for user ' . $id);
        }

        redirect('/verify-email/pending');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    public function showChangePassword(): void
    {
        Auth::requireLogin();
        redirect('/account/security');
    }

    public function changePassword(): void
    {
        Auth::requireLogin();
        redirect('/account/security');
    }

    public function showVerifyPending(): void
    {
        Auth::requireLogin();
        if (Auth::isVerified()) {
            Auth::redirectIntended('/kitchen');
        }
        $user = Auth::user();
        View::render('auth/verify-pending', [
            'title'   => 'Verify your email',
            'pageCss' => 'auth',
            'user'    => $user,
            'emailFailed' => isset($_GET['delivery']) && $_GET['delivery'] === 'failed',
        ]);
    }

    public function verifyEmail(string $token): void
    {
        $user = User::findByVerificationToken($token);
        if ($user === null) {
            Flash::set('error', 'This verification link is invalid or has expired.');
            redirect(Auth::check() ? '/verify-email/pending' : '/login');
        }

        User::markEmailVerified((int) $user['id']);
        if (Auth::check() && Auth::id() === (int) $user['id']) {
            Auth::refresh();
        }

        Flash::set('success', 'Email verified. You are all set.');

        if ($user['onboarding_completed_at'] === null && (int) ($user['simulation'] ?? 0) === 0) {
            redirect('/onboarding');
        }

        if (Auth::check()) {
            Auth::redirectIntended('/kitchen');
        }
        redirect('/login');
    }

    public function resendVerification(): void
    {
        Auth::requireLogin();
        if (Auth::isVerified()) {
            Flash::set('notice', 'Your email is already verified.');
            redirect('/kitchen');
        }

        $user = Auth::user();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userId = (int) $user['id'];

        if (RateLimiter::tooMany('verify-resend', (string) $userId, 3, 900)
            || RateLimiter::tooMany('verify-resend', $ip, 5, 900)) {
            Flash::set('error', 'Too many resend attempts. Wait a few minutes and try again.');
            redirect('/verify-email/pending');
        }

        RateLimiter::hit('verify-resend', (string) $userId);
        RateLimiter::hit('verify-resend', $ip);

        $token = User::issueVerificationToken($userId);
        $sent = AccountMailer::sendVerification($userId, (string) $user['email'], (string) $user['name'], $token);

        if ($sent) {
            Flash::set('success', 'Verification email sent. Check your inbox.');
        } else {
            Flash::set('error', 'We could not send the email right now. Try again in a few minutes.');
            error_log('SousMeow: verification resend failed for user ' . $userId);
            redirect('/verify-email/pending?delivery=failed');
        }

        redirect('/verify-email/pending');
    }

    public function showForgotPassword(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }
        View::render('auth/forgot', [
            'title'   => 'Forgot password',
            'pageCss' => 'auth',
            'errors'  => [],
            'old'     => ['email' => ''],
            'sent'    => false,
        ]);
    }

    public function forgotPassword(): void
    {
        if (Auth::check()) {
            redirect('/kitchen');
        }

        $email = User::normalizeEmail((string) ($_POST['email'] ?? ''));
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($errors === []) {
            if (RateLimiter::tooMany('forgot-password', $ip, 10, 3600)
                || RateLimiter::tooMany('forgot-password', $email, 5, 3600)) {
                $errors['email'] = 'Too many requests. Try again later.';
            }
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/forgot', [
                'title'   => 'Forgot password',
                'pageCss' => 'auth',
                'errors'  => $errors,
                'old'     => ['email' => $email],
                'sent'    => false,
            ]);
            return;
        }

        RateLimiter::hit('forgot-password', $ip);
        RateLimiter::hit('forgot-password', $email);

        $user = User::findByEmail($email);
        if ($user !== null
            && (int) ($user['simulation'] ?? 0) === 0
            && ($user['role'] ?? 'user') !== 'admin') {
            $token = PasswordReset::issue((int) $user['id']);
            AccountMailer::sendPasswordReset((int) $user['id'], $email, $token);
        }

        View::render('auth/forgot', [
            'title'   => 'Forgot password',
            'pageCss' => 'auth',
            'errors'  => [],
            'old'     => ['email' => ''],
            'sent'    => true,
        ]);
    }

    public function showResetPassword(string $token): void
    {
        if (PasswordReset::findValid($token) === null) {
            Flash::set('error', 'This reset link is invalid or has expired.');
            redirect('/forgot-password');
        }
        View::render('auth/reset', [
            'title'   => 'Reset password',
            'pageCss' => 'auth',
            'token'   => $token,
            'errors'  => [],
        ]);
    }

    public function resetPassword(string $token): void
    {
        $row = PasswordReset::findValid($token);
        if ($row === null) {
            Flash::set('error', 'This reset link is invalid or has expired.');
            redirect('/forgot-password');
        }

        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $errors = [];
        if (mb_strlen($password) < 8) {
            $errors['password'] = 'Use at least 8 characters.';
        }
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/reset', [
                'title'   => 'Reset password',
                'pageCss' => 'auth',
                'token'   => $token,
                'errors'  => $errors,
            ]);
            return;
        }

        $userId = PasswordReset::consume($token);
        if ($userId === null) {
            Flash::set('error', 'This reset link has already been used or expired.');
            redirect('/forgot-password');
        }

        User::updatePassword($userId, $password);
        PasswordReset::invalidateForUser($userId);

        $user = User::find($userId);
        if ($user !== null) {
            AccountMailer::sendPasswordChanged($userId, (string) $user['email']);
        }

        if (Auth::check() && Auth::id() === $userId) {
            Auth::logout();
        }

        Flash::set('success', 'Password updated. Sign in with your new password.');
        redirect('/login');
    }
}
