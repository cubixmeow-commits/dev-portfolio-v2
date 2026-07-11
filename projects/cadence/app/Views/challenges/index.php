<?php use Cadence\Models\Challenge; ?>
<div class="container">
  <div class="page-head">
    <div>
      <h1>Challenges</h1>
      <p><?= fmt_int($result['total']) ?> challenge<?= $result['total'] === 1 ? '' : 's' ?> to build with.</p>
    </div>
  </div>

  <form class="filters" method="get" action="<?= e(url('/challenges')) ?>">
    <input class="input filter-search" type="search" name="q" value="<?= e($q) ?>"
           placeholder="Search challenges" aria-label="Search challenges">

    <select class="input filter-select" name="category" aria-label="Category">
      <option value="">All categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat) ?>"<?= $category === $cat ? ' selected' : '' ?>><?= e(ucfirst($cat)) ?></option>
      <?php endforeach; ?>
    </select>

    <select class="input filter-select" name="status" aria-label="Status">
      <option value="">Any status</option>
      <option value="active"<?= $status === 'active' ? ' selected' : '' ?>>Active</option>
      <option value="upcoming"<?= $status === 'upcoming' ? ' selected' : '' ?>>Upcoming</option>
      <option value="ended"<?= $status === 'ended' ? ' selected' : '' ?>>Ended</option>
    </select>

    <select class="input filter-select" name="sort" aria-label="Sort">
      <option value="popular"<?= $sort === 'popular' ? ' selected' : '' ?>>Most popular</option>
      <option value="start"<?= $sort === 'start' ? ' selected' : '' ?>>Newest start</option>
    </select>

    <button class="btn btn-secondary" type="submit">Apply</button>
  </form>

  <?php if ($result['rows'] === []): ?>
    <div class="card" style="margin-top: var(--sp-5)">
      <div class="empty">
        <h3>Nothing matches those filters</h3>
        <p>Try clearing the search or picking a different category.</p>
        <p style="margin-top: var(--sp-4)"><a class="btn btn-secondary" href="<?= e(url('/challenges')) ?>">Clear filters</a></p>
      </div>
    </div>
  <?php else: ?>
    <div class="challenge-grid">
      <?php foreach ($result['rows'] as $c): ?>
        <?php $st = Challenge::status($c); ?>
        <a class="card challenge-card" href="<?= e(url('/challenges/' . $c['slug'])) ?>">
          <div class="challenge-cover cover-<?= e($c['cover_style']) ?>">
            <span class="cover-ring" aria-hidden="true"></span>
          </div>
          <div class="challenge-body">
            <div class="challenge-meta">
              <span class="pill"><?= e(ucfirst((string) $c['category'])) ?></span>
              <?php if ($st === 'active'): ?>
                <span class="pill pill-accent"><?= e((string) Challenge::daysLeft($c)) ?> days left</span>
              <?php elseif ($st === 'upcoming'): ?>
                <span class="pill">Starts <?= e(date('M j', strtotime((string) $c['start_date']))) ?></span>
              <?php else: ?>
                <span class="pill">Ended</span>
              <?php endif; ?>
            </div>
            <h3><?= e($c['title']) ?></h3>
            <p class="muted small challenge-desc"><?= e(mb_strimwidth((string) $c['description'], 0, 110, '...')) ?></p>
            <div class="challenge-foot">
              <span class="muted small"><span class="num"><?= fmt_int($c['participant_count']) ?></span> member<?= (int) $c['participant_count'] === 1 ? '' : 's' ?></span>
              <?php if (in_array((int) $c['id'], $joinedIds, true)): ?>
                <span class="pill pill-accent">Joined</span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($result['pages'] > 1): ?>
      <?php
        $qs = static function (int $p) use ($q, $category, $status, $sort): string {
            return url('/challenges?' . http_build_query(array_filter([
                'q' => $q, 'category' => $category, 'status' => $status,
                'sort' => $sort !== 'popular' ? $sort : '', 'page' => $p > 1 ? $p : '',
            ], static fn($v) => $v !== '' && $v !== null)));
        };
      ?>
      <nav class="pagination" aria-label="Pages">
        <?php if ($result['page'] > 1): ?>
          <a class="btn btn-secondary" href="<?= e($qs($result['page'] - 1)) ?>">Previous</a>
        <?php endif; ?>
        <span class="muted small">Page <span class="num"><?= e((string) $result['page']) ?></span> of <span class="num"><?= e((string) $result['pages']) ?></span></span>
        <?php if ($result['page'] < $result['pages']): ?>
          <a class="btn btn-secondary" href="<?= e($qs($result['page'] + 1)) ?>">Next</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>
