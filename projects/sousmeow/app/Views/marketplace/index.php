<?php
/**
 * Marketplace — first place Cookbook / Recipe language is earned.
 *
 * @var string                      $query
 * @var list<array<string, mixed>>  $cookbooks
 */
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
      <?php foreach ($cookbooks as $cookbook): ?>
        <?php \SousMeow\Core\View::partial('partials/cookbook-card', ['c' => $cookbook]); ?>
      <?php endforeach; ?>
    </div>
    <p class="marketplace-honesty">
      Portfolio demonstration: twenty-two Cookbooks are ready to run today; two are full previews
      labeled clearly. There is no checkout. You bring your own AI assistant.
    </p>
  <?php endif; ?>
</div>
