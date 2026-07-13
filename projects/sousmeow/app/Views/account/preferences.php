<?php
use SousMeow\Core\Csrf;
use SousMeow\Models\User;
?>
<div class="page account-page">
  <div class="account-layout">
    <?php \SousMeow\Core\View::partial('account/_nav', ['section' => $section, 'user' => $user]); ?>
    <div class="account-content card rise-in">
      <h1>Preferences</h1>
      <p class="account-lead">Optional settings to personalize your experience. Nothing here is required.</p>
      <form method="post" action="<?= e(url('/account/preferences')) ?>" class="account-form" novalidate>
        <?= Csrf::field() ?>
        <div class="field">
          <label class="field-label" for="preferred_ai">Preferred AI</label>
          <select class="input" id="preferred_ai" name="preferred_ai">
            <?php foreach (User::PREFERRED_AI_OPTIONS as $opt): ?>
            <option value="<?= e($opt) ?>" <?= ($user['preferred_ai'] ?? 'No preference') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label class="field-label" for="ai_experience_level">AI experience level <span class="field-optional">optional</span></label>
          <select class="input" id="ai_experience_level" name="ai_experience_level">
            <option value="">—</option>
            <?php foreach (User::EXPERIENCE_LEVELS as $opt): ?>
            <option value="<?= e($opt) ?>" <?= ($user['ai_experience_level'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['ai_experience_level'])): ?><p class="field-error"><?= e($errors['ai_experience_level']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="timezone">Time zone <span class="field-optional">optional</span></label>
          <select class="input" id="timezone" name="timezone">
            <option value="">Use server default</option>
            <?php foreach ($timezones as $tz): ?>
            <option value="<?= e($tz) ?>" <?= ($user['timezone'] ?? '') === $tz ? 'selected' : '' ?>><?= e($tz) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['timezone'])): ?><p class="field-error"><?= e($errors['timezone']) ?></p><?php endif; ?>
        </div>
        <div class="field">
          <label class="field-label" for="theme_preference">Theme</label>
          <select class="input" id="theme_preference" name="theme_preference">
            <?php foreach (User::THEME_OPTIONS as $opt): ?>
            <option value="<?= e($opt) ?>" <?= ($user['theme_preference'] ?? 'system') === $opt ? 'selected' : '' ?>><?= e(ucfirst($opt)) ?></option>
            <?php endforeach; ?>
          </select>
          <p class="field-help">Stored for future use. Full theme switching is not enabled yet.</p>
        </div>
        <button type="submit" class="button button-primary">Save preferences</button>
      </form>
    </div>
  </div>
</div>
