<?php
use SousMeow\Services\HomepageActivityPresenter;
use SousMeow\Services\SiteStats;
use SousMeow\Services\Simulation;

/**
 * @var list<array<string, mixed>> $featuredCookbooks
 * @var array{
 *   metrics: array<string, int|float>,
 *   heatmap: array<string, mixed>,
 *   feed: list<array<string, string>>,
 *   achievements: list<array<string, string>>,
 *   insights: list<array<string, string>>,
 *   popular: list<array<string, mixed>>
 * } $activityBoard
 */
$accentClass = static fn(array $c): string => 'accent-' . preg_replace('/[^a-z]/', '', (string) $c['accent']);
$feedBadge = static fn(string $tone): string => match ($tone) {
    'completed' => 'badge-sage',
    'started'   => 'badge-lilac',
    'milestone' => 'badge-amber',
    'resumed'   => 'badge-terracotta',
    default     => 'badge-neutral',
};
$metrics = $activityBoard['metrics'];
$heatmap = $activityBoard['heatmap'];
$feed = $activityBoard['feed'];
$achievements = $activityBoard['achievements'];
$insights = $activityBoard['insights'];
$popular = $activityBoard['popular'];
$marketplaceUrl = url('/marketplace');
?>
<div class="marketing-home">

  <!-- 1. Hero -->
  <section class="hero">
    <div class="hero-wash" aria-hidden="true">
      <span class="wash-blob wash-peach" style="width: 22rem; height: 22rem; top: -6rem; right: -5rem;"></span>
      <span class="wash-blob wash-sage" style="width: 18rem; height: 18rem; bottom: -7rem; left: -6rem;"></span>
      <span class="wash-blob wash-butter" style="width: 12rem; height: 12rem; top: 40%; left: 12%;"></span>
    </div>
    <div class="hero-inner">
      <div class="hero-mascot" aria-hidden="true">
        <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
      </div>
      <h1>SousMeow guides you through every AI project, one step at a time.</h1>
      <p class="hero-lede">Make AI easier. Pick a Cookbook, follow one clear step, and finish with work you can actually use.</p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Explore Cookbooks</a>
        <a class="button button-ghost button-large" href="#how-it-works">See how it works</a>
      </div>
    </div>
  </section>

  <!-- 2. How it works -->
  <section class="how-section" id="how-it-works" aria-labelledby="how-heading">
    <div class="how-inner">
      <h2 id="how-heading">How SousMeow works</h2>
      <ol class="how-steps">
        <li class="how-step">
          <div class="how-step-visual" aria-hidden="true">
            <span class="how-icon how-icon-cookbook">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8" y="10" width="32" height="28" rx="4" stroke="currentColor" stroke-width="2"/>
                <path d="M16 10V8a8 8 0 0 1 16 0v2" stroke="currentColor" stroke-width="2"/>
                <path d="M16 22h16M16 28h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <span class="how-illustration how-illustration-cookbook">
              <span class="wash-blob wash-peach" style="width: 5rem; height: 5rem; top: 10%; right: 5%;"></span>
              <span class="wash-blob wash-sage" style="width: 4rem; height: 4rem; bottom: 5%; left: 10%;"></span>
            </span>
          </div>
          <p class="how-copy">Choose a Cookbook.</p>
        </li>
        <li class="how-step">
          <div class="how-step-visual" aria-hidden="true">
            <span class="how-icon how-icon-step">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="12" r="6" stroke="currentColor" stroke-width="2"/>
                <circle cx="24" cy="24" r="6" stroke="currentColor" stroke-width="2"/>
                <circle cx="24" cy="36" r="6" stroke="currentColor" stroke-width="2"/>
                <path d="M24 18v0M24 30v0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <span class="how-illustration how-illustration-step">
              <span class="wash-blob wash-butter" style="width: 5rem; height: 5rem; top: 15%; left: 8%;"></span>
              <span class="wash-blob wash-lilac" style="width: 3.5rem; height: 3.5rem; bottom: 10%; right: 12%;"></span>
            </span>
          </div>
          <p class="how-copy">Follow one guided step at a time.</p>
        </li>
        <li class="how-step">
          <div class="how-step-visual" aria-hidden="true">
            <span class="how-icon how-icon-finish">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 14h28v24H10z" stroke="currentColor" stroke-width="2" rx="3"/>
                <path d="M18 26l4 4 8-10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span class="how-illustration how-illustration-finish">
              <span class="wash-blob wash-sage" style="width: 5rem; height: 5rem; top: 8%; right: 10%;"></span>
              <span class="wash-blob wash-peach" style="width: 4rem; height: 4rem; bottom: 8%; left: 5%;"></span>
            </span>
          </div>
          <p class="how-copy">Finish with organized work you can actually use.</p>
        </li>
      </ol>
    </div>
  </section>

  <!-- 3. Explore Cookbooks -->
  <section class="cookbooks-section" aria-labelledby="cookbooks-heading">
    <div class="cookbooks-inner">
      <h2 id="cookbooks-heading">Explore Cookbooks</h2>
      <div class="featured-cookbook-grid">
        <?php foreach ($featuredCookbooks as $cookbook): ?>
          <article class="featured-cookbook-card card <?= e($accentClass($cookbook)) ?>">
            <h3 class="featured-cookbook-title"><?= e($cookbook['title']) ?></h3>
            <p class="featured-cookbook-build">What you'll build: <?= e($cookbook['outcome']) ?></p>
            <dl class="featured-cookbook-meta">
              <div>
                <dt>Steps</dt>
                <dd><?= e(plural((int) $cookbook['recipe_count'], 'step')) ?></dd>
              </div>
              <div>
                <dt>Time</dt>
                <dd>~<?= (int) $cookbook['est_minutes'] ?> min</dd>
              </div>
              <div>
                <dt>Level</dt>
                <dd><?= e($cookbook['difficulty'] ?? 'Intermediate') ?></dd>
              </div>
            </dl>
            <a class="button button-primary button-block" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">Start</a>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="cookbooks-more">
        <a href="<?= e($marketplaceUrl) ?>">Browse all Cookbooks</a>
      </p>
    </div>
  </section>

  <!-- 4. Why SousMeow -->
  <section class="benefits-section" aria-labelledby="benefits-heading">
    <div class="benefits-inner">
      <h2 id="benefits-heading">Why SousMeow</h2>
      <ul class="benefits-grid">
        <li class="benefit-card">
          <span class="benefit-icon" aria-hidden="true">
            <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="14" width="36" height="28" rx="6" stroke="currentColor" stroke-width="2.5"/>
              <path d="M18 28h20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="4 6"/>
              <circle cx="28" cy="28" r="5" fill="currentColor" opacity="0.2"/>
            </svg>
          </span>
          <p>No blank-chat guessing.</p>
        </li>
        <li class="benefit-card">
          <span class="benefit-icon" aria-hidden="true">
            <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="28" cy="28" r="18" stroke="currentColor" stroke-width="2.5"/>
              <path d="M20 28h16M28 20v16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
          </span>
          <p>Use the AI you already have.</p>
        </li>
        <li class="benefit-card">
          <span class="benefit-icon" aria-hidden="true">
            <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14 38l10-12 8 6 14-18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="42" cy="14" r="6" stroke="currentColor" stroke-width="2.5"/>
            </svg>
          </span>
          <p>Finish projects instead of collecting prompts.</p>
        </li>
      </ul>
    </div>
  </section>

  <!-- 5. Simulation showcase -->
  <section class="showcase-section" aria-labelledby="showcase-heading">
    <div class="showcase-inner">
      <header class="showcase-header">
        <h2 id="showcase-heading">See SousMeow in action</h2>
        <p class="section-sub">A live portfolio demonstration — real guided projects, simulated creator activity.</p>
      </header>

      <section class="kitchen-dashboard rise-in" aria-labelledby="dashboard-heading">
        <div class="dashboard-head">
          <div class="dashboard-head-main">
            <p class="dashboard-eyebrow"><span class="live-dot" aria-hidden="true"></span> Portfolio demo</p>
            <h3 id="dashboard-heading" class="dashboard-title">Project progress today</h3>
          </div>
          <p class="dashboard-honesty">Simulated creator activity</p>
        </div>

        <div class="insights-hero" aria-labelledby="insights-heading">
          <div class="insights-hero-glow" aria-hidden="true"></div>
          <div class="insights-top">
            <div class="insights-primary">
              <p id="insights-heading" class="insights-eyebrow">Today</p>
              <div class="insights-primary-value" aria-label="<?= e((string) $metrics['projects_completed_today']) ?> projects completed today">
                <span class="insights-number"><?= e((string) $metrics['projects_completed_today']) ?></span>
                <span class="insights-primary-label">projects completed</span>
              </div>
              <p class="insights-primary-meta">
                <span class="insights-pill"><?= e((string) $metrics['milestones_today']) ?> milestones reached</span>
                <span class="insights-pill"><?= e((string) $metrics['active_creators_today']) ?> creators active</span>
              </p>
            </div>

            <div class="insights-kpis" role="list">
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e(SiteStats::formatCompact((int) $metrics['creators_total'])) ?></span>
                <span class="insight-kpi-label">Active creators</span>
                <span class="insight-kpi-bar" style="--fill: <?= min(100, (int) round(((int) $metrics['creators_total'] / Simulation::POOL_SIZE) * 100)) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e(SiteStats::formatCompact((int) $metrics['projects_finished_total'])) ?></span>
                <span class="insight-kpi-label">Finished projects</span>
                <span class="insight-kpi-bar insight-kpi-bar-sage" style="--fill: <?= min(100, max(8, (int) round(((int) $metrics['projects_finished_total'] / max((int) $metrics['creators_total'], 1)) * 100))) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e((string) $metrics['workflows_in_progress']) ?></span>
                <span class="insight-kpi-label">In progress now</span>
                <span class="insight-kpi-bar insight-kpi-bar-amber" style="--fill: <?= min(100, max(8, (int) $metrics['workflows_in_progress'])) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e((string) $metrics['projects_completed_week']) ?></span>
                <span class="insight-kpi-label">Finished this week</span>
                <span class="insight-kpi-bar insight-kpi-bar-teal" style="--fill: <?= min(100, max(8, (int) round(((int) $metrics['projects_completed_week'] / max((int) $metrics['projects_finished_total'], 1)) * 100))) ?>%"></span>
              </div>
            </div>
          </div>

          <div class="insights-heatmap">
            <div class="heatmap-head">
              <div>
                <h4 class="heatmap-title"><?= e((string) ($heatmap['label'] ?? 'Project milestones')) ?></h4>
                <p class="heatmap-sub"><?= e((string) ($heatmap['summary'] ?? '')) ?></p>
              </div>
              <div class="heatmap-legend" aria-hidden="true">
                <span>Less</span>
                <?php for ($lvl = 0; $lvl <= 4; $lvl++): ?>
                  <span class="heatmap-cell heatmap-level-<?= $lvl ?>"></span>
                <?php endfor; ?>
                <span>More</span>
              </div>
            </div>
            <div class="heatmap-scroll">
              <div class="heatmap-chart" role="img" aria-label="Project milestone heatmap for the last <?= count($heatmap['weeks']) ?> weeks">
                <div class="heatmap-weeks">
                  <?php foreach ($heatmap['weeks'] as $week): ?>
                    <div class="heatmap-week">
                      <?php foreach ($week as $cell): ?>
                        <?php if ($cell === null): ?>
                          <span class="heatmap-cell heatmap-empty" aria-hidden="true"></span>
                        <?php else: ?>
                          <span
                            class="heatmap-cell heatmap-level-<?= (int) $cell['level'] ?>"
                            title="<?= e($cell['date'] . ': ' . $cell['count'] . ' milestones') ?>"
                          ></span>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="dashboard-boards">
          <div class="dashboard-panel dashboard-activity">
            <div class="panel-heading">
              <div>
                <h3>What people are finishing</h3>
              </div>
              <span class="panel-live">Live demo</span>
            </div>
            <?php if ($feed === []): ?>
              <p class="panel-empty">Activity appears when the portfolio simulation runs.</p>
            <?php else: ?>
              <ul class="activity-feed">
                <?php foreach ($feed as $event): ?>
                  <li class="activity-card activity-<?= e($event['tone']) ?>">
                    <span class="activity-avatar" style="--avatar-hue: <?= e((string) SiteStats::avatarHue($event['name'])) ?>" aria-hidden="true"><?= e(SiteStats::initials($event['name'])) ?></span>
                    <div class="activity-body">
                      <div class="activity-card-top">
                        <span class="badge <?= e($feedBadge($event['tone'])) ?>"><?= e($event['badge']) ?></span>
                        <time class="activity-time" datetime="<?= e($event['at']) ?>"><?= e(time_ago($event['at'])) ?></time>
                      </div>
                      <p class="activity-copy"><?= e($event['message']) ?></p>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="dashboard-panel dashboard-achievements">
            <div class="panel-heading">
              <div>
                <h3>Community achievements</h3>
              </div>
            </div>
            <?php if ($achievements === []): ?>
              <p class="panel-empty">Achievements appear as simulated creators finish projects.</p>
            <?php else: ?>
              <ul class="achievement-list">
                <?php foreach ($achievements as $achievement): ?>
                  <li class="achievement-card">
                    <span class="achievement-mark" aria-hidden="true">&#9733;</span>
                    <div class="achievement-body">
                      <p class="achievement-title"><?= e($achievement['title']) ?></p>
                      <p class="achievement-copy"><?= e($achievement['message']) ?></p>
                      <time class="achievement-time" datetime="<?= e($achievement['at']) ?>"><?= e(time_ago($achievement['at'])) ?></time>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>

        <div class="dashboard-panel dashboard-insights">
          <div class="panel-heading">
            <div>
              <h3>Community insights</h3>
            </div>
          </div>
          <ul class="insight-grid" role="list">
            <?php foreach ($insights as $insight): ?>
              <li class="insight-card" role="listitem">
                <p class="insight-card-label"><?= e($insight['label']) ?></p>
                <p class="insight-card-value"><?= e($insight['value']) ?></p>
                <p class="insight-card-detail"><?= e($insight['detail']) ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="dashboard-panel dashboard-popular">
          <div class="panel-heading">
            <div>
              <h3>Popular workflows</h3>
            </div>
          </div>
          <ol class="trending-list">
            <?php $rank = 1; foreach ($popular as $cookbook): ?>
              <li>
                <a class="trending-card <?= e($accentClass($cookbook)) ?>" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">
                  <span class="trending-rank" aria-hidden="true"><?= $rank++ ?></span>
                  <span class="trending-body">
                    <span class="trending-title"><?= e($cookbook['title']) ?></span>
                    <span class="trending-metrics">
                      <span class="trending-metric"><?= e((string) ((int) ($cookbook['completed_today'] ?? 0))) ?> finished today</span>
                      <span class="trending-metric"><?= e((string) ((int) ($cookbook['in_progress'] ?? 0))) ?> in progress</span>
                      <span class="trending-metric"><?= e((string) ((int) ($cookbook['completion_rate'] ?? 0))) ?>% completion rate</span>
                    </span>
                  </span>
                  <span class="trending-chevron" aria-hidden="true">&#8250;</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      </section>
    </div>
  </section>

  <!-- 6. Final CTA -->
  <section class="final-cta">
    <div class="final-cta-inner card card-pad">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Ready to start?</h2>
      <p class="section-sub">Choose a Cookbook and let SousMeow guide the rest.</p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Explore Cookbooks</a>
      </div>
    </div>
  </section>
</div>
