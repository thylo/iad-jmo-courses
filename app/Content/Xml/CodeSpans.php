<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * Hides fenced blocks and inline code from the rewrites that run on the markdown
 * source, so a link or a [[reference]] stays literal when it is being shown
 * rather than used.
 *
 * Every pass that rewrites the source masks through here first. The tokens use
 * NUL, which cannot appear in the XML we load.
 */
final readonly class CodeSpans
{
    private const string PATTERN = '/(?:^|(?<=\n))(?:```|~~~).*?(?:\R(?:```|~~~)|$)|`[^`\n]*`/s';

    /** @return array{0: string, 1: array<string, string>} the masked source, and what to put back */
    public static function hide(string $markdown): array
    {
        $masked = [];
        $counter = 0;

        $result = preg_replace_callback(
            self::PATTERN,
            function (array $match) use (&$masked, &$counter): string {
                $token = sprintf("\x00code-%d\x00", $counter++);
                $masked[$token] = $match[0];

                return $token;
            },
            $markdown,
        );

        return [$result ?? $markdown, $masked];
    }

    /** @param array<string, string> $masked */
    public static function restore(string $markdown, array $masked): string
    {
        return strtr($markdown, $masked);
    }
}
