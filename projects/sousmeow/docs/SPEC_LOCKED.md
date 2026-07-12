# SousMeow v1: Locked Specification

This document is the scope-locked specification for SousMeow v1, codified
from the refined build brief. See `REFINEMENT_NOTE.md` for why the brief
itself serves as the locked text. Do not expand this back into the larger
commercial marketplace concept. Do not add speculative infrastructure.

## 1. Product definition

SousMeow is a guided AI cooking companion for makers. A **Cookbook** is a
packaged, step-by-step workflow. Each step is a **Recipe**. The user
stocks a **Pantry** (structured inputs about their project), and each
Recipe turns the Pantry into a ready-to-run prompt. The user copies the
prompt, runs it in the AI of their choice, pastes the response back,
confirms human **Quality Checks**, and approves the result as an
**Artifact**. Approved Artifacts across all Recipes export as a
**Project Kit**.

SousMeow never calls an AI itself. The user's own AI subscription does
the generation; SousMeow supplies structure, judgement prompts, review
discipline, versioning, and a finished deliverable.

## 2. North star

- A first-time visitor understands SousMeow in under 30 seconds.
- A first-time user completes their first Cookbook in under 10 minutes.
- Every screen answers: "What is the next thing I should do?"
- Clarity beats feature count.

## 3. Effort distribution

- 40% Recipe Runner
- 20% Design System
- 15% Public marketing site
- 10% Kitchen Dashboard
- 10% Authentication
- 5% Remaining features

The Recipe Runner is the product. Everything else supports it.

## 4. The core loop (must be immediately understandable)

Pantry
to generated prompt
to Copy Prompt
to run in the user's chosen AI
to paste response into SousMeow
to complete Quality Checks
to approve or revise
to continue
to export Project Kit

The user must never wonder: what to do next, what information was used,
where to paste the AI response, why the output is being reviewed, what
accepting it will unlock, or where the finished work will go.

## 5. Fixed scope quantities

- Exactly one executable sample Cookbook ("Launch Day Kit") with four
  Recipes.
- The sample Cookbook has eight Pantry fields using six supported
  ingredient types: short text, long text, single select, multi select,
  number, URL.
- One high-quality example AI response per Recipe (four total), seeded
  as realistic sample content and surfaced via a "Paste example
  response" affordance so the demo loop always completes without an
  external AI. These are seed data, not lorem ipsum, and are clearly
  marked as sample data.
- Seven additional marketplace Cookbooks that are presentation-complete
  but intentionally non-executable. They show an honest Preview or
  Coming Soon state, never a broken Start button. Paid Cookbooks show
  their price and must not simulate checkout success.

## 6. Runner semantics

- Version history in the Runner means Artifact response versions, not
  Cookbook publishing versions.
- Exact raw pasted responses are immutable. Edits are stored as new
  Artifact versions. Restoring an old version creates a new version.
- Pasted AI responses are untrusted input: validated server side,
  escaped on output, never executed or interpreted as HTML.
- Quality Checks are human-confirmed. The product never claims automatic
  quality evaluation.
- A Recipe unlocks only when every prior Recipe's Artifact is approved.
  Order is enforced server side.
- All core Runner actions work at 390px viewport width.

## 7. Experience rules

- Never present a blank screen. Every screen states what the page does,
  why it matters, and what happens next.
- Every page has a thoughtful empty state (No Projects, No Pantry Yet,
  No Exports, No Search Results, No Cookbook Started) that teaches the
  product rather than merely stating that nothing exists.
- Tasteful microinteractions: hover states, subtle transitions, loading
  states, success animations, copy confirmation, progress indicators,
  and completion celebrations. Calm and premium; no excessive motion;
  respects reduced-motion preferences.
- Visual system: "Cozy Engineering". Original watercolor-inspired
  environment art, restrained original cat mascots, and structured
  repository-style UI. No copyrighted characters and no references to
  copyrighted visual properties.
- Real seeded content throughout. No lorem ipsum.

## 8. Platform and security requirements

- Plain PHP 8 with PDO. No external AI API, SMTP service, Stripe SDK,
  Node runtime, Docker service, or background worker.
- Hostinger-compatible deployment (Apache, mod_rewrite, MySQL). SQLite
  driver for zero-setup local runs.
- PDO prepared statements for every dynamic database operation.
- Authorization enforced server side on every route and mutation.
- All rendered user content is escaped.
- Every state-changing form is CSRF protected.
- Admin creation happens only through a documented CLI seed script
  (`php scripts/seed.php`) that generates and prints a temporary
  password. No web-accessible installer exists.

## 9. Priorities, in order

1. A complete end-to-end Cookbook execution loop.
2. An exceptionally polished Recipe Runner.
3. A distinctive Cozy Engineering visual system.
4. Reliable project state, artifact versions, quality checks, exports.
5. A convincing but deliberately limited marketplace shell.
6. Straightforward Hostinger-compatible deployment.

## 10. Do not optimize for

Enterprise scalability, microservices, plugin systems, generic workflow
engines, future AI APIs, or multi-tenancy. When multiple technically
correct implementations exist, choose the one that produces the most
beautiful, understandable, and portfolio-worthy result rather than the
most abstract or architecturally generic one.

## 11. Implementation discipline

- Build vertical slices that remain runnable; test each slice.
- Never leave primary navigation pointing to missing pages.
- Never substitute static mockups for required functional behavior.
- Prefer complete, understandable PHP over premature abstractions.
- Run `php -l` across all PHP files and complete the documented test
  flow before declaring completion.
- Before the final report, audit the repository for secrets, debug
  credentials, accidental production URLs, and em dashes.
