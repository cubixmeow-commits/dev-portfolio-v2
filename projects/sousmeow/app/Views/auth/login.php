<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-split card rise-in">
    <aside class="auth-aside">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
      <h2>Back to the kitchen</h2>
      <p>Everything is where you left it. Sign in and pick up the next Recipe.</p>
      <ul class="auth-points">
        <li>Projects resume at the exact step you stopped at</li>
        <li>Every pasted response is versioned; nothing is ever lost</li>
        <li>Finished Cookbooks export as a Project Kit</li>
      </ul>
    </aside>
    <div class="auth-form-col">
      <h1>Sign in</h1>
      <p class="auth-sub">New here? <a href="<?= e(url('/register')) ?>">Create an account</a> in under a minute.</p>
      <form method="post" action="<?= e(url('/login')) ?>" data-loading novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="input <?= isset($errors['email']) ? 'input-invalid' : '' ?>" type="email" id="email" name="email"
                 value="<?= e($old['email']) ?>" autocomplete="email" required autofocus>
          <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="password">Password</label>
          <input class="input <?= isset($errors['password']) ? 'input-invalid' : '' ?>" type="password" id="password"
                 name="password" autocomplete="current-password" required>
          <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary button-block">Sign in</button>
      </form>
      <p class="auth-footnote">Forgot your password? This demo has no email delivery; an admin can issue a temporary password with <code>php scripts/seed.php --reset-password you@example.com</code>.</p>
    </div>
  </div>
</div>
