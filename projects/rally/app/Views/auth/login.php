<?php
/** @var array<string,string> $errors */
/** @var string $email */
?>
<section class="auth-panel">
  <h1>Sign in</h1>
  <p class="auth-lede">Jump back into your active series.</p>
  <?php if (!empty($errors['form'])): ?>
    <p class="form-error" role="alert"><?= e($errors['form']) ?></p>
  <?php endif; ?>
  <form method="post" action="<?= e(url('/login')) ?>" class="stack-form" novalidate>
    <?= \Rally\Core\Csrf::field() ?>
    <label>
      <span>Email</span>
      <input type="email" name="email" autocomplete="username" required value="<?= e($email) ?>">
    </label>
    <label>
      <span>Password</span>
      <input type="password" name="password" autocomplete="current-password" required>
    </label>
    <button type="submit" class="button button-primary">Sign in</button>
  </form>
  <p class="auth-alt">No account? <a href="<?= e(url('/register')) ?>">Create one</a></p>
  <p class="hint">Demo: iain@rally.demo / rally-demo-2026</p>
</section>
