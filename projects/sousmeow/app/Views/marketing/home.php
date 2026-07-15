<?php
/**
 * Marketing homepage — guided companion positioning.
 *
 * First half uses plain English only. Brand terms (Cookbook) appear
 * only after the experience is explained. Quality Checks are framed as
 * clear human criteria, matching the real Runner (not automated AI grading).
 *
 * @var array<string, mixed>|null       $featured
 * @var list<array<string, mixed>>      $featuredStages
 * @var list<array<string, mixed>>      $featuredRecipes
 * @var list<array<string, mixed>>      $cookbooks
 */

$marketplaceUrl = url('/marketplace');
$productLawUrl = url('/docs/product-law-002');
$startUrl = $marketplaceUrl;
?>
<div class="tw" id="top">

  <!-- 1. HERO -->
  <section class="tw-hero" aria-labelledby="hero-h">
    <div class="tw-wrap">
      <div class="hero-grid">
        <div class="hero-copy">
          <div class="hero-mascot" aria-hidden="true">
            <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'plain']); ?>
          </div>
          <p class="tw-kicker mono">sousmeow</p>
          <h1 id="hero-h">Stop guessing what to ask&nbsp;AI.</h1>
          <p class="hero-lede">SousMeow walks you through complete projects one step at a time.
            It prepares the prompts, explains what matters, checks your progress, and helps you
            get better results from ChatGPT, Claude, Gemini, or the AI tools you already use.</p>
          <p class="hero-philosophy">Not a prompt library. More like sitting next to someone
            who's good at&nbsp;AI.</p>
          <div class="hero-actions">
            <a class="button button-primary button-large" href="<?= e($startUrl) ?>">Start a guided project</a>
            <a class="button button-ghost button-large" href="#how-it-works">See how it works</a>
          </div>

          <aside class="hero-compat" aria-label="AI compatibility">
            <p class="hero-compat-title">
              <span class="hero-compat-check" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                  <path d="M3.2 7.2 5.8 9.8 10.8 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              Works with the AI you already use
            </p>
            <ul class="hero-compat-providers">
              <li>ChatGPT</li>
              <li>Claude</li>
              <li>Gemini</li>
              <li>Grok</li>
              <li class="hero-compat-more-hide">Copilot</li>
              <li class="hero-compat-more-hide">DeepSeek</li>
              <li class="hero-compat-others">and others</li>
            </ul>
            <p class="hero-compat-reassure">
              <span class="hero-compat-badge">Free or paid</span>
              <span>accounts supported · No API keys required</span>
            </p>
            <p class="hero-compat-micro">SousMeow prepares the prompts. You run them in the AI account you already have.</p>
          </aside>

          <p class="hero-law">
            <a href="<?= e($productLawUrl) ?>">Built on Product Law 002: Remove Cognitive Load</a>
          </p>
        </div>

        <figure class="hero-visual hero-loop" data-hero aria-hidden="true">
          <figcaption class="visually-hidden">A simple loop: goal, prepared prompt, your AI, check, finished files.</figcaption>
          <ol class="hl-steps">
            <li class="hl-step" style="--i:0"><span class="hl-no mono">1</span><span>Your goal</span></li>
            <li class="hl-step" style="--i:1"><span class="hl-no mono">2</span><span>Prepared prompt</span></li>
            <li class="hl-step" style="--i:2"><span class="hl-no mono">3</span><span>Your AI</span></li>
            <li class="hl-step" style="--i:3"><span class="hl-no mono">4</span><span>Paste &amp; check</span></li>
            <li class="hl-step hl-done" style="--i:4"><span class="hl-no mono">✓</span><span>Finished files</span></li>
          </ol>
          <p class="hero-annot mono">you stay in the AI you already use</p>
        </figure>
      </div>
    </div>
  </section>

  <!-- 2. SIMPLE GUIDED EXPERIENCE -->
  <section class="tw-sec sec-loop" id="how-it-works" aria-labelledby="loop-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">how it works</p>
        <h2 id="loop-h">One clear loop until the project is done.</h2>
        <p class="sec-sub-tw">You do not need to know how to write prompts.
          SousMeow guides each step. Your AI does the writing.</p>
      </header>

      <ol class="loop-grid" data-reveal>
        <li class="loop-card" style="--i:0">
          <span class="loop-no mono">01</span>
          <h3>Tell SousMeow what you want to create</h3>
          <p>Share a few plain facts about your project.</p>
        </li>
        <li class="loop-card" style="--i:1">
          <span class="loop-no mono">02</span>
          <h3>Follow one clear step</h3>
          <p>Each step explains what you are doing and why it matters.</p>
        </li>
        <li class="loop-card" style="--i:2">
          <span class="loop-no mono">03</span>
          <h3>Copy the prepared prompt</h3>
          <p>Paste it into ChatGPT, Claude, Gemini, or another AI you already use.</p>
        </li>
        <li class="loop-card" style="--i:3">
          <span class="loop-no mono">04</span>
          <h3>Paste the response back</h3>
          <p>SousMeow saves it exactly as you received it.</p>
        </li>
        <li class="loop-card" style="--i:4">
          <span class="loop-no mono">05</span>
          <h3>Check if it is ready</h3>
          <p>Clear criteria help you decide. Improve one thing if needed.</p>
        </li>
        <li class="loop-card" style="--i:5">
          <span class="loop-no mono">06</span>
          <h3>Continue until you finish</h3>
          <p>Approved work carries forward. You leave with finished files.</p>
        </li>
      </ol>
    </div>
  </section>

  <!-- 3. COMPARISON -->
  <section class="tw-sec sec-compare" id="feel" aria-labelledby="feel-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">the difference</p>
        <h2 id="feel-h">We don't expect you to know AI.</h2>
      </header>

      <div class="compare-grid" data-reveal>
        <div class="compare-col compare-before">
          <h3>What most AI tools feel like</h3>
          <ul>
            <li>A blank chat box</li>
            <li>No idea what to ask</li>
            <li>Generic answers</li>
            <li>No explanation</li>
            <li>No way to know if the result is good</li>
            <li>Start over when something goes wrong</li>
          </ul>
        </div>
        <div class="compare-col compare-after">
          <h3>What SousMeow feels like</h3>
          <ul>
            <li>One small step at a time</li>
            <li>The prompt is already prepared</li>
            <li>Each step explains why it matters</li>
            <li>Examples show what good looks like</li>
            <li>Clear checks help you continue</li>
            <li>When answers are weak, you know what to fix</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. EVERY STEP HAS A TEACHER -->
  <section class="tw-sec sec-teacher" id="teacher" aria-labelledby="teacher-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">inside a step</p>
        <h2 id="teacher-h">Every step has a teacher.</h2>
        <p class="sec-sub-tw">Not just a prompt. An explanation, an example, a place to paste,
          and a clear way to know when you are ready.</p>
      </header>

      <article class="teacher-demo" data-reveal aria-label="Example guided step">
        <header class="td-head">
          <p class="td-eyebrow mono">Step 1 of 4 · about 5 min</p>
          <h3>Define who your landing page is for</h3>
          <p class="td-what"><strong>What you are doing:</strong> Name a specific audience,
            their main problem, and the outcome they want.</p>
          <p class="td-why"><strong>Why it matters:</strong> Later steps quote this.
            Vague audiences make generic landing pages.</p>
        </header>

        <div class="td-prompt prompt-frame">
          <div class="prompt-frame-bar mono">
            <span>prepared prompt</span>
            <span class="td-copy mono" aria-hidden="true">⧉ copy</span>
          </div>
          <pre class="td-pre mono">Using only the facts below, write who this landing page is for.
