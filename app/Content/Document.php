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
    ) {}

    public string $html {
        get => $this->rendered ??= ($this->render)();
    }
}
