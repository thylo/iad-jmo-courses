<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\Autolinks;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\EntityLink;
use App\Content\Xml\RenderContext;
use App\Content\Xml\WikiLinks;
use App\View\Component;
use Tempest\Markdown\Markdown;

/**
 * <markdown>prose</markdown> — opaque to tags, so the prose reads as prose.
 *
 * The prose leaf: dedent, expand [[wikilinks]] and bare URLs, hand over to
 * tempest/markdown.
 *
 * What comes back is the only HTML on the site that cannot be given a class at
 * the source — bare <p>, <h2>, <ul>, <blockquote>. That is what .c-prose is
 * for, so the wrapper goes here and nowhere else: every other block the content
 * layer writes says what it is.
 *
 * <preamble> is prose too, and it borrows the pipeline through prose(): same
 * dedent, same wikilinks, same parser, a second class on the same box. The one
 * thing it does not borrow is where it lands on the page.
 */
final readonly class MarkdownElement implements ElementRenderer
{
    public function __construct(
        private Markdown $markdown,
        private Autolinks $autolinks,
        private EntityLink $links,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->prose($element, $context, 'c-prose');
    }

    /**
     * The prose of an element, wrapped in the box the classes name.
     *
     * Shared with <preamble>, which is the same prose in another place on the
     * sheet. An element holding nothing but whitespace renders nothing at all,
     * rather than an empty box the owl would still count.
     */
    public function prose(\Dom\Element $element, RenderContext $context, string $class): string
    {
        $dedented = $this->dedent($element->textContent);

        if ($dedented === '') {
            return '';
        }

        $prose = new WikiLinks($context->index, $this->links, $this->components)->expand($dedented);

        return $this->components->render(
            'x-prose',
            class: $class,
            html: $this->markdown->parse($this->autolinks->expand($prose))->html,
        );
    }

    /**
     * Strips the XML indentation.
     *
     * Without this, prose nested two levels deep reads as an indented code block.
     */
    private function dedent(string $source): string
    {
        $lines = preg_split('/\R/', $source) ?: [];

        while ($lines !== [] && trim($lines[0]) === '') {
            array_shift($lines);
        }

        while ($lines !== [] && trim(end($lines)) === '') {
            array_pop($lines);
        }

        $indent = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $width = strlen($line) - strlen(ltrim($line, " \t"));
            $indent = $indent === null ? $width : min($indent, $width);
        }

        if ($indent === null || $indent === 0) {
            return implode("\n", $lines);
        }

        return implode("\n", array_map(
            static fn (string $line): string => trim($line) === '' ? '' : substr($line, $indent),
            $lines,
        ));
    }
}
