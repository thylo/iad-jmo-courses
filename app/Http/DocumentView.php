<?php

declare(strict_types=1);

namespace App\Http;

use App\Content\ContentRepository;
use App\Content\Document;
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

    private TableOfContents $toc;

    private TreeRenderer $renderer;

    public function __construct(
        public readonly Document $document,
        private readonly ContentRepository $content,
    ) {
        $this->path = __DIR__ . '/../View/document.view.php';
        $this->toc = TableOfContents::fromHtml($document->html);
        $this->renderer = new TreeRenderer();

        $this->data = [
            'title' => $document->title,
            'description' => $document->description,
        ];
    }

    public function navHtml(): string
    {
        return $this->renderer->nav($this->content->tree(), $this->document->slug);
    }

    public function hasToc(): bool
    {
        return ! $this->toc->isEmpty();
    }

    public function tocHtml(): string
    {
        return $this->renderer->toc($this->toc->entries);
    }
}
