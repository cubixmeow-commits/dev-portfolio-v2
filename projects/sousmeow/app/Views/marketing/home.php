<?php
use SousMeow\Core\Csrf;
use SousMeow\Services\SiteStats;
use SousMeow\Services\Simulation;

/**
 * @var array<string, mixed>|null  $featured
 * @var list<array<string, mixed>> $featuredRecipes
 * @var array{chefs:int,kits_today:int,kits_total:int,approved_today:int,rating:float,cookbooks:int,recipes:int,active_today:int} $stats
 * @var array{weeks:list<list<array{date:string,count:int,level:int}|null>>,max:int,total:int,end:string,month_labels:list<array{label:string,col:int}>} $heatmap
 * @var list<array<string, mixed>> $popular
 * @var list<array{kind:string,at:string,name:string,cookbook_title?:string,detail?:string}> $activity
 */
$accentClass = static fn(array $c): string => 'accent-' . preg_replace('/[^a-z]/', '', (string) $c['accent']);
$activityBadge = static fn(string $kind): string => match ($kind) {
    'completed' => 'badge-sage', 'cooking' => 'badge-terracotta', 'pantry' => 'badge-amber', 'joined' => 'badge-lilac', default => 'badge-neutral',
};
$activityLabel = static fn(string $kind): string => match ($kind) {
    'completed' => 'Project complete', 'cooking' => 'In progress', 'pantry' => 'Details added', 'joined' => 'New member', default => 'Active',
};
?>
<div class="marketing-home">

  <section class="hero">
    <div class="hero-wash" aria-hidden="true">
      <span class="wash-blob wash-peach" style="width: 22rem; height: 22rem; top: -6rem; right: -5rem;"></span>
      <span class="wash-blob wash-sage" style="width: 18rem; height: 18rem; bottom: -7rem; left: -6rem;"></span>
      <span class="wash-blob wash-butter" style="width: 12rem; height: 12rem; top: 40%; left: 12%;"></span>
    </div>
    <div class="hero-inner">
      <p class="hero-eyebrow">Guided AI workflows · no API required</p>
      <h1>Build complete projects using the AI subscriptions you already have.</h1>
      <p class="hero-lede">
        SousMeow provides guided workflows for complex projects. Each step prepares a prompt for you to run in
        ChatGPT, Claude, Gemini, or another assistant. Bring the responses back, review them, approve them,
        and export finished project files.
      </p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
          <?= $auth ? 'Open my projects' : 'Start free' ?>
        </a>
        <a class="button button-ghost button-large" href="<?= e(url('/marketplace')) ?>">Explore workflows</a>
      </div>
      <p class="terms-callout">
        In SousMeow, workflows are called <strong>Cookbooks</strong>, each step is a <strong>Recipe</strong>,
        your project details live in the <strong>Pantry</strong>, and finished files export as a <strong>Project Kit</strong>.
      </p>
      <p class="hero-footnote">
        Works with the AI you already use. SousMeow never calls one for you — no API keys, no token markup.
        Built-in sample responses let you tour the full loop without opening an AI at all.
      </p>
    </div>
  </section>

  <section class="loop-section" aria-labelledby="loop-heading">
    <div class="loop-inner">
      <h2 id="loop-heading">How it works</h2>
      <p class="section-sub loop-sub">Four stages. Same honest cycle for every workflow. No black box, no surprise bills.</p>
      <ol class="loop-steps loop-steps-four">
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">1</span>
          <h3>Choose a workflow</h3>
          <p class="step-plain">Pick a guided workflow from the Marketplace — we call these Cookbooks.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">2</span>
          <h3>Add project details</h3>
          <p class="step-plain">Tell SousMeow about your project once. Every prompt is built from those facts — your Pantry.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">3</span>
          <h3>Run each prompt</h3>
          <p class="step-plain">Copy the ready-made prompt, run it in your own AI, and paste the response back for review.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-done">&check;</span>
          <h3>Review and export</h3>
          <p class="step-plain">Approve each step, then download your Project Kit — clean files ready to publish.</p>
        </li>
      </ol>
    </div>
  </section>

  <?php if ($featured !== null): ?>
  <section class="featured-section" aria-labelledby="featured-heading">
    <div class="featured-inner card">
      <div class="featured-copy">
        <p class="hero-eyebrow">Featured workflow</p>
        <h2 id="featured-heading"><?= e($featured['title']) ?></h2>
        <p class="featured-tagline"><?= e($featured['tagline']) ?></p>
        <p>A guided Cookbook — <?= e($featured['outcome']) ?>. Built for <?= e(strtolower((string) $featured['audience'])) ?>;
           about <?= (int) $featured['est_minutes'] ?> minutes of your attention. Free to start.</p>
        <h3 class="featured-steps-heading">Example steps</h3>
        <ul class="featured-recipes">
          <?php foreach ($featuredRecipes as $recipe): ?>
            <li><span class="step-dot"><?= (int) $recipe['position'] ?></span> <strong><?= e($recipe['title']) ?></strong> <span class="featured-recipe-sub"><?= e($recipe['summary']) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <div class="hero-actions">
          <?php if ($auth): ?>
            <form method="post" action="<?= e(url('/projects')) ?>" data-loading>
              <?= Csrf::field() ?>
              <input type="hidden" name="cookbook" value="<?= e($featured['slug']) ?>">
              <button type="submit" class="button button-primary button-large">Start this workflow</button>
            </form>
          <?php else: ?>
            <a class="button button-primary button-large" href="<?= e(url('/register')) ?>">Create a free account</a>
          <?php endif; ?>
          <a class="button button-ghost" href="<?= e(url('/cookbooks/' . $featured['slug'])) ?>">View all steps</a>
        </div>
      </div>
      <div class="featured-art" aria-hidden="true">
        <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="marketplace-teaser" aria-labelledby="marketplace-teaser-heading">
    <div class="marketplace-teaser-inner card card-pad">
      <div>
        <h2 id="marketplace-teaser-heading">Explore workflows</h2>
        <p class="section-sub">Five guided Cookbooks cover launch campaigns, SaaS validation, portfolio building, YouTube planning, and novel development. Three are fully available today.</p>
      </div>
      <a class="button button-primary button-large" href="<?= e(url('/marketplace')) ?>">Browse the Marketplace</a>
    </div>
  </section>

  <section class="community-section" aria-labelledby="community-heading">
    <div class="community-inner">
      <header class="community-header">
        <h2 id="community-heading">Community activity</h2>
        <p class="section-sub">Live metrics from the portfolio demonstration. Simulated creators, real workflow structure.</p>
      </header>

      <section class="kitchen-dashboard rise-in" aria-labelledby="dashboard-heading">
        <div class="dashboard-head">
          <div class="dashboard-head-main">
            <p class="dashboard-eyebrow"><span class="live-dot" aria-hidden="true"></span> Portfolio demo</p>
            <h3 id="dashboard-heading" class="dashboard-title">Workflow activity today</h3>
            <p class="dashboard-theme-line">Fresh from the kitchen.</p>
          </div>
          <p class="dashboard-honesty">Simulated activity · Pacific time</p>
        </div>

        <div class="insights-hero" aria-labelledby="insights-heading">
          <div class="insights-hero-glow" aria-hidden="true"></div>
          <div class="insights-top">
            <div class="insights-primary">
              <p id="insights-heading" class="insights-eyebrow">Today</p>
              <div class="insights-primary-value" aria-label="<?= e((string) $stats['kits_today']) ?> projects completed today">
                <span class="insights-number"><?= e((string) $stats['kits_today']) ?></span>
                <span class="insights-primary-label">projects completed</span>
              </div>
              <p class="insights-primary-meta">
                <span class="insights-pill"><?= e((string) $stats['approved_today']) ?> steps approved</span>
                <span class="insights-pill"><?= e((string) $stats['active_today']) ?> creators active</span>
              </p>
            </div>

            <div class="insights-kpis" role="list">
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e(SiteStats::formatCompact($stats['chefs'])) ?></span>
                <span class="insight-kpi-label">Creators</span>
                <span class="insight-kpi-bar" style="--fill: <?= min(100, (int) round(($stats['chefs'] / Simulation::POOL_SIZE) * 100)) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e(SiteStats::formatCompact($stats['kits_total'])) ?></span>
                <span class="insight-kpi-label">All-time projects</span>
                <span class="insight-kpi-bar insight-kpi-bar-sage" style="--fill: <?= min(100, max(8, (int) round(($stats['kits_total'] / max($stats['chefs'], 1)) * 100))) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e(number_format($stats['rating'], 1)) ?><span class="insight-star" aria-hidden="true">&#9733;</span></span>
                <span class="insight-kpi-label">Satisfaction</span>
                <span class="insight-kpi-bar insight-kpi-bar-amber" style="--fill: <?= min(100, (int) round(($stats['rating'] / 5) * 100)) ?>%"></span>
              </div>
              <div class="insight-kpi" role="listitem">
                <span class="insight-kpi-value"><?= e((string) $stats['cookbooks']) ?></span>
                <span class="insight-kpi-label">Workflows</span>
                <span class="insight-kpi-bar insight-kpi-bar-teal" style="--fill: <?= min(100, $stats['cookbooks'] * 12) ?>%"></span>
              </div>
            </div>
          </div>

          <div class="insights-heatmap">
            <div class="heatmap-head">
              <div>
                <h4 class="heatmap-title">Workflow activity</h4>
                <p class="heatmap-sub"><?= e(SiteStats::formatCompact($heatmap['total'])) ?> actions across <?= count($heatmap['weeks']) ?> weeks · Pacific time</p>
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
              <div class="heatmap-chart" role="img" aria-label="Workflow activity heatmap for the last <?= count($heatmap['weeks']) ?> weeks">
                <div class="heatmap-weeks">
                  <?php foreach ($heatmap['weeks'] as $week): ?>
                    <div class="heatmap-week">
                      <?php foreach ($week as $cell): ?>
                        <?php if ($cell === null): ?>
                          <span class="heatmap-cell heatmap-empty" aria-hidden="true"></span>
                        <?php else: ?>
                          <span
                            class="heatmap-cell heatmap-level-<?= (int) $cell['level'] ?>"
                            title="<?= e($cell['date'] . ': ' . $cell['count'] . ' actions') ?>"
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

        <div class="dashboard-panels">
          <div class="dashboard-panel dashboard-activity">
            <div class="panel-heading">
              <div>
                <h3>Recent activity</h3>
                <p class="panel-theme-line">The kitchen pulse.</p>
              </div>
              <span class="panel-live">Live</span>
            </div>
            <?php if ($activity === []): ?>
              <p class="panel-empty">Activity feed populates when the portfolio simulation runs.</p>
            <?php else: ?>
              <ul class="activity-feed">
                <?php foreach ($activity as $event): $msg = SiteStats::activityMessage($event); ?>
                  <li class="activity-card activity-<?= e($event['kind']) ?>">
                    <span class="activity-avatar" style="--avatar-hue: <?= e((string) SiteStats::avatarHue($event['name'])) ?>" aria-hidden="true"><?= e(SiteStats::initials($event['name'])) ?></span>
                    <div class="activity-body">
                      <div class="activity-card-top">
                        <span class="badge <?= e($activityBadge($event['kind'])) ?>"><?= e($activityLabel($event['kind'])) ?></span>
                        <time class="activity-time" datetime="<?= e($event['at']) ?>"><?= e(time_ago($event['at'])) ?></time>
                      </div>
                      <p class="activity-copy">
                        <?= e($msg['prefix']) ?>
                        <?php if ($msg['emphasis'] !== ''): ?> <strong><?= e($msg['emphasis']) ?></strong><?php endif; ?>
                        <?php if ($msg['suffix'] !== ''): ?><span class="activity-detail"><?= e($msg['suffix']) ?></span><?php endif; ?>
                      </p>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="dashboard-panel dashboard-popular">
            <div class="panel-heading">
              <div>
                <h3>Popular workflows</h3>
                <p class="panel-theme-line">Trending Cookbooks today.</p>
              </div>
            </div>
            <ol class="trending-list">
              <?php $rank = 1; foreach ($popular as $cookbook):
                $today = (int) ($cookbook['activity_today'] ?? 0);
                $rating = $cookbook['demo_avg_rating'] ?? null;
              ?>
                <li>
                  <a class="trending-card <?= e($accentClass($cookbook)) ?>" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">
                    <span class="trending-rank" aria-hidden="true"><?= $rank++ ?></span>
                    <span class="trending-body">
                      <span class="trending-title"><?= e($cookbook['title']) ?></span>
                      <span class="trending-metrics">
                        <span class="trending-metric"><?= e((string) $today) ?> active</span>
                        <?php if ($rating !== null): ?>
                          <span class="trending-metric trending-metric-star"><?= e(number_format((float) $rating, 1)) ?> &#9733;</span>
                        <?php endif; ?>
                        <span class="trending-metric"><?= e(plural((int) $cookbook['recipe_count'], 'step')) ?></span>
                      </span>
                    </span>
                    <span class="trending-chevron" aria-hidden="true">&#8250;</span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ol>
          </div>
        </div>
      </section>
    </div>
  </section>

  <section class="honesty-section" aria-labelledby="honesty-heading">
    <div class="honesty-inner">
      <h2 id="honesty-heading">Why SousMeow is different</h2>
      <div class="honesty-grid">
        <div class="honesty-item">
          <h3>It never calls an AI for you</h3>
          <p>No API keys, no markup on tokens, no surprise bills. You run each prompt in ChatGPT, Claude, Gemini, or whatever you already pay for.</p>
        </div>
        <div class="honesty-item">
          <h3>It never grades your work</h3>
          <p>Quality Checks are questions only you can answer. SousMeow records your judgement — it does not fake having one.</p>
        </div>
        <div class="honesty-item">
          <h3>It never loses a version</h3>
          <p>Pasted responses are kept exactly as pasted. Edits stack as new versions, so the original is always one click away.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="final-cta">
    <div class="final-cta-inner card card-pad">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Try a full workflow in ten minutes</h2>
      <p class="section-sub">The Launch Day Kit is free. Every step includes a sample response, so you can finish an entire workflow before deciding if SousMeow is for you.</p>
      <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
        <?= $auth ? 'Open my projects' : 'Start free' ?>
      </a>
      <p class="final-cta-theme">The kitchen is ready when you are.</p>
    </div>
  </section>
</div>
