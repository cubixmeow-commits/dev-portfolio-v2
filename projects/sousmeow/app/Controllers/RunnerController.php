<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Flash;
use SousMeow\Core\View;
use SousMeow\Models\Artifact;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;
use SousMeow\Services\PromptBuilder;

/**
 * The Recipe Runner: the product's core loop. Each step renders one of
 * three states (gather, review, approved) and every transition is
 * validated server side: recipe order, version immutability, and the
 * all-checks-confirmed gate on approval.
 */
final class RunnerController
{
    public function step(string $id, string $position): void
    {
        [$project, $recipe, $recipes] = $this->load($id, $position);
        $projectId = (int) $project['id'];
        $recipeId = (int) $recipe['id'];

        $artifact = Artifact::find($projectId, $recipeId);
        $versions = $artifact !== null ? Artifact::versions((int) $artifact['id']) : [];
        $latest = $artifact !== null ? Artifact::latestVersion((int) $artifact['id']) : null;

        // Optionally view an older, immutable version (read-only).
        $viewing = $latest;
        if ($artifact !== null && isset($_GET['version'])) {
            $requested = Artifact::version((int) $artifact['id'], (int) $_GET['version']);
            if ($requested !== null) {
                $viewing = $requested;
            }
        }

        $checks = Recipe::checks($recipeId);
        $confirmed = ($latest !== null) ? Artifact::confirmedCheckIds((int) $latest['id']) : [];

        $state = 'gather';
        if ($artifact !== null && $artifact['status'] === 'approved') {
            $state = 'approved';
        } elseif ($latest !== null) {
            $state = 'review';
        }

        $prompt = PromptBuilder::build($recipe, $project);
        $statuses = Artifact::statusByRecipe($projectId);
        $approvedCount = Artifact::approvedCount($projectId);

        // Pantry summary for the "ingredients used" panel.
        $fields = PantryField::forCookbook((int) $project['cookbook_id']);
        $values = Project::pantryValues($projectId);
        $ingredients = [];
        foreach ($fields as $field) {
            if (str_contains((string) $recipe['prompt_template'], '{{' . $field['field_key'] . '}}')) {
                $raw = trim($values[(int) $field['id']] ?? '');
                if ($field['type'] === 'multiselect') {
                    $decoded = json_decode($raw, true);
                    $raw = is_array($decoded) ? implode(', ', $decoded) : $raw;
                }
                // Multi-line values read as one summary line in the panel.
                $raw = implode(' · ', array_filter(array_map('trim', explode("\n", $raw))));
                $ingredients[] = ['label' => $field['label'], 'value' => $raw];
            }
        }

        View::render('runner/step', [
            'title'        => $recipe['title'] . ' · ' . $project['title'],
            'pageCss'      => 'runner',
            'pageJs'       => 'runner',
            'bodyClass'    => 'runner-body',
            'project'      => $project,
            'recipe'       => $recipe,
            'recipes'      => $recipes,
            'statuses'     => $statuses,
            'artifact'     => $artifact,
            'versions'     => $versions,
            'latest'       => $latest,
            'viewing'      => $viewing,
            'checks'       => $checks,
            'confirmed'    => $confirmed,
            'state'        => $state,
            'prompt'       => $prompt,
            'ingredients'  => $ingredients,
            'approvedCount' => $approvedCount,
        ]);
    }

