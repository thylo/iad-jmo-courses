<?php
/**
 * A part of a page — what <section title="…"> renders.
 *
 * The heading carries the id, so the summary (TableOfContents) picks it up
 * exactly as it does a markdown ## heading.
 *
 * @var ?string $title
 * @var ?string $id
 * @var string $body
 */
?>
<section class="c-section">
    <h2 :if="$title !== null" class="c-section__title" :id="$id">{{ $title }}</h2>
    {!! $body !!}
</section>
