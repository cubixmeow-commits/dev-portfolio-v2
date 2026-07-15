<section class="wrap home-hero">
  <h1 class="home-title">Every day is a new game.</h1>
  <p class="home-lede">Compete head-to-head using wearable health statistics. Win more days. Win the series.</p>
  <div class="home-cta">
    <a class="button button-primary" href="<?= e(url('/register')) ?>">Start a series</a>
    <a class="button button-ghost" href="<?= e(url('/login')) ?>">Sign in</a>
  </div>
  <p class="home-demo-hint hint">Demo: iain@rally.demo / rally-demo-2026</p>
</section>

<section class="wrap home-board" aria-label="Example live Rally match">
  <div class="demo-shell">
    <div class="board-meta">
      <span class="t-label">Daily Steps</span>
      <span class="t-label" aria-hidden="true">·</span>
      <span class="t-label">14-game series</span>
      <span class="t-label" aria-hidden="true">·</span>
      <span class="t-label">Game 9 of 14</span>
      <span class="board-meta-spacer"></span>
      <span class="status-pill status-live">Live</span>
    </div>

    <div class="board" aria-hidden="true">
      <div class="board-side board-side--a">
        <div class="board-ident">
          <span class="avatar">I</span>
          <span class="board-name">Iain</span>
        </div>
        <p class="board-source">Apple Watch</p>
        <p class="board-score-num t-num board-score-inner">5</p>
        <span class="board-lead-tick board-score-inner"></span>
      </div>
      <div class="board-center"><span class="board-center-rule"></span></div>
      <div class="board-side board-side--b">
        <div class="board-ident">
          <span class="avatar">M</span>
          <span class="board-name">Mike</span>
        </div>
        <p class="board-source">Garmin Watch</p>
        <p class="board-score-num t-num board-score-inner is-trailing">3</p>
        <span class="board-lead-tick board-score-inner is-hidden"></span>
      </div>
    </div>
    <p class="visually-hidden">Example match: Iain leads Mike five games to three. Game nine is live. Six games remain.</p>

    <div class="board-verdict" aria-hidden="true">
      <span class="board-verdict-text"><strong>Provisional</strong> — 6 games remaining</span>
    </div>

    <div class="rail-wrap" aria-hidden="true">
      <ol class="rail">
        <?php
        $demo = ['a', 'a', 'b', 'a', 'b', 'a', 'b', 'a', 'live', 'future', 'future', 'future', 'future', 'future'];
        foreach ($demo as $i => $state):
          $glyph = $state === 'a' ? 'I' : ($state === 'b' ? 'M' : null);
        ?>
        <li>
          <span class="rail-cell rail-cell--<?= e($state) ?>">
            <span class="rail-track">
              <span class="rail-chip"><?php if ($state === 'live'): ?><span class="rail-live-dot"></span><?php else: ?><?= e((string) $glyph) ?><?php endif; ?></span>
            </span>
            <span class="rail-num"><?= $i + 1 ?></span>
          </span>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>

    <div class="demo-live" aria-hidden="true">
      <span class="t-label t-label--signal">Today · Game 9</span>
      <span class="demo-live-vals t-num">8,421 <span class="demo-dim">·</span> 7,984</span>
      <span class="demo-live-lead">Iain leads by 437</span>
    </div>
  </div>
</section>

<section class="wrap home-points">
  <article class="home-point">
    <p class="t-label t-label--signal">01 · The series rail</p>
    <h2>Fourteen games. One court.</h2>
    <p>Every match contests a single metric across fourteen daily games. Wins stack above the line, losses below — the rail shows the whole story of a series at a glance.</p>
  </article>
  <article class="home-point">
    <p class="t-label t-label--signal">02 · Daily reset</p>
    <h2>Yesterday doesn’t carry.</h2>
    <p>Each day is a separate game and every morning the score is 0–0. A 4,000-step blowout and a 40-step nail-biter are worth exactly one game each. Win more days, win the series.</p>
  </article>
  <article class="home-point">
    <p class="t-label t-label--signal">03 · Official settlement</p>
    <h2>Results become official.</h2>
    <p>A finished day stays <strong>pending</strong> while devices finish syncing, then locks as <strong>official</strong>. Only official games count toward the confirmed series result.</p>
    <div class="settle-panel" aria-hidden="true">
      <span class="settle-glyph">…</span>
      <div>
        <p class="settle-title">Pending review</p>
        <p class="settle-body">Waiting for final device sync. Locks <strong>July 22 at 6:00 AM PDT</strong>.</p>
      </div>
    </div>
  </article>
  <article class="home-point">
    <p class="t-label t-label--signal">04 · Source transparency</p>
    <h2>Equipment is declared.</h2>
    <p>Each competitor declares a data source — Apple Watch, iPhone, Fitbit, Garmin. Different classes of equipment are flagged so every series stays an honest contest.</p>
    <div class="source-strip source-strip--mismatch" aria-hidden="true">
      <span class="source-strip-kicker">Source mismatch</span>
      <span class="source-strip-pair">Apple Watch vs iPhone</span>
      <p class="source-strip-note">Results may not be directly comparable.</p>
    </div>
  </article>
</section>

<section class="wrap home-final">
  <h2>Ready to play?</h2>
  <p class="home-lede">Rally is a competition engine for wearable data — not a fitness tracker.</p>
  <div class="home-cta">
    <a class="button button-primary" href="<?= e(url('/register')) ?>">Create your account</a>
  </div>
</section>
