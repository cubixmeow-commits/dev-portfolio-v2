<?php

use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$metric = MetricCompetitionService::metricPayload($m);
$isAvg = MetricCompetitionService::isSeriesAverage($m);
$status = (string) $day['status'];
$dayNum = (int) $day['day_number'];
$winnerSide = $outcome['winner_side'] ?? null;
$dayDate = (new DateTimeImmutable((string) $day['competition_date']))->format('l, F j, Y');
$score = MetricCompetitionService::scoreline($m, $seriesAsOf);
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
    <h1 class="day-title"><?= $isAvg ? 'Day' : 'Game' ?> <?= $dayNum ?></h1>
  </header>

  <div class="live-values day-values">
    <div class="live-side live-side--a">
      <p class="live-side-name"><?= e($m['player_a_name']) ?></p>
      <p class="live-side-value t-num<?= $outcome['value_a'] === null ? ' is-awaiting' : '' ?><?= $winnerSide === 'a' ? ' is-leading' : '' ?>">
        <?= $outcome['value_a'] !== null ? e(MetricFormatter::format((int) $outcome['value_a'], $metric)) : 'Awaiting sync' ?>
      </p>
    </div>
    <div class="live-mid" aria-hidden="true"><span class="t-label">vs</span></div>
    <div class="live-side live-side--b">
      <p class="live-side-name"><?= e($m['player_b_name']) ?></p>
      <p class="live-side-value t-num<?= $outcome['value_b'] === null ? ' is-awaiting' : '' ?><?= $winnerSide === 'b' ? ' is-leading' : '' ?>">
        <?= $outcome['value_b'] !== null ? e(MetricFormatter::format((int) $outcome['value_b'], $metric)) : 'Awaiting sync' ?>
      </p>
    </div>
  </div>

  <?php if ($status === 'official' && $outcome['kind'] === 'win'): ?>
    <?php $winner = $winnerSide === 'a' ? $m['player_a_name'] : $m['player_b_name']; ?>
    <p class="day-headline"><?= e($winner) ?> <?= $isAvg ? 'recorded the leading value by' : 'wins by' ?> <span class="t-num"><?= e(MetricFormatter::formatCompact((int) $outcome['margin'], $metric)) ?></span></p>
  <?php elseif ($status === 'official' && $outcome['kind'] === 'tie'): ?>
    <p class="day-headline"><?= $isAvg ? 'Within threshold' : 'Tie' ?> — difference <span class="t-num"><?= e(MetricFormatter::formatCompact((int) $outcome['margin'], $metric)) ?></span> <span class="day-headline-note">(threshold <?= (int) $m['tie_threshold'] ?>)</span></p>
  <?php elseif ($status === 'void'): ?>
    <p class="day-headline is-void">Void — no result awarded</p>
    <p class="hint">Data was missing at settlement. Void days count for neither player.</p>
  <?php elseif ($status === 'pending' || $status === 'live'): ?>
    <p class="day-headline is-open">Provisional — not yet official</p>
  <?php else: ?>
    <p class="day-headline is-open">Scheduled</p>
  <?php endif; ?>

  <div class="day-series source-strip">
    <span class="source-strip-kicker" style="color: var(--ink-2);"><?= $isAvg ? 'Series averages after this day' : 'Series after this game' ?></span>
    <span class="source-strip-pair t-num"><?= e($m['player_a_name']) ?> <?= e($score['primary']) ?> <?= e($m['player_b_name']) ?></span>
    <p class="source-strip-note"><?= $isAvg ? 'Official averages only. Daily comparisons do not decide the series.' : 'Official results only.' ?></p>
  </div>

  <?php if ($status === 'official'): ?>
    <p><a class="button button-primary" href="<?= e(url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share')) ?>">Open result card</a></p>
  <?php endif; ?>
</section>
