<?php
/** @var list<array> $matches */
/** @var array|null $pack */
/** @var int $selectedId */
/** @var list<array> $sources */
/** @var DateTimeImmutable $clock */
/** @var bool $hasOverride */
?>
<section class="sim-page">
  <header class="page-header">
    <p class="t-label">Development</p>
    <h1>Simulation controls</h1>
    <p class="lede">Development only. Mutates results through the ingestion service and advances the application clock.</p>
    <p class="hint">Application clock: <strong><?= e($clock->format('Y-m-d H:i:s')) ?> UTC</strong>
      <?= $hasOverride ? '(override active)' : '(real time)' ?></p>
  </header>

  <form method="get" action="<?= e(url('/simulation')) ?>" class="stack-form inline-form">
    <label>
      <span>Match</span>
      <select name="match_id" onchange="this.form.submit()">
        <?php foreach ($matches as $m): ?>
          <option value="<?= (int) $m['id'] ?>" <?= $selectedId === (int) $m['id'] ? 'selected' : '' ?>>
            #<?= (int) $m['id'] ?> <?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?> (<?= e($m['status']) ?>)
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

  <?php if ($pack):
    $m = $pack['match'];
    $s = $pack['summary'];
  ?>
  <section class="sim-block">
    <h2>Derived score</h2>
    <p class="sim-score"><?= (int) $s['player_a_wins'] ?>–<?= (int) $s['player_b_wins'] ?></p>
    <ul class="hint">
      <li>Ties: <?= (int) $s['ties'] ?> · Voids: <?= (int) $s['voids'] ?></li>
      <li>Official: <?= (int) $s['official_days'] ?> · Pending: <?= (int) $s['pending_days'] ?> · Remaining: <?= (int) $s['remaining_days'] ?></li>
      <li>Match status: <?= e($m['status']) ?> · Complete: <?= $s['is_complete'] ? 'yes' : 'no' ?> · Draw: <?= $s['is_draw'] ? 'yes' : 'no' ?></li>
    </ul>
    <p><a href="<?= e(url('/matches/' . (int) $m['id'])) ?>">Open match screen</a></p>
    <form method="post" action="<?= e(url('/simulation')) ?>">
      <?= \Rally\Core\Csrf::field() ?>
      <input type="hidden" name="match_id" value="<?= (int) $m['id'] ?>">
      <input type="hidden" name="action" value="refresh_match">
      <button type="submit" class="button button-ghost">Refresh lifecycle</button>
    </form>
  </section>

  <section class="sim-block">
    <h2>Match days</h2>
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
    ?>
      <form method="post" action="<?= e(url('/simulation')) ?>" class="sim-day-form">
        <?= \Rally\Core\Csrf::field() ?>
        <input type="hidden" name="match_id" value="<?= (int) $m['id'] ?>">
        <input type="hidden" name="match_day_id" value="<?= (int) $day['id'] ?>">
        <input type="hidden" name="action" value="update_day">
        <fieldset>
          <legend>Day <?= (int) $day['day_number'] ?> · <?= e($day['competition_date']) ?> · <?= e($day['status']) ?></legend>
          <label><?= e($m['player_a_name']) ?>
            <input type="number" name="value_a" min="0" max="100000" value="<?= e($valA) ?>" placeholder="steps">
          </label>
          <label><?= e($m['player_b_name']) ?>
            <input type="number" name="value_b" min="0" max="100000" value="<?= e($valB) ?>" placeholder="steps">
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
