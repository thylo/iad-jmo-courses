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
 * A page may ask for three more. <definition> is the gloss of a title the
 * reader may not know, set under it before the summary. <image width="opening"/>
 * is the plate that opens it — same move as the facts and for the same reason,
 * the field beside the summary is the one place on the sheet with room for an
 * image and nothing for it to interrupt. <preamble> is the paragraph that
 * carries on from the summary instead of starting the reading, and it stays in
 * the opening block, under the lead. All three are lifted off the class their
 * renderer writes, and a page gets one of each; a second stays where it was
 * written, which is what it deserves.
 *
 * Reading them back out of the rendered HTML keeps the copy in content/ where
 * it belongs. Writing it into a template instead would mean maintaining the
 * homepage in two places.
 */
final readonly class Intro
{
    private function __construct(
        /** The <p> a page set with <suptitle>, written above the heading, or null. */
        public ?string $suptitle,
        /** Inner HTML of the first <h1>, or null when the page opens without one. */
        public ?string $title,
        /** The <p> a page set with <subtitle>, the heading's second line, or null. */
        public ?string $subtitle,
        /** The <p> a page set with <definition>, or null. */
        public ?string $definition,
        /** Inner HTML of the paragraph right after it, when there is one. */
        public ?string $lead,
        /** The <dl class="c-facts"> of an entity, or null on a page that has none. */
        public ?string $facts,
        /** The <figure> a page named as its opening plate, or null. */
        public ?string $plate,
        /** The prose a page set with <preamble>, or null. */
        public ?string $preamble,
        /** Everything left, in document order. */
        public string $body,
    ) {}

    public static function split(string $html): self
    {
        // First, and out of the whole page: the gloss is a paragraph too, and on
        // a page with no summary it would otherwise be taken for the lead.
        [$definition, $html] = self::lift('#\s*<p class="c-intro__definition">.*?</p>\s*#is', $html);

        // The other two lines of the heading, lifted before it is looked for.
        // The suptitle is written above the <h1>, so the opening would not be
        // recognised at all with it still in place; the subtitle sits exactly
        // where the lead is looked for, and would be taken for the summary.
        [$suptitle, $html] = self::lift('#\s*<p class="c-intro__suptitle">.*?</p>\s*#is', $html);
        [$subtitle, $html] = self::lift('#\s*<p class="c-intro__subtitle">.*?</p>\s*#is', $html);

        if (preg_match('#\A\s*<h1\b[^>]*>(?P<title>.*?)</h1>\s*#is', $html, $heading) !== 1) {
            return self::rest(
                suptitle: $suptitle,
                title: null,
                subtitle: $subtitle,
                definition: $definition,
                lead: null,
                html: $html,
            );
        }

        $rest = substr($html, strlen($heading[0]));

        // A page that opens on a list or a note has no lead; the body keeps it all.
        if (preg_match('#\A<p\b[^>]*>(?P<lead>.*?)</p>\s*#is', $rest, $paragraph) !== 1) {
            return self::rest(
                suptitle: $suptitle,
                title: $heading['title'],
                subtitle: $subtitle,
                definition: $definition,
                lead: null,
                html: $rest,
            );
        }

        return self::rest(
            suptitle: $suptitle,
            title: $heading['title'],
            subtitle: $subtitle,
            definition: $definition,
            lead: $paragraph['lead'],
            html: substr($rest, strlen($paragraph[0])),
        );
    }

    /**
     * Lifts the three blocks that belong to the opening, and builds the split.
     *
     * None of the three ever nests another of its own kind — a <dl class=
     * "c-facts">, a <figure>, and the single <div> x-prose writes — so the
     * non-greedy matches are exact. All three are written by code rather than
     * by hand: NodeRenderer writes the facts, ImageTag writes the figure and
     * only on width="opening", PreambleElement writes the second class on the
     * prose box. So the classes are a contract with this file and not a guess
     * about what an author typed.
     */
    private static function rest(
        ?string $suptitle,
        ?string $title,
        ?string $subtitle,
        ?string $definition,
        ?string $lead,
        string $html,
    ): self {
        [$plate, $html] = self::lift('#\s*<figure class="c-figure c-figure--opening">.*?</figure>\s*#is', $html);
        [$preamble, $html] = self::lift('#\s*<div class="c-prose c-intro__preamble">.*?</div>\s*#is', $html);
        [$facts, $html] = self::lift('#\s*<dl class="c-facts">.*?</dl>\s*#is', $html);

        return new self(
            suptitle: $suptitle,
            title: $title,
            subtitle: $subtitle,
            definition: $definition,
            lead: $lead,
            facts: $facts,
            plate: $plate,
            preamble: $preamble,
            body: $html,
        );
    }

    /**
     * The first block the pattern matches, and the body without it.
     *
     * Cut by offset rather than by str_replace: two identical figures on one
     * page would otherwise both disappear, and the second one is a mistake to
     * see rather than a mistake to hide.
     *
     * @return array{0: ?string, 1: string}
     */
    private static function lift(string $pattern, string $html): array
    {
        if (preg_match($pattern, $html, $match, PREG_OFFSET_CAPTURE) !== 1) {
            return [null, $html];
        }

        [$block, $offset] = $match[0];

        return [trim($block), substr_replace($html, '', $offset, strlen($block))];
    }
}
