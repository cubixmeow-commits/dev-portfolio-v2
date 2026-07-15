<?php
/** @var array $player */
/** @var array $stats */
/** @var string $initials */
?>
<section class="wrap-narrow player-page">
  <header class="player-hero">
    <span class="avatar avatar--l" aria-hidden="true"><?= e($initials) ?></span>
    <div>
      <h1><?= e($player['name']) ?></h1>
      <p class="player-handle">@<?= e($player['username']) ?></p>
    </div>
  </header>

  <div class="section-head"><h2>Record</h2></div>
  <dl class="record-grid">
    <div class="record-cell">
      <dt>Series</dt>
      <dd class="t-num"><?= (int) $stats['match_wins'] ?>–<?= (int) $stats['match_losses'] ?><?= (int) $stats['match_draws'] > 0 ? '–' . (int) $stats['match_draws'] : '' ?></dd>
      <span class="record-note">W–L<?= (int) $stats['match_draws'] > 0 ? '–D' : '' ?></span>
    </div>
    <div class="record-cell">
      <dt>Daily games</dt>
      <dd class="t-num"><?= (int) $stats['daily_wins'] ?>–<?= (int) $stats['daily_losses'] ?></dd>
      <span class="record-note">W–L</span>
    </div>
    <div class="record-cell">
      <dt>Tied days</dt>
      <dd class="t-num"><?= (int) $stats['ties'] ?></dd>
      <span class="record-note">within threshold</span>
    </div>
  </dl>

  <section aria-labelledby="active-h">
    <div class="section-head"><h2 id="active-h">Active series</h2></div>
    <?php if ($stats['active_matches'] === []): ?>
      <p class="hint">No active matches.</p>
    <?php else: ?>
      <ul class="fixture-list">
        <?php foreach ($stats['active_matches'] as $row): ?>
          <li class="series-row">
            <a href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <span class="series-row-vs"><?= e($row['player_a_name']) ?> vs <?= e($row['player_b_name']) ?></span>
              <span class="series-row-meta"><?= e(ucfirst((string) $row['status'])) ?></span>
            </a>
            <span class="series-row-score t-num"><?= (int) $row['summary']['player_a_wins'] ?>–<?= (int) $row['summary']['player_b_wins'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section aria-labelledby="done-h">
    <div class="section-head"><h2 id="done-h">Completed</h2></div>
    <?php if ($stats['completed_matches'] === []): ?>
      <p class="hint">No completed matches yet.</p>
    <?php else: ?>
      <ul class="fixture-list">
        <?php foreach (array_slice($stats['completed_matches'], 0, 10) as $row): ?>
          <li class="series-row series-row--done">
            <a href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <span class="series-row-vs"><?= e($row['player_a_name']) ?> <span class="t-num"><?= (int) $row['summary']['player_a_wins'] ?>–<?= (int) $row['summary']['player_b_wins'] ?></span> <?= e($row['player_b_name']) ?></span>
            </a>
            <span class="status-pill status-<?= !empty($row['summary']['is_draw']) ? 'void' : 'official' ?>"><?= !empty($row['summary']['is_draw']) ? 'Draw' : 'Final' ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</section>
