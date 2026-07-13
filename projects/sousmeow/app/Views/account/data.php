<?php use SousMeow\Core\Csrf; ?>
<div class="page account-page">
  <div class="account-layout">
    <?php \SousMeow\Core\View::partial('account/_nav', ['section' => $section, 'user' => $user]); ?>
    <div class="account-content card rise-in">
      <h1>Your data</h1>
      <p class="account-lead">Export or delete your personal account data.</p>

      <section class="account-section" aria-labelledby="export-heading">
        <h2 id="export-heading">Export your data</h2>
        <p>Download a ZIP with your profile, projects, pantry details, artifact versions, quality confirmations, and export history.</p>
        <form method="post" action="<?= e(url('/account/data/export')) ?>">
          <?= Csrf::field() ?>
          <button type="submit" class="button button-secondary">Download data export</button>
        </form>
      </section>

      <section class="account-section account-danger" aria-labelledby="delete-heading">
        <h2 id="delete-heading">Delete account</h2>
        <p>This permanently removes your account, projects, and artifacts. This cannot be undone.</p>
        <form method="post" action="<?= e(url('/account/delete')) ?>" class="account-form" novalidate>
          <?= Csrf::field() ?>
          <div class="field">
            <label class="field-label" for="password">Your password</label>
            <input class="input <?= isset($errors['password']) ? 'input-invalid' : '' ?>" type="password"
                   id="password" name="password" autocomplete="current-password" required>
            <?php if (isset($errors['password'])): ?><p class="field-error"><?= e($errors['password']) ?></p><?php endif; ?>
          </div>
          <div class="field">
            <label class="field-label" for="confirm_phrase">Type <strong>DELETE MY ACCOUNT</strong> to confirm</label>
            <input class="input <?= isset($errors['confirm_phrase']) ? 'input-invalid' : '' ?>" type="text"
                   id="confirm_phrase" name="confirm_phrase" autocomplete="off" required
                   aria-describedby="delete-help">
            <p class="field-help" id="delete-help">This extra step prevents accidental deletion.</p>
            <?php if (isset($errors['confirm_phrase'])): ?><p class="field-error"><?= e($errors['confirm_phrase']) ?></p><?php endif; ?>
          </div>
          <button type="submit" class="button button-danger">Permanently delete my account</button>
        </form>
      </section>
    </div>
  </div>
</div>
