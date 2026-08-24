<?php

declare(strict_types=1);

namespace App\Media;

use App\Content\Html;
use App\Content\Xml\XmlSource;

/**
 * The image of an entity: <visuel src="unlock.jpg" alt="…" credit="…" source="…"/>.
 *
 * A data field carries one value, and this element carries four. The one the
 * graph needs — the file name — is collected by the parser like any other
 * field; the three the renderer needs are read back from the element here, the
 * way App\Content\Intro reads them back from rendered HTML.
 *
 * That split is deliberate and temporary: the day <seance> forces Field to
 * accept structured values, this is the second caller waiting for it.
 */
final readonly class Visual
{
    /** Where each type's images live under media/. */
    private const array DIRECTORIES = [
        'oeuvre' => 'oeuvres',
        'personne' => 'personnes',
        'concept' => 'concepts',
    ];

    public function __construct(
        /** Name in the manifest, without extension: "oeuvres/unlock". */
        public string $asset,
        /** File name as written in the XML, resolved under media/<directory>/. */
        public string $file,
        /** Written by hand, always. Null until someone does. */
        public ?string $alt,
        /** Who holds the rights. */
        public ?string $credit,
        /** Where the file was taken from. */
        public ?string $source,
        /** What a block image says under itself. A lead image has none. */
        public ?string $caption = null,
    ) {}

    public static function of(XmlSource $entity): ?self
    {
        foreach ($entity->root->children as $child) {
            if ($child->localName === 'visuel') {
                return self::from($child, $entity->type);
            }
        }

        return null;
    }

    /**
     * Reads the attributes of a <visuel> or an <image>.
     *
     * Both carry the same thing — a file and what is known about it — so a
     * capture in the middle of the prose and the image at the top of a fiche
     * are stored, credited and rendered by the same code.
     */
    public static function from(\Dom\Element $element, string $type): ?self
    {
        $file = Html::attribute($element, 'src');

        if ($file === '') {
            return null;
        }

        return new self(
            asset: self::asset($type, $file),
            file: $file,
            alt: self::attribute($element, 'alt'),
            credit: self::attribute($element, 'credit'),
            source: self::attribute($element, 'source'),
            caption: self::attribute($element, 'caption'),
        );
    }

    /** The directory of a type, under media/ and under public/media/ alike. */
    public static function directory(string $type): string
    {
        return self::DIRECTORIES[$type] ?? $type;
    }

    /** "oeuvre" + "unlock.jpg" -> "oeuvres/unlock", the name MediaLibrary answers to. */
    public static function asset(string $type, string $file): string
    {
        return self::directory($type) . '/' . pathinfo($file, PATHINFO_FILENAME);
    }

    private static function attribute(\Dom\Element $element, string $name): ?string
    {
        $value = Html::attribute($element, $name);

        return $value !== '' ? $value : null;
    }
}
