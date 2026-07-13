<section class="error-page">
  <div class="error-card">
    <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'guarding']); ?>
    <h1>Access denied</h1>
    <p>You do not have permission to view this project.</p>
    <p class="error-actions">
      <a class="button button-primary" href="<?= e(url('/kitchen')) ?>">Back to my projects</a>
    </p>
  </div>
</section>
