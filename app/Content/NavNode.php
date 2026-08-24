<?php

declare(strict_types=1);

namespace App\Content;

/**
 * A node of the navigation tree, built from the layout of content/.
 */
final readonly class NavNode
{
    public function __construct(
        public string $slug,
        public string $title,
        /** @var NavNode[] */
        public array $children = [],
    ) {}

    /** A folder without an index page: a label, nothing to link to. */
    public function isLabel(): bool
    {
        return $this->slug === '';
    }

    /** Whether the given page is this node or sits somewhere below it. */
    public function leadsTo(string $slug): bool
    {
        if ($this->slug === $slug) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->leadsTo($slug)) {
                return true;
            }
        }

        return false;
    }
}
