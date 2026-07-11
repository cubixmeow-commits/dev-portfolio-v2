<?php use Cadence\Core\Csrf; ?>
<div class="container auth-wrap">
  <div class="card card-pad auth-card">
    <h1>Choose a new password</h1>

    <?php if (!empty($error)): ?>
      <div class="flash flash-error" role="alert" style="margin-top: var(--sp-4)"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/reset-password/' . $token)) ?>" style="margin-top: var(--sp-5)" novalidate>
      <?= Csrf::field() ?>

      <div class="field">
        <label for="password">New password</label>
        <input class="input" type="password" id="password" name="password" required minlength="10" autocomplete="new-password">
        <p class="hint">At least 10 characters. Common passwords are rejected.</p>
      </div>

      <button class="btn btn-primary" type="submit" style="width: 100%">Set new password</button>
    </form>
  </div>
</div>
