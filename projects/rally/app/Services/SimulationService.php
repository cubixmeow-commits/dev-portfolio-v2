<?php

declare(strict_types=1);

namespace Rally\Services;

use Rally\Core\Database;

/**
 * Development simulation controls. Advances the application clock, mutates
 * provisional results through ResultIngestionService, and settles days.
 */
final class SimulationService
{
    /**
     * Advance (or set) the simulated clock, then refresh open matches.
     */
    public static function advanceTo(\DateTimeImmutable $instant): void
    {
        Clock::setOverride($instant->setTimezone(new \DateTimeZone('UTC')));
        SettlementService::refreshAllOpenMatches();
    }

    public static function advanceDays(int $days): \DateTimeImmutable
    {
        $current = Clock::now();
        $next = $current->modify(($days >= 0 ? '+' : '') . $days . ' days');
        self::advanceTo($next);
        return $next;
    }

    public static function clearClock(): void
    {
        Clock::clearOverride();
        SettlementService::refreshAllOpenMatches();
    }

    /**
     * Set or update both players' values for a match day via ingestion,
     * optionally settling immediately.
     *
     * @param array{
     *   match_day_id: int,
     *   value_a?: ?int,
     *   value_b?: ?int,
     *   source_a?: string|int|null,
     *   source_b?: string|int|null,
     *   settle?: bool,
     *   day_status?: ?string
     * } $input
     */
    public static function updateDay(array $input): void
    {
        $matchDayId = (int) $input['match_day_id'];
        $day = Database::fetch(
            'SELECT d.*, m.player_a_user_id, m.player_b_user_id, m.player_a_source_id, m.player_b_source_id,
                    m.id AS match_id
             FROM rly_match_days d
             JOIN rly_matches m ON m.id = d.match_id
             WHERE d.id = ?',
            [$matchDayId]
        );
        if ($day === null) {
            throw new \RuntimeException('Match day not found.');
        }

        $sourceA = $input['source_a'] ?? (int) $day['player_a_source_id'];
        $sourceB = $input['source_b'] ?? (int) ($day['player_b_source_id'] ?? $day['player_a_source_id']);

        if (array_key_exists('value_a', $input) && $input['value_a'] !== null && $input['value_a'] !== '') {
            ResultIngestionService::ingest([
                'user_id' => (int) $day['player_a_user_id'],
                'match_day_id' => $matchDayId,
                'value' => (int) $input['value_a'],
                'data_source' => $sourceA,
                'source_record_key' => 'sim-a-' . $matchDayId,
                'is_manual' => true,
            ], true);
        }

        if (array_key_exists('value_b', $input) && $input['value_b'] !== null && $input['value_b'] !== '') {
            ResultIngestionService::ingest([
                'user_id' => (int) $day['player_b_user_id'],
                'match_day_id' => $matchDayId,
                'value' => (int) $input['value_b'],
                'data_source' => $sourceB,
                'source_record_key' => 'sim-b-' . $matchDayId,
                'is_manual' => true,
            ], true);
        }

        if (!empty($input['day_status'])) {
            $allowed = ['scheduled', 'live', 'pending', 'official', 'void'];
            $status = (string) $input['day_status'];
            if (!in_array($status, $allowed, true)) {
                throw new \InvalidArgumentException('Invalid day status.');
            }
            $now = Clock::nowUtcString();
            $officialAt = in_array($status, ['official', 'void'], true) ? $now : null;
            Database::run(
                'UPDATE rly_match_days SET status = ?, official_at = ?, updated_at = ? WHERE id = ?',
                [$status, $officialAt, $now, $matchDayId]
            );
        }

        if (!empty($input['settle'])) {
            SettlementService::settleDayNow($matchDayId);
        } else {
            SettlementService::refreshMatch((int) $day['match_id']);
        }
    }

    /** @return list<array<string, mixed>> */
    public static function listMatches(): array
    {
        return Database::fetchAll(
            'SELECT m.*, ua.name AS player_a_name, ub.name AS player_b_name, mt.name AS metric_name
             FROM rly_matches m
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_users ub ON ub.id = m.player_b_user_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             ORDER BY m.id ASC'
        );
    }
}
