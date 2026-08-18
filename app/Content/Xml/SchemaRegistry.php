<?php

declare(strict_types=1);

namespace App\Content\Xml;

/**
 * The known entity types.
 *
 * Adding a type happens here and nowhere else. "concept" and "seance" will come
 * once "oeuvre" and "personne" have been judged on real content.
 */
final readonly class SchemaRegistry
{
    /** @var array<string, Schema> */
    private array $schemas;

    public function __construct()
    {
        $work = new Schema(
            type: 'oeuvre',
            fields: [
                new Field(name: 'annee', label: 'Année'),
                new Field(name: 'url', label: 'En ligne', isUrl: true),
                new Field(name: 'par', label: 'Par', repeatable: true, isReference: true),
                new Field(name: 'concept', label: 'Concepts', repeatable: true, isReference: true),
                new Field(name: 'voir', label: 'Voir aussi', repeatable: true, isReference: true),
            ],
            attributes: ['statut' => ['en-ligne', 'hors-ligne', 'archive']],
        );

        $person = new Schema(
            type: 'personne',
            fields: [
                new Field(name: 'lieu', label: 'Lieu'),
                new Field(name: 'depuis', label: 'Depuis'),
                new Field(name: 'url', label: 'Site', isUrl: true),
            ],
            attributes: ['genre' => ['personne', 'studio', 'collectif']],
        );

        $this->schemas = [$work->type => $work, $person->type => $person];
    }

    public function get(string $type): ?Schema
    {
        return $this->schemas[$type] ?? null;
    }

    /** @return string[] */
    public function types(): array
    {
        return array_keys($this->schemas);
    }
}
