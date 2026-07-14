<?php

declare(strict_types=1);

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
Open the day review and your raw activity is already grouped into draft entries. Tap to approve, drag to adjust, done. Keeping the log honest costs about a minute a day, which is exactly why it stays honest.

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

## X (Twitter) launch post

**Post 1:** I lost about 10 percent of my billable hours to bad memory. Not bad work. Bad memory. So I built Driftlog: time logging with no timer.

**Post 2:** How it works: Driftlog watches your actual work activity (design tools, browser, files), builds a draft of your day, and you approve it in one tap. The log stays honest because honesty takes 60 seconds.

**Post 3:** At the end of the week you get a client-ready summary you can send as a link. Hours by project, what moved, what is next. No spreadsheet cleanup.

**Post 4:** It is live today at https://driftlog.example, 9 dollars a month, works offline. If you have ever reconstructed a Tuesday from file timestamps, this one is for you.

## Email newsletter launch post

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
9 dollars a month, cancel whenever. You run a full week free first, so the decision is made on your own real data, not a demo.

**Does it work offline?**
Yes. Capture and day review work fully offline; summaries sync when you are back online. A dropped cafe connection costs you nothing.

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
    'slug'                => 'launch-day-kit',
    'title'               => 'Launch Day Kit',
    'tagline'             => 'Turn plain product notes into everything you publish on launch day.',
    'description'         => "Launch day is a writing day: the positioning that explains you, the landing page that sells you, the announcements that bring people in, and honest answers for the questions that follow. This Cookbook walks you through all four steps. Enter the plain facts about your product once; every prompt uses those facts and invents nothing. What you approve is what you ship: ready-to-publish files that sound like you on your best day.",
    'primary_category'    => 'marketing-growth',
    'collections'         => ['start-here', 'selected-by-sousmeow', 'launch-something'],
    'audience'            => 'Indie makers and small teams shipping a v1',
    'outcome'             => 'positioning, landing page copy, announcements for your channels, and an honest FAQ',
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'terracotta',
    'difficulty'          => 'Intermediate',
    'est_minutes'         => 25,
    'demo_completed_runs' => 847,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 1,
    'stages' => [
        ['title' => 'Position', 'summary' => 'Lock the story every later asset will quote.'],
        ['title' => 'Convert', 'summary' => 'Turn positioning into a page that earns the click.'],
        ['title' => 'Announce', 'summary' => 'Write native posts for each launch channel.'],
        ['title' => 'Defend', 'summary' => 'Prepare honest answers before the comments arrive.'],
    ],
    'fields' => [
        [
            'field_key'    => 'product_name',
            'label'        => 'Product name',
            'type'         => 'text',
            'help'         => 'Spelled and capitalized exactly as it should appear everywhere.',
            'placeholder'  => 'e.g. Driftlog',
            'sample_value' => 'Driftlog',
        ],
        [
            'field_key'    => 'one_liner',
            'label'        => 'One-line description',
            'type'         => 'text',
            'help'         => 'One sentence: what it is and who it serves. The Recipes sharpen this; they never replace it.',
            'placeholder'  => 'e.g. Effortless time logging for freelance designers',
            'sample_value' => 'Effortless time logging for freelance designers',
        ],
        [
            'field_key'    => 'audience',
            'label'        => 'Who is it for?',
            'type'         => 'textarea',
            'help'         => 'Describe the person and the pain they feel today. Two honest sentences beat a demographic list.',
            'placeholder'  => 'Who are they? What do they struggle with right now?',
            'sample_value' => "Freelance designers and small studios who bill by the hour but hate timers. They juggle several clients a week, forget to track as they go, and reconstruct their week from memory every Friday.",
        ],
        [
            'field_key'    => 'key_features',
            'label'        => 'Key features',
            'type'         => 'textarea',
            'help'         => 'One feature per line. Every Recipe quotes this list and is told to invent nothing beyond it.',
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
            'help'         => 'Pick where you will announce. The announcements Recipe writes one native post per channel you choose.',
            'options'      => ['Product Hunt', 'X (Twitter)', 'LinkedIn', 'Email newsletter', 'Indie Hackers'],
            'sample_value' => 'Product Hunt, X (Twitter), Email newsletter',
        ],
        [
            'field_key'    => 'price_usd',
            'label'        => 'Monthly price (USD)',
            'type'         => 'number',
            'help'         => 'Stated plainly in the FAQ, exactly as entered here. Enter 0 if it is free.',
            'placeholder'  => '9',
            'sample_value' => '9',
        ],
        [
            'field_key'    => 'website_url',
            'label'        => 'Website URL',
            'type'         => 'url',
            'help'         => 'Every announcement points here. Include the https://.',
            'placeholder'  => 'https://',
            'sample_value' => 'https://driftlog.example',
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'position-the-dish',
            'title'            => 'Define your positioning',
            'summary'          => 'Clarify what your product is, who it is for, and why it matters.',
            'why_it_matters'   => 'Every later step quotes this positioning word for word. Ten focused minutes here is why the whole project will sound like one person wrote it.',
            'unlocks_text'     => 'Approving unlocks the landing page step, which builds on this positioning.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are a positioning-focused product marketer. Write positioning for a product using only the facts below. Do not invent features, statistics, customers, or claims.

Product name: {{product_name}}
One-line description: {{one_liner}}
Who it is for: {{audience}}
Features (the complete list, do not add to it):
{{key_features}}
Voice: {{tone}}

Produce, in Markdown with the exact section headings given. Write each heading as a plain ATX Markdown heading on its own line, like "## Section name". Do not bold the headings, do not number them, and do not wrap your whole response in a code block or code fence:

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

Keep the five section headings exactly as written above, in that order, with each part under its heading. Keep the whole thing under 450 words. Voice: {{tone}}.
TXT,
            'example_response' => $positioningExample,
            'output_sections' => [
                ['key' => 'positioning_statement', 'heading' => 'Positioning statement', 'required' => true],
                ['key' => 'one_sentence_version', 'heading' => 'The one-sentence version', 'aliases' => ['One-sentence version'], 'required' => true],
                ['key' => 'who_it_is_for', 'heading' => 'Who it is for', 'aliases' => ["Who it's for"], 'required' => true],
                ['key' => 'why_it_wins', 'heading' => 'Why it wins', 'required' => true],
                ['key' => 'honest_limits', 'heading' => 'What we deliberately do not claim', 'aliases' => ['What we do not claim'], 'required' => true],
            ],
            'checks' => [
                ['label' => 'It sounds like your product', 'help' => 'No borrowed hype and nothing you could not say out loud to a customer.',
                 'evidence_sections' => ['positioning_statement', 'one_sentence_version']],
                ['label' => 'No invented features or claims', 'help' => 'Every capability mentioned appears in your Pantry feature list.',
                 'evidence_sections' => ['why_it_wins']],
                ['label' => 'A stranger could repeat it', 'help' => 'After one read, someone could explain your product to a friend.',
                 'evidence_sections' => ['one_sentence_version']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'plate-the-landing-page',
            'title'            => 'Write landing page copy',
            'summary'          => 'Turn your positioning into a headline, feature blocks, and call to action.',
            'why_it_matters'   => 'Every announcement will send people to this page. This step turns your approved positioning into copy that makes the next click obvious.',
            'unlocks_text'     => 'Approving unlocks the launch posts step.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are a conversion copywriter who writes plainly. Using the approved positioning below and only the listed features, write landing page copy.

Product name: {{product_name}}
Website: {{website_url}}
Voice: {{tone}}
Features (complete list, do not add to it):
{{key_features}}

Approved positioning (treat as ground truth):
{{artifact:position-the-dish}}

Produce, in Markdown with the exact section headings given. Write each heading as a plain ATX Markdown heading on its own line, like "## Section name". Do not bold the headings, do not number them, and do not wrap your whole response in a code block or code fence:

## Hero
A headline of at most 8 words that passes the glance test, a subheadline of at most 35 words, a primary button label of at most 4 words, and one secondary link label.

## Feature blocks
Exactly three blocks. Each: a heading of at most 6 words and a 2 or 3 sentence body that leads with the customer's gain. Cover the most decision-driving features; do not force all features in.

## Closing call to action
One short line plus the button label again.

## Footer reassurance
One or two sentences that remove the biggest hesitation (privacy, effort, or lock-in), using only true facts from the positioning.

Keep the four section headings exactly as written above, in that order, with each part under its heading. No emoji, no exclamation marks, under 350 words total. Voice: {{tone}}.
TXT,
            'example_response' => $landingExample,
            'output_sections' => [
                ['key' => 'hero', 'heading' => 'Hero', 'required' => true],
                ['key' => 'feature_blocks', 'heading' => 'Feature blocks', 'aliases' => ['Features'], 'required' => true],
                ['key' => 'closing_cta', 'heading' => 'Closing call to action', 'aliases' => ['Call to action'], 'required' => true],
                ['key' => 'footer_reassurance', 'heading' => 'Footer reassurance', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Headline passes the glance test', 'help' => 'Someone skimming for three seconds still learns what this is.',
                 'evidence_sections' => ['hero']],
                ['label' => 'Every feature claim is real', 'help' => 'The three blocks only describe features from your Pantry list.',
                 'evidence_sections' => ['feature_blocks']],
                ['label' => 'One clear next step', 'help' => 'A single primary action, stated the same way each time it appears.',
                 'evidence_sections' => ['hero', 'closing_cta']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'serve-the-announcements',
            'title'            => 'Write launch posts',
            'summary'          => 'Draft a post shaped for each launch channel you chose.',
            'why_it_matters'   => 'One paragraph pasted into five channels reads as spam five times. This step writes a native post for every channel you picked.',
            'unlocks_text'     => 'Approving unlocks the final step: your FAQ.',
            'est_minutes'      => 7,
            'prompt_template'  => <<<'TXT'
You are launching a product and writing announcements that respect each channel's culture. Use only the facts in the positioning below; invent nothing.

Product name: {{product_name}}
Link to include: {{website_url}}
Price: {{price_usd}} USD per month (say "free" if 0)
Voice: {{tone}}
Channels to write for: {{channels}}

Approved positioning (ground truth):
{{artifact:position-the-dish}}

For each listed channel, produce one Markdown section whose heading is exactly "## [channel name] launch post" — for example "## Product Hunt launch post" or "## Email newsletter launch post". Write each heading as a plain ATX Markdown heading on its own line; do not bold or number the headings, and do not wrap your whole response in a code block or code fence. Keep these heading labels exactly as given and put each post under its own heading, shaped for that channel:
- Product Hunt: a tagline of at most 60 characters plus a founder's first comment of 120 to 180 words that tells the honest origin story.
- X (Twitter): a thread of 3 to 5 numbered posts, each under 260 characters, first post hooks with the problem.
- LinkedIn: one post of 120 to 180 words, professional but human, no hashtag spam (at most 3).
- Email newsletter: a subject line under 50 characters and a body of 120 to 180 words written to one reader.
- Indie Hackers: a post of 150 to 220 words leading with a real number or honest struggle, community-first, sell last.

Only write sections for the channels listed above. Include the link naturally in each. Voice: {{tone}}.
TXT,
            'example_response' => $announcementExample,
            'output_sections' => [
                // Channels are user-chosen in the Pantry, so every channel
                // section is optional; only the picked ones should appear.
                ['key' => 'product_hunt_post', 'heading' => 'Product Hunt launch post', 'required' => false],
                ['key' => 'x_twitter_post', 'heading' => 'X (Twitter) launch post', 'aliases' => ['X launch post', 'Twitter launch post', 'X (Twitter) thread'], 'required' => false],
                ['key' => 'linkedin_post', 'heading' => 'LinkedIn launch post', 'required' => false],
                ['key' => 'email_newsletter_post', 'heading' => 'Email newsletter launch post', 'aliases' => ['Email newsletter announcement', 'Email launch post'], 'required' => false],
                ['key' => 'indie_hackers_post', 'heading' => 'Indie Hackers launch post', 'required' => false],
            ],
            'checks' => [
                ['label' => 'Each post fits its channel', 'help' => 'The Product Hunt comment, thread, and email read differently, not one blurb copied around.',
                 'evidence_sections' => ['product_hunt_post', 'x_twitter_post', 'linkedin_post', 'email_newsletter_post', 'indie_hackers_post']],
                ['label' => 'Links and price are correct', 'help' => 'Every post points at your URL and any price mentioned matches your Pantry.',
                 'evidence_sections' => ['product_hunt_post', 'x_twitter_post', 'linkedin_post', 'email_newsletter_post', 'indie_hackers_post']],
                // Voice is judged across the whole response, so this check
                // deliberately keeps manual full-response review.
                ['label' => 'It reads in your voice', 'help' => 'You would post each of these under your own name without editing the tone.'],
            ],
        ],
        [
            'stage_position'   => 4,
            'slug'             => 'answer-the-table',
            'title'            => 'Prepare your FAQ',
            'summary'          => 'Answer the questions and objections people will raise on launch day.',
            'why_it_matters'   => 'Launch day is mostly answering comments. This step prepares the hard questions and your honest answers in advance.',
            'unlocks_text'     => 'Approving this completes the workflow and opens export.',
            'est_minutes'      => 6,
            'prompt_template'  => <<<'TXT'
You are preparing a founder for launch day questions. Using only the facts below, write a FAQ and objection-handling section. Honesty is the strategy: when the product has a real limit, state it and say who the product is not for.

Product name: {{product_name}}
Price: {{price_usd}} USD per month (say "free" if 0)
Website: {{website_url}}
Voice: {{tone}}
Features (complete list):
{{key_features}}

Approved positioning (ground truth):
{{artifact:position-the-dish}}

Produce, in Markdown with the exact section headings given. Write each heading as a plain ATX Markdown heading on its own line, like "## Section name". Do not bold the headings, do not number them, and do not wrap your whole response in a code block or code fence:

## Frequently asked questions
Five questions a skeptical first-time visitor would actually ask (about how it works, data or privacy if relevant, price, and switching costs), each answered in 2 to 4 plain sentences. State the price plainly in one of them.

## Objections, answered honestly
The three hardest objections, each as a bold quoted objection followed by an answer that concedes what is true before making the counterpoint. No objection may be a softball.

Keep both section headings exactly as written above, in that order, with each part under its heading. Under 450 words total. Voice: {{tone}}.
TXT,
            'example_response' => $faqExample,
            'output_sections' => [
                ['key' => 'faq', 'heading' => 'Frequently asked questions', 'aliases' => ['FAQ'], 'required' => true],
                ['key' => 'objections', 'heading' => 'Objections, answered honestly', 'aliases' => ['Objections'], 'required' => true],
            ],
            'checks' => [
                ['label' => 'Answers are honest', 'help' => 'Real limits are admitted; at least one answer says who this is not for.',
                 'evidence_sections' => ['faq', 'objections']],
                ['label' => 'Price is stated plainly', 'help' => 'The price appears in the FAQ exactly as set in your Pantry.',
                 'evidence_sections' => ['faq']],
                ['label' => 'No unbuilt promises', 'help' => 'Nothing is promised that is not in your feature list today.',
                 'evidence_sections' => ['faq', 'objections']],
            ],
        ],
    ],
];
