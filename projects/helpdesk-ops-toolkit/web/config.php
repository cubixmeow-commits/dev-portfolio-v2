<?php
/**
 * Helpdesk Ops Toolkit — configuration.
 *
 * Settings are resolved in this order (later wins):
 *   1. the built-in defaults below;
 *   2. environment variables (HOT_DB_DSN, HOT_DB_USER, …), if set;
 *   3. a git-ignored config.local.php file, if present.
 *
 * On shared hosting, environment variables often don't reach PHP, so the
 * recommended way to configure a live deployment is to copy
 * config.local.example.php to config.local.php and fill in your values.
 * config.local.php is git-ignored, so your credentials stay out of the repo.
 *
 * For a local, MySQL-free demo, point the DSN at the generated SQLite file:
 *   HOT_DB_DSN="sqlite:/abs/path/db/hot_demo.sqlite" php -S localhost:8000 -t web
 */
declare(strict_types=1);

$config = [
    'db_dsn'  => getenv('HOT_DB_DSN')  ?: 'mysql:host=127.0.0.1;dbname=helpdesk_ops;charset=utf8mb4',
    'db_user' => getenv('HOT_DB_USER') ?: 'root',
    'db_pass' => getenv('HOT_DB_PASS') ?: '',

    // Demo agent sign-in. This is a public portfolio demo, not a live system;
    // the credentials are intentionally shown on the login page. Override them
    // in config.local.php for a real deployment.
    'agent_user' => getenv('HOT_AGENT_USER') ?: 'agent',
    'agent_pass' => getenv('HOT_AGENT_PASS') ?: 'demo123',
    'agent_name' => getenv('HOT_AGENT_NAME') ?: 'A. Rivera',

    // Show PHP errors on screen (demo). Set to false in config.local.php,
    // or HOT_DEBUG=0 in the environment, for production.
    'debug' => getenv('HOT_DEBUG') === false ? true : getenv('HOT_DEBUG') === '1',
];

// Local overrides — copy config.local.example.php to config.local.php.
$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        $config = array_merge($config, $local);
    }
}

return $config;
