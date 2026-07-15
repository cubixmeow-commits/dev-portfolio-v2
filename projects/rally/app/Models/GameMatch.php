<?php

declare(strict_types=1);

namespace Rally\Models;

use Rally\Core\Database;
use Rally\Services\MatchScoringService;
use Rally\Services\SettlementService;

final class GameMatch
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM rly_matches WHERE id = ?', [$id]);
    }

    /**
     * Full match pack with days, summary, and source comparability.
     *
     * @return array{match: array<string, mixed>, days: list<array<string, mixed>>, summary: array<string, mixed>}|null
     */
    public static function load(int $id, bool $refresh = true): ?array
    {
        if (self::find($id) === null) {
            return null;
        }
        if ($refresh) {
            SettlementService::refreshMatch($id);
        }
        return MatchScoringService::forMatchId($id);
    }

    /** @return list<array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT m.*, mt.name AS metric_name, mt.unit AS metric_unit,
                    ua.name AS player_a_name, ub.name AS player_b_name
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_users ub ON ub.id = m.player_b_user_id
             WHERE m.player_a_user_id = ? OR m.player_b_user_id = ?
             ORDER BY
               CASE m.status
                 WHEN \'active\' THEN 0
                 WHEN \'settling\' THEN 1
                 WHEN \'scheduled\' THEN 2
                 WHEN \'invited\' THEN 3
                 WHEN \'completed\' THEN 4
                 ELSE 5
               END,
               m.start_date DESC, m.id DESC',
            [$userId, $userId]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function invitationsFor(int $userId): array
    {
        return Database::fetchAll(
            "SELECT m.*, ua.name AS player_a_name, mt.name AS metric_name
             FROM rly_matches m
             JOIN rly_users ua ON ua.id = m.player_a_user_id
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE m.player_b_user_id = ?
               AND m.invitation_status = 'pending'
               AND m.status = 'invited'
             ORDER BY m.created_at DESC",
            [$userId]
        );
    }
}
