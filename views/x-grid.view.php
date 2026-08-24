<?php
/**
 * The entries a <grid> query found.
 *
 * The grid arrives with the images: entries carrying a thumbnail get the
 * columns, an index of concepts stays the list it has always been — so a
 * half-illustrated corpus never renders as a grid of holes.
 *
 * @var \App\Content\Xml\Elements\GridEntry[] $entries
 * @var bool $thumbnails
 */
?>
<ul :if="$entries !== []" :class="$thumbnails ? 'c-grid c-grid--thumbnails' : 'c-grid'">
    <li :foreach="$entries as $entry">
        {!! $entry->thumbnail !!}
        <a :href="$entry->href">{{ $entry->title }}</a>
        <span :if="$entry->meta !== null" class="meta">{{ $entry->meta }}</span>
        <span :if="$entry->summary !== null">— {{ $entry->summary }}</span>
    </li>
</ul>

<p :else class="c-grid-empty">Rien pour l’instant.</p>
