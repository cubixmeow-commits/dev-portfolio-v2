<?php

declare(strict_types=1);

/**
 * Plan a Career Change - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'plan-a-career-change',
    'title'               => 'Plan a Career Change',
    'tagline'             => 'Map a realistic transition without pretending gaps disappear.',
    'description'         => 'A career change is easier to plan when transferable skills, real gaps, proof, and small validation experiments are visible. This Cookbook helps you define the transition, map skills, compare capabilities, name gaps, prioritize learning and proof, build a staged plan, test assumptions, and produce a 90-day roadmap. It does not tell you to quit a job or make major financial decisions; it presents options, assumptions, and risks to verify. ' . sm_career_beginner_footer('a transferable-skills map, gap analysis, learning plan, proof plan, experiments, 90-day roadmap, and progress review template', 8, 'about 90 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => [],
    'audience'            => 'People exploring a role or industry change who want a grounded transition plan',
    'outcome'             => 'transferable-skills map, gap analysis, learning plan, proof plan, experiments, 90-day roadmap, and progress review template',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 90,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 28,
    'stages' => [
        ['title' => 'Define transition', 'summary' => 'Name the desired move and transferable skill base.'],
        ['title' => 'Compare fit', 'summary' => 'Compare capabilities to target requirements and identify real gaps.'],
        ['title' => 'Build proof', 'summary' => 'Prioritize learning, portfolio proof, and staged actions.'],
        ['title' => 'Validate and plan', 'summary' => 'Design experiments and a realistic 90-day roadmap.'],
    ],
    'fields' => [
        ['field_key' => 'current_role', 'label' => 'Current role or work base', 'type' => 'text', 'help' => 'Use your real current role, student status, freelance focus, or caregiving/work context.', 'placeholder' => 'e.g. Retail store manager', 'sample_value' => 'Retail store manager'],
        ['field_key' => 'years_experience', 'label' => 'Years of relevant experience', 'type' => 'text', 'help' => 'Use a truthful range or short explanation.', 'placeholder' => 'e.g. 6 years retail operations', 'sample_value' => '6 years in retail operations and team scheduling'],
        ['field_key' => 'target_role', 'label' => 'Desired target role', 'type' => 'text', 'help' => 'Name the role you want to explore. It can be tentative.', 'placeholder' => 'e.g. Customer success manager', 'sample_value' => 'Customer success manager'],
        ['field_key' => 'target_industry', 'label' => 'Target industry or context', 'type' => 'text', 'help' => 'Name the industry, customer type, or work setting if known.', 'placeholder' => 'e.g. B2B SaaS', 'sample_value' => 'B2B SaaS'],
        ['field_key' => 'job_description', 'label' => 'Target role examples', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste sample postings or target-role requirements.'), 'placeholder' => 'Responsibilities and requirements from 1 to 3 postings.', 'sample_value' => "Manage onboarding for small business customers\nTrack account health and follow-ups\nExplain product features clearly\nUse CRM notes to coordinate next steps\nPreferred: SaaS experience and comfort with customer calls"],
        ['field_key' => 'existing_resume', 'label' => 'Resume or background notes', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste relevant background only; remove contact details and private employer information.'), 'placeholder' => 'Selected role bullets, education, training, or volunteer work.', 'sample_value' => "Store Manager, Northline Market, 2018-present\nSchedule 18 associates weekly\nTrain new cashiers and shift leads\nHandle customer escalations\nTrack inventory issues and vendor follow-up"],
        ['field_key' => 'strongest_achievements', 'label' => 'Strongest achievements', 'type' => 'textarea', 'help' => 'List real transferable achievements. Only include metrics you can verify.', 'placeholder' => 'e.g. Trained shift leads...', 'sample_value' => "Built a new shift handoff checklist\nTrained four shift leads\nHandled difficult customer escalations calmly\nCoordinated vendor follow-up when inventory shipments were late"],
        ['field_key' => 'available_resources', 'label' => 'Available resources and limits', 'type' => 'textarea', 'help' => 'Time, budget, support, schedule, risk tolerance, and constraints. Do not include sensitive financial details.', 'placeholder' => 'e.g. 4 hours/week; cannot relocate...', 'sample_value' => 'Can spend 4 hours per week for learning and outreach. Cannot relocate this year. Wants to keep current job while exploring options.'],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred planning tone', 'type' => 'select', 'help' => 'Choose how direct the roadmap should sound.', 'options' => ['Practical and calm', 'Encouraging but realistic', 'Direct and structured', 'Careful and risk-aware'], 'sample_value' => 'Practical and calm'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Concerns or constraints', 'type' => 'textarea', 'help' => 'Name gaps, risks, confidence concerns, or decisions you do not want the AI to make for you.', 'placeholder' => 'e.g. No SaaS experience; avoid financial advice.', 'sample_value' => 'No SaaS experience. Worried about taking a pay cut. Do not advise quitting or making financial decisions.'],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'define-desired-transition',
            'title' => 'Define desired transition',
            'summary' => 'Describe the target move, why it is being explored, and what is still unknown.',
            'why_it_matters' => 'A career change plan needs a clear target before comparing skills or gaps. This step names the move without pretending it is already decided. It keeps big assumptions visible for later validation.',
            'unlocks_text' => 'Approving unlocks the transferable-skills map.',
            'before_you_begin' => 'Gather target postings and your background notes. Keep sensitive financial details out of the prompt. Treat the result as planning support, not a life decision.',
            'common_problems' => 'The AI may make the transition sound easier than it is or assume motivation and finances. It may also advise quitting too early. Keep language exploratory and risk-aware.',
            'recovery_guidance' => 'If the output makes major decisions for you, rerun and require options, assumptions, and risks. If the target is too vague, add one or two sample postings.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are helping define a realistic career transition. Fact rule: {$factRule}
Do not advise quitting a job or making major financial decisions. Present options, assumptions, and risks.

Current role: {{current_role}}
Years of experience: {{years_experience}}
Target role: {{target_role}}
Target industry: {{target_industry}}
Target role examples:
{{job_description}}
Available resources and limits:
{{available_resources}}
Concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Transition statement
Two or three sentences describing the desired move and what is known.

## Why this move may fit
Supported reasons only, tied to background or target role facts.

## Assumptions to test
Assumptions that need evidence before major decisions.

## Risks and boundaries
Risks to watch and decisions the plan will not make.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Transition statement
The desired move is from retail store management into customer success management in B2B SaaS. The current background includes scheduling, training, customer escalations, vendor follow-up, and team coordination; the target postings emphasize onboarding, account health, product explanation, CRM notes, and customer calls.

## Why this move may fit
- Customer escalation experience may transfer to customer calls.
- Training shift leads may transfer to onboarding and explanation.
- Scheduling and follow-up habits may transfer to account coordination.
- Vendor follow-up may transfer to structured next steps.

## Assumptions to test
- Whether employers will value retail operations as customer success evidence.
- Whether SaaS product learning can be demonstrated without a prior SaaS role.
- Whether the user wants the daily rhythm of account management.

## Risks and boundaries
Do not assume a salary change, job offer timeline, or financial decision. Do not advise quitting the current job. Treat this as an exploration plan with evidence checkpoints.
MD,
            'output_sections' => $sections([
                ['transition_statement', 'Transition statement'],
                ['why_this_move_may_fit', 'Why this move may fit'],
                ['assumptions_to_test', 'Assumptions to test'],
                ['risks_and_boundaries', 'Risks and boundaries'],
            ]),
            'checks' => [
                ['label' => 'Transition is specific', 'help' => 'The current and target roles are both visible.', 'evidence_sections' => ['transition_statement']],
                ['label' => 'No major decision advice', 'help' => 'The plan does not tell the user to quit or make financial moves.', 'evidence_sections' => ['risks_and_boundaries']],
                ['label' => 'Assumptions are testable', 'help' => 'The next steps can gather evidence.', 'evidence_sections' => ['assumptions_to_test']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'extract-transferable-skills',
            'title' => 'Extract transferable skills',
            'summary' => 'Map current experience into skills that may transfer to the target role.',
            'why_it_matters' => 'Transferable skills are the bridge between old context and new role. They need specific evidence, not vague traits. This step creates a skills map that can later feed resume, interview, and proof planning.',
            'unlocks_text' => 'Approving unlocks capability comparison.',
            'before_you_begin' => 'Review your background notes and strongest achievements. Include small examples if they show a real skill. Do not add skills you cannot explain with a story.',
            'common_problems' => 'The AI may translate every duty into a target-role skill or use buzzwords without evidence. It may also ignore context differences. Keep transfer claims careful.',
            'recovery_guidance' => 'If a skill lacks proof, move it to a possible skill or gap. If the map is thin, add more real examples from work, volunteering, or training.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are extracting transferable skills for a career change. Fact rule: {$factRule}

Resume or background notes:
{{existing_resume}}
Strongest achievements:
{{strongest_achievements}}

Approved transition statement:
{{artifact:define-desired-transition}}

Produce Markdown with these exact headings:

## Transferable-skills map
Map current experience to target-role skill areas. Include the evidence for each transfer claim.

## Strongest transfer stories
Three stories that could explain the transition credibly.

## Weak or uncertain transfers
Skills that may transfer but need more proof or context.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Transferable-skills map
- Customer communication: handled difficult customer escalations calmly; may transfer to customer calls.
- Onboarding and training: trained four shift leads; may transfer to customer onboarding explanation.
- Follow-up coordination: tracked vendor shipment issues; may transfer to account next steps.
- Process improvement: built a shift handoff checklist; may transfer to onboarding process clarity.
- Team scheduling: scheduled 18 associates weekly; may transfer to organized account planning, though the context differs.

## Strongest transfer stories
1. Shift handoff checklist: shows process improvement and practical documentation.
2. Training shift leads: shows explanation, patience, and onboarding behavior.
3. Customer escalation handling: shows calm communication under pressure.

## Weak or uncertain transfers
CRM usage, SaaS product fluency, account health tracking, and business customer communication need more direct proof.
MD,
            'output_sections' => $sections([
                ['transferable_skills_map', 'Transferable-skills map'],
                ['strongest_transfer_stories', 'Strongest transfer stories'],
                ['weak_or_uncertain_transfers', 'Weak or uncertain transfers'],
            ]),
            'checks' => [
                ['label' => 'Transfers cite evidence', 'help' => 'Each skill points to a real background fact.', 'evidence_sections' => ['transferable_skills_map']],
                ['label' => 'Best stories are clear', 'help' => 'The strongest stories could be used in applications or interviews.', 'evidence_sections' => ['strongest_transfer_stories']],
                ['label' => 'Uncertain transfers are not overstated', 'help' => 'Weak areas remain marked as uncertain.', 'evidence_sections' => ['weak_or_uncertain_transfers']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'compare-capabilities-to-target',
            'title' => 'Compare capabilities to target',
            'summary' => 'Compare current proof against target-role requirements without smoothing over differences.',
            'why_it_matters' => 'A transition plan needs an honest fit comparison. This step shows which requirements are supported, partially supported, or unsupported. It keeps optimism useful by tying it to evidence.',
            'unlocks_text' => 'Approving unlocks genuine gap identification.',
            'before_you_begin' => 'Use target postings as requirements, not as a wish list. Keep the transferable-skills map nearby. Be willing to mark a requirement as unsupported.',
            'common_problems' => 'The AI may overmatch retail or unrelated experience to every target requirement. It can also make the gap list too discouraging. Keep three categories: supported, partial, unsupported.',
            'recovery_guidance' => 'If the comparison is too flattering, ask for stricter evidence standards. If it is too negative, ask for partial evidence and realistic proof-building options.',
            'est_minutes' => 11,
            'prompt_template' => <<<TXT
You are comparing current capabilities to target-role requirements. Fact rule: {$factRule}
Do not pretend gaps disappear. Present supported, partial, and unsupported areas.

Target role examples:
{{job_description}}

Approved transition:
{{artifact:define-desired-transition}}

Approved transferable-skills map:
{{artifact:extract-transferable-skills}}

Produce Markdown with these exact headings:

## Capability comparison
A table or bullets with target requirement, current evidence, fit level, and notes.

## Strongest fit areas
Requirements with the clearest evidence.

## Partial fit areas
Requirements with some evidence but meaningful context differences.

## Unsupported areas
Requirements with no current proof supplied.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Capability comparison
| Target requirement | Current evidence | Fit level | Notes |
| --- | --- | --- | --- |
| Customer onboarding | Training shift leads | Partial | Training transfers, but customer onboarding context differs. |
| Account follow-up | Vendor follow-up | Partial | Follow-up habit transfers; account ownership is not proven. |
| Product explanation | Shift lead training | Partial | Explanation skill is real; SaaS product context missing. |
| CRM notes | No direct proof supplied | Unsupported | Needs practice or proof. |
| Account health tracking | Inventory issue tracking | Partial | Pattern tracking transfers, but account health model is missing. |

## Strongest fit areas
Training, calm customer communication, follow-up coordination, and process documentation.

## Partial fit areas
Customer onboarding, account coordination, product explanation, and account-health thinking.

## Unsupported areas
Direct SaaS experience, CRM workflow examples, customer success terminology, and account-health metrics.
MD,
            'output_sections' => $sections([
                ['capability_comparison', 'Capability comparison'],
                ['strongest_fit_areas', 'Strongest fit areas'],
                ['partial_fit_areas', 'Partial fit areas'],
                ['unsupported_areas', 'Unsupported areas'],
            ]),
            'checks' => [
                ['label' => 'Fit levels are honest', 'help' => 'Requirements are separated into supported, partial, and unsupported.', 'evidence_sections' => ['capability_comparison']],
                ['label' => 'Strong areas have proof', 'help' => 'Strong fit areas are not just traits.', 'evidence_sections' => ['strongest_fit_areas']],
                ['label' => 'Unsupported areas are named', 'help' => 'Missing proof is visible.', 'evidence_sections' => ['unsupported_areas']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'identify-genuine-gaps',
            'title' => 'Identify genuine gaps',
            'summary' => 'Turn unsupported and partial areas into a clear gap analysis.',
            'why_it_matters' => 'Gaps are not failures; they are planning inputs. Naming them specifically prevents vague anxiety and prevents false confidence. This step distinguishes knowledge gaps, proof gaps, network gaps, and decision gaps.',
            'unlocks_text' => 'Approving unlocks learning and proof priorities.',
            'before_you_begin' => 'Review the capability comparison without trying to fix every gap immediately. Think about which gaps matter for entry-level target roles versus later growth. Keep risk and time limits visible.',
            'common_problems' => 'The AI may tell the user to solve every gap at once or minimize important gaps. It may also imply paid training is required without evidence. Keep gaps prioritized and options-based.',
            'recovery_guidance' => 'If the list feels overwhelming, ask for critical, helpful, and optional gaps. If it prescribes expensive action, ask for low-cost alternatives and verification steps.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are identifying genuine career-change gaps. Fact rule: {$factRule}
Do not recommend quitting, debt, or major financial decisions. Present options and risks.

Available resources and limits:
{{available_resources}}
Concerns:
{{constraints_or_concerns}}

Approved capability comparison:
{{artifact:compare-capabilities-to-target}}

Produce Markdown with these exact headings:

## Gap analysis
Group gaps into knowledge, proof, experience, network, and decision gaps where relevant.

## Critical vs optional gaps
Which gaps likely matter first and which can wait.

## Risks if ignored
Practical risks of ignoring the critical gaps.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Gap analysis
- Knowledge gaps: SaaS customer success terminology, account health concepts, and CRM workflows.
- Proof gaps: no direct CRM example, no SaaS onboarding sample, no account-health tracking artifact.
- Experience gaps: no direct B2B SaaS customer role supplied.
- Network gaps: no evidence yet from people doing CSM work.
- Decision gaps: needs to confirm whether account management rhythm is appealing.

## Critical vs optional gaps
Critical first: CRM workflow familiarity, customer success vocabulary, one proof artifact, and at least two informational conversations. Optional later: formal certification, advanced SaaS tooling, and specialized account-health metrics.

## Risks if ignored
Applications may sound like generic customer service rather than customer success. Interviews may expose unfamiliar terms. The user may pursue the path without knowing whether the daily work fits.
MD,
            'output_sections' => $sections([
                ['gap_analysis', 'Gap analysis'],
                ['critical_vs_optional_gaps', 'Critical vs optional gaps'],
                ['risks_if_ignored', 'Risks if ignored'],
            ]),
            'checks' => [
                ['label' => 'Gaps are specific', 'help' => 'The analysis names concrete missing proof or knowledge.', 'evidence_sections' => ['gap_analysis']],
                ['label' => 'Priorities are realistic', 'help' => 'Critical and optional gaps are separated.', 'evidence_sections' => ['critical_vs_optional_gaps']],
                ['label' => 'Risks are practical', 'help' => 'Risks explain what could happen without fearmongering.', 'evidence_sections' => ['risks_if_ignored']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'prioritize-learning-and-proof',
            'title' => 'Prioritize learning and proof',
            'summary' => 'Choose focused learning moves and proof artifacts that address the most important gaps.',
            'why_it_matters' => 'Learning without proof can become endless preparation. Proof without learning can look shallow. This step pairs each priority with a realistic artifact or conversation.',
            'unlocks_text' => 'Approving unlocks the staged transition plan.',
            'before_you_begin' => 'Review available weekly time and budget limits. Prefer small proof artifacts before expensive commitments. Keep all choices reversible where possible.',
            'common_problems' => 'The AI may prescribe courses, certifications, or big portfolio projects without evidence they are needed. It may also skip outreach. Keep learning tied to target gaps.',
            'recovery_guidance' => 'If the plan is too expensive or time-heavy, ask for a four-hours-per-week version. If it lacks proof, require one artifact per critical gap.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are prioritizing learning and proof for a career transition. Fact rule: {$factRule}
Do not prescribe expensive programs or major financial decisions. Offer low-risk options first.

Available resources and limits:
{{available_resources}}
Preferred tone: {{preferred_tone}}

Approved gap analysis:
{{artifact:identify-genuine-gaps}}

Approved transferable-skills map:
{{artifact:extract-transferable-skills}}

Produce Markdown with these exact headings:

## Learning plan
Three to five focused learning priorities with small actions.

## Proof plan
Three proof artifacts or evidence moves the candidate can create or gather.

## Outreach questions
Questions for informational conversations that test assumptions.

## Not now list
Actions that are too broad, premature, or risky for this stage.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Learning plan
1. Learn basic customer success terms: onboarding, adoption, account health, escalation, renewal risk.
2. Practice a simple CRM-style note workflow using a sample customer scenario.
3. Study three customer onboarding examples from public, non-confidential sources.
4. Learn how CSMs describe first-30-day success in job postings and interviews.

## Proof plan
1. Create a one-page sample onboarding checklist for a fictional small business customer.
2. Rewrite the shift handoff checklist story as a customer-success transfer story.
3. Build a simple account follow-up tracker using fictional data.

## Outreach questions
- What surprised you about moving into customer success?
- Which skills mattered most in your first CSM role?
- What proof helped you get interviews?
- How much product knowledge was expected before starting?

## Not now list
Do not quit the current job based on this plan. Do not pay for an expensive certification before validating employer expectations. Do not claim SaaS experience before you have it.
MD,
            'output_sections' => $sections([
                ['learning_plan', 'Learning plan'],
                ['proof_plan', 'Proof plan'],
                ['outreach_questions', 'Outreach questions'],
                ['not_now_list', 'Not now list'],
            ]),
            'checks' => [
                ['label' => 'Learning targets critical gaps', 'help' => 'Actions connect to the gap analysis.', 'evidence_sections' => ['learning_plan']],
                ['label' => 'Proof is concrete', 'help' => 'Artifacts can be made without pretending prior experience.', 'evidence_sections' => ['proof_plan']],
                ['label' => 'Risky moves are delayed', 'help' => 'The not-now list avoids quitting or major financial decisions.', 'evidence_sections' => ['not_now_list']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'create-staged-transition-plan',
            'title' => 'Create staged transition plan',
            'summary' => 'Sequence learning, proof, outreach, and application readiness into realistic stages.',
            'why_it_matters' => 'A staged plan prevents all-or-nothing thinking. It lets the user gather evidence before committing harder. Each stage should have an exit condition, not just a to-do list.',
            'unlocks_text' => 'Approving unlocks validation experiments.',
            'before_you_begin' => 'Use your available time honestly. Keep current obligations visible. Decide what evidence would make you continue, pause, or revise the target.',
            'common_problems' => 'Plans often jump straight to applications or delay forever in learning mode. They may also imply a guaranteed timeline. Keep stages conditional and evidence-based.',
            'recovery_guidance' => 'If the plan is too aggressive, ask for a slower version. If it has no decision points, add continue/revise/stop criteria for each stage.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are creating a staged career transition plan. Fact rule: {$factRule}
Do not tell the user to quit a job or make major financial decisions. Use reversible stages and decision points.

Available resources and limits:
{{available_resources}}
Concerns:
{{constraints_or_concerns}}

Approved learning and proof priorities:
{{artifact:prioritize-learning-and-proof}}

Approved capability comparison:
{{artifact:compare-capabilities-to-target}}

Produce Markdown with these exact headings:

## Staged transition plan
Three or four stages with focus, actions, evidence to collect, and exit condition.

## Proof milestones
Concrete milestones that show readiness is increasing.

## Risk controls
Ways to keep the plan reversible and realistic.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Staged transition plan
Stage 1: Learn the target role language. Actions: study customer success terms and three postings. Evidence: can explain onboarding, account health, and escalation in plain language. Exit condition: can describe the role without guessing.

Stage 2: Build proof. Actions: create a sample onboarding checklist and fictional account follow-up tracker. Evidence: two artifacts that connect retail operations skills to CSM tasks. Exit condition: can explain each artifact in two minutes.

Stage 3: Validate with people. Actions: hold two informational conversations. Evidence: notes on what employers value and what gaps matter. Exit condition: continue, revise target role, or pause.

Stage 4: Prepare applications. Actions: update resume language, LinkedIn positioning, and story bank. Evidence: materials reviewed against real postings.

## Proof milestones
- Customer success vocabulary notes completed.
- One sample onboarding checklist drafted.
- One fictional account tracker drafted.
- Two informational conversations completed.
- Resume bullets revised for transferable skills.

## Risk controls
Keep current job while exploring. Use free or low-cost learning first. Make no salary, relocation, or resignation decision from this plan alone. Review evidence before applying broadly.
MD,
            'output_sections' => $sections([
                ['staged_transition_plan', 'Staged transition plan'],
                ['proof_milestones', 'Proof milestones'],
                ['risk_controls', 'Risk controls'],
            ]),
            'checks' => [
                ['label' => 'Stages have exit conditions', 'help' => 'Each stage has evidence for moving forward or revising.', 'evidence_sections' => ['staged_transition_plan']],
                ['label' => 'Milestones are concrete', 'help' => 'Progress can be observed, not just felt.', 'evidence_sections' => ['proof_milestones']],
                ['label' => 'Plan is reversible', 'help' => 'Risk controls avoid major irreversible advice.', 'evidence_sections' => ['risk_controls']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'define-validation-experiments',
            'title' => 'Define validation experiments',
            'summary' => 'Design small experiments to test fit, employer demand, and daily-work assumptions.',
            'why_it_matters' => 'Validation turns a career change from a belief into a set of learnable signals. Small experiments reduce risk before bigger commitments. They should test assumptions without requiring a leap.',
            'unlocks_text' => 'Approving unlocks the 90-day roadmap.',
            'before_you_begin' => 'Review the assumptions from the transition statement and outreach questions. Choose experiments you can run with available time. Avoid experiments that require confidential data or risky commitments.',
            'common_problems' => 'The AI may call applications an experiment without defining what will be learned. It may also create experiments that are too large. Keep each experiment small and measurable by observation.',
            'recovery_guidance' => 'If an experiment feels vague, add a hypothesis, action, signal, and decision rule. If it is too risky, shrink it to a conversation, artifact, or practice task.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are defining validation experiments for a career transition. Fact rule: {$factRule}
Do not recommend quitting or major financial decisions. Use small tests with decision rules.

Approved transition assumptions:
{{artifact:define-desired-transition}}

Approved staged plan:
{{artifact:create-staged-transition-plan}}

Produce Markdown with these exact headings:

## Validation experiments
Three to five experiments. For each: hypothesis, action, signal to watch, and decision rule.

## Experiments not to run yet
Tests that are premature, too expensive, or too risky.

## Progress review template
A short template for reviewing experiment results.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Validation experiments
1. Hypothesis: retail training stories can translate to CSM onboarding. Action: write and explain a sample onboarding checklist. Signal: two reviewers understand the transfer. Decision rule: if unclear, revise proof or target role language.
2. Hypothesis: the daily CSM rhythm sounds appealing. Action: hold two informational conversations. Signal: notes show the work matches interests and constraints. Decision rule: continue if tradeoffs feel acceptable.
3. Hypothesis: CRM workflow is learnable with practice. Action: create fictional account notes and follow-up tracker. Signal: can explain the workflow without confusion. Decision rule: if not, add one more learning week.
4. Hypothesis: job postings value transferable operations skills. Action: compare five postings. Signal: repeated keywords match evidence. Decision rule: if not, revise target role or proof plan.

## Experiments not to run yet
Do not quit the current job, pay for an expensive program, relocate, or make financial commitments based only on early interest.

## Progress review template
- Experiment:
- What I did:
- Evidence collected:
- What surprised me:
- Continue, revise, or pause:
- Next small step:
MD,
            'output_sections' => $sections([
                ['validation_experiments', 'Validation experiments'],
                ['experiments_not_to_run_yet', 'Experiments not to run yet'],
                ['progress_review_template', 'Progress review template'],
            ]),
            'checks' => [
                ['label' => 'Experiments have decision rules', 'help' => 'Each test says what signal changes the plan.', 'evidence_sections' => ['validation_experiments']],
                ['label' => 'Risky tests are deferred', 'help' => 'Premature commitments are explicitly blocked.', 'evidence_sections' => ['experiments_not_to_run_yet']],
                ['label' => 'Review template is usable', 'help' => 'The user can record what happened and decide next.', 'evidence_sections' => ['progress_review_template']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'produce-ninety-day-roadmap',
            'title' => 'Produce ninety-day roadmap',
            'summary' => 'Package the transition into a 90-day roadmap with weekly focus, proof, experiments, and review points.',
            'why_it_matters' => 'A 90-day roadmap gives enough time to learn and test without pretending the whole change is solved. It keeps action, evidence, and review connected. The roadmap should be adjustable as new evidence arrives.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your career-change planning kit.',
            'before_you_begin' => 'Review every approved artifact and your available time. Treat the roadmap as a planning document, not a guarantee. Confirm any financial, legal, or employment implications with appropriate advisors outside SousMeow.',
            'common_problems' => 'Roadmaps may promise outcomes, compress timelines, or skip review. They may also sneak in quitting advice. Keep the plan outcome-focused but not outcome-guaranteed.',
            'recovery_guidance' => 'If the roadmap promises a job or salary, remove that claim. If it is too dense, ask for weekly themes plus three priority actions per month.',
            'est_minutes' => 14,
            'prompt_template' => <<<TXT
You are producing a 90-day career-change roadmap. Fact rule: {$factRule}
Do not tell the user to quit a job, make salary assumptions, or make major financial decisions. Present options, assumptions, risks, and review points.

Available resources and limits:
{{available_resources}}
Preferred tone: {{preferred_tone}}

Approved staged transition plan:
{{artifact:create-staged-transition-plan}}

Approved validation experiments:
{{artifact:define-validation-experiments}}

Approved learning and proof plan:
{{artifact:prioritize-learning-and-proof}}

Produce Markdown with these exact headings:

## 90-day roadmap
Month-by-month plan with weekly focus, actions, proof, and review point.

## Progress review template
Reusable review questions for the end of each month.

## Assumptions and risks
Assumptions to revisit and risks to manage.

## Next seven days
The first small actions to start the roadmap.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## 90-day roadmap
Month 1: Learn and translate. Week 1: study CSM terms and compare five postings. Week 2: write a role-language glossary. Week 3: rewrite three transferable stories. Week 4: review fit and choose two proof artifacts.

Month 2: Build proof and talk to people. Week 5: draft a fictional onboarding checklist. Week 6: build a fictional account follow-up tracker. Week 7: hold one informational conversation. Week 8: hold a second conversation and revise proof.

Month 3: Prepare materials and test applications carefully. Week 9: revise resume bullets for transferable evidence. Week 10: update LinkedIn positioning. Week 11: practice two interview stories. Week 12: choose whether to apply selectively, revise target roles, or extend validation.

## Progress review template
- What evidence did I collect this month?
- Which assumptions changed?
- Which gap is smaller?
- What still feels risky or unclear?
- Continue, revise, pause, or seek outside advice?

## Assumptions and risks
Assumptions: employers value operations transfer, SaaS basics can be learned with small proof, and the daily work fits the user's preferences. Risks: overclaiming SaaS experience, paying for training too early, or making financial decisions without advice.

## Next seven days
1. Save five target postings.
2. List repeated requirements.
3. Build a one-page customer success vocabulary sheet.
4. Choose one transfer story to rewrite.
5. Identify two people to ask for informational conversations.
MD,
            'output_sections' => $sections([
                ['ninety_day_roadmap', '90-day roadmap'],
                ['progress_review_template', 'Progress review template'],
                ['assumptions_and_risks', 'Assumptions and risks'],
                ['next_seven_days', 'Next seven days'],
            ]),
            'checks' => [
                ['label' => 'Roadmap is realistic', 'help' => 'Actions fit the supplied resources and avoid guarantees.', 'evidence_sections' => ['ninety_day_roadmap']],
                ['label' => 'Review points exist', 'help' => 'The user has a way to continue, revise, or pause.', 'evidence_sections' => ['progress_review_template']],
                ['label' => 'Risks are managed', 'help' => 'The roadmap avoids quitting, salary assumptions, and major financial advice.', 'evidence_sections' => ['assumptions_and_risks']],
            ],
        ]),
    ],
];
