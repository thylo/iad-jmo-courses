<?php

declare(strict_types=1);

namespace App\Content\Xml;

/** Turns one XML element into HTML. */
interface ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string;
}
