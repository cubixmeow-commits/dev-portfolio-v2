<?php use Cadence\Models\ActivityEvent; ?>
<div class="container profile-wrap">
  <header class="profile-head card card-pad">
    <?php Cadence\Core\View::partial('layout/avatar', ['user' => $profile, 'size' => 72, 'ring' => $ring]); ?>
    <div class="profile-id">
      <h1><?= e($profile['display_name']) ?></h1>
      <p class="muted">@<?= e($profile['handle']) ?></p>
      <?php if (!empty($profile['bio'])): ?>
        <p class="profile-bio"><?= e($profile['bio']) ?></p>
      <?php endif; ?>
      <p class="muted small">Member since <?= e(date('M Y', strtotime((string) $profile['created_at']))) ?></p>
    </div>
    <?php if ($isSelf): ?>
      <a class="btn btn-secondary" href="<?= e(url('/settings')) ?>">Edit profile</a>
    <?php endif; ?>
  </header>

  <div class="profile-stats">
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($profile['total_points']) ?></span>
      <span class="stat-label">total points</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($stats['longest_streak']) ?></span>
      <span class="stat-label">longest streak</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($totalCheckins) ?></span>
      <span class="stat-label">check-ins</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($stats['challenges_joined']) ?></span>
      <span class="stat-label">challenges</span>
    </div>
  </div>

  <div class="profile-columns">
    <div class="profile-main">
      <section class="card card-pad">
        <h2 class="section-title">Current challenges</h2>
        <?php if ($activeChallenges === []): ?>
          <p class="muted small">Not in any active challenges right now.</p>
        <?php else: ?>
          <ul class="profile-challenge-list">
            <?php foreach ($activeChallenges as $c): ?>
              <li class="profile-challenge-row">
                <span class="cover-dot cover-<?= e($c['cover_style']) ?>" aria-hidden="true"></span>
                <a class="profile-challenge-title" href="<?= e(url('/challenges/' . $c['slug'])) ?>"><?= e($c['title']) ?></a>
                <span class="muted small num"><?= e((string) $c['current_streak']) ?> day streak</span>
                <span class="muted small num"><?= fmt_int($c['points']) ?> pts</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <section class="card card-pad">
        <h2 class="section-title">Recent activity</h2>
        <?php if ($events === []): ?>
          <p class="muted small">No activity yet.</p>
        <?php else: ?>
          <ul class="mini-feed">
            <?php foreach ($events as $ev): ?>
              <li class="mini-feed-row">
                <div>
                  <span class="small"><?= ActivityEvent::sentence($ev) ?></span>
                  <span class="muted small" style="display:block"><?= e(time_ago((string) $ev['created_at'])) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    </div>

    <aside class="profile-side">
      <section class="card card-pad" id="badges">
        <h2 class="section-title">Badge case</h2>
        <?php if ($badges === []): ?>
          <p class="muted small">No badges yet. The first check-in earns one.</p>
        <?php else: ?>
          <ul class="badge-grid">
            <?php foreach ($badges as $b): ?>
              <li class="badge-tile" title="<?= e($b['description']) ?>">
                <span class="badge-icon badge-icon-<?= e($b['icon']) ?>" aria-hidden="true"></span>
                <span class="badge-name"><?= e($b['name']) ?></span>
                <span class="muted small"><?= e(date('M j', strtotime((string) $b['earned_at']))) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    </aside>
  </div>
</div>
