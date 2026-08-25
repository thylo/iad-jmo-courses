<?php
/**
 * A quotation with its attribution — what <quote credit="…" source="…"> renders.
 *
 * <figure> around <blockquote>, because that is the one arrangement where the
 * attribution is allowed to be markup rather than a sentence: a <figcaption>
 * outside the quotation says who said it without pretending they said it.
 *
 * The drawn guillemet is a background on the block, not markup and not
 * generated content: it is a mark, it says nothing, and nothing should try to
 * read it aloud.
 *
 * cite= holds the same URL the credit links to. It is machine-readable and
 * shown nowhere, which is why the visible link exists as well.
 *
 * @var string $body      already rendered blocks
 * @var ?string $credit   who said it
 * @var ?string $source   where to read it
 * @var ?string $lang     the language of the passage, when it is not the page's
 */
?>
<figure class="c-quote">
    <blockquote class="c-quote__body" :cite="$source" :lang="$lang">{!! $body !!}</blockquote>
    <figcaption :if="$credit !== null" class="c-quote__credit"><x-credit :name="$credit" :url="$source" /></figcaption>
</figure>
