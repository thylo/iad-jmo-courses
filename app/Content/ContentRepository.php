<?php

declare(strict_types=1);

namespace App\Content;

use App\Content\Xml\Autolinks;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\NodeRenderer;
use App\Content\Xml\XmlParser;
use Tempest\Container\Singleton;
use Tempest\Markdown\Markdown;

use function Tempest\root_path;

/**
 * Reads the files under content/ and exposes them as Documents.
 *
 * Markdown and XML live side by side. Markdown carries the pages that are still
 * prose; XML carries the typed entities, and only those take part in the graph.
 *
 * Loading is memoised for the request: a page that renders the full navigation
 * touches every file, so parsing them once is worth it.
 */
#[Singleton]
final class ContentRepository
{
    private string $contentRoot;

    private SlugResolver $slugs;

    /** @var array<string, Document>|null */
    private ?array $documents = null;

    /** @var Xml\XmlSource[] */
    private array $sources = [];

    /** @var array<string, true> slugs of typed entities, kept out of the navigation */
    private array $entitySlugs = [];

    private ?ContentIndex $index = null;

    public function __construct(
        private readonly Markdown $markdown,
        private readonly XmlParser $xml,
        private readonly NodeRenderer $renderer,
    ) {
        $this->contentRoot = realpath(root_path('content')) ?: root_path('content');
        $this->slugs = new SlugResolver($this->contentRoot);
    }

    /** @return array<string, Document> keyed by slug */
    public function all(): array
    {
        $this->load();

        return $this->documents;
    }

    public function find(string $slug): ?Document
    {
        $path = $this->slugs->toPath($slug);

        if ($path === null) {
            return null;
        }

        return $this->all()[$this->slugs->toSlug($path)] ?? null;
    }

    /** @return Xml\XmlSource[] the typed entities */
    public function sources(): array
    {
        $this->load();

        return $this->sources;
    }

    /** The graph: every entity by id, plus its backlinks. */
    public function index(): ContentIndex
    {
        $this->load();

        return $this->index ??= ContentIndex::build($this->sources);
    }

    /**
     * The navigation tree, sorted by filename.
     *
     * That is the rule Starlight applied (01-, 02- prefixes), so the order of the
     * real content will be the same the day we migrate it.
     *
     * Pages only. Entities are a graph, not a tree: they are reached through the
     * index of their section, through backlinks, or through a link in the prose.
     * Listing the 125 œuvres in the navigation of every page said nothing that
     * /oeuvres does not already say, and drowned the rest.
     *
     * @return NavNode[]
     */
    public function tree(): array
    {
        // buildTree() reads $entitySlugs, which only exists once everything is parsed.
        $this->load();

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
                // A folder is represented by its index file when it has one.
                $slug = $this->slugs->toSlug($path . DIRECTORY_SEPARATOR . 'index.md');
                $index = $this->all()[$slug] ?? null;

                $nodes[] = new NavNode(
                    slug: $index !== null ? $slug : '',
                    title: $index?->title ?? $this->humanize($entry),
                    children: $this->buildTree($path),
                );

                continue;
            }

            // A folder's index carries the folder itself, not a separate entry.
            if ($entry === 'index.md' || $entry === 'index.xml') {
                continue;
            }

            if (! $this->isContentFile($entry)) {
                continue;
            }

            $slug = $this->slugs->toSlug($path);

            if (isset($this->entitySlugs[$slug])) {
                continue;
            }

            $document = $this->all()[$slug] ?? null;

            if ($document === null) {
                continue;
            }

