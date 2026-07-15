<?php
/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$dayNum = (int) $day['day_number'];
$winner = null;
$loser = null;
if (($outcome['winner_side'] ?? null) === 'a') {
    $winner = $m['player_a_name'];
    $loser = $m['player_b_name'];
} elseif (($outcome['winner_side'] ?? null) === 'b') {
    $winner = $m['player_b_name'];
    $loser = $m['player_a_name'];
}
$dateLabel = (new DateTimeImmutable((string) $day['competition_date']))->format('F j, Y');
$shareUrl = full_url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share');
$shareText = $outcome['kind'] === 'tie'
    ? "Rally Game {$dayNum} FINAL: Tie — " . number_format((int) $outcome['value_a']) . ' vs ' . number_format((int) $outcome['value_b'])
    : "Rally Game {$dayNum} FINAL: {$winner} defeats {$loser}";
?>
<article class="share-card" id="share-card">
  <p class="share-brand">Rally</p>
  <p class="share-kicker">Game <?= $dayNum ?> Final</p>
  <?php if ($outcome['kind'] === 'tie'): ?>
    <h1 class="share-headline">It’s a tie</h1>
  <?php else: ?>
    <h1 class="share-headline"><?= e($winner) ?> defeats <?= e($loser) ?></h1>
  <?php endif; ?>

  <div class="share-scores">
    <div>
      <p class="share-name"><?= e($m['player_a_name']) ?></p>
      <p class="share-value"><?= e(number_format((int) $outcome['value_a'])) ?></p>
    </div>
    <p class="share-vs" aria-hidden="true">vs</p>
    <div>
      <p class="share-name"><?= e($m['player_b_name']) ?></p>
      <p class="share-value"><?= e(number_format((int) $outcome['value_b'])) ?></p>
    </div>
  </div>

  <?php if ($outcome['kind'] === 'win'): ?>
    <p class="share-margin-label">Winning margin</p>
    <p class="share-margin"><?= e(number_format((int) $outcome['margin'])) ?> steps</p>
  <?php else: ?>
    <p class="share-margin-label">Difference</p>
    <p class="share-margin"><?= e(number_format((int) $outcome['margin'])) ?> steps (tie)</p>
  <?php endif; ?>

  <p class="share-series-label">Series</p>
  <p class="share-series">
    <?php if ((int) $seriesAsOf['player_a_wins'] === (int) $seriesAsOf['player_b_wins']): ?>
      Tied <?= (int) $seriesAsOf['player_a_wins'] ?>–<?= (int) $seriesAsOf['player_b_wins'] ?>
    <?php elseif ((int) $seriesAsOf['player_a_wins'] > (int) $seriesAsOf['player_b_wins']): ?>
      <?= e($m['player_a_name']) ?> leads <?= (int) $seriesAsOf['player_a_wins'] ?>–<?= (int) $seriesAsOf['player_b_wins'] ?>
    <?php else: ?>
      <?= e($m['player_b_name']) ?> leads <?= (int) $seriesAsOf['player_b_wins'] ?>–<?= (int) $seriesAsOf['player_a_wins'] ?>
    <?php endif; ?>
  </p>

  <p class="share-meta"><?= e($m['metric_name']) ?> · <?= e($dateLabel) ?> · Official</p>
</article>

<div class="share-actions">
  <button type="button" class="button button-primary" id="share-btn"
          data-share-url="<?= e($shareUrl) ?>"
          data-share-text="<?= e($shareText) ?>">Share</button>
  <button type="button" class="button button-ghost" id="copy-link-btn" data-share-url="<?= e($shareUrl) ?>">Copy link</button>
  <a class="button button-ghost" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">Back to match</a>
  <p class="hint" id="share-feedback" role="status" aria-live="polite"></p>
</div>
