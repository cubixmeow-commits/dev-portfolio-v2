<?php /* Stage 0 placeholder. Stage 7 replaces this with live-data hero strips and the feed preview. */ ?>
<section class="container" style="padding-top: var(--sp-9); text-align: center; max-width: 720px">
  <h1 style="font-size: var(--text-3xl)">Build habits together.</h1>
  <p class="muted" style="font-size: var(--text-lg); margin-top: var(--sp-4)">
    Join time-boxed challenges, log daily check-ins, grow streaks, and climb the leaderboard with people doing the same.
  </p>
  <div style="display: flex; gap: var(--sp-3); justify-content: center; margin-top: var(--sp-6)">
    <a class="btn btn-primary btn-lg" href="<?= e(url('/register')) ?>">Create your account</a>
    <a class="btn btn-secondary btn-lg" href="<?= e(url('/challenges')) ?>">Explore challenges</a>
  </div>
</section>
