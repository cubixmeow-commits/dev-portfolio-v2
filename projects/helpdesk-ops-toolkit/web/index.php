<?php
/**
 * Ticket dashboard (agent) / submit-a-ticket (public).
 */
require __DIR__ . '/lib/bootstrap.php';

$flash = '';

// ---- Public ticket submission -------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'submit') {
    csrf_check();
    $title = trim((string) ($_POST['title'] ?? ''));
    $desc  = trim((string) ($_POST['description'] ?? ''));
    $name  = trim((string) ($_POST['requester_name'] ?? ''));
    $dept  = trim((string) ($_POST['requester_department'] ?? ''));
    $errors = [];
    if ($title === '')       $errors[] = 'a short title';
    if ($desc === '')        $errors[] = 'a description';
    if ($name === '')        $errors[] = 'your name';
    if ($dept === '')        $errors[] = 'your department';

    if (!$errors) {
        $id = ticket_create([
            'title'                => $title,
            'description'          => $desc,
            'category'             => (string) ($_POST['category'] ?? 'other'),
            'priority'             => (string) ($_POST['priority'] ?? 'medium'),
            'requester_name'       => $name,
            'requester_department' => $dept,
        ]);
        redirect('ticket.php?id=' . $id . '&submitted=1');
    }
    $flash = 'Please include ' . implode(', ', $errors) . '.';
}

/* =================================================================== */
if (is_agent()):
    /* ---- AGENT DASHBOARD ---- */
    $filters = [
        'status'     => pick((string) ($_GET['status'] ?? ''), HOT_STATUSES, ''),
        'category'   => pick((string) ($_GET['category'] ?? ''), HOT_CATEGORIES, ''),
        'priority'   => pick((string) ($_GET['priority'] ?? ''), HOT_PRIORITIES, ''),
        'department' => (string) ($_GET['department'] ?? ''),
        'q'          => trim((string) ($_GET['q'] ?? '')),
    ];
    $tickets = tickets_find($filters);
    $depts   = departments_all();

    hot_header('Ticket queue', 'tickets');
    ?>
    <div class="page-head">
      <h1>Ticket queue</h1>
      <p class="lead"><?= count($tickets) ?> ticket<?= count($tickets) === 1 ? '' : 's' ?> shown.</p>
    </div>

    <form class="filters" method="get" aria-label="Filter tickets">
      <div class="field">
        <label for="f-q">Search</label>
        <input id="f-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="title, requester…">
      </div>
      <div class="field">
        <label for="f-status">Status</label>
        <select id="f-status" name="status">
          <option value="">All</option>
          <?php foreach (HOT_STATUSES as $s): ?>
            <option value="<?= e($s) ?>"<?= $filters['status'] === $s ? ' selected' : '' ?>><?= e(label($s)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="f-cat">Category</label>
        <select id="f-cat" name="category">
          <option value="">All</option>
          <?php foreach (HOT_CATEGORIES as $c): ?>
            <option value="<?= e($c) ?>"<?= $filters['category'] === $c ? ' selected' : '' ?>><?= e(label($c)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="f-pri">Priority</label>
        <select id="f-pri" name="priority">
          <option value="">All</option>
          <?php foreach (HOT_PRIORITIES as $p): ?>
            <option value="<?= e($p) ?>"<?= $filters['priority'] === $p ? ' selected' : '' ?>><?= e(label($p)) ?></option>
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
        <a class="btn btn--ghost" href="index.php">Reset</a>
      </div>
    </form>

    <div class="table-scroll">
      <table class="data">
        <thead>
          <tr><th>#</th><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Department</th><th>Assigned</th><th>Opened</th></tr>
        </thead>
        <tbody>
          <?php if (!$tickets): ?>
            <tr><td colspan="8" class="empty">No tickets match these filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($tickets as $t): ?>
            <tr>
              <td class="num"><a href="ticket.php?id=<?= (int) $t['id'] ?>">#<?= (int) $t['id'] ?></a></td>
              <td><a href="ticket.php?id=<?= (int) $t['id'] ?>"><?= e($t['title']) ?></a></td>
              <td><?= e(label($t['category'])) ?></td>
              <td><?= priority_badge($t['priority']) ?></td>
              <td><?= status_badge($t['status']) ?></td>
              <td><?= e($t['requester_department']) ?></td>
              <td><?= $t['assigned_to'] ? e($t['assigned_to']) : '<span class="muted">Unassigned</span>' ?></td>
              <td class="nowrap"><?= fmt_date($t['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
    hot_footer();
    exit;
endif;

/* ---- PUBLIC VIEW ---- */
$depts = departments_all();
hot_header('Submit a ticket', 'tickets');
?>
<div class="page-head">
  <h1>Get IT support</h1>
  <p class="lead">Submit a request to the IT Support team. You'll get a ticket number so you can check its status.</p>
</div>

<?php if ($flash): ?><div class="alert alert--error"><?= e($flash) ?></div><?php endif; ?>

<div class="cols">
  <section class="card">
    <h2>Submit a ticket</h2>
    <form method="post" class="stack" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="submit">
      <div class="field">
        <label for="title">What do you need help with? <span class="req">*</span></label>
        <input id="title" name="title" maxlength="160" required value="<?= e($_POST['title'] ?? '') ?>" placeholder="e.g. My monitor won't turn on">
      </div>
      <div class="field">
        <label for="description">Describe the problem <span class="req">*</span></label>
        <textarea id="description" name="description" rows="5" required placeholder="What happened, when it started, and any error message."><?= e($_POST['description'] ?? '') ?></textarea>
      </div>
      <div class="grid-2">
        <div class="field">
          <label for="category">Category</label>
          <select id="category" name="category">
            <?php foreach (HOT_CATEGORIES as $c): ?>
              <option value="<?= e($c) ?>"><?= e(label($c)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="priority">Priority</label>
          <select id="priority" name="priority">
            <?php foreach (HOT_PRIORITIES as $p): ?>
              <option value="<?= e($p) ?>"<?= $p === 'medium' ? ' selected' : '' ?>><?= e(label($p)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="grid-2">
        <div class="field">
          <label for="rname">Your name <span class="req">*</span></label>
          <input id="rname" name="requester_name" required value="<?= e($_POST['requester_name'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="rdept">Your department <span class="req">*</span></label>
          <select id="rdept" name="requester_department" required>
            <option value="">Choose…</option>
            <?php foreach ($depts as $d): ?>
              <option value="<?= e($d) ?>"<?= (($_POST['requester_department'] ?? '') === $d) ? ' selected' : '' ?>><?= e($d) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field field--actions">
        <button class="btn btn--lg" type="submit">Submit ticket</button>
      </div>
    </form>
  </section>

  <aside class="card card--aside">
    <h2>Check a ticket</h2>
    <p>Have a ticket number? Look up its current status.</p>
    <form method="get" action="ticket.php" class="stack">
      <div class="field">
        <label for="tid">Ticket number</label>
        <input id="tid" name="id" inputmode="numeric" pattern="[0-9]*" placeholder="e.g. 42" required>
      </div>
      <button class="btn btn--ghost" type="submit">Check status</button>
    </form>
    <hr>
    <h2>Self-help</h2>
    <p>Many issues have a quick fix in the <a href="kb/index.php">Knowledge Base</a> — monitors, Wi-Fi, passwords, and more.</p>
  </aside>
</div>
<?php
hot_footer();
