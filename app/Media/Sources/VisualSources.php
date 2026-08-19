<?php

declare(strict_types=1);

namespace App\Media\Sources;

/**
 * The sources, in the order they are tried.
 *
 * Same shape as the ElementRegistry: adding the Chrome capture or the Wayback
 * Machine is a class and one line here.
 */
final readonly class VisualSources
{
    /** @var VisualSource[] */
    private array $sources;

    public function __construct(
        OpenGraphSource $openGraph,
        YoutubeSource $youtube,
    ) {
        $this->sources = [$openGraph, $youtube];
    }

    /** @return VisualSource[] */
    public function all(): array
    {
        return $this->sources;
    }
}
