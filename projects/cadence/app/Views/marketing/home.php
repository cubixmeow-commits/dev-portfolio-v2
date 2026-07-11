<section class="hero">
  <div class="container hero-inner">
    <h1 class="hero-title">Build habits together.</h1>
    <p class="hero-sub">
      Join time-boxed challenges, log a daily check-in, grow a streak, and climb the
      leaderboard with a few hundred people doing the same thing.
    </p>
    <div class="hero-actions">
      <a class="btn btn-primary btn-lg" href="<?= e(url('/register')) ?>">Create your account</a>
      <a class="btn btn-secondary btn-lg" href="<?= e(url('/challenges')) ?>">Explore challenges</a>
    </div>
  </div>
</section>

<section class="container proof-strips" aria-label="Live platform numbers">
  <div class="card card-pad proof">
    <span class="proof-value num"><?= fmt_int($weekCheckins) ?></span>
    <span class="proof-label">check-ins this week</span>
  </div>
  <div class="card card-pad proof">
    <?php if ($longestStreak !== null): ?>
      <span class="proof-value num"><?= fmt_int($longestStreak['current_streak']) ?> days</span>
      <span class="proof-label">longest active streak, held by
        <a href="<?= e(url('/u/' . $longestStreak['handle'])) ?>"><?= e($longestStreak['display_name']) ?></a>
      </span>
    <?php else: ?>
      <span class="proof-value num">0 days</span>
      <span class="proof-label">longest active streak</span>
    <?php endif; ?>
  </div>
  <div class="card card-pad proof">
    <?php if ($topChallenge !== null): ?>
      <span class="proof-value num"><?= fmt_int($topChallenge['participant_count']) ?></span>
      <span class="proof-label">people in
        <a href="<?= e(url('/challenges/' . $topChallenge['slug'])) ?>"><?= e($topChallenge['title']) ?></a>
      </span>
    <?php else: ?>
      <span class="proof-value num">6</span>
      <span class="proof-label">challenge categories to pick from</span>
    <?php endif; ?>
  </div>
</section>

<section class="container home-feed">
  <div class="home-feed-head">
    <h2>Happening right now</h2>
    <a class="small" href="<?= e(url('/feed')) ?>">See the full feed</a>
  </div>
  <?php if ($events === []): ?>
    <div class="card">
      <div class="empty">
        <h3>The feed is warming up</h3>
        <p>Activity appears here as members join challenges and check in.</p>
      </div>
    </div>
  <?php else: ?>
    <ul class="feed-list card">
      <?php foreach ($events as $event): ?>
        <?php Cadence\Core\View::partial('feed/event-row', ['event' => $event, 'rings' => $rings]); ?>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<section class="container how-it-works">
  <h2 style="text-align: center">How it works</h2>
  <div class="how-grid">
    <div class="how-step">
      <span class="how-num num">1</span>
      <h3>Pick a challenge</h3>
      <p class="muted small">Fitness, mindfulness, learning, and more. Each one is time-boxed with a clear daily action.</p>
    </div>
    <div class="how-step">
      <span class="how-num num">2</span>
      <h3>Check in daily</h3>
      <p class="muted small">One tap a day. Your streak ring fills as you go, and milestones earn bonus points.</p>
    </div>
    <div class="how-step">
      <span class="how-num num">3</span>
      <h3>Keep each other honest</h3>
      <p class="muted small">A live feed, leaderboards, and badges make consistency feel like a team sport.</p>
    </div>
  </div>
  <div style="text-align: center; margin-top: var(--sp-6)">
    <a class="btn btn-primary btn-lg" href="<?= e(url('/register')) ?>">Start your first streak</a>
  </div>
</section>
