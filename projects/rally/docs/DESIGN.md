# Rally Design System

**Broadcast scoreboard, not fitness dashboard.**

This document specifies the Rally V1 interface as implemented in
`public/assets/css/` and `app/Views/`. It covers visual direction, tokens,
typography, layout, every core screen, the status system, the fourteen-game
rail, the component inventory, motion, and implementation notes for the
PHP + plain-CSS stack.

---

## 1. Visual direction

Rally presents ordinary people's wearable data with the seriousness of a
televised sport. The interface borrows from live sports broadcasts, tennis
and combat-sports score presentation, F1 timing screens, and editorial sports
publications — composition and typography carry the identity, not decoration.

Principles:

- **The score is the interface.** Every screen leads with competition state.
  Branding and copy never sit above the score on the match page.
- **Neutral graphite canvas.** The old green-tinted background is gone. Color
  is information: lime = live/now/primary action, amber = pending, mint =
  official, gray = void.
- **Surfaces and rules before cards.** Sections are separated by 1–2px rules
  and typographic hierarchy. Rounded containers are reserved for genuinely
  elevated moments (live panel, result card, feature panel).
- **Two sides of one event.** Competitors are mirrored around a central
  dividing rule, never presented as two stacked profile cards.
- **Pending is a state of play, not an error.** Settlement is styled as
  officiating (review → locked), not as a warning.

---

## 2. Color tokens

Defined in `assets/css/tokens.css`. Neutral cool grays; a single signal hue.

| Token | Value | Role |
| --- | --- | --- |
| `--canvas` | `#0b0c0e` | Page background (near black, cool) |
| `--surface-1` | `#131518` | Primary raised surface (panels, rail cells) |
| `--surface-2` | `#1a1d21` | Secondary surface (menus, inline settle) |
| `--surface-3` | `#22262b` | Hover / avatar fill |
| `--line` | `#262b31` | Hairline rules |
| `--line-strong` | `#3a4149` | Section rules, input borders, baselines |
| `--ink` | `#f1f3f5` | Primary text (soft white) |
| `--ink-2` | `#a5adb8` | Secondary text |
| `--ink-3` | `#6c7480` | Tertiary/metadata text |
| `--signal` | `#c8f542` | Rally acid lime — live, current game, leader tick, primary action, focus |
| `--signal-ink` | `#131608` | Text on lime |
| `--signal-dim` | `rgba(200,245,66,.13)` | Lime tint fills |
| `--pending` | `#e2b04e` | Pending / settling (warm amber) |
| `--official` | `#85d3aa` | Official / final (restrained mint) |
| `--void` | `#79828d` | Void (neutral gray) |
| `--danger` | `#e96a5c` | Errors only (never used for "losing") |

Rules of use:

- Lime appears only on: live indicators, the live rail cell, the leader tick,
  primary buttons, focus rings, active nav item, and one kicker per screen.
  Never as a background wash.
- Winning/losing is **never** conveyed by red/green. Winners are heavier and
  brighter (weight + `--ink`), losers lighter (`--ink-3`), plus explicit text
  ("Iain d. Mike", "wins by 1,517") and position in the rail.

## 3. Typography

Self-hosted variable fonts (`assets/css/fonts.css`, files in `assets/fonts/`,
~90KB per family, latin + latin-ext subsets). No third-party font requests.

| Role | Face | Settings |
| --- | --- | --- |
| Display / headlines | **Archivo** variable | `font-stretch: 118%`, weight 740–860, uppercase for match headlines |
| Scoreboard numerals | **Archivo** | stretch 118%, weight 820–840, `font-variant-numeric: tabular-nums` |
| Broadcast labels / chips / buttons | **Archivo** | `font-stretch: 76–86%` (condensed), weight 600–720, uppercase, letter-spaced |
| Interface & body | **Inter** variable | 400–700 |
| Data (values, dates, margins, times) | **Inter** | + `tabular-nums` via `.t-num` |

