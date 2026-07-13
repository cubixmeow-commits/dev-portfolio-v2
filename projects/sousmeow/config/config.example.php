<?php

/**
 * SousMeow application configuration template.
 *
 * Copy this file to config.php in the same directory and adjust values.
 * config.php is gitignored and must never be committed. When config.php
 * is absent the application falls back to these defaults, which run
 * entirely on SQLite so the project works out of the box.
 *
 * Production-sensitive values can also be set via .env (see .env.example).
 * Environment variables take precedence over values in config.php.
 */

use SousMeow\Core\Env;

return [
    'app' => [
        // 'production' or 'development'. Development shows detailed errors.
        'env' => 'development',

        // Public base URL of the app, no trailing slash. Used for email links.
        'url' => Env::get('APP_URL', 'http://localhost:8090'),

        // Legacy key kept for compatibility with existing config.php files.
        'base_url' => Env::get('APP_URL', 'http://localhost:8090'),

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

    'mail' => [
        // 'smtp' sends via Hostinger; 'log' writes .eml files for local dev.
        'driver'       => Env::get('MAIL_DRIVER', 'log'),
        'host'         => Env::get('SMTP_HOST', 'smtp.hostinger.com'),
        'port'         => (int) Env::get('SMTP_PORT', '465'),
        'encryption'   => Env::get('SMTP_ENCRYPTION', 'ssl'),
        'username'     => Env::get('SMTP_USERNAME', ''),
        'password'     => Env::get('SMTP_PASSWORD', ''),
        'from_address' => Env::get('MAIL_FROM_ADDRESS', 'ChefMeow@sousmeow.com'),
        'from_name'    => Env::get('MAIL_FROM_NAME', 'Chef Meow'),
        'reply_to'     => Env::get('MAIL_REPLY_TO', 'ChefMeow@sousmeow.com'),
        'log_dir'      => __DIR__ . '/../storage/mail',
    ],
];
