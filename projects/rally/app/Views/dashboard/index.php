<?php
/** @var list<array<string,mixed>> $invitations */
/** @var list<array<string,mixed>> $todayLive */
/** @var list<array<string,mixed>> $pendingOfficial */
/** @var list<array<string,mixed>> $activeSummaries */
/** @var list<array<string,mixed>> $upcoming */
/** @var list<array<string,mixed>> $recentCompleted */
/** @var bool $hasAny */
?>
<section class="dash">
  <header class="dash-header">
    <p class="eyebrow">Dashboard</p>
    <h1>Today’s competition</h1>
    <p class="lede">Series scores count daily wins — not total steps.</p>
    <p><a class="button button-primary" href="<?= e(url('/matches/create')) ?>">Create series</a></p>
  </header>

  <?php if (!$hasAny): ?>
    <div class="empty-state" role="status">
      <h2>Create your first 14-game series</h2>
      <p>Challenge a demo opponent and watch daily step contests unfold.</p>
      <p><a class="button button-primary" href="<?= e(url('/matches/create')) ?>">Start a series</a></p>
      <p class="hint">Demo login: iain@rally.demo / rally-demo-2026</p>
    </div>
  <?php endif; ?>

  <?php if ($invitations !== []): ?>
  <section class="dash-section" aria-labelledby="invites-heading">
    <h2 id="invites-heading">Invitations</h2>
    <ul class="dash-list">
      <?php foreach ($invitations as $inv): ?>
        <li>
          <a class="dash-card" href="<?= e(url('/matches/' . (int) $inv['id'] . '/accept')) ?>">
            <strong><?= e($inv['player_a_name']) ?></strong> challenged you · <?= e($inv['metric_name']) ?> · <?= e($inv['length_days']) ?> days
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if ($todayLive !== []): ?>
  <section class="dash-section" aria-labelledby="live-heading">
    <h2 id="live-heading">Live now</h2>
    <ul class="dash-list">
      <?php foreach ($todayLive as $item):
        $m = $item['match'];
        $s = $item['summary'];
        $d = $item['day'];
      ?>
        <li>
          <a class="dash-card dash-card-live" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="status-pill status-live">LIVE</span>
            <span class="dash-vs"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></span>
            <span class="dash-score" aria-label="Series score <?= (int) $s['player_a_wins'] ?> to <?= (int) $s['player_b_wins'] ?>">
              <?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?>
            </span>
            <span class="dash-meta">Game <?= (int) $d['day_number'] ?> · <?= e($m['metric_name']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if ($pendingOfficial !== []): ?>
  <section class="dash-section" aria-labelledby="pending-heading">
    <h2 id="pending-heading">Pending settlement</h2>
    <ul class="dash-list">
      <?php foreach ($pendingOfficial as $item):
        $m = $item['match'];
        $d = $item['day'];
      ?>
        <li>
          <a class="dash-card" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="status-pill status-pending">PENDING</span>
            <?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?> · Game <?= (int) $d['day_number'] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if ($activeSummaries !== []): ?>
  <section class="dash-section" aria-labelledby="active-heading">
    <h2 id="active-heading">Active series</h2>
    <ul class="dash-list">
      <?php foreach ($activeSummaries as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
      ?>
        <li>
          <a class="dash-card" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="dash-vs"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></span>
            <span class="dash-score"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></span>
            <span class="dash-meta"><?= e(ucfirst((string) $m['status'])) ?> · <?= e($m['metric_name']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if ($recentCompleted !== []): ?>
  <section class="dash-section" aria-labelledby="done-heading">
    <h2 id="done-heading">Recently completed</h2>
    <ul class="dash-list">
      <?php foreach ($recentCompleted as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
        $result = $s['is_draw'] ? 'Draw' : (($s['leader_user_id'] ?? null) ? 'Final' : 'Final');
      ?>
        <li>
          <a class="dash-card" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="status-pill status-official"><?= e($s['is_draw'] ? 'DRAW' : 'FINAL') ?></span>
            <?= e($m['player_a_name']) ?> <?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?> <?= e($m['player_b_name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>
</section>
