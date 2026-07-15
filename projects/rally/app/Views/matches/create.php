<?php

use Rally\Services\MetricFormatter;

/** @var list<array> $opponents */
/** @var list<array> $metrics */
/** @var list<array> $sources */
/** @var array $errors */
/** @var array $old */

$selectedMetricId = (int) ($old['metric_type_id'] ?? 0);
if ($selectedMetricId === 0) {
    foreach ($metrics as $m) {
        if (($m['slug'] ?? '') === 'steps') {
            $selectedMetricId = (int) $m['id'];
            break;
        }
    }
    if ($selectedMetricId === 0 && $metrics !== []) {
        $selectedMetricId = (int) $metrics[0]['id'];
    }
}
$selectedMetric = null;
foreach ($metrics as $m) {
    if ((int) $m['id'] === $selectedMetricId) {
        $selectedMetric = $m;
        break;
    }
}
$defaultLength = (int) ($old['length_days'] ?? ($selectedMetric['default_length_days'] ?? 14));
$defaultThreshold = (int) ($old['tie_threshold'] ?? ($selectedMetric['default_tie_threshold'] ?? 100));
?>
<section class="wrap-narrow create-page">
  <header class="page-header">
    <p class="t-label">New series</p>
    <h1>Create a match</h1>
    <p class="lede">One metric. One court. Choose how you compete — daily wins or series average.</p>
  </header>

  <?php if (!empty($errors['form'])): ?>
    <p class="form-error" role="alert"><?= e($errors['form']) ?></p>
  <?php endif; ?>

  <form method="post" action="<?= e(url('/matches/create')) ?>" class="stack-form" id="create-match-form">
    <?= \Rally\Core\Csrf::field() ?>

    <fieldset class="form-group">
      <legend class="t-label">Matchup</legend>

      <label>
        <span>Opponent</span>
        <select name="opponent_id" required>
          <option value="">Select player</option>
          <?php foreach ($opponents as $o): ?>
            <option value="<?= (int) $o['id'] ?>" <?= ((int) ($old['opponent_id'] ?? 0) === (int) $o['id']) ? 'selected' : '' ?>>
              <?= e($o['name']) ?> (@<?= e($o['username']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </fieldset>

    <fieldset class="form-group metric-picker">
      <legend class="t-label">Metric — the court</legend>
      <p class="hint">Every match contests exactly one metric.</p>
      <div class="metric-cards" role="radiogroup" aria-label="Metric type">
        <?php foreach ($metrics as $metric):
          $checked = (int) $metric['id'] === $selectedMetricId;
          $cls = MetricFormatter::classificationLabel((string) ($metric['classification'] ?? 'performance'));
          $strat = MetricFormatter::strategyLabel((string) ($metric['scoring_strategy'] ?? 'daily_wins'));
          $dir = MetricFormatter::directionLabel((int) ($metric['higher_wins'] ?? 1) === 1);
          $len = (int) ($metric['default_length_days'] ?? 14);
          $thr = (int) ($metric['default_tie_threshold'] ?? 100);
        ?>
          <label class="metric-card<?= $checked ? ' is-selected' : '' ?>">
            <input
              type="radio"
              name="metric_type_id"
              value="<?= (int) $metric['id'] ?>"
              required
              <?= $checked ? 'checked' : '' ?>
              data-length="<?= $len ?>"
              data-threshold="<?= $thr ?>"
              data-strategy="<?= e((string) ($metric['scoring_strategy'] ?? 'daily_wins')) ?>"
              data-unit="<?= e((string) ($metric['unit'] ?? '')) ?>"
            >
            <span class="metric-card-name"><?= e((string) $metric['name']) ?></span>
            <span class="metric-card-meta">
              <span><?= e($cls) ?></span>
              <span aria-hidden="true">·</span>
              <span><?= e($strat) ?></span>
              <span aria-hidden="true">·</span>
              <span><?= $len ?>-day default</span>
            </span>
            <span class="metric-card-dir"><?= e($dir) ?> · Tie threshold <?= $thr ?> <?= e((string) $metric['unit']) ?></span>
            <?php if (!empty($metric['description'])): ?>
              <span class="metric-card-desc"><?= e((string) $metric['description']) ?></span>
            <?php endif; ?>
            <?php if (!empty($metric['context_note'])): ?>
              <span class="metric-card-note hint"><?= e((string) $metric['context_note']) ?></span>
            <?php endif; ?>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset class="form-group">
      <legend class="t-label">Schedule</legend>

      <div class="field-pair">
        <label>
          <span>Start date</span>
          <input type="date" name="start_date" required value="<?= e($old['start_date'] ?? '') ?>">
        </label>

        <label>
          <span>Length</span>
          <select name="length_days" id="length-days" required>
            <option value="7" <?= $defaultLength === 7 ? 'selected' : '' ?>>7 days</option>
            <option value="14" <?= $defaultLength === 14 ? 'selected' : '' ?>>14 days</option>
          </select>
        </label>
      </div>

      <label>
        <span>Match timezone</span>
        <input type="text" name="timezone" required value="<?= e($old['timezone'] ?? 'America/Los_Angeles') ?>" list="tz-list">
        <datalist id="tz-list">
          <option value="America/Los_Angeles"><option value="America/New_York">
          <option value="America/Chicago"><option value="Europe/London"><option value="UTC">
        </datalist>
        <span class="hint">This timezone alone controls day boundaries and settlement — not player account timezones.</span>
      </label>

      <label>
        <span>Tie threshold</span>
        <input type="number" name="tie_threshold" id="tie-threshold" min="0" max="10000" required value="<?= e((string) $defaultThreshold) ?>">
        <span class="hint" id="threshold-hint">Difference below this value is a tie (or draw for series averages). Equal to the threshold is decisive.</span>
      </label>
    </fieldset>

    <fieldset class="form-group">
      <legend class="t-label">Equipment</legend>

      <label>
        <span>Your data source</span>
        <select name="player_a_source_id" id="source-a" required>
          <?php foreach ($sources as $src): ?>
            <option value="<?= (int) $src['id'] ?>" data-class="<?= e($src['source_class']) ?>"
              <?= ((int) ($old['player_a_source_id'] ?? 0) === (int) $src['id'] || $src['slug'] === 'apple_watch') ? 'selected' : '' ?>>
              <?= e($src['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        <span>Opponent source (demo auto-accept)</span>
        <select name="player_b_source_id" id="source-b">
          <option value="">Opponent chooses later</option>
          <?php foreach ($sources as $src): ?>
            <option value="<?= (int) $src['id'] ?>" data-class="<?= e($src['source_class']) ?>"
              <?= ((int) ($old['player_b_source_id'] ?? 0) === (int) $src['id']) ? 'selected' : '' ?>>
              <?= e($src['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="checkbox-row">
        <input type="checkbox" name="auto_accept" value="1" <?= ($old['auto_accept'] ?? '1') === '1' ? 'checked' : '' ?>>
        <span>Demo mode: accept immediately when opponent source is set</span>
      </label>

      <aside id="source-mismatch-warning" class="source-warning" hidden role="alert">
        <strong>Source mismatch</strong>
        <p>Different source types may not be directly comparable. The match can continue with caution.</p>
      </aside>
    </fieldset>

    <button type="submit" class="button button-primary">Create match</button>
  </form>
</section>
