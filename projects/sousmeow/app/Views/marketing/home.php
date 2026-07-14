<?php
/**
 * Marketing homepage — editorial simplification pass (Product Law 002).
 *
 * Emotional arc: problem → relief → how it works → proof → explore.
 * Class-1 terms (Pantry, Runner, Artifact, Project Kit) stay out of
 * public copy. Cookbook is introduced only in the explore section.
 *
 * @var array<string, mixed>|null       $featured
 * @var list<array<string, mixed>>      $featuredStages
 * @var list<array<string, mixed>>      $featuredRecipes
 * @var list<array<string, mixed>>      $cookbooks
 */

$marketplaceUrl = url('/marketplace');
$productLawUrl = url('/docs/product-law-002');

$recipesByStage = [];
foreach ($featuredRecipes as $recipe) {
    $recipesByStage[(int) $recipe['stage_position']][] = $recipe;
}

$shelfLabel = static fn(array $c): string => match ((string) $c['slug']) {
    'launch-day-kit'               => 'Creator pick',
    'validate-saas-idea'           => 'Most cooked',
    'plan-youtube-video'           => 'Recently updated',
    'build-professional-portfolio' => 'New',
    'plan-a-novel'                 => 'Preview',
    default                        => (string) ($c['category_name'] ?? 'Guide'),
};

