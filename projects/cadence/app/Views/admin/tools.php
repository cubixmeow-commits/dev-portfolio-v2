<?php
use Cadence\Controllers\AdminToolsController;
use Cadence\Core\Csrf;
use Cadence\Models\OpsRun;
?>
<div class="container">
  <div class="page-head">
    <div>
      <h1>Ops tools</h1>
      <p>The web face of the Java engine. Both this page and the CLI drive the same jar.</p>
    </div>
    <a class="btn btn-secondary" href="<?= e(url('/admin')) ?>">Analytics</a>
  </div>

  <?php if (!$shellEnabled): ?>
    <div class="flash flash-error" style="margin-bottom: var(--sp-5)">
      shell_exec is disabled on this host, so runs cannot start from the browser.
      Each tool below shows the exact command to run over SSH instead; run history still updates here.
    </div>
  <?php endif; ?>

  <div class="tools-grid" id="tools-root"
       data-run-url="<?= e(url('/admin/tools/run')) ?>"
       data-status-url="<?= e(url('/admin/tools/status')) ?>"
       data-csrf="<?= e(Csrf::token()) ?>"
       data-shell="<?= $shellEnabled ? '1' : '0' ?>">

    <section class="card card-pad tool-card">
      <h2 style="font-size: var(--text-lg)">Seed demo data</h2>
      <p class="muted small" style="margin-top: var(--sp-1)">Adds a demo population on top of what exists. For a clean slate, use Reset.</p>
      <form class="tool-form" data-tool="seed" style="margin-top: var(--sp-4)">
        <div class="tool-fields">
          <div class="field">
            <label for="seed-users">Users</label>
            <input class="input" type="number" id="seed-users" name="users" value="500" min="1" max="20000">
          </div>
          <div class="field">
            <label for="seed-challenges">Challenges</label>
            <input class="input" type="number" id="seed-challenges" name="challenges" value="12" min="1" max="200">
          </div>
          <div class="field">
            <label for="seed-history">History days</label>
            <input class="input" type="number" id="seed-history" name="history_days" value="120" min="1" max="730">
          </div>
        </div>
        <button class="btn btn-primary" type="submit"<?= $shellEnabled ? '' : ' disabled' ?>>Run seed</button>
      </form>
      <?php if (!$shellEnabled): ?>
        <p class="muted small cli-fallback">SSH: <code class="num"><?= e(AdminToolsController::cliCommand('seed', ['--users=500', '--challenges=12', '--history-days=120'])) ?></code></p>
      <?php endif; ?>
    </section>

    <section class="card card-pad tool-card">
      <h2 style="font-size: var(--text-lg)">Reset demo data</h2>
      <p class="muted small" style="margin-top: var(--sp-1)">Wipes all demo data and rebuilds the known-good state: 500 users, 12 challenges, 120 days, curated demo member.</p>
      <form class="tool-form" data-tool="reset" style="margin-top: var(--sp-4)">
        <div class="field">
          <label for="reset-confirm">Type <strong>RESET</strong> to confirm</label>
          <input class="input" type="text" id="reset-confirm" name="confirm_text" autocomplete="off" placeholder="RESET">
        </div>
        <button class="btn btn-danger" type="submit"<?= $shellEnabled ? '' : ' disabled' ?>>Reset demo</button>
      </form>
      <?php if (!$shellEnabled): ?>
        <p class="muted small cli-fallback">SSH: <code class="num"><?= e(AdminToolsController::cliCommand('reset', ['--confirm'])) ?></code></p>
      <?php endif; ?>
    </section>

    <section class="card card-pad tool-card">
      <h2 style="font-size: var(--text-lg)">Generate report</h2>
      <p class="muted small" style="margin-top: var(--sp-1)">Runs in the engine and renders below; the output is also downloadable.</p>
      <form class="tool-form" data-tool="report" style="margin-top: var(--sp-4)">
        <div class="field">
          <label for="report-type">Report</label>
          <select class="input" id="report-type" name="type">
            <option value="engagement">Engagement summary</option>
            <option value="retention">Retention snapshot</option>
            <option value="challenge-health">Challenge health</option>
          </select>
        </div>
        <button class="btn btn-primary" type="submit"<?= $shellEnabled ? '' : ' disabled' ?>>Run report</button>
      </form>
      <?php if (!$shellEnabled): ?>
        <p class="muted small cli-fallback">SSH: <code class="num"><?= e(AdminToolsController::cliCommand('report', ['--type=engagement'])) ?></code></p>
      <?php endif; ?>
    </section>
  </div>

  <section class="card output-panel" id="output-panel" hidden>
    <div class="output-head">
      <h2 class="section-title" style="margin: 0">Live output</h2>
      <span class="pill" id="output-status">running</span>
      <a class="btn btn-quiet" id="output-download" download="cadence-report.txt" hidden>Download</a>
    </div>
    <pre class="output-log num" id="output-log" aria-live="polite"></pre>
  </section>

  <section style="margin-top: var(--sp-6)">
    <h2 class="section-title">Run history</h2>
    <div class="card">
      <?php if ($runs === []): ?>
        <div class="empty">
          <h3>No runs yet</h3>
          <p>Seed, reset, and report executions from the web or CLI will appear here.</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>#</th><th>Tool</th><th>Params</th><th>By</th><th>When</th><th>Duration</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($runs as $run): ?>
                <tr>
                  <td class="num"><?= e((string) $run['id']) ?></td>
                  <td><?= e($run['tool']) ?></td>
                  <td class="muted small num"><?= e((string) ($run['params'] ?? '')) ?></td>
                  <td class="muted small"><?= $run['triggered_by'] === 'web' ? e($run['runner_name'] ?? 'web') : 'CLI' ?></td>
                  <td class="muted small"><?= e(time_ago((string) $run['started_at'])) ?></td>
                  <td class="num small"><?= e(OpsRun::duration($run)) ?></td>
                  <td>
                    <span class="pill<?= $run['status'] === 'success' ? ' pill-accent' : ($run['status'] === 'failed' ? ' pill-warn' : '') ?>">
                      <?= e($run['status']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>
<script src="<?= e(url('/assets/js/admin-tools.js')) ?>" defer></script>
