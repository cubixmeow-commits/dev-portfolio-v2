<?php
/**
 * Asset tracker (agent only): list/filter equipment, add/edit, reassign, and
 * move through the surplus/retire lifecycle. CSV export mirrors a real
 * "equipment surplus" hand-off workflow.
 */
require __DIR__ . '/lib/bootstrap.php';
require_agent();

$flash = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        if (trim((string) ($_POST['asset_tag'] ?? '')) === '' || trim((string) ($_POST['make_model'] ?? '')) === '') {
            $flash = 'Asset tag and make/model are required.';
        } else {
            try {
                asset_save([
                    'asset_tag'           => trim((string) $_POST['asset_tag']),
                    'type'                => (string) ($_POST['type'] ?? ''),
                    'make_model'          => trim((string) $_POST['make_model']),
                    'assigned_to'         => trim((string) ($_POST['assigned_to'] ?? '')),
                    'assigned_department' => (string) ($_POST['assigned_department'] ?? ''),
                    'status'              => (string) ($_POST['status'] ?? ''),
                    'acquired_date'       => (string) ($_POST['acquired_date'] ?? ''),
                ], $id ?: null);
                redirect('assets.php?saved=1');
            } catch (PDOException $e) {
                $flash = 'Could not save — the asset tag may already exist.';
            }
        }
    } elseif ($action === 'surplus') {
        asset_set_status((int) ($_POST['id'] ?? 0), 'surplus');
        redirect('assets.php?saved=1');
    }
}

$filters = [
    'status'     => pick((string) ($_GET['status'] ?? ''), HOT_ASSET_STATUS, ''),
    'type'       => pick((string) ($_GET['type'] ?? ''), HOT_ASSET_TYPES, ''),
    'department' => (string) ($_GET['department'] ?? ''),
];
$assets = assets_find($filters);
$depts  = departments_all();

// Editing? load the row.
$edit = null;
if (($eid = (int) ($_GET['edit'] ?? 0)) > 0) {
    $edit = asset_get($eid);
}
$q = http_build_query(array_filter($filters));

hot_header('Asset tracker', 'assets');
?>
<div class="page-head">
  <h1>Asset tracker</h1>
  <p class="lead"><?= count($assets) ?> asset<?= count($assets) === 1 ? '' : 's' ?> shown.
     <a class="inline-link" href="assets_export.php?<?= e($q) ?>">Export CSV &darr;</a></p>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert alert--ok"><strong>Saved.</strong></div><?php endif; ?>
<?php if ($flash): ?><div class="alert alert--error"><?= e($flash) ?></div><?php endif; ?>

<form class="filters" method="get" aria-label="Filter assets">
  <div class="field">
    <label for="f-status">Status</label>
    <select id="f-status" name="status">
      <option value="">All</option>
      <?php foreach (HOT_ASSET_STATUS as $s): ?>
        <option value="<?= e($s) ?>"<?= $filters['status'] === $s ? ' selected' : '' ?>><?= e(label($s)) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field">
    <label for="f-type">Type</label>
    <select id="f-type" name="type">
      <option value="">All</option>
      <?php foreach (HOT_ASSET_TYPES as $t): ?>
        <option value="<?= e($t) ?>"<?= $filters['type'] === $t ? ' selected' : '' ?>><?= e(label($t)) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field">
    <label for="f-dept">Department</label>
    <select id="f-dept" name="department">
      <option value="">All</option>
      <?php foreach ($depts as $d): ?>
        <option value="<?= e($d) ?>"<?= $filters['department'] === $d ? ' selected' : '' ?>><?= e($d) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field field--actions">
    <button class="btn" type="submit">Apply</button>
    <a class="btn btn--ghost" href="assets.php">Reset</a>
  </div>
</form>

<div class="table-scroll">
  <table class="data">
    <thead>
      <tr><th>Asset tag</th><th>Type</th><th>Make / model</th><th>Assigned to</th><th>Department</th><th>Status</th><th>Acquired</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$assets): ?>
        <tr><td colspan="8" class="empty">No assets match these filters.</td></tr>
      <?php endif; ?>
      <?php foreach ($assets as $a): ?>
        <tr>
          <td class="mono"><?= e($a['asset_tag']) ?></td>
          <td><?= e(label($a['type'])) ?></td>
          <td><?= e($a['make_model']) ?></td>
          <td><?= $a['assigned_to'] ? e($a['assigned_to']) : '<span class="muted">—</span>' ?></td>
          <td><?= $a['assigned_department'] ? e($a['assigned_department']) : '<span class="muted">—</span>' ?></td>
          <td><?= asset_badge($a['status']) ?></td>
          <td class="nowrap"><?= fmt_date($a['acquired_date']) ?></td>
          <td class="row-actions">
            <a class="link-sm" href="assets.php?edit=<?= (int) $a['id'] ?>">Edit</a>
            <?php if ($a['status'] !== 'surplus' && $a['status'] !== 'retired'): ?>
              <form method="post" class="inline" onsubmit="return confirm('Mark <?= e($a['asset_tag']) ?> for surplus?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="surplus">
                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                <button class="link-sm link-danger" type="submit">Surplus</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<section class="card" id="form">
  <h2><?= $edit ? 'Edit asset ' . e($edit['asset_tag']) : 'Add an asset' ?></h2>
  <form method="post" class="stack">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : '' ?>">
    <div class="grid-2">
      <div class="field">
        <label for="a-tag">Asset tag <span class="req">*</span></label>
        <input id="a-tag" name="asset_tag" required value="<?= e($edit['asset_tag'] ?? '') ?>" placeholder="IT-LT-0105">
      </div>
      <div class="field">
        <label for="a-type">Type</label>
        <select id="a-type" name="type">
          <?php foreach (HOT_ASSET_TYPES as $t): ?>
            <option value="<?= e($t) ?>"<?= (($edit['type'] ?? '') === $t) ? ' selected' : '' ?>><?= e(label($t)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="field">
      <label for="a-model">Make / model <span class="req">*</span></label>
      <input id="a-model" name="make_model" required value="<?= e($edit['make_model'] ?? '') ?>" placeholder="Latitude 5440">
    </div>
    <div class="grid-2">
      <div class="field">
        <label for="a-user">Assigned to</label>
        <input id="a-user" name="assigned_to" value="<?= e($edit['assigned_to'] ?? '') ?>" placeholder="Leave blank if unassigned">
      </div>
      <div class="field">
        <label for="a-dept">Department</label>
        <select id="a-dept" name="assigned_department">
          <option value="">—</option>
          <?php foreach ($depts as $d): ?>
            <option value="<?= e($d) ?>"<?= (($edit['assigned_department'] ?? '') === $d) ? ' selected' : '' ?>><?= e($d) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="grid-2">
      <div class="field">
        <label for="a-status">Status</label>
        <select id="a-status" name="status">
          <?php foreach (HOT_ASSET_STATUS as $s): ?>
            <option value="<?= e($s) ?>"<?= (($edit['status'] ?? 'in_use') === $s) ? ' selected' : '' ?>><?= e(label($s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="a-acq">Acquired date</label>
        <input id="a-acq" name="acquired_date" type="date" value="<?= e($edit['acquired_date'] ?? '') ?>">
      </div>
    </div>
    <div class="field field--actions">
      <button class="btn" type="submit"><?= $edit ? 'Save asset' : 'Add asset' ?></button>
      <?php if ($edit): ?><a class="btn btn--ghost" href="assets.php">Cancel</a><?php endif; ?>
    </div>
  </form>
</section>
<?php
hot_footer();
