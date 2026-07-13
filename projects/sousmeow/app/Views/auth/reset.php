<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-single card rise-in" style="margin:0 auto;padding:var(--space-6);">
    <h1>Choose a new password</h1>
    <form method="post" action="<?= e(url('/reset-password/' . $token)) ?>" data-loading novalidate>
      <?= Csrf::field() ?>
      <div class="field">
        <label class="field-label" for="password">New password</label>
        <p class="field-help">At least 8 characters.</p>
        <input class="input <?= isset($errors['password']) ? 'input-invalid' : '' ?>" type="password" id="password"
               name="password" autocomplete="new-password" minlength="8" required autofocus>
        <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
      </div>
      <div class="field">
        <label class="field-label" for="password_confirm">Confirm password</label>
        <input class="input <?= isset($errors['password_confirm']) ? 'input-invalid' : '' ?>" type="password"
               id="password_confirm" name="password_confirm" autocomplete="new-password" required>
        <?php if (isset($errors['password_confirm'])): ?><p class="field-error"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
      </div>
      <button type="submit" class="button button-primary button-block">Reset password</button>
    </form>
  </div>
</div>
