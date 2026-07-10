<?php
/**
 * Minimal session auth separating the public "submit a ticket" view from the
 * agent view (queue management, reporting, assets). A real deployment would
 * back this with the hot_users table and hashed passwords / SSO; for a
 * portfolio demo a single shared agent login keeps it easy to try.
 */
declare(strict_types=1);

function is_agent(): bool
{
    return !empty($_SESSION['agent']);
}

function current_agent(): ?string
{
    return $_SESSION['agent'] ?? null;
}

function attempt_login(string $user, string $pass): bool
{
    $cfg = $GLOBALS['HOT_CFG'];
    $ok = hash_equals($cfg['agent_user'], $user) && hash_equals($cfg['agent_pass'], $pass);
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['agent'] = $cfg['agent_name'];
    }
    return $ok;
}

function logout(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

/** Guard agent-only pages; bounce to login with a return path. */
function require_agent(): void
{
    if (!is_agent()) {
        redirect('login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? 'index.php'));
    }
}
