<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'spill']); ?>
    <h1>Something went wrong</h1>
    <p>An unexpected error occurred. Your saved project data is safe. Try again in a minute.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/')) ?>">Try again</a>
    </p>
  </div>
</section>
