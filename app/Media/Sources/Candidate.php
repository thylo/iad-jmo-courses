<?php

declare(strict_types=1);

namespace App\Media\Sources;

/** An image worth downloading, and the page it should be credited to. */
final readonly class Candidate
{
    public function __construct(
        public string $imageUrl,
        /** What goes in source=: the page a reader can check, not the file. */
        public string $page,
    ) {}
}
