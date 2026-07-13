<?php
use SousMeow\Core\Csrf;
use SousMeow\Services\ResponseParser;
use SousMeow\Services\SafeText;

/**
 * The Recipe Runner as a guided wizard. One primary task per screen.
 *
 * Durable, server-authoritative states (never lost on refresh):
 *  - gather:   no response version exists
 *  - review:   a version exists and awaits Quality Checks + approval
 *  - approved: this Recipe is locked into the kit
 *
 * Within gather, $wizard walks three instructional screens
 * (understand → prompt → paste) via a clamped ?stage= parameter.
 *
 * @var array<string, mixed>       $project
 * @var array<string, mixed>       $recipe
 * @var list<array<string, mixed>> $recipes
 * @var array<int, string>         $statuses    recipe id => artifact status
 * @var array<string, mixed>|null  $artifact
 * @var list<array<string, mixed>> $versions    newest first
 * @var array<string, mixed>|null  $latest
 * @var array<string, mixed>|null  $viewing
 * @var list<array<string, mixed>> $checks
 * @var list<int>                  $confirmed   confirmed check ids (latest version)
 * @var string                     $state       gather|review|approved
 * @var string                     $wizard      understand|prompt|paste|review|approved
 * @var list<array<string, mixed>>|null $contract
 * @var array<string, mixed>|null  $parsed
 * @var array<string, string>      $headingByKey
 * @var list<array<string, mixed>> $reviewCards
 * @var array{text:string, html:string, missing:list<string>} $prompt
 * @var list<array{label:string, value:string}> $ingredients
 * @var int                        $approvedCount
 */

$projectId = (int) $project['id'];
$position = (int) $recipe['position'];
$total = count($recipes);
$runBase = url('/projects/' . $projectId . '/run/' . $position);
$isViewingOld = $viewing !== null && $latest !== null && (int) $viewing['id'] !== (int) $latest['id'];
$allConfirmed = $checks !== [] && count($confirmed) >= count($checks);
$sourceLabels = ['pasted' => 'Pasted response', 'example' => 'Sample data', 'edited' => 'Edited', 'restored' => 'Restored'];

$statusLabels = [
    ResponseParser::STATUS_LOCATED  => 'Evidence located',
    ResponseParser::STATUS_MISSING  => 'Expected section missing',
    ResponseParser::STATUS_MANUAL   => 'Manual review required',
    ResponseParser::STATUS_MULTIPLE => 'Multiple matching sections found',
];
$statusMarks = [
    ResponseParser::STATUS_LOCATED  => '&#182;',   /* pilcrow: section found */
    ResponseParser::STATUS_MISSING  => '&#8709;',  /* empty set */
    ResponseParser::STATUS_MANUAL   => '&#9998;',  /* pencil */
    ResponseParser::STATUS_MULTIPLE => '&#8801;',  /* triple bar */
];

$stageLabels = [
    'understand' => 'Understand the step',
    'prompt'     => 'Copy the prompt',
    'paste'      => 'Add the response',
    'review'     => 'Review the response',
    'approved'   => 'Step approved',
];

$nextRecipe = null;
foreach ($recipes as $r) {
    if ((int) $r['position'] === $position + 1) {
        $nextRecipe = $r;
    }
}
$isFinal = $nextRecipe === null;

