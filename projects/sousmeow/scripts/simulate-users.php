<?php

declare(strict_types=1);

/**
 * Create the simulation user pool (kitchen+1@demo.local … +POOL_SIZE).
 *
 * Usage:
 *   php scripts/simulate-users.php
 *   php scripts/simulate-users.php --fresh   Remove simulation users, re-create pool
 *   php scripts/simulate-users.php --status
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;
use SousMeow\Models\User;
use SousMeow\Services\Simulation;

$fresh = in_array('--fresh', $argv, true);
$status = in_array('--status', $argv, true);

if ($status) {
    print_status();
    exit(0);
}

if ($fresh) {
    $n = (int) Database::fetchValue('SELECT COUNT(*) FROM users WHERE simulation = 1');
    Database::run('DELETE FROM users WHERE simulation = 1');
    fwrite(STDOUT, "Removed {$n} simulation user(s).\n");
}

$personas = Simulation::loadPersonas();
$passwordHash = password_hash(Simulation::PASSWORD, PASSWORD_DEFAULT);
$created = 0;
$nowPacific = new DateTimeImmutable('now', Simulation::pacific());

foreach ($personas as $persona) {
    $id = (int) $persona['id'];
    $email = Simulation::emailForId($id);
    if (User::findByEmail($email) !== null) {
        continue;
    }

    $daysAgo = random_int(3, 75);
    $signupLocal = $nowPacific->modify('-' . $daysAgo . ' days')->setTime(random_int(8, 21), random_int(0, 59));
    $signupUtc = $signupLocal->setTimezone(Simulation::utc())->format('Y-m-d H:i:s');

    Database::run(
        'INSERT INTO users (name, email, password_hash, role, simulation, email_verified_at, onboarding_completed_at, created_at) VALUES (?, ?, ?, ?, 1, ?, ?, ?)',
        [(string) $persona['name'], $email, $passwordHash, 'user', $signupUtc, $signupUtc, $signupUtc]
    );
    $created++;
}

fwrite(STDOUT, "Simulation users ready: {$created} inserted, " . count($personas) . " personas in pool.\n");
fwrite(STDOUT, 'Login: ' . Simulation::emailForId(1) . ' … ' . Simulation::emailForId(Simulation::POOL_SIZE) . "\n");
fwrite(STDOUT, 'Password (all): ' . Simulation::PASSWORD . "\n");
print_status();

function print_status(): void
{
    $sim = (int) Database::fetchValue('SELECT COUNT(*) FROM users WHERE simulation = 1');
    fwrite(STDOUT, "\nSimulation pool: {$sim} users (target " . Simulation::POOL_SIZE . ")\n");
    fwrite(STDOUT, "Shared password: " . Simulation::PASSWORD . "\n");
}
