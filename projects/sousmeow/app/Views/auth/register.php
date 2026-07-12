<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-split card rise-in">
    <aside class="auth-aside">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Ten minutes to your first kit</h2>
      <p>An account gives you a Kitchen: a home for your Projects, your Pantry, and everything you approve along the way.</p>
      <ol class="auth-steps">
        <li><strong>Stock the Pantry</strong> with facts about your product</li>
        <li><strong>Cook each Recipe</strong>: copy a prompt, run it in your own AI, paste the result</li>
        <li><strong>Export the kit</strong> once every step is approved</li>
      </ol>
      <p class="auth-aside-note">No AI subscription is sold here and none is required to try it: every Recipe includes a sample response you can paste with one click.</p>
    </aside>
    <div class="auth-form-col">
      <h1>Create your account</h1>
      <p class="auth-sub">Already have one? <a href="<?= e(url('/login')) ?>">Sign in</a> instead.</p>
      <form method="post" action="<?= e(url('/register')) ?>" data-loading novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="name">Name</label>
          <p class="field-help">What should we call you around the kitchen?</p>
          <input class="input <?= isset($errors['name']) ? 'input-invalid' : '' ?>" type="text" id="name" name="name"
                 value="<?= e($old['name']) ?>" autocomplete="name" maxlength="80" required autofocus>
          <?php if (isset($errors['name'])): ?><p class="field-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="email">Email</label>
          <input class="input <?= isset($errors['email']) ? 'input-invalid' : '' ?>" type="email" id="email" name="email"
                 value="<?= e($old['email']) ?>" autocomplete="email" required>
          <?php if (isset($errors['email'])): ?><p class="field-error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="password">Password</label>
          <p class="field-help">At least 8 characters. A short sentence works well.</p>
          <input class="input <?= isset($errors['password']) ? 'input-invalid' : '' ?>" type="password" id="password"
                 name="password" autocomplete="new-password" minlength="8" required>
          <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary button-block">Create account</button>
      </form>
    </div>
  </div>
</div>
