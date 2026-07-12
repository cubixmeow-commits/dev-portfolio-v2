<?php

declare(strict_types=1);

/**
 * One-time generator for database/simulation/personas.json (500 chefs).
 * US-weighted (~65%) with international diversity. Re-run only to regenerate.
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$out = __DIR__ . '/../database/simulation/personas.json';
$target = 500;
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
];

$personas = [];
$usedNames = [];

for ($i = 1; $i <= $usTarget; $i++) {
    do {
        $name = $usFirst[array_rand($usFirst)] . ' ' . $usLast[array_rand($usLast)];
    } while (isset($usedNames[$name]));
    $usedNames[$name] = true;
    $personas[] = ['id' => $i, 'name' => $name, 'country' => 'United States', 'code' => 'US'];
}

$intlCount = $target - $usTarget;
for ($i = 0; $i < $intlCount; $i++) {
    $pool = $intlPools[$i % count($intlPools)];
    do {
        $name = $pool['first'][array_rand($pool['first'])] . ' ' . $pool['last'][array_rand($pool['last'])];
    } while (isset($usedNames[$name]));
    $usedNames[$name] = true;
    $personas[] = [
        'id'      => $usTarget + $i + 1,
        'name'    => $name,
        'country' => $pool['country'],
        'code'    => $pool['code'],
    ];
}

shuffle($personas);
foreach ($personas as $idx => &$p) {
    $p['id'] = $idx + 1;
}
unset($p);

$dir = dirname($out);
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

file_put_contents($out, json_encode($personas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
echo "Wrote " . count($personas) . " personas to {$out}\n";
$us = count(array_filter($personas, static fn(array $p): bool => $p['code'] === 'US'));
echo "US: {$us}, International: " . ($target - $us) . "\n";
