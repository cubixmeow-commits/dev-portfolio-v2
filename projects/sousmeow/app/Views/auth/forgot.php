<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-single card rise-in" style="margin:0 auto;padding:var(--space-6);">
    <h1>Forgot your password?</h1>
    <?php if ($sent): ?>
      <p role="status">If an account exists for that email, a password-reset link has been sent.</p>
      <p class="auth-sub"><a href="<?= e(url('/login')) ?>">Back to sign in</a></p>
    <?php else: ?>
      <p class="auth-sub">Enter your email and we will send a reset link if an account exists.</p>
      <form method="post" action="<?= e(url('/forgot-password')) ?>" data-loading novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="input <?= isset($errors['email']) ? 'input-invalid' : '' ?>" type="email" id="email" name="email"
                 value="<?= e($old['email']) ?>" autocomplete="email" required autofocus
                 aria-describedby="<?= isset($errors['email']) ? 'email-error' : '' ?>">
          <?php if (isset($errors['email'])): ?><p class="field-error" id="email-error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary button-block">Send reset link</button>
      </form>
      <p class="auth-footnote"><a href="<?= e(url('/login')) ?>">Back to sign in</a></p>
    <?php endif; ?>
  </div>
</div>
