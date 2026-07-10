<?php
/**
 * Layout + small presentational helpers. The look is deliberately "civic":
 * an official-notice banner, a solid navy masthead, a simple tab nav, and
 * plain high-contrast tables — the visual grammar of a public-sector IT
 * portal (USWDS / GOV.UK inspired), kept dependency-free.
 */
declare(strict_types=1);

function status_badge(string $status): string
{
    return '<span class="badge badge--' . e($status) . '">' . e(label($status)) . '</span>';
}

function priority_badge(string $priority): string
{
    return '<span class="pri pri--' . e($priority) . '">' . e(label($priority)) . '</span>';
}

function asset_badge(string $status): string
{
    return '<span class="badge badge--asset-' . e($status) . '">' . e(label($status)) . '</span>';
}

/**
 * @param string $active One of: tickets, reports, assets, kb
 */
function hot_header(string $title, string $active = ''): void
{
    $agent = is_agent();
    $nav = [
        'tickets' => ['index.php', 'Tickets'],
        'reports' => ['reports.php', 'Reports'],
        'assets'  => ['assets.php', 'Assets'],
        'kb'      => ['kb/index.php', 'Knowledge Base'],
    ];
    // kb path is one level down; build links relative to document root instead.
    $base = hot_base();
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> · Helpdesk Ops Toolkit</title>
<link rel="stylesheet" href="<?= e($base) ?>static/css/style.css">
</head>
<body>
<a class="skip" href="#main">Skip to main content</a>

<div class="gov-banner">
  <div class="wrap">
    <svg class="gov-flag" viewBox="0 0 20 14" aria-hidden="true"><rect width="20" height="14" rx="1" fill="#4b6b8a"/><rect width="20" height="2" y="2" fill="#dfe8f0"/><rect width="20" height="2" y="6" fill="#dfe8f0"/><rect width="20" height="2" y="10" fill="#dfe8f0"/><rect width="8" height="8" fill="#28425c"/></svg>
    <span>A demonstration system for a County IT Support portfolio — <strong>sample data only, not a live service.</strong></span>
  </div>
</div>

<header class="masthead">
  <div class="wrap masthead-row">
    <a class="brand" href="<?= e($base) ?>index.php">
      <svg class="brand-mark" viewBox="0 0 32 32" aria-hidden="true">
        <rect x="3" y="7" width="26" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/>
        <path d="M9 25 v3 M23 25 v3 M7 28 h18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
        <path d="M9 13 h14 M9 17 h9" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
      </svg>
      <span class="brand-text">
        <strong>Helpdesk Ops Toolkit</strong>
        <span class="brand-sub">County IT Support Services</span>
      </span>
    </a>
    <div class="masthead-auth">
      <?php if ($agent): ?>
        <span class="who">Signed in — <strong><?= e(current_agent()) ?></strong> · Support agent</span>
        <a class="btn btn--ghost btn--sm" href="<?= e($base) ?>logout.php">Sign out</a>
      <?php else: ?>
        <span class="who">Public view</span>
        <a class="btn btn--sm" href="<?= e($base) ?>login.php">Agent sign in</a>
      <?php endif; ?>
    </div>
  </div>
  <nav class="tabs" aria-label="Primary">
    <div class="wrap tabs-row">
      <?php foreach ($nav as $key => [$href, $text]):
            $agentOnly = in_array($key, ['reports', 'assets'], true);
            if ($agentOnly && !$agent) continue; ?>
        <a class="tab<?= $key === $active ? ' is-active' : '' ?>" href="<?= e($base . $href) ?>"><?= e($text) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>

<main id="main" class="wrap main">
    <?php
}

function hot_footer(): void
{
    ?>
</main>

<footer class="site-footer">
  <div class="wrap">
    <p><strong>Helpdesk Ops Toolkit</strong> — portfolio demo by
       <a href="https://github.com/cubixmeow-commits">cubixmeow-commits</a>.
       PHP 8 · MySQL · Java (JDBC). No framework, no external requests.</p>
    <p class="muted">All names, departments, and tickets are fictional sample data.</p>
  </div>
</footer>
</body>
</html>
    <?php
}

/**
 * Document-root-relative base path so links work whether the app is served
 * from "/" or a sub-folder, and from the /kb/ subdirectory. Derives the base
 * from this request's script location.
 */
function hot_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir    = str_replace('\\', '/', dirname($script));
    // If we're inside /kb, step back up to the web root.
    if (preg_match('#/kb$#', $dir)) {
        $dir = dirname($dir);
    }
    $base = rtrim($dir, '/') . '/';
    return $base === '//' ? '/' : $base;
}
