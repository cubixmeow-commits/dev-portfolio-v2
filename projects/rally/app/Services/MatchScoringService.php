<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Derives match scores and summary statistics from official match days.
 * Supports:
 *   classic + daily_wins
 *   classic + series_average
 *   baseline + daily_wins
 * Nothing is persisted.
 */
final class MatchScoringService
{
    /**
     * @param array<string, mixed> $match
     * @param list<array<string, mixed>> $days
     * @return array<string, mixed>
     */
    public static function summarize(array $match, array $days): array
    {
        $competitionType = MetricCompetitionService::competitionType($match);
        $strategy = MetricCompetitionService::strategy($match);

        if ($competitionType === MetricCompetitionService::TYPE_BASELINE) {
            if ($strategy !== MetricCompetitionService::STRATEGY_DAILY_WINS) {
                throw new \RuntimeException('Baseline competitions require daily_wins scoring.');
            }
            $summary = self::summarizeBaselineDailyWins($match, $days);
        } elseif ($strategy === MetricCompetitionService::STRATEGY_SERIES_AVERAGE) {
            $summary = self::summarizeSeriesAverage($match, $days);
        } else {
            $summary = self::summarizeDailyWins($match, $days);
        }

        $summary['competition_type'] = $competitionType;
        $summary['scoring_strategy'] = $strategy;
        $summary['baseline'] = BaselineService::contextForMatch($match);
        $summary['result_basis'] = MetricCompetitionService::resultBasisLabel($match);
        $summary['surface_label'] = MetricCompetitionService::formatSurfaceLabel($match);

        return $summary;
    }

    /**
     * @param array<string, mixed> $match
     * @param list<array<string, mixed>> $days
     * @return array<string, mixed>
     */
    private static function summarizeDailyWins(array $match, array $days): array
    {
        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];
        $tieThreshold = (int) $match['tie_threshold'];
        $higherWins = (int) ($match['higher_wins'] ?? 1) === 1;

        $playerAWins = 0;
        $playerBWins = 0;
        $ties = 0;
        $voids = 0;
        $officialDays = 0;
        $pendingDays = 0;
        $remainingDays = 0;
        $liveDays = 0;
        $officialOutcomes = [];
        $margins = [];
        $valuesA = [];
        $valuesB = [];

        foreach ($days as $day) {
            $status = (string) $day['status'];
            if ($status === 'scheduled') {
                $remainingDays++;
                continue;
            }
            if ($status === 'live') {
                $liveDays++;
                $remainingDays++;
                continue;
            }
            if ($status === 'pending') {
                $pendingDays++;
                continue;
            }
            if ($status === 'void') {
                $voids++;
                $officialDays++;
                continue;
            }
            if ($status !== 'official') {
                continue;
            }

            $officialDays++;
            $resultA = self::resultFor($day, $playerA);
            $resultB = self::resultFor($day, $playerB);
            if ($resultA === null || $resultB === null) {
                $voids++;
                continue;
            }

            $valA = (int) $resultA['metric_value'];
            $valB = (int) $resultB['metric_value'];
            $valuesA[] = $valA;
            $valuesB[] = $valB;
            $cmp = MetricComparisonService::compare($valA, $valB, $tieThreshold, $higherWins);

            if ($cmp['is_tie']) {
                $ties++;
                $officialOutcomes[] = 'tie';
            } elseif ($cmp['winner_side'] === 'a') {
                $playerAWins++;
                $officialOutcomes[] = 'a';
                $margins[] = $cmp['margin'];
            } else {
                $playerBWins++;
                $officialOutcomes[] = 'b';
                $margins[] = $cmp['margin'];
            }
        }

        $isComplete = self::allDaysTerminal($days);
        $leaderUserId = null;
        if ($playerAWins > $playerBWins) {
            $leaderUserId = $playerA;
        } elseif ($playerBWins > $playerAWins) {
            $leaderUserId = $playerB;
        }

