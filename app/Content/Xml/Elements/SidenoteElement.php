<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <sidenote>…</sidenote> — a margin note, set beside the reading rather than in it.
 *
 * Distinct from <note> on purpose. A <note> interrupts: it carries a title, it
 * sits in the flow, the reader is meant to stop. A <sidenote> does not interrupt:
 * it is the aside you would pencil next to a paragraph, and the reader may
 * never look at it. Two registers, two tags — deciding between them is an
 * editorial act, not a length threshold.
 *
 * Unnumbered, so there is no reference mark to chase and no counter to keep.
 * It annotates what FOLLOWS it: the note floats into the margin at the height
 * of the block it precedes, so it is written above the paragraph it comments.
 */
final readonly class SidenoteElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $body = $context->renderer->children($element, $context);

        if (trim($body) === '') {
            return '';
        }

        return $this->components->render('x-sidenote', body: $body);
    }
}
