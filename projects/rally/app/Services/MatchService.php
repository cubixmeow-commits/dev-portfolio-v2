<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Match creation, invitation acceptance, and source-comparability helpers.
 */
final class MatchService
{
    /**
     * @param array{
     *   creator_id: int,
     *   opponent_id: int,
     *   metric_type_id: int,
     *   start_date: string,
     *   length_days?: int,
     *   timezone: string,
     *   tie_threshold?: int,
     *   player_a_source_id: int,
     *   player_b_source_id?: ?int,
     *   auto_accept?: bool
     * } $input
     * @return array<string, mixed>
     */
    public static function create(array $input): array
    {
        $creatorId = (int) $input['creator_id'];
        $opponentId = (int) $input['opponent_id'];
        $metricTypeId = (int) $input['metric_type_id'];
        $startDate = (string) $input['start_date'];
        $lengthDays = (int) ($input['length_days'] ?? 14);
        $timezone = (string) $input['timezone'];
        $tieThreshold = (int) ($input['tie_threshold'] ?? 100);
        $sourceA = (int) $input['player_a_source_id'];
        $sourceB = isset($input['player_b_source_id']) ? (int) $input['player_b_source_id'] : null;
        $autoAccept = (bool) ($input['auto_accept'] ?? false);

        if ($creatorId === $opponentId) {
            throw new \InvalidArgumentException('You cannot challenge yourself.');
        }
        if ($lengthDays < 1 || $lengthDays > 60) {
            throw new \InvalidArgumentException('Match length must be between 1 and 60 days.');
        }
        if ($tieThreshold < 0 || $tieThreshold > 10000) {
            throw new \InvalidArgumentException('Tie threshold is out of range.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            throw new \InvalidArgumentException('Start date must be YYYY-MM-DD.');
        }
        try {
            new \DateTimeZone($timezone);
        } catch (\Exception) {
            throw new \InvalidArgumentException('Invalid IANA timezone.');
        }

        $metric = Database::fetch(
            'SELECT * FROM rly_metric_types WHERE id = ? AND is_active = 1',
            [$metricTypeId]
        );
        if ($metric === null) {
            throw new \InvalidArgumentException('Metric type not available.');
        }

        if (!isset($input['length_days'])) {
            $lengthDays = (int) ($metric['default_length_days'] ?? 14);
        }
        if (!isset($input['tie_threshold'])) {
            $tieThreshold = (int) ($metric['default_tie_threshold'] ?? 100);
        }
        if ($lengthDays < 1 || $lengthDays > 60) {
            throw new \InvalidArgumentException('Match length must be between 1 and 60 days.');
        }
        if ($tieThreshold < 0 || $tieThreshold > 10000) {
            throw new \InvalidArgumentException('Tie threshold is out of range.');
        }

        $now = Clock::nowUtcString();
        $invitationStatus = $autoAccept ? 'accepted' : 'pending';
        $status = $autoAccept ? 'scheduled' : 'invited';

        return Database::transaction(static function () use (
            $creatorId, $opponentId, $metricTypeId, $startDate, $lengthDays,
            $timezone, $tieThreshold, $sourceA, $sourceB, $invitationStatus, $status, $now, $autoAccept
        ): array {
            Database::run(
                'INSERT INTO rly_matches
                 (metric_type_id, player_a_user_id, player_b_user_id, player_a_source_id, player_b_source_id,
                  created_by_user_id, start_date, length_days, timezone, tie_threshold, status,
                  invitation_status, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $metricTypeId, $creatorId, $opponentId, $sourceA, $sourceB,
                    $creatorId, $startDate, $lengthDays, $timezone, $tieThreshold,
                    $status, $invitationStatus, $now, $now,
                ]
            );
            $matchId = Database::lastInsertId();
            self::createMatchDays($matchId, $startDate, $lengthDays, $timezone, $now);

            if ($autoAccept) {
                SettlementService::refreshMatch($matchId);
            }

            return Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$matchId]) ?? [];
        });
    }

    public static function createMatchDays(
        int $matchId,
        string $startDate,
        int $lengthDays,
        string $timezone,
        ?string $now = null
    ): void {
        $now ??= Clock::nowUtcString();
        $tz = new \DateTimeZone($timezone);
        $start = new \DateTimeImmutable($startDate . ' 00:00:00', $tz);

        for ($i = 0; $i < $lengthDays; $i++) {
            $day = $start->modify("+{$i} days");
            $compDate = $day->format('Y-m-d');
            $settlesAt = SettlementService::computeSettlesAt($compDate, $timezone);
            Database::run(
                'INSERT INTO rly_match_days
                 (match_id, day_number, competition_date, status, settles_at, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$matchId, $i + 1, $compDate, 'scheduled', $settlesAt, $now, $now]
            );
        }
    }

    /**
     * Opponent accepts invitation and declares their data source.
     *
     * @return array<string, mixed>
     */
    public static function accept(int $matchId, int $userId, int $sourceId): array
    {
        $match = Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$matchId]);
        if ($match === null) {
            throw new \RuntimeException('Match not found.');
        }
        if ((int) $match['player_b_user_id'] !== $userId) {
            throw new \RuntimeException('Only the invited opponent can accept.');
        }
        if ((string) $match['invitation_status'] !== 'pending') {
            throw new \RuntimeException('This invitation is no longer pending.');
        }

        $source = Database::fetch('SELECT * FROM rly_data_sources WHERE id = ? AND is_active = 1', [$sourceId]);
        if ($source === null) {
            throw new \InvalidArgumentException('Choose a valid data source.');
        }

        $now = Clock::nowUtcString();
        Database::run(
            'UPDATE rly_matches
             SET player_b_source_id = ?, invitation_status = ?, status = ?, updated_at = ?
             WHERE id = ?',
            [$sourceId, 'accepted', 'scheduled', $now, $matchId]
        );

        SettlementService::refreshMatch($matchId);

        return Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$matchId]) ?? [];
    }

    public static function decline(int $matchId, int $userId): void
    {
        $match = Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$matchId]);
        if ($match === null) {
            throw new \RuntimeException('Match not found.');
        }
        if ((int) $match['player_b_user_id'] !== $userId) {
            throw new \RuntimeException('Only the invited opponent can decline.');
        }
        $now = Clock::nowUtcString();
        Database::run(
            'UPDATE rly_matches SET invitation_status = ?, status = ?, updated_at = ? WHERE id = ?',
            ['declined', 'cancelled', $now, $matchId]
        );
    }

    /**
     * @param array<string, mixed> $match
     * @return array{mismatch: bool, message: string, title: string}
     */
    public static function sourceComparability(array $match): array
    {
        $classA = (string) ($match['player_a_source_class'] ?? '');
        $classB = (string) ($match['player_b_source_class'] ?? '');
        $nameA = (string) ($match['player_a_source_name'] ?? 'Player A source');
        $nameB = (string) ($match['player_b_source_name'] ?? 'Player B source');
        $playerA = (string) ($match['player_a_name'] ?? 'Player A');
        $playerB = (string) ($match['player_b_name'] ?? 'Player B');

        if ($classB === '' || $match['player_b_source_id'] === null) {
            return [
                'mismatch' => false,
                'title' => 'Source pending',
                'message' => 'Opponent has not declared a data source yet.',
            ];
        }

        // Normalize simulated_* into their base class for comparison messaging.
        $normA = self::normalizeClass($classA);
        $normB = self::normalizeClass($classB);

        if ($normA === $normB) {
            $label = $normA === 'watch' ? 'watches' : ($normA === 'phone' ? 'phones' : $normA . 's');
            return [
                'mismatch' => false,
                'title' => 'Comparable sources',
                'message' => "Both players are using {$label}.",
            ];
        }

        return [
            'mismatch' => true,
            'title' => 'Source mismatch',
            'message' => "{$playerA} is using {$nameA}. {$playerB} is using {$nameB}. "
                . ucfirst($normA) . ' and ' . $normB . ' readings may not be directly comparable. '
                . 'This match can continue, but results should be interpreted with caution.',
        ];
    }

    private static function normalizeClass(string $class): string
    {
        $class = strtolower($class);
        if (str_contains($class, 'watch') || $class === 'wearable') {
            return 'watch';
        }
        if (str_contains($class, 'phone')) {
            return 'phone';
        }
        if ($class === 'simulated') {
            return 'simulated';
        }
        return $class;
    }

    public static function userCanView(array $match, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $userId === (int) $match['player_a_user_id']
            || $userId === (int) $match['player_b_user_id']
            || (\Rally\Core\Auth::isAdmin());
    }
}
