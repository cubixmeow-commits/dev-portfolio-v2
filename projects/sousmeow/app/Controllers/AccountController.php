<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Flash;
use SousMeow\Core\RateLimiter;
use SousMeow\Core\Session;
use SousMeow\Core\View;
use SousMeow\Models\PasswordReset;
use SousMeow\Models\User;
use SousMeow\Services\AccountDataExport;
use SousMeow\Services\AccountMailer;
use SousMeow\Services\UserStats;

final class AccountController
{
    public function index(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $stats = UserStats::forUser((int) $user['id'], Auth::isSimulated());

        View::render('account/index', [
            'title'   => 'Account',
            'pageCss' => 'account',
            'user'    => $user,
            'stats'   => $stats,
            'section' => 'overview',
        ]);
    }

    public function profile(): void
    {
        Auth::requireLogin();
        View::render('account/profile', [
            'title'   => 'Profile',
            'pageCss' => 'account',
            'user'    => Auth::user(),
            'errors'  => [],
            'section' => 'profile',
        ]);
    }

    public function updateProfile(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) $user['id'];

        $name = trim((string) ($_POST['name'] ?? ''));
        $bio = trim((string) ($_POST['bio'] ?? ''));
        $website = trim((string) ($_POST['website'] ?? ''));
        $avatarUrl = trim((string) ($_POST['avatar_url'] ?? ''));

