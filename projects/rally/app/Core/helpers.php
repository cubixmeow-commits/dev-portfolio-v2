<?php

declare(strict_types=1);

use Rally\Core\Config;
use Rally\Core\Env;

/**
 * Escape for HTML output. Every piece of dynamic data printed in a
 * template goes through this helper; templates never echo raw values.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Public URL origin for email links (no trailing slash). */
function app_origin(): string
{
    $url = rtrim(Config::string('app.url'), '/');
    if ($url !== '') {
        return $url;
    }
    $url = rtrim(Config::string('app.base_url'), '/');
    if ($url !== '') {
        return $url;
    }
    return rtrim(Env::get('APP_URL', ''), '/');
}

/** Path prefix when the app is not served from the domain root. */
function app_base_path(): string
{
    $base = rtrim(Config::string('app.base_path'), '/');
    if ($base !== '') {
        return $base;
    }
    return rtrim(Env::get('APP_BASE_PATH', ''), '/');
}

/** Build an app URL from a path, respecting the configured base path. */
function url(string $path = '/'): string
{
    return app_base_path() . '/' . ltrim($path, '/');
}

/** Absolute URL for email links: APP_URL + base path + route path. */
function full_url(string $path = '/'): string
{
    $origin = app_origin();
    if ($origin === '') {
        $origin = 'http://localhost';
    }
    return $origin . url($path);
}

/**
 * Static asset URL with a filemtime cache-buster so CSS/JS updates show up
 * immediately after deploy (shared hosts often cache /assets for a day).
 */
function asset(string $path): string
{
    $publicRoot = dirname(__DIR__, 2) . '/public';
    $file = $publicRoot . '/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '0';

    return url($path) . '?v=' . $version;
}

/** Redirect to an app path and end the request. */
function redirect(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit;
}

/** Send a JSON response and end the request. */
function json_response(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/** "1 recipe" / "3 recipes" without repeating the ternary everywhere. */
function plural(int $count, string $singular, ?string $pluralForm = null): string
{
    $word = $count === 1 ? $singular : ($pluralForm ?? $singular . 's');
    return $count . ' ' . $word;
}

/**
 * Human relative timestamp: "just now", "4m ago", "3h ago", "2d ago",
 * then a short date. Input is a UTC datetime string from the database.
 */
function time_ago(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '';
    }
    $then = new DateTimeImmutable($datetime, new DateTimeZone('UTC'));
    $diff = time() - $then->getTimestamp();
    if ($diff < 60) {
        return 'just now';
    }
    if ($diff < 3600) {
        return intdiv($diff, 60) . 'm ago';
    }
    if ($diff < 86400) {
        return intdiv($diff, 3600) . 'h ago';
    }
    if ($diff < 86400 * 7) {
        return intdiv($diff, 86400) . 'd ago';
    }
    return $then->format('M j') . ($then->format('Y') !== date('Y') ? $then->format(', Y') : '');
}

/** Current UTC timestamp in the storage format used across the schema. */
function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}
