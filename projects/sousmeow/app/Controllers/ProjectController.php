<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Database;
use SousMeow\Core\Flash;
use SousMeow\Core\View;
use SousMeow\Models\Artifact;
use SousMeow\Models\Cookbook;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;

final class ProjectController
{
    /** Start a new project from an executable cookbook. */
    public function create(): void
    {
        Auth::requireLogin();
        $slug = (string) ($_POST['cookbook'] ?? '');
        $cookbook = Cookbook::findBySlug($slug);

        if ($cookbook === null) {
            Flash::set('error', 'That Cookbook does not exist.');
            redirect('/marketplace');
        }
        // Server-side gate: presentation-only cookbooks can never start.
        if ((int) $cookbook['is_executable'] !== 1) {
            Flash::set('notice', $cookbook['title'] . ' is not open for cooking yet.');
            redirect('/cookbooks/' . $cookbook['slug']);
        }

        $id = Project::create((int) Auth::id(), (int) $cookbook['id'], (string) $cookbook['title']);
        Flash::set('success', 'Project started. First stop: stock your Pantry.');
        redirect('/projects/' . $id . '/pantry');
    }

    /** Send the user to wherever this project actually is. */
    public function show(string $id): void
    {
        $project = $this->own($id);
        $projectId = (int) $project['id'];

        if ($project['pantry_saved_at'] === null) {
            redirect('/projects/' . $projectId . '/pantry');
        }
        if ($project['completed_at'] !== null) {
            redirect('/projects/' . $projectId . '/export');
        }
        redirect('/projects/' . $projectId . '/run/' . $this->nextPosition($project));
    }

    public function pantry(string $id): void
    {
        $project = $this->own($id);
        $fields = PantryField::forCookbook((int) $project['cookbook_id']);
        $stored = Project::pantryValues((int) $project['id']);

        // Stored values keyed by field id, decoded for the form.
        $values = [];
        foreach ($fields as $field) {
            $raw = $stored[(int) $field['id']] ?? '';
            $values[(int) $field['id']] = $field['type'] === 'multiselect'
                ? (json_decode($raw, true) ?: [])
                : $raw;
        }

        // "Fill with sample Pantry": prefill the form (nothing saved)
        // with the seeded sample values, clearly marked in the view.
        $usingSample = isset($_GET['sample']) && $_GET['sample'] === '1';
        if ($usingSample) {
            foreach ($fields as $field) {
                $sample = (string) $field['sample_value'];
                $values[(int) $field['id']] = $field['type'] === 'multiselect'
                    ? array_map('trim', explode(',', $sample))
                    : $sample;
            }
        }

        View::render('projects/pantry', [
            'title'       => 'Pantry · ' . $project['title'],
            'pageCss'     => 'pantry',
            'project'     => $project,
            'fields'      => $fields,
            'values'      => $values,
            'errors'      => [],
            'usingSample' => $usingSample,
        ]);
    }

