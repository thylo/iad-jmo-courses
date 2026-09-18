<?php
/**
 * One form — what <term> renders.
 *
 * Three parts, and the stylesheet sends each to the column it belongs in: the
 * name to the margin, the definition to the measure, the work to the field.
 *
 * The rule above the entry is drawn rather than bordered, like every other line
 * on the site, so it is composed from rule.css rather than redrawn here. Every
 * entry carries one, the first included: a rule that opens the block is what
 * makes the whole thing read as a spread instead of as a stack.
 *
 * The example is labelled for a screen reader and not for an eye. Sighted, a
 * column of titles and dates in the field is already legible as "the works";
 * heard, it is a title arriving after a definition with nothing to say why.
 * Seven visible "Exemple" would be seven repetitions of what the layout says.
 *
 * Unresolved is not fatal and not silent, on either link: the entry keeps its
 * place, says so, and content:check reports it.
 *
 * @var string $term
 * @var ?string $href
 * @var string $definition  already rendered: it goes through the prose pipeline
 * @var ?string $example
 * @var ?string $exampleHref
 * @var ?string $exampleYear
 */
?>
<div class="c-glossary__entry c-rule-over c-rule--2">
    <dt class="c-glossary__name">
        <a :if="$href !== null" :href="$href">{{ $term }}</a>
        <span :else class="c-missing-link">{{ $term }}</span>
    </dt>

    <dd class="c-glossary__definition">{!! $definition !!}</dd>

    <dd :if="$example !== null" class="c-glossary__example">
        <span class="u-visually-hidden">Exemple : </span>
        <a :if="$exampleHref !== null" class="c-glossary__work" :href="$exampleHref">{{ $example }}</a>
        <span :else class="c-glossary__work c-missing-link">{{ $example }}</span>
        <span :if="$exampleYear !== null" class="c-glossary__year">{{ $exampleYear }}</span>
    </dd>
</div>
