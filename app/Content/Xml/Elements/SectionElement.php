<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <section title="…">…</section>
 *
 * The heading carries an id so the page summary (TableOfContents) picks it up
 * exactly as it does for a markdown ## heading.
 */
final readonly class SectionElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $title = Html::attribute($element, 'title');

        return $this->components->render(
            'x-section',
            title: $title !== '' ? $title : null,
            id: $title !== '' ? Html::id($title) : null,
            body: $context->renderer->children($element, $context),
        );
    }
}
