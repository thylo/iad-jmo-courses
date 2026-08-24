<?php
/** @var \App\View\DocumentView $this */
?>
<x-base>
    {{-- The opening: the page's own <h1> and first paragraph, lifted out of the
         body so they can be set as an introduction rather than as prose. It
         spans the sheet; the reading below steps in by one column, and that
         step is what announces the margin. --}}
    <div :class="$this->intro->fiche ? 'c-intro c-intro--avec-fiche' : 'c-intro'">
        <h1 :if="$this->intro->title" class="c-intro__title">{!! $this->intro->title !!}</h1>

        <div :if="$this->isHome()" class="c-signature" aria-hidden="true"></div>

        <p :if="$this->intro->lead" class="c-intro__lead">{!! $this->intro->lead !!}</p>
    </div>

    {{-- The fiche is data, not prose: it leaves the reading for the open field,
         level with the opening it describes. --}}
    <div :if="$this->intro->fiche" class="o-canvas__champ">
        {!! $this->intro->fiche !!}
    </div>

    {{-- The reading. It takes the text column and the field: the prose keeps to
         the measure, and only figures and indexes use the extra width. --}}
    <article class="c-prose o-canvas__lecture">
        <div :if="$this->isHome()" class="c-portrait" aria-hidden="true"></div>

        <x-toc :if="$this->hasToc()" :entries="$this->toc->entries" />

        {!! $this->intro->body !!}
    </article>
</x-base>
