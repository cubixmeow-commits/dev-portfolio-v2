<?php

declare(strict_types=1);

/**
 * Seed content for SousMeow v1. Everything here is real, purposeful
 * content: one executable Cookbook (Launch Day Kit) with four Recipes,
 * eight Pantry fields across six ingredient types, three Quality Checks
 * per Recipe, one realistic example AI response per Recipe, and seven
 * presentation-only marketplace Cookbooks.
 *
 * Prompt templates use {{field_key}} placeholders for Pantry values and
 * {{artifact:recipe-slug}} for the approved Artifact of an earlier
 * Recipe. The example responses are written for a fictional product
 * called Driftlog and are always labeled as sample data in the UI.
 */

$positioningExample = <<<'MD'
## Positioning statement

Driftlog is a passive time logger for freelance designers who bill by the hour but refuse to babysit a timer. It watches the apps you already work in, then turns your day into clean, client-ready entries in one review tap. You reclaim billable hours you were quietly writing off, without changing how you work.

## The one-sentence version

Driftlog remembers your workday so you can bill it, no timers, no Friday archaeology.

## Who it is for

Freelance designers and small studios juggling several clients a week. They lose money two ways: hours they forget to log, and Friday afternoons spent reconstructing the week from calendar scraps and file timestamps. Driftlog is for the person who has tried four timer apps and abandoned all of them.

## Why it wins

1. **Capture is passive.** Every timer app fails at the same moment: when you forget to press start. Driftlog has no start button to forget.
2. **Review is one tap.** The day review turns raw activity into tidy entries you approve in under a minute, so the log stays honest without becoming a chore.
3. **The output is client-ready.** Weekly summaries are formatted to send as a link, not exported into a spreadsheet you still have to clean up.
4. **It respects the offline reality.** Cafe wifi drops; the log does not. Everything works offline and syncs later.

## What we deliberately do not claim

Driftlog does not promise to increase your rates, win you clients, or manage projects. It does one thing: make sure the hours you actually worked end up on the invoice.
MD;

$landingExample = <<<'MD'
## Hero

**Headline:** Bill the hours you actually worked.

**Subheadline:** Driftlog quietly logs your workday from the apps you already use, then turns it into clean, client-ready time entries with one tap. No timers. No Friday reconstruction.

**Primary button:** Start logging free
**Secondary link:** See a sample weekly summary

## Feature blocks

### 1. Capture without the start button
Timers fail the moment you forget them. Driftlog captures your working time passively from your design tools, browser, and files, so a deep-focus afternoon never disappears from the record.

### 2. Review a day in under a minute
Open the day review, and your raw activity is already grouped into draft entries. Tap to approve, drag to adjust, done. The log stays accurate because keeping it accurate costs almost nothing.

### 3. Summaries your clients actually read
Send a weekly summary as a clean link: hours by project, what moved, what is next. It looks professional because it is generated from real activity, not memory.

## Closing call to action

**Line:** Your week is already logged. Come approve it.
**Button:** Start logging free

## Footer reassurance

Works offline and syncs when you are back. Your activity data stays on your devices until you choose to share a summary.
MD;

$announcementExample = <<<'MD'
## Product Hunt launch post

**Tagline:** Passive time logging for freelancers who hate timers

**First comment:**
Hey hunters! I built Driftlog after realizing I was losing two to three billable hours every week, not because I did not work them, but because I never wrote them down.

Driftlog takes a different approach to time tracking: there is no timer. It passively captures your working time from the tools you already use, and every evening (or Friday, no judgement) you approve a pre-built draft of your day in about a minute. Weekly client summaries come out the other end as a shareable link.

It is 9 dollars a month, works offline, and your data stays on your device until you share it. I would love to hear how you all handle the "what did I even do on Tuesday" problem.

## X (Twitter) thread

**Post 1:** I lost about 10 percent of my billable hours to bad memory. Not bad work. Bad memory. So I built Driftlog: time logging with no timer.

