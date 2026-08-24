<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Html;
use App\Media\ImageTag;

/**
 * Walks an entity tree and dispatches each element to its renderer.
 *
 * A plain class rather than a view component: Tempest expands components at
 * compile time, so a component that renders itself never terminates — and this
 * tree is arbitrarily deep, unlike the navigation and the summary, which are
 * flat enough to be written as <x-masthead> and <x-toc>.
 *
 * The page opens with the identity block — title, summary, data fields — and
 * then follows the document order for everything that renders in place. That
 * split is the model itself: data feeds the graph, elements produce HTML.
 */
final readonly class NodeRenderer
{
    public function __construct(
        private ElementRegistry $elements,
        private SchemaRegistry $schemas,
        private ImageTag $images,
    ) {}

    public function render(XmlSource $source, ContentIndex $index): string
    {
        $context = new RenderContext($source, $index, $this);

        // The image sits between the summary and the fiche: it is the only place
        // where it says something before anything has been read.
        return sprintf('<h1>%s</h1>', $this->title($source))
            . ($source->summary !== null ? sprintf('<p class="resume">%s</p>', Html::escape($source->summary)) : '')
            . $this->images->lead($source)
            . $this->dataList($source, $index)
            . $this->children($source->root, $context);
    }

    /**
     * The title, with the stretch the page named set in the accent.
     *
     * Escaping happens before the wrap, and the needle is escaped the same way,
     * so the search runs over comparable text and the only markup this can
     * produce is the span. XmlParser has already refused an accent that is not
     * in the title, so there is nothing to fall back to here.
     *
     * The last occurrence, not the first: the accented part is the end of the
     * phrase — "Ho, salut !" — and a word that appears twice should take the
     * mark where the eye lands, not where the string search does.
     */
    private function title(XmlSource $source): string
    {
        $title = Html::escape($source->title);

        if ($source->titleAccent === null) {
            return $title;
        }

        $accent = Html::escape($source->titleAccent);

        return substr_replace(
            $title,
            sprintf('<span class="u-ink-accent">%s</span>', $accent),
            (int) strrpos($title, $accent),
            strlen($accent),
        );
    }

    /** Renders every child element that has a renderer; data fields are skipped. */
    public function children(\Dom\Element $parent, RenderContext $context): string
    {
        $html = '';

        foreach ($parent->children as $node) {
            $html .= $this->elements->get($node->localName)?->render($node, $context) ?? '';
        }

        return $html;
    }

    /** The fiche: year, links, creators, concepts. */
    private function dataList(XmlSource $source, ContentIndex $index): string
    {
        $schema = $this->schemas->get($source->type);

        if ($schema === null) {
            return '';
        }

        $rows = '';

        foreach ($schema->fields as $field) {
            $values = $source->values($field->name);

            if (! $field->inFiche || $values === []) {
                continue;
            }

            $rows .= sprintf(
                '<dt>%s</dt><dd>%s</dd>',
                Html::escape($field->label),
                implode(', ', array_map(
                    fn (string $value): string => $this->value($field, $value, $index),
                    $values,
                )),
            );
        }

        return $rows === '' ? '' : sprintf('<dl class="fiche">%s</dl>', $rows);
    }

    private function value(Field $field, string $value, ContentIndex $index): string
    {
        if ($field->isReference) {
            return $field->allowsText && ! $index->has($value)
                ? Html::escape($value)
                : EntityLink::html($index, $value);
        }

        if ($field->isUrl) {
            return ExternalLink::html($value);
        }

        return Html::escape($value);
    }
}