            $nodes[] = new NavNode(slug: $document->slug, title: $document->title);
        }

        return $nodes;
    }

    /** @return string[] folders and files, sorted by name */
    private function sortedEntries(string $directory): array
    {
        $entries = array_values(array_diff(scandir($directory) ?: [], ['.', '..']));
        sort($entries, SORT_NATURAL);

        return $entries;
    }

    private function load(): void
    {
        if ($this->documents !== null) {
            return;
        }

        $documents = [];
        $sources = [];

        foreach ($this->contentFiles() as $path) {
            $slug = $this->slugs->toSlug($path);

            // Leaving a converted .md next to its .xml would give one URL two
            // sources, and the navigation two entries.
            if (isset($documents[$slug])) {
                throw ContentException::at(
                    $path,
                    sprintf('Même URL (%s) que %s.', $slug, $documents[$slug]->path),
                );
            }

            if (str_ends_with($path, '.xml')) {
                $source = $this->xml->parse($path, $slug);
                $sources[] = $source;

                // 'page' is the only XML type that is not an entity.
                if ($source->type !== 'page') {
                    $this->entitySlugs[$slug] = true;
                }

                $documents[$slug] = new Document(
                    slug: $slug,
                    title: $source->title,
                    description: $source->summary,
                    frontmatter: [],
                    path: $path,
                    render: fn (): string => $this->renderer->render($source, $this->index()),
                );

                continue;
            }

            $documents[$slug] = $this->parseMarkdown($path, $slug);
        }

        $this->documents = $documents;
        $this->sources = $sources;
    }

    /** @return string[] absolute paths of every content file under content/ */
    private function contentFiles(): array
    {
        if (! is_dir($this->contentRoot)) {
            return [];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->contentRoot, \FilesystemIterator::SKIP_DOTS),
        );

        $paths = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $this->isContentFile($file->getFilename())) {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths, SORT_NATURAL);

        return $paths;
    }

    private function isContentFile(string $filename): bool
    {
        return str_ends_with($filename, '.md') || str_ends_with($filename, '.xml');
    }

    /**
     * Only the frontmatter is read now; the body waits until the page is rendered.
     *
     * Building the navigation touches every file, but a request displays exactly
     * one of them. Parsing all the bodies up front was the bulk of the work done
     * per request, and all of it but one was thrown away.
     */
    private function parseMarkdown(string $path, string $slug): Document
    {
        $raw = (string) file_get_contents($path);
        $frontmatter = $this->frontmatter($raw);
        $markdown = $this->markdown;

        return new Document(
            slug: $slug,
            title: $frontmatter['title'] ?? $this->titleFromBody($raw) ?? $this->humanize(basename($path, '.md')),
            description: $frontmatter['description'] ?? null,
            frontmatter: $frontmatter,
            path: $path,
            // Prose pages get the same URL handling as the prose inside an
            // entity — the syntax should not mean two different things.
            render: static fn (): string => $markdown->parse(Autolinks::expand($raw))->html,
        );
    }

    /**
     * Parses the leading --- block on its own.
     *
     * The slice follows tempest/markdown's FrontMatterRule exactly — skip the
     * opening dashes, stop at the next "---" — so the YAML handling stays in one
     * place and the result is the same as parsing the whole file.
     *
     * @return array<string, mixed>
     */
    private function frontmatter(string $raw): array
    {
        if (! str_starts_with($raw, '---')) {
            return [];
        }

        $end = strpos($raw, '---', strspn($raw, '-'));

        if ($end === false) {
            return [];
        }

        return $this->markdown->parse(substr($raw, 0, $end + 3))->frontmatter;
    }

    /** Falls back to the first <h1> when the frontmatter has no title. */
    private function titleFromBody(string $raw): ?string
    {
        if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $this->markdown->parse($raw)->html, $matches) !== 1) {
            return null;
        }

        $title = trim(html_entity_decode(strip_tags($matches[1])));

        return $title !== '' ? $title : null;
    }

    /** Last resort: "01-frontmatter" -> "Frontmatter". */
    private function humanize(string $name): string
    {
        $name = preg_replace('/^\d+[-_]/', '', $name);

        return ucfirst(str_replace(['-', '_'], ' ', $name));
    }
}
