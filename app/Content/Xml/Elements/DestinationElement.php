<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <destination to="oeuvres">ce qu'on y trouve</destination>
 *
 * The destination names itself: `to` is an entity id, and the label and the
 * href both come from the target. Writing "Œuvres" here as well would be a
 * second copy of a title the graph already holds — one that goes stale the day
 * the section is renamed. Same argument as <grid of="…">: a page states what
 * it wants, not what it happens to contain today.
 *
 * The element's own text is the description, on one line: the indentation of
 * the XML is not part of it.
 *
 * There is no number in the markup, and that is deliberate. Œuvres, Panorama
 * and Cours are not ranked, not counted and not read in an order — numbering
 * them would state a hierarchy that does not exist. What ranks them is that
 * they are three, side by side.
 */
final readonly class DestinationElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $id = Html::attribute($element, 'to');
        $target = $context->index->find($id);

        return $this->components->render(
            'x-destination',
            // Unresolved is not fatal, and it is not silent either — same bargain
            // as EntityLink: the entry stays in the index without an href, says
            // so, and content:check reports it. You write the homepage before
            // the section it sends you to.
            href: $target?->slug,
            label: $target?->title ?? $id,
            description: Html::line($element),
        );
    }
}
