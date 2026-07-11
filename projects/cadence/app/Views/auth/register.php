<?php use Cadence\Core\Csrf; ?>
<div class="container auth-wrap">
  <div class="card card-pad auth-card">
    <h1>Create your account</h1>
    <p class="muted small" style="margin-top: var(--sp-2)">Join challenges, keep streaks, and build habits with company.</p>

    <form method="post" action="<?= e(url('/register')) ?>" style="margin-top: var(--sp-5)" novalidate>
      <?= Csrf::field() ?>

      <div class="field">
        <label for="email">Email</label>
        <input class="input" type="email" id="email" name="email" required autocomplete="email"
               value="<?= e($old['email'] ?? '') ?>"<?= isset($errors['email']) ? ' aria-invalid="true"' : '' ?>>
        <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="display_name">Display name</label>
        <input class="input" type="text" id="display_name" name="display_name" required maxlength="60" autocomplete="name"
               value="<?= e($old['display_name'] ?? '') ?>"<?= isset($errors['display_name']) ? ' aria-invalid="true"' : '' ?>>
        <?php if (isset($errors['display_name'])): ?><p class="field-error"><?= e($errors['display_name']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="handle">Handle</label>
        <input class="input" type="text" id="handle" name="handle" required maxlength="30"
               pattern="[a-z0-9_]{3,30}" autocomplete="username" spellcheck="false"
               value="<?= e($old['handle'] ?? '') ?>"<?= isset($errors['handle']) ? ' aria-invalid="true"' : '' ?>
               data-check-url="<?= e(url('/register/check-handle')) ?>">
        <p class="hint" id="handle-hint">Your public URL: /u/handle. Lowercase letters, numbers, underscores.</p>
        <?php if (isset($errors['handle'])): ?><p class="field-error"><?= e($errors['handle']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input class="input" type="password" id="password" name="password" required minlength="10" autocomplete="new-password"
               <?= isset($errors['password']) ? ' aria-invalid="true"' : '' ?>>
        <p class="hint">At least 10 characters. Common passwords are rejected.</p>
        <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
      </div>

      <div class="field">
        <label for="timezone">Timezone</label>
        <select class="input" id="timezone" name="timezone">
          <?php foreach ($timezones as $tz): ?>
            <option value="<?= e($tz) ?>"<?= ($old['timezone'] ?? 'America/Los_Angeles') === $tz ? ' selected' : '' ?>><?= e($tz) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="hint">Streaks roll over at midnight in your timezone.</p>
      </div>

      <button class="btn btn-primary btn-lg" type="submit" style="width: 100%">Create account</button>
    </form>

    <p class="muted small" style="margin-top: var(--sp-4); text-align: center">
      Already have an account? <a href="<?= e(url('/login')) ?>">Sign in</a>
    </p>
  </div>
</div>
<script src="<?= e(url('/assets/js/handle-check.js')) ?>" defer></script>
