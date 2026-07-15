<?php
/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$status = (string) $day['status'];
$dayNum = (int) $day['day_number'];
?>
<section class="page-narrow day-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">← Match</a></p>
    <h1>Game <?= $dayNum ?></h1>
    <p><span class="status-pill status-<?= e($status) ?>"><?= e(strtoupper($status)) ?></span> · <?= e($day['competition_date']) ?></p>
  </header>

  <div class="day-values">
    <div>
      <p class="today-name"><?= e($m['player_a_name']) ?></p>
      <p class="today-value"><?= $outcome['value_a'] !== null ? e(number_format((int) $outcome['value_a'])) : 'Awaiting sync' ?></p>
    </div>
    <div>
      <p class="today-name"><?= e($m['player_b_name']) ?></p>
      <p class="today-value"><?= $outcome['value_b'] !== null ? e(number_format((int) $outcome['value_b'])) : 'Awaiting sync' ?></p>
    </div>
  </div>

  <?php if ($status === 'official' && $outcome['kind'] === 'win'): ?>
    <?php $winner = ($outcome['winner_side'] ?? '') === 'a' ? $m['player_a_name'] : $m['player_b_name']; ?>
    <p class="day-headline"><?= e($winner) ?> wins by <?= e(number_format((int) $outcome['margin'])) ?></p>
  <?php elseif ($status === 'official' && $outcome['kind'] === 'tie'): ?>
    <p class="day-headline">Tie · difference <?= e(number_format((int) $outcome['margin'])) ?> (threshold <?= (int) $m['tie_threshold'] ?>)</p>
  <?php elseif ($status === 'void'): ?>
    <p class="day-headline">Void · no win awarded (missing data at settlement)</p>
  <?php elseif ($status === 'pending' || $status === 'live'): ?>
    <p class="day-headline">Provisional · not yet official</p>
  <?php else: ?>
    <p class="day-headline">Scheduled</p>
  <?php endif; ?>

  <p>Series after this game (official only): <?= e($m['player_a_name']) ?> <?= (int) $seriesAsOf['player_a_wins'] ?>–<?= (int) $seriesAsOf['player_b_wins'] ?> <?= e($m['player_b_name']) ?></p>

  <?php if ($status === 'official'): ?>
    <p><a class="button button-primary" href="<?= e(url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share')) ?>">Open share card</a></p>
  <?php endif; ?>
</section>
