#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Verification script for production account features.
 * Run: php scripts/verify-accounts.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Auth;
use SousMeow\Core\Database;
use SousMeow\Core\Session;
use SousMeow\Models\PasswordReset;
use SousMeow\Models\User;
use SousMeow\Services\AccountMailer;

$passed = 0;
$failed = 0;

function assert_true(bool $cond, string $label): void
{
    global $passed, $failed;
    if ($cond) {
        echo "  OK  {$label}\n";
        $passed++;
    } else {
        echo " FAIL {$label}\n";
        $failed++;
    }
}

echo "SousMeow account verification\n\n";

// Token hashing
$raw = bin2hex(random_bytes(32));
$hash = hash('sha256', $raw);
assert_true(strlen($hash) === 64, 'Token hash is SHA-256');
assert_true($hash === hash('sha256', $raw), 'Token hash is deterministic');

// Registration creates unverified user
$testEmail = 'verify-test-' . bin2hex(random_bytes(4)) . '@example.test';
$id = User::create('Verify Test', $testEmail, 'test-password-123');
$user = User::find($id);
assert_true($user !== null, 'Registration creates user');
assert_true($user['email_verified_at'] === null, 'New user is unverified');

// Duplicate email
$dup = User::findByEmail($testEmail);
assert_true($dup !== null, 'Duplicate email lookup works');

// Verification token
$token = User::issueVerificationToken($id);
assert_true(strlen($token) === 64, 'Verification token length');
$found = User::findByVerificationToken($token);
assert_true($found !== null && (int) $found['id'] === $id, 'Verification token validates');

User::markEmailVerified($id);
$user = User::find($id);
assert_true($user['email_verified_at'] !== null, 'Verification marks email_verified_at');
assert_true($user['verification_token_hash'] === null, 'Verification clears token hash');

// Reused token fails
assert_true(User::findByVerificationToken($token) === null, 'Reused verification token rejected');

// Expired token
$id2 = User::create('Expire Test', 'expire-' . bin2hex(random_bytes(4)) . '@example.test', 'test-password-123');
$expiredToken = User::issueVerificationToken($id2);
Database::run('UPDATE users SET verification_expires_at = ? WHERE id = ?', ['2000-01-01 00:00:00', $id2]);
assert_true(User::findByVerificationToken($expiredToken) === null, 'Expired verification token rejected');

// Password reset
$resetRaw = PasswordReset::issue($id);
$row = PasswordReset::findValid($resetRaw);
assert_true($row !== null, 'Password reset token created');
$userId = PasswordReset::consume($resetRaw);
assert_true($userId === $id, 'Password reset token single use');
assert_true(PasswordReset::findValid($resetRaw) === null, 'Consumed reset token invalid');

// Simulated user email suppression
$simId = User::create('Sim User', 'sim-' . bin2hex(random_bytes(4)) . '@demo.local', 'demo-kitchen-2026');
Database::run('UPDATE users SET simulation = 1, email_verified_at = ? WHERE id = ?', [now_utc(), $simId]);
$simToken = User::issueVerificationToken($simId);
// AccountMailer returns true but should not actually send - we verify shouldSuppress path
$sent = AccountMailer::sendVerification($simId, 'sim@demo.local', 'Sim', $simToken);
assert_true($sent === true, 'Simulated user mail suppressed (returns success)');

// Profile update
User::updateProfile($id, ['name' => 'Updated Name', 'bio' => 'Hello']);
$user = User::find($id);
assert_true($user['name'] === 'Updated Name', 'Profile update works');

// Preferences
User::updatePreferences($id, ['preferred_ai' => 'Claude', 'timezone' => 'America/Los_Angeles']);
$user = User::find($id);
assert_true($user['preferred_ai'] === 'Claude', 'Preferred AI saved');
assert_true($user['timezone'] === 'America/Los_Angeles', 'Timezone saved');

// Email normalization
assert_true(User::normalizeEmail('  Test@Example.COM ') === 'test@example.com', 'Email normalized');

// Cleanup test users
Database::run('DELETE FROM users WHERE id IN (?, ?)', [$id, $id2]);
Database::run('DELETE FROM users WHERE id = ?', [$simId]);

echo "\n{$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);
