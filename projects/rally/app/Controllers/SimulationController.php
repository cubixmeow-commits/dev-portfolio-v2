<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\Flash;
use Rally\Core\View;
use Rally\Models\DataSource;
use Rally\Services\Clock;
use Rally\Services\MatchScoringService;
use Rally\Services\SettlementService;
use Rally\Services\SimulationService;

final class SimulationController
{
    public function index(): void
    {
        Auth::requireSimulationAccess();

        $matches = SimulationService::listMatches();
        $selectedId = (int) ($_GET['match_id'] ?? ($matches[0]['id'] ?? 0));
        $pack = null;
        if ($selectedId > 0) {
            SettlementService::refreshMatch($selectedId);
            try {
                $pack = MatchScoringService::forMatchId($selectedId);
            } catch (\Throwable) {
                $pack = null;
            }
        }

        View::render('simulation/index', [
            'title' => 'Simulation controls',
            'pageCss' => 'simulation',
            'matches' => $matches,
            'pack' => $pack,
            'selectedId' => $selectedId,
            'sources' => DataSource::active(),
            'clock' => Clock::now(),
            'hasOverride' => Clock::hasOverride(),
        ]);
    }

    public function update(): void
    {
        Auth::requireSimulationAccess();
        $action = (string) ($_POST['action'] ?? '');

        try {
            switch ($action) {
                case 'advance_days':
                    $days = (int) ($_POST['days'] ?? 1);
                    $next = SimulationService::advanceDays($days);
                    Flash::set('success', 'Clock advanced to ' . $next->format('Y-m-d H:i:s') . ' UTC.');
                    break;

                case 'set_clock':
                    $raw = trim((string) ($_POST['clock_at'] ?? ''));
                    $instant = new \DateTimeImmutable($raw, new \DateTimeZone('UTC'));
                    SimulationService::advanceTo($instant);
                    Flash::set('success', 'Clock set to ' . $instant->format('Y-m-d H:i:s') . ' UTC.');
                    break;

                case 'clear_clock':
                    SimulationService::clearClock();
                    Flash::set('notice', 'Clock override cleared. Using real time.');
                    break;

                case 'update_day':
                    SimulationService::updateDay([
                        'match_day_id' => (int) ($_POST['match_day_id'] ?? 0),
                        'value_a' => $_POST['value_a'] !== '' ? (int) $_POST['value_a'] : null,
                        'value_b' => $_POST['value_b'] !== '' ? (int) $_POST['value_b'] : null,
                        'source_a' => (int) ($_POST['source_a'] ?? 0) ?: null,
                        'source_b' => (int) ($_POST['source_b'] ?? 0) ?: null,
                        'day_status' => trim((string) ($_POST['day_status'] ?? '')) ?: null,
                        'settle' => isset($_POST['settle']),
                    ]);
                    Flash::set('success', 'Match day updated through the ingestion pathway.');
                    break;

                case 'settle_day':
                    SettlementService::settleDayNow((int) ($_POST['match_day_id'] ?? 0));
                    Flash::set('success', 'Day settled.');
                    break;

                case 'refresh_match':
                    SettlementService::refreshMatch((int) ($_POST['match_id'] ?? 0));
                    Flash::set('success', 'Match lifecycle refreshed against the application clock.');
                    break;

                default:
                    Flash::set('error', 'Unknown simulation action.');
            }
        } catch (\Throwable $e) {
            Flash::set('error', $e->getMessage());
        }

        $matchId = (int) ($_POST['match_id'] ?? $_GET['match_id'] ?? 0);
        redirect('/simulation' . ($matchId > 0 ? '?match_id=' . $matchId : ''));
    }
}
