<?php
use SousMeow\Core\Csrf;

/**
 * @var array<string, mixed>            $project
 * @var list<array<string, mixed>>      $recipes
 * @var array<int, string>              $statuses recipe id => status
 * @var array<int, array<string,mixed>> $approved recipe id => approved artifact
 * @var bool                            $ready
 * @var list<array<string, mixed>>      $exports
 */
$projectId = (int) $project['id'];
$doneCount = count($approved);
$total = count($recipes);
?>
<div class="page page-narrow export-page">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/kitchen')) ?>">My Kitchen</a>
    <span aria-hidden="true">/</span>
    <span><?= e($project['title']) ?></span>
    <span aria-hidden="true">/</span>
    <span>Project Kit</span>
  </nav>

  <?php if ($ready): ?>
    <header class="export-hero rise-in">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h1>Your Project Kit is ready</h1>
      <p class="section-sub">Every Recipe is approved. The kit is a zip of tidy Markdown files, one per Recipe,
         a self-contained <code>kit.html</code> reader you can open in any browser, plus a README manifest
         recording your Pantry and what was approved when. Yours to publish anywhere.</p>
    </header>

    <section class="card card-pad kit-contents">
      <h2>What is inside</h2>
      <ul class="kit-file-list">
        <li class="kit-file">
          <span class="kit-file-name mono">kit.html</span>
          <span class="kit-file-desc">Offline HTML reader: every approved Recipe rendered in one page</span>
        </li>
        <li class="kit-file">
          <span class="kit-file-name mono">README.md</span>
          <span class="kit-file-desc">Manifest: your Pantry, file list, and approval provenance</span>
        </li>
        <?php $i = 1; foreach ($recipes as $recipe): $a = $approved[(int) $recipe['id']] ?? null; if ($a === null) continue; ?>
          <li class="kit-file">
            <span class="kit-file-name mono"><?= e(sprintf('%02d-%s.md', $i++, $recipe['slug'])) ?></span>
            <span class="kit-file-desc">
              <?= e($recipe['title']) ?> · v<?= (int) $a['version_no'] ?> approved
              <?php if ($a['source'] === 'example'): ?><span class="badge badge-sample">Sample data</span><?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <form method="post" action="<?= e(url('/projects/' . $projectId . '/export')) ?>" data-loading>
        <?= Csrf::field() ?>
        <button type="submit" class="button button-primary button-large">Pack a fresh kit (.zip)</button>
      </form>
      <p class="export-note">Packing again after revisions creates a new zip; earlier exports stay listed below.</p>
    </section>

  <?php else: ?>
    <header class="export-hero">
      <h1>The kit exports when every Recipe is approved</h1>
      <p class="section-sub">That rule is what makes the export publish-ready: nothing gets in without your
         explicit approval. Here is where this Project stands.</p>
    </header>

    <section class="card card-pad">
      <ul class="kit-progress-list">
        <?php foreach ($recipes as $recipe): $status = $statuses[(int) $recipe['id']] ?? null; ?>
          <li class="kit-progress-item">
            <span class="step-dot <?= $status === 'approved' ? 'is-done' : ($status === 'review' ? 'is-active' : 'is-locked') ?>">
              <?= $status === 'approved' ? '&check;' : (int) $recipe['position'] ?>
            </span>
            <span class="kit-progress-title"><?= e($recipe['title']) ?></span>
            <span class="kit-progress-status">
              <?= $status === 'approved' ? 'Approved' : ($status === 'review' ? 'In review' : 'Not started') ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
      <a class="button button-primary" href="<?= e(url('/projects/' . $projectId)) ?>">
        Continue cooking (<?= $doneCount ?> of <?= $total ?> approved)
      </a>
    </section>
  <?php endif; ?>

  <section class="export-history">
    <div class="section-heading">
      <h2>Past exports</h2>
      <?php if ($exports !== []): ?><span class="section-sub"><?= e(plural(count($exports), 'kit')) ?> packed</span><?php endif; ?>
    </div>

    <?php if ($exports === []): ?>
      <div class="empty-state">
        <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
        <h3>No exports yet</h3>
        <p><?= $ready
            ? 'Pack your first kit above; it takes about a second. Every export is timestamped, so you can revise a Recipe next week and pack a fresh kit without losing this one.'
            : 'Your first packed kit will land here. Approve the remaining Recipes above and this shelf starts filling.' ?></p>
      </div>
    <?php else: ?>
      <ul class="export-list">
        <?php foreach ($exports as $export): ?>
          <li class="export-item card" id="export-<?= (int) $export['id'] ?>">
            <div class="export-item-info">
              <span class="mono export-filename"><?= e($export['filename']) ?></span>
              <span class="export-meta">
                <?= e(plural((int) $export['artifact_count'], 'file')) ?> + manifest ·
                <?= e(number_format((int) $export['file_size'] / 1024, 1)) ?> KB ·
                <?= e(time_ago((string) $export['created_at'])) ?>
              </span>
            </div>
            <a class="button button-ghost button-small" href="<?= e(url('/exports/' . $export['id'] . '/download')) ?>">Download</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</div>