// Structural warnings that remain for the review summary. Advisory only:
// they inform the reviewer and never gate approval.
$structuralWarnings = [];
if ($parsed !== null) {
    foreach ($parsed['missing_required'] as $key) {
        $structuralWarnings[] = 'Expected section missing: "' . ($headingByKey[$key] ?? $key) . '"';
    }
    foreach ($parsed['duplicates'] as $key) {
        $structuralWarnings[] = 'Section appears more than once: "' . ($headingByKey[$key] ?? $key) . '"';
    }
    foreach ($parsed['unexpected'] as $headingText) {
        $structuralWarnings[] = 'Extra section not in this Recipe\'s outline: "' . $headingText . '"';
    }
}
?>
<div class="page runner-page">
  <nav class="crumbs" aria-label="Breadcrumb">
    <a href="<?= e(url('/kitchen')) ?>">My Kitchen</a>
    <span aria-hidden="true">/</span>
    <span><?= e($project['title']) ?></span>
    <span aria-hidden="true">/</span>
    <span><?= e($recipe['title']) ?></span>
  </nav>

  <header class="runner-head card" aria-label="Cookbook progress">
    <div class="runner-head-row">
      <p class="runner-head-step">Recipe <?= $position ?> of <?= $total ?> · <?= e($stageLabels[$wizard] ?? '') ?></p>
      <p class="runner-head-summary"><?= $approvedCount ?> of <?= $total ?> approved</p>
    </div>
    <ol class="rail-steps">
      <?php foreach ($recipes as $r):
          $rStatus = $statuses[(int) $r['id']] ?? null;
          $isDone = $rStatus === 'approved';
          $isCurrent = (int) $r['position'] === $position;
          // A step is reachable when every earlier step is approved.
          $reachable = true;
          foreach ($recipes as $prev) {
              if ((int) $prev['position'] >= (int) $r['position']) break;
              if (($statuses[(int) $prev['id']] ?? '') !== 'approved') { $reachable = false; break; }
          }
      ?>
        <li class="rail-step <?= $isCurrent ? 'is-current' : '' ?>">
          <?php if ($reachable): ?>
            <a class="rail-link" href="<?= e(url('/projects/' . $projectId . '/run/' . $r['position'])) ?>"
               <?= $isCurrent ? 'aria-current="step"' : '' ?> title="<?= e($r['title']) ?>">
              <span class="step-dot <?= $isDone ? 'is-done' : ($isCurrent ? 'is-active' : '') ?>"><?= $isDone ? '&check;' : (int) $r['position'] ?></span>
              <span class="rail-title"><?= e($r['title']) ?></span>
            </a>
          <?php else: ?>
            <span class="rail-link is-locked" title="<?= e($r['title']) ?> — unlocks when the previous Recipe is approved">
              <span class="step-dot is-locked"><?= (int) $r['position'] ?></span>
              <span class="rail-title"><?= e($r['title']) ?></span>
            </span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      <li class="rail-step rail-step-kit">
        <?php if ($approvedCount >= $total): ?>
          <a class="rail-link" href="<?= e(url('/projects/' . $projectId . '/export')) ?>" title="Project Kit">
            <span class="step-dot is-done" aria-hidden="true">&#8681;</span>
            <span class="rail-title">Project Kit</span>
          </a>
        <?php else: ?>
          <span class="rail-link is-locked" title="Project Kit — unlocks when every Recipe is approved">
            <span class="step-dot is-locked" aria-hidden="true">&#8681;</span>
            <span class="rail-title">Project Kit</span>
          </span>
        <?php endif; ?>
      </li>
    </ol>
  </header>

  <main class="wizard">

  <?php if ($wizard === 'understand'): ?>

    <section class="card card-pad wizard-card rise-in">
      <p class="recipe-eyebrow">Recipe <?= $position ?> of <?= $total ?> · about <?= (int) $recipe['est_minutes'] ?> min</p>
      <h1><?= e($recipe['title']) ?></h1>
      <p class="recipe-summary"><strong>What this step produces:</strong> <?= e($recipe['summary']) ?></p>
      <div class="recipe-why well">
        <p><strong>Why it matters:</strong> <?= e($recipe['why_it_matters']) ?></p>
        <?php if ($recipe['unlocks_text'] !== ''): ?><p class="recipe-unlocks"><?= e($recipe['unlocks_text']) ?></p><?php endif; ?>
      </div>
      <div class="wizard-actions">
        <a class="button button-primary button-large" href="<?= e($runBase . '?stage=prompt') ?>">Continue to prompt</a>
      </div>
    </section>

  <?php elseif ($wizard === 'prompt'): ?>

    <section class="card card-pad wizard-card rise-in">
      <p class="recipe-eyebrow">Recipe <?= $position ?> of <?= $total ?> · <?= e($recipe['title']) ?></p>
      <h1>Copy this prompt and run it in your AI</h1>
      <p class="section-sub">
        Copy the prompt below, paste it into the assistant you already use (ChatGPT, Claude, Gemini, or any
        other), and let it write. SousMeow never calls an AI itself.
      </p>
      <?php if ($prompt['missing'] !== []): ?>
        <div class="well prompt-missing">
          <p>Some ingredients are empty: <strong><?= e(implode(', ', $prompt['missing'])) ?></strong>.
             <a href="<?= e(url('/projects/' . $projectId . '/pantry')) ?>">Fill them in the Pantry</a> for a complete prompt.</p>
        </div>
      <?php endif; ?>
      <div class="prompt-block">
        <div class="prompt-toolbar">
          <span class="prompt-toolbar-label">prompt · <?= e($recipe['slug']) ?></span>
          <button type="button" class="button button-small copy-button" data-copy-target="#prompt-text">
            <span class="copy-label">Copy prompt</span>
            <span class="copied-label">Copied &check;</span>
          </button>
        </div>
        <pre id="prompt-text" tabindex="0"><?= $prompt['html'] ?></pre>
      </div>
      <details class="disclose">
        <summary>See what this prompt uses</summary>
        <div class="disclose-body">
          <p class="section-sub">The <span class="ingredient-swatch">highlighted parts</span> of the prompt come from
             your Pantry and earlier approved Recipes; nothing else is sent anywhere.</p>
          <?php if ($ingredients === []): ?>
            <p class="section-sub">This Recipe builds on earlier approved Recipes rather than Pantry fields directly.</p>
          <?php else: ?>
            <dl class="ingredient-list">
              <?php foreach ($ingredients as $ing): ?>
                <div class="ingredient-row">
                  <dt><?= e($ing['label']) ?></dt>
                  <dd><?= $ing['value'] !== '' ? e(mb_strimwidth($ing['value'], 0, 90, '…')) : '(empty)' ?></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          <?php endif; ?>
          <a class="button button-quiet button-small" href="<?= e(url('/projects/' . $projectId . '/pantry')) ?>">Edit Pantry</a>
        </div>
      </details>
      <div class="wizard-actions">
        <a class="button button-primary button-large" href="<?= e($runBase . '?stage=paste') ?>">I have the response</a>
        <a class="button button-quiet" href="<?= e($runBase . '?stage=understand') ?>">Back</a>
      </div>
    </section>

  <?php elseif ($wizard === 'paste'): ?>

    <section class="card card-pad wizard-card rise-in">
      <p class="recipe-eyebrow">Recipe <?= $position ?> of <?= $total ?> · <?= e($recipe['title']) ?></p>
      <h1>Add the response</h1>
      <p class="section-sub">
        Paste your AI's full answer, formatting and all. It is saved exactly as submitted, and nothing
        enters your Project Kit until you review and approve it.
      </p>
      <form method="post" action="<?= e($runBase . '/paste') ?>" data-loading>
        <?= Csrf::field() ?>
        <textarea class="textarea textarea-mono paste-textarea" name="content" rows="12" required
                  placeholder="Paste your AI's full response here" aria-label="AI response"></textarea>
        <div class="wizard-actions">
          <button type="submit" class="button button-primary button-large">Save and review</button>
          <a class="button button-quiet" href="<?= e($runBase . '?stage=prompt') ?>">Back to prompt</a>
        </div>
      </form>
      <div class="demo-divider" role="separator"><span>no AI handy?</span></div>
      <div class="demo-row">
        <form method="post" action="<?= e($runBase . '/example') ?>" data-loading>
          <?= Csrf::field() ?>
          <button type="submit" class="button button-ghost">Use the sample response</button>
        </form>
        <p class="demo-note">
          <span class="badge badge-sample">Sample data</span>
          A realistic response for Driftlog, a fictional product, so you can finish this Recipe right now.
          It stays marked as sample data everywhere it appears.
        </p>
      </div>
    </section>

  <?php elseif ($isViewingOld): ?>

    <section class="card wizard-card rise-in">
      <div class="response-wrap">
        <div class="response-header">
          <span class="badge badge-lilac">v<?= (int) $viewing['version_no'] ?> · <?= e($sourceLabels[$viewing['source']] ?? $viewing['source']) ?></span>
          <?php if ($viewing['source'] === 'example'): ?><span class="badge badge-sample">Sample data</span><?php endif; ?>
          <span class="version-meta"><?= e(time_ago((string) $viewing['created_at'])) ?></span>
        </div>
        <div class="well old-version-note">
          <p>You are reading version <?= (int) $viewing['version_no'] ?>, kept exactly as it was saved. Reviewing and approving always happen on the newest version.</p>
          <div class="old-version-actions">
            <a class="button button-quiet button-small" href="<?= e($runBase) ?>">Back to v<?= (int) $latest['version_no'] ?> (current)</a>
            <form method="post" action="<?= e($runBase . '/restore') ?>" data-loading>
              <?= Csrf::field() ?>
              <input type="hidden" name="version_no" value="<?= (int) $viewing['version_no'] ?>">
              <button type="submit" class="button button-ghost button-small">Restore this as newest</button>
            </form>
          </div>
        </div>
        <div class="response-body"><?= SafeText::render((string) $viewing['content']) ?></div>
      </div>
    </section>

  <?php elseif ($wizard === 'review'): ?>

    <section class="card card-pad wizard-card rise-in">
      <p class="recipe-eyebrow">Recipe <?= $position ?> of <?= $total ?> · <?= e($recipe['title']) ?></p>
      <div class="section-heading">
        <h1>Review against the quality checks</h1>
        <span class="checks-counter badge <?= $allConfirmed ? 'badge-sage' : 'badge-neutral' ?>" data-checks-counter
              data-confirmed="<?= count($confirmed) ?>" data-total="<?= count($checks) ?>">
          <?= count($confirmed) ?> of <?= count($checks) ?> confirmed
        </span>
      </div>
      <p class="section-sub checks-lede">
        SousMeow found where things live in the response; it never judges whether they are good.
        Each confirmation records <em>your</em> judgement against this exact version — editing the text
        unchecks everything, on purpose.
      </p>
      <div class="response-header">
        <span class="badge badge-terracotta">v<?= (int) $latest['version_no'] ?> · <?= e($sourceLabels[$latest['source']] ?? $latest['source']) ?></span>
        <?php if ($latest['source'] === 'example'): ?><span class="badge badge-sample">Sample data</span><?php endif; ?>
        <span class="version-meta"><?= e(time_ago((string) $latest['created_at'])) ?></span>
      </div>
      <details class="disclose full-response-disclose">
        <summary>View full response</summary>
        <div class="disclose-body">
          <div class="response-body"><?= SafeText::render((string) $latest['content']) ?></div>
        </div>
      </details>
      <p class="visually-hidden" role="status" aria-live="polite" data-save-announce></p>

      <form method="post" action="<?= e($runBase . '/checks') ?>" data-checks-form>
        <?= Csrf::field() ?>
        <ul class="review-list">
          <?php foreach ($reviewCards as $card):
              $check = $card['check'];
              $checkId = (int) $check['id'];
              $isChecked = in_array($checkId, $confirmed, true);
              $status = $card['status'];
          ?>
            <li class="review-card <?= $isChecked ? 'is-checked' : '' ?>" data-review-card>
              <div class="review-card-head">
                <span class="check-label"><?= e($check['label']) ?></span>
                <span class="check-help"><?= e($check['help']) ?></span>
              </div>

              <p class="evidence-status status-<?= e($status) ?>">
                <span class="evidence-mark" aria-hidden="true"><?= $statusMarks[$status] ?? '' ?></span>
                <?= e($statusLabels[$status] ?? $status) ?>
              </p>

              <?php if ($status === ResponseParser::STATUS_MANUAL): ?>
                <p class="evidence-note">This check considers the whole response — open “View full response” above and judge it there.</p>
              <?php endif; ?>

              <?php if ($status === ResponseParser::STATUS_MULTIPLE): ?>
                <p class="evidence-note">The response contains the same section more than once. All copies are shown below;
                   decide for yourself which one counts, or revise the response.</p>
              <?php endif; ?>

              <?php foreach ($card['evidence'] as $block): ?>
                <div class="evidence-block <?= $block['duplicate'] ? 'is-duplicate' : '' ?>">
                  <p class="evidence-heading">
                    <span aria-hidden="true">##</span> <?= e($block['heading']) ?>
                    <?php if ($block['duplicate']): ?><span class="badge badge-amber">duplicate</span><?php endif; ?>
                  </p>
                  <?php if ($block['empty']): ?>
                    <p class="evidence-note">This section is present but empty.</p>
                  <?php else: ?>
                    <div class="evidence-text response-body"><?= SafeText::render((string) $block['content']) ?></div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>

              <?php if ($card['missingHeadings'] !== []): ?>
                <p class="evidence-note">
                  Not found in this response:
                  <?php foreach ($card['missingHeadings'] as $mi => $mh): ?><?= $mi > 0 ? ', ' : '' ?>“<?= e($mh) ?>”<?php endforeach; ?>.
                  You can still confirm this check after reading the full response, or revise the response to add it.
                </p>
              <?php endif; ?>

              <div class="review-card-actions">
                <span class="review-confirm">
                  <input type="checkbox" id="check-<?= $checkId ?>" name="checks[]"
                         value="<?= $checkId ?>" <?= $isChecked ? 'checked' : '' ?> data-check-box>
                  <label for="check-<?= $checkId ?>">Meets this check</label>
                </span>
                <button type="button" class="link-button needs-revision" data-needs-revision
                        data-for-check="check-<?= $checkId ?>">Needs revision</button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <button type="submit" class="button button-quiet button-small checks-fallback" data-checks-save>Save review</button>
      </form>
    </section>

    <section class="card card-pad wizard-card review-summary <?= $allConfirmed ? 'is-ready' : '' ?>" id="approve" data-review-summary>
      <h2>Approve or revise</h2>
      <ul class="summary-list">
        <li data-summary-confirmed><?= count($confirmed) ?> of <?= count($checks) ?> quality checks confirmed</li>
        <li>Reviewing v<?= (int) $latest['version_no'] ?><?= $latest['source'] === 'example' ? ' (sample data)' : '' ?></li>
        <li>
          <?= $isFinal
              ? 'This is the final Recipe — approving completes the workflow and opens export.'
              : 'Approving unlocks step ' . (int) $nextRecipe['position'] . ': ' . e($nextRecipe['title']) . '.' ?>
        </li>
        <?php foreach ($structuralWarnings as $warning): ?>
          <li class="summary-warning">
            <span class="evidence-mark" aria-hidden="true">&#8709;</span>
            <?= e($warning) ?> — advisory only; your confirmations decide.
          </li>
        <?php endforeach; ?>
      </ul>
      <form class="approve-form" method="post" action="<?= e($runBase . '/approve') ?>" data-loading>
        <?= Csrf::field() ?>
        <div class="wizard-actions">
          <button type="submit" class="button button-success button-large" data-approve-button <?= $allConfirmed ? '' : 'disabled' ?>>
            Approve this step
          </button>
          <button type="button" class="button button-ghost" data-open-options>Revise the response</button>
        </div>
        <p class="approve-note" data-approve-note>
          <?= $allConfirmed
              ? 'Everything is confirmed. Approving locks v' . (int) $latest['version_no'] . ' into your Project Kit' . (!$isFinal ? ' and unlocks the next step.' : ' and completes the workflow.')
              : 'The approve button wakes up when every check is confirmed.' ?>
        </p>
      </form>
    </section>

  <?php else: /* approved */ ?>

    <section class="card approved-card wizard-card rise-in">
      <div class="approved-banner">
        <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
        <div>
          <h1>Step approved</h1>
          <p>This result is locked into your Project Kit exactly as it reads below.
             <?= !$isFinal ? 'The next step builds on it from here.' : 'This was the final step — your project is complete.' ?></p>
        </div>
      </div>
      <div class="response-wrap">
        <div class="response-header">
          <span class="badge badge-sage badge-dot">Approved · v<?= (int) $viewing['version_no'] ?></span>
          <?php if ($viewing['source'] === 'example'): ?><span class="badge badge-sample">Sample data</span><?php endif; ?>
          <span class="version-meta"><?= e(time_ago((string) $viewing['created_at'])) ?></span>
        </div>
        <div class="response-body"><?= SafeText::render((string) $viewing['content']) ?></div>
      </div>
      <div class="approved-actions">
        <?php if (!$isFinal): ?>
          <a class="button button-primary button-large" href="<?= e(url('/projects/' . $projectId . '/run/' . $nextRecipe['position'])) ?>">
            Continue to step <?= (int) $nextRecipe['position'] ?>: <?= e($nextRecipe['title']) ?>
          </a>
        <?php else: ?>
          <a class="button button-primary button-large" href="<?= e(url('/projects/' . $projectId . '/export')) ?>">Export project</a>
        <?php endif; ?>
      </div>
    </section>

  <?php endif; ?>

  <?php if ($state !== 'gather' && !$isViewingOld): ?>
    <section class="card card-pad wizard-card response-options">
      <details class="disclose" id="response-options" data-response-options>
        <summary>Response options</summary>
        <div class="disclose-body">
          <p class="section-sub">Whatever you choose here, every saved version stays in the history untouched.</p>

          <?php if ($state === 'approved'): ?>
            <details class="revise-option">
              <summary>Reopen this approved Recipe</summary>
              <p class="field-help">Withdraws the approval so you can revise or re-review, then approve again.
                 Later Recipes that build on this one keep their own approvals.</p>
              <form method="post" action="<?= e($runBase . '/reopen') ?>"
                    data-confirm="Withdraw the approval for this Recipe? You can revise and approve it again.">
                <?= Csrf::field() ?>
                <button type="submit" class="button button-ghost button-small">Reopen for revision</button>
              </form>
            </details>
          <?php else: ?>
            <details class="revise-option" data-edit-option>
              <summary>Edit this response by hand</summary>
              <form method="post" action="<?= e($runBase . '/edit') ?>" data-loading>
                <?= Csrf::field() ?>
                <textarea class="textarea textarea-mono revise-textarea" name="content" rows="14" spellcheck="false"><?= e((string) $latest['content']) ?></textarea>
                <button type="submit" class="button button-primary button-small">Save as new version</button>
              </form>
            </details>
            <details class="revise-option">
              <summary>Paste a replacement response</summary>
              <p class="field-help">Tweak the prompt in your AI (ask for shorter, warmer, more direct) and paste the new take here.</p>
              <form method="post" action="<?= e($runBase . '/paste') ?>" data-loading>
                <?= Csrf::field() ?>
                <textarea class="textarea textarea-mono" name="content" rows="8"
                          placeholder="Paste the new response from your AI here"></textarea>
                <button type="submit" class="button button-primary button-small">Save as new version</button>
              </form>
            </details>
            <details class="revise-option">
              <summary>Copy the prompt again</summary>
              <div class="prompt-block">
                <div class="prompt-toolbar">
                  <span class="prompt-toolbar-label">prompt · <?= e($recipe['slug']) ?></span>
                  <button type="button" class="button button-small copy-button" data-copy-target="#prompt-text-again">
                    <span class="copy-label">Copy prompt</span>
                    <span class="copied-label">Copied &check;</span>
                  </button>
                </div>
                <pre id="prompt-text-again" tabindex="0"><?= $prompt['html'] ?></pre>
              </div>
            </details>
          <?php endif; ?>

          <details class="revise-option">
            <summary>Version history</summary>
            <p class="field-help">Every paste, edit, and restore is kept. Raw responses are never overwritten.</p>
            <ul class="version-list">
              <?php foreach ($versions as $v):
                  $isCurrentV = $latest !== null && (int) $v['id'] === (int) $latest['id'];
                  $isApprovedV = $artifact !== null && $artifact['approved_version_id'] !== null && (int) $artifact['approved_version_id'] === (int) $v['id'];
              ?>
                <li class="version-item <?= $isApprovedV ? 'is-approved' : ($isCurrentV ? 'is-current' : '') ?>">
                  <span class="version-num">v<?= (int) $v['version_no'] ?></span>
                  <span class="version-meta">
                    <?= e($sourceLabels[$v['source']] ?? $v['source']) ?> · <?= e(time_ago((string) $v['created_at'])) ?>
                    <?= $isApprovedV ? ' · approved' : ($isCurrentV ? ' · current' : '') ?>
                  </span>
                  <span class="version-spacer"></span>
                  <?php if (!$isCurrentV): ?>
                    <a class="version-view" href="<?= e($runBase . '?version=' . (int) $v['version_no']) ?>">View</a>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </details>
        </div>
      </details>
    </section>
  <?php endif; ?>

  </main>
</div>
