<?php

declare(strict_types=1);

/**
 * CLI test suite for the deterministic output-contract system:
 * ResponseParser (structural section extraction) and OutputContract
 * (validation). Also parses every seeded example response of the
 * contract-bearing Cookbooks against their contracts, so seed drift
 * between prompts, samples, and contracts fails here instead of in
 * front of a user. No database needed.
 *
 * Usage: php scripts/verify-parser.php
 */

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require __DIR__ . '/../app/bootstrap.php';

use SousMeow\Services\OutputContract;
use SousMeow\Services\ResponseParser;

$failures = 0;
$tests = 0;

function check(string $name, bool $ok, string $detail = ''): void
{
    global $failures, $tests;
    $tests++;
    if ($ok) {
        echo "  ok: {$name}\n";
    } else {
        $failures++;
        echo "  FAIL: {$name}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
    }
}

/** Shorthand contract builder. */
function sections(array ...$defs): array
{
    return array_map(static fn(array $d): array => [
        'key'      => $d[0],
        'heading'  => $d[1],
        'aliases'  => $d[2] ?? [],
        'required' => $d[3] ?? true,
    ], $defs);
}

$contract = sections(
    ['alpha', 'Target Audience', ['Primary Audience', 'Audience']],
    ['beta', 'Core Promise', ['Viewer Promise']],
    ['gamma', 'Optional Notes', [], false],
);

echo "ResponseParser\n";

// Canonical heading matching, content extraction, order independence.
$r = ResponseParser::parse("## Target Audience\nPeople.\n\n## Core Promise\nA promise.", $contract);
check('canonical headings located', isset($r['sections']['alpha'], $r['sections']['beta']));
check('section content extracted', ($r['sections']['alpha'][0]['content'] ?? '') === 'People.');
check('optional section missing is not required-missing',
    in_array('gamma', $r['missing'], true) && !in_array('gamma', $r['missing_required'], true));

// Alias matching.
$r = ResponseParser::parse("## Primary Audience\nPeople.\n## Viewer Promise\nYes.", $contract);
check('aliases map to section keys', isset($r['sections']['alpha'], $r['sections']['beta']));
check('matched heading text preserved', ($r['sections']['alpha'][0]['heading'] ?? '') === 'Primary Audience');

// Case differences, trailing punctuation, emphasis wrappers, ATX levels 1-3.
$r = ResponseParser::parse("# TARGET AUDIENCE:\nA.\n### **Core Promise.**\nB.", $contract);
check('case-insensitive matching', isset($r['sections']['alpha']));
check('trailing punctuation and emphasis ignored', isset($r['sections']['beta']));

// Windows line endings.
$r = ResponseParser::parse("## Target Audience\r\nCRLF people.\r\n\r\n## Core Promise\r\nFine.", $contract);
check('CRLF input parses', ($r['sections']['alpha'][0]['content'] ?? '') === 'CRLF people.');

// Missing required section.
$r = ResponseParser::parse("## Target Audience\nOnly this.", $contract);
check('missing required section reported', in_array('beta', $r['missing_required'], true));

// Empty section.
$r = ResponseParser::parse("## Target Audience\n\n## Core Promise\nText.", $contract);
check('empty section flagged', in_array('alpha', $r['empty'], true) && ($r['sections']['alpha'][0]['empty'] ?? false));

// Duplicate section: never merged, all blocks kept.
$r = ResponseParser::parse("## Core Promise\nFirst.\n## Core Promise\nSecond.", $contract);
check('duplicate section flagged', in_array('beta', $r['duplicates'], true));
check('all duplicate blocks preserved', count($r['sections']['beta'] ?? []) === 2
    && $r['sections']['beta'][0]['content'] === 'First.' && $r['sections']['beta'][1]['content'] === 'Second.');

// Unexpected heading.
$r = ResponseParser::parse("## Random Thoughts\nHm.\n## Core Promise\nOk.", $contract);
check('unexpected heading reported', in_array('Random Thoughts', $r['unexpected'], true));

