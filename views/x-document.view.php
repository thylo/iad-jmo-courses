<?php
/**
 * The body of a page: its opening, its fiche, its reading.
 *
 * Three slots are open in it, and they are how a page adds something of its
 * own without this file having to know which page it is: one before the lead,
 * one at the head of the reading, one after it. All three are empty on every
 * page but the homepage.
 *
 * The opening pairs when there is something to pair it with — an entity's
 * facts, or the plate a page asked for with <image width="opening"/>. It gives
 * up the field for that, and only then. A <preamble> does not pair it: that
 * prose sits inside the opening, under the lead, and takes no column away.
 * App\Content\Intro is what lifts all three out of the body.
 *
 * @var \App\View\DocumentView $this
 * @var \App\Content\Trail $trail
 */
?>
{{-- The opening: the page's own <h1> and first paragraph, lifted out of the
     body so they can be set as an introduction rather than as prose. It
     spans the sheet; the reading below steps in by one column, and that
     step is what announces the margin. --}}
<div :class="$this->intro->facts || $this->intro->plate ? 'c-intro c-intro--paired' : 'c-intro'">
    {{-- Above the title, and only where there is somewhere to name: the
         homepage and the sections have nothing above them, and an empty trail
         renders nothing rather than an empty line. --}}
    <x-trail :if="$trail->crumbs" />

    <h1 :if="$this->intro->title" class="c-intro__title">{!! $this->intro->title !!}</h1>

    <x-slot name="opening" />

    <p :if="$this->intro->lead" class="c-intro__lead">{!! $this->intro->lead !!}</p>

    {!! $this->intro->preamble !!}
</div>

{{-- What rides beside the opening rather than in the reading: the plate a page
     named as its own, and the facts of an entity. Neither is prose — one is an
     image and the other is data — and the field is level with the opening they
     belong to, which the first block of the reading is not. --}}
<div :if="$this->intro->facts || $this->intro->plate" class="o-canvas__field">
    {!! $this->intro->plate !!}
    {!! $this->intro->facts !!}
</div>

{{-- The reading. It takes the text column and the field: what it holds keeps
     to the measure, and only figures and indexes use the extra width.

     It is a stack of blocks, not a block of prose: each block the content layer
     writes says what it is, and .c-prose is one of them — the one markdown
     produces, because markdown is the only source that cannot name itself. --}}
<article class="o-canvas__reading">
    <x-slot name="reading" />

    <x-toc :if="$this->hasToc()" :entries="$this->toc->entries" />

    {!! $this->intro->body !!}

    <x-slot name="closing" />
</article>
