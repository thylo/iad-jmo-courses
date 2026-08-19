<?php

declare(strict_types=1);

namespace App\Media\Screen;

/** One work as the review screen shows it. */
final readonly class ScreenFrame
{
    public function __construct(
        public int $position,
        public int $total,
        public string $title,
        /** @var array<string, string> label => value */
        public array $facts,
        public ?string $summary,
        /** @var string[] reasons to look twice */
        public array $doubts,
        /** Absolute path of the image, null when the work has none. */
        public ?string $imagePath,
        /** What the file is: name and dimensions. */
        public ?string $caption,
        /** Where the image came from. */
        public ?string $source,
    ) {}
}
