<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <destinations>…</destinations> — the index of where the homepage sends you.
 *
 * It was a markdown list, and a markdown list could not say what this is. Three
 * links in a <ul> are three links in a <ul>: the layout had to guess, from the
 * page they happened to be on, that this particular list was the one thing on
 * the site with somewhere to go. Guessing worked exactly as long as no second
 * list appeared in the same reading.
 *
 * So the content says it instead. The tag is what earns the whole width of the
 * canvas and the display type; a <list> in the same page keeps being a list.
 */
final readonly class DestinationsElement implements ElementRenderer
{
    public function __construct(
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $modifiers = [];

        // Columns are a rhythm, and a rhythm of one is not one: a lone door in
        // the grid reads as a table that lost its other rows. The view drops the
        // columns for it, the same way <grid> drops them for a corpus with no
        // thumbnails. The author still writes <destinations> either way — how
        // many there are is a fact about the content, not a decision to take
        // again in the markup.
        if ($element->getElementsByTagName('destination')->length === 1) {
            $modifiers[] = 'c-destinations--single';
        }

        // rank="lead" is the one thing the writer decides here, and like an
        // image's width= it is editorial rather than a fact about the content:
        // it says this index is the way in rather than one group among the
        // others. Nothing in the markup could be read to mean that — a page's
        // front door and its last aside are both <destinations> with one entry
        // inside a <section> — so it has to be said.
        if (Html::attribute($element, 'rank') === 'lead') {
            $modifiers[] = 'c-destinations--lead';
        }

        return $this->components->render(
            'x-destinations',
            entries: $context->renderer->children($element, $context),
            // Composed here rather than in the view, the way <figure> takes the
            // classes its caller assembled: two independent modifiers make a
            // ternary in the template that reads as neither of them.
            class: implode(' ', ['c-destinations', ...$modifiers]),
        );
    }
}
