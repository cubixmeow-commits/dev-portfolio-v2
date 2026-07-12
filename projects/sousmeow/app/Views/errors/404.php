<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'searching']); ?>
    <h1>This shelf is empty</h1>
    <p>The page you asked for is not in the cookbook. It may have moved, or the address has a typo.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/')) ?>">
        <?= $auth ? 'Back to my Kitchen' : 'Back to the homepage' ?>
      </a>
      <a class="button button-ghost" href="<?= e(url('/marketplace')) ?>">Browse the Marketplace</a>
    </p>
  </div>
</section>
