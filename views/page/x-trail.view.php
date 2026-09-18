<?php
/**
 * Where this page sits, said just above its title.
 *
 * Ancestors only — the <h1> under it is the last step, so the two read as one
 * sentence. See App\Content\Trail.
 *
 * An <ol>, because the order is the meaning: these are not three links to three
 * places, they are one address read from the outside in.
 *
 * $trail comes from the view data, supplied by App\View\NavigationViewProcessor.
 *
 * @var \App\Content\Trail $trail
 */
?>
<nav class="c-trail" aria-label="Chemin">
    <ol class="c-trail__list">
        <li :foreach="$trail->crumbs as $crumb">
            <a class="c-trail__link" :href="$crumb->slug">{{ $crumb->title }}</a>
        </li>
    </ol>
</nav>
