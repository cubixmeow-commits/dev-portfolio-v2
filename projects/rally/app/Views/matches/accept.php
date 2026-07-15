<?php

use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var array $match */
/** @var list<array> $sources */
/** @var array|null $baselinePreview */
/** @var array $comparability */
/** @var int|null $selectedSourceId */
/** @var array $errors */

$isBaseline = MetricCompetitionService::isBaseline($match);
$ctype = MetricCompetitionService::competitionType($match);
$startLabel = (new DateTimeImmutable((string) $match['start_date']))->format('F j, Y');
$metricName = (string) ($match['metric_name'] ?? 'Metric');
?>
<section class="wrap-narrow accept-page">
  <header class="page-header">
    <p class="t-label">Match invitation</p>
    <h1><?= e($match['player_a_name'] ?? 'Opponent') ?> challenged you</h1>
    <p class="lede">
      <?= e($metricName) ?> · <?= e(MetricCompetitionService::competitionTypeLabel($ctype)) ?> ·
      <?= (int) $match['length_days'] ?>-<?= MetricCompetitionService::isSeriesAverage($match) ? 'day' : 'game' ?> series starting
      <span class="t-num"><?= e($startLabel) ?></span>.
    </p>
    <p class="hint"><?= e(MetricCompetitionService::competitionTypePromise($ctype)) ?></p>
  </header>

  <div class="source-strip" role="note">
    <span class="source-strip-kicker" style="color: var(--ink-2);">Match timezone</span>
    <span class="source-strip-pair"><?= e($match['timezone']) ?></span>
    <p class="source-strip-note">Authoritative for all day boundaries and settlement times.</p>
  </div>

  <div class="source-strip" role="note">
    <span class="source-strip-kicker" style="color: var(--ink-2);">Their declared source</span>
    <span class="source-strip-pair"><?= e((string) ($match['player_a_source_name'] ?? '—')) ?></span>
  </div>

  <?php if (!empty($comparability['mismatch'])): ?>
    <aside class="source-warning" role="alert">
      <strong><?= e($comparability['title']) ?></strong>
      <p><?= e($comparability['message']) ?></p>
    </aside>
  <?php endif; ?>

  <?php if ($isBaseline): ?>
    <aside class="source-warning" role="note">
      <strong>Baseline fairness notice</strong>
      <p>Baseline matches reward performance relative to recent history. Unusually low or incomplete baselines can affect fairness.</p>
    </aside>
  <?php endif; ?>

  <?php if ($baselinePreview !== null): ?>
    <section class="baseline-preview" aria-labelledby="baseline-preview-h">
      <div class="section-head"><h2 id="baseline-preview-h">Estimated baselines</h2></div>
      <div class="record-grid">
        <?php foreach (['player_a', 'player_b'] as $side):
          $bp = $baselinePreview[$side] ?? [];
        ?>
          <div class="record-cell">
            <dt><?= e((string) ($bp['player_name'] ?? ($side === 'player_a' ? 'Player A' : 'You'))) ?></dt>
            <?php if (!empty($bp['available'])): ?>
              <dd class="t-num"><?= e(MetricFormatter::format((int) round((float) $bp['mean']), $match)) ?></dd>
              <span class="record-note">
                <?= (int) $bp['sample_count'] ?> complete days
                · <?= e((string) ($bp['window_start_date'] ?? '')) ?>–<?= e((string) ($bp['window_end_date'] ?? '')) ?>
                · <?= e((string) ($bp['source_name'] ?? '')) ?>
              </span>
              <?php if (!empty($bp['typical_range']['label'])): ?>
                <span class="record-note">Typical range <?= e((string) $bp['typical_range']['label']) ?></span>
              <?php endif; ?>
            <?php else: ?>
              <dd>Unavailable</dd>
              <span class="record-note"><?= e((string) ($bp['reason'] ?? 'Insufficient history')) ?></span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (empty($baselinePreview['ok']) && $isBaseline): ?>
        <p class="form-error" role="alert"><?= e((string) ($baselinePreview['message'] ?? 'Baseline unavailable for one or both players.')) ?></p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/accept')) ?>" class="stack-form" id="accept-match-form">
    <?= \Rally\Core\Csrf::field() ?>
    <label>
      <span>Your declared data source</span>
      <select name="source_id" id="accept-source" required>
        <option value="">Select source</option>
        <?php foreach ($sources as $src): ?>
          <option value="<?= (int) $src['id'] ?>" <?= ((int) ($selectedSourceId ?? 0) === (int) $src['id']) ? 'selected' : '' ?>>
            <?= e($src['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="hint">Changing source recalculates your estimated baseline, updates comparability, and resets acknowledgement.</span>
    </label>

    <?php if ($isBaseline): ?>
      <label class="checkbox-row">
        <input type="checkbox" name="baseline_acknowledged" value="1" id="baseline-acknowledged" required>
        <span>I reviewed both baseline summaries and understand how this match will be scored.</span>
      </label>
    <?php endif; ?>

    <button type="submit" class="button button-primary" <?= ($isBaseline && empty($baselinePreview['ok'])) ? 'disabled' : '' ?>>
      Accept the series
    </button>
  </form>
  <form method="post" action="<?= e(url('/matches/' . (int) $match['id'] . '/decline')) ?>" class="stack-form" data-confirm="Decline this invitation?">
    <?= \Rally\Core\Csrf::field() ?>
    <button type="submit" class="button button-ghost">Decline</button>
  </form>
</section>
