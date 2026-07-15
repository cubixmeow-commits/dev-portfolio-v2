<?php

declare(strict_types=1);

namespace Rally\Models;

use Rally\Core\Database;
use Rally\Services\Clock;
use Rally\Services\MatchScoringService;

final class User
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM rly_users WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM rly_users WHERE email = ?', [self::normalizeEmail($email)]);
    }

    /** @return array<string, mixed>|null */
    public static function findByUsername(string $username): ?array
    {
        return Database::fetch('SELECT * FROM rly_users WHERE username = ?', [strtolower(trim($username))]);
    }

    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    /**
     * @return array<string, mixed>
     */
    public static function create(string $name, string $username, string $email, string $password, string $timezone = 'UTC'): array
    {
        $now = Clock::nowUtcString();
        Database::run(
            'INSERT INTO rly_users (name, username, email, password_hash, role, simulation, timezone, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                trim($name),
                strtolower(trim($username)),
                self::normalizeEmail($email),
                password_hash($password, PASSWORD_DEFAULT),
                'user',
                0,
                $timezone,
                'active',
                $now,
                $now,
            ]
        );
        return self::find(Database::lastInsertId()) ?? [];
    }

    /** @return list<array<string, mixed>> */
    public static function allActive(): array
    {
        return Database::fetchAll(
            "SELECT id, name, username, email, timezone, role, simulation FROM rly_users WHERE status = 'active' ORDER BY name"
        );
    }

    /**
     * Derived career/profile stats from official match days and completed matches.
     *
     * @return array<string, mixed>
     */
    public static function derivedStats(int $userId): array
    {
        $matches = Database::fetchAll(
            'SELECT m.*, mt.higher_wins, mt.scoring_strategy, mt.name AS metric_name, mt.unit AS metric_unit,
                    mt.display_unit, mt.classification, mt.slug AS metric_slug
             FROM rly_matches m
             JOIN rly_metric_types mt ON mt.id = m.metric_type_id
             WHERE (m.player_a_user_id = ? OR m.player_b_user_id = ?)
               AND m.invitation_status = \'accepted\'
             ORDER BY m.start_date DESC, m.id DESC',
            [$userId, $userId]
        );
        // competition_type is selected via m.*

        $matchWins = 0;
        $matchLosses = 0;
        $matchDraws = 0;
        $dailyWins = 0;
        $dailyLosses = 0;
        $ties = 0;
        $voids = 0;
        $active = [];
        $completed = [];
        $byType = [
            'classic' => ['wins' => 0, 'losses' => 0, 'draws' => 0],
            'baseline' => ['wins' => 0, 'losses' => 0, 'draws' => 0],
        ];

        foreach ($matches as $match) {
            $pack = MatchScoringService::forMatchId((int) $match['id']);
            $summary = $pack['summary'];
            $isA = (int) $match['player_a_user_id'] === $userId;
            $ctype = (string) ($match['competition_type'] ?? $summary['competition_type'] ?? 'classic');
            if ($ctype !== 'baseline') {
                $ctype = 'classic';
            }

            $myDaily = $isA ? $summary['player_a_wins'] : $summary['player_b_wins'];
            $oppDaily = $isA ? $summary['player_b_wins'] : $summary['player_a_wins'];
            $dailyWins += $myDaily;
            $dailyLosses += $oppDaily;
            $ties += $summary['ties'];
            $voids += $summary['voids'];

            $row = array_merge($match, [
                'summary' => $summary,
                'player_a_name' => $pack['match']['player_a_name'],
                'player_b_name' => $pack['match']['player_b_name'],
                'competition_type' => $ctype,
            ]);

            if (in_array((string) $match['status'], ['active', 'scheduled', 'settling', 'invited'], true)) {
                $active[] = $row;
            }

            if ((string) $match['status'] === 'completed') {
                $completed[] = $row;
                if ($summary['is_draw']) {
                    $matchDraws++;
                    $byType[$ctype]['draws']++;
                } elseif ($summary['leader_user_id'] === $userId) {
                    $matchWins++;
                    $byType[$ctype]['wins']++;
                } else {
                    $matchLosses++;
                    $byType[$ctype]['losses']++;
                }
            }
        }

        return [
            'match_wins' => $matchWins,
            'match_losses' => $matchLosses,
            'match_draws' => $matchDraws,
            'daily_wins' => $dailyWins,
            'daily_losses' => $dailyLosses,
            'ties' => $ties,
            'voids' => $voids,
            'classic_record' => $byType['classic'],
            'baseline_record' => $byType['baseline'],
            'active_matches' => $active,
            'completed_matches' => $completed,
        ];
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }
        return $letters !== '' ? $letters : '?';
    }
}
