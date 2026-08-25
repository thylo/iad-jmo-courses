<?php
/**
 * A Spotify embed — what <spotify id="…"/> renders.
 *
 * The player is Spotify's, and nothing here can reach inside it. What this
 * component owns is the frame: the width the player is given, and the legend
 * under it. The title is what a screen reader announces, so it falls back to
 * something rather than to the URL.
 *
 * @var string $src
 * @var string $title
 * @var ?string $caption
 */
?>
<figure class="c-spotify">
    <iframe class="c-spotify__player" :src="$src" :title="$title" loading="lazy" allowfullscreen allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin"></iframe>
    <figcaption :if="$caption !== null" class="c-spotify__caption">{{ $caption }}</figcaption>
</figure>
