<?php

declare(strict_types=1);

namespace SousMeow\Core;

/**
 * Minimal .env loader for shared hosting without Composer. Values already
 * present in the process environment are never overwritten.
 */
final class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            if ($name === '' || array_key_exists($name, $_ENV)) {
                continue;
            }
            $value = self::stripQuotes(trim($value));
            $_ENV[$name] = $value;
            putenv($name . '=' . $value);
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return (string) $value;
    }

    private static function stripQuotes(string $value): string
    {
        if ($value === '') {
            return $value;
        }
        $first = $value[0];
        $last = $value[strlen($value) - 1];
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            return substr($value, 1, -1);
        }
        return $value;
    }
}
