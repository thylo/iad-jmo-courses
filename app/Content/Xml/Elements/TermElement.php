<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\InlineProse;
use App\Content\Xml\RenderContext;
use App\View\Component;

/**
 * <term name="Linéaire" to="structure-lineaire">
 *   Un seul chemin. Le public avance, il ne dévie pas.
 * </term>
 *
 * One form: what it is called, where it is explained, one work that is made of
 * it. The href and the work's title are both resolved against the graph rather
 * than written here — same bargain as <destination to="…">, and the same
 * reason: a title copied into a second file goes stale the day it changes.
 *
 * The work is not even named. A concept fiche already carries <exemple ref="…"/>
 * — that is the field the fiche prints as "Illustré par" — so the index asks it
 * instead of keeping a second, quieter list of the same answers that nobody
 * would think to update. The first one wins, because a fiche lists its examples
 * best first. `example="…"` overrides it, for the page that wants to make a
 * different point with the same concept.
 *
 * `name` is the exception and it earns it. The fiches are called "Structure
 * Embranchements (Arborescent)", because that is what a fiche is called; a
 * column of seven titles all opening on the same word is not an index, it is a
 * stutter. The short form is what THIS page calls the form, the way link text
 * is not a copy of the target's <h1>. Leave the attribute out and the target's
 * own title is used.
 *
 * The element's own text is the definition, on one line: the indentation of the
 * XML is not part of it. It goes through InlineProse, so a definition holds the
 * same language as the paragraph above it — [[wikilinks]], links, emphasis. A
 * definition is prose; only its shape is short.
 *
 * Unresolved is not fatal and not silent, like everywhere else on the site: the
 * entry keeps its place, says so, and content:check reports the dead link.
 */
final readonly class TermElement implements ElementRenderer
{
    public function __construct(
        private InlineProse $prose,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $id = Html::attribute($element, 'to');
        $concept = $context->index->find($id);

        $exampleId = Html::attribute($element, 'example');

        // Unwritten, the concept answers for itself. A fiche that names no
        // example leaves the column empty rather than inventing one.
        if ($exampleId === '') {
            $exampleId = $concept?->value('exemple') ?? '';
        }

        $example = $context->index->find($exampleId);

        $name = Html::attribute($element, 'name');

        return $this->components->render(
            'x-term',
            // Not `name`: Component::render() takes the component's own name
            // as its first parameter, and a named argument cannot shadow it.
            term: $name !== '' ? $name : ($concept?->title ?? $id),
            href: $concept?->slug,
            definition: $this->prose->toHtml(Html::line($element), $context->index),
            example: $exampleId === '' ? null : ($example?->title ?? $exampleId),
            exampleHref: $example?->slug,
            // The year, and nothing else. A date under a title is a reference;
            // adding the makers would be a second index of a corpus the site
            // already indexes twice, in a column meant to be glanced at.
            exampleYear: $example?->value('annee'),
        );
    }
}
