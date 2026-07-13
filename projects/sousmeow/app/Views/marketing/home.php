<?php
/**
 * Marketing homepage — "Twilight workbench".
 *
 * The page reads as a narrative walk through the product: prompts break
 * down → the Cookbook → the Pantry → the Runner → Quality Checks → the
 * Project Kit → the shelf. Every demonstration below uses the real
 * Launch Day Kit seed content (the Driftlog sample), not invented UI.
 *
 * @var array<string, mixed>|null       $featured        Executable sample Cookbook.
 * @var list<array<string, mixed>>      $featuredStages  Its stages, ordered.
 * @var list<array<string, mixed>>      $featuredRecipes Its recipes, ordered.
 * @var list<array<string, mixed>>      $cookbooks       Full marketplace listing.
 */

$marketplaceUrl = url('/marketplace');

$recipesByStage = [];
foreach ($featuredRecipes as $recipe) {
    $recipesByStage[(int) $recipe['stage_position']][] = $recipe;
}

// Editorial shelf labels: curated by hand, like a shop window.
$shelfLabel = static fn(array $c): string => match ((string) $c['slug']) {
    'launch-day-kit'               => 'Creator pick',
    'validate-saas-idea'           => 'Most cooked',
    'plan-youtube-video'           => 'Recently updated',
    'build-professional-portfolio' => 'New on the shelf',
    'plan-a-novel'                 => 'Preview',
    default                        => (string) ($c['category'] ?? 'Cookbook'),
};
?>
<div class="tw" id="top">

  <!-- ============================================================
       HERO — one idea, stated plainly, then shown
       ============================================================ -->
  <section class="tw-hero" aria-labelledby="hero-h">
    <div class="tw-wrap">
      <div class="hero-grid">
        <div class="hero-copy">
          <div class="hero-mascot" aria-hidden="true">
            <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'plain']); ?>
          </div>
          <p class="tw-kicker mono">sousmeow · structured AI workflows</p>
          <h1 id="hero-h">AI&nbsp;workflows,<br>not AI&nbsp;prompts.</h1>
          <p class="hero-lede">A prompt gets you a paragraph. A Cookbook walks you through
            every step of a real project — in the AI you already use — and ends with
            finished work in your hands.</p>
          <div class="hero-actions">
            <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Browse the Cookbooks</a>
            <a class="button button-ghost button-large" href="#why-prompts">Walk through it</a>
          </div>
          <p class="hero-foot mono">works with ChatGPT · Claude · Gemini — no API keys, nothing to install</p>
        </div>

        <figure class="hero-visual" data-hero>
          <figcaption class="visually-hidden">A Cookbook of four Recipes completing one
            by one, each producing a file that collects into a finished Project Kit.</figcaption>
          <div class="hv-grid" aria-hidden="true">
            <div class="hv-col-head mono">cookbook</div>
            <div class="hv-col-head hv-gap"></div>
            <div class="hv-col-head mono">project kit</div>

            <div class="hv-recipe" style="--i:0"><span class="hv-dot"></span><span class="hv-name">Define your positioning</span></div>
            <div class="hv-arrow" style="--i:0"><svg viewBox="0 0 40 12"><path d="M2 6 H32 M32 6 l-6 -4 M32 6 l-6 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
            <div class="hv-file mono" style="--i:0">positioning.md</div>

            <div class="hv-recipe" style="--i:1"><span class="hv-dot"></span><span class="hv-name">Write landing page copy</span></div>
            <div class="hv-arrow" style="--i:1"><svg viewBox="0 0 40 12"><path d="M2 6 H32 M32 6 l-6 -4 M32 6 l-6 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
            <div class="hv-file mono" style="--i:1">landing-page.md</div>

            <div class="hv-recipe" style="--i:2"><span class="hv-dot"></span><span class="hv-name">Write launch posts</span></div>
            <div class="hv-arrow" style="--i:2"><svg viewBox="0 0 40 12"><path d="M2 6 H32 M32 6 l-6 -4 M32 6 l-6 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
            <div class="hv-file mono" style="--i:2">announcements.md</div>

            <div class="hv-recipe" style="--i:3"><span class="hv-dot"></span><span class="hv-name">Prepare your FAQ</span></div>
            <div class="hv-arrow" style="--i:3"><svg viewBox="0 0 40 12"><path d="M2 6 H32 M32 6 l-6 -4 M32 6 l-6 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
            <div class="hv-file mono" style="--i:3">faq.md</div>

            <div class="hv-zip mono" style="--i:4"><span class="hv-zip-mark"></span>launch-day-kit.zip · 4 files + manifest</div>
          </div>
          <p class="hero-annot mono" aria-hidden="true">every Recipe leaves <br>something finished behind</p>
        </figure>
      </div>
    </div>
  </section>

  <!-- ============================================================
       01 · WHY PROMPTS BREAK DOWN
       ============================================================ -->
  <section class="tw-sec sec-problem" id="why-prompts" aria-labelledby="problem-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">01 · the problem</p>
        <h2 id="problem-h">Prompts don't finish projects.</h2>
      </header>

      <div class="problem-grid">
        <figure class="problem-panel" data-reveal style="--i:0">
          <div class="diagram">
            <svg viewBox="0 0 340 220" role="img" aria-label="Diagram: three isolated prompts drift apart, each trailing off into nothing.">
              <g class="pr-chip" transform="rotate(-4 70 46)">
                <rect x="18" y="28" width="150" height="34" rx="8"/>
                <text x="34" y="50">“write me a landing page”</text>
              </g>
              <path class="pr-trail" d="M168 46 C 210 46, 240 30, 268 24"/>
              <circle class="pr-end" cx="278" cy="22" r="2.2"/><circle class="pr-end" cx="290" cy="20" r="2.2"/><circle class="pr-end" cx="302" cy="19" r="2.2"/>

              <g class="pr-chip" transform="rotate(2 110 106)">
                <rect x="46" y="88" width="118" height="34" rx="8"/>
                <text x="62" y="110">“hmm. make it better”</text>
              </g>
              <path class="pr-trail" d="M164 106 C 210 108, 238 118, 262 130"/>
              <circle class="pr-end" cx="272" cy="134" r="2.2"/><circle class="pr-end" cx="284" cy="139" r="2.2"/>

              <g class="pr-chip" transform="rotate(-2 96 168)">
                <rect x="26" y="150" width="140" height="34" rx="8"/>
                <text x="42" y="172">“wait, who is this for?”</text>
              </g>
              <path class="pr-trail" d="M166 168 C 204 172, 224 186, 240 198"/>
              <text class="pr-q" x="252" y="210">?</text>
            </svg>
          </div>
          <figcaption>Every chat starts from zero. Context evaporates, quality drifts,
            and nothing accumulates.</figcaption>
        </figure>

        <div class="problem-vs mono" aria-hidden="true" data-reveal style="--i:1">vs</div>

        <figure class="problem-panel" data-reveal style="--i:2">
          <div class="diagram">
            <svg viewBox="0 0 340 220" role="img" aria-label="Diagram: four connected steps pass their work forward and end in a finished kit.">
              <g class="wf-node"><rect x="16" y="92" width="56" height="36" rx="8"/><text x="28" y="114">step 1</text></g>
              <path class="wf-link" d="M72 110 H96"/>
              <g class="wf-node"><rect x="98" y="92" width="56" height="36" rx="8"/><text x="110" y="114">step 2</text></g>
              <path class="wf-link" d="M154 110 H178"/>
              <g class="wf-node"><rect x="180" y="92" width="56" height="36" rx="8"/><text x="192" y="114">step 3</text></g>
              <path class="wf-link" d="M236 110 H260"/>
              <g class="wf-kit"><rect x="262" y="86" width="62" height="48" rx="8"/><text x="274" y="107">done</text><text class="wf-kit-sub" x="274" y="122">.zip</text></g>
              <path class="wf-carry" d="M44 128 C 44 156, 126 156, 126 130"/>
              <path class="wf-carry" d="M126 128 C 126 162, 208 162, 208 130"/>
              <text class="wf-annot" x="70" y="186">each step hands its work to the next</text>
            </svg>
          </div>
          <figcaption>A workflow carries context forward. Every step builds on approved
            work — until the project is done.</figcaption>
        </figure>
      </div>

      <p class="sec-bridge" data-reveal>SousMeow packages that workflow as something you can
        hold: <strong>a&nbsp;Cookbook</strong>.</p>
    </div>
  </section>

  <?php if ($featured !== null): ?>
  <!-- ============================================================
       02 · MEET THE COOKBOOK — one real artifact, typeset
       ============================================================ -->
  <section class="tw-sec sec-cookbook" id="cookbook" aria-labelledby="cookbook-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">02 · the cookbook</p>
        <h2 id="cookbook-h">A Cookbook is a project with the thinking already done.</h2>
      </header>

      <article class="cb-artifact" data-reveal aria-label="The <?= e($featured['title']) ?> Cookbook">
        <div class="cb-cover">
          <p class="cb-cat mono"><?= e($featured['category']) ?> · <?= e($featured['difficulty'] ?? 'Intermediate') ?></p>
          <h3 class="cb-title"><?= e($featured['title']) ?></h3>
          <p class="cb-tagline"><?= e($featured['tagline']) ?></p>

          <dl class="cb-facts">
            <div><dt class="mono">recipes</dt><dd><?= (int) $featured['recipe_count'] ?></dd></div>
            <div><dt class="mono">time</dt><dd>~<?= (int) $featured['est_minutes'] ?> min</dd></div>
            <div><dt class="mono">completed runs</dt><dd><?= number_format((int) ($featured['demo_completed_runs'] ?? 0)) ?></dd></div>
          </dl>

          <div class="cb-outcome">
            <p class="cb-outcome-label mono">you leave with</p>
            <p class="cb-outcome-text"><?= e(ucfirst((string) $featured['outcome'])) ?>.</p>
          </div>

          <a class="button button-ghost" href="<?= e(url('/cookbooks/' . $featured['slug'])) ?>">Open this Cookbook</a>
        </div>

        <div class="cb-toc" aria-label="Table of contents">
          <p class="cb-toc-head mono">contents</p>
          <?php foreach ($featuredStages as $stage): ?>
            <div class="cb-stage">
              <p class="cb-stage-name"><span class="cb-stage-no mono"><?= sprintf('%02d', (int) $stage['position']) ?></span>
                <?= e($stage['title']) ?> <span class="cb-stage-sum">— <?= e($stage['summary']) ?></span></p>
              <ol class="cb-recipes">
                <?php foreach ($recipesByStage[(int) $stage['position']] ?? [] as $recipe): ?>
                  <li class="cb-recipe">
                    <span class="cb-recipe-name"><?= e($recipe['title']) ?></span>
                    <span class="cb-leader" aria-hidden="true"></span>
                    <span class="cb-recipe-min mono"><?= (int) $recipe['est_minutes'] ?>&nbsp;min</span>
                  </li>
                <?php endforeach; ?>
              </ol>
            </div>
          <?php endforeach; ?>
        </div>
      </article>

      <p class="sec-bridge" data-reveal>Every Recipe cooks from the same set of facts about
        <em>your</em> project. Those facts live in <strong>the&nbsp;Pantry</strong>.</p>
    </div>
  </section>
  <?php endif; ?>

  <!-- ============================================================
       03 · THE PANTRY — variables become prompts
       ============================================================ -->
  <section class="tw-sec sec-pantry" id="pantry" aria-labelledby="pantry-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">03 · the pantry</p>
        <h2 id="pantry-h">Answer once. Every prompt remembers.</h2>
        <p class="sec-sub-tw">Stock the Pantry with the plain facts of your project.
          Every Recipe quotes them — and is told to invent nothing beyond them.</p>
      </header>

      <div class="pantry-demo" data-pantry>
        <div class="pantry-fields" role="list" aria-label="Pantry values for the sample project">
          <div class="pf" role="listitem" data-field="product_name" style="--i:0">
            <span class="pf-label mono">product_name</span>
            <span class="pf-value">Driftlog</span>
          </div>
          <div class="pf" role="listitem" data-field="one_liner" style="--i:1">
            <span class="pf-label mono">one_liner</span>
            <span class="pf-value">Effortless time logging for freelance designers</span>
          </div>
          <div class="pf" role="listitem" data-field="audience" style="--i:2">
            <span class="pf-label mono">audience</span>
            <span class="pf-value">Freelancers who bill by the hour but hate timers</span>
          </div>
          <div class="pf" role="listitem" data-field="tone" style="--i:3">
            <span class="pf-label mono">tone</span>
            <span class="pf-value">Quietly confident</span>
          </div>
        </div>

        <div class="pantry-prompt prompt-frame" aria-label="A prompt being prepared from Pantry values">
          <div class="prompt-frame-bar mono"><span>recipe 1 of 4 · prompt, prepared for you</span></div>
          <pre class="pantry-pre mono">You are a positioning-focused product marketer.
