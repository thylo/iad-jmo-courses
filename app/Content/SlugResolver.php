<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Traduit dans les deux sens entre chemin de fichier et slug d'URL.
 *
 *   content/index.md              -> /
 *   content/demo/index.md         -> /demo
 *   content/demo/01-frontmatter.md -> /demo/01-frontmatter
 *
 * Les préfixes numériques restent dans l'URL : ils portent l'ordre d'affichage
 * et les enlever créerait des collisions silencieuses.
 */
final readonly class SlugResolver
{
    public function __construct(
        private string $contentRoot,
    ) {}

    public function toSlug(string $absolutePath): string
    {
        $relative = ltrim(str_replace($this->contentRoot, '', $absolutePath), DIRECTORY_SEPARATOR);
        $relative = preg_replace('/\.md$/', '', $relative);
        $relative = preg_replace('#(^|/)index$#', '', $relative);

        return '/' . trim($relative, '/');
    }

    /**
     * Slug -> chemin de fichier existant, ou null.
     *
     * Le routeur accepte n'importe quelle chaîne (y compris « ../ »), donc on
     * résout puis on vérifie que le résultat est bien sous content/.
     */
    public function toPath(string $slug): ?string
    {
        $relative = trim($slug, '/');

        $candidates = $relative === ''
            ? ['index.md']
            : ["{$relative}.md", "{$relative}/index.md"];

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
