<?php
/** @var list<array> $packs */
?>
<section class="wrap-narrow">
  <header class="page-header matches-header">
    <h1>Matches</h1>
    <a class="button button-ghost button-small" href="<?= e(url('/matches/create')) ?>">New series</a>
  </header>
  <?php if ($packs === []): ?>
    <div class="empty-state">
      <div class="empty-rail" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <h2>No matches yet</h2>
      <p>Create your first 14-game series and this becomes your fixture list.</p>
      <p><a class="button button-primary" href="<?= e(url('/matches/create')) ?>">Start a series</a></p>
    </div>
  <?php else: ?>
    <ul class="fixture-list">
      <?php foreach ($packs as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
        $status = (string) $m['status'];
      ?>
        <li class="series-row<?= $status === 'completed' ? ' series-row--done' : '' ?>">
          <a href="<?= e(url('/matches/' . (int) $m['id'] . ($status === 'invited' ? '/accept' : ''))) ?>">
            <span class="series-row-vs"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></span>
            <span class="series-row-meta">
              <span class="status-pill status-<?= e($status) ?>"><?= e(ucfirst($status)) ?></span>
              <?= e($m['metric_name'] ?? 'Daily Steps') ?> · starts <span class="t-num"><?= e((new DateTimeImmutable((string) $m['start_date']))->format('M j, Y')) ?></span>
            </span>
          </a>
          <?php if ($s): ?>
            <span class="series-row-score t-num" aria-label="Series score <?= (int) $s['player_a_wins'] ?> to <?= (int) $s['player_b_wins'] ?>"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