        $errors = [];
        if ($name === '' || mb_strlen($name) > 80) {
            $errors['name'] = 'Name is required (up to 80 characters).';
        }
        if (mb_strlen($bio) > 280) {
            $errors['bio'] = 'Bio can be up to 280 characters.';
        }
        if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Enter a valid http or https URL.';
        }
        if ($website !== '' && !preg_match('#^https?://#i', $website)) {
            $errors['website'] = 'Website must start with http:// or https://';
        }
        if ($avatarUrl !== '' && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            $errors['avatar_url'] = 'Enter a valid image URL or leave blank.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('account/profile', [
                'title'   => 'Profile',
                'pageCss' => 'account',
                'user'    => array_merge($user, compact('name', 'bio', 'website', 'avatar_url')),
                'errors'  => $errors,
                'section' => 'profile',
            ]);
            return;
        }

        User::updateProfile($userId, [
            'name'       => $name,
            'bio'        => $bio !== '' ? $bio : null,
            'website'    => $website !== '' ? $website : null,
            'avatar_url' => $avatarUrl !== '' ? $avatarUrl : null,
        ]);
        Auth::refresh();
        Flash::set('success', 'Profile updated.');
        redirect('/account/profile');
    }

    public function preferences(): void
    {
        Auth::requireLogin();
        View::render('account/preferences', [
            'title'     => 'Preferences',
            'pageCss'   => 'account',
            'user'      => Auth::user(),
            'errors'    => [],
            'timezones' => self::timezones(),
            'section'   => 'preferences',
        ]);
    }

    public function updatePreferences(): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();

        $preferredAi = (string) ($_POST['preferred_ai'] ?? '');
        $experience = (string) ($_POST['ai_experience_level'] ?? '');
        $timezone = (string) ($_POST['timezone'] ?? '');
        $theme = (string) ($_POST['theme_preference'] ?? 'system');

        $errors = [];
        if (!in_array($preferredAi, User::PREFERRED_AI_OPTIONS, true)) {
            $preferredAi = 'No preference';
        }
        if ($experience !== '' && !in_array($experience, User::EXPERIENCE_LEVELS, true)) {
            $errors['ai_experience_level'] = 'Choose a valid experience level.';
        }
        if ($timezone !== '' && !in_array($timezone, self::timezones(), true)) {
            $errors['timezone'] = 'Choose a valid timezone.';
        }
        if (!in_array($theme, User::THEME_OPTIONS, true)) {
            $theme = 'system';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('account/preferences', [
                'title'     => 'Preferences',
                'pageCss'   => 'account',
                'user'      => Auth::user(),
                'errors'    => $errors,
                'timezones' => self::timezones(),
                'section'   => 'preferences',
            ]);
            return;
        }

        User::updatePreferences($userId, [
            'preferred_ai'        => $preferredAi,
            'ai_experience_level' => $experience !== '' ? $experience : null,
            'timezone'            => $timezone !== '' ? $timezone : null,
            'theme_preference'    => $theme,
        ]);
        Auth::refresh();
        Flash::set('success', 'Preferences saved.');
        redirect('/account/preferences');
    }

    public function security(): void
    {
        Auth::requireLogin();
        View::render('account/security', [
            'title'   => 'Security',
            'pageCss' => 'account',
            'user'    => Auth::user(),
            'errors'  => [],
            'section' => 'security',
        ]);
    }

    public function updatePassword(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) $user['id'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        $errors = [];
        if (RateLimiter::tooMany('password-change', (string) $userId, 5, 900)) {
            $errors['current_password'] = 'Too many attempts. Wait a few minutes.';
        }
        if (!password_verify($current, (string) $user['password_hash'])) {
            $errors['current_password'] = 'Your current password does not match.';
        }
        if (mb_strlen($new) < 8) {
            $errors['new_password'] = 'Use at least 8 characters.';
        }
        if ($new !== $confirm) {
            $errors['new_password_confirm'] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('account/security', [
                'title'   => 'Security',
                'pageCss' => 'account',
                'user'    => $user,
                'errors'  => $errors,
                'section' => 'security',
            ]);
            return;
        }

        RateLimiter::hit('password-change', (string) $userId);
        User::updatePassword($userId, $new);
        PasswordReset::invalidateForUser($userId);
        Session::regenerate();
        Auth::refresh();

        if (!Auth::isSimulated()) {
            AccountMailer::sendPasswordChanged($userId, (string) $user['email']);
        }

        Flash::set('success', 'Password updated.');
        redirect('/account/security');
    }

    public function requestEmailChange(): void
    {
        Auth::requireVerified();
        $user = Auth::user();
        if (!Auth::isVerified()) {
            redirect('/verify-email/pending');
        }

        $newEmail = User::normalizeEmail((string) ($_POST['new_email'] ?? ''));
        $password = (string) ($_POST['current_password'] ?? '');

        $errors = [];
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['new_email'] = 'Enter a valid email address.';
        }
        if ($newEmail === $user['email']) {
            $errors['new_email'] = 'That is already your email address.';
        }
        if ($errors === [] && User::findByEmail($newEmail) !== null) {
            $errors['new_email'] = 'That email is already in use.';
        }
        if (!password_verify($password, (string) $user['password_hash'])) {
            $errors['current_password'] = 'Your current password does not match.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('account/security', [
                'title'   => 'Security',
                'pageCss' => 'account',
                'user'    => $user,
                'errors'  => $errors,
                'section' => 'security',
            ]);
            return;
        }

        $userId = (int) $user['id'];
        $token = User::issuePendingEmailChange($userId, $newEmail);
        AccountMailer::sendEmailChangeConfirmation($userId, $newEmail, $token);
        AccountMailer::sendEmailChangeNotice($userId, (string) $user['email'], $newEmail);

        Flash::set('success', 'Confirmation sent to your new email address. Your current email stays active until you confirm.');
        redirect('/account/security');
    }

    public function confirmEmailChange(string $token): void
    {
        $user = User::findByPendingEmailToken($token);
        if ($user === null) {
            Flash::set('error', 'This confirmation link is invalid or has expired.');
            redirect('/account/security');
        }

        User::confirmPendingEmail((int) $user['id']);
        if (Auth::check() && Auth::id() === (int) $user['id']) {
            Auth::refresh();
        }

        Flash::set('success', 'Email address updated.');
        redirect('/account/security');
    }

    public function data(): void
    {
        Auth::requireRealUser();
        View::render('account/data', [
            'title'   => 'Your data',
            'pageCss' => 'account',
            'user'    => Auth::user(),
            'section' => 'data',
        ]);
    }

    public function exportData(): void
    {
        Auth::requireRealUser();
        $userId = (int) Auth::id();
        $path = AccountDataExport::createZip($userId);

        if ($path === null || !is_file($path)) {
            Flash::set('error', 'Export failed. Try again later.');
            redirect('/account/data');
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="sousmeow-data-export.zip"');
        header('Content-Length: ' . (string) filesize($path));
        readfile($path);
        @unlink($path);
        exit;
    }

    public function deleteAccount(): void
    {
        Auth::requireRealUser();
        $user = Auth::user();

        if (($user['role'] ?? 'user') === 'admin') {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            exit;
        }

        $password = (string) ($_POST['password'] ?? '');
        $confirm = trim((string) ($_POST['confirm_phrase'] ?? ''));

        $errors = [];
        if (!password_verify($password, (string) $user['password_hash'])) {
            $errors['password'] = 'Your password does not match.';
        }
        if ($confirm !== 'DELETE MY ACCOUNT') {
            $errors['confirm_phrase'] = 'Type DELETE MY ACCOUNT exactly to confirm.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('account/data', [
                'title'   => 'Your data',
                'pageCss' => 'account',
                'user'    => $user,
                'errors'  => $errors,
                'section' => 'data',
            ]);
            return;
        }

        $userId = (int) $user['id'];
        PasswordReset::invalidateForUser($userId);
        User::deleteAccount($userId);
        Auth::logout();

        Flash::set('success', 'Your account has been deleted.');
        redirect('/');
    }

    public function showOnboarding(): void
    {
        Auth::requireLogin();
        if (!Auth::isVerified()) {
            redirect('/verify-email/pending');
        }
        $user = Auth::user();
        if ($user['onboarding_completed_at'] !== null) {
            redirect('/kitchen');
        }

        View::render('account/onboarding', [
            'title'     => 'Welcome to SousMeow',
            'pageCss'   => 'account',
            'user'      => $user,
            'timezones' => self::timezones(),
            'errors'    => [],
        ]);
    }

    public function completeOnboarding(): void
    {
        Auth::requireLogin();
        if (!Auth::isVerified()) {
            redirect('/verify-email/pending');
        }

        $userId = (int) Auth::id();
        $skip = isset($_POST['skip']);

        if (!$skip) {
            $preferredAi = (string) ($_POST['preferred_ai'] ?? '');
            $experience = (string) ($_POST['ai_experience_level'] ?? '');
            $timezone = (string) ($_POST['timezone'] ?? '');

            if (in_array($preferredAi, User::PREFERRED_AI_OPTIONS, true)) {
                User::updatePreferences($userId, ['preferred_ai' => $preferredAi]);
            }
            if (in_array($experience, User::EXPERIENCE_LEVELS, true)) {
                User::updatePreferences($userId, ['ai_experience_level' => $experience]);
            }
            if ($timezone !== '' && in_array($timezone, self::timezones(), true)) {
                User::updatePreferences($userId, ['timezone' => $timezone]);
            }
        }

        User::completeOnboarding($userId);
        Auth::refresh();

        if (isset($_POST['explore'])) {
            Flash::set('success', 'Welcome aboard. Pick a workflow to start.');
            redirect('/marketplace');
        }

        Flash::set('success', 'You are ready to go.');
        Auth::redirectIntended('/kitchen');
    }

    /** @return list<string> */
    private static function timezones(): array
    {
        return \DateTimeZone::listIdentifiers();
    }
}