// Headings inside fenced code blocks are not document sections.
$r = ResponseParser::parse("## Target Audience\nBefore.\n```\n## Core Promise\nnot a section\n```\nAfter.", $contract);
check('heading inside code fence ignored', in_array('beta', $r['missing'], true));
check('fenced content stays in surrounding section',
    str_contains($r['sections']['alpha'][0]['content'] ?? '', '## Core Promise'));
$r = ResponseParser::parse("~~~\n## Core Promise\n~~~\n## Target Audience\nA.", $contract);
check('tilde fences respected', in_array('beta', $r['missing'], true) && isset($r['sections']['alpha']));

// Content before the first recognized heading.
$r = ResponseParser::parse("Sure! Here's what you asked for.\n\n## Target Audience\nA.", $contract);
check('preamble captured', $r['preamble'] === "Sure! Here's what you asked for.");
$r = ResponseParser::parse("No headings at all, just prose.", $contract);
check('headingless response is all preamble',
    $r['preamble'] === 'No headings at all, just prose.' && count($r['missing']) === 3);

// Deeper subheadings (####+) stay inside their parent section.
$r = ResponseParser::parse("## Target Audience\nIntro.\n#### Detail\nMore.\n## Core Promise\nP.", $contract);
check('h4 subheading stays in section', str_contains($r['sections']['alpha'][0]['content'] ?? '', '#### Detail'));

// Unrecognized deeper (###) subheading under a ## section stays inside it.
$r = ResponseParser::parse("## Core Promise\nLead.\n### Sub-block\nNested.\n## Target Audience\nA.", $contract);
check('unrecognized deeper heading nests inside section',
    str_contains($r['sections']['beta'][0]['content'] ?? '', '### Sub-block')
    && !in_array('Sub-block', $r['unexpected'], true));

// A recognized heading always ends the previous section, whatever its level.
$r = ResponseParser::parse("## Core Promise\nLead.\n### Target Audience\nNested but declared.", $contract);
check('recognized heading ends previous section',
    isset($r['sections']['alpha']) && !str_contains($r['sections']['beta'][0]['content'], 'Nested'));

echo "\ncheckStatus\n";

$parsed = ResponseParser::parse("## Target Audience\nA.\n## Core Promise\nB.", $contract);
check('no mapping => manual', ResponseParser::checkStatus([], $parsed) === ResponseParser::STATUS_MANUAL);
check('single evidence section => located', ResponseParser::checkStatus(['alpha'], $parsed) === ResponseParser::STATUS_LOCATED);
check('multiple evidence sections for one check => located',
    ResponseParser::checkStatus(['alpha', 'beta'], $parsed) === ResponseParser::STATUS_LOCATED);
check('one section can serve multiple checks',
    ResponseParser::checkStatus(['alpha'], $parsed) === ResponseParser::checkStatus(['alpha', 'gamma'], $parsed));
check('absent section => missing', ResponseParser::checkStatus(['gamma'], $parsed) === ResponseParser::STATUS_MISSING);

$parsed = ResponseParser::parse("## Target Audience\n\n## Core Promise\nB.", $contract);
check('empty evidence section counts as missing',
    ResponseParser::checkStatus(['alpha'], $parsed) === ResponseParser::STATUS_MISSING);

$parsed = ResponseParser::parse("## Target Audience\nA.\n## Target Audience\nAgain.", $contract);
check('duplicated evidence section => multiple',
    ResponseParser::checkStatus(['alpha', 'beta'], $parsed) === ResponseParser::STATUS_MULTIPLE);

$parsed = ResponseParser::parse('Plain unstructured response.', $contract);
check('unstructured response => missing, never an error',
    ResponseParser::checkStatus(['alpha'], $parsed) === ResponseParser::STATUS_MISSING);

