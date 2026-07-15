<?php
/** @var list<array> $packs */
?>
<section class="page-narrow">
  <header class="page-header">
    <h1>Matches</h1>
    <p><a class="button button-primary button-small" href="<?= e(url('/matches/create')) ?>">New series</a></p>
  </header>
  <?php if ($packs === []): ?>
    <div class="empty-state">
      <h2>No matches yet</h2>
      <p>Create your first 14-game series.</p>
    </div>
  <?php else: ?>
    <ul class="dash-list">
      <?php foreach ($packs as $pack):
        $m = $pack['match'];
        $s = $pack['summary'];
      ?>
        <li>
          <a class="dash-card" href="<?= e(url('/matches/' . (int) $m['id'])) ?>">
            <span class="status-pill status-<?= e((string) $m['status']) ?>"><?= e(strtoupper((string) $m['status'])) ?></span>
            <span class="dash-vs"><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></span>
            <?php if ($s): ?>
              <span class="dash-score"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></span>
            <?php endif; ?>
            <span class="dash-meta"><?= e($m['metric_name'] ?? 'Daily Steps') ?> · <?= e($m['start_date']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
