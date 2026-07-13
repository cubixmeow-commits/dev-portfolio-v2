<?php

declare(strict_types=1);

/**
 * Application bootstrap: autoloader, helpers, config, session. Both the
 * front controller and the CLI scripts include this file.
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'SousMeow\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/Core/helpers.php';

use SousMeow\Core\Config;
use SousMeow\Core\Env;
use SousMeow\Core\Session;

Env::load(__DIR__ . '/../.env');
Config::load(__DIR__ . '/../config');

date_default_timezone_set(Config::string('app.timezone', 'UTC'));

if (PHP_SAPI !== 'cli') {
    Session::start();
}
