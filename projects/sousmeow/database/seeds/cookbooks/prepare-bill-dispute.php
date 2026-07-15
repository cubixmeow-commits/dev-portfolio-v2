<?php

declare(strict_types=1);

/**
 * Prepare a Bill Dispute - executable Cookbook for money-major-decisions.
 */

require_once __DIR__ . '/../money_helpers.php';

$factRule = SM_MONEY_FACT_RULE;
$disclaimer = SM_MONEY_DISCLAIMER;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'prepare-bill-dispute',
    'title'               => 'Prepare a Bill Dispute',
    'tagline'             => 'Organize the facts and write a clear dispute or negotiation request.',
    'description'         => 'Turn a confusing or disputed bill into a timeline, evidence checklist, call script, dispute letter, and escalation plan. This Cookbook helps organize facts and requests; it does not invent consumer-rights statutes, legal claims, account facts, or guaranteed outcomes. ' . sm_money_beginner_footer('a billing timeline, evidence checklist, call script, dispute letter, and escalation plan', 5, 'about 45 minutes'),
    'primary_category'    => 'money-major-decisions',
    'collections'         => ['money-major-decisions'],
    'audience'            => 'People preparing a careful bill dispute or negotiation request for a provider, vendor, landlord, utility, or other company',
    'outcome'             => 'billing timeline, evidence checklist, call script, dispute letter, and escalation plan',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'harbor',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 45,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 31,
    'stages' => [
        ['title' => 'Organize facts', 'summary' => 'Build a billing timeline from supplied facts and unknowns.'],
        ['title' => 'Prepare evidence and script', 'summary' => 'List supporting documents and draft a calm call script.'],
        ['title' => 'Write dispute', 'summary' => 'Draft a clear dispute or negotiation letter with placeholders.'],
        ['title' => 'Escalate carefully', 'summary' => 'Pack next steps, tracking notes, and boundaries.'],
    ],
    'fields' => [
        ['field_key' => 'provider_or_company', 'label' => 'Provider or company', 'type' => 'text', 'help' => 'Name the company, provider, landlord, utility, or biller. Use "unknown" if not sure.', 'placeholder' => 'e.g. North Star Internet', 'sample_value' => 'North Star Internet'],
        ['field_key' => 'account_reference_placeholder', 'label' => 'Account reference placeholder', 'type' => 'text', 'help' => sm_money_privacy_pantry_help('Use a placeholder instead of a full account, card, bank, or claim number.'), 'placeholder' => 'e.g. [ACCOUNT-LAST4] or [INVOICE-ID]', 'sample_value' => '[ACCOUNT-LAST4]'],
        ['field_key' => 'bill_summary', 'label' => 'Bill summary', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('Summarize the bill and charges. Redact private account details before pasting.'), 'placeholder' => 'Amount, bill date, due date, charge names, and any notices.', 'sample_value' => "Bill dated May 4 shows $248.70 due June 1.\nUsual monthly bill is about $89.99 before taxes and fees.\nThe bill includes a $129 equipment fee and a $25 late fee.\nAccount reference: [ACCOUNT-LAST4]."],
        ['field_key' => 'what_seems_wrong', 'label' => 'What seems wrong', 'type' => 'textarea', 'help' => 'Explain what you believe is inaccurate, unclear, unfair, duplicated, or negotiable. Mark guesses as guesses.', 'placeholder' => 'e.g. Equipment was returned; late fee may be due to autopay failure.', 'sample_value' => "The modem was returned at the store on April 18, but the bill includes an equipment fee.\nThe late fee may relate to an autopay card expiration that I updated on May 2.\nI do not know whether fees can be waived."],
        ['field_key' => 'actions_already_taken', 'label' => 'Actions already taken', 'type' => 'textarea', 'help' => 'List calls, chats, emails, payments, returns, confirmation numbers, and dates. Use placeholders for identifiers.', 'placeholder' => 'e.g. Called support on [date], chat transcript saved, receipt [RECEIPT-ID].', 'sample_value' => "Returned modem in person on April 18 and saved receipt [RECEIPT-ID].\nCalled support on May 8; representative said they would open a ticket but I did not receive a confirmation email.\nPaid the undisputed monthly service portion."],
        ['field_key' => 'desired_resolution', 'label' => 'Desired resolution', 'type' => 'textarea', 'help' => 'State the outcome you want, such as correction, fee waiver, payment plan, explanation, or documentation. Do not assume entitlement.', 'placeholder' => 'e.g. Remove equipment fee, review late fee, provide corrected statement.', 'sample_value' => 'Remove the equipment fee if return receipt verifies it, review or waive the late fee if possible, and send a corrected statement showing any remaining balance.'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Include deadlines, service risk, communication preferences, accessibility needs, or facts not to invent.', 'placeholder' => 'e.g. Need internet service active; prefer written confirmation.', 'sample_value' => 'I need service to remain active and prefer written confirmation. Do not cite statutes unless I provide verified official sources.'],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the dispute should sound.', 'options' => ['Calm and firm', 'Polite and concise', 'Detailed and factual', 'Warm but persistent'], 'sample_value' => 'Calm and firm'],
    ],
    'recipes' => [
        sm_money_recipe([
            'stage_position' => 1,
            'slug' => 'build-billing-timeline',
            'title' => 'Build billing timeline',
            'summary' => 'Arrange the bill, issue, prior actions, and unknowns into a factual timeline.',
            'why_it_matters' => 'A dispute is easier to explain when dates, documents, charges, and prior contacts are in order. This step keeps assumptions separate from facts and protects against invented consumer rights or legal claims. It also creates a neutral record the user can correct before contacting the company.',
            'unlocks_text' => 'Approving unlocks the evidence checklist and call script.',
            'before_you_begin' => 'Gather bill dates, due dates, receipts, ticket numbers, and call notes. Replace SSNs, bank numbers, cards, insurance IDs, medical record numbers, passwords, and account numbers with placeholders such as [ACCOUNT-LAST4] or [RECEIPT-ID].',
            'common_problems' => 'The AI may infer why a fee appeared, invent a legal right, or treat a guess as fact. It may also omit important dates. Keep the timeline factual and label unknowns.',
            'recovery_guidance' => 'If the timeline includes unsupported motives or laws, rerun with "facts only." If dates are missing, add placeholder rows for unknown dates rather than guessing.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are organizing a billing dispute timeline. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent consumer-rights statutes, legal claims, account facts, or company policy. Mark unknowns clearly.

Provider or company: {{provider_or_company}}
Account reference placeholder: {{account_reference_placeholder}}

Bill summary:
{{bill_summary}}

What seems wrong:
{{what_seems_wrong}}

Actions already taken:
{{actions_already_taken}}

Desired resolution:
{{desired_resolution}}

Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Billing timeline
A dated or sequenced timeline using only supplied facts. Use [DATE NEEDED] where dates are missing.

## Dispute summary
Two or three sentences explaining the issue and requested review in plain language.

## Facts versus assumptions
Separate confirmed facts from guesses, unclear points, and unknowns.

## Privacy redaction check
Remind the user to use placeholders for SSNs, bank numbers, cards, insurance IDs, medical record numbers, passwords, and account credentials.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Billing timeline
- April 18: Modem returned in person; receipt saved as [RECEIPT-ID].
- May 2: Autopay card updated.
- May 4: Bill dated with $248.70 due June 1, including a $129 equipment fee and a $25 late fee.
- May 8: Support call completed; representative said a ticket would be opened, but no confirmation email was received.
- [DATE NEEDED]: Undisputed monthly service portion was paid.

## Dispute summary
The customer is asking North Star Internet to review an equipment fee and late fee on account [ACCOUNT-LAST4]. The requested resolution is to remove the equipment fee if the return receipt verifies the return, review or waive the late fee if possible, and provide a corrected statement.

## Facts versus assumptions
Confirmed facts: the bill amount, bill date, due date, listed fees, modem return receipt placeholder, support call, and desired written confirmation. Assumptions or unknowns: why the equipment fee appeared, whether the late fee can be waived, whether the support ticket was actually opened, and any consumer-rights statutes or company policies not supplied.

## Privacy redaction check
Use placeholders for SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, account credentials, account numbers, receipts, and ticket numbers before using external AI.
MD,
            'output_sections' => $sections([
                ['billing_timeline', 'Billing timeline'],
                ['dispute_summary', 'Dispute summary'],
                ['facts_versus_assumptions', 'Facts versus assumptions'],
                ['privacy_redaction_check', 'Privacy redaction check'],
            ]),
            'checks' => [
                ['label' => 'Timeline uses supplied facts', 'help' => 'Dates and events are not invented.', 'evidence_sections' => ['billing_timeline']],
                ['label' => 'Assumptions are labeled', 'help' => 'Unclear policies, motives, and statutes remain unknown.', 'evidence_sections' => ['facts_versus_assumptions']],
                ['label' => 'Private identifiers are redacted', 'help' => 'The privacy section teaches placeholder use.', 'evidence_sections' => ['privacy_redaction_check']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'assemble-evidence-checklist',
            'title' => 'Assemble evidence checklist',
            'summary' => 'List the documents, screenshots, receipts, and notes that can support the dispute.',
            'why_it_matters' => 'Evidence helps keep a dispute specific and reduces back-and-forth. This step identifies what the user already has, what is missing, and what should be requested. It avoids pretending that a missing document exists or that a law applies.',
            'unlocks_text' => 'Approving unlocks the call script.',
            'before_you_begin' => 'Collect receipts, bills, screenshots, emails, chats, payment records, and call notes you can safely reference. Redact full account numbers, cards, bank details, passwords, and private identifiers.',
            'common_problems' => 'The AI may treat a receipt placeholder as proof of what the receipt says or demand documents the user cannot access. It should instead create a practical checklist and note what each item would help verify.',
            'recovery_guidance' => 'If the checklist assumes evidence content, revise it to say "if the receipt shows..." If it is too long, ask for priority evidence first.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are assembling an evidence checklist for a bill dispute. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Do not invent consumer rights, statutes, company policies, document contents, or account details.

Approved billing timeline:
{{artifact:build-billing-timeline}}

Provider or company: {{provider_or_company}}
Account reference placeholder: {{account_reference_placeholder}}

Actions already taken:
{{actions_already_taken}}

Desired resolution:
{{desired_resolution}}

Produce Markdown with these exact headings:

## Evidence checklist
List evidence to gather, why it matters, and whether it appears supplied, missing, or needs verification.

## Priority documents
Name the three to five most important items to have before calling or writing.

## Information to request
List confirmation numbers, policies, itemized charges, or written explanations to request without citing unsupplied laws.

## Evidence handling notes
Give privacy and organization tips, including placeholder redaction.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Evidence checklist
- Current bill dated May 4: supplied in summary; verifies amount, due date, equipment fee, and late fee.
- Modem return receipt [RECEIPT-ID]: appears available; verify it shows return date, equipment identifier if safe to share, and store location.
- Payment record for undisputed service portion: supplied generally; add date, amount, and safe placeholder.
- May 8 support call notes: supplied generally; add time, representative name if known, and ticket number if received.
- Written confirmation or corrected statement: missing; request from company.

## Priority documents
1. Current bill.
2. Equipment return receipt [RECEIPT-ID].
3. Payment record for undisputed amount.
4. Notes from the May 8 call.
5. Any chat or email confirmation from the company.

## Information to request
- Itemized explanation of the equipment fee.
- Status of any ticket from the May 8 call.
- Written confirmation of any adjustment or remaining balance.
- Company policy or account note explaining whether the late fee can be reviewed.

## Evidence handling notes
Save copies in one folder with clear names and dates. Redact SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, and full account credentials. Use placeholders such as [ACCOUNT-LAST4], [RECEIPT-ID], and [TICKET-ID].
MD,
            'output_sections' => $sections([
                ['evidence_checklist', 'Evidence checklist'],
                ['priority_documents', 'Priority documents'],
                ['information_to_request', 'Information to request'],
                ['evidence_handling_notes', 'Evidence handling notes'],
            ]),
            'checks' => [
                ['label' => 'Evidence is not invented', 'help' => 'The checklist distinguishes supplied, missing, and verification-needed items.', 'evidence_sections' => ['evidence_checklist']],
                ['label' => 'Priority is clear', 'help' => 'The user can gather the most important items first.', 'evidence_sections' => ['priority_documents']],
                ['label' => 'Requests avoid fake legal claims', 'help' => 'Information requests do not cite unsupplied statutes.', 'evidence_sections' => ['information_to_request']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'draft-call-script',
            'title' => 'Draft call script',
            'summary' => 'Prepare a concise call opener, questions, and note-taking structure.',
            'why_it_matters' => 'A call can go better when the user has a calm summary and specific asks. This step creates language for requesting review, documentation, and next steps without escalating too early or making unsupported legal claims. It also helps the user capture the outcome in writing.',
            'unlocks_text' => 'Approving unlocks the dispute letter.',
            'before_you_begin' => 'Have the timeline, priority evidence, account placeholder, and desired resolution nearby. Decide what private details you will not say aloud or paste into notes unless required and safe.',
            'common_problems' => 'The AI may make the script adversarial, cite made-up rights, or promise the company must waive a fee. It may also forget to ask for confirmation numbers. Keep the script calm, factual, and verification-focused.',
            'recovery_guidance' => 'If the script sounds threatening, ask for a polite firm version. If it cites laws you did not supply, remove them and request official policy or written explanation instead.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are drafting a bill dispute call script. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Do not invent consumer-rights statutes, legal rights, company policy, or guaranteed fee waivers.

Approved timeline:
{{artifact:build-billing-timeline}}

Approved evidence checklist:
{{artifact:assemble-evidence-checklist}}

Provider or company: {{provider_or_company}}
Account reference placeholder: {{account_reference_placeholder}}
Desired resolution:
{{desired_resolution}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Call opener
A short script introducing the issue, account placeholder, and requested review.

## Questions to ask
Specific questions about the charges, documents, policy, confirmation numbers, and next steps.

## If the answer is no
Calm language for asking for explanation, documentation, supervisor review, or another appropriate next step without threats.

## Call notes template
A fill-in template for date, time, representative, reference number, promises made, and next action.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Call opener
Hello, I am calling about account [ACCOUNT-LAST4] and the bill dated May 4. I would like help reviewing a $129 equipment fee and a $25 late fee. I returned the modem on April 18 and have receipt [RECEIPT-ID], and I would like to understand what documentation you need to review the charges.

## Questions to ask
- Can you see the April 18 equipment return on the account?
- What does the $129 equipment fee refer to?
- What documentation should I send to verify the return?
- Was a ticket opened after my May 8 call? If yes, what is the ticket number?
- Can the late fee be reviewed or waived? If not, what is the policy or reason?
- Can you send written confirmation of any adjustment or remaining balance?
- What is the next review date or expected response time?

## If the answer is no
I understand you may not be able to adjust it on this call. Could you please explain the reason, note the account that I disputed the fee, and tell me what documentation or next review option is available? If supervisor review is available, I would like to request it calmly and receive a reference number.

## Call notes template
- Date and time:
- Representative name or ID:
- Account placeholder used:
- Reference or ticket number:
- Documents discussed:
- Company explanation:
- Any adjustment offered:
- Written confirmation promised:
- Next action and deadline:
MD,
            'output_sections' => $sections([
                ['call_opener', 'Call opener'],
                ['questions_to_ask', 'Questions to ask'],
                ['if_the_answer_is_no', 'If the answer is no'],
                ['call_notes_template', 'Call notes template'],
            ]),
            'checks' => [
                ['label' => 'Script is factual and calm', 'help' => 'The opener requests review without unsupported threats.', 'evidence_sections' => ['call_opener']],
                ['label' => 'Questions seek verifiable information', 'help' => 'The questions request policy, documentation, and confirmation.', 'evidence_sections' => ['questions_to_ask']],
                ['label' => 'Notes capture follow-up evidence', 'help' => 'The template records reference numbers and promises.', 'evidence_sections' => ['call_notes_template']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'write-dispute-letter',
            'title' => 'Write dispute letter',
            'summary' => 'Draft a clear written dispute or negotiation request with evidence placeholders.',
            'why_it_matters' => 'A written request creates a record and can be easier to review than a phone call. This step turns the timeline and desired resolution into a concise letter that includes attachments, asks for confirmation, and avoids invented legal claims. It leaves placeholders for facts the user must verify.',
            'unlocks_text' => 'Approving unlocks the escalation plan.',
            'before_you_begin' => 'Confirm the correct mailing address, email address, portal, or upload instructions outside this tool. Attach only documents you are comfortable sharing and redact sensitive identifiers.',
            'common_problems' => 'The AI may cite statutes, threaten regulators, or state rights that were not supplied. It may also include too much emotion. Keep it clear, documented, and bounded.',
            'recovery_guidance' => 'If the letter cites unknown laws, replace them with a request for written explanation or official policy. If it is too long, ask for a one-page version.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are drafting a written bill dispute or negotiation request. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent consumer-rights statutes, legal claims, account facts, company policy, or guaranteed outcomes. Mark unknowns.

Approved timeline:
{{artifact:build-billing-timeline}}

Approved evidence checklist:
{{artifact:assemble-evidence-checklist}}

Provider or company: {{provider_or_company}}
Account reference placeholder: {{account_reference_placeholder}}
Desired resolution:
{{desired_resolution}}
Constraints or concerns:
{{constraints_or_concerns}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Dispute letter
A complete concise letter with bracketed placeholders for send date, address or portal, attachments, and contact details. Do not cite laws unless supplied.

## Attachments to include
List supporting documents by safe placeholder name and what each may verify.

## Unknowns to resolve before sending
List addresses, deadlines, missing reference numbers, policy details, or legal questions that require verification.

## Sending checklist
Five to seven checks for facts, attachments, privacy, tone, and proof of submission.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Dispute letter
[Send date]

To North Star Internet:

Re: Account [ACCOUNT-LAST4], bill dated May 4

I am writing to dispute or request review of two charges on my May 4 bill: a $129 equipment fee and a $25 late fee. I returned the modem in person on April 18 and have receipt [RECEIPT-ID]. I also called support on May 8 and was told a ticket would be opened, but I have not received written confirmation.

Please review the equipment fee against the return receipt and remove it if the return was properly recorded. Please also review whether the late fee can be waived or explained in writing. I have paid the undisputed monthly service portion and would like service to remain active while this review is pending.

Please send a corrected statement or written explanation of any remaining balance, along with any ticket or reference number for this review.

Thank you,
[Your name]
[Preferred contact method]

## Attachments to include
- Current bill dated May 4: verifies amount, due date, and disputed charges.
- Receipt [RECEIPT-ID]: may verify modem return date and location.
- Payment confirmation [PAYMENT-ID]: verifies payment of the undisputed service portion if available.
- Call notes from May 8: records prior contact and requested ticket.

## Unknowns to resolve before sending
- Correct dispute mailing address, email address, or portal.
- Whether a ticket number exists for the May 8 call.
- Any company policy for equipment returns or late-fee review.
- Any legal rights or deadlines, which should be verified through official sources if relevant.

## Sending checklist
- [ ] Account number is redacted or limited to the required safe reference.
- [ ] Full card, bank, SSN, password, insurance ID, and medical record numbers are not included.
- [ ] Attachments are named clearly and redacted.
- [ ] The letter does not cite unsupplied laws or policies.
- [ ] The requested resolution is specific.
- [ ] Proof of submission is saved.
MD,
            'output_sections' => $sections([
                ['dispute_letter', 'Dispute letter'],
                ['attachments_to_include', 'Attachments to include'],
                ['unknowns_to_resolve_before_sending', 'Unknowns to resolve before sending'],
                ['sending_checklist', 'Sending checklist'],
            ]),
            'checks' => [
                ['label' => 'Letter stays grounded', 'help' => 'The dispute letter uses supplied facts and placeholders.', 'evidence_sections' => ['dispute_letter']],
                ['label' => 'Attachments support the claim', 'help' => 'Each attachment has a purpose and safe placeholder.', 'evidence_sections' => ['attachments_to_include']],
                ['label' => 'Unknown rights stay unknown', 'help' => 'Legal questions and company policies are flagged for verification.', 'evidence_sections' => ['unknowns_to_resolve_before_sending']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 4,
            'slug' => 'pack-escalation-plan',
            'title' => 'Pack escalation plan',
            'summary' => 'Create a careful follow-up and escalation tracker if the first request does not resolve the bill.',
            'why_it_matters' => 'Escalation works best when it is documented, calm, and based on confirmed next steps. This final step helps the user track responses, decide when to follow up, and identify official sources to verify without inventing rights or deadlines. It does not promise a refund, waiver, or legal outcome.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your bill dispute kit.',
            'before_you_begin' => 'Save the call script, dispute letter, attachments, and proof of submission. Confirm any deadlines or official complaint options from current official sources before acting.',
            'common_problems' => 'The AI may jump to regulator complaints, legal threats, or consumer rights language without evidence. It may also invent timelines. Keep escalation conditional and verification-based.',
            'recovery_guidance' => 'If the plan includes unsupplied deadlines or rights, replace them with "verify from official source." If it is too aggressive, ask for a service-preserving version.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are packing a careful bill dispute escalation plan. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent consumer-rights statutes, complaint deadlines, legal rights, company policy, or guaranteed outcomes.

Approved dispute letter:
{{artifact:write-dispute-letter}}

Approved call script:
{{artifact:draft-call-script}}

Provider or company: {{provider_or_company}}
Desired resolution:
{{desired_resolution}}
Constraints or concerns:
{{constraints_or_concerns}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Escalation plan
Step-by-step follow-up plan with placeholders for dates and reference numbers. Keep it calm and conditional.

## Response tracker
A fill-in tracker for submissions, calls, documents sent, replies, and next actions.

## Official sources to verify
List types of official sources the user may check, without inventing rights, laws, deadlines, or text.

## Final caution checklist
Checks for privacy, facts, tone, service risk, and advice boundaries.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Escalation plan
1. Submit the dispute letter through the verified company channel and save proof of submission.
2. Record [SUBMISSION-DATE], [METHOD], and [REFERENCE-ID].
3. If no response is received by [FOLLOW-UP-DATE YOU CHOOSE AFTER VERIFYING TIMELINES], call using the script and ask for status.
4. If the answer is unclear, request written explanation, supervisor review if available, and a reference number.
5. Before using any external complaint or legal option, verify current official instructions and deadlines from the relevant official source.

## Response tracker
| Date | Method | Person or department | Reference ID | Documents sent | Response summary | Next action |
| --- | --- | --- | --- | --- | --- | --- |
| [DATE] | [Portal/email/mail/phone] | [NAME OR TEAM] | [REFERENCE-ID] | [DOCUMENTS] | [SUMMARY] | [NEXT STEP] |

## Official sources to verify
- The company's official dispute or customer support instructions.
- Current biller policy documents or account terms if available.
- Relevant government consumer information pages for the bill type and location, if applicable.
- A qualified legal or consumer advisor if rights, deadlines, or legal strategy matter.

## Final caution checklist
- [ ] No SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, or account credentials are exposed.
- [ ] All dates, charges, and reference numbers are verified or marked as placeholders.
- [ ] No unsupplied statutes or consumer rights are cited.
- [ ] Service-risk concerns are considered before withholding payment or escalating.
- [ ] This plan is not financial, legal, medical, tax, or lending advice.
MD,
            'output_sections' => $sections([
                ['escalation_plan', 'Escalation plan'],
                ['response_tracker', 'Response tracker'],
                ['official_sources_to_verify', 'Official sources to verify'],
                ['final_caution_checklist', 'Final caution checklist'],
            ]),
            'checks' => [
                ['label' => 'Escalation is conditional', 'help' => 'The plan uses placeholders and verified channels.', 'evidence_sections' => ['escalation_plan']],
                ['label' => 'Responses can be tracked', 'help' => 'The tracker captures evidence for follow-up.', 'evidence_sections' => ['response_tracker']],
                ['label' => 'Rights are not invented', 'help' => 'Official sources are verification targets, not fabricated legal claims.', 'evidence_sections' => ['official_sources_to_verify', 'final_caution_checklist']],
            ],
        ]),
    ],
];
