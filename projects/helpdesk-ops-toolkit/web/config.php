<?php
/**
 * Helpdesk Ops Toolkit — configuration.
 *
 * Production target is MySQL. Everything here is overridable by environment
 * variable so no credentials live in the repo. For a local, MySQL-free demo,
 * point HOT_DB_DSN at the generated SQLite file (see db/make_demo_db.php):
 *   HOT_DB_DSN="sqlite:/abs/path/db/hot_demo.sqlite" php -S localhost:8000 -t web
 */
declare(strict_types=1);

return [
    'db_dsn'  => getenv('HOT_DB_DSN')  ?: 'mysql:host=127.0.0.1;dbname=helpdesk_ops;charset=utf8mb4',
    'db_user' => getenv('HOT_DB_USER') ?: 'root',
    'db_pass' => getenv('HOT_DB_PASS') ?: '',

    // Demo agent sign-in. This is a public portfolio demo, not a live system;
    // the credentials are intentionally shown on the login page.
    'agent_user' => getenv('HOT_AGENT_USER') ?: 'agent',
    'agent_pass' => getenv('HOT_AGENT_PASS') ?: 'demo123',
    'agent_name' => getenv('HOT_AGENT_NAME') ?: 'A. Rivera',

    // Show PHP errors on screen (demo). Set HOT_DEBUG=0 in production.
    'debug' => getenv('HOT_DEBUG') === false ? true : getenv('HOT_DEBUG') === '1',
];
