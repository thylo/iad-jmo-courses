<?php

declare(strict_types=1);

namespace App\Content\Xml\Elements;

use App\Content\Html;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\ElementRenderer;
use App\Content\Xml\RenderContext;
use App\Content\Xml\SchemaRegistry;
use App\Content\Xml\XmlSource;
use App\View\Component;

/**
 * <backlinks/> — who points here.
 *
 * Free, because it is the reference map read backwards. Nothing to declare, and
 * nothing to keep in sync.
 *
 * The map has no order of its own, so the list takes the only two the data
 * holds: what kind of thing points here, and, for what is dated, when. An
 * alphabetical run of 26 titles put a concept between two films because both
 * started with an F; grouped, the notions next door and the works that show
 * the idea are two answers to two questions, and the works read as a history.
 */
final readonly class BacklinksElement implements ElementRenderer
{
    /**
     * Short groups first, so none is buried under a long register: the pages
     * of the course, the neighbouring notions, the people, then the works.
     * Fixed rather than counted, so the order is the same on every page and a
     * reader only has to learn it once. A type missing here goes last.
     */
    private const array ORDER = ['page', 'concept', 'personne', 'oeuvre'];

    /**
     * Kinds of source left out, by the type of the page. A concept is cited by
     * the neighbouring concepts that compare themselves to it, and listing them
     * says nothing about it: what it needs at the foot of the page is the works
     * that show it.
     */
    private const array LEFT_OUT = ['concept' => ['concept']];

    public function __construct(
        private Component $components,
        private SchemaRegistry $schemas,
    ) {}

    public function render(\Dom\Element $element, RenderContext $context): string
    {
        $except = self::LEFT_OUT[$context->source->type] ?? [];

        $sources = array_values(array_filter(
            $context->index->backlinksTo($context->source->id),
            static fn (XmlSource $source): bool => ! in_array($source->type, $except, true),
        ));

        if ($sources === []) {
            return '';
        }

        $byType = [];

        foreach ($sources as $source) {
            $byType[$source->type][] = $source;
        }

        uksort($byType, static fn (string $a, string $b): int => self::rank($a) <=> self::rank($b));

        $title = Html::attribute($element, 'title') ?: 'Mentionné dans';

        return $this->components->render(
            'x-backlinks',
            title: $title,
            id: Html::id($title),
            groups: array_map(
                fn (string $type, array $group): array => $this->group($type, $group, $context->index),
                array_keys($byType),
                $byType,
            ),
        );
    }

    private static function rank(string $type): int
    {
        $rank = array_search($type, self::ORDER, true);

        return $rank === false ? count(self::ORDER) : $rank;
    }

    /**
     * One kind of source. Dated when any entry carries a year: then the group
     * sorts by it, oldest first, and an undated entry closes the run rather
     * than opening it.
     *
     * @param XmlSource[] $sources
     * @return array{label: string, dated: bool, entries: array<int, array{href: string, label: string, credit: ?string, year: ?string, repeat: bool}>}
     */
    private function group(string $type, array $sources, ContentIndex $index): array
    {
        $dated = array_any($sources, static fn (XmlSource $source): bool => $source->value('annee') !== null);

        usort($sources, $dated ? self::byYear(...) : XmlSource::byTitle(...));

        $entries = [];
        $previous = null;

        foreach ($sources as $source) {
            $year = $source->value('annee');

            $entries[] = [
                'href' => $source->slug,
                'label' => $source->title,
                'credit' => $index->creditOf($source),
                'year' => $year,
                // A year is printed once, at the head of its run. The repeats
                // stay in the markup for whoever reads it aloud.
                'repeat' => $year !== null && $year === $previous,
            ];

            $previous = $year;
        }

        return [
            'label' => mb_ucfirst($this->schemas->get($type)?->plural ?? $type),
            'dated' => $dated,
            'entries' => $entries,
        ];
    }

    private static function byYear(XmlSource $a, XmlSource $b): int
    {
        return [$a->value('annee') === null, $a->value('annee')] <=> [$b->value('annee') === null, $b->value('annee')]
            ?: XmlSource::byTitle($a, $b);
    }
}
