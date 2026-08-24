<?php

declare(strict_types=1);

namespace App\View;

use App\Content\ContentRepository;
use Tempest\View\View;
use Tempest\View\ViewProcessor;

/**
 * Hands the site sections to every page, so no view has to ask for them.
 *
 * The masthead is the same on all pages: the list of sections, and a mark on
 * the one being read. Passing that down from each controller action would be
 * the same two lines everywhere, and threading it through <x-base> as a prop
 * would make the frame carry data it does not use itself.
 *
 * View data is visible inside view components, so <x-masthead> reads $sections
 * and $current directly.
 *
 * The filter is on the HasNavigation contract, not on a class: pages of other
 * kinds — the 404 — get their sections by implementing it, and the framework's
 * own views (error pages) are left alone.
 */
final readonly class NavigationViewProcessor implements ViewProcessor
{
    public function __construct(
        private ContentRepository $content,
    ) {}

    public function process(View $view): View
    {
        if (! $view instanceof HasNavigation) {
            return $view;
        }

        return $view->data(
            sections: $this->content->tree(),
            current: $view->currentSlug,
        );
    }
}