    /** Store a pasted AI response as a new immutable version. */
    public function paste(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $content = $this->cleanContent((string) ($_POST['content'] ?? ''));

        if ($content === null) {
            Flash::set('error', 'Paste the full response from your AI first; it looks empty or too short to review.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        Artifact::addVersion((int) $project['id'], (int) $recipe['id'], $content, 'pasted');
        Project::touch((int) $project['id']);
        Flash::set('success', 'Response saved. Nothing enters your kit unreviewed, so walk the Quality Checks below.');
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }

    /** Demo Mode: paste the recipe's seeded example response. */
    public function pasteExample(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $example = (string) ($recipe['example_response'] ?? '');
        if (trim($example) === '') {
            Flash::set('error', 'This Recipe has no example response.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }
        Artifact::addVersion((int) $project['id'], (int) $recipe['id'], $example, 'example');
        Project::touch((int) $project['id']);
        Flash::set('success', 'Example response pasted and marked as sample data. Review it exactly like a real one.');
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }

    /**
     * Confirm or unconfirm Quality Checks. Accepts a single toggle from
     * fetch() (check_id + confirmed) answered as JSON, or the plain
     * form fallback (checks[] of confirmed ids) answered by redirect.
     * Confirmations always target the latest version: checking a box
     * means "I read THIS text".
     */
    public function toggleCheck(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $artifact = Artifact::find((int) $project['id'], (int) $recipe['id']);
        $latest = $artifact !== null ? Artifact::latestVersion((int) $artifact['id']) : null;
        if ($artifact === null || $latest === null) {
            $this->checkResponse($project, $position, false, 'Paste a response before reviewing.');
        }

        $checks = Recipe::checks((int) $recipe['id']);
        $validIds = array_map(static fn(array $c): int => (int) $c['id'], $checks);
        $versionId = (int) $latest['id'];

        if (isset($_POST['check_id'])) {
            $checkId = (int) $_POST['check_id'];
            if (!in_array($checkId, $validIds, true)) {
                $this->checkResponse($project, $position, false, 'Unknown check.');
            }
            Artifact::setCheck($versionId, $checkId, ($_POST['confirmed'] ?? '') === '1');
        } else {
            $picked = $_POST['checks'] ?? [];
            $picked = is_array($picked) ? array_map('intval', $picked) : [];
            foreach ($validIds as $checkId) {
                Artifact::setCheck($versionId, $checkId, in_array($checkId, $picked, true));
            }
        }

        Project::touch((int) $project['id']);
        $confirmedCount = count(Artifact::confirmedCheckIds($versionId));
        $this->checkResponse($project, $position, true, null, $confirmedCount, count($validIds));
    }

    /** Approve the latest version; all checks must be confirmed. */
    public function approve(string $id, string $position): void
    {
        [$project, $recipe, $recipes] = $this->load($id, $position);
        $projectId = (int) $project['id'];
        $artifact = Artifact::find($projectId, (int) $recipe['id']);
        $latest = $artifact !== null ? Artifact::latestVersion((int) $artifact['id']) : null;

        if ($artifact === null || $latest === null) {
            Flash::set('error', 'There is nothing to approve yet; paste a response first.');
            redirect('/projects/' . $projectId . '/run/' . $position);
        }

        $checks = Recipe::checks((int) $recipe['id']);
        $confirmed = Artifact::confirmedCheckIds((int) $latest['id']);
        if (count($confirmed) < count($checks)) {
            Flash::set('notice', 'Walk every Quality Check first. They are your judgement, recorded; SousMeow never grades the text for you.');
            redirect('/projects/' . $projectId . '/run/' . $position);
        }

        Artifact::approve((int) $artifact['id'], (int) $latest['id']);
        Project::touch($projectId);

        if (Project::markCompleteIfDone($projectId)) {
            Flash::set('celebrate', 'Workflow complete! Every step approved. Export your Project Kit below.');
            redirect('/projects/' . $projectId . '/export');
        }

        $next = (int) $recipe['position'] + 1;
        $hasNext = false;
        foreach ($recipes as $r) {
            if ((int) $r['position'] === $next) {
                $hasNext = true;
                break;
            }
        }
        if ($hasNext) {
            Flash::set('celebrate', $recipe['title'] . ' is approved and locked in. The next step is ready when you are.');
            redirect('/projects/' . $projectId . '/run/' . $next);
        }
        redirect('/projects/' . $projectId . '/export');
    }

    /** Withdraw an approval to keep working on this artifact. */
    public function reopen(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $artifact = Artifact::find((int) $project['id'], (int) $recipe['id']);
        if ($artifact === null) {
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }
        Artifact::reopen((int) $artifact['id']);
        Project::reopen((int) $project['id']);
        Project::touch((int) $project['id']);
        Flash::set('notice', 'Approval withdrawn. Revise or re-review, then approve again.');
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }

    /** Save an edited copy as a new version; the original is untouched. */
    public function saveEdit(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $artifact = Artifact::find((int) $project['id'], (int) $recipe['id']);
        if ($artifact === null) {
            Flash::set('error', 'Paste a response before editing.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        $content = $this->cleanContent((string) ($_POST['content'] ?? ''));
        if ($content === null) {
            Flash::set('error', 'The edited response looks empty. Your previous version is untouched.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        $latest = Artifact::latestVersion((int) $artifact['id']);
        if ($latest !== null && $latest['content'] === $content) {
            Flash::set('notice', 'No changes to save; the text matches the current version.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        Artifact::addVersion((int) $project['id'], (int) $recipe['id'], $content, 'edited');
        Project::touch((int) $project['id']);
        Flash::set('success', 'Saved as a new version. The exact original is kept in the history below.');
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }

    /** Bring an older version back as a new version (history intact). */
    public function restore(string $id, string $position): void
    {
        [$project, $recipe] = $this->load($id, $position);
        $artifact = Artifact::find((int) $project['id'], (int) $recipe['id']);
        $versionNo = (int) ($_POST['version_no'] ?? 0);
        $version = $artifact !== null ? Artifact::version((int) $artifact['id'], $versionNo) : null;

        if ($version === null) {
            Flash::set('error', 'That version was not found.');
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        $latest = Artifact::latestVersion((int) $artifact['id']);
        if ($latest !== null && (int) $latest['version_no'] === $versionNo) {
            redirect('/projects/' . $project['id'] . '/run/' . $position);
        }

        Artifact::addVersion((int) $project['id'], (int) $recipe['id'], (string) $version['content'], 'restored');
        Project::touch((int) $project['id']);
        Flash::set('success', 'Version ' . $versionNo . ' restored as the newest version. Re-run the Quality Checks against it.');
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }

    /**
     * Shared loading and authorization: project ownership, pantry
     * completeness, recipe existence, and strict step order.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>, 2: list<array<string, mixed>>}
     */
    private function load(string $id, string $position): array
    {
        Auth::requireLogin();
        $project = Project::findForUser((int) $id, (int) Auth::id());
        if ($project === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Project not found']);
            exit;
        }
        if ($project['pantry_saved_at'] === null) {
            Flash::set('notice', 'Add your project details first — every prompt is built from them.');
            redirect('/projects/' . $project['id'] . '/pantry');
        }

        $recipes = Recipe::forCookbook((int) $project['cookbook_id']);
        $recipe = null;
        foreach ($recipes as $r) {
            if ((int) $r['position'] === (int) $position) {
                $recipe = $r;
                break;
            }
        }
        if ($recipe === null || $recipe['prompt_template'] === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Recipe not found']);
            exit;
        }

        // Recipes unlock strictly in order: every earlier recipe must
        // hold an approved artifact.
        $statuses = Artifact::statusByRecipe((int) $project['id']);
        foreach ($recipes as $r) {
            if ((int) $r['position'] >= (int) $recipe['position']) {
                break;
            }
            if (($statuses[(int) $r['id']] ?? '') !== 'approved') {
                Flash::set('notice', 'Steps unlock in order. Finish "' . $r['title'] . '" first; later steps build on its approved result.');
                redirect('/projects/' . $project['id'] . '/run/' . $r['position']);
            }
        }

        return [$project, $recipe, $recipes];
    }

    /** Normalize pasted content; null when unusable. */
    private function cleanContent(string $raw): ?string
    {
        $content = str_replace(["\r\n", "\r"], "\n", $raw);
        // Strip control characters except newline and tab; pasted AI
        // output is untrusted input.
        $content = (string) preg_replace('/[^\P{C}\n\t]/u', '', $content);
        $content = trim($content);
        if (mb_strlen($content) < 20 || mb_strlen($content) > 200000) {
            return null;
        }
        return $content;
    }

    /** Answer a check toggle: JSON for fetch, redirect for plain forms. */
    private function checkResponse(array $project, string $position, bool $ok, ?string $error = null, int $confirmedCount = 0, int $total = 0): never
    {
        $wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
        if ($wantsJson) {
            if (!$ok) {
                json_response(['ok' => false, 'error' => $error], 422);
            }
            json_response([
                'ok'          => true,
                'confirmed'   => $confirmedCount,
                'total'       => $total,
                'can_approve' => $confirmedCount >= $total,
            ]);
        }
        if (!$ok && $error !== null) {
            Flash::set('error', $error);
        }
        redirect('/projects/' . $project['id'] . '/run/' . $position);
    }
}
