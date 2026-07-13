<?php use SousMeow\Core\Csrf; ?>
<div class="page auth-page">
  <div class="auth-split card rise-in">
    <aside class="auth-aside">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Check your inbox</h2>
      <p>We sent a verification link to <strong><?= e($user['email']) ?></strong>.</p>
      <p>Click the link to unlock starting projects, approving steps, and exporting Project Kits.</p>
      <?php if (!empty($emailFailed)): ?>
        <p class="auth-aside-note" role="alert">We could not deliver the email. Check your address or try resending below.</p>
      <?php endif; ?>
    </aside>
    <div class="auth-form-col">
      <h1>Verify your email</h1>
      <p class="auth-sub">Until you verify, you can browse workflows and view your account, but cannot start new projects.</p>
      <form method="post" action="<?= e(url('/verify-email/resend')) ?>" data-loading>
        <?= Csrf::field() ?>
        <button type="submit" class="button button-primary button-block">Resend verification email</button>
      </form>
      <p class="auth-footnote"><a href="<?= e(url('/account')) ?>">Go to account settings</a> · <a href="<?= e(url('/marketplace')) ?>">Browse workflows</a></p>
    </div>
  </div>
</div>
