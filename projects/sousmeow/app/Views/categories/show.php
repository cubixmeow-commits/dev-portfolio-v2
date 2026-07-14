<?php

use SousMeow\Core\View;
use SousMeow\Models\Category;
use SousMeow\Services\Accent;

/**
 * Shared category detail: one template for all twelve categories. Shows the
 * category's Cookbooks (with filtering) plus the Collections those Cookbooks
 * appear in.
 *
 * @var array<string, mixed>       $category
 * @var list<array<string, mixed>> $cookbooks
 * @var int                        $totalInCategory
 * @var list<string>               $levels
 * @var bool                       $hasExecutable
 * @var bool                       $hasPreview
 * @var list<array<string, mixed>> $featuredIn
 * @var string                     $filterLevel
 * @var string                     $filterShow
 */
$slug = (string) $category['slug'];
$accent = Accent::cssClass((string) $category['accent']);
$mark = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';

/** Build a filter URL, keeping only the parts that are set. */
$filterUrl = static function (?string $level, ?string $show) use ($slug): string {
    $q = [];
    if ($level !== null && $level !== '') { $q['level'] = $level; }
    if ($show !== null && $show !== '') { $q['show'] = $show; }
    return url('/categories/' . $slug) . ($q !== [] ? '?' . http_build_query($q) : '');
};
$noFilter = $filterLevel === '' && $filterShow === '';
?>
<div class="page category-detail <?= e($accent) ?>">

  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/categories')) ?>">Browse by topic</a>
    <span aria-hidden="true">/</span>
    <span><?= e($category['name']) ?></span>
  </nav>

  <header class="cat-detail-hero">
    <span class="category-mark" aria-hidden="true"><?= $mark ?></span>
    <h1><?= e($category['name']) ?></h1>
    <p class="cat-detail-tagline"><?= e($category['tagline']) ?></p>
    <ul class="category-outcomes mono">
      <?php foreach (Category::outcomes($category) as $outcome): ?>
        <li><?= e($outcome) ?></li>
      <?php endforeach; ?>
    </ul>
    <p class="cat-detail-desc"><?= e($category['description']) ?></p>
    <p class="cat-detail-count mono"><?= $totalInCategory > 0 ? e(plural($totalInCategory, 'Cookbook')) : 'No Cookbooks yet' ?></p>
  </header>

  <?php if ($totalInCategory > 1): ?>
    <div class="cat-filters" role="group" aria-label="Filter Cookbooks">
      <span class="cat-filter-label mono">Filter</span>
      <a class="chip <?= $noFilter ? 'is-on' : '' ?>" href="<?= e($filterUrl(null, null)) ?>">All</a>
      <?php foreach ($levels as $lv): ?>
        <a class="chip <?= $filterLevel === $lv ? 'is-on' : '' ?>" href="<?= e($filterUrl($lv, $filterShow)) ?>"><?= e($lv) ?></a>
      <?php endforeach; ?>
      <?php if ($hasExecutable && $hasPreview): ?>
        <a class="chip <?= $filterShow === 'available' ? 'is-on' : '' ?>" href="<?= e($filterUrl($filterLevel, 'available')) ?>">Available now</a>
        <a class="chip <?= $filterShow === 'preview' ? 'is-on' : '' ?>" href="<?= e($filterUrl($filterLevel, 'preview')) ?>">Preview</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($cookbooks === []): ?>
    <div class="empty-state">
      <?php View::partial('partials/mascot', ['pose' => 'searching']); ?>
      <?php if ($totalInCategory === 0): ?>
        <h2>No Cookbooks here yet</h2>
        <p>This topic is ready for its first Cookbooks. In the meantime,
           <a href="<?= e(url('/categories')) ?>">browse another topic</a> or
           <a href="<?= e(url('/marketplace')) ?>">find something to finish</a>.</p>
      <?php else: ?>
        <h2>Nothing matches those filters</h2>
        <p>Try a different filter, or <a href="<?= e(url('/categories/' . $slug)) ?>">see every Cookbook in <?= e($category['name']) ?></a>.</p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="disc-grid cat-detail-grid">
      <?php foreach ($cookbooks as $c): ?>
        <?php View::partial('partials/discovery-card', ['c' => $c]); ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($featuredIn !== []): ?>
    <section class="cat-featured-in" aria-labelledby="featured-in-h">
      <h2 id="featured-in-h">Also appears in</h2>
      <ul class="collection-tags">
        <?php foreach ($featuredIn as $col): ?>
          <li class="collection-tag <?= e(Accent::cssClass((string) $col['accent'])) ?>">
            <a href="<?= e(url('/collections/' . $col['slug'])) ?>"><?= e($col['name']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

</div>
