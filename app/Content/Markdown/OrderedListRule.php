<?php

declare(strict_types=1);

namespace App\Content\Markdown;

use Tempest\Markdown\Parser;
use Tempest\Markdown\ProvidesFirstChar;
use Tempest\Markdown\Rule;
use Tempest\Markdown\Token;
use Tempest\Markdown\Tokens\ListItem;
use Tempest\Markdown\Tokens\OrderedListToken;

/**
 * An ordered list whose items may run over more than one line.
 *
 * Tempest's own rule reads the indented block under an item and hands it to a
 * lexer that knows a single rule, itself. A nested "2." in that block comes
 * back as a list; a plain sentence matches nothing, produces no token, and is
 * dropped without a word. An item wrapped at the width of the file therefore
 * loses everything after its first line, and the page says half of what the
 * source says.
 *
 * ListRule, the one for bullets, already carries the branch that is missing:
 * no nested list in the block means the block belongs to the item, folded back
 * into one line. This is that branch, for digits, and the rest is the rule as
 * it stands upstream.
 *
 * Prose here is written wrapped at the width of the file, so an item of two or
 * three lines is the normal case rather than an edge one. That is the whole
 * reason this class exists: the content decides how the source is written, and
 * the parser follows.
 *
 * MarkdownInitializer prepends it, which is what puts it ahead of the upstream
 * rule on the digits.
 */
final class OrderedListRule implements Rule, ProvidesFirstChar
{
    public string $firstChar = '0123456789';

    public function shouldParse(Parser $parser): bool
    {
        if (! ctype_digit($parser->current ?? '')) {
            return false;
        }

        $search = $parser->lookaheadUntil('.', PHP_EOL);

        if (count($search) !== 2) {
            return false;
        }

        if (! ctype_digit(rtrim($search[0], '.'))) {
            return false;
        }

        // Must be followed by a space.
        if (($search[1][0] ?? null) !== ' ') {
            return false;
        }

        return true;
    }

    public function parse(Parser $parser): ?Token
    {
        $parser->consumeWhile('0123456789');
        $parser->consumeIncluding('.');
        $content = trim($parser->consumeUntil(Parser::NEW_LINE));
        $parser->consumeWhile(Parser::NEW_LINE);

        $childContent = '';
        $indent = strspn($parser->content, ' ', $parser->position);

        while ($indent >= 2 && $parser->current !== null) {
            if (strspn($parser->content, ' ', $parser->position) < $indent) {
                break;
            }

            $parser->consume($indent);
            $childContent .= $parser->consumeUntil(Parser::NEW_LINE) . PHP_EOL;
            $parser->consumeWhile(Parser::NEW_LINE);
        }

        $children = null;

        // A line of its own that opens with "1. " is a list; anything else the
        // block holds is the item continuing, and the item is where it goes.
        if ($childContent !== '' && preg_match('/^\d+\.\s/m', $childContent) === 1) {
            $children = $parser->withRules(new self())->lex($childContent)[0];
        }

        if ($children === null && $childContent !== '') {
            $content .= ' ' . trim(preg_replace('/\s+/u', ' ', $childContent) ?? '');
        }

        $item = new ListItem($content, $children);

        if ($parser->lastToken instanceof OrderedListToken) {
            $parser->lastToken->items[] = $item;

            return null;
        }

        return new OrderedListToken([$item]);
    }
}
