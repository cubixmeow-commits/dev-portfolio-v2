<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Read-only access to the application configuration array.
 * Loaded once by the front controller; consumed everywhere via dot keys,
 * for example Config::get('db.host').
 */
final class Config
{
    /** @var array<string, mixed> */
    private static array $values = [];

    /** @param array<string, mixed> $values */
    public static function load(array $values): void
    {
        self::$values = $values;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $node = self::$values;
        foreach (explode('.', $key) as $part) {
            if (!is_array($node) || !array_key_exists($part, $node)) {
                return $default;
            }
            $node = $node[$part];
        }
        return $node;
    }

    public static function string(string $key, string $default = ''): string
    {
        $value = self::get($key, $default);
        return is_scalar($value) ? (string) $value : $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = self::get($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);
        return is_bool($value) ? $value : $default;
    }
}
