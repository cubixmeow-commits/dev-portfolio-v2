<?php
/** @var array $pack */
/** @var array $day */
/** @var array $outcome */
/** @var array $seriesAsOf */
$m = $pack['match'];
$dayNum = (int) $day['day_number'];
$isTie = $outcome['kind'] === 'tie';
$winnerSide = $outcome['winner_side'] ?? null;
$winner = $winnerSide === 'a' ? $m['player_a_name'] : ($winnerSide === 'b' ? $m['player_b_name'] : null);
$loser = $winnerSide === 'a' ? $m['player_b_name'] : ($winnerSide === 'b' ? $m['player_a_name'] : null);
$dateLabel = (new DateTimeImmutable((string) $day['competition_date']))->format('F j, Y');
$shareUrl = full_url('/matches/' . (int) $m['id'] . '/day/' . $dayNum . '/share');
$shareText = $isTie
    ? "Rally Game {$dayNum} FINAL: Tie — " . number_format((int) $outcome['value_a']) . ' vs ' . number_format((int) $outcome['value_b'])
    : "Rally Game {$dayNum} FINAL: {$winner} defeats {$loser} by " . number_format((int) $outcome['margin']);

$aWins = (int) $seriesAsOf['player_a_wins'];
$bWins = (int) $seriesAsOf['player_b_wins'];
if ($aWins === $bWins) {
    $seriesLine = 'Series level ' . $aWins . '–' . $bWins;
} elseif ($aWins > $bWins) {
    $seriesLine = $m['player_a_name'] . ' leads ' . $aWins . '–' . $bWins;
} else {
    $seriesLine = $m['player_b_name'] . ' leads ' . $bWins . '–' . $aWins;
}

$rows = [
    ['name' => $m['player_a_name'], 'value' => (int) $outcome['value_a'], 'winner' => $winnerSide === 'a'],
    ['name' => $m['player_b_name'], 'value' => (int) $outcome['value_b'], 'winner' => $winnerSide === 'b'],
];
// Winner reads first, like an official result sheet
if ($winnerSide === 'b') {
    $rows = array_reverse($rows);
}
?>
<section class="share-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">← Back to match</a></p>
    <h1 class="visually-hidden">Game <?= $dayNum ?> result card</h1>
  </header>

  <div class="share-stage">
    <article class="share-card" id="share-card" data-variant="tall">
      <header class="share-head">
        <span class="share-brand"><span class="brand-mark" aria-hidden="true"></span>Rally</span>
        <span class="share-kicker t-num">Game <?= $dayNum ?> · Final</span>
      </header>

      <div class="share-result">
        <?php foreach ($rows as $row): ?>
          <div class="share-row<?= $row['winner'] ? ' is-winner' : '' ?>">
            <span class="share-row-name"><?= e($row['name']) ?></span>
            <span class="share-row-value t-num"><?= e(number_format($row['value'])) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <p class="share-headline">
        <?php if ($isTie): ?>
          Tie — difference <span class="t-num"><?= e(number_format((int) $outcome['margin'])) ?></span>
        <?php else: ?>
          <?= e($winner) ?> wins by <span class="t-num"><?= e(number_format((int) $outcome['margin'])) ?></span>
        <?php endif; ?>
      </p>

      <footer class="share-foot">
        <p class="share-series t-num"><?= e($seriesLine) ?></p>
        <p class="share-meta"><?= e($m['metric_name']) ?> · <span class="t-num"><?= e($dateLabel) ?></span> · Official</p>
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
