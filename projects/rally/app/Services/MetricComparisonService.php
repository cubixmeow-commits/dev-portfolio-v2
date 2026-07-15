<?php

declare(strict_types=1);

namespace Rally\Services;

/**
 * Central metric comparison: higher_wins / lower_wins and tie threshold.
 *
 * Tie rule (authoritative):
 *   absolute_difference < tie_threshold  →  tie
 *   absolute_difference >= tie_threshold →  decisive (higher or lower wins)
 */
final class MetricComparisonService
{
    public const OUTCOME_TIE = 'tie';
    public const OUTCOME_A = 'player_a';
    public const OUTCOME_B = 'player_b';

    /**
     * @return array{
     *   outcome: string,
     *   margin: int,
     *   difference: int,
     *   is_tie: bool,
     *   winner_side: ?string
     * }
     */
    public static function compare(
        int $valueA,
        int $valueB,
        int $tieThreshold,
        bool $higherWins = true
    ): array {
        $difference = abs($valueA - $valueB);

        if ($difference < $tieThreshold) {
            return [
                'outcome' => self::OUTCOME_TIE,
                'margin' => $difference,
                'difference' => $difference,
                'is_tie' => true,
                'winner_side' => null,
            ];
        }

        $aWins = $higherWins ? ($valueA > $valueB) : ($valueA < $valueB);

        return [
            'outcome' => $aWins ? self::OUTCOME_A : self::OUTCOME_B,
            'margin' => $difference,
            'difference' => $difference,
            'is_tie' => false,
            'winner_side' => $aWins ? 'a' : 'b',
        ];
    }
}
