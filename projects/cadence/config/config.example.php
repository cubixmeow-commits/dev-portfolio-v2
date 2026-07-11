<?php

/**
 * Cadence application configuration template.
 *
 * Copy this file to config.php in the same directory and fill in real
 * values. config.php is gitignored and must never be committed. This
 * directory sits outside the web root (public/), so credentials are not
 * servable even if PHP handling breaks.
 */

return [
    'app' => [
        // 'production' or 'development'. Development shows detailed errors.
        'env' => 'development',

        // Public base URL of the app, no trailing slash.
        'base_url' => 'http://localhost:8080',

        // Path prefix if the app lives in a subdirectory of the domain,
        // for example '/cadence'. Empty string when served at the root.
        'base_path' => '',

        // Fallback timezone for anonymous visitors and system jobs.
        'timezone' => 'America/Los_Angeles',
    ],

    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'cadence',
        'user'     => 'cadence',
        'password' => 'change-me',
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'cookie_name'  => 'cadence_session',
        // Idle expiry in seconds. 30 days.
        'idle_ttl'     => 2592000,
        // Set true when serving over HTTPS (always true in production).
        'secure'       => false,
    ],

    'mail' => [
        // 'spool' writes .eml files to storage/mail/ (reliable on shared
        // hosting). See RUNBOOK.md for swapping in real SMTP delivery.
        'driver' => 'spool',
        'from'   => 'Cadence <no-reply@cubixmeow.com>',
        'spool_dir' => __DIR__ . '/../storage/mail',
    ],

    'engine' => [
        // Absolute path to the ops engine jar. Fixed here so web-triggered
        // runs can never be pointed at an arbitrary executable.
        'jar_path' => __DIR__ . '/../engine/build/cadence-engine.jar',
        // Absolute path to engine.properties (DB config for the jar).
        'properties_path' => __DIR__ . '/engine.properties',
        // Java binary. Plain 'java' if it is on PATH for the web user.
        'java_bin' => 'java',
    ],

    'security' => [
        // Bcrypt cost. 12 is a sensible production default.
        'bcrypt_cost' => 12,
        // Minimum password length for registration and resets.
        'min_password_length' => 10,
    ],
];
