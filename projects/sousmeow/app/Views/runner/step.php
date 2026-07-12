<?php
use SousMeow\Core\Csrf;
use SousMeow\Services\SafeText;

/**
 * The Recipe Runner. Three states:
 *  - gather:   no response yet (copy prompt, run, paste)
 *  - review:   a response exists and awaits Quality Checks + approval
 *  - approved: this Recipe is locked into the kit
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
 * @var string                     $state
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

$nextRecipe = null;
foreach ($recipes as $r) {
    if ((int) $r['position'] === $position + 1) {
        $nextRecipe = $r;
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

  <header class="runner-rail card" aria-label="Cookbook progress">
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
               <?= $isCurrent ? 'aria-current="step"' : '' ?>>
              <span class="step-dot <?= $isDone ? 'is-done' : ($isCurrent ? 'is-active' : '') ?>"><?= $isDone ? '&check;' : (int) $r['position'] ?></span>
              <span class="rail-title"><?= e($r['title']) ?></span>
            </a>
          <?php else: ?>
            <span class="rail-link is-locked" title="Unlocks when the previous Recipe is approved">
              <span class="step-dot is-locked"><?= (int) $r['position'] ?></span>
              <span class="rail-title"><?= e($r['title']) ?></span>
            </span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      <li class="rail-step rail-step-kit">
        <?php if ($approvedCount >= $total): ?>
          <a class="rail-link" href="<?= e(url('/projects/' . $projectId . '/export')) ?>">
            <span class="step-dot is-done" aria-hidden="true">&#8681;</span>
            <span class="rail-title">Project Kit</span>
          </a>
        <?php else: ?>
          <span class="rail-link is-locked" title="Unlocks when every Recipe is approved">
            <span class="step-dot is-locked" aria-hidden="true">&#8681;</span>
            <span class="rail-title">Project Kit</span>
          </span>
        <?php endif; ?>
      </li>
    </ol>
    <p class="rail-summary"><?= $approvedCount ?> of <?= $total ?> Recipes approved. Approved work goes into your exported Project Kit.</p>
  </header>

  <div class="runner-grid">
    <main class="runner-main">

      <section class="card card-pad recipe-intro rise-in">
        <p class="recipe-eyebrow">Recipe <?= $position ?> of <?= $total ?> · about <?= (int) $recipe['est_minutes'] ?> min</p>
        <h1><?= e($recipe['title']) ?></h1>
        <p class="recipe-summary"><?= e($recipe['summary']) ?></p>
        <div class="recipe-why well">
          <p><strong>Why this step matters:</strong> <?= e($recipe['why_it_matters']) ?></p>
          <?php if ($recipe['unlocks_text'] !== ''): ?><p class="recipe-unlocks"><?= e($recipe['unlocks_text']) ?></p><?php endif; ?>
        </div>
      </section>

      <?php if ($state === 'approved' && !$isViewingOld): ?>
        <section class="card approved-card rise-in">
          <div class="approved-banner">
            <?php \SousMeow\Core\View::partial('partials/mascot', ['pose' => 'cheering']); ?>
            <div>
              <h2>Approved and in the kit</h2>
              <p>This Recipe's result is locked into your Project Kit exactly as shown below.
                 <?= $nextRecipe !== null ? 'The next Recipe cooks with it.' : 'It was the final Recipe.' ?></p>
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
            <?php if ($nextRecipe !== null): ?>
              <a class="button button-primary button-large" href="<?= e(url('/projects/' . $projectId . '/run/' . $nextRecipe['position'])) ?>">
                Continue to Recipe <?= (int) $nextRecipe['position'] ?>: <?= e($nextRecipe['title']) ?>
              </a>
            <?php else: ?>
              <a class="button button-primary button-large" href="<?= e(url('/projects/' . $projectId . '/export')) ?>">Export your Project Kit</a>
            <?php endif; ?>
            <form method="post" action="<?= e($runBase . '/reopen') ?>"
                  data-confirm="Withdraw the approval for this Recipe? You can revise and approve it again.">
              <?= Csrf::field() ?>
              <button type="submit" class="button button-ghost">Revise this Recipe</button>
            </form>
          </div>
        </section>

      <?php elseif ($state === 'review' || $isViewingOld): ?>

        <section class="card response-card rise-in">
          <div class="response-wrap">
            <div class="response-header">
              <span class="badge <?= $isViewingOld ? 'badge-lilac' : 'badge-terracotta' ?>">
                v<?= (int) $viewing['version_no'] ?> · <?= e($sourceLabels[$viewing['source']] ?? $viewing['source']) ?>
              </span>
              <?php if ($viewing['source'] === 'example'): ?><span class="badge badge-sample">Sample data</span><?php endif; ?>
              <span class="version-meta"><?= e(time_ago((string) $viewing['created_at'])) ?></span>
            </div>
            <?php if ($isViewingOld): ?>
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
            <?php endif; ?>
            <div class="response-body"><?= SafeText::render((string) $viewing['content']) ?></div>
          </div>
        </section>

        <?php if (!$isViewingOld): ?>
          <section class="card card-pad revise-card">
            <details class="revise-details">
              <summary>Not quite right? Revise it</summary>
              <div class="revise-body">
                <p class="section-sub">Whatever you choose, the current text stays in the version history untouched.</p>
                <details class="revise-option">
                  <summary>Edit this response by hand</summary>
                  <form method="post" action="<?= e($runBase . '/edit') ?>" data-loading>
                    <?= Csrf::field() ?>
                    <textarea class="textarea textarea-mono revise-textarea" name="content" rows="14" spellcheck="false"><?= e((string) $latest['content']) ?></textarea>
                    <button type="submit" class="button button-primary button-small">Save as new version</button>
                  </form>
                </details>
                <details class="revise-option">
                  <summary>Paste a different response</summary>
                  <p class="field-help">Tweak the prompt in your AI (ask for shorter, warmer, more direct) and paste the new take here.</p>
                  <form method="post" action="<?= e($runBase . '/paste') ?>" data-loading>
                    <?= Csrf::field() ?>
                    <textarea class="textarea textarea-mono" name="content" rows="8"
                              placeholder="Paste the new response from your AI here"></textarea>
                    <button type="submit" class="button button-primary button-small">Save as new version</button>
                  </form>
                </details>
              </div>
            </details>
          </section>

          <section class="card card-pad checks-card" id="quality-checks">
            <div class="section-heading">
              <h2>Quality Checks</h2>
              <span class="checks-counter badge <?= $allConfirmed ? 'badge-sage' : 'badge-neutral' ?>" data-checks-counter
                    data-confirmed="<?= count($confirmed) ?>" data-total="<?= count($checks) ?>">
                <?= count($confirmed) ?> of <?= count($checks) ?> confirmed
              </span>
            </div>
            <p class="section-sub checks-lede">
              SousMeow never grades text for you; you are the chef who tastes the dish. Each box records
              <em>your</em> confirmation against this exact version. Editing the text unchecks everything, on purpose.
            </p>
            <form method="post" action="<?= e($runBase . '/checks') ?>" data-checks-form>
              <?= Csrf::field() ?>
              <ul class="checklist">
                <?php foreach ($checks as $check): $isChecked = in_array((int) $check['id'], $confirmed, true); ?>
                  <li class="check-item <?= $isChecked ? 'is-checked' : '' ?>">
                    <input type="checkbox" id="check-<?= (int) $check['id'] ?>" name="checks[]"
                           value="<?= (int) $check['id'] ?>" <?= $isChecked ? 'checked' : '' ?> data-check-box>
                    <label class="check-text" for="check-<?= (int) $check['id'] ?>">
                      <span class="check-label"><?= e($check['label']) ?></span>
                      <span class="check-help"><?= e($check['help']) ?></span>
                    </label>
                  </li>
                <?php endforeach; ?>
              </ul>
              <button type="submit" class="button button-quiet button-small checks-fallback" data-checks-save>Save review</button>
            </form>
            <form class="approve-form" method="post" action="<?= e($runBase . '/approve') ?>" data-loading>
              <?= Csrf::field() ?>
              <button type="submit" class="button button-success button-large" data-approve-button <?= $allConfirmed ? '' : 'disabled' ?>>
                Approve and lock into the kit
              </button>
              <p class="approve-note" data-approve-note>
                <?= $allConfirmed
                    ? 'Approving locks v' . (int) $latest['version_no'] . ' into your Project Kit' . ($nextRecipe !== null ? ' and unlocks the next Recipe.' : ' and completes the Cookbook.')
                    : 'Confirm every check to approve. ' . ($recipe['unlocks_text'] !== '' ? e($recipe['unlocks_text']) : '') ?>
              </p>
            </form>
          </section>
        <?php endif; ?>

      <?php else: /* gather */ ?>

        <section class="card card-pad prompt-card rise-in">
          <div class="step-flag"><span class="step-dot is-active">1</span><h2>Copy this prompt</h2></div>
          <p class="section-sub">
            Built from your Pantry just now; the <span class="ingredient-swatch">highlighted parts</span> are your
            ingredients, so you can see exactly what information is used. Nothing else is sent anywhere.
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
        </section>

        <section class="card card-pad run-card">
          <div class="step-flag"><span class="step-dot is-active">2</span><h2>Run it in your own AI</h2></div>
          <p class="section-sub">
            Open the assistant you already use (Claude, ChatGPT, Gemini, anything that chats), paste the prompt,
            and let it cook. SousMeow never calls an AI itself: your subscription, your data, your model choice.
          </p>
        </section>

        <section class="card card-pad paste-card">
          <div class="step-flag"><span class="step-dot is-active">3</span><h2>Paste the response back</h2></div>
          <p class="section-sub">
            Paste the whole answer, formatting and all. It is saved exactly as pasted (version 1) and you review
            it next; nothing goes into your kit unapproved.
          </p>
          <form method="post" action="<?= e($runBase . '/paste') ?>" data-loading>
            <?= Csrf::field() ?>
            <textarea class="textarea textarea-mono paste-textarea" name="content" rows="10" required
                      placeholder="Paste your AI's full response here" aria-label="AI response"></textarea>
            <button type="submit" class="button button-primary button-large">Save response and review it</button>
          </form>
          <div class="demo-divider" role="separator"><span>no AI handy?</span></div>
          <div class="demo-row">
            <form method="post" action="<?= e($runBase . '/example') ?>" data-loading>
              <?= Csrf::field() ?>
              <button type="submit" class="button button-ghost">Paste example response</button>
            </form>
            <p class="demo-note">
              <span class="badge badge-sample">Sample data</span>
              A realistic response for a fictional product (Driftlog), so you can walk the whole loop right now.
              It stays marked as sample data everywhere it appears.
            </p>
          </div>
        </section>
      <?php endif; ?>
    </main>

    <aside class="runner-side">
      <section class="card card-pad side-card">
        <h3>Ingredients used</h3>
        <p class="side-sub">What this Recipe's prompt is built from.</p>
        <?php if ($ingredients === []): ?>
          <p class="side-sub">This Recipe uses earlier approved Recipes rather than Pantry fields directly.</p>
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
      </section>

      <section class="card card-pad side-card">
        <h3>Version history</h3>
        <?php if ($versions === []): ?>
          <p class="side-sub">No versions yet. The first response you paste becomes v1, kept forever exactly as pasted;
             edits and re-pastes stack on top as v2, v3, and so on.</p>
        <?php else: ?>
          <p class="side-sub">Every paste, edit, and restore is kept. Raw responses are never overwritten.</p>
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
        <?php endif; ?>
      </section>

      <section class="card card-pad side-card loop-reminder">
        <h3>Where this is going</h3>
        <p class="side-sub">Approve all <?= $total ?> Recipes and this Project exports as a Project Kit: a zip of tidy
           Markdown files (one per Recipe) plus a manifest of your Pantry. Yours to publish anywhere.</p>
      </section>
    </aside>
  </div>
</div>
