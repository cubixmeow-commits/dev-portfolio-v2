<?php
use Cadence\Core\Csrf;
use Cadence\Models\ActivityEvent;
?>
<div class="container">
  <div class="page-head">
    <div>
      <h1>Today</h1>
      <p>
        <?php if ($activeChallenges === []): ?>
          Welcome back, <?= e($user['display_name']) ?>.
        <?php elseif ($todayDone === count($activeChallenges)): ?>
          All <?= count($activeChallenges) ?> check-in<?= count($activeChallenges) === 1 ? '' : 's' ?> done. Full ring.
        <?php else: ?>
          <?= count($activeChallenges) - $todayDone ?> of <?= count($activeChallenges) ?> check-ins left today.
        <?php endif; ?>
      </p>
    </div>
  </div>

  <div class="dash-grid">
    <div class="dash-main">
      <section>
        <h2 class="section-title">Your challenges</h2>
        <?php if ($activeChallenges === []): ?>
          <div class="card">
            <div class="empty">
              <h3>No active challenges</h3>
              <p>Join one and your daily check-ins will live here.</p>
              <p style="margin-top: var(--sp-4)"><a class="btn btn-primary" href="<?= e(url('/challenges')) ?>">Explore challenges</a></p>
            </div>
          </div>
        <?php else: ?>
          <div class="today-grid">
            <?php foreach ($activeChallenges as $c): ?>
              <?php $done = $c['last_checkin_date'] === $today; ?>
              <div class="card today-card<?= $done ? ' today-card-done' : '' ?>" data-slug="<?= e($c['slug']) ?>">
                <div class="today-card-top">
                  <span class="cover-dot cover-<?= e($c['cover_style']) ?>" aria-hidden="true"></span>
                  <a class="today-card-title" href="<?= e(url('/challenges/' . $c['slug'])) ?>"><?= e($c['title']) ?></a>
                </div>
                <div class="today-card-streak">
                  <span class="num streak-inline<?= $done ? ' streak-inline-done' : '' ?>" data-role="streak"><?= e((string) $c['current_streak']) ?></span>
                  <span class="muted small">day streak</span>
                </div>
                <div class="today-card-action" data-role="action">
                  <?php if ($done): ?>
                    <span class="pill pill-accent">Done for today</span>
                  <?php else: ?>
                    <form method="post" action="<?= e(url('/challenges/' . $c['slug'] . '/checkin')) ?>" class="dash-checkin-form" data-slug="<?= e($c['slug']) ?>">
                      <?= Csrf::field() ?>
                      <button class="btn btn-primary" type="submit">Check in</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <section>
        <h2 class="section-title">Recent activity</h2>
        <div class="card card-pad">
          <?php if ($events === []): ?>
            <p class="muted small">Your check-ins, joins, and badges will show here.</p>
          <?php else: ?>
            <ul class="mini-feed">
              <?php foreach ($events as $ev): ?>
                <li class="mini-feed-row">
                  <div>
                    <span class="small"><?= ActivityEvent::sentence($ev) ?></span>
                    <span class="muted small" style="display:block"><?= e(time_ago((string) $ev['created_at'])) ?></span>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </section>
    </div>

    <aside class="dash-side">
      <section class="card card-pad">
        <h2 class="section-title">This week</h2>
        <p class="stat-value num" style="font-size: var(--text-2xl)"><?= fmt_int($weekPoints) ?></p>
        <p class="muted small">points since Monday</p>

        <?php
          // Inline SVG sparkline: 14 days of points, no chart library.
          $w = 260; $h = 56; $pad = 4;
          $max = max(1, max(array_column($spark, 'points')));
          $n = count($spark);
          $pts = [];
          foreach ($spark as $i => $day) {
              $x = $pad + ($w - 2 * $pad) * ($n > 1 ? $i / ($n - 1) : 0);
              $y = $h - $pad - ($h - 2 * $pad) * ($day['points'] / $max);
              $pts[] = round($x, 1) . ',' . round($y, 1);
          }
        ?>
        <svg class="sparkline" viewBox="0 0 <?= $w ?> <?= $h ?>" width="100%" height="<?= $h ?>" role="img"
             aria-label="Points per day over the last 14 days">
          <polyline fill="none" stroke="var(--accent)" stroke-width="2" stroke-linejoin="round"
                    stroke-linecap="round" points="<?= implode(' ', $pts) ?>"></polyline>
          <?php $lastPt = explode(',', end($pts)); ?>
          <circle cx="<?= e($lastPt[0]) ?>" cy="<?= e($lastPt[1]) ?>" r="3" fill="var(--accent)"></circle>
        </svg>
        <p class="muted small sparkline-range">
          <span><?= e(date('M j', strtotime($spark[0]['date']))) ?></span>
          <span>today</span>
        </p>

        <?php if ($bestStreak > 0): ?>
          <hr class="rule">
          <p><span class="stat-value num" style="font-size: var(--text-xl)"><?= fmt_int($bestStreak) ?></span></p>
          <p class="muted small">best current streak</p>
        <?php endif; ?>
      </section>

      <section class="card card-pad">
        <h2 class="section-title">Recent badges</h2>
        <?php if ($recentBadges === []): ?>
          <p class="muted small">No badges yet. Your first check-in earns one.</p>
        <?php else: ?>
          <ul class="badge-grid">
            <?php foreach ($recentBadges as $b): ?>
              <li class="badge-tile" title="<?= e($b['description']) ?>">
                <span class="badge-icon badge-icon-<?= e($b['icon']) ?>" aria-hidden="true"></span>
                <span class="badge-name"><?= e($b['name']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          <p class="small" style="margin-top: var(--sp-3)"><a href="<?= e(url('/u/' . $user['handle'])) ?>">See your full badge case</a></p>
        <?php endif; ?>
      </section>

      <section class="card card-pad">
        <h2 class="section-title">Account</h2>
        <div style="display: flex; gap: var(--sp-3); flex-wrap: wrap">
          <a class="btn btn-secondary" href="<?= e(url('/settings')) ?>">Settings</a>
          <form method="post" action="<?= e(url('/logout')) ?>">
            <?= Csrf::field() ?>
            <button class="btn btn-quiet" type="submit">Sign out</button>
          </form>
        </div>
      </section>
    </aside>
  </div>
</div>
<script src="<?= e(url('/assets/js/dashboard.js')) ?>" defer></script>
