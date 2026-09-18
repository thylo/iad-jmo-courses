<?php

declare(strict_types=1);

namespace App\Content;

/**
 * The space French puts before a double punctuation mark, and inside quotation
 * marks — the one typographic rule a French page cannot skip.
 *
 * Without it a line may break between a word and its colon, and between a
 * guillemet and what it opens. Both happen on this site: the measure is narrow
 * by design, so there are a lot of line ends to be wrong at.
 *
 * --- Why here, and only here --------------------------------------------
 * French prose reaches a page by four routes — a <markdown> block, a caption
 * written in an attribute, a <destination> description, the title and summary
 * lifted into the opening. Fixing four pipelines means the fifth one someone
 * adds next year is wrong again. Document::$html is where all four have already
 * become one string, so the rule is stated once and cannot be escaped.
 *
 * What it does not cover is the frame — the masthead, the colophon — which is
 * literal text in a view rather than content. Three sentences, written by hand,
 * and they carry their own &nbsp; where they need one.
 *
 * --- Why U+00A0 and not U+202F -----------------------------------------
 * The Imprimerie nationale wants a narrow no-break space before ; ! ? and a
 * full one before :. U+202F is the correct character and it is the wrong choice
 * here: the two faces on this site are Japanese, subset to latin and latin-ext
 * by fontsource, and neither subset carries it. A missing glyph falls through
 * to whatever the system has, which is a different width in a different face at
 * every reader — or nothing at all.
 *
 * U+00A0 is in every font that has ever existed and browsers treat it as
 * whitespace rather than as a glyph to find. Slightly wide before a semicolon,
 * right everywhere, and it will still be right in ten years. Durability beats
 * the finer rule when the finer rule depends on a font subset.
 */
final class FrenchSpacing
{
    /**
     * A space before one of these belongs to the word, not to the line break.
     * The closing guillemet is in the list for the same reason as the colon.
     */
    private const string BEFORE = '~[ \t\n\r]+([;:!?»])~u';

    /** And the opening guillemet holds on to what follows it. */
    private const string AFTER = '~(«)[ \t\n\r]+~u';

    /**
     * What the rule may not touch: the inside of a tag, and anything being
     * shown as code rather than read as prose.
     *
     * Masking rather than matching around, the same way CodeSpans does it on
     * the markdown source — a rewrite that has to know where it is is a rewrite
     * that will one day be somewhere else. The tag pattern is safe against a
     * ">" inside an attribute because the views escape attribute values, so the
     * only ">" left in the document is the one that closes a tag.
     */
    private const string OPAQUE = '~<(pre|code|script|style)\b[^>]*>.*?</\1\s*>|<[^>]*>~is';

    private const string NBSP = "\u{00A0}";

    public static function apply(string $html): string
    {
        [$masked, $held] = self::hide($html);

        $spaced = preg_replace(
            [self::BEFORE, self::AFTER],
            [self::NBSP . '$1', '$1' . self::NBSP],
            $masked,
        );

        return strtr($spaced ?? $masked, $held);
    }

    /**
     * @return array{0: string, 1: array<string, string>} the masked HTML, and
     *                                                    what to put back
     */
    private static function hide(string $html): array
    {
        $held = [];
        $counter = 0;

        $masked = preg_replace_callback(
            self::OPAQUE,
            function (array $match) use (&$held, &$counter): string {
                // NUL, which cannot appear in the HTML we render — the same
                // token CodeSpans uses on the source side.
                $token = sprintf("\x00html-%d\x00", $counter++);
                $held[$token] = $match[0];

                return $token;
            },
            $html,
        );

        return [$masked ?? $html, $held];
    }
}
