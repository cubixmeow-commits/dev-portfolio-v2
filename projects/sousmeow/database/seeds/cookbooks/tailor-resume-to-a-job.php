<?php

declare(strict_types=1);

/**
 * Tailor Your Resume to a Job - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'tailor-resume-to-a-job',
    'title'               => 'Tailor Your Resume to a Job',
    'tagline'             => 'Align a real resume to a role without inventing experience.',
    'description'         => 'This Cookbook helps you read a job posting, map real evidence from your resume, improve weak bullets, organize a targeted summary and skills section, assemble a consistent resume draft, review it for readability and evidence, and produce a final application version. The goal is truthful alignment: show the match clearly, mark gaps honestly, and never invent experience, metrics, tools, credentials, employers, or dates. ' . sm_career_beginner_footer('a job requirement map, evidence and gap analysis, rewritten bullet bank, tailored resume, plain-text resume, and final fact-verification checklist', 7, 'about 80 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Job seekers tailoring an existing resume to a real posting without exaggerating experience',
    'outcome'             => 'job requirement map, evidence and gap analysis, rewritten bullet bank, tailored resume, plain-text resume, and final fact-verification checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 80,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 25,
    'stages' => [
        ['title' => 'Understand the match', 'summary' => 'Extract the role requirements and map them to truthful evidence or gaps.'],
        ['title' => 'Strengthen the content', 'summary' => 'Improve bullets, summary, and skills without keyword stuffing or invented metrics.'],
        ['title' => 'Assemble the draft', 'summary' => 'Build a complete tailored resume and review it for human clarity and screening readability.'],
        ['title' => 'Finalize for application', 'summary' => 'Prepare the final resume, plain-text version, and fact-verification checklist.'],
    ],
    'fields' => [
        ['field_key' => 'target_role', 'label' => 'Target role', 'type' => 'text', 'help' => 'Use the exact role title if known.', 'placeholder' => 'e.g. Junior operations analyst', 'sample_value' => 'Junior operations analyst'],
        ['field_key' => 'job_description', 'label' => 'Job description', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste the job posting or a concise role summary. Remove private recruiter notes and confidential employer material.'), 'placeholder' => 'Responsibilities, required skills, preferred skills, tools, and application instructions.', 'sample_value' => "Junior Operations Analyst at HarborLink Mobility\nResponsibilities: maintain weekly operations dashboards, clean route service data, document recurring process issues, coordinate updates with dispatch and customer support, prepare simple reports for operations managers.\nRequired: Excel or Google Sheets, attention to detail, clear written communication, comfort working with messy data, 1+ year admin or operations experience.\nPreferred: SQL exposure, transportation or logistics experience, experience with ticketing systems.\nPosting emphasizes reliable follow-through, practical problem solving, and clear status updates."],
        ['field_key' => 'existing_resume', 'label' => 'Existing resume', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste the current resume text or selected sections. Remove contact details, full addresses, private references, and identifying details not needed for tailoring.'), 'placeholder' => 'Summary, experience bullets, education, skills, and selected projects.', 'sample_value' => "Maya Patel\nAdministrative Assistant, Northstar Community College Continuing Education, 2022-present\n- Helped with reports\n- Updated enrollment spreadsheets\n- Answered student and instructor questions\n- Made a tracking sheet for certificate paperwork\nVolunteer Data Coordinator, City Food Share, 2021-2022\n- Cleaned volunteer sign-up spreadsheet\n- Sent weekly availability reminders\nSkills: Excel, Google Sheets, email, scheduling, basic SQL course completed"],
        ['field_key' => 'career_level', 'label' => 'Career level', 'type' => 'select', 'help' => 'Choose the level that best describes your current search.', 'options' => ['Student or early career', 'Mid-level', 'Senior or lead', 'Career change'], 'sample_value' => 'Student or early career'],
        ['field_key' => 'years_experience', 'label' => 'Years of relevant experience', 'type' => 'text', 'help' => 'A truthful range or note is fine.', 'placeholder' => 'e.g. 2 years admin support; 6 months analyst coursework', 'sample_value' => '2 years of administrative support plus volunteer spreadsheet cleanup'],
        ['field_key' => 'preferred_resume_length', 'label' => 'Preferred resume length', 'type' => 'select', 'help' => 'Choose the length target. Employer instructions should win if supplied.', 'options' => ['One page', 'One to two pages', 'Follow employer instructions'], 'sample_value' => 'One page'],
        ['field_key' => 'strongest_achievements', 'label' => 'Strongest achievements', 'type' => 'textarea', 'help' => 'List real accomplishments, examples, tools, or outcomes. Only include numbers you can verify.', 'placeholder' => 'e.g. Built a spreadsheet tracker used weekly by the team.', 'sample_value' => "Built a certificate paperwork tracker that staff used to see missing forms\nCleaned a volunteer sign-up spreadsheet with duplicate rows and unclear availability notes\nPrepared weekly enrollment updates for a program coordinator\nCompleted an introductory SQL course with a small project querying sample enrollment data"],
        ['field_key' => 'facts_to_preserve', 'label' => 'Facts to preserve', 'type' => 'textarea', 'help' => 'List non-negotiable facts, dates, titles, credentials, gaps, or claims that must not be changed. These are boundaries, not suggestions.', 'placeholder' => 'e.g. Do not claim SQL work experience; keep title as Administrative Assistant.', 'sample_value' => "Do not claim paid analyst title.\nDo not claim transportation or logistics experience.\nSQL is coursework only, not job experience.\nKeep Administrative Assistant title and 2022-present date range."],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the resume language should sound.', 'options' => ['Clear and professional', 'Concise and confident', 'Warm and practical', 'Direct and evidence-focused'], 'sample_value' => 'Clear and professional'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Name gaps, ATS preference, length constraints, application instructions, or claims to avoid.', 'placeholder' => 'e.g. Career change; avoid keyword stuffing; conventional format requested.', 'sample_value' => 'I am moving from admin support toward operations analysis. Keep to one page, use a conventional ATS-friendly format, and do not make the SQL course sound like work experience.'],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'parse-target-role',
            'title' => 'Parse target role',
            'summary' => 'Extract responsibilities, requirements, recurring terms, and evidence signals from the posting.',
            'why_it_matters' => 'A tailored resume starts with understanding what the employer actually asks for. This step separates real requirements from generic employer language so the rest of the work is grounded in the posting instead of guesswork or fear-based keyword stuffing.',
            'unlocks_text' => 'Approving unlocks the truthful match map.',
            'before_you_begin' => 'Have the posting ready and remove private recruiter notes or confidential employer documents before pasting. If the posting is short, mark unknowns instead of filling them in.',
            'common_problems' => 'The AI may treat every phrase as a requirement, overvalue generic traits, or infer hidden priorities from the employer name. Keep the map tied to words in the posting.',
            'recovery_guidance' => 'If the output sounds like invented research, rerun and ask for posting-backed evidence only. If it is too broad, ask for the top responsibilities and top requirements.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are analyzing a job posting before tailoring a resume. Fact rule: {$factRule}

Target role: {{target_role}}
Career level: {{career_level}}
Job description:
{{job_description}}
Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Role snapshot
Two or three sentences summarizing the role using only the posting.

## Responsibilities
Bullets for the work the person would likely do, grouped by theme if helpful.

## Required and preferred qualifications
Separate required qualifications from preferred qualifications. Mark unclear items as unclear instead of guessing.

## Recurring terms and evidence signals
List repeated terms, tools, work habits, and proof signals the resume should address naturally.

## Generic employer language to treat carefully
Identify broad phrases that should not be mistaken for concrete requirements.

Keep the headings in order. Do not add outside research.
TXT,
            'example_response' => <<<'MD'
## Role snapshot
HarborLink Mobility is hiring a junior operations analyst to keep weekly operations information accurate, visible, and useful for managers. The posting emphasizes spreadsheet work, messy data cleanup, practical reporting, documentation, and clear updates across dispatch and customer support.

## Responsibilities
- Maintain weekly operations dashboards and simple reports.
- Clean route service data and identify recurring process issues.
- Document problems or repeatable fixes in a clear way.
- Coordinate status updates with dispatch and customer support.
- Support operations managers with reliable follow-through.

## Required and preferred qualifications
Required:
- Excel or Google Sheets.
- Attention to detail.
- Clear written communication.
- Comfort working with messy data.
- 1+ year of administrative or operations experience.

Preferred:
- SQL exposure.
- Transportation or logistics experience.
- Ticketing system experience.

Unclear:
- The posting does not specify dashboard software, exact SQL level, ticketing platform, or whether the role owns analysis decisions.

## Recurring terms and evidence signals
- Spreadsheet accuracy and cleanup.
- Weekly reporting or dashboard updates.
- Documentation of recurring issues.
- Cross-team communication.
- Practical problem solving and status updates.
- Evidence should show real spreadsheets, reports, documentation, and coordination.

## Generic employer language to treat carefully
- "Reliable follow-through" is important but should be supported with concrete examples.
- "Practical problem solving" should not become an unsupported claim.
- "Clear status updates" should be shown through real communication work, not repeated as a keyword.
MD,
            'output_sections' => $sections([
                ['role_snapshot', 'Role snapshot'],
                ['responsibilities', 'Responsibilities'],
                ['required_and_preferred_qualifications', 'Required and preferred qualifications'],
                ['recurring_terms_and_evidence_signals', 'Recurring terms and evidence signals'],
                ['generic_employer_language_to_treat_carefully', 'Generic employer language to treat carefully'],
            ]),
            'checks' => [
                ['label' => 'Posting-backed requirements', 'help' => 'Responsibilities and qualifications come from the supplied posting.', 'evidence_sections' => ['responsibilities', 'required_and_preferred_qualifications']],
                ['label' => 'Fluff is separated', 'help' => 'Generic employer language is not treated as a hard requirement.', 'evidence_sections' => ['generic_employer_language_to_treat_carefully']],
                ['label' => 'Signals are resume-useful', 'help' => 'Recurring terms point to evidence, not keyword stuffing.', 'evidence_sections' => ['recurring_terms_and_evidence_signals']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'build-truthful-match-map',
            'title' => 'Build truthful match map',
            'summary' => 'Classify each requirement as supported, partially supported, unsupported, or unclear.',
            'why_it_matters' => 'Tailoring should make real matches easier to see. This step prevents unsupported requirements from being converted into claimed experience and gives the later draft a clean evidence and gap analysis.',
            'unlocks_text' => 'Approving unlocks resume bullet strengthening.',
            'before_you_begin' => 'Gather your current resume, strongest achievements, and facts that must not change. Be ready to leave true gaps visible.',
            'common_problems' => 'A common mistake is turning coursework into work experience, exposure into proficiency, or a preferred skill into a claimed achievement. Unsupported means unsupported.',
            'recovery_guidance' => 'If the map overclaims, rerun and require direct evidence from the resume for every supported label. If useful experience is missing a metric, mark the metric opportunity instead of inventing a number.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are building a truthful resume match map. Fact rule: {$factRule}

Target role: {{target_role}}
Career level: {{career_level}}
Years of experience: {{years_experience}}
Existing resume:
{{existing_resume}}
Strongest achievements:
{{strongest_achievements}}
Facts to preserve:
{{facts_to_preserve}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved role analysis:
{{artifact:parse-target-role}}

Produce Markdown with these exact headings:

## Job requirement map
For each responsibility, required qualification, and preferred qualification, classify it as Supported, Partially supported, Unsupported, or Unclear. Include the evidence or reason.

## Evidence and gap analysis
Summarize the strongest evidence, partial matches, unsupported gaps, and unclear items to verify.

## Safe alignment opportunities
Ways to make real matches more visible without changing facts.

## Claims not to make
Unsupported claims that must not appear in the resume.

Keep the headings in order. Never convert unsupported requirements into claimed experience.
TXT,
            'example_response' => <<<'MD'
## Job requirement map
| Requirement | Classification | Evidence or reason |
| --- | --- | --- |
| Maintain weekly dashboards or reports | Partially supported | Maya prepared weekly enrollment updates; no dashboard ownership is stated. |
| Clean messy data | Supported | Resume and achievements mention updating enrollment spreadsheets and cleaning duplicate volunteer sign-up rows. |
| Document recurring process issues | Partially supported | Certificate paperwork tracker suggests process tracking; recurring issue documentation is not explicit. |
| Coordinate updates across teams | Partially supported | Student and instructor questions plus program coordinator updates show communication, but not dispatch/customer support. |
| Excel or Google Sheets | Supported | Skills list includes Excel and Google Sheets; spreadsheet examples support use. |
| Clear written communication | Supported | Email, reminders, and updates are supplied. |
| 1+ year admin or operations experience | Supported | Administrative Assistant role from 2022-present. |
| SQL exposure | Supported | Introductory SQL course completed; this is exposure, not work experience. |
| Transportation or logistics experience | Unsupported | No transportation or logistics experience supplied. |
| Ticketing systems | Unsupported | No ticketing system experience supplied. |

## Evidence and gap analysis
Strongest evidence: spreadsheet cleanup, weekly enrollment updates, certificate paperwork tracker, and written reminders. Partial matches: dashboard/reporting language and process documentation can be framed through weekly updates and trackers. Unsupported gaps: transportation/logistics and ticketing systems. Unclear items: exact report frequency beyond weekly enrollment updates and whether any spreadsheet work produced measurable time savings.

## Safe alignment opportunities
- Rename weak "helped with reports" language into a specific weekly enrollment reporting bullet.
- Emphasize spreadsheet cleanup and duplicate-row work under experience.
- Place SQL in skills or education as coursework only.
- Use "operations support" carefully when describing administrative coordination, without changing the job title.

## Claims not to make
- Do not claim logistics, transportation, dispatch, or ticketing system experience.
- Do not claim SQL work experience or advanced SQL proficiency.
- Do not claim dashboard ownership unless the weekly enrollment updates were actually dashboards.
- Do not invent percentages, time saved, error reductions, or team size.
MD,
            'output_sections' => $sections([
                ['job_requirement_map', 'Job requirement map'],
                ['evidence_and_gap_analysis', 'Evidence and gap analysis'],
                ['safe_alignment_opportunities', 'Safe alignment opportunities'],
                ['claims_not_to_make', 'Claims not to make'],
            ]),
            'checks' => [
                ['label' => 'Each requirement is classified', 'help' => 'The map uses supported, partially supported, unsupported, or unclear labels.', 'evidence_sections' => ['job_requirement_map']],
                ['label' => 'Unsupported stays unsupported', 'help' => 'Gaps are not rewritten as experience.', 'evidence_sections' => ['evidence_and_gap_analysis', 'claims_not_to_make']],
                ['label' => 'Alignment is safe', 'help' => 'Suggested improvements make real evidence visible without changing facts.', 'evidence_sections' => ['safe_alignment_opportunities']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'strengthen-experience-bullets',
            'title' => 'Strengthen experience bullets',
            'summary' => 'Rewrite weak duties into accomplishment-oriented bullets while preserving facts.',
            'why_it_matters' => 'Resume bullets should show what you did, how you worked, and why it mattered when that evidence is available. This step improves readability and relevance while flagging optional metric opportunities instead of inventing numbers.',
            'unlocks_text' => 'Approving unlocks the targeted summary and skills organization.',
            'before_you_begin' => 'Review the match map and decide which weak bullets are worth improving. Keep original titles, employers, dates, and scope accurate.',
            'common_problems' => 'The AI may add outcomes, tools, scale, or leadership that were not supplied. It may also over-optimize every bullet for the job posting. Preserve facts and vary wording naturally.',
            'recovery_guidance' => 'If a bullet invents a metric, replace it with a metric opportunity note. If a bullet sounds inflated, ask for a plainer accomplishment-oriented version.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are strengthening resume experience bullets. Fact rule: {$factRule}

Target role: {{target_role}}
Preferred tone: {{preferred_tone}}
Existing resume:
{{existing_resume}}
Strongest achievements:
{{strongest_achievements}}
Facts to preserve:
{{facts_to_preserve}}

Approved match map:
{{artifact:build-truthful-match-map}}

Produce Markdown with these exact headings:

## Rewritten bullet bank
Rewrite the most relevant weak duties into accomplishment-oriented bullets. Preserve facts. Do not invent metrics, tools, seniority, or outcomes.

## Original-to-revised notes
Briefly explain what changed for each rewritten bullet.

## Optional metric opportunities
List useful numbers the candidate could add only if they can verify them.

## Bullets to leave out or de-emphasize
Name bullets that are less relevant for this target role.

Keep the headings in order. Mark missing metrics; never invent numbers.
TXT,
            'example_response' => <<<'MD'
## Rewritten bullet bank
- Prepared weekly enrollment updates for a program coordinator, keeping spreadsheet information organized for recurring operations review.
- Updated enrollment spreadsheets and corrected incomplete or duplicate entries to improve the reliability of program records.
- Built a certificate paperwork tracker that helped staff see missing forms and follow up on open items.
- Answered student and instructor questions by email and phone, routing issues and status updates to the appropriate program contact.
- Cleaned a volunteer sign-up spreadsheet by organizing duplicate rows and unclear availability notes.
- Sent weekly volunteer availability reminders to support predictable scheduling.

## Original-to-revised notes
- "Helped with reports" became a specific weekly enrollment update bullet without claiming full dashboard ownership.
- "Updated enrollment spreadsheets" now shows the type of data quality work supplied by the candidate.
- "Made a tracking sheet" became a clearer process-tracking achievement.
- Communication work now connects questions, routing, and status updates.
- Volunteer spreadsheet cleanup and reminders were separated so each proof point is easy to scan.

## Optional metric opportunities
- Number of weekly enrollment reports or update cycles prepared, if verified.
- Number of programs, forms, or certificate records tracked, if verified.
- Approximate size of volunteer spreadsheet cleaned, if verified.
- Any verified reduction in missing paperwork or duplicate rows, if records support it.

## Bullets to leave out or de-emphasize
- Generic office tasks that do not show spreadsheet work, reporting, documentation, or coordination.
- Any SQL bullet beyond coursework unless there is a real project or work example.
- Unrelated student service details that do not support analyst readiness.
MD,
            'output_sections' => $sections([
                ['rewritten_bullet_bank', 'Rewritten bullet bank'],
                ['original_to_revised_notes', 'Original-to-revised notes'],
                ['optional_metric_opportunities', 'Optional metric opportunities'],
                ['bullets_to_leave_out_or_de_emphasize', 'Bullets to leave out or de-emphasize'],
            ]),
            'checks' => [
                ['label' => 'Bullets preserve facts', 'help' => 'No new employers, tools, titles, metrics, or outcomes were added.', 'evidence_sections' => ['rewritten_bullet_bank']],
                ['label' => 'Metrics are optional', 'help' => 'Missing numbers are flagged as opportunities, not stated as facts.', 'evidence_sections' => ['optional_metric_opportunities']],
                ['label' => 'Relevance is intentional', 'help' => 'Less relevant material is clearly de-emphasized.', 'evidence_sections' => ['bullets_to_leave_out_or_de_emphasize']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'improve-summary-and-skills',
            'title' => 'Improve summary and skills',
            'summary' => 'Create a targeted summary and naturally organized skills section.',
            'why_it_matters' => 'The top of the resume should quickly explain the truthful match without dumping every keyword from the posting. A clear summary and organized skills section help readers find evidence while keeping unsupported gaps out.',
            'unlocks_text' => 'Approving unlocks the complete tailored resume draft.',
            'before_you_begin' => 'Use the approved match map and bullet bank. Decide whether the resume needs a summary at all for the target level and length.',
            'common_problems' => 'Summaries often become inflated branding statements or repeat the job description. Skills sections can become keyword piles. Keep both anchored to real experience and coursework labels.',
            'recovery_guidance' => 'If the summary overclaims, ask for a more modest version. If the skills list is too long, group only skills with supplied evidence or honest exposure.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are improving a resume summary and skills section. Fact rule: {$factRule}

Target role: {{target_role}}
Career level: {{career_level}}
Years of experience: {{years_experience}}
Preferred resume length: {{preferred_resume_length}}
Preferred tone: {{preferred_tone}}
Existing resume:
{{existing_resume}}
Facts to preserve:
{{facts_to_preserve}}

Approved role analysis:
{{artifact:parse-target-role}}

Approved match map:
{{artifact:build-truthful-match-map}}

Approved bullet bank:
{{artifact:strengthen-experience-bullets}}

Produce Markdown with these exact headings:

## Targeted summary options
Provide two resume summary options. Keep them truthful, concise, and appropriate for the career level.

## Skills organization
Group skills naturally by category. Label coursework, exposure, or beginner skills honestly.

## Keywords to use naturally
List role language that can appear when supported by evidence. Do not dump keywords.

## Summary and skills claims to avoid
Name overclaims, unsupported skills, or inflated labels to keep out.

Keep the headings in order.
TXT,
            'example_response' => <<<'MD'
## Targeted summary options
Option 1: Administrative and operations support professional with 2 years of experience maintaining spreadsheets, preparing weekly updates, tracking paperwork, and communicating status clearly across students, instructors, and program staff. Bringing strong Google Sheets/Excel habits and introductory SQL coursework to a junior operations analyst role.

Option 2: Early-career operations support candidate with hands-on experience cleaning spreadsheet data, organizing recurring updates, and documenting open items through trackers. Known for reliable follow-through, clear written communication, and careful handling of administrative records.

## Skills organization
Spreadsheets and reporting: Excel, Google Sheets, spreadsheet cleanup, weekly enrollment updates, tracking sheets.

Operations support: status updates, process tracking, paperwork follow-up, scheduling support, record organization.

Communication: email coordination, phone support, student and instructor questions, volunteer availability reminders.

Coursework and exposure: introductory SQL coursework with a sample enrollment-data project.

## Keywords to use naturally
- Operations support.
- Spreadsheet cleanup.
- Weekly updates.
- Data quality.
- Status updates.
- Process tracking.
- Clear written communication.
- SQL exposure or SQL coursework.

## Summary and skills claims to avoid
- Do not call Maya an operations analyst yet.
- Do not list transportation, logistics, dispatch, or ticketing systems as skills.
- Do not present SQL as professional work experience.
- Do not claim dashboard ownership unless the weekly updates were truly dashboards.
- Do not add "advanced Excel" unless that level is verified.
MD,
            'output_sections' => $sections([
                ['targeted_summary_options', 'Targeted summary options'],
                ['skills_organization', 'Skills organization'],
                ['keywords_to_use_naturally', 'Keywords to use naturally'],
                ['summary_and_skills_claims_to_avoid', 'Summary and skills claims to avoid'],
            ]),
            'checks' => [
                ['label' => 'Summary fits real level', 'help' => 'The summary does not upgrade the candidate beyond supplied experience.', 'evidence_sections' => ['targeted_summary_options']],
                ['label' => 'Skills are organized, not dumped', 'help' => 'Skills are grouped and labeled by real evidence or coursework.', 'evidence_sections' => ['skills_organization']],
                ['label' => 'Unsupported claims are blocked', 'help' => 'The avoid list catches overclaims before drafting.', 'evidence_sections' => ['summary_and_skills_claims_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'assemble-tailored-resume',
            'title' => 'Assemble tailored resume',
            'summary' => 'Create a complete, consistent resume draft tailored to the role.',
            'why_it_matters' => 'The tailored resume should read like one coherent document, not a pile of optimized fragments. This step assembles the approved pieces and uses an ATS-friendly conventional format when requested, without tables, graphics, columns, or hidden keyword blocks.',
            'unlocks_text' => 'Approving unlocks the human and ATS review.',
            'before_you_begin' => 'Choose the preferred summary option and confirm length expectations. Keep exact names, dates, titles, degrees, and contact information outside SousMeow if you have removed private details.',
            'common_problems' => 'The AI may add missing contact details, change dates, add tools, or create a visually complex format. Keep the draft plain and consistent.',
            'recovery_guidance' => 'If the draft is too long, ask for a one-page version that trims lower-relevance bullets. If it invents facts, replace them with placeholders or remove them.',
            'est_minutes' => 15,
            'prompt_template' => <<<TXT
You are assembling a tailored resume draft. Fact rule: {$factRule}

Target role: {{target_role}}
Career level: {{career_level}}
Years of experience: {{years_experience}}
Preferred resume length: {{preferred_resume_length}}
Preferred tone: {{preferred_tone}}
Existing resume:
{{existing_resume}}
Facts to preserve:
{{facts_to_preserve}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved bullet bank:
{{artifact:strengthen-experience-bullets}}

Approved summary and skills:
{{artifact:improve-summary-and-skills}}

Approved match map:
{{artifact:build-truthful-match-map}}

Produce Markdown with these exact headings:

## Tailored resume draft
A complete resume draft with consistent headings and truthful content. If a conventional or ATS-friendly format is requested, use simple headings, plain bullets, and no tables, graphics, text boxes, columns, or hidden keyword sections.

## Tailoring rationale
Bullets explaining how the draft aligns real evidence to the role.

## Missing information placeholders
List any contact details, dates, degree details, links, metrics, or facts the candidate must fill in or verify.

Keep the headings in order. Do not invent missing resume content.
TXT,
            'example_response' => <<<'MD'
## Tailored resume draft
Maya Patel  
[City, State] | [Email] | [Phone] | [LinkedIn or portfolio if relevant]

Summary  
Administrative and operations support professional with 2 years of experience maintaining spreadsheets, preparing weekly updates, tracking paperwork, and communicating status clearly across students, instructors, and program staff. Bringing strong Google Sheets/Excel habits and introductory SQL coursework to a junior operations analyst role.

Skills  
- Spreadsheets and reporting: Excel, Google Sheets, spreadsheet cleanup, weekly enrollment updates, tracking sheets
- Operations support: status updates, process tracking, paperwork follow-up, scheduling support, record organization
- Communication: email coordination, phone support, student and instructor questions, volunteer availability reminders
- Coursework and exposure: introductory SQL coursework with sample enrollment-data project

Experience  
Administrative Assistant, Northstar Community College Continuing Education | 2022-present
- Prepared weekly enrollment updates for a program coordinator, keeping spreadsheet information organized for recurring operations review.
- Updated enrollment spreadsheets and corrected incomplete or duplicate entries to improve the reliability of program records.
- Built a certificate paperwork tracker that helped staff see missing forms and follow up on open items.
- Answered student and instructor questions by email and phone, routing issues and status updates to the appropriate program contact.

Volunteer Data Coordinator, City Food Share | 2021-2022
- Cleaned a volunteer sign-up spreadsheet by organizing duplicate rows and unclear availability notes.
- Sent weekly volunteer availability reminders to support predictable scheduling.

Education and Coursework  
- Introductory SQL course, [Provider or school], [Year if verified]

## Tailoring rationale
- The summary names spreadsheet work, weekly updates, tracking, and communication because those are supported by Maya's experience and relevant to the posting.
- Skills are grouped around spreadsheet/reporting, operations support, communication, and coursework instead of copying every posting term.
- SQL is labeled as coursework and exposure, not work experience.
- Unsupported logistics, transportation, dispatch, and ticketing claims are left out.

## Missing information placeholders
- Add only the contact details Maya wants to include.
- Verify the SQL course provider and year before adding them.
- Add verified metrics for reports, records, or spreadsheet cleanup only if Maya can confirm them.
- Confirm whether the employer requested one page or a specific file format.
MD,
            'output_sections' => $sections([
                ['tailored_resume_draft', 'Tailored resume draft'],
                ['tailoring_rationale', 'Tailoring rationale'],
                ['missing_information_placeholders', 'Missing information placeholders'],
            ]),
            'checks' => [
                ['label' => 'Draft is complete and consistent', 'help' => 'The resume has clear sections and no unexplained fragments.', 'evidence_sections' => ['tailored_resume_draft']],
                ['label' => 'Format matches constraints', 'help' => 'ATS-friendly requests use simple headings and bullets without tables or graphics.', 'evidence_sections' => ['tailored_resume_draft']],
                ['label' => 'Missing facts stay placeholders', 'help' => 'Unknown contact details, metrics, and coursework details are not invented.', 'evidence_sections' => ['missing_information_placeholders']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'human-and-ats-review',
            'title' => 'Human and ATS review',
            'summary' => 'Review the tailored draft for clarity, evidence, chronology, repetition, and keyword balance.',
            'why_it_matters' => 'A resume must work for both a human reader and basic parsing. This review improves clarity and relevance without turning the resume into a suspicious keyword list or hiding weak evidence behind jargon.',
            'unlocks_text' => 'Approving unlocks the final application version.',
            'before_you_begin' => 'Read the assembled resume once as a hiring manager and once as a parser would. Keep the posting, match map, and facts-to-preserve nearby.',
            'common_problems' => 'Reviewers may focus only on keywords or only on style. The useful review checks evidence, chronology, repeated wording, unsupported claims, and readability together.',
            'recovery_guidance' => 'If the review asks for unsupported additions, reject those items and ask for evidence-based alternatives. If keyword density looks forced, replace repeated terms with plain language tied to real bullets.',
            'est_minutes' => 11,
            'prompt_template' => <<<TXT
You are reviewing a tailored resume for human clarity and basic ATS readability. Fact rule: {$factRule}

Target role: {{target_role}}
Preferred resume length: {{preferred_resume_length}}
Facts to preserve:
{{facts_to_preserve}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved role analysis:
{{artifact:parse-target-role}}

Approved match map:
{{artifact:build-truthful-match-map}}

Approved tailored resume:
{{artifact:assemble-tailored-resume}}

Produce Markdown with these exact headings:

## Review findings
Assess clarity, relevance, chronology, repeated wording, missing evidence, suspicious keyword density, and format readability.

## Evidence-based edits
Recommend edits that preserve facts and improve alignment or readability.

## Risk flags to resolve
List possible overclaims, missing verification, chronology issues, formatting issues, or unsupported keywords.

## Revised resume after review
Provide a tightened version of the resume only using approved facts.

Keep the headings in order. Teach alignment, readability, and evidence, not fear-based keyword stuffing.
TXT,
            'example_response' => <<<'MD'
## Review findings
- Clarity: The draft is easy to scan and uses conventional section headings.
- Relevance: Spreadsheet cleanup, weekly updates, tracking, and communication are visible near the top.
- Chronology: Dates are consistent with the supplied resume, but the SQL course year is still a placeholder.
- Repeated wording: "Status" appears several times but not excessively; "spreadsheet" is frequent because it is central evidence.
- Missing evidence: No proof is supplied for transportation, logistics, dispatch, ticketing systems, or advanced SQL.
- Suspicious keyword density: The draft does not repeat the posting as a keyword block.
- Format readability: The draft uses plain headings and bullets, with no tables or graphics.

## Evidence-based edits
- Keep SQL under "Coursework and exposure" to avoid overclaiming.
- Shorten the summary slightly if the one-page version needs space.
- Replace one "status" use with "updates" or "follow-up" for more natural wording.
- Keep unsupported preferred qualifications out of the skills section.

## Risk flags to resolve
- Verify the SQL course provider and year.
- Confirm that "weekly enrollment updates" is the most accurate phrase.
- Add metrics only if Maya can verify report counts, records tracked, or spreadsheet size.
- Make sure contact details are added outside SousMeow from Maya's current information.

## Revised resume after review
Maya Patel  
[City, State] | [Email] | [Phone] | [LinkedIn or portfolio if relevant]

Summary  
Operations support candidate with 2 years of administrative experience maintaining spreadsheets, preparing weekly updates, tracking paperwork, and communicating clearly with students, instructors, and program staff. Bringing practical Excel/Google Sheets experience and introductory SQL coursework to a junior operations analyst role.

Skills  
- Spreadsheets and reporting: Excel, Google Sheets, spreadsheet cleanup, weekly enrollment updates, tracking sheets
- Operations support: process tracking, paperwork follow-up, scheduling support, record organization
- Communication: email coordination, phone support, student and instructor questions, volunteer availability reminders
- Coursework and exposure: introductory SQL coursework with sample enrollment-data project

Experience  
Administrative Assistant, Northstar Community College Continuing Education | 2022-present
- Prepared weekly enrollment updates for a program coordinator, keeping spreadsheet information organized for recurring operations review.
- Updated enrollment spreadsheets and corrected incomplete or duplicate entries to improve the reliability of program records.
- Built a certificate paperwork tracker that helped staff see missing forms and follow up on open items.
- Answered student and instructor questions by email and phone, routing issues and updates to the appropriate program contact.

Volunteer Data Coordinator, City Food Share | 2021-2022
- Cleaned a volunteer sign-up spreadsheet by organizing duplicate rows and unclear availability notes.
- Sent weekly volunteer availability reminders to support predictable scheduling.

Education and Coursework  
- Introductory SQL course, [Provider or school], [Year if verified]
MD,
            'output_sections' => $sections([
                ['review_findings', 'Review findings'],
                ['evidence_based_edits', 'Evidence-based edits'],
                ['risk_flags_to_resolve', 'Risk flags to resolve'],
                ['revised_resume_after_review', 'Revised resume after review'],
            ]),
            'checks' => [
                ['label' => 'Review covers human and parser needs', 'help' => 'The findings discuss clarity, evidence, chronology, repetition, keywords, and format.', 'evidence_sections' => ['review_findings']],
                ['label' => 'Edits are evidence-based', 'help' => 'Recommendations do not ask for unsupported claims.', 'evidence_sections' => ['evidence_based_edits']],
                ['label' => 'Risks are visible before finalizing', 'help' => 'Verification and overclaim risks are named clearly.', 'evidence_sections' => ['risk_flags_to_resolve']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'finalize-application-version',
            'title' => 'Finalize application version',
            'summary' => 'Prepare the final resume, plain-text resume, and verification checklist.',
            'why_it_matters' => 'The final step packages a resume version that is ready to paste, attach, or convert after the user verifies facts. A plain-text version helps catch formatting problems and gives the candidate a clean backup for application portals.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your tailored resume kit.',
            'before_you_begin' => 'Confirm employer instructions, file type, naming convention, and all remaining placeholders. Add private contact details only where you intend to include them.',
            'common_problems' => 'Final drafts can reintroduce placeholders, unverified metrics, or formatting that breaks when pasted. Check facts and plain text carefully before sending.',
            'recovery_guidance' => 'If placeholders remain, keep them visible until the user fills them. If the plain-text version loses structure, simplify section headings and bullets.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are finalizing a tailored resume application version. Fact rule: {$factRule}

Target role: {{target_role}}
Preferred resume length: {{preferred_resume_length}}
Facts to preserve:
{{facts_to_preserve}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved reviewed resume:
{{artifact:human-and-ats-review}}

Produce Markdown with these exact headings:

## Final tailored resume
Provide the final resume version. Keep placeholders only where the candidate must fill real details.

## Plain-text resume
Provide a plain-text version suitable for portals, using simple headings and standard bullets or hyphens.

## Final fact-verification checklist
Provide a checklist for verifying names, dates, titles, metrics, skills, formatting, privacy, and employer instructions before applying.

Keep the headings in order. Do not invent facts to remove placeholders.
TXT,
            'example_response' => <<<'MD'
## Final tailored resume
Maya Patel  
[City, State] | [Email] | [Phone] | [LinkedIn or portfolio if relevant]

Summary  
Operations support candidate with 2 years of administrative experience maintaining spreadsheets, preparing weekly updates, tracking paperwork, and communicating clearly with students, instructors, and program staff. Bringing practical Excel/Google Sheets experience and introductory SQL coursework to a junior operations analyst role.

Skills  
- Spreadsheets and reporting: Excel, Google Sheets, spreadsheet cleanup, weekly enrollment updates, tracking sheets
- Operations support: process tracking, paperwork follow-up, scheduling support, record organization
- Communication: email coordination, phone support, student and instructor questions, volunteer availability reminders
- Coursework and exposure: introductory SQL coursework with sample enrollment-data project

Experience  
Administrative Assistant, Northstar Community College Continuing Education | 2022-present
- Prepared weekly enrollment updates for a program coordinator, keeping spreadsheet information organized for recurring operations review.
- Updated enrollment spreadsheets and corrected incomplete or duplicate entries to improve the reliability of program records.
- Built a certificate paperwork tracker that helped staff see missing forms and follow up on open items.
- Answered student and instructor questions by email and phone, routing issues and updates to the appropriate program contact.

Volunteer Data Coordinator, City Food Share | 2021-2022
- Cleaned a volunteer sign-up spreadsheet by organizing duplicate rows and unclear availability notes.
- Sent weekly volunteer availability reminders to support predictable scheduling.

Education and Coursework  
- Introductory SQL course, [Provider or school], [Year if verified]

## Plain-text resume
Maya Patel
[City, State] | [Email] | [Phone] | [LinkedIn or portfolio if relevant]

SUMMARY
Operations support candidate with 2 years of administrative experience maintaining spreadsheets, preparing weekly updates, tracking paperwork, and communicating clearly with students, instructors, and program staff. Bringing practical Excel/Google Sheets experience and introductory SQL coursework to a junior operations analyst role.

SKILLS
- Spreadsheets and reporting: Excel, Google Sheets, spreadsheet cleanup, weekly enrollment updates, tracking sheets
- Operations support: process tracking, paperwork follow-up, scheduling support, record organization
- Communication: email coordination, phone support, student and instructor questions, volunteer availability reminders
- Coursework and exposure: introductory SQL coursework with sample enrollment-data project

EXPERIENCE
Administrative Assistant, Northstar Community College Continuing Education | 2022-present
- Prepared weekly enrollment updates for a program coordinator, keeping spreadsheet information organized for recurring operations review.
- Updated enrollment spreadsheets and corrected incomplete or duplicate entries to improve the reliability of program records.
- Built a certificate paperwork tracker that helped staff see missing forms and follow up on open items.
- Answered student and instructor questions by email and phone, routing issues and updates to the appropriate program contact.

Volunteer Data Coordinator, City Food Share | 2021-2022
- Cleaned a volunteer sign-up spreadsheet by organizing duplicate rows and unclear availability notes.
- Sent weekly volunteer availability reminders to support predictable scheduling.

EDUCATION AND COURSEWORK
- Introductory SQL course, [Provider or school], [Year if verified]

## Final fact-verification checklist
- [ ] Contact details are current and intentionally included.
- [ ] Administrative Assistant title and 2022-present dates are accurate.
- [ ] City Food Share volunteer dates are accurate.
- [ ] SQL course provider and year are verified before brackets are removed.
- [ ] No transportation, logistics, dispatch, ticketing system, or SQL work experience is claimed.
- [ ] No metric is added unless Maya can verify it from records.
- [ ] Resume length and file type follow the employer's instructions.
- [ ] Plain-text version still reads clearly when pasted into a portal.
- [ ] Private details, references, and unnecessary identifying information are removed.
MD,
            'output_sections' => $sections([
                ['final_tailored_resume', 'Final tailored resume'],
                ['plain_text_resume', 'Plain-text resume'],
                ['final_fact_verification_checklist', 'Final fact-verification checklist'],
            ]),
            'checks' => [
                ['label' => 'Final resume is application-ready', 'help' => 'The final version is coherent and keeps only intentional placeholders.', 'evidence_sections' => ['final_tailored_resume']],
                ['label' => 'Plain text remains readable', 'help' => 'The portal version keeps clear headings and bullet structure.', 'evidence_sections' => ['plain_text_resume']],
                ['label' => 'Verification checklist protects facts', 'help' => 'The checklist catches dates, metrics, skills, privacy, and employer instructions.', 'evidence_sections' => ['final_fact_verification_checklist']],
            ],
        ]),
    ],
];
