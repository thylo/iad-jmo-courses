<?php

declare(strict_types=1);

namespace App\Content;

use Tempest\Container\Singleton;
use Tempest\Markdown\Markdown;

use function Tempest\root_path;

/**
 * Lit les fichiers markdown de content/ et les expose comme des Document.
 *
 * Le parsing est mémoïsé sur la durée de la requête : une page qui affiche la
 * navigation complète touche tous les fichiers, autant ne les parser qu'une fois.
 */
#[Singleton]
final class ContentRepository
{
    private string $contentRoot;

    private SlugResolver $slugs;

    /** @var array<string, Document>|null */
    private ?array $documents = null;

    public function __construct(
        private readonly Markdown $markdown,
    ) {
        $this->contentRoot = realpath(root_path('content')) ?: root_path('content');
        $this->slugs = new SlugResolver($this->contentRoot);
    }

    /** @return array<string, Document> indexé par slug */
    public function all(): array
    {
        return $this->documents ??= $this->load();
    }

    public function find(string $slug): ?Document
    {
        $path = $this->slugs->toPath($slug);

        if ($path === null) {
            return null;
        }

        return $this->all()[$this->slugs->toSlug($path)] ?? null;
    }

    /**
     * L'arbre de navigation, trié par nom de fichier.
     *
     * C'est la règle qu'appliquait Starlight (préfixes 01-, 02-), donc l'ordre
     * du contenu réel sera le même le jour où on le migrera.
     *
     * @return NavNode[]
     */
    public function tree(): array
    {
        $home = $this->all()['/'] ?? null;

        return [
            ...($home !== null ? [new NavNode(slug: '/', title: $home->title)] : []),
            ...$this->buildTree($this->contentRoot),
        ];
    }

    /** @return NavNode[] */
    private function buildTree(string $directory): array
    {
        $nodes = [];

        foreach ($this->sortedEntries($directory) as $entry) {
            $path = $directory . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($path)) {
                // Un dossier est représenté par son index.md s'il en a un.
                $slug = $this->slugs->toSlug($path . DIRECTORY_SEPARATOR . 'index.md');
                $index = $this->all()[$slug] ?? null;

                $nodes[] = new NavNode(
                    slug: $index !== null ? $slug : '',
                    title: $index?->title ?? $this->humanize($entry),
                    children: $this->buildTree($path),
                );

                continue;
            }

            // L'index d'un dossier porte le dossier lui-même, pas une entrée à part.
            if ($entry === 'index.md') {
                continue;
            }

            if (! str_ends_with($entry, '.md')) {
                continue;
            }

            $document = $this->all()[$this->slugs->toSlug($path)] ?? null;

            if ($document === null) {
                continue;
            }

            $nodes[] = new NavNode(slug: $document->slug, title: $document->title);
        }

        return $nodes;
    }

    /** @return string[] dossiers et fichiers triés par nom */
    private function sortedEntries(string $directory): array
    {
        $entries = array_values(array_diff(scandir($directory) ?: [], ['.', '..']));
        sort($entries, SORT_NATURAL);

        return $entries;
    }

    /** @return array<string, Document> */
    private function load(): array
    {
        $documents = [];

        foreach ($this->markdownFiles() as $path) {
            $document = $this->parse($path);
            $documents[$document->slug] = $document;
        }

        return $documents;
    }

    /** @return string[] chemins absolus de tous les .md sous content/ */
    private function markdownFiles(): array
    {
        if (! is_dir($this->contentRoot)) {
            return [];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->contentRoot, \FilesystemIterator::SKIP_DOTS),
        );

        $paths = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'md') {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths, SORT_NATURAL);

        return $paths;
    }

    private function parse(string $path): Document
    {
        $parsed = $this->markdown->parse(file_get_contents($path));
        $frontmatter = $parsed->frontmatter;

        return new Document(
            slug: $this->slugs->toSlug($path),
            title: $frontmatter['title'] ?? $this->titleFromHtml($parsed->html) ?? $this->humanize(basename($path, '.md')),
            description: $frontmatter['description'] ?? null,
            html: $parsed->html,
            frontmatter: $frontmatter,
            path: $path,
        );
    }

    /** Repli sur le premier <h1> quand le frontmatter n'a pas de titre. */
    private function titleFromHtml(string $html): ?string
    {
        if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $html, $matches) !== 1) {
            return null;
        }

        $title = trim(html_entity_decode(strip_tags($matches[1])));

        return $title !== '' ? $title : null;
    }

    /** Dernier repli : « 01-frontmatter » -> « Frontmatter ». */
    private function humanize(string $name): string
    {
        $name = preg_replace('/^\d+[-_]/', '', $name);

        return ucfirst(str_replace(['-', '_'], ' ', $name));
    }
}
