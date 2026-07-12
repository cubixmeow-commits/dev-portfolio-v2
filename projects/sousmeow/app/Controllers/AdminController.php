<?php

declare(strict_types=1);

namespace SousMeow\Controllers;

use SousMeow\Core\Auth;
use SousMeow\Core\Database;
use SousMeow\Core\View;
use SousMeow\Models\Artifact;
use SousMeow\Models\Export;
use SousMeow\Models\Project;
use SousMeow\Models\User;
use SousMeow\Services\SiteStats;

/**
 * Read-only admin overview. Admin accounts exist only via the CLI seed
 * script; the role check happens server side on every request.
 */
final class AdminController
{
    public function index(): void
    {
        Auth::requireAdmin();

        $recentProjects = Database::fetchAll(
            "SELECT p.id, p.title, p.created_at, p.completed_at, u.name AS user_name, u.simulation,
                    c.title AS cookbook_title,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS approved_count,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id) AS recipe_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             ORDER BY p.updated_at DESC LIMIT 12"
        );

        $sim = SiteStats::adminBundle();

        View::render('admin/index', [
            'title'          => 'Admin overview',
            'pageCss'        => 'admin',
            'stats'          => [
                'users'     => User::count(),
                'projects'  => Project::count(),
                'completed' => Project::completedCount(),
                'approved'  => Artifact::totalApproved(),
                'exports'   => Export::count(),
            ],
            'simulation'     => $sim,
            'recentUsers'    => User::recent(8),
            'recentProjects' => $recentProjects,
        ]);
    }
}
