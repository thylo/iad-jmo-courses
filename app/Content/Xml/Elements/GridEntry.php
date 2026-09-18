<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

/**
 * One entry of a <grid>, as the template needs it.
 *
 * The element answers the query, this says what an entry shows, and
 * views/elements/x-grid.view.php decides what that looks like. No HTML in any of it
 * except the thumbnail, which ImageTag renders because a responsive <img>
 * is its own subject.
 */
final readonly class GridEntry
{
    public function __construct(
        public string $title,
        public string $href,
        /** Year and creators, already joined — absent rather than empty. */
        public ?string $meta,
        public ?string $summary,
        /** The <img> tag, or '' when the entity has no visual. */
        public string $thumbnail,
    ) {}

    public function hasImage(): bool
    {
        return $this->thumbnail !== '';
    }
}
