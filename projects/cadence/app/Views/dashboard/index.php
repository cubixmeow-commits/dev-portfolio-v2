<?php /* Stage 1 placeholder. Stage 4 replaces this with the Today panel, sparkline, and badges. */ ?>
<?php use Cadence\Core\Csrf; ?>
<div class="container">
  <div class="page-head">
    <div>
      <h1>Today</h1>
      <p>Welcome back, <?= e($user['display_name']) ?>.</p>
    </div>
    <div style="display: flex; gap: var(--sp-3)">
      <a class="btn btn-secondary" href="<?= e(url('/settings')) ?>">Settings</a>
      <form method="post" action="<?= e(url('/logout')) ?>">
        <?= Csrf::field() ?>
        <button class="btn btn-quiet" type="submit">Sign out</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="empty">
      <h3>No check-ins yet today</h3>
      <p>Challenges are coming in the next build stage. Your streaks will live here.</p>
    </div>
  </div>
</div>
