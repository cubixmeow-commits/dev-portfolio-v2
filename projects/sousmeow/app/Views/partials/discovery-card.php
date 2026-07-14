<?php

use SousMeow\Services\Accent;

/**
 * Compact Cookbook card for discovery surfaces (category index strips,
 * category detail grids). Accent comes through the single mapper.
 *
 * @var array<string, mixed> $c A marketplace listing row (with category_name, recipe_count).
 */
$exec = (int) ($c['is_executable'] ?? 0) === 1;
?>
<a class="disc-card <?= e(Accent::cssClass((string) ($c['accent'] ?? 'terracotta'))) ?>" href="<?= e(url('/cookbooks/' . $c['slug'])) ?>">
  <span class="disc-band" aria-hidden="true"></span>
  <span class="disc-body">
    <span class="disc-top">
      <?php if (!empty($c['category_name'])): ?>
        <span class="badge badge-outline"><?= e($c['category_name']) ?></span>
      <?php endif; ?>
      <?php if ($exec): ?>
        <span class="badge badge-sage badge-dot">Available now</span>
      <?php else: ?>
        <span class="badge badge-neutral">Preview</span>
      <?php endif; ?>
    </span>
    <span class="disc-title"><?= e($c['title']) ?></span>
    <span class="disc-tagline"><?= e($c['tagline']) ?></span>
    <span class="disc-meta mono"><?= e(plural((int) ($c['recipe_count'] ?? 0), 'Recipe')) ?> &middot; ~<?= (int) ($c['est_minutes'] ?? 0) ?> min &middot; <?= e($c['difficulty'] ?? 'Intermediate') ?></span>
  </span>
</a>
