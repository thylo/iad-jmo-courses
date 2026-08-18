<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\EntityLink;
use App\Content\Html;
use App\Content\Xml\RenderContext;
use App\Content\Xml\XmlSource;

/**
 * <retroliens/> — who points here.
 *
 * Free, because it is the reference map read backwards. Nothing to declare, and
 * nothing to keep in sync.
 */
final readonly class BacklinksElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $sources = $context->index->backlinksTo($context->source->id);

        if ($sources === []) {
            return '';
        }

        usort($sources, XmlSource::byTitle(...));

        $items = '';

        foreach ($sources as $source) {
            $items .= sprintf('<li>%s</li>', EntityLink::html($context->index, $source->id));
        }

        $title = Html::attribute($element, 'titre') ?: 'Mentionné dans';

        return sprintf(
            '<nav class="retroliens"><h2 id="%s">%s</h2><ul>%s</ul></nav>',
            Html::escape(Html::id($title)),
            Html::escape($title),
            $items,
        );
    }
}
