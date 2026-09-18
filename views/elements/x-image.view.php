<?php
/**
 * A responsive image. The browser picks the file; sizes only tells it how wide
 * the image will be drawn, which it cannot know before the layout.
 *
 * alt is written as a plain attribute so that an empty one survives: an image
 * nobody has described yet is decorative until someone says otherwise, and a
 * screen reader skipping it beats it reading out "unlock.jpg".
 *
 * srcset and sizes are dropped when there is nothing to choose between — a
 * moving image is copied rather than encoded, so it has one width and one file.
 *
 * @var string $src
 * @var string $srcset
 * @var string $sizes
 * @var string $width
 * @var string $height
 * @var string $alt
 * @var bool $lazy
 */
?>
<img src="{{ $src }}" :srcset="$srcset !== '' ? $srcset : null" :sizes="$srcset !== '' ? $sizes : null" width="{{ $width }}" height="{{ $height }}" alt="{{ $alt }}" decoding="async" :loading="$lazy ? 'lazy' : null">
