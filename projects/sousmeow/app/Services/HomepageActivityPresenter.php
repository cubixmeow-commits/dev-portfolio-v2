<?php

declare(strict_types=1);

namespace SousMeow\Services;

use SousMeow\Core\Database;

/**
 * Homepage-only presentation layer: translates simulation data into
 * visitor-friendly accomplishments without changing the engine.
 */
final class HomepageActivityPresenter
{
    /** @var array<string, string> */
    private const PROJECT_OUTCOMES = [
        'launch-day-kit'             => 'a product launch package',
        'plan-youtube-video'         => 'a YouTube publishing plan',
        'validate-saas-idea'         => 'a SaaS validation report',
        'build-professional-portfolio' => 'a professional portfolio',
        'plan-a-novel'               => 'a novel planning blueprint',
    ];

    /** @var array<string, string> Friendly milestone labels keyed by recipe slug. */
    private const MILESTONE_LABELS = [
        'position-the-dish'           => 'the messaging stage of a product launch',
        'plate-the-landing-page'      => 'launch landing page copy',
        'serve-the-announcements'     => 'launch announcements',
        'answer-the-table'            => 'launch FAQ responses',
        'audience-targeting'          => 'target audience definition',
        'positioning-statement'       => 'portfolio positioning',
        'research-brief'              => 'video research',
        'video-outline'               => 'a video outline',
        'publishing-checklist'          => 'a publishing checklist',
        'problem-validation-brief'    => 'market validation',
        'ideal-customer-profile'      => 'ideal customer research',
        'go-revise-stop-memo'         => 'a go / revise / stop decision memo',
        'original-premise'            => 'a novel premise',
        'act-outline'                 => 'a three-act outline',
    ];

    /**
     * @return array{
     *   metrics: array<string, int|float|string>,
     *   heatmap: array<string, mixed>,
     *   feed: list<array<string, string>>,
     *   achievements: list<array<string, string>>,
     *   insights: list<array<string, string>>,
     *   popular: list<array<string, mixed>>
     * }
     */
    public static function bundle(): array
    {
        return [
            'metrics'      => self::visitorMetrics(),
            'heatmap'      => self::presentHeatmap(SiteStats::activityHeatmap(12)),
            'feed'         => self::activityFeed(12),
            'achievements' => self::achievements(6),
            'insights'     => self::insights(),
            'popular'      => self::popularWorkflows(3),
        ];
    }

