<?php

declare(strict_types=1);

namespace App\Content;

/**
 * A content page: one markdown or XML file.
 */
final class Document
{
    private ?string $rendered = null;

    public function __construct(
        /** Canonical slug, no trailing slash. The root is '/'. */
        public readonly string $slug,
        public readonly string $title,
        public readonly ?string $description,
        /** Raw frontmatter, for the markdown fields we have not typed. Empty for XML. */
        public readonly array $frontmatter,
        /** Absolute path of the source file, useful in error messages. */
        public readonly string $path,
        /**
         * Rendering is deferred: an XML page needs the index to resolve its
         * links and run its queries, and the index needs every file parsed
         * first. Reading ->html happens well after loading, so there is no
         * cycle — only a construction order to respect.
         */
        private readonly \Closure $render,
        /** The layout the page asks for. Last, because it is the only optional one. */
        public readonly Layout $layout = Layout::Document,
    ) {}

    /**
     * French spacing runs here rather than in any of the pipelines that feed
     * this: markdown, captions, descriptions and the opening all arrive as one
     * string by now, and a rule stated once cannot be forgotten by the fifth
     * route someone adds later.
     */
    public string $html {
        get => $this->rendered ??= FrenchSpacing::apply(($this->render)());
    }
}
