<div class="container" style="max-width: 640px">
  <div class="page-head">
    <div>
      <h1>Notifications</h1>
      <p>Badges, milestones, and challenge news.</p>
    </div>
  </div>

  <?php if ($items === []): ?>
    <div class="card">
      <div class="empty">
        <h3>All quiet</h3>
        <p>Notifications land here when you earn badges, hit milestones, or a challenge changes state.</p>
      </div>
    </div>
  <?php else: ?>
    <ul class="notif-list card">
      <?php foreach ($items as $n): ?>
        <li class="notif-row<?= $n['read_at'] === null ? ' notif-unread' : '' ?>">
          <span class="notif-dot" aria-hidden="true"></span>
          <div class="notif-body">
            <?php if (!empty($n['link'])): ?>
              <a class="notif-title" href="<?= e($n['link']) ?>"><?= e($n['title']) ?></a>
            <?php else: ?>
              <span class="notif-title"><?= e($n['title']) ?></span>
            <?php endif; ?>
            <?php if ($n['body'] !== ''): ?>
              <p class="muted small"><?= e($n['body']) ?></p>
            <?php endif; ?>
            <span class="muted small"><?= e(time_ago((string) $n['created_at'])) ?></span>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
