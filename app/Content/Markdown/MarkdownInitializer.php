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
 * It exists for one rule. Everything the container built before is still built
 * here — the highlighter that colours the code blocks of the six séances — and
 * OrderedListRule goes in front of the rule set, where it takes the digits from
 * the upstream one.
 *
 * Prepended rather than swapped in: the rule list is upstream's business, and a
 * version that fixes this bug should be able to take it back without anything
 * here having to be unpicked first. Removing this file restores the default
 * parser exactly.
 */
final readonly class MarkdownInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): Markdown
    {
        return new Markdown(
            highlighter: $container->get(Highlighter::class),
        )->prependRules(new OrderedListRule());
    }
}