echo "\nOutputContract validation\n";

check('recipe with no contract is valid', OutputContract::validate(null, [['label' => 'x']]) === []);
check('check with no evidence mapping is valid',
    OutputContract::validate(sections(['a', 'Head']), [['label' => 'x']]) === []);
check('valid contract passes',
    OutputContract::validate($contract, [['label' => 'x', 'evidence_sections' => ['alpha', 'beta']]]) === []);
check('duplicate section keys rejected',
    OutputContract::validate(sections(['a', 'One'], ['a', 'Two']), []) !== []);
check('bad key format rejected', OutputContract::validate(sections(['Bad-Key', 'One']), []) !== []);
check('heading collision after normalization rejected',
    OutputContract::validate(sections(['a', 'The Plan'], ['b', 'the plan:']), []) !== []);
check('alias colliding with another heading rejected',
    OutputContract::validate(sections(['a', 'One', ['Two']], ['b', 'Two']), []) !== []);
check('alias colliding with another alias rejected',
    OutputContract::validate(sections(['a', 'One', ['Shared']], ['b', 'Two', ['shared']]), []) !== []);
check('dangling evidence reference rejected',
    OutputContract::validate(sections(['a', 'One']), [['label' => 'x', 'evidence_sections' => ['nope']]]) !== []);
check('duplicate evidence reference rejected',
    OutputContract::validate(sections(['a', 'One']), [['label' => 'x', 'evidence_sections' => ['a', 'a']]]) !== []);
check('evidence on contract-less recipe rejected',
    OutputContract::validate(null, [['label' => 'x', 'evidence_sections' => ['a']]]) !== []);

echo "\nencode / decode round trip\n";

check('encode(null) stays null', OutputContract::encode(null) === null);
check('decode(null) stays null', OutputContract::decode(null) === null);
check('decode(garbage) degrades to null (fallback, not error)', OutputContract::decode('{not json') === null);
$roundTrip = OutputContract::decode(OutputContract::encode($contract));
check('round trip preserves keys, order, required flags', $roundTrip === $contract);

echo "\nSeeded cookbooks with contracts\n";

$content = require __DIR__ . '/../database/seeds/content.php';
foreach ($content['cookbooks'] as $book) {
    foreach ($book['recipes'] as $recipe) {
        $label = "{$book['slug']}/{$recipe['slug']}";
        $errors = OutputContract::validate($recipe['output_sections'] ?? null, $recipe['checks'] ?? []);
        check("{$label}: contract valid", $errors === [], implode('; ', $errors));

        $sections = $recipe['output_sections'] ?? null;
        if ($sections === null) {
            continue;
        }
        $decoded = OutputContract::decode(OutputContract::encode($sections));
        $example = (string) ($recipe['example_response'] ?? '');
        check("{$label}: has example response", $example !== '');
        $parsed = ResponseParser::parse($example, $decoded ?? []);
        check("{$label}: example satisfies required sections", $parsed['missing_required'] === [],
            'missing: ' . implode(', ', $parsed['missing_required']));
        check("{$label}: example has no duplicate sections", $parsed['duplicates'] === []);
        check("{$label}: example has no empty sections", $parsed['empty'] === []);
        check("{$label}: example has no unexpected headings", $parsed['unexpected'] === [],
            'unexpected: ' . implode(', ', $parsed['unexpected']));
        foreach ($recipe['checks'] ?? [] as $checkDef) {
            $keys = $checkDef['evidence_sections'] ?? [];
            $status = ResponseParser::checkStatus($keys, $parsed);
            $wanted = $keys === [] ? ResponseParser::STATUS_MANUAL : ResponseParser::STATUS_LOCATED;
            check("{$label}: check '{$checkDef['label']}' status {$wanted}", $status === $wanted, "got {$status}");
        }
    }
}

echo "\n{$tests} checks, {$failures} failures\n";
exit($failures === 0 ? 0 : 1);
