<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\InlineProse;
use App\Content\Xml\RenderContext;
use App\Media\Diagrams;
use App\View\Component;

/**
 * <diagram src="structure-lineaire.svg" alt="…" caption="…" width="field"/>
 *
 * A drawing that explains, as opposed to <image>, which shows. The distinction
 * is not decorative: a photograph is somebody else's, needs a credit and comes
 * in four widths; a diagram is drawn for the page it is on, has no author to
 * name, and is one file that fits every size.
 *
 * It is a tag rather than raw <svg> in the content because the vocabulary is
 * closed on purpose — an unknown tag is caught at load time instead of
 * disappearing from the page — and because a 168 kB export of path data pasted
 * between two paragraphs makes the file unreadable to the person writing it.
 * The drawing lives in media/diagrams/, the page names it.
 *
 * width= is the same editorial decision as on <image>, and takes the same three
 * answers: field, reading (default), full.
 */
final readonly class DiagramElement implements ElementRenderer
{
    public function __construct(
        private Diagrams $diagrams,
        private InlineProse $prose,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $svg = $this->diagrams->svg(Html::attribute($element, 'src'));

        if ($svg === '') {
            return '';
        }

        $caption = Html::attribute($element, 'caption');

        $drawing = $this->components->render(
            'x-diagram',
            svg: $svg,
            // A drawing nobody has described is decorative until someone says
            // otherwise: better skipped than read out as a list of curves.
            alt: Html::attribute($element, 'alt'),
        );

        return $this->components->render(
            'x-figure',
            class: $this->class($element),
            image: $drawing,
            caption: $caption !== '' ? $this->prose->toHtml($caption, $context->index) : null,
            credit: null,
            creditUrl: null,
        );
    }

    /** The same three widths as <image>, so the two behave alike in the grid. */
    private function class(\Dom\Element $element): string
    {
        return match (Html::attribute($element, 'width')) {
            'full' => 'c-figure c-figure--diagram c-bleed',
            'field' => 'c-figure c-figure--diagram c-figure--field',
            default => 'c-figure c-figure--diagram',
        };
    }
}
