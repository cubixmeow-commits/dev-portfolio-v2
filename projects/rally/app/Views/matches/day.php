<?php
/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$status = (string) $day['status'];
$dayNum = (int) $day['day_number'];
$winnerSide = $outcome['winner_side'] ?? null;
$dayDate = (new DateTimeImmutable((string) $day['competition_date']))->format('l, F j, Y');
?>
<section class="wrap-narrow day-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">← Back to match</a></p>
    <div class="board-meta">
      <span class="t-label"><?= e($m['metric_name']) ?></span>
      <span class="t-label" aria-hidden="true">·</span>
      <span class="t-label t-num"><?= e($dayDate) ?></span>
      <span class="board-meta-spacer"></span>
      <span class="status-pill status-<?= e($status) ?>"><?= e(ucfirst($status)) ?></span>
    </div>
    <h1 class="day-title">Game <?= $dayNum ?></h1>
  </header>

  <div class="live-values day-values">
    <div class="live-side live-side--a">
      <p class="live-side-name"><?= e($m['player_a_name']) ?></p>
      <p class="live-side-value t-num<?= $outcome['value_a'] === null ? ' is-awaiting' : '' ?><?= $winnerSide === 'a' ? ' is-leading' : '' ?>">
        <?= $outcome['value_a'] !== null ? e(number_format((int) $outcome['value_a'])) : 'Awaiting sync' ?>
      </p>
    </div>
    <div class="live-mid" aria-hidden="true"><span class="t-label">vs</span></div>
    <div class="live-side live-side--b">
      <p class="live-side-name"><?= e($m['player_b_name']) ?></p>
      <p class="live-side-value t-num<?= $outcome['value_b'] === null ? ' is-awaiting' : '' ?><?= $winnerSide === 'b' ? ' is-leading' : '' ?>">
        <?= $outcome['value_b'] !== null ? e(number_format((int) $outcome['value_b'])) : 'Awaiting sync' ?>
      </p>
    </div>
  </div>

  <?php if ($status === 'official' && $outcome['kind'] === 'win'): ?>
    <?php $winner = $winnerSide === 'a' ? $m['player_a_name'] : $m['player_b_name']; ?>
    <p class="day-headline"><?= e($winner) ?> wins by <span class="t-num"><?= e(number_format((int) $outcome['margin'])) ?></span></p>
  <?php elseif ($status === 'official' && $outcome['kind'] === 'tie'): ?>
    <p class="day-headline">Tie — difference <span class="t-num"><?= e(number_format((int) $outcome['margin'])) ?></span> <span class="day-headline-note">(threshold <?= (int) $m['tie_threshold'] ?>)</span></p>
  <?php elseif ($status === 'void'): ?>
    <p class="day-headline is-void">Void — no win awarded</p>
    <p class="hint">Data was missing at settlement. Void games count for neither player.</p>
  <?php elseif ($status === 'pending' || $status === 'live'): ?>
    <p class="day-headline is-open">Provisional — not yet official</p>
  <?php else: ?>
    <p class="day-headline is-open">Scheduled</p>
  <?php endif; ?>

  <div class="day-series source-strip">
    <span class="source-strip-kicker" style="color: var(--ink-2);">Series after this game</span>
    <span class="source-strip-pair t-num"><?= e($m['player_a_name']) ?> <?= (int) $seriesAsOf['player_a_wins'] ?>–<?= (int) $seriesAsOf['player_b_wins'] ?> <?= e($m['player_b_name']) ?></span>
    <p class="source-strip-note">Official results only.</p>
  </div>

  <?php if ($status === 'official'): ?>
    <p><a class="button button-primary" href="<?= e(url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share')) ?>">Open result card</a></p>
  <?php endif; ?>
</section>
