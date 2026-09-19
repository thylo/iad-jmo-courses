<?php

declare(strict_types=1);

namespace App\Content;

use App\Content\Xml\Autolinks;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\NodeRenderer;
use App\Content\Xml\XmlParser;
use Tempest\Container\Singleton;
use Tempest\Core\Environment;
use Tempest\Markdown\Markdown;

use function Tempest\root_path;

/**
 * Reads the files under content/ and exposes them as Documents.
 *
 * Every page under content/ is XML now. The markdown path is kept because the
 * loader still accepts a .md file dropped in while drafting, but nothing ships
 * that way: only XML sources enter the graph, so only they can be linked to,
 * indexed, or backlinked.
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

    /** @var array<string, string> path => why that file did not load */
    private array $failures = [];

    private ?ContentIndex $index = null;

    public function __construct(
        private readonly Markdown $markdown,
        private readonly XmlParser $xml,
        private readonly NodeRenderer $renderer,
        private readonly Autolinks $autolinks,
        private readonly Environment $environment,
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

    /**
     * The files that did not load, and why.
     *
     * Nothing renders them: their URL is simply not there, which is honest. The
     * loud part is content:check, whose job is exactly this.
     *
     * @return array<string, string> path => message
     */
    public function failures(): array
    {
        $this->load();

        return $this->failures;
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
     * The homepage is not in it either. The masthead is a link home on every
     * page, so naming it again at the top of the list was the site telling the
     * reader twice about the one place they already know how to reach.
     *
     * @return NavNode[]
     */
    public function tree(): array
    {
        // buildTree() reads $entitySlugs, which only exists once everything is parsed.
        $this->load();

        return $this->buildTree($this->contentRoot);
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

        /** @var array<string, string> id => the file that took it first */
        $ids = [];

        foreach ($this->contentFiles() as $path) {
            $slug = $this->slugs->toSlug($path);

            // One bad file is one missing page, not a site that is down. The
            // rule is still that a half-rendered page is worse than a plain
            // error — but the error belongs to the file that carries it. A
            // typo in one œuvre used to take the other 139 with it, plus the
            // homepage, which is a punishment out of all proportion.
            try {
                if (isset($documents[$slug])) {
                    // Leaving a converted .md next to its .xml would give one URL
                    // two sources, and the navigation two entries.
                    throw ContentException::at(
                        $path,
                        sprintf('Même URL (%s) que %s.', $slug, $documents[$slug]->path),
                    );
                }

                if (! str_ends_with($path, '.xml')) {
                    $documents[$slug] = $this->parseMarkdown($path, $slug);

                    continue;
                }

                $source = $this->xml->parse($path, $slug);

                // A draft is skipped before anything sees it, so production has
                // no URL, no navigation entry, no sitemap line and no backlink
                // for it. Locally it loads like any page, to be read in place.
                if ($source->draft && $this->environment->isProduction()) {
                    continue;
                }

                if (isset($ids[$source->id])) {
                    // Two files claiming one id would make [[wikilinks]] point
                    // at whichever was read last. Same verdict as a duplicated
                    // URL: the second file loses, and says so.
                    throw ContentException::at(
                        $path,
                        sprintf('id « %s » déjà utilisé par %s.', $source->id, $ids[$source->id]),
                    );
                }

                $ids[$source->id] = $path;
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
                    layout: $source->layout,
                );
            } catch (ContentException $exception) {
                $this->failures[$path] = $exception->getMessage();
            }
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
        $autolinks = $this->autolinks;

        return new Document(
            slug: $slug,
            title: $frontmatter['title'] ?? $this->titleFromBody($raw) ?? $this->humanize(basename($path, '.md')),
            description: $frontmatter['description'] ?? null,
            frontmatter: $frontmatter,
            path: $path,
            // Prose pages get the same URL handling as the prose inside an
            // entity — the syntax should not mean two different things.
            render: static fn (): string => $markdown->parse($autolinks->expand($raw))->html,
            layout: $this->layout($path, $frontmatter),
        );
    }

    /**
     * The layout declared in frontmatter, the ordinary one by default.
     *
     * XML gets this check from its schema. Markdown has none, so it happens
     * here rather than at render time, where an unknown name would only show
     * up as a missing template.
     *
     * @param array<string, mixed> $frontmatter
     */
    private function layout(string $path, array $frontmatter): Layout
    {
        $declared = $frontmatter['layout'] ?? null;

        if ($declared === null) {
            return Layout::Document;
        }

        return Layout::tryFrom((string) $declared) ?? throw ContentException::at(
            $path,
            sprintf('layout « %s » inconnu. Attendus : %s.', $declared, implode(' | ', Layout::names())),
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