One display family at two widths (expanded for headlines, condensed for
labels) plus one interface family gives a broadcast hierarchy without a
novelty font. Key classes: `.t-label` (condensed uppercase label),
`.t-label--signal`, `.t-num` (tabular numerals).

Type scale (tokens): `--text-xs .72rem`, `--text-s .85rem`, `--text-m 1rem`,
`--text-l 1.2rem`, `--display-s/m/l` clamps, `--score-xl
clamp(4.25rem, 20vw, 7.5rem)` for the central series score.

## 4. Spacing & grid

- 4px base scale: `--s-1 .25rem` … `--s-8 4rem`.
- Page gutter: `--gutter` 16px mobile / 24px ≥640px.
- Containers: `.wrap` (max 71rem, full compositions) and `.wrap-narrow`
  (max 42rem, forms/reading). Full-width composition inside; no page-level
  centering of a mobile column on desktop.
- Radii: 2–3px (chips, buttons, rail cells), 6px (panels/cards). Restrained.

## 5. Responsive breakpoints

| Breakpoint | Use |
| --- | --- |
| base (mobile-first) | single column, bottom navigation, stacked identity |
| `640px` | wider gutters, side-by-side field pairs, horizontal board identity |
| `760px` | match history switches rows → fixture table |
| `840px` | desktop header nav replaces bottom nav + overflow menu |
| `900px` | desktop sports layouts (match columns, dashboard columns, taller rail) |

## 6–7. Match view (mobile and desktop)

`app/Views/matches/show.php` + `pages/match.css`. Highest-priority screen.

Mobile order (score inside the first viewport, no branding above it):

1. **Meta bar** — `DAILY STEPS · 14-GAME SERIES · JUL 13–26 · GAME 9 OF 14`
   with the match status chip (LIVE/SETTLING/FINAL) right-aligned.
2. **Board** — competitors mirrored around a central vertical rule: initials
   avatar + name + declared source at the outer edges, giant tabular score
   numerals pulled toward the center rule. The series leader gets a lime
   "serve tick" under their number; the trailing number drops to `--ink-3`.
3. **Verdict line** — never buried: `PROVISIONAL — 6 GAMES REMAINING`,
   `SETTLING — 1 RESULT AWAITING OFFICIAL REVIEW`, `FINAL — IAIN WINS THE
   SERIES`, or `FINAL — DRAWN SERIES`, plus ties/voids when present.
4. **Settling panel** (settling only) — amber officiating notice.
5. **Fourteen-game rail** (see §13) with text legend.
6. **Source strip** — equipment metadata (see status system).
7. **Live game panel** — today as an event: `GAME 9 · TODAY`, LIVE chip,
   both values in display numerals, leader + margin or tie-threshold state,
   settle time, last sync, games remaining. Pending days swap the lime top
   rule for amber and embed the `PENDING REVIEW … Locks Jul 22, 6:00 AM PDT`
   block.
8. **Recent games** — five fixture rows (game number block, result with
   winner emphasis, date + status chip + margin, values right-aligned),
   link to full history.
9. **Series notes** — label/value list: largest margin, closest decisive
   game, official averages, tie threshold, match timezone.

Desktop (≥900px) keeps the same DOM and recomposes:

```
metadata bar
competitor A ——— central score ——— competitor B
fourteen-game series rail (taller cells)
source strip
live-game panel (main col) │ series notes (sticky aside)
recent games (main col)    │
```

## 8. Dashboard

`app/Views/dashboard/index.php` + `pages/dashboard.css`. A matchday screen,
not a KPI grid.

- **Featured match** dominates: the user's live match (else first active
  series) as one large linked panel — lime top rule, metric + game number,
  `IAIN 5–3 MIKE` scoreline with trailing-side de-emphasis, **mini rail**,
  and a `TODAY` line with both current values and the margin.
- **Supporting sections** use distinct compact treatments, all rule-based:
  - *Invitations*: text row + primary "Respond" button (the only list with
    a button).
  - *Pending review*: amber left-rule rows, "Awaiting final device sync".
  - *Active series*: quiet fixture rows with display-face scores.
  - *Recently completed*: muted rows with FINAL/DRAW chips.
