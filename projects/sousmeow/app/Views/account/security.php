<?php use SousMeow\Core\Auth; use SousMeow\Core\Csrf; ?>
<div class="page account-page">
  <div class="account-layout">
    <?php \SousMeow\Core\View::partial('account/_nav', ['section' => $section, 'user' => $user]); ?>
    <div class="account-content card rise-in">
      <h1>Security</h1>

      <section class="account-section" aria-labelledby="email-heading">
        <h2 id="email-heading">Email</h2>
        <p><strong><?= e($user['email']) ?></strong>
          <?php if (Auth::isVerified()): ?>
            <span class="account-badge account-badge-ok">Verified</span>
          <?php else: ?>
            <span class="account-badge account-badge-warn">Not verified</span>
          <?php endif; ?>
        </p>
        <?php if ($user['pending_email'] ?? null): ?>
          <p class="field-help">Pending change to <?= e($user['pending_email']) ?> — check that inbox for a confirmation link.</p>
        <?php endif; ?>
      </section>

      <section class="account-section" aria-labelledby="password-heading">
        <h2 id="password-heading">Change password</h2>
        <?php if (!empty($user['password_changed_at'])): ?>
          <p class="field-help">Last changed <?= e(date('M j, Y', strtotime((string) $user['password_changed_at']))) ?></p>
        <?php endif; ?>
        <form method="post" action="<?= e(url('/account/security/password')) ?>" class="account-form" novalidate>
          <?= Csrf::field() ?>
          <div class="field">
            <label class="field-label" for="current_password">Current password</label>
            <input class="input <?= isset($errors['current_password']) ? 'input-invalid' : '' ?>" type="password"
                   id="current_password" name="current_password" autocomplete="current-password" required>
            <?php if (isset($errors['current_password'])): ?><p class="field-error"><?= e($errors['current_password']) ?></p><?php endif; ?>
          </div>
          <div class="field">
            <label class="field-label" for="new_password">New password</label>
            <input class="input <?= isset($errors['new_password']) ? 'input-invalid' : '' ?>" type="password"
                   id="new_password" name="new_password" autocomplete="new-password" minlength="8" required>
            <?php if (isset($errors['new_password'])): ?><p class="field-error"><?= e($errors['new_password']) ?></p><?php endif; ?>
          </div>
          <div class="field">
            <label class="field-label" for="new_password_confirm">Confirm new password</label>
            <input class="input <?= isset($errors['new_password_confirm']) ? 'input-invalid' : '' ?>" type="password"
                   id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" required>
            <?php if (isset($errors['new_password_confirm'])): ?><p class="field-error"><?= e($errors['new_password_confirm']) ?></p><?php endif; ?>
          </div>
          <button type="submit" class="button button-primary">Update password</button>
        </form>
      </section>

      <?php if (Auth::isVerified()): ?>
      <section class="account-section" aria-labelledby="email-change-heading">
        <h2 id="email-change-heading">Change email</h2>
        <p class="field-help">Your current email stays active until you confirm the new address.</p>
        <form method="post" action="<?= e(url('/account/security/email')) ?>" class="account-form" novalidate>
          <?= Csrf::field() ?>
          <div class="field">
            <label class="field-label" for="new_email">New email</label>
            <input class="input <?= isset($errors['new_email']) ? 'input-invalid' : '' ?>" type="email" id="new_email"
                   name="new_email" autocomplete="email" required>
            <?php if (isset($errors['new_email'])): ?><p class="field-error"><?= e($errors['new_email']) ?></p><?php endif; ?>
          </div>
          <div class="field">
            <label class="field-label" for="email_current_password">Current password</label>
            <input class="input <?= isset($errors['current_password']) ? 'input-invalid' : '' ?>" type="password"
                   id="email_current_password" name="current_password" autocomplete="current-password" required>
            <?php if (isset($errors['current_password']) && !isset($errors['new_password'])): ?>
              <p class="field-error"><?= e($errors['current_password']) ?></p>
            <?php endif; ?>
          </div>
          <button type="submit" class="button button-secondary">Request email change</button>
        </form>
      </section>
      <?php endif; ?>
    </div>
  </div>
</div>
