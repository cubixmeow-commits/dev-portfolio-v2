<?php

declare(strict_types=1);

/**
 * Build a Budget and Debt Payoff Plan - executable Cookbook for money-major-decisions.
 */

require_once __DIR__ . '/../money_helpers.php';

$factRule = SM_MONEY_FACT_RULE;
$disclaimer = SM_MONEY_DISCLAIMER;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'build-budget-and-debt-plan',
    'title'               => 'Build a Budget and Debt Payoff Plan',
    'tagline'             => 'Make a realistic monthly budget and compare debt strategies calmly.',
    'description'         => 'Build a plain-language monthly money plan from numbers you supply, compare debt payoff approaches without pretending there is one universally correct strategy, and leave with a weekly review checklist. This Cookbook does not invent financial figures, rates, account details, or outcomes. ' . sm_money_beginner_footer('a monthly budget, expense breakdown, debt strategy comparison, monthly action plan, and weekly review checklist', 5, 'about 55 minutes'),
    'primary_category'    => 'money-major-decisions',
    'collections'         => ['money-major-decisions', 'start-here'],
    'audience'            => 'Beginners who want to organize monthly income, expenses, debts, priorities, and next actions',
    'outcome'             => 'monthly budget, expense breakdown, debt strategy comparison, monthly action plan, and weekly review checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'harbor',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 55,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 30,
    'stages' => [
        ['title' => 'Map reality', 'summary' => 'Collect supplied income, expenses, debts, priorities, and boundaries without inventing figures.'],
        ['title' => 'Break down expenses', 'summary' => 'Turn fixed and variable costs into a clear monthly expense view.'],
        ['title' => 'Choose a plan', 'summary' => 'Compare debt payoff approaches and assemble a realistic monthly budget.'],
        ['title' => 'Review weekly', 'summary' => 'Pack an action plan and repeatable review checklist.'],
    ],
    'fields' => [
        ['field_key' => 'monthly_income', 'label' => 'Monthly income', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('List monthly income amounts or ranges you want to use. Mark estimates as estimates and do not paste pay stubs with private identifiers.'), 'placeholder' => 'e.g. Net pay from Job A: [amount]; side income estimate: [amount]', 'sample_value' => "Net household income: $4,250 per month\nSide income varies; use $150 as an estimate and mark it clearly"],
        ['field_key' => 'fixed_expenses', 'label' => 'Fixed expenses', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('Include recurring bills you want in the plan. Replace account numbers with placeholders such as [UTILITY-ACCOUNT].'), 'placeholder' => 'Rent, utilities, insurance, subscriptions, minimum debt payments.', 'sample_value' => "Rent: $1,650\nUtilities: $210 estimate\nPhone: $95\nCar insurance: $140\nStreaming: $18\nDebt A minimum: $120, account [CARD-LAST4]\nDebt B minimum: $85, account [LOAN-LAST4]"],
        ['field_key' => 'variable_expenses', 'label' => 'Variable expenses', 'type' => 'textarea', 'help' => 'List flexible or changing categories. Use real amounts or clearly labeled estimates only.', 'placeholder' => 'Groceries, gas, household supplies, pets, school costs, gifts.', 'sample_value' => "Groceries: about $650\nGas/transit: $180\nHousehold and personal care: $120\nPets: $60\nKids activities: $75\nEating out: about $180"],
        ['field_key' => 'debts_list', 'label' => 'Debts list', 'type' => 'textarea', 'help' => sm_money_privacy_pantry_help('List each debt with balance, minimum payment, and interest rate only if you know them. Use placeholders for account numbers.'), 'placeholder' => 'Debt name, balance, minimum payment, APR if known, current status.', 'sample_value' => "Credit card [CARD-LAST4]: balance $3,400, minimum $120, APR unknown\nPersonal loan [LOAN-LAST4]: balance $5,800, minimum $85, fixed rate unknown\nBoth are current"],
        ['field_key' => 'goals_or_priorities', 'label' => 'Goals or priorities', 'type' => 'textarea', 'help' => 'Name what matters most: stability, faster debt payoff, emergency savings, predictable bills, or another priority.', 'placeholder' => 'e.g. Avoid overdrafts, keep groceries realistic, pay down debt steadily.', 'sample_value' => 'Avoid overdrafts, keep groceries realistic for a family of three, make progress on debt, and set aside a small emergency buffer.'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Include timing, irregular income, seasonal costs, stress points, or numbers you do not want the AI to guess.', 'placeholder' => 'e.g. Income changes during school breaks; do not suggest missing minimum payments.', 'sample_value' => 'Income can vary by about $150. Do not suggest missing minimum payments. We have a school fee next month and want a plan that feels calm, not extreme.'],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose how the plan should sound.', 'options' => ['Calm and practical', 'Warm and encouraging', 'Direct and simple', 'Detailed but not judgmental'], 'sample_value' => 'Calm and practical'],
    ],
    'recipes' => [
        sm_money_recipe([
            'stage_position' => 1,
            'slug' => 'map-money-reality',
            'title' => 'Map money reality',
            'summary' => 'Create a grounded snapshot of income, expenses, debts, priorities, and unknowns.',
            'why_it_matters' => 'A budget is only useful if it starts with the user-supplied reality. This step separates known amounts, estimates, unknowns, and sensitive details that should stay redacted. It also keeps the plan from becoming financial advice or a promise of a payoff result.',
            'unlocks_text' => 'Approving unlocks the expense breakdown.',
            'before_you_begin' => 'Gather the income, expense, and debt details you are comfortable using. Replace SSNs, bank numbers, cards, insurance IDs, medical record numbers, passwords, and account numbers with placeholders such as [CARD-LAST4] or [BANK-LAST4]. Mark estimates clearly.',
            'common_problems' => 'The AI may invent missing income, rates, balances, or payoff dates. It may also treat estimates as verified numbers or make moral judgments about spending. Keep the output factual, calm, and limited to supplied information.',
            'recovery_guidance' => 'If the snapshot invents figures, rerun with instructions to mark unknowns. If it feels shaming, ask for a neutral rewrite. If it omits privacy redaction, add a privacy check before continuing.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are organizing a beginner monthly budget snapshot. Fact rule: {$factRule}
Disclaimer: {$disclaimer}

Monthly income:
{{monthly_income}}

Fixed expenses:
{{fixed_expenses}}

Variable expenses:
{{variable_expenses}}

Debts list:
{{debts_list}}

Goals or priorities:
{{goals_or_priorities}}

Constraints or concerns:
{{constraints_or_concerns}}

Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Money snapshot
Summarize the supplied income, expense, debt, priority, and constraint facts in plain language. Label estimates and unknowns.

## Known numbers
List only supplied numbers. Do not calculate totals unless all required numbers were supplied and clearly label any calculation as based on supplied information.

## Unknowns to verify
List missing balances, minimum payments, rates, bill dates, or other details that should be verified outside this tool.

## Privacy redaction check
Confirm that SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, and account credentials should be replaced with placeholders before using external AI.

## Planning boundaries
State that this is not financial, legal, medical, tax, or lending advice and that no outcome is promised.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Money snapshot
The household is planning from $4,250 in monthly net income plus a clearly marked $150 side-income estimate. Major supplied costs include rent, utilities, phone, car insurance, groceries, transportation, household needs, pets, kids activities, eating out, and minimum payments on two debts. Priorities are avoiding overdrafts, keeping groceries realistic, making steady debt progress, and building a small emergency buffer.

## Known numbers
- Net household income: $4,250 per month.
- Side income estimate: $150.
- Rent: $1,650.
- Utilities estimate: $210.
- Phone: $95.
- Car insurance: $140.
- Streaming: $18.
- Credit card [CARD-LAST4]: $3,400 balance, $120 minimum, APR unknown.
- Personal loan [LOAN-LAST4]: $5,800 balance, $85 minimum, rate unknown.
- Groceries: about $650.
- Gas/transit: $180.
- Household and personal care: $120.
- Pets: $60.
- Kids activities: $75.
- Eating out: about $180.

## Unknowns to verify
- APR or interest rate for each debt.
- Due dates for debt minimum payments and recurring bills.
- Whether the $150 side income is reliable enough to use in the core budget.
- The exact school fee amount due next month.

## Privacy redaction check
Before using external AI, replace SSNs, bank account numbers, full card numbers, insurance IDs, medical record numbers, passwords, and account credentials with placeholders such as [CARD-LAST4], [LOAN-LAST4], or [BANK-LAST4].

## Planning boundaries
This is an organizing aid, not financial, legal, medical, tax, or lending advice. It does not promise debt payoff results, credit decisions, or any financial outcome.
MD,
            'output_sections' => $sections([
                ['money_snapshot', 'Money snapshot'],
                ['known_numbers', 'Known numbers'],
                ['unknowns_to_verify', 'Unknowns to verify'],
                ['privacy_redaction_check', 'Privacy redaction check'],
                ['planning_boundaries', 'Planning boundaries'],
            ]),
            'checks' => [
                ['label' => 'No figures were invented', 'help' => 'Known numbers come from supplied pantry fields only.', 'evidence_sections' => ['known_numbers', 'unknowns_to_verify']],
                ['label' => 'Sensitive details stay redacted', 'help' => 'The privacy check names placeholders for private identifiers.', 'evidence_sections' => ['privacy_redaction_check']],
                ['label' => 'Boundaries are clear', 'help' => 'The output avoids advice promises and financial outcomes.', 'evidence_sections' => ['planning_boundaries']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 2,
            'slug' => 'build-expense-breakdown',
            'title' => 'Build expense breakdown',
            'summary' => 'Sort supplied spending into fixed, variable, debt minimum, and planning categories.',
            'why_it_matters' => 'A budget becomes easier to adjust when expenses are grouped by how they behave. This step distinguishes fixed commitments, flexible categories, required minimum payments, estimates, and next-month exceptions. It helps users see tradeoffs without pretending the tool knows their life better than they do.',
            'unlocks_text' => 'Approving unlocks debt strategy comparison.',
            'before_you_begin' => 'Review the money snapshot. Add any missing bill dates or next-month exceptions you know. Keep account identifiers redacted and avoid pasting bank statements with private details.',
            'common_problems' => 'The AI may classify a cost as optional when it is actually important to the household. It may also cut categories too aggressively or calculate totals from incomplete numbers. Keep the breakdown realistic and mark incomplete data.',
            'recovery_guidance' => 'If a category feels unrealistic, revise it with a household note. If calculations appear from missing data, ask for a version that says "not enough information to total."',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are building an expense breakdown for a beginner budget. Fact rule: {$factRule}
Disclaimer: {$disclaimer}

Approved money snapshot:
{{artifact:map-money-reality}}

Fixed expenses:
{{fixed_expenses}}

Variable expenses:
{{variable_expenses}}

Debts list:
{{debts_list}}

Goals or priorities:
{{goals_or_priorities}}

Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Expense breakdown
Group supplied costs into fixed bills, variable needs, debt minimums, and flexible choices. Keep estimates labeled.

## Monthly baseline notes
Name what appears to be part of a normal month versus a one-time or next-month exception.

## Adjustment candidates
List categories the user could review if they choose. Do not order them as commands or shame spending.

## Data gaps
List due dates, missing amounts, rates, or categories that must be verified before relying on the budget.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Expense breakdown
- Fixed bills: rent $1,650; utilities estimate $210; phone $95; car insurance $140; streaming $18.
- Debt minimums: credit card [CARD-LAST4] $120; personal loan [LOAN-LAST4] $85.
- Variable needs: groceries about $650; gas/transit $180; household and personal care $120; pets $60.
- Flexible or reviewable choices: kids activities $75; eating out about $180.

## Monthly baseline notes
The baseline month appears to include rent, utilities, phone, insurance, streaming, debt minimums, groceries, transportation, household supplies, pets, kids activities, and eating out. The school fee next month is an exception and needs an exact amount before it can be included.

## Adjustment candidates
- Review eating out if the household wants more cash for debt or savings.
- Review subscriptions only if they are not important to the household.
- Review kids activity timing around the school fee month.
- Keep groceries realistic rather than using an extreme target.

## Data gaps
- Exact school fee amount and due date.
- Debt due dates.
- APR or rate for each debt.
- Whether side income should be used for baseline expenses or treated as extra.
MD,
            'output_sections' => $sections([
                ['expense_breakdown', 'Expense breakdown'],
                ['monthly_baseline_notes', 'Monthly baseline notes'],
                ['adjustment_candidates', 'Adjustment candidates'],
                ['data_gaps', 'Data gaps'],
            ]),
            'checks' => [
                ['label' => 'Categories are realistic', 'help' => 'Expenses are grouped without shaming or over-cutting.', 'evidence_sections' => ['expense_breakdown', 'adjustment_candidates']],
                ['label' => 'Exceptions are separated', 'help' => 'One-time costs are not hidden in the normal month.', 'evidence_sections' => ['monthly_baseline_notes']],
                ['label' => 'Incomplete data stays visible', 'help' => 'Missing details remain data gaps.', 'evidence_sections' => ['data_gaps']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'compare-debt-strategies',
            'title' => 'Compare debt strategies',
            'summary' => 'Compare avalanche and snowball debt approaches as options, not as a single correct answer.',
            'why_it_matters' => 'Debt payoff choices mix math, motivation, cash flow, risk, and stress. Avalanche and snowball are common comparison frames, but either can be unsuitable if required payments or household stability are ignored. This step keeps the comparison neutral and avoids promising savings or payoff dates.',
            'unlocks_text' => 'Approving unlocks monthly budget assembly.',
            'before_you_begin' => 'Verify balances, minimum payments, and rates if you have them. Do not paste full card numbers, SSNs, bank numbers, passwords, or account credentials. Remember that this comparison is planning support, not financial advice.',
            'common_problems' => 'The AI may declare one strategy universally best, invent interest rates, calculate payoff dates without enough information, or suggest skipping minimum payments. It should instead compare tradeoffs using supplied data.',
            'recovery_guidance' => 'If the output picks one strategy as the only right answer, rerun for a comparison table. If it invents rates or payoff dates, replace those with "unknown until verified."',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are comparing debt payoff strategies for a beginner budget. Fact rule: {$factRule}
Disclaimer: {$disclaimer}

Approved expense breakdown:
{{artifact:build-expense-breakdown}}

Debts list:
{{debts_list}}

Goals or priorities:
{{goals_or_priorities}}

Constraints or concerns:
{{constraints_or_concerns}}

Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Debt strategy comparison
Compare avalanche and snowball in plain language using only supplied debt facts. Explain avalanche as generally prioritizing highest interest rate first and snowball as generally prioritizing smallest balance first, but do not declare either the only correct choice.

## Information needed for calculations
List missing rates, balances, payment dates, minimums, and extra-payment capacity needed before any payoff or interest comparison.

## Strategy fit notes
Describe how each approach may fit different priorities such as lower interest cost, motivation, simplicity, cash flow, or stress. Avoid promises.

## Red flags to avoid
Name unsafe or unsupported suggestions to avoid, including skipped minimum payments, invented consolidation terms, or unverified rates.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Debt strategy comparison
Avalanche usually means paying required minimums on every debt, then aiming extra money at the debt with the highest verified interest rate. In this case, the APR for the credit card [CARD-LAST4] is unknown and the loan rate is unknown, so the avalanche order cannot be confirmed yet. Snowball usually means paying required minimums on every debt, then aiming extra money at the smallest balance first; with supplied balances, that would likely start with the $3,400 credit card before the $5,800 personal loan, if all other details are acceptable to the household.

## Information needed for calculations
- Verified APR or rate for each debt.
- Exact minimum payments and due dates.
- Whether either debt has fees, promotional terms, or prepayment limits.
- A realistic monthly amount available after essentials, minimum payments, and a small buffer.

## Strategy fit notes
- Avalanche may appeal if the household wants to compare potential interest cost after rates are verified.
- Snowball may appeal if the household wants a visible early win and simpler motivation.
- Either approach should keep minimum payments current and fit the household's cash-flow stability.
- This comparison does not promise payoff dates, credit outcomes, or total savings.

## Red flags to avoid
- Do not skip minimum payments unless a qualified advisor or creditor arrangement says otherwise.
- Do not invent interest rates or payoff dates.
- Do not assume consolidation, refinancing, or credit approval.
- Do not expose full account numbers; keep placeholders such as [CARD-LAST4].
MD,
            'output_sections' => $sections([
                ['debt_strategy_comparison', 'Debt strategy comparison'],
                ['information_needed_for_calculations', 'Information needed for calculations'],
                ['strategy_fit_notes', 'Strategy fit notes'],
                ['red_flags_to_avoid', 'Red flags to avoid'],
            ]),
            'checks' => [
                ['label' => 'Avalanche and snowball are compared', 'help' => 'The output explains both without declaring one universally correct.', 'evidence_sections' => ['debt_strategy_comparison', 'strategy_fit_notes']],
                ['label' => 'No payoff math is invented', 'help' => 'Missing rates and payment capacity are called out before calculations.', 'evidence_sections' => ['information_needed_for_calculations']],
                ['label' => 'Risky suggestions are excluded', 'help' => 'The red flags reject skipped minimums and invented credit terms.', 'evidence_sections' => ['red_flags_to_avoid']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 3,
            'slug' => 'assemble-monthly-budget',
            'title' => 'Assemble monthly budget',
            'summary' => 'Turn the expense breakdown and chosen priorities into a realistic monthly plan.',
            'why_it_matters' => 'The budget draft brings income, expenses, minimum payments, buffers, and selected priorities into one usable view. It should be realistic enough to survive a normal month and cautious about uncertain income. This step does not tell the user what they can afford; it organizes their own supplied inputs.',
            'unlocks_text' => 'Approving unlocks the action and review kit.',
            'before_you_begin' => 'Decide whether variable income should be treated as baseline or extra. Verify any missing bill amounts or debt minimums. Keep sensitive account details redacted.',
            'common_problems' => 'The AI may make the budget balance by inventing cuts, assuming side income is guaranteed, or ignoring one-time expenses. It may also use exact totals from estimates without labeling them. Ask for conservative labels where needed.',
            'recovery_guidance' => 'If the budget is too tight, ask for a version with a buffer line and a "needs verification" list. If totals rely on estimates, require estimated totals to be labeled.',
            'est_minutes' => 14,
            'prompt_template' => <<<TXT
You are assembling a monthly budget from supplied information. Fact rule: {$factRule}
Disclaimer: {$disclaimer}

Approved money snapshot:
{{artifact:map-money-reality}}

Approved expense breakdown:
{{artifact:build-expense-breakdown}}

Approved debt strategy comparison:
{{artifact:compare-debt-strategies}}

Monthly income:
{{monthly_income}}

Goals or priorities:
{{goals_or_priorities}}

Constraints or concerns:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Monthly budget draft
Create a clear budget using supplied figures only. Label estimates and avoid pretending the draft is a final financial recommendation.

## Debt payment lane
Show required minimum payments and any optional extra-payment planning lane only if the user supplied room for it. Do not invent extra money.

## Buffer and irregular costs
Name a place for emergency buffer, variable income, and known one-time costs. Use placeholders where exact amounts are missing.

## Budget assumptions
List the assumptions and supplied facts the user must verify before relying on the plan.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Monthly budget draft
Using the supplied figures, the draft budget includes monthly income of $4,250 plus a separate $150 side-income estimate. Fixed bills include rent, utilities estimate, phone, car insurance, and streaming. Variable categories include groceries, gas/transit, household and personal care, pets, kids activities, and eating out. Debt minimums are $120 for credit card [CARD-LAST4] and $85 for personal loan [LOAN-LAST4].

## Debt payment lane
Required minimum payments should stay visible first: $120 to credit card [CARD-LAST4] and $85 to personal loan [LOAN-LAST4]. Any extra-payment lane should wait until the household confirms the monthly leftover after essentials, the school fee, and a small buffer. The strategy comparison can then be used to choose avalanche, snowball, or another verified approach.

## Buffer and irregular costs
- Emergency buffer: set a line item the household chooses, even if small.
- Variable income: treat the $150 side-income estimate cautiously unless it is reliable.
- School fee: add [SCHOOL-FEE-AMOUNT] and [DUE-DATE] before finalizing next month.
- Irregular costs: add categories for car maintenance, medical costs, gifts, or annual renewals if relevant.

## Budget assumptions
- All amounts are user-supplied and may need verification.
- Debt rates are unknown, so payoff calculations are not included.
- The school fee amount is missing.
- This budget is a planning aid, not financial, legal, medical, tax, or lending advice.
MD,
            'output_sections' => $sections([
                ['monthly_budget_draft', 'Monthly budget draft'],
                ['debt_payment_lane', 'Debt payment lane'],
                ['buffer_and_irregular_costs', 'Buffer and irregular costs'],
                ['budget_assumptions', 'Budget assumptions'],
            ]),
            'checks' => [
                ['label' => 'Budget uses supplied inputs', 'help' => 'The draft does not create income, cuts, or extra payment room.', 'evidence_sections' => ['monthly_budget_draft', 'budget_assumptions']],
                ['label' => 'Debt minimums remain visible', 'help' => 'The debt lane separates required payments from optional extras.', 'evidence_sections' => ['debt_payment_lane']],
                ['label' => 'Irregular costs are not ignored', 'help' => 'The buffer section makes one-time costs explicit.', 'evidence_sections' => ['buffer_and_irregular_costs']],
            ],
        ]),
        sm_money_recipe([
            'stage_position' => 4,
            'slug' => 'pack-action-and-review',
            'title' => 'Pack action and review',
            'summary' => 'Create a monthly action plan and weekly review checklist for maintaining the budget.',
            'why_it_matters' => 'A budget is more likely to help if it becomes a repeatable routine. This final step turns the draft into short actions, a weekly check-in, and a privacy-aware update loop. It keeps the user in control and avoids promising financial outcomes.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your budget and debt plan kit.',
            'before_you_begin' => 'Review the budget assumptions and decide which details need verification first. Keep account numbers, bank numbers, SSNs, card numbers, insurance IDs, medical record numbers, and passwords out of any external tool.',
            'common_problems' => 'The AI may create too many tasks, promise a debt-free date, or suggest changes that do not fit constraints. The final kit should be short, practical, and easy to revisit weekly.',
            'recovery_guidance' => 'If the action plan feels overwhelming, ask for the top five actions only. If it promises outcomes, rewrite as a review routine with no guarantees.',
            'est_minutes' => 10,
            'prompt_template' => <<<TXT
You are packing a monthly budget and debt plan into next actions. Fact rule: {$factRule}
Disclaimer: {$disclaimer}

Approved monthly budget:
{{artifact:assemble-monthly-budget}}

Approved debt strategy comparison:
{{artifact:compare-debt-strategies}}

Goals or priorities:
{{goals_or_priorities}}

Constraints or concerns:
{{constraints_or_concerns}}

Preferred tone: {{preferred_tone}}

Produce Markdown with these exact headings:

## Monthly action plan
Five to eight practical actions for this month, including verification tasks and budget updates. Do not promise outcomes.

## Weekly review checklist
A checkbox checklist for a short weekly budget review.

## What to update next month
List the pantry fields or budget lines the user should update before running the plan again.

## Privacy and advice reminders
Remind the user to redact SSNs, bank numbers, card numbers, insurance IDs, medical record numbers, passwords, and account credentials; restate advice boundaries.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Monthly action plan
1. Verify the APR or rate, due date, and minimum payment for each debt.
2. Add the exact school fee amount and due date to next month's budget.
3. Decide whether the $150 side-income estimate belongs in the baseline budget or an extra category.
4. Choose a small emergency buffer line that fits the month.
5. Keep minimum payments visible before any optional extra debt payment.
6. Compare avalanche and snowball again after rates are verified.
7. Review eating out and other flexible categories only if the household wants more room.

## Weekly review checklist
- [ ] Check actual income received against the budget.
- [ ] Check upcoming bill due dates.
- [ ] Confirm debt minimum payments are scheduled or paid.
- [ ] Compare grocery and transportation spending with the plan.
- [ ] Note any new irregular cost.
- [ ] Decide whether any optional extra debt payment still fits.

## What to update next month
- Monthly income and variable income reliability.
- Fixed expenses that changed.
- Variable expense estimates.
- Debt balances, minimums, rates, and due dates.
- Goals, priorities, constraints, and upcoming one-time costs.

## Privacy and advice reminders
Use placeholders for SSNs, bank numbers, full card numbers, insurance IDs, medical record numbers, passwords, and account credentials. This kit organizes information and does not provide financial, legal, medical, tax, or lending advice. It does not promise debt payoff results, credit decisions, or financial outcomes.
MD,
            'output_sections' => $sections([
                ['monthly_action_plan', 'Monthly action plan'],
                ['weekly_review_checklist', 'Weekly review checklist'],
                ['what_to_update_next_month', 'What to update next month'],
                ['privacy_and_advice_reminders', 'Privacy and advice reminders'],
            ]),
            'checks' => [
                ['label' => 'Actions are practical', 'help' => 'The plan names doable next steps without guarantees.', 'evidence_sections' => ['monthly_action_plan']],
                ['label' => 'Review can repeat weekly', 'help' => 'The checklist supports a short recurring review.', 'evidence_sections' => ['weekly_review_checklist']],
                ['label' => 'Privacy and advice limits remain clear', 'help' => 'Sensitive placeholders and disclaimers are included.', 'evidence_sections' => ['privacy_and_advice_reminders']],
            ],
        ]),
    ],
];
