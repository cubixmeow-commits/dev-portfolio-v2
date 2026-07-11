<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * One-shot messages across a redirect: set on the POST, rendered once on
 * the following GET. Levels map to styles: success, error, info.
 */
final class Flash
{
    public static function set(string $level, string $message): void
    {
        $_SESSION['flash'][] = ['level' => $level, 'message' => $message];
    }

    /** @return list<array{level: string, message: string}> */
    public static function pull(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
