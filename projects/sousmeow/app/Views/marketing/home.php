<?php
use SousMeow\Core\Csrf;

/**
 * @var array<string, mixed>|null  $featured
 * @var list<array<string, mixed>> $featuredRecipes
 */
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
        SousMeow walks you through proven workflows, one Recipe at a time. You copy a carefully built prompt,
        run it in the AI you already use, paste the answer back, and approve it against real quality checks.
        At the end: a finished, publish-ready Project Kit.
      </p>
      <div class="hero-actions">
        <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
          <?= $auth ? 'Open my Kitchen' : 'Start cooking free' ?>
        </a>
        <a class="button button-ghost button-large" href="<?= e(url('/marketplace')) ?>">Browse Cookbooks</a>
      </div>
      <p class="hero-footnote">Bring your own AI (Claude, ChatGPT, Gemini, anything). SousMeow never calls one for you, and you can tour the whole product with built-in sample responses.</p>
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
          <p>Answer a short set of questions about your project, once. These facts feed every prompt.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">2</span>
          <h3>Copy the prompt</h3>
          <p>Each Recipe builds a precise prompt from your Pantry, with your ingredients highlighted.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">3</span>
          <h3>Cook in your AI</h3>
          <p>Run it in the assistant you already pay for and trust. Your data goes where you put it, nowhere else.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">4</span>
          <h3>Paste and review</h3>
          <p>Paste the answer back. Walk the Recipe's Quality Checks: your judgement, recorded, never automated.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-active">5</span>
          <h3>Approve or revise</h3>
          <p>Approve to lock it in and unlock the next Recipe, or revise: every version is kept, nothing is lost.</p>
        </li>
        <li class="loop-step card card-hover">
          <span class="step-dot is-done">&check;</span>
          <h3>Export the kit</h3>
          <p>Finish every Recipe and export a Project Kit: clean Markdown files plus a manifest, ready to publish.</p>
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
        <p><?= e($featured['outcome']) ?>. For <?= e(strtolower((string) $featured['audience'])) ?>, in about <?= (int) $featured['est_minutes'] ?> minutes.</p>
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
          <p>No API keys, no markup on tokens, no surprise bills. You run prompts in the assistant you already use, and the quality of your model is yours to choose.</p>
        </div>
        <div class="honesty-item">
          <h3>It never grades your work</h3>
          <p>Quality Checks are questions only you can answer ("does this sound like us?"). SousMeow records your judgement; it does not fake having one.</p>
        </div>
        <div class="honesty-item">
          <h3>It never loses a version</h3>
          <p>Raw responses are immutable. Every edit and re-paste stacks as a new version, so the exact text your AI produced is always one click away.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="final-cta">
    <div class="final-cta-inner card card-pad">
      <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
      <h2>Ten minutes to your first kit</h2>
      <p class="section-sub">The Launch Day Kit is free and includes sample responses, so you can finish a whole Cookbook before deciding if the loop is for you.</p>
      <a class="button button-primary button-large" href="<?= e(url($auth ? '/kitchen' : '/register')) ?>">
        <?= $auth ? 'Open my Kitchen' : 'Start cooking free' ?>
      </a>
    </div>
  </section>
</div>
