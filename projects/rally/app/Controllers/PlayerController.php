<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\View;
use Rally\Models\User;

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

        View::render('players/show', [
            'title' => $player['name'],
            'pageCss' => 'players',
            'player' => $player,
            'stats' => $stats,
            'initials' => User::initials((string) $player['name']),
        ]);
    }
}
