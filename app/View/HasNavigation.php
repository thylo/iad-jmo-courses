<?php

declare(strict_types=1);

namespace App\View;

/**
 * A page that renders inside the site frame, and therefore needs its sections.
 *
 * NavigationViewProcessor fills those in for every view implementing this.
 * Keying on the contract rather than on a class means a new kind of page — the
 * 404, tomorrow an index — gets its masthead by saying so; and a page that
 * renders <x-base> without saying so fails loudly, instead of quietly showing
 * an empty band.
 */
interface HasNavigation
{
    /**
     * The address being read, so the masthead can mark where the reader is.
     *
     * An address that matches nothing in the tree marks nothing, which is the
     * right answer for a page that isn't in the tree.
     */
    public string $currentSlug { get; }
}
