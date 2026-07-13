<?php
use SousMeow\Core\Csrf;
use SousMeow\Services\SiteStats;
use SousMeow\Services\Simulation;

/**
 * @var array<string, mixed>|null  $featured
 * @var list<array<string, mixed>> $featuredRecipes
 * @var list<array<string, mixed>> $cookbooks
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
$primaryCta = $auth ? url('/kitchen') : url('/marketplace');
$primaryLabel = $auth ? 'Continue my project' : 'Explore workflows';
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
      <p class="hero-eyebrow">Use the AI you already have</p>
      <h1>Finish projects with AI, one step at a time.</h1>
      <p class="hero-lede">
        Instead of staring at a blank chat, follow a guided workflow that shows you exactly what to do next.
        Use ChatGPT, Claude, Gemini, or another AI you already have, and build complete projects from start to finish.
      </p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e($primaryCta) ?>"><?= e($primaryLabel) ?></a>
        <a class="button button-ghost button-large" href="#how-it-works">See how it works</a>
      </div>
      <p class="terms-callout terms-callout-compact">
        In SousMeow, workflows are called <strong>Cookbooks</strong> and their steps are called <strong>Recipes</strong>.
      </p>
    </div>
  </section>

  <!-- 2. Three product pillars -->
  <section class="pillars-section" aria-labelledby="pillars-heading">
    <div class="pillars-inner">
      <h2 id="pillars-heading" class="visually-hidden">Three reasons to use SousMeow</h2>
      <div class="pillars-grid">
        <article class="pillar-card card card-pad">
          <p class="pillar-number" aria-hidden="true">1</p>
          <h3>Guided workflows</h3>
          <p>Complex projects become clear, manageable steps — one goal and one action at a time. No blank page, no giant prompt, no guessing what comes next.</p>
          <p class="pillar-theme">From idea to finished project, step by step.</p>
        </article>
        <article class="pillar-card card card-pad">
          <p class="pillar-number" aria-hidden="true">2</p>
          <h3>Your AI, your subscription</h3>
          <p>Run each prepared prompt in ChatGPT, Claude, Gemini, or another assistant you already pay for. SousMeow provides the process — not another AI bill.</p>
          <p class="pillar-theme">Bring your own AI.</p>
        </article>
        <article class="pillar-card card card-pad">
          <p class="pillar-number" aria-hidden="true">3</p>
          <h3>Reusable processes</h3>
          <p>Discover structured workflows with defined inputs, ordered steps, quality checks, and finished exports — more predictable than isolated prompts.</p>
          <p class="pillar-theme">A marketplace direction for trustworthy workflows.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- 3. The loop -->
  <section class="loop-section" id="how-it-works" aria-labelledby="loop-heading">
    <div class="loop-inner">
      <h2 id="loop-heading">How it works</h2>
      <p class="section-sub loop-sub">Four stages. You stay in control at every step.</p>
      <ol class="loop-steps loop-steps-four">
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">1</span>
          <h3>Choose a workflow</h3>
          <p class="step-plain">Select a guided workflow built for the outcome you want — a Cookbook.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">2</span>
          <h3>Add your project details</h3>
          <p class="step-plain">Provide the information the workflow reuses throughout your project — your Pantry.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">3</span>
          <h3>Run each step in your AI</h3>
          <p class="step-plain">SousMeow prepares the prompt. You run it in your assistant and paste the response back.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-done">&check;</span>
          <h3>Review and finish</h3>
          <p class="step-plain">Check the response, approve it, and continue until your finished project files are ready.</p>
        </li>
      </ol>
    </div>
  </section>

  <!-- 4. Comparison: prompts vs workflows -->
  <section class="compare-section" aria-labelledby="compare-heading">
    <div class="compare-inner">
      <h2 id="compare-heading">A prompt gives you an answer. A workflow gets you to the finish line.</h2>
      <p class="section-sub compare-sub">SousMeow is a process system — not a prompt library.</p>
      <div class="compare-grid">
        <article class="compare-card card card-pad">
          <h3>Individual prompts</h3>
          <ul class="compare-list">
            <li>Solve one moment</li>
            <li>Depend on you knowing what comes next</li>
            <li>Lose context between chats</li>
            <li>Produce inconsistent formats</li>
            <li>Leave results scattered</li>
          </ul>
        </article>
        <article class="compare-card compare-card-highlight card card-pad">
          <h3>SousMeow workflows</h3>
          <ul class="compare-list">
            <li>Guide a complete project</li>
            <li>Run steps in the correct order</li>
            <li>Reuse your project context</li>
            <li>Request predictable output structures</li>
            <li>Apply quality checks you answer</li>
            <li>Organize approved work into finished files</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <!-- 5. Subscription advantage -->
  <section class="subscription-section" aria-labelledby="subscription-heading">
    <div class="subscription-inner card card-pad">
      <h2 id="subscription-heading">The workflow layer for the AI tools you already use</h2>
      <p class="section-sub">You choose the assistant. SousMeow handles structure, memory, review, and export.</p>
      <ol class="flow-steps" aria-label="How SousMeow works with your AI subscription">
        <li><span class="flow-label">SousMeow workflow</span></li>
        <li class="flow-arrow" aria-hidden="true">↓</li>
        <li><span class="flow-label">Prepared prompt</span></li>
        <li class="flow-arrow" aria-hidden="true">↓</li>
        <li><span class="flow-label flow-label-ai">ChatGPT · Claude · Gemini · another AI</span></li>
        <li class="flow-arrow" aria-hidden="true">↓</li>
        <li><span class="flow-label">Response returned to SousMeow</span></li>
        <li class="flow-arrow" aria-hidden="true">↓</li>
        <li><span class="flow-label">Review · approval · next step</span></li>
      </ol>
      <ul class="subscription-points">
        <li>No API key or setup</li>
        <li>No token markup from SousMeow</li>
        <li>Model freedom — pick the best assistant per job</li>
        <li>Your data goes where you run the prompt</li>
      </ul>
      <p class="subscription-note">SousMeow never calls an AI for you. The loop is intentional: you run the prompt, you paste the result, you approve what earns its place.</p>
    </div>
  </section>

  <!-- 6. Trust: definition of done -->
  <section class="trust-section" aria-labelledby="trust-heading">
    <div class="trust-inner">
      <h2 id="trust-heading">Workflows with a definition of done</h2>
      <p class="section-sub trust-sub">Strong workflows do more than generate prompts. Each one can define what you need, what each step produces, and what you verify before moving on.</p>
      <div class="trust-grid">
        <div class="trust-item card card-pad">
          <h3>Required inputs</h3>
          <p>Project details collected once and reused in every prompt — no re-explaining your product each step.</p>
        </div>
        <div class="trust-item card card-pad">
          <h3>Expected outputs</h3>
          <p>Each step can request a predictable response structure, making results easier to review and reuse.</p>
        </div>
        <div class="trust-item card card-pad">
          <h3>Quality checks</h3>
          <p>Questions only you can answer. SousMeow records your judgement — it does not grade your work.</p>
        </div>
        <div class="trust-item card card-pad">
          <h3>Finished export</h3>
          <p>Approved steps assemble into organized project files — a Project Kit ready to publish.</p>
        </div>
      </div>
      <p class="trust-direction">This structure is what makes workflows shareable and trustworthy — the foundation for a future marketplace of reusable processes.</p>
    </div>
  </section>

  <!-- 7. Explore guided workflows -->
  <section class="workflows-section" aria-labelledby="workflows-heading">
    <div class="workflows-inner">
      <header class="workflows-header">
        <h2 id="workflows-heading">Explore guided workflows</h2>
        <p class="section-sub">Complete outcomes, not one-off answers. Every workflow below is a real Cookbook in this build.</p>
      </header>
      <div class="home-workflow-grid">
        <?php foreach ($cookbooks as $cookbook):
          $executable = (int) $cookbook['is_executable'] === 1;
        ?>
          <a class="home-workflow-card card card-hover <?= e($accentClass($cookbook)) ?>" href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>">
            <div class="home-workflow-top">
              <span class="badge badge-outline"><?= e($cookbook['category']) ?></span>
              <?php if ($executable): ?>
                <span class="badge badge-sage badge-dot">Available now</span>
              <?php else: ?>
                <span class="badge badge-neutral">Preview</span>
              <?php endif; ?>
            </div>
            <h3 class="home-workflow-title"><?= e($cookbook['title']) ?></h3>
            <p class="home-workflow-tagline"><?= e($cookbook['tagline']) ?></p>
            <div class="home-workflow-meta">
              <span><?= e(plural((int) $cookbook['recipe_count'], 'step')) ?></span>
              <span>·</span>
              <span>~<?= (int) $cookbook['est_minutes'] ?> min</span>
              <span>·</span>
              <span><?= e(strtolower((string) $cookbook['audience'])) ?></span>
            </div>
            <p class="home-workflow-receive">You receive: <?= e($cookbook['outcome']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="workflows-footer">
        <a class="button button-primary button-large" href="<?= e(url('/marketplace')) ?>">Browse all workflows</a>
        <?php if (!$auth): ?>
          <a class="button button-ghost button-large" href="<?= e(url('/register')) ?>">Create an account</a>
        <?php endif; ?>
      </div>
      <p class="workflows-honesty">Three workflows are fully runnable today. Two are designed previews — every step, input, and quality check is real, but the Runner is not open yet. No purchases or creator payouts in this build.</p>
    </div>
  </section>

  <!-- 8. Community activity dashboard -->
  <section class="community-section" aria-labelledby="community-heading">
    <div class="community-inner">
      <header class="community-header">
        <h2 id="community-heading">Community activity</h2>
        <p class="section-sub">Portfolio demonstration metrics — simulated creators, real workflow structure. Pacific time.</p>
      </header>

      <section class="kitchen-dashboard rise-in" aria-labelledby="dashboard-heading">
        <div class="dashboard-head">
          <div class="dashboard-head-main">
            <p class="dashboard-eyebrow"><span class="live-dot" aria-hidden="true"></span> Portfolio demo</p>
            <h3 id="dashboard-heading" class="dashboard-title">Workflow activity today</h3>
            <p class="dashboard-theme-line">Fresh from the kitchen.</p>
          </div>
          <p class="dashboard-honesty">Simulated activity</p>
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
                <p class="heatmap-sub"><?= e(SiteStats::formatCompact($heatmap['total'])) ?> actions across <?= count($heatmap['weeks']) ?> weeks</p>
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

  <!-- 9. Final CTA -->
  <section class="final-cta">
    <div class="final-cta-inner card card-pad">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Start with a workflow, not a blank page</h2>
      <p class="section-sub">Choose a guided process, use the AI assistant you already have, and work through your project one clear step at a time.</p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e(url('/marketplace')) ?>">Explore workflows</a>
        <?php if (!$auth): ?>
          <a class="button button-ghost button-large" href="<?= e(url('/register')) ?>">Create an account</a>
        <?php endif; ?>
      </div>
      <p class="final-cta-theme">Your first Cookbook is ready when you are.</p>
    </div>
  </section>
</div>
