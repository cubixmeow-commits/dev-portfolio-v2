#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Send a test email using configured SMTP or log driver.
 *
 * Usage: php scripts/test-email.php recipient@example.com
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$recipient = $argv[1] ?? '';
if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php scripts/test-email.php recipient@example.com\n");
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Config;
use SousMeow\Core\Env;
use SousMeow\Core\Mailer;
use SousMeow\Services\AccountMailer;

$driver = Mailer::driver();
echo "Mail driver: {$driver}\n";

if ($driver === 'smtp' && Env::get('SMTP_PASSWORD', '') === '' && \SousMeow\Core\Config::string('mail.password') === '') {
    fwrite(STDERR, "FAIL: SMTP_PASSWORD is not set. Configure .env before sending.\n");
    exit(1);
}

$ok = AccountMailer::sendTest($recipient);
if ($ok) {
    echo "OK: Test message sent to {$recipient}\n";
    if ($driver === 'log') {
        $logDir = Config::string('mail.log_dir') ?: dirname(__DIR__) . '/storage/mail';
        echo "Note: log driver writes to {$logDir}\n";
    }
    exit(0);
}

fwrite(STDERR, "FAIL: Could not send test message. Check error logs.\n");
exit(1);