Write positioning for a product using only the
facts below. Invent nothing.

Product: <span class="pv" data-token="product_name"><span class="pv-key">{{product_name}}</span><span class="pv-val">Driftlog</span></span>
One-liner: <span class="pv" data-token="one_liner"><span class="pv-key">{{one_liner}}</span><span class="pv-val">Effortless time logging for freelance designers</span></span>
Audience: <span class="pv" data-token="audience"><span class="pv-key">{{audience}}</span><span class="pv-val">Freelancers who bill by the hour but hate timers</span></span>
Voice: <span class="pv" data-token="tone"><span class="pv-key">{{tone}}</span><span class="pv-val">Quietly confident</span></span></pre>
        </div>

        <button type="button" class="pantry-replay mono" data-pantry-replay hidden>↻ fill the prompt again</button>
      </div>

      <p class="sec-bridge" data-reveal>A prepared prompt is only half the loop. The other
        half happens in <strong>the&nbsp;Runner</strong>.</p>
    </div>
  </section>

  <!-- ============================================================
       04 · THE RECIPE RUNNER — the centerpiece
       ============================================================ -->
  <section class="tw-sec sec-runner" id="runner" aria-labelledby="runner-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">04 · the runner</p>
        <h2 id="runner-h">One loop, repeated until the work is done.</h2>
        <p class="sec-sub-tw">SousMeow never calls an AI for you. You carry each prompt to
          the assistant you already pay for, and carry the answer back. The Runner keeps
          the loop honest.</p>
      </header>

      <div class="runner-demo is-step-1" data-runner data-reveal>
        <ol class="run-rail" aria-label="The six moments of the loop">
          <li><button type="button" class="run-step" data-step="1"><span class="rs-no mono">1</span><span class="rs-name">Recipe</span></button></li>
          <li><button type="button" class="run-step" data-step="2"><span class="rs-no mono">2</span><span class="rs-name">Prompt</span></button></li>
          <li><button type="button" class="run-step" data-step="3"><span class="rs-no mono">3</span><span class="rs-name">Your AI</span></button></li>
          <li><button type="button" class="run-step" data-step="4"><span class="rs-no mono">4</span><span class="rs-name">Paste back</span></button></li>
          <li><button type="button" class="run-step" data-step="5"><span class="rs-no mono">5</span><span class="rs-name">Quality check</span></button></li>
          <li><button type="button" class="run-step" data-step="6"><span class="rs-no mono">6</span><span class="rs-name">Next Recipe</span></button></li>
        </ol>

        <div class="run-stage">
          <div class="run-panel run-panel-recipe" data-panel="1 2">
            <p class="run-panel-tag mono">sousmeow · recipe 1 of 4</p>
            <h3 class="run-panel-title">Define your positioning</h3>
            <p class="run-panel-body">Clarify what your product is, who it is for, and why
              it matters. Every later step quotes this word for word.</p>
            <div class="run-prompt mono" aria-label="Prepared prompt, ready to copy">
              <span class="run-prompt-line">Write positioning for <b>Driftlog</b> using only</span>
              <span class="run-prompt-line">the facts below. Invent nothing. …</span>
              <span class="run-copy" aria-hidden="true">⧉ copy</span>
            </div>
          </div>

          <div class="run-panel run-panel-ai" data-panel="3">
            <p class="run-panel-tag mono">your AI · any of them</p>
            <div class="run-ai-tabs" aria-hidden="true">
              <span class="run-ai-tab is-on">ChatGPT</span><span class="run-ai-tab">Claude</span><span class="run-ai-tab">Gemini</span>
            </div>
            <div class="run-ai-thread" aria-hidden="true">
              <span class="run-line run-line-user"></span>
              <span class="run-line w80"></span>
              <span class="run-line w95"></span>
              <span class="run-line w60"></span>
            </div>
            <p class="run-panel-body">Paste the prompt into whichever assistant you already
              use. Your subscription, your data, your choice.</p>
          </div>

          <div class="run-panel run-panel-review" data-panel="4 5 6">
            <p class="run-panel-tag mono">sousmeow · review</p>
            <div class="run-response" aria-hidden="true">
              <span class="run-resp-head mono">## Positioning statement</span>
              <span class="run-line w95"></span>
              <span class="run-line w85"></span>
            </div>
            <ul class="run-checks">
              <li class="run-check" style="--i:0"><span class="rc-box" aria-hidden="true"></span>Names the audience from your Pantry, not a generic one</li>
              <li class="run-check" style="--i:1"><span class="rc-box" aria-hidden="true"></span>Claims only features from your list</li>
              <li class="run-check" style="--i:2"><span class="rc-box" aria-hidden="true"></span>You would say this sentence out loud</li>
            </ul>
            <p class="run-next mono"><span class="run-next-arrow" aria-hidden="true">→</span> recipe 2 unlocked: Write landing page copy</p>
          </div>
        </div>

        <div class="run-captions">
          <p class="run-caption mono" data-caption="1">the Recipe frames the step: what you're making and why it matters</p>
          <p class="run-caption mono" data-caption="2">the prompt is prepared from your Pantry — copy it in one tap</p>
          <p class="run-caption mono" data-caption="3">run it in the AI you already have; SousMeow never sees your chat</p>
          <p class="run-caption mono" data-caption="4">paste the response back — it's saved as version 1, untouched</p>
          <p class="run-caption mono" data-caption="5">confirm the Quality Checks: you read it, it holds up</p>
          <p class="run-caption mono" data-caption="6">approval unlocks the next Recipe, carrying this work forward</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       05 · QUALITY CHECKS — the part nobody else does
       ============================================================ -->
  <section class="tw-sec sec-checks" id="checks" aria-labelledby="checks-h">
    <div class="tw-wrap">
      <div class="checks-grid">
        <header class="sec-head checks-head" data-reveal>
          <p class="tw-kicker mono">05 · quality checks</p>
          <h2 id="checks-h">Nothing advances unread.</h2>
          <p class="sec-sub-tw">Most AI tools grade their own homework. SousMeow doesn't
            pretend to: a Quality Check is your judgement, recorded. Each confirmation
            binds to the exact version you read — edit the text and the checks reset,
            on purpose.</p>
          <ul class="checks-versions" aria-label="Version history of one response">
            <li class="cv mono" style="--i:0"><span class="cv-v">v1</span> pasted response <span class="cv-state">superseded</span></li>
            <li class="cv mono" style="--i:1"><span class="cv-v">v2</span> edited · checks reset <span class="cv-state">superseded</span></li>
            <li class="cv cv-approved mono" style="--i:2"><span class="cv-v">v3</span> restored from v1 <span class="cv-state">approved</span></li>
          </ul>
        </header>

        <figure class="checks-artifact" data-checks data-reveal>
          <figcaption class="visually-hidden">A pasted response with margin annotations,
            three confirmed Quality Checks, and an approval stamp.</figcaption>
          <div class="ca-sheet">
            <p class="ca-head mono">artifact · positioning.md · v3</p>
            <p class="ca-line ca-strong">Driftlog is a passive time logger for freelance
              designers who bill by the hour but refuse to babysit a timer.</p>
            <p class="ca-line">It watches the apps you already work in, then turns your day
              into clean, client-ready entries in one review tap.</p>
            <span class="ca-annot ca-annot-1 mono" aria-hidden="true">audience quoted from<br>your Pantry — nothing invented</span>
            <span class="ca-annot ca-annot-2 mono" aria-hidden="true">a claim you can<br>defend in comments</span>

            <ul class="ca-checks">
              <li class="ca-check" style="--i:0"><span class="ca-mark" aria-hidden="true"></span>Names the audience from your Pantry</li>
              <li class="ca-check" style="--i:1"><span class="ca-mark" aria-hidden="true"></span>Claims only features from your list</li>
              <li class="ca-check" style="--i:2"><span class="ca-mark" aria-hidden="true"></span>You would say this out loud</li>
            </ul>

            <span class="ca-stamp" aria-hidden="true">
              <span class="ca-stamp-top">approved</span>
              <span class="ca-stamp-sub mono">read by you · v3 · 3/3</span>
            </span>
          </div>
        </figure>
      </div>
    </div>
  </section>

  <!-- ============================================================
       06 · THE PROJECT KIT — everything, collected
       ============================================================ -->
  <section class="tw-sec sec-kit" id="kit" aria-labelledby="kit-h">
    <div class="tw-wrap">
      <div class="kit-grid">
        <header class="sec-head" data-reveal>
          <p class="tw-kicker mono">06 · the project kit</p>
          <h2 id="kit-h">Finish with files, not a chat history.</h2>
          <p class="sec-sub-tw">Everything you approve collects into one organized bundle:
            documents, copy, plans, research — plus a manifest of what was made, when,
            and from which Recipe. Yours to keep, ship, or hand off.</p>
        </header>

        <div class="kit-manifest" data-kit data-reveal>
          <p class="km-head mono">launch-day-kit.zip <span class="km-count" data-kit-count>0 of 5 packed</span></p>
          <ol class="km-files">
            <li class="km-file" style="--i:0"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">positioning.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">recipe 1</span></li>
            <li class="km-file" style="--i:1"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">landing-page.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">recipe 2</span></li>
            <li class="km-file" style="--i:2"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">announcements.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">recipe 3</span></li>
            <li class="km-file" style="--i:3"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">faq.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">recipe 4</span></li>
            <li class="km-file km-file-manifest" style="--i:4"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">manifest.txt</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">who approved what, when</span></li>
          </ol>
          <p class="km-foot mono">packed as .zip · plain files · no lock-in</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       07 · THE SHELF — curated marketplace
       ============================================================ -->
  <section class="tw-sec sec-shelf" id="shelf" aria-labelledby="shelf-h">
    <div class="tw-wrap">
      <header class="sec-head shelf-head" data-reveal>
        <div>
          <p class="tw-kicker mono">07 · the shelf</p>
          <h2 id="shelf-h">Cookbooks worth cooking.</h2>
        </div>
        <a class="shelf-all mono" href="<?= e($marketplaceUrl) ?>">browse the whole shelf →</a>
      </header>

      <?php if ($cookbooks === []): ?>
        <p class="sec-sub-tw" data-reveal>The shelf is being stocked —
          <a href="<?= e($marketplaceUrl) ?>">see the marketplace</a>.</p>
      <?php else: ?>
      <ul class="shelf" data-reveal>
        <?php foreach ($cookbooks as $i => $cookbook): ?>
          <li class="shelf-book accent-<?= e(preg_replace('/[^a-z]/', '', (string) $cookbook['accent'])) ?>" style="--i:<?= (int) $i ?>">
            <p class="sb-tag mono"><?= e($shelfLabel($cookbook)) ?></p>
            <h3 class="sb-title"><a href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>"><?= e($cookbook['title']) ?></a></h3>
            <p class="sb-tagline"><?= e($cookbook['tagline']) ?></p>
            <p class="sb-meta mono">
              <?= e(plural((int) $cookbook['recipe_count'], 'recipe')) ?> ·
              ~<?= (int) $cookbook['est_minutes'] ?> min ·
              <?= e($cookbook['difficulty'] ?? 'Intermediate') ?>
            </p>
            <p class="sb-foot">
              <?php if ((int) $cookbook['is_executable'] === 1): ?>
                <span class="sb-runs mono"><?= number_format((int) ($cookbook['demo_completed_runs'] ?? 0)) ?> runs · ★ <?= e((string) ($cookbook['demo_avg_rating'] ?? '')) ?></span>
              <?php else: ?>
                <span class="sb-runs mono">preview · full run coming soon</span>
              <?php endif; ?>
            </p>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="shelf-rail" aria-hidden="true"></div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============================================================
       FINAL CTA — quiet and sure
       ============================================================ -->
  <section class="tw-sec sec-cta" aria-labelledby="cta-h">
    <div class="tw-wrap">
      <div class="cta-inner" data-reveal>
        <h2 id="cta-h">Stop prompting. Start finishing.</h2>
        <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Start your first Cookbook</a>
        <p class="cta-foot mono">free to try · bring the AI you already have · leave with files</p>
      </div>
    </div>
  </section>

</div>
