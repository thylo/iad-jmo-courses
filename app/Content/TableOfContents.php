<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Extracts a page's table of contents from the rendered HTML.
 *
 * tempest/markdown already puts ids on headings (<h2 id="section-deux">), and
 * SectionElement uses the same formula, so there is nothing to rewrite here —
 * only to read.
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

            // An h3 nests under the preceding h2; with no parent h2 it stays at the top level.
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
