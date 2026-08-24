<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <destinations>…</destinations> — the index of where the homepage sends you.
 *
 * It was a markdown list, and a markdown list could not say what this is. Three
 * links in a <ul> are three links in a <ul>: the layout had to guess, from the
 * page they happened to be on, that this particular list was the one thing on
 * the site with somewhere to go. Guessing worked exactly as long as no second
 * list appeared in the same reading.
 *
 * So the content says it instead. The tag is what earns the whole width of the
 * canvas and the display type; a <list> in the same page keeps being a list.
 */
final readonly class DestinationsElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->components->render(
            'x-destinations',
            entries: $context->renderer->children($element, $context),
        );
    }
}