**Post 2:** How it works: Driftlog watches your actual work activity (design tools, browser, files), builds a draft of your day, and you approve it in one tap. The log stays honest because honesty takes 60 seconds.

**Post 3:** At the end of the week you get a client-ready summary you can send as a link. Hours by project, what moved, what is next. No spreadsheet cleanup.

**Post 4:** It is live today at https://driftlog.example, 9 dollars a month, works offline. If you have ever reconstructed a Tuesday from file timestamps, this one is for you.

## Email newsletter announcement

**Subject:** Stop reconstructing your week from memory

**Body:**
Hi there,

Quick and honest: if you bill by the hour and you have never forgotten to start a timer, this email is not for you.

For everyone else, today I am launching Driftlog. It logs your working time passively from the apps you already use, then asks you to approve a tidy draft of your day in about a minute. At the end of the week, you get a client-ready summary you can send as a link.

No timers to forget. Works offline. 9 dollars a month, with a free start so you can see a real week of your own data before paying anything.

Have a look at https://driftlog.example, and reply to this email if you have questions. I read everything.
MD;

$faqExample = <<<'MD'
## Frequently asked questions

**Is Driftlog a surveillance tool?**
No. Driftlog is built for you, not your boss. Activity is captured locally, stays on your devices, and nothing is shared until you explicitly send a summary. There is no team dashboard and no manager view.

**What exactly does it capture?**
Which applications and documents you were actively working in, and for how long. It does not record your screen, your keystrokes, or the content of your files.

**What does it cost?**
9 dollars a month. You can run a full week free first, so your decision is based on your own real data rather than a demo.

**Does it work offline?**
Yes. Capture and day review work fully offline; summaries sync when you are back online. Cafe wifi is not a data-loss event.

**I already use a timer app. Why switch?**
If your timer log is complete and accurate, do not switch. Driftlog exists for people whose timers are full of forgotten starts and 9-hour "lunch" entries.

## Objections, answered honestly

**"Passive tracking sounds creepy."**
Fair instinct. The difference is who the data serves: Driftlog has no employer features and no cloud copy of raw activity. You review everything before it becomes a log entry.

**"One more subscription?"**
If Driftlog does not recover at least one billable hour a month, it is not paying for itself and you should cancel. For most hourly freelancers, one recovered hour covers several months of Driftlog.

**"I can just be more disciplined."**
You can, and Friday-you has been saying that for years. Tools that require discipline at the exact moment discipline fails tend to lose. Driftlog moves the discipline to a one-minute review, where it is cheap.
MD;

