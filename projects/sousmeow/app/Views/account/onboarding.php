<?php
use SousMeow\Core\Csrf;
use SousMeow\Models\User;
?>
<div class="page account-page onboarding-page">
  <div class="account-content card rise-in auth-single">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
    <h1>Welcome to SousMeow</h1>
    <p class="account-lead">Here is how a guided project works:</p>
    <ol class="onboarding-steps">
      <li><strong>Choose a workflow</strong> from the marketplace</li>
      <li><strong>Add your project details</strong> in the Pantry</li>
      <li><strong>Run each prepared prompt</strong> in the AI you already use</li>
      <li><strong>Bring the response back</strong> for review</li>
      <li><strong>Approve each step</strong> and export your finished Project Kit</li>
    </ol>

    <form method="post" action="<?= e(url('/onboarding')) ?>" class="account-form">
      <?= Csrf::field() ?>
      <p class="field-help">Optional — personalize your experience (you can change these anytime in Account).</p>
      <div class="field">
        <label class="field-label" for="preferred_ai">Preferred AI</label>
        <select class="input" id="preferred_ai" name="preferred_ai">
          <?php foreach (User::PREFERRED_AI_OPTIONS as $opt): ?>
          <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="field-label" for="ai_experience_level">Experience level</label>
        <select class="input" id="ai_experience_level" name="ai_experience_level">
          <option value="">—</option>
          <?php foreach (User::EXPERIENCE_LEVELS as $opt): ?>
          <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label class="field-label" for="timezone">Time zone</label>
        <select class="input" id="timezone" name="timezone">
          <option value="">Server default</option>
          <?php foreach ($timezones as $tz): ?>
          <option value="<?= e($tz['id']) ?>"><?= e($tz['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="onboarding-actions">
        <button type="submit" name="explore" value="1" class="button button-primary">Explore workflows</button>
        <button type="submit" name="skip" value="1" class="button button-secondary">Skip for now</button>
      </div>
    </form>
  </div>
</div>
