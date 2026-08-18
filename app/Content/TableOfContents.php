<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Extrait le sommaire d'une page depuis le HTML rendu.
 *
 * tempest/markdown pose déjà des id sur les titres (<h2 id="section-deux">),
 * donc il n'y a rien à réécrire : on se contente de lire.
 */
final readonly class TableOfContents
{
    private function __construct(
        /** @var TocEntry[] */
        public array $entries,
    ) {}

    public static function fromHtml(string $html): self
    {
        preg_match_all(
            '#<h(?P<level>[23])[^>]*\bid="(?P<id>[^"]+)"[^>]*>(?P<label>.*?)</h\1>#is',
            $html,
            $matches,
            PREG_SET_ORDER,
        );

        $entries = [];

        foreach ($matches as $match) {
            $entry = new TocEntry(
                id: $match['id'],
                label: trim(html_entity_decode(strip_tags($match['label']))),
            );

            // Un h3 se range sous le h2 précédent ; sans h2 parent, il reste au premier niveau.
            if ($match['level'] === '3' && $entries !== []) {
                $parent = array_key_last($entries);
                $entries[$parent] = $entries[$parent]->withChild($entry);

                continue;
            }

            $entries[] = $entry;
        }

        return new self(array_values($entries));
    }

    public function isEmpty(): bool
    {
        return $this->entries === [];
    }
}
