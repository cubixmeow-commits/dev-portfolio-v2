<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\View;
use Cadence\Models\Leaderboard;
use Cadence\Models\Participation;

final class LeaderboardController
{
    public function index(): void
    {
        $window = (string) ($_GET['window'] ?? 'week');
        if (!in_array($window, Leaderboard::WINDOWS, true)) {
            $window = 'week';
        }

        $rows = Leaderboard::global($window, 50);
        $rings = Participation::ringMap(array_column($rows, 'id'));

        // Pin the signed-in user's own rank when they are not on the board.
        $own = null;
        if (Auth::check()) {
            $userId = Auth::id();
            $visible = in_array($userId, array_map('intval', array_column($rows, 'id')), true);
            if (!$visible) {
                $own = Leaderboard::rankFor($userId, $window);
            }
        }

        View::render('leaderboard/index', [
            'title'    => 'Leaderboard',
            'active'   => 'leaderboard',
            'page_css' => 'community',
            'rows'     => $rows,
            'rings'    => $rings,
            'window'   => $window,
            'own'      => $own,
        ]);
    }
}
