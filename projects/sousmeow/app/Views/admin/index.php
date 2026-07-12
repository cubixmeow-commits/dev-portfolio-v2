<?php
/**
 * @var array{users:int, projects:int, completed:int, approved:int, exports:int} $stats
 * @var list<array<string, mixed>> $recentUsers
 * @var list<array<string, mixed>> $recentProjects
 */
?>
<div class="page admin-page">
  <header class="admin-header">
    <h1>Admin overview</h1>
    <p class="section-sub">A read-only view of kitchen activity. Admin accounts are created only by
       <code>php scripts/seed.php</code>; there is no web installer and no user-facing path to this role.</p>
  </header>

  <section class="stat-grid">
    <div class="stat-card card card-pad">
      <span class="stat-value"><?= e(number_format($stats['users'])) ?></span>
      <span class="stat-label">Accounts</span>
    </div>
    <div class="stat-card card card-pad">
      <span class="stat-value"><?= e(number_format($stats['projects'])) ?></span>
      <span class="stat-label">Projects started</span>
    </div>
    <div class="stat-card card card-pad">
      <span class="stat-value"><?= e(number_format($stats['completed'])) ?></span>
      <span class="stat-label">Cookbooks completed</span>
    </div>
    <div class="stat-card card card-pad">
      <span class="stat-value"><?= e(number_format($stats['approved'])) ?></span>
      <span class="stat-label">Artifacts approved</span>
    </div>
    <div class="stat-card card card-pad">
      <span class="stat-value"><?= e(number_format($stats['exports'])) ?></span>
      <span class="stat-label">Kits exported</span>
    </div>
  </section>

  <div class="admin-columns">
    <section class="card card-pad">
      <div class="section-heading">
        <h2>Recent projects</h2>
      </div>
      <?php if ($recentProjects === []): ?>
        <div class="empty-state">
          <h3>No projects yet</h3>
          <p>When someone starts a Cookbook, it appears here with its progress. Quiet kitchens are honest kitchens.</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr><th>Project</th><th>Chef</th><th>Cookbook</th><th>Progress</th><th>Started</th></tr>
            </thead>
            <tbody>
              <?php foreach ($recentProjects as $p): ?>
                <tr>
                  <td><?= e($p['title']) ?></td>
                  <td><?= e($p['user_name']) ?></td>
                  <td><?= e($p['cookbook_title']) ?></td>
                  <td>
                    <?php if ($p['completed_at'] !== null): ?>
                      <span class="badge badge-sage">Complete</span>
                    <?php else: ?>
                      <?= (int) $p['approved_count'] ?>/<?= (int) $p['recipe_count'] ?>
                    <?php endif; ?>
                  </td>
                  <td><?= e(time_ago((string) $p['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <section class="card card-pad">
      <div class="section-heading">
        <h2>Newest accounts</h2>
      </div>
      <?php if ($recentUsers === []): ?>
        <div class="empty-state">
          <h3>No accounts yet</h3>
          <p>Sign-ups appear here newest first.</p>
        </div>
      <?php else: ?>
        <ul class="admin-user-list">
          <?php foreach ($recentUsers as $user): ?>
            <li class="admin-user">
              <span class="admin-user-name">
                <?= e($user['name']) ?>
                <?php if ($user['role'] === 'admin'): ?><span class="badge badge-lilac">admin</span><?php endif; ?>
              </span>
              <span class="admin-user-meta"><?= e($user['email']) ?> · <?= e(time_ago((string) $user['created_at'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </div>
</div>
