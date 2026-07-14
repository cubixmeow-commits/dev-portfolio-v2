<?php

use SousMeow\Core\View;

/**
 * Shared Collection detail: one template for every Collection. Renders the
 * Collection's resolved Cookbooks (membership handled by CollectionResolver,
 * regardless of editorial/dynamic/attribute type).
 *
 * @var array<string, mixed>       $collection
 * @var list<array<string, mixed>> $cookbooks
 */
use SousMeow\Services\Accent;

$accent = Accent::cssClass((string) $collection['accent']);
?>
<div class="page collection-detail <?= e($accent) ?>">

  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/categories')) ?>">Browse by topic</a>
    <span aria-hidden="true">/</span>
    <span><?= e($collection['name']) ?></span>
  </nav>

  <header class="cat-detail-hero collection-hero">
    <p class="cat-kicker mono">Collection</p>
    <h1><?= e($collection['name']) ?></h1>
    <p class="cat-detail-tagline"><?= e($collection['tagline']) ?></p>
    <p class="cat-detail-desc"><?= e($collection['description']) ?></p>
    <p class="cat-detail-count mono"><?= e(plural(count($cookbooks), 'Cookbook')) ?></p>
  </header>

  <div class="disc-grid cat-detail-grid">
    <?php foreach ($cookbooks as $c): ?>
      <?php View::partial('partials/discovery-card', ['c' => $c]); ?>
    <?php endforeach; ?>
  </div>

</div>
