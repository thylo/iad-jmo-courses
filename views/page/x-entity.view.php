<?php
/**
 * The head of a page and everything under it, in document order.
 *
 * The opening is always the same shape — one <h1>, then a line saying what the
 * page is — whatever the entity. App\Content\Intro reads those two back out of
 * this HTML and hands them to the layout, so their order here is a contract:
 * the heading first, the summary right after it, the facts before the body.
 *
 * The heading can run on three lines. The sup- and subtitle are written around
 * the <h1> rather than inside it, because they are read as separate lines and a
 * screen reader should not run them into the title. They carry their class from
 * here: it is what Intro lifts them by, and what tells them apart from the
 * summary, which is the same tag one line down.
 *
 * The accent is a stretch of the title set in the second ink. It is given as
 * three parts rather than as marked-up HTML: deciding where the mark falls is
 * the renderer's job, drawing it is this one's.
 *
 * The two headings are one heading written twice, and that is on purpose: a
 * :if INSIDE the <h1> would put a line break where the mark starts, and an
 * accent that begins mid-word — "Interactiv|ité" — would be read with a space
 * in it.
 *
 * @var string $title       what comes before the accent, or the whole title
 * @var ?string $accent     the stretch set in the accent, when the page named one
 * @var string $after       what follows it
 * @var ?string $suptitle   the line above the title, when the page gave one
 * @var ?string $subtitle   the line under it
 * @var ?string $summary
 * @var string $image       the lead image, already rendered
 * @var string $facts       the typed fields, already rendered
 * @var string $body        the blocks of the page, already rendered
 */
?>
<p :if="$suptitle !== null" class="c-intro__suptitle">{{ $suptitle }}</p>
<h1 :if="$accent !== null">{{ $title }}<span class="u-ink-accent">{{ $accent }}</span>{{ $after }}</h1>
<h1 :else>{{ $title }}</h1>
<p :if="$subtitle !== null" class="c-intro__subtitle">{{ $subtitle }}</p>
<p :if="$summary !== null" class="summary">{{ $summary }}</p>
{!! $image !!}
{!! $facts !!}
{!! $body !!}
