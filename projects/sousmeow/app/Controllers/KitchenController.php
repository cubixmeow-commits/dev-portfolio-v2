<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\View;
use SousMeow\Models\Cookbook;
use SousMeow\Models\Project;

final class KitchenController
{
    public function index(): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();

        $projects = Project::listForUser($userId);

        // The most recently touched unfinished project gets the big
        // "continue" card; everything else lists below it.
        $continue = null;
        foreach ($projects as $project) {
            if ($project['completed_at'] === null) {
                $continue = $project;
                break;
            }
        }

        View::render('kitchen/index', [
            'title'    => 'My Kitchen',
            'pageCss'  => 'kitchen',
            'projects' => $projects,
            'continue' => $continue,
            'featured' => Cookbook::featured(),
        ]);
    }
}
