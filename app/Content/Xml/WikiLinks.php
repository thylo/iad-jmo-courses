<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Html;

/**
 * Rewrites [[id]], [[id|label]] and ![[id]] before the markdown is parsed.
 *
 * This happens on the source string rather than through a Markdown rule on
 * purpose: every block token in tempest/markdown rebuilds its own inline rule
 * list (see ParagraphToken), so a rule registered with prependRules() never
 * reaches inside a paragraph, a list item or a heading. Rewriting upstream works
 * everywhere, and lets us emit the exact anchor we want — inline HTML is passed
 * through untouched by the parser.
 */
final readonly class WikiLinks
{
    /** [[id]], [[id|label]], optionally prefixed by ! for a transclusion. */
    private const string PATTERN = '/(!?)\[\[\s*([^\]|]+?)\s*(?:\|\s*([^\]]*?)\s*)?\]\]/u';

    public function __construct(
        private ContentIndex $index,
    ) {}

    public function expand(string $markdown): string
    {
        [$markdown, $code] = CodeSpans::hide($markdown);

        $expanded = preg_replace_callback(
            self::PATTERN,
            fn (array $match): string => $match[1] === '!'
                ? $this->transclusion($match[2])
                : EntityLink::html($this->index, $match[2], ($match[3] ?? '') !== '' ? $match[3] : null),
            $markdown,
        );

        return CodeSpans::restore($expanded ?? $markdown, $code);
    }

    /** Every id mentioned, resolved or not. Used to build the backlink map. */
    public static function targets(string $markdown): array
    {
        preg_match_all(self::PATTERN, $markdown, $matches);

        return array_values(array_unique($matches[2] ?? []));
    }

    /** ![[id]] pulls in the target's <resume>, next to its link. */
    private function transclusion(string $id): string
    {
        $link = EntityLink::html($this->index, $id);
        $summary = $this->index->find($id)?->summary;

        if ($summary === null) {
            return $link;
        }

        return sprintf('<span class="transclusion">%s — %s</span>', $link, Html::escape($summary));
    }
}
