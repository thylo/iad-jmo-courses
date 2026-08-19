<?php

use App\Http\DocumentView;

/** @var DocumentView $this */
?>
<x-base
    :title="$this->document->title"
    :description="$this->document->description"
    :sections="$this->sectionsHtml()"
>
    {{-- The opening: the page's own <h1> and first paragraph, lifted out of the
         body so they can be set as an introduction rather than as prose. --}}
    <div class="o-grid">
        <div class="o-grid__aside">
            <div :if="$this->isHome()" class="c-portrait" aria-hidden="true"></div>
        </div>

        <div class="o-grid__main">
            <h1 :if="$this->intro->title" class="c-intro__title">{!! $this->intro->title !!}</h1>

            <svg :if="$this->isHome()" class="c-rule" viewBox="0 0 176 14" aria-hidden="true">
                <path d="M2 9.2c14-5.6 27.5 3.4 41.5-.6 14-4 21 5.2 35.5 1.2s24-6.4 38-2.4 24.5-1.6 31-4.4" />
            </svg>

            <p :if="$this->intro->lead" class="c-intro__lead">{!! $this->intro->lead !!}</p>
        </div>
    </div>

    {{-- The reading. The aside column is left empty on purpose: it is the margin
         the notes float into, and it belongs to the article beside it. --}}
    <div class="o-grid">
        <div class="o-grid__aside"></div>

        <article class="o-grid__main c-prose">
            <nav :if="$this->hasToc()" class="c-toc" aria-label="Sommaire">
                <p class="c-toc__label">Sur cette page</p>
                {!! $this->tocHtml() !!}
            </nav>

            {!! $this->intro->body !!}
        </article>
    </div>
</x-base>
