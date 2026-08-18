<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * What an entity type accepts: its attributes and its data fields.
 *
 * Every type gets <titre> and <resume> for free, so no schema has to redeclare
 * them and the generic required/unknown checks in XmlParser cover them like any
 * other field. Both render in the page header rather than in the fiche.
 *
 * Rendering elements (<section>, <markdown>, …) are not listed here: they are
 * shared by every type and known to the ElementRegistry instead.
 */
final readonly class Schema
{
    /** @var array<string, Field> */
    public array $fields;

    /**
     * @param Field[] $fields the type-specific ones
     * @param array<string, string[]|null> $attributes name => allowed values, null when free
     */
    public function __construct(
        public string $type,
        array $fields,
        public array $attributes = [],
    ) {
        $this->fields = array_column([
            new Field(name: 'titre', label: 'Titre', required: true, inFiche: false),
            new Field(name: 'resume', label: 'Résumé', inFiche: false),
            ...$fields,
        ], null, 'name');
    }

    public function field(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }
}
