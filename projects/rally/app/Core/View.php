<?php

declare(strict_types=1);

namespace Rally\Core;

/**
 * Renders PHP templates from app/Views. Templates receive extracted
 * variables plus $auth (current user row or null). Full pages render
 * inside layout/app.php unless $layout is false.
 */
final class View
{
    private static string $viewsDir = __DIR__ . '/../Views';

    /** @param array<string, mixed> $data */
    public static function render(string $template, array $data = [], bool $layout = true): void
    {
        echo self::capture($template, $data, $layout);
    }

    /** @param array<string, mixed> $data */
    public static function capture(string $template, array $data = [], bool $layout = true): string
    {
        $data['auth'] = $data['auth'] ?? Auth::user();
        $content = self::renderFile($template, $data);
        if (!$layout) {
            return $content;
        }
        $data['content'] = $content;
        return self::renderFile('layout/app', $data);
    }

    /** @param array<string, mixed> $data */
    public static function partial(string $template, array $data = []): void
    {
        $data['auth'] = $data['auth'] ?? Auth::user();
        echo self::renderFile($template, $data);
    }

    /** @param array<string, mixed> $data */
    private static function renderFile(string $template, array $data): string
    {
        $file = self::$viewsDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('View not found: ' . $template);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
