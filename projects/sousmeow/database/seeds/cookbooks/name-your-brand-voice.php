<?php

declare(strict_types=1);

/**
 * Name Your Brand Voice - executable Cookbook for design-brand.
 *
 * Inspired by public brand-voice teaching (NN/g-style plain guidance and
 * government plain-language brand ideas). Ideas only. All copy, examples, and
 * prompts are original SousMeow work. No source wording is copied.
 */

$audienceExample = <<<'MD'
## Audience snapshot
Harbor Thread serves people who want clothing repaired, altered, or refreshed instead of replaced.

## What they need to feel
They need to feel welcome even if they do not know sewing terms. They should feel that their clothes are worth caring for and that the process will be clear.

## Plain-language needs
- Clear prices or next steps.
- Simple explanations of what can and cannot be fixed.
- Reassurance that small repairs are not embarrassing.

## Not for
This voice is not aimed at luxury fashion collectors, fast-turnaround industrial uniforms, or people looking for new clothing sales.

## Unknowns to confirm
- Whether Harbor Thread offers pickup or delivery.
- Which services are most common.
- Whether appointments are required.
MD;

$promiseExample = <<<'MD'
## Offer in plain words
Harbor Thread mends and alters clothing so customers can keep wearing pieces they already own.

## Brand promise
We make clothing care feel practical, kind, and easy to understand.

## Proof points
- Mending and alterations are the named services.
- The shop focuses on keeping existing clothes useful.
- The voice can explain repairs without assuming customers know sewing terms.

## Boundaries
Do not promise same-day turnaround, designer-level restoration, pickup service, or exact prices unless those facts are supplied.

## Voice risk
If the voice gets too precious, customers may worry the shop is expensive or judgmental. If it gets too casual, they may doubt the care taken with their clothes.
MD;

$traitsExample = <<<'MD'
## Voice traits
1. Warm
2. Practical
3. Reassuring

## Trait meanings
Warm means the customer feels welcome. Practical means the copy explains what happens next. Reassuring means repairs and alterations feel normal, not fussy or shameful.

## Words to use
- Bring it in
- We will take a look
- Mend
- Alter
- Keep wearing
- Clear next step

## Words to avoid
- Flawless
- Couture
- Cheap
- Emergency
- Perfect body
- Fashion rescue

## Voice decision
Harbor Thread should sound like a capable neighbor with good hands: clear, kind, and never dramatic.
MD;

$doDontExample = <<<'MD'
## Do examples
- Say "Bring the hem in and we will talk through the options."
- Say "A small tear is normal. We can tell you what is repairable."
- Say "Keep wearing the pieces you already reach for."

## Do not examples
- Do not say "We rescue your wardrobe from disaster."
- Do not say "Perfect your figure with flawless alterations."
- Do not say "Cheap fixes while you wait" unless that service and price are real.

## Rewrite rules
1. Replace dramatic claims with a clear next step.
2. Replace body-judging language with fit and comfort language.
3. Replace sewing jargon with plain explanations.

## Tone guardrails
Be friendly without becoming cute. Be confident without promising results the shop has not confirmed.
MD;

$sampleLinesExample = <<<'MD'
## Homepage line
Mending and alterations that help you keep wearing what you already love.

## About line
Harbor Thread is a small repair and alterations shop for everyday clothes, careful fixes, and clear next steps.

## Service line
Bring in the piece, tell us what feels off, and we will explain what can be mended or altered.

## Social bio
Mending, alterations, and practical clothing care. Clear advice for the pieces you want to keep.

## Confirmation note
Thanks for reaching out to Harbor Thread. We will review your note and let you know the next step for your repair or alteration.
MD;

$visualCuesExample = <<<'MD'
## Visual feeling
The visuals should feel calm, useful, and handmade without looking old-fashioned.

## Type and layout cues
Use readable type, short blocks of copy, and plenty of spacing around service explanations. The layout should make next steps easy to find.

## Color and material cues
Soft neutrals, thread-inspired accents, and fabric textures could fit the facts supplied. Exact colors are not decided here.

## Image direction
Use honest photos of hands, hems, stitches, fabric, tools, or finished repairs if the shop has them. Do not invent product photos or logo files.

## Not decided
Logo, final palette, typeface names, packaging, signage, and photography assets are still undecided.
MD;

