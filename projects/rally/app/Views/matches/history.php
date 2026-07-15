<?php

use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $pack */
/** @var list<array> $rows */
$m = $pack['match'];
$s = $pack['summary'];
$metric = MetricCompetitionService::metricPayload($m);
$isAvg = MetricCompetitionService::isSeriesAverage($m);
$matchId = (int) $m['id'];
$aName = (string) $m['player_a_name'];
$bName = (string) $m['player_b_name'];
$score = MetricCompetitionService::scoreline($m, $s);
$dayWord = $isAvg ? 'Day' : 'Game';
?>
<section class="wrap history-page">
  <header class="page-header">
    <p class="eyebrow"><a href="<?= e(url('/matches/' . $matchId)) ?>">← Back to match</a></p>
    <h1>Match history</h1>
    <p class="history-scoreline t-num"><?= e($aName) ?> <strong><?= e($score['primary']) ?></strong> <?= e($bName) ?><?= !empty($s['is_provisional']) ? ' · provisional' : '' ?></p>
    <?php if ($isAvg): ?>
      <p class="hint">Daily columns are comparisons. The series winner is the final official average.</p>
    <?php endif; ?>
  </header>

  <?php
  $prepared = [];
  foreach ($rows as $row) {
      $day = $row['day'];
      $o = $row['outcome'];
      $status = (string) $day['status'];
      $winnerSide = $o['winner_side'] ?? null;
      $resultLabel = match (true) {
          $status === 'scheduled' => $isAvg ? 'Future day' : 'Future game',
          $status === 'live' => 'Live now',
          $status === 'void' => 'Void — no result awarded',
          $o['kind'] === 'tie' => $isAvg ? 'Within threshold' : 'Tie',
          $o['kind'] === 'win' => ($winnerSide === 'a' ? $aName : $bName) . ($isAvg ? ' leading value' : ' win'),
          default => 'Awaiting sync',
      };
      $prepared[] = [
          'day' => $day,
          'o' => $o,
          'status' => $status,
          'winnerSide' => $winnerSide,
          'resultLabel' => $resultLabel,
          'date' => (new DateTimeImmutable((string) $day['competition_date']))->format('D M j'),
      ];
  }
  ?>

  <ul class="fixture-list history-rows">
    <?php foreach ($prepared as $p):
      $day = $p['day'];
      $o = $p['o'];
      $dayNum = (int) $day['day_number'];
    ?>
    <li class="fixture<?= $p['status'] === 'scheduled' ? ' fixture--future' : '' ?>">
      <span class="fixture-game" aria-hidden="true">
        <span class="fixture-game-num t-num"><?= $dayNum ?></span>
        <span class="fixture-game-label"><?= e($dayWord) ?></span>
      </span>
      <div class="fixture-main">
        <p class="fixture-result">
          <?php if ($p['status'] === 'scheduled'): ?>
            <span class="is-loser"><?= e($p['resultLabel']) ?></span>
          <?php else: ?>
            <a href="<?= e(url('/matches/' . $matchId . '/day/' . $dayNum)) ?>">
              <?php if ($p['o']['kind'] === 'win'): ?>
                <?= e($p['winnerSide'] === 'a' ? $aName : $bName) ?> <?= $isAvg ? 'leads' : 'd.' ?> <span class="is-loser"><?= e($p['winnerSide'] === 'a' ? $bName : $aName) ?></span>
              <?php else: ?>
                <?= e($p['resultLabel']) ?>
              <?php endif; ?>
            </a>
          <?php endif; ?>
        </p>
        <p class="fixture-meta">
          <span class="t-num"><?= e($p['date']) ?></span>
          <span class="status-pill status-<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span>
          <?php if ($p['status'] === 'official' && $o['kind'] === 'win'): ?>
            <span>Margin <span class="t-num"><?= e(MetricFormatter::formatCompact((int) $o['margin'], $metric)) ?></span></span>
          <?php endif; ?>
        </p>
      </div>
      <div class="fixture-vals" aria-hidden="true">
        <?php if ($p['status'] !== 'void' && $p['status'] !== 'scheduled'): ?>
          <div class="<?= $p['winnerSide'] === 'a' ? 'is-winner' : 'is-loser' ?>"><?= $o['value_a'] !== null ? e(MetricFormatter::formatCompact((int) $o['value_a'], $metric)) : '—' ?></div>
          <div class="<?= $p['winnerSide'] === 'b' ? 'is-winner' : 'is-loser' ?>"><?= $o['value_b'] !== null ? e(MetricFormatter::formatCompact((int) $o['value_b'], $metric)) : '—' ?></div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>

  <div class="history-table-wrap" role="region" aria-label="<?= $isAvg ? 'Daily comparisons' : 'Daily games' ?>" tabindex="0">
    <table class="history-table">
      <thead>
        <tr>
          <th scope="col" class="t-num"><?= e($dayWord) ?></th>
          <th scope="col">Date</th>
          <th scope="col" class="col-num"><?= e($aName) ?></th>
          <th scope="col" class="col-num"><?= e($bName) ?></th>
          <th scope="col">Result</th>
          <th scope="col" class="col-num">Margin</th>
          <th scope="col">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($prepared as $p):
          $day = $p['day'];
          $o = $p['o'];
          $dayNum = (int) $day['day_number'];
        ?>
          <tr class="status-row-<?= e($p['status']) ?>">
            <td class="t-num">
              <?php if ($p['status'] === 'scheduled'): ?>
                <?= $dayNum ?>
              <?php else: ?>
                <a href="<?= e(url('/matches/' . $matchId . '/day/' . $dayNum)) ?>"><?= $dayNum ?></a>
              <?php endif; ?>
            </td>
            <td class="t-num"><?= e($p['date']) ?></td>
            <td class="col-num t-num<?= $p['winnerSide'] === 'a' ? ' is-winner' : '' ?>"><?= $o['value_a'] !== null ? e(MetricFormatter::formatCompact((int) $o['value_a'], $metric)) : '—' ?></td>
            <td class="col-num t-num<?= $p['winnerSide'] === 'b' ? ' is-winner' : '' ?>"><?= $o['value_b'] !== null ? e(MetricFormatter::formatCompact((int) $o['value_b'], $metric)) : '—' ?></td>
            <td><?= e($p['resultLabel']) ?></td>
            <td class="col-num t-num"><?= $o['margin'] !== null && $o['kind'] === 'win' ? e(MetricFormatter::formatCompact((int) $o['margin'], $metric)) : '—' ?></td>
            <td><span class="status-pill status-<?= e($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
