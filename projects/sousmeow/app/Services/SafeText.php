<?php

declare(strict_types=1);

namespace SousMeow\Services;

/**
 * Renders pasted AI responses for display. The input is untrusted, so
 * the entire string is HTML-escaped first; a small allowlist of
 * Markdown structures (headings, bold, italic, inline code, fenced code,
 * lists, blockquotes, paragraphs) is then recognized on the escaped
 * text. No HTML from the paste ever reaches the page, and there is no
 * raw-HTML passthrough by design.
 */
final class SafeText
{
    public static function render(string $raw): string
    {
        // Normalize line endings, then escape everything up front.
        $text = str_replace(["\r\n", "\r"], "\n", $raw);
        $escaped = e($text);

        $lines = explode("\n", $escaped);
        $html = [];
        $inCode = false;
        $codeBuffer = [];
        $listType = null;   // 'ul' | 'ol' | null
        $paragraph = [];

        $flushParagraph = static function () use (&$paragraph, &$html): void {
            if ($paragraph !== []) {
                $html[] = '<p>' . self::inline(implode('<br>', $paragraph)) . '</p>';
                $paragraph = [];
            }
        };
        $closeList = static function () use (&$listType, &$html): void {
            if ($listType !== null) {
                $html[] = '</' . $listType . '>';
                $listType = null;
            }
        };

        foreach ($lines as $line) {
            // Fenced code blocks: kept verbatim (already escaped).
            if (preg_match('/^```/', trim($line)) === 1) {
                if ($inCode) {
                    $html[] = '<pre><code>' . implode("\n", $codeBuffer) . '</code></pre>';
                    $codeBuffer = [];
                    $inCode = false;
                } else {
                    $flushParagraph();
                    $closeList();
                    $inCode = true;
                }
                continue;
            }
            if ($inCode) {
                $codeBuffer[] = $line;
                continue;
            }

            $trimmed = trim($line);

            if ($trimmed === '') {
                $flushParagraph();
                $closeList();
                continue;
            }

            // Headings (levels 1-4 render one size down to fit the card).
            if (preg_match('/^(#{1,4})\s+(.*)$/', $trimmed, $m) === 1) {
                $flushParagraph();
                $closeList();
                $level = min(strlen($m[1]) + 1, 5);
                $html[] = '<h' . $level . '>' . self::inline($m[2]) . '</h' . $level . '>';
                continue;
            }

            // Blockquote.
            if (preg_match('/^&gt;\s?(.*)$/', $trimmed, $m) === 1) {
                $flushParagraph();
                $closeList();
                $html[] = '<blockquote><p>' . self::inline($m[1]) . '</p></blockquote>';
                continue;
            }

            // Unordered list item.
            if (preg_match('/^[-*]\s+(.*)$/', $trimmed, $m) === 1) {
                $flushParagraph();
                if ($listType !== 'ul') {
                    $closeList();
                    $html[] = '<ul>';
                    $listType = 'ul';
                }
                $html[] = '<li>' . self::inline($m[1]) . '</li>';
                continue;
            }

            // Ordered list item.
            if (preg_match('/^\d+[.)]\s+(.*)$/', $trimmed, $m) === 1) {
                $flushParagraph();
                if ($listType !== 'ol') {
                    $closeList();
                    $html[] = '<ol>';
                    $listType = 'ol';
                }
                $html[] = '<li>' . self::inline($m[1]) . '</li>';
                continue;
            }

            $paragraph[] = $trimmed;
        }

        // Unclosed fence: render what we collected as code.
        if ($inCode && $codeBuffer !== []) {
            $html[] = '<pre><code>' . implode("\n", $codeBuffer) . '</code></pre>';
        }
        $flushParagraph();
        $closeList();

        return implode("\n", $html);
    }

    /** Inline spans on escaped text: bold, italic, inline code. */
    private static function inline(string $escaped): string
    {
        // Inline code first so its content is not bolded/italicized.
        $out = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped) ?? $escaped;
        $out = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $out) ?? $out;
        $out = preg_replace('/(?<![*\w])\*([^*\n]+)\*(?![*\w])/', '<em>$1</em>', $out) ?? $out;
        return $out;
    }
}
