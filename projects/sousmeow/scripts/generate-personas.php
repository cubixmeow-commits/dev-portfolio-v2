<?php

declare(strict_types=1);

/**
 * Generate or extend database/simulation/personas.json.
 *
 * Usage:
 *   php scripts/generate-personas.php          Append missing personas up to POOL_SIZE
 *   php scripts/generate-personas.php --fresh  Regenerate the full pool from scratch
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Services\Simulation;

$out = __DIR__ . '/../database/simulation/personas.json';
$target = Simulation::POOL_SIZE;
$fresh = in_array('--fresh', $argv, true);
$usTarget = (int) round($target * 0.65);

$usFirst = [
    'James', 'Maria', 'Michael', 'Sarah', 'David', 'Jennifer', 'Robert', 'Lisa', 'William', 'Karen',
    'Richard', 'Nancy', 'Joseph', 'Betty', 'Thomas', 'Sandra', 'Christopher', 'Ashley', 'Daniel', 'Emily',
    'Matthew', 'Amanda', 'Anthony', 'Melissa', 'Mark', 'Deborah', 'Donald', 'Stephanie', 'Steven', 'Rebecca',
    'Andrew', 'Laura', 'Joshua', 'Sharon', 'Kevin', 'Cynthia', 'Brian', 'Kathleen', 'George', 'Amy',
    'Timothy', 'Angela', 'Ronald', 'Shirley', 'Jason', 'Anna', 'Ryan', 'Ruth', 'Jacob', 'Brenda',
    'Tyler', 'Pamela', 'Brandon', 'Nicole', 'Aaron', 'Katherine', 'Ethan', 'Samantha', 'Noah', 'Christine',
    'Mason', 'Debra', 'Logan', 'Rachel', 'Aiden', 'Carolyn', 'Liam', 'Janet', 'Lucas', 'Catherine',
    'Mia', 'Heather', 'Ella', 'Diane', 'Harper', 'Julie', 'Evelyn', 'Joyce', 'Abigail', 'Victoria',
    'Emma', 'Olivia', 'Sophia', 'Isabella', 'Charlotte', 'Amelia', 'Hannah', 'Grace', 'Chloe', 'Zoe',
    'Nathan', 'Caleb', 'Dylan', 'Jordan', 'Cameron', 'Hunter', 'Austin', 'Blake', 'Connor', 'Colton',
    'Wesley', 'Paige', 'Grant', 'Brooke', 'Spencer', 'Morgan', 'Trevor', 'Kelsey', 'Derek', 'Jillian',
];

$usLast = [
    'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
    'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
    'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
    'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
    'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell', 'Carter', 'Roberts',
    'Gomez', 'Phillips', 'Evans', 'Turner', 'Diaz', 'Parker', 'Cruz', 'Edwards', 'Collins', 'Reyes',
    'Stewart', 'Morris', 'Morales', 'Murphy', 'Cook', 'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Cooper',
    'Peterson', 'Bailey', 'Reed', 'Kelly', 'Howard', 'Ramos', 'Kim', 'Ward', 'Cox', 'Richardson',
    'Watson', 'Brooks', 'Chavez', 'Wood', 'James', 'Bennett', 'Gray', 'Mendoza', 'Ruiz', 'Hughes',
    'Price', 'Alvarez', 'Castillo', 'Sanders', 'Patel', 'Myers', 'Long', 'Ross', 'Foster', 'Jimenez',
];

/** @var list<array{country: string, code: string, first: list<string>, last: list<string>}> */
$intlPools = [
    ['code' => 'CA', 'country' => 'Canada', 'first' => ['Liam', 'Olivia', 'Noah', 'Emma', 'Lucas', 'Sophie', 'Mateo', 'Camille'], 'last' => ['Tremblay', 'Roy', 'Gagnon', 'Singh', 'Chen', 'MacDonald', 'Leblanc', 'Patel']],
    ['code' => 'GB', 'country' => 'United Kingdom', 'first' => ['Oliver', 'Amelia', 'Harry', 'Isla', 'George', 'Freya', 'Arthur', 'Poppy'], 'last' => ['Smith', 'Jones', 'Taylor', 'Brown', 'Williams', 'Davies', 'Evans', 'Wilson']],
    ['code' => 'IN', 'country' => 'India', 'first' => ['Priya', 'Arjun', 'Ananya', 'Rohan', 'Kavya', 'Vikram', 'Meera', 'Aditya'], 'last' => ['Sharma', 'Patel', 'Singh', 'Kumar', 'Reddy', 'Iyer', 'Gupta', 'Nair']],
    ['code' => 'BR', 'country' => 'Brazil', 'first' => ['Lucas', 'Ana', 'Gabriel', 'Julia', 'Miguel', 'Larissa', 'Rafael', 'Camila'], 'last' => ['Silva', 'Santos', 'Oliveira', 'Souza', 'Lima', 'Costa', 'Ferreira', 'Almeida']],
    ['code' => 'MX', 'country' => 'Mexico', 'first' => ['Santiago', 'Valentina', 'Diego', 'Sofia', 'Mateo', 'Camila', 'Emilio', 'Regina'], 'last' => ['Hernandez', 'Garcia', 'Martinez', 'Lopez', 'Gonzalez', 'Rodriguez', 'Perez', 'Sanchez']],
    ['code' => 'DE', 'country' => 'Germany', 'first' => ['Lukas', 'Mia', 'Finn', 'Emma', 'Leon', 'Hannah', 'Paul', 'Lea'], 'last' => ['Muller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Meyer', 'Wagner', 'Becker']],
    ['code' => 'FR', 'country' => 'France', 'first' => ['Louis', 'Camille', 'Hugo', 'Chloe', 'Gabriel', 'Ines', 'Raphael', 'Manon'], 'last' => ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand']],
    ['code' => 'NG', 'country' => 'Nigeria', 'first' => ['Chidi', 'Amara', 'Tunde', 'Ngozi', 'Emeka', 'Folake', 'Ibrahim', 'Aisha'], 'last' => ['Okafor', 'Adeyemi', 'Bello', 'Eze', 'Okonkwo', 'Musa', 'Nwosu', 'Adebayo']],
    ['code' => 'JP', 'country' => 'Japan', 'first' => ['Haruto', 'Yui', 'Sota', 'Hina', 'Ren', 'Aoi', 'Kaito', 'Mei'], 'last' => ['Sato', 'Suzuki', 'Takahashi', 'Tanaka', 'Watanabe', 'Ito', 'Yamamoto', 'Nakamura']],
    ['code' => 'KR', 'country' => 'South Korea', 'first' => ['Min-jun', 'Seo-yeon', 'Do-yun', 'Ji-woo', 'Ha-jun', 'Su-bin'], 'last' => ['Kim', 'Lee', 'Park', 'Choi', 'Jung', 'Kang']],
    ['code' => 'AU', 'country' => 'Australia', 'first' => ['Jack', 'Charlotte', 'Oliver', 'Amelia', 'William', 'Isla'], 'last' => ['Wilson', 'Taylor', 'Anderson', 'Thomas', 'White', 'Harris']],
    ['code' => 'PH', 'country' => 'Philippines', 'first' => ['Jose', 'Maria', 'Angelo', 'Grace', 'Mark', 'Angelica'], 'last' => ['Reyes', 'Santos', 'Cruz', 'Bautista', 'Garcia', 'Mendoza']],
    ['code' => 'PK', 'country' => 'Pakistan', 'first' => ['Ahmed', 'Fatima', 'Hassan', 'Ayesha', 'Usman', 'Zainab'], 'last' => ['Khan', 'Malik', 'Hussain', 'Sheikh', 'Butt', 'Chaudhry']],
    ['code' => 'EG', 'country' => 'Egypt', 'first' => ['Omar', 'Nour', 'Youssef', 'Salma', 'Karim', 'Layla'], 'last' => ['Hassan', 'Ali', 'Ibrahim', 'Mahmoud', 'Farouk', 'Nasser']],
    ['code' => 'CO', 'country' => 'Colombia', 'first' => ['Santiago', 'Valeria', 'Sebastian', 'Mariana', 'Nicolas', 'Laura'], 'last' => ['Gomez', 'Rodriguez', 'Martinez', 'Lopez', 'Diaz', 'Moreno']],
    ['code' => 'ES', 'country' => 'Spain', 'first' => ['Pablo', 'Lucia', 'Hugo', 'Martina', 'Alejandro', 'Sofia'], 'last' => ['Garcia', 'Fernandez', 'Gonzalez', 'Rodriguez', 'Lopez', 'Martinez']],
    ['code' => 'IT', 'country' => 'Italy', 'first' => ['Leonardo', 'Giulia', 'Francesco', 'Sofia', 'Alessandro', 'Aurora'], 'last' => ['Rossi', 'Russo', 'Ferrari', 'Esposito', 'Bianchi', 'Romano']],
    ['code' => 'PL', 'country' => 'Poland', 'first' => ['Jakub', 'Zuzanna', 'Jan', 'Julia', 'Filip', 'Maja'], 'last' => ['Nowak', 'Kowalski', 'Lewandowski', 'Wojcik', 'Kaminski', 'Zielinski']],
    ['code' => 'ZA', 'country' => 'South Africa', 'first' => ['Thabo', 'Naledi', 'Sipho', 'Lerato', 'Mandla', 'Zanele'], 'last' => ['Nkosi', 'Dlamini', 'Mokoena', 'Pillay', 'Botha', 'Van Wyk']],
    ['code' => 'AR', 'country' => 'Argentina', 'first' => ['Mateo', 'Sofia', 'Benicio', 'Emma', 'Thiago', 'Mia'], 'last' => ['Gonzalez', 'Rodriguez', 'Fernandez', 'Lopez', 'Martinez', 'Garcia']],
    ['code' => 'NL', 'country' => 'Netherlands', 'first' => ['Daan', 'Emma', 'Sem', 'Sophie', 'Lucas', 'Julia'], 'last' => ['De Vries', 'Van Dijk', 'Bakker', 'Visser', 'Smit', 'Meijer']],
    ['code' => 'SE', 'country' => 'Sweden', 'first' => ['Erik', 'Astrid', 'Oscar', 'Elsa', 'Hugo', 'Maja'], 'last' => ['Andersson', 'Johansson', 'Karlsson', 'Nilsson', 'Eriksson', 'Larsson']],
];

$personas = [];
$usedNames = [];

if (!$fresh && is_file($out)) {
    $existing = json_decode((string) file_get_contents($out), true);
    if (is_array($existing)) {
        foreach ($existing as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id = (int) ($row['id'] ?? 0);
            if ($id < 1 || $id > $target) {
                continue;
            }
            $personas[$id] = $row;
            $usedNames[(string) ($row['name'] ?? '')] = true;
        }
    }
}

$startId = count($personas) > 0 ? max(array_keys($personas)) + 1 : 1;
if ($fresh) {
    $personas = [];
    $usedNames = [];
    $startId = 1;
}

$makeUniqueName = static function (callable $picker) use (&$usedNames): string {
    do {
        $name = $picker();
    } while (isset($usedNames[$name]));
    $usedNames[$name] = true;
    return $name;
};

for ($id = $startId; $id <= $target; $id++) {
    if (isset($personas[$id])) {
        continue;
    }
    $isUs = $id <= $usTarget || random_int(1, 100) <= 65;
    if ($isUs) {
        $name = $makeUniqueName(static fn(): string => $usFirst[array_rand($usFirst)] . ' ' . $usLast[array_rand($usLast)]);
        $personas[$id] = ['id' => $id, 'name' => $name, 'country' => 'United States', 'code' => 'US'];
    } else {
        $pool = $intlPools[($id - 1) % count($intlPools)];
        $name = $makeUniqueName(static fn() => $pool['first'][array_rand($pool['first'])] . ' ' . $pool['last'][array_rand($pool['last'])]);
        $personas[$id] = ['id' => $id, 'name' => $name, 'country' => $pool['country'], 'code' => $pool['code']];
    }
}

ksort($personas);
$personas = array_values($personas);

$dir = dirname($out);
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

file_put_contents($out, json_encode($personas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
$added = max(0, count($personas) - ($startId - 1));
echo "Wrote " . count($personas) . " personas to {$out} (target {$target})\n";
if (!$fresh && $startId > 1) {
    echo "Appended from id {$startId}\n";
}
$us = count(array_filter($personas, static fn(array $p): bool => $p['code'] === 'US'));
echo "US: {$us}, International: " . (count($personas) - $us) . "\n";
