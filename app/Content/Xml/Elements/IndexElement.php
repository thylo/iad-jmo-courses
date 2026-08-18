<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Xml\ContentIndex;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\EntityLink;
use App\Content\Html;
use App\Content\Xml\RenderContext;
use App\Content\Xml\XmlSource;

/**
 * <index de="oeuvre" ou="par = self" tri="annee"/>
 *
 * The derived block: a page states what it wants to show, not what it shows.
 * A studio page never names its works — the works name the studio, and this
 * reads the relation the other way round.
 *
 * The predicate is deliberately minimal: `field = value`, where value may be
 * the keyword `self`. No query language until a real need shows up.
 */
final readonly class IndexElement implements ElementRenderer
{
    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $type = Html::attribute($element, 'de');
        $entries = $type !== '' ? $context->index->ofType($type) : [];
        $entries = $this->filter($entries, Html::attribute($element, 'ou'), $context->source);
        $entries = $this->sort($entries, Html::attribute($element, 'tri'));

        if ($entries === []) {
            return '<p class="index-vide">Rien pour l’instant.</p>';
        }

        $items = '';

        foreach ($entries as $entry) {
            $items .= sprintf('<li>%s</li>', $this->line($entry, $context->index));
        }

        return sprintf('<ul class="index">%s</ul>', $items);
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

    private function line(XmlSource $entry, ContentIndex $index): string
    {
        $link = EntityLink::html($index, $entry->id);

        $meta = array_filter([$entry->value('annee'), $this->creators($entry, $index)]);

        if ($meta !== []) {
            $link .= sprintf(' <span class="meta">%s</span>', Html::escape(implode(', ', $meta)));
        }

        if ($entry->summary !== null) {
            $link .= ' — ' . Html::escape($entry->summary);
        }

        return $link;
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
