<?php
/**
 * Knowledge base index — public. Lists articles grouped by audience so
 * non-technical staff see the plain-language help first.
 */
require __DIR__ . '/../lib/bootstrap.php';

$articles = kb_find();
$groups = ['end_user' => [], 'technical' => []];
foreach ($articles as $a) {
    $groups[$a['audience']][] = $a;
}

hot_header('Knowledge Base', 'kb');
?>
<div class="page-head">
  <h1>Knowledge Base</h1>
  <p class="lead">Step-by-step help for common issues. Many problems have a fix here that's faster than waiting on a ticket.</p>
</div>

<section class="card">
  <h2>For everyone</h2>
  <ul class="kb-list">
    <?php foreach ($groups['end_user'] as $a): ?>
      <li>
        <a href="article.php?id=<?= (int) $a['id'] ?>"><?= e($a['title']) ?></a>
        <span class="kb-tag"><?= e(label($a['category'])) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>

<?php if ($groups['technical']): ?>
<section class="card">
  <h2>For support agents</h2>
  <ul class="kb-list">
    <?php foreach ($groups['technical'] as $a): ?>
      <li>
        <a href="article.php?id=<?= (int) $a['id'] ?>"><?= e($a['title']) ?></a>
        <span class="kb-tag kb-tag--tech">Technical</span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<p class="muted"><a href="../index.php">&larr; Back to the helpdesk</a></p>
<?php
hot_footer();
