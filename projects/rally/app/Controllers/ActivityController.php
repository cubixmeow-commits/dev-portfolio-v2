<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\View;
use Rally\Services\ActivityFeedService;

final class ActivityController
{
    public function index(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        assert($userId !== null);

        View::render('activity/index', [
            'title' => 'Activity',
            'pageCss' => 'activity',
            'events' => ActivityFeedService::forUser($userId),
        ]);
    }
}
