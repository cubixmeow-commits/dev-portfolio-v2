<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Flash;
use Cadence\Core\RateLimiter;
use Cadence\Core\View;
use Cadence\Models\Challenge;
use Cadence\Models\CheckIn;
use Cadence\Models\Participation;

final class CheckInController
{
    /**
     * POST /challenges/{slug}/checkin. Answers JSON for fetch() calls
     * (the optimistic UI) and a flash redirect for no-JS form posts.
     */
    public function store(string $slug): void
    {
        $user = Auth::requireUser();

        // Generous limit: normal use is a handful a day; this only stops
        // scripted hammering.
        if (!RateLimiter::hit('checkin', 'user:' . $user['id'], 60, 3600)) {
            $this->respond($slug, ['ok' => false, 'error' => 'Too many check-in attempts. Slow down a little.']);
        }

        $challenge = Challenge::findBySlug($slug);
        if ($challenge === null) {
            if ($this->wantsJson()) {
                json_response(['ok' => false, 'error' => 'Challenge not found.'], 404);
            }
            http_response_code(404);
            View::render('errors/404', ['title' => 'Challenge not found']);
            return;
        }

        $note = isset($_POST['note']) ? (string) $_POST['note'] : null;
        $result = CheckIn::perform($user, $challenge, $note);

        if ($result['ok']) {
            $ring = Participation::ringToday((int) $user['id'], (string) $user['timezone']);
            $result['ring'] = $ring;
            $result['message'] = $result['milestone'] !== null
                ? 'Day ' . $result['streak'] . '. Milestone hit, +' . CheckIn::MILESTONE_BONUS . ' bonus points.'
                : 'Checked in. Day ' . $result['streak'] . ' of your streak.';
        }

        $this->respond($slug, $result);
    }

    /** @param array<string, mixed> $result */
    private function respond(string $slug, array $result): never
    {
        if ($this->wantsJson()) {
            json_response($result, $result['ok'] ? 200 : 422);
        }
        if ($result['ok']) {
            Flash::set('success', (string) ($result['message'] ?? 'Checked in.'));
        } else {
            Flash::set('error', (string) ($result['error'] ?? 'Check-in failed.'));
        }
        redirect('/challenges/' . $slug);
    }

    private function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
