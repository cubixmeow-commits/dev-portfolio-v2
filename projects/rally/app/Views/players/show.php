<?php

use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $player */
/** @var array $stats */
/** @var array $records */
/** @var list<array> $feed */
/** @var string $initials */
?>
<section class="wrap-narrow player-page">
  <header class="player-hero">
    <span class="avatar avatar--l" aria-hidden="true"><?= e($initials) ?></span>
    <div>
      <h1><?= e($player['name']) ?></h1>
      <p class="player-handle">@<?= e($player['username']) ?></p>
    </div>
  </header>

  <div class="section-head"><h2>Competition record</h2></div>
  <dl class="record-grid">
    <div class="record-cell">
      <dt>Classic</dt>
      <dd class="t-num"><?= (int) ($stats['classic_record']['wins'] ?? 0) ?>–<?= (int) ($stats['classic_record']['losses'] ?? 0) ?><?= (int) ($stats['classic_record']['draws'] ?? 0) > 0 ? '–' . (int) $stats['classic_record']['draws'] : '' ?></dd>
      <span class="record-note">Five supported metrics</span>
    </div>
    <div class="record-cell">
      <dt>Baseline</dt>
      <dd class="t-num"><?= (int) ($stats['baseline_record']['wins'] ?? 0) ?>–<?= (int) ($stats['baseline_record']['losses'] ?? 0) ?><?= (int) ($stats['baseline_record']['draws'] ?? 0) > 0 ? '–' . (int) $stats['baseline_record']['draws'] : '' ?></dd>
      <span class="record-note">Steps and Active Minutes</span>
    </div>
    <div class="record-cell">
      <dt>Active</dt>
      <dd class="t-num"><?= count($stats['active_matches']) ?></dd>
      <span class="record-note">open series</span>
    </div>
  </dl>
  <p class="hint">Classic and Baseline are different competition types with different eligible metric pools. Do not combine them into one total.</p>

  <section aria-labelledby="metric-rec-h">
    <div class="section-head"><h2 id="metric-rec-h">Metric records</h2></div>
    <ul class="metric-records">
      <?php foreach ($records['by_metric'] as $rec): ?>
        <li>
          <span class="metric-records-label"><?= e((string) $rec['label']) ?></span>
          <span class="metric-records-value t-num"><?= e((string) $rec['formatted']) ?></span>
        </li>
      <?php endforeach; ?>
      <?php if (!empty($records['competition_records']['classic']['by_metric'])): ?>
        <?php foreach ($records['competition_records']['classic']['by_metric'] as $mr): ?>
          <li>
            <span class="metric-records-label">Classic <?= e((string) $mr['name']) ?></span>
            <span class="metric-records-value t-num"><?= (int) $mr['wins'] ?>–<?= (int) $mr['losses'] ?><?= (int) $mr['draws'] > 0 ? '–' . (int) $mr['draws'] : '' ?></span>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
      <?php if (!empty($records['competition_records']['baseline']['by_metric'])): ?>
        <?php foreach ($records['competition_records']['baseline']['by_metric'] as $mr): ?>
          <li>
            <span class="metric-records-label">Baseline <?= e((string) $mr['name']) ?></span>
            <span class="metric-records-value t-num"><?= (int) $mr['wins'] ?>–<?= (int) $mr['losses'] ?><?= (int) $mr['draws'] > 0 ? '–' . (int) $mr['draws'] : '' ?></span>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </section>

  <section aria-labelledby="baseline-h">
    <div class="section-head"><h2 id="baseline-h">Your recent baselines</h2></div>
    <?php if (empty($records['recent_baselines'])): ?>
      <p class="hint">No canonical history yet.</p>
    <?php else: ?>
      <ul class="metric-records">
        <?php foreach ($records['recent_baselines'] as $bl): ?>
          <li>
            <span class="metric-records-label"><?= e((string) $bl['metric_name']) ?> · <?= e((string) $bl['source_name']) ?></span>
            <span class="metric-records-value t-num">
              <?= e((string) $bl['formatted_mean']) ?>
              <?php if (!empty($bl['available'])): ?>
                · <?= (int) $bl['sample_count'] ?> days
                <?php if (!empty($bl['typical_range']['label'])): ?>
                  · Typical range <?= e((string) $bl['typical_range']['label']) ?>
                <?php endif; ?>
              <?php else: ?>
                · unavailable
              <?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="hint">Your recent baseline and typical range are personal history, not a healthy range or age norm.</p>
    <?php endif; ?>
  </section>

  <section aria-labelledby="active-h">
    <div class="section-head"><h2 id="active-h">Active series</h2></div>
    <?php if ($stats['active_matches'] === []): ?>
      <p class="hint">No active matches.</p>
    <?php else: ?>
      <ul class="fixture-list">
        <?php foreach ($stats['active_matches'] as $row):
          $score = MetricCompetitionService::scoreline($row, $row['summary']);
        ?>
          <li class="series-row">
            <a href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <span class="series-row-vs"><?= e($row['player_a_name']) ?> vs <?= e($row['player_b_name']) ?></span>
              <span class="series-row-meta"><?= e(ucfirst((string) $row['status'])) ?><?= !empty($row['metric_name']) ? ' · ' . e((string) $row['metric_name']) : '' ?></span>
            </a>
            <span class="series-row-score t-num"><?= e($score['primary']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section aria-labelledby="opp-h">
    <div class="section-head"><h2 id="opp-h">Recent opponents</h2></div>
    <?php
      $opponents = [];
      foreach (array_merge($stats['active_matches'], $stats['completed_matches']) as $row) {
          $opp = ((int) $row['player_a_user_id'] === (int) $player['id'])
              ? (string) $row['player_b_name']
              : (string) $row['player_a_name'];
          $oppId = ((int) $row['player_a_user_id'] === (int) $player['id'])
              ? (int) $row['player_b_user_id']
              : (int) $row['player_a_user_id'];
          if (!isset($opponents[$oppId])) {
              $opponents[$oppId] = $opp;
          }
          if (count($opponents) >= 6) {
              break;
          }
      }
    ?>
    <?php if ($opponents === []): ?>
      <p class="hint">No opponents yet.</p>
    <?php else: ?>
      <ul class="opponent-list">
        <?php foreach ($opponents as $oppId => $oppName): ?>
          <li><a href="<?= e(url('/players/' . (int) $oppId)) ?>"><?= e($oppName) ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section aria-labelledby="done-h">
    <div class="section-head"><h2 id="done-h">Completed</h2></div>
    <?php if ($stats['completed_matches'] === []): ?>
      <p class="hint">No completed matches yet.</p>
    <?php else: ?>
      <ul class="fixture-list">
        <?php foreach (array_slice($stats['completed_matches'], 0, 10) as $row):
          $score = MetricCompetitionService::scoreline($row, $row['summary']);
        ?>
          <li class="series-row series-row--done">
            <a href="<?= e(url('/matches/' . (int) $row['id'])) ?>">
              <span class="series-row-vs"><?= e($row['player_a_name']) ?> <span class="t-num"><?= e($score['primary']) ?></span> <?= e($row['player_b_name']) ?></span>
            </a>
            <span class="status-pill status-<?= !empty($row['summary']['is_draw']) ? 'void' : 'official' ?>"><?= !empty($row['summary']['is_draw']) ? 'Draw' : 'Final' ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section aria-labelledby="feed-h">
    <div class="section-head">
      <h2 id="feed-h">Recent competition events</h2>
      <a class="section-aside" href="<?= e(url('/activity')) ?>">Activity</a>
    </div>
    <?php if ($feed === []): ?>
      <p class="hint">No recent events.</p>
    <?php else: ?>
      <ul class="player-feed">
        <?php foreach (array_slice($feed, 0, 8) as $ev): ?>
          <li>
            <span class="player-feed-head"><?= e((string) ($ev['headline'] ?? '')) ?></span>
            <span class="player-feed-body muted"><?= e((string) ($ev['body'] ?? '')) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</section>
