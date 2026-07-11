<?php

declare(strict_types=1);

use Cadence\Core\Config;

/**
 * Escape for HTML output. Every piece of dynamic data printed in a
 * template goes through this helper; templates never echo raw values.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build an app URL from a path, respecting the configured base path. */
function url(string $path = '/'): string
{
    $base = rtrim(Config::string('app.base_path'), '/');
    return $base . '/' . ltrim($path, '/');
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

/** Format an integer with thousands separators for display. */
function fmt_int(int|float|string|null $n): string
{
    return number_format((float) ($n ?? 0), 0, '.', ',');
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

/**
 * Deterministic avatar colors from a seed string. Returns two hex colors
 * used by the generated initials avatar; the same seed always produces
 * the same pair, so avatars are stable without storing any image.
 *
 * @return array{string, string}
 */
function avatar_colors(string $seed): array
{
    $palette = [
        ['#0A7C5C', '#E3F2EC'],
        ['#1F6FB2', '#E4EFF8'],
        ['#7A4FB0', '#EFE8F7'],
        ['#B4552D', '#F8ECE5'],
        ['#8A6D1F', '#F6F0DD'],
        ['#2D7A8A', '#E3F1F4'],
        ['#A03E62', '#F7E7EE'],
        ['#4B6C2F', '#EBF2E2'],
    ];
    $index = hexdec(substr(hash('sha256', $seed), 0, 4)) % count($palette);
    return $palette[$index];
}

/** Initials for the generated avatar, from a display name. */
function avatar_initials(string $displayName): string
{
    $parts = preg_split('/\s+/', trim($displayName)) ?: [];
    $first = $parts[0] ?? '';
    $last = count($parts) > 1 ? $parts[count($parts) - 1] : '';
    $initials = mb_substr($first, 0, 1) . mb_substr($last, 0, 1);
    return mb_strtoupper($initials !== '' ? $initials : '?');
}
