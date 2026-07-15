<?php

use Rally\Core\Auth;
use Rally\Core\Csrf;
use Rally\Core\Flash;

/**
 * @var string $content
 * @var string|null $title
 * @var string|null $bodyClass
 * @var list<string>|string|null $pageCss
 * @var list<string>|string|null $pageJs
 */
$title = isset($title) && $title !== '' ? $title . ' · Rally' : 'Rally · Health stats competition';
$pageCssList = isset($pageCss) ? (array) $pageCss : [];
$pageJsList = isset($pageJs) ? (array) $pageJs : [];
$flash = Flash::pull();
$canSim = Auth::check() && Auth::canSimulate();
$currentPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$navCurrent = static function (string $path, bool $exact = false) use ($currentPath): string {
    $target = url($path);
    $match = $exact ? $currentPath === $target : str_starts_with($currentPath, $target);
    return $match ? ' aria-current="page"' : '';
};
$bodyClasses = trim(($bodyClass ?? '') . ($auth ? ' has-bottom-nav' : ''));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="Rally turns wearable health stats into head-to-head sports series. Every day is a new game.">
<meta name="theme-color" content="#0b0c0e">
<link rel="icon" href="<?= e(asset('/assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preload" href="<?= e(asset('/assets/fonts/archivo-var-latin.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= e(asset('/assets/fonts/inter-var-latin.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('/assets/css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/tokens.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/components.css')) ?>">
<?php foreach ($pageCssList as $css): ?>
<link rel="stylesheet" href="<?= e(asset('/assets/css/pages/' . $css . '.css')) ?>">
<?php endforeach; ?>
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
</head>
<body class="<?= e($bodyClasses) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header">
  <div class="site-header-inner">
    <a class="brand" href="<?= e(url($auth ? '/dashboard' : '/')) ?>" aria-label="Rally home">
      <span class="brand-mark" aria-hidden="true"></span>
      <span class="brand-name">Rally</span>
    </a>
    <nav class="site-nav" aria-label="Main">
      <?php if ($auth): ?>
        <div class="nav-links nav-links--primary">
          <a href="<?= e(url('/dashboard')) ?>"<?= $navCurrent('/dashboard') ?>>Dashboard</a>
          <a href="<?= e(url('/matches')) ?>"<?= $navCurrent('/matches', true) ?>>Matches</a>
          <a href="<?= e(url('/matches/create')) ?>"<?= $navCurrent('/matches/create') ?>>New series</a>
          <a href="<?= e(url('/players/' . (int) $auth['id'])) ?>"<?= $navCurrent('/players/' . (int) $auth['id']) ?>>Profile</a>
          <?php if ($canSim): ?><a href="<?= e(url('/simulation')) ?>"<?= $navCurrent('/simulation') ?>>Simulation</a><?php endif; ?>
          <form method="post" action="<?= e(url('/logout')) ?>" class="nav-logout">
            <?= Csrf::field() ?>
            <button type="submit" class="link-button">Sign out</button>
          </form>
        </div>
        <div class="nav-menu nav-menu--authed">
          <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-menu-panel">
            <span class="nav-toggle-bar"></span><span class="nav-toggle-bar"></span>
            <span class="visually-hidden">Account menu</span>
          </button>
          <div class="nav-menu-panel" id="nav-menu-panel">
            <span class="nav-user"><?= e($auth['name']) ?></span>
            <?php if ($canSim): ?><a href="<?= e(url('/simulation')) ?>">Simulation</a><?php endif; ?>
            <form method="post" action="<?= e(url('/logout')) ?>" class="nav-logout">
              <?= Csrf::field() ?>
              <button type="submit" class="link-button">Sign out</button>
            </form>
          </div>
        </div>
      <?php else: ?>
        <div class="nav-links">
          <a href="<?= e(url('/login')) ?>">Sign in</a>
          <a class="button button-primary button-small" href="<?= e(url('/register')) ?>">Join</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>

<?php if ($flash !== null): ?>
  <div class="flash flash-<?= e($flash['type']) ?>" role="status" data-flash>
    <div class="flash-inner">
      <span class="flash-message"><?= e($flash['message']) ?></span>
      <button type="button" class="flash-dismiss" data-flash-dismiss aria-label="Dismiss">&times;</button>
    </div>
  </div>
<?php endif; ?>

<main id="main" class="site-main">
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="site-footer-inner">
    <p class="footer-note">Rally is a competition engine for wearable data — not a fitness tracker.</p>
  </div>
</footer>

<?php if ($auth): ?>
<nav class="bottom-nav" aria-label="Primary">
  <a href="<?= e(url('/dashboard')) ?>"<?= $navCurrent('/dashboard') ?>>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3.5 10.5 12 3.5l8.5 7v9a1 1 0 0 1-1 1h-5v-6h-5v6h-5a1 1 0 0 1-1-1z"/></svg>
    <span>Today</span>
  </a>
  <a href="<?= e(url('/matches')) ?>"<?= $navCurrent('/matches', true) ?>>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12h19"/><path d="M5 12V6.5h4V12M10 12v5.5h4V12M15 12V7.5h4V12" fill="currentColor" stroke="none"/></svg>
    <span>Matches</span>
  </a>
  <a href="<?= e(url('/matches/create')) ?>"<?= $navCurrent('/matches/create') ?>>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 8v8M8 12h8"/></svg>
    <span>Create</span>
  </a>
  <a href="<?= e(url('/players/' . (int) $auth['id'])) ?>"<?= $navCurrent('/players/' . (int) $auth['id']) ?>>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8.5" r="3.5"/><path d="M4.5 20c1.5-3.6 4.2-5 7.5-5s6 1.4 7.5 5"/></svg>
    <span>Profile</span>
  </a>
</nav>
<?php endif; ?>

<script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
<?php foreach ($pageJsList as $js): ?>
<script src="<?= e(asset('/assets/js/' . $js . '.js')) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
