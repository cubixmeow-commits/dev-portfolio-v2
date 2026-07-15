<?php

use Rally\Core\View;
use Rally\Services\MatchScoringService;

/** @var list<array<string,mixed>> $invitations */
/** @var list<array<string,mixed>> $todayLive */
/** @var list<array<string,mixed>> $pendingOfficial */
/** @var list<array<string,mixed>> $activeSummaries */
/** @var list<array<string,mixed>> $upcoming */
/** @var list<array<string,mixed>> $recentCompleted */
/** @var bool $hasAny */

$userId = (int) ($auth['id'] ?? 0);

// Featured match: the user's live game if one exists, otherwise the first active series.
$featured = null;       // ['match' =>, 'summary' =>, 'days' =>, 'day' => ?live day]
$featuredId = null;
if ($todayLive !== []) {
    $live = $todayLive[0];
    foreach ($activeSummaries as $pack) {
        if ((int) $pack['match']['id'] === (int) $live['match']['id']) {
            $featured = $pack + ['day' => $live['day']];
            break;
        }
    }
} elseif ($activeSummaries !== []) {
    $featured = $activeSummaries[0] + ['day' => null];
}
if ($featured !== null) {
    $featuredId = (int) $featured['match']['id'];
}

$scoreFor = static function (array $m, array $s) use ($userId): array {
    // Orient scores around the signed-in player: [yours, theirs, opponent name]
    if ((int) $m['player_a_user_id'] === $userId) {
        return [(int) $s['player_a_wins'], (int) $s['player_b_wins'], (string) $m['player_b_name']];
    }
    return [(int) $s['player_b_wins'], (int) $s['player_a_wins'], (string) $m['player_a_name']];
};
?>
<section class="wrap dash">
  <header class="dash-header">
    <div>
      <p class="t-label">Matchday</p>
      <h1>Today</h1>
    </div>
    <a class="button button-ghost button-small" href="<?= e(url('/matches/create')) ?>">New series</a>
  </header>

  <?php if (!$hasAny): ?>
    <div class="empty-state" role="status">
      <div class="empty-rail" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <h2>No series yet</h2>
      <p>Challenge an opponent to a 14-game Daily Steps series and the scoreboard starts here.</p>
      <p><a class="button button-primary" href="<?= e(url('/matches/create')) ?>">Start a series</a></p>
    </div>
  <?php endif; ?>

  <?php if ($featured !== null):
    $m = $featured['match'];
    $s = $featured['summary'];
    $fDay = $featured['day'];
    [$aWins, $bWins] = [(int) $s['player_a_wins'], (int) $s['player_b_wins']];
    $leaderSide = $aWins > $bWins ? 'a' : ($bWins > $aWins ? 'b' : null);
    $isLive = $fDay !== null;
    $fOutcome = $isLive ? MatchScoringService::dayOutcome($m, $fDay) : null;
    $gameNum = null;
    foreach ($featured['days'] as $d) {
        if (in_array((string) $d['status'], ['live', 'pending'], true)) {
            $gameNum = max((int) $d['day_number'], (int) $gameNum);
        }
    }
  ?>
  <section aria-label="Featured match">
    <a class="feature<?= $isLive ? ' feature--live' : '' ?>" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
      <div class="feature-meta">
        <span class="t-label"><?= e($m['metric_name']) ?></span>
        <?php if ($gameNum !== null): ?>
          <span class="t-label" aria-hidden="true">·</span>
          <span class="t-label">Game <?= $gameNum ?> of <?= (int) $m['length_days'] ?></span>
        <?php endif; ?>
        <span class="board-meta-spacer"></span>
        <?php if ($isLive): ?>
          <span class="status-pill status-live">Live</span>
        <?php else: ?>
          <span class="status-pill status-<?= e((string) $m['status']) ?>"><?= e(ucfirst((string) $m['status'])) ?></span>
        <?php endif; ?>
      </div>

      <div class="feature-score" role="img"
           aria-label="Series score: <?= e($m['player_a_name']) ?> <?= $aWins ?>, <?= e($m['player_b_name']) ?> <?= $bWins ?>">
        <span class="feature-name feature-name--a<?= $leaderSide === 'b' ? ' is-trailing' : '' ?>"><?= e($m['player_a_name']) ?></span>
        <span class="feature-nums t-num"><?= $aWins ?><span class="feature-sep">–</span><?= $bWins ?></span>
        <span class="feature-name feature-name--b<?= $leaderSide === 'a' ? ' is-trailing' : '' ?>"><?= e($m['player_b_name']) ?></span>
      </div>

      <?php View::partial('partials/rail', ['match' => $m, 'days' => $featured['days'], 'mini' => true]); ?>

      <?php if ($isLive && $fOutcome !== null): ?>
        <p class="feature-today">
          <span class="t-label t-label--signal">Today</span>
          <span class="t-num feature-today-vals">
            <?= $fOutcome['value_a'] !== null ? e(number_format((int) $fOutcome['value_a'])) : '—' ?>
            <span aria-hidden="true">·</span>
            <?= $fOutcome['value_b'] !== null ? e(number_format((int) $fOutcome['value_b'])) : '—' ?>
          </span>
          <?php if ($fOutcome['value_a'] !== null && $fOutcome['value_b'] !== null):
            $margin = abs((int) $fOutcome['value_a'] - (int) $fOutcome['value_b']);
            if ($margin >= (int) $m['tie_threshold']):
              $dayLeader = ((int) $fOutcome['value_a'] > (int) $fOutcome['value_b']) ? $m['player_a_name'] : $m['player_b_name'];
          ?>
            <span class="feature-today-lead"><?= e($dayLeader) ?> leads by <span class="t-num"><?= e(number_format($margin)) ?></span></span>
          <?php else: ?>
            <span class="feature-today-lead">Level — within tie threshold</span>
          <?php endif; endif; ?>
        </p>
      <?php endif; ?>
    </a>
  </section>
  <?php endif; ?>

  <div class="dash-columns">
    <?php if ($invitations !== []): ?>
    <section class="dash-section" aria-labelledby="invites-heading">
      <div class="section-head"><h2 id="invites-heading">Invitations</h2></div>
      <ul class="fixture-list">
        <?php foreach ($invitations as $inv): ?>
          <li class="invite-row">
            <div class="invite-main">
              <p class="invite-line"><strong><?= e($inv['player_a_name']) ?></strong> challenged you</p>
              <p class="invite-meta"><?= e($inv['metric_name'] ?? 'Daily Steps') ?> · <?= (int) $inv['length_days'] ?>-game series · starts <span class="t-num"><?= e((new DateTimeImmutable((string) $inv['start_date']))->format('M j, Y')) ?></span></p>
            </div>
            <a class="button button-primary button-small" href="<?= e(url('/matches/' . (int) $inv['id'] . '/accept')) ?>">Respond</a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endif; ?>

    <?php if ($pendingOfficial !== []): ?>
    <section class="dash-section" aria-labelledby="pending-heading">
      <div class="section-head"><h2 id="pending-heading">Pending review</h2></div>
      <ul class="fixture-list">
        <?php foreach ($pendingOfficial as $item):
          $m = $item['match'];
          $d = $item['day'];
        ?>
          <li class="pending-row">
            <a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
              <span class="pending-row-line"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?> — Game <?= (int) $d['day_number'] ?></span>
              <span class="pending-row-meta">Awaiting final device sync</span>
            </a>
            <span class="status-pill status-pending">Pending</span>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <?php endif; ?>
  </div>

  <?php
  $otherActive = array_values(array_filter(
      $activeSummaries,
      static fn(array $pack): bool => (int) $pack['match']['id'] !== $featuredId
  ));
  ?>
  <?php if ($otherActive !== []): ?>
  <section class="dash-section" aria-labelledby="active-heading">
    <div class="section-head"><h2 id="active-heading">Active series</h2></div>
    <ul class="fixture-list">
      <?php foreach ($otherActive as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
        [$mine, $theirs, $opponent] = $scoreFor($m, $s);
      ?>
        <li class="series-row">
          <a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="series-row-vs"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></span>
            <span class="series-row-meta"><?= e(ucfirst((string) $m['status'])) ?> · <?= e($m['metric_name']) ?></span>
          </a>
          <span class="series-row-score t-num" aria-label="Series score <?= (int) $s['player_a_wins'] ?> to <?= (int) $s['player_b_wins'] ?>"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if ($recentCompleted !== []): ?>
  <section class="dash-section" aria-labelledby="done-heading">
    <div class="section-head">
      <h2 id="done-heading">Recently completed</h2>
      <a class="section-aside" href="<?= e(url('/matches')) ?>">All matches</a>
    </div>
    <ul class="fixture-list">
      <?php foreach ($recentCompleted as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
      ?>
        <li class="series-row series-row--done">
          <a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="series-row-vs"><?= e($m['player_a_name']) ?> <span class="t-num"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></span> <?= e($m['player_b_name']) ?></span>
            <span class="series-row-meta"><?= e($m['metric_name']) ?></span>
          </a>
          <span class="status-pill status-<?= !empty($s['is_draw']) ? 'void' : 'official' ?>"><?= !empty($s['is_draw']) ? 'Draw' : 'Final' ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>
</section>
