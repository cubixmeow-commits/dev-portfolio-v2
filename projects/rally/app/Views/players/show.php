<?php
/** @var array $player */
/** @var array $stats */
/** @var string $initials */
?>
<section class="page-narrow player-page">
  <header class="player-hero">
    <div class="avatar" aria-hidden="true"><?= e($initials) ?></div>
    <h1><?= e($player['name']) ?></h1>
    <p class="lede">@<?= e($player['username']) ?></p>
  </header>

  <dl class="stat-grid">
    <div><dt>Match wins</dt><dd><?= (int) $stats['match_wins'] ?></dd></div>
    <div><dt>Match losses</dt><dd><?= (int) $stats['match_losses'] ?></dd></div>
    <div><dt>Match draws</dt><dd><?= (int) $stats['match_draws'] ?></dd></div>
    <div><dt>Daily wins</dt><dd><?= (int) $stats['daily_wins'] ?></dd></div>
    <div><dt>Daily losses</dt><dd><?= (int) $stats['daily_losses'] ?></dd></div>
    <div><dt>Tied days</dt><dd><?= (int) $stats['ties'] ?></dd></div>
  </dl>

  <section aria-labelledby="active-h">
    <h2 id="active-h">Active series</h2>
    <?php if ($stats['active_matches'] === []): ?>
      <p class="hint">No active matches.</p>
    <?php else: ?>
      <ul class="dash-list">
        <?php foreach ($stats['active_matches'] as $row): ?>
          <li>
            <a class="dash-card" href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <?= e($row['player_a_name']) ?> vs <?= e($row['player_b_name']) ?>
              · <?= (int) $row['summary']['player_a_wins'] ?>–<?= (int) $row['summary']['player_b_wins'] ?>
              · <?= e(ucfirst((string) $row['status'])) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section aria-labelledby="done-h">
    <h2 id="done-h">Completed</h2>
    <?php if ($stats['completed_matches'] === []): ?>
      <p class="hint">No completed matches yet.</p>
    <?php else: ?>
      <ul class="dash-list">
        <?php foreach (array_slice($stats['completed_matches'], 0, 10) as $row): ?>
          <li>
            <a class="dash-card" href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <?= e($row['player_a_name']) ?> <?= (int) $row['summary']['player_a_wins'] ?>–<?= (int) $row['summary']['player_b_wins'] ?> <?= e($row['player_b_name']) ?>
              <?= !empty($row['summary']['is_draw']) ? ' · Draw' : '' ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</section>
