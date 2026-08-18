<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Html;
use App\Content\Xml\RenderContext;

/** <note type="astuce|attention">…</note> — the aside, with a real title this time. */
final readonly class NoteElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $type = Html::attribute($element, 'type') ?: 'note';
        $title = Html::attribute($element, 'titre');

        return sprintf(
            '<aside class="note note--%s">%s%s</aside>',
            Html::escape($type),
            $title !== '' ? sprintf('<p class="note__titre">%s</p>', Html::escape($title)) : '',
            $context->renderer->children($element, $context),
        );
    }
}
