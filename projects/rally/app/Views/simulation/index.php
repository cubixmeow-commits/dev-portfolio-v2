<?php

use Rally\Services\ActivityFeedService;
use Rally\Services\MetricCompetitionService;
use Rally\Services\MetricFormatter;

/** @var list<array> $matches */
/** @var array|null $pack */
/** @var int $selectedId */
/** @var list<array> $sources */
/** @var DateTimeImmutable $clock */
/** @var bool $hasOverride */
/** @var list<array> $previewEvents */
$previewEvents = $previewEvents ?? [];
?>
<section class="sim-page">
  <header class="page-header">
    <p class="t-label">Development</p>
    <h1>Simulation controls</h1>
    <p class="lede">Development only. Primary path edits canonical observations via UserMetricDayService, then projects into eligible matches. A legacy match-day editor remains for debugging.</p>
    <p class="hint">Application clock: <strong><?= e($clock->format('Y-m-d H:i:s')) ?> UTC</strong>
      <?= $hasOverride ? '(override active)' : '(real time)' ?></p>
    <p class="hint">Reset seeded data: <code>php scripts/seed.php</code> from the Rally project root.</p>
  </header>

  <form method="get" action="<?= e(url('/simulation')) ?>" class="stack-form inline-form">
    <label>
      <span>Match</span>
      <select name="match_id" onchange="this.form.submit()">
        <?php foreach ($matches as $m): ?>
          <option value="<?= (int) $m['id'] ?>" <?= $selectedId === (int) $m['id'] ? 'selected' : '' ?>>
            #<?= (int) $m['id'] ?> <?= e($m['metric_name'] ?? '') ?> · <?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?> (<?= e($m['status']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </label>
  </form>

  <section class="sim-block">
    <h2>Clock</h2>
    <form method="post" action="<?= e(url('/simulation')) ?>" class="stack-form">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $selectedId ?>">
      <input type="hidden" name="action" value="advance_days">
      <label>
        <span>Advance days</span>
        <input type="number" name="days" value="1" min="-30" max="30">
      </label>
      <button type="submit" class="button button-primary">Advance</button>
    </form>
    <form method="post" action="<?= e(url('/simulation')) ?>" class="stack-form">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $selectedId ?>">
      <input type="hidden" name="action" value="set_clock">
      <label>
        <span>Set clock (UTC)</span>
        <input type="datetime-local" name="clock_at" value="<?= e($clock->format('Y-m-d\TH:i')) ?>">
      </label>
      <button type="submit" class="button button-primary">Set clock</button>
    </form>
    <form method="post" action="<?= e(url('/simulation')) ?>">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $selectedId ?>">
      <input type="hidden" name="action" value="clear_clock">
      <button type="submit" class="button button-ghost">Clear override</button>
    </form>
  </section>

  <section class="sim-block">
    <h2>Canonical observation (primary path)</h2>
    <p class="hint">Select user, metric, source, and date. Value writes to rly_user_metric_days then projects into eligible matches.</p>
    <form method="post" action="<?= e(url('/simulation')) ?>" class="stack-form">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $selectedId ?>">
      <input type="hidden" name="action" value="ingest_canonical">
      <label>
        <span>User</span>
        <select name="user_id" required>
          <?php foreach (($users ?? []) as $u): ?>
            <option value="<?= (int) $u['id'] ?>"><?= e($u['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <span>Metric</span>
        <select name="metric_type_id" required>
          <?php foreach (($metrics ?? []) as $mt): ?>
            <option value="<?= (int) $mt['id'] ?>"><?= e($mt['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <span>Source</span>
        <select name="data_source_id" required>
          <?php foreach ($sources as $src): ?>
            <option value="<?= (int) $src['id'] ?>"><?= e($src['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <span>Observation date</span>
        <input type="date" name="observation_date" required value="<?= e($clock->format('Y-m-d')) ?>">
      </label>
      <label>
        <span>Value</span>
        <input type="number" name="value" required min="0" max="100000" value="10000">
      </label>
      <button type="submit" class="button button-primary">Ingest &amp; project</button>
    </form>
    <form method="post" action="<?= e(url('/simulation')) ?>" class="stack-form">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $selectedId ?>">
      <input type="hidden" name="action" value="estimate_baseline">
      <label>
        <span>Estimate baseline for start date</span>
        <input type="date" name="start_date" required value="<?= e($clock->modify('+1 day')->format('Y-m-d')) ?>">
      </label>
      <label>
        <span>User</span>
        <select name="user_id" required>
          <?php foreach (($users ?? []) as $u): ?>
            <option value="<?= (int) $u['id'] ?>"><?= e($u['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <span>Metric</span>
        <select name="metric_type_id" required>
          <?php foreach (($metrics ?? []) as $mt): ?>
            <option value="<?= (int) $mt['id'] ?>"><?= e($mt['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        <span>Source</span>
        <select name="data_source_id" required>
          <?php foreach ($sources as $src): ?>
            <option value="<?= (int) $src['id'] ?>"><?= e($src['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit" class="button button-ghost">Estimate baseline</button>
    </form>
  </section>

  <?php if ($pack):
    $m = $pack['match'];
    $s = $pack['summary'];
    $metric = MetricCompetitionService::metricPayload($m);
    $isAvg = MetricCompetitionService::isSeriesAverage($m);
    $score = MetricCompetitionService::scoreline($m, $s);
    $unit = MetricFormatter::unitLabel($metric);
  ?>
  <section class="sim-block">
    <h2>Derived score</h2>
    <p class="sim-score"><?= e($score['primary']) ?></p>
    <ul class="hint">
      <li>Competition: <?= e(MetricCompetitionService::competitionTypeLabel(MetricCompetitionService::competitionType($m))) ?> · <?= e((string) ($s['surface_label'] ?? '')) ?></li>
      <li>Metric: <?= e($m['metric_name']) ?> · Strategy: <?= e(MetricFormatter::strategyLabel((string) ($m['scoring_strategy'] ?? 'daily_wins'))) ?> · Unit: <?= e($unit !== '' ? $unit : 'formatted') ?></li>
      <?php if ($isAvg): ?>
        <li>Averages: <?= e(MetricFormatter::format($s['player_a_average'] ?? null, $metric)) ?> vs <?= e(MetricFormatter::format($s['player_b_average'] ?? null, $metric)) ?></li>
        <li>Daily comparisons: <?= (int) ($s['daily_comparison_a_leads'] ?? 0) ?>–<?= (int) ($s['daily_comparison_b_leads'] ?? 0) ?> (ties <?= (int) ($s['daily_comparison_ties'] ?? 0) ?>)</li>
      <?php else: ?>
        <li>Wins: <?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?> · Ties: <?= (int) $s['ties'] ?> · Voids: <?= (int) $s['voids'] ?></li>
      <?php endif; ?>
      <li>Official: <?= (int) $s['official_days'] ?> · Pending: <?= (int) $s['pending_days'] ?> · Remaining: <?= (int) $s['remaining_days'] ?></li>
      <li>Match status: <?= e($m['status']) ?> · Complete: <?= $s['is_complete'] ? 'yes' : 'no' ?> · Draw: <?= $s['is_draw'] ? 'yes' : 'no' ?> · Provisional: <?= $s['is_provisional'] ? 'yes' : 'no' ?></li>
      <?php if (!empty($s['baseline']['player_a']['available'])): ?>
        <li>Frozen baseline A: <?= e(number_format((float) $s['baseline']['player_a']['mean'], 2)) ?> (<?= (int) $s['baseline']['player_a']['sample_count'] ?> days)</li>
      <?php endif; ?>
      <?php if (!empty($s['baseline']['player_b']['available'])): ?>
        <li>Frozen baseline B: <?= e(number_format((float) $s['baseline']['player_b']['mean'], 2)) ?> (<?= (int) $s['baseline']['player_b']['sample_count'] ?> days)</li>
      <?php endif; ?>
    </ul>
    <p><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">Open match screen</a></p>
    <form method="post" action="<?= e(url('/simulation')) ?>">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $m['id'] ?>">
      <input type="hidden" name="action" value="refresh_match">
      <button type="submit" class="button button-ghost">Recalculate lifecycle</button>
    </form>
  </section>

  <?php if ($previewEvents !== []): ?>
  <section class="sim-block">
    <h2>Feed event preview</h2>
    <ul class="hint">
      <?php foreach (array_slice($previewEvents, 0, 8) as $ev): ?>
        <li><strong><?= e((string) ($ev['headline'] ?? '')) ?></strong> — <?= e((string) ($ev['body'] ?? '')) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <section class="sim-block">
    <h2>Legacy match-day editor (debug)</h2>
    <p class="hint">Debugging only. Prefer the canonical observation form above. Values still route through UserMetricDayService.</p>
    <?php foreach ($pack['days'] as $day):
      $results = $day['results'] ?? [];
      $valA = $valB = '';
      foreach ($results as $r) {
          if ((int) $r['user_id'] === (int) $m['player_a_user_id']) {
              $valA = (string) $r['metric_value'];
          }
          if ((int) $r['user_id'] === (int) $m['player_b_user_id']) {
              $valB = (string) $r['metric_value'];
          }
      }
      $fmtA = $valA !== '' ? MetricFormatter::format((int) $valA, $metric) : '—';
      $fmtB = $valB !== '' ? MetricFormatter::format((int) $valB, $metric) : '—';
    ?>
      <form method="post" action="<?= e(url('/simulation')) ?>" class="sim-day-form">
        <?= \Rally\Core\Csrf::field() ?>
        <input type="hidden" name="match_id" value="<?= (int) $m['id'] ?>">
        <input type="hidden" name="match_day_id" value="<?= (int) $day['id'] ?>">
        <input type="hidden" name="action" value="update_day">
        <fieldset>
          <legend>Day <?= (int) $day['day_number'] ?> · <?= e($day['competition_date']) ?> · <?= e($day['status']) ?></legend>
          <p class="hint">Display: <?= e($fmtA) ?> vs <?= e($fmtB) ?></p>
          <label><?= e($m['player_a_name']) ?> (<?= e($unit !== '' ? $unit : (string) ($m['metric_unit'] ?? 'value')) ?>)
            <input type="number" name="value_a" min="0" max="100000" value="<?= e($valA) ?>" placeholder="<?= e((string) ($m['metric_unit'] ?? 'value')) ?>">
          </label>
          <label><?= e($m['player_b_name']) ?> (<?= e($unit !== '' ? $unit : (string) ($m['metric_unit'] ?? 'value')) ?>)
            <input type="number" name="value_b" min="0" max="100000" value="<?= e($valB) ?>" placeholder="<?= e((string) ($m['metric_unit'] ?? 'value')) ?>">
          </label>
          <label>Source A
            <select name="source_a">
              <?php foreach ($sources as $src): ?>
                <option value="<?= (int) $src['id'] ?>" <?= (int) $src['id'] === (int) $m['player_a_source_id'] ? 'selected' : '' ?>><?= e($src['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Source B
            <select name="source_b">
              <?php foreach ($sources as $src): ?>
                <option value="<?= (int) $src['id'] ?>" <?= (int) $src['id'] === (int) ($m['player_b_source_id'] ?? 0) ? 'selected' : '' ?>><?= e($src['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Day status
            <select name="day_status">
              <?php foreach (['', 'scheduled', 'live', 'pending', 'official', 'void'] as $st): ?>
                <option value="<?= e($st) ?>" <?= $st === (string) $day['status'] ? 'selected' : '' ?>><?= $st === '' ? '(unchanged)' : $st ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label class="checkbox-row">
            <input type="checkbox" name="settle" value="1">
            <span>Settle now</span>
          </label>
          <button type="submit" class="button button-primary button-small">Save day</button>
        </fieldset>
      </form>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>
</section>