        return [
            'scoring_strategy' => MetricCompetitionService::STRATEGY_DAILY_WINS,
            'player_a_wins' => $playerAWins,
            'player_b_wins' => $playerBWins,
            'ties' => $ties,
            'voids' => $voids,
            'official_days' => $officialDays,
            'pending_days' => $pendingDays,
            'remaining_days' => $remainingDays + $liveDays,
            'live_days' => $liveDays,
            'leader_user_id' => $leaderUserId,
            'is_provisional' => !$isComplete,
            'is_complete' => $isComplete,
            'is_draw' => $isComplete && $playerAWins === $playerBWins,
            'current_streak' => self::currentStreak($officialOutcomes, $playerA, $playerB),
            'largest_margin' => $margins === [] ? null : max($margins),
            'closest_decisive_margin' => $margins === [] ? null : min($margins),
            'average_a' => $valuesA === [] ? null : (int) round(array_sum($valuesA) / count($valuesA)),
            'average_b' => $valuesB === [] ? null : (int) round(array_sum($valuesB) / count($valuesB)),
            'score_display' => $playerAWins . '–' . $playerBWins,
        ];
    }

    /**
     * @param array<string, mixed> $match
     * @param list<array<string, mixed>> $days
     * @return array<string, mixed>
     */
    private static function summarizeBaselineDailyWins(array $match, array $days): array
    {
        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];
        $baselines = BaselineService::contextForMatch($match);
        $higherWins = (int) ($match['higher_wins'] ?? 1) === 1;

        $playerAWins = 0;
        $playerBWins = 0;
        $ties = 0;
        $voids = 0;
        $officialDays = 0;
        $pendingDays = 0;
        $remainingDays = 0;
        $liveDays = 0;
        $officialOutcomes = [];
        $pctMargins = [];
        $valuesA = [];
        $valuesB = [];
        $pctsA = [];
        $pctsB = [];
        $currentDayPctA = null;
        $currentDayPctB = null;

        foreach ($days as $day) {
            $status = (string) $day['status'];
            if ($status === 'scheduled') {
                $remainingDays++;
                continue;
            }
            if ($status === 'live') {
                $liveDays++;
                $remainingDays++;
                $resultA = self::resultFor($day, $playerA);
                $resultB = self::resultFor($day, $playerB);
                if (!empty($baselines['player_a']['available']) && $resultA !== null) {
                    $currentDayPctA = BaselineCompetitionService::percentageChange(
                        (int) $resultA['metric_value'],
                        (float) $baselines['player_a']['mean'],
                        $higherWins
                    );
                }
                if (!empty($baselines['player_b']['available']) && $resultB !== null) {
                    $currentDayPctB = BaselineCompetitionService::percentageChange(
                        (int) $resultB['metric_value'],
                        (float) $baselines['player_b']['mean'],
                        $higherWins
                    );
                }
                continue;
            }
            if ($status === 'pending') {
                $pendingDays++;
                continue;
            }
            if ($status === 'void') {
                $voids++;
                $officialDays++;
                continue;
            }
            if ($status !== 'official') {
                continue;
            }

            $officialDays++;
            $resultA = self::resultFor($day, $playerA);
            $resultB = self::resultFor($day, $playerB);
            if ($resultA === null || $resultB === null) {
                $voids++;
                continue;
            }

            $valA = (int) $resultA['metric_value'];
            $valB = (int) $resultB['metric_value'];
            $valuesA[] = $valA;
            $valuesB[] = $valB;

            $outcome = BaselineCompetitionService::dayOutcome($match, $valA, $valB, $baselines);
            if (($outcome['percentage_a'] ?? null) !== null) {
                $pctsA[] = (float) $outcome['percentage_a'];
            }
            if (($outcome['percentage_b'] ?? null) !== null) {
                $pctsB[] = (float) $outcome['percentage_b'];
            }

            if ($outcome['kind'] === 'tie') {
                $ties++;
                $officialOutcomes[] = 'tie';
            } elseif ($outcome['kind'] === 'win' && ($outcome['winner_side'] ?? null) === 'a') {
                $playerAWins++;
                $officialOutcomes[] = 'a';
                if ($outcome['margin'] !== null) {
                    $pctMargins[] = (float) $outcome['margin'];
                }
            } elseif ($outcome['kind'] === 'win' && ($outcome['winner_side'] ?? null) === 'b') {
                $playerBWins++;
                $officialOutcomes[] = 'b';
                if ($outcome['margin'] !== null) {
                    $pctMargins[] = (float) $outcome['margin'];
                }
            } else {
                $voids++;
            }
        }

        $isComplete = self::allDaysTerminal($days);
        $leaderUserId = null;
        if ($playerAWins > $playerBWins) {
            $leaderUserId = $playerA;
        } elseif ($playerBWins > $playerAWins) {
            $leaderUserId = $playerB;
        }

        return [
            'scoring_strategy' => MetricCompetitionService::STRATEGY_DAILY_WINS,
            'player_a_wins' => $playerAWins,
            'player_b_wins' => $playerBWins,
            'ties' => $ties,
            'voids' => $voids,
            'official_days' => $officialDays,
            'pending_days' => $pendingDays,
            'remaining_days' => $remainingDays + $liveDays,
            'live_days' => $liveDays,
            'leader_user_id' => $leaderUserId,
            'is_provisional' => !$isComplete,
            'is_complete' => $isComplete,
            'is_draw' => $isComplete && $playerAWins === $playerBWins,
            'current_streak' => self::currentStreak($officialOutcomes, $playerA, $playerB),
            'largest_margin' => $pctMargins === [] ? null : max($pctMargins),
            'closest_decisive_margin' => $pctMargins === [] ? null : min($pctMargins),
            'average_a' => $valuesA === [] ? null : (int) round(array_sum($valuesA) / count($valuesA)),
            'average_b' => $valuesB === [] ? null : (int) round(array_sum($valuesB) / count($valuesB)),
            'current_day_percentage_a' => $currentDayPctA,
            'current_day_percentage_b' => $currentDayPctB,
            'average_percentage_a' => $pctsA === [] ? null : round(array_sum($pctsA) / count($pctsA), 2),
            'average_percentage_b' => $pctsB === [] ? null : round(array_sum($pctsB) / count($pctsB), 2),
            'score_display' => $playerAWins . '–' . $playerBWins,
        ];
    }

    /**
     * @param array<string, mixed> $match
     * @param list<array<string, mixed>> $days
     * @return array<string, mixed>
     */
    private static function summarizeSeriesAverage(array $match, array $days): array
    {
        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];
        $tieThreshold = (int) $match['tie_threshold'];
        $higherWins = (int) ($match['higher_wins'] ?? 1) === 1;

        $voids = 0;
        $officialDays = 0;
        $pendingDays = 0;
        $remainingDays = 0;
        $liveDays = 0;
        $valuesA = [];
        $valuesB = [];
        $dailyA = 0;
        $dailyB = 0;
        $dailyTies = 0;
        $allOfficialValues = [];

        foreach ($days as $day) {
            $status = (string) $day['status'];
            if ($status === 'scheduled') {
                $remainingDays++;
                continue;
            }
            if ($status === 'live') {
                $liveDays++;
                $remainingDays++;
                continue;
            }
            if ($status === 'pending') {
                $pendingDays++;
                continue;
            }
            if ($status === 'void') {
                $voids++;
                $officialDays++;
                continue;
            }
            if ($status !== 'official') {
                continue;
            }

            $officialDays++;
            $resultA = self::resultFor($day, $playerA);
            $resultB = self::resultFor($day, $playerB);
            if ($resultA === null || $resultB === null) {
                $voids++;
                continue;
            }

            $valA = (int) $resultA['metric_value'];
            $valB = (int) $resultB['metric_value'];
            $valuesA[] = $valA;
            $valuesB[] = $valB;
            $allOfficialValues[] = $valA;
            $allOfficialValues[] = $valB;

            $cmp = MetricComparisonService::compare($valA, $valB, $tieThreshold, $higherWins);
            if ($cmp['is_tie']) {
                $dailyTies++;
            } elseif ($cmp['winner_side'] === 'a') {
                $dailyA++;
            } else {
                $dailyB++;
            }
        }

        $avgA = $valuesA === [] ? null : array_sum($valuesA) / count($valuesA);
        $avgB = $valuesB === [] ? null : array_sum($valuesB) / count($valuesB);
        $avgDiff = ($avgA !== null && $avgB !== null) ? abs($avgA - $avgB) : null;

        $isComplete = self::allDaysTerminal($days);
        $leaderUserId = null;
        $isDraw = false;

        if ($avgA !== null && $avgB !== null) {
            if ($avgDiff !== null && $avgDiff < $tieThreshold) {
                if ($isComplete) {
                    $isDraw = true;
                }
            } else {
                $aLeads = $higherWins ? ($avgA > $avgB) : ($avgA < $avgB);
                $leaderUserId = $aLeads ? $playerA : $playerB;
                if ($isComplete && $avgDiff !== null && $avgDiff < $tieThreshold) {
                    $isDraw = true;
                    $leaderUserId = null;
                }
            }
        }

        return [
            'scoring_strategy' => MetricCompetitionService::STRATEGY_SERIES_AVERAGE,
            'player_a_average' => $avgA === null ? null : round($avgA, 2),
            'player_b_average' => $avgB === null ? null : round($avgB, 2),
            'average_difference' => $avgDiff === null ? null : round($avgDiff, 2),
            'daily_comparison_a_leads' => $dailyA,
            'daily_comparison_b_leads' => $dailyB,
            'daily_comparison_ties' => $dailyTies,
            'voids' => $voids,
            'official_days' => $officialDays,
            'pending_days' => $pendingDays,
            'remaining_days' => $remainingDays + $liveDays,
            'live_days' => $liveDays,
            'leader_user_id' => $isDraw ? null : $leaderUserId,
            'is_provisional' => !$isComplete,
            'is_complete' => $isComplete,
            'is_draw' => $isComplete && ($isDraw || ($avgA !== null && $avgB !== null && $avgDiff !== null && $avgDiff < $tieThreshold)),
            'highest_value' => $allOfficialValues === [] ? null : max($allOfficialValues),
            'lowest_value' => $allOfficialValues === [] ? null : min($allOfficialValues),
            'player_a_wins' => $dailyA,
            'player_b_wins' => $dailyB,
            'ties' => $dailyTies,
            'average_a' => $avgA === null ? null : (int) round($avgA),
            'average_b' => $avgB === null ? null : (int) round($avgB),
            'score_display' => ($avgA !== null && $avgB !== null)
                ? MetricFormatter::formatCompact($avgA, $match) . ' / ' . MetricFormatter::formatCompact($avgB, $match)
                : '—',
        ];
    }

    /**
     * @return array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>}
     */
    public static function forMatchId(int $matchId): array
    {
        $match = Database::fetch(
            'SELECT m.*, mt.slug AS metric_slug, mt.name AS metric_name, mt.unit AS metric_unit,
                    mt.display_unit, mt.classification, mt.scoring_strategy, mt.higher_wins,
                    mt.description, mt.context_note, mt.default_length_days, mt.default_tie_threshold,
                    ua.name AS player_a_name, ua.username AS player_a_username,
                    ub.name AS player_b_name, ub.username AS player_b_username,
                    sa.name AS player_a_source_name, sa.source_class AS player_a_source_class, sa.slug AS player_a_source_slug,
                    sb.name AS player_b_source_name, sb.source_class AS player_b_source_class, sb.slug AS player_b_source_slug
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_users ub ON ub.id = m.player_b_user_id
             JOIN rly_data_sources sa ON sa.id = m.player_a_source_id
             LEFT JOIN rly_data_sources sb ON sb.id = m.player_b_source_id
             WHERE m.id = ?',
            [$matchId]
        );
        if ($match === null) {
            throw new \RuntimeException('Match not found: ' . $matchId);
        }

        $days = self::daysWithResults($matchId);
        $summary = self::summarize($match, $days);

        return ['match' => $match, 'days' => $days, 'summary' => $summary];
    }

    /** @return list<array<string, mixed>> */
    public static function daysWithResults(int $matchId): array
    {
        $days = Database::fetchAll(
            'SELECT * FROM rly_match_days WHERE match_id = ? ORDER BY day_number ASC',
            [$matchId]
        );
        if ($days === []) {
            return [];
        }

        $dayIds = array_map(static fn(array $d): int => (int) $d['id'], $days);
        $placeholders = implode(',', array_fill(0, count($dayIds), '?'));
        $results = Database::fetchAll(
            "SELECT * FROM rly_match_day_results WHERE match_day_id IN ({$placeholders})",
            $dayIds
        );

        $byDay = [];
        foreach ($results as $row) {
            $byDay[(int) $row['match_day_id']][] = $row;
        }
        foreach ($days as &$day) {
            $day['results'] = $byDay[(int) $day['id']] ?? [];
        }
        unset($day);

        return $days;
    }

    /** @param array<string, mixed> $day @return array<string, mixed>|null */
    private static function resultFor(array $day, int $userId): ?array
    {
        foreach ($day['results'] ?? [] as $result) {
            if ((int) $result['user_id'] === $userId) {
                return $result;
            }
        }
        return null;
    }

    /** @param list<array<string, mixed>> $days */
    private static function allDaysTerminal(array $days): bool
    {
        if ($days === []) {
            return false;
        }
        foreach ($days as $day) {
            $status = (string) $day['status'];
            if ($status !== 'official' && $status !== 'void') {
                return false;
            }
        }
        return true;
    }

    /**
     * @param list<string> $outcomes
     * @return array{user_id: ?int, length: int, side: ?string}|null
     */
    private static function currentStreak(array $outcomes, int $playerA, int $playerB): ?array
    {
        if ($outcomes === []) {
            return null;
        }
        $last = null;
        $length = 0;
        for ($i = count($outcomes) - 1; $i >= 0; $i--) {
            $outcome = $outcomes[$i];
            if ($outcome === 'tie') {
                break;
            }
            if ($last === null) {
                $last = $outcome;
                $length = 1;
                continue;
            }
            if ($outcome === $last) {
                $length++;
            } else {
                break;
            }
        }
        if ($last === null || $length === 0) {
            return null;
        }
        return [
            'user_id' => $last === 'a' ? $playerA : $playerB,
            'length' => $length,
            'side' => $last,
        ];
    }

    /**
     * @param array<string, mixed> $match
     * @param array<string, mixed> $day
     * @return array<string, mixed>
     */
    public static function dayOutcome(array $match, array $day): array
    {
        $status = (string) $day['status'];
        if ($status === 'void') {
            return [
                'kind' => 'void',
                'label' => 'Void',
                'winner_user_id' => null,
                'margin' => null,
                'value_a' => null,
                'value_b' => null,
                'percentage_a' => null,
                'percentage_b' => null,
                'result_basis' => MetricCompetitionService::resultBasisLabel($match),
            ];
        }

        $playerA = (int) $match['player_a_user_id'];
        $playerB = (int) $match['player_b_user_id'];
        $resultA = self::resultFor($day, $playerA);
        $resultB = self::resultFor($day, $playerB);
        $valA = $resultA !== null ? (int) $resultA['metric_value'] : null;
        $valB = $resultB !== null ? (int) $resultB['metric_value'] : null;

        if (MetricCompetitionService::isBaseline($match)) {
            $baselines = BaselineService::contextForMatch($match);
            $outcome = BaselineCompetitionService::dayOutcome($match, $valA, $valB, $baselines);
            $outcome['result_basis'] = MetricCompetitionService::resultBasisLabel($match);
            return $outcome;
        }

        if ($resultA === null && $resultB === null) {
            return [
                'kind' => 'awaiting',
                'label' => 'Awaiting sync',
                'winner_user_id' => null,
                'margin' => null,
                'value_a' => null,
                'value_b' => null,
                'percentage_a' => null,
                'percentage_b' => null,
                'result_basis' => MetricCompetitionService::resultBasisLabel($match),
            ];
        }
        if ($resultA === null || $resultB === null) {
            return [
                'kind' => 'partial',
                'label' => 'Awaiting sync',
                'winner_user_id' => null,
                'margin' => null,
                'value_a' => $valA,
                'value_b' => $valB,
                'percentage_a' => null,
                'percentage_b' => null,
                'result_basis' => MetricCompetitionService::resultBasisLabel($match),
            ];
        }

        $cmp = MetricComparisonService::compare(
            $valA,
            $valB,
            (int) $match['tie_threshold'],
            (int) ($match['higher_wins'] ?? 1) === 1
        );

        $baselines = BaselineService::contextForMatch($match);
        $higherWins = (int) ($match['higher_wins'] ?? 1) === 1;
        $pctA = null;
        $pctB = null;
        if (!empty($baselines['player_a']['available'])) {
            $pctA = BaselineCompetitionService::percentageChange($valA, (float) $baselines['player_a']['mean'], $higherWins);
        }
        if (!empty($baselines['player_b']['available'])) {
            $pctB = BaselineCompetitionService::percentageChange($valB, (float) $baselines['player_b']['mean'], $higherWins);
        }

        if ($cmp['is_tie']) {
            return [
                'kind' => 'tie',
                'label' => MetricCompetitionService::isSeriesAverage($match) ? 'Within threshold' : 'Tie',
                'winner_user_id' => null,
                'margin' => $cmp['margin'],
                'value_a' => $valA,
                'value_b' => $valB,
                'percentage_a' => $pctA,
                'percentage_b' => $pctB,
                'baseline_a' => $baselines['player_a']['mean'] ?? null,
                'baseline_b' => $baselines['player_b']['mean'] ?? null,
                'result_basis' => MetricCompetitionService::resultBasisLabel($match),
            ];
        }

        $winnerId = $cmp['winner_side'] === 'a' ? $playerA : $playerB;
        return [
            'kind' => 'win',
            'label' => MetricCompetitionService::isSeriesAverage($match) ? 'Daily comparison' : 'Win',
            'winner_user_id' => $winnerId,
            'winner_side' => $cmp['winner_side'],
            'margin' => $cmp['margin'],
            'value_a' => $valA,
            'value_b' => $valB,
            'percentage_a' => $pctA,
            'percentage_b' => $pctB,
            'baseline_a' => $baselines['player_a']['mean'] ?? null,
            'baseline_b' => $baselines['player_b']['mean'] ?? null,
            'result_basis' => MetricCompetitionService::resultBasisLabel($match),
        ];
    }
}
