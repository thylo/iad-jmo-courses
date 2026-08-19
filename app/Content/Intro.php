<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Splits a rendered page into its opening and the rest of its body.
 *
 * Every page opens the same way, whether it came from markdown or from XML:
 * one <h1>, then a paragraph saying what the page is. The design gives that
 * opening its own treatment — display type, a wider measure, the drawn rule —
 * so the template needs the two apart.
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
        /** Everything left, in document order. */
        public string $body,
    ) {}

    public static function split(string $html): self
    {
        if (preg_match('#\A\s*<h1\b[^>]*>(?P<title>.*?)</h1>\s*#is', $html, $heading) !== 1) {
            return new self(title: null, lead: null, body: $html);
        }

        $rest = substr($html, strlen($heading[0]));

        // A page that opens on a list or a note has no lead; the body keeps it all.
        if (preg_match('#\A<p\b[^>]*>(?P<lead>.*?)</p>\s*#is', $rest, $paragraph) !== 1) {
            return new self(title: $heading['title'], lead: null, body: $rest);
        }

        return new self(
            title: $heading['title'],
            lead: $paragraph['lead'],
            body: substr($rest, strlen($paragraph[0])),
        );
    }
}
