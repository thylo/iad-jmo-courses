<?php
/**
 * The summary of a page: its <h2> headings, with their <h3> beneath.
 *
 * Two levels, no more — that is exactly what App\Content\TableOfContents reads
 * out of the page, so the template does not need to recurse.
 *
 * @var \App\Content\TocEntry[] $entries
 */
?>
<nav class="c-toc c-rule-under" aria-label="Sommaire">
    <p class="c-toc__label">Sur cette page</p>

    <ul class="c-toc__list">
        <li :foreach="$entries as $entry">
            <a class="c-toc__link" :href="'#' . $entry->id">{{ $entry->label }}</a>

            <ul :if="$entry->hasChildren()" class="c-toc__list">
                <li :foreach="$entry->children as $child">
                    <a class="c-toc__link" :href="'#' . $child->id">{{ $child->label }}</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
