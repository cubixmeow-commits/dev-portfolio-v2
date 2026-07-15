<?php

use SousMeow\Core\View;
use SousMeow\Services\Accent;

/**
 * Shared category index: compact directory cards for fast scanning, then a
 * Start Here section and a small number of surfaced Collection strips.
 *
 * @var list<array<string, mixed>> $categories  Visible categories with counts.
 * @var array{collection: array<string, mixed>, cookbooks: list<array<string, mixed>>}|null $startHere
 * @var list<array{collection: array<string, mixed>, cookbooks: list<array<string, mixed>>}> $strips
 */
$mark = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
?>
<div class="page categories-index">

  <header class="cat-hero">
    <p class="cat-kicker mono">Browse by topic</p>
    <h1>Choose what you're making.</h1>
    <p class="cat-hero-lede">Each topic groups guided Cookbooks that take you from a rough goal to finished files, one step at a time.</p>
    <form class="cat-search" method="get" action="<?= e(url('/marketplace')) ?>" role="search">
      <input class="input" type="search" name="q" placeholder="Search Cookbooks by name, topic, or category" aria-label="Search Cookbooks">
      <button type="submit" class="button button-primary">Search</button>
    </form>
  </header>

  <section class="cat-grid-section" aria-labelledby="cat-grid-h">
    <h2 id="cat-grid-h" class="visually-hidden">All categories</h2>
    <ul class="category-grid">
      <?php foreach ($categories as $cat):
          $count = (int) $cat['cookbook_count'];
          $countLabel = $count > 0 ? plural($count, 'Cookbook') : 'No Cookbooks yet';
          $href = url('/categories/' . $cat['slug']);
          $label = (string) $cat['name'] . ' — ' . $countLabel;
          ?>
        <li>
          <a class="category-card category-card-dir <?= e(Accent::cssClass((string) $cat['accent'])) ?>"
             href="<?= e($href) ?>"
             aria-label="<?= e($label) ?>">
            <span class="category-mark" aria-hidden="true"><?= $mark ?></span>
            <span class="category-card-body">
              <span class="category-name"><?= e($cat['name']) ?></span>
              <span class="category-tagline"><?= e($cat['tagline']) ?></span>
              <span class="category-count"><?= e($countLabel) ?></span>
            </span>
            <span class="category-arrow" aria-hidden="true">&rarr;</span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <?php if ($startHere !== null): ?>
    <section class="disc-section" aria-labelledby="start-here-h">
      <div class="disc-section-head">
        <h2 id="start-here-h"><?= e($startHere['collection']['name']) ?></h2>
        <p class="section-sub"><?= e($startHere['collection']['tagline']) ?></p>
      </div>
      <div class="disc-grid">
        <?php foreach ($startHere['cookbooks'] as $c): ?>
          <?php View::partial('partials/discovery-card', ['c' => $c]); ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php foreach ($strips as $strip): ?>
    <section class="disc-section">
      <div class="disc-section-head">
        <h2><?= e($strip['collection']['name']) ?></h2>
        <p class="section-sub"><?= e($strip['collection']['tagline']) ?></p>
      </div>
      <div class="disc-rail">
        <?php foreach ($strip['cookbooks'] as $c): ?>
          <?php View::partial('partials/discovery-card', ['c' => $c]); ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

</div>
