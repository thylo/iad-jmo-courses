<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\View\Component;
use Tempest\Markdown\Markdown;

/**
 * One line of prose, for the places that are not a <markdown> block.
 *
 * A caption is written in an attribute, so it cannot hold elements — but there
 * is no reason for it to hold less *language* than the paragraph next to it. It
 * gets the same expansion: [[wikilinks]], bare URLs, emphasis, links.
 *
 * Minus the paragraph. The caption is rendered inside a <figcaption>, which is
 * already a block, and a <p> in there would be a box nobody asked for.
 */
final readonly class InlineProse
{
    public function __construct(
        private Markdown $markdown,
        private Autolinks $autolinks,
        private EntityLink $links,
        private Component $components,
    ) {}

    public function toHtml(string $source, ContentIndex $index): string
    {
        if (trim($source) === '') {
            return '';
        }

        $expanded = new WikiLinks($index, $this->links, $this->components)->expand($source);

        return $this->unwrap($this->markdown->parse($this->autolinks->expand($expanded))->html);
    }

    /**
     * A single paragraph loses its tag; anything longer is left as it came.
     *
     * Someone will eventually write two sentences and a list in a caption. That
     * is a bad caption, not a broken page, so it renders as what it is.
     */
    private function unwrap(string $html): string
    {
        $trimmed = trim($html);

        if (preg_match('~^<p>(.*)</p>$~su', $trimmed, $match) !== 1) {
            return $trimmed;
        }

        return str_contains($match[1], '<p>') ? $trimmed : $match[1];
    }
}
