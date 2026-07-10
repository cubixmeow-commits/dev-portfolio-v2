<?php
/**
 * Single ticket — read-only status for the public, full update controls for
 * a signed-in agent (status, assignment, resolution note).
 */
require __DIR__ . '/lib/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);

// ---- Agent update --------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_agent();
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    ticket_update(
        $id,
        (string) ($_POST['status'] ?? ''),
        (string) ($_POST['assigned_to'] ?? ''),
        (string) ($_POST['note'] ?? '')
    );
    redirect('ticket.php?id=' . $id . '&updated=1');
}

$t = $id ? ticket_get($id) : null;

if (!$t) {
    hot_header('Ticket not found', 'tickets');
    echo '<div class="page-head"><h1>Ticket not found</h1>'
       . '<p class="lead">No ticket matches that number. Check the number and try again, or '
       . '<a href="index.php">submit a new ticket</a>.</p></div>';
    hot_footer();
    exit;
}

$agent     = is_agent();
$submitted = isset($_GET['submitted']);
$updated   = isset($_GET['updated']);

hot_header('Ticket #' . $id, 'tickets');
?>
<p class="crumbs"><a href="index.php">&larr; <?= $agent ? 'Back to queue' : 'Submit another ticket' ?></a></p>

<?php if ($submitted): ?>
  <div class="alert alert--ok">
    <strong>Ticket #<?= (int) $t['id'] ?> submitted.</strong>
    Save this number to check its status later. The IT Support team will pick it up from the queue.
  </div>
<?php endif; ?>
<?php if ($updated): ?>
  <div class="alert alert--ok"><strong>Ticket updated.</strong></div>
<?php endif; ?>

<div class="page-head">
  <span class="ticket-no">Ticket #<?= (int) $t['id'] ?></span>
  <h1><?= e($t['title']) ?></h1>
  <p class="badges">
    <?= status_badge($t['status']) ?>
    <?= priority_badge($t['priority']) ?>
    <span class="badge badge--plain"><?= e(label($t['category'])) ?></span>
  </p>
</div>

<div class="cols">
  <section class="card">
    <h2>Details</h2>
    <div class="ticket-body"><?php
        // description may carry appended resolution notes; render as text blocks
        foreach (preg_split('/\n{2,}/', (string) $t['description']) as $para) {
            echo '<p>' . nl2br(e(trim($para))) . '</p>';
        }
    ?></div>
  </section>

  <aside class="card card--aside">
    <h2>Ticket info</h2>
    <dl class="kv">
      <dt>Requester</dt><dd><?= e($t['requester_name']) ?></dd>
      <dt>Department</dt><dd><?= e($t['requester_department']) ?></dd>
      <dt>Assigned to</dt><dd><?= $t['assigned_to'] ? e($t['assigned_to']) : '<span class="muted">Unassigned</span>' ?></dd>
      <dt>Opened</dt><dd><?= fmt_dt($t['created_at']) ?></dd>
      <dt>Resolved</dt><dd><?= fmt_dt($t['resolved_at']) ?></dd>
    </dl>
  </aside>
</div>

<?php if ($agent): ?>
  <section class="card">
    <h2>Update ticket</h2>
    <form method="post" class="stack">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
      <div class="grid-2">
        <div class="field">
          <label for="u-status">Status</label>
          <select id="u-status" name="status">
            <?php foreach (HOT_STATUSES as $s): ?>
              <option value="<?= e($s) ?>"<?= $t['status'] === $s ? ' selected' : '' ?>><?= e(label($s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="u-assignee">Assign to</label>
          <select id="u-assignee" name="assigned_to">
            <option value="">Unassigned</option>
            <?php foreach (agents_all() as $a): ?>
              <option value="<?= e($a) ?>"<?= $t['assigned_to'] === $a ? ' selected' : '' ?>><?= e($a) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field">
        <label for="u-note">Add a resolution note <span class="muted">(optional — appended to the record)</span></label>
        <textarea id="u-note" name="note" rows="3" placeholder="What you did, or what you're waiting on."></textarea>
      </div>
      <div class="field field--actions">
        <button class="btn" type="submit">Save changes</button>
      </div>
    </form>
  </section>
<?php else: ?>
  <p class="muted">Signed-in agents can update this ticket. <a href="login.php?next=<?= rawurlencode('ticket.php?id=' . $id) ?>">Agent sign in</a>.</p>
<?php endif; ?>
<?php
hot_footer();
