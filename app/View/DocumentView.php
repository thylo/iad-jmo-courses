<?php

declare(strict_types=1);

namespace App\View;

use App\Content\Document;
use App\Content\Intro;
use App\Content\Layout;
use App\Content\TableOfContents;
use Tempest\View\IsView;
use Tempest\View\View;

use function Tempest\root_path;

/**
 * What a content page needs to know in order to be rendered.
 *
 * The chrome around the page — the site sections in the masthead — is not here:
 * it is the same on every page, so NavigationViewProcessor supplies it, and
 * <x-masthead> reads it as view data.
 *
 * Which template renders the document is not decided here either: the page
 * declares its layout — layout="home" — and this only maps it to a file.
 * The templates share <x-document> and add what is theirs through its slots,
 * so no template has to test which page it is rendering.
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
        $this->path = root_path(match ($document->layout) {
            Layout::Document => 'views/layouts/document.view.php',
            Layout::Home => 'views/layouts/home.view.php',
        });
        $this->intro = Intro::split($document->html);
        $this->toc = TableOfContents::fromHtml($document->html);

        // Read by <x-base> through the view data, so the frame needs no props.
        $this->data(
            title: $document->title,
            description: $document->description,
        );
    }

    public function hasToc(): bool
    {
        return count($this->toc->entries) >= self::TOC_THRESHOLD;
    }
}
