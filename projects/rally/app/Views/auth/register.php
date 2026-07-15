<?php
/** @var array<string,string> $errors */
/** @var array<string,string> $old */
?>
<section class="auth-panel">
  <h1>Create account</h1>
  <p class="auth-lede">Set a display name and join the competition.</p>
  <form method="post" action="<?= e(url('/register')) ?>" class="stack-form" novalidate>
    <?= \Rally\Core\Csrf::field() ?>
    <label>
      <span>Display name</span>
      <input type="text" name="name" required maxlength="120" value="<?= e($old['name'] ?? '') ?>">
      <?php if (!empty($errors['name'])): ?><span class="field-error"><?= e($errors['name']) ?></span><?php endif; ?>
    </label>
    <label>
      <span>Username</span>
      <input type="text" name="username" required maxlength="30" pattern="[a-z0-9_]{3,30}" value="<?= e($old['username'] ?? '') ?>">
      <?php if (!empty($errors['username'])): ?><span class="field-error"><?= e($errors['username']) ?></span><?php endif; ?>
    </label>
    <label>
      <span>Email</span>
      <input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>">
      <?php if (!empty($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
    </label>
    <label>
      <span>Timezone</span>
      <input type="text" name="timezone" required value="<?= e($old['timezone'] ?? 'America/Los_Angeles') ?>" list="tz-list">
      <datalist id="tz-list">
        <option value="America/Los_Angeles">
        <option value="America/New_York">
        <option value="America/Chicago">
        <option value="America/Denver">
        <option value="Europe/London">
        <option value="UTC">
      </datalist>
      <?php if (!empty($errors['timezone'])): ?><span class="field-error"><?= e($errors['timezone']) ?></span><?php endif; ?>
    </label>
    <label>
      <span>Password</span>
      <input type="password" name="password" required minlength="8" autocomplete="new-password">
      <?php if (!empty($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
    </label>
    <label>
      <span>Confirm password</span>
      <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password">
      <?php if (!empty($errors['password_confirm'])): ?><span class="field-error"><?= e($errors['password_confirm']) ?></span><?php endif; ?>
    </label>
    <button type="submit" class="button button-primary">Create account</button>
  </form>
  <p class="auth-alt">Already competing? <a href="<?= e(url('/login')) ?>">Sign in</a></p>
</section>
