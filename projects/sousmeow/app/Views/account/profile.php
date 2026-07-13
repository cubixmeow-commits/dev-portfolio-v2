<?php use SousMeow\Core\Csrf; use SousMeow\Models\User; ?>
<div class="page account-page">
  <div class="account-layout">
    <?php \SousMeow\Core\View::partial('account/_nav', ['section' => $section, 'user' => $user]); ?>
    <div class="account-content card rise-in">
      <h1>Profile</h1>
      <p class="account-lead">Optional details for your account. Initials are used when no avatar is set.</p>
      <form method="post" action="<?= e(url('/account/profile')) ?>" class="account-form" novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="name">Display name</label>
          <input class="input <?= isset($errors['name']) ? 'input-invalid' : '' ?>" type="text" id="name" name="name"
                 value="<?= e($user['name']) ?>" autocomplete="name" maxlength="80" required>
          <?php if (isset($errors['name'])): ?><p class="field-error" id="name-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="bio">Short bio <span class="field-optional">optional</span></label>
          <textarea class="input <?= isset($errors['bio']) ? 'input-invalid' : '' ?>" id="bio" name="bio" rows="3"
                    maxlength="280" aria-describedby="bio-help"><?= e($user['bio'] ?? '') ?></textarea>
          <p class="field-help" id="bio-help">Up to 280 characters.</p>
          <?php if (isset($errors['bio'])): ?><p class="field-error"><?= e($errors['bio']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="website">Website <span class="field-optional">optional</span></label>
          <input class="input <?= isset($errors['website']) ? 'input-invalid' : '' ?>" type="url" id="website" name="website"
                 value="<?= e($user['website'] ?? '') ?>" autocomplete="url" placeholder="https://">
          <?php if (isset($errors['website'])): ?><p class="field-error"><?= e($errors['website']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="avatar_url">Avatar URL <span class="field-optional">optional</span></label>
          <input class="input <?= isset($errors['avatar_url']) ? 'input-invalid' : '' ?>" type="url" id="avatar_url" name="avatar_url"
                 value="<?= e($user['avatar_url'] ?? '') ?>" placeholder="https://">
          <?php if (isset($errors['avatar_url'])): ?><p class="field-error"><?= e($errors['avatar_url']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="button button-primary">Save profile</button>
      </form>
    </div>
  </div>
</div>
