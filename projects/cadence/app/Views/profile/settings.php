<?php use Cadence\Core\Csrf; ?>
<div class="container" style="max-width: 640px">
  <div class="page-head">
    <div>
      <h1>Settings</h1>
      <p>Your profile, password, and account.</p>
    </div>
  </div>

  <?php if ($user['email_verified_at'] === null): ?>
    <div class="card card-pad" style="margin-bottom: var(--sp-5); display: flex; align-items: center; justify-content: space-between; gap: var(--sp-4); flex-wrap: wrap">
      <div>
        <h3>Verify your email</h3>
        <p class="muted small">We sent a link to <span class="num"><?= e($user['email']) ?></span>. It works for 24 hours.</p>
      </div>
      <form method="post" action="<?= e(url('/verify/resend')) ?>">
        <?= Csrf::field() ?>
        <button class="btn btn-secondary" type="submit">Resend link</button>
      </form>
    </div>
  <?php endif; ?>

  <section class="card card-pad" style="margin-bottom: var(--sp-5)">
    <h2 style="font-size: var(--text-lg)">Profile</h2>
    <form method="post" action="<?= e(url('/settings/profile')) ?>" style="margin-top: var(--sp-4)">
      <?= Csrf::field() ?>

      <div class="field">
        <label for="display_name">Display name</label>
        <input class="input" type="text" id="display_name" name="display_name" required maxlength="60"
               value="<?= e($user['display_name']) ?>">
      </div>

      <div class="field">
        <label for="bio">Bio</label>
        <textarea class="input" id="bio" name="bio" rows="2" maxlength="160"
                  placeholder="A sentence about what you are building."><?= e($user['bio'] ?? '') ?></textarea>
        <p class="hint">Shown on your public profile. 160 characters.</p>
      </div>

      <div class="field">
        <label for="timezone">Timezone</label>
        <select class="input" id="timezone" name="timezone">
          <?php foreach ($timezones as $tz): ?>
            <option value="<?= e($tz) ?>"<?= $user['timezone'] === $tz ? ' selected' : '' ?>><?= e($tz) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="hint">Streaks roll over at midnight in your timezone.</p>
      </div>

      <button class="btn btn-primary" type="submit">Save changes</button>
    </form>
  </section>

  <section class="card card-pad" style="margin-bottom: var(--sp-5)">
    <h2 style="font-size: var(--text-lg)">Password</h2>
    <form method="post" action="<?= e(url('/settings/password')) ?>" style="margin-top: var(--sp-4)">
      <?= Csrf::field() ?>

      <div class="field">
        <label for="current_password">Current password</label>
        <input class="input" type="password" id="current_password" name="current_password" required autocomplete="current-password">
      </div>

      <div class="field">
        <label for="new_password">New password</label>
        <input class="input" type="password" id="new_password" name="new_password" required minlength="10" autocomplete="new-password">
        <p class="hint">At least 10 characters. Common passwords are rejected.</p>
      </div>

      <button class="btn btn-primary" type="submit">Change password</button>
    </form>
  </section>

  <section class="card card-pad" style="border-color: var(--warn)">
    <h2 style="font-size: var(--text-lg); color: var(--warn)">Delete account</h2>
    <p class="muted small" style="margin-top: var(--sp-2)">
      Deleting removes your name, handle, and email permanently. Your check-ins stay in challenge totals under "Deleted member".
    </p>
    <form method="post" action="<?= e(url('/settings/delete')) ?>" style="margin-top: var(--sp-4)"
          data-confirm="Delete your account? This cannot be undone.">
      <?= Csrf::field() ?>

      <div class="field">
        <label for="delete_password">Your password</label>
        <input class="input" type="password" id="delete_password" name="password" required autocomplete="current-password">
      </div>

      <button class="btn btn-danger" type="submit">Delete my account</button>
    </form>
  </section>
</div>
