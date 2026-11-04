<?php

declare(strict_types=1);

namespace App\Content\Markdown;

use Tempest\Markdown\Parser;
use Tempest\Markdown\ProvidesFirstChar;
use Tempest\Markdown\ProvidesStopChar;
use Tempest\Markdown\Rule;
use Tempest\Markdown\Token;

/**
 * A blockquote, read line by line as upstream does, handed to a token that
 * knows inline code.
 *
 * The rule itself is upstream's, unchanged: what was wrong is the token it
 * returns — see QuoteToken. It has to be copied all the same, because the
 * upstream rule is final and builds its own token by name.
 *
 * MarkdownInitializer prepends it, which is what puts it ahead of the upstream
 * rule on the ">".
 */
final class QuoteRule implements Rule, ProvidesFirstChar, ProvidesStopChar
{
    public string $firstChar = '>';

    public string $stopChar = '>';

    public function shouldParse(Parser $parser): bool
    {
        if (! $parser->comesNext('>', 1)) {
            return false;
        }

        return $parser->position === 0 || ($parser->content[$parser->position - 1] ?? null) === PHP_EOL;
    }

    public function parse(Parser $parser): Token
    {
        $lines = [];

        while ($parser->comesNext('>', 1)) {
            $line = $parser->consumeUntil(Parser::NEW_LINE);

            $lines[] = str_starts_with($line, '> ') ? substr($line, 2) : substr($line, 1);

            $parser->consumeWhile(Parser::NEW_LINE);
        }

        return new QuoteToken(implode(PHP_EOL, $lines));
    }
}
