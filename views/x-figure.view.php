<?php
/**
 * An image with what is owed to whoever made it.
 *
 * A figure gets one <figcaption>: when the image carries both a legend and a
 * credit, the credit sits inside it as a quieter aside rather than as a second
 * block. These are other people's images on a public course site, so the credit
 * is part of the component rather than something a writer remembers to add.
 *
 * @var string $class      the figure's classes, as the caller composes them
 * @var string $image      the <img>, already rendered
 * @var ?string $caption
 * @var ?string $credit
 * @var ?string $creditUrl
 */
?>
<figure :class="$class">
    {!! $image !!}
    <figcaption :if="$caption !== null" class="c-figure__caption">{{ $caption }}<span :if="$credit !== null" class="c-figure__credit"><x-credit :name="$credit" :url="$creditUrl" /></span></figcaption>
    <figcaption :elseif="$credit !== null" class="c-figure__credit"><x-credit :name="$credit" :url="$creditUrl" /></figcaption>
</figure>
