<?php
/** @var list<array> $opponents */
/** @var list<array> $metrics */
/** @var list<array> $sources */
/** @var array $errors */
/** @var array $old */
?>
<section class="wrap-narrow create-page">
  <header class="page-header">
    <p class="t-label">New series</p>
    <h1>Create a 14-game series</h1>
    <p class="lede">One metric. One court. Daily winners decide the series.</p>
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

      <label>
        <span>Metric — the court</span>
        <select name="metric_type_id" required>
          <?php foreach ($metrics as $metric): ?>
            <option value="<?= (int) $metric['id'] ?>" <?= ((int) ($old['metric_type_id'] ?? 0) === (int) $metric['id'] || $metric['slug'] === 'steps') ? 'selected' : '' ?>>
              <?= e($metric['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="hint">Every match contests exactly one metric.</span>
      </label>
    </fieldset>

    <fieldset class="form-group">
      <legend class="t-label">Schedule</legend>

      <div class="field-pair">
        <label>
          <span>Start date</span>
          <input type="date" name="start_date" required value="<?= e($old['start_date'] ?? '') ?>">
        </label>

        <label>
          <span>Length (days)</span>
          <input type="number" name="length_days" min="1" max="60" required value="<?= e((string) ($old['length_days'] ?? 14)) ?>">
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
        <input type="number" name="tie_threshold" min="0" max="10000" required value="<?= e((string) ($old['tie_threshold'] ?? 100)) ?>">
        <span class="hint">Difference below this value is a tie. Equal to the threshold is a win.</span>
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
