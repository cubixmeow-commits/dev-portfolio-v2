<?php

use Rally\Services\BaselineCompetitionService;
use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$metric = MetricCompetitionService::metricPayload($m);
$isAvg = MetricCompetitionService::isSeriesAverage($m);
$isBaseline = MetricCompetitionService::isBaseline($m);
$ctype = MetricCompetitionService::competitionType($m);
$dayNum = (int) $day['day_number'];
$isTie = $outcome['kind'] === 'tie';
$winnerSide = $outcome['winner_side'] ?? null;
$winner = $winnerSide === 'a' ? $m['player_a_name'] : ($winnerSide === 'b' ? $m['player_b_name'] : null);
$loser = $winnerSide === 'a' ? $m['player_b_name'] : ($winnerSide === 'b' ? $m['player_a_name'] : null);
$dateLabel = (new DateTimeImmutable((string) $day['competition_date']))->format('F j, Y');
$shareUrl = full_url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share');
$valA = MetricFormatter::format((int) ($outcome['value_a'] ?? 0), $metric);
$valB = MetricFormatter::format((int) ($outcome['value_b'] ?? 0), $metric);
$dayWord = $isAvg ? 'Day' : 'Game';
$ctypeLabel = MetricCompetitionService::competitionTypeLabel($ctype);

