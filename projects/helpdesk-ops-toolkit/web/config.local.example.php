<?php
/**
 * LOCAL CONFIG TEMPLATE — how to configure a live deployment.
 *
 *   1. Copy this file to  config.local.php  (same folder).
 *   2. Fill in your real database name, user, and password.
 *   3. Leave anything you don't want to change commented out — only the keys
 *      you set here override the defaults in config.php.
 *
 * config.local.php is git-ignored, so your credentials never get committed.
 * This is the recommended approach on shared hosting, where environment
 * variables usually don't reach PHP.
 */
declare(strict_types=1);

return [
    // ---- Database (required) --------------------------------------------
    // On cPanel/shared hosting the DB and user names usually carry your
    // account prefix, e.g. "cpaneluser_helpdesk_ops" / "cpaneluser_helpdesk".
    'db_dsn'  => 'mysql:host=localhost;dbname=YOUR_DB_NAME;charset=utf8mb4',
    'db_user' => 'YOUR_DB_USER',
    'db_pass' => 'YOUR_DB_PASSWORD',

    // ---- Production hardening --------------------------------------------
    // Hide PHP errors from visitors on the live site.
    'debug' => false,

    // ---- Agent sign-in (optional) ---------------------------------------
    // Change these from the public demo defaults if you don't want the
    // agent view to be open to anyone.
    // 'agent_user' => 'agent',
    // 'agent_pass' => 'choose-a-password',
    // 'agent_name' => 'A. Rivera',
];
