<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Flash;
use Cadence\Core\View;
use Cadence\Models\User;

final class ProfileController
{
    /* ---- Account settings ---- */

    public function settings(): void
    {
        $user = Auth::requireUser();
        View::render('profile/settings', [
            'title'     => 'Settings',
            'user'      => $user,
            'timezones' => \DateTimeZone::listIdentifiers() ?: ['UTC'],
        ]);
    }

    public function updateProfile(): void
    {
        $user = Auth::requireUser();

        $displayName = trim((string) ($_POST['display_name'] ?? ''));
        $bio         = (string) ($_POST['bio'] ?? '');
        $timezone    = (string) ($_POST['timezone'] ?? $user['timezone']);

        if (mb_strlen($displayName) < 2 || mb_strlen($displayName) > 60) {
            Flash::set('error', 'Display name needs 2 to 60 characters.');
            redirect('/settings');
        }
        if (mb_strlen($bio) > 160) {
            Flash::set('error', 'Bios fit in 160 characters.');
            redirect('/settings');
        }
        if (!in_array($timezone, \DateTimeZone::listIdentifiers() ?: [], true)) {
            $timezone = (string) $user['timezone'];
        }

        User::updateProfile((int) $user['id'], $displayName, $bio, $timezone);
        Auth::refresh();
        Flash::set('success', 'Profile saved.');
        redirect('/settings');
    }

    public function updatePassword(): void
    {
        $user = Auth::requireUser();

        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');

        if (!password_verify($current, (string) $user['password_hash'])) {
            Flash::set('error', 'Your current password did not match.');
            redirect('/settings');
        }
        if (($error = AuthController::passwordError($new)) !== null) {
            Flash::set('error', $error);
            redirect('/settings');
        }

        User::updatePassword((int) $user['id'], $new);
        Flash::set('success', 'Password changed.');
        redirect('/settings');
    }

    public function deleteAccount(): void
    {
        $user = Auth::requireUser();

        if (!password_verify((string) ($_POST['password'] ?? ''), (string) $user['password_hash'])) {
            Flash::set('error', 'Enter your password to confirm deletion.');
            redirect('/settings');
        }

        User::softDelete((int) $user['id']);
        Auth::logout();
        Flash::set('success', 'Your account is deleted. Thanks for trying Cadence.');
        redirect('/');
    }
}
