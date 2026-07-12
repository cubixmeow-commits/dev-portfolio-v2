<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'napping']); ?>
    <h1>Your session dozed off</h1>
    <p>The form took too long to submit and its security token expired. Nothing was lost on our side; go back, refresh the page, and try again.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url($auth ? '/kitchen' : '/login')) ?>">Continue</a>
    </p>
  </div>
</section>
