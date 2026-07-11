<?php
/**
 * Global layout. Expects: $title, $content, $auth (user row or null).
 * Optional: $active (nav key), $page_css (extra stylesheet name under
 * assets/css/pages/), $body_class.
 */
use Cadence\Core\Flash;

$active = $active ?? '';
// Nav streak ring: today's check-in completion across active challenges.
if (!isset($ring_today) && $auth !== null) {
    $ring_today = Cadence\Models\Participation::ringToday((int) $auth['id'], (string) $auth['timezone']);
}
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

<?php if ($auth === null): ?>
  <div class="demo-ribbon" id="demo-ribbon" hidden>
    <div class="container demo-ribbon-inner">
      <span>This is a live demo. Explore freely; everything you see is real seeded data.</span>
      <span class="demo-ribbon-actions">
        <form method="post" action="<?= e(url('/demo-login')) ?>">
          <?= Cadence\Core\Csrf::field() ?>
          <button class="btn btn-primary" type="submit">Sign in as demo member</button>
        </form>
        <button class="btn btn-quiet" type="button" data-dismiss-remember="demo-ribbon" aria-label="Dismiss demo notice">Dismiss</button>
      </span>
    </div>
  </div>
<?php endif; ?>

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
        <?php if ($auth['role'] === 'admin'): ?>
          <a href="<?= e(url('/admin')) ?>" class="nav-link">Admin</a>
        <?php endif; ?>
        <a href="<?= e(url('/dashboard')) ?>" class="nav-link"<?= $active === 'dashboard' ? ' aria-current="page"' : '' ?>>Today</a>
        <?php $unread = Cadence\Models\Notification::unreadCount((int) $auth['id']); ?>
        <a class="bell" href="<?= e(url('/notifications')) ?>" aria-label="Notifications<?= $unread > 0 ? ', ' . $unread . ' unread' : '' ?>">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
          </svg>
          <?php if ($unread > 0): ?><span class="bell-count"><?= $unread > 9 ? '9+' : $unread ?></span><?php endif; ?>
        </a>
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
