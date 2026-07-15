<?php

declare(strict_types=1);

/**
 * CLI smoke test: Career Collection Cookbooks end-to-end without a browser.
 * For each Career Cookbook: create a project, fill pantry sample values,
 * paste each example_response, confirm checks, approve, then export kit.
 *
 * Usage: php scripts/verify-career-collection.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once __DIR__ . '/../database/seeds/career_helpers.php';
require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;
use SousMeow\Models\Artifact;
use SousMeow\Models\Cookbook;
use SousMeow\Models\PantryField;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;
use SousMeow\Services\ProjectKit;
use SousMeow\Services\PromptBuilder;
use SousMeow\Services\ResponseParser;

function fail(string $message): never
{
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

$userId = (int) Database::fetchValue('SELECT id FROM users LIMIT 1');
if ($userId < 1) {
    fail('No user available for smoke test');
}

foreach (sm_career_collection_slugs() as $slug) {
    $cookbook = Cookbook::findBySlug($slug);
    if ($cookbook === null || (int) $cookbook['is_executable'] !== 1) {
        fail("{$slug} missing or not executable");
    }

    $projectId = Project::create($userId, (int) $cookbook['id'], 'Career QA ' . $slug);
    $fields = PantryField::forCookbook((int) $cookbook['id']);
    $values = [];
    foreach ($fields as $field) {
        $sample = (string) $field['sample_value'];
        if ($field['type'] === 'multiselect') {
            $picked = array_map('trim', explode(',', $sample));
            $values[(int) $field['id']] = json_encode($picked);
        } else {
            $values[(int) $field['id']] = $sample;
        }
    }
    Project::savePantry($projectId, $values);
    Project::markPantrySaved($projectId);

    $project = Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]);
    if ($project === null) {
        fail("{$slug}: project missing after create");
    }

    $recipes = Recipe::forCookbook((int) $cookbook['id']);
    if ($recipes === []) {
        fail("{$slug}: no recipes");
    }

    foreach ($recipes as $recipe) {
        $recipeId = (int) $recipe['id'];
        $prompt = PromptBuilder::build($recipe, $project);
        foreach ($prompt['missing'] as $missing) {
            if (!str_starts_with($missing, 'artifact:')) {
                fail("{$slug}/{$recipe['slug']}: unresolved pantry {$missing}");
            }
        }
        if (preg_match('/\{\{[a-z0-9_:\-]+\}\}/i', $prompt['text'])) {
            fail("{$slug}/{$recipe['slug']}: raw placeholder left in prompt");
        }

        foreach (['before_you_begin', 'common_problems', 'recovery_guidance'] as $key) {
            if (trim((string) ($recipe[$key] ?? '')) === '') {
                fail("{$slug}/{$recipe['slug']}: empty {$key}");
            }
        }

        $example = (string) ($recipe['example_response'] ?? '');
        if ($example === '') {
            fail("{$slug}/{$recipe['slug']}: missing example_response");
        }

        $artifactId = Artifact::addVersion($projectId, $recipeId, $example, 'example');
        $checks = Recipe::checks($recipeId);
        $contract = Recipe::outputContract($recipe);
        $latest = Artifact::latestVersion($artifactId);
        if ($latest === null) {
            fail("{$slug}/{$recipe['slug']}: no latest version");
        }
        $versionId = (int) $latest['id'];
        if ($contract !== null) {
            $parsed = ResponseParser::parse((string) $latest['content'], $contract);
            if ($parsed['missing_required'] !== []) {
                fail("{$slug}/{$recipe['slug']}: example missing sections: " . implode(', ', $parsed['missing_required']));
            }
        }
        foreach ($checks as $check) {
            Artifact::setCheck($versionId, (int) $check['id'], true);
        }
        Artifact::approve($artifactId, $versionId);

        $project = Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]) ?? $project;
    }

    $last = $recipes[array_key_last($recipes)];
    $prompt = PromptBuilder::build($last, Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]) ?? $project);
    foreach ($prompt['missing'] as $missing) {
        fail("{$slug}/{$last['slug']}: still missing after approvals: {$missing}");
    }

    if (!Project::markCompleteIfDone($projectId)) {
        fail("{$slug}: project not marked complete");
    }

    $approved = Artifact::approvedByRecipe($projectId);
    if (count($approved) !== count($recipes)) {
        fail("{$slug}: expected " . count($recipes) . " approved artifacts, got " . count($approved));
    }

    if (class_exists(ZipArchive::class)) {
        $project = Database::fetch('SELECT * FROM projects WHERE id = ?', [$projectId]);
        $result = ProjectKit::build($project, $cookbook);
        if (!is_file($result['path'])) {
            fail("{$slug}: export zip missing");
        }
        $zip = new ZipArchive();
        if ($zip->open($result['path']) !== true) {
            fail("{$slug}: cannot open zip");
        }
        $expected = count($recipes) + 2;
        if ($zip->numFiles < $expected) {
            fail("{$slug}: zip has {$zip->numFiles} files, expected at least {$expected}");
        }
        if ($zip->locateName('kit.html') === false) {
            fail("{$slug}: kit.html missing");
        }
        $zip->close();
        echo "[OK] {$slug}: " . count($recipes) . " recipes approved, zip ok\n";
    } else {
        echo "[OK] {$slug}: " . count($recipes) . " recipes approved (zip skipped: ZipArchive unavailable)\n";
    }
}

echo "Career Collection verification passed.\n";
