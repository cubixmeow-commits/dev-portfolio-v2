<?php
use SousMeow\Core\Csrf;

/**
 * @var array<string, mixed>       $cookbook
 * @var array<string, mixed>|null  $category
 * @var list<array<string, mixed>> $collections
 * @var list<array<string, mixed>> $stages
 * @var list<array<string, mixed>> $recipes
 * @var array<int, list<array<string, mixed>>> $recipeChecks
 * @var list<array<string, mixed>> $fields
 * @var list<array<string, mixed>> $relatedCookbooks
 */
$executable = (int) $cookbook['is_executable'] === 1;
$isPaid = $cookbook['price_cents'] !== null;
$price = $isPaid ? '$' . number_format(((int) $cookbook['price_cents']) / 100, 0) : 'Free';
$runs = (int) ($cookbook['demo_completed_runs'] ?? 0);
$rating = $cookbook['demo_avg_rating'] ?? null;
$relatedCookbooks = $relatedCookbooks ?? [];

$recipesByStage = [];
foreach ($recipes as $recipe) {
    $stagePos = (int) ($recipe['stage_position'] ?? 0);
    $recipesByStage[$stagePos][] = $recipe;
}
$stageMap = [];
foreach ($stages as $stage) {
    $stageMap[(int) $stage['position']] = $stage;
}
?>
<div class="page marketplace-page cookbook-detail accent-<?= e(preg_replace('/[^a-z]/', '', (string) $cookbook['accent'])) ?>">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/marketplace')) ?>">Find something</a>
    <span aria-hidden="true">/</span>
    <span><?= e($cookbook['title']) ?></span>
  </nav>

  <header class="detail-header card">
    <div class="cookbook-band detail-band" aria-hidden="true"></div>
    <div class="detail-header-body">
      <div class="cookbook-top">
        <?php if ($category !== null): ?>
          <a class="badge badge-outline" href="<?= e(url('/categories/' . $category['slug'])) ?>"><?= e($category['name']) ?></a>
        <?php endif; ?>
        <span class="badge badge-neutral"><?= e($cookbook['difficulty'] ?? 'Intermediate') ?></span>
        <?php if ($executable): ?>
          <span class="badge badge-sage badge-dot">Available now</span>
        <?php else: ?>
          <span class="badge badge-neutral">Preview</span>
        <?php endif; ?>
        <span class="badge <?= $isPaid ? 'badge-amber' : 'badge-terracotta' ?>"><?= e($price) ?></span>
      </div>
      <h1><?= e($cookbook['title']) ?></h1>
      <p class="detail-tagline"><?= e($cookbook['tagline']) ?></p>
      <p class="detail-description"><?= e($cookbook['description']) ?></p>
      <dl class="detail-facts">
        <div><dt>Made for</dt><dd><?= e($cookbook['audience']) ?></dd></div>
        <div><dt>You leave with</dt><dd><?= e($cookbook['outcome']) ?></dd></div>
        <div><dt>Time</dt><dd>about <?= (int) $cookbook['est_minutes'] ?> minutes</dd></div>
        <div><dt>Steps</dt><dd><?= e(plural(count($recipes), 'guided step')) ?></dd></div>
        <div><dt>Requires</dt><dd>any AI you already use (a free chat account is usually enough); sample answers included where available</dd></div>
        <?php if ($runs > 0): ?>
          <div><dt>Completed runs</dt><dd><?= e(number_format($runs)) ?> (demo metric)</dd></div>
        <?php endif; ?>
        <?php if ($rating !== null): ?>
          <div><dt>Average rating</dt><dd><?= e(number_format((float) $rating, 1)) ?> / 5 (demo metric)</dd></div>
        <?php endif; ?>
      </dl>

      <?php if ($collections !== []): ?>
        <p class="detail-collections">
          <span class="detail-collections-label mono">Also in</span>
          <?php foreach ($collections as $col): ?>
            <a class="badge badge-neutral" href="<?= e(url('/collections/' . $col['slug'])) ?>"><?= e($col['name']) ?></a>
          <?php endforeach; ?>
        </p>
      <?php endif; ?>

      <div class="detail-cta">
        <?php if ($executable): ?>
          <?php if ($auth): ?>
            <form method="post" action="<?= e(url('/projects')) ?>" data-loading>
              <?= Csrf::field() ?>
              <input type="hidden" name="cookbook" value="<?= e($cookbook['slug']) ?>">
              <button type="submit" class="button button-primary button-large">Start this Cookbook</button>
            </form>
          <?php else: ?>
            <a class="button button-primary button-large" href="<?= e(url('/register')) ?>">Start free to begin</a>
            <p class="detail-cta-note">Have an account? <a href="<?= e(url('/login')) ?>">Sign in</a>.</p>
          <?php endif; ?>
        <?php elseif ($isPaid): ?>
          <div class="well detail-honest">
            <p><strong>Purchases are not open yet.</strong> This Cookbook will sell for <?= e($price) ?> when it ships.
               SousMeow is a portfolio demonstration with no checkout.
               The steps below are the real design, shown as a preview.</p>
          </div>
        <?php else: ?>
          <div class="well detail-honest">
            <p><strong>Coming soon.</strong> This Cookbook is fully designed but not runnable in this build yet.
               Every stage, step, and check below is the real plan. Meanwhile,
               <a href="<?= e(url('/cookbooks/launch-day-kit')) ?>">Launch Day Kit</a>,
               <a href="<?= e(url('/cookbooks/validate-saas-idea')) ?>">Validate a SaaS Idea</a>, and
               <a href="<?= e(url('/cookbooks/plan-youtube-video')) ?>">Plan a YouTube Video</a> are ready to start.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <?php if ($stages !== []): ?>
    <section class="detail-stages">
      <div class="section-heading">
        <h2>Stages</h2>
        <span class="section-sub"><?= e(plural(count($stages), 'stage')) ?> in sequence</span>
      </div>
      <ol class="detail-stage-list">
        <?php foreach ($stages as $stage):
          $stageRecipes = $recipesByStage[(int) $stage['position']] ?? [];
        ?>
          <li class="detail-stage card">
            <div class="detail-stage-head">
              <span class="step-dot"><?= (int) $stage['position'] ?></span>
              <div>
                <h3><?= e($stage['title']) ?></h3>
                <?php if ($stage['summary'] !== ''): ?>
                  <p><?= e($stage['summary']) ?></p>
                <?php endif; ?>
              </div>
              <span class="badge badge-outline detail-stage-count"><?= e(plural(count($stageRecipes), 'step')) ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>
  <?php endif; ?>

  <section class="detail-recipes">
    <div class="section-heading">
      <h2><?= $executable ? 'Workflow steps' : 'Step preview' ?></h2>
      <span class="section-sub"><?= e(plural(count($recipes), 'step')) ?> · completed in order</span>
    </div>

    <?php if ($stages !== []): ?>
      <?php foreach ($stages as $stage):
        $stageRecipes = $recipesByStage[(int) $stage['position']] ?? [];
        if ($stageRecipes === []) {
            continue;
        }
      ?>
        <div class="detail-recipe-stage">
          <h3 class="detail-recipe-stage-title">Stage <?= (int) $stage['position'] ?>: <?= e($stage['title']) ?></h3>
          <ol class="detail-recipe-list">
            <?php foreach ($stageRecipes as $recipe):
              $checks = $recipeChecks[(int) $recipe['id']] ?? [];
              $hasSample = $recipe['example_response'] !== null && $recipe['example_response'] !== '';
            ?>
              <li class="detail-recipe card">
                <span class="step-dot"><?= (int) $recipe['position'] ?></span>
                <div class="detail-recipe-body">
                  <h4><?= e($recipe['title']) ?></h4>
                  <p><?= e($recipe['summary']) ?></p>
                  <?php if ($recipe['why_it_matters'] !== ''): ?>
                    <p class="detail-recipe-why"><?= e($recipe['why_it_matters']) ?></p>
                  <?php endif; ?>
                  <?php if ($checks !== []): ?>
                    <ul class="detail-check-list">
                      <?php foreach ($checks as $check): ?>
                        <li><strong><?= e($check['label']) ?></strong> <?= e($check['help']) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                  <?php if ($hasSample && !$executable): ?>
                    <details class="detail-sample">
                      <summary>Sample output</summary>
                      <pre class="detail-sample-pre"><?= e((string) $recipe['example_response']) ?></pre>
                    </details>
                  <?php endif; ?>
                </div>
                <?php if (!$executable): ?><span class="badge badge-neutral detail-recipe-badge">Preview</span><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <ol class="detail-recipe-list">
        <?php foreach ($recipes as $recipe):
          $checks = $recipeChecks[(int) $recipe['id']] ?? [];
        ?>
          <li class="detail-recipe card">
            <span class="step-dot"><?= (int) $recipe['position'] ?></span>
            <div class="detail-recipe-body">
              <h4><?= e($recipe['title']) ?></h4>
              <p><?= e($recipe['summary']) ?></p>
              <?php if ($recipe['why_it_matters'] !== ''): ?>
                <p class="detail-recipe-why"><?= e($recipe['why_it_matters']) ?></p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </section>

  <?php if ($fields !== []): ?>
    <section class="detail-pantry">
      <div class="section-heading">
        <h2>Your information</h2>
        <span class="section-sub"><?= e(plural(count($fields), 'detail')) ?>, answered once<?= $executable ? '' : ' (preview)' ?></span>
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

  <section class="detail-kit">
    <div class="section-heading">
      <h2>Finished files</h2>
      <span class="section-sub">What you export when every step is approved</span>
    </div>
    <p class="detail-kit-copy">
      <?php if ($executable): ?>
        One Markdown file per approved step, plus <code>kit.html</code> (opens in any browser, offline)
        and a README of what you approved.
      <?php else: ?>
        When this Cookbook ships, you export every approved step as numbered Markdown files plus a README.
        The step list above is that export list.
      <?php endif; ?>
    </p>
    <ul class="detail-kit-list">
      <li><code>kit.html</code> Offline HTML reader for every approved step</li>
      <?php foreach ($recipes as $recipe): ?>
        <li><code><?= sprintf('%02d', (int) $recipe['position']) ?>-<?= e($recipe['slug']) ?>.md</code> <?= e($recipe['title']) ?></li>
      <?php endforeach; ?>
      <li><code>README.md</code> Manifest of what you approved</li>
    </ul>
  </section>

  <?php if ($relatedCookbooks !== []): ?>
    <section class="detail-related" aria-labelledby="related-heading">
      <div class="section-heading">
        <h2 id="related-heading"><?= in_array((string) $cookbook['slug'], [
            'tailor-resume-to-a-job',
            'prep-for-an-interview',
            'write-a-strong-cover-letter',
            'improve-your-linkedin-profile',
            'plan-a-career-change',
            'prepare-for-salary-negotiation',
        ], true) ? 'Continue your job search' : 'Related Cookbooks' ?></h2>
        <span class="section-sub">Nearby guided projects you can start next</span>
      </div>
      <div class="cookbook-grid detail-related-grid">
        <?php foreach ($relatedCookbooks as $c): ?>
          <?php \SousMeow\Core\View::partial('partials/cookbook-card', ['c' => $c]); ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>
