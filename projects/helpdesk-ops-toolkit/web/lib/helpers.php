<?php
/**
 * Small view/utility helpers: output escaping, CSRF, redirects, date
 * formatting, controlled-vocabulary labels, and a minimal Markdown renderer
 * for knowledge-base articles (no external library).
 */
declare(strict_types=1);

/** HTML-escape for safe output. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/* ---- CSRF ---------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $ok = isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
    if (!$ok) {
        http_response_code(400);
        exit('Bad request (invalid CSRF token). Go back and try again.');
    }
}

/* ---- Controlled vocabularies (mirror the schema enums) ------------- */
const HOT_CATEGORIES = ['hardware', 'software', 'network', 'account_access', 'other'];
const HOT_PRIORITIES = ['low', 'medium', 'high'];
const HOT_STATUSES   = ['open', 'in_progress', 'resolved', 'closed'];
const HOT_ASSET_TYPES  = ['laptop', 'desktop', 'monitor', 'peripheral'];
const HOT_ASSET_STATUS = ['in_use', 'surplus', 'repair', 'retired'];

function label(string $v): string
{
    return ucwords(str_replace('_', ' ', $v));
}

/** Validate a value against an allowed list, with a fallback default. */
function pick(string $v, array $allowed, string $default): string
{
    return in_array($v, $allowed, true) ? $v : $default;
}

/* ---- Dates --------------------------------------------------------- */
function fmt_dt(?string $dt): string
{
    if (!$dt) {
        return '—';
    }
    $ts = strtotime($dt);
    return $ts ? date('M j, Y g:i A', $ts) : e($dt);
}

function fmt_date(?string $d): string
{
    if (!$d) {
        return '—';
    }
    $ts = strtotime($d);
    return $ts ? date('M j, Y', $ts) : e($d);
}

/** Human "2d 4h" style duration from hours. */
function fmt_hours(float $hours): string
{
    if ($hours <= 0) {
        return '—';
    }
    $d = intdiv((int) round($hours), 24);
    $h = (int) round($hours) % 24;
    return $d > 0 ? "{$d}d {$h}h" : "{$h}h";
}

/* ---- Minimal, safe Markdown -> HTML -------------------------------- *
 * Supports: # headings, - bullet lists, 1. ordered lists, **bold**,
 * `code`, [text](url), and paragraphs. Input is escaped first, so no raw
 * HTML from the article body can reach the page.
 */
function markdown_to_html(string $md): string
{
    $md    = str_replace("\r\n", "\n", $md);
    $lines = explode("\n", $md);
    $html  = '';
    $list  = null; // 'ul' | 'ol' | null

    $closeList = function () use (&$list, &$html): void {
        if ($list !== null) {
            $html .= "</$list>\n";
            $list = null;
        }
    };

    $inline = function (string $text): string {
        $text = e($text);
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
        // [label](http/https/relative) — restrict scheme to avoid javascript:
        $text = preg_replace_callback('/\[(.+?)\]\(([^)]+)\)/', function ($m) {
            $url = $m[2];
            if (!preg_match('#^(https?:|/|\.\/|#|mailto:)#i', $url)) {
                return $m[1];
            }
            return '<a href="' . $url . '">' . $m[1] . '</a>';
        }, $text);
        return $text;
    };

    foreach ($lines as $line) {
        $t = rtrim($line);

        if ($t === '') {
            $closeList();
            continue;
        }
        if (preg_match('/^(#{1,4})\s+(.*)$/', $t, $m)) {
            $closeList();
            $lvl = strlen($m[1]);
            $html .= "<h" . $lvl . ">" . $inline($m[2]) . "</h" . $lvl . ">\n";
            continue;
        }
        if (preg_match('/^\s*[-*]\s+(.*)$/', $t, $m)) {
            if ($list !== 'ul') {
                $closeList();
                $html .= "<ul>\n";
                $list = 'ul';
            }
            $html .= '<li>' . $inline($m[1]) . "</li>\n";
            continue;
        }
        if (preg_match('/^\s*\d+\.\s+(.*)$/', $t, $m)) {
            if ($list !== 'ol') {
                $closeList();
                $html .= "<ol>\n";
                $list = 'ol';
            }
            $html .= '<li>' . $inline($m[1]) . "</li>\n";
            continue;
        }
        $closeList();
        $html .= '<p>' . $inline($t) . "</p>\n";
    }
    $closeList();
    return $html;
}
