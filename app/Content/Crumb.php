<?php

declare(strict_types=1);

namespace App\Content;

/**
 * One step of the trail: a page the reader passed through to get here.
 *
 * A slug and a title, nothing else. It is not a NavNode — a node knows what
 * hangs under it, and a crumb is the opposite view of the same graph: what this
 * page hangs under. Keeping them apart means neither has to carry a field the
 * other needs.
 */
final readonly class Crumb
{
    public function __construct(
        public string $slug,
        public string $title,
    ) {}
}
