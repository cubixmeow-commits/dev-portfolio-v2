<?php
use SousMeow\Core\Csrf;

/**
 * @var int    $projectId
 * @var string $buttonClass  optional extra classes on the submit button
 * @var string $label        button text
 */
$buttonClass = $buttonClass ?? 'button button-ghost button-small project-delete';
$label = $label ?? 'Delete project';
?>
<form method="post" action="<?= e(url('/projects/' . $projectId . '/delete')) ?>"
      class="project-delete-form"
      data-confirm="Delete this Project and all its pasted responses? This cannot be undone.">
  <?= Csrf::field() ?>
  <button type="submit" class="<?= e($buttonClass) ?>"><?= e($label) ?></button>
</form>
