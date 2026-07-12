<?php

/**
 * SousMeow application configuration template.
 *
 * Copy this file to config.php in the same directory and adjust values.
 * config.php is gitignored and must never be committed. When config.php
 * is absent the application falls back to these defaults, which run
 * entirely on SQLite so the project works out of the box.
 *
 * This directory sits outside the web root (public/), so credentials are
 * not servable even if PHP handling breaks.
 */

return [
    'app' => [
        // 'production' or 'development'. Development shows detailed errors.
        'env' => 'development',

        // Public base URL of the app, no trailing slash.
        'base_url' => 'http://localhost:8090',

        // Path prefix if the app lives in a subdirectory of the domain,
        // for example '/sousmeow'. Empty string when served at the root.
        'base_path' => '',

        'timezone' => 'UTC',
    ],

    'db' => [
        // 'sqlite' (zero setup, default) or 'mysql' (Hostinger).
        'driver' => 'sqlite',

        // SQLite settings. The file lives outside the web root.
        'sqlite_path' => __DIR__ . '/../storage/sousmeow.sqlite',

        // MySQL settings, used when driver is 'mysql'.
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'sousmeow',
        'user'     => 'sousmeow',
        'password' => 'change-me',
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'cookie_name' => 'sousmeow_session',
        // Idle expiry in seconds. 14 days.
        'idle_ttl'    => 1209600,
        // Set true when serving over HTTPS (always true in production).
        'secure'      => false,
    ],

    'exports' => [
        // Where generated Project Kit zips are written. Outside web root.
        'dir' => __DIR__ . '/../storage/exports',
    ],
];
