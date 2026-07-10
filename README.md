# Iain — Operational Software

A static portfolio site for an independent developer who builds operational
software: inventory tracking, service-request pipelines, runbooks, and
AI-assisted operations, all on plain PHP 8 and MySQL.

Live copy: [cubixmeow.com](https://cubixmeow.com)

## Design intent — GitHub-literate, not GitHub-styled

The site is **inspired by GitHub's visual vocabulary (commits, labels, states,
README-first content) but deliberately not a visual clone — original color
system, original iconography, original layout.**

The subject works Git-natively and ships production systems solo, so a design
that *thinks* in GitHub's concepts fits who he is. The rule for this build was
to borrow the concepts, never the skin:

- **README-as-hero** — the page opens like a rendered `README.md` file: a
  document frame, an H1, a description, and a row of labels. Content-first, not
  a repo-page facsimile.
- **Repos-as-cards** — the six subsystems, three proof projects, and peripheral
  systems each read like a small repository card (name, one-line description,
  primary-language dot, status).
- **Labels, not badges** — stack tags render as pill labels in a *custom* hue
  system (amber = language, teal = data, lilac = infra, clay = API/AI), not
  GitHub's actual label colors.
- **Merged / Open / Draft states** — "online / building / planned" becomes
  `MERGED` / `OPEN` / `DRAFT`, PR-literate but in an original monospace type
  treatment and original colors (sage / amber / lilac — **not** GitHub's
  purple/green/gray).
- **Commit-log rhythm** — the career route is a real commit log: a vertical
  rail with commit dots, monospace hashes and timestamps, short log-line
  messages, a `HEAD → main` tip and a `root` commit. The live inventory audit
  log is rendered in the same log idiom.
- **Diff framing** — the AI-integration policy is a two-column diff (permitted
  `+` vs. forbidden `−`), but in an invented sage/clay pair rather than
  GitHub's literal diff green/red (`#e6ffec` / `#ffebe9`).
- **Contribution graph, reimagined** — instead of the instantly-recognizable
  7×52 green calendar grid, activity is shown as **horizontal density bars, one
  per subsystem** — depth of production time across a competency, a different
  shape language carrying the same idea.

### What was deliberately avoided

- None of GitHub's color tokens (`#0d1117`, `#161b22`, `#238636`, `#2f81f7`,
  `#f85149`). The base is a **warm brown-black** with an **amber** primary and a
  **teal** link — an adjacent but clearly distinct palette.
- No Octicons or GitHub iconography. The brand mark, document glyph, checkmarks,
  commit dots, and diagram arrows are **custom inline SVG / CSS**.
- No repo-page layout (no left About sidebar, file tree, or Code/Issues/PRs tab
  bar). The concepts are recomposed into an **original single-scroll portfolio**.
- No literal contribution-graph grid.

## Static and dependency-free — by design

This is a hard constraint, not a convenience: **no framework, no build step, no
external requests.** The person behind the site designs systems that stay
deterministic and inspectable, and the page practices what it preaches.

- Plain HTML5, CSS3, and vanilla JavaScript. No React, no bundler, no npm.
- Fonts (Space Grotesk for headings, IBM Plex Sans for body, IBM Plex Mono for
  genuine data/log content only) are **self-hosted** as `woff2` in `/fonts` —
  no font-CDN calls.
- All diagrams are **inline SVG**, crisp at any zoom with zero dependencies.
- JavaScript only handles the mobile nav, the scroll-reveal on diagrams, and
  building the density bars — and it fully honours `prefers-reduced-motion`.

## Accessibility

- Semantic HTML with a correct heading hierarchy and a skip-to-content link.
- Fully responsive; the repo cards and commit-log rail restack cleanly down to
  375px.
- Color contrast meets WCAG AA in the warm-dark palette.
- Motion is disabled entirely under `prefers-reduced-motion: reduce`.

## Structure

```
/
├── index.html          # the whole site, one document
├── css/
│   └── style.css       # the GitHub-literate design system + components
├── js/
│   └── main.js         # nav toggle + scroll-reveal + density graph
├── fonts/              # self-hosted woff2 (Space Grotesk, IBM Plex Sans/Mono)
└── README.md
```

## Running it

No build, no server required. Open `index.html` directly in a browser, or serve
the folder statically:

```sh
python3 -m http.server 8000
# then visit http://localhost:8000
```
