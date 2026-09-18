<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\InlineProse;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <definition>n. m. Note placée à la fin d'un livre…</definition>
 *
 * What the title means, for a page whose title is a word the reader may not
 * know. The <h1> is the headword and this is its gloss, set the way a
 * dictionary sets one: small, right under it, before the page says anything
 * of its own.
 *
 * Same bargain as <preamble>: the tag places the line rather than styling it.
 * App\Content\Intro lifts it into the opening, between the title and the
 * summary, wherever it was written in the file.
 *
 * One line of prose, like a <term>: the XML indentation is not part of it, and
 * it goes through InlineProse, so it can hold a link or an emphasis.
 */
final readonly class DefinitionElement implements ElementRenderer
{
    public function __construct(
        private InlineProse $prose,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->components->render(
            'x-definition',
            html: $this->prose->toHtml(Html::line($element), $context->index),
        );
    }
}