return [
    'executable' => [
        'slug'        => 'launch-day-kit',
        'title'       => 'Launch Day Kit',
        'tagline'     => 'Turn your product notes into a complete, honest launch kit.',
        'description' => "Launching is mostly writing: positioning, a landing page, announcement posts, and answers to the questions people will actually ask. This Cookbook walks you through all four, one Recipe at a time. You stock the Pantry once with the facts about your product; every Recipe builds its prompt from those facts, so nothing gets invented and everything sounds like you. At the end you export a Project Kit with every approved piece, ready to publish.",
        'category'    => 'Marketing',
        'audience'    => 'Indie makers and small teams shipping a v1',
        'outcome'     => 'Positioning, landing page copy, channel announcements, and a FAQ, exported as one kit',
        'price_cents' => null,
        'accent'      => 'terracotta',
        'est_minutes' => 25,
        'sort_order'  => 1,
        'fields' => [
            [
                'field_key'    => 'product_name',
                'label'        => 'Product name',
                'type'         => 'text',
                'help'         => 'Used everywhere your product is named. Exact spelling and capitalization.',
                'placeholder'  => 'e.g. Driftlog',
                'sample_value' => 'Driftlog',
            ],
            [
                'field_key'    => 'one_liner',
                'label'        => 'One-line description',
                'type'         => 'text',
                'help'         => 'One sentence: what it is and who it serves. Recipes sharpen it; they never replace it.',
                'placeholder'  => 'e.g. Effortless time logging for freelance designers',
                'sample_value' => 'Effortless time logging for freelance designers',
            ],
            [
                'field_key'    => 'audience',
                'label'        => 'Who is it for?',
                'type'         => 'textarea',
                'help'         => 'Describe the person, their situation, and the pain they feel today. Two or three sentences beat a demographic list.',
                'placeholder'  => 'Who are they? What do they struggle with right now?',
                'sample_value' => "Freelance designers and small studios who bill by the hour but hate timers. They juggle several clients a week, forget to track as they go, and reconstruct their week from memory every Friday.",
            ],
            [
                'field_key'    => 'key_features',
                'label'        => 'Key features',
                'type'         => 'textarea',
                'help'         => 'One feature per line. Recipes quote from this list and are instructed never to invent capabilities.',
                'placeholder'  => "One feature per line",
                'sample_value' => "Passive time capture from the apps you already use\nOne-tap day review that turns activity into clean entries\nClient-ready weekly summaries you can send as a link\nWorks offline, syncs when you are back",
            ],
            [
                'field_key'    => 'tone',
                'label'        => 'Voice and tone',
                'type'         => 'select',
                'help'         => 'Every Recipe writes in this voice, so the whole kit sounds like one author.',
                'options'      => ['Warm and friendly', 'Clear and professional', 'Playful and bold', 'Quietly confident'],
                'sample_value' => 'Quietly confident',
            ],
            [
                'field_key'    => 'channels',
                'label'        => 'Launch channels',
                'type'         => 'multiselect',
                'help'         => 'Pick where you will announce. The announcements Recipe writes one post per channel, shaped for that channel.',
                'options'      => ['Product Hunt', 'X (Twitter)', 'LinkedIn', 'Email newsletter', 'Indie Hackers'],
                'sample_value' => 'Product Hunt, X (Twitter), Email newsletter',
            ],
            [
                'field_key'    => 'price_usd',
                'label'        => 'Monthly price (USD)',
                'type'         => 'number',
                'help'         => 'Stated plainly in the FAQ and objection handling. Enter 0 if it is free.',
                'placeholder'  => '9',
                'sample_value' => '9',
            ],
            [
                'field_key'    => 'website_url',
                'label'        => 'Website URL',
                'type'         => 'url',
                'help'         => 'Where every announcement should point. Include https://.',
                'placeholder'  => 'https://',
                'sample_value' => 'https://driftlog.example',
            ],
        ],
        'recipes' => [
            [
                'slug'    => 'position-the-dish',
                'title'   => 'Position the Dish',
                'summary' => 'Nail what your product is, who it serves, and why it wins.',
                'why_it_matters' => 'Every later Recipe quotes this positioning. Ten minutes here saves an hour of inconsistent copy later, and it is the piece most products skip.',
                'unlocks_text'   => 'Approving this unlocks the Landing Page Recipe, which reuses this positioning word for word.',
                'est_minutes'    => 6,
                'prompt_template' => <<<'TXT'
You are a positioning-focused product marketer. Write positioning for a product using only the facts below. Do not invent features, statistics, customers, or claims.

Product name: {{product_name}}
One-line description: {{one_liner}}
Who it is for: {{audience}}
Features (the complete list, do not add to it):
{{key_features}}
Voice: {{tone}}

Produce, in Markdown with the exact section headings given:

## Positioning statement
A short paragraph: what it is, who it is for, the pain it removes, and the outcome. Plain language, no hype words (revolutionary, game-changing, seamless).

## The one-sentence version
One sentence a customer could repeat to a friend.

## Who it is for
Two or three sentences that show you understand this person's week, not just their job title.

## Why it wins
A numbered list, one item per real feature, each framed as the customer's gain rather than the capability.

## What we deliberately do not claim
Two or three honest limits. This section builds trust; do not skip it.

Keep the whole thing under 450 words. Voice: {{tone}}.
TXT,
                'example_response' => $positioningExample,
                'checks' => [
                    ['label' => 'It sounds like your product', 'help' => 'No borrowed hype and nothing you could not say out loud to a customer.'],
                    ['label' => 'No invented features or claims', 'help' => 'Every capability mentioned appears in your Pantry feature list.'],
                    ['label' => 'A stranger could repeat it', 'help' => 'After one read, someone could explain your product to a friend.'],
                ],
            ],
            [
                'slug'    => 'plate-the-landing-page',
                'title'   => 'Plate the Landing Page',
                'summary' => 'Write the hero, feature blocks, and call to action for your landing page.',
                'why_it_matters' => 'Your landing page is where every announcement sends people. This Recipe turns the approved positioning into copy with one job: make the next click obvious.',
                'unlocks_text'   => 'Approving this unlocks the Announcements Recipe, which links people to this page.',
                'est_minutes'    => 6,
                'prompt_template' => <<<'TXT'
You are a conversion copywriter who writes plainly. Using the approved positioning below and only the listed features, write landing page copy.

Product name: {{product_name}}
Website: {{website_url}}
Voice: {{tone}}
Features (complete list, do not add to it):
{{key_features}}

Approved positioning (treat as ground truth):
{{artifact:position-the-dish}}

Produce, in Markdown with the exact section headings given:

## Hero
A headline of at most 8 words that passes the glance test, a subheadline of at most 35 words, a primary button label of at most 4 words, and one secondary link label.

## Feature blocks
Exactly three blocks. Each: a heading of at most 6 words and a 2 or 3 sentence body that leads with the customer's gain. Cover the most decision-driving features; do not force all features in.

## Closing call to action
One short line plus the button label again.

## Footer reassurance
One or two sentences that remove the biggest hesitation (privacy, effort, or lock-in), using only true facts from the positioning.

No emoji, no exclamation marks, under 350 words total. Voice: {{tone}}.
TXT,
                'example_response' => $landingExample,
                'checks' => [
                    ['label' => 'Headline passes the glance test', 'help' => 'Someone skimming for three seconds still learns what this is.'],
                    ['label' => 'Every feature claim is real', 'help' => 'The three blocks only describe features from your Pantry list.'],
                    ['label' => 'One clear next step', 'help' => 'A single primary action, stated the same way each time it appears.'],
                ],
            ],
            [
                'slug'    => 'serve-the-announcements',
                'title'   => 'Serve the Announcements',
                'summary' => 'Write a launch post shaped for each channel you picked.',
                'why_it_matters' => 'A launch that is one paragraph pasted into five places reads as spam five times. Each channel has its own etiquette; this Recipe writes for each one you chose.',
                'unlocks_text'   => 'Approving this unlocks the final Recipe: the FAQ your announcements will generate questions for.',
                'est_minutes'    => 7,
                'prompt_template' => <<<'TXT'
You are launching a product and writing announcements that respect each channel's culture. Use only the facts in the positioning below; invent nothing.

Product name: {{product_name}}
Link to include: {{website_url}}
Price: {{price_usd}} USD per month (say "free" if 0)
Voice: {{tone}}
Channels to write for: {{channels}}

Approved positioning (ground truth):
{{artifact:position-the-dish}}

For each listed channel, produce a Markdown section titled "## [channel name] launch post" containing a post shaped for that channel:
- Product Hunt: a tagline of at most 60 characters plus a founder's first comment of 120 to 180 words that tells the honest origin story.
- X (Twitter): a thread of 3 to 5 numbered posts, each under 260 characters, first post hooks with the problem.
- LinkedIn: one post of 120 to 180 words, professional but human, no hashtag spam (at most 3).
- Email newsletter: a subject line under 50 characters and a body of 120 to 180 words written to one reader.
- Indie Hackers: a post of 150 to 220 words leading with a real number or honest struggle, community-first, sell last.

Only write sections for the channels listed above. Include the link naturally in each. Voice: {{tone}}.
TXT,
                'example_response' => $announcementExample,
                'checks' => [
                    ['label' => 'Each post fits its channel', 'help' => 'The Product Hunt comment, thread, and email read differently, not one blurb copied around.'],
                    ['label' => 'Links and price are correct', 'help' => 'Every post points at your URL and any price mentioned matches your Pantry.'],
                    ['label' => 'It reads in your voice', 'help' => 'You would post each of these under your own name without editing the tone.'],
                ],
            ],
            [
                'slug'    => 'answer-the-table',
                'title'   => 'Answer the Table',
                'summary' => 'Prepare honest answers for the questions and objections launch day brings.',
                'why_it_matters' => 'Launch day comments decide how your product is perceived. Answering fast and honestly beats answering perfectly; this Recipe prepares both the questions and the honest answers in advance.',
                'unlocks_text'   => 'Approving this completes the Cookbook and unlocks your Project Kit export.',
                'est_minutes'    => 6,
                'prompt_template' => <<<'TXT'
You are preparing a founder for launch day questions. Using only the facts below, write a FAQ and objection-handling section. Honesty is the strategy: when the product has a real limit, state it and say who the product is not for.

Product name: {{product_name}}
Price: {{price_usd}} USD per month (say "free" if 0)
Website: {{website_url}}
Voice: {{tone}}
Features (complete list):
{{key_features}}

Approved positioning (ground truth):
{{artifact:position-the-dish}}

Produce, in Markdown with the exact section headings given:

## Frequently asked questions
Five questions a skeptical first-time visitor would actually ask (about how it works, data or privacy if relevant, price, and switching costs), each answered in 2 to 4 plain sentences. State the price plainly in one of them.

## Objections, answered honestly
The three hardest objections, each as a bold quoted objection followed by an answer that concedes what is true before making the counterpoint. No objection may be a softball.

Under 450 words total. Voice: {{tone}}.
TXT,
                'example_response' => $faqExample,
                'checks' => [
                    ['label' => 'Answers are honest', 'help' => 'Real limits are admitted; at least one answer says who this is not for.'],
                    ['label' => 'Price is stated plainly', 'help' => 'The price appears in the FAQ exactly as set in your Pantry.'],
                    ['label' => 'No unbuilt promises', 'help' => 'Nothing is promised that is not in your feature list today.'],
                ],
            ],
        ],
    ],

    'marketplace' => [
        [
            'slug'        => 'cold-outreach-kit',
            'title'       => 'Cold Outreach Kit',
            'tagline'     => 'Outreach that gets replies because it respects the reader.',
            'description' => 'A five-Recipe path from "who exactly am I writing to" through a first email that earns a reply, a follow-up ladder that is persistent without being creepy, and honest replies to the brush-offs you will get.',
            'category'    => 'Sales',
            'audience'    => 'Freelancers and founders doing their own outbound',
            'outcome'     => 'A prospect profile, first email, follow-up sequence, and objection replies',
            'price_cents' => 1900,
            'accent'      => 'teal',
            'est_minutes' => 35,
            'sort_order'  => 10,
            'recipes' => [
                ['slug' => 'prospect-profile', 'title' => 'Prospect Profile', 'summary' => 'Define one specific reader before writing a single line.'],
                ['slug' => 'the-first-email', 'title' => 'The First Email', 'summary' => 'Ninety words that earn a reply, not a pitch deck in prose.'],
                ['slug' => 'follow-up-ladder', 'title' => 'The Follow-up Ladder', 'summary' => 'Three follow-ups that add value instead of just bumping.'],
                ['slug' => 'brush-off-replies', 'title' => 'Brush-off Replies', 'summary' => 'Graceful answers to "not now", "too expensive", and silence.'],
                ['slug' => 'send-checklist', 'title' => 'The Send Checklist', 'summary' => 'A final pass that catches the embarrassing mistakes.'],
            ],
        ],
        [
            'slug'        => 'case-study-kitchen',
            'title'       => 'Case Study Kitchen',
            'tagline'     => 'Turn one happy customer into your best sales page.',
            'description' => 'From interview questions that get real quotes, through a story skeleton, a full write-up, and a one-page version for people who will never read the long one.',
            'category'    => 'Marketing',
            'audience'    => 'Consultants and product teams with at least one happy customer',
            'outcome'     => 'Interview kit, full case study, pull quotes, and a one-pager',
            'price_cents' => 2400,
            'accent'      => 'amber',
            'est_minutes' => 45,
            'sort_order'  => 11,
            'recipes' => [
                ['slug' => 'interview-menu', 'title' => 'Interview Menu', 'summary' => 'Questions that surface numbers and quotable moments.'],
                ['slug' => 'story-skeleton', 'title' => 'Story Skeleton', 'summary' => 'Before, struggle, decision, after: the bones of the story.'],
                ['slug' => 'the-write-up', 'title' => 'The Write-up', 'summary' => 'The full case study, told in the customer\'s words.'],
                ['slug' => 'pull-quotes-and-stats', 'title' => 'Pull Quotes and Stats', 'summary' => 'The five lines you will reuse everywhere for a year.'],
                ['slug' => 'one-page-version', 'title' => 'The One-Pager', 'summary' => 'The skimmable version for the person who decides.'],
            ],
        ],
        [
            'slug'        => 'blog-batch-prep',
            'title'       => 'Blog Batch Prep',
            'tagline'     => 'Plan and draft a month of posts in one honest sitting.',
            'description' => 'Batch the thinking, then batch the drafting. A topic pantry built from questions your audience already asks, outlines for four posts, focused draft sprints, and an edit pass that respects your voice.',
            'category'    => 'Content',
            'audience'    => 'Solo builders who keep meaning to blog',
            'outcome'     => 'Four outlined and drafted posts plus a reusable topic backlog',
            'price_cents' => null,
            'accent'      => 'sage',
            'est_minutes' => 60,
            'sort_order'  => 12,
            'recipes' => [
                ['slug' => 'topic-pantry', 'title' => 'Topic Pantry', 'summary' => 'A backlog built from real questions, not keyword tools.'],
                ['slug' => 'outline-batch', 'title' => 'Outline Batch', 'summary' => 'Four outlines with one promise each.'],
                ['slug' => 'draft-sprints', 'title' => 'Draft Sprints', 'summary' => 'Drafts written fast against the outline, ugly first.'],
                ['slug' => 'edit-passes', 'title' => 'Edit Passes', 'summary' => 'Three passes: truth, structure, then sentences.'],
            ],
        ],
        [
            'slug'        => 'naming-tasting-menu',
            'title'       => 'Naming Tasting Menu',
            'tagline'     => 'Name your product without convening a committee.',
            'description' => 'A structured tasting: map the flavors your name should carry, generate wide rounds, stress-test the shortlist against pronunciation, confusion, and cringe, then sanity-check availability before you fall in love.',
            'category'    => 'Branding',
            'audience'    => 'Founders naming a product, feature, or company',
            'outcome'     => 'A tested shortlist of three names with rationale',
            'price_cents' => 1200,
            'accent'      => 'lilac',
            'est_minutes' => 30,
            'sort_order'  => 13,
            'recipes' => [
                ['slug' => 'flavor-map', 'title' => 'Flavor Map', 'summary' => 'What the name must say, suggest, and never imply.'],
                ['slug' => 'name-rounds', 'title' => 'Name Rounds', 'summary' => 'Wide generation across five naming styles.'],
                ['slug' => 'shortlist-stress-test', 'title' => 'Shortlist Stress Test', 'summary' => 'Say it on the phone, shout it in a bar, put it on an invoice.'],
                ['slug' => 'availability-pass', 'title' => 'Availability Pass', 'summary' => 'A human-checked sweep of domains and collisions.'],
            ],
        ],
        [
            'slug'        => 'job-hunt-mise-en-place',
            'title'       => 'Job Hunt Mise en Place',
            'tagline'     => 'Everything prepped before the applications start.',
            'description' => 'Get your ingredients ready first: a full experience inventory, a resume tailored to one real posting, a cover letter base you can adapt in minutes, and an interview story bank in the shape interviewers actually ask for.',
            'category'    => 'Career',
            'audience'    => 'Developers and designers preparing a focused search',
            'outcome'     => 'Experience inventory, tailored resume, letter base, and story bank',
            'price_cents' => null,
            'accent'      => 'teal',
            'est_minutes' => 50,
            'sort_order'  => 14,
            'recipes' => [
                ['slug' => 'experience-inventory', 'title' => 'Experience Inventory', 'summary' => 'Every project, number, and save-the-day story in one place.'],
                ['slug' => 'tailored-resume', 'title' => 'Tailored Resume', 'summary' => 'One page aimed at one specific posting.'],
                ['slug' => 'cover-letter-base', 'title' => 'Cover Letter Base', 'summary' => 'A base letter that adapts in five minutes, not fifty.'],
                ['slug' => 'interview-story-bank', 'title' => 'Interview Story Bank', 'summary' => 'Eight stories in situation-action-result shape.'],
            ],
        ],
        [
            'slug'        => 'customer-interview-digest',
            'title'       => 'Customer Interview Digest',
            'tagline'     => 'From messy interview notes to decisions you can defend.',
            'description' => 'Wrangle raw notes into clean observations, extract themes without flattening disagreement, build a quote bank with real attribution, and end with a decision memo your team can argue with productively.',
            'category'    => 'Research',
            'audience'    => 'Product folks sitting on a pile of interview notes',
            'outcome'     => 'Cleaned notes, themes, quote bank, and a decision memo',
            'price_cents' => 1900,
            'accent'      => 'amber',
            'est_minutes' => 40,
            'sort_order'  => 15,
            'recipes' => [
                ['slug' => 'note-wrangle', 'title' => 'Note Wrangle', 'summary' => 'Raw notes become clean, attributed observations.'],
                ['slug' => 'theme-extraction', 'title' => 'Theme Extraction', 'summary' => 'Patterns, tensions, and the opinions that disagree.'],
                ['slug' => 'quote-bank', 'title' => 'Quote Bank', 'summary' => 'The lines you will quote in every roadmap debate.'],
                ['slug' => 'decision-memo', 'title' => 'Decision Memo', 'summary' => 'What we learned, what we will do, what we still do not know.'],
            ],
        ],
        [
            'slug'        => 'ux-microcopy-pantry',
            'title'       => 'UX Microcopy Pantry',
            'tagline'     => 'The small words that make software feel cared for.',
            'description' => 'Calibrate your product voice once, then batch-write the copy nobody plans for: error messages that help instead of blame, empty states that teach, and onboarding nudges that respect attention.',
            'category'    => 'Product',
            'audience'    => 'Developers shipping products without a writer on the team',
            'outcome'     => 'A voice guide plus batches of errors, empty states, and nudges',
            'price_cents' => null,
            'accent'      => 'sage',
            'est_minutes' => 35,
            'sort_order'  => 16,
            'recipes' => [
                ['slug' => 'voice-calibration', 'title' => 'Voice Calibration', 'summary' => 'Three sliders and ten example sentences that define your voice.'],
                ['slug' => 'error-message-batch', 'title' => 'Error Message Batch', 'summary' => 'Errors that say what happened and what to do next.'],
                ['slug' => 'empty-states', 'title' => 'Empty States', 'summary' => 'First-run screens that teach instead of apologize.'],
                ['slug' => 'onboarding-nudges', 'title' => 'Onboarding Nudges', 'summary' => 'The three moments worth interrupting, and the words for them.'],
            ],
        ],
    ],
];
