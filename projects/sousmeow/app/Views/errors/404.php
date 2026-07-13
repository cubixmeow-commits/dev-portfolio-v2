<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'searching']); ?>
    <h1>Page not found</h1>
    <p>The page you asked for does not exist. It may have moved, or the address has a typo.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/')) ?>">
        <?= $auth ? 'Back to my projects' : 'Back to homepage' ?>
      </a>
      <a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Explore workflows</a>
    </p>
  </div>
</section>
