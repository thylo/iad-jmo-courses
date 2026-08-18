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

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }
}
