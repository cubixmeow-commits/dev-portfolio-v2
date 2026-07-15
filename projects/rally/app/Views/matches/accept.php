<?php
/** @var array $match */
/** @var list<array> $sources */
/** @var array $errors */
?>
<section class="wrap-narrow accept-page">
  <header class="page-header">
    <p class="t-label">Match invitation</p>
    <h1><?= e($match['player_a_name'] ?? 'Opponent') ?> challenged you</h1>
    <p class="lede">A <?= (int) $match['length_days'] ?>-game Daily Steps series starting <span class="t-num"><?= e((new DateTimeImmutable((string) $match['start_date']))->format('F j, Y')) ?></span>.</p>
  </header>

  <div class="source-strip" role="note">
    <span class="source-strip-kicker" style="color: var(--ink-2);">Match timezone</span>
    <span class="source-strip-pair"><?= e($match['timezone']) ?></span>
    <p class="source-strip-note">Authoritative for all day boundaries and settlement times.</p>
  </div>

  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/accept')) ?>" class="stack-form">
    <?= \Rally\Core\Csrf::field() ?>
    <label>
      <span>Your declared data source</span>
      <select name="source_id" required>
        <?php foreach ($sources as $src): ?>
          <option value="<?= (int) $src['id'] ?>"><?= e($src['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <span class="hint">Declared equipment is shown on the match — different source classes are flagged.</span>
    </label>
    <button type="submit" class="button button-primary">Accept the series</button>
  </form>
  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/decline')) ?>" class="stack-form" data-confirm="Decline this invitation?">
    <?= \Rally\Core\Csrf::field() ?>
    <button type="submit" class="button button-ghost">Decline</button>
  </form>
</section>
