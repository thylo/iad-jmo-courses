<?php
/**
 * The entries a <grid> query found.
 *
 * The grid arrives with the images: entries carrying a thumbnail get the
 * columns, an index of concepts stays the list it has always been — so a
 * half-illustrated corpus never renders as a grid of holes.
 *
 * A long index gets two things a short one does not: a search field, and the
 * whole width of the page instead of the width of the reading. Both follow the
 * same signal, decided in GridElement — the number of entries.
 *
 * The form has no action, so it submits to the page it is on and replaces the
 * query string with its own field. No JavaScript: the reader types, presses
 * enter, and the server answers with the same page, narrower.
 *
 * @var \App\Content\Xml\Elements\GridEntry[] $entries
 * @var bool $thumbnails   at least one entry has an image
 * @var bool $searchable   the index is long enough to be searched
 * @var string $parameter  the name of the query parameter
 * @var string $term       what the reader is looking for, or ''
 * @var int $showing       entries on screen
 * @var int $total         entries in the index
 * @var string $plural     what a collection of these is called
 * @var string $href       the page itself, for the link back to everything
 */
?>
<nav class="u-clearfix" :class="$searchable ? 'c-grid c-grid--wide' : 'c-grid'" :aria-label="$plural">
    <form :if="$searchable" class="c-grid__search" method="get" role="search">
        <label class="u-visually-hidden" :for="$parameter">Chercher</label>

        <input
            class="c-grid__field"
            type="search"
            :id="$parameter"
            :name="$parameter"
            :value="$term"
            placeholder="Chercher"
            autocomplete="off"
            spellcheck="false"
        >

        <button class="c-grid__submit" type="submit">Chercher</button>
    </form>

    <p :if="$searchable" class="c-grid__count">
        <span :if="$term === ''">{{ $total }} {{ $plural }}</span>
        <span :else>{{ $showing }} sur {{ $total }} {{ $plural }}</span>

        <a :if="$term !== ''" class="c-grid__reset" :href="$href">Tout afficher</a>
    </p>

    <ul :if="$entries !== []" :class="$thumbnails ? 'c-grid__list c-grid__list--thumbnails' : 'c-grid__list'">
        <li :foreach="$entries as $entry">
            {!! $entry->thumbnail !!}
            <a :href="$entry->href">{{ $entry->title }}</a>
            <span :if="$entry->meta !== null" class="meta">{{ $entry->meta }}</span>
        </li>
    </ul>

    <p :elseif="$term !== ''" class="c-grid__empty">Rien ne correspond à « {{ $term }} ».</p>

    <p :else class="c-grid__empty">Rien pour l’instant.</p>
</nav>
