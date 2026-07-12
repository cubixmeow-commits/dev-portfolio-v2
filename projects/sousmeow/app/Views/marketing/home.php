<?php
use SousMeow\Core\Csrf;
use SousMeow\Services\SiteStats;

/**
 * @var array<string, mixed>|null  $featured
 * @var list<array<string, mixed>> $featuredRecipes
 * @var array{chefs:int,kits_today:int,kits_total:int,approved_today:int,rating:float,cookbooks:int,recipes:int,active_today:int} $stats
 * @var list<array<string, mixed>> $popular
 * @var list<array{kind:string,at:string,name:string,cookbook_title?:string,detail?:string}> $activity
 */
$accentClass = static fn(array $c): string => 'accent-' . preg_replace('/[^a-z]/', '', (string) $c['accent']);
$activityBadge = static fn(string $kind): string => match ($kind) {
    'completed' => 'badge-sage', 'cooking' => 'badge-terracotta', 'pantry' => 'badge-amber', 'joined' => 'badge-lilac', default => 'badge-neutral',
};
$activityLabel = static fn(string $kind): string => match ($kind) {
    'completed' => 'Kit ready', 'cooking' => 'Cooking', 'pantry' => 'Pantry stocked', 'joined' => 'New chef', default => 'Active',
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
      <p class="hero-eyebrow">Your sous-chef for AI work</p>
      <h1>Great AI results are a recipe,<br>not a lucky prompt.</h1>
      <p class="hero-lede">
        SousMeow turns a big writing job into short, guided Recipes. Copy a ready-made prompt, run it in
        ChatGPT or Claude, paste the answer back, and approve what earns its place. By the last Recipe,
        you are holding a finished Project Kit, ready to publish.
      </p>

      <section class="kitchen-dashboard rise-in" aria-labelledby="dashboard-heading">
        <div class="dashboard-head">
          <div class="dashboard-head-main">
            <p class="dashboard-eyebrow"><span class="live-dot" aria-hidden="true"></span> Live from the kitchen</p>
            <h2 id="dashboard-heading" class="dashboard-title">The stove is busy today</h2>
          </div>
          <p class="dashboard-honesty">Portfolio demo · simulated activity · Pacific time</p>
        </div>

        <div class="dashboard-stats" role="list">
          <div class="dash-stat dash-stat-hero" role="listitem">
            <span class="dash-stat-value"><?= e((string) $stats['kits_today']) ?></span>
            <span class="dash-stat-label">Kits packed today</span>
          </div>
          <div class="dash-stat" role="listitem">
            <span class="dash-stat-value"><?= e(SiteStats::formatCompact($stats['chefs'])) ?></span>
            <span class="dash-stat-label">Chefs</span>
          </div>
          <div class="dash-stat" role="listitem">
            <span class="dash-stat-value"><?= e((string) $stats['active_today']) ?></span>
            <span class="dash-stat-label">Active today</span>
          </div>
          <div class="dash-stat" role="listitem">
            <span class="dash-stat-value"><?= e(number_format($stats['rating'], 1)) ?><span class="dash-star" aria-hidden="true">&#9733;</span></span>
            <span class="dash-stat-label">Satisfaction</span>
          </div>
          <div class="dash-stat dash-stat-muted" role="listitem">
            <span class="dash-stat-value"><?= e(SiteStats::formatCompact($stats['kits_total'])) ?></span>
            <span class="dash-stat-label">All-time kits</span>
          </div>
          <div class="dash-stat dash-stat-muted" role="listitem">
            <span class="dash-stat-value"><?= e((string) $stats['approved_today']) ?></span>
            <span class="dash-stat-label">Approved today</span>
          </div>
        </div>

        <div class="dashboard-panels">
          <div class="dashboard-panel dashboard-activity">
            <div class="panel-heading">
              <h3>Kitchen pulse</h3>
              <span class="panel-live">Updating</span>
            </div>
            <?php if ($activity === []): ?>
              <p class="panel-empty">Run <code>php scripts/simulate-day.php</code> to populate activity.</p>
            <?php else: ?>
              <ul class="activity-feed">
                <?php foreach ($activity as $event): $msg = SiteStats::activityMessage($event); ?>
                  <li class="activity-card activity-<?= e($event['kind']) ?>">
                    <div class="activity-card-top">
                      <span class="badge <?= e($activityBadge($event['kind'])) ?>"><?= e($activityLabel($event['kind'])) ?></span>
                      <time class="activity-time" datetime="<?= e($event['at']) ?>"><?= e(time_ago($event['at'])) ?></time>
                    </div>
                    <p class="activity-copy">
                      <?= e($msg['prefix']) ?>
                      <?php if ($msg['emphasis'] !== ''): ?> <strong><?= e($msg['emphasis']) ?></strong><?php endif; ?>
                      <?php if ($msg['suffix'] !== ''): ?><span class="activity-detail"><?= e($msg['suffix']) ?></span><?php endif; ?>
                    </p>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="dashboard-panel dashboard-popular">
            <div class="panel-heading"><h3>Trending today</h3></div>
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
                        <span class="trending-metric"><?= e(plural((int) $cookbook['recipe_count'], 'Recipe')) ?></span>
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

      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
          <?= $auth ? 'Open my Kitchen' : 'Start cooking free' ?>
        </a>
        <a class="button button-ghost button-large" href="<?= e(url('/marketplace')) ?>">Browse Cookbooks</a>
      </div>
      <p class="hero-footnote">Works with the AI you already use: Claude, ChatGPT, Gemini, anything that chats. SousMeow never calls one for you, and built-in sample responses let you tour the whole loop without opening an AI at all.</p>
    </div>
  </section>

  <section class="loop-section" aria-labelledby="loop-heading">
    <div class="loop-inner">
      <h2 id="loop-heading">The loop, in one glance</h2>
      <p class="section-sub loop-sub">Every Cookbook runs the same honest cycle. No magic, no black box, no API key.</p>
      <ol class="loop-steps">
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">1</span>
          <h3>Stock the Pantry</h3>
          <p>Tell SousMeow about your project once. Every prompt is built from those facts and nothing else.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">2</span>
          <h3>Copy the prompt</h3>
          <p>Each Recipe hands you a precise, ready-to-run prompt with your own ingredients highlighted inside it.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">3</span>
          <h3>Cook in your AI</h3>
          <p>Run it in the assistant you already pay for. Your data goes where you put it, nowhere else.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">4</span>
          <h3>Paste and review</h3>
          <p>Paste the answer back and walk the Recipe's Quality Checks. Your judgement, recorded, never automated.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">5</span>
          <h3>Approve or revise</h3>
          <p>Approve to lock it in and unlock the next Recipe. Every version is kept, so revising costs nothing.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-done">&check;</span>
          <h3>Export the kit</h3>
          <p>Finish the last Recipe and download your Project Kit: clean Markdown files plus a manifest, ready to publish.</p>
        </li>
      </ol>
    </div>
  </section>

  <?php if ($featured !== null): ?>
  <section class="featured-section" aria-labelledby="featured-heading">
    <div class="featured-inner card">
      <div class="featured-copy">
        <p class="hero-eyebrow">Free starter Cookbook</p>
        <h2 id="featured-heading"><?= e($featured['title']) ?></h2>
        <p class="featured-tagline"><?= e($featured['tagline']) ?></p>
        <p>By the end you have <?= e($featured['outcome']) ?>, exported as one kit and sounding like you on
           your best day. Built for <?= e(strtolower((string) $featured['audience'])) ?>; about
           <?= (int) $featured['est_minutes'] ?> minutes of your attention.</p>
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
              <button type="submit" class="button button-primary button-large">Start this Cookbook</button>
            </form>
          <?php else: ?>
            <a class="button button-primary button-large" href="<?= e(url('/register')) ?>">Create an account and start</a>
          <?php endif; ?>
          <a class="button button-ghost" href="<?= e(url('/cookbooks/' . $featured['slug'])) ?>">See the full Recipe list</a>
        </div>
      </div>
      <div class="featured-art" aria-hidden="true">
        <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cooking']); ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="honesty-section" aria-labelledby="honesty-heading">
    <div class="honesty-inner">
      <h2 id="honesty-heading">What SousMeow deliberately does not do</h2>
      <div class="honesty-grid">
        <div class="honesty-item">
          <h3>It never calls an AI for you</h3>
          <p>No API keys, no markup on tokens, no surprise bills. You run each prompt in the assistant you already use, on the model you already trust.</p>
        </div>
        <div class="honesty-item">
          <h3>It never grades your work</h3>
          <p>Quality Checks are questions only you can answer, like "does this sound like us?" SousMeow records your judgement; it does not fake having one.</p>
        </div>
        <div class="honesty-item">
          <h3>It never loses a version</h3>
          <p>Pasted responses are kept exactly as pasted, forever. Edits stack on top as new versions, so the original is always one click away.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="final-cta">
    <div class="final-cta-inner card card-pad">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Ten minutes to your first kit</h2>
      <p class="section-sub">The Launch Day Kit is free, and every Recipe includes a sample response. Finish an entire Cookbook first; decide if the loop is for you after.</p>
      <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
        <?= $auth ? 'Open my Kitchen' : 'Start cooking free' ?>
      </a>
    </div>
  </section>
</div>
