<?php

declare(strict_types=1);

namespace Rally\Core;

/**
 * Dot-notation access to the configuration array. Loads config.php when
 * present, otherwise config.example.php, whose defaults run on SQLite so
 * a fresh checkout works with zero setup.
 */
final class Config
{
    /** @var array<string, mixed> */
    private static array $values = [];

    public static function load(string $configDir): void
    {
        $file = $configDir . '/config.php';
        if (!is_file($file)) {
            $file = $configDir . '/config.example.php';
        }
        /** @var array<string, mixed> $values */
        $values = require $file;
        self::$values = $values;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $node = self::$values;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($node) || !array_key_exists($segment, $node)) {
                return $default;
            }
            $node = $node[$segment];
        }
        return $node;
    }

    public static function string(string $key, string $default = ''): string
    {
        return (string) self::get($key, $default);
    }

    public static function int(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return (bool) self::get($key, $default);
    }
}
