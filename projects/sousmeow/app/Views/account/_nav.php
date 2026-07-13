<?php use SousMeow\Models\User; ?>
<nav class="account-nav" aria-label="Account sections">
  <a href="<?= e(url('/account')) ?>" class="<?= ($section ?? '') === 'overview' ? 'is-active' : '' ?>">Overview</a>
  <a href="<?= e(url('/account/profile')) ?>" class="<?= ($section ?? '') === 'profile' ? 'is-active' : '' ?>">Profile</a>
  <a href="<?= e(url('/account/preferences')) ?>" class="<?= ($section ?? '') === 'preferences' ? 'is-active' : '' ?>">Preferences</a>
  <a href="<?= e(url('/account/security')) ?>" class="<?= ($section ?? '') === 'security' ? 'is-active' : '' ?>">Security</a>
  <?php if (User::isRealUser($user ?? [])): ?>
  <a href="<?= e(url('/account/data')) ?>" class="<?= ($section ?? '') === 'data' ? 'is-active' : '' ?>">Your data</a>
  <?php endif; ?>
</nav>
