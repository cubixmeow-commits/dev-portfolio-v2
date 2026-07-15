<?php

declare(strict_types=1);

/**
 * Write a Strong Cover Letter - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'write-a-strong-cover-letter',
    'title'               => 'Write a Strong Cover Letter',
    'tagline'             => "Connect real experience to the employer's needs in one letter.",
    'description'         => 'A strong cover letter does not repeat the resume in paragraph form. This Cookbook helps you identify employer needs, choose truthful evidence, form one central argument, draft the letter, remove generic language, and prepare short application messages. ' . sm_career_beginner_footer('an employer-needs brief, evidence selection, tailored cover letter, application note, follow-up email, and fact checklist', 6, 'about 55 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => [],
    'audience'            => 'Job seekers writing a tailored cover letter from real experience and a real posting',
    'outcome'             => 'employer-needs brief, evidence selection, opening-hook alternatives, tailored cover letter, short application note, follow-up email, and fact and tone checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 55,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 26,
    'stages' => [
        ['title' => 'Read the employer', 'summary' => 'Name the employer needs and the truthful evidence you can offer.'],
        ['title' => 'Shape the letter', 'summary' => 'Choose a central argument and draft a tailored letter.'],
        ['title' => 'Remove generic language', 'summary' => 'Cut resume repetition, filler, and unsupported claims.'],
        ['title' => 'Prepare messages', 'summary' => 'Pack a short application note, follow-up, and checklist.'],
    ],
    'fields' => [
        ['field_key' => 'target_role', 'label' => 'Target role', 'type' => 'text', 'help' => 'Use the exact role title if known.', 'placeholder' => 'e.g. Operations coordinator', 'sample_value' => 'Operations coordinator'],
        ['field_key' => 'target_industry', 'label' => 'Target industry', 'type' => 'text', 'help' => 'Name the industry or work context if known.', 'placeholder' => 'e.g. Community health nonprofit', 'sample_value' => 'Community health nonprofit'],
        ['field_key' => 'employer_name', 'label' => 'Employer name', 'type' => 'text', 'help' => 'Use the organization name. If unknown, write unknown.', 'placeholder' => 'e.g. Riverbend Health Access', 'sample_value' => 'Riverbend Health Access'],
        ['field_key' => 'job_description', 'label' => 'Job description', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste the posting or a concise summary of the role.'), 'placeholder' => 'Responsibilities, requirements, mission notes, and application instructions.', 'sample_value' => "Coordinate clinic volunteer schedules\nKeep intake records organized\nCommunicate with community partners\nPosting emphasizes calm follow-through and respect for clients"],
        ['field_key' => 'current_role', 'label' => 'Current or recent role', 'type' => 'text', 'help' => 'Use a truthful title, student status, freelance focus, or volunteer role.', 'placeholder' => 'e.g. Front desk assistant', 'sample_value' => 'Front desk assistant'],
        ['field_key' => 'years_experience', 'label' => 'Years of relevant experience', 'type' => 'text', 'help' => 'A truthful range or note is fine.', 'placeholder' => 'e.g. 2 years admin support', 'sample_value' => '2 years of administrative support plus volunteer scheduling experience'],
        ['field_key' => 'existing_resume', 'label' => 'Resume bullets or profile notes', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste only relevant bullets; remove contact details and private references.'), 'placeholder' => 'Selected resume bullets, not the whole document.', 'sample_value' => "Managed a front desk calendar for a tutoring center\nUpdated intake spreadsheets weekly\nCoordinated twelve volunteers for a weekend food pantry\nAnswered parent and partner questions by phone and email"],
        ['field_key' => 'strongest_achievements', 'label' => 'Strongest evidence', 'type' => 'textarea', 'help' => 'List real accomplishments, examples, or outcomes. Only include numbers you can verify.', 'placeholder' => 'e.g. Coordinated volunteer schedule...', 'sample_value' => "Reduced missed volunteer shifts by sending a weekly reminder checklist\nOrganized a messy intake spreadsheet into clear status columns\nHandled a difficult parent call and routed it to the right program lead"],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the letter should sound.', 'options' => ['Warm and direct', 'Clear and professional', 'Mission-aligned but modest', 'Concise and confident'], 'sample_value' => 'Clear and professional'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Name gaps, application instructions, or claims to avoid.', 'placeholder' => 'e.g. No healthcare background; keep under one page.', 'sample_value' => 'I do not have direct healthcare experience. Keep the letter under one page and avoid sounding dramatic about the mission.'],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'identify-employer-needs',
            'title' => 'Identify employer needs',
            'summary' => 'Translate the posting into the employer problems the letter should address.',
            'why_it_matters' => 'A cover letter should answer the employer needs behind the posting. This step keeps attention on the role instead of generic enthusiasm. It also records what is unknown so the letter does not pretend inside knowledge.',
            'unlocks_text' => 'Approving unlocks truthful evidence selection.',
            'before_you_begin' => 'Have the posting and application instructions ready. Remove private contact details before pasting. If the posting is short, mark gaps as unknown.',
            'common_problems' => 'The AI may invent mission details, company culture, or hidden priorities. It may also turn every bullet into a need. Keep only needs supported by the text.',
            'recovery_guidance' => 'If the output sounds like fake research, rerun and require a boundary list. If it is too long, ask for the top five needs only.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are analyzing a job posting for a cover letter. Fact rule: {$factRule}

Target role: {{target_role}}
Target industry: {{target_industry}}
Employer name: {{employer_name}}
Job description:
{{job_description}}
Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Employer-needs brief
Two or three sentences summarizing what the employer appears to need, using only supplied facts.

## Must-address points
Five bullets the cover letter should address.

## Useful language from the posting
Phrases or concepts worth echoing without copying whole sentences.

## Unknowns not to invent
Facts about the employer, team, mission, or process that were not supplied.

Keep the headings in order.
TXT,
            'example_response' => <<<'MD'
## Employer-needs brief
Riverbend Health Access appears to need an operations coordinator who can keep volunteer schedules, intake records, and partner communication organized. The posting emphasizes calm follow-through and respectful communication with clients.

## Must-address points
- Scheduling volunteers reliably.
- Keeping intake records organized.
- Communicating clearly with community partners.
- Handling sensitive questions respectfully.
- Learning the health nonprofit context without overstating experience.

## Useful language from the posting
- Calm follow-through.
- Respect for clients.
- Community partners.
- Intake records.

## Unknowns not to invent
Do not invent clinic size, client demographics, software tools, reporting structure, or specific health programs.
MD,
            'output_sections' => $sections([
                ['employer_needs_brief', 'Employer-needs brief'],
                ['must_address_points', 'Must-address points'],
                ['useful_language_from_the_posting', 'Useful language from the posting'],
                ['unknowns_not_to_invent', 'Unknowns not to invent'],
            ]),
            'checks' => [
                ['label' => 'Needs come from the posting', 'help' => 'The brief does not invent employer priorities.', 'evidence_sections' => ['employer_needs_brief', 'unknowns_not_to_invent']],
                ['label' => 'Points are letter-ready', 'help' => 'Must-address points can guide paragraphs.', 'evidence_sections' => ['must_address_points']],
                ['label' => 'Posting language is not copied wholesale', 'help' => 'The language list captures concepts, not plagiarized sentences.', 'evidence_sections' => ['useful_language_from_the_posting']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'select-truthful-evidence',
            'title' => 'Select truthful evidence',
            'summary' => 'Pick the resume facts and stories that prove fit without repeating the whole resume.',
            'why_it_matters' => 'The letter needs a few strong proof points, not every job duty. This step chooses evidence that speaks to employer needs. It protects against inflated numbers and unsupported claims.',
            'unlocks_text' => 'Approving unlocks the central argument and opening hooks.',
            'before_you_begin' => 'Review your selected resume bullets and strongest achievements. Keep exact metrics only if you can verify them. Mark any missing proof as a gap.',
            'common_problems' => 'A common mistake is rewriting the resume in prose. Another is using mission language as a substitute for evidence. The evidence should show action and fit.',
            'recovery_guidance' => 'If the evidence list is too broad, ask for three proof points only. If it invents outcomes, replace them with a qualitative result or a gap note.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are selecting evidence for a cover letter. Fact rule: {$factRule}

Current role: {{current_role}}
Years of experience: {{years_experience}}
Resume notes:
{{existing_resume}}
Strongest evidence:
{{strongest_achievements}}

Approved employer-needs brief:
{{artifact:identify-employer-needs}}

Produce Markdown with these exact headings:

## Evidence selection
Choose three to five evidence points. For each, name the employer need it supports and the exact fact to use.

## Resume details to avoid repeating
Resume facts that should not be repeated unless they support the letter argument.

## Evidence gaps
Missing details to verify instead of inventing.

## Best proof combination
One short paragraph naming the strongest two or three proof points to use together.

Keep the headings in order. Do not create new achievements.
TXT,
            'example_response' => <<<'MD'
## Evidence selection
1. Volunteer scheduling: supports reliable schedule coordination; use the food pantry volunteer schedule example.
2. Intake spreadsheet cleanup: supports organized records; use the clear status columns example.
3. Parent and partner communication: supports respectful communication; use phone and email support without inventing outcomes.
4. Weekly reminder checklist: supports follow-through; say it reduced missed volunteer shifts only if that is verified from your records.

## Resume details to avoid repeating
Do not list every front desk task. Do not repeat job dates, all software tools, or the whole volunteer role description unless needed for proof.

## Evidence gaps
Verify whether "reduced missed shifts" can be stated as a result. Verify dates for the food pantry example if the application asks for them.

## Best proof combination
Use scheduling, intake organization, and partner communication together. They directly answer the role without pretending direct healthcare experience.
MD,
            'output_sections' => $sections([
                ['evidence_selection', 'Evidence selection'],
                ['resume_details_to_avoid_repeating', 'Resume details to avoid repeating'],
                ['evidence_gaps', 'Evidence gaps'],
                ['best_proof_combination', 'Best proof combination'],
            ]),
            'checks' => [
                ['label' => 'Evidence is truthful', 'help' => 'Every proof point comes from resume notes or achievements.', 'evidence_sections' => ['evidence_selection']],
                ['label' => 'Not a resume repeat', 'help' => 'The avoid list prevents paragraph-form resume copying.', 'evidence_sections' => ['resume_details_to_avoid_repeating']],
                ['label' => 'Gaps remain gaps', 'help' => 'Missing proof is not filled in by the AI.', 'evidence_sections' => ['evidence_gaps']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'choose-central-argument',
            'title' => 'Choose central argument',
            'summary' => 'Create a one-sentence case for why your evidence fits this employer need.',
            'why_it_matters' => 'A focused argument keeps the letter from becoming a biography. It tells each paragraph what job it has. Opening options help you start without generic flattery.',
            'unlocks_text' => 'Approving unlocks the full cover letter draft.',
            'before_you_begin' => 'Read the employer-needs brief and evidence selection together. Decide what you can honestly claim in one sentence. Keep enthusiasm tied to the role, not imagined culture.',
            'common_problems' => 'Central arguments often overreach with "perfect fit" language or repeat the job title without proof. Openings may also sound copied from templates. Keep them specific and modest.',
            'recovery_guidance' => 'If the argument sounds inflated, ask for a narrower claim. If openings feel generic, require each one to mention a real employer need and a real proof point.',
            'est_minutes' => 7,
            'prompt_template' => <<<TXT
You are shaping the central argument for a cover letter. Fact rule: {$factRule}

Target role: {{target_role}}
Employer name: {{employer_name}}
Preferred tone: {{preferred_tone}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved employer-needs brief:
{{artifact:identify-employer-needs}}

Approved evidence selection:
{{artifact:select-truthful-evidence}}

Produce Markdown with these exact headings:

## Central argument
One sentence that connects the candidate's real evidence to the employer's needs.

## Opening-hook alternatives
Three opening options, each grounded in a need and proof point. No generic "I am excited to apply" opening by itself.

## Letter structure
A three-paragraph plan: opening, evidence paragraph, closing.

## Claims to avoid
Unsupported claims, exaggerated fit language, or resume repetition to keep out.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Central argument
My strongest fit for Riverbend's operations coordinator role is organized follow-through: I have coordinated volunteers, cleaned up intake records, and communicated calmly with families and partners.

## Opening-hook alternatives
1. Riverbend's need for calm coordination matches the work I have done keeping volunteer schedules and intake records organized.
2. I would bring practical front desk and volunteer coordination experience to a role centered on follow-through and respectful communication.
3. The operations coordinator posting stood out because it asks for the same habits I have practiced: clear records, reliable scheduling, and careful communication.

## Letter structure
Opening: connect Riverbend's coordination needs to the central argument. Evidence paragraph: use volunteer scheduling, intake spreadsheet cleanup, and partner communication. Closing: name the healthcare-context gap honestly and express interest in supporting organized client service.

## Claims to avoid
Avoid "perfect fit," direct healthcare experience, unverified missed-shift reduction, and a paragraph that repeats every resume bullet.
MD,
            'output_sections' => $sections([
                ['central_argument', 'Central argument'],
                ['opening_hook_alternatives', 'Opening-hook alternatives'],
                ['letter_structure', 'Letter structure'],
                ['claims_to_avoid', 'Claims to avoid'],
            ]),
            'checks' => [
                ['label' => 'Argument is one sentence', 'help' => 'The claim is focused enough to guide the letter.', 'evidence_sections' => ['central_argument']],
                ['label' => 'Openings use evidence', 'help' => 'Each hook connects a need to proof.', 'evidence_sections' => ['opening_hook_alternatives']],
                ['label' => 'Boundaries are visible', 'help' => 'Claims to avoid prevent exaggeration.', 'evidence_sections' => ['claims_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'draft-cover-letter',
            'title' => 'Draft cover letter',
            'summary' => 'Draft a one-page letter that adds context instead of copying the resume.',
            'why_it_matters' => 'The draft is where evidence becomes a coherent case. A good cover letter adds why the evidence matters for this employer. It should not restate every bullet already on the resume.',
            'unlocks_text' => 'Approving unlocks the generic-language review.',
            'before_you_begin' => 'Choose one opening hook before drafting. Check application instructions for length or format. Use the resume as source material, not as a script.',
            'common_problems' => 'Drafts can become too long, too flattering, or too similar to the resume. They may also claim passion without evidence. Keep the letter specific, brief, and grounded.',
            'recovery_guidance' => 'If the draft repeats the resume, ask for more explanation of why the selected evidence matters. If it sounds inflated, ask for a plainer version in the selected tone.',
            'est_minutes' => 14,
            'prompt_template' => <<<TXT
You are drafting a tailored cover letter. Fact rule: {$factRule}

Target role: {{target_role}}
Employer name: {{employer_name}}
Preferred tone: {{preferred_tone}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved central argument:
{{artifact:choose-central-argument}}

Approved evidence selection:
{{artifact:select-truthful-evidence}}

Produce Markdown with these exact headings:

## Tailored cover letter
A complete cover letter under one page. It must add context and fit, not merely repeat the resume. Include greeting only if a recipient name was supplied; otherwise use a neutral greeting.

## Why it is not a resume repeat
Three bullets explaining what the letter adds beyond the resume.

## Facts to verify before sending
Any names, details, metrics, or application instructions the candidate should verify.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Tailored cover letter
Dear Hiring Team,

I am applying for the Operations Coordinator role at Riverbend Health Access because the posting centers on the kind of organized follow-through I have practiced in front desk and volunteer coordination work.

In my current administrative support role, I manage calendar details, update intake spreadsheets, and answer questions from families and partners. In volunteer work with a weekend food pantry, I coordinated schedules for twelve volunteers and used a weekly reminder checklist to keep shifts visible. That experience taught me that calm communication and clear records can prevent small misses from becoming service problems.

I do not yet have direct healthcare experience, but I have worked in settings where people needed respectful answers and reliable follow-up. I would be glad to bring that coordination habit to Riverbend's clinic operations and learn the health nonprofit context carefully.

Thank you for considering my application.

## Why it is not a resume repeat
- It connects selected evidence to Riverbend's stated coordination needs.
- It explains the working habit behind the resume bullets: organized follow-through.
- It names the healthcare gap honestly instead of hiding it.

## Facts to verify before sending
- Whether there is a specific hiring manager name.
- Whether the weekly reminder checklist result can be quantified.
- Any application length or attachment instructions.
MD,
            'output_sections' => $sections([
                ['tailored_cover_letter', 'Tailored cover letter'],
                ['why_it_is_not_a_resume_repeat', 'Why it is not a resume repeat'],
                ['facts_to_verify_before_sending', 'Facts to verify before sending'],
            ]),
            'checks' => [
                ['label' => 'Letter is tailored', 'help' => 'The role and employer needs are visible.', 'evidence_sections' => ['tailored_cover_letter']],
                ['label' => 'Not just the resume', 'help' => 'The letter adds context and a central argument.', 'evidence_sections' => ['why_it_is_not_a_resume_repeat']],
                ['label' => 'Facts are verified or flagged', 'help' => 'Unknown names and metrics are not invented.', 'evidence_sections' => ['facts_to_verify_before_sending']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'remove-generic-language',
            'title' => 'Remove generic language',
            'summary' => 'Revise the letter for specificity, honest tone, and one-page clarity.',
            'why_it_matters' => 'Generic language makes a truthful letter sound interchangeable. This step cuts filler and checks for overclaiming. It keeps the tone professional without making the candidate sound like a different person.',
            'unlocks_text' => 'Approving unlocks application notes and follow-up messages.',
            'before_you_begin' => 'Read the draft once for facts and once for tone. Keep the job posting nearby. Mark any sentence that could apply to any employer.',
            'common_problems' => 'Common issues include "passionate about your mission," "uniquely qualified," and long resume summaries. The revision should remove claims that are unsupported or too broad.',
            'recovery_guidance' => 'If the revision becomes bland, restore one specific proof point. If it becomes too salesy, ask for the same evidence in plainer language.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are revising a cover letter to remove generic language. Fact rule: {$factRule}

Preferred tone: {{preferred_tone}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved draft:
{{artifact:draft-cover-letter}}

Approved employer-needs brief:
{{artifact:identify-employer-needs}}

Produce Markdown with these exact headings:

## Revised cover letter
The tightened letter, preserving truthful evidence and the selected tone.

## Generic language removed
Bullets naming filler, exaggeration, or resume repetition that was cut or changed.

## Fact and tone checklist
Six checkbox items the candidate can verify before sending.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Revised cover letter
Dear Hiring Team,

I am applying for the Operations Coordinator role at Riverbend Health Access because the posting calls for organized follow-through with volunteer schedules, intake records, and partner communication.

In administrative support work, I have managed calendar details, updated intake spreadsheets, and answered questions from families and partners. In volunteer work with a weekend food pantry, I coordinated schedules for twelve volunteers and used a weekly reminder checklist to keep shifts visible. Those examples fit the practical coordination Riverbend describes: clear records, timely communication, and calm follow-up.

I do not yet have direct healthcare experience, so I would approach the role by learning Riverbend's client and clinic context carefully. I would be glad to bring my scheduling, record-keeping, and communication habits to the team.

Thank you for considering my application.

## Generic language removed
- Cut broad "mission passion" language and tied interest to role tasks.
- Removed "perfect fit" wording.
- Kept only selected evidence instead of summarizing every resume bullet.

## Fact and tone checklist
- [ ] Employer name is spelled correctly.
- [ ] No direct healthcare experience is claimed.
- [ ] Any number used is verified.
- [ ] The letter adds context beyond the resume.
- [ ] Tone matches clear and professional.
- [ ] Application instructions are followed.
MD,
            'output_sections' => $sections([
                ['revised_cover_letter', 'Revised cover letter'],
                ['generic_language_removed', 'Generic language removed'],
                ['fact_and_tone_checklist', 'Fact and tone checklist'],
            ]),
            'checks' => [
                ['label' => 'Generic wording is cut', 'help' => 'The removed list names concrete edits.', 'evidence_sections' => ['generic_language_removed']],
                ['label' => 'Letter still has proof', 'help' => 'The revision keeps truthful evidence, not just tone.', 'evidence_sections' => ['revised_cover_letter']],
                ['label' => 'Checklist is send-ready', 'help' => 'Checklist items can be verified before applying.', 'evidence_sections' => ['fact_and_tone_checklist']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'prepare-application-messages',
            'title' => 'Prepare application messages',
            'summary' => 'Create a short application note, follow-up email, and final send checklist.',
            'why_it_matters' => 'Applications often need small messages around the main letter. Preparing them now keeps the tone consistent and prevents last-minute generic notes. Follow-up drafts should never invent silence, timelines, or recruiter promises.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your cover letter kit.',
            'before_you_begin' => 'Check whether the application portal allows a note or email. Confirm names, submission date, and attachments outside SousMeow. Leave follow-up timing flexible if no timeline is supplied.',
            'common_problems' => 'The AI may invent a referral, application date, or hiring timeline. It can also make the follow-up sound impatient. Keep messages short and factual.',
            'recovery_guidance' => 'If a message invents details, replace them with brackets for the user to fill later. If it sounds too pushy, ask for a warmer concise version.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are preparing short application messages. Fact rule: {$factRule}

Target role: {{target_role}}
Employer name: {{employer_name}}
Preferred tone: {{preferred_tone}}
Constraints or concerns:
{{constraints_or_concerns}}

Approved revised cover letter:
{{artifact:remove-generic-language}}

Produce Markdown with these exact headings:

## Short application note
A brief note for an application portal or email body.

## Follow-up email
A polite follow-up template that uses bracketed fields for date or real prior contact. Do not invent a timeline.

## Final send checklist
Five to seven checks for attachments, facts, tone, and privacy.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Short application note
Hello,

I am submitting my application for the Operations Coordinator role at Riverbend Health Access. My background includes administrative support, volunteer scheduling, intake record organization, and careful communication with families and partners. Thank you for reviewing my materials.

## Follow-up email
Subject: Following up on Operations Coordinator application

Hello,

I applied for the Operations Coordinator role on [application date] and wanted to follow up briefly. I remain interested in the opportunity to support Riverbend's scheduling, intake record, and partner communication work.

Please let me know if I can provide anything else.

Thank you,
[Your name]

## Final send checklist
- [ ] Cover letter uses the correct employer and role name.
- [ ] Resume and letter do not expose unnecessary private details.
- [ ] No unverified metric or healthcare experience claim appears.
- [ ] Attachments are named clearly.
- [ ] Application instructions are followed.
- [ ] Follow-up placeholders are filled only with real dates or contacts.
MD,
            'output_sections' => $sections([
                ['short_application_note', 'Short application note'],
                ['follow_up_email', 'Follow-up email'],
                ['final_send_checklist', 'Final send checklist'],
            ]),
            'checks' => [
                ['label' => 'Application note is short', 'help' => 'The note can fit in a portal or email body.', 'evidence_sections' => ['short_application_note']],
                ['label' => 'Follow-up invents nothing', 'help' => 'Dates and contacts remain bracketed until real.', 'evidence_sections' => ['follow_up_email']],
                ['label' => 'Privacy is checked', 'help' => 'Checklist includes private detail review before sending.', 'evidence_sections' => ['final_send_checklist']],
            ],
        ]),
    ],
];
