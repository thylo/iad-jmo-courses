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
    /** @param NavNode[] $nodes */
    public function nav(array $nodes): string
    {
        if ($nodes === []) {
            return '';
        }

        $items = '';

        foreach ($nodes as $node) {
            $label = $this->escape($node->title);

            $items .= '<li>';
            $items .= $node->slug !== ''
                ? '<a href="' . $this->escape($node->slug) . '">' . $label . '</a>'
                : '<span>' . $label . '</span>';
            $items .= $this->nav($node->children);
            $items .= '</li>';
        }

        return '<ul>' . $items . '</ul>';
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
