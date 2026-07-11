<?php

declare(strict_types=1);

namespace Cadence\Core;

/**
 * Per-session CSRF token. The front controller calls verify() for every
 * POST before routing, so no state-changing endpoint can forget the
 * check. The token rotates whenever the session id rotates (login).
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Hidden input for forms. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    /**
     * Validate the submitted token. Accepts the _csrf form field or the
     * X-CSRF-Token header (used by fetch() calls). Terminates the request
     * with 419 on failure.
     */
    public static function verify(): void
    {
        $submitted = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $expected = $_SESSION['csrf_token'] ?? '';
        if ($expected === '' || !is_string($submitted) || !hash_equals($expected, $submitted)) {
            http_response_code(419);
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                json_response(['error' => 'Your session expired. Refresh the page and try again.'], 419);
            }
            View::render('errors/419', ['title' => 'Session expired']);
            exit;
        }
    }
}
