<?php
use SousMeow\Core\Csrf;
use SousMeow\Models\PantryField;

/**
 * @var array<string, mixed>        $project
 * @var list<array<string, mixed>>  $fields
 * @var array<int, mixed>           $values  field id => string|array
 * @var array<int, string>          $errors  field id => message
 * @var bool                        $usingSample
 */
$stocked = $project['pantry_saved_at'] !== null;
?>
<div class="page page-narrow pantry-page">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/kitchen')) ?>">My Kitchen</a>
    <span aria-hidden="true">/</span>
    <span><?= e($project['title']) ?></span>
  </nav>

  <header class="pantry-header rise-in">
    <h1><?= $stocked ? 'Project details' : 'Add project details' ?></h1>
    <p class="pantry-lede">
      Your Pantry holds this project's facts — the single source of truth for every prompt.
      Each workflow step quotes these details exactly and is told to invent nothing beyond them.
      <?= $stocked ? 'Editing a field changes future prompts; work you already approved stays as it is.' : 'Fill it once and every step builds from it.' ?>
    </p>
  </header>

  <?php if ($usingSample): ?>
    <div class="well sample-note" role="status">
      <span class="badge badge-sample">Sample data</span>
      <p>The form is prefilled with the sample Pantry for a fictional product called Driftlog. Nothing is saved yet;
         look it over, then save it as-is to tour the product, or replace it with your own ingredients.</p>
    </div>
  <?php elseif (!$stocked): ?>
    <div class="well sample-note">
      <p>Just looking around? <a href="<?= e(url('/projects/' . $project['id'] . '/pantry?sample=1')) ?>">Fill the form with a sample Pantry</a>
         (clearly marked) and see the whole loop without writing a word.</p>
    </div>
  <?php endif; ?>

  <form class="card card-pad pantry-form" method="post" action="<?= e(url('/projects/' . $project['id'] . '/pantry')) ?>" data-loading novalidate>
    <?= Csrf::field() ?>

    <?php foreach ($fields as $field):
        $fieldId = (int) $field['id'];
        $name = 'field_' . $fieldId;
        $value = $values[$fieldId] ?? '';
        $error = $errors[$fieldId] ?? null;
        $options = PantryField::options($field);
    ?>
      <div class="field">
        <label class="field-label" for="<?= e($name) ?>"><?= e($field['label']) ?><?php if (!(int) $field['required']): ?> <span class="field-optional">optional</span><?php endif; ?></label>
        <?php if ($field['help'] !== ''): ?><p class="field-help"><?= e($field['help']) ?></p><?php endif; ?>

        <?php if ($field['type'] === 'textarea'): ?>
          <textarea class="textarea <?= $error ? 'input-invalid' : '' ?>" id="<?= e($name) ?>" name="<?= e($name) ?>"
                    placeholder="<?= e($field['placeholder']) ?>" maxlength="5000"><?= e(is_string($value) ? $value : '') ?></textarea>

        <?php elseif ($field['type'] === 'select'): ?>
          <select class="select <?= $error ? 'input-invalid' : '' ?>" id="<?= e($name) ?>" name="<?= e($name) ?>">
            <option value="" <?= $value === '' ? 'selected' : '' ?> disabled>Choose one</option>
            <?php foreach ($options as $option): ?>
              <option value="<?= e($option) ?>" <?= $value === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
          </select>

        <?php elseif ($field['type'] === 'multiselect'): ?>
          <div class="choice-grid" role="group" aria-labelledby="<?= e($name) ?>">
            <?php $picked = is_array($value) ? $value : []; ?>
            <?php foreach ($options as $i => $option): ?>
              <label class="choice-chip">
                <input type="checkbox" name="<?= e($name) ?>[]" value="<?= e($option) ?>" <?= in_array($option, $picked, true) ? 'checked' : '' ?>>
                <span><?= e($option) ?></span>
              </label>
            <?php endforeach; ?>
          </div>

        <?php elseif ($field['type'] === 'number'): ?>
          <input class="input input-number <?= $error ? 'input-invalid' : '' ?>" type="number" inputmode="decimal" min="0" step="any"
                 id="<?= e($name) ?>" name="<?= e($name) ?>" value="<?= e(is_string($value) ? $value : '') ?>"
                 placeholder="<?= e($field['placeholder']) ?>">

        <?php elseif ($field['type'] === 'url'): ?>
          <input class="input <?= $error ? 'input-invalid' : '' ?>" type="url" id="<?= e($name) ?>" name="<?= e($name) ?>"
                 value="<?= e(is_string($value) ? $value : '') ?>" placeholder="<?= e($field['placeholder']) ?>" maxlength="300">

        <?php else: ?>
          <input class="input <?= $error ? 'input-invalid' : '' ?>" type="text" id="<?= e($name) ?>" name="<?= e($name) ?>"
                 value="<?= e(is_string($value) ? $value : '') ?>" placeholder="<?= e($field['placeholder']) ?>" maxlength="200">
        <?php endif; ?>

        <?php if ($error): ?><p class="field-error"><?= e($error) ?></p><?php endif; ?>
      </div>
    <?php endforeach; ?>

    <div class="pantry-actions">
      <button type="submit" class="button button-primary button-large">
        <?= $stocked ? 'Save project details' : 'Save and start first step' ?>
      </button>
      <?php if ($stocked): ?>
        <a class="button button-ghost" href="<?= e(url('/projects/' . $project['id'])) ?>">Back to project</a>
      <?php endif; ?>
    </div>
    <p class="pantry-next-note">
      <?= $stocked ? 'Saving returns you to your current step with prompts rebuilt from the new values.'
                   : 'Next: the first step turns these details into a ready-to-run prompt. Copy it, run it in your AI, and paste the answer back.' ?>
    </p>
  </form>
</div>