Invent nothing.

Product: Driftlog
One-liner: Effortless time logging for freelance designers
Audience notes: Freelancers who bill by the hour but hate timers

Include: a specific audience, their main problem, and the outcome they want.</pre>
        </div>

        <div class="td-grid">
          <div class="td-panel">
            <p class="td-label mono">paste your AI response</p>
            <div class="td-paste" aria-hidden="true">
              <p>Freelance designers and small studios who bill by the hour…
                They lose money forgetting to log hours and rebuilding the week on Friday.</p>
            </div>
          </div>
          <div class="td-panel">
            <p class="td-label mono">what good looks like</p>
            <ul class="td-good">
              <li>Names a specific audience</li>
              <li>States a real problem</li>
              <li>Ends with a clear outcome</li>
            </ul>
            <p class="td-label mono td-label-spaced">common mistakes</p>
            <ul class="td-miss">
              <li>“Everyone who needs time tracking”</li>
              <li>Features with no audience</li>
              <li>Vague “save time” claims</li>
            </ul>
          </div>
        </div>

        <div class="td-check">
          <p class="td-check-label mono">confidence check</p>
          <p>Ready to continue when the answer names a specific audience, their main problem,
            and the outcome they want.</p>
          <p class="td-continue mono">→ continue to next step</p>
        </div>
      </article>
    </div>
  </section>

  <!-- 5. LEARN WHILE YOU BUILD -->
  <section class="tw-sec sec-learn" id="learn" aria-labelledby="learn-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">skill while you work</p>
        <h2 id="learn-h">Get better at AI by actually using it.</h2>
        <p class="sec-sub-tw">You finish a real project. Along the way you pick up practical
          judgment. No course required.</p>
      </header>

      <ul class="learn-grid" data-reveal>
        <li class="learn-item" style="--i:0">
          <h3>Give useful context</h3>
          <p>You learn what facts help AI write something specific.</p>
        </li>
        <li class="learn-item" style="--i:1">
          <h3>Spot weak answers</h3>
          <p>Examples and checks train your eye for generic fluff.</p>
        </li>
        <li class="learn-item" style="--i:2">
          <h3>Improve without starting over</h3>
          <p>Edit one part, paste a new take, or try again with clearer instructions.</p>
        </li>
        <li class="learn-item" style="--i:3">
          <h3>See why a prompt works</h3>
          <p>Prepared prompts show structure you can recognize next time.</p>
        </li>
      </ul>
    </div>
  </section>

  <!-- 6. RELATABLE THOUGHTS -->
  <section class="tw-sec sec-said" id="said" aria-labelledby="said-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">if this sounds familiar</p>
        <h2 id="said-h">Built for people who have said…</h2>
      </header>

      <ul class="said-grid" data-reveal>
        <li class="said-card" style="--i:0">“I never know what to ask.”</li>
        <li class="said-card" style="--i:1">“The answers always feel generic.”</li>
        <li class="said-card" style="--i:2">“I don't know whether the result is good.”</li>
        <li class="said-card" style="--i:3">“I keep rewriting the prompt.”</li>
        <li class="said-card" style="--i:4">“I feel like everyone else understands AI better than I do.”</li>
      </ul>
    </div>
  </section>

  <!-- 7. RECOVERY -->
  <section class="tw-sec sec-recover" id="recover" aria-labelledby="recover-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">when output is weak</p>
        <h2 id="recover-h">Bad AI answer? You're not stuck.</h2>
        <p class="sec-sub-tw">Weak answers are normal. SousMeow keeps the work and shows
          what you can do next. You stay in control.</p>
      </header>

      <ul class="recover-grid" data-reveal>
        <li class="recover-card" style="--i:0"><strong>Improve this answer</strong><span>Edit the text by hand until it fits.</span></li>
        <li class="recover-card" style="--i:1"><strong>Retry in your AI</strong><span>Paste a clearer prompt and bring a new response back.</span></li>
        <li class="recover-card" style="--i:2"><strong>See an example</strong><span>Sample answers show the shape of a good result.</span></li>
        <li class="recover-card" style="--i:3"><strong>Find what is missing</strong><span>Criteria call out the specific gap to fix.</span></li>
        <li class="recover-card" style="--i:4"><strong>Simplify the response</strong><span>Trim noise until the important parts are clear.</span></li>
        <li class="recover-card" style="--i:5"><strong>Continue and return later</strong><span>Approved work stays saved. Come back when ready.</span></li>
      </ul>
    </div>
  </section>

  <!-- 8. CONFIDENCE CHECKS -->
  <section class="tw-sec sec-confidence" id="checks" aria-labelledby="checks-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">confidence checks</p>
        <h2 id="checks-h">You'll always know when you're ready to continue.</h2>
        <p class="sec-sub-tw">These are clear criteria you confirm yourself. SousMeow does not
          auto-grade your AI's writing.</p>
      </header>

      <div class="conf-grid" data-reveal>
        <figure class="conf-card conf-ok">
          <p class="conf-state mono">ready</p>
          <blockquote>You're ready. This answer includes a clear audience, problem, and outcome.</blockquote>
          <ul class="conf-list">
            <li class="is-on">Specific audience</li>
            <li class="is-on">Real problem</li>
            <li class="is-on">Desired outcome</li>
          </ul>
        </figure>
        <figure class="conf-card conf-fix">
          <p class="conf-state mono">needs work</p>
          <blockquote>Almost there. The audience is still too broad. Make it more specific.</blockquote>
          <ul class="conf-list">
            <li class="is-on">Specific audience</li>
            <li>Real problem</li>
            <li>Desired outcome</li>
          </ul>
        </figure>
      </div>
    </div>
  </section>

  <!-- 9. OUTCOMES -->
  <section class="tw-sec sec-outcomes" id="outcomes" aria-labelledby="outcomes-h">
    <div class="tw-wrap">
      <header class="sec-head shelf-head" data-reveal>
        <div>
          <p class="tw-kicker mono">what you can finish</p>
          <h2 id="outcomes-h">Real projects. Finished files.</h2>
        </div>
        <a class="shelf-all mono" href="<?= e($marketplaceUrl) ?>">see guided projects →</a>
      </header>

      <?php if ($cookbooks === []): ?>
        <p class="sec-sub-tw" data-reveal>Guides are being added.
          <a href="<?= e($marketplaceUrl) ?>">Browse what is ready</a>.</p>
      <?php else: ?>
        <div class="cookbook-grid" data-reveal>
          <?php foreach ($cookbooks as $c): ?>
            <?php \SousMeow\Core\View::partial('partials/cookbook-card', ['c' => $c]); ?>
          <?php endforeach; ?>
        </div>
        <p class="outcomes-note mono" data-reveal>We call these reusable guides <strong>Cookbooks</strong>
          once you are ready to start one.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- 10. FINAL CTA -->
  <section class="tw-sec sec-cta" aria-labelledby="cta-h">
    <div class="tw-wrap">
      <div class="cta-inner" data-reveal>
        <h2 id="cta-h">You do not need to become an AI expert.</h2>
        <p class="cta-sub">You just need a clear next step.</p>
        <a class="button button-primary button-large" href="<?= e($startUrl) ?>">Start your first guided project</a>
        <p class="cta-foot mono">free to try · your own AI · finished files</p>
      </div>
    </div>
  </section>

</div>
