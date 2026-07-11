<?php use Cadence\Core\Csrf; ?>
<div class="container auth-wrap">
  <div class="card card-pad auth-card">
    <h1>Reset your password</h1>
    <p class="muted small" style="margin-top: var(--sp-2)">Enter your account email. We will send a reset link that works for 30 minutes.</p>

    <form method="post" action="<?= e(url('/forgot-password')) ?>" style="margin-top: var(--sp-5)" novalidate>
      <?= Csrf::field() ?>

      <div class="field">
        <label for="email">Email</label>
        <input class="input" type="email" id="email" name="email" required autocomplete="email">
      </div>

      <button class="btn btn-primary" type="submit" style="width: 100%">Send reset link</button>
    </form>

    <p class="muted small" style="margin-top: var(--sp-4); text-align: center">
      <a href="<?= e(url('/login')) ?>">Back to sign in</a>
    </p>
  </div>
</div>
