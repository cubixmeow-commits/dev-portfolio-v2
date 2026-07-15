<?php

use Rally\Core\View;
use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $pack */
/** @var array $comparability */
/** @var list<array> $timeline */
/** @var array|null $today */
$m = $pack['match'];
$s = $pack['summary'];
$days = $pack['days'];
$matchId = (int) $m['id'];
$status = (string) $m['status'];
$provisional = !empty($s['is_provisional']);
$isAvg = MetricCompetitionService::isSeriesAverage($m);
$metric = MetricCompetitionService::metricPayload($m);
$railCopy = MetricCompetitionService::railLegendCopy($m);

$tz = new DateTimeZone((string) $m['timezone']);
$startDate = new DateTimeImmutable((string) $m['start_date']);
$endDate = $days !== [] ? new DateTimeImmutable((string) end($days)['competition_date']) : $startDate;
$dateRange = $startDate->format('M j') . ' – ' . ($startDate->format('M') === $endDate->format('M') ? $endDate->format('j') : $endDate->format('M j'));

$liveDayNum = null;
$currentDayNum = null;
$remainingGames = 0;
foreach ($days as $d) {
    if ((string) $d['status'] === 'live') {
        $liveDayNum = (int) $d['day_number'];
    }
    if (in_array((string) $d['status'], ['live', 'pending'], true)) {
        $currentDayNum = max((int) $d['day_number'], (int) $currentDayNum);
    }
    if (in_array((string) $d['status'], ['live', 'scheduled'], true)) {
        $remainingGames++;
    }
}

if ($isAvg) {
    $avgA = $s['player_a_average'] ?? null;
    $avgB = $s['player_b_average'] ?? null;
    $aWins = (int) ($s['daily_comparison_a_leads'] ?? 0);
    $bWins = (int) ($s['daily_comparison_b_leads'] ?? 0);
    $leaderSide = null;
    if (($s['leader_user_id'] ?? null) === (int) $m['player_a_user_id']) {
        $leaderSide = 'a';
    } elseif (($s['leader_user_id'] ?? null) === (int) $m['player_b_user_id']) {
        $leaderSide = 'b';
    }
} else {
    $aWins = (int) $s['player_a_wins'];
    $bWins = (int) $s['player_b_wins'];
    $leaderSide = $aWins > $bWins ? 'a' : ($bWins > $aWins ? 'b' : null);
}

$matchChip = match (true) {
    $liveDayNum !== null => ['live', 'Live'],
    $status === 'settling' => ['settling', 'Settling'],
    $status === 'completed' => ['completed', 'Final'],
    $status === 'scheduled' => ['scheduled', 'Scheduled'],
    default => ['active', ucfirst($status)],
};

if ($status === 'completed') {
    if (!empty($s['is_draw'])) {
        $verdict = '<strong>Final</strong> — drawn series';
    } else {
        $winnerName = $leaderSide === 'a' ? $m['player_a_name'] : $m['player_b_name'];
        $verdict = '<strong>Final</strong> — ' . e($winnerName) . ' wins the series';
    }
} elseif ($status === 'settling') {
    $verdict = '<strong>Settling</strong> — ' . (int) $s['pending_days'] . ' result' . ((int) $s['pending_days'] === 1 ? '' : 's') . ' awaiting official review';
} elseif ($provisional) {
    $remainLabel = $isAvg ? 'day' : 'game';
    $verdict = '<strong>Provisional</strong> — ' . $remainingGames . ' ' . $remainLabel . ($remainingGames === 1 ? '' : 's') . ' remaining';
} else {
    $verdict = '<strong>Final</strong>';
}
$tieCount = $isAvg ? (int) ($s['daily_comparison_ties'] ?? 0) : (int) ($s['ties'] ?? 0);
$tieVoidNote = ($tieCount > 0 || (int) $s['voids'] > 0)
    ? ($isAvg ? 'Within threshold ' : 'Ties ') . $tieCount . ' · Voids ' . (int) $s['voids']
    : null;
