<?php

declare(strict_types=1);

/**
 * Application bootstrap: autoloading, configuration, timezone, session.
 * Required once by the front controller (and nothing else).
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'Cadence\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/Core/helpers.php';

$configFile = __DIR__ . '/../config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    echo 'Missing config/config.php. Copy config/config.example.php and fill in your values.';
    exit;
}

Cadence\Core\Config::load(require $configFile);

date_default_timezone_set(Cadence\Core\Config::string('app.timezone', 'UTC'));

Cadence\Core\Session::start();
