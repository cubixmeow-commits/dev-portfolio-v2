# Iain — Operational Software

A static portfolio site for an independent developer who builds operational
software: inventory tracking, service-request pipelines, runbooks, and
AI-assisted operations, all on plain PHP 8 and MySQL.

Live copy: [cubixmeow.com](https://cubixmeow.com)

## Design concept — Technical Dossier / Blueprint

The site is styled as an **engineering schematic / drawing set**. It reads like
a drafting table at night: a deep blueprint-navy base, fine hairline graph-paper
grids, thin cyan/white line work, and formal title blocks stamped in the corner
of each section. Every section is a numbered **sheet** ("Sheet 03 of 07 —
Subsystems"), and the system flows — the asset state machine, the triage
pipeline, and the AI advisory data flow — are drawn as real inline-SVG
schematics with boxes, connectors, and annotation callouts rather than styled
bullet lists. Status is shown with drawing-style **revision stamps** (`LIVE`,
`BUILDING`, `PLANNED`, `REV 0.1 DRAFT`).

This replaces a previous terminal/console theme. The visual language now
communicates "I design deterministic systems" rather than "I like hacker
aesthetics."

## Static and dependency-free — by design

This is a hard constraint, not a convenience: **no framework, no build step, no
external requests.** The person behind the site designs systems that stay
deterministic and inspectable, and the page practices what it preaches.

- Plain HTML5, CSS3, and vanilla JavaScript. No React, no bundler, no npm.
- Fonts (Space Grotesk, IBM Plex Sans, IBM Plex Mono) are **self-hosted** as
  `woff2` in `/fonts` — no font-CDN calls.
- All diagrams are **inline SVG**, so they stay crisp at any zoom with zero
  dependencies.
- The only JavaScript animates the SVG "draw-on" effect and the mobile nav, and
  it fully honours `prefers-reduced-motion`.

## Accessibility

- Semantic HTML with a correct heading hierarchy and a skip-to-content link.
- Fully responsive; single-column stacking on mobile, and the schematics
  simplify gracefully down to 375px.
- Colour contrast meets WCAG AA in the dark blueprint palette.
- Motion is disabled entirely under `prefers-reduced-motion: reduce`.

## Structure

```
/
├── index.html          # the whole site, one document
├── css/
│   └── style.css       # blueprint design system + all components
├── js/
│   └── main.js         # nav toggle + scroll-triggered SVG draw-on
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
