<?php
/**
 * The body of a page: its opening, its fiche, its reading.
 *
 * Three slots are open in it, and they are how a page adds something of its
 * own without this file having to know which page it is: one before the lead,
 * one at the head of the reading, one after it. All three are empty on every
 * page but the homepage.
 *
 * @var \App\View\DocumentView $this
 */
?>
{{-- The opening: the page's own <h1> and first paragraph, lifted out of the
     body so they can be set as an introduction rather than as prose. It
     spans the sheet; the reading below steps in by one column, and that
     step is what announces the margin. --}}
<div :class="$this->intro->facts ? 'c-intro c-intro--with-facts' : 'c-intro'">
    <h1 :if="$this->intro->title" class="c-intro__title">{!! $this->intro->title !!}</h1>

    <x-slot name="opening" />

    <p :if="$this->intro->lead" class="c-intro__lead">{!! $this->intro->lead !!}</p>
</div>

{{-- The facts are data, not prose: it leaves the reading for the open field,
     level with the opening it describes. --}}
<div :if="$this->intro->facts" class="o-canvas__field">
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
