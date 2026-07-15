# Career Collection — source notes

Internal research notes for the SousMeow Career Collection. Public sources informed principles only. All user-facing Cookbook copy, prompts, examples, and checklists are original SousMeow writing. No proprietary templates, commercial prompt packs, or long source passages were reproduced.

## Shared principles extracted

- Preserve facts; never invent employers, titles, dates, credentials, metrics, clients, salary figures, or competing offers.
- Prefer readable, evidence-based materials over fear-based ATS or negotiation myths.
- Teach the copy-and-paste loop for beginners who may not know prompting.
- Distinguish missing evidence from permission to invent.
- Warn users before pasting private identifiers into external AI tools.

## Cookbook 1 — Tailor Your Resume to a Job

### Sources reviewed

- Tufts Career Center — resume keywords / ATS clarification: https://careers.tufts.edu/blog/2025/10/30/resume-keywords-what-they-are-and-how-to-use-them/
- GW SEAS Careers — AI resume / perfect-match trap: https://careers.seas.gwu.edu/blog/2025/10/13/ai-resumes-and-the-perfect-match-trap/
- OSU Engineering Career Services — ATS and AI myths: https://ecs.osu.edu/news/2025/10/understanding-ats-and-ai-job-search
- NDSU Career and Advising Center — navigating ATS: https://career-advising.ndsu.edu/navigating-an-applicant-tracking-system-ats/
- University of Rhode Island CCEE — AI arms race and keyword stuffing risks: https://career.uri.edu/blog/2026/01/20/the-new-arms-race-ai-used-by-job-seekers-vs-that-used-by-employers/

### Principles extracted

- ATS organizes applications; humans still decide. Keywords help when used naturally.
- Keyword stuffing, hidden text, and copy-paste job descriptions harm credibility.
- Favor standard headings, plain formatting, accomplishment bullets grounded in evidence.
- Mark gaps and missing metrics instead of fabricating numbers.

### Disputed or weak claims excluded

- The common viral claim that “75% of resumes are automatically rejected by ATS.”
- Advice that treats keyword density as the primary success factor.

### Safety / accuracy constraints

- Invent-nothing rule on all career facts.
- Privacy warning on pasted resumes and postings.
- Prefer qualitative language when metrics are unavailable.

### Transformation

Job parsing → truthful match map → bullet rewrite → summary/skills → full draft → human/ATS review → final kit. Teaching fields live outside prompts via Runner schema extensions.

## Cookbook 2 — Prepare for an Interview

### Sources reviewed

- UVA Career Center — STAR interviews: https://career.virginia.edu/Students/Launch/Interviews/STAR
- MIT CAPD — STAR method worksheet guidance: https://capd.mit.edu/resources/the-star-method-for-behavioral-interviews/
- University of Maryland Career Center — preparing for interviews: https://careers.umd.edu/find-jobs-internships/interviewing/preparing-for-interviews

### Principles extracted

- Prepare role criteria and a small bank of real stories before drafting answers.
- Structure behavioral answers with Situation, Task, Action, Result; keep Action as the weight.
- Practice out loud; avoid memorized scripts that invent details.
- Prepare questions for the employer and a specific thank-you note grounded in the conversation.

### Disputed or weak claims excluded

- Scripted “perfect answers” that the AI speaks for the user.
- Fake transcript mock interviews where the assistant plays both sides as the candidate.

### Safety / accuracy constraints

- One-question-at-a-time mock interview protocol in a single external chat.
- No invented company facts or achievements.

### Transformation

Criteria brief → story bank → predicted questions → answer frameworks → mock conductor prompt → review notes → day-of kit.

## Cookbook 3 — Write a Strong Cover Letter

### Sources reviewed

- University career centers’ common cover-letter advice: lead with employer need, one clear argument, concrete evidence, short length, close with a next step. (Principles synthesized; no single proprietary template used.)

### Principles extracted

- The letter must advance an argument the resume alone cannot.
- Evidence selection precedes drafting.
- Generic enthusiasm and resume repetition are primary failure modes.

### Disputed or weak claims excluded

- Formulaic “I am writing to apply…” as a required opening.
- Soft claims about knowing company culture when not supplied.

### Safety / accuracy constraints

- Invent-nothing on mission, metrics, and insider knowledge.
- Privacy on pasted resume bullets.

### Transformation

Needs → evidence → argument → draft → de-robot → short application + follow-up messages.

## Cookbook 4 — Improve Your LinkedIn Profile

### Sources reviewed

- Broad public LinkedIn profile guidance from university career centers: clear headline, About that explains direction, experience bullets with proof, skills tied to evidence, modest content planning. (Principles only.)

### Principles extracted

- Positioning before clever phrasing.
- Headlines should say what you do and where you are heading.
- Experience entries need outcomes without influencer exaggeration.
- Consistency across headline, About, and experience builds credibility.

### Disputed or weak claims excluded

- Growth-hacker personal-brand formulas that require invented audience metrics.
- “Guru” tone unless the user asks for it.

### Safety / accuracy constraints

- No invented awards, follower counts, or client lists.
- Remove private contact details before pasting profile text into external AI.

### Transformation

Positioning → headlines → About → experience → skills/proof → content pillars → consistency review.

## Cookbook 5 — Plan a Career Change

### Sources reviewed

- Public career-change guidance from university and workforce sources emphasizing transferable skills plus honest gap analysis, staged learning, and low-risk validation. (Principles only.)

### Principles extracted

- Transferable skills help but do not erase skill gaps.
- Separate evidence, gaps, and uncertainties.
- Prefer experiments and 30/60/90 plans over irreversible advice.

### Disputed or weak claims excluded

- “Just quit and figure it out.”
- Guarantees that a pivot succeeds within a fixed timeline.

### Safety / accuracy constraints

- Present options, assumptions, and risks.
- Never instruct quitting a job or making major financial decisions.
- No invented certifications or prior roles.

### Transformation

Transition definition → transferable map → capability compare → gaps → learning priority → staged plan → experiments → roadmap + review template.

## Cookbook 6 — Prepare for a Salary Negotiation

### Sources reviewed

- Public salary-negotiation preparation advice emphasizing evidence, researched ranges the user verifies, BATNA/alternatives, calm scripts, and documentation. (Principles only; no invented comps.)

### Principles extracted

- Communication prep is not legal, financial, or employment-law advice.
- Market figures must be user-verified from reputable sources.
- Separate target, anchor, and fallbacks; prepare for pushback without inventing competing offers.

### Disputed or weak claims excluded

- AI-invented “market rates.”
- Claims about what someone “deserves” without evidence.

### Safety / accuracy constraints

- Research checklist instead of fabricated ranges.
- No invented competing offers or confidential compensation of peers.

### Transformation

Objective → evidence → research checklist → target/fallback table → opening script → pushback bank → follow-up + decision checklist.

## Schema adaptation note

Runner previously lacked first-class `before_you_begin`, `common_problems`, and `recovery_guidance`. Those fields were added to recipes and the understand step so teaching is not buried inside giant prompts.
