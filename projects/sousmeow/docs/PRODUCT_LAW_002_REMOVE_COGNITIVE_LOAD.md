# Product Law 002 — Remove Cognitive Load

> The product is not the workflow.
> The product is the cognitive load the workflow removes.

**Status:** Core Product Law  
**Peer Principle:** Workflows over Prompts  
**Scope:** All Cookbooks  
**Enforcement:** Before authoring

This is a **CORE PRODUCT LAW**. It is a peer to **Workflows over Prompts**. It applies to **every Cookbook**.

---

## Hierarchy

| Layer | Name | Role |
| --- | --- | --- |
| **Product Law** | Remove Cognitive Load | The non-negotiable purpose of every Cookbook |
| **Constraint** | Stay Below the Complexity Threshold | User-facing complexity must stay low even when internal expertise is high |
| **Evaluation Tool** | Complexity Scorecard | Concrete checks that surface cognitive-load failures early |
| **Approval Process** | Complexity Gate | Mandatory pass before any Cookbook is authored |

No Cookbook proposal advances to authoring until it passes the Complexity Gate.

---

## Who SousMeow Is For

SousMeow exists for people who struggle to get consistently good results from AI.

It is built for people who struggle to get reliable results from ChatGPT, Claude, Gemini, and other AI assistants — people who do not want to become prompt engineers, workflow designers, or domain experts just to finish a project.

Cookbooks must transfer expertise without requiring expertise.

If using a Cookbook ever feels like learning a new profession, the Cookbook has failed this law.

---

## The Law

### 1. Cognitive load is the product

- The product is not the workflow.
- The product is the cognitive load the workflow removes.
- A Cookbook succeeds when the user reaches a finished result without carrying the professional judgment that produced it.

### 2. Expertise transfer, not expertise demand

- Cookbooks should transfer expertise without requiring expertise.
- The Cookbooks contain the expertise.
- Users should not need to:
  - learn prompt engineering
  - learn AI workflows
  - understand implementation details
  - become experts

### 3. Hide judgment; ask only for known facts

- Workflow complexity may be high **internally**.
- User-facing complexity must remain **low**.
- The workflow should hide professional judgment whenever possible.
- The workflow should ask only for facts the user actually knows.
- The workflow must never invent those facts.
- Pantry ingredients are facts the user owns. Prompts, sequencing, checks, and domain judgment stay inside the Cookbook.

### 4. Stay below the Complexity Threshold

The **Constraint** under this law is: **Stay Below the Complexity Threshold.**

Internal process may be rich. External experience must stay simple enough that a struggling AI user can still finish.

If a proposal requires the user to perform expert work they do not already do, it is above the threshold.

### 5. Complexity Scorecard (evaluation tool)

Before authoring, score the proposal against the **Complexity Scorecard**. Checks include (at minimum):

1. **Audience fit** — Is this clearly usable by someone who struggles with AI, not only by experts?
2. **Expertise demand** — Does the user need new professional skill, or only facts they already know?
3. **Judgment visibility** — Is professional judgment hidden inside the workflow, or pushed onto the user?
4. **Fact honesty** — Does every required input ask for something the user can actually know? Does the workflow refuse to invent missing facts?
5. **Surface simplicity** — Would a first-time user describe the experience as guided steps, or as learning a new craft?
6. **Failure test** — If using this Cookbook feels like learning a new profession, does the design fail this check on purpose so it can be caught?

### 6. Complexity Gate (approval process)

Every Cookbook proposal **must** pass the **Complexity Gate** **before authoring**.

- Run the Complexity Scorecard on the proposal.
- Cookbooks failing **two or more** scorecard checks must be **simplified or rejected**.
- A failed gate is not a soft suggestion. Authoring does not begin until the proposal passes.
- Marketplace Cookbooks are bound by this law. Internal reference workflows that fail the gate must not ship as marketplace Cookbooks.

---

## Case: “Make, Not Generated” (expert version)

The current expert version of **“Make, Not Generated”** fails this law.

It demands too much expert judgment from the user, sits above the Complexity Threshold, and fails multiple Complexity Scorecard checks.

Therefore:

- It must not be authored or published as a marketplace Cookbook.
- The expert workflow should remain an **internal reference only**, not a marketplace Cookbook.
- Any future public version must be redesigned to pass the Complexity Gate first — asking only for known facts, hiding professional judgment, and staying usable by people who struggle with AI.

---

## Mandatory Authoring Rule

> Every Cookbook proposal MUST pass Product Law 002 before authoring.

This document is the authoritative source. Authoring guides reference this law; they do not restate it in full.

Peer principle for product direction: **Workflows over Prompts**.  
This law for Cookbook design: **Remove Cognitive Load**.

Together they mean: ship workflows, and ship only those workflows that remove cognitive load without teaching a new profession.
