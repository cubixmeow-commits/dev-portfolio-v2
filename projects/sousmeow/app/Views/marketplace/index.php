<?php
/**
 * @var string                      $query
 * @var list<array<string, mixed>>  $cookbooks
 */
$accentClass = static fn(array $c): string => 'accent-' . preg_replace('/[^a-z]/', '', (string) $c['accent']);
$formatRuns = static function (int $n): string {
    if ($n >= 1000) {
        return number_format($n / 1000, 1) . 'k';
    }
    return (string) $n;
};
?>
<div class="page marketplace-page">
  <header class="marketplace-header">
    <div>
      <h1>Explore workflows</h1>
      <p class="section-sub">Guided Cookbooks for launch campaigns, SaaS validation, portfolio building, YouTube planning, and novel development. Add your project details once; each step builds on the last; you stay in control of every review.</p>
    </div>
  </header>

  <form class="marketplace-search" method="get" action="<?= e(url('/marketplace')) ?>" role="search">
    <input class="input" type="search" name="q" value="<?= e($query) ?>"
           placeholder="Search workflows by name, topic, or category" aria-label="Search workflows">
    <button type="submit" class="button button-quiet">Search</button>
    <?php if ($query !== ''): ?><a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Clear</a><?php endif; ?>
  </form>

  <?php if ($cookbooks === []): ?>
    <div class="empty-state">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'searching']); ?>
      <h2>No workflows match "<?= e($query) ?>"</h2>
      <p>Try a broader word, like "launch" or "portfolio", or browse all available Cookbooks.</p>
      <div class="empty-actions">
        <a class="button button-primary" href="<?= e(url('/marketplace')) ?>">Show all workflows</a>
      </div>
    </div>
  <?php else: ?>
    <div class="cookbook-grid">
      <?php foreach ($cookbooks as $cookbook):
        $executable = (int) $cookbook['is_executable'] === 1;
        $runs = (int) ($cookbook['demo_completed_runs'] ?? 0);
        $rating = $cookbook['demo_avg_rating'] ?? null;
      ?>
        <a class="cookbook-card card card-hover <?= e($accentClass($cookbook)) ?>" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">
          <div class="cookbook-band" aria-hidden="true"></div>
          <div class="cookbook-body">
            <div class="cookbook-top">
              <span class="badge badge-outline"><?= e($cookbook['category']) ?></span>
              <span class="badge badge-neutral cookbook-difficulty"><?= e($cookbook['difficulty'] ?? 'Intermediate') ?></span>
              <?php if ($executable): ?>
                <span class="badge badge-sage badge-dot">Available now</span>
              <?php else: ?>
                <span class="badge badge-neutral">Workflow preview</span>
              <?php endif; ?>
            </div>
            <h2 class="cookbook-title"><?= e($cookbook['title']) ?></h2>
            <p class="cookbook-tagline"><?= e($cookbook['tagline']) ?></p>
            <p class="cookbook-outcome"><?= e($cookbook['outcome']) ?></p>
            <div class="cookbook-meta">
              <span><?= e(plural((int) $cookbook['recipe_count'], 'Recipe')) ?></span>
              <span>&middot;</span>
              <span>about <?= (int) $cookbook['est_minutes'] ?> min</span>
              <?php if ($runs > 0): ?>
                <span>&middot;</span>
                <span><?= e($formatRuns($runs)) ?> runs</span>
              <?php endif; ?>
              <?php if ($rating !== null): ?>
                <span class="cookbook-rating" aria-label="Average rating <?= e((string) $rating) ?> out of 5">
                  <?= e(number_format((float) $rating, 1)) ?> &#9733;
                </span>
              <?php endif; ?>
              <span class="cookbook-price">
                <?= $cookbook['price_cents'] === null ? 'Free' : '$' . number_format(((int) $cookbook['price_cents']) / 100, 0) ?>
              </span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="marketplace-honesty">
      SousMeow is a portfolio demonstration: three Cookbooks are fully cookable today; two show complete workflow
      previews with honest "Runner coming soon" labels. No checkout exists, deliberately.
    </p>
  <?php endif; ?>
</div>
