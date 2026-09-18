<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * Every entity, keyed by id, plus the reverse of their references.
 *
 * This is what turns the content into a graph: a page never lists what points at
 * it, it asks. Backlinks are simply the reference map read the other way round.
 */
final readonly class ContentIndex
{
    /**
     * @param array<string, XmlSource> $byId
     * @param array<string, string[]> $backlinks target id => ids pointing at it
     */
    private function __construct(
        private array $byId,
        private array $backlinks,
    ) {}

    /** @param XmlSource[] $sources */
    public static function build(array $sources): self
    {
        $byId = [];
        $backlinks = [];

        foreach ($sources as $source) {
            // The loader rejects a second file claiming an id and records it as
            // that file's failure, so it never gets here. Building the graph is
            // not the place to take the site down: first read wins.
            $byId[$source->id] ??= $source;
        }

        foreach ($sources as $source) {
            foreach (array_unique($source->references) as $target) {
                // An entity citing itself is not a backlink.
                if ($target !== $source->id) {
                    $backlinks[$target][] = $source->id;
                }
            }
        }

        return new self($byId, $backlinks);
    }

    public function find(string $id): ?XmlSource
    {
        return $this->byId[$id] ?? null;
    }

    public function has(string $id): bool
    {
        return isset($this->byId[$id]);
    }

    /** @return XmlSource[] */
    public function ofType(string $type): array
    {
        return array_values(array_filter($this->byId, static fn (XmlSource $s): bool => $s->type === $type));
    }

    /**
     * The credit line of an entity: who made it, by name.
     *
     * A <par> holds either a reference or a plain name, so a value that
     * resolves to nothing is already the name. Null rather than empty, like
     * every other absent field.
     */
    public function creditOf(XmlSource $source): ?string
    {
        $names = array_map(
            fn (string $value): string => $this->find($value)?->title ?? $value,
            $source->values('par'),
        );

        return $names === [] ? null : implode(', ', $names);
    }

    /** @return XmlSource[] entities pointing at $id */
    public function backlinksTo(string $id): array
    {
        return array_values(array_map(
            fn (string $source): XmlSource => $this->byId[$source],
            $this->backlinks[$id] ?? [],
        ));
    }
}
