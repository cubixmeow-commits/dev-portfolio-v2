<?php
/**
 * Global layout. Expects: $title, $content, $auth (user row or null).
 * Optional: $active (nav key), $page_css (extra stylesheet name under
 * assets/css/pages/), $body_class.
 */
use Cadence\Core\Flash;

$active = $active ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(isset($title) && $title !== '' ? $title . ' · Cadence' : 'Cadence · Build habits together') ?></title>
  <meta name="description" content="Cadence is where habits get built together: join challenges, keep streaks, earn badges.">
  <link rel="icon" href="<?= e(url('/assets/img/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= e(url('/assets/css/tokens.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('/assets/css/base.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('/assets/css/components.css')) ?>">
  <?php if (!empty($page_css)): ?>
    <link rel="stylesheet" href="<?= e(url('/assets/css/pages/' . $page_css . '.css')) ?>">
  <?php endif; ?>
</head>
<body<?= !empty($body_class) ? ' class="' . e($body_class) . '"' : '' ?>>

<a class="visually-hidden" href="#main">Skip to content</a>

<nav class="nav" aria-label="Main">
  <div class="container nav-inner">
    <a class="wordmark" href="<?= e(url('/')) ?>"><span class="wordmark-dot" aria-hidden="true"></span>Cadence</a>

    <div class="nav-links">
      <a class="nav-link" href="<?= e(url('/challenges')) ?>"<?= $active === 'challenges' ? ' aria-current="page"' : '' ?>>Challenges</a>
      <a class="nav-link" href="<?= e(url('/feed')) ?>"<?= $active === 'feed' ? ' aria-current="page"' : '' ?>>Feed</a>
      <a class="nav-link" href="<?= e(url('/leaderboard')) ?>"<?= $active === 'leaderboard' ? ' aria-current="page"' : '' ?>>Leaderboard</a>
    </div>

    <div class="nav-actions">
      <?php if ($auth !== null): ?>
        <a href="<?= e(url('/dashboard')) ?>" class="nav-link"<?= $active === 'dashboard' ? ' aria-current="page"' : '' ?>>Today</a>
        <a href="<?= e(url('/u/' . $auth['handle'])) ?>" aria-label="Your profile">
          <?php Cadence\Core\View::partial('layout/avatar', ['user' => $auth, 'size' => 36, 'ring' => $ring_today ?? null]); ?>
        </a>
      <?php else: ?>
        <a class="btn btn-quiet" href="<?= e(url('/login')) ?>">Sign in</a>
        <a class="btn btn-primary" href="<?= e(url('/register')) ?>">Create your account</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main id="main">
  <?php $flashes = Flash::pull(); ?>
  <?php if ($flashes !== []): ?>
    <div class="container flash-stack" role="status">
      <?php foreach ($flashes as $f): ?>
        <div class="flash flash-<?= e($f['level']) ?>"><?= e($f['message']) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($auth !== null && $auth['email_verified_at'] === null && ($active ?? '') !== 'settings' && empty($hide_verify_banner)): ?>
    <div class="container" style="margin-top: var(--sp-4)">
      <div class="flash flash-info" style="display: flex; justify-content: space-between; gap: var(--sp-3); flex-wrap: wrap">
        <span>Verify your email to secure your account. The link is in your inbox.</span>
        <a href="<?= e(url('/settings')) ?>">Resend from settings</a>
      </div>
    </div>
  <?php endif; ?>

  <?= $content ?>
</main>

<script src="<?= e(url('/assets/js/app.js')) ?>" defer></script>

<footer class="footer">
  <div class="container footer-inner">
    <span>Cadence. Build habits together.</span>
    <span class="small">A portfolio build: PHP, MySQL, and a Java ops engine. No frameworks.</span>
  </div>
</footer>

</body>
</html>
