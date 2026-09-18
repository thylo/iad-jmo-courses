<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Xml\XmlSource;

/** One work waiting for a human: what it is, and what it is missing. */
final readonly class ReviewItem
{
    public function __construct(
        public XmlSource $entity,
        public ?Visual $visual,
    ) {}

    public function needsImage(): bool
    {
        return $this->visual === null;
    }

    public function needsAlt(): bool
    {
        return $this->visual !== null && $this->visual->alt === null;
    }
}
