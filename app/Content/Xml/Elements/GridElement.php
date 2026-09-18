<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;
use App\Http\QueryString;
use App\Media\ImageTag;
use App\View\Component;

use function Tempest\Support\Str\to_ascii;

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
 * views/elements/x-grid.view.php. The tree walk cannot be a view component — Tempest
 * expands those at compile time, and NodeRenderer says why — but a flat list
 * of entries can, so it is one.
 *
 * A long index also answers ?q=…, and says how many entries it holds. Neither
 * is written on the tag: see LONG_INDEX for why the count decides.
 */
final readonly class GridElement implements ElementRenderer
{
    /**
     * Past this many entries, an index stops being read and starts being
     * hunted through: nobody scans 141 titles to find one. That is the moment
     * a search field earns its place, and the moment the index is worth the
     * whole width of the page rather than the width of the reading.
     *
     * Derived from the count rather than written on the tag, like the
     * thumbnails modifier and for the same reason: whether a list is long is
     * a fact about the corpus, not an intention of the page. The day a studio
     * has thirty works, its page gets a search field without anyone editing it.
     */
    private const int LONG_INDEX = 24;

    /** The name in the address bar. Short, because a reader sees it. */
    private const string SEARCH_PARAM = 'q';

    public function __construct(
        private ImageTag $images,
        private Component $components,
        private SchemaRegistry $schemas,
        private QueryString $query,
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

        // The whole index decides, never the result of a search: a term that
        // matches three works must not take away the field it was typed in.
        $long = count($entries) >= self::LONG_INDEX;
        $term = $long ? $this->query->get(self::SEARCH_PARAM) : '';
        $shown = $term === '' ? $entries : $this->matching($entries, $term);

        return $this->components->render(
            'x-grid',
            entries: $shown,
            // Read from the whole index too, so a search whose results happen
            // to have no image does not turn the columns back into a list.
            thumbnails: array_any($entries, static fn (GridEntry $entry): bool => $entry->hasImage()),
            searchable: $long,
            parameter: self::SEARCH_PARAM,
            term: $term,
            showing: count($shown),
            total: count($entries),
            plural: $this->schemas->get($type)?->plural ?? '',
            href: $context->source->slug,
        );
    }

    /**
     * The entries a search term keeps.
     *
     * Every word has to be found, in the title, the year, the creators or the
     * summary — the line the reader is looking at, nothing hidden behind it.
     * Several words narrow instead of widening: "case explorable" is one work,
     * not everything by Nicky Case plus everything explorable.
     *
     * @param GridEntry[] $entries
     * @return GridEntry[]
     */
    private function matching(array $entries, string $term): array
    {
        $words = preg_split('/\s+/u', $this->fold($term), flags: PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return $entries;
        }

        return array_values(array_filter($entries, function (GridEntry $entry) use ($words): bool {
            $haystack = $this->fold(implode(' ', array_filter([
                $entry->title,
                $entry->meta,
                $entry->summary,
            ])));

            foreach ($words as $word) {
                if (! str_contains($haystack, $word)) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * Case and accents folded away, so "phallaina" finds Phallaïna and "oeuvre"
     * finds œuvre. A reader typing into a search field is not going to reach
     * for the right diacritic, and refusing them the result would be pedantry.
     */
    private function fold(string $value): string
    {
        return mb_strtolower(to_ascii($value));
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
