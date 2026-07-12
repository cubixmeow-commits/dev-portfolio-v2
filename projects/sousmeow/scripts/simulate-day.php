<?php

declare(strict_types=1);

/**
 * Preload one Pacific calendar day of kitchen activity in a single run.
 *
 * Usage:
 *   php scripts/simulate-day.php --date=yesterday
 *   php scripts/simulate-day.php --date=2026-07-11
 *   php scripts/simulate-day.php --date=today --force
 *   php scripts/simulate-day.php --status
 *
 * Prerequisite: php scripts/seed.php && php scripts/simulate-users.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;
use SousMeow\Services\Simulation;
use SousMeow\Services\SimulationKitchen;

$status = in_array('--status', $argv, true);
$force = in_array('--force', $argv, true);
$dateArg = 'yesterday';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--date=')) {
        $dateArg = substr($arg, 7);
    }
}

if ($status) {
    print_status();
    exit(0);
}

$pacificDate = Simulation::resolvePacificDate($dateArg);
$existing = Database::fetch('SELECT * FROM simulation_runs WHERE pacific_date = ?', [$pacificDate]);
if ($existing !== null && !$force) {
    fwrite(STDOUT, "Already simulated {$pacificDate} (PT). Use --force to run again.\n");
    print_status();
    exit(0);
}

$simUsers = Database::fetchAll('SELECT id, name FROM users WHERE simulation = 1 ORDER BY id');
if (count($simUsers) < 50) {
    fwrite(STDERR, "Simulation pool too small (" . count($simUsers) . "). Run php scripts/simulate-users.php\n");
    exit(1);
}

SimulationKitchen::catalog();

$slugWeights = ['launch-day-kit' => 40, 'plan-youtube-video' => 30, 'validate-saas-idea' => 30];
$productTitles = [
    'Driftlog', 'Taskloom', 'Clearcast', 'Northline', 'Patchwork', 'Harborlight', 'Sidekick OS',
    'Meridian', 'Papertrail', 'Brightloom', 'Fieldnote', 'Stackwell', 'Openframe', 'Rivermark',
    'Signalhouse', 'Craftlane', 'Bluehour', 'Nestform', 'Waypoint', 'Threadline', 'Launchpad X',
    'Studio Nine', 'Copperline', 'Daybreak', 'Foxglove', 'Kitehouse', 'Moonjar', 'Pinecode',
];

$poolSize = count($simUsers);
$activeCount = max(40, (int) round($poolSize * random_int(12, 18) / 100));
shuffle($simUsers);
$activeUsers = array_slice($simUsers, 0, $activeCount);

$actions = 0;
fwrite(STDOUT, "Simulating {$pacificDate} (Pacific): {$activeCount} active chefs...\n");

foreach ($activeUsers as $user) {
    $userId = (int) $user['id'];
    $roll = random_int(1, 100);
    $ts = Simulation::randomUtcInPacificDay($pacificDate);

    if ($roll <= 35) {
        $slug = SimulationKitchen::pickCookbookSlug($slugWeights);
        $title = $productTitles[random_int(0, count($productTitles) - 1)];
        $projectId = SimulationKitchen::createProject($userId, $title, $slug, $ts);
        if (random_int(1, 100) > 20) {
            $pantryTs = Simulation::randomUtcInPacificDay($pacificDate);
            SimulationKitchen::stockPantry($projectId, $slug, $pantryTs);
            $actions += 2;
        } else {
            $actions++;
        }
        continue;
    }

    $open = SimulationKitchen::randomOpenProject($userId);
    if ($open === null) {
        $slug = SimulationKitchen::pickCookbookSlug($slugWeights);
        $title = $productTitles[random_int(0, count($productTitles) - 1)];
        $projectId = SimulationKitchen::createProject($userId, $title, $slug, $ts);
        SimulationKitchen::stockPantry($projectId, $slug, Simulation::randomUtcInPacificDay($pacificDate));
        $actions += 2;
        continue;
    }

    $projectId = (int) $open['id'];
    $slug = (string) $open['cookbook_slug'];
    $total = SimulationKitchen::recipeTotal($slug);
    $approved = SimulationKitchen::approvedCount($projectId);

    if ($open['pantry_saved_at'] === null) {
        SimulationKitchen::stockPantry($projectId, $slug, $ts);
        $actions++;
        continue;
    }

    if ($approved >= $total) {
        continue;
    }

    if ($roll <= 75) {
        $target = min($total, $approved + random_int(1, max(1, (int) ceil($total * 0.35))));
        SimulationKitchen::approveRecipes($projectId, $slug, $target, $ts);
        $actions++;
        continue;
    }

    SimulationKitchen::completeProject($projectId, $slug, $ts);
    $actions += 2;
}

$executedAt = gmdate('Y-m-d H:i:s');
if (Database::driver() === 'mysql') {
    Database::run(
        'INSERT INTO simulation_runs (pacific_date, users_active, actions_count, executed_at)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE users_active = VALUES(users_active),
             actions_count = VALUES(actions_count), executed_at = VALUES(executed_at)',
        [$pacificDate, $activeCount, $actions, $executedAt]
    );
} else {
    Database::run(
        'INSERT INTO simulation_runs (pacific_date, users_active, actions_count, executed_at)
         VALUES (?, ?, ?, ?)
         ON CONFLICT(pacific_date) DO UPDATE SET users_active = excluded.users_active,
             actions_count = excluded.actions_count, executed_at = excluded.executed_at',
        [$pacificDate, $activeCount, $actions, $executedAt]
    );
}

fwrite(STDOUT, "Done {$pacificDate}: {$activeCount} chefs, ~{$actions} actions.\n");
print_status();

function print_status(): void
{
    $runs = Database::fetchAll('SELECT * FROM simulation_runs ORDER BY pacific_date DESC LIMIT 7');
    fwrite(STDOUT, "\nRecent simulation runs (Pacific date):\n");
    if ($runs === []) {
        fwrite(STDOUT, "  (none)\n");
        return;
    }
    foreach ($runs as $run) {
        fwrite(STDOUT, sprintf(
            "  %s — %d active, %d actions, executed %s UTC\n",
            $run['pacific_date'],
            $run['users_active'],
            $run['actions_count'],
            $run['executed_at']
        ));
    }
}
