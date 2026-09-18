<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Content\Xml\XmlSource;
use App\View\Component;

/**
 * <backlinks/> — who points here.
 *
 * Free, because it is the reference map read backwards. Nothing to declare, and
 * nothing to keep in sync.
 */
final readonly class BacklinksElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $sources = $context->index->backlinksTo($context->source->id);

        if ($sources === []) {
            return '';
        }

        usort($sources, XmlSource::byTitle(...));

        $title = Html::attribute($element, 'title') ?: 'Mentionné dans';

        return $this->components->render(
            'x-backlinks',
            title: $title,
            id: Html::id($title),
            entries: array_map(
                static fn (XmlSource $source): array => ['href' => $source->slug, 'label' => $source->title],
                $sources,
            ),
        );
    }
}
