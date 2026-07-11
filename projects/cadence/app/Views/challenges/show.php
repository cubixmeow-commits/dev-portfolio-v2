<?php
use Cadence\Core\Csrf;
use Cadence\Models\ActivityEvent;
use Cadence\Models\Challenge;

$st = Challenge::status($challenge);
?>
<div class="challenge-hero cover-<?= e($challenge['cover_style']) ?>">
  <div class="container challenge-hero-inner">
    <div class="challenge-meta">
      <span class="pill pill-on-cover"><?= e(ucfirst((string) $challenge['category'])) ?></span>
      <?php if ($st === 'active'): ?>
        <span class="pill pill-on-cover"><?= e((string) Challenge::daysLeft($challenge)) ?> days left</span>
      <?php elseif ($st === 'upcoming'): ?>
        <span class="pill pill-on-cover">Starts <?= e(date('M j', strtotime((string) $challenge['start_date']))) ?></span>
      <?php else: ?>
        <span class="pill pill-on-cover">Ended <?= e(date('M j', strtotime((string) $challenge['end_date']))) ?></span>
      <?php endif; ?>
    </div>
    <h1><?= e($challenge['title']) ?></h1>
    <p class="challenge-hero-stats">
      <span><span class="num"><?= fmt_int($challenge['participant_count']) ?></span> members</span>
      <span><span class="num"><?= e(date('M j', strtotime((string) $challenge['start_date']))) ?></span> to <span class="num"><?= e(date('M j', strtotime((string) $challenge['end_date']))) ?></span></span>
      <span><span class="num"><?= e((string) $challenge['points_per_checkin']) ?></span> points per check-in</span>
    </p>
  </div>
</div>

<div class="container challenge-layout">
  <div class="challenge-main">

    <?php if ($auth !== null && $participation !== null && $st === 'active'): ?>
      <section class="card card-pad checkin-module" id="checkin-module"
               data-checkin-url="<?= e(url('/challenges/' . $challenge['slug'] . '/checkin')) ?>"
               data-csrf="<?= e(Csrf::token()) ?>">
        <?php if ($checkedInToday): ?>
          <div class="checkin-done" id="checkin-state">
            <h3>Checked in for today</h3>
            <p class="muted small">Day <span class="num"><?= e((string) $participation['current_streak']) ?></span> of your streak. Come back tomorrow.</p>
          </div>
        <?php else: ?>
          <div id="checkin-state">
            <h3>Ready for today?</h3>
            <form method="post" action="<?= e(url('/challenges/' . $challenge['slug'] . '/checkin')) ?>" id="checkin-form" style="margin-top: var(--sp-3)">
              <?= Csrf::field() ?>
              <div class="field" style="margin-bottom: var(--sp-3)">
                <label class="visually-hidden" for="note">Note (optional)</label>
                <input class="input" type="text" id="note" name="note" maxlength="200"
                       placeholder="Optional note: how did it go?">
              </div>
              <button class="btn btn-primary btn-lg" type="submit" id="checkin-button">Check in</button>
            </form>
          </div>
        <?php endif; ?>
        <div class="checkin-streak">
          <span class="streak-number num"><?= e((string) $participation['current_streak']) ?></span>
          <span class="muted small">day streak</span>
          <span class="muted small">best <span class="num"><?= e((string) $participation['longest_streak']) ?></span></span>
        </div>
      </section>
    <?php endif; ?>

    <section class="card card-pad">
      <h2 style="font-size: var(--text-lg)">About this challenge</h2>
      <p style="margin-top: var(--sp-3); white-space: pre-line"><?= e($challenge['description']) ?></p>

      <div class="challenge-actions">
        <?php if ($auth === null): ?>
          <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Sign in to join</a>
        <?php elseif ($participation === null && $st !== 'ended'): ?>
          <form method="post" action="<?= e(url('/challenges/' . $challenge['slug'] . '/join')) ?>">
            <?= Csrf::field() ?>
            <button class="btn btn-primary" type="submit">Join challenge</button>
          </form>
        <?php elseif ($participation !== null): ?>
          <form method="post" action="<?= e(url('/challenges/' . $challenge['slug'] . '/leave')) ?>"
                data-confirm="Leave this challenge? Your streak and its points are removed.">
            <?= Csrf::field() ?>
            <button class="btn btn-danger" type="submit">Leave challenge</button>
          </form>
        <?php endif; ?>
      </div>
    </section>

    <?php if ($history !== []): ?>
      <section class="card card-pad">
        <h2 style="font-size: var(--text-lg)">Your recent check-ins</h2>
        <div class="table-wrap" style="margin-top: var(--sp-3)">
          <table class="table">
            <thead><tr><th>Date</th><th>Note</th><th>Points</th></tr></thead>
            <tbody>
              <?php foreach ($history as $h): ?>
                <tr>
                  <td class="num"><?= e(date('D, M j', strtotime((string) $h['checkin_date']))) ?></td>
                  <td><?= $h['note'] !== null ? e($h['note']) : '<span class="muted">-</span>' ?></td>
                  <td class="num">+<?= e((string) $h['points_awarded']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </div>

  <aside class="challenge-side">
    <section class="card card-pad">
      <h2 class="section-title">Leaderboard</h2>
      <?php if ($leaders === []): ?>
        <p class="muted small">No members yet. Be the first to join.</p>
      <?php else: ?>
        <ol class="leader-list">
          <?php foreach ($leaders as $i => $l): ?>
            <li class="leader-row">
              <span class="leader-rank num"><?= $i + 1 ?></span>
              <?php Cadence\Core\View::partial('layout/avatar', ['user' => $l, 'size' => 28, 'ring' => null]); ?>
              <a class="leader-name" href="<?= e(url('/u/' . $l['handle'])) ?>"><?= e($l['display_name']) ?></a>
              <span class="leader-points num"><?= fmt_int($l['points']) ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </section>

    <section class="card card-pad">
      <h2 class="section-title">Recent activity</h2>
      <?php if ($events === []): ?>
        <p class="muted small">Quiet so far. Activity shows up as members check in.</p>
      <?php else: ?>
        <ul class="mini-feed">
          <?php foreach ($events as $ev): ?>
            <li class="mini-feed-row">
              <?php Cadence\Core\View::partial('layout/avatar', ['user' => $ev, 'size' => 24, 'ring' => null]); ?>
              <div>
                <span class="small"><?= ActivityEvent::sentence($ev) ?></span>
                <span class="muted small" style="display:block"><?= e(time_ago((string) $ev['created_at'])) ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </aside>
</div>
<script src="<?= e(url('/assets/js/checkin.js')) ?>" defer></script>
