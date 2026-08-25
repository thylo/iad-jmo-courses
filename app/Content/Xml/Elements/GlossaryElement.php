<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <glossary>…</glossary> — the named forms a page is about.
 *
 * Half the panorama says the same sentence N times: here is a form, here is
 * what it means, here is a work made of it. Written as a markdown list, that is
 * seven bullets of identical weight stacked in one column — you have to read
 * all of it to find any of it — while the two columns the sheet owns, the
 * margin and the open field, stay empty beside it.
 *
 * The tag is what earns those columns. An entry has three parts, the page has
 * three places to put them, and a <list> in the same page keeps being a list.
 *
 * It answers no query: what a page calls its forms is written, not derived.
 * <grid of="concept" where="genre = structure"/> lists the same seven and is a
 * different object — an index of everything filed under a genre, ordered by
 * title, with no definition and no example. One is the argument, the other is
 * the drawer.
 */
final readonly class GlossaryElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->components->render(
            'x-glossary',
            entries: $context->renderer->children($element, $context),
        );
    }
}
