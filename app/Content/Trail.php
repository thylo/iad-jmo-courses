<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Where a page sits, from the section down to its immediate parent.
 *
 * The page itself is not in it. The trail is set immediately above the <h1>,
 * and a last crumb repeating the title one line under it would say the same
 * thing twice — so the heading is the last step, and the reader reads it as
 * one sentence: Panorama › Structures narratives › *Structure Linéaire*.
 *
 * Empty is a normal answer, not a failure: the homepage and the sections
 * themselves have nothing above them, and the masthead already says so.
 */
final readonly class Trail
{
    /** @param Crumb[] $crumbs outermost first */
    public function __construct(
        public array $crumbs = [],
    ) {}

    /**
     * Whether the trail passes through that page.
     *
     * The masthead asks, so that the section it marks is the one the trail
     * names. Reading a concept fiche filed under /cours but reached through
     * /panorama, the two would otherwise contradict each other on the same
     * screen.
     */
    public function contains(string $slug): bool
    {
        foreach ($this->crumbs as $crumb) {
            if ($crumb->slug === $slug) {
                return true;
            }
        }

        return false;
    }
}
