<?php
/**
 * An embed — what <video src="youtube:…" caption="…"/> renders.
 *
 * No third-party script: an iframe, deferred, on the no-cookie host when the
 * source has one. The title is what a screen reader announces, so it falls back
 * to something rather than to the URL.
 *
 * The parts are named because an iframe has no intrinsic size and the caption
 * is not the player — video.css has to reach each of them separately.
 *
 * @var string $src
 * @var string $title
 * @var ?string $caption
 */
?>
<figure class="c-video">
    <iframe class="c-video__player" :src="$src" :title="$title" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
    <figcaption :if="$caption !== null" class="c-video__caption">{{ $caption }}</figcaption>
</figure>
