<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'guarding']); ?>
    <h1>That pantry is not yours</h1>
    <p>Your account does not have access to this page. If you think it should, sign in with the right account.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/login')) ?>">
        <?= $auth ? 'Back to my Kitchen' : 'Sign in' ?>
      </a>
    </p>
  </div>
</section>
