<?php

use App\Http\DocumentView;

/** @var DocumentView $this */
?>
<x-base :title="$this->document->title" :description="$this->document->description">
    <header>
        <a href="/">Narration Interactive</a>
    </header>

    <nav aria-label="Navigation principale">
        {!! $this->navHtml() !!}
    </nav>

    <main>
        <nav :if="$this->hasToc()" aria-label="Sommaire">
            <h2>Sommaire</h2>
            {!! $this->tocHtml() !!}
        </nav>

        <article>
            {!! $this->document->html !!}
        </article>
    </main>
</x-base>
