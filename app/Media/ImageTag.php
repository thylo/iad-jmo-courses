<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Html;
use App\Content\Xml\XmlSource;

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
 */
final readonly class ImageTag
{
    /**
     * The column is 62ch (~530px) and the full grid ~780px, so the browser is
     * told the displayed width rather than left to assume 100vw.
     */
    private const string SIZES_LEAD = '(min-width: 48em) 33rem, calc(100vw - 3.5rem)';

    private const string SIZES_THUMBNAIL = '(min-width: 48em) 11rem, 40vw';

    public function __construct(
        private MediaLibrary $library,
    ) {}

    /** The image at the top of a fiche. Visible on load, so never deferred. */
    public function lead(XmlSource $entity): string
    {
        $visual = Visual::of($entity);

        if ($visual === null) {
            return '';
        }

        $image = $this->tag($visual, width: 640, sizes: self::SIZES_LEAD, lazy: false);

        if ($image === '') {
            return '';
        }

        return sprintf(
            '<figure class="c-figure c-figure--lead">%s%s</figure>',
            $image,
            $this->caption($visual),
        );
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
     * A capture in the middle of the prose: <image src="…" legende="…"/>.
     *
     * Same markup as the lead image, with the caption the block carries and the
     * credit under it. Lazy: it is below the fold by definition.
     */
    public function block(\Dom\Element $element, string $type): string
    {
        $visual = Visual::from($element, $type);

        if ($visual === null) {
            return '';
        }

        $image = $this->tag($visual, width: 640, sizes: self::SIZES_LEAD, lazy: true);

        if ($image === '') {
            return '';
        }

        return sprintf('<figure class="c-figure">%s%s</figure>', $image, $this->caption($visual));
    }

    private function tag(Visual $visual, int $width, string $sizes, bool $lazy): string
    {
        $asset = $this->library->find($visual->asset);

        // Named by a fiche but never built, or built and since removed. Silent
        // here, counted by content:check.
        if ($asset === null) {
            return '';
        }

        return sprintf(
            '<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" decoding="async"%s>',
            Html::escape($asset->src($width)),
            Html::escape($asset->srcset()),
            Html::escape($sizes),
            $asset->width,
            $asset->height,
            // An alt nobody has written yet is an empty one: a screen reader
            // skipping an image beats it reading out "unlock.jpg".
            Html::escape($visual->alt ?? ''),
            $lazy ? ' loading="lazy"' : '',
        );
    }

    /**
     * What the image says under itself: its caption, then who owns it.
     *
     * One <figcaption> holds both — a <figure> only ever gets one, and the
     * credit is a detail of the caption rather than a second statement.
     *
     * These are other people's images on a public course site, so the credit is
     * part of the component rather than something a writer remembers to add.
     * That is the only way it stays systematic.
     */
    private function caption(Visual $visual): string
    {
        $credit = $this->credit($visual);

        if ($visual->caption === null && $credit === '') {
            return '';
        }

        if ($visual->caption === null) {
            return sprintf('<figcaption class="c-figure__credit">%s</figcaption>', $credit);
        }

        return sprintf(
            '<figcaption class="c-figure__caption">%s%s</figcaption>',
            Html::escape($visual->caption),
            $credit !== '' ? sprintf('<span class="c-figure__credit">%s</span>', $credit) : '',
        );
    }

    /** Who holds the rights, linked to where the file came from. */
    private function credit(Visual $visual): string
    {
        if ($visual->credit === null && $visual->source === null) {
            return '';
        }

        $name = Html::escape($visual->credit ?? 'Source');

        return $visual->source !== null
            ? sprintf('<a href="%s" rel="noreferrer">%s</a>', Html::escape($visual->source), $name)
            : $name;
    }
}
