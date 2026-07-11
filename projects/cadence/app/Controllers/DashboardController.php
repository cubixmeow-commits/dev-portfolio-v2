<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\View;

final class DashboardController
{
    /**
     * Root route: marketing page for visitors, the Today dashboard for
     * signed-in members.
     */
    public function home(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        View::render('marketing/home', ['title' => '']);
    }

    public function index(): void
    {
        $user = Auth::requireUser();
        View::render('dashboard/index', [
            'title'  => 'Today',
            'active' => 'dashboard',
            'user'   => $user,
        ]);
    }
}
