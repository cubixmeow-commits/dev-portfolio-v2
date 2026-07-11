<div class="container leaderboard-wrap">
  <div class="page-head">
    <div>
      <h1>Leaderboard</h1>
      <p>Points from check-ins. Consistency wins.</p>
    </div>
    <nav class="tab-row" aria-label="Time window">
      <a class="tab<?= $window === 'week' ? ' tab-active' : '' ?>" href="<?= e(url('/leaderboard')) ?>">This week</a>
      <a class="tab<?= $window === 'month' ? ' tab-active' : '' ?>" href="<?= e(url('/leaderboard?window=month')) ?>">This month</a>
      <a class="tab<?= $window === 'all' ? ' tab-active' : '' ?>" href="<?= e(url('/leaderboard?window=all')) ?>">All time</a>
    </nav>
  </div>

  <?php if ($rows === []): ?>
    <div class="card">
      <div class="empty">
        <h3>No points on the board yet</h3>
        <p>The first check-in of the <?= e($window === 'all' ? 'platform' : $window) ?> takes the top spot.</p>
      </div>
    </div>
  <?php else: ?>
    <div class="card">
      <ol class="board-list">
        <?php foreach ($rows as $i => $row): ?>
          <li class="board-row<?= $auth !== null && (int) $row['id'] === (int) $auth['id'] ? ' board-row-self' : '' ?>">
            <span class="board-rank num"><?= $i + 1 ?></span>
            <?php Cadence\Core\View::partial('layout/avatar', [
                'user' => $row, 'size' => 36,
                'ring' => $rings[(int) $row['id']] ?? null,
            ]); ?>
            <a class="board-name" href="<?= e(url('/u/' . $row['handle'])) ?>"><?= e($row['display_name']) ?></a>
            <span class="board-points num"><?= fmt_int($row['points']) ?> pts</span>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  <?php endif; ?>
</div>

<?php if ($own !== null && $auth !== null): ?>
  <div class="own-rank-bar" role="status">
    <div class="container own-rank-inner">
      <?php Cadence\Core\View::partial('layout/avatar', ['user' => $auth, 'size' => 28, 'ring' => null]); ?>
      <span>You're <strong class="num">#<?= fmt_int($own['rank']) ?></strong> of <span class="num"><?= fmt_int($own['of']) ?></span></span>
      <span class="muted num"><?= fmt_int($own['points']) ?> pts</span>
    </div>
  </div>
<?php endif; ?>
