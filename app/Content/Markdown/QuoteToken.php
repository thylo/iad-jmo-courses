<?php

declare(strict_types=1);

namespace App\Content\Markdown;

use Tempest\Markdown\Parser;
use Tempest\Markdown\Rules\BoldAndItalicRule;
use Tempest\Markdown\Rules\BoldRule;
use Tempest\Markdown\Rules\CodeRule;
use Tempest\Markdown\Rules\ImageRule;
use Tempest\Markdown\Rules\ItalicRule;
use Tempest\Markdown\Rules\LinkRule;
use Tempest\Markdown\Rules\StrikethroughRule;
use Tempest\Markdown\Rules\TextRule;
use Tempest\Markdown\Token;

/**
 * The inside of a blockquote, with the inline rules a paragraph has.
 *
 * Upstream's QuoteToken leaves CodeRule and StrikethroughRule out of its list,
 * so `draw()` in a quote reaches the page with its backticks, as typed. A
 * paragraph and a heading both have them; a quote here is a sentence of the
 * same prose set apart, and a course on code quotes code. The list below is
 * the paragraph's, less its block rules — a quote holds a line, not a <pre> —
 * plus QuoteRule for a nested quote.
 */
final readonly class QuoteToken implements Token
{
    public function __construct(
        public string $content,
    ) {}

    public function parse(Parser $parser): string
    {
        $content = $parser
            ->forToken($this, [
                new BoldAndItalicRule(),
                new BoldRule(),
                new ItalicRule(),
                new StrikethroughRule(),
                new QuoteRule(),
                new LinkRule(),
                new ImageRule(),
                new CodeRule(),
                new TextRule(),
            ])
            ->parse($this->content);

        return "<blockquote>{$content}</blockquote>";
    }
}
