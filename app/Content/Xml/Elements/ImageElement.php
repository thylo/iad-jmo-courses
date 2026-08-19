<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Media\ImageTag;

/**
 * <image src="unlock-detail.jpg" alt="…" legende="…" credit="…"/>
 *
 * The image that renders where it is written, as opposed to <visuel>, which is
 * a data field feeding the header and the indexes. Same attributes, plus a
 * caption — and the file is resolved in the same folder, by the entity's type.
 */
final readonly class ImageElement implements ElementRenderer
{
    public function __construct(
        private ImageTag $images,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->images->block($element, $context->source->type);
    }
}
