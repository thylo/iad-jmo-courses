<?php

declare(strict_types=1);

namespace App\Content;

final readonly class TocEntry
{
    public function __construct(
        public string $id,
        public string $label,
        /** @var TocEntry[] */
        public array $children = [],
    ) {}

    public function withChild(self $child): self
    {
        return new self($this->id, $this->label, [...$this->children, $child]);
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }
}
