<?php
/** @var array $pack */
/** @var array $comparability */
/** @var list<array> $timeline */
/** @var array|null $today */
$m = $pack['match'];
$s = $pack['summary'];
$matchId = (int) $m['id'];
$provisional = !empty($s['is_provisional']);
?>
<article class="scoreboard" aria-labelledby="match-title">
  <header class="scoreboard-header">
    <p class="brand-kicker">Rally</p>
    <p class="metric-line"><?= e($m['metric_name']) ?></p>
    <p class="series-line"><?= (int) $m['length_days'] ?>-Game Series</p>
    <h1 id="match-title" class="visually-hidden"><?= e($m['player_a_name']) ?> versus <?= e($m['player_b_name']) ?></h1>
  </header>

  <div class="scoreboard-players" aria-hidden="false">
    <div class="player player-a">
      <a class="player-name" href="<?= e(url('/players/' . (int) $m['player_a_user_id'])) ?>"><?= e($m['player_a_name']) ?></a>
      <p class="player-source"><?= e($m['player_a_source_name']) ?></p>
    </div>
    <div class="player player-b">
      <a class="player-name" href="<?= e(url('/players/' . (int) $m['player_b_user_id'])) ?>"><?= e($m['player_b_name']) ?></a>
      <p class="player-source"><?= e($m['player_b_source_name'] ?? 'Source pending') ?></p>
    </div>
  </div>

  <p class="series-score" aria-live="polite"
     aria-label="Series score: <?= e($m['player_a_name']) ?> <?= (int) $s['player_a_wins'] ?>, <?= e($m['player_b_name']) ?> <?= (int) $s['player_b_wins'] ?><?= $provisional ? ', provisional' : '' ?>">
    <span class="score-num"><?= (int) $s['player_a_wins'] ?></span>
    <span class="score-sep" aria-hidden="true">–</span>
    <span class="score-num"><?= (int) $s['player_b_wins'] ?></span>
  </p>
  <?php if ($provisional && (string) $m['status'] !== 'completed'): ?>
    <p class="score-note">Provisional series score · ties <?= (int) $s['ties'] ?> · voids <?= (int) $s['voids'] ?></p>
  <?php elseif (!empty($s['is_draw'])): ?>
    <p class="score-note">Final draw · ties <?= (int) $s['ties'] ?> · voids <?= (int) $s['voids'] ?></p>
  <?php elseif ((string) $m['status'] === 'completed'): ?>
    <p class="score-note">Final · ties <?= (int) $s['ties'] ?> · voids <?= (int) $s['voids'] ?></p>
  <?php endif; ?>

  <?php if ((string) $m['status'] === 'settling'): ?>
    <div class="settling-banner" role="status">
      <strong>Series ended</strong>
      <p>Final results are settling. <?= (int) $s['pending_days'] ?> result<?= (int) $s['pending_days'] === 1 ? '' : 's' ?> remain provisional.</p>
    </div>
  <?php endif; ?>

  <ol class="timeline" aria-label="Daily results timeline">
    <?php foreach ($timeline as $item):
      $day = $item['day'];
      $sym = $item['symbol'];
      $label = $item['label'];
    ?>
      <li>
        <a href="<?= e(url('/matches/' . $matchId . '/day/' . (int) $day['day_number'])) ?>"
           class="timeline-mark status-<?= e((string) $day['status']) ?>"
           title="Game <?= (int) $day['day_number'] ?>: <?= e($label) ?>"
           aria-label="Game <?= (int) $day['day_number'] ?>: <?= e($label) ?>">
          <span aria-hidden="true"><?= e($sym) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ol>
  <p class="timeline-legend" aria-hidden="false">
    <span>● <?= e($m['player_a_name']) ?> win</span>
    <span>○ <?= e($m['player_b_name']) ?> win</span>
    <span>– Tie</span>
    <span>× Void</span>
    <span>◌ Pending / live</span>
    <span>· Future</span>
  </p>

  <?php if ($comparability['mismatch']): ?>
    <aside class="source-warning" role="alert">
      <strong><?= e($comparability['title']) ?></strong>
      <p><?= e($comparability['message']) ?></p>
    </aside>
  <?php else: ?>
    <aside class="source-ok" role="status">
      <strong><?= e($comparability['title']) ?></strong>
      <p><?= e($comparability['message']) ?></p>
    </aside>
  <?php endif; ?>

  <?php if ($today !== null):
    $day = $today['day'];
    $outcome = $today['outcome'];
    $isLive = (string) $day['status'] === 'live';
  ?>
  <section class="today-panel" aria-labelledby="today-heading">
    <h2 id="today-heading"><?= $isLive ? 'Today' : 'Latest provisional' ?> — Game <?= (int) $day['day_number'] ?></h2>
    <div class="today-grid">
      <div>
        <p class="today-name"><?= e($m['player_a_name']) ?></p>
        <p class="today-value"><?= $outcome['value_a'] !== null ? e(number_format((int) $outcome['value_a'])) : 'Awaiting sync' ?></p>
      </div>
      <div>
        <p class="today-name"><?= e($m['player_b_name']) ?></p>
        <p class="today-value"><?= $outcome['value_b'] !== null ? e(number_format((int) $outcome['value_b'])) : 'Awaiting sync' ?></p>
      </div>
    </div>
    <?php if ($outcome['value_a'] !== null && $outcome['value_b'] !== null && $outcome['kind'] !== 'tie'): ?>
      <?php
        $leaderName = ((int) $outcome['value_a'] > (int) $outcome['value_b']) ? $m['player_a_name'] : $m['player_b_name'];
        $margin = abs((int) $outcome['value_a'] - (int) $outcome['value_b']);
      ?>
      <p class="today-lead"><?= e($leaderName) ?> leads by <?= e(number_format($margin)) ?></p>
    <?php elseif ($outcome['kind'] === 'tie' || ($outcome['value_a'] !== null && $outcome['value_b'] !== null && abs((int)$outcome['value_a']-(int)$outcome['value_b']) < (int)$m['tie_threshold'])): ?>
      <p class="today-lead">Within tie threshold (<?= (int) $m['tie_threshold'] ?>)</p>
    <?php endif; ?>
    <p>
      <span class="status-pill status-<?= e((string) $day['status']) ?>"><?= e(strtoupper((string) $day['status'])) ?></span>
      <?php if ($isLive): ?>
        <span class="countdown" data-ends-at="<?= e($day['competition_date']) ?>" data-tz="<?= e($m['timezone']) ?>">Competition day in progress</span>
      <?php else: ?>
        <span>Settles at <?= e((new DateTimeImmutable((string) $day['settles_at'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone((string) $m['timezone']))->format('M j, g:i A T')) ?></span>
      <?php endif; ?>
    </p>
    <p class="today-note"><?= (int) $s['remaining_days'] ?> games remaining · values may still change until settlement</p>
  </section>
  <?php endif; ?>

  <section class="recent-games" aria-labelledby="recent-heading">
    <h2 id="recent-heading">Recent games</h2>
    <ul class="game-list">
      <?php
      $shown = 0;
      foreach (array_reverse($timeline) as $item):
        $day = $item['day'];
        $outcome = $item['outcome'];
        if (!in_array((string) $day['status'], ['official', 'void', 'pending'], true)) {
            continue;
        }
        if ($shown >= 5) {
            break;
        }
        $shown++;
        $status = (string) $day['status'];
      ?>
        <li class="game-row status-<?= e($status) ?>">
          <a href="<?= e(url('/matches/' . $matchId . '/day/' . (int) $day['day_number'])) ?>">
            <span class="game-label">Game <?= (int) $day['day_number'] ?> — <?= e(strtoupper($status)) ?></span>
            <?php if ($status === 'void'): ?>
              <span class="game-result">Void · no winner awarded</span>
            <?php elseif ($status === 'pending'): ?>
              <span class="game-result">
                <?= $outcome['value_a'] !== null ? e(number_format((int) $outcome['value_a'])) : '—' ?>
                vs
                <?= $outcome['value_b'] !== null ? e(number_format((int) $outcome['value_b'])) : '—' ?>
                · provisional
              </span>
            <?php elseif ($outcome['kind'] === 'tie'): ?>
              <span class="game-result">Tie · <?= e(number_format((int) $outcome['value_a'])) ?> vs <?= e(number_format((int) $outcome['value_b'])) ?></span>
            <?php elseif ($outcome['kind'] === 'win'): ?>
              <?php $winner = ($outcome['winner_side'] ?? '') === 'a' ? $m['player_a_name'] : $m['player_b_name'];
                    $loser = ($outcome['winner_side'] ?? '') === 'a' ? $m['player_b_name'] : $m['player_a_name']; ?>
              <span class="game-result"><?= e($winner) ?> defeats <?= e($loser) ?></span>
              <span class="game-meta"><?= e(number_format((int) $outcome['value_a'])) ?> vs <?= e(number_format((int) $outcome['value_b'])) ?> · Margin <?= e(number_format((int) $outcome['margin'])) ?></span>
            <?php endif; ?>
          </a>
          <?php if ($status === 'official'): ?>
            <a class="share-link" href="<?= e(url('/matches/' . $matchId . '/day/' . (int) $day['day_number'] . '/share')) ?>">Share card</a>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      <?php if ($shown === 0): ?>
        <li class="game-row"><span class="game-result">No settled games yet.</span></li>
      <?php endif; ?>
    </ul>
    <p><a href="<?= e(url('/matches/' . $matchId . '/history')) ?>">Full match history</a></p>
  </section>

  <?php if (($s['largest_margin'] ?? null) !== null || ($s['average_a'] ?? null) !== null): ?>
  <section class="secondary-stats" aria-labelledby="stats-heading">
    <h2 id="stats-heading">Series notes</h2>
    <ul>
      <?php if ($s['largest_margin'] !== null): ?><li>Largest official margin: <?= e(number_format((int) $s['largest_margin'])) ?></li><?php endif; ?>
      <?php if ($s['closest_decisive_margin'] !== null): ?><li>Closest decisive game: <?= e(number_format((int) $s['closest_decisive_margin'])) ?></li><?php endif; ?>
      <?php if ($s['average_a'] !== null): ?><li><?= e($m['player_a_name']) ?> avg (official): <?= e(number_format((int) $s['average_a'])) ?></li><?php endif; ?>
      <?php if ($s['average_b'] !== null): ?><li><?= e($m['player_b_name']) ?> avg (official): <?= e(number_format((int) $s['average_b'])) ?></li><?php endif; ?>
      <li>Tie threshold: <?= (int) $m['tie_threshold'] ?> <?= e($m['metric_unit']) ?></li>
      <li>Match timezone: <?= e($m['timezone']) ?></li>
    </ul>
  </section>
  <?php endif; ?>
</article>
