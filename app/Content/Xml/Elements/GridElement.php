<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Content\Xml\XmlSource;
use App\Media\ImageTag;
use App\View\Component;

/**
 * <grid of="oeuvre" where="par = self" sort="-annee"/>
 *
 * The derived block: a page states what it wants to show, not what it shows.
 * A studio page never names its works — the works name the studio, and this
 * reads the relation the other way round.
 *
 * The predicate is deliberately minimal: `field = value`, where value may be
 * the keyword `self`. No query language until a real need shows up.
 *
 * This answers the query and stops there: what an entry looks like is in
 * views/x-grid.view.php. The tree walk cannot be a view component — Tempest
 * expands those at compile time, and NodeRenderer says why — but a flat list
 * of entries can, so it is one.
 */
final readonly class GridElement implements ElementRenderer
{
    public function __construct(
        private ImageTag $images,
        private Component $components,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $type = Html::attribute($element, 'of');
        $found = $type !== '' ? $context->index->ofType($type) : [];
        $found = $this->filter($found, Html::attribute($element, 'where'), $context->source);
        $found = $this->sort($found, Html::attribute($element, 'sort'));

        $entries = array_map(
            fn (XmlSource $source): GridEntry => $this->entry($source, $context->index),
            $found,
        );

        return $this->components->render(
            'x-grid',
            entries: $entries,
            thumbnails: array_any($entries, static fn (GridEntry $entry): bool => $entry->hasImage()),
        );
    }

    /**
     * @param XmlSource[] $entries
     * @return XmlSource[]
     */
    private function filter(array $entries, string $predicate, XmlSource $current): array
    {
        if ($predicate === '') {
            return $entries;
        }

        if (preg_match('/^(\S+)\s*=\s*(.+)$/u', $predicate, $match) !== 1) {
            return $entries;
        }

        $field = $match[1];
        $expected = trim($match[2], " \t\"'");

        if ($expected === 'self') {
            $expected = $current->id;
        }

        return array_values(array_filter(
            $entries,
            static fn (XmlSource $entry): bool => in_array($expected, $entry->values($field), true),
        ));
    }

    /**
     * @param XmlSource[] $entries
     * @return XmlSource[]
     */
    private function sort(array $entries, string $by): array
    {
        $descending = str_starts_with($by, '-');
        $field = ltrim($by, '-');

        usort($entries, static function (XmlSource $a, XmlSource $b) use ($field): int {
            if ($field === '') {
                return XmlSource::byTitle($a, $b);
            }

            return strnatcasecmp($a->value($field) ?? '', $b->value($field) ?? '')
                ?: XmlSource::byTitle($a, $b);
        });

        return $descending ? array_reverse($entries) : $entries;
    }

    /**
     * An entry always resolves: it came out of the index, so it has a slug.
     * The unresolved case belongs to EntityLink, which serves the prose, where
     * you can name an entity before it exists.
     */
    private function entry(XmlSource $source, ContentIndex $index): GridEntry
    {
        $meta = array_filter([$source->value('annee'), $this->creators($source, $index)]);

        return new GridEntry(
            title: $source->title,
            href: $source->slug,
            meta: $meta === [] ? null : implode(', ', $meta),
            summary: $source->summary,
            thumbnail: $this->images->thumbnail($source),
        );
    }

    private function creators(XmlSource $entry, ContentIndex $index): ?string
    {
        $names = [];

        foreach ($entry->values('par') as $id) {
            $names[] = $index->find($id)?->title ?? $id;
        }

        return $names === [] ? null : implode(', ', $names);
    }
}
