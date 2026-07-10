<?php
/**
 * Knowledge base article — public. Body is stored as Markdown and rendered
 * with the small in-repo renderer (helpers.php), so no HTML from the body
 * reaches the page unescaped.
 */
require __DIR__ . '/../lib/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$a  = $id ? kb_get($id) : null;

if (!$a) {
    hot_header('Article not found', 'kb');
    echo '<div class="page-head"><h1>Article not found</h1>'
       . '<p class="lead"><a href="index.php">Back to the Knowledge Base</a>.</p></div>';
    hot_footer();
    exit;
}

hot_header($a['title'], 'kb');
?>
<p class="crumbs"><a href="index.php">&larr; Knowledge Base</a></p>

<article class="card article">
  <p class="article-meta">
    <span class="kb-tag"><?= e(label($a['category'])) ?></span>
    <span class="kb-tag<?= $a['audience'] === 'technical' ? ' kb-tag--tech' : '' ?>"><?= e(label($a['audience'])) ?></span>
    <span class="muted">Updated <?= fmt_date($a['updated_at']) ?></span>
  </p>
  <div class="prose">
    <?= markdown_to_html((string) $a['body']) ?>
  </div>
</article>

<div class="card card--cta">
  <p>Didn't solve it? <a href="../index.php">Submit a ticket</a> and the IT Support team will help.</p>
</div>
<?php
hot_footer();
