<?php
use Rally\Services\MetricFormatter;

/** @var list<array<string, mixed>> $events */
$pageCss = ['activity'];
?>
<section class="wrap wrap-narrow activity-page">
  <header class="page-header">
    <p class="t-label">Activity</p>
    <h1>Competition feed</h1>
    <p class="lede">Structured match events — results and series updates, not a social timeline.</p>
  </header>

  <?php if ($events === []): ?>
    <div class="empty-state" role="status">
      <h2>No events yet</h2>
      <p>Create a match or wait for days to settle.</p>
    </div>
  <?php else: ?>
    <div class="feed-list">
      <?php foreach ($events as $ev): ?>
        <?php
          $href = !empty($ev['url']) ? url((string) $ev['url']) : null;
          $tag = match ((string) ($ev['type'] ?? '')) {
              'match_completed', 'match_drawn', 'match_official' => 'Final',
              'match_settling', 'final_day' => 'Update',
              'daily_final', 'close_result', 'decisive_result' => 'Game',
              'winning_streak' => 'Streak',
              'personal_record' => 'Record',
              'series_update' => 'Series',
              default => 'Event',
          };
        ?>
        <article class="feed-card">
          <header class="feed-card-head">
            <span class="feed-tag"><?= e($tag) ?></span>
            <?php if (!empty($ev['metric_name'])): ?>
              <span class="feed-metric muted tiny"><?= e((string) $ev['metric_name']) ?></span>
            <?php endif; ?>
          </header>
          <h2 class="feed-title"><?= e((string) ($ev['headline'] ?? 'Update')) ?></h2>
          <p class="feed-body"><?= e((string) ($ev['body'] ?? '')) ?></p>
          <?php if ($href): ?>
            <p class="feed-link"><a href="<?= e($href) ?>">Open match</a></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
