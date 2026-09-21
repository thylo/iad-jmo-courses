<?php
/** @var \App\View\SiteMapView $this */
?>
<x-base>
    {{-- Same shape as any page: an opening, then the reading. --}}
    <div class="c-intro">
        <h1 class="c-intro__title">Plan du site</h1>

        <p class="c-intro__lead">
            Toutes les pages du site. Les œuvres, concepts et personnes sont
            listés à part&nbsp;: <a href="/oeuvres">Œuvres</a>,
            <a href="/cours/concepts">Concepts</a>,
            <a href="/cours/personnes">Personnes et studios</a>.
        </p>
    </div>

    <article class="o-canvas__reading">
        <x-sitemap />
    </article>
</x-base>
