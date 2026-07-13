<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * Deterministic structural parser for pasted AI responses. Given the
 * raw immutable response text and a Recipe's output contract, it splits
 * the document on ATX Markdown headings (#, ##, ###) outside fenced
 * code blocks and maps recognized headings (canonical or alias,
 * case-insensitive, trailing punctuation ignored) to stable section keys.
 *
 * The parser reports where content appears — located, missing, empty,
 * duplicated — and nothing more. It never grades accuracy, completeness,
 * or quality, never rewrites the response, and its failure modes only
 * ever downgrade a check to manual review of the full response.
 */
final class ResponseParser
{
    public const STATUS_LOCATED  = 'located';
    public const STATUS_MISSING  = 'missing';
    public const STATUS_MANUAL   = 'manual';
    public const STATUS_MULTIPLE = 'multiple';

    /**
     * Parse a response against a contract (decoded section list).
     *
     * @param list<array{key: string, heading: string, aliases: list<string>, required: bool}> $sections
     * @return array{
     *   sections: array<string, list<array{heading: string, content: string, empty: bool}>>,
     *   missing: list<string>,
     *   missing_required: list<string>,
     *   empty: list<string>,
     *   duplicates: list<string>,
     *   unexpected: list<string>,
     *   preamble: string
     * }
     */
    public static function parse(string $content, array $sections): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", $text);

        // Some assistants return the whole answer wrapped in a single
        // ```markdown code fence. Taken literally, every heading inside
        // would be code and nothing would match. When the entire response
        // is one wrapper fence, unwrap it once so the real document is
        // parsed. Genuine embedded code blocks are untouched (they never
        // start on the very first line and close on the very last).
        $lines = self::unwrapWrapperFence($lines);

        // Map every normalized heading and alias to its section key.
        // Contract validation guarantees these are collision-free.
        $lookup = [];
        foreach ($sections as $section) {
            $lookup[OutputContract::normalizeHeading($section['heading'])] = $section['key'];
            foreach ($section['aliases'] as $alias) {
                $lookup[OutputContract::normalizeHeading($alias)] = $section['key'];
            }
        }

        // Collect candidate headings (levels 1-3) outside fenced code.
        // Deeper headings (####+) are sub-structure inside a section and
        // never split the document. Bare lines that exactly match a
        // declared heading or alias are held back as a fallback (below).
        $headings = [];
        $bareCandidates = [];
        $fence = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/^\s{0,3}(`{3,}|~{3,})/', $line, $m) === 1) {
                if ($fence === null) {
                    $fence = $m[1][0];
                } elseif ($m[1][0] === $fence) {
                    $fence = null;
                }
                continue;
            }
            if ($fence !== null) {
                continue;
            }
            if (preg_match('/^(#{1,3})\s+(.+?)\s*$/', $line, $m) === 1) {
                $headings[] = [
                    'line'  => $i,
                    'level' => strlen($m[1]),
                    'text'  => $m[2],
                    'key'   => $lookup[OutputContract::normalizeHeading($m[2])] ?? null,
                ];
                continue;
            }
            // A non-blank line whose whole text is exactly a declared
            // heading or alias (case, emphasis, and trailing punctuation
            // ignored). Some assistants — Gemini, pasted plain text —
            // drop the leading ## markers entirely.
            $trimmed = trim($line);
            if ($trimmed !== '' && isset($lookup[OutputContract::normalizeHeading($trimmed)])) {
                $bareCandidates[] = [
                    'line'  => $i,
                    'level' => 2,
                    'text'  => $trimmed,
                    'key'   => $lookup[OutputContract::normalizeHeading($trimmed)],
                ];
            }
        }

        // Fallback for responses with no ATX heading markers at all: only
        // when no declared section was found via real headings do the
        // exact-match bare lines count as headings. This keeps well-formed
        // ## responses untouched — a body line that merely quotes a
        // heading phrase never creates a spurious section there.
        $recognizedViaAtx = false;
        foreach ($headings as $heading) {
            if ($heading['key'] !== null) {
                $recognizedViaAtx = true;
                break;
            }
        }
        if (!$recognizedViaAtx && $bareCandidates !== []) {
            $headings = array_merge($headings, $bareCandidates);
            usort($headings, static fn(array $a, array $b): int => $a['line'] <=> $b['line']);
        }

        // A recognized section spans from its heading to the next heading
        // of the same or shallower level, or the next recognized heading
        // of any level — whichever comes first. Two recognized sections
        // therefore never swallow one another, while unrecognized deeper
        // subheadings stay inside their parent's content.
        $found = [];
        $insideSpans = [];
        foreach ($headings as $idx => $heading) {
            if ($heading['key'] === null) {
                continue;
            }
            $end = count($lines);
            for ($j = $idx + 1; $j < count($headings); $j++) {
                if ($headings[$j]['key'] !== null || $headings[$j]['level'] <= $heading['level']) {
                    $end = $headings[$j]['line'];
                    break;
                }
            }
            $body = trim(implode("\n", array_slice($lines, $heading['line'] + 1, $end - $heading['line'] - 1)));
            $found[$heading['key']][] = [
                'heading' => $heading['text'],
                'content' => $body,
                'empty'   => $body === '',
            ];
            $insideSpans[] = [$heading['line'], $end];
        }

        // Unexpected headings: candidates matching no declared section
        // and not nested inside a recognized section's span.
        $unexpected = [];
        foreach ($headings as $heading) {
            if ($heading['key'] !== null) {
                continue;
            }
            $nested = false;
            foreach ($insideSpans as [$start, $end]) {
                if ($heading['line'] > $start && $heading['line'] < $end) {
                    $nested = true;
                    break;
                }
            }
            if (!$nested) {
                $unexpected[] = $heading['text'];
            }
        }

        // Content before the first recognized heading (the whole response
        // when nothing matched).
        $firstLine = count($lines);
        foreach ($headings as $heading) {
            if ($heading['key'] !== null) {
                $firstLine = $heading['line'];
                break;
            }
        }
        $preamble = trim(implode("\n", array_slice($lines, 0, $firstLine)));

        $missing = [];
        $missingRequired = [];
        $empty = [];
        $duplicates = [];
        foreach ($sections as $section) {
            $key = $section['key'];
            $blocks = $found[$key] ?? [];
            if ($blocks === []) {
                $missing[] = $key;
                if ($section['required']) {
                    $missingRequired[] = $key;
                }
                continue;
            }
            if (count($blocks) > 1) {
                $duplicates[] = $key;
            }
            if (count($blocks) === 1 && $blocks[0]['empty']) {
                $empty[] = $key;
            }
        }

        return [
            'sections'         => $found,
            'missing'          => $missing,
            'missing_required' => $missingRequired,
            'empty'            => $empty,
            'duplicates'       => $duplicates,
            'unexpected'       => $unexpected,
            'preamble'         => $preamble,
        ];
    }

    /**
     * If the entire response is a single fenced block (optionally with a
     * `markdown`/`md` info string), return its inner lines; otherwise
     * return the lines unchanged. This only fires when the first non-blank
     * line opens a fence and the last non-blank line closes the matching
     * fence with no same-marker fence in between — i.e. a pure wrapper,
     * never a document that merely contains a code block.
     *
     * @param list<string> $lines
     * @return list<string>
     */
    private static function unwrapWrapperFence(array $lines): array
    {
        $firstIdx = null;
        $lastIdx = null;
        foreach ($lines as $i => $line) {
            if (trim($line) !== '') {
                $firstIdx ??= $i;
                $lastIdx = $i;
            }
        }
        if ($firstIdx === null || $firstIdx === $lastIdx) {
            return $lines;
        }
        // Opening fence may carry an info string (```markdown); closing
        // fence must be bare. Both must use the same marker character.
        if (preg_match('/^\s{0,3}(`{3,}|~{3,})\s*[A-Za-z0-9_+-]*\s*$/', $lines[$firstIdx], $open) !== 1) {
            return $lines;
        }
        if (preg_match('/^\s{0,3}(`{3,}|~{3,})\s*$/', $lines[$lastIdx], $close) !== 1) {
            return $lines;
        }
        if ($open[1][0] !== $close[1][0]) {
            return $lines;
        }
        // Any same-marker fence between the outer pair means this is not a
        // clean wrapper (a real embedded block); leave it alone.
        for ($i = $firstIdx + 1; $i < $lastIdx; $i++) {
            if (preg_match('/^\s{0,3}(`{3,}|~{3,})/', $lines[$i], $inner) === 1 && $inner[1][0] === $open[1][0]) {
                return $lines;
            }
        }
        return array_slice($lines, $firstIdx + 1, $lastIdx - $firstIdx - 1);
    }

    /**
     * Structural status for one Quality Check, from its evidence keys and
     * a parse result. Describes structure only, never quality:
     *  - manual:   no evidence mapping; review the full response
     *  - multiple: an evidence section appears more than once — the
     *              parser will not guess which duplicate is correct
     *  - located:  at least one evidence section was found with content
     *  - missing:  every evidence section is absent or empty
     *
     * @param list<string> $evidenceKeys
     * @param array{sections: array<string, list<array{heading: string, content: string, empty: bool}>>, duplicates: list<string>} $parsed
     */
    public static function checkStatus(array $evidenceKeys, array $parsed): string
    {
        if ($evidenceKeys === []) {
            return self::STATUS_MANUAL;
        }
        $located = false;
        foreach ($evidenceKeys as $key) {
            if (in_array($key, $parsed['duplicates'], true)) {
                return self::STATUS_MULTIPLE;
            }
            $blocks = $parsed['sections'][$key] ?? [];
            if ($blocks !== [] && !$blocks[0]['empty']) {
                $located = true;
            }
        }
        return $located ? self::STATUS_LOCATED : self::STATUS_MISSING;
    }
}
