<?php use Cadence\Core\Csrf; ?>
<div class="container auth-wrap">
  <div class="card card-pad auth-card">
    <h1>Sign in</h1>

    <?php if (!empty($error)): ?>
      <div class="flash flash-error" role="alert" style="margin-top: var(--sp-4)"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/login')) ?>" style="margin-top: var(--sp-5)" novalidate>
      <?= Csrf::field() ?>

      <div class="field">
        <label for="email">Email</label>
        <input class="input" type="email" id="email" name="email" required autocomplete="email"
               value="<?= e($old['email'] ?? '') ?>">
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input class="input" type="password" id="password" name="password" required autocomplete="current-password">
        <p class="hint"><a href="<?= e(url('/forgot-password')) ?>">Forgot your password?</a></p>
      </div>

      <button class="btn btn-primary btn-lg" type="submit" style="width: 100%">Sign in</button>
    </form>

    <p class="muted small" style="margin-top: var(--sp-4); text-align: center">
      New here? <a href="<?= e(url('/register')) ?>">Create your account</a>
    </p>
  </div>
</div>
