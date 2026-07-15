<?php

/**
 * Rally application configuration template.
 *
 * Copy this file to config.php in the same directory and adjust values.
 * config.php is gitignored and must never be committed. When config.php
 * is absent the application falls back to these defaults, which run
 * entirely on SQLite so the project works out of the box.
 */

use Rally\Core\Env;

return [
    'app' => [
        // 'production' or 'development'. Development shows detailed errors
        // and unlocks the simulation control page for signed-in users.
        'env' => 'development',

        // Public origin only (no path). Used for absolute / share links.
        'url' => Env::get('APP_URL', 'http://localhost:8091'),

        'base_url' => Env::get('APP_URL', 'http://localhost:8091'),

        // Subdirectory prefix with no trailing slash, e.g.
        // '/iain/projects/rally/public' on cubixmeow.com.
        'base_path' => Env::get('APP_BASE_PATH', ''),

        'timezone' => 'UTC',

        // Settlement lag: day N becomes official at this local time on day N+2.
        'settlement_hour' => 6,
        'settlement_lag_days' => 2,

        // Metric value bounds for daily steps (and future metrics).
        'metric_value_min' => 0,
        'metric_value_max' => 100000,
    ],

    'db' => [
        // 'sqlite' (zero setup, default) or 'mysql' (Hostinger).
        'driver' => 'sqlite',

        'sqlite_path' => __DIR__ . '/../storage/rally.sqlite',

        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'rally',
        'user'     => 'rally',
        'password' => 'change-me',
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'cookie_name' => 'rally_session',
        'idle_ttl'    => 1209600,
        'secure'      => false,
    ],

    'demo' => [
        // Shared password for all seeded demo players.
        'password' => 'rally-demo-2026',
    ],
];
