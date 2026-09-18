<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Media\ImageTag;
use App\View\Component;

/**
 * Walks an entity tree and dispatches each element to its renderer.
 *
 * A plain class rather than a view component: Tempest expands components at
 * compile time, so a component that renders itself never terminates — and this
 * tree is arbitrarily deep, unlike the navigation and the summary, which are
 * flat enough to be written as <x-masthead> and <x-toc>.
 *
 * It composes what the templates give back, and writes no HTML of its own: the
 * shape of a page is in views/page/x-entity.view.php, the facts in x-facts.
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
        private EntityLink $entityLinks,
        private ExternalLink $externalLinks,
        private Component $components,
    ) {}

    public function render(XmlSource $source, ContentIndex $index): string
    {
        $context = new RenderContext($source, $index, $this);

        [$title, $accent, $after] = $this->title($source);

        // The image sits between the summary and the facts: it is the only place
        // where it says something before anything has been read.
        return $this->components->render(
            'x-entity',
            title: $title,
            accent: $accent,
            after: $after,
            summary: $source->summary,
            image: $this->images->lead($source, $index),
            facts: $this->dataList($source, $index),
            body: $this->children($source->root, $context),
        );
    }

    /**
     * The title, split where the page said to set the accent.
     *
     * XmlParser has already refused an accent that is not in the title, so
     * there is nothing to fall back to here.
     *
     * The last occurrence, not the first: the accented part is the end of the
     * phrase — "Ho, salut !" — and a word that appears twice should take the
     * mark where the eye lands, not where the string search does.
     *
     * @return array{0: string, 1: ?string, 2: string} before, accent, after
     */
    private function title(XmlSource $source): array
    {
        $title = $source->title;
        $accent = $source->titleAccent;

        if ($accent === null) {
            return [$title, null, ''];
        }

        $at = (int) strrpos($title, $accent);

        return [substr($title, 0, $at), $accent, substr($title, $at + strlen($accent))];
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

    /** The facts: year, links, creators, concepts. */
    private function dataList(XmlSource $source, ContentIndex $index): string
    {
        $schema = $this->schemas->get($source->type);

        if ($schema === null) {
            return '';
        }

        $rows = [];

        foreach ($schema->fields as $field) {
            $values = $source->values($field->name);

            if (! $field->inFiche || $values === []) {
                continue;
            }

            $rows[] = [
                'label' => $field->label,
                'value' => implode(', ', array_map(
                    fn (string $value): string => $this->value($field, $value, $index),
                    $values,
                )),
            ];
        }

        return $rows === [] ? '' : $this->components->render('x-facts', rows: $rows);
    }

    /** One value of the facts: an entity, an outside address, or plain text. */
    private function value(Field $field, string $value, ContentIndex $index): string
    {
        if ($field->isReference) {
            return $field->allowsText && ! $index->has($value)
                ? $this->components->render('x-text', text: $value)
                : $this->entityLinks->html($index, $value);
        }

        if ($field->isUrl) {
            return $this->externalLinks->html($value);
        }

        return $this->components->render('x-text', text: $value);
    }
}