    public function savePantry(string $id): void
    {
        $project = $this->own($id);
        $projectId = (int) $project['id'];
        $fields = PantryField::forCookbook((int) $project['cookbook_id']);

        $errors = [];
        $toStore = [];   // field id => storage string
        $values = [];    // field id => form value for re-render

        foreach ($fields as $field) {
            $fieldId = (int) $field['id'];
            $key = 'field_' . $fieldId;
            $type = (string) $field['type'];
            $required = (int) $field['required'] === 1;
            $options = PantryField::options($field);

            if ($type === 'multiselect') {
                $picked = $_POST[$key] ?? [];
                $picked = is_array($picked) ? array_values(array_intersect(array_map('strval', $picked), $options)) : [];
                $values[$fieldId] = $picked;
                if ($required && $picked === []) {
                    $errors[$fieldId] = 'Pick at least one.';
                    continue;
                }
                $toStore[$fieldId] = (string) json_encode($picked);
                continue;
            }

            $value = trim((string) ($_POST[$key] ?? ''));
            $values[$fieldId] = $value;

            if ($value === '') {
                if ($required) {
                    $errors[$fieldId] = 'This ingredient is required.';
                } else {
                    $toStore[$fieldId] = '';
                }
                continue;
            }

            switch ($type) {
                case 'text':
                    if (mb_strlen($value) > 200) {
                        $errors[$fieldId] = 'Keep this under 200 characters.';
                    }
                    break;
                case 'textarea':
                    if (mb_strlen($value) > 5000) {
                        $errors[$fieldId] = 'Keep this under 5,000 characters.';
                    }
                    break;
                case 'select':
                    if (!in_array($value, $options, true)) {
                        $errors[$fieldId] = 'Pick one of the listed options.';
                    }
                    break;
                case 'number':
                    if (!is_numeric($value) || (float) $value < 0 || (float) $value > 1000000) {
                        $errors[$fieldId] = 'Enter a number between 0 and 1,000,000.';
                    } else {
                        $value = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
                    }
                    break;
                case 'url':
                    if (mb_strlen($value) > 300
                        || !filter_var($value, FILTER_VALIDATE_URL)
                        || !preg_match('#^https?://#i', $value)) {
                        $errors[$fieldId] = 'Enter a full URL starting with https://';
                    }
                    break;
            }

            if (!isset($errors[$fieldId])) {
                $toStore[$fieldId] = $value;
            }
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('projects/pantry', [
                'title'       => 'Pantry · ' . $project['title'],
                'pageCss'     => 'pantry',
                'project'     => $project,
                'fields'      => $fields,
                'values'      => $values,
                'errors'      => $errors,
                'usingSample' => false,
            ]);
            return;
        }

        $firstSave = $project['pantry_saved_at'] === null;
        Project::savePantry($projectId, $toStore);
        Project::markPantrySaved($projectId);

        // Name the project after the product when the cookbook has a
        // product_name ingredient; "Driftlog" beats "Launch Day Kit #2".
        foreach ($fields as $field) {
            if ($field['field_key'] === 'product_name' && trim((string) ($toStore[(int) $field['id']] ?? '')) !== '') {
                Database::run('UPDATE projects SET title = ? WHERE id = ?', [
                    trim((string) $toStore[(int) $field['id']]),
                    $projectId,
                ]);
                break;
            }
        }

        if ($firstSave) {
            Flash::set('success', 'Pantry stocked. Time to cook the first Recipe.');
            redirect('/projects/' . $projectId . '/run/1');
        }
        Flash::set('success', 'Pantry updated. New prompts will use the fresh ingredients.');
        redirect('/projects/' . $projectId . '/run/' . $this->nextPosition(Project::findForUser($projectId, (int) Auth::id()) ?? $project));
    }

    public function delete(string $id): void
    {
        $project = $this->own($id);
        Project::delete((int) $project['id']);
        Flash::set('notice', 'Project deleted. Its exports were kept on disk but are no longer downloadable.');
        redirect('/kitchen');
    }

    /**
     * Load the project or stop the request: 404 when it does not exist
     * for this user (never reveals other users' project ids).
     *
     * @return array<string, mixed>
     */
    private function own(string $id): array
    {
        Auth::requireLogin();
        $project = Project::findForUser((int) $id, (int) Auth::id());
        if ($project === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Project not found']);
            exit;
        }
        return $project;
    }

    /** First recipe position without an approved artifact. */
    private function nextPosition(array $project): int
    {
        $recipes = Recipe::forCookbook((int) $project['cookbook_id']);
        $statuses = Artifact::statusByRecipe((int) $project['id']);
        foreach ($recipes as $recipe) {
            if (($statuses[(int) $recipe['id']] ?? '') !== 'approved') {
                return (int) $recipe['position'];
            }
        }
        return count($recipes) > 0 ? (int) end($recipes)['position'] : 1;
    }
}
