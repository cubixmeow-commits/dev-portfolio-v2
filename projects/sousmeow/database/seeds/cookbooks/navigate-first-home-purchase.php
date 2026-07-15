<?php

declare(strict_types=1);

/**
 * Navigate a First Home Purchase - executable Cookbook for money-major-decisions.
 */

require_once __DIR__ . '/../money_helpers.php';

$factRule = SM_MONEY_FACT_RULE;
$disclaimer = SM_MONEY_DISCLAIMER;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'navigate-first-home-purchase',
    'title'               => 'Navigate a First Home Purchase',
    'tagline'             => 'Understand the homebuying path and prepare questions for lenders and agents.',
    'description'         => 'Map the first-time homebuying path, organize affordability notes, compare mortgage information, and prepare lender, realtor, viewing, and closing questions. Worksheets in this Cookbook are planning aids, not underwriting, mortgage approval, legal, tax, or financial advice. Verify programs, rates, eligibility, and consumer protections with official sources, HUD-approved housing counseling, and qualified professionals. ' . sm_money_beginner_footer('a roadmap, affordability worksheet, mortgage comparison notes, lender questions, realtor questions, viewing checklist, and closing prep checklist', 7, 'about 70 minutes'),
    'primary_category'    => 'money-major-decisions',
    'collections'         => ['money-major-decisions'],
    'audience'            => 'First-time homebuyers who want a clear roadmap and careful questions before speaking with lenders and agents',
    'outcome'             => 'roadmap, affordability worksheet, mortgage comparison notes, lender questions, realtor questions, viewing checklist, and closing prep checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'harbor',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 70,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 33,
    'stages' => [
        ['title' => 'Map the path', 'summary' => 'Understand the homebuying roadmap and what to verify.'],
        ['title' => 'Organize money inputs', 'summary' => 'Build planning worksheets and compare mortgage notes without inventing approvals.'],
        ['title' => 'Prepare questions', 'summary' => 'Create lender, realtor, and viewing questions for real conversations.'],
        ['title' => 'Pack closing prep', 'summary' => 'Build a closing checklist and official-source verification plan.'],
    ],
    'fields' => [
        ['field_key' => 'location_or_market_notes', 'label' => 'Location or market notes', 'type' => 'textarea', 'help' => 'Use broad location or market context. Do not paste private addresses unless needed and safe.', 'placeholder' => 'e.g. Looking around Cleveland suburbs; school commute matters; market feels competitive.', 'sample_value' => "Looking in the River City metro area, mostly older starter homes and townhomes.\nCommute under 40 minutes matters.\nWe have seen listings move quickly, but we do not know the current market data."],
        ['field_key' => 'household_income_band', 'label' => 'Household income band', 'type' => 'text', 'help' => sm_money_privacy_pantry_help('Use a broad band or planning amount you are comfortable sharing. Do not paste pay stubs, tax forms, SSNs, or bank details.'), 'placeholder' => 'e.g. $80k-$90k gross household income, unverified planning band', 'sample_value' => '$80k-$90k gross household income band for planning only'],
        ['field_key' => 'monthly_obligations', 'label' => 'Monthly obligations', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('List recurring obligations you want to consider. Use placeholders for account numbers.'), 'placeholder' => 'Student loans, car payment, credit cards, childcare, insurance, support payments.', 'sample_value' => "Car payment: $360\nStudent loan: $180\nCredit card minimums: $95 across [CARD-LAST4]\nChildcare: $700\nUtilities and insurance vary"],
        ['field_key' => 'savings_and_down_payment_notes', 'label' => 'Savings and down payment notes', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('Use rounded or planning amounts if preferred. Do not paste bank account numbers or statements.'), 'placeholder' => 'Savings available, emergency fund boundary, gift funds if verified, closing cost concerns.', 'sample_value' => "Savings for down payment and closing costs: about $28,000.\nWant to keep at least $8,000 as emergency savings.\nNo verified gift funds.\nClosing costs are unclear."],
        ['field_key' => 'must_haves', 'label' => 'Must-haves', 'type' => 'textarea', 'help' => 'List non-negotiable home or location needs.', 'placeholder' => 'Bedrooms, commute, accessibility, school, pet needs, safety, repair limits.', 'sample_value' => "At least 2 bedrooms.\nCommute under 40 minutes.\nNo major foundation or roof problems if avoidable.\nSpace for one home office.\nPet-friendly HOA if townhome."],
        ['field_key' => 'nice_to_haves', 'label' => 'Nice-to-haves', 'type' => 'textarea', 'help' => 'List preferences that can flex.', 'placeholder' => 'Yard, garage, extra bedroom, updated kitchen, transit access.', 'sample_value' => "Small yard.\nGarage or covered parking.\nUpdated kitchen.\nNear a park.\nExtra half bath."],
        ['field_key' => 'known_constraints', 'label' => 'Known constraints', 'type' => 'textarea', 'help' => 'Include timing, credit, cash, market, work, family, or uncertainty constraints. Do not ask the AI to determine approval.', 'placeholder' => 'e.g. Lease ends in six months; credit score estimate not verified; want counseling.', 'sample_value' => 'Lease ends in six months. Credit score estimate is unverified. We want to understand the process before applying. Do not predict approval, rates, or program eligibility.'],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the roadmap and questions should sound.', 'options' => ['Calm and practical', 'Detailed and careful', 'Warm and beginner-friendly', 'Direct and organized'], 'sample_value' => 'Warm and beginner-friendly'],
    ],
    'recipes' => [
        sm_money_recipe([
            'stage_position' => 1,
            'slug' => 'map-homebuying-roadmap',
            'title' => 'Map homebuying roadmap',
            'summary' => 'Create a beginner roadmap from early preparation through closing and move-in.',
            'why_it_matters' => 'First-time buyers often face a confusing sequence of lenders, agents, inspections, offers, underwriting, closing, and move-in tasks. This step gives a plain roadmap while separating general process education from decisions that require official sources or professionals. It avoids inventing rates, approvals, eligibility, or legal rights.',
            'unlocks_text' => 'Approving unlocks the affordability worksheet and mortgage notes.',
            'before_you_begin' => 'Gather broad location notes, income band, obligations, savings notes, and home preferences. Redact SSNs, bank numbers, card numbers, insurance IDs, medical record numbers, passwords, account credentials, and loan account numbers. Use official sources or HUD-approved housing counseling for program-specific guidance.',
            'common_problems' => 'The AI may predict approval, invent local programs, quote rates, or assume market facts. It may also skip inspection, appraisal, title, and closing steps. Keep the roadmap general and verification-focused.',
            'recovery_guidance' => 'If the roadmap includes specific rates or eligibility, replace them with verification tasks. If it sounds like legal or lending advice, ask for process education only.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are creating a first-time homebuyer roadmap. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent mortgage rates, approvals, credit decisions, program eligibility, consumer rights, laws, taxes, or local market facts. Encourage verification through official sources, HUD-approved housing counseling, and qualified professionals without copying proprietary text.

Location or market notes:
{{location_or_market_notes}}

Household income band:
{{household_income_band}}

Savings and down payment notes:
{{savings_and_down_payment_notes}}

Must-haves:
{{must_haves}}

Nice-to-haves:
{{nice_to_haves}}

Known constraints:
{{known_constraints}}

Produce Markdown with these exact headings:

## Homebuying roadmap
A beginner-friendly sequence from preparation through closing and move-in. Keep it general and verification-focused.

## Decisions to prepare for
List key decisions the buyer may need to make, without deciding them.

## Official sources to verify
List types of official or qualified sources to check, including HUD-approved housing counseling, lender disclosures, local government sources, and qualified legal or tax help when needed.

## Boundaries and privacy
State that worksheets are planning aids, not underwriting; include privacy redaction reminders.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Homebuying roadmap
1. Organize finances, savings boundaries, monthly obligations, and home priorities.
2. Learn the mortgage process and gather questions before applying.
3. Speak with lenders or a HUD-approved housing counselor to understand options, documents, and next steps.
4. Choose an agent or buyer representative after asking about experience, communication, and local process.
5. Tour homes using a must-have and repair-risk checklist.
6. If making an offer, review contingencies, inspection, appraisal, title, insurance, and closing timeline with qualified professionals.
7. Track underwriting requests, closing disclosures, final walkthrough, funds transfer safety, and move-in tasks.

## Decisions to prepare for
- Which monthly payment range feels sustainable after obligations and emergency savings.
- Whether to apply now or keep preparing.
- Which lender questions matter most before preapproval.
- Which must-haves are non-negotiable.
- Which inspection or repair risks would be dealbreakers.
- Which official sources to use for programs, rights, taxes, and local rules.

## Official sources to verify
- HUD-approved housing counseling or official HUD counseling search tools.
- Lender disclosures and verified lender representatives.
- Local government or housing agency program pages.
- Official tax assessor, recorder, and property record sources where applicable.
- Qualified real estate attorney, tax professional, or insurance professional when needed.

## Boundaries and privacy
This roadmap is process education and planning support, not underwriting, mortgage approval, legal, tax, financial, or insurance advice. Do not paste SSNs, bank account numbers, full card numbers, insurance IDs, medical record numbers, passwords, pay stubs, tax returns, or account credentials into external AI. Use placeholders such as [BANK-LAST4], [LOAN-LAST4], and [DOCUMENT-NAME].
MD,
            'output_sections' => $sections([
                ['homebuying_roadmap', 'Homebuying roadmap'],
                ['decisions_to_prepare_for', 'Decisions to prepare for'],
                ['official_sources_to_verify', 'Official sources to verify'],
                ['boundaries_and_privacy', 'Boundaries and privacy'],
            ]),
            'checks' => [
                ['label' => 'Roadmap stays general', 'help' => 'The process sequence does not predict approval or eligibility.', 'evidence_sections' => ['homebuying_roadmap', 'boundaries_and_privacy']],
                ['label' => 'Decisions are prepared, not made', 'help' => 'The list helps buyers think without telling them what to choose.', 'evidence_sections' => ['decisions_to_prepare_for']],
                ['label' => 'Official verification is included', 'help' => 'HUD counseling and other official sources are named.', 'evidence_sections' => ['official_sources_to_verify']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'build-affordability-worksheet',
            'title' => 'Build affordability worksheet',
            'summary' => 'Organize income band, obligations, savings boundaries, and payment comfort as planning inputs.',
            'why_it_matters' => 'Affordability is not just a lender calculation; it also involves cash flow, emergency savings, maintenance, insurance, taxes, repairs, and comfort with risk. This worksheet is a planning aid only and cannot determine approval or safe price. It keeps unknowns and verification tasks visible.',
            'unlocks_text' => 'Approving unlocks mortgage comparison notes.',
            'before_you_begin' => 'Use broad or rounded figures if preferred. Redact SSNs, bank numbers, card numbers, passwords, tax identifiers, loan account numbers, and credentials. Do not ask this worksheet to underwrite you.',
            'common_problems' => 'The AI may calculate a purchase price, payment, debt-to-income ratio, or approval from incomplete data. It may also invent taxes, insurance, HOA dues, or rates. Use supplied planning inputs only and mark missing categories.',
            'recovery_guidance' => 'If the worksheet names a maximum price or approval, rewrite it as a question for lender or counselor. If it calculates with unknown rates, remove the calculation and list needed inputs.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are building a first-home affordability planning worksheet. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
This worksheet is a planning aid, not underwriting, mortgage approval, credit decision, legal, tax, financial, or insurance advice. Never invent rates, taxes, insurance, HOA dues, closing costs, program eligibility, or approval.

Household income band:
{{household_income_band}}

Monthly obligations:
{{monthly_obligations}}

Savings and down payment notes:
{{savings_and_down_payment_notes}}

Known constraints:
{{known_constraints}}

Approved roadmap:
{{artifact:map-homebuying-roadmap}}

Produce Markdown with these exact headings:

## Affordability worksheet
Organize supplied income band, obligations, savings boundary, emergency reserve, and planning questions. Do not calculate approval.

## Monthly payment factors
List payment components to ask lenders about, such as principal and interest, taxes, insurance, HOA, mortgage insurance, utilities, repairs, and maintenance, without inventing amounts.

## Cash needed questions
List down payment, closing cost, moving cost, inspection, appraisal, reserves, and emergency fund questions to verify.

## Worksheet limits
Explain why this does not decide affordability, underwriting, eligibility, rates, or approval.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Affordability worksheet
- Planning income band: $80k-$90k gross household income, supplied for planning only.
- Monthly obligations: car payment $360, student loan $180, credit card minimums $95 across [CARD-LAST4], childcare $700, utilities and insurance vary.
- Savings: about $28,000 for down payment and closing costs.
- Emergency reserve boundary: keep at least $8,000 untouched if possible.
- Timing constraint: lease ends in six months.
- Credit score estimate: unverified, so it should not be used for approval predictions.

## Monthly payment factors
Ask lenders to explain principal and interest, property taxes, homeowners insurance, HOA dues if any, mortgage insurance if any, estimated utilities, repair reserve, maintenance, and any payment changes over time. Do not use unsupplied rates or tax figures.

## Cash needed questions
- How much cash would be needed for down payment in each loan option?
- What closing costs should be estimated, and when will official disclosures arrive?
- What inspection, appraisal, earnest money, moving, utility setup, and repair costs should be planned for?
- How much emergency savings should remain after closing?
- Are any assistance programs available, and where can eligibility be verified officially?

## Worksheet limits
This worksheet does not determine what the household can afford, whether a lender will approve the loan, what rate is available, or whether any program applies. It is a planning aid to prepare lender, counselor, tax, legal, and insurance questions.
MD,
            'output_sections' => $sections([
                ['affordability_worksheet', 'Affordability worksheet'],
                ['monthly_payment_factors', 'Monthly payment factors'],
                ['cash_needed_questions', 'Cash needed questions'],
                ['worksheet_limits', 'Worksheet limits'],
            ]),
            'checks' => [
                ['label' => 'Worksheet does not underwrite', 'help' => 'The output refuses approval, rate, and affordability decisions.', 'evidence_sections' => ['worksheet_limits']],
                ['label' => 'Payment factors are complete enough for questions', 'help' => 'The user can ask about taxes, insurance, HOA, repairs, and maintenance.', 'evidence_sections' => ['monthly_payment_factors']],
                ['label' => 'Cash questions are visible', 'help' => 'Closing, moving, inspection, reserves, and emergency savings are included.', 'evidence_sections' => ['cash_needed_questions']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'compare-mortgage-notes',
            'title' => 'Compare mortgage notes',
            'summary' => 'Create a neutral mortgage comparison template for lender-provided information.',
            'why_it_matters' => 'Mortgage options can vary by rate, term, fees, points, mortgage insurance, closing costs, and program requirements. This step creates a comparison structure but does not invent offers, quote rates, or determine eligibility. It helps the buyer know what to ask and what to verify in official disclosures.',
            'unlocks_text' => 'Approving unlocks lender questions.',
            'before_you_begin' => 'Use only lender-provided or official information. Redact application numbers, SSNs, bank numbers, card numbers, tax IDs, passwords, and credentials. Do not paste proprietary lender documents unless you have permission and redact sensitive data.',
            'common_problems' => 'The AI may rank loan products without enough data, assume a rate, or say the buyer qualifies. It may also copy proprietary disclosure text. Keep notes as a neutral comparison template.',
            'recovery_guidance' => 'If the output selects a loan, ask it to return pros, cons, and questions only. If it quotes rates, replace them with placeholders for lender-provided rates.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are building mortgage comparison notes for a first-time homebuyer. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent rates, APRs, lender fees, approvals, credit decisions, program eligibility, consumer rights, laws, taxes, or insurance coverage. Use lender-provided placeholders only.

Approved affordability worksheet:
{{artifact:build-affordability-worksheet}}

Location or market notes:
{{location_or_market_notes}}

Known constraints:
{{known_constraints}}

Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Mortgage comparison notes
A neutral table or list for comparing lender-provided options. Use placeholders for rate, APR, term, payment, fees, points, mortgage insurance, closing costs, and conditions.

## Questions before comparing
List missing information needed before comparing offers.

## Tradeoffs to discuss
Name common tradeoffs to discuss with lenders or counselors without recommending one product.

## Verification reminders
Remind the user to rely on official lender disclosures, HUD-approved counseling, and qualified advice where needed.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Mortgage comparison notes
| Item | Option A | Option B | Notes to verify |
| --- | --- | --- | --- |
| Lender | [LENDER A] | [LENDER B] | Use verified lender names. |
| Loan type | [TYPE] | [TYPE] | Verify eligibility and requirements. |
| Rate/APR | [LENDER-PROVIDED] | [LENDER-PROVIDED] | Do not estimate from AI. |
| Term | [TERM] | [TERM] | Confirm fixed or adjustable features. |
| Estimated payment | [DISCLOSURE AMOUNT] | [DISCLOSURE AMOUNT] | Confirm included taxes, insurance, HOA, and mortgage insurance. |
| Closing costs | [DISCLOSURE AMOUNT] | [DISCLOSURE AMOUNT] | Compare official Loan Estimates. |
| Points or credits | [DETAILS] | [DETAILS] | Ask how they affect cost. |
| Conditions | [CONDITIONS] | [CONDITIONS] | Verify documents and timing. |

## Questions before comparing
- Are these official Loan Estimates or informal estimates?
- Does the payment include taxes, insurance, HOA, and mortgage insurance?
- Are there points, lender credits, or fees that change the comparison?
- Are any assistance programs included, and where is eligibility verified?
- What assumptions were used for credit, down payment, property type, and closing date?

## Tradeoffs to discuss
- Lower monthly payment versus higher upfront cost.
- Points or credits versus cash needed at closing.
- Fixed versus adjustable features if applicable.
- Mortgage insurance cost and duration.
- Program requirements, occupancy rules, property standards, and timelines.
- Comfort with cash reserves after closing.

## Verification reminders
Use official Loan Estimates, verified lender communications, HUD-approved housing counseling, and qualified legal, tax, or insurance professionals where needed. These notes do not determine approval, eligibility, rates, or which mortgage is best.
MD,
            'output_sections' => $sections([
                ['mortgage_comparison_notes', 'Mortgage comparison notes'],
                ['questions_before_comparing', 'Questions before comparing'],
                ['tradeoffs_to_discuss', 'Tradeoffs to discuss'],
                ['verification_reminders', 'Verification reminders'],
            ]),
            'checks' => [
                ['label' => 'Comparison uses placeholders', 'help' => 'Rates, fees, and approvals are not invented.', 'evidence_sections' => ['mortgage_comparison_notes']],
                ['label' => 'Missing info is surfaced', 'help' => 'The user knows what to ask before comparing.', 'evidence_sections' => ['questions_before_comparing']],
                ['label' => 'No product is recommended', 'help' => 'Tradeoffs are framed for discussion with verified sources.', 'evidence_sections' => ['tradeoffs_to_discuss', 'verification_reminders']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'prepare-lender-questions',
            'title' => 'Prepare lender questions',
            'summary' => 'Write questions for lenders about process, costs, documents, disclosures, and verification.',
            'why_it_matters' => 'A lender conversation can be more productive when the buyer knows what to ask before sharing sensitive documents. This step prepares questions about costs, documents, timing, assumptions, and disclosures while avoiding approval predictions. It also reminds the buyer to use secure channels for sensitive information.',
            'unlocks_text' => 'Approving unlocks realtor questions.',
            'before_you_begin' => 'Review the affordability worksheet and mortgage comparison notes. Do not paste SSNs, bank numbers, tax returns, pay stubs, passwords, or credentials into external AI.',
            'common_problems' => 'The AI may tell the buyer what they will qualify for or which lender to choose. It may also omit fraud-prevention and funds-transfer safety questions. Keep the list inquiry-based.',
            'recovery_guidance' => 'If the questions imply approval, rewrite as "what would you need to evaluate..." If private documents appear, replace them with document-name placeholders.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are preparing lender questions for a first-time homebuyer. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent rates, approvals, credit decisions, program eligibility, consumer rights, laws, taxes, or insurance coverage.

Approved affordability worksheet:
{{artifact:build-affordability-worksheet}}

Approved mortgage comparison notes:
{{artifact:compare-mortgage-notes}}

Known constraints:
{{known_constraints}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Lender questions
Questions about process, documents, loan options, costs, assumptions, disclosures, and timing.

## Documents to ask about
List document categories the lender may request and privacy-safe handling reminders. Do not request the user paste sensitive documents into AI.

## Cost and disclosure questions
Questions about Loan Estimates, closing costs, rate locks, points, credits, mortgage insurance, taxes, insurance, and payment components.

## Approval boundary reminders
State that only lenders can evaluate applications and that this worksheet is not underwriting.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Lender questions
- What information do you need to evaluate a preapproval request?
- Which loan types might be worth discussing for a first-time buyer, and where can eligibility be verified?
- What assumptions are you using for down payment, credit, property type, taxes, and insurance?
- What timeline should we expect from application through closing?
- How do underwriting conditions work, and how will you communicate document requests?
- What should we avoid doing financially while an application is in process?
- How do you help buyers compare official Loan Estimates?

## Documents to ask about
Ask which categories may be needed, such as pay stubs, W-2s or tax documents, bank statements, ID, debt statements, gift documentation if applicable, and employment verification. Share documents only through secure verified lender channels, not external AI. Use placeholders like [PAYSTUB], [BANK-STATEMENT], and [LOAN-LAST4] in planning notes.

## Cost and disclosure questions
- When will we receive an official Loan Estimate?
- Which costs are lender fees, third-party fees, prepaid items, escrow, taxes, insurance, or HOA-related?
- What is the difference between rate and APR in your disclosure?
- Are points or lender credits included?
- How does a rate lock work, and what happens if closing is delayed?
- Does the payment estimate include taxes, insurance, HOA dues, and mortgage insurance?
- What cash-to-close estimate should we verify before making offers?

## Approval boundary reminders
Only a lender can evaluate an application using verified documents and current criteria. This question list is not underwriting, mortgage approval, credit advice, legal advice, tax advice, or a promise of rates or eligibility.
MD,
            'output_sections' => $sections([
                ['lender_questions', 'Lender questions'],
                ['documents_to_ask_about', 'Documents to ask about'],
                ['cost_and_disclosure_questions', 'Cost and disclosure questions'],
                ['approval_boundary_reminders', 'Approval boundary reminders'],
            ]),
            'checks' => [
                ['label' => 'Questions prepare a real lender conversation', 'help' => 'The list covers process, documents, costs, assumptions, and timing.', 'evidence_sections' => ['lender_questions']],
                ['label' => 'Sensitive documents stay out of AI', 'help' => 'Document handling uses categories and secure-channel reminders.', 'evidence_sections' => ['documents_to_ask_about']],
                ['label' => 'Approval is not predicted', 'help' => 'Boundary reminders make underwriting limits explicit.', 'evidence_sections' => ['approval_boundary_reminders']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'prepare-realtor-questions',
            'title' => 'Prepare realtor questions',
            'summary' => 'Write questions for agents about representation, process, communication, offers, and local expertise.',
            'why_it_matters' => 'Agent fit matters because buyers need clear communication, local process knowledge, offer guidance, and coordination with lenders and inspectors. This step prepares questions without giving legal advice or assuming agency rules. It encourages buyers to verify representation terms and contracts before signing.',
            'unlocks_text' => 'Approving unlocks the viewing checklist.',
            'before_you_begin' => 'Review must-haves, nice-to-haves, location notes, and constraints. Do not paste private addresses, financial documents, or identifying information unless safe and necessary.',
            'common_problems' => 'The AI may invent local law, agent duties, commission rules, or market facts. It may also tell the buyer to sign or not sign a contract. Keep questions focused on explanation and verification.',
            'recovery_guidance' => 'If the output cites local rules or consumer rights, change them to "ask the agent or qualified professional to explain." If it chooses an agent, rewrite as evaluation criteria.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are preparing realtor or buyer-agent questions for a first-time homebuyer. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent laws, consumer rights, commission rules, representation duties, local market facts, property values, or legal advice.

Location or market notes:
{{location_or_market_notes}}

Must-haves:
{{must_haves}}

Nice-to-haves:
{{nice_to_haves}}

Known constraints:
{{known_constraints}}

Approved roadmap:
{{artifact:map-homebuying-roadmap}}

Produce Markdown with these exact headings:

## Realtor questions
Questions about experience, communication, representation, search strategy, offer process, inspections, contingencies, and coordination.

## Buyer representation topics
Topics to ask about before signing any agreement, without giving legal advice.

## Local process questions
Questions about local norms and market information the agent should support with current data or sources.

## Agent fit criteria
Neutral criteria for evaluating whether the agent relationship feels clear and supportive.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Realtor questions
- How often do you work with first-time buyers in this area and price range?
- How do you communicate about new listings, showings, offers, and deadlines?
- How do you help buyers balance must-haves, repair risk, commute, and budget boundaries?
- How do you explain offer terms, contingencies, inspections, appraisal, and closing timeline?
- How do you coordinate with lenders, inspectors, title, attorneys if used locally, and insurance contacts?
- How do you help buyers avoid rushing in a competitive market?

## Buyer representation topics
- What agreement would we be asked to sign, and when?
- What services, duties, duration, fees, or cancellation terms are included?
- How are agent compensation and potential conflicts explained?
- What should we review with a qualified legal professional if we do not understand the agreement?

## Local process questions
- What current data supports your view of the River City metro starter-home market?
- How quickly are similar listings moving, and how should we verify that?
- What inspection issues are common in older homes in this area?
- Are there local transfer taxes, HOA norms, attorney practices, or disclosure customs we should verify with official or qualified sources?

## Agent fit criteria
- Explains process without pressure.
- Respects must-haves and savings boundaries.
- Answers beginner questions clearly.
- Supports claims with current data or official sources.
- Encourages inspections and qualified advice where needed.
- Communicates promptly in the buyer's preferred style.
MD,
            'output_sections' => $sections([
                ['realtor_questions', 'Realtor questions'],
                ['buyer_representation_topics', 'Buyer representation topics'],
                ['local_process_questions', 'Local process questions'],
                ['agent_fit_criteria', 'Agent fit criteria'],
            ]),
            'checks' => [
                ['label' => 'Questions cover agent fit and process', 'help' => 'The list addresses communication, offers, inspections, and coordination.', 'evidence_sections' => ['realtor_questions']],
                ['label' => 'Representation terms are not assumed', 'help' => 'The output asks about agreements without giving legal advice.', 'evidence_sections' => ['buyer_representation_topics']],
                ['label' => 'Local facts require support', 'help' => 'Market and local process claims are framed as questions to verify.', 'evidence_sections' => ['local_process_questions']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'build-viewing-checklist',
            'title' => 'Build viewing checklist',
            'summary' => 'Create a home tour checklist that balances must-haves, repair risk, and follow-up questions.',
            'why_it_matters' => 'Tours can be emotional and fast. A checklist helps buyers notice must-haves, dealbreakers, repair concerns, HOA questions, commute realities, and items for inspection without acting as an inspector or legal advisor. It keeps observations separate from professional conclusions.',
            'unlocks_text' => 'Approving unlocks the closing prep checklist.',
            'before_you_begin' => 'Bring must-haves, nice-to-haves, constraints, and a way to capture notes. Do not rely on AI to evaluate structural, environmental, legal, or safety issues.',
            'common_problems' => 'The AI may diagnose foundation, roof, mold, electrical, or legal issues from descriptions. It may also tell buyers to waive inspection. Keep the checklist as observations and questions for qualified professionals.',
            'recovery_guidance' => 'If the output declares a problem safe or unsafe, rewrite as "ask inspector or qualified professional." If it pushes offers, remove the recommendation.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are building a first-home viewing checklist. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never give inspection, legal, tax, insurance, medical, or safety advice. Never invent property values, market facts, laws, consumer rights, or repair costs.

Location or market notes:
{{location_or_market_notes}}

Must-haves:
{{must_haves}}

Nice-to-haves:
{{nice_to_haves}}

Known constraints:
{{known_constraints}}

Approved realtor questions:
{{artifact:prepare-realtor-questions}}

Produce Markdown with these exact headings:

## Viewing checklist
A checkbox checklist for must-haves, layout, condition observations, systems to ask about, HOA or neighborhood notes, and commute fit.

## Follow-up questions
Questions for the agent, seller disclosures, inspector, lender, insurer, HOA, or local official source as relevant.

## Do-not-decide-on-tour list
Items that require inspection, legal review, insurance quotes, official records, or qualified advice.

## Viewing score notes
A simple note structure for comparing homes without making an offer recommendation.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Viewing checklist
- [ ] At least 2 bedrooms.
- [ ] Space for one home office.
- [ ] Commute appears under 40 minutes at relevant times, to be verified.
- [ ] Pet-friendly HOA if townhome.
- [ ] Roof, foundation, water stains, electrical panel, HVAC, plumbing, windows, and grading noted for inspector questions.
- [ ] Kitchen condition noted.
- [ ] Yard, garage or covered parking, park access, and half bath noted as nice-to-haves.
- [ ] Noise, parking, stairs, storage, and safety concerns observed.
- [ ] HOA rules, dues, rental restrictions, and pet rules requested if applicable.

## Follow-up questions
- Can we review seller disclosures before making decisions?
- Are there known roof, foundation, water, electrical, plumbing, pest, or HVAC issues?
- What should a licensed inspector evaluate based on what we observed?
- Are HOA documents available, and what do they say about pets, dues, reserves, restrictions, and maintenance?
- Should we ask insurance about property-specific factors before closing?
- Where can we verify taxes, permits, property records, and flood or hazard information through official sources?

## Do-not-decide-on-tour list
Do not decide structural condition, roof life, mold risk, electrical safety, legal boundaries, insurance cost, tax impact, HOA legal meaning, property value, or repair cost from the tour alone. Use inspectors, official records, insurers, qualified legal or tax help, and other verified sources.

## Viewing score notes
- Home address or safe label:
- Must-haves met:
- Nice-to-haves met:
- Concerns to verify:
- Documents requested:
- Professional questions:
- Emotional reaction after waiting 24 hours:
MD,
            'output_sections' => $sections([
                ['viewing_checklist', 'Viewing checklist'],
                ['follow_up_questions', 'Follow-up questions'],
                ['do_not_decide_on_tour_list', 'Do-not-decide-on-tour list'],
                ['viewing_score_notes', 'Viewing score notes'],
            ]),
            'checks' => [
                ['label' => 'Checklist supports tours', 'help' => 'Must-haves, nice-to-haves, condition observations, and HOA notes are included.', 'evidence_sections' => ['viewing_checklist']],
                ['label' => 'Professional questions are preserved', 'help' => 'Follow-ups go to agents, inspectors, insurers, officials, or qualified advisors.', 'evidence_sections' => ['follow_up_questions']],
                ['label' => 'Tour does not replace inspection', 'help' => 'The do-not-decide list avoids safety and legal conclusions.', 'evidence_sections' => ['do_not_decide_on_tour_list']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 4,
            'slug' => 'pack-closing-checklist',
            'title' => 'Pack closing checklist',
            'summary' => 'Create a closing preparation checklist with document, funds, walkthrough, and verification reminders.',
            'why_it_matters' => 'The closing period has many moving parts: lender conditions, disclosures, title, insurance, inspections, funds transfer, final walkthrough, and move-in logistics. This step creates an organized checklist while leaving legal, tax, lending, and insurance decisions to verified professionals. It also highlights wire-fraud and privacy caution without giving legal advice.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your first home purchase planning kit.',
            'before_you_begin' => 'Use this after you have real lender, agent, title, insurance, and contract information to verify. Do not paste full loan applications, SSNs, bank statements, tax returns, passwords, or wire instructions into external AI.',
            'common_problems' => 'The AI may create legal deadlines, tell users to waive contingencies, or invent closing costs. It may also mishandle wire instructions. Keep the checklist verification-based and require official channels.',
            'recovery_guidance' => 'If the checklist invents dates or costs, replace them with placeholders from official documents. If it gives legal or tax advice, convert it into questions for qualified professionals.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are packing a first-home closing preparation checklist. Fact rule: {$factRule}
Disclaimer: {$disclaimer}
Never invent rates, approvals, credit decisions, program eligibility, closing costs, taxes, insurance coverage, consumer rights, laws, deadlines, or legal obligations. Encourage verification through official documents, HUD-approved housing counseling, and qualified professionals.

Approved roadmap:
{{artifact:map-homebuying-roadmap}}

Approved lender questions:
{{artifact:prepare-lender-questions}}

Approved realtor questions:
{{artifact:prepare-realtor-questions}}

Approved viewing checklist:
{{artifact:build-viewing-checklist}}

Known constraints:
{{known_constraints}}
Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Closing prep checklist
A checkbox checklist for lender conditions, inspections, appraisal, title, insurance, closing disclosure, final walkthrough, funds transfer safety, and move-in logistics. Use placeholders for dates and amounts.

## Documents and contacts tracker
A table for official documents, verified contacts, reference numbers, deadlines, and next actions.

## Questions for professionals
Questions for lender, agent, title or escrow, inspector, insurer, attorney if used, tax professional, or housing counselor.

## Final boundary reminders
Restate privacy redaction, wire-fraud caution, official-source verification, and that worksheets are planning aids, not underwriting or advice.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Closing prep checklist
- [ ] Track lender conditions and document requests through verified lender channels.
- [ ] Review inspection report with qualified professionals and decide what questions remain.
- [ ] Confirm appraisal status through lender updates.
- [ ] Review title or escrow instructions through verified contacts.
- [ ] Obtain homeowners insurance quote or binder through verified insurer channels.
- [ ] Review Closing Disclosure when issued and compare questions with lender or counselor.
- [ ] Verify cash-to-close amount from official documents only.
- [ ] Confirm wire or funds-transfer instructions by calling a verified known number, not a number from a suspicious email.
- [ ] Schedule final walkthrough and list items to check.
- [ ] Plan utilities, movers, address updates, keys, and first-week repairs or supplies.

## Documents and contacts tracker
| Item | Verified source | Reference or contact | Due date | Status | Next action |
| --- | --- | --- | --- | --- | --- |
| Loan conditions | [LENDER PORTAL] | [CONTACT] | [DATE] | [STATUS] | [NEXT STEP] |
| Inspection report | [INSPECTOR] | [REPORT-ID] | [DATE] | [STATUS] | [QUESTIONS] |
| Closing Disclosure | [LENDER] | [DOCUMENT-ID] | [DATE] | [STATUS] | [REVIEW QUESTIONS] |
| Insurance binder | [INSURER] | [POLICY-ID] | [DATE] | [STATUS] | [NEXT STEP] |

## Questions for professionals
- Lender: What conditions remain, and what official cash-to-close amount should we use?
- Agent: What are the next contract milestones and final walkthrough steps?
- Title or escrow: How should we verify funds-transfer instructions safely?
- Inspector: Which findings need specialist follow-up?
- Insurer: What coverage options and exclusions should we understand before closing?
- Attorney or qualified legal professional if used: What contract or title questions should we review?
- Tax professional or official source: What property tax questions should we verify?
- HUD-approved housing counselor: Are there process questions or assistance options we should understand?

## Final boundary reminders
Redact SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, tax IDs, loan account numbers, and account credentials from external AI. Verify wire instructions through trusted official channels because email instructions can be fraudulent. Use official lender disclosures, title or escrow contacts, HUD-approved housing counseling, and qualified professionals. These worksheets are planning aids, not underwriting, mortgage approval, legal, tax, financial, insurance, or lending advice.
MD,
            'output_sections' => $sections([
                ['closing_prep_checklist', 'Closing prep checklist'],
                ['documents_and_contacts_tracker', 'Documents and contacts tracker'],
                ['questions_for_professionals', 'Questions for professionals'],
                ['final_boundary_reminders', 'Final boundary reminders'],
            ]),
            'checks' => [
                ['label' => 'Checklist covers closing basics', 'help' => 'Lender, inspection, appraisal, title, insurance, disclosure, walkthrough, and move-in tasks are included.', 'evidence_sections' => ['closing_prep_checklist']],
                ['label' => 'Official documents are trackable', 'help' => 'The tracker records sources, due dates, statuses, and next actions.', 'evidence_sections' => ['documents_and_contacts_tracker']],
                ['label' => 'Boundaries and fraud caution are clear', 'help' => 'Final reminders protect privacy and avoid underwriting or advice claims.', 'evidence_sections' => ['final_boundary_reminders']],
            ],
        ]),
    ],
];
