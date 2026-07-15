<?php
/** @var array $match */
/** @var list<array> $sources */
/** @var array $errors */
?>
<section class="page-narrow">
  <h1>Accept invitation</h1>
  <p><?= e($match['player_a_name'] ?? 'Opponent') ?> invited you to a <?= (int) $match['length_days'] ?>-game Daily Steps series starting <?= e($match['start_date']) ?>.</p>
  <p>Match timezone: <strong><?= e($match['timezone']) ?></strong> (authoritative for all day boundaries).</p>
  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/accept')) ?>" class="stack-form">
    <?= \Rally\Core\Csrf::field() ?>
    <label>
      <span>Your declared data source</span>
      <select name="source_id" required>
        <?php foreach ($sources as $src): ?>
          <option value="<?= (int) $src['id'] ?>"><?= e($src['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button type="submit" class="button button-primary">Accept</button>
  </form>
  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/decline')) ?>" class="stack-form" data-confirm="Decline this invitation?">
    <?= \Rally\Core\Csrf::field() ?>
    <button type="submit" class="button button-ghost">Decline</button>
  </form>
</section>
