<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Translates both ways between file path and URL slug.
 *
 *   content/index.md               -> /
 *   content/demo/index.md          -> /demo
 *   content/demo/01-frontmatter.md -> /demo/01-frontmatter
 *   content/oeuvres/a-dark-room.xml -> /oeuvres/a-dark-room
 *
 * Numeric prefixes stay in the URL: they carry the display order, and stripping
 * them would create silent collisions.
 */
final readonly class SlugResolver
{
    public function __construct(
        private string $contentRoot,
    ) {}

    public function toSlug(string $absolutePath): string
    {
        $relative = ltrim(str_replace($this->contentRoot, '', $absolutePath), DIRECTORY_SEPARATOR);
        $relative = preg_replace('/\.(md|xml)$/', '', $relative);
        $relative = preg_replace('#(^|/)index$#', '', $relative);

        return '/' . trim($relative, '/');
    }

    /**
     * Slug -> an existing file path, or null.
     *
     * The router accepts any string, "../" included, so we resolve first and
     * then check the result really sits under content/.
     */
    public function toPath(string $slug): ?string
    {
        $relative = trim($slug, '/');

        // XML first: it is the format we are migrating towards, so it wins a tie.
        $candidates = $relative === ''
            ? ['index.xml', 'index.md']
            : ["{$relative}.xml", "{$relative}.md", "{$relative}/index.xml", "{$relative}/index.md"];

        foreach ($candidates as $candidate) {
            $resolved = realpath($this->contentRoot . DIRECTORY_SEPARATOR . $candidate);

            if ($resolved === false || ! $this->isInsideContentRoot($resolved)) {
                continue;
            }

            return $resolved;
        }

        return null;
    }

    private function isInsideContentRoot(string $resolved): bool
    {
        return str_starts_with($resolved, $this->contentRoot . DIRECTORY_SEPARATOR);
    }
}
