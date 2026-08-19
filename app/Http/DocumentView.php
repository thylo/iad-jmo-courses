<?php

declare(strict_types=1);

namespace App\Http;

use App\Content\ContentRepository;
use App\Content\Document;
use App\Content\Intro;
use App\Content\TableOfContents;
use App\View\TreeRenderer;
use Tempest\View\IsView;
use Tempest\View\View;

/**
 * Ce qu'une page de contenu a besoin de savoir pour s'afficher.
 */
final class DocumentView implements View
{
    use IsView;

    public readonly Intro $intro;

    private TableOfContents $toc;

    private TreeRenderer $renderer;

    public function __construct(
        public readonly Document $document,
        private readonly ContentRepository $content,
    ) {
        $this->path = __DIR__ . '/../View/document.view.php';
        $this->intro = Intro::split($document->html);
        $this->toc = TableOfContents::fromHtml($document->html);
        $this->renderer = new TreeRenderer();

        $this->data = [
            'title' => $document->title,
            'description' => $document->description,
        ];
    }

    /**
     * The homepage carries two marks no other page does: the drawn rule and the
     * portrait. They only work as an introduction to the person writing, which
     * happens once.
     */
    public function isHome(): bool
    {
        return $this->document->slug === '/';
    }

    public function sectionsHtml(): string
    {
        return $this->renderer->sections($this->content->tree(), $this->document->slug);
    }

    /**
     * A summary only earns its place on a page long enough to get lost in.
     *
     * Under this many sections the headings are visible by scrolling, and an
     * index of three links above the text is one more thing to read before
     * reading. The long pages here are the indexes — /oeuvres, /concepts —
     * and those are exactly the ones worth jumping around in.
     */
    private const int TOC_THRESHOLD = 4;

    public function hasToc(): bool
    {
        return count($this->toc->entries) >= self::TOC_THRESHOLD;
    }

    public function tocHtml(): string
    {
        return $this->renderer->toc($this->toc->entries);
    }
}
