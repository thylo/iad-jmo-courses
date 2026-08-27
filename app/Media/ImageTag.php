<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Html;
use App\Content\Xml\ContentIndex;
use App\Content\Xml\InlineProse;
use App\Content\Xml\XmlSource;
use App\View\Component;

/**
 * Renders an image, or nothing at all.
 *
 * Every path out of here returns a string, never an exception. An image is an
 * addition to a page, not the page: a fiche whose file was never built still
 * says everything it said before, and an index of 83 works cannot go down
 * because one of them is missing a thumbnail. What is missing is counted by
 * content:check instead, which is where a content problem belongs.
 *
 * That is the opposite of the rule the text follows — a fiche without its title
 * is not a fiche — and the difference is deliberate: the sanction has to stay
 * smaller than the fault.
 *
 * What an image looks like is in views/x-image and views/x-figure. This decides
 * which file, how wide, and whether it waits.
 */
final readonly class ImageTag
{
    /**
     * A figure spans the reading — the text column plus the open field — so its
     * width is the window less the gutters and the margin, which is about 20rem
     * across the range the layout is used at. Not exact, and it does not need
     * to be: sizes is a hint, and being a little generous costs less than
     * letting the browser assume 100vw.
     */
    private const string SIZES_FIGURE = '(min-width: 48em) calc(100vw - 20rem), calc(100vw - 3.5rem)';

    private const string SIZES_THUMBNAIL = '(min-width: 48em) 11rem, 40vw';

    /** A bleed spans the paper, edge to edge. */
    private const string SIZES_BLEED = '100vw';

    /**
     * The field is what the window gives beyond everything of fixed width, so a
     * figure set there is the window less the two gutters, the margin, the
     * measure and the step between — about 62rem across the range. Same
     * approximation as SIZES_FIGURE, and the same reason it is allowed.
     *
     * Past 85em the figure hands back a margin the width of the sidebar, so it
     * is another 12.25rem narrower. figure.css owns that threshold and names it;
     * this is the same threshold said where the browser can read it before the
     * layout exists.
     */
    private const string SIZES_FIELD = '(min-width: 85em) calc(100vw - 74rem), (min-width: 48em) calc(100vw - 62rem), calc(100vw - 3.5rem)';

    /**
     * The opening plate is the only image that changes column with the window:
     * the field beside the summary where there is a field worth having, the
     * reading below it where there is not. figure.css owns that switch and
     * names the threshold; this is the same threshold said in the one place
     * the browser reads before the layout exists.
     */
    private const string SIZES_OPENING = '(min-width: 72em) calc(100vw - 62rem), (min-width: 48em) calc(100vw - 20rem), calc(100vw - 3.5rem)';

    public function __construct(
        private MediaLibrary $library,
        private InlineProse $prose,
        private Component $components,
    ) {}

    /** The image at the top of a fiche. Visible on load, so never deferred. */
    public function lead(XmlSource $entity, ContentIndex $index): string
    {
        $visual = Visual::of($entity);

        if ($visual === null) {
            return '';
        }

        $image = $this->tag($visual, width: 1280, sizes: self::SIZES_FIGURE, lazy: false);

        if ($image === '') {
            return '';
        }

        return $this->figure($visual, $image, 'c-figure c-figure--lead', $index);
    }

    /** One thumbnail among many: always deferred, always the narrow variant. */
    public function thumbnail(XmlSource $entity): string
    {
        $visual = Visual::of($entity);

        if ($visual === null) {
            return '';
        }

        return $this->tag($visual, width: 320, sizes: self::SIZES_THUMBNAIL, lazy: true);
    }

    /**
     * A capture in the middle of the prose: <image src="…" caption="…"/>.
     *
     * Same markup as the lead image, with the caption the block carries and the
     * credit under it. Lazy: it is below the fold by definition.
     *
     * width="…" is the one thing the writer decides, and it is an editorial
     * decision rather than a property of the file: it says what the image does
     * to the reading, and the grid already has a column for each answer.
     *
     *   opening  the field, level with the summary — the reading has not begun
     *   field    the open column, beside the text — the reading continues
     *   reading  the text column plus the field — the reading stops (default)
     *   full     the paper, edge to edge — the reading stops before it began
     *
     * The rule that follows: at most one image per page takes the paper, and it
     * is the first one. Every image after it belongs in the field, where a
     * figure annotates instead of interrupting — which is the whole reason a
     * page can carry several without turning into a slideshow.
     *
     * `opening` is the same column as `field` and a different row: the plate
     * that opens the page rides beside the title and the summary rather than
     * beside the first paragraph. A figure in the field aligns with the top of
     * the block it annotates, so it needs a block as tall as itself standing
     * beside it — and the block at the top of a page is one paragraph long. The
     * opening is the one place on the sheet with room for a plate and nothing
     * to interrupt, which is exactly what a fiche already does with its facts.
     * App\Content\Intro lifts it out on that class.
     */
    public function block(\Dom\Element $element, string $type, ContentIndex $index): string
    {
        $visual = Visual::from($element, $type);

        if ($visual === null) {
            return '';
        }

        [$class, $sizes, $fallback] = match (Html::attribute($element, 'width')) {
            'full' => ['c-figure c-bleed', self::SIZES_BLEED, 1280],
            'opening' => ['c-figure c-figure--opening', self::SIZES_OPENING, 640],
            'field' => ['c-figure c-figure--field', self::SIZES_FIELD, 640],
            default => ['c-figure', self::SIZES_FIGURE, 1280],
        };

        $image = $this->tag($visual, width: $fallback, sizes: $sizes, lazy: true);

        if ($image === '') {
            return '';
        }

        return $this->figure($visual, $image, $class, $index);
    }

    private function tag(Visual $visual, int $width, string $sizes, bool $lazy): string
    {
        $asset = $this->library->find($visual->asset);

        // Named by a fiche but never built, or built and since removed. Silent
        // here, counted by content:check.
        if ($asset === null) {
            return '';
        }

        return $this->components->render(
            'x-image',
            src: $asset->src($width),
            srcset: $asset->srcset(),
            sizes: $sizes,
            width: (string) $asset->width,
            height: (string) $asset->height,
            // An alt nobody has written yet is an empty one: a screen reader
            // skipping an image beats it reading out "unlock.jpg".
            alt: $visual->alt ?? '',
            lazy: $lazy,
        );
    }

    /**
     * The image and what is owed to whoever made it.
     *
     * The credit is part of the component rather than something a writer
     * remembers to add — these are other people's images on a public course
     * site, and that is the only way it stays systematic. A visual that names a
     * source but nobody to credit still says "Source", linked.
     *
     * The caption goes through the prose pipeline: it is a sentence, and a
     * sentence that names a work should be able to link to it.
     *
     * A moving image says so on the figure, because the whole figure is what a
     * reader who asked for no motion does without — hiding the image alone
     * would leave a caption crediting something nobody can see. Said here
     * rather than at each call site: a figure that moves is a figure that
     * moves, whether it opens a fiche or annotates a paragraph.
     */
    private function figure(Visual $visual, string $image, string $class, ContentIndex $index): string
    {
        if ($this->library->find($visual->asset)?->animated === true) {
            $class .= ' c-figure--motion';
        }

        return $this->components->render(
            'x-figure',
            class: $class,
            image: $image,
            caption: $visual->caption !== null ? $this->prose->toHtml($visual->caption, $index) : null,
            credit: $visual->credit ?? ($visual->source !== null ? 'Source' : null),
            creditUrl: $visual->source,
        );
    }
}
