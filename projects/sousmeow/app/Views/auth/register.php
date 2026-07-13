<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-split card rise-in">
    <aside class="auth-aside">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Try a full workflow in ten minutes</h2>
      <p>An account gives you a place to save projects, review responses, and export finished files.</p>
      <ol class="auth-steps">
        <li><strong>Add project details</strong> — your Pantry, filled once</li>
        <li><strong>Run each step</strong> — copy the prompt, run it in your AI, paste the answer back</li>
        <li><strong>Export your Project Kit</strong> when every step is approved</li>
      </ol>
    </aside>
    <div class="auth-form-col">
      <h1>Create your account</h1>
      <p class="auth-sub">Already have one? <a href="<?= e(url('/login')) ?>">Sign in</a> instead.</p>
      <?php if ($errors !== []): ?>
        <div class="form-errors" role="alert" tabindex="-1">
          <p>Please fix the highlighted fields.</p>
        </div>
      <?php endif; ?>
      <form method="post" action="<?= e(url('/register')) ?>" data-loading novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="name">Display name</label>
          <input class="input <?= isset($errors['name']) ? 'input-invalid' : '' ?>" type="text" id="name" name="name"
                 value="<?= e($old['name']) ?>" autocomplete="name" maxlength="80" required autofocus
                 aria-describedby="<?= isset($errors['name']) ? 'name-error' : '' ?>">
          <?php if (isset($errors['name'])): ?><p class="field-error" id="name-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="input <?= isset($errors['email']) ? 'input-invalid' : '' ?>" type="email" id="email" name="email"
                 value="<?= e($old['email']) ?>" autocomplete="email" maxlength="190" required>
          <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="password">Password</label>
          <p class="field-help">At least 8 characters.</p>
          <input class="input <?= isset($errors['password']) ? 'input-invalid' : '' ?>" type="password" id="password"
                 name="password" autocomplete="new-password" minlength="8" required>
          <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="password_confirm">Confirm password</label>
          <input class="input <?= isset($errors['password_confirm']) ? 'input-invalid' : '' ?>" type="password"
                 id="password_confirm" name="password_confirm" autocomplete="new-password" minlength="8" required>
          <?php if (isset($errors['password_confirm'])): ?><p class="field-error"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
        </div>
        <div class="field field-checkbox">
          <label class="checkbox-label">
            <input type="checkbox" name="terms" value="1" <?= isset($errors['terms']) ? '' : '' ?> required
                   aria-describedby="<?= isset($errors['terms']) ? 'terms-error' : '' ?>">
            I agree to the <a href="<?= e(url('/terms')) ?>" target="_blank" rel="noopener">Terms of Service</a>
            and <a href="<?= e(url('/privacy')) ?>" target="_blank" rel="noopener">Privacy Policy</a>
          </label>
          <?php if (isset($errors['terms'])): ?><p class="field-error" id="terms-error"><?= e($errors['terms']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary button-block">Create account</button>
      </form>
    </div>
  </div>
</div>
