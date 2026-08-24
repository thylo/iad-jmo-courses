<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Splits a rendered page into its opening, its facts, and the rest of its body.
 *
 * Every page opens the same way, whether it came from markdown or from XML:
 * one <h1>, then a paragraph saying what the page is. The design gives that
 * opening its own treatment — display type, a wider measure, the signature —
 * so the template needs the parts apart.
 *
 * An entity carries a third part: the <dl> of its typed fields. That is data
 * rather than prose, and the layout sets it in the open field beside the
 * opening rather than in the reading, so it comes out here too.
 *
 * Reading them back out of the rendered HTML keeps the copy in content/ where
 * it belongs. Writing it into a template instead would mean maintaining the
 * homepage in two places.
 */
final readonly class Intro
{
    private function __construct(
        /** Inner HTML of the first <h1>, or null when the page opens without one. */
        public ?string $title,
        /** Inner HTML of the paragraph right after it, when there is one. */
        public ?string $lead,
        /** The <dl class="c-facts"> of an entity, or null on a page that has none. */
        public ?string $facts,
        /** Everything left, in document order. */
        public string $body,
    ) {}

    public static function split(string $html): self
    {
        if (preg_match('#\A\s*<h1\b[^>]*>(?P<title>.*?)</h1>\s*#is', $html, $heading) !== 1) {
            return self::rest(title: null, lead: null, html: $html);
        }

        $rest = substr($html, strlen($heading[0]));

        // A page that opens on a list or a note has no lead; the body keeps it all.
        if (preg_match('#\A<p\b[^>]*>(?P<lead>.*?)</p>\s*#is', $rest, $paragraph) !== 1) {
            return self::rest(title: $heading['title'], lead: null, html: $rest);
        }

        return self::rest(
            title: $heading['title'],
            lead: $paragraph['lead'],
            html: substr($rest, strlen($paragraph[0])),
        );
    }

    /**
     * Lifts the facts out of what is left, and builds the split.
     *
     * A <dl class="c-facts"> never nests another, so the non-greedy match is
     * exact. NodeRenderer writes it, and it writes one at most; a page with no
     * typed fields comes through untouched.
     */
    private static function rest(?string $title, ?string $lead, string $html): self
    {
        if (preg_match('#\s*<dl class="c-facts">.*?</dl>\s*#is', $html, $match) !== 1) {
            return new self(title: $title, lead: $lead, facts: null, body: $html);
        }

        return new self(
            title: $title,
            lead: $lead,
            facts: trim($match[0]),
            body: str_replace($match[0], '', $html),
        );
    }
}
