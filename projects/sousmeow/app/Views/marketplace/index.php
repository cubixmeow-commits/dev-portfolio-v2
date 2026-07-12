<?php
/**
 * @var string                      $query
 * @var list<array<string, mixed>>  $cookbooks
 */
$accentClass = static fn(array $c): string => 'accent-' . preg_replace('/[^a-z]/', '', (string) $c['accent']);
?>
<div class="page marketplace-page">
  <header class="marketplace-header">
    <div>
      <h1>The Cookbook shelf</h1>
      <p class="section-sub">Each Cookbook is a guided workflow: a Pantry of inputs, a handful of Recipes, and a
         finished kit at the end. One is open for cooking today; the rest show where this kitchen is headed.</p>
    </div>
  </header>

  <form class="marketplace-search" method="get" action="<?= e(url('/marketplace')) ?>" role="search">
    <input class="input" type="search" name="q" value="<?= e($query) ?>"
           placeholder="Search by name, topic, or category" aria-label="Search Cookbooks">
    <button type="submit" class="button button-quiet">Search</button>
    <?php if ($query !== ''): ?><a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Clear</a><?php endif; ?>
  </form>

  <?php if ($cookbooks === []): ?>
    <div class="empty-state">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'searching']); ?>
      <h2>No Cookbooks match "<?= e($query) ?>"</h2>
      <p>The shelf covers marketing, content, research, career, branding, sales, and product work. Try a broader
         word, like "launch" or "interview", or browse the whole shelf; it is only eight Cookbooks deep.</p>
      <div class="empty-actions">
        <a class="button button-primary" href="<?= e(url('/marketplace')) ?>">Show all Cookbooks</a>
      </div>
    </div>
  <?php else: ?>
    <div class="cookbook-grid">
      <?php foreach ($cookbooks as $cookbook): $executable = (int) $cookbook['is_executable'] === 1; ?>
        <a class="cookbook-card card card-hover <?= e($accentClass($cookbook)) ?>" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">
          <div class="cookbook-band" aria-hidden="true"></div>
          <div class="cookbook-body">
            <div class="cookbook-top">
              <span class="badge badge-outline"><?= e($cookbook['category']) ?></span>
              <?php if ($executable): ?>
                <span class="badge badge-sage badge-dot">Available now</span>
              <?php else: ?>
                <span class="badge badge-neutral">Coming soon</span>
              <?php endif; ?>
            </div>
            <h2 class="cookbook-title"><?= e($cookbook['title']) ?></h2>
            <p class="cookbook-tagline"><?= e($cookbook['tagline']) ?></p>
            <div class="cookbook-meta">
              <span><?= e(plural((int) $cookbook['recipe_count'], 'Recipe')) ?></span>
              <span>&middot;</span>
              <span>about <?= (int) $cookbook['est_minutes'] ?> min</span>
              <span class="cookbook-price">
                <?= $cookbook['price_cents'] === null ? 'Free' : '$' . number_format(((int) $cookbook['price_cents']) / 100, 0) ?>
              </span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="marketplace-honesty">
      SousMeow is a portfolio demonstration: the seven "coming soon" Cookbooks are real designs but are not yet
      cookable, and paid Cookbooks cannot be purchased here. No checkout exists, deliberately.
    </p>
  <?php endif; ?>
</div>
