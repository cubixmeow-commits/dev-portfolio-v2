<?php

declare(strict_types=1);

namespace Cadence\Controllers;

use Cadence\Core\Auth;
use Cadence\Core\Database;
use Cadence\Core\Flash;
use Cadence\Core\View;
use Cadence\Models\ActivityEvent;
use Cadence\Models\Challenge;
use Cadence\Models\CheckIn;
use Cadence\Models\Participation;

final class ChallengeController
{
    public function index(): void
    {
        $q        = trim((string) ($_GET['q'] ?? ''));
        $category = (string) ($_GET['category'] ?? '');
        $status   = (string) ($_GET['status'] ?? '');
        $sort     = (string) ($_GET['sort'] ?? 'popular');
        $page     = max(1, (int) ($_GET['page'] ?? 1));

        $result = Challenge::browse($q, $category, $status, $sort, $page);

        // Which of these the signed-in user already joined, for card CTAs.
        $joinedIds = [];
        if (Auth::check() && $result['rows'] !== []) {
            $ids = array_column($result['rows'], 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $joinedIds = array_map('intval', array_column(Database::fetchAll(
                "SELECT challenge_id FROM challenge_participants WHERE user_id = ? AND challenge_id IN ($placeholders)",
                [Auth::id(), ...$ids]
            ), 'challenge_id'));
        }

        View::render('challenges/index', [
            'title'      => 'Challenges',
            'active'     => 'challenges',
            'page_css'   => 'challenges',
            'result'     => $result,
            'q'          => $q,
            'category'   => $category,
            'status'     => $status,
            'sort'       => $sort,
            'joinedIds'  => $joinedIds,
            'categories' => Challenge::CATEGORIES,
        ]);
    }

    public function show(string $slug): void
    {
        $challenge = Challenge::findBySlug($slug);
        if ($challenge === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Challenge not found']);
            return;
        }

        $user = Auth::user();
        $participation = $user !== null ? Participation::find((int) $challenge['id'], (int) $user['id']) : null;

        $checkedInToday = false;
        if ($user !== null && $participation !== null) {
            $checkedInToday = $participation['last_checkin_date'] === CheckIn::todayFor((string) $user['timezone']);
        }

        // Per-challenge leaderboard: points, tiebroken by current streak.
        $leaders = Database::fetchAll(
            'SELECT cp.points, cp.current_streak, cp.longest_streak,
                    u.display_name, u.handle, u.avatar_seed
             FROM challenge_participants cp
             JOIN users u ON u.id = cp.user_id
             WHERE cp.challenge_id = ?
             ORDER BY cp.points DESC, cp.current_streak DESC, cp.id ASC
             LIMIT 10',
            [(int) $challenge['id']]
        );

        View::render('challenges/show', [
            'title'          => (string) $challenge['title'],
            'active'         => 'challenges',
            'page_css'       => 'challenges',
            'challenge'      => $challenge,
            'participation'  => $participation,
            'checkedInToday' => $checkedInToday,
            'leaders'        => $leaders,
            'events'         => ActivityEvent::forChallenge((int) $challenge['id'], 10),
            'history'        => $participation !== null ? CheckIn::historyFor((int) $participation['id'], 14) : [],
        ]);
    }

    public function join(string $slug): void
    {
        $user = Auth::requireUser();
        $challenge = Challenge::findBySlug($slug);
        if ($challenge === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Challenge not found']);
            return;
        }
        if (Challenge::status($challenge) === 'ended') {
            Flash::set('error', 'This challenge has ended and cannot be joined.');
            redirect('/challenges/' . $slug);
        }

        if (Participation::join((int) $challenge['id'], (int) $user['id'])) {
            Flash::set('success', 'You are in. Check in once a day to build your streak.');
        } else {
            Flash::set('info', 'You already joined this challenge.');
        }
        redirect('/challenges/' . $slug);
    }

    public function leave(string $slug): void
    {
        $user = Auth::requireUser();
        $challenge = Challenge::findBySlug($slug);
        if ($challenge === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Challenge not found']);
            return;
        }

        if (Participation::leave((int) $challenge['id'], (int) $user['id'])) {
            Flash::set('success', 'You left ' . $challenge['title'] . '. Points from it were removed.');
        }
        redirect('/challenges/' . $slug);
    }
}
