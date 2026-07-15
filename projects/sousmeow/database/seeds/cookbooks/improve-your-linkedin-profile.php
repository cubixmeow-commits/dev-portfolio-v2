<?php

declare(strict_types=1);

/**
 * Improve Your LinkedIn Profile - executable Cookbook for career-freelance.
 */

require_once __DIR__ . '/../career_helpers.php';

$factRule = SM_CAREER_FACT_RULE;
$sections = static fn(array $rows): array => array_map(
    static fn(array $row): array => ['key' => $row[0], 'heading' => $row[1], 'required' => true],
    $rows
);

return [
    'slug'                => 'improve-your-linkedin-profile',
    'title'               => 'Improve Your LinkedIn Profile',
    'tagline'             => "Clarify what you do, where you're heading, and why it matters.",
    'description'         => 'A useful LinkedIn profile makes your current work, target direction, and evidence easy to understand. This Cookbook helps you define positioning, compare headlines, rewrite About copy, improve experience entries, organize skills and proof, draft content pillars, and review consistency. It avoids influencer exaggeration unless you explicitly ask for that tone. ' . sm_career_beginner_footer('a positioning statement, headline options, About section, experience entries, skills/proof map, content pillars, and consistency checklist', 7, 'about 65 minutes'),
    'primary_category'    => 'career-freelance',
    'collections'         => [],
    'audience'            => 'Professionals updating a LinkedIn profile for job search, freelance work, or clearer positioning',
    'outcome'             => 'positioning statement, headline options, About section, experience entries, skills/proof map, content pillars, and consistency checklist',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'indigo',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 65,
    'demo_completed_runs' => 0,
    'demo_avg_rating'     => null,
    'sort_order'          => 27,
    'stages' => [
        ['title' => 'Position', 'summary' => 'Name what you do and compare headline directions.'],
        ['title' => 'Rewrite', 'summary' => 'Improve the About, experience, and skills proof sections.'],
        ['title' => 'Share', 'summary' => 'Choose practical content pillars that fit your goals.'],
        ['title' => 'Review', 'summary' => 'Check consistency, truthfulness, and privacy before updating.'],
    ],
    'fields' => [
        ['field_key' => 'current_role', 'label' => 'Current role or identity', 'type' => 'text', 'help' => 'Use a truthful current role, student status, freelance focus, or transition label.', 'placeholder' => 'e.g. Data analyst', 'sample_value' => 'Data analyst'],
        ['field_key' => 'years_experience', 'label' => 'Years of relevant experience', 'type' => 'text', 'help' => 'Use a truthful number, range, or short explanation.', 'placeholder' => 'e.g. 4 years analytics', 'sample_value' => '4 years in operations and analytics support'],
        ['field_key' => 'target_role', 'label' => 'Target role or direction', 'type' => 'text', 'help' => 'Name where the profile should point next.', 'placeholder' => 'e.g. Product analyst', 'sample_value' => 'Product analyst'],
        ['field_key' => 'target_industry', 'label' => 'Target industry or audience', 'type' => 'text', 'help' => 'Name the industry, customer type, or audience if known.', 'placeholder' => 'e.g. Fintech or B2B SaaS', 'sample_value' => 'B2B SaaS'],
        ['field_key' => 'current_profile_text', 'label' => 'Current LinkedIn text', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste headline, About, and selected experience text only.'), 'placeholder' => 'Current headline, About section, and experience bullets.', 'sample_value' => "Headline: Data Analyst at MapleOps\nAbout: I like solving business problems with data.\nExperience: Built weekly operations dashboards; cleaned CRM exports; answered ad hoc reporting questions"],
        ['field_key' => 'existing_resume', 'label' => 'Resume or profile notes', 'type' => 'textarea', 'help' => sm_career_privacy_pantry_help('Paste selected career facts; remove phone numbers, addresses, and private references.'), 'placeholder' => 'Selected bullets or profile notes.', 'sample_value' => "Operations analyst, MapleOps, 2020-present\nCreated weekly dashboard for support volume\nDocumented recurring CRM data cleanup steps\nPartnered with customer success managers on renewal-risk reports"],
        ['field_key' => 'strongest_achievements', 'label' => 'Strongest achievements and proof', 'type' => 'textarea', 'help' => 'List real proof, projects, tools, or themes. Only include numbers you can verify.', 'placeholder' => 'e.g. Built dashboard used by support leads...', 'sample_value' => "Built a support-volume dashboard used in weekly operations meetings\nCreated a cleanup checklist for duplicate CRM records\nTurned renewal-risk questions into a repeatable report for customer success"],
        ['field_key' => 'preferred_tone', 'label' => 'Preferred tone', 'type' => 'select', 'help' => 'Choose a profile voice. Influencer-style exaggeration is avoided unless you choose an expressive tone.', 'options' => ['Clear and professional', 'Warm and practical', 'Confident but modest', 'More expressive and personal'], 'sample_value' => 'Clear and professional'],
        ['field_key' => 'constraints_or_concerns', 'label' => 'Constraints or concerns', 'type' => 'textarea', 'help' => 'Name privacy limits, claims to avoid, or discomfort with self-promotion.', 'placeholder' => 'e.g. Avoid sounding like an influencer.', 'sample_value' => 'Avoid hype. Do not claim product analytics ownership beyond reporting and operations analysis.'],
    ],
    'recipes' => [
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'define-professional-positioning',
            'title' => 'Define professional positioning',
            'summary' => 'Write a truthful positioning statement that connects current work to target direction.',
            'why_it_matters' => 'Positioning gives the rest of the profile a spine. It should describe what you do, who benefits, and where you are headed without pretending the transition is already complete. This step keeps claims proportional to evidence.',
            'unlocks_text' => 'Approving unlocks headline comparison.',
            'before_you_begin' => 'Gather your current profile, selected resume facts, and target direction. Remove private details before pasting. Decide whether you want a modest or more expressive voice.',
            'common_problems' => 'The AI may write a grand personal brand statement or overstate seniority. It may also hide the target direction to avoid risk. Keep the statement clear, specific, and truthful.',
            'recovery_guidance' => 'If the statement sounds inflated, ask for a plainer version with fewer adjectives. If it is too vague, add the strongest proof points and target role.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are defining LinkedIn profile positioning. Fact rule: {$factRule}

Current role: {{current_role}}
Years of experience: {{years_experience}}
Target role: {{target_role}}
Target industry: {{target_industry}}
Current profile text:
{{current_profile_text}}
Strongest achievements:
{{strongest_achievements}}
Preferred tone: {{preferred_tone}}
Constraints:
{{constraints_or_concerns}}

Produce Markdown with these exact headings:

## Positioning statement
One or two sentences naming current work, target direction, and proof theme.

## Proof themes
Three to five themes supported by supplied facts.

## Boundary notes
Claims, titles, or expertise levels not to imply.

## Profile promise
One sentence describing what a reader should understand after scanning the profile.

Keep the tone grounded. Avoid influencer exaggeration unless the selected tone explicitly asks for it.
TXT,
            'example_response' => <<<'MD'
## Positioning statement
I am a data analyst with four years of operations and analytics support experience, now aiming toward product analyst work in B2B SaaS. My strongest proof is turning messy operational questions into dashboards, cleanup processes, and repeatable reports teams can use.

## Proof themes
- Operations dashboards.
- CRM data cleanup.
- Customer success reporting.
- Translating recurring questions into repeatable analysis.

## Boundary notes
Do not imply ownership of product strategy, experimentation, or senior analytics leadership. Do not claim product analyst title yet.

## Profile promise
Readers should quickly see a practical analyst who can organize messy data questions and is moving toward product analytics.
MD,
            'output_sections' => $sections([
                ['positioning_statement', 'Positioning statement'],
                ['proof_themes', 'Proof themes'],
                ['boundary_notes', 'Boundary notes'],
                ['profile_promise', 'Profile promise'],
            ]),
            'checks' => [
                ['label' => 'Positioning is truthful', 'help' => 'It names current work and target direction without changing titles.', 'evidence_sections' => ['positioning_statement', 'boundary_notes']],
                ['label' => 'Proof themes have evidence', 'help' => 'Themes come from supplied achievements.', 'evidence_sections' => ['proof_themes']],
                ['label' => 'Tone is grounded', 'help' => 'The profile promise avoids hype.', 'evidence_sections' => ['profile_promise']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 1,
            'slug' => 'compare-headline-options',
            'title' => 'Compare headline options',
            'summary' => 'Draft and compare LinkedIn headlines for clarity, searchability, and truthfulness.',
            'why_it_matters' => 'The headline is the most scanned part of the profile. It needs to be searchable without pretending you already hold the target role. Comparing options helps you choose between current-role clarity and target-direction clarity.',
            'unlocks_text' => 'Approving unlocks the About section rewrite.',
            'before_you_begin' => 'Review the positioning statement. Decide whether your profile should prioritize current credibility, target direction, or both. Keep title claims exact.',
            'common_problems' => 'Headlines often stuff keywords, use vague traits, or imply a title the user does not hold. Another issue is sounding like a motivational slogan. Keep it concrete.',
            'recovery_guidance' => 'If options feel too keyword-heavy, ask for shorter versions. If they hide the target direction, ask for one bridge headline that says "toward" or "focused on" truthfully.',
            'est_minutes' => 7,
            'prompt_template' => <<<TXT
You are writing LinkedIn headline options. Fact rule: {$factRule}

Current role: {{current_role}}
Target role: {{target_role}}
Target industry: {{target_industry}}
Preferred tone: {{preferred_tone}}

Approved positioning:
{{artifact:define-professional-positioning}}

Produce Markdown with these exact headings:

## Headline options
Five headline options under typical LinkedIn headline length. Use truthful titles and grounded keywords.

## Comparison notes
Explain the tradeoff for each option: current credibility, target direction, or keyword clarity.

## Recommended headline
Pick one option and explain why.

## Avoid in headline
Terms or claims that would overstate the profile.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Headline options
1. Data Analyst | Operations Dashboards, CRM Cleanup, Customer Success Reporting
2. Data Analyst moving toward Product Analytics in B2B SaaS
3. Operations Data Analyst | Turning recurring questions into usable reports
4. Data Analyst | Dashboards, data cleanup, and customer success insights
5. Analytics Support for Operations and Customer Success Teams

## Comparison notes
Option 1 is strongest for current credibility and searchable skills. Option 2 is clearest about target direction but less proof-heavy. Option 3 is plain and memorable. Option 4 balances proof and audience. Option 5 is broad but avoids title overclaiming.

## Recommended headline
Data Analyst | Operations Dashboards, CRM Cleanup, Customer Success Reporting. It is truthful, specific, and searchable without claiming product analyst title before the transition.

## Avoid in headline
Avoid "Product Analyst" as a current title, "growth hacker," "data storyteller changing the world," and unverified seniority claims.
MD,
            'output_sections' => $sections([
                ['headline_options', 'Headline options'],
                ['comparison_notes', 'Comparison notes'],
                ['recommended_headline', 'Recommended headline'],
                ['avoid_in_headline', 'Avoid in headline'],
            ]),
            'checks' => [
                ['label' => 'Headlines are truthful', 'help' => 'No option claims an unheld title.', 'evidence_sections' => ['headline_options', 'avoid_in_headline']],
                ['label' => 'Recommendation is reasoned', 'help' => 'The recommended headline explains the tradeoff.', 'evidence_sections' => ['recommended_headline', 'comparison_notes']],
                ['label' => 'No empty hype', 'help' => 'Avoid list catches slogans and exaggeration.', 'evidence_sections' => ['avoid_in_headline']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'rewrite-about-section',
            'title' => 'Rewrite About section',
            'summary' => 'Write a concise About section that explains work, proof, direction, and boundaries.',
            'why_it_matters' => 'The About section should help readers understand your pattern, not overwhelm them with a life story. It can show direction while staying honest about current experience. This step keeps the voice professional and scan-friendly.',
            'unlocks_text' => 'Approving unlocks experience description improvements.',
            'before_you_begin' => 'Choose the recommended headline or your preferred alternative. Review privacy settings before adding personal details. Keep the About section public-safe.',
            'common_problems' => 'About drafts can become too long, too personal, or too promotional. They may also repeat every experience entry. Keep it focused on positioning and proof themes.',
            'recovery_guidance' => 'If it sounds like a sales page, ask for a calmer version. If it lacks proof, add two supplied examples and rerun.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are rewriting a LinkedIn About section. Fact rule: {$factRule}

Current role: {{current_role}}
Target role: {{target_role}}
Target industry: {{target_industry}}
Preferred tone: {{preferred_tone}}
Constraints:
{{constraints_or_concerns}}

Approved positioning:
{{artifact:define-professional-positioning}}

Approved headline options:
{{artifact:compare-headline-options}}

Produce Markdown with these exact headings:

## About section
A polished LinkedIn About section in 2 to 4 short paragraphs. Keep it public-safe and grounded.

## Profile keywords used
Keywords included because they are supported by supplied facts.

## Claims intentionally avoided
Unsupported claims, inflated expertise, or private details kept out.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## About section
I am a data analyst with four years of operations and analytics support experience. My work has focused on making recurring business questions easier to answer through dashboards, cleanup processes, and repeatable reports.

At MapleOps, I have built weekly operations dashboards, documented CRM cleanup steps, and partnered with customer success managers on renewal-risk reporting questions. I like work where messy inputs become clearer decisions for a team.

I am now building toward product analyst work in B2B SaaS. I am especially interested in roles where operations context, customer questions, and practical analysis come together.

## Profile keywords used
Data analyst, operations dashboards, CRM cleanup, customer success reporting, product analyst, B2B SaaS.

## Claims intentionally avoided
Product strategy ownership, senior analyst title, experimentation expertise, revenue impact, and private customer details.
MD,
            'output_sections' => $sections([
                ['about_section', 'About section'],
                ['profile_keywords_used', 'Profile keywords used'],
                ['claims_intentionally_avoided', 'Claims intentionally avoided'],
            ]),
            'checks' => [
                ['label' => 'About is public-safe', 'help' => 'No private contact or customer details appear.', 'evidence_sections' => ['about_section', 'claims_intentionally_avoided']],
                ['label' => 'Keywords have support', 'help' => 'Every keyword reflects supplied facts.', 'evidence_sections' => ['profile_keywords_used']],
                ['label' => 'Tone is restrained', 'help' => 'The copy avoids influencer exaggeration.', 'evidence_sections' => ['about_section']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'improve-experience-descriptions',
            'title' => 'Improve experience descriptions',
            'summary' => 'Rewrite experience bullets to show scope, action, and proof without inflated metrics.',
            'why_it_matters' => 'Experience entries are where readers check whether the positioning is real. Strong bullets show what you did and why it mattered. Missing metrics should stay missing rather than becoming fake numbers.',
            'unlocks_text' => 'Approving unlocks the skills and proof map.',
            'before_you_begin' => 'Use resume notes and current profile text as the fact base. Verify any numbers before keeping them. Remove confidential employer or customer details.',
            'common_problems' => 'The AI may add percentages, team sizes, tools, or impact claims not supplied. It may also rewrite bullets so broadly that your specific action disappears. Keep actions exact.',
            'recovery_guidance' => 'If bullets invent metrics, rerun with metrics banned and ask for qualitative results. If they are too bland, add one real detail about audience, cadence, or artifact.',
            'est_minutes' => 12,
            'prompt_template' => <<<TXT
You are improving LinkedIn experience descriptions. Fact rule: {$factRule}

Current profile text:
{{current_profile_text}}
Resume notes:
{{existing_resume}}
Strongest achievements:
{{strongest_achievements}}
Preferred tone: {{preferred_tone}}

Approved positioning:
{{artifact:define-professional-positioning}}

Approved About section:
{{artifact:rewrite-about-section}}

Produce Markdown with these exact headings:

## Experience entries
Rewrite selected experience bullets. For each bullet, include action, context, and honest result or purpose.

## Proof notes
Explain which supplied fact supports each improved bullet.

## Missing metrics
Metrics or outcomes that would be useful but were not supplied.

Keep headings in order. Do not invent numbers, tools, employers, or customer details.
TXT,
            'example_response' => <<<'MD'
## Experience entries
MapleOps, Data Analyst
- Built a weekly support-volume dashboard for operations meetings, helping the team review recurring workload questions in one place.
- Documented CRM cleanup steps for duplicate records so repeated cleanup work could be handled more consistently.
- Partnered with customer success managers to turn renewal-risk questions into a repeatable reporting format.

## Proof notes
The dashboard bullet comes from the weekly operations dashboard fact. The CRM bullet comes from the cleanup checklist fact. The customer success bullet comes from renewal-risk reporting work supplied in the resume notes.

## Missing metrics
No verified usage count, time saved, renewal outcome, or data quality percentage was supplied. Do not add those unless verified.
MD,
            'output_sections' => $sections([
                ['experience_entries', 'Experience entries'],
                ['proof_notes', 'Proof notes'],
                ['missing_metrics', 'Missing metrics'],
            ]),
            'checks' => [
                ['label' => 'Bullets use real actions', 'help' => 'Each bullet is grounded in supplied work.', 'evidence_sections' => ['experience_entries', 'proof_notes']],
                ['label' => 'Metrics are not invented', 'help' => 'Missing metrics remain missing.', 'evidence_sections' => ['missing_metrics']],
                ['label' => 'Entries support positioning', 'help' => 'Experience descriptions reinforce the approved profile direction.', 'evidence_sections' => ['experience_entries']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 2,
            'slug' => 'organize-skills-and-proof',
            'title' => 'Organize skills and proof',
            'summary' => 'Map skills, tools, and proof so the profile is credible and searchable.',
            'why_it_matters' => 'Skills lists are easy to stuff with unsupported keywords. A proof map helps you choose skills that match actual examples. It also shows which target-role skills need more proof before being emphasized.',
            'unlocks_text' => 'Approving unlocks content pillars.',
            'before_you_begin' => 'Review the improved experience entries. Keep only skills you can discuss. Separate current skills from skills you are learning.',
            'common_problems' => 'The AI may add fashionable tools or strategic skills not supplied. It may also bury practical skills that are actually well supported. Keep the map evidence-first.',
            'recovery_guidance' => 'If unsupported skills appear, move them to a learning or gap section. If the map is too long, keep the top ten profile skills only.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are organizing LinkedIn skills and proof. Fact rule: {$factRule}

Target role: {{target_role}}
Target industry: {{target_industry}}
Strongest achievements:
{{strongest_achievements}}
Constraints:
{{constraints_or_concerns}}

Approved experience entries:
{{artifact:improve-experience-descriptions}}

Produce Markdown with these exact headings:

## Skills and proof map
Map 8 to 12 skills or keywords to the proof that supports each one.

## Skills to de-emphasize
Skills that are unsupported, too broad, or not central to the target direction.

## Proof gaps to build
Practical proof the candidate could build later without inventing it now.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Skills and proof map
- Data analysis: dashboard and reporting work.
- Operations dashboards: weekly support-volume dashboard.
- CRM data quality: duplicate-record cleanup steps.
- Customer success reporting: renewal-risk questions turned into reports.
- Process documentation: cleanup checklist.
- Cross-functional communication: work with customer success managers.
- B2B SaaS operations: current MapleOps context.
- Product analytics direction: target direction, not current title.

## Skills to de-emphasize
Experiment design, product strategy, machine learning, executive analytics leadership, and revenue forecasting are not supported by the supplied facts.

## Proof gaps to build
Build a small product-metrics case study, document one analysis decision, and collect a verified example of a dashboard influencing a team decision.
MD,
            'output_sections' => $sections([
                ['skills_and_proof_map', 'Skills and proof map'],
                ['skills_to_de_emphasize', 'Skills to de-emphasize'],
                ['proof_gaps_to_build', 'Proof gaps to build'],
            ]),
            'checks' => [
                ['label' => 'Skills have proof', 'help' => 'Most skills map to a supplied artifact or fact.', 'evidence_sections' => ['skills_and_proof_map']],
                ['label' => 'Unsupported skills are controlled', 'help' => 'Weak claims are de-emphasized instead of inflated.', 'evidence_sections' => ['skills_to_de_emphasize']],
                ['label' => 'Gaps are future work', 'help' => 'Proof gaps are suggestions, not current claims.', 'evidence_sections' => ['proof_gaps_to_build']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 3,
            'slug' => 'draft-content-pillars',
            'title' => 'Draft content pillars',
            'summary' => 'Choose practical topics you could post or comment about without pretending to be an influencer.',
            'why_it_matters' => 'Content pillars can make a profile feel active, but they should fit your actual experience and comfort. This step creates low-pressure topics for posts, comments, or saved notes. It avoids performative thought leadership unless you want that style.',
            'unlocks_text' => 'Approving unlocks the profile consistency review.',
            'before_you_begin' => 'Decide whether you actually want to post, comment, or simply keep topics ready for networking. Keep confidential work out of examples. Choose a cadence you can sustain.',
            'common_problems' => 'The AI may suggest viral hooks, hot takes, or claims beyond your authority. It may also prescribe a posting schedule that does not fit your goals. Keep pillars modest and optional.',
            'recovery_guidance' => 'If content ideas feel performative, ask for comment prompts instead of posts. If they expose employer details, replace them with generalized lessons.',
            'est_minutes' => 8,
            'prompt_template' => <<<TXT
You are drafting LinkedIn content pillars. Fact rule: {$factRule}

Preferred tone: {{preferred_tone}}
Constraints:
{{constraints_or_concerns}}

Approved positioning:
{{artifact:define-professional-positioning}}

Approved skills and proof map:
{{artifact:organize-skills-and-proof}}

Produce Markdown with these exact headings:

## Content pillars
Three to five practical topics the candidate can post, comment, or save notes about.

## Sample post ideas
Five low-pressure ideas. Avoid viral hooks and exaggerated authority.

## Comment prompts
Short prompts for thoughtful comments on other people's posts.

## Privacy boundaries
Work details, customer details, or employer information to keep out.

Keep headings in order.
TXT,
            'example_response' => <<<'MD'
## Content pillars
- Turning recurring operations questions into reports.
- Data cleanup as practical team support.
- Moving from operations analytics toward product analytics.
- Working with customer success questions in B2B SaaS.

## Sample post ideas
1. A short note on why repeated questions are a signal to document a report.
2. A before-and-after lesson from cleaning duplicate CRM records without naming the employer.
3. What I am learning about product analytics from operations reporting.
4. A checklist for making dashboards easier to discuss in meetings.
5. A reflection on translating customer success questions into reporting requirements.

## Comment prompts
- "This matches what I have seen when recurring questions become reporting needs."
- "I like the emphasis on making the decision clearer, not just adding a chart."
- "Curious how you decide when a one-off question deserves a repeatable report."

## Privacy boundaries
Do not name customers, renewal risks, internal metrics, private CRM fields, or employer-specific strategy.
MD,
            'output_sections' => $sections([
                ['content_pillars', 'Content pillars'],
                ['sample_post_ideas', 'Sample post ideas'],
                ['comment_prompts', 'Comment prompts'],
                ['privacy_boundaries', 'Privacy boundaries'],
            ]),
            'checks' => [
                ['label' => 'Pillars fit the profile', 'help' => 'Topics connect to positioning and proof.', 'evidence_sections' => ['content_pillars']],
                ['label' => 'No influencer exaggeration', 'help' => 'Ideas avoid viral hooks and unsupported authority.', 'evidence_sections' => ['sample_post_ideas']],
                ['label' => 'Privacy is protected', 'help' => 'Boundaries block confidential work details.', 'evidence_sections' => ['privacy_boundaries']],
            ],
        ]),
        sm_career_recipe([
            'stage_position' => 4,
            'slug' => 'profile-consistency-review',
            'title' => 'Profile consistency review',
            'summary' => 'Review headline, About, experience, skills, and content pillars for one coherent profile.',
            'why_it_matters' => 'A profile can be truthful section by section and still feel scattered. This review checks that the same story appears across headline, About, experience, and skills. It also catches privacy issues before public updates.',
            'unlocks_text' => 'Approving completes the Cookbook and opens your LinkedIn profile kit.',
            'before_you_begin' => 'Gather all approved profile artifacts. Confirm what you are comfortable posting publicly. Keep any final profile update inside LinkedIn, not across unrelated Cookbooks.',
            'common_problems' => 'Consistency reviews can add new claims while trying to smooth the story. They may also ignore privacy boundaries. This step should review and package, not invent.',
            'recovery_guidance' => 'If the review adds new facts, remove them and rerun with approved artifacts only. If the checklist is too abstract, ask for section-by-section edits.',
            'est_minutes' => 9,
            'prompt_template' => <<<TXT
You are reviewing a LinkedIn profile update for consistency. Fact rule: {$factRule}

Preferred tone: {{preferred_tone}}
Constraints:
{{constraints_or_concerns}}

Approved headline:
{{artifact:compare-headline-options}}

Approved About section:
{{artifact:rewrite-about-section}}

Approved experience entries:
{{artifact:improve-experience-descriptions}}

Approved skills and proof:
{{artifact:organize-skills-and-proof}}

Approved content pillars:
{{artifact:draft-content-pillars}}

Produce Markdown with these exact headings:

## Consistency checklist
Section-by-section checklist for headline, About, experience, skills, and content pillars.

## Final profile package
The recommended headline, About section, top experience edits, and top skills to update.

## Final risk review
Truth, tone, privacy, and unsupported-claim risks to fix before publishing.

Keep headings in order. Do not introduce new career facts.
TXT,
            'example_response' => <<<'MD'
## Consistency checklist
- [ ] Headline names data analyst work and supported skills.
- [ ] About section explains current experience and target direction without claiming product analyst title.
- [ ] Experience entries support dashboards, cleanup, and customer success reporting.
- [ ] Skills list matches the proof map.
- [ ] Content topics avoid confidential employer and customer details.

## Final profile package
Recommended headline: Data Analyst | Operations Dashboards, CRM Cleanup, Customer Success Reporting.

Use the approved About section. Update MapleOps experience with the dashboard, CRM cleanup, and renewal-risk reporting bullets. Prioritize skills: data analysis, operations dashboards, CRM data quality, customer success reporting, process documentation, cross-functional communication.

## Final risk review
Do not claim product strategy ownership, experimentation expertise, seniority, revenue impact, or customer-specific details. Keep the tone clear and professional rather than hype-driven.
MD,
            'output_sections' => $sections([
                ['consistency_checklist', 'Consistency checklist'],
                ['final_profile_package', 'Final profile package'],
                ['final_risk_review', 'Final risk review'],
            ]),
            'checks' => [
                ['label' => 'Sections align', 'help' => 'Headline, About, experience, and skills tell the same story.', 'evidence_sections' => ['consistency_checklist', 'final_profile_package']],
                ['label' => 'No new facts appear', 'help' => 'The package uses approved artifacts only.', 'evidence_sections' => ['final_profile_package']],
                ['label' => 'Risk review is practical', 'help' => 'Truth, tone, and privacy risks are named before publishing.', 'evidence_sections' => ['final_risk_review']],
            ],
        ]),
    ],
];
