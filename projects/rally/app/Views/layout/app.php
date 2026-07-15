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
$isAdmin = ($auth['role'] ?? '') === 'admin';
$canSim = Auth::check() && Auth::canSimulate();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="Rally turns wearable health stats into head-to-head sports series. Every day is a new game.">
<link rel="icon" href="<?= e(asset('/assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('/assets/css/tokens.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/base.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/components.css')) ?>">
<?php foreach ($pageCssList as $css): ?>
<link rel="stylesheet" href="<?= e(asset('/assets/css/pages/' . $css . '.css')) ?>">
<?php endforeach; ?>
<meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header">
  <div class="site-header-inner">
    <a class="brand" href="<?= e(url($auth ? '/dashboard' : '/')) ?>" aria-label="Rally home">
      <span class="brand-mark" aria-hidden="true"></span>
      <span class="brand-name">Rally</span>
    </a>
    <nav class="site-nav" aria-label="Main">
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">
        <span class="nav-toggle-bar"></span><span class="nav-toggle-bar"></span>
        <span class="visually-hidden">Menu</span>
      </button>
      <div class="nav-links" id="nav-links">
        <?php if ($auth): ?>
          <a href="<?= e(url('/dashboard')) ?>">Dashboard</a>
          <a href="<?= e(url('/matches')) ?>">Matches</a>
          <a href="<?= e(url('/matches/create')) ?>">New series</a>
          <a href="<?= e(url('/players/' . (int) $auth['id'])) ?>">Profile</a>
          <?php if ($canSim): ?><a href="<?= e(url('/simulation')) ?>">Simulation</a><?php endif; ?>
          <form method="post" action="<?= e(url('/logout')) ?>" class="nav-logout">
            <?= Csrf::field() ?>
            <button type="submit" class="link-button">Sign out</button>
          </form>
          <span class="nav-user"><?= e($auth['name']) ?></span>
        <?php else: ?>
          <a href="<?= e(url('/login')) ?>">Sign in</a>
          <a class="button button-primary button-small" href="<?= e(url('/register')) ?>">Join</a>
        <?php endif; ?>
      </div>
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

<script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
<?php foreach ($pageJsList as $js): ?>
<script src="<?= e(asset('/assets/js/' . $js . '.js')) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
