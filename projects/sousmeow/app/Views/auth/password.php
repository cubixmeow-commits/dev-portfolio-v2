<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-single card card-pad rise-in">
    <h1>Change password</h1>
    <p class="auth-sub">Pick something new; you will stay signed in on this device.</p>
    <form method="post" action="<?= e(url('/account/password')) ?>" data-loading novalidate>
      <?= Csrf::field() ?>
      <div class="field">
        <label class="field-label" for="current_password">Current password</label>
        <input class="input <?= isset($errors['current_password']) ? 'input-invalid' : '' ?>" type="password"
               id="current_password" name="current_password" autocomplete="current-password" required autofocus>
        <?php if (isset($errors['current_password'])): ?><p class="field-error"><?= e($errors['current_password']) ?></p><?php endif; ?>
      </div>
      <div class="field">
        <label class="field-label" for="new_password">New password</label>
        <p class="field-help">At least 8 characters. A short sentence works well.</p>
        <input class="input <?= isset($errors['new_password']) ? 'input-invalid' : '' ?>" type="password"
               id="new_password" name="new_password" autocomplete="new-password" minlength="8" required>
        <?php if (isset($errors['new_password'])): ?><p class="field-error"><?= e($errors['new_password']) ?></p><?php endif; ?>
      </div>
      <div class="auth-actions">
        <button type="submit" class="button button-primary">Update password</button>
        <a class="button button-ghost" href="<?= e(url('/kitchen')) ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>
