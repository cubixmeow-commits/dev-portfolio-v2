<?php

declare(strict_types=1);

namespace Rally\Controllers;

use Rally\Core\Auth;
use Rally\Core\Flash;
use Rally\Core\View;
use Rally\Models\DataSource;
use Rally\Models\GameMatch;
use Rally\Models\MetricType;
use Rally\Models\User;
use Rally\Services\BaselineService;
use Rally\Services\MatchService;
use Rally\Services\MatchScoringService;
use Rally\Services\MetricCompetitionService;
use Rally\Services\SettlementService;

final class MatchController
{
    public function index(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        assert($userId !== null);

        $matches = GameMatch::forUser($userId);
        $packs = [];
        foreach ($matches as $match) {
            if ((string) $match['invitation_status'] === 'accepted'
                || (string) $match['status'] === 'invited') {
                if ((string) $match['status'] !== 'invited') {
                    SettlementService::refreshMatch((int) $match['id']);
                }
                try {
                    $packs[] = MatchScoringService::forMatchId((int) $match['id']);
                } catch (\Throwable) {
                    $packs[] = ['match' => $match, 'days' => [], 'summary' => null];
                }
            }
        }

        View::render('matches/index', [
            'title' => 'Matches',
            'pageCss' => 'matches',
            'packs' => $packs,
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        assert($user !== null);

        $metrics = MetricType::active();
        $defaultMetric = $metrics[0] ?? null;
        foreach ($metrics as $m) {
            if (($m['slug'] ?? '') === 'steps') {
                $defaultMetric = $m;
                break;
            }
        }

        View::render('matches/create', [
            'title' => 'Create match',
            'pageCss' => 'matches',
            'pageJs' => 'create-match',
            'opponents' => array_values(array_filter(
                User::allActive(),
                static fn(array $u): bool => (int) $u['id'] !== (int) $user['id']
            )),
            'metrics' => $metrics,
            'sources' => DataSource::active(),
            'errors' => [],
            'old' => [
                'start_date' => (new \DateTimeImmutable('tomorrow'))->format('Y-m-d'),
                'length_days' => (int) ($defaultMetric['default_length_days'] ?? 14),
                'tie_threshold' => (int) ($defaultMetric['default_tie_threshold'] ?? 100),
                'baseline_tie_threshold' => BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
                'competition_type' => MetricCompetitionService::TYPE_CLASSIC,
                'metric_type_id' => (int) ($defaultMetric['id'] ?? 0),
                'timezone' => $user['timezone'] ?: 'America/Los_Angeles',
                'auto_accept' => '1',
                'baseline_acknowledged' => '0',
            ],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        assert($user !== null);

        $old = [
            'opponent_id' => (int) ($_POST['opponent_id'] ?? 0),
            'metric_type_id' => (int) ($_POST['metric_type_id'] ?? 0),
            'start_date' => trim((string) ($_POST['start_date'] ?? '')),
            'length_days' => (int) ($_POST['length_days'] ?? 14),
            'timezone' => trim((string) ($_POST['timezone'] ?? '')),
            'tie_threshold' => (int) ($_POST['tie_threshold'] ?? 100),
            'baseline_tie_threshold' => $_POST['baseline_tie_threshold'] ?? BaselineService::DEFAULT_BASELINE_TIE_THRESHOLD,
            'competition_type' => trim((string) ($_POST['competition_type'] ?? 'classic')),
            'player_a_source_id' => (int) ($_POST['player_a_source_id'] ?? 0),
            'player_b_source_id' => (int) ($_POST['player_b_source_id'] ?? 0) ?: null,
            'auto_accept' => isset($_POST['auto_accept']) ? '1' : '0',
            'baseline_acknowledged' => isset($_POST['baseline_acknowledged']) ? '1' : '0',
        ];
        $errors = [];

        try {
            $match = MatchService::create([
                'creator_id' => (int) $user['id'],
                'opponent_id' => $old['opponent_id'],
                'metric_type_id' => $old['metric_type_id'],
                'start_date' => $old['start_date'],
                'length_days' => $old['length_days'],
                'timezone' => $old['timezone'],
                'tie_threshold' => $old['tie_threshold'],
                'baseline_tie_threshold' => (float) $old['baseline_tie_threshold'],
                'competition_type' => $old['competition_type'],
                'player_a_source_id' => $old['player_a_source_id'],
                'player_b_source_id' => $old['auto_accept'] === '1' ? $old['player_b_source_id'] : null,
                'auto_accept' => $old['auto_accept'] === '1' && $old['player_b_source_id'],
                'baseline_acknowledged' => $old['baseline_acknowledged'] === '1',
            ]);
            Flash::set('success', 'Match created.');
            redirect('/matches/' . (int) $match['id']);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            $errors['form'] = $e->getMessage();
            http_response_code(422);
            View::render('matches/create', [
                'title' => 'Create match',
                'pageCss' => 'matches',
                'pageJs' => 'create-match',
                'opponents' => array_values(array_filter(
                    User::allActive(),
                    static fn(array $u): bool => (int) $u['id'] !== (int) $user['id']
                )),
                'metrics' => MetricType::active(),
                'sources' => DataSource::active(),
                'errors' => $errors,
                'old' => $old,
            ]);
        }
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $matchId = (int) $id;
        $pack = GameMatch::load($matchId);
        if ($pack === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Match not found']);
            return;
        }
        if (!MatchService::userCanView($pack['match'], Auth::id())) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            return;
        }

        $comparability = MatchService::sourceComparability($pack['match']);
        $timeline = self::buildTimeline($pack);

        View::render('matches/show', [
            'title' => $pack['match']['player_a_name'] . ' vs ' . $pack['match']['player_b_name'],
            'pageCss' => 'match',
            'pageJs' => 'match',
            'pack' => $pack,
            'comparability' => $comparability,
            'timeline' => $timeline,
            'today' => self::findToday($pack),
        ]);
    }

    public function showAccept(string $id): void
    {
        Auth::requireLogin();
        $pack = GameMatch::load((int) $id, false);
        if ($pack === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Match not found']);
            return;
        }
        $match = $pack['match'];
        if ((int) $match['player_b_user_id'] !== Auth::id()) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            return;
        }

        $previewSource = (int) ($_GET['source_id'] ?? 0) ?: null;
        $baselinePreview = null;
        try {
            $baselinePreview = MatchService::baselinePreview((int) $match['id'], $previewSource);
        } catch (\Throwable) {
            $baselinePreview = null;
        }

        $tempMatch = $match;
        if ($previewSource) {
            $src = DataSource::find($previewSource);
            if ($src !== null) {
                $tempMatch['player_b_source_id'] = $previewSource;
                $tempMatch['player_b_source_name'] = $src['name'];
                $tempMatch['player_b_source_class'] = $src['source_class'];
            }
        }
        $comparability = MatchService::sourceComparability($tempMatch);

        View::render('matches/accept', [
            'title' => 'Accept invitation',
            'pageCss' => 'matches',
            'pageJs' => 'accept-match',
            'match' => $match,
            'sources' => DataSource::active(),
            'baselinePreview' => $baselinePreview,
            'comparability' => $comparability,
            'selectedSourceId' => $previewSource,
            'errors' => [],
        ]);
    }

    public function accept(string $id): void
    {
        Auth::requireLogin();
        $sourceId = (int) ($_POST['source_id'] ?? 0);
        $acknowledged = isset($_POST['baseline_acknowledged']);
        try {
            $match = MatchService::accept((int) $id, (int) Auth::id(), $sourceId, [
                'baseline_acknowledged' => $acknowledged,
            ]);
            Flash::set('success', 'Invitation accepted. The series is scheduled.');
            redirect('/matches/' . (int) $match['id']);
        } catch (\Throwable $e) {
            Flash::set('error', $e->getMessage());
            redirect('/matches/' . (int) $id . '/accept' . ($sourceId > 0 ? '?source_id=' . $sourceId : ''));
        }
    }

    public function decline(string $id): void
    {
        Auth::requireLogin();
        try {
            MatchService::decline((int) $id, (int) Auth::id());
            Flash::set('notice', 'Invitation declined.');
            redirect('/dashboard');
        } catch (\Throwable $e) {
            Flash::set('error', $e->getMessage());
            redirect('/dashboard');
        }
    }

    public function history(string $id): void
    {
        Auth::requireLogin();
        $pack = GameMatch::load((int) $id);
        if ($pack === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Match not found']);
            return;
        }
        if (!MatchService::userCanView($pack['match'], Auth::id())) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            return;
        }

        $rows = [];
        foreach ($pack['days'] as $day) {
            $outcome = MatchScoringService::dayOutcome($pack['match'], $day);
            $rows[] = ['day' => $day, 'outcome' => $outcome];
        }

        View::render('matches/history', [
            'title' => 'Match history',
            'pageCss' => 'match',
            'pack' => $pack,
            'rows' => $rows,
        ]);
    }

    public function day(string $id, string $day): void
    {
        Auth::requireLogin();
        $pack = GameMatch::load((int) $id);
        if ($pack === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Match not found']);
            return;
        }
        if (!MatchService::userCanView($pack['match'], Auth::id())) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            return;
        }

        $dayNum = (int) $day;
        $matchDay = null;
        foreach ($pack['days'] as $d) {
            if ((int) $d['day_number'] === $dayNum) {
                $matchDay = $d;
                break;
            }
        }
        if ($matchDay === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Day not found']);
            return;
        }

        $outcome = MatchScoringService::dayOutcome($pack['match'], $matchDay);
        $seriesAsOf = self::seriesThroughDay($pack, $dayNum);
        $dayLabel = MetricCompetitionService::isSeriesAverage($pack['match'])
            ? 'Day ' . $dayNum . ' observation'
            : 'Game ' . $dayNum;

        View::render('matches/day', [
            'title' => $dayLabel,
            'pageCss' => 'match',
            'pack' => $pack,
            'day' => $matchDay,
            'outcome' => $outcome,
            'seriesAsOf' => $seriesAsOf,
        ]);
    }

