<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Html;
use App\Content\Xml\RenderContext;

/**
 * <section titre="…">…</section>
 *
 * The heading carries an id so the page summary (TableOfContents) picks it up
 * exactly as it does for a markdown ## heading.
 */
final readonly class SectionElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $title = Html::attribute($element, 'titre');
        $heading = $title !== ''
            ? sprintf('<h2 id="%s">%s</h2>', Html::escape(Html::id($title)), Html::escape($title))
            : '';

        return sprintf('<section>%s%s</section>', $heading, $context->renderer->children($element, $context));
    }
}
