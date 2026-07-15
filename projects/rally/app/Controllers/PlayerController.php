<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\View;
use Rally\Models\User;
use Rally\Services\ActivityFeedService;
use Rally\Services\PersonalRecordsService;

final class PlayerController
{
    public function show(string $id): void
    {
        Auth::requireLogin();
        $player = User::find((int) $id);
        if ($player === null || ($player['status'] ?? '') !== 'active') {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Player not found']);
            return;
        }

        $stats = User::derivedStats((int) $player['id']);
        $records = PersonalRecordsService::forUser((int) $player['id']);
        $feed = ActivityFeedService::forUser((int) $player['id'], 12);

        View::render('players/show', [
            'title' => $player['name'],
            'pageCss' => 'players',
            'player' => $player,
            'stats' => $stats,
            'records' => $records,
            'feed' => $feed,
            'initials' => User::initials((string) $player['name']),
        ]);
    }
}
