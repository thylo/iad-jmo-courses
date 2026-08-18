<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;

/** <liste><item>…</item></liste> — for lists whose items hold more than prose. */
final readonly class ListElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return sprintf('<ul>%s</ul>', $context->renderer->children($element, $context));
    }
}
