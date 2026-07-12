<?php

declare(strict_types=1);

/**
 * SousMeow database setup and seeding. CLI only; there is deliberately
 * no web-accessible installer.
 *
 * Usage:
 *   php scripts/seed.php                          Create schema, seed content,
 *                                                 create the admin account if
 *                                                 missing (prints a temporary
 *                                                 password once).
 *   php scripts/seed.php --admin-email you@x.com  Choose the admin email on
 *                                                 first creation.
 *   php scripts/seed.php --reset-password EMAIL   Generate and print a new
 *                                                 temporary password for an
 *                                                 existing account.
 *   php scripts/seed.php --fresh                  Drop all tables first.
 *                                                 Destroys data; asks for
 *                                                 confirmation.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Core\Database;

$args = $argv;
array_shift($args);

$options = [
    'fresh'          => false,
    'admin_email'    => 'chef@sousmeow.example',
    'reset_password' => null,
];
for ($i = 0; $i < count($args); $i++) {
    switch ($args[$i]) {
        case '--fresh':
            $options['fresh'] = true;
            break;
        case '--admin-email':
            $options['admin_email'] = $args[++$i] ?? $options['admin_email'];
            break;
        case '--reset-password':
            $options['reset_password'] = $args[++$i] ?? null;
            break;
        default:
            fwrite(STDERR, "Unknown option: {$args[$i]}\n");
            exit(1);
    }
}

$pdo = Database::pdo();
$driver = Database::driver();

/** Generate a readable temporary password: 4 words + 2 digits. */
function temp_password(): string
{
    $words = ['maple', 'thyme', 'ember', 'clove', 'birch', 'honey', 'cedar', 'plume',
              'saffron', 'juniper', 'cocoa', 'basil', 'amber', 'willow', 'pepper', 'sage'];
    $picks = [];
    foreach (array_rand($words, 4) as $idx) {
        $picks[] = $words[$idx];
    }
    return implode('-', $picks) . '-' . random_int(10, 99);
}

// --reset-password: standalone maintenance action.
if ($options['reset_password'] !== null) {
    $email = strtolower(trim($options['reset_password']));
    $user = Database::fetch('SELECT id, email FROM users WHERE email = ?', [$email]);
    if ($user === null) {
        fwrite(STDERR, "No account found for {$email}.\n");
        exit(1);
    }
    $password = temp_password();
    Database::run('UPDATE users SET password_hash = ? WHERE id = ?', [
        password_hash($password, PASSWORD_DEFAULT),
        $user['id'],
    ]);
    echo "Temporary password for {$user['email']}: {$password}\n";
    echo "Share it over a safe channel and change it after signing in.\n";
    exit(0);
}

// --fresh: drop everything after an explicit confirmation.
if ($options['fresh']) {
    echo "This drops ALL SousMeow tables and data ({$driver}). Type 'yes' to continue: ";
    $answer = trim((string) fgets(STDIN));
    if ($answer !== 'yes') {
        echo "Aborted.\n";
        exit(0);
    }
    $tables = ['artifact_checks', 'artifact_versions', 'artifacts', 'exports', 'pantry_values',
               'projects', 'pantry_fields', 'recipe_checks', 'recipes', 'cookbooks',
               'rate_events', 'users'];
    if ($driver === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    }
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS {$table}");
    }
    if ($driver === 'mysql') {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
    echo "Dropped existing tables.\n";
}

// 1. Schema.
$schemaFile = __DIR__ . '/../database/schema.' . ($driver === 'mysql' ? 'mysql' : 'sqlite') . '.sql';
$sql = (string) file_get_contents($schemaFile);
// Drop comment lines first; they may contain semicolons, and the
// statement splitter below is a plain explode on ';'.
$sql = implode("\n", array_filter(
    explode("\n", $sql),
    static fn(string $line): bool => !str_starts_with(ltrim($line), '--')
));
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}
echo "Schema ready ({$driver}).\n";

