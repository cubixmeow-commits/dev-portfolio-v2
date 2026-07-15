<?php

use SousMeow\Services\Accent;

/**
 * Shared compact Cookbook listing card for marketplace, Explore, category
 * pages, collections, homepage shelf, and search results.
 *
 * @var array<string, mixed> $c  Listing row (title, tagline, difficulty,
 *                               est_minutes, recipe_count, is_executable,
 *                               price_cents, accent, optional category_name,
 *                               demo_avg_rating).
 */
$exec = (int) ($c['is_executable'] ?? 0) === 1;
$accent = Accent::cssClass((string) ($c['accent'] ?? 'terracotta'));
$steps = (int) ($c['recipe_count'] ?? 0);
$minutes = (int) ($c['est_minutes'] ?? 0);
$difficulty = (string) ($c['difficulty'] ?? 'Intermediate');
$rating = $c['demo_avg_rating'] ?? null;
$priceLabel = ($c['price_cents'] ?? null) === null
    ? 'Free'
    : '$' . number_format(((int) $c['price_cents']) / 100, 0);
$metaParts = [
    $steps . ' ' . ($steps === 1 ? 'step' : 'steps'),
    $minutes . ' min',
    $difficulty,
];
if ($rating !== null && $rating !== '') {
    $metaParts[] = number_format((float) $rating, 1) . ' ★';
}
$metaParts[] = $priceLabel;
?>
<a class="cookbook-card <?= e($accent) ?>" href="<?= e(url('/cookbooks/' . $c['slug'])) ?>">
  <span class="cookbook-card-main">
    <span class="cookbook-top">
      <?php if (!empty($c['category_name'])): ?>
        <span class="badge badge-outline cookbook-badge"><?= e($c['category_name']) ?></span>
      <?php endif; ?>
      <?php if ($exec): ?>
        <span class="badge badge-sage badge-dot cookbook-badge">Available</span>
      <?php else: ?>
        <span class="badge badge-neutral cookbook-badge">Preview</span>
      <?php endif; ?>
    </span>
    <span class="cookbook-title"><?= e($c['title']) ?></span>
    <span class="cookbook-blurb"><?= e($c['tagline']) ?></span>
    <span class="cookbook-meta mono"><?= e(implode(' · ', $metaParts)) ?></span>
  </span>
  <span class="cookbook-arrow" aria-hidden="true">&rarr;</span>
</a>
