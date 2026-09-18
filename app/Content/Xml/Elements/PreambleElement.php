<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;

/**
 * <preamble>prose</preamble> — the paragraph that belongs to the opening.
 *
 * A page sometimes has a second thing to say before the reading starts: what
 * the summary states in one line, said again with an example. Written as
 * <markdown> it becomes the first block of the reading, which is a different
 * job — the reading is where the page argues, and it starts one column in and
 * one full step down.
 *
 * The tag moves it rather than restyling it. App\Content\Intro lifts it into
 * the opening, where it sits under the summary and beside whatever the page put
 * in the field. That is the whole of the difference: same prose, same pipeline,
 * a place on the sheet instead of a place in the flow.
 *
 * One per page. A second one stays where it is written, which is what it
 * deserves — a preamble that arrives after the reading has begun is a paragraph.
 */
final readonly class PreambleElement implements ElementRenderer
{
    public function __construct(
        private MarkdownElement $markdown,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->markdown->prose($element, $context, 'c-prose c-intro__preamble');
    }
}
