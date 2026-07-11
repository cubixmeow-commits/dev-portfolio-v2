<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Outbound mail. The default driver writes RFC 5322 .eml files to
 * storage/mail/ because shared-hosting sendmail is unreliable and demo
 * reviewers need to see the verification and reset emails anyway.
 * RUNBOOK.md documents swapping in SMTP: implement send() against a
 * socket and switch mail.driver in config.
 */
final class Mailer
{
    public static function send(string $to, string $subject, string $textBody): bool
    {
        $driver = Config::string('mail.driver', 'spool');
        if ($driver === 'spool') {
            return self::spool($to, $subject, $textBody);
        }
        // 'mail' driver: native mail() for hosts where it works.
        return mail($to, $subject, $textBody, self::headers());
    }

    private static function spool(string $to, string $subject, string $textBody): bool
    {
        $dir = Config::string('mail.spool_dir', __DIR__ . '/../../storage/mail');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return false;
        }
        $eml = self::headers()
            . 'To: ' . $to . "\r\n"
            . 'Subject: ' . $subject . "\r\n"
            . 'Date: ' . date(DATE_RFC2822) . "\r\n"
            . 'Content-Type: text/plain; charset=utf-8' . "\r\n"
            . "\r\n"
            . $textBody . "\r\n";
        $file = $dir . '/' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.eml';
        return file_put_contents($file, $eml) !== false;
    }

    private static function headers(): string
    {
        return 'From: ' . Config::string('mail.from', 'Cadence <no-reply@localhost>') . "\r\n"
            . 'MIME-Version: 1.0' . "\r\n";
    }
}
