<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <quote credit="…" source="…" lang="en">…</quote> — someone else's words, attributed.
 *
 * The markdown blockquote stays and does not do this job. A `>` in the prose is
 * a typographic move — prose set off from prose, whoever wrote it. This is the
 * other thing: a passage lifted from a named source, where who said it and
 * where to read it are facts about the passage rather than a line the writer
 * remembers to type underneath. So the attribution belongs to the component,
 * exactly like the credit under a figure, and for the same reason: other
 * people's work on a public course site.
 *
 * lang= is there because a quotation is not translated by default. Set it and
 * the passage says what language it is in, which is what a screen reader needs
 * to pronounce it and what a browser needs to hyphenate it.
 */
final readonly class QuoteElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $body = $context->renderer->children($element, $context);

        if (trim($body) === '') {
            return '';
        }

        $credit = Html::attribute($element, 'credit');
        $source = Html::attribute($element, 'source');
        $lang = Html::attribute($element, 'lang');

        return $this->components->render(
            'x-quote',
            body: $body,
            credit: $credit !== '' ? $credit : null,
            source: $source !== '' ? $source : null,
            lang: $lang !== '' ? $lang : null,
        );
    }
}
