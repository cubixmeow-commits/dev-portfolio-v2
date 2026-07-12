<?php
use SousMeow\Core\Csrf;

/**
 * @var list<array<string, mixed>> $projects
 * @var array<string, mixed>|null  $continue
 * @var array<string, mixed>|null  $featured
 */

$projectStatus = static function (array $p): array {
    $total = (int) $p['recipe_count'];
    $done = (int) $p['approved_count'];
    if ($p['completed_at'] !== null) {
        return ['label' => 'Complete', 'badge' => 'badge-sage', 'action' => 'Open export', 'hint' => 'Every Recipe approved. Your Project Kit is ready to download.'];
    }
    if ($p['pantry_saved_at'] === null) {
        return ['label' => 'Stocking the Pantry', 'badge' => 'badge-amber', 'action' => 'Stock Pantry', 'hint' => 'Add the facts about your product; every prompt cooks from them.'];
    }
    return ['label' => 'Cooking · Recipe ' . min($done + 1, $total) . ' of ' . $total, 'badge' => 'badge-terracotta', 'action' => 'Continue cooking', 'hint' => 'Pick up exactly where you left off.'];
};
?>
<div class="page kitchen-page">
  <header class="kitchen-header">
    <div>
      <h1>My Kitchen</h1>
      <p class="section-sub">Every Project lives here, at the exact step you left it. Nothing burns while you are away.</p>
    </div>
    <a class="button button-ghost button-small" href="<?= e(url('/account/password')) ?>">Change password</a>
  </header>

  <?php if ($projects === []): ?>
    <section class="empty-state kitchen-empty rise-in">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
      <h2>No Projects yet, and that is the fun part</h2>
      <p>A Project is one run through a Cookbook: you stock a Pantry with facts about <em>your</em> product, cook each Recipe by
         copying a prompt into the AI you already use, paste the result back, and approve it. Finished Projects export as a kit of ready-to-publish files.</p>
      <p>The Launch Day Kit is free, takes about 25 minutes, and includes sample responses so you can try the whole loop without opening an AI at all.</p>
      <div class="empty-actions">
        <?php if ($featured !== null): ?>
          <form method="post" action="<?= e(url('/projects')) ?>" data-loading>
            <?= Csrf::field() ?>
            <input type="hidden" name="cookbook" value="<?= e($featured['slug']) ?>">
            <button type="submit" class="button button-primary button-large">Start the <?= e($featured['title']) ?></button>
          </form>
        <?php endif; ?>
        <a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Browse all Cookbooks</a>
      </div>
    </section>
  <?php else: ?>

    <?php if ($continue !== null): $s = $projectStatus($continue); $pct = (int) $continue['recipe_count'] > 0 ? (int) round(100 * (int) $continue['approved_count'] / (int) $continue['recipe_count']) : 0; ?>
      <section class="continue-card card card-pad rise-in">
        <div class="continue-info">
          <p class="continue-eyebrow">Next thing to do</p>
          <h2><?= e($continue['title']) ?></h2>
          <p class="continue-meta"><?= e($continue['cookbook_title']) ?> · <span class="badge <?= e($s['badge']) ?>"><?= e($s['label']) ?></span></p>
          <p class="continue-hint"><?= e($s['hint']) ?></p>
          <div class="progress-track" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
            <div class="progress-fill" style="width: <?= $pct ?>%"></div>
          </div>
          <p class="progress-label"><?= e(plural((int) $continue['approved_count'], 'Recipe')) ?> approved of <?= (int) $continue['recipe_count'] ?></p>
        </div>
        <div class="continue-action">
          <a class="button button-primary button-large" href="<?= e(url('/projects/' . $continue['id'])) ?>"><?= e($s['action']) ?></a>
        </div>
      </section>
    <?php endif; ?>

    <section class="kitchen-projects">
      <div class="section-heading">
        <h2>All Projects</h2>
        <span class="section-sub"><?= e(plural(count($projects), 'Project')) ?></span>
      </div>
      <div class="project-grid">
        <?php foreach ($projects as $p): $s = $projectStatus($p); $pct = (int) $p['recipe_count'] > 0 ? (int) round(100 * (int) $p['approved_count'] / (int) $p['recipe_count']) : 0; ?>
          <article class="project-card card card-hover">
            <div class="project-card-body">
              <div class="project-card-top">
                <span class="badge <?= e($s['badge']) ?>"><?= e($s['label']) ?></span>
                <span class="project-updated"><?= e(time_ago((string) $p['updated_at'])) ?></span>
              </div>
              <h3><a class="project-title-link" href="<?= e(url('/projects/' . $p['id'])) ?>"><?= e($p['title']) ?></a></h3>
              <p class="project-cookbook"><?= e($p['cookbook_title']) ?></p>
              <div class="progress-track">
                <div class="progress-fill <?= $p['completed_at'] !== null ? 'is-complete' : '' ?>" style="width: <?= $pct ?>%"></div>
              </div>
              <p class="progress-label"><?= (int) $p['approved_count'] ?> of <?= (int) $p['recipe_count'] ?> Recipes approved</p>
            </div>
            <div class="project-card-actions">
              <a class="button button-quiet button-small" href="<?= e(url('/projects/' . $p['id'])) ?>"><?= e($s['action']) ?></a>
              <?php if ($p['completed_at'] !== null): ?>
                <a class="button button-ghost button-small" href="<?= e(url('/projects/' . $p['id'] . '/export')) ?>">Exports</a>
              <?php endif; ?>
              <form method="post" action="<?= e(url('/projects/' . $p['id'] . '/delete')) ?>"
                    data-confirm="Delete this Project and all its pasted responses? This cannot be undone.">
                <?= Csrf::field() ?>
                <button type="submit" class="link-button project-delete">Delete</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <?php if ($featured !== null): ?>
      <section class="kitchen-start-new well">
        <div>
          <h3>Start something new</h3>
          <p class="section-sub">Run the <?= e($featured['title']) ?> again for another product, or browse the shelf for what is coming next.</p>
        </div>
        <div class="kitchen-start-actions">
          <form method="post" action="<?= e(url('/projects')) ?>" data-loading>
            <?= Csrf::field() ?>
            <input type="hidden" name="cookbook" value="<?= e($featured['slug']) ?>">
            <button type="submit" class="button button-primary">New <?= e($featured['title']) ?></button>
          </form>
          <a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Browse the Marketplace</a>
        </div>
      </section>
    <?php endif; ?>
  <?php endif; ?>
</div>
