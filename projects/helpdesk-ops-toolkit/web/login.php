<?php
require __DIR__ . '/lib/bootstrap.php';

$next  = (string) ($_GET['next'] ?? $_POST['next'] ?? 'index.php');
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_check();
    if (attempt_login((string) ($_POST['user'] ?? ''), (string) ($_POST['pass'] ?? ''))) {
        // only allow same-app relative redirects
        redirect(preg_match('#^[a-z0-9_\-./?=&]+$#i', $next) ? $next : 'index.php');
    }
    $error = 'That username or password was not recognised.';
}

if (is_agent()) {
    redirect('index.php');
}

$cfg = $GLOBALS['HOT_CFG'];
hot_header('Agent sign in', '');
?>
<div class="page-head">
  <h1>Agent sign in</h1>
  <p class="lead">Support agents can manage the queue, run reports, and update assets.</p>
</div>

<div class="cols cols--narrow">
  <section class="card">
    <?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= e($next) ?>">
      <div class="field">
        <label for="user">Username</label>
        <input id="user" name="user" autocomplete="username" required autofocus>
      </div>
      <div class="field">
        <label for="pass">Password</label>
        <input id="pass" name="pass" type="password" autocomplete="current-password" required>
      </div>
      <div class="field field--actions">
        <button class="btn btn--lg" type="submit">Sign in</button>
      </div>
    </form>
  </section>
  <aside class="card card--aside">
    <h2>Demo credentials</h2>
    <p>This is a public portfolio demo, so the agent login is shared:</p>
    <p class="creds">
      Username <code><?= e($cfg['agent_user']) ?></code><br>
      Password <code><?= e($cfg['agent_pass']) ?></code>
    </p>
    <p class="muted">In a real deployment this would be individual accounts backed by the users table, hashed passwords, or single sign-on.</p>
  </aside>
</div>
<?php
hot_footer();
