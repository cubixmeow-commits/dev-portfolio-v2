<?php
/**
 * Admin analytics. Charts are hand-rolled inline SVG computed right
 * here from the series the controller passed: no chart library, no JS.
 */

/** Build polyline points for a series in a W x H box with padding. */
$linePoints = static function (array $series, int $w, int $h, int $pad): string {
    $max = max(1, max(array_column($series, 'value')));
    $n = count($series);
    $pts = [];
    foreach ($series as $i => $day) {
        $x = $pad + ($w - 2 * $pad) * ($n > 1 ? $i / ($n - 1) : 0);
        $y = $h - $pad - ($h - 2 * $pad) * ($day['value'] / $max);
        $pts[] = round($x, 1) . ',' . round($y, 1);
    }
    return implode(' ', $pts);
};
?>
<div class="container">
  <div class="page-head">
    <div>
      <h1>Admin</h1>
      <p>Live platform analytics, computed from the database on every load.</p>
    </div>
    <a class="btn btn-secondary" href="<?= e(url('/admin/tools')) ?>">Ops tools</a>
  </div>

  <div class="admin-cards">
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($cards['members']) ?></span>
      <span class="stat-label">total members</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($cards['wau']) ?></span>
      <span class="stat-label">weekly active</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= fmt_int($cards['checkins_today']) ?></span>
      <span class="stat-label">check-ins today</span>
    </div>
    <div class="card card-pad stat">
      <span class="stat-value num"><?= e(number_format($cards['avg_streak'], 1)) ?></span>
      <span class="stat-label">avg current streak</span>
    </div>
  </div>

  <div class="admin-charts">
    <section class="card card-pad">
      <h2 class="section-title">Signups, last 90 days</h2>
      <?php $peak = max(1, max(array_column($signups, 'value'))); ?>
      <svg viewBox="0 0 560 140" width="100%" role="img" aria-label="Signups per day over the last 90 days, peaking at <?= e((string) $peak) ?>">
        <line x1="8" y1="132" x2="552" y2="132" stroke="var(--line)" stroke-width="1"></line>
        <polyline fill="none" stroke="var(--accent)" stroke-width="2" stroke-linejoin="round"
                  points="<?= $linePoints($signups, 560, 140, 8) ?>"></polyline>
      </svg>
      <p class="muted small chart-range">
        <span><?= e(date('M j', strtotime($signups[0]['date']))) ?></span>
        <span>peak <span class="num"><?= e((string) $peak) ?></span>/day</span>
        <span>today</span>
      </p>
    </section>

    <section class="card card-pad">
      <h2 class="section-title">Check-ins per day, last 30 days</h2>
      <?php $peak = max(1, max(array_column($checkins, 'value'))); $n = count($checkins); ?>
      <svg viewBox="0 0 560 140" width="100%" role="img" aria-label="Check-ins per day over the last 30 days, peaking at <?= e((string) $peak) ?>">
        <line x1="8" y1="132" x2="552" y2="132" stroke="var(--line)" stroke-width="1"></line>
        <?php foreach ($checkins as $i => $day):
          $bw = (560 - 16) / $n;
          $x = 8 + $i * $bw;
          $bh = round(124 * $day['value'] / $peak, 1);
        ?>
          <rect x="<?= round($x + 1, 1) ?>" y="<?= 132 - $bh ?>" width="<?= round($bw - 2, 1) ?>" height="<?= $bh ?>"
                rx="2" fill="var(--accent)" opacity="<?= $i === $n - 1 ? '1' : '0.55' ?>">
            <title><?= e(date('M j', strtotime($day['date']))) ?>: <?= e((string) $day['value']) ?></title>
          </rect>
        <?php endforeach; ?>
      </svg>
      <p class="muted small chart-range">
        <span><?= e(date('M j', strtotime($checkins[0]['date']))) ?></span>
        <span>peak <span class="num"><?= fmt_int($peak) ?></span>/day</span>
        <span>today</span>
      </p>
    </section>

    <section class="card card-pad">
      <h2 class="section-title">Challenges joined per member</h2>
      <?php $peak = max(1, max(array_map(static fn($r) => (int) $r['members'], $distribution ?: [['members' => 1]]))); ?>
      <div class="dist-rows">
        <?php foreach ($distribution as $row): ?>
          <div class="dist-row">
            <span class="num dist-label"><?= e((string) $row['joined']) ?></span>
            <div class="dist-track">
              <div class="dist-fill" style="width: <?= round(100 * (int) $row['members'] / $peak, 1) ?>%"></div>
            </div>
            <span class="muted small num"><?= fmt_int($row['members']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <div class="admin-tables">
    <section class="card card-pad">
      <h2 class="section-title">Newest members</h2>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Member</th><th>Joined</th><th>Points</th></tr></thead>
          <tbody>
            <?php foreach ($newestUsers as $u): ?>
              <tr>
                <td>
                  <a href="<?= e(url('/u/' . $u['handle'])) ?>"><?= e($u['display_name']) ?></a>
                  <span class="muted small">@<?= e($u['handle']) ?></span>
                </td>
                <td class="muted small"><?= e(time_ago((string) $u['created_at'])) ?></td>
                <td class="num"><?= fmt_int($u['total_points']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="card card-pad">
      <h2 class="section-title">Most active challenges (7 days)</h2>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Challenge</th><th>Members</th><th>7d check-ins</th></tr></thead>
          <tbody>
            <?php foreach ($topChallenges as $c): ?>
              <tr>
                <td><a href="<?= e(url('/challenges/' . $c['slug'])) ?>"><?= e($c['title']) ?></a></td>
                <td class="num"><?= fmt_int($c['participant_count']) ?></td>
                <td class="num"><?= fmt_int($c['week_checkins']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>
