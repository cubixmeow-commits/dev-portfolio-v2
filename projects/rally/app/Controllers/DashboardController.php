<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\View;
use Rally\Models\GameMatch;
use Rally\Services\MatchScoringService;
use Rally\Services\SettlementService;

final class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        assert($userId !== null);

        $matches = GameMatch::forUser($userId);
        $invitations = GameMatch::invitationsFor($userId);

        $todayLive = [];
        $pendingOfficial = [];
        $upcoming = [];
        $recentCompleted = [];
        $activeSummaries = [];

        foreach ($matches as $match) {
            $status = (string) $match['status'];
            if (in_array($status, ['active', 'settling', 'scheduled'], true)) {
                SettlementService::refreshMatch((int) $match['id']);
                $pack = MatchScoringService::forMatchId((int) $match['id']);
                $activeSummaries[] = $pack;
                foreach ($pack['days'] as $day) {
                    if ((string) $day['status'] === 'live') {
                        $todayLive[] = ['match' => $pack['match'], 'day' => $day, 'summary' => $pack['summary']];
                    }
                    if ((string) $day['status'] === 'pending') {
                        $pendingOfficial[] = ['match' => $pack['match'], 'day' => $day, 'summary' => $pack['summary']];
                    }
                }
                if ($status === 'scheduled' || ((string) $pack['match']['status'] === 'scheduled')) {
                    $upcoming[] = $pack;
                }
            }
            if ($status === 'completed') {
                $recentCompleted[] = MatchScoringService::forMatchId((int) $match['id']);
            }
        }

        $recentCompleted = array_slice($recentCompleted, 0, 5);

        View::render('dashboard/index', [
            'title' => 'Dashboard',
            'pageCss' => 'dashboard',
            'invitations' => $invitations,
            'todayLive' => $todayLive,
            'pendingOfficial' => $pendingOfficial,
            'upcoming' => $upcoming,
            'activeSummaries' => $activeSummaries,
            'recentCompleted' => $recentCompleted,
            'hasAny' => $matches !== [] || $invitations !== [],
        ]);
    }
}
