<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\Autolinks;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Content\Xml\WikiLinks;
use Tempest\Markdown\Markdown;

/**
 * <markdown>prose</markdown> — opaque to tags, so the prose reads as prose.
 *
 * The prose leaf: dedent, expand [[wikilinks]] and bare URLs, hand over to
 * tempest/markdown.
 */
final readonly class MarkdownElement implements ElementRenderer
{
    public function __construct(
        private Markdown $markdown,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        return $this->toHtml($element->textContent, $context->index);
    }

    private function toHtml(string $source, ContentIndex $index): string
    {
        $dedented = $this->dedent($source);

        if ($dedented === '') {
            return '';
        }

        $prose = new WikiLinks($index)->expand($dedented);

        return $this->markdown->parse(Autolinks::expand($prose))->html;
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
