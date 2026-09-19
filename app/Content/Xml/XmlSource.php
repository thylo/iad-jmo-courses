<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Layout;

/**
 * A parsed XML entity: its identity, its data, and the tree left to render.
 *
 * Parsing an entity never renders it. The index is built from XmlSource alone,
 * so the markdown of a page can safely reference an entity parsed later.
 */
final readonly class XmlSource
{
    public function __construct(
        public string $path,
        public string $slug,
        /** Root element name: 'oeuvre', 'personne'. */
        public string $type,
        /** Stable identity, the target of [[wikilinks]]. Independent of the path. */
        public string $id,
        public string $title,
        /**
         * The stretch of the title set in the accent, or null when none is.
         *
         * A piece of the title, never a second copy of it: the plain string is
         * what feeds the <title> tag, the index and the sort, and only the <h1>
         * knows this exists. XmlParser has already checked that it occurs in
         * the title, so the renderer can wrap it without looking.
         */
        public ?string $titleAccent,
        public ?string $summary,
        /** The layout the page asks for; an entity always keeps the ordinary one. */
        public Layout $layout,
        /** status="draft": loaded locally, absent in production. */
        public bool $draft,
        public \Dom\Element $root,
        /** @var array<string, string[]> field name => values (text, or referenced id) */
        public array $data,
        /** @var string[] every id this entity points at, ref= attributes and [[wikilinks]] alike */
        public array $references,
    ) {}

    /** @return string[] */
    public function values(string $field): array
    {
        return $this->data[$field] ?? [];
    }

    public function value(string $field): ?string
    {
        return $this->values($field)[0] ?? null;
    }

    /** Shared ordering, so every list of entities sorts the same way. */
    public static function byTitle(self $a, self $b): int
    {
        return strnatcasecmp($a->title, $b->title);
    }
}