// 2. Content. Idempotent: cookbooks are seeded once.
$existing = (int) Database::fetchValue('SELECT COUNT(*) FROM cookbooks');
if ($existing > 0) {
    echo "Content already seeded ({$existing} cookbooks), skipping.\n";
} else {
    /** @var array{executable: array<string, mixed>, marketplace: list<array<string, mixed>>} $content */
    $content = require __DIR__ . '/../database/seeds/content.php';

    Database::transaction(function () use ($content): void {
        $now = now_utc();

        $insertCookbook = static function (array $book, bool $executable) use ($now): int {
            Database::run(
                'INSERT INTO cookbooks (slug, title, tagline, description, category, audience, outcome,
                                        price_cents, is_executable, status, accent, est_minutes, sort_order, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $book['slug'], $book['title'], $book['tagline'], $book['description'],
                    $book['category'], $book['audience'], $book['outcome'],
                    $book['price_cents'], $executable ? 1 : 0,
                    $executable ? 'available' : 'coming_soon',
                    $book['accent'], $book['est_minutes'], $book['sort_order'], $now,
                ]
            );
            return Database::lastInsertId();
        };

        // The executable sample Cookbook, in full.
        $book = $content['executable'];
        $cookbookId = $insertCookbook($book, true);

        foreach ($book['fields'] as $position => $field) {
            Database::run(
                'INSERT INTO pantry_fields (cookbook_id, position, field_key, label, type, help,
                                            placeholder, options, required, sample_value)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $cookbookId, $position + 1, $field['field_key'], $field['label'], $field['type'],
                    $field['help'] ?? '', $field['placeholder'] ?? '',
                    isset($field['options']) ? json_encode($field['options']) : null,
                    $field['required'] ?? 1, $field['sample_value'] ?? '',
                ]
            );
        }

        foreach ($book['recipes'] as $position => $recipe) {
            Database::run(
                'INSERT INTO recipes (cookbook_id, position, slug, title, summary, why_it_matters,
                                      unlocks_text, prompt_template, example_response, est_minutes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $cookbookId, $position + 1, $recipe['slug'], $recipe['title'], $recipe['summary'],
                    $recipe['why_it_matters'], $recipe['unlocks_text'],
                    $recipe['prompt_template'], $recipe['example_response'], $recipe['est_minutes'],
                ]
            );
            $recipeId = Database::lastInsertId();
            foreach ($recipe['checks'] as $checkPos => $check) {
                Database::run(
                    'INSERT INTO recipe_checks (recipe_id, position, label, help) VALUES (?, ?, ?, ?)',
                    [$recipeId, $checkPos + 1, $check['label'], $check['help']]
                );
            }
        }

        // Marketplace shells: presentation-complete, intentionally
        // non-executable (no prompts, no examples, no pantry).
        foreach ($content['marketplace'] as $book) {
            $cookbookId = $insertCookbook($book, false);
            foreach ($book['recipes'] as $position => $recipe) {
                Database::run(
                    'INSERT INTO recipes (cookbook_id, position, slug, title, summary, why_it_matters,
                                          unlocks_text, prompt_template, example_response, est_minutes)
                     VALUES (?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?)',
                    [
                        $cookbookId, $position + 1, $recipe['slug'], $recipe['title'],
                        $recipe['summary'], '', '', 5,
                    ]
                );
            }
        }
    });
    echo "Seeded 1 executable cookbook and " . count($content['marketplace']) . " marketplace cookbooks.\n";
}

// 3. Admin account, created once. The temporary password is printed
// exactly once and never stored in plain text.
$adminCount = (int) Database::fetchValue("SELECT COUNT(*) FROM users WHERE role = 'admin'");
if ($adminCount > 0) {
    echo "Admin account already exists, skipping.\n";
} else {
    $email = strtolower(trim($options['admin_email']));
    $password = temp_password();
    Database::run(
        'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)',
        ['Head Chef', $email, password_hash($password, PASSWORD_DEFAULT), 'admin', now_utc()]
    );
    echo "\nAdmin account created.\n";
    echo "  Email:              {$email}\n";
    echo "  Temporary password: {$password}\n";
    echo "Sign in and change this password. It is shown only this once.\n";
}

echo "\nDone. Start the app with:\n  php -S localhost:8090 -t public public/index.php\n";
