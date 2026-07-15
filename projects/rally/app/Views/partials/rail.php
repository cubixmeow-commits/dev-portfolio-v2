<?php

use Rally\Services\MatchScoringService;
use Rally\Services\MetricCompetitionService;

/**
 * Match-day series rail — the Rally signature match tape.
 *
 * A wins sit above the baseline, B wins below, ties on it, voids struck,
 * pending straddles it in amber, the live game is boxed in signal color.
 * Readable without color: position + initials + symbols + per-cell labels.
 *
 * For series_average matches, markers show daily comparison ownership,
 * not match points.
 *
 * @var array $match  Match row (names, tie_threshold, player ids)
 * @var list<array> $days  Day rows with embedded results
 * @var bool|null $mini  Compact variant: no numbers, no legend, no links
 * @var bool|null $legend  Render the text legend (default: !$mini)
 * @var array|null $railCopy Optional legend/title/note override
 */
$mini = !empty($mini);
$showLegend = $legend ?? !$mini;
$matchId = (int) $match['id'];
$railCopy = $railCopy ?? MetricCompetitionService::railLegendCopy($match);
$isAvg = MetricCompetitionService::isSeriesAverage($match);
$initialA = mb_strtoupper(mb_substr((string) $match['player_a_name'], 0, 1));
$initialB = mb_strtoupper(mb_substr((string) $match['player_b_name'], 0, 1));
if ($initialA === $initialB) {
    $initialA = mb_strtoupper(mb_substr((string) $match['player_a_name'], 0, 2));
    $initialB = mb_strtoupper(mb_substr((string) $match['player_b_name'], 0, 2));
}
$winLabelA = $isAvg ? ($match['player_a_name'] . ' leading value') : ($match['player_a_name'] . ' win, official');
$winLabelB = $isAvg ? ($match['player_b_name'] . ' leading value') : ($match['player_b_name'] . ' win, official');
$tieLabel = $isAvg ? 'within threshold' : 'tie, official';
$ariaPrefix = $isAvg ? 'Day ' : 'Game ';
?>
<div class="rail-wrap">
  <ol class="rail<?= $mini ? ' rail--mini' : '' ?>" aria-label="<?= count($days) ?>-<?= $isAvg ? 'day comparison' : 'game series' ?> rail">
    <?php foreach ($days as $day):
      $status = (string) $day['status'];
      $num = (int) $day['day_number'];
      $outcome = MatchScoringService::dayOutcome($match, $day);

      $modifier = 'future';
      $glyph = '';
      $label = 'future';
      if ($status === 'live') {
          $modifier = 'live';
          $glyph = null; // rendered as the live dot
          $label = 'live now';
      } elseif ($status === 'pending') {
          $modifier = 'pending';
          $glyph = '…';
          $label = 'pending review';
      } elseif ($status === 'void') {
          $modifier = 'void';
          $glyph = '×';
          $label = 'void, no winner awarded';
      } elseif ($status === 'official') {
          if ($outcome['kind'] === 'tie') {
              $modifier = 'tie';
              $glyph = '=';
              $label = $tieLabel;
          } elseif (($outcome['winner_side'] ?? null) === 'a') {
              $modifier = 'a';
              $glyph = $initialA;
              $label = $winLabelA;
          } elseif (($outcome['winner_side'] ?? null) === 'b') {
              $modifier = 'b';
              $glyph = $initialB;
              $label = $winLabelB;
          } else {
              $modifier = 'void';
              $glyph = '×';
              $label = 'official, no result';
          }
      }
      $aria = $ariaPrefix . $num . ': ' . $label;
      $tag = $mini ? 'span' : 'a';
      $href = $mini ? '' : ' href="' . e(url('/matches/' . $matchId . '/day/' . $num)) . '"';
    ?>
    <li>
      <<?= $tag ?><?= $href ?> class="rail-cell rail-cell--<?= e($modifier) ?>"
        <?= $mini ? 'aria-hidden="true"' : 'aria-label="' . e($aria) . '" title="' . e($aria) . '"' ?>>
        <span class="rail-track">
          <span class="rail-chip" aria-hidden="true"><?php if ($glyph === null): ?><span class="rail-live-dot"></span><?php else: ?><?= e($glyph) ?><?php endif; ?></span>
        </span>
        <?php if (!$mini): ?><span class="rail-num" aria-hidden="true"><?= $num ?></span><?php endif; ?>
      </<?= $tag ?>>
    </li>
    <?php endforeach; ?>
  </ol>
  <?php if ($showLegend): ?>
  <p class="rail-legend-title t-label"><?= e((string) ($railCopy['title'] ?? 'Daily games')) ?></p>
  <ul class="rail-legend" aria-hidden="true">
    <li><span class="glyph"><?= e($initialA) ?></span> <?= e($match['player_a_name']) ?> — <?= e((string) ($railCopy['a'] ?? 'above the line')) ?></li>
    <li><span class="glyph"><?= e($initialB) ?></span> <?= e($match['player_b_name']) ?> — <?= e((string) ($railCopy['b'] ?? 'below the line')) ?></li>
    <li><span class="glyph">=</span> <?= e((string) ($railCopy['tie'] ?? 'Tie')) ?></li>
    <li><span class="glyph">×</span> Void</li>
    <li><span class="glyph">…</span> Pending</li>
  </ul>
  <?php if (!empty($railCopy['note'])): ?>
    <p class="rail-note hint"><?= e((string) $railCopy['note']) ?></p>
  <?php endif; ?>
  <?php endif; ?>
</div>
