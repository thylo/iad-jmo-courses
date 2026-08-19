<?php

declare(strict_types=1);

namespace App\View;

use App\Content\Html;
use App\Content\NavNode;
use App\Content\TocEntry;

/**
 * Renders the two lists a page carries: the sections in the masthead, and the
 * table of contents.
 *
 * A class rather than a view component: the table of contents nests, and
 * Tempest expands components at compile time, so a component that renders
 * itself never terminates. The compiler also isolates <?php ?> blocks from the
 * template, which rules out declaring a recursive function inline.
 */
final readonly class TreeRenderer
{
    /**
     * The sections of the site, as one flat line in the masthead.
     *
     * Only the top level. A section's own pages are listed by its index page —
     * /cours names its courses, /oeuvres indexes its œuvres — so unfolding the
     * tree here would repeat, on every page, what the section already says
     * better on one.
     *
     * @param NavNode[] $nodes
     */
    public function sections(array $nodes, string $current): string
    {
        if ($nodes === []) {
            return '';
        }

        $items = '';

        foreach ($nodes as $node) {
            $label = $this->escape($node->title);

            if ($node->slug === '') {
                // A folder without an index page: a label, nothing to link to.
                $items .= '<li><span class="c-nav__label">' . $label . '</span></li>';

                continue;
            }

            // The section you are reading inside is marked, but only the page
            // you are actually on claims to be the current page.
            $isCurrent = $node->slug === $current;
            $class = 'c-nav__link' . (! $isCurrent && $this->leadsTo($node, $current) ? ' c-nav__link--within' : '');

            $items .= '<li><a class="' . $class . '" href="' . $this->escape($node->slug) . '"'
                . ($isCurrent ? ' aria-current="page"' : '')
                . '>' . $label . '</a></li>';
        }

        return '<ul class="c-nav__list">' . $items . '</ul>';
    }

    /** Whether the current page is this node or sits somewhere below it. */
    private function leadsTo(NavNode $node, string $current): bool
    {
        if ($node->slug === $current) {
            return true;
        }

        foreach ($node->children as $child) {
            if ($this->leadsTo($child, $current)) {
                return true;
            }
        }

        return false;
    }

    /** @param TocEntry[] $entries */
    public function toc(array $entries): string
    {
        if ($entries === []) {
            return '';
        }

        $items = '';

        foreach ($entries as $entry) {
            $items .= '<li>';
            $items .= '<a class="c-toc__link" href="#' . $this->escape($entry->id) . '">' . $this->escape($entry->label) . '</a>';
            $items .= $this->toc($entry->children);
            $items .= '</li>';
        }

        return '<ul class="c-toc__list">' . $items . '</ul>';
    }

    private function escape(string $value): string
    {
        return Html::escape($value);
    }
}
