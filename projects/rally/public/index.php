<?php

declare(strict_types=1);

/**
 * Front controller. Every request that is not a real file under public/
 * lands here: security headers first, then bootstrap, then a single
 * CSRF gate for all POSTs, then routing.
 */

if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; script-src 'self'; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");

require __DIR__ . '/../app/bootstrap.php';

use Rally\Core\Config;
use Rally\Core\Csrf;
use Rally\Core\Router;
use Rally\Core\View;

$isDev = Config::string('app.env') === 'development';
ini_set('display_errors', $isDev ? '1' : '0');
error_reporting(E_ALL);

set_exception_handler(static function (Throwable $e) use ($isDev): void {
    error_log((string) $e);
    if (!headers_sent()) {
        http_response_code(500);
    }
    if ($isDev) {
        echo '<pre style="padding:2rem;white-space:pre-wrap">' . e((string) $e) . '</pre>';
        return;
    }
    try {
        View::render('errors/500', ['title' => 'Something went wrong']);
    } catch (Throwable) {
        echo 'Something went wrong on our side. Try again in a minute.';
    }
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$basePath = rtrim(app_base_path(), '/');
if ($basePath !== '' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}
if ($path === '/public' || str_starts_with($path, '/public/')) {
    $path = substr($path, strlen('/public')) ?: '/';
}

if ($method === 'POST') {
    Csrf::verify();
}

$router = new Router();
(require __DIR__ . '/../app/routes.php')($router);
$router->dispatch($method, $path);