$score = MetricCompetitionService::scoreline($m, $seriesAsOf);
if ($isAvg) {
    $leader = MetricCompetitionService::leaderSide($m, $seriesAsOf);
    if (!empty($seriesAsOf['is_draw']) || $leader === null) {
        $seriesLine = 'Series averages level · ' . $score['primary'];
    } elseif ($leader === 'a') {
        $seriesLine = $m['player_a_name'] . ' leads series average · ' . $score['primary'];
    } else {
        $seriesLine = $m['player_b_name'] . ' leads series average · ' . $score['primary'];
    }
    $shareText = $isTie
        ? "Rally {$dayWord} {$dayNum}: Within threshold — {$valA} vs {$valB}"
        : "Rally {$dayWord} {$dayNum}: {$winner} leads daily comparison · {$valA} vs {$valB}";
} elseif ($isBaseline) {
    $aWins = (int) $seriesAsOf['player_a_wins'];
    $bWins = (int) $seriesAsOf['player_b_wins'];
    $pctA = BaselineCompetitionService::formatPercentage(isset($outcome['percentage_a']) ? (float) $outcome['percentage_a'] : null);
    $pctB = BaselineCompetitionService::formatPercentage(isset($outcome['percentage_b']) ? (float) $outcome['percentage_b'] : null);
    $seriesLine = ($aWins === $bWins)
        ? ('Series level ' . $aWins . '–' . $bWins)
        : (($aWins > $bWins ? $m['player_a_name'] : $m['player_b_name']) . ' leads ' . max($aWins, $bWins) . '–' . min($aWins, $bWins));
    $shareText = $isTie
        ? "Rally BASELINE Game {$dayNum}: Tie · {$pctA} vs {$pctB}"
        : "Rally BASELINE Game {$dayNum}: {$winner} {$pctA} · {$loser} {$pctB}";
} else {
    $aWins = (int) $seriesAsOf['player_a_wins'];
    $bWins = (int) $seriesAsOf['player_b_wins'];
    if ($aWins === $bWins) {
        $seriesLine = 'Series level ' . $aWins . '–' . $bWins;
    } elseif ($aWins > $bWins) {
        $seriesLine = $m['player_a_name'] . ' leads ' . $aWins . '–' . $bWins;
    } else {
        $seriesLine = $m['player_b_name'] . ' leads ' . $bWins . '–' . $aWins;
    }
    $shareText = $isTie
        ? "Rally CLASSIC Game {$dayNum}: Tie — {$valA} vs {$valB}"
        : "Rally CLASSIC Game {$dayNum}: {$winner} defeats {$loser} · {$valA} vs {$valB}";
}
?>
<section class="share-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">← Back to match</a></p>
    <h1 class="visually-hidden"><?= e($dayWord) ?> <?= $dayNum ?> result card</h1>
  </header>

  <div class="share-stage">
    <article class="share-card" id="share-card" data-variant="tall">
      <header class="share-head">
        <span class="share-brand"><span class="brand-mark" aria-hidden="true"></span>Rally</span>
        <span class="share-kicker t-num">
          <?= $isAvg ? 'SERIES FINAL · ' . strtoupper((string) $m['metric_name']) . ' COMPARISON' : (e(strtoupper($dayWord)) . ' ' . $dayNum . ' FINAL · ' . e(strtoupper($ctypeLabel))) ?>
        </span>
      </header>

      <div class="share-result">
        <?php if ($isBaseline): ?>
          <div class="share-row<?= $winnerSide === 'a' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_a_name']) ?></span>
            <span class="share-row-value t-num"><?= e(BaselineCompetitionService::formatPercentage(isset($outcome['percentage_a']) ? (float) $outcome['percentage_a'] : null)) ?></span>
          </div>
          <p class="share-meta"><?= e($valA) ?> · Baseline <?= isset($outcome['baseline_a']) ? e(number_format((int) round((float) $outcome['baseline_a']))) : '—' ?></p>
          <div class="share-row<?= $winnerSide === 'b' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_b_name']) ?></span>
            <span class="share-row-value t-num"><?= e(BaselineCompetitionService::formatPercentage(isset($outcome['percentage_b']) ? (float) $outcome['percentage_b'] : null)) ?></span>
          </div>
          <p class="share-meta"><?= e($valB) ?> · Baseline <?= isset($outcome['baseline_b']) ? e(number_format((int) round((float) $outcome['baseline_b']))) : '—' ?></p>
        <?php elseif ($isAvg): ?>
          <div class="share-row<?= $winnerSide === 'a' || MetricCompetitionService::leaderSide($m, $seriesAsOf) === 'a' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_a_name']) ?></span>
            <span class="share-row-value t-num"><?= e(MetricFormatter::formatCompact($seriesAsOf['player_a_average'] ?? $outcome['value_a'], $metric)) ?></span>
          </div>
          <p class="share-meta">average</p>
          <div class="share-row<?= $winnerSide === 'b' || MetricCompetitionService::leaderSide($m, $seriesAsOf) === 'b' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_b_name']) ?></span>
            <span class="share-row-value t-num"><?= e(MetricFormatter::formatCompact($seriesAsOf['player_b_average'] ?? $outcome['value_b'], $metric)) ?></span>
          </div>
          <p class="share-meta">average</p>
        <?php else: ?>
          <div class="share-row<?= $winnerSide === 'a' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_a_name']) ?></span>
            <span class="share-row-value t-num"><?= e(MetricFormatter::formatCompact((int) $outcome['value_a'], $metric)) ?></span>
          </div>
          <div class="share-row<?= $winnerSide === 'b' ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($m['player_b_name']) ?></span>
            <span class="share-row-value t-num"><?= e(MetricFormatter::formatCompact((int) $outcome['value_b'], $metric)) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <p class="share-headline">
        <?php if ($isTie): ?>
          <?= $isAvg ? 'Within threshold' : 'Tie' ?>
        <?php elseif ($isBaseline): ?>
          <?= e((string) $winner) ?> wins
        <?php elseif ($isAvg): ?>
          <?= e((string) $winner) ?> recorded the leading value
        <?php else: ?>
          <?= e((string) $winner) ?> defeats <?= e((string) $loser) ?>
        <?php endif; ?>
      </p>

      <footer class="share-foot">
        <p class="share-series t-num"><?= e($seriesLine) ?></p>
        <p class="share-meta"><?= e($m['metric_name']) ?> · <?= e($ctypeLabel) ?> · <span class="t-num"><?= e($dateLabel) ?></span> · Official</p>
        <?php if ($isAvg): ?>
          <p class="share-meta hint">Daily comparison only. Series winner uses the final average.</p>
        <?php endif; ?>
      </footer>
    </article>
  </div>

  <div class="share-variants" role="group" aria-label="Card format">
    <button type="button" class="button button-ghost button-small is-active" data-variant-btn="tall">Social</button>
    <button type="button" class="button button-ghost button-small" data-variant-btn="square">Square</button>
    <button type="button" class="button button-ghost button-small" data-variant-btn="compact">Link preview</button>
  </div>

  <div class="share-actions">
    <button type="button" class="button button-primary" id="share-btn"
            data-share-url="<?= e($shareUrl) ?>"
            data-share-text="<?= e($shareText) ?>">Share</button>
    <button type="button" class="button button-ghost" id="copy-link-btn" data-share-url="<?= e($shareUrl) ?>">Copy link</button>
    <p class="hint" id="share-feedback" role="status" aria-live="polite"></p>
  </div>
</section>