    /** @return array<string, int|float> */
    public static function visitorMetrics(): array
    {
        [$dayStart, $dayEnd] = SiteStats::pacificTodayRange();
        $weekStart = self::utcDaysAgo(7);
        $monthStart = self::utcDaysAgo(30);

        $raw = SiteStats::hero();
        $inProgress = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL'
        );
        $completedWeek = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
               AND p.completed_at >= ? AND p.completed_at <= ?',
            [$weekStart, $dayEnd]
        );
        $completedMonth = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
               AND p.completed_at >= ?',
            [$monthStart]
        );
        $avgProgress = self::averageCompletionPercent();

        return [
            'projects_completed_today' => (int) $raw['kits_today'],
            'milestones_today'         => (int) $raw['approved_today'],
            'active_creators_today'    => (int) $raw['active_today'],
            'creators_total'           => (int) $raw['chefs'],
            'projects_finished_total'  => (int) $raw['kits_total'],
            'workflows_in_progress'    => $inProgress,
            'projects_completed_week'  => $completedWeek,
            'projects_completed_month' => $completedMonth,
            'avg_completion_percent'   => $avgProgress,
            'workflow_count'           => (int) $raw['cookbooks'],
        ];
    }

    /**
     * @param array<string, mixed> $heatmap
     * @return array<string, mixed>
     */
    public static function presentHeatmap(array $heatmap): array
    {
        $heatmap['label'] = 'Project milestones';
        $heatmap['summary'] = SiteStats::formatCompact((int) $heatmap['total'])
            . ' milestones across ' . count($heatmap['weeks']) . ' weeks';
        return $heatmap;
    }

    /**
     * @return list<array<string, string>>
     */
    public static function activityFeed(int $limit = 12): array
    {
        $events = array_merge(
            self::internalCompletions(8),
            self::internalStarts(6),
            self::internalMilestones(10),
            self::internalResumes(5),
        );

        return self::mixFeed($events, max(1, $limit));
    }

    /**
     * @return list<array<string, string>>
     */
    public static function achievements(int $limit = 6): array
    {
        $earned = array_merge(
            self::achievementFirstProject(3),
            self::achievementThreeWorkflows(3),
            self::achievementReturned(3),
            self::achievementFullReview(3),
            self::achievementMultiCategory(3),
            self::achievementFiveProjects(3),
        );

        usort($earned, static fn(array $a, array $b): int => strcmp($b['at'], $a['at']));

        $seen = [];
        $picked = [];
        foreach ($earned as $row) {
            $key = $row['title'] . '|' . $row['name'];
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $picked[] = $row;
            if (count($picked) >= $limit) {
                break;
            }
        }

        return $picked;
    }

    /** @return list<array<string, string>> */
    public static function insights(): array
    {
        [$dayStart, $dayEnd] = SiteStats::pacificTodayRange();
        $weekStart = self::utcDaysAgo(7);
        $metrics = self::visitorMetrics();

        $mostCompleted = Database::fetch(
            "SELECT c.title,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.completed_at IS NOT NULL
                       AND p.completed_at >= ? AND p.completed_at <= ?) AS cnt
             FROM cookbooks c
             ORDER BY cnt DESC, c.demo_completed_runs DESC
             LIMIT 1",
            [$dayStart, $dayEnd]
        );

        $fastestGrowing = Database::fetch(
            "SELECT c.title,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.created_at >= ? AND p.created_at <= ?) AS cnt
             FROM cookbooks c
             ORDER BY cnt DESC, c.demo_completed_runs DESC
             LIMIT 1",
            [$dayStart, $dayEnd]
        );

        $mostInProgress = Database::fetch(
            "SELECT c.title,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL) AS cnt
             FROM cookbooks c
             ORDER BY cnt DESC
             LIMIT 1"
        );

        $returnedTo = Database::fetch(
            'SELECT c.title, COUNT(*) AS cnt
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1
               AND p.completed_at IS NULL
               AND p.pantry_saved_at IS NOT NULL
               AND p.updated_at > ' . self::sqlDaysAfter('p.created_at', 2) . '
             GROUP BY c.id
             ORDER BY cnt DESC
             LIMIT 1'
        );

        $prevWeekStart = self::utcDaysAgo(14);
        $prevWeekEnd = self::utcDaysAgo(7);
        $thisWeek = (int) $metrics['projects_completed_week'];
        $lastWeek = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
               AND p.completed_at >= ? AND p.completed_at < ?',
            [$prevWeekStart, $prevWeekEnd]
        );
        $trend = $thisWeek > $lastWeek
            ? 'Up from last week'
            : ($thisWeek < $lastWeek ? 'Steady week over week' : 'Holding steady');

        $insights = [
            [
                'label' => 'Most completed today',
                'value' => (string) ($mostCompleted['title'] ?? 'Launch Day Kit'),
                'detail' => ((int) ($mostCompleted['cnt'] ?? 0)) . ' finished today',
            ],
            [
                'label' => 'Fastest growing workflow',
                'value' => (string) ($fastestGrowing['title'] ?? 'Plan a YouTube Video'),
                'detail' => ((int) ($fastestGrowing['cnt'] ?? 0)) . ' new projects today',
            ],
            [
                'label' => 'Creators active today',
                'value' => (string) $metrics['active_creators_today'],
                'detail' => 'working on guided projects',
            ],
            [
                'label' => 'Projects completed this week',
                'value' => (string) $metrics['projects_completed_week'],
                'detail' => $trend,
            ],
            [
                'label' => 'Average project progress',
                'value' => (string) $metrics['avg_completion_percent'] . '%',
                'detail' => 'across active workflows',
            ],
            [
                'label' => 'Most in progress',
                'value' => (string) ($mostInProgress['title'] ?? 'Launch Day Kit'),
                'detail' => ((int) ($mostInProgress['cnt'] ?? 0)) . ' projects underway',
            ],
        ];

        if ($returnedTo !== null && (int) ($returnedTo['cnt'] ?? 0) > 0) {
            $insights[] = [
                'label' => 'Most returned to',
                'value' => (string) $returnedTo['title'],
                'detail' => (int) $returnedTo['cnt'] . ' creators picked it back up',
            ];
        }

        return $insights;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function popularWorkflows(int $limit = 3): array
    {
        $limit = max(1, $limit);
        [$dayStart, $dayEnd] = SiteStats::pacificTodayRange();

        $rows = Database::fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = c.id) AS recipe_count,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.completed_at IS NOT NULL
                       AND p.completed_at >= ? AND p.completed_at <= ?) AS completed_today,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.created_at >= ? AND p.created_at <= ?) AS started_today,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL) AS in_progress,
                    (SELECT COUNT(*) FROM projects p JOIN users u ON u.id = p.user_id
                     WHERE p.cookbook_id = c.id AND u.simulation = 1
                       AND p.completed_at IS NOT NULL) AS completed_total
             FROM cookbooks c
             ORDER BY completed_today DESC, in_progress DESC, started_today DESC, c.demo_completed_runs DESC
             LIMIT {$limit}",
            [$dayStart, $dayEnd, $dayStart, $dayEnd]
        );

        foreach ($rows as &$row) {
            $finished = (int) $row['completed_total'];
            $active = (int) $row['in_progress'];
            $denom = $finished + $active;
            $row['completion_rate'] = $denom > 0 ? (int) round(($finished / $denom) * 100) : 0;
        }
        unset($row);

        return $rows;
    }

    /** @return list<array<string, string>> */
    private static function internalCompletions(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT p.completed_at AS at, u.name, c.slug, c.title, c.outcome
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
             ORDER BY p.completed_at DESC
             LIMIT {$limit}"
        );

        $events = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $outcome = self::projectOutcome((string) $row['slug'], (string) $row['outcome']);
            $events[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'kind'    => 'completed',
                'badge'   => 'Project finished',
                'message' => $who . ' completed ' . $outcome . '.',
                'tone'    => 'completed',
            ];
        }

        return $events;
    }

    /** @return list<array<string, string>> */
    private static function internalStarts(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT COALESCE(p.pantry_saved_at, p.created_at) AS at, u.name, c.slug, c.outcome
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.pantry_saved_at IS NOT NULL
             ORDER BY at DESC
             LIMIT {$limit}"
        );

        $events = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $outcome = self::projectOutcome((string) $row['slug'], (string) $row['outcome']);
            $events[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'kind'    => 'started',
                'badge'   => 'Project started',
                'message' => $who . ' started building ' . $outcome . '.',
                'tone'    => 'started',
            ];
        }

        return $events;
    }

    /** @return list<array<string, string>> */
    private static function internalMilestones(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT a.updated_at AS at, u.name, r.title AS recipe_title, r.slug AS recipe_slug,
                    c.slug AS cookbook_slug, c.outcome
             FROM artifacts a
             JOIN projects p ON p.id = a.project_id
             JOIN users u ON u.id = p.user_id
             JOIN recipes r ON r.id = a.recipe_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND a.status = 'approved' AND p.completed_at IS NULL
             ORDER BY a.updated_at DESC
             LIMIT {$limit}"
        );

        $events = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $milestone = self::milestoneLabel(
                (string) $row['recipe_slug'],
                (string) $row['recipe_title'],
                (string) $row['cookbook_slug']
            );
            $events[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'kind'    => 'milestone',
                'badge'   => 'Major milestone',
                'message' => $who . ' completed ' . $milestone . '.',
                'tone'    => 'milestone',
            ];
        }

        return $events;
    }

    /** @return list<array<string, string>> */
    private static function internalResumes(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT p.updated_at AS at, u.name, c.slug, c.outcome,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS approved_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1
               AND p.completed_at IS NULL
               AND p.pantry_saved_at IS NOT NULL
               AND p.updated_at > ' . self::sqlDaysAfter('p.created_at', 2) . '
             ORDER BY p.updated_at DESC
             LIMIT {$limit}"
        );

        $events = [];
        foreach ($rows as $row) {
            if ((int) $row['approved_count'] < 1) {
                continue;
            }
            $who = SiteStats::firstName((string) $row['name']);
            $outcome = self::projectOutcome((string) $row['slug'], (string) $row['outcome']);
            $events[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'kind'    => 'resumed',
                'badge'   => 'Returned to project',
                'message' => $who . ' picked ' . $outcome . ' back up.',
                'tone'    => 'resumed',
            ];
        }

        return $events;
    }

    /** @return list<array<string, string>> */
    private static function achievementFirstProject(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, p.completed_at AS at, c.slug, c.outcome
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
               AND p.completed_at = (
                   SELECT MIN(p2.completed_at) FROM projects p2
                   WHERE p2.user_id = u.id AND p2.completed_at IS NOT NULL
               )
             ORDER BY p.completed_at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'First project completed',
                'message' => $who . ' finished their first guided project.',
            ];
        }

        return $achievements;
    }

    /** @return list<array<string, string>> */
    private static function achievementThreeWorkflows(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, MAX(p.completed_at) AS at, COUNT(DISTINCT p.cookbook_id) AS workflow_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
             GROUP BY u.id
             HAVING workflow_count >= 3
             ORDER BY at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'Three workflows finished',
                'message' => $who . ' completed projects across ' . (int) $row['workflow_count'] . ' different workflows.',
            ];
        }

        return $achievements;
    }

    /** @return list<array<string, string>> */
    private static function achievementReturned(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, p.updated_at AS at, c.slug, c.outcome
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1
               AND p.completed_at IS NOT NULL
               AND p.updated_at > ' . self::sqlDaysAfter('p.created_at', 2) . '
               AND ' . self::sqlProjectSpanAtLeast(2) . '
             ORDER BY p.completed_at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $outcome = self::projectOutcome((string) $row['slug'], (string) $row['outcome']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'Returned to finish',
                'message' => $who . ' came back to complete ' . $outcome . '.',
            ];
        }

        return $achievements;
    }

    /** @return list<array<string, string>> */
    private static function achievementFullReview(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, p.completed_at AS at, c.slug
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
               AND (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id)
                 = (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved')
             ORDER BY p.completed_at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'Every review completed',
                'message' => $who . ' finished a workflow without skipping a step.',
            ];
        }

        return $achievements;
    }

    /** @return list<array<string, string>> */
    private static function achievementMultiCategory(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, MAX(p.completed_at) AS at, COUNT(DISTINCT c.category) AS category_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             JOIN cookbooks c ON c.id = p.cookbook_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
             GROUP BY u.id
             HAVING category_count >= 2
             ORDER BY at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'Across multiple categories',
                'message' => $who . ' finished projects in ' . (int) $row['category_count'] . ' workflow categories.',
            ];
        }

        return $achievements;
    }

    /** @return list<array<string, string>> */
    private static function achievementFiveProjects(int $limit): array
    {
        $rows = Database::fetchAll(
            "SELECT u.name, MAX(p.completed_at) AS at, COUNT(*) AS project_count
             FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NOT NULL
             GROUP BY u.id
             HAVING project_count >= 5
             ORDER BY at DESC
             LIMIT {$limit}"
        );

        $achievements = [];
        foreach ($rows as $row) {
            $who = SiteStats::firstName((string) $row['name']);
            $achievements[] = [
                'at'      => (string) $row['at'],
                'name'    => (string) $row['name'],
                'title'   => 'Five finished projects',
                'message' => $who . ' built ' . (int) $row['project_count'] . ' finished projects.',
            ];
        }

        return $achievements;
    }

    /**
     * @param list<array<string, string>> $events
     * @return list<array<string, string>>
     */
    private static function mixFeed(array $events, int $limit): array
    {
        usort($events, static fn(array $a, array $b): int => strcmp($b['at'], $a['at']));

        $buckets = [
            'milestone' => [],
            'started'   => [],
            'resumed'   => [],
            'completed' => [],
        ];
        foreach ($events as $event) {
            $kind = $event['kind'];
            if (isset($buckets[$kind])) {
                $buckets[$kind][] = $event;
            }
        }

        $order = ['milestone', 'started', 'resumed', 'completed'];
        $picked = [];
        $lastUser = '';
        $lastKind = '';
        $sameKindRun = 0;

        while (count($picked) < $limit) {
            $added = false;
            foreach ($order as $kind) {
                if ($buckets[$kind] === []) {
                    continue;
                }

                $index = 0;
                while ($index < count($buckets[$kind])) {
                    $candidate = $buckets[$kind][$index];
                    $sameUser = $candidate['name'] === $lastUser;
                    $sameKind = $kind === $lastKind && $sameKindRun >= 2;

                    if ($sameUser || $sameKind) {
                        $index++;
                        continue;
                    }

                    array_splice($buckets[$kind], $index, 1);
                    $picked[] = $candidate;
                    $lastUser = $candidate['name'];
                    $sameKindRun = $kind === $lastKind ? $sameKindRun + 1 : 1;
                    $lastKind = $kind;
                    $added = true;
                    break 2;
                }
            }

            if (!$added) {
                break;
            }
        }

        if (count($picked) < $limit) {
            foreach ($events as $event) {
                $id = $event['at'] . '|' . $event['name'] . '|' . $event['kind'];
                foreach ($picked as $existing) {
                    if ($existing['at'] === $event['at']
                        && $existing['name'] === $event['name']
                        && $existing['kind'] === $event['kind']) {
                        continue 2;
                    }
                }
                $picked[] = $event;
                if (count($picked) >= $limit) {
                    break;
                }
            }
        }

        usort($picked, static fn(array $a, array $b): int => strcmp($b['at'], $a['at']));
        return array_slice($picked, 0, $limit);
    }

    private static function projectOutcome(string $slug, string $fallbackOutcome): string
    {
        if (isset(self::PROJECT_OUTCOMES[$slug])) {
            return self::PROJECT_OUTCOMES[$slug];
        }

        $trimmed = trim($fallbackOutcome);
        if ($trimmed === '') {
            return 'a guided project';
        }

        return 'a finished project: ' . strtolower($trimmed);
    }

    private static function milestoneLabel(string $recipeSlug, string $recipeTitle, string $cookbookSlug): string
    {
        if (isset(self::MILESTONE_LABELS[$recipeSlug])) {
            return self::MILESTONE_LABELS[$recipeSlug];
        }

        $title = strtolower(trim($recipeTitle));
        $title = (string) preg_replace('/^(define|write|prepare|plan|build|create|draft|map|shape)\s+(your\s+)?/i', '', $title);

        return $title !== '' ? $title : 'a major project milestone';
    }

    private static function averageCompletionPercent(): int
    {
        $rows = Database::fetchAll(
            "SELECT p.id, p.cookbook_id,
                    (SELECT COUNT(*) FROM recipes r WHERE r.cookbook_id = p.cookbook_id) AS total_steps,
                    (SELECT COUNT(*) FROM artifacts a WHERE a.project_id = p.id AND a.status = 'approved') AS done_steps
             FROM projects p
             JOIN users u ON u.id = p.user_id
             WHERE u.simulation = 1 AND p.completed_at IS NULL AND p.pantry_saved_at IS NOT NULL"
        );

        if ($rows === []) {
            return 0;
        }

        $sum = 0;
        foreach ($rows as $row) {
            $total = max(1, (int) $row['total_steps']);
            $sum += (int) round(((int) $row['done_steps'] / $total) * 100);
        }

        return (int) round($sum / count($rows));
    }

    private static function utcDaysAgo(int $days): string
    {
        return (new \DateTimeImmutable('now', Simulation::utc()))
            ->modify('-' . $days . ' days')
            ->format('Y-m-d H:i:s');
    }

    private static function sqlDaysAfter(string $column, int $days): string
    {
        if (Database::driver() === 'mysql') {
            return "DATE_ADD({$column}, INTERVAL {$days} DAY)";
        }

        return "datetime({$column}, '+{$days} days')";
    }

    private static function sqlProjectSpanAtLeast(int $days): string
    {
        if (Database::driver() === 'mysql') {
            return "DATEDIFF(p.completed_at, p.created_at) >= {$days}";
        }

        return "julianday(p.completed_at) - julianday(p.created_at) >= {$days}";
    }
}
