<?php
/**
 * Bootstrap — loads config, starts the session, and pulls in the shared
 * libraries. Every page starts with:  require __DIR__ . '/lib/bootstrap.php';
 */
declare(strict_types=1);

$CFG = require __DIR__ . '/../config.php';
$GLOBALS['HOT_CFG'] = $CFG;

if ($CFG['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('hot_sess');
    session_start();
}

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/repo.php';
require __DIR__ . '/view.php';
