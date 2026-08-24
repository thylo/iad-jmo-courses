<?php
/**
 * A responsive image. The browser picks the file; sizes only tells it how wide
 * the image will be drawn, which it cannot know before the layout.
 *
 * alt is written as a plain attribute so that an empty one survives: an image
 * nobody has described yet is decorative until someone says otherwise, and a
 * screen reader skipping it beats it reading out "unlock.jpg".
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
<img src="{{ $src }}" srcset="{{ $srcset }}" sizes="{{ $sizes }}" width="{{ $width }}" height="{{ $height }}" alt="{{ $alt }}" decoding="async" :loading="$lazy ? 'lazy' : null">
