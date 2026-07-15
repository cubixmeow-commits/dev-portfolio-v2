<?php

declare(strict_types=1);

/**
 * Prepare for a Salary Negotiation - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'prepare-for-salary-negotiation',
    'title'               => 'Prepare for a Salary Negotiation',
    'tagline'             => 'Build a calm, evidence-based negotiation plan you can verify.',
    'description'         => 'Salary negotiation prep should separate evidence, research to verify, relationship tone, and fallback options. This Cookbook is not legal, financial, tax, or employment-law advice; verify all salary figures with reliable sources before using them. It never invents market ranges or tells you what you deserve without evidence. ' . sm_career_beginner_footer('a negotiation objective, evidence case, research checklist, target and fallback plan, opening script, pushback responses, follow-up note, and decision checklist', 7, 'about 70 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => [],
    'audience'            => 'Job seekers and employees preparing a calm, evidence-based compensation conversation',
    'outcome'             => 'negotiation objective, evidence case, research checklist, target and fallback plan, opening conversation draft, pushback responses, follow-up note, and decision checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 70,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 29,
    'stages' => [
        ['title' => 'Clarify', 'summary' => 'Define the negotiation type and objective.'],
        ['title' => 'Prepare evidence', 'summary' => 'Build the evidence case, research checklist, and target/fallback plan.'],
        ['title' => 'Practice conversation', 'summary' => 'Draft the opener and prepare calm responses to pushback.'],
        ['title' => 'Decide', 'summary' => 'Pack follow-up notes and a decision checklist.'],
    ],
    'fields' => [
        ['field_key' => 'negotiation_type', 'label' => 'Negotiation type', 'type' => 'select', 'help' => 'Choose the closest situation.', 'options' => ['New offer', 'Current-role raise'], 'sample_value' => 'New offer'],
        ['field_key' => 'role_location', 'label' => 'Role and location', 'type' => 'text', 'help' => 'Name the role and location context. Do not include private home address details.', 'placeholder' => 'e.g. Senior support specialist, Chicago or remote US', 'sample_value' => 'Customer success specialist, remote US'],
        ['field_key' => 'compensation_context', 'label' => 'Compensation context', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Include only offer or current-comp details you intentionally want to use. Verify all figures before negotiating.'), 'placeholder' => 'Offer/current pay details, benefits context, recruiter notes, timing.', 'sample_value' => 'New offer includes base salary and standard benefits. Recruiter said there may be limited flexibility but did not give a range. Start date is flexible.'],
        ['field_key' => 'accomplishments', 'label' => 'Relevant accomplishments', 'type' => 'textarea', 'help' => 'List real achievements, scope, responsibilities, and feedback. Only include numbers you can verify.', 'placeholder' => 'e.g. Led onboarding refresh...', 'sample_value' => "Led onboarding checklist refresh for support team\nHandled high-priority customer escalations\nTrained two new teammates on ticket triage\nReceived manager feedback for calm customer communication"],
        ['field_key' => 'preferred_outcome', 'label' => 'Preferred outcome', 'type' => 'textarea', 'help' => 'Name what you would like to improve. Use figures only if you have verified them.', 'placeholder' => 'e.g. Improve base salary if supported by verified market research.', 'sample_value' => 'Improve base salary if verified research and offer context support the ask; otherwise discuss signing bonus, review timeline, or professional development support.'],
        ['field_key' => 'acceptable_alternatives', 'label' => 'Acceptable alternatives', 'type' => 'textarea', 'help' => 'List non-salary options you would consider. This is planning support, not financial advice.', 'placeholder' => 'e.g. Signing bonus, review timeline, extra PTO...', 'sample_value' => 'Earlier compensation review, professional development budget, signing bonus if available, extra PTO if policy allows.'],
        ['field_key' => 'relationship_tone', 'label' => 'Relationship and tone', 'type' => 'select', 'help' => 'Choose the conversation style that fits the relationship.', 'options' => ['Warm and collaborative', 'Calm and direct', 'Formal and careful', 'Brief and practical'], 'sample_value' => 'Warm and collaborative'],
        ['field_key' => 'known_constraints', 'label' => 'Known constraints', 'type' => 'textarea', 'help' => 'Known budget, policy, timing, risk, or relationship constraints. Do not guess hidden constraints.', 'placeholder' => 'e.g. Recruiter mentioned limited flexibility.', 'sample_value' => 'Recruiter mentioned limited flexibility. I want to preserve a positive relationship and avoid sounding adversarial.'],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'clarify-negotiation-objective',
            'title' => 'Clarify negotiation objective',
            'summary' => 'Define what you are negotiating, what evidence is available, and what must be verified.',
            'why_it_matters' => 'Negotiation goes better when the ask, evidence, and constraints are clear before wording begins. This step separates preferences from verified facts. It also reminds the user that this is not legal, financial, tax, or employment-law advice.',
            'unlocks_text' => 'Approving unlocks evidence case, research checklist, and target/fallback planning.',
            'before_you_begin' => 'Gather the offer, current-role context, accomplishments, and constraints you are comfortable sharing. Remove private identifiers and verify any compensation figures outside SousMeow. Do not use this output as legal or financial advice.',
            'common_problems' => 'The AI may invent salary ranges, imply entitlement, or assume hidden budget rules. It may also make the ask too aggressive for the relationship tone. Keep the objective evidence-based.',
            'recovery_guidance' => 'If the objective includes unverified numbers, mark them for research. If it says what you deserve without evidence, rerun and require evidence-first language.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are clarifying a compensation negotiation objective. Fact rule: {$factRule}
This is not legal, financial, tax, or employment-law advice. The user must verify all salary figures. Never invent market ranges or say what the user deserves without evidence.

Negotiation type: {{negotiation_type}}
Role and location: {{role_location}}
Compensation context:
{{compensation_context}}
Preferred outcome:
{{preferred_outcome}}
Acceptable alternatives:
{{acceptable_alternatives}}
Relationship and tone: {{relationship_tone}}
Known constraints:
{{known_constraints}}

Produce Markdown with these exact headings:

## Negotiation objective
Two or three sentences naming the main ask, the context, and what must be verified.

## Evidence available now
Facts already supplied that may support the conversation.

## Research still required
Salary figures, policies, or market context the user must verify before negotiating.

## Advice boundaries
What this plan will not decide or claim.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Negotiation objective
This is a new-offer negotiation for a Customer Success Specialist role in a remote US context. The preferred outcome is to improve base salary if verified research and offer context support the ask, with alternatives such as review timing, professional development support, or signing bonus if available.

## Evidence available now
- The offer includes base salary and standard benefits.
- The recruiter mentioned limited flexibility but did not give a range.
- The candidate has onboarding, escalation, teammate training, and calm customer communication evidence.
- Start date appears flexible.

## Research still required
- Verified market compensation sources for the role and location context.
- Whether the employer has public pay bands or compensation policy.
- Which alternatives are available under company policy.
- Any deadlines for responding to the offer.

## Advice boundaries
This is not legal, financial, tax, or employment-law advice. Do not invent market ranges, competing offers, hidden budgets, or what the candidate "deserves" without verified evidence.
MD,
            'output_sections' => $sections([
                ['negotiation_objective', 'Negotiation objective'],
                ['evidence_available_now', 'Evidence available now'],
                ['research_still_required', 'Research still required'],
                ['advice_boundaries', 'Advice boundaries'],
            ]),
            'checks' => [
                ['label' => 'Objective is evidence-based', 'help' => 'The ask is tied to facts and verification needs.', 'evidence_sections' => ['negotiation_objective', 'research_still_required']],
                ['label' => 'No invented ranges', 'help' => 'The output does not create market salary figures.', 'evidence_sections' => ['research_still_required', 'advice_boundaries']],
                ['label' => 'Advice boundaries are clear', 'help' => 'Legal, financial, and employment-law limits are named.', 'evidence_sections' => ['advice_boundaries']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'build-evidence-case',
            'title' => 'Build evidence case',
            'summary' => 'Organize accomplishments and scope into a calm case for the conversation.',
            'why_it_matters' => 'A negotiation case should rest on role fit, scope, and verified accomplishments rather than pressure or vague deserving. This step creates evidence language that can support an ask without inventing impact. It also separates strong evidence from weaker claims.',
            'unlocks_text' => 'Approving unlocks the research checklist.',
            'before_you_begin' => 'Review accomplishments and manager feedback. Use numbers only when verified. Include relationship tone so the case does not sound adversarial.',
            'common_problems' => 'The AI may overstate impact, add metrics, or imply the employer owes an increase. It may also overlook non-salary alternatives. Keep the case factual and calm.',
            'recovery_guidance' => 'If the case says "deserve" without proof, rewrite as "based on these responsibilities and verified research." If impact is unverified, use qualitative evidence.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are building an evidence case for compensation negotiation. Fact rule: {$factRule}
Never invent achievements, market ranges, competing offers, or what the user deserves. This is not legal, financial, tax, or employment-law advice.

Accomplishments:
{{accomplishments}}
Role and location: {{role_location}}
Relationship and tone: {{relationship_tone}}

Approved objective:
{{artifact:clarify-negotiation-objective}}

Produce Markdown with these exact headings:

## Evidence case
Organize the strongest evidence into role fit, scope, accomplishments, and reliability.

## Strongest proof points
Three to five proof points to use in conversation.

## Weak claims to avoid
Claims that lack evidence or could damage credibility.

## Evidence phrasing
Two or three calm sentences the user can adapt.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Evidence case
Role fit: the candidate has customer communication, onboarding support, escalation, and teammate training evidence. Scope: the role depends on customer-facing follow-through. Accomplishments: onboarding checklist refresh, high-priority escalation handling, and triage training. Reliability: manager feedback supports calm customer communication.

## Strongest proof points
- Led an onboarding checklist refresh for the support team.
- Handled high-priority customer escalations.
- Trained two teammates on ticket triage.
- Received manager feedback for calm customer communication.

## Weak claims to avoid
Do not claim revenue impact, retention improvement, competing offers, or market underpayment unless verified. Do not say "I deserve more" without connecting the ask to evidence and research.

## Evidence phrasing
Based on the customer-facing scope of the role and my onboarding, escalation, and training experience, I would like to discuss whether there is room to improve the compensation package. I want to approach this collaboratively and anchor the conversation in verified information.
MD,
            'output_sections' => $sections([
                ['evidence_case', 'Evidence case'],
                ['strongest_proof_points', 'Strongest proof points'],
                ['weak_claims_to_avoid', 'Weak claims to avoid'],
                ['evidence_phrasing', 'Evidence phrasing'],
            ]),
            'checks' => [
                ['label' => 'Proof points are real', 'help' => 'The case uses supplied accomplishments only.', 'evidence_sections' => ['strongest_proof_points']],
                ['label' => 'No unsupported deserve claim', 'help' => 'Phrasing ties the ask to evidence and research.', 'evidence_sections' => ['evidence_phrasing', 'weak_claims_to_avoid']],
                ['label' => 'Weak claims are visible', 'help' => 'Unsupported claims are explicitly avoided.', 'evidence_sections' => ['weak_claims_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'prepare-research-checklist',
            'title' => 'Prepare research checklist',
            'summary' => 'List the compensation, policy, and context checks to verify before choosing numbers.',
            'why_it_matters' => 'Salary figures should come from research the user can verify, not AI guesses. This step creates a checklist for market context, employer policy, benefits, and timing. It keeps the next target-setting step honest.',
            'unlocks_text' => 'Approving unlocks target and fallback planning.',
            'before_you_begin' => 'Do not ask the AI to invent ranges. Gather reliable public sources, employer-provided ranges, recruiter notes, and benefits details outside SousMeow. Record source dates when possible.',
            'common_problems' => 'The AI may supply ranges, cite vague sources, or treat one website as truth. It may also ignore benefits and alternatives. Keep this as a verification checklist only.',
            'recovery_guidance' => 'If any salary number appears without a user-supplied source, delete it. Rerun and require source categories rather than figures.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are preparing a research checklist for salary negotiation. Fact rule: {$factRule}
Never invent salary ranges, market data, employer policy, or competing offers. The user must verify all figures outside this response.

Negotiation type: {{negotiation_type}}
Role and location: {{role_location}}
Compensation context:
{{compensation_context}}
Known constraints:
{{known_constraints}}

Approved objective:
{{artifact:clarify-negotiation-objective}}

Produce Markdown with these exact headings:

## Research checklist
Checklist of sources and facts to verify before choosing a target or fallback.

## Source quality notes
How to judge whether a source is useful without treating it as automatic truth.

## Figures not supplied
Compensation figures the AI must not create because the user has not provided verified values.

Keep headings in order and include no invented salary figures.
TXT,
            'example_response' => <<<'MD'
## Research checklist
- [ ] Employer-posted pay range, if available.
- [ ] Recruiter or offer-letter compensation details.
- [ ] Reliable role-and-location compensation sources with source dates.
- [ ] Benefits, bonus, equity, PTO, and review-timeline details.
- [ ] Company policy on signing bonus, relocation, professional development, or review timing.
- [ ] Response deadline and who owns compensation questions.

## Source quality notes
Prefer sources that match role level, location or remote context, company size, and date. Treat self-reported salary sites as directional until confirmed by multiple sources. Keep notes on where every figure came from.

## Figures not supplied
No verified market range, target number, minimum acceptable number, bonus amount, or competing offer was supplied. Do not invent any of them.
MD,
            'output_sections' => $sections([
                ['research_checklist', 'Research checklist'],
                ['source_quality_notes', 'Source quality notes'],
                ['figures_not_supplied', 'Figures not supplied'],
            ]),
            'checks' => [
                ['label' => 'Checklist requires verification', 'help' => 'The user has source categories to check before choosing figures.', 'evidence_sections' => ['research_checklist']],
                ['label' => 'No ranges are invented', 'help' => 'The output contains no made-up salary figures.', 'evidence_sections' => ['figures_not_supplied']],
                ['label' => 'Source quality is addressed', 'help' => 'The checklist explains how to judge research usefulness.', 'evidence_sections' => ['source_quality_notes']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'define-target-and-fallbacks',
            'title' => 'Define target and fallbacks',
            'summary' => 'Create a target/fallback planning sheet using only verified user-supplied figures or non-salary options.',
            'why_it_matters' => "A negotiation needs a target, acceptable alternatives, and a decision process before the conversation. The AI should not choose numbers without verified research. This step gives structure while leaving figures to the user's verified sources.",
            'unlocks_text' => 'Approving unlocks the opening conversation draft.',
            'before_you_begin' => 'Complete the research checklist first. If you have verified figures, paste them into compensation context before rerunning. If not, use non-salary fallback categories and verification reminders.',
            'common_problems' => 'The AI may invent a target number or minimum. It may also frame fallbacks as guarantees. Keep targets source-linked and alternatives policy-dependent.',
            'recovery_guidance' => 'If the output invents figures, rerun after deleting them and require bracketed fields. If it ignores alternatives, add acceptable alternatives and company constraints.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are defining target and fallback options for a compensation negotiation. Fact rule: {$factRule}
Do not invent salary ranges, target figures, minimums, policies, or competing offers. This is not legal, financial, tax, or employment-law advice.

Preferred outcome:
{{preferred_outcome}}
Acceptable alternatives:
{{acceptable_alternatives}}
Compensation context:
{{compensation_context}}
Known constraints:
{{known_constraints}}

Approved evidence case:
{{artifact:build-evidence-case}}

Approved research checklist:
{{artifact:prepare-research-checklist}}

Produce Markdown with these exact headings:

## Target and fallback plan
A planning sheet with bracketed fields for verified target, fallback, and walk-away considerations. Use only user-supplied verified figures if present.

## Alternative levers
Non-salary items to discuss if base salary is limited, with policy-verification notes.

## Decision criteria
Questions the user should answer before deciding.

## Verification reminders
Facts to confirm before using the plan.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Target and fallback plan
- Verified target request: [enter only after verified research].
- Preferred ask language: improve the base salary if the verified research and offer context support it.
- Fallback option: discuss an earlier compensation review or professional development support if base salary flexibility is limited.
- Walk-away considerations: [user decides privately after reviewing finances, risk, and outside advice if needed].

## Alternative levers
- Earlier compensation review: verify manager and HR policy.
- Professional development budget: verify whether the role offers it.
- Signing bonus: ask only if the employer uses signing bonuses.
- Extra PTO: verify policy before requesting.

## Decision criteria
- Does verified research support the ask?
- Which alternatives would materially improve the offer?
- What response deadline applies?
- What advice or personal financial review is needed before deciding?

## Verification reminders
Confirm all figures, policies, deadlines, benefits, and who can approve changes before using the plan.
MD,
            'output_sections' => $sections([
                ['target_and_fallback_plan', 'Target and fallback plan'],
                ['alternative_levers', 'Alternative levers'],
                ['decision_criteria', 'Decision criteria'],
                ['verification_reminders', 'Verification reminders'],
            ]),
            'checks' => [
                ['label' => 'No invented target', 'help' => 'Targets use bracketed fields unless verified figures were supplied.', 'evidence_sections' => ['target_and_fallback_plan']],
                ['label' => 'Alternatives are policy-aware', 'help' => 'Non-salary levers include verification notes.', 'evidence_sections' => ['alternative_levers']],
                ['label' => 'Decision remains with user', 'help' => 'The plan asks decision questions instead of making financial decisions.', 'evidence_sections' => ['decision_criteria']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'draft-opening-conversation',
            'title' => 'Draft opening conversation',
            'summary' => 'Write a concise opener that makes the ask calmly and leaves space for dialogue.',
            'why_it_matters' => 'The opening sets the tone for the negotiation. It should be direct, evidence-based, and respectful. It should not sound like a threat or rely on unverified market claims.',
            'unlocks_text' => 'Approving unlocks pushback response preparation.',
            'before_you_begin' => 'Choose which verified target or bracketed ask you are comfortable using. Keep the relationship tone visible. If no figures are verified, use a conversation opener that asks about flexibility without naming a number.',
            'common_problems' => 'Openers may be too apologetic, too adversarial, or too vague. They may also cite research not actually completed. Keep the ask clear and source-aware.',
            'recovery_guidance' => 'If the opener includes unsupported figures, replace them with bracketed verified fields. If it sounds pushy, ask for the same ask in a collaborative tone.',
            'est_minutes' => 11,
            'prompt_template' => <<<TXT
You are drafting the opening for a compensation conversation. Fact rule: {$factRule}
Never invent salary figures, market ranges, or competing offers. Do not say what the user deserves without evidence. This is not legal, financial, tax, or employment-law advice.

Relationship and tone: {{relationship_tone}}
Negotiation type: {{negotiation_type}}

Approved evidence case:
{{artifact:build-evidence-case}}

Approved target and fallback plan:
{{artifact:define-target-and-fallbacks}}

Produce Markdown with these exact headings:

## Opening conversation draft
A concise script or email opener. Use bracketed fields for verified figures if needed.

## Why this works
Three bullets explaining how it uses evidence, tone, and verification.

## Lines to avoid
Phrases that sound unsupported, adversarial, or legally/financially overconfident.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Opening conversation draft
Thank you again for the offer. I am excited about the Customer Success Specialist role and the chance to bring my onboarding, escalation, and teammate-training experience to the team.

I would like to discuss whether there is room to improve the compensation package. Based on the customer-facing scope of the role and the research I am verifying, I was hoping we could explore [verified target request] or, if base salary flexibility is limited, options such as [verified alternative].

## Why this works
- It starts with interest and keeps the relationship collaborative.
- It ties the ask to role scope and real evidence.
- It uses bracketed fields instead of invented figures or unverified alternatives.

## Lines to avoid
- "I know I deserve more" without evidence.
- "The market rate is..." without verified sources.
- "I have another offer" unless true.
- "This is non-negotiable" unless the user has privately decided that after appropriate review.
MD,
            'output_sections' => $sections([
                ['opening_conversation_draft', 'Opening conversation draft'],
                ['why_this_works', 'Why this works'],
                ['lines_to_avoid', 'Lines to avoid'],
            ]),
            'checks' => [
                ['label' => 'Opener is calm and clear', 'help' => 'The ask is understandable and relationship-aware.', 'evidence_sections' => ['opening_conversation_draft']],
                ['label' => 'Figures stay verified', 'help' => 'Bracketed fields are used when figures are not supplied.', 'evidence_sections' => ['opening_conversation_draft', 'why_this_works']],
                ['label' => 'Risky lines are blocked', 'help' => 'The avoid list prevents unsupported claims.', 'evidence_sections' => ['lines_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'prepare-pushback-responses',
            'title' => 'Prepare pushback responses',
            'summary' => 'Prepare calm replies to common constraints without arguing or inventing leverage.',
            'why_it_matters' => 'Pushback is normal in negotiation, and preparation prevents reactive language. Responses should acknowledge constraints, restate evidence, and ask a useful next question. They should not invent competing offers or legal claims.',
            'unlocks_text' => 'Approving unlocks follow-up and decision planning.',
            'before_you_begin' => 'Review known constraints and alternatives. Decide which responses fit the actual relationship. Keep all leverage truthful.',
            'common_problems' => 'The AI may make responses combative or add facts to gain leverage. It may also ignore the option to pause and think. Keep responses short and honest.',
            'recovery_guidance' => 'If a response invents leverage, delete it. If it sounds too soft, ask for a clearer question while preserving tone.',
            'est_minutes' => 11,
            'prompt_template' => <<<TXT
You are preparing responses to compensation negotiation pushback. Fact rule: {$factRule}
Never invent competing offers, salary ranges, legal rights, employer policies, or what the user deserves. This is not legal, financial, tax, or employment-law advice.

Known constraints:
{{known_constraints}}
Relationship and tone: {{relationship_tone}}

Approved opening draft:
{{artifact:draft-opening-conversation}}

Approved target and fallback plan:
{{artifact:define-target-and-fallbacks}}

Produce Markdown with these exact headings:

## Pushback responses
Responses to likely pushback such as limited budget, fixed range, needing approval, or no immediate flexibility.

## Questions to ask next
Questions that clarify options, timing, and decision process.

## Pause script
A short line for asking for time to review without making a rushed decision.

## Claims to avoid
Unsupported leverage, figures, or legal/financial claims to keep out.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Pushback responses
- If base salary is limited: I understand there may be constraints. Is there flexibility in review timing, professional development support, signing bonus, or PTO if policy allows?
- If approval is needed: Thank you for checking. What information would help the approval conversation?
- If the range is fixed: I appreciate the clarity. Could we discuss when compensation is reviewed and what performance milestones would matter?
- If no flexibility is available: Thank you for being direct. I would like to review the full package and timeline before I respond.

## Questions to ask next
- Who approves compensation changes?
- Which parts of the package are flexible?
- What is the timeline for a response?
- If changes are not possible now, when is review normally revisited?

## Pause script
Thank you for explaining the constraints. I would like to review the full package and follow up by [real date].

## Claims to avoid
Do not invent another offer, a market range, legal entitlement, hidden budget, or a walk-away position that the user has not decided.
MD,
            'output_sections' => $sections([
                ['pushback_responses', 'Pushback responses'],
                ['questions_to_ask_next', 'Questions to ask next'],
                ['pause_script', 'Pause script'],
                ['claims_to_avoid', 'Claims to avoid'],
            ]),
            'checks' => [
                ['label' => 'Responses stay collaborative', 'help' => 'Pushback replies acknowledge constraints and ask useful questions.', 'evidence_sections' => ['pushback_responses']],
                ['label' => 'Pause is available', 'help' => 'The user has language to avoid rushed decisions.', 'evidence_sections' => ['pause_script']],
                ['label' => 'Leverage is truthful', 'help' => 'Unsupported leverage is explicitly avoided.', 'evidence_sections' => ['claims_to_avoid']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'pack-follow-up-and-decision',
            'title' => 'Pack follow-up and decision',
            'summary' => 'Finish with follow-up messages, a verification checklist, and decision review prompts.',
            'why_it_matters' => 'The final step helps the user leave a clear record and make a deliberate decision. It should support review, not decide for the user. Follow-up language must reflect only what actually happened.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your negotiation prep kit.',
            'before_you_begin' => 'Confirm all figures, policy details, and deadlines outside SousMeow. Consider appropriate legal, financial, tax, or employment advice if the decision has significant consequences. Fill bracketed fields only with real conversation details.',
            'common_problems' => 'The AI may invent what the employer said, set a deadline, or advise acceptance/rejection. It may also omit verification. Keep follow-up factual and decisions user-owned.',
            'recovery_guidance' => 'If the follow-up invents conversation details, replace them with bracketed fields. If the decision checklist tells you what to do, rewrite it as questions.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are packing follow-up and decision materials for a compensation negotiation. Fact rule: {$factRule}
This is not legal, financial, tax, or employment-law advice. Never invent salary figures, market ranges, employer responses, or what the user deserves.

Approved opening draft:
{{artifact:draft-opening-conversation}}

Approved pushback responses:
{{artifact:prepare-pushback-responses}}

Approved target and fallback plan:
{{artifact:define-target-and-fallbacks}}

Produce Markdown with these exact headings:

## Follow-up message
A concise follow-up email template with bracketed fields for real conversation details, dates, and verified terms.

## Decision checklist
Questions the user should answer before accepting, declining, or continuing the conversation.

## Verification checklist
Facts, figures, deadlines, and policies to verify before acting.

## Final reminder
A short reminder about advice boundaries and user-owned decision-making.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Follow-up message
Subject: Follow-up on compensation discussion

Hi [Name],

Thank you for discussing the Customer Success Specialist offer with me. I appreciated learning more about [real detail from the conversation].

As discussed, I wanted to follow up on [verified request or alternative]. Please let me know if there is any additional information I can provide for the review.

Thank you,
[Your name]

## Decision checklist
- Have I verified every compensation figure I plan to use?
- Do I understand the full package, not only base salary?
- Which alternatives would actually matter to me?
- What is the real response deadline?
- Do I need outside legal, financial, tax, or employment advice before deciding?
- Am I comfortable with the relationship tone of my next message?

## Verification checklist
- [ ] Offer terms or current compensation context.
- [ ] Verified salary research sources and dates.
- [ ] Benefits, bonus, equity, PTO, and review timing.
- [ ] Company policy for alternatives.
- [ ] Response deadline and approval owner.
- [ ] Any written changes before relying on them.

## Final reminder
Use this as conversation preparation, not legal, financial, tax, or employment-law advice. Do not act on unverified figures or invented leverage; the decision remains yours.
MD,
            'output_sections' => $sections([
                ['follow_up_message', 'Follow-up message'],
                ['decision_checklist', 'Decision checklist'],
                ['verification_checklist', 'Verification checklist'],
                ['final_reminder', 'Final reminder'],
            ]),
            'checks' => [
                ['label' => 'Follow-up uses real details only', 'help' => 'Conversation details are bracketed until true.', 'evidence_sections' => ['follow_up_message']],
                ['label' => 'Decision remains user-owned', 'help' => 'Checklist asks questions instead of deciding.', 'evidence_sections' => ['decision_checklist', 'final_reminder']],
                ['label' => 'Verification is complete', 'help' => 'Figures, policies, deadlines, and written terms are checked.', 'evidence_sections' => ['verification_checklist']],
            ],
        ]),
    ],
];
