<?php

use SousMeow\Core\Auth;
use SousMeow\Core\Csrf;
use SousMeow\Core\Flash;

/**
 * @var string      $content   Rendered page body.
 * @var string|null $title     Page title.
 * @var string|null $bodyClass Extra class on <body>.
 * @var list<string>|string|null $pageCss Page stylesheet names under css/pages/.
 */
$title = isset($title) && $title !== '' ? $title . ' · SousMeow' : 'SousMeow · Finish what AI started';
$pageCssList = isset($pageCss) ? (array) $pageCss : [];
$flash = Flash::pull();
$isAdmin = ($auth['role'] ?? '') === 'admin';
$needsVerification = $auth && !Auth::isVerified();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="SousMeow helps when AI won't give you exactly what you meant. Guided steps, the assistant you already use, finished files — without becoming a prompt expert.">
<link rel="icon" href="<?= e(asset('/assets/img/favicon.svg')) ?>" type="image/svg+xml">
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
    <a class="brand" href="<?= e(url($auth ? '/kitchen' : '/')) ?>" aria-label="SousMeow home">
      <?php \SousMeow\Core\View::partial('partials/logo'); ?>
      <span class="brand-name">SousMeow</span>
    </a>
    <nav class="site-nav" aria-label="Main">
      <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">
        <span class="nav-toggle-bar"></span><span class="nav-toggle-bar"></span>
        <span class="visually-hidden">Menu</span>
      </button>
      <div class="nav-links" id="nav-links">
        <?php if ($auth): ?>
          <a href="<?= e(url('/kitchen')) ?>">My projects</a>
          <a href="<?= e(url('/marketplace')) ?>">Find something</a>
          <a href="<?= e(url('/account')) ?>">Account</a>
          <?php if ($isAdmin): ?><a href="<?= e(url('/admin')) ?>">Admin</a><?php endif; ?>
          <form method="post" action="<?= e(url('/logout')) ?>" class="nav-logout">
            <?= Csrf::field() ?>
            <button type="submit" class="link-button">Sign out</button>
          </form>
          <span class="nav-user" title="Signed in as <?= e($auth['email']) ?>"><?= e($auth['name']) ?></span>
        <?php else: ?>
          <a href="<?= e(url('/#how-it-works')) ?>">How it works</a>
          <a href="<?= e(url('/marketplace')) ?>">Find something</a>
          <a href="<?= e(url('/login')) ?>">Sign in</a>
          <a class="button button-primary button-small" href="<?= e(url('/register')) ?>">Start free</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<?php if ($needsVerification): ?>
<div class="verify-banner" role="status">
  Verify your email to start a project and export finished files.
  <a href="<?= e(url('/verify-email/pending')) ?>">Resend verification</a>
</div>
<?php endif; ?>

<?php if ($flash !== null): ?>
  <div class="flash flash-<?= e($flash['type']) ?>" role="status" data-flash <?= $flash['type'] === 'celebrate' ? 'data-celebrate' : '' ?>>
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
    <p class="footer-note">
      <?php \SousMeow\Core\View::partial('partials/logo'); ?>
      SousMeow is a portfolio demonstration build. No payments are collected and no AI is called;
      you bring your own assistant.
    </p>
    <nav class="footer-links" aria-label="Footer">
      <a href="<?= e(url('/#how-it-works')) ?>">How it works</a>
      <a href="<?= e(url('/marketplace')) ?>">Find something</a>
      <a href="<?= e(url('/categories')) ?>">Browse by topic</a>
      <a href="<?= e(url('/terms')) ?>">Terms</a>
      <a href="<?= e(url('/privacy')) ?>">Privacy</a>
      <?php if (!$auth): ?><a href="<?= e(url('/register')) ?>">Start free</a><?php endif; ?>
    </nav>
  </div>
</footer>

<script src="<?= e(asset('/assets/js/app.js')) ?>" defer></script>
<?php if (!empty($pageJs)): foreach ((array) $pageJs as $js): ?>
<script src="<?= e(asset('/assets/js/' . $js . '.js')) ?>" defer></script>
<?php endforeach; endif; ?>
</body>
</html>
