<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;

/** <item>…</item> — only meaningful inside <liste>. */
final readonly class ItemElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return sprintf('<li>%s</li>', $context->renderer->children($element, $context));
    }
}
