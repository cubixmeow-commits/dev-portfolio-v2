<?php
use SousMeow\Core\Auth;
use SousMeow\Models\User;

$verified = Auth::isVerified();
?>
<div class="page account-page">
  <div class="account-layout">
    <?php \SousMeow\Core\View::partial('account/_nav', ['section' => $section, 'user' => $user]); ?>
    <div class="account-content card rise-in">
      <header class="account-header">
        <div class="account-avatar" aria-hidden="true"><?= e(User::initials((string) $user['name'])) ?></div>
        <div>
          <h1><?= e($user['name']) ?></h1>
          <p class="account-email"><?= e($user['email']) ?></p>
          <?php if (!$verified): ?>
            <p class="account-badge account-badge-warn">Email not verified · <a href="<?= e(url('/verify-email/pending')) ?>">Resend link</a></p>
          <?php else: ?>
            <p class="account-badge account-badge-ok">Verified</p>
          <?php endif; ?>
        </div>
      </header>

      <section class="account-stats" aria-labelledby="stats-heading">
        <h2 id="stats-heading">Your activity</h2>
        <dl class="stats-grid">
          <div><dt>Member since</dt><dd><?= e(date('M j, Y', strtotime((string) $stats['member_since']))) ?></dd></div>
          <div><dt>Active projects</dt><dd><?= (int) $stats['active_projects'] ?></dd></div>
          <div><dt>Completed projects</dt><dd><?= (int) $stats['completed_projects'] ?></dd></div>
          <div><dt>Steps approved</dt><dd><?= (int) $stats['steps_completed'] ?></dd></div>
          <div><dt>Project Kits exported</dt><dd><?= (int) $stats['kits_exported'] ?></dd></div>
          <?php if ($stats['most_used_workflow']): ?>
          <div><dt>Most-used workflow</dt><dd><?= e($stats['most_used_workflow']) ?></dd></div>
          <?php endif; ?>
          <?php if ($stats['completion_rate'] !== null): ?>
          <div><dt>Step completion rate</dt><dd><?= (int) $stats['completion_rate'] ?>%</dd></div>
          <?php endif; ?>
        </dl>
      </section>
    </div>
  </div>
</div>
