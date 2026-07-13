<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Config;
use SousMeow\Core\Mailer;
use SousMeow\Models\User;

/**
 * Account-related transactional email. Simulated users never receive mail.
 */
final class AccountMailer
{
    public static function sendVerification(int $userId, string $email, string $name, string $rawToken): bool
    {
        if (self::shouldSuppress($userId)) {
            return true;
        }

        $link = full_url('/verify-email/' . $rawToken);
        $subject = 'Verify your SousMeow account';

        $text = <<<TEXT
Welcome to SousMeow.

Verify your email address to finish setting up your account and start your first guided project.

Verify my email:
{$link}

This link expires in 24 hours.

If you did not create this account, you can safely ignore this email.

—

Chef Meow
Your guide through every AI project
TEXT;

        $html = self::wrapHtml(
            'Welcome to SousMeow.',
            '<p>Verify your email address to finish setting up your account and start your first guided project.</p>'
            . self::button('Verify my email', $link)
            . '<p style="color:#666;font-size:14px;">This link expires in 24 hours.</p>'
            . '<p style="color:#666;font-size:14px;">If you did not create this account, you can safely ignore this email.</p>'
        );

        return Mailer::send($email, $subject, ['text' => $text, 'html' => $html]);
    }

    public static function sendPasswordReset(int $userId, string $email, string $rawToken): bool
    {
        if (self::shouldSuppress($userId)) {
            return true;
        }

        $link = full_url('/reset-password/' . $rawToken);
        $subject = 'Reset your SousMeow password';

        $text = <<<TEXT
We received a request to reset your SousMeow password.

Reset my password:
{$link}

This link expires in 60 minutes.

If you did not request this, no action is required.

—

Chef Meow
Your guide through every AI project
TEXT;

        $html = self::wrapHtml(
            'Reset your password',
            '<p>We received a request to reset your SousMeow password.</p>'
            . self::button('Reset my password', $link)
            . '<p style="color:#666;font-size:14px;">This link expires in 60 minutes.</p>'
            . '<p style="color:#666;font-size:14px;">If you did not request this, no action is required.</p>'
        );

        return Mailer::send($email, $subject, ['text' => $text, 'html' => $html]);
    }

    public static function sendPasswordChanged(int $userId, string $email): bool
    {
        if (self::shouldSuppress($userId)) {
            return true;
        }

        $subject = 'Your SousMeow password was changed';
        $text = "Your SousMeow password was just changed.\n\nIf you did not make this change, contact support immediately.\n\n—\nChef Meow";
        $html = self::wrapHtml(
            'Password changed',
            '<p>Your SousMeow password was just changed.</p>'
            . '<p style="color:#666;font-size:14px;">If you did not make this change, contact support immediately.</p>'
        );

        return Mailer::send($email, $subject, ['text' => $text, 'html' => $html]);
    }

    public static function sendEmailChangeConfirmation(int $userId, string $newEmail, string $rawToken): bool
    {
        if (self::shouldSuppress($userId)) {
            return true;
        }

        $link = full_url('/account/email/confirm/' . $rawToken);
        $subject = 'Confirm your new SousMeow email';

        $text = "Confirm your new email address for SousMeow:\n\n{$link}\n\nThis link expires in 24 hours.\n\n—\nChef Meow";
        $html = self::wrapHtml(
            'Confirm your new email',
            '<p>Confirm your new email address for SousMeow.</p>'
            . self::button('Confirm email', $link)
            . '<p style="color:#666;font-size:14px;">This link expires in 24 hours.</p>'
        );

        return Mailer::send($newEmail, $subject, ['text' => $text, 'html' => $html]);
    }

    public static function sendEmailChangeNotice(int $userId, string $oldEmail, string $newEmail): bool
    {
        if (self::shouldSuppress($userId)) {
            return true;
        }

        $subject = 'Your SousMeow email address is changing';
        $text = "A request was made to change your SousMeow email to {$newEmail}.\n\nIf you did not request this, sign in and secure your account.\n\n—\nChef Meow";
        $html = self::wrapHtml(
            'Email change requested',
            '<p>A request was made to change your SousMeow email to <strong>' . htmlspecialchars($newEmail) . '</strong>.</p>'
            . '<p style="color:#666;font-size:14px;">If you did not request this, sign in and secure your account.</p>'
        );

        return Mailer::send($oldEmail, $subject, ['text' => $text, 'html' => $html]);
    }

    public static function sendTest(string $to): bool
    {
        $subject = 'SousMeow SMTP test';
        $text = 'This is a test message from SousMeow. SMTP is configured correctly.';
        $html = self::wrapHtml('SMTP test', '<p>This is a test message from SousMeow. SMTP is configured correctly.</p>');
        return Mailer::send($to, $subject, ['text' => $text, 'html' => $html]);
    }

    private static function shouldSuppress(int $userId): bool
    {
        $user = User::find($userId);
        if ($user === null) {
            return true;
        }
        return (int) ($user['simulation'] ?? 0) === 1;
    }

    private static function button(string $label, string $href): string
    {
        $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        return '<p style="margin:24px 0;"><a href="' . $safeHref . '" style="display:inline-block;padding:12px 24px;background:#c45c26;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;">'
            . $safeLabel . '</a></p>';
    }

    private static function wrapHtml(string $title, string $body): string
    {
        $fromName = htmlspecialchars(Config::string('mail.from_name', 'Chef Meow'), ENT_QUOTES, 'UTF-8');
        return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>'
            . htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
            . '</title></head><body style="font-family:system-ui,sans-serif;line-height:1.5;color:#1a1a1a;max-width:560px;margin:0 auto;padding:24px;">'
            . $body
            . '<hr style="border:none;border-top:1px solid #e0e0e0;margin:32px 0;">'
            . '<p style="color:#666;font-size:14px;"><strong>' . $fromName . '</strong><br>Your guide through every AI project</p>'
            . '</body></html>';
    }
}
