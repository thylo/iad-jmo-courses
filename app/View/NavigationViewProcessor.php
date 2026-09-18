<?php

declare(strict_types=1);

namespace App\View;

use App\Content\ContentRepository;
use App\Content\TrailResolver;
use Tempest\View\View;
use Tempest\View\ViewProcessor;

/**
 * Hands every page what it needs to say where the reader is.
 *
 * Three things, and they are one answer: the sections of the site, the address
 * being read, and the trail down to it. Passing that down from each controller
 * action would be the same lines everywhere, and threading it through <x-base>
 * as props would make the frame carry data it does not use itself.
 *
 * They travel together because they are read together: the masthead marks the
 * section the trail names, so a page cannot end up with a trail through
 * /panorama and a mark on /cours.
 *
 * View data is visible inside view components, so <x-masthead> reads $sections,
 * $current and $trail directly, and <x-document> reads $trail.
 *
 * The filter is on the HasNavigation contract, not on a class: pages of other
 * kinds — the 404 — get their sections by implementing it, and the framework's
 * own views (error pages) are left alone.
 */
final readonly class NavigationViewProcessor implements ViewProcessor
{
    public function __construct(
        private ContentRepository $content,
        private TrailResolver $trails,
    ) {}

    public function process(View $view): View
    {
        if (! $view instanceof HasNavigation) {
            return $view;
        }

        return $view->data(
            sections: $this->content->tree(),
            current: $view->currentSlug,
            trail: $this->trails->to($view->currentSlug),
        );
    }
}
