<?php

declare(strict_types=1);

namespace App\View;

use App\Content\Html;
use App\Content\NavNode;
use App\Content\TocEntry;

/**
 * Renders the two trees on a page: the navigation and the table of contents.
 *
 * A class rather than a view component: Tempest expands components at compile
 * time, so a component that renders itself never terminates. The compiler also
 * isolates <?php ?> blocks from the template, which rules out declaring a
 * recursive function inline.
 */
final readonly class TreeRenderer
{
    /**
     * The navigation, opened along the current page only.
     *
     * Every section stays visible, but a section unfolds its pages only when the
     * reader is inside it. A whole site rendered on every page is not navigation,
     * it is a sitemap.
     *
     * @param NavNode[] $nodes
     */
    public function nav(array $nodes, string $current): string
    {
        if ($nodes === []) {
            return '';
        }

        $items = '';

        foreach ($nodes as $node) {
            $label = $this->escape($node->title);

            $items .= '<li>';

            if ($node->slug === '') {
                // A folder without an index page: a label, nothing to link to.
                $items .= '<span>' . $label . '</span>';
            } else {
                $items .= '<a href="' . $this->escape($node->slug) . '"'
                    . ($node->slug === $current ? ' aria-current="page"' : '')
                    . '>' . $label . '</a>';
            }

            if ($this->leadsTo($node, $current)) {
                $items .= $this->nav($node->children, $current);
            }

            $items .= '</li>';
        }

        return '<ul>' . $items . '</ul>';
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
            $items .= '<a href="#' . $this->escape($entry->id) . '">' . $this->escape($entry->label) . '</a>';
            $items .= $this->toc($entry->children);
            $items .= '</li>';
        }

        return '<ul>' . $items . '</ul>';
    }

    private function escape(string $value): string
    {
        return Html::escape($value);
    }
}