- Empty state: dashed panel with a decorative empty rail and a single
  "Start a series" action.

## 9. Landing page

`app/Views/home.php` + `pages/home.css`. Compact, product-led; the hero
demonstration **is** the product.

1. Compact header (existing site header).
2. Hero: **"Every day is a new game."** + one supporting line + two actions.
3. **Live match demonstration** — a framed, full-fidelity match board built
   from the real components (meta bar, board, verdict, rail with live game 9,
   today values). Marked decorative for screen readers with a text summary.
4. Four numbered product points: the series rail, daily reset, pending →
   official settlement (with a real settle panel), source transparency
   (with a real mismatch strip).
5. Final action: "Create your account".

No multi-section SaaS scroll, no invented tournament content.

## 10. Result card

`app/Views/matches/share.php` + `pages/share.css`. An official result
graphic for screenshots.

Content: Rally identity, `GAME 8 · FINAL`, winner row (name + value,
lime tick, full ink) above loser row (muted), `IAIN WINS BY 1,517` headline
in lime condensed caps, updated series line, `DAILY STEPS · JULY 20, 2026 ·
OFFICIAL` footer.

Variants (toggle, no reload): **Social** 4:5, **Square** 1:1, **Link
preview** compact horizontal. No confetti, trophies, flames, or badges.
Ties render "TIE — DIFFERENCE n" with no winner emphasis.

## 11. Match history

`app/Views/matches/history.php`. Two presentations of the same prepared rows:

- **Mobile (<760px)**: stacked game records — game-number block, result with
  winner emphasis, date + status chip + margin, both values right-aligned
  (winner bold). Future games dimmed. No table.
- **Desktop (≥760px)**: fixture table with condensed uppercase column heads,
  right-aligned numeric columns, live row tinted lime, pending amber,
  scheduled dimmed.

## 12. Status system

One chip component (`.status-pill`): condensed uppercase, 6px dot, tinted
background — color never alone (dot + text + tint).

| State | Color | Notes |
| --- | --- | --- |
| LIVE / ACTIVE | lime | dot blinks (disabled under reduced motion) |
| PENDING / SETTLING | amber | review, not error |
| OFFICIAL / FINAL / COMPLETED | mint | conclusive |
| VOID / DRAW | gray | neutral |
| SCHEDULED / INVITED / FUTURE | outline, faint | inert |

Settlement moment: `.settle-panel` — left rule + circled ellipsis glyph +
`PENDING REVIEW / Waiting for final device sync / Locks {time}`; the official
variant swaps amber for mint. Source compatibility: `.source-strip` — a
bordered text strip reading as officiating metadata (`COMPARABLE SOURCES /
Apple Watch vs Garmin Watch` or amber `SOURCE MISMATCH … Results may not be
directly comparable.`), visible but subordinate.

## 13. Fourteen-game rail (signature)

`app/Views/partials/rail.php` + rail styles in `components.css`. A match
tape: every cell shares a horizontal **baseline**; ownership is positional.

| Game state | Rendering |
| --- | --- |
| A win (official) | white block **above** the baseline with A's initial |
| B win (official) | white block **below** the baseline with B's initial |
| Tie | slim gray block centered on the baseline, `=` |
| Void | dashed outline centered, `×` |
| Pending | amber-outlined block straddling the baseline, `…` |
| Live | cell boxed in lime, tinted, pulsing dot; lime game number |
| Future | faint outlined cell, baseline only |

- Game numbers run under every cell; the live number is lime (current
  position in the series).
- Identical treatment for both sides (same white blocks) — position + initial
  differentiates, so the rail reads without color and in grayscale.
- Initials fall back to two letters when both players share a first letter.
- Full rail: cells are links to day pages with `aria-label="Game 4: Iain win,
  official"`; a text legend sits below. Mini rail (`rail--mini`, dashboard /
  feature panels): 1.4rem tall, no numbers/legend, `aria-hidden` with the
  series score carried by adjacent text.