$supportedAi = [
    'ChatGPT', 'Claude', 'Gemini', 'Cursor', 'Claude Code', 'Codex',
    'GitHub Copilot', 'Grok', 'Perplexity', 'Microsoft Copilot', 'DeepSeek',
    'Qwen', 'Kimi', 'Mistral', 'Le Chat', 'Amazon Q', 'Windsurf', 'Cline',
    'Continue.dev', 'Roo Code', 'OpenHands', 'Bolt.new', 'Lovable',
    'Firebase Studio', 'Replit AI', 'Zed AI', 'Phind',
];
?>
<div class="tw" id="top">

  <!-- 1. PROBLEM + RELIEF -->
  <section class="tw-hero" aria-labelledby="hero-h">
    <div class="tw-wrap">
      <div class="hero-grid">
        <div class="hero-copy">
          <div class="hero-mascot" aria-hidden="true">
            <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'plain']); ?>
          </div>
          <p class="tw-kicker mono">sousmeow</p>
          <h1 id="hero-h">You know what you want.<br>AI keeps missing&nbsp;it.</h1>
          <p class="hero-lede">SousMeow walks you through proven steps until you finish.
            You use the AI you already have. You never need to become a prompt expert.</p>
          <div class="hero-actions">
            <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Find something to finish</a>
            <a class="button button-ghost button-large" href="#how-it-works">See how it works</a>
          </div>
          <p class="hero-law">
            <a href="<?= e($productLawUrl) ?>">Built on Product Law 002 — Remove Cognitive Load</a>
          </p>
        </div>

        <figure class="hero-visual" data-hero>
          <figcaption class="visually-hidden">Four guided steps, each producing a file that
            collects into a finished zip of results.</figcaption>
          <div class="hv-grid" aria-hidden="true">
            <div class="hv-col-head mono">steps</div>
            <div class="hv-col-head hv-gap"></div>
            <div class="hv-col-head mono">finished files</div>

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

            <div class="hv-zip mono" style="--i:4"><span class="hv-zip-mark"></span>launch-day-kit.zip · 4 files</div>
          </div>
          <p class="hero-annot mono" aria-hidden="true">you bring the goal · <br>SousMeow brings the steps</p>
        </figure>
      </div>
    </div>
  </section>

  <!-- Relief: your AI -->
  <section class="tw-sec sec-ai" id="supported-ai" aria-labelledby="ai-h">
    <div class="tw-wrap">
      <header class="sec-head ai-head" data-reveal>
        <p class="tw-kicker mono">your AI</p>
        <h2 id="ai-h">Works with the AI you already use.</h2>
        <p class="sec-sub-tw">SousMeow does not replace ChatGPT, Claude, Gemini, Cursor, or
          any other assistant. You copy a prepared prompt, run it there, and paste the answer
          back. No API keys. No new subscription.</p>
      </header>

      <ul class="ai-wall" data-reveal aria-label="Examples of AI tools you can use with SousMeow">
        <?php foreach ($supportedAi as $i => $name): ?>
          <li class="ai-chip mono" style="--i:<?= (int) $i ?>"><?= e($name) ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="ai-disclaimer mono" data-reveal>Examples of tools people use — not partnerships
        or built-in integrations.</p>
    </div>
  </section>

  <!-- Problem deepened -->
  <section class="tw-sec sec-problem" id="why-prompts" aria-labelledby="problem-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">the problem</p>
        <h2 id="problem-h">One prompt rarely finishes the job.</h2>
      </header>

      <div class="problem-grid">
        <figure class="problem-panel" data-reveal style="--i:0">
          <div class="diagram">
            <svg viewBox="0 0 340 220" role="img" aria-label="Diagram: three isolated prompts drift apart, each trailing off into nothing.">
              <g class="pr-chip" transform="rotate(-4 70 46)">
                <rect x="18" y="28" width="198" height="34" rx="8"/>
                <text x="34" y="50">“write me a landing page”</text>
              </g>
              <path class="pr-trail" d="M216 46 C 236 42, 252 30, 268 24"/>
              <circle class="pr-end" cx="278" cy="22" r="2.2"/><circle class="pr-end" cx="290" cy="20" r="2.2"/><circle class="pr-end" cx="302" cy="19" r="2.2"/>

              <g class="pr-chip" transform="rotate(2 110 106)">
                <rect x="46" y="88" width="172" height="34" rx="8"/>
                <text x="62" y="110">“hmm. make it better”</text>
              </g>
              <path class="pr-trail" d="M218 106 C 234 110, 248 120, 262 130"/>
              <circle class="pr-end" cx="272" cy="134" r="2.2"/><circle class="pr-end" cx="284" cy="139" r="2.2"/>

              <g class="pr-chip" transform="rotate(-2 96 168)">
                <rect x="26" y="150" width="192" height="34" rx="8"/>
                <text x="42" y="172">“wait, who is this for?”</text>
              </g>
              <path class="pr-trail" d="M218 168 C 228 174, 234 186, 240 198"/>
              <text class="pr-q" x="252" y="210">?</text>
            </svg>
          </div>
          <figcaption>Each chat starts over. You re-explain. Quality drifts. Nothing adds up.</figcaption>
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
          <figcaption>Guided steps carry your work forward until the project is actually done.</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 3. HOW IT WORKS (plain language; keep real demos) -->
  <section class="tw-sec sec-pantry" id="how-it-works" aria-labelledby="how-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">how it works</p>
        <h2 id="how-h">Answer a few facts. Follow the steps. Check the work. Finish.</h2>
        <p class="sec-sub-tw">You enter what you already know about your project once.
          SousMeow builds each prompt from that. Your AI does the writing. You decide what
          is good enough to keep.</p>
      </header>

      <div class="pantry-demo" data-pantry>
        <div class="pantry-fields" role="list" aria-label="Sample project details">
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

        <div class="pantry-prompt prompt-frame" aria-label="A prompt prepared from your information">
          <div class="prompt-frame-bar mono"><span>step 1 of 4 · prompt, ready to copy</span></div>
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
    </div>
  </section>

  <section class="tw-sec sec-runner" id="runner" aria-labelledby="loop-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">each step</p>
        <h2 id="loop-h">Copy. Run in your AI. Paste back. Check. Continue.</h2>
        <p class="sec-sub-tw">SousMeow never calls an AI for you. You stay in the assistant
          you already pay for.</p>
      </header>

      <div class="runner-demo is-step-1" data-runner data-reveal>
        <ol class="run-rail" aria-label="The loop for each step">
          <li><button type="button" class="run-step" data-step="1"><span class="rs-no mono">1</span><span class="rs-name">Goal</span></button></li>
          <li><button type="button" class="run-step" data-step="2"><span class="rs-no mono">2</span><span class="rs-name">Prompt</span></button></li>
          <li><button type="button" class="run-step" data-step="3"><span class="rs-no mono">3</span><span class="rs-name">Your AI</span></button></li>
          <li><button type="button" class="run-step" data-step="4"><span class="rs-no mono">4</span><span class="rs-name">Paste back</span></button></li>
          <li><button type="button" class="run-step" data-step="5"><span class="rs-no mono">5</span><span class="rs-name">Check</span></button></li>
          <li><button type="button" class="run-step" data-step="6"><span class="rs-no mono">6</span><span class="rs-name">Next</span></button></li>
        </ol>

        <div class="run-stage">
          <div class="run-panel run-panel-recipe" data-panel="1 2">
            <p class="run-panel-tag mono">sousmeow · step 1 of 4</p>
            <h3 class="run-panel-title">Define your positioning</h3>
            <p class="run-panel-body">Clarify what your product is, who it is for, and why
              it matters. Later steps build on this.</p>
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
              <li class="run-check" style="--i:0"><span class="rc-box" aria-hidden="true"></span>Names the audience you gave, not a generic one</li>
              <li class="run-check" style="--i:1"><span class="rc-box" aria-hidden="true"></span>Claims only features from your list</li>
              <li class="run-check" style="--i:2"><span class="rc-box" aria-hidden="true"></span>You would say this sentence out loud</li>
            </ul>
            <p class="run-next mono"><span class="run-next-arrow" aria-hidden="true">→</span> next: Write landing page copy</p>
          </div>
        </div>

        <div class="run-captions">
          <p class="run-caption mono" data-caption="1">each step says what you're making and why it matters</p>
          <p class="run-caption mono" data-caption="2">the prompt is built from your information — copy it in one tap</p>
          <p class="run-caption mono" data-caption="3">run it in the AI you already have; SousMeow never sees your chat</p>
          <p class="run-caption mono" data-caption="4">paste the answer back — saved exactly as you received it</p>
          <p class="run-caption mono" data-caption="5">check the work yourself; nothing moves on unread</p>
          <p class="run-caption mono" data-caption="6">approve it, and the next step unlocks</p>
        </div>
      </div>
    </div>
  </section>

  <section class="tw-sec sec-checks" id="checks" aria-labelledby="checks-h">
    <div class="tw-wrap">
      <div class="checks-grid">
        <header class="sec-head checks-head" data-reveal>
          <p class="tw-kicker mono">your call</p>
          <h2 id="checks-h">Nothing advances unread.</h2>
          <p class="sec-sub-tw">You read the answer. You confirm a short checklist.
            Edit it, and the checks reset on purpose.</p>
          <ul class="checks-versions" aria-label="Version history of one response">
            <li class="cv mono" style="--i:0"><span class="cv-v">v1</span> pasted response <span class="cv-state">superseded</span></li>
            <li class="cv mono" style="--i:1"><span class="cv-v">v2</span> edited · checks reset <span class="cv-state">superseded</span></li>
            <li class="cv cv-approved mono" style="--i:2"><span class="cv-v">v3</span> restored from v1 <span class="cv-state">approved</span></li>
          </ul>
        </header>

        <figure class="checks-artifact" data-checks data-reveal>
          <figcaption class="visually-hidden">A pasted response with three confirmed checks
            and an approval stamp.</figcaption>
          <div class="ca-sheet">
            <p class="ca-head mono">positioning.md · v3</p>
            <p class="ca-line ca-strong">Driftlog is a passive time logger for freelance
              designers who bill by the hour but refuse to babysit a timer.</p>
            <p class="ca-line">It watches the apps you already work in, then turns your day
              into clean, client-ready entries in one review tap.</p>
            <span class="ca-annot ca-annot-1 mono" aria-hidden="true">audience from your<br>facts — nothing invented</span>
            <span class="ca-annot ca-annot-2 mono" aria-hidden="true">a claim you can<br>defend</span>

            <ul class="ca-checks">
              <li class="ca-check" style="--i:0"><span class="ca-mark" aria-hidden="true"></span>Names the audience you gave</li>
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

  <section class="tw-sec sec-kit" id="kit" aria-labelledby="kit-h">
    <div class="tw-wrap">
      <div class="kit-grid">
        <header class="sec-head" data-reveal>
          <p class="tw-kicker mono">the result</p>
          <h2 id="kit-h">Finish with files, not a chat history.</h2>
          <p class="sec-sub-tw">Everything you approve lands in one zip of plain files.
            Yours to keep, ship, or hand off.</p>
        </header>

        <div class="kit-manifest" data-kit data-reveal>
          <p class="km-head mono">launch-day-kit.zip <span class="km-count" data-kit-count>0 of 5 packed</span></p>
          <ol class="km-files">
            <li class="km-file" style="--i:0"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">positioning.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">step 1</span></li>
            <li class="km-file" style="--i:1"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">landing-page.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">step 2</span></li>
            <li class="km-file" style="--i:2"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">announcements.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">step 3</span></li>
            <li class="km-file" style="--i:3"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">faq.md</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">step 4</span></li>
            <li class="km-file km-file-manifest" style="--i:4"><span class="km-in" aria-hidden="true">→</span><span class="km-name mono">manifest.txt</span><span class="km-leader" aria-hidden="true"></span><span class="km-from">what you approved</span></li>
          </ol>
          <p class="km-foot mono">plain files · no lock-in</p>
        </div>
      </div>
    </div>
  </section>

  <?php if ($featured !== null): ?>
  <!-- 4. PROOF — real guided project demo -->
  <section class="tw-sec sec-cookbook" id="proof" aria-labelledby="proof-h">
    <div class="tw-wrap">
      <header class="sec-head" data-reveal>
        <p class="tw-kicker mono">an example</p>
        <h2 id="proof-h">One guided project, start to finish.</h2>
      </header>

      <article class="cb-artifact" data-reveal aria-label="<?= e($featured['title']) ?>">
        <div class="cb-cover">
          <p class="cb-cat mono"><?= e($featured['category_name'] ?? '') ?> · <?= e($featured['difficulty'] ?? 'Intermediate') ?></p>
          <h3 class="cb-title"><?= e($featured['title']) ?></h3>
          <p class="cb-tagline"><?= e($featured['tagline']) ?></p>

          <dl class="cb-facts">
            <div><dt class="mono">steps</dt><dd><?= (int) $featured['recipe_count'] ?></dd></div>
            <div><dt class="mono">time</dt><dd>~<?= (int) $featured['est_minutes'] ?> min</dd></div>
            <div><dt class="mono">completed runs</dt><dd><?= number_format((int) ($featured['demo_completed_runs'] ?? 0)) ?></dd></div>
          </dl>

          <div class="cb-outcome">
            <p class="cb-outcome-label mono">you leave with</p>
            <p class="cb-outcome-text"><?= e(ucfirst((string) $featured['outcome'])) ?>.</p>
          </div>

          <a class="button button-ghost" href="<?= e(url('/cookbooks/' . $featured['slug'])) ?>">Open this guide</a>
        </div>

        <div class="cb-toc" aria-label="Table of contents">
          <p class="cb-toc-head mono">what's inside</p>
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
    </div>
  </section>
  <?php endif; ?>

  <!-- 5. EXPLORE — first natural introduction of Cookbook -->
  <section class="tw-sec sec-shelf" id="shelf" aria-labelledby="shelf-h">
    <div class="tw-wrap">
      <header class="sec-head shelf-head" data-reveal>
        <div>
          <p class="tw-kicker mono">explore</p>
          <h2 id="shelf-h">We call these guides Cookbooks.</h2>
        </div>
        <a class="shelf-all mono" href="<?= e($marketplaceUrl) ?>">see all Cookbooks →</a>
      </header>
      <p class="sec-sub-tw" data-reveal>Each one is a step-by-step project with the hard thinking
        already done. Pick one that matches what you want to finish.</p>

      <?php if ($cookbooks === []): ?>
        <p class="sec-sub-tw" data-reveal>Guides are being added —
          <a href="<?= e($marketplaceUrl) ?>">browse what is ready</a>.</p>
      <?php else: ?>
      <ul class="shelf" data-reveal>
        <?php foreach ($cookbooks as $i => $cookbook): ?>
          <li class="shelf-book accent-<?= e(preg_replace('/[^a-z]/', '', (string) $cookbook['accent'])) ?>" style="--i:<?= (int) $i ?>">
            <p class="sb-tag mono"><?= e($shelfLabel($cookbook)) ?></p>
            <h3 class="sb-title"><a href="<?= e(url('/cookbooks/' . $cookbook['slug'])) ?>"><?= e($cookbook['title']) ?></a></h3>
            <p class="sb-tagline"><?= e($cookbook['tagline']) ?></p>
            <p class="sb-meta mono">
              <?= (int) $cookbook['recipe_count'] ?> steps ·
              ~<?= (int) $cookbook['est_minutes'] ?> min ·
              <?= e($cookbook['difficulty'] ?? 'Intermediate') ?>
            </p>
            <p class="sb-foot">
              <?php if ((int) $cookbook['is_executable'] === 1): ?>
                <span class="sb-runs mono"><?= number_format((int) ($cookbook['demo_completed_runs'] ?? 0)) ?> runs · ★ <?= e((string) ($cookbook['demo_avg_rating'] ?? '')) ?></span>
              <?php else: ?>
                <span class="sb-runs mono">preview · coming soon</span>
              <?php endif; ?>
            </p>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="shelf-rail" aria-hidden="true"></div>
      <?php endif; ?>
    </div>
  </section>

  <section class="tw-sec sec-cta" aria-labelledby="cta-h">
    <div class="tw-wrap">
      <div class="cta-inner" data-reveal>
        <h2 id="cta-h">You don't have to become an AI expert.</h2>
        <a class="button button-primary button-large" href="<?= e($marketplaceUrl) ?>">Find something to finish</a>
        <p class="cta-foot mono">free to try · your AI · finished files</p>
      </div>
    </div>
  </section>

</div>