?>
<article class="scoreboard wrap" aria-labelledby="match-title">
  <h1 id="match-title" class="visually-hidden"><?= e($m['player_a_name']) ?> versus <?= e($m['player_b_name']) ?>, series score <?= $aWins ?>–<?= $bWins ?><?= $provisional ? ', provisional' : ', final' ?></h1>

  <div class="board-meta">
    <span class="t-label"><?= e($m['metric_name']) ?></span>
    <span class="t-label" aria-hidden="true">·</span>
    <span class="t-label"><?= $isAvg ? ((int) $m['length_days'] . '-day average series') : ((int) $m['length_days'] . '-game series') ?></span>
    <span class="t-label" aria-hidden="true">·</span>
    <span class="t-label t-num"><?= e($dateRange) ?></span>
    <?php if ($currentDayNum !== null && $status !== 'completed'): ?>
      <span class="t-label" aria-hidden="true">·</span>
      <span class="t-label">Game <?= $currentDayNum ?> of <?= (int) $m['length_days'] ?></span>
    <?php endif; ?>
    <span class="board-meta-spacer"></span>
    <span class="status-pill status-<?= e($matchChip[0]) ?>"><?= e($matchChip[1]) ?></span>
  </div>

  <div class="board<?= $isAvg ? ' board--average' : '' ?>" aria-live="polite">
    <div class="board-side board-side--a">
      <div class="board-ident">
        <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) $m['player_a_name'], 0, 1))) ?></span>
        <a class="board-name" href="<?= e(url('/players/' . (int) $m['player_a_user_id'])) ?>"><?= e($m['player_a_name']) ?></a>
      </div>
      <p class="board-source"><?= e($m['player_a_source_name']) ?></p>
      <p class="board-score-num t-num board-score-inner<?= $leaderSide === 'b' ? ' is-trailing' : '' ?>"><?= $isAvg ? e(MetricFormatter::formatCompact($avgA, $metric)) : (int) $aWins ?></p>
      <?php if ($isAvg): ?><p class="board-avg-unit t-label">Series average</p><?php endif; ?>
      <span class="board-lead-tick board-score-inner<?= $leaderSide === 'a' ? '' : ' is-hidden' ?>"></span>
    </div>
    <div class="board-center">
      <span class="board-center-rule"></span>
      <?php if ($isAvg && ($s['average_difference'] ?? null) !== null): ?>
        <span class="board-diff t-label">Diff <span class="t-num"><?= e(MetricFormatter::formatCompact($s['average_difference'], $metric)) ?></span></span>
      <?php endif; ?>
    </div>
    <div class="board-side board-side--b">
      <div class="board-ident">
        <span class="avatar"><?= e(mb_strtoupper(mb_substr((string) $m['player_b_name'], 0, 1))) ?></span>
        <a class="board-name" href="<?= e(url('/players/' . (int) $m['player_b_user_id'])) ?>"><?= e($m['player_b_name']) ?></a>
      </div>
      <p class="board-source"><?= e($m['player_b_source_name'] ?? 'Source pending') ?></p>
      <p class="board-score-num t-num board-score-inner<?= $leaderSide === 'a' ? ' is-trailing' : '' ?>"><?= $isAvg ? e(MetricFormatter::formatCompact($avgB, $metric)) : (int) $bWins ?></p>
      <?php if ($isAvg): ?><p class="board-avg-unit t-label">Series average</p><?php endif; ?>
      <span class="board-lead-tick board-score-inner<?= $leaderSide === 'b' ? '' : ' is-hidden' ?>"></span>
    </div>
  </div>

  <div class="board-verdict" aria-live="polite">
    <span class="board-verdict-text"><?= $verdict ?><?= $tieVoidNote !== null ? ' <span class="verdict-sep" aria-hidden="true">·</span> ' . e($tieVoidNote) : '' ?></span>
  </div>

  <?php if ($status === 'settling'): ?>
    <div class="settle-panel" role="status">
      <span class="settle-glyph" aria-hidden="true">…</span>
      <div>
        <p class="settle-title">Series settling</p>
        <p class="settle-body">All games are played. <strong><?= (int) $s['pending_days'] ?> result<?= (int) $s['pending_days'] === 1 ? '' : 's' ?></strong> remain<?= (int) $s['pending_days'] === 1 ? 's' : '' ?> provisional until final device sync is reviewed.</p>
      </div>
    </div>
  <?php endif; ?>

  <?php View::partial('partials/rail', ['match' => $m, 'days' => $days, 'railCopy' => $railCopy]); ?>

  <div class="source-strip<?= $comparability['mismatch'] ? ' source-strip--mismatch' : '' ?>" role="<?= $comparability['mismatch'] ? 'alert' : 'status' ?>">
    <span class="source-strip-kicker"><?= e($comparability['title']) ?></span>
    <span class="source-strip-pair"><?= e($m['player_a_source_name']) ?> vs <?= e($m['player_b_source_name'] ?? 'Source pending') ?></span>
    <p class="source-strip-note"><?= e($comparability['message']) ?></p>
  </div>

  <div class="match-columns">
    <div class="match-col-main">
      <?php if ($today !== null):
        $day = $today['day'];
        $outcome = $today['outcome'];
        $isLive = (string) $day['status'] === 'live';
        $dayDate = new DateTimeImmutable((string) $day['competition_date']);
        $settlesLocal = (new DateTimeImmutable((string) $day['settles_at'], new DateTimeZone('UTC')))->setTimezone($tz);
        $lastSync = null;
        foreach ($day['results'] ?? [] as $r) {
            if (!empty($r['ingested_at']) && ($lastSync === null || $r['ingested_at'] > $lastSync)) {
                $lastSync = (string) $r['ingested_at'];
            }
        }
        $bothIn = $outcome['value_a'] !== null && $outcome['value_b'] !== null;
        $dayLeader = $outcome['winner_side'] ?? null;
        $margin = $outcome['margin'] ?? null;
        $withinTie = $bothIn && ($outcome['kind'] ?? '') === 'tie';
      ?>
      <section class="live-panel<?= $isLive ? '' : ' live-panel--pending' ?>" aria-labelledby="today-heading">
        <div class="live-panel-head">
          <h2 class="live-panel-title" id="today-heading">Game <?= (int) $day['day_number'] ?><?= $isLive ? ' · Today' : '' ?></h2>
          <span>
            <span class="status-pill status-<?= e((string) $day['status']) ?>"><?= $isLive ? 'Live' : 'Pending' ?></span>
            <span class="live-panel-date t-num"><?= e($dayDate->format('D M j')) ?></span>
          </span>
        </div>

        <div class="live-values">
          <div class="live-side live-side--a">
            <p class="live-side-name"><?= e($m['player_a_name']) ?></p>
            <p class="live-side-value t-num<?= $outcome['value_a'] === null ? ' is-awaiting' : '' ?><?= $dayLeader === 'a' ? ' is-leading' : '' ?>">
              <?= $outcome['value_a'] !== null ? e(MetricFormatter::formatCompact((int) $outcome['value_a'], $metric)) : 'Awaiting sync' ?>
            </p>
          </div>
          <div class="live-mid" aria-hidden="true">
            <span class="t-label">vs</span>
          </div>
          <div class="live-side live-side--b">
            <p class="live-side-name"><?= e($m['player_b_name']) ?></p>
            <p class="live-side-value t-num<?= $outcome['value_b'] === null ? ' is-awaiting' : '' ?><?= $dayLeader === 'b' ? ' is-leading' : '' ?>">
              <?= $outcome['value_b'] !== null ? e(MetricFormatter::formatCompact((int) $outcome['value_b'], $metric)) : 'Awaiting sync' ?>
            </p>
          </div>
        </div>

        <div class="live-margin">
          <?php if ($dayLeader !== null): ?>
            <span class="live-margin-lead"><?= e($dayLeader === 'a' ? $m['player_a_name'] : $m['player_b_name']) ?> leads by <span class="t-num"><?= e(MetricFormatter::formatCompact((int) $margin, $metric)) ?></span></span>
          <?php elseif ($withinTie): ?>
            <span class="live-margin-lead">Level — within tie threshold (<span class="t-num"><?= (int) $m['tie_threshold'] ?></span>)</span>
          <?php else: ?>
            <span class="live-margin-lead hint">Waiting for both competitors to sync</span>
          <?php endif; ?>
        </div>

        <?php if (!$isLive): ?>
          <div class="settle-panel settle-inline" role="status">
            <span class="settle-glyph" aria-hidden="true">…</span>
            <div>
              <p class="settle-title">Pending review</p>
              <p class="settle-body">Waiting for final device sync. Locks <strong class="t-num"><?= e($settlesLocal->format('M j \a\t g:i A T')) ?></strong>.</p>
            </div>
          </div>
        <?php endif; ?>

        <div class="live-meta">
          <?php if ($isLive): ?>
            <span>Competition day in progress · settles <span class="t-num"><?= e($settlesLocal->format('M j, g:i A T')) ?></span></span>
          <?php endif; ?>
          <?php if ($lastSync !== null): ?>
            <span>Last sync <span class="t-num"><?= e(time_ago($lastSync)) ?></span></span>
          <?php endif; ?>
          <?php if ($remainingGames > 0): ?>
            <span><span class="t-num"><?= $remainingGames ?></span> game<?= $remainingGames === 1 ? '' : 's' ?> remaining</span>
          <?php endif; ?>
        </div>
      </section>
      <?php endif; ?>

      <section aria-labelledby="recent-heading">
        <div class="section-head">
          <h2 id="recent-heading"><?= $isAvg ? 'Recent days' : 'Recent games' ?></h2>
          <a class="section-aside" href="<?= e(url('/matches/' . $matchId . '/history')) ?>">Full history</a>
        </div>
        <ul class="fixture-list">
          <?php
          $shown = 0;
          foreach (array_reverse($timeline) as $item):
            $day = $item['day'];
            $outcome = $item['outcome'];
            $st = (string) $day['status'];
            if (!in_array($st, ['official', 'void', 'pending'], true)) {
                continue;
            }
            if ($shown >= 5) {
                break;
            }
            $shown++;
            $dayNum = (int) $day['day_number'];
            $dayUrl = url('/matches/' . $matchId . '/day/' . $dayNum);
            $rowDate = (new DateTimeImmutable((string) $day['competition_date']))->format('M j');
            $winnerSide = ($outcome['winner_side'] ?? null);
          ?>
          <li class="fixture">
            <span class="fixture-game" aria-hidden="true">
              <span class="fixture-game-num t-num"><?= $dayNum ?></span>
              <span class="fixture-game-label"><?= $isAvg ? 'Day' : 'Game' ?></span>
            </span>
            <div class="fixture-main">
              <p class="fixture-result">
                <a href="<?= e($dayUrl) ?>">
                <?php if ($st === 'void'): ?>
                  Void — no winner awarded
                <?php elseif ($st === 'pending'): ?>
                  <?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?>
                <?php elseif ($outcome['kind'] === 'tie'): ?>
                  Tie
                <?php elseif ($outcome['kind'] === 'win'): ?>
                  <?= e($winnerSide === 'a' ? $m['player_a_name'] : $m['player_b_name']) ?> d. <span class="is-loser"><?= e($winnerSide === 'a' ? $m['player_b_name'] : $m['player_a_name']) ?></span>
                <?php else: ?>
                  Awaiting sync
                <?php endif; ?>
                </a>
              </p>
              <p class="fixture-meta">
                <span class="t-num"><?= e($rowDate) ?></span>
                <span class="status-pill status-<?= e($st) ?>"><?= e($st === 'pending' ? 'Pending' : ucfirst($st)) ?></span>
                <?php if ($st === 'official' && $outcome['kind'] === 'win'): ?>
                  <span>Margin <span class="t-num"><?= e(number_format((int) $outcome['margin'])) ?></span></span>
                <?php endif; ?>
                <?php if ($st === 'official'): ?>
                  <a class="share-link" href="<?= e(url('/matches/' . $matchId . '/day/' . $dayNum . '/share')) ?>">Share card</a>
                <?php endif; ?>
              </p>
            </div>
            <div class="fixture-vals" aria-hidden="true">
              <?php if ($st !== 'void'): ?>
                <div class="<?= $winnerSide === 'a' ? 'is-winner' : 'is-loser' ?>"><?= $outcome['value_a'] !== null ? e(MetricFormatter::formatCompact((int) $outcome['value_a'], $metric)) : '—' ?></div>
                <div class="<?= $winnerSide === 'b' ? 'is-winner' : 'is-loser' ?>"><?= $outcome['value_b'] !== null ? e(MetricFormatter::formatCompact((int) $outcome['value_b'], $metric)) : '—' ?></div>
              <?php endif; ?>
            </div>
          </li>
          <?php endforeach; ?>
          <?php if ($shown === 0): ?>
            <li class="fixture"><span></span><div class="fixture-main"><p class="fixture-result hint">No settled games yet.</p></div></li>
          <?php endif; ?>
        </ul>
      </section>
    </div>

    <aside class="match-col-aside">
      <div class="section-head">
        <h2 id="stats-heading">Series notes</h2>
      </div>
      <dl class="note-list" aria-labelledby="stats-heading">
        <div><dt>Scoring</dt><dd><?= e(MetricFormatter::strategyLabel((string) ($m['scoring_strategy'] ?? 'daily_wins'))) ?></dd></div>
        <div><dt>Classification</dt><dd><?= e(MetricFormatter::classificationLabel((string) ($m['classification'] ?? 'performance'))) ?></dd></div>
        <?php if (!$isAvg && ($s['largest_margin'] ?? null) !== null): ?>
          <div><dt>Largest official margin</dt><dd class="t-num"><?= e(MetricFormatter::formatCompact((int) $s['largest_margin'], $metric)) ?></dd></div>
        <?php endif; ?>
        <?php if ($isAvg && ($s['highest_value'] ?? null) !== null): ?>
          <div><dt>Highest official value</dt><dd class="t-num"><?= e(MetricFormatter::formatCompact((int) $s['highest_value'], $metric)) ?></dd></div>
        <?php endif; ?>
        <?php if ($isAvg && ($s['lowest_value'] ?? null) !== null): ?>
          <div><dt>Lowest official value</dt><dd class="t-num"><?= e(MetricFormatter::formatCompact((int) $s['lowest_value'], $metric)) ?></dd></div>
        <?php endif; ?>
        <?php if (!$isAvg && ($s['average_a'] ?? null) !== null): ?>
          <div><dt><?= e($m['player_a_name']) ?> official average</dt><dd class="t-num"><?= e(MetricFormatter::formatCompact((int) $s['average_a'], $metric)) ?></dd></div>
        <?php endif; ?>
        <?php if (!$isAvg && ($s['average_b'] ?? null) !== null): ?>
          <div><dt><?= e($m['player_b_name']) ?> official average</dt><dd class="t-num"><?= e(MetricFormatter::formatCompact((int) $s['average_b'], $metric)) ?></dd></div>
        <?php endif; ?>
        <div><dt>Tie threshold</dt><dd class="t-num"><?= (int) $m['tie_threshold'] ?> <?= e($m['metric_unit']) ?></dd></div>
        <div><dt>Match timezone</dt><dd><?= e($m['timezone']) ?></dd></div>
      </dl>
      <?php if (!empty($m['context_note'])): ?>
        <p class="hint metric-context"><?= e($m['context_note']) ?></p>
      <?php endif; ?>
    </aside>
  </div>
</article>
