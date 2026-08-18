<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * Turns a URL written in prose into a link, in the two forms people actually
 * write: <https://example.org>, the CommonMark autolink, and a bare
 * https://example.org, the GFM one.
 *
 * Nothing else is invented here. A mail link is written as the anchor it is,
 * <a href="mailto:…">, and passes through as inline HTML.
 *
 * tempest/markdown has a rule for [label](url) and nothing else, so both forms
 * rendered as dead text. Like [[wikilinks]], this runs on the source rather than
 * as a Markdown rule — block tokens rebuild their own inline rule list, so a
 * registered rule never reaches inside a paragraph or a list item.
 *
 * The three passes run in order of explicitness, each holding what it produces
 * aside so the next one cannot look at it twice.
 */
final readonly class Autolinks
{
    /** <https://…>, the CommonMark form. */
    private const string BRACKETED = '~<(https?://[^\s<>]+)>~u';

    /** Already a link: a [label](url) destination, or an HTML tag with its content. */
    private const string LINKED = '~!?\[[^\]]*\]\([^)\s]*\)|<a\b[^<>]*>.*?</a>|<[a-zA-Z/!][^<>]*>~us';

    /** What is left: a URL on its own, up to the first whitespace or delimiter. */
    private const string BARE = '~(?<![\w"\'=/])(https?://[^\s<>"\'`\[\]]+)~u';

    /** Trailing punctuation that belongs to the sentence, not to the URL. */
    private const string TRAILING = '.,;:!?»…';

    public static function expand(string $markdown): string
    {
        [$markdown, $code] = CodeSpans::hide($markdown);

        $held = [];

        $markdown = self::hold(
            self::BRACKETED,
            $markdown,
            $held,
            static fn (array $match): string => ExternalLink::html($match[1]),
        );

        $markdown = self::hold(
            self::LINKED,
            $markdown,
            $held,
            static fn (array $match): string => $match[0],
        );

        $markdown = preg_replace_callback(
            self::BARE,
            static function (array $match): string {
                [$url, $tail] = self::split($match[1]);

                return ExternalLink::html($url) . $tail;
            },
            $markdown,
        ) ?? $markdown;

        return CodeSpans::restore(strtr($markdown, $held), $code);
    }

    /**
     * Swaps every match for a placeholder and keeps what it becomes, to be put
     * back once the later passes have run.
     *
     * @param array<string, string> $held
     */
    private static function hold(string $pattern, string $markdown, array &$held, callable $becomes): string
    {
        $result = preg_replace_callback(
            $pattern,
            function (array $match) use (&$held, $becomes): string {
                $token = sprintf("\x00link-%d\x00", count($held));
                $held[$token] = $becomes($match);

                return $token;
            },
            $markdown,
        );

        return $result ?? $markdown;
    }

    /**
     * Splits the sentence's punctuation off the end of the URL.
     *
     * A closing parenthesis only counts as the URL's own when one was opened
     * inside it, which is what keeps addresses like Wikipedia's whole.
     *
     * @return array{0: string, 1: string} the URL, and what followed it
     */
    private static function split(string $url): array
    {
        $tail = '';

        while ($url !== '') {
            $last = mb_substr($url, -1);

            $isSentence = str_contains(self::TRAILING, $last)
                || ($last === ')' && mb_substr_count($url, '(') < mb_substr_count($url, ')'));

            if (! $isSentence) {
                break;
            }

            $tail = $last . $tail;
            $url = mb_substr($url, 0, -1);
        }

        return [$url, $tail];
    }
}
