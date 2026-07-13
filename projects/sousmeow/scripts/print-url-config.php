#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Print the URL settings used for email links. Run on the server to verify
 * verification and reset links will resolve correctly.
 *
 * Usage: php scripts/print-url-config.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

echo "SousMeow URL configuration\n\n";
echo 'app_origin():   ' . app_origin() . "\n";
echo 'app_base_path(): ' . (app_base_path() !== '' ? app_base_path() : '(empty — app at domain root)') . "\n";
echo 'full_url(/kitchen):        ' . full_url('/kitchen') . "\n";
echo 'full_url(/verify-email/…): ' . full_url('/verify-email/example-token') . "\n";
echo "\n";
echo "Open the kitchen URL in your browser. If it 404s, set APP_BASE_PATH in .env\n";
echo "to the path segment before /kitchen in your working site URL.\n";
