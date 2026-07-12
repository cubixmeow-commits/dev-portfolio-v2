<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'spill']); ?>
    <h1>We dropped a pan</h1>
    <p>Something went wrong on our side. Your Pantry and approved Artifacts are safe. Try again in a minute.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/')) ?>">Try again</a>
    </p>
  </div>
</section>
