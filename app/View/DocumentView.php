<?php

declare(strict_types=1);

namespace App\View;

use App\Content\Document;
use App\Content\Intro;
use App\Content\TableOfContents;
use Tempest\View\IsView;
use Tempest\View\View;

use function Tempest\root_path;

/**
 * Ce qu'une page de contenu a besoin de savoir pour s'afficher.
 *
 * The chrome around the page — the site sections in the masthead — is not here:
 * it is the same on every page, so NavigationViewProcessor supplies it, and
 * <x-masthead> reads it as view data.
 */
final class DocumentView implements View, HasNavigation
{
    use IsView;

    /**
     * A summary only earns its place on a page long enough to get lost in.
     *
     * Under this many sections the headings are visible by scrolling, and an
     * index of three links above the text is one more thing to read before
     * reading. The long pages here are the indexes — /oeuvres, /concepts —
     * and those are exactly the ones worth jumping around in.
     */
    private const int TOC_THRESHOLD = 4;

    public string $currentSlug {
        get => $this->document->slug;
    }

    public readonly Intro $intro;

    public readonly TableOfContents $toc;

    public function __construct(
        public readonly Document $document,
    ) {
        $this->path = root_path('views/document.view.php');
        $this->intro = Intro::split($document->html);
        $this->toc = TableOfContents::fromHtml($document->html);

        // Read by <x-base> through the view data, so the frame needs no props.
        $this->data(
            title: $document->title,
            description: $document->description,
        );
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

    public function hasToc(): bool
    {
        return count($this->toc->entries) >= self::TOC_THRESHOLD;
    }
}
