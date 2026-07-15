<?php

declare(strict_types=1);

/**
 * Prepare for an Interview - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'prep-for-an-interview',
    'title'               => 'Prepare for an Interview',
    'tagline'             => 'Build STAR stories and run a one-question mock interview.',
    'description'         => 'Interview prep works best when it is grounded in the role, your real evidence, and short spoken practice. This Cookbook turns a job description and truthful achievements into criteria, STAR stories, predicted questions, answer frameworks, a one-question mock interview protocol, improvement notes, employer questions, and follow-up messages. ' . sm_career_beginner_footer('an interview criteria brief, STAR story bank, mock interview prompt, answer improvement notes, employer question list, and thank-you email', 7, 'about 75 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Job seekers, freelancers, students, and career changers preparing for a real interview without inventing experience',
    'outcome'             => 'interview criteria brief, STAR story bank, predicted question set, answer preparation sheet, mock interview prompt, answer improvement notes, employer question list, and thank-you email',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 75,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 19,
    'stages' => [
        ['title' => 'Read the role', 'summary' => 'Translate the job facts into honest evaluation criteria.'],
        ['title' => 'Choose evidence', 'summary' => 'Build STAR stories and predict likely interview questions.'],
        ['title' => 'Practice aloud', 'summary' => 'Prepare answer frameworks and run a one-question mock interview protocol.'],
        ['title' => 'Pack the follow-up', 'summary' => 'Review answer quality and finish final-day notes.'],
    ],
    'fields' => [
        [
            'field_key'    => 'target_role',
            'label'        => 'Target role',
            'type'         => 'text',
            'help'         => 'Use the exact title if you have it.',
            'placeholder'  => 'e.g. Customer success manager',
            'sample_value' => 'Customer success manager',
        ],
        [
            'field_key'    => 'target_industry',
            'label'        => 'Target industry',
            'type'         => 'text',
            'help'         => 'Name the industry, customer type, or context if known. If unknown, say unknown.',
            'placeholder'  => 'e.g. B2B education software',
            'sample_value' => 'B2B education software',
        ],
        [
            'field_key'    => 'job_description',
            'label'        => 'Job description or interview brief',
            'type'         => 'textarea',
            'help'         => sm_career_privacy_pantry_help('Paste only the role details needed for interview prep.'),
            'placeholder'  => "Responsibilities, requirements, interview format, and employer notes you actually have.",
            'sample_value' => "Own onboarding for school district accounts\nExplain product workflows to non-technical users\nTrack customer health and escalate risks\nInterview is a 45-minute video call with the hiring manager",
        ],
        [
            'field_key'    => 'current_role',
            'label'        => 'Current or most recent role',
            'type'         => 'text',
            'help'         => 'Use your real role, student status, freelance focus, or volunteer role.',
            'placeholder'  => 'e.g. Support specialist',
            'sample_value' => 'Support specialist',
        ],
        [
            'field_key'    => 'years_experience',
            'label'        => 'Years of relevant experience',
            'type'         => 'text',
            'help'         => 'Use a truthful range or short explanation. Do not round up if it changes the meaning.',
            'placeholder'  => 'e.g. 3 years in support, 1 year onboarding users',
            'sample_value' => '3 years in support, including 1 year onboarding small business customers',
        ],
        [
            'field_key'    => 'strongest_achievements',
            'label'        => 'Strongest achievements and stories',
            'type'         => 'textarea',
            'help'         => 'List real projects, saves, improvements, feedback, or responsibilities. Metrics are optional; only include numbers you can verify.',
            'placeholder'  => "Resolved recurring billing confusion by writing a help guide\nTrained new support teammate...",
            'sample_value' => "Created a new onboarding checklist after customers missed setup steps\nHandled escalations for a regional nonprofit account and kept renewal conversation on track\nTrained two new support teammates on ticket triage\nBuilt a simple spreadsheet to spot accounts with repeated setup questions",
        ],
        [
            'field_key'    => 'existing_resume',
            'label'        => 'Resume or profile notes (optional)',
            'type'         => 'textarea',
            'help'         => sm_career_privacy_pantry_help('Use this only for career facts that help interview prep; redact contact details before pasting.'),
            'placeholder'  => 'Paste selected bullets or profile notes, not your full contact block.',
            'sample_value' => "Support Specialist, LumaDesk, 2021-present\nCustomer Success Certificate, 2024\nVolunteer tech help desk for neighborhood library, 2022-2023",
            'required'     => 0,
        ],
        [
            'field_key'    => 'preferred_tone',
            'label'        => 'Preferred answer tone',
            'type'         => 'select',
            'help'         => 'Choose the speaking style you want the interview answers to practice.',
            'options'      => ['Warm and clear', 'Calm and concise', 'Confident but modest', 'Formal and careful'],
            'sample_value' => 'Calm and concise',
        ],
        [
            'field_key'    => 'constraints_or_concerns',
            'label'        => 'Constraints or concerns',
            'type'         => 'textarea',
            'help'         => 'Name gaps, nerves, schedule constraints, or questions you fear. The prompts will not hide real gaps.',
            'placeholder'  => 'e.g. Concerned about not having the exact industry background.',
            'sample_value' => 'I have not worked directly in education software. I tend to ramble when asked about conflict.',
        ],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position'    => 1,
            'slug'              => 'analyze-evaluation-criteria',
            'title'             => 'Analyze evaluation criteria',
            'summary'           => 'Turn the job description into a brief of likely skills, proof needs, and unknowns to avoid.',
            'why_it_matters'    => 'Interview answers land better when they answer the role instead of reciting a generic pitch. This step separates supplied facts from reasonable preparation targets. It also makes missing information visible before the story work begins.',
            'unlocks_text'      => 'Approving unlocks the STAR story bank and predicted question set.',
            'before_you_begin'  => 'Have the job description, interview invitation, and any recruiter notes nearby. Remove private contact information before pasting. If the role details are thin, say what is unknown instead of filling it in.',
            'common_problems'   => 'The AI may sound too certain about employer culture or hidden hiring criteria. It may also over-weight keywords that appear once. Keep only criteria supported by the role facts.',
            'recovery_guidance' => 'If the brief invents employer facts, rerun the prompt and ask it to move unsupported ideas to Unknowns. If the criteria feel too broad, add more posting details or ask for the top five signals only.',
            'est_minutes'       => 10,
            'prompt_template'   => <<<TXT
You are an interview preparation coach. Fact rule: {$factRule}

Target role: {{target_role}}
Target industry: {{target_industry}}
Job description or interview brief:
{{job_description}}
Current role: {{current_role}}
Years of relevant experience: {{years_experience}}
Concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Role snapshot
Two or three sentences describing the opportunity using only supplied facts.

## Likely evaluation criteria
Five to seven bullets naming skills, behaviors, or evidence areas the interviewer may evaluate. Mark inferred items as inferred.

## Evidence to prepare
Three to five evidence types the candidate should be ready to discuss.

## Unknowns and boundaries
Facts not supplied that should not be claimed or guessed.

## Preparation focus
One short paragraph naming the highest-leverage prep focus for this interview.

Keep the headings in order. Do not wrap the response in a code fence.
TXT,
            'example_response'  => <<<'MD'
## Role snapshot
The target role is Customer Success Manager in B2B education software. The supplied brief emphasizes onboarding school district accounts, explaining workflows to non-technical users, tracking account health, and escalating risks during a 45-minute video call.

## Likely evaluation criteria
- Can explain product steps clearly to non-technical customers.
- Can organize onboarding work and prevent missed setup steps.
- Can notice account risk and escalate calmly.
- Can connect support experience to customer success work.
- Inferred: can speak with school district stakeholders without pretending prior district experience.

## Evidence to prepare
- Onboarding checklist story.
- Regional nonprofit escalation story.
- Training two support teammates.
- Spreadsheet for repeated setup questions.

## Unknowns and boundaries
Do not claim education software experience. Do not assume the manager's priorities beyond the brief. Do not invent customer retention metrics, renewal outcomes, or product tools.

## Preparation focus
Prepare concise stories showing customer communication, onboarding structure, and risk escalation while naming the education-software gap honestly.
MD,
            'output_sections'   => $sections([
                ['role_snapshot', 'Role snapshot'],
                ['likely_evaluation_criteria', 'Likely evaluation criteria'],
                ['evidence_to_prepare', 'Evidence to prepare'],
                ['unknowns_and_boundaries', 'Unknowns and boundaries'],
                ['preparation_focus', 'Preparation focus'],
            ]),
            'checks' => [
                ['label' => 'Criteria are grounded', 'help' => 'Every criterion connects to the posting or is clearly marked inferred.', 'evidence_sections' => ['likely_evaluation_criteria']],
                ['label' => 'No employer fiction', 'help' => 'The brief does not invent culture, interviewers, software stack, or hidden priorities.', 'evidence_sections' => ['role_snapshot', 'unknowns_and_boundaries']],
                ['label' => 'Prep focus is usable', 'help' => 'The focus points to specific stories or skills to prepare next.', 'evidence_sections' => ['preparation_focus']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 2,
            'slug'              => 'build-star-story-bank',
            'title'             => 'Build STAR story bank',
            'summary'           => 'Choose true stories and map each one into situation, task, action, result, and evidence gaps.',
            'why_it_matters'    => 'A small bank of truthful stories is easier to remember than a pile of scripted answers. STAR structure keeps context short and action visible. This step prevents inflated metrics by naming missing proof instead of inventing it.',
            'unlocks_text'      => 'Approving unlocks predicted interview questions.',
            'before_you_begin'  => 'Review your achievements and resume notes. Keep only stories you can discuss honestly under your own name. If a metric is missing, leave it missing.',
            'common_problems'   => 'Common issues include turning team work into solo ownership, adding unverified numbers, or choosing only impressive stories that do not fit the role. Another risk is making every story too long to say aloud.',
            'recovery_guidance' => 'If a story overclaims, rewrite it with your exact action and the known result or learning. If the bank feels thin, add smaller real examples rather than asking the AI to make examples up.',
            'est_minutes'       => 12,
            'prompt_template'   => <<<TXT
You are helping build an interview STAR story bank. Fact rule: {$factRule}

Strongest achievements and stories:
{{strongest_achievements}}
Existing resume or profile notes:
{{existing_resume}}
Preferred tone: {{preferred_tone}}

Approved criteria brief:
{{artifact:analyze-evaluation-criteria}}

Produce Markdown with these exact headings:

## Story bank
Four to six numbered stories. For each, include Situation, Task, Action, Result or Learning. Use only supplied facts.

## Best-fit criteria
Map each story to one or two criteria from the approved brief.

## Evidence gaps
List missing dates, metrics, names, or details the candidate should verify. Do not fill them in.

## Claims to avoid
Unsupported claims the candidate should not say.

Keep the headings in order and use a spoken, practical tone.
TXT,
            'example_response'  => <<<'MD'
## Story bank
1. Onboarding checklist: Situation: customers were missing setup steps. Task: make onboarding easier to follow. Action: created a checklist from repeated support questions. Result or Learning: customers had a clearer path, and I learned to turn support patterns into proactive guidance.
2. Nonprofit escalation: Situation: a regional nonprofit account had repeated confusion before renewal. Task: keep the conversation organized and calm. Action: summarized open issues, escalated the risk, and tracked follow-up. Result or Learning: the renewal conversation stayed on track without hiding the risk.
3. Teammate training: Situation: two new teammates needed ticket triage support. Task: help them handle early queues. Action: walked them through examples and decision rules. Result or Learning: I practiced explaining process clearly.
4. Setup-question spreadsheet: Situation: similar setup questions kept appearing. Task: spot patterns. Action: made a simple spreadsheet of repeated questions. Result or Learning: it helped me see which onboarding topics needed clearer guidance.

## Best-fit criteria
- Onboarding checklist: organizing onboarding work; explaining workflows.
- Nonprofit escalation: tracking account risk; escalating calmly.
- Teammate training: clear communication; process explanation.
- Setup-question spreadsheet: noticing patterns; proactive customer success thinking.

## Evidence gaps
- Exact dates for the checklist and spreadsheet.
- Whether any customer-facing guide was formally adopted.
- Any verified renewal or satisfaction outcome.

## Claims to avoid
Do not claim district experience, retention lift, customer health ownership, or formal management responsibility unless verified.
MD,
            'output_sections'   => $sections([
                ['story_bank', 'Story bank'],
                ['best_fit_criteria', 'Best-fit criteria'],
                ['evidence_gaps', 'Evidence gaps'],
                ['claims_to_avoid', 'Claims to avoid'],
            ]),
            'checks' => [
                ['label' => 'Stories are real', 'help' => 'Each story comes from the supplied achievements or resume notes.', 'evidence_sections' => ['story_bank']],
                ['label' => 'Actions are visible', 'help' => 'Every story names what the candidate personally did.', 'evidence_sections' => ['story_bank']],
                ['label' => 'Gaps are not invented', 'help' => 'Missing details are listed as gaps, not silently filled.', 'evidence_sections' => ['evidence_gaps', 'claims_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 2,
            'slug'              => 'predict-interview-questions',
            'title'             => 'Predict interview questions',
            'summary'           => 'Create a likely question set tied to the criteria and story bank.',
            'why_it_matters'    => 'Predicted questions help you practice patterns without memorizing every possible answer. This step keeps the question list tied to evidence and role facts. It also names questions you should answer with boundaries instead of bluffing.',
            'unlocks_text'      => 'Approving unlocks the answer preparation sheet.',
            'before_you_begin'  => 'Review the criteria brief and story bank. Add any recruiter-provided interview format details to the job description if you have them. Keep salary or benefits questions out unless the interview stage calls for them.',
            'common_problems'   => "Question lists often become generic or assume a panel, case study, or technical screen not in the invite. They can also dodge the candidate's real concerns. Keep the set realistic and useful for this interview.",
            'recovery_guidance' => 'If the questions are generic, rerun with the criteria and ask for questions that each test one named signal. If it invents interview logistics, move those assumptions to a separate unknowns list.',
            'est_minutes'       => 8,
            'prompt_template'   => <<<TXT
You are predicting practical interview questions for preparation. Fact rule: {$factRule}

Target role: {{target_role}}
Target industry: {{target_industry}}
Job description or interview brief:
{{job_description}}
Concerns:
{{constraints_or_concerns}}

Approved criteria brief:
{{artifact:analyze-evaluation-criteria}}

Approved STAR story bank:
{{artifact:build-star-story-bank}}

Produce Markdown with these exact headings:

## Predicted question set
Eight to ten likely questions. Include behavioral, role-fit, gap, and candidate-question prompts when supported by the facts.

## What each question tests
Map each question to the likely criterion or concern it tests.

## Best story to use
Suggest the best approved story for each major question type.

## Questions to handle carefully
Questions where the candidate should name a gap, uncertainty, or boundary instead of overclaiming.

Keep the headings in order. Do not invent interview rounds or employer facts.
TXT,
            'example_response'  => <<<'MD'
## Predicted question set
1. Tell me about your experience onboarding customers.
2. Describe a time you explained a workflow to someone non-technical.
3. Tell me about a customer risk you noticed and escalated.
4. How would you approach learning education software customers?
5. What support habits would help you in customer success?
6. Tell me about a time you improved a process.
7. How do you keep track of follow-up across several accounts?
8. What would you want to learn in your first month?
9. Tell me about a time you trained or coached someone.

## What each question tests
Questions 1, 2, and 6 test onboarding and communication. Question 3 tests risk escalation. Questions 4 and 8 test honest learning posture. Questions 5, 7, and 9 test transfer from support to customer success.

## Best story to use
Use the onboarding checklist for questions 1 and 6. Use the nonprofit escalation for question 3. Use teammate training for question 2 or 9. Use the setup-question spreadsheet for follow-up and pattern tracking.

## Questions to handle carefully
Handle education software questions carefully: name that the direct industry background is missing, then show how you learn customer context. Do not imply ownership of renewals or district relationships unless verified.
MD,
            'output_sections'   => $sections([
                ['predicted_question_set', 'Predicted question set'],
                ['what_each_question_tests', 'What each question tests'],
                ['best_story_to_use', 'Best story to use'],
                ['questions_to_handle_carefully', 'Questions to handle carefully'],
            ]),
            'checks' => [
                ['label' => 'Questions fit the role', 'help' => 'The set reflects the criteria brief and supplied interview facts.', 'evidence_sections' => ['predicted_question_set', 'what_each_question_tests']],
                ['label' => 'Stories are reusable', 'help' => 'Major question types point back to approved stories.', 'evidence_sections' => ['best_story_to_use']],
                ['label' => 'Gaps are handled honestly', 'help' => 'Careful questions identify where to avoid bluffing.', 'evidence_sections' => ['questions_to_handle_carefully']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 3,
            'slug'              => 'prepare-answer-frameworks',
            'title'             => 'Prepare answer frameworks',
            'summary'           => 'Draft concise answer structures for the predicted questions without scripting every word.',
            'why_it_matters'    => 'Frameworks give you a path to speak naturally while keeping the answer anchored. They help you avoid memorized language that collapses under follow-up questions. This step also prepares honest bridge lines for gaps.',
            'unlocks_text'      => 'Approving unlocks the one-question mock interview prompt.',
            'before_you_begin'  => 'Read the predicted questions out loud once. Decide which stories you are comfortable telling. Keep frameworks short enough to glance at before practice.',
            'common_problems'   => 'Answers may become cover-letter paragraphs instead of spoken frameworks. Another common issue is hiding a real gap with vague confidence. Keep the structure simple: answer, proof, result, bridge.',
            'recovery_guidance' => 'If a framework sounds too scripted, ask for bullet cues instead of full sentences. If it dodges a gap, add one honest sentence naming the gap and the evidence you do have.',
            'est_minutes'       => 14,
            'prompt_template'   => <<<TXT
You are preparing spoken interview answer frameworks. Fact rule: {$factRule}

Preferred tone: {{preferred_tone}}
Current role: {{current_role}}
Years of relevant experience: {{years_experience}}
Concerns:
{{constraints_or_concerns}}

Approved criteria brief:
{{artifact:analyze-evaluation-criteria}}

Approved STAR story bank:
{{artifact:build-star-story-bank}}

Approved predicted questions:
{{artifact:predict-interview-questions}}

Produce Markdown with these exact headings:

## Answer preparation sheet
Prepare frameworks for five priority questions. For each: direct answer, story cue, action proof, result or learning, and one follow-up detail.

## Gap bridge lines
Two or three honest lines for gaps or missing direct experience.

## Speaking cues
Short reminders for pacing, length, and tone.

## Questions for the employer
Four questions the candidate can ask the employer, based only on supplied facts and approved criteria.

Keep answers conversational. Do not write a memorized script longer than needed.
TXT,
            'example_response'  => <<<'MD'
## Answer preparation sheet
1. Customer onboarding: Direct answer: I have built practical onboarding support from repeated customer questions. Story cue: checklist. Action proof: turned missed setup steps into a clearer sequence. Result or learning: proactive guidance prevents confusion. Follow-up detail: which questions repeated most.
2. Explaining workflows: Direct answer: I explain by using examples and decision rules. Story cue: teammate training. Action proof: walked two new teammates through triage examples. Result or learning: process is easier to follow when the rule is visible.
3. Escalating risk: Direct answer: I document the issue, summarize the risk, and escalate without drama. Story cue: nonprofit account. Action proof: tracked open issues and follow-up. Result or learning: calm structure kept the conversation moving.
4. Education software gap: Direct answer: I have not worked directly in education software yet. Story cue: learning customer context in support. Action proof: used repeated questions to learn user needs. Result or learning: I learn fast by listening for patterns.
5. Process improvement: Direct answer: I look for repeated friction. Story cue: setup-question spreadsheet. Action proof: grouped recurring issues. Result or learning: patterns can become better onboarding material.

## Gap bridge lines
- I have not worked directly with school districts yet, so I would start by learning the customer language and success measures.
- My background is support, and the transfer I can prove is clear communication, follow-through, and risk visibility.

## Speaking cues
Keep each answer near 60 to 90 seconds. Lead with the direct answer before the story. Use "I" only for actions you personally took.

## Questions for the employer
1. What does successful onboarding look like for a new district account?
2. What customer risks should this role notice early?
3. How does the team share customer context with new CSMs?
4. What would you want the person in this role to learn first?
MD,
            'output_sections'   => $sections([
                ['answer_preparation_sheet', 'Answer preparation sheet'],
                ['gap_bridge_lines', 'Gap bridge lines'],
                ['speaking_cues', 'Speaking cues'],
                ['questions_for_the_employer', 'Questions for the employer'],
            ]),
            'checks' => [
                ['label' => 'Frameworks are spoken', 'help' => 'The sheet gives cues, not overlong memorized essays.', 'evidence_sections' => ['answer_preparation_sheet', 'speaking_cues']],
                ['label' => 'Gaps are honest', 'help' => 'Bridge lines name missing direct experience without apologizing or bluffing.', 'evidence_sections' => ['gap_bridge_lines']],
                ['label' => 'Employer questions are useful', 'help' => 'Questions ask about work, success, risk, or learning.', 'evidence_sections' => ['questions_for_the_employer']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 3,
            'slug'              => 'run-mock-interview',
            'title'             => 'Run mock interview',
            'summary'           => 'Create a ready-to-use conductor prompt for one external-AI mock interview question.',
            'why_it_matters'    => 'Mock practice should make you answer aloud, not watch the AI perform both sides. This step creates the protocol you paste into one external AI conversation so it asks one question, waits, gives brief feedback, and optionally asks one follow-up. SousMeow stores the conductor prompt and your later notes; it does not run live chat inside the step.',
            'unlocks_text'      => 'Approving unlocks answer-quality review and improvement notes.',
            'before_you_begin'  => 'Plan to use one separate external AI conversation for the mock interview. Copy the conductor prompt from this artifact, paste it into that chat, answer as yourself, and paste any useful feedback notes back into SousMeow if you want them reviewed later.',
            'common_problems'   => 'The AI may try to answer as the candidate, ask too many questions, or create a fake transcript. The protocol must prevent that. Keep the mock to one question plus an optional follow-up so the practice stays focused.',
            'recovery_guidance' => 'If the conductor prompt asks multiple questions at once or writes candidate answers, rerun and emphasize that the external AI must wait for the human response. If you already practiced, add your real notes below the artifact instead of inventing a transcript.',
            'est_minutes'       => 10,
            'prompt_template'   => <<<TXT
You are creating a mock interview conductor prompt for the user to paste into a separate external AI chat. Fact rule: {$factRule}

Important: Produce the conductor prompt and how-to-run instructions only. Do not conduct the interview here. Do not answer as the candidate. Do not create a fake transcript.

Target role: {{target_role}}
Target industry: {{target_industry}}
Preferred tone: {{preferred_tone}}

Approved criteria brief:
{{artifact:analyze-evaluation-criteria}}

Approved answer preparation sheet:
{{artifact:prepare-answer-frameworks}}

Produce Markdown with these exact headings:

## Mock interview conductor prompt
A paste-ready prompt addressed to an external AI. It must instruct that AI to ask ONE interview question, wait for the user's answer, give brief structured feedback, and optionally ask one follow-up question. It must explicitly say not to answer as the candidate.

## How to run it
Step-by-step instructions telling the user to keep the mock interview in one external AI conversation and paste only their own notes or the AI's feedback back into SousMeow.

## What to capture afterward
Short bullets for notes the user should save after practice.

## Guardrails
Rules that prevent fake transcripts, invented candidate facts, and multi-question dumps.

Keep the headings in order.
TXT,
            'example_response'  => <<<'MD'
## Mock interview conductor prompt
You are my mock interviewer for a Customer Success Manager role in B2B education software. Ask me one interview question based on customer onboarding, explaining workflows to non-technical users, risk escalation, or transferring from support into customer success. After you ask the question, stop and wait for my answer.

When I answer, do not rewrite the answer as if you are me. Give brief feedback in this structure: what worked, what needs evidence, one tightening suggestion, and one optional follow-up question. If you ask a follow-up, ask only one and wait again. Do not invent my achievements, employers, metrics, customer types, or education software experience.

## How to run it
1. Open one external AI conversation and paste the conductor prompt above.
2. Answer the single question out loud or by typing as yourself.
3. Keep any follow-up in the same conversation so the AI has context.
4. Copy only useful feedback notes or your own reflection back into this SousMeow artifact if you want to review them later.

## What to capture afterward
- The question asked.
- One answer strength.
- One evidence gap or overclaim to fix.
- One sentence you want to shorten or clarify.

## Guardrails
- No fake transcript.
- No AI-written candidate answer.
- No more than one first question.
- No invented employer facts or candidate achievements.
MD,
            'output_sections'   => $sections([
                ['mock_interview_conductor_prompt', 'Mock interview conductor prompt'],
                ['how_to_run_it', 'How to run it'],
                ['what_to_capture_afterward', 'What to capture afterward'],
                ['guardrails', 'Guardrails'],
            ]),
            'checks' => [
                ['label' => 'One-question protocol', 'help' => 'The conductor prompt asks one question and waits for the human answer.', 'evidence_sections' => ['mock_interview_conductor_prompt', 'guardrails']],
                ['label' => 'AI does not answer as candidate', 'help' => 'The prompt explicitly blocks fake candidate answers and fake transcripts.', 'evidence_sections' => ['mock_interview_conductor_prompt', 'guardrails']],
                ['label' => 'Run instructions are clear', 'help' => 'The user knows to keep the mock interview in one external AI conversation.', 'evidence_sections' => ['how_to_run_it']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 4,
            'slug'              => 'review-answer-quality',
            'title'             => 'Review answer quality',
            'summary'           => 'Review prepared answers and any real mock feedback for clarity, evidence, and overclaim risk.',
            'why_it_matters'    => 'Practice is useful only if you notice what changed. This step turns prepared answers and real mock notes into improvement priorities. It does not invent a transcript or pretend the mock happened if you did not paste notes.',
            'unlocks_text'      => 'Approving unlocks final-day notes and follow-up messages.',
            'before_you_begin'  => 'If you ran the mock interview, paste your real feedback notes into the run-mock-interview artifact before this step. If you did not, this recipe will review only the answer preparation sheet and protocol. Do not paste private interviewer contact details.',
            'common_problems'   => 'The AI may try to infer how the mock went without notes. It may also over-polish answers into written paragraphs. Keep the review focused on evidence, clarity, length, and honest boundaries.',
            'recovery_guidance' => 'If the review invents mock performance, rerun and tell it to say "no mock notes supplied" where needed. If advice is too vague, ask for three specific edits to answer cues.',
            'est_minutes'       => 9,
            'prompt_template'   => <<<TXT
You are reviewing interview answer quality. Fact rule: {$factRule}

Preferred tone: {{preferred_tone}}
Concerns:
{{constraints_or_concerns}}

Approved answer preparation sheet:
{{artifact:prepare-answer-frameworks}}

Mock interview prompt and any real notes the user saved:
{{artifact:run-mock-interview}}

Produce Markdown with these exact headings:

## Answer improvement notes
Three to five concrete improvements for clarity, evidence, length, or honest boundaries. If no mock notes are supplied, say so and review the preparation sheet only.

## Strong answers to keep
The answer cues or stories that should stay mostly unchanged.

## Overclaim watchlist
Claims, metrics, or assumptions to avoid in the interview.

## Next practice round
A short plan for one more practice pass.

Keep the headings in order. Do not invent a mock transcript or candidate performance.
TXT,
            'example_response'  => <<<'MD'
## Answer improvement notes
- Keep the education software gap direct, then bridge to how you learn customer context.
- Shorten the onboarding checklist answer by cutting extra setup context.
- Add one verified detail about what the checklist contained if you can confirm it.
- If no renewal result is verified, describe the nonprofit escalation as keeping the conversation organized, not saving the renewal.

## Strong answers to keep
The onboarding checklist and nonprofit escalation stories are the strongest fit for this role. The teammate training story is useful for explaining workflows but should stay shorter.

## Overclaim watchlist
Do not claim district experience, customer retention ownership, renewal results, formal team leadership, or a quantified improvement unless verified.

## Next practice round
Run one more single-question mock focused on risk escalation. Aim for a 75-second answer with one situation sentence, two action sentences, and one honest result or learning.
MD,
            'output_sections'   => $sections([
                ['answer_improvement_notes', 'Answer improvement notes'],
                ['strong_answers_to_keep', 'Strong answers to keep'],
                ['overclaim_watchlist', 'Overclaim watchlist'],
                ['next_practice_round', 'Next practice round'],
            ]),
            'checks' => [
                ['label' => 'No fake mock transcript', 'help' => 'The review does not invent performance or dialogue.', 'evidence_sections' => ['answer_improvement_notes']],
                ['label' => 'Improvements are specific', 'help' => 'Notes name exactly what to shorten, clarify, or verify.', 'evidence_sections' => ['answer_improvement_notes', 'next_practice_round']],
                ['label' => 'Overclaims are blocked', 'help' => 'The watchlist catches unsupported claims before the interview.', 'evidence_sections' => ['overclaim_watchlist']],
            ],
        ]),
        sm_career_recipe([
            'stage_position'    => 4,
            'slug'              => 'pack-final-day-notes',
            'title'             => 'Pack final-day notes',
            'summary'           => 'Finish with a one-page prep card, employer questions, and thank-you message templates.',
            'why_it_matters'    => 'The final day is for calm execution, not rewriting the whole interview plan. This step packs the strongest stories, careful boundaries, questions to ask, and follow-up drafts into one place. It keeps thank-you messages truthful by leaving conversation details blank until after the interview.',
            'unlocks_text'      => 'Approving completes the Cookbook and opens your interview prep kit.',
            'before_you_begin'  => 'Confirm the interview time, format, and link or address outside SousMeow. Keep any personal contact details out of the prompt unless needed. Use the thank-you draft only after replacing placeholders with real conversation details.',
            'common_problems'   => 'Final notes can become too long to scan or include invented logistics. Thank-you emails may also invent topics before the conversation happens. Keep this artifact short and fill conversation details later.',
            'recovery_guidance' => 'If the prep card is too long, ask for a one-screen version. If the follow-up invents a topic, replace it with a bracketed reminder to insert a real topic after the interview.',
            'est_minutes'       => 12,
            'prompt_template'   => <<<TXT
You are packing final interview notes and follow-up drafts. Fact rule: {$factRule}

Target role: {{target_role}}
Target industry: {{target_industry}}
Preferred tone: {{preferred_tone}}
Job description or interview brief:
{{job_description}}

Approved answer preparation sheet:
{{artifact:prepare-answer-frameworks}}

Approved answer review:
{{artifact:review-answer-quality}}

Produce Markdown with these exact headings:

## Final prep card
A compact one-page card with top criteria, top stories, bridge lines, and reminders.

## Employer question list
Four to six questions to ask the employer.

## Thank-you email
A short thank-you email template that leaves a bracketed space for one real topic from the interview. Do not invent the topic.

## Final checklist
Five to seven day-of checks. Use "confirm" for logistics not supplied.

Keep the headings in order and make the notes easy to scan.
TXT,
            'example_response'  => <<<'MD'
## Final prep card
Top criteria: onboarding, clear workflow explanation, risk escalation, transfer from support to customer success.

Top stories: onboarding checklist, nonprofit escalation, teammate training, setup-question spreadsheet.

Bridge line: I have not worked directly in education software yet, so I would start by learning the customer language and success measures.

Reminder: answer first, then story, then result or learning. Do not claim unverified renewal or retention outcomes.

## Employer question list
1. What does successful onboarding look like for a new district account?
2. What customer risks should this role notice early?
3. How does the team share customer context with new CSMs?
4. What would you want the person in this role to learn first?
5. What are the next steps after this conversation?

## Thank-you email
Subject: Thank you for the Customer Success Manager conversation

Hi [Name],

Thank you for speaking with me about the Customer Success Manager role. I appreciated learning more about [real topic from the interview].

Our conversation reinforced my interest in helping customers onboard clearly and spot risks early. Please let me know if I can share anything else.

Thank you,
[Your name]

## Final checklist
- [ ] Confirm interview time, format, and link or address.
- [ ] Open resume or notes without private extras showing.
- [ ] Read top three story cues once.
- [ ] Practice the gap bridge line once.
- [ ] Prepare water, charger, and quiet space if video.
- [ ] Save the thank-you template for after the conversation.
MD,
            'output_sections'   => $sections([
                ['final_prep_card', 'Final prep card'],
                ['employer_question_list', 'Employer question list'],
                ['thank_you_email', 'Thank-you email'],
                ['final_checklist', 'Final checklist'],
            ]),
            'checks' => [
                ['label' => 'Prep card is scannable', 'help' => 'The card fits on one page and highlights only the most useful cues.', 'evidence_sections' => ['final_prep_card']],
                ['label' => 'Thank-you stays truthful', 'help' => 'The email leaves real interview details blank until after the conversation.', 'evidence_sections' => ['thank_you_email']],
                ['label' => 'Checklist is practical', 'help' => 'Every item can be completed or confirmed before or after the interview.', 'evidence_sections' => ['final_checklist']],
            ],
        ]),
    ],
];
