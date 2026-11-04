<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\Dedent;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\InlineProse;
use App\Content\Xml\RenderContext;
use App\View\Component;
use Tempest\Markdown\Markdown;

/**
 * <sketch caption="…" scripts="outils.js">p5 code</sketch>
 *
 * A p5 sketch shown as code, and run on demand in the page. The code is
 * written once: the block a reader sees is the text the runner executes —
 * <p5-sketch> reads it back out of the <pre> — so the two cannot drift apart
 * the way a capture or a sketch kept in the online editor would.
 *
 * Without JavaScript it is a code block like any other, highlighted by the
 * same parser as the fenced blocks of <markdown>, and nothing else.
 *
 * scripts= names files in public/documents/ to load before the sketch, in
 * order, the way index.html does in the editor. The run resolves every
 * relative path against that folder too, so loadImage("billy.png") finds the
 * image without the code having to say where it is.
 *
 * The canvas size is read from createCanvas(w, h) when the sketch states it
 * in numbers. It reserves the frame before the sketch runs, and it caps the
 * frame so a canvas is never drawn larger than the sketch made it.
 */
final readonly class SketchElement implements ElementRenderer
{
    private const string DOCUMENTS = '/documents/';

    public function __construct(
        private Markdown $markdown,
        private InlineProse $prose,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $code = Dedent::text($element->textContent);

        if ($code === '') {
            return '';
        }

        $caption = Html::attribute($element, 'caption');

        return $this->components->render(
            'x-sketch',
            code: $this->components->render(
                'x-prose',
                class: 'c-prose',
                html: $this->markdown->parse("```js\n{$code}\n```")->html,
            ),
            scripts: $this->scripts(Html::attribute($element, 'scripts')),
            size: $this->size($code),
            caption: $caption !== '' ? $this->prose->toHtml($caption, $context->index) : null,
        );
    }

    /** "outils.js autre.js" → the URLs, space-separated, in the order given. */
    private function scripts(string $names): string
    {
        $names = preg_split('/\s+/', trim($names), flags: PREG_SPLIT_NO_EMPTY) ?: [];

        return implode(' ', array_map(
            static fn (string $name): string => self::DOCUMENTS . rawurlencode($name),
            $names,
        ));
    }

    /**
     * The canvas, as custom properties for sketch.css, or '' when the sketch
     * sizes it from the window or leaves it to p5.
     */
    private function size(string $code): string
    {
        if (preg_match('/createCanvas\(\s*(\d+)\s*,\s*(\d+)/', $code, $match) !== 1) {
            return '';
        }

        return sprintf('--sketch-width: %dpx; --sketch-ratio: %d / %d', $match[1], $match[1], $match[2]);
    }
}
