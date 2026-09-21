<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * What an entity type accepts: its attributes and its data fields.
 *
 * Every type gets <titre>, <resume>, <suptitle>, <subtitle> and <parent> for
 * free, so no schema has to redeclare them and the generic required/unknown
 * checks in XmlParser cover them like any other field. None of the five renders
 * in the fiche: the first four are the page header, and the last is the trail
 * above it.
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
        /** What a collection of these is called, for the count above an index. */
        public string $plural,
        array $fields,
        public array $attributes = [],
    ) {
        $this->fields = array_column([
            new Field(name: 'titre', label: 'Titre', required: true, inFiche: false),
            new Field(name: 'resume', label: 'Résumé', inFiche: false),
            // The two other lines of the heading. They belong to the title and
            // not to the prose under it: one names the series the page is part
            // of — "Séance 1" — the other carries the title's second half.
            //
            // Neither reaches the <title> tag, the index or the sort, which
            // stay on <titre> alone. A link to this page has room for one
            // string, and a heading that reads on its own is the writer's job
            // rather than the template's.
            new Field(name: 'suptitle', label: 'Sur-titre', inFiche: false),
            new Field(name: 'subtitle', label: 'Sous-titre', inFiche: false),
            // Where the page is filed, when the graph gets it wrong or has
            // nothing to say — see App\Content\TrailResolver. A reference like
            // any other, so a <parent> pointing nowhere is reported by
            // content:check instead of quietly shortening the trail.
            new Field(name: 'parent', label: 'Dans', isReference: true, inFiche: false),
            ...$fields,
        ], null, 'name');
    }

    public function field(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }
}
