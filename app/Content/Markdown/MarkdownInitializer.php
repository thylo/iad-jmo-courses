<?php

declare(strict_types=1);

namespace App\Content\Markdown;

use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;
use Tempest\Highlight\Highlighter;
use Tempest\Markdown\Markdown;

/**
 * The parser every piece of prose on the site goes through.
 *
 * It exists for two rules. Everything the container built before is still
 * built here — the highlighter that colours the code blocks of the séances —
 * and two rules go in front of the rule set, where each takes its first
 * character from the upstream one: OrderedListRule the digits, QuoteRule the
 * ">".
 *
 * Prepended rather than swapped in: the rule list is upstream's business, and a
 * version that fixes these bugs should be able to take them back without
 * anything here having to be unpicked first. Removing this file restores the default
 * parser exactly.
 */
final readonly class MarkdownInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): Markdown
    {
        return new Markdown(
            highlighter: $container->get(Highlighter::class),
        )->prependRules(new OrderedListRule(), new QuoteRule());
    }
}
