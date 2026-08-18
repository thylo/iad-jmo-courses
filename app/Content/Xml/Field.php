<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * A data field of an entity: <annee>2017</annee>, <par ref="studio-moniker"/>.
 *
 * Fields are not rendered where they are written. They feed the index and the
 * fiche shown at the top of the page. That is what separates them from
 * rendering elements (<section>, <markdown>, <video>), which emit HTML in place.
 */
final readonly class Field
{
    public function __construct(
        public string $name,
        /** Intitulé in the fiche at the top of the page. */
        public string $label,
        public bool $required = false,
        public bool $repeatable = false,
        /** The value is an entity id carried by the ref= attribute, not the text. */
        public bool $isReference = false,
        /**
         * A reference field that also accepts a bare name.
         *
         * <par ref="blast-theory"/> links to an entity; <par>Doublespeak Games</par>
         * is a creator we have not written a page for. Rendering follows the graph
         * rather than the syntax: a value that resolves becomes a link, one that
         * does not stays plain text.
         */
        public bool $allowsText = false,
        /** The value is an external URL. */
        public bool $isUrl = false,
        /** False when the field is rendered elsewhere, like the title and the summary. */
        public bool $inFiche = true,
        /** @var string[]|null allowed values, null when free */
        public ?array $values = null,
    ) {}
}