$onePagerExample = <<<'MD'
## Brand voice brief
Harbor Thread sounds warm, practical, and reassuring. The voice helps people understand mending and alterations without shame, jargon, or pressure.

## Audience and promise
Audience: people who want clothing repaired, altered, or refreshed instead of replaced. Promise: clothing care that feels practical, kind, and easy to understand.

## Voice traits
- Warm: welcoming and calm.
- Practical: explains what happens next.
- Reassuring: treats repairs and fit questions as normal.

## Do and do not
Do say: "Bring it in and we will talk through the options." Do not say: "We rescue your wardrobe from disaster." Avoid dramatic claims, body judgment, and service promises not backed by facts.

## Sample lines
- Homepage: Mending and alterations that help you keep wearing what you already love.
- Social bio: Mending, alterations, and practical clothing care.
- Confirmation: Thanks for reaching out. We will let you know the next step.

## Visual notes
Aim for calm, useful, handmade cues: readable type, clear spacing, honest repair photos, and soft material references. Logo, colors, typefaces, and final assets remain undecided.
MD;

return [
    'slug'                => 'name-your-brand-voice',
    'title'               => 'Name Your Brand Voice',
    'tagline'             => 'Define a brand voice you can reuse in every sentence.',
    'description'         => "A brand voice gives your words a job before you pick visuals. This Cookbook helps you name who you serve, what you stand for, how you sound, what to avoid, and how that voice shows up in sample lines. Enter only facts you already know about the offer and audience; every step is told not to invent logos, services, prices, or customer research.",
    'primary_category'    => 'design-brand',
    'collections'         => ['start-here', 'selected-by-sousmeow'],
    'audience'            => 'Solopreneurs and small teams who need a clear voice before designing visuals',
    'outcome'             => "audience notes, promise, voice traits, do/don't examples, sample lines, and a one-page brand voice brief",
    'price_cents'         => null,
    'is_executable'       => true,
    'accent'              => 'plum',
    'difficulty'          => 'Beginner',
    'est_minutes'         => 30,
    'demo_completed_runs' => 267,
    'demo_avg_rating'     => 4.8,
    'sort_order'          => 9,
    'stages' => [
        ['title' => 'Audience', 'summary' => 'Who you serve and what you stand for.'],
        ['title' => 'Voice', 'summary' => 'Traits, examples, sample lines.'],
        ['title' => 'Brief', 'summary' => 'Visual notes and one-pager.'],
    ],
    'fields' => [
        [
            'field_key'    => 'brand_name',
            'label'        => 'Brand name',
            'type'         => 'text',
            'help'         => 'Use the exact name you want in the brief.',
            'placeholder'  => 'e.g. Harbor Thread',
            'sample_value' => 'Harbor Thread',
        ],
        [
            'field_key'    => 'what_you_offer',
            'label'        => 'What do you offer?',
            'type'         => 'textarea',
            'help'         => 'Products or services in plain words. Known facts only.',
            'placeholder'  => 'e.g. Mending and alterations for everyday clothes',
            'sample_value' => 'Mending and alterations for everyday clothes, including hems, small repairs, and fit adjustments.',
        ],
        [
            'field_key'    => 'who_you_serve',
            'label'        => 'Who do you serve?',
            'type'         => 'textarea',
            'help'         => 'Name the people you most want to help. Avoid imaginary personas.',
            'placeholder'  => 'e.g. People who want to repair clothes instead of replacing them',
            'sample_value' => 'People who want to repair, alter, or refresh clothes they already own instead of replacing them.',
        ],
        [
            'field_key'    => 'three_words_you_want',
            'label'        => 'Three words you want the voice to feel like',
            'type'         => 'text',
            'help'         => 'Three plain adjectives or qualities.',
            'placeholder'  => 'e.g. Warm, practical, reassuring',
            'sample_value' => 'Warm, practical, reassuring',
        ],
        [
            'field_key'    => 'three_words_you_avoid',
            'label'        => 'Three words you want to avoid',
            'type'         => 'text',
            'help'         => 'Words or qualities that should not fit the voice.',
            'placeholder'  => 'e.g. Fancy, pushy, cheap',
            'sample_value' => 'Fancy, pushy, cheap',
        ],
        [
            'field_key'    => 'where_voice_shows_up',
            'label'        => 'Where will this voice show up?',
            'type'         => 'textarea',
            'help'         => 'Website, bio, emails, signs, packaging, ads. List only real places you expect to write.',
            'placeholder'  => "Website homepage\nInstagram bio\nAppointment messages",
            'sample_value' => "Website homepage\nService descriptions\nInstagram bio\nAppointment request messages\nSmall in-shop signs",
        ],
    ],
    'recipes' => [
        [
            'stage_position'   => 1,
            'slug'             => 'name-who-you-serve',
            'title'            => 'Name who you serve',
            'summary'          => 'Turn your audience notes into a plain snapshot, needs, exclusions, and unknowns.',
            'why_it_matters'   => 'Voice gets vague when it tries to charm everyone. This step names the audience in everyday words so later lines can be useful instead of generic. Common mistakes: inventing demographics, writing a fictional persona, or making the audience too broad. Need help if the AI guesses research? Retry and ask it to mark missing facts as unknown. You are ready when the audience sounds like real people you can serve.',
            'unlocks_text'     => 'Approving unlocks the brand promise.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are helping define a brand audience. Use ONLY the facts below. Do not invent demographics, customer quotes, research findings, prices, or locations.

Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Who you serve: {{who_you_serve}}
Where voice shows up:
{{where_voice_shows_up}}

Produce Markdown with these exact headings:

## Audience snapshot
One or two sentences naming the audience in plain words.

## What they need to feel
Two sentences about the feeling the brand should create, based only on the offer and audience.

## Plain-language needs
Three to five bullets for what the audience needs the words to explain.

## Not for
One sentence naming who this voice is not primarily trying to serve.

## Unknowns to confirm
Three bullets for missing facts to confirm later.

Keep all five headings in order. Under 280 words.
TXT,
            'example_response' => $audienceExample,
            'output_sections' => [
                ['key' => 'audience_snapshot', 'heading' => 'Audience snapshot', 'required' => true],
                ['key' => 'need_to_feel', 'heading' => 'What they need to feel', 'required' => true],
                ['key' => 'plain_language_needs', 'heading' => 'Plain-language needs', 'required' => true],
                ['key' => 'not_for', 'heading' => 'Not for', 'required' => true],
                ['key' => 'unknowns', 'heading' => 'Unknowns to confirm', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Audience uses known facts', 'help' => 'No invented demographics, quotes, or market research appear.',
                 'evidence_sections' => ['audience_snapshot', 'unknowns']],
                ['label' => 'Needs are practical', 'help' => 'The needs point to copy the brand can actually write.',
                 'evidence_sections' => ['plain_language_needs']],
                ['label' => 'Not-for line narrows focus', 'help' => 'The voice has a clear edge without insulting anyone.',
                 'evidence_sections' => ['not_for']],
            ],
        ],
        [
            'stage_position'   => 1,
            'slug'             => 'name-what-you-stand-for',
            'title'            => 'Name what you stand for',
            'summary'          => 'Write the plain offer, promise, proof points, boundaries, and voice risk.',
            'why_it_matters'   => 'A voice needs something to stand on besides adjectives. This step turns the offer into a promise the brand can keep. Common mistakes: promising outcomes not supplied, making the brand sound bigger than it is, or hiding what is still undecided. Need help if the promise overreaches? Retry with "only promise what the offer proves." You are ready when the promise feels useful and believable.',
            'unlocks_text'     => 'Approving unlocks voice traits.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are writing a small brand promise. Use ONLY the pantry facts and approved audience notes. Do not invent services, prices, guarantees, credentials, or customer research.

Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Words you want: {{three_words_you_want}}
Words you avoid: {{three_words_you_avoid}}

Approved audience notes:
{{artifact:name-who-you-serve}}

Produce Markdown with these exact headings:

## Offer in plain words
One sentence explaining the offer without marketing fluff.

## Brand promise
One sentence the brand can keep.

## Proof points
Three bullets from the known offer or audience notes.

## Boundaries
One sentence naming what not to promise without more facts.

## Voice risk
One or two sentences naming how the voice could go wrong.

Keep all five headings in order. Under 260 words.
TXT,
            'example_response' => $promiseExample,
            'output_sections' => [
                ['key' => 'plain_offer', 'heading' => 'Offer in plain words', 'required' => true],
                ['key' => 'brand_promise', 'heading' => 'Brand promise', 'required' => true],
                ['key' => 'proof_points', 'heading' => 'Proof points', 'required' => true],
                ['key' => 'boundaries', 'heading' => 'Boundaries', 'required' => true],
                ['key' => 'voice_risk', 'heading' => 'Voice risk', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Promise is believable', 'help' => 'The promise follows from the offer, not wishful claims.',
                 'evidence_sections' => ['brand_promise', 'proof_points']],
                ['label' => 'Boundaries prevent invention', 'help' => 'Services, prices, and guarantees are not added without facts.',
                 'evidence_sections' => ['boundaries']],
                ['label' => 'Risk is useful', 'help' => 'The risk would help someone avoid the wrong tone.',
                 'evidence_sections' => ['voice_risk']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'pick-voice-traits',
            'title'            => 'Pick voice traits',
            'summary'          => 'Choose traits, define what they mean, and build word lists to guide writing.',
            'why_it_matters'   => 'Adjectives only help when everyone knows what they mean in sentences. This step turns preferred words into usable writing decisions. Common mistakes: picking too many traits, choosing traits that fight each other, or ignoring words to avoid. Need help if traits sound generic? Retry and ask for trait meanings tied to the approved promise. You are ready when each trait changes what you would write.',
            'unlocks_text'     => 'Approving unlocks do-and-don\'t examples.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are choosing brand voice traits. Use ONLY the pantry facts and approved artifacts. Do not invent a brand archetype, slogan, visual identity, or customer quotes.

Brand name: {{brand_name}}
Words you want: {{three_words_you_want}}
Words you avoid: {{three_words_you_avoid}}

Approved audience notes:
{{artifact:name-who-you-serve}}

Approved promise:
{{artifact:name-what-you-stand-for}}

Produce Markdown with these exact headings:

## Voice traits
A numbered list of exactly three traits.

## Trait meanings
One sentence per trait explaining how it should affect copy.

## Words to use
Six to ten words or short phrases that fit.

## Words to avoid
Six to ten words or short phrases to avoid.

## Voice decision
One sentence summarizing how the brand should sound.
TXT,
            'example_response' => $traitsExample,
            'output_sections' => [
                ['key' => 'voice_traits', 'heading' => 'Voice traits', 'required' => true],
                ['key' => 'trait_meanings', 'heading' => 'Trait meanings', 'required' => true],
                ['key' => 'words_to_use', 'heading' => 'Words to use', 'required' => true],
                ['key' => 'words_to_avoid', 'heading' => 'Words to avoid', 'required' => true],
                ['key' => 'voice_decision', 'heading' => 'Voice decision', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Exactly three traits', 'help' => 'The list is focused enough for a small team to remember.',
                 'evidence_sections' => ['voice_traits']],
                ['label' => 'Meanings guide writing', 'help' => 'Each trait explains what changes in the copy.',
                 'evidence_sections' => ['trait_meanings']],
                ['label' => 'Avoid list is visible', 'help' => 'The voice includes words or tones to keep out.',
                 'evidence_sections' => ['words_to_avoid']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-do-and-dont',
            'title'            => 'Write do and don\'t examples',
            'summary'          => 'Make the voice concrete with examples, anti-examples, rewrite rules, and guardrails.',
            'why_it_matters'   => 'People learn voice faster from examples than from labels. This step shows what to say and what to avoid before the brand writes public copy. Common mistakes: making examples too clever, using shame or hype, or inventing services inside sample sentences. Need help if examples drift? Retry with "only use known offer facts." You are ready when a teammate can spot an off-brand line.',
            'unlocks_text'     => 'Approving unlocks sample lines.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are writing brand voice examples. Use ONLY the pantry facts and approved voice traits. Do not invent prices, turnaround times, locations, awards, or services.

Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Where voice shows up:
{{where_voice_shows_up}}

Approved voice traits:
{{artifact:pick-voice-traits}}

Approved promise:
{{artifact:name-what-you-stand-for}}

Produce Markdown with these exact headings:

## Do examples
Three short lines the brand could say.

## Do not examples
Three short lines the brand should not say.

## Rewrite rules
Three rules for turning off-brand copy into on-brand copy.

## Tone guardrails
Two sentences that keep the voice honest.

Keep all four headings in order. Under 260 words.
TXT,
            'example_response' => $doDontExample,
            'output_sections' => [
                ['key' => 'do_examples', 'heading' => 'Do examples', 'required' => true],
                ['key' => 'do_not_examples', 'heading' => 'Do not examples', 'required' => true],
                ['key' => 'rewrite_rules', 'heading' => 'Rewrite rules', 'required' => true],
                ['key' => 'tone_guardrails', 'heading' => 'Tone guardrails', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Do examples fit the offer', 'help' => 'Each line could appear for the real brand as described.',
                 'evidence_sections' => ['do_examples']],
                ['label' => 'Do-not examples catch risk', 'help' => 'The bad examples show tones or claims to avoid.',
                 'evidence_sections' => ['do_not_examples']],
                ['label' => 'Rules are reusable', 'help' => 'Rewrite rules can guide future copy beyond these examples.',
                 'evidence_sections' => ['rewrite_rules']],
            ],
        ],
        [
            'stage_position'   => 2,
            'slug'             => 'write-sample-lines',
            'title'            => 'Write sample lines',
            'summary'          => 'Draft reusable sample lines for the places where the voice will show up.',
            'why_it_matters'   => 'Voice becomes real when it survives actual sentences. This step creates starter copy for common touchpoints without pretending every channel is already designed. Common mistakes: adding channels you did not list, promising service details you did not provide, or writing lines too polished to use. Need help if lines sound generic? Retry and ask for simple lines tied to the do examples. You are ready when you can paste at least one line into real copy.',
            'unlocks_text'     => 'Approving unlocks visual cue notes.',
            'est_minutes'      => 5,
            'prompt_template'  => <<<'TXT'
You are drafting sample brand lines. Use ONLY the pantry facts and approved artifacts. Do not invent offers, prices, deadlines, addresses, logo text, or visual assets.

Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Where voice shows up:
{{where_voice_shows_up}}

Approved voice traits:
{{artifact:pick-voice-traits}}

Approved do and don't examples:
{{artifact:write-do-and-dont}}

Produce Markdown with these exact headings:

## Homepage line
One clear line for the top of a website or landing page.

## About line
One sentence explaining the brand.

## Service line
One sentence for a service description.

## Social bio
One short bio line.

## Confirmation note
One short message for an inquiry or appointment confirmation.
TXT,
            'example_response' => $sampleLinesExample,
            'output_sections' => [
                ['key' => 'homepage_line', 'heading' => 'Homepage line', 'required' => true],
                ['key' => 'about_line', 'heading' => 'About line', 'required' => true],
                ['key' => 'service_line', 'heading' => 'Service line', 'required' => true],
                ['key' => 'social_bio', 'heading' => 'Social bio', 'required' => true],
                ['key' => 'confirmation_note', 'heading' => 'Confirmation note', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Lines match real touchpoints', 'help' => 'The samples fit places where the voice will actually show up.',
                 'evidence_sections' => ['homepage_line', 'social_bio', 'confirmation_note']],
                ['label' => 'No new service claims', 'help' => 'The samples do not invent prices, locations, speed, or offerings.',
                 'evidence_sections' => ['service_line', 'confirmation_note']],
                ['label' => 'Voice traits are audible', 'help' => 'The sample lines sound like the approved traits.',
                 'evidence_sections' => ['homepage_line', 'about_line']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'note-visual-cues',
            'title'            => 'Note visual cues',
            'summary'          => 'Capture factual visual direction without inventing logos, files, or finished design assets.',
            'why_it_matters'   => 'Voice can guide visuals, but it should not fake design decisions that have not been made. This step translates the voice into gentle cues while keeping unknown assets unknown. Common mistakes: inventing a logo, naming exact fonts, or pretending photos exist. Need help if the output designs too much? Retry with "facts only; list undecided assets." You are ready when the notes help a designer without becoming a fake brand kit.',
            'unlocks_text'     => 'Approving unlocks the one-page brand voice brief.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are noting visual cues from an approved brand voice. Use ONLY known facts. Do not invent logo files, exact colors, font names, photography assets, packaging, or signage.

Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Where voice shows up:
{{where_voice_shows_up}}

Approved voice traits:
{{artifact:pick-voice-traits}}

Approved sample lines:
{{artifact:write-sample-lines}}

Produce Markdown with these exact headings:

## Visual feeling
One sentence describing the feeling visuals should support.

## Type and layout cues
Two sentences about readability, spacing, or hierarchy.

## Color and material cues
One or two cautious sentences. Do not choose final colors.

## Image direction
One or two sentences about image types only if implied by the offer.

## Not decided
List visual assets or decisions still unknown.
TXT,
            'example_response' => $visualCuesExample,
            'output_sections' => [
                ['key' => 'visual_feeling', 'heading' => 'Visual feeling', 'required' => true],
                ['key' => 'type_layout_cues', 'heading' => 'Type and layout cues', 'required' => true],
                ['key' => 'color_material_cues', 'heading' => 'Color and material cues', 'required' => true],
                ['key' => 'image_direction', 'heading' => 'Image direction', 'required' => true],
                ['key' => 'not_decided', 'heading' => 'Not decided', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Visuals stay factual', 'help' => 'The notes do not invent finished logos, files, fonts, or photos.',
                 'evidence_sections' => ['not_decided', 'image_direction']],
                ['label' => 'Cues support the voice', 'help' => 'The visual feeling follows from the approved traits.',
                 'evidence_sections' => ['visual_feeling', 'type_layout_cues']],
                ['label' => 'Design decisions remain open', 'help' => 'Unknown brand-kit choices are clearly listed as not decided.',
                 'evidence_sections' => ['not_decided']],
            ],
        ],
        [
            'stage_position'   => 3,
            'slug'             => 'pack-voice-one-pager',
            'title'            => 'Pack the voice one-pager',
            'summary'          => 'Assemble the audience, promise, traits, examples, sample lines, and visual notes into one brief.',
            'why_it_matters'   => 'A voice guide only works if people can find and use it. This step compresses the approved pieces into a one-page brief for writing, reviewing, and sharing. Common mistakes: adding new claims at the end, dropping the avoid list, or turning visual notes into fake finished assets. Need help if the one-pager grows? Retry and ask for one concise brief using approved artifacts only. You are ready when the brief can guide the next piece of copy.',
            'unlocks_text'     => 'Approving completes the Cookbook and opens your finished-file export.',
            'est_minutes'      => 4,
            'prompt_template'  => <<<'TXT'
You are assembling a one-page brand voice brief. Use ONLY pantry facts and approved artifacts. Do not add services, prices, guarantees, logos, colors, or research.
Brand name: {{brand_name}}
Offer: {{what_you_offer}}
Where voice shows up:
{{where_voice_shows_up}}
Approved audience notes:
{{artifact:name-who-you-serve}}
Approved promise:
{{artifact:name-what-you-stand-for}}
Approved voice traits:
{{artifact:pick-voice-traits}}
Approved do and don't examples:
{{artifact:write-do-and-dont}}
Approved sample lines:
{{artifact:write-sample-lines}}
Approved visual cues:
{{artifact:note-visual-cues}}

Produce Markdown with these exact headings:
## Brand voice brief
Two sentences summarizing the voice.
## Audience and promise
Two sentences naming audience and promise.
## Voice traits
Three bullets, one per trait.
## Do and do not
One short paragraph with a do and a do-not example.
## Sample lines
Three to five reusable lines.
## Visual notes
Two sentences about visual cues and undecided assets.
TXT,
            'example_response' => $onePagerExample,
            'output_sections' => [
                ['key' => 'brand_voice_brief', 'heading' => 'Brand voice brief', 'required' => true],
                ['key' => 'audience_promise', 'heading' => 'Audience and promise', 'required' => true],
                ['key' => 'voice_traits', 'heading' => 'Voice traits', 'required' => true],
                ['key' => 'do_and_do_not', 'heading' => 'Do and do not', 'required' => true],
                ['key' => 'sample_lines', 'heading' => 'Sample lines', 'required' => true],
                ['key' => 'visual_notes', 'heading' => 'Visual notes', 'required' => true],
            ],
            'checks' => [
                ['label' => 'Brief is one-page usable', 'help' => 'The summary is compact enough to share before writing copy.',
                 'evidence_sections' => ['brand_voice_brief', 'audience_promise']],
                ['label' => 'Examples are preserved', 'help' => 'The one-pager keeps both do and do-not guidance.',
                 'evidence_sections' => ['do_and_do_not']],
                ['label' => 'Visual notes do not fake assets', 'help' => 'The final notes keep logos, colors, and assets undecided unless known.',
                 'evidence_sections' => ['visual_notes']],
            ],
        ],
    ],
];
