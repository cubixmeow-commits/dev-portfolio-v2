<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Config;
use SousMeow\Core\Flash;
use SousMeow\Core\View;
use SousMeow\Models\Artifact;
use SousMeow\Models\Cookbook;
use SousMeow\Models\Export;
use SousMeow\Models\Project;
use SousMeow\Models\Recipe;
use SousMeow\Services\ProjectKit;

final class ExportController
{
    /** The export page: readiness, history, and honest empty states. */
    public function show(string $id): void
    {
        $project = $this->own($id);
        $projectId = (int) $project['id'];

        $recipes = Recipe::forCookbook((int) $project['cookbook_id']);
        $statuses = Artifact::statusByRecipe($projectId);
        $approved = Artifact::approvedByRecipe($projectId);
        $ready = count($approved) >= count($recipes) && count($recipes) > 0;

        View::render('projects/export', [
            'title'    => 'Project Kit · ' . $project['title'],
            'pageCss'  => 'export',
            'project'  => $project,
            'recipes'  => $recipes,
            'statuses' => $statuses,
            'approved' => $approved,
            'ready'    => $ready,
            'exports'  => Export::forProject($projectId),
        ]);
    }

    /** Build a fresh kit zip. */
    public function create(string $id): void
    {
        Auth::requireVerified();
        $project = $this->own($id);
        $projectId = (int) $project['id'];

        $recipes = Recipe::forCookbook((int) $project['cookbook_id']);
        $approved = Artifact::approvedByRecipe($projectId);
        if (count($recipes) === 0 || count($approved) < count($recipes)) {
            Flash::set('notice', 'Export is available once every step is approved.');
            redirect('/projects/' . $projectId . '/export');
        }

        $cookbook = Cookbook::find((int) $project['cookbook_id']) ?? [];
        $kit = ProjectKit::build($project, $cookbook);
        $exportId = Export::record($projectId, $kit['filename'], $kit['size'], $kit['count']);
        Project::touch($projectId);

        Flash::set('celebrate', 'Project exported: ' . $kit['count'] . ' files, kit.html reader, and a manifest. Download it below.');
        redirect('/projects/' . $projectId . '/export#export-' . $exportId);
    }

    /** Stream a previously built kit to its owner. */
    public function download(string $id): void
    {
        Auth::requireVerified();
        $export = Export::findForUser((int) $id, (int) Auth::id());
        if ($export === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Export not found']);
            return;
        }

        // Filenames are generated server side, but re-anchor to the
        // exports directory anyway before touching the filesystem.
        $dir = rtrim(Config::string('exports.dir'), '/');
        $path = $dir . '/' . basename((string) $export['filename']);
        if (!is_file($path)) {
            Flash::set('error', 'That export file is no longer on disk. Build a fresh one below.');
            redirect('/projects/' . $export['project_id'] . '/export');
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }

    /** @return array<string, mixed> */
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
}
