<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/** <note type="astuce|attention" title="…">…</note> — the aside, with a real title this time. */
final readonly class NoteElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $title = Html::attribute($element, 'title');

        return $this->components->render(
            'x-note',
            type: Html::attribute($element, 'type') ?: 'note',
            title: $title !== '' ? $title : null,
            body: $context->renderer->children($element, $context),
        );
    }
}
