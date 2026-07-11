<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Flash;
use Cadence\Core\View;
use Cadence\Models\User;

final class ProfileController
{
    /* ---- Public profile ---- */

    public function show(string $handle): void
    {
        $handle = mb_strtolower($handle);
        $user = User::findByHandle($handle);

        if ($user === null || str_starts_with((string) $user['handle'], 'deleted_')) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Profile not found']);
            return;
        }

        $userId = (int) $user['id'];
        $stats = \Cadence\Core\Database::fetch(
            'SELECT COALESCE(MAX(longest_streak), 0) AS longest_streak,
                    COALESCE(MAX(current_streak), 0) AS best_current_streak,
                    COUNT(*) AS challenges_joined
             FROM challenge_participants WHERE user_id = ?',
            [$userId]
        ) ?? ['longest_streak' => 0, 'best_current_streak' => 0, 'challenges_joined' => 0];
        $totalCheckins = (int) \Cadence\Core\Database::fetchValue(
            'SELECT COUNT(*) FROM check_ins WHERE user_id = ?',
            [$userId]
        );

        View::render('profile/show', [
            'title'         => (string) $user['display_name'],
            'profile'       => $user,
            'page_css'      => 'community',
            'ring'          => \Cadence\Models\Participation::ringMap([$userId])[$userId] ?? null,
            'stats'         => $stats,
            'totalCheckins' => $totalCheckins,
            'badges'        => \Cadence\Models\Badge::forUser($userId),
            'active'        => '',
            'activeChallenges' => \Cadence\Models\Participation::activeForUser($userId),
            'events'        => \Cadence\Models\ActivityEvent::forUser($userId, 15),
            'isSelf'        => Auth::id() === $userId,
        ]);
    }

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
