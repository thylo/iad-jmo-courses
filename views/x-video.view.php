<?php
/**
 * An embed — what <video src="youtube:…" caption="…"/> renders.
 *
 * No third-party script: an iframe, deferred, on the no-cookie host when the
 * source has one. The title is what a screen reader announces, so it falls back
 * to something rather than to the URL.
 *
 * @var string $src
 * @var string $title
 * @var ?string $caption
 */
?>
<figure class="video">
    <iframe :src="$src" :title="$title" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
    <figcaption :if="$caption !== null">{{ $caption }}</figcaption>
</figure>
