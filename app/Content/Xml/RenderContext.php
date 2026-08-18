<?php

declare(strict_types=1);

namespace App\Content\Xml;

/** What every element renderer needs: the page it is on, the graph, and a way back down the tree. */
final readonly class RenderContext
{
    public function __construct(
        public XmlSource $source,
        public ContentIndex $index,
        public NodeRenderer $renderer,
    ) {}
}
