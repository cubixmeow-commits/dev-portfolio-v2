<?php

declare(strict_types=1);

/**
 * Audit a Medical Bill - executable Cookbook for money-major-decisions.
 */

require_once __DIR__ . '/../money_helpers.php';

$factRule = SM_MONEY_FACT_RULE;
$disclaimer = SM_MONEY_DISCLAIMER;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'audit-medical-bill',
    'title'               => 'Audit a Medical Bill',
    'tagline'             => 'Translate a medical bill into plain language and prepare careful questions.',
    'description'         => 'Organize a redacted medical bill, insurance context, questions, call script, inquiry letter, and case timeline without giving medical advice or inventing coverage, legal rights, codes, or outcomes. This Cookbook helps prepare questions for billing, insurance, or qualified advisors. ' . sm_money_beginner_footer('a plain language summary, verification checklist, question list, call script, appeal or inquiry letter, and case timeline', 6, 'about 60 minutes'),
    'primary_category'    => 'money-major-decisions',
    'collections'         => ['money-major-decisions'],
    'audience'            => 'People trying to understand a medical bill, prepare billing questions, or organize an insurance inquiry',
    'outcome'             => 'plain language summary, verification checklist, question list, call script, appeal or inquiry letter, and case timeline',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'harbor',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 60,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 32,
    'stages' => [
        ['title' => 'Translate and verify', 'summary' => 'Summarize the redacted bill and identify verification needs.'],
        ['title' => 'Prepare questions', 'summary' => 'Turn concerns into careful questions for billing or insurance.'],
        ['title' => 'Contact carefully', 'summary' => 'Draft a call script and written appeal or inquiry without inventing coverage.'],
        ['title' => 'Track the case', 'summary' => 'Build a timeline and follow-up record.'],
    ],
    'fields' => [
        ['field_key' => 'care_setting', 'label' => 'Care setting', 'type' => 'text', 'help' => 'Name the general setting, not private medical details. Use broad terms if preferred.', 'placeholder' => 'e.g. Urgent care visit, outpatient lab, hospital ER', 'sample_value' => 'Urgent care visit with outside lab charge'],
        ['field_key' => 'bill_summary_redacted', 'label' => 'Bill summary redacted', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('Summarize the bill. Replace MRNs, insurance IDs, claim numbers, SSNs, dates of birth, and account numbers with placeholders.'), 'placeholder' => 'Provider, bill date, amount, charge lines, codes if visible, claim status, safe placeholders.', 'sample_value' => "Provider bill dated June 10 for $642.30.\nVisit date [VISIT-DATE]. Patient account [PATIENT-ACCOUNT]. MRN [MRN].\nLines include office visit $215, lab panel $310, facility fee $117.30.\nStatement says insurance pending or not applied; claim number unknown."],
        ['field_key' => 'insurance_context_redacted', 'label' => 'Insurance context redacted', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('Include only coverage context you know. Use placeholders for member ID, group ID, claim ID, and insurance portal details.'), 'placeholder' => 'Plan type if known, EOB notes, deductible context, claim status, safe placeholders.', 'sample_value' => "Insurance member ID [MEMBER-ID], plan name [PLAN-NAME].\nPortal shows no matching claim yet, but I may be looking in the wrong place.\nI have not received an Explanation of Benefits for this bill.\nDeductible status unknown."],
        ['field_key' => 'what_you_noticed', 'label' => 'What you noticed', 'type' => 'textarea', 'help' => 'Describe what looks confusing, unexpected, duplicated, missing, or worth asking about. Do not include medical symptoms unless necessary.', 'placeholder' => 'e.g. Insurance not applied; duplicate lab line; provider name unfamiliar.', 'sample_value' => "Insurance may not have been applied.\nThe outside lab name is unfamiliar.\nThe facility fee was unexpected.\nI do not know whether the lab was in network or whether coding is correct."],
        ['field_key' => 'questions_or_goals', 'label' => 'Questions or goals', 'type' => 'textarea', 'help' => 'State what you want to understand or request. This is not medical, legal, insurance, or tax advice.', 'placeholder' => 'e.g. Ask for itemized bill, confirm claim submission, request EOB, ask about financial assistance.', 'sample_value' => 'Understand whether insurance was billed, request an itemized bill, ask whether a claim should be submitted or resubmitted, ask about payment plan or financial assistance options, and avoid missing any deadline.'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Include deadlines, stress points, communication needs, or areas where the AI must not guess.', 'placeholder' => 'e.g. Avoid medical advice; do not invent coverage or legal rights.', 'sample_value' => 'Do not give medical advice. Do not say a service should be covered. I need a calm script and want everything in writing if possible.'],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the questions and letters should sound.', 'options' => ['Calm and careful', 'Polite and direct', 'Detailed and factual', 'Warm but firm'], 'sample_value' => 'Calm and careful'],
    ],
    'recipes' => [
        sm_money_recipe([
            'stage_position' => 1,
            'slug' => 'summarize-bill-plainly',
            'title' => 'Summarize bill plainly',
            'summary' => 'Translate the redacted bill into a plain-language summary with unknowns separated.',
            'why_it_matters' => 'Medical bills often combine provider charges, insurance status, codes, and patient balances in confusing ways. This step creates a plain-language summary without interpreting medical care, inventing coverage, or deciding what is owed. It also reinforces privacy redaction for MRNs and insurance IDs.',
            'unlocks_text' => 'Approving unlocks the verification checklist.',
            'before_you_begin' => 'Redact MRNs, insurance IDs, claim IDs, SSNs, dates of birth, full account numbers, card numbers, bank numbers, passwords, and portal credentials. Have the bill and any EOB nearby if available.',
            'common_problems' => 'The AI may say a service should be covered, diagnose a code, or decide a charge is wrong. It may also reveal sensitive identifiers if pasted. Keep the summary descriptive and mark unknowns.',
            'recovery_guidance' => 'If the output gives medical advice or coverage conclusions, rerun and require questions only. If it invents claim status, replace it with "unknown until verified."',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are summarizing a redacted medical bill in plain language. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never give medical advice, interpret symptoms, invent insurance coverage, invent legal rights, or decide whether care should be covered. Use placeholders for MRN, insurance IDs, claim IDs, account numbers, SSNs, cards, bank numbers, medical record numbers, passwords, and credentials.

Care setting: {{care_setting}}

Bill summary redacted:
{{bill_summary_redacted}}

Insurance context redacted:
{{insurance_context_redacted}}

What the user noticed:
{{what_you_noticed}}

Questions or goals:
{{questions_or_goals}}

Produce Markdown with these exact headings:

## Plain language summary
Explain what the bill appears to show using only supplied facts. Label unknowns.

## Charge and status notes
List visible charges, bill status, insurance status, and placeholders exactly as supplied.

## Unknowns not to decide
List coverage, coding, network, medical, legal, and payment questions that must be verified elsewhere.

## Privacy redaction check
Remind the user to redact MRNs, insurance IDs, claim IDs, SSNs, dates of birth, bank numbers, card numbers, passwords, and credentials.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Plain language summary
The redacted provider bill is for an urgent care visit with an outside lab charge and shows $642.30 on a June 10 statement. The statement lists office visit, lab panel, and facility fee lines. The insurance status is unclear because the statement says insurance is pending or not applied, and the insurance portal does not show a matching claim yet.

## Charge and status notes
- Provider bill date: June 10.
- Visit date: [VISIT-DATE].
- Patient account: [PATIENT-ACCOUNT].
- MRN placeholder: [MRN].
- Office visit: $215.
- Lab panel: $310.
- Facility fee: $117.30.
- Insurance member ID placeholder: [MEMBER-ID].
- Claim number: unknown.
- EOB: not received.
- Deductible status: unknown.

## Unknowns not to decide
- Whether insurance was billed correctly.
- Whether any line should be covered.
- Whether the lab was in network.
- Whether coding is correct.
- Whether the facility fee is valid.
- Whether any legal rights or deadlines apply.
- Whether the medical care itself was appropriate.

## Privacy redaction check
Before using external AI, replace MRNs, insurance member IDs, claim IDs, SSNs, dates of birth, full account numbers, card numbers, bank numbers, passwords, portal credentials, and medical record numbers with placeholders such as [MRN], [MEMBER-ID], and [CLAIM-ID].
MD,
            'output_sections' => $sections([
                ['plain_language_summary', 'Plain language summary'],
                ['charge_and_status_notes', 'Charge and status notes'],
                ['unknowns_not_to_decide', 'Unknowns not to decide'],
                ['privacy_redaction_check', 'Privacy redaction check'],
            ]),
            'checks' => [
                ['label' => 'Summary stays descriptive', 'help' => 'The output explains the bill without deciding coverage or care.', 'evidence_sections' => ['plain_language_summary', 'unknowns_not_to_decide']],
                ['label' => 'Visible details are preserved', 'help' => 'Charges and placeholders match supplied information.', 'evidence_sections' => ['charge_and_status_notes']],
                ['label' => 'MRN and insurance IDs are protected', 'help' => 'The redaction check names sensitive medical and insurance identifiers.', 'evidence_sections' => ['privacy_redaction_check']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 1,
            'slug' => 'build-verification-checklist',
            'title' => 'Build verification checklist',
            'summary' => 'List what to verify with the provider, insurer, bill, EOB, and official sources.',
            'why_it_matters' => 'Before disputing or paying, the user may need to verify itemization, claim submission, EOB status, network questions, deadlines, and assistance options. This step turns confusion into careful verification tasks. It avoids deciding coverage or legal rights.',
            'unlocks_text' => 'Approving unlocks the question list.',
            'before_you_begin' => 'Have the plain-language summary ready. Add any EOB, portal screenshots, or itemized bill notes only after redacting MRNs, member IDs, claim IDs, account numbers, and credentials.',
            'common_problems' => 'The AI may treat insurance portal absence as proof no claim exists or assume a charge is an error. It should instead list what to verify and where to ask.',
            'recovery_guidance' => 'If the checklist says what is covered, rewrite it as "ask insurer whether..." If it creates deadlines, mark them for official verification.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are building a medical bill verification checklist. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Do not provide medical advice or invent insurance coverage, legal rights, coding conclusions, claim status, or deadlines.

Approved plain language summary:
{{artifact:summarize-bill-plainly}}

Insurance context redacted:
{{insurance_context_redacted}}

What the user noticed:
{{what_you_noticed}}

Questions or goals:
{{questions_or_goals}}

Produce Markdown with these exact headings:

## Verification checklist
List items to verify with the provider, insurer, bill, EOB, or official source. Include why each matters.

## Documents to request
List itemized bill, EOB, claim details, financial assistance forms, or written explanations to request when relevant.

## Who to contact
Name the type of contact, such as provider billing, insurer member services, lab billing, or qualified advisor. Do not invent phone numbers.

## Verification boundaries
State what the checklist will not decide, including medical advice, coverage, legal rights, or what the patient owes.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Verification checklist
- Confirm whether the provider submitted a claim to insurance and, if so, request the claim ID.
- Confirm whether the outside lab billed separately or through the urgent care provider.
- Request an itemized bill showing each charge line and any codes already on the statement.
- Ask the insurer whether an EOB exists or is pending for [VISIT-DATE].
- Ask whether the facility fee is part of the provider's normal billing for this setting.
- Ask about payment plan or financial assistance options before making decisions.
- Verify any appeal, dispute, or assistance deadlines from official sources.

## Documents to request
- Itemized provider bill.
- Explanation of Benefits if available.
- Claim submission confirmation or denial explanation.
- Outside lab bill or statement if separate.
- Financial assistance or payment plan information.
- Written explanation of any corrected balance.

## Who to contact
- Provider billing department.
- Insurance member services using a verified number from the insurance card or official portal.
- Outside lab billing department if the lab charge is separate.
- A qualified medical billing advocate, legal aid, or benefits advisor if deadlines or rights matter.

## Verification boundaries
This checklist does not give medical advice, decide whether care was necessary, determine coverage, interpret legal rights, or state what the patient owes. It organizes questions for verified sources.
MD,
            'output_sections' => $sections([
                ['verification_checklist', 'Verification checklist'],
                ['documents_to_request', 'Documents to request'],
                ['who_to_contact', 'Who to contact'],
                ['verification_boundaries', 'Verification boundaries'],
            ]),
            'checks' => [
                ['label' => 'Tasks are verification-focused', 'help' => 'Checklist items ask sources to confirm facts rather than deciding them.', 'evidence_sections' => ['verification_checklist']],
                ['label' => 'Documents are practical', 'help' => 'Requested documents connect to the bill questions.', 'evidence_sections' => ['documents_to_request']],
                ['label' => 'Boundaries avoid advice', 'help' => 'Coverage, medical, legal, and payment decisions are not made.', 'evidence_sections' => ['verification_boundaries']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'write-question-list',
            'title' => 'Write question list',
            'summary' => 'Prepare focused questions for provider billing, insurer, and any separate biller.',
            'why_it_matters' => 'Good questions help the user get clear answers without arguing about conclusions too early. This step translates verification tasks into exact questions for each contact. It keeps coverage and legal conclusions with the appropriate source.',
            'unlocks_text' => 'Approving unlocks the billing call script.',
            'before_you_begin' => 'Review the verification checklist and choose which organizations to contact first. Keep member IDs, MRNs, claim IDs, account numbers, and credentials redacted in drafts.',
            'common_problems' => 'The AI may ask leading questions that assume an error or coverage entitlement. It may also combine provider and insurer questions. Keep questions neutral and directed to the right party.',
            'recovery_guidance' => 'If questions sound accusatory, ask for neutral wording. If they imply coverage, change them to "Can you explain whether..."',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are writing a medical bill question list. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never give medical advice or invent coverage, legal rights, claim decisions, codes, rates, or deadlines.

Approved verification checklist:
{{artifact:build-verification-checklist}}

Care setting: {{care_setting}}
Questions or goals:
{{questions_or_goals}}
Constraints or concerns:
{{constraints_or_concerns}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Provider billing questions
Questions for the provider or facility billing office.

## Insurance questions
Questions for the insurer or plan administrator, using placeholders for member and claim identifiers.

## Separate biller questions
Questions for an outside lab, facility, or other biller if relevant.

## Questions not to ask the AI
List medical, coverage, legal, and personal-data questions that should go to verified sources instead.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Provider billing questions
- Can you confirm whether a claim was submitted to insurance for visit [VISIT-DATE]?
- If submitted, what is the claim ID or submission date?
- Can you send an itemized bill for patient account [PATIENT-ACCOUNT]?
- Can you explain what the facility fee represents in billing terms?
- Should any outside lab charge be billed separately, and if so, which biller should I contact?
- Are payment plan or financial assistance options available while insurance status is being verified?

## Insurance questions
- Do you show a claim for [VISIT-DATE] from this provider or lab?
- If yes, what is the claim status and when should I expect an EOB?
- If no, what information would the provider need to submit or resubmit?
- Can you explain whether the provider or lab appears in network based on your current records?
- Are there appeal or inquiry deadlines I should verify from official plan documents?

## Separate biller questions
- Are you billing for the lab panel connected to visit [VISIT-DATE]?
- Was insurance information [MEMBER-ID] received, and was a claim submitted?
- Can you provide an itemized statement and claim reference if one exists?
- Who should correct insurance information if it is missing or wrong?

## Questions not to ask the AI
Do not ask AI to decide whether a service was medically necessary, whether insurance must cover it, whether codes are correct, what legal rights apply, or what private identifiers should be revealed. Use verified provider, insurer, official, or qualified advisor sources for those questions.
MD,
            'output_sections' => $sections([
                ['provider_billing_questions', 'Provider billing questions'],
                ['insurance_questions', 'Insurance questions'],
                ['separate_biller_questions', 'Separate biller questions'],
                ['questions_not_to_ask_the_ai', 'Questions not to ask the AI'],
            ]),
            'checks' => [
                ['label' => 'Questions are routed correctly', 'help' => 'Provider, insurer, and separate biller questions are separated.', 'evidence_sections' => ['provider_billing_questions', 'insurance_questions', 'separate_biller_questions']],
                ['label' => 'No coverage decision is implied', 'help' => 'Questions request explanation rather than asserting coverage.', 'evidence_sections' => ['insurance_questions']],
                ['label' => 'AI limits are explicit', 'help' => 'The output names questions that require verified sources.', 'evidence_sections' => ['questions_not_to_ask_the_ai']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'draft-billing-call-script',
            'title' => 'Draft billing call script',
            'summary' => 'Create a calm phone script and note template for provider or insurance calls.',
            'why_it_matters' => 'Medical billing calls can be stressful and full of unfamiliar terms. A script helps the user explain the issue, request documents, and record the answer without sharing unnecessary sensitive details. It also avoids making medical or coverage claims the user cannot verify.',
            'unlocks_text' => 'Approving unlocks the appeal or inquiry letter.',
            'before_you_begin' => 'Use verified phone numbers from official cards, portals, or statements. Keep placeholders ready for MRN, member ID, claim ID, and account number; share full identifiers only through safe verified channels when required.',
            'common_problems' => 'The AI may put private identifiers in the script, state that insurance must pay, or argue medical necessity. It may also omit call tracking details. Keep it careful and document-focused.',
            'recovery_guidance' => 'If the script includes medical advice or coverage conclusions, rewrite as questions. If it lacks a record template, add date, time, representative, and reference number fields.',
            'est_minutes' => 11,
            'prompt_template' => <<<TXT
You are drafting a medical billing call script. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Do not give medical advice, invent coverage, interpret codes as medical advice, invent legal rights, or promise billing outcomes.

Approved question list:
{{artifact:write-question-list}}

Approved verification checklist:
{{artifact:build-verification-checklist}}

Care setting: {{care_setting}}
Preferred tone: {{preferred_tone}}
Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Call opener
A short script for provider billing or insurance, with placeholders for MRN, member ID, claim ID, and account number.

## Call flow
Step-by-step prompts for asking questions, requesting documents, and confirming next steps.

## If the answer is unclear
Polite language for asking for written explanation, supervisor review, or another verified contact.

## Call record template
A fill-in template for date, time, representative, reference number, claim or account placeholder, answer summary, and next action.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Call opener
Hello, I am calling about a bill for an urgent care visit on [VISIT-DATE]. I am using patient account [PATIENT-ACCOUNT] and can provide additional verified identifiers through this official channel if needed. I want to understand whether insurance was billed, whether an EOB exists, and what documents I should request before making payment decisions.

## Call flow
1. Confirm the caller is speaking with the correct billing or insurance department.
2. Ask whether a claim exists for [VISIT-DATE] and request [CLAIM-ID] if available.
3. Ask for an itemized bill or EOB if it has not been received.
4. Ask what the facility fee or lab line represents in billing terms.
5. Ask what information is needed to submit, resubmit, or review the claim.
6. Ask about payment plan or financial assistance options if appropriate.
7. Ask for written confirmation and a reference number.

## If the answer is unclear
Thank you for checking. I am still not sure what my next step should be. Could you please send a written explanation, note the account, and tell me which department or document can confirm the claim status? If supervisor review is available, I would like to request it and receive a reference number.

## Call record template
- Date and time:
- Organization called:
- Representative name or ID:
- Patient account placeholder:
- MRN/member/claim placeholder used:
- Reference number:
- Documents requested:
- Answer summary:
- Next action:
- Follow-up date:
MD,
            'output_sections' => $sections([
                ['call_opener', 'Call opener'],
                ['call_flow', 'Call flow'],
                ['if_the_answer_is_unclear', 'If the answer is unclear'],
                ['call_record_template', 'Call record template'],
            ]),
            'checks' => [
                ['label' => 'Script protects identifiers', 'help' => 'The opener uses placeholders and safe-channel language.', 'evidence_sections' => ['call_opener']],
                ['label' => 'Flow asks for documents', 'help' => 'The call flow seeks itemized bills, EOBs, and written confirmation.', 'evidence_sections' => ['call_flow']],
                ['label' => 'Record supports follow-up', 'help' => 'The template captures reference numbers and next actions.', 'evidence_sections' => ['call_record_template']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'draft-appeal-or-inquiry-letter',
            'title' => 'Draft appeal or inquiry letter',
            'summary' => 'Draft a written billing inquiry, insurance inquiry, or appeal-style request with safe placeholders.',
            'why_it_matters' => 'Written inquiries help preserve a clear record and give billing or insurance teams the facts they need. This step creates a careful letter that asks for review and documents without asserting unsupplied coverage rights. It can be adapted for provider billing, insurer inquiry, or appeal preparation after verifying instructions.',
            'unlocks_text' => 'Approving unlocks the case timeline.',
            'before_you_begin' => 'Verify the correct submission address, portal, form, and deadline from official sources. Redact MRNs, member IDs, claim IDs, account numbers, SSNs, dates of birth, cards, bank numbers, passwords, and credentials in drafts.',
            'common_problems' => 'The AI may call the letter an appeal when no denial exists, cite plan rights not supplied, or say a charge is illegal. It should use conditional language and mark appeal requirements for verification.',
            'recovery_guidance' => 'If the draft asserts coverage or rights, replace with "please review and explain." If no denial exists, call it an inquiry unless the user verifies an appeal process.',
            'est_minutes' => 13,
            'prompt_template' => <<<TXT
You are drafting a medical bill inquiry or appeal-preparation letter. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never give medical advice, invent insurance coverage, legal rights, plan terms, appeal deadlines, coding conclusions, or guaranteed outcomes.

Approved plain language summary:
{{artifact:summarize-bill-plainly}}

Approved question list:
{{artifact:write-question-list}}

Approved call script:
{{artifact:draft-billing-call-script}}

Care setting: {{care_setting}}
Questions or goals:
{{questions_or_goals}}
Constraints or concerns:
{{constraints_or_concerns}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Inquiry or appeal letter
A complete letter with bracketed placeholders. Use "inquiry" unless the user has verified a formal appeal process or denial.

## Documents to attach or request
List documents by safe placeholder and purpose.

## Submission details to verify
List addresses, portals, forms, deadlines, plan requirements, or official instructions to verify before sending.

## Letter safety check
Checks for privacy, medical advice, coverage claims, legal claims, and factual placeholders.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Inquiry or appeal letter
[Send date]

To [Provider billing or insurance department]:

Re: Medical billing inquiry for visit [VISIT-DATE], patient account [PATIENT-ACCOUNT], member ID [MEMBER-ID]

I am writing to request review and written clarification of a bill dated June 10 for an urgent care visit with an outside lab charge. The bill lists an office visit, lab panel, and facility fee, and it appears unclear whether insurance has been applied or whether an Explanation of Benefits has been issued.

Please confirm whether a claim was submitted for this visit and provide the claim ID if available. If a claim has not been submitted or needs correction, please explain what information is needed and who should provide it. Please also send an itemized bill and any EOB or written explanation available for this account.

I am not asking this letter to decide coverage or medical necessity. I am requesting the records and explanation needed to understand the bill and any next steps.

Thank you,
[Your name]
[Preferred contact method]

## Documents to attach or request
- Current bill dated June 10: shows the disputed or unclear balance.
- Itemized bill: request if not already available.
- EOB: request from insurer if available.
- Claim confirmation [CLAIM-ID]: request or attach if later received.
- Any prior call notes: attach only if helpful and redacted.

## Submission details to verify
- Correct provider or insurer submission channel.
- Whether this should be submitted as an inquiry, claim correction request, or formal appeal.
- Any official appeal or inquiry deadline.
- Any required form or member authorization.
- Whether written confirmation will be sent.

## Letter safety check
- [ ] MRN, member ID, claim ID, SSN, date of birth, account, card, bank, password, and credential details are redacted or shared only through verified required channels.
- [ ] The letter does not give medical advice.
- [ ] The letter does not claim coverage, coding errors, legal rights, or deadlines that were not verified.
- [ ] Unknowns are framed as questions.
- [ ] Attachments are safe copies, not originals unless required.
MD,
            'output_sections' => $sections([
                ['inquiry_or_appeal_letter', 'Inquiry or appeal letter'],
                ['documents_to_attach_or_request', 'Documents to attach or request'],
                ['submission_details_to_verify', 'Submission details to verify'],
                ['letter_safety_check', 'Letter safety check'],
            ]),
            'checks' => [
                ['label' => 'Letter uses inquiry language safely', 'help' => 'The draft avoids formal appeal claims unless verified.', 'evidence_sections' => ['inquiry_or_appeal_letter']],
                ['label' => 'Submission requirements are verified elsewhere', 'help' => 'Deadlines, forms, and channels remain verification tasks.', 'evidence_sections' => ['submission_details_to_verify']],
                ['label' => 'Privacy and advice boundaries are checked', 'help' => 'The safety check rejects medical, coverage, and legal conclusions.', 'evidence_sections' => ['letter_safety_check']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 4,
            'slug' => 'build-case-timeline',
            'title' => 'Build case timeline',
            'summary' => 'Package a timeline, response tracker, and follow-up checklist for the medical bill case.',
            'why_it_matters' => 'Medical billing questions may involve multiple organizations and documents. A timeline helps the user track what was requested, who responded, and what remains unknown. It also keeps follow-up grounded in official instructions rather than invented deadlines or rights.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your medical bill audit kit.',
            'before_you_begin' => 'Gather the summary, checklist, questions, call script, and inquiry letter. Use placeholders for MRNs, insurance IDs, claim IDs, patient accounts, SSNs, dates of birth, cards, bank numbers, passwords, and credentials.',
            'common_problems' => 'The AI may create deadlines, decide coverage, or recommend medical action. It may also merge provider and insurer responses. Keep the timeline administrative and source-specific.',
            'recovery_guidance' => 'If the timeline includes invented deadlines, change them to placeholders for verified dates. If it gives advice beyond billing organization, remove it.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are building a medical bill case timeline and follow-up tracker. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never give medical advice, invent insurance coverage, legal rights, plan terms, deadlines, claim decisions, or billing outcomes.

Approved inquiry or appeal letter:
{{artifact:draft-appeal-or-inquiry-letter}}

Approved call script:
{{artifact:draft-billing-call-script}}

Approved verification checklist:
{{artifact:build-verification-checklist}}

Questions or goals:
{{questions_or_goals}}
Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Case timeline
A source-specific timeline with dates, contacts, documents, placeholders, and unknowns.

## Follow-up tracker
A table for calls, submissions, reference numbers, requested documents, responses, and next actions.

## Next verification steps
Five to seven next steps that require official confirmation or qualified advice where appropriate.

## Final privacy and advice reminders
Redaction, safe-channel, and advice-boundary reminders for the completed kit.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Case timeline
- [VISIT-DATE]: Urgent care visit with outside lab charge.
- June 10: Provider bill issued for $642.30.
- [DATE NEEDED]: Check provider billing for claim submission status.
- [DATE NEEDED]: Check insurer for claim or EOB status.
- [DATE NEEDED]: Request itemized bill and written explanation.
- [DATE NEEDED]: Review any financial assistance or payment plan options.

## Follow-up tracker
| Date | Source | Method | Reference ID | Documents requested or sent | Response summary | Next action |
| --- | --- | --- | --- | --- | --- | --- |
| [DATE] | [Provider/insurer/lab] | [Phone/portal/mail] | [REFERENCE-ID] | [DOCUMENTS] | [SUMMARY] | [NEXT STEP] |

## Next verification steps
1. Verify whether the provider submitted a claim.
2. Request claim ID or written explanation if no claim exists.
3. Request the itemized bill.
4. Ask insurer whether an EOB exists or is pending.
5. Verify any appeal, inquiry, payment plan, or financial assistance deadlines from official sources.
6. Ask for written confirmation of corrected balances or next steps.
7. Consider qualified billing, legal, benefits, or financial assistance help if deadlines, rights, or affordability concerns are significant.

## Final privacy and advice reminders
Use placeholders for MRNs, insurance IDs, claim IDs, patient accounts, SSNs, dates of birth, bank numbers, full card numbers, medical record numbers, passwords, and credentials. Share full identifiers only through verified required channels. This kit is not medical, financial, legal, tax, insurance, or lending advice and does not determine coverage, coding, eligibility, or what you owe.
MD,
            'output_sections' => $sections([
                ['case_timeline', 'Case timeline'],
                ['follow_up_tracker', 'Follow-up tracker'],
                ['next_verification_steps', 'Next verification steps'],
                ['final_privacy_and_advice_reminders', 'Final privacy and advice reminders'],
            ]),
            'checks' => [
                ['label' => 'Timeline separates sources', 'help' => 'The case timeline distinguishes provider, insurer, and lab follow-up.', 'evidence_sections' => ['case_timeline']],
                ['label' => 'Follow-up is trackable', 'help' => 'The table captures reference IDs, documents, and next actions.', 'evidence_sections' => ['follow_up_tracker']],
                ['label' => 'No coverage or medical decision is made', 'help' => 'Final reminders keep the kit inside administrative support.', 'evidence_sections' => ['final_privacy_and_advice_reminders']],
            ],
        ]),
    ],
];