    public function share(string $id, string $day): void
    {
        Auth::requireLogin();
        $pack = GameMatch::load((int) $id);
        if ($pack === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Match not found']);
            return;
        }
        if (!MatchService::userCanView($pack['match'], Auth::id())) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Not allowed']);
            return;
        }

        $dayNum = (int) $day;
        $matchDay = null;
        foreach ($pack['days'] as $d) {
            if ((int) $d['day_number'] === $dayNum) {
                $matchDay = $d;
                break;
            }
        }
        if ($matchDay === null || (string) $matchDay['status'] !== 'official') {
            Flash::set('notice', 'Share cards are available for official days.');
            redirect('/matches/' . (int) $id . '/day/' . $dayNum);
            return;
        }

        $outcome = MatchScoringService::dayOutcome($pack['match'], $matchDay);
        $seriesAsOf = self::seriesThroughDay($pack, $dayNum);
        $isAvg = MetricCompetitionService::isSeriesAverage($pack['match']);

        View::render('matches/share', [
            'title' => $isAvg ? ('Day ' . $dayNum . ' comparison') : ('Game ' . $dayNum . ' final'),
            'pageCss' => 'share',
            'pageJs' => 'share',
            'bodyClass' => 'page-share',
            'pack' => $pack,
            'day' => $matchDay,
            'outcome' => $outcome,
            'seriesAsOf' => $seriesAsOf,
        ]);
    }

    /**
     * @param array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>} $pack
     * @return list<array<string, mixed>>
     */
    private static function buildTimeline(array $pack): array
    {
        $items = [];
        $isAvg = MetricCompetitionService::isSeriesAverage($pack['match']);
        foreach ($pack['days'] as $day) {
            $status = (string) $day['status'];
            $outcome = MatchScoringService::dayOutcome($pack['match'], $day);
            $symbol = '·';
            $label = 'Future';
            if ($status === 'void') {
                $symbol = '×';
                $label = 'Void';
            } elseif ($status === 'pending') {
                $symbol = '◌';
                $label = 'Pending';
            } elseif ($status === 'live') {
                $symbol = '◌';
                $label = 'Live';
            } elseif ($status === 'official') {
                if ($outcome['kind'] === 'tie') {
                    $symbol = '–';
                    $label = $isAvg ? 'Within threshold' : 'Tie';
                } elseif (($outcome['winner_side'] ?? null) === 'a') {
                    $symbol = '●';
                    $label = $isAvg
                        ? ($pack['match']['player_a_name'] . ' daily comparison')
                        : ($pack['match']['player_a_name'] . ' win');
                } elseif (($outcome['winner_side'] ?? null) === 'b') {
                    $symbol = '○';
                    $label = $isAvg
                        ? ($pack['match']['player_b_name'] . ' daily comparison')
                        : ($pack['match']['player_b_name'] . ' win');
                }
            }
            $items[] = [
                'day' => $day,
                'symbol' => $symbol,
                'label' => $label,
                'outcome' => $outcome,
            ];
        }
        return $items;
    }

    /**
     * @param array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>} $pack
     * @return array<string, mixed>|null
     */
    private static function findToday(array $pack): ?array
    {
        foreach ($pack['days'] as $day) {
            if (in_array((string) $day['status'], ['live', 'pending'], true)) {
                return [
                    'day' => $day,
                    'outcome' => MatchScoringService::dayOutcome($pack['match'], $day),
                ];
            }
        }
        return null;
    }

    /**
     * @param array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>} $pack
     * @return array<string, mixed>
     */
    private static function seriesThroughDay(array $pack, int $throughDayNumber): array
    {
        $subset = [];
        foreach ($pack['days'] as $day) {
            if ((int) $day['day_number'] <= $throughDayNumber) {
                if ((int) $day['day_number'] < $throughDayNumber
                    || in_array((string) $day['status'], ['official', 'void'], true)) {
                    $subset[] = $day;
                } else {
                    $clone = $day;
                    $clone['status'] = 'scheduled';
                    $subset[] = $clone;
                }
            } else {
                $clone = $day;
                $clone['status'] = 'scheduled';
                $subset[] = $clone;
            }
        }
        return MatchScoringService::summarize($pack['match'], $subset);
    }
}
