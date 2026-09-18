<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/** <item>…</item> — only meaningful inside <list>. */
final readonly class ItemElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->components->render(
            'x-list-item',
            body: $context->renderer->children($element, $context),
        );
    }
}
