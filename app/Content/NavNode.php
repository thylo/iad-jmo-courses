<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Un nœud de l'arbre de navigation, construit depuis l'arborescence de content/.
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
