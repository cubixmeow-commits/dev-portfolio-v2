<?php

use Rally\Services\MatchScoringService;

/**
 * Fourteen-game series rail — the Rally signature match tape.
 *
 * A wins sit above the baseline, B wins below, ties on it, voids struck,
 * pending straddles it in amber, the live game is boxed in signal color.
 * Readable without color: position + initials + symbols + per-cell labels.
 *
 * @var array $match  Match row (names, tie_threshold, player ids)
 * @var list<array> $days  Day rows with embedded results
 * @var bool|null $mini  Compact variant: no numbers, no legend, no links
 * @var bool|null $legend  Render the text legend (default: !$mini)
 */
$mini = !empty($mini);
$showLegend = $legend ?? !$mini;
$matchId = (int) $match['id'];
$initialA = mb_strtoupper(mb_substr((string) $match['player_a_name'], 0, 1));
$initialB = mb_strtoupper(mb_substr((string) $match['player_b_name'], 0, 1));
if ($initialA === $initialB) {
    $initialA = mb_strtoupper(mb_substr((string) $match['player_a_name'], 0, 2));
    $initialB = mb_strtoupper(mb_substr((string) $match['player_b_name'], 0, 2));
}
?>
<div class="rail-wrap">
  <ol class="rail<?= $mini ? ' rail--mini' : '' ?>" aria-label="<?= count($days) ?>-game series rail">
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
              $label = 'tie, official';
          } elseif (($outcome['winner_side'] ?? null) === 'a') {
              $modifier = 'a';
              $glyph = $initialA;
              $label = $match['player_a_name'] . ' win, official';
          } elseif (($outcome['winner_side'] ?? null) === 'b') {
              $modifier = 'b';
              $glyph = $initialB;
              $label = $match['player_b_name'] . ' win, official';
          } else {
              $modifier = 'void';
              $glyph = '×';
              $label = 'official, no result';
          }
      }
      $aria = 'Game ' . $num . ': ' . $label;
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
  <ul class="rail-legend" aria-hidden="true">
    <li><span class="glyph"><?= e($initialA) ?></span> <?= e($match['player_a_name']) ?> — above the line</li>
    <li><span class="glyph"><?= e($initialB) ?></span> <?= e($match['player_b_name']) ?> — below the line</li>
    <li><span class="glyph">=</span> Tie</li>
    <li><span class="glyph">×</span> Void</li>
    <li><span class="glyph">…</span> Pending</li>
  </ul>
  <?php endif; ?>
</div>
