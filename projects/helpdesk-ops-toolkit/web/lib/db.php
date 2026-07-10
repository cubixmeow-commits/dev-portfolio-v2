<?php
/**
 * Database access — a single shared PDO connection.
 * Uses prepared statements everywhere (see repo.php); no query is built by
 * concatenating user input.
 */
declare(strict_types=1);

function hot_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $cfg = $GLOBALS['HOT_CFG'];
    try {
        $pdo = new PDO($cfg['db_dsn'], $cfg['db_user'], $cfg['db_pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(503);
        header('Content-Type: text/html; charset=utf-8');
        $detail = $cfg['debug'] ? htmlspecialchars($e->getMessage(), ENT_QUOTES) : '';
        echo '<!doctype html><meta charset="utf-8"><title>Service unavailable</title>'
           . '<div style="font:16px/1.5 system-ui,sans-serif;max-width:640px;margin:12vh auto;padding:0 20px;color:#1b1b1b">'
           . '<h1 style="color:#b50909">The helpdesk is temporarily unavailable</h1>'
           . '<p>The application could not reach its database. If you are running this locally, '
           . 'import <code>db/schema.sql</code> and <code>db/seed.sql</code> into MySQL, or build the '
           . 'demo database with <code>php db/make_demo_db.php</code> and set <code>HOT_DB_DSN</code>.</p>'
           . ($detail ? "<pre style=\"background:#f0f0f0;padding:12px;overflow:auto\">$detail</pre>" : '')
           . '</div>';
        exit;
    }
    return $pdo;
}

/** True when the active connection is the local SQLite demo. */
function hot_is_sqlite(): bool
{
    return strncmp($GLOBALS['HOT_CFG']['db_dsn'], 'sqlite:', 7) === 0;
}