- It is a fixture record, not a streak or a chart: no cumulative encoding.

## 14. Component inventory

All in `base.css` / `components.css` unless noted:

- **Site header** (`.site-header`, `.brand` lime tick + oblique wordmark)
- **Bottom navigation** (`.bottom-nav`, authenticated mobile: Today,
  Matches, Create, Profile; `aria-current` in lime; safe-area padding)
- **Overflow menu** (`.nav-menu`, mobile secondary: simulation, sign out)
- **Buttons** (`.button`, `-primary` lime, `-ghost` outline, `-small`)
- **Form fields** (`.stack-form`, condensed uppercase labels, `.field-pair`,
  `.checkbox-row`, error text; grouped by `fieldset.form-group` rules)
- **Status chip** (`.status-pill status-*`)
- **Competitor identity** (`.avatar` squared bib initials, `.board-ident`)
- **Match scoreline** (`.board-*`: meta bar, sides, center rule, score
  numerals, lead tick, verdict)
- **Live indicator** (`.rail-live-dot`, chip dot, `live-blink`)
- **Fourteen-game rail** (`.rail`, `.rail--mini`, legend)
- **Live-game panel** (`.live-panel`, `--pending` variant, `.live-values`)
- **Settlement review** (`.settle-panel`, `--official` variant)
- **Source compatibility** (`.source-strip`, `--mismatch`; form-side
  `.source-warning`)
- **Fixture/result row** (`.fixture`, values with winner emphasis)
- **Compact rows** (`.series-row`, `.invite-row`, `.pending-row`)
- **Result card** (`.share-card` + variants, `pages/share.css`)
- **Empty state** (`.empty-state` + `.empty-rail` motif)
- **Section head** (`.section-head` rule + condensed heading)

## 15. Interaction & motion

Sparse and purposeful; everything honors `prefers-reduced-motion` via the
global override in `tokens.css`.

- **Live indicator**: 1.8s opacity blink on live dots only.
- **Score update**: `score-in` — numerals rise 0.35rem/fade over 320ms on load.
- **Result card reveal**: `card-in` rise + settle.
- **Hovers**: background/border shifts at 140ms; rows brighten, no movement.
- The signature transition is **PENDING → OFFICIAL**: amber review panel is
  replaced by mint official state on settlement (server-rendered state swap;
  the two panels share identical geometry so the change reads as a lock,
  not a layout shift).
- No parallax, confetti, particles, spinning icons, or constant pulsing.

Accessibility: skip link; semantic headings; visible lime focus ring
(`--focus`, two-layer for contrast); chips pair dot + text; rail readable
without color, each cell labeled; series score exposed as a sentence for
screen readers (`role="img"` labels / visually-hidden h1); 44px-class touch
targets (buttons 2.8rem, bottom nav 3.6rem); form labels always visible;
tabular numerals everywhere data aligns.

## 16. Implementation notes (PHP + plain CSS)

- **No build step.** Plain CSS custom properties; two self-hosted variable
  fonts (woff2, preloaded in the layout); vanilla JS only for nav toggle,
  flash dismiss, confirm dialogs, share/copy, and the card-variant toggle.
- **Load order**: `fonts.css → tokens.css → base.css → components.css →
  pages/{page}.css` via the existing `$pageCss` mechanism and `asset()`
  cache-busting.
- **Shared markup**: the rail is a `View::partial('partials/rail', [...])`
  taking `match`, `days`, and `mini`; it derives per-day outcomes with the
  existing `MatchScoringService::dayOutcome()`. The board/scoreline is plain
  markup reused by the match page and the landing demonstration.
- **No route, controller, schema, or scoring changes.** All redesign logic is
  view-layer; presentational values (date ranges, remaining games, last sync
  via `ingested_at`, settle times in the match timezone) are computed in
  templates from data the controllers already pass.
- Server-rendered state remains the source of truth; the design assumes no
  client-side data fetching.
