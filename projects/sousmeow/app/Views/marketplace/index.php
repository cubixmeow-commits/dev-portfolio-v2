<?php
/**
 * Marketplace — first place Cookbook / Recipe language is earned.
 *
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
      <h1>Find something to finish</h1>
      <p class="section-sub">We call these guided step-by-step projects <strong>Cookbooks</strong>.
        Pick one, answer a few facts about your project, follow the steps in the AI you already use,
        and leave with finished files.</p>
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
      <h2>Nothing matches "<?= e($query) ?>"</h2>
      <p>Try a broader word, like "launch" or "portfolio", or browse all Cookbooks.</p>
      <div class="empty-actions">
        <a class="button button-primary" href="<?= e(url('/marketplace')) ?>">Show all Cookbooks</a>
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
              <span class="badge badge-outline"><?= e($cookbook['category_name'] ?? '') ?></span>
              <span class="badge badge-neutral cookbook-difficulty"><?= e($cookbook['difficulty'] ?? 'Intermediate') ?></span>
              <?php if ($executable): ?>
                <span class="badge badge-sage badge-dot">Available now</span>
              <?php else: ?>
                <span class="badge badge-neutral">Preview</span>
              <?php endif; ?>
            </div>
            <h2 class="cookbook-title"><?= e($cookbook['title']) ?></h2>
            <p class="cookbook-tagline"><?= e($cookbook['tagline']) ?></p>
            <p class="cookbook-outcome"><?= e($cookbook['outcome']) ?></p>
            <div class="cookbook-meta">
              <span><?= (int) $cookbook['recipe_count'] ?> steps</span>
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
      Portfolio demonstration: twenty-two Cookbooks are ready to run today; two are full previews
      labeled clearly. There is no checkout. You bring your own AI assistant.
    </p>
  <?php endif; ?>
</div>
