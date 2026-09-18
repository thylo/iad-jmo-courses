<?php

declare(strict_types=1);

namespace App\Media\Sources;

/**
 * What one source found, or why it found nothing.
 *
 * A source that returns null tells us nothing; a source that says "HTTP 404"
 * tells us the work is gone while our fiche still claims it is online. That
 * note is half the value of the run.
 */
final readonly class Attempt
{
    private function __construct(
        public string $via,
        public ?Candidate $candidate,
        public string $note,
    ) {}

    public static function found(string $via, string $imageUrl, string $page): self
    {
        return new self($via, new Candidate($imageUrl, $page), '');
    }

    public static function nothing(string $via, string $note): self
    {
        return new self($via, null, $note);
    }
}
