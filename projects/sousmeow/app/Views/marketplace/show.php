<?php
use SousMeow\Core\Csrf;

/**
 * @var array<string, mixed>       $cookbook
 * @var list<array<string, mixed>> $recipes
 * @var list<array<string, mixed>> $fields
 */
$executable = (int) $cookbook['is_executable'] === 1;
$isPaid = $cookbook['price_cents'] !== null;
$price = $isPaid ? '$' . number_format(((int) $cookbook['price_cents']) / 100, 0) : 'Free';
?>
<div class="page marketplace-page cookbook-detail accent-<?= e(preg_replace('/[^a-z]/', '', (string) $cookbook['accent'])) ?>">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/marketplace')) ?>">Marketplace</a>
    <span aria-hidden="true">/</span>
    <span><?= e($cookbook['title']) ?></span>
  </nav>

  <header class="detail-header card">
    <div class="cookbook-band detail-band" aria-hidden="true"></div>
    <div class="detail-header-body">
      <div class="cookbook-top">
        <span class="badge badge-outline"><?= e($cookbook['category']) ?></span>
        <?php if ($executable): ?>
          <span class="badge badge-sage badge-dot">Available now</span>
        <?php else: ?>
          <span class="badge badge-neutral">Coming soon · preview</span>
        <?php endif; ?>
        <span class="badge <?= $isPaid ? 'badge-amber' : 'badge-terracotta' ?>"><?= e($price) ?></span>
      </div>
      <h1><?= e($cookbook['title']) ?></h1>
      <p class="detail-tagline"><?= e($cookbook['tagline']) ?></p>
      <p class="detail-description"><?= e($cookbook['description']) ?></p>
      <dl class="detail-facts">
        <div><dt>Made for</dt><dd><?= e($cookbook['audience']) ?></dd></div>
        <div><dt>You leave with</dt><dd><?= e($cookbook['outcome']) ?></dd></div>
        <div><dt>Time</dt><dd>about <?= (int) $cookbook['est_minutes'] ?> minutes of your attention</dd></div>
        <div><dt>Requires</dt><dd>any AI you already use; sample responses included for the tour</dd></div>
      </dl>

      <div class="detail-cta">
        <?php if ($executable): ?>
          <?php if ($auth): ?>
            <form method="post" action="<?= e(url('/projects')) ?>" data-loading>
              <?= Csrf::field() ?>
              <input type="hidden" name="cookbook" value="<?= e($cookbook['slug']) ?>">
              <button type="submit" class="button button-primary button-large">Start cooking</button>
            </form>
          <?php else: ?>
            <a class="button button-primary button-large" href="<?= e(url('/register')) ?>">Create a free account to start</a>
            <p class="detail-cta-note">Have an account? <a href="<?= e(url('/login')) ?>">Sign in</a> and start from your Kitchen.</p>
          <?php endif; ?>
        <?php elseif ($isPaid): ?>
          <div class="well detail-honest">
            <p><strong>Purchases are not open yet.</strong> This Cookbook will sell for <?= e($price) ?> when it ships.
               SousMeow is a portfolio demonstration and deliberately has no checkout, so nothing here pretends to charge you.
               The Recipe list below is the real design, shown as a preview.</p>
          </div>
        <?php else: ?>
          <div class="well detail-honest">
            <p><strong>Coming soon, free.</strong> This Cookbook is designed but not yet cookable in this build.
               The Recipe list below is the real plan, shown as a preview. In the meantime, the
               <a href="<?= e(url('/cookbooks/launch-day-kit')) ?>">Launch Day Kit</a> is fully open.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <section class="detail-recipes">
    <div class="section-heading">
      <h2><?= $executable ? 'The Recipes' : 'Recipe preview' ?></h2>
      <span class="section-sub"><?= e(plural(count($recipes), 'Recipe')) ?>, cooked in order</span>
    </div>
    <ol class="detail-recipe-list">
      <?php foreach ($recipes as $recipe): ?>
        <li class="detail-recipe card">
          <span class="step-dot"><?= (int) $recipe['position'] ?></span>
          <div>
            <h3><?= e($recipe['title']) ?></h3>
            <p><?= e($recipe['summary']) ?></p>
            <?php if ($executable && $recipe['why_it_matters'] !== ''): ?>
              <p class="detail-recipe-why"><?= e($recipe['why_it_matters']) ?></p>
            <?php endif; ?>
          </div>
          <?php if (!$executable): ?><span class="badge badge-neutral detail-recipe-badge">Preview</span><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </section>

  <?php if ($executable && $fields !== []): ?>
    <section class="detail-pantry">
      <div class="section-heading">
        <h2>What the Pantry asks for</h2>
        <span class="section-sub"><?= e(plural(count($fields), 'ingredient')) ?>, filled once</span>
      </div>
      <ul class="detail-pantry-grid">
        <?php foreach ($fields as $field): ?>
          <li class="detail-pantry-item well">
            <strong><?= e($field['label']) ?></strong>
            <span><?= e($field['help']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>
</div>
