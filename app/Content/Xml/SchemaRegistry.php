<?php

declare(strict_types=1);

namespace App\Content\Xml;

use App\Content\Layout;

/**
 * The known entity types.
 *
 * Adding a type happens here and nowhere else.
 */
final readonly class SchemaRegistry
{
    /** Cleaned up from the Astro corpus, which had drifted into synonyms. */
    private const array CATEGORIES = [
        'Fiction', 'Documentaire', 'Interface', 'Outil', 'Jeu',
        'Performance', 'Urbain', 'Muséale', 'Installation',
        'Expérimentation',
    ];

    /** @var array<string, Schema> */
    private array $schemas;

    public function __construct()
    {
        $schemas = [
            new Schema(
                type: 'oeuvre',
                plural: 'œuvres',
                fields: [
                    new Field(name: 'annee', label: 'Année'),
                    // The image of the work: not shown where it is written, like
                    // the title, because it serves the header of the fiche and
                    // the thumbnail in every index.
                    new Field(name: 'visuel', label: 'Visuel', inFiche: false, attribute: 'src'),
                    new Field(name: 'url', label: 'En ligne', isUrl: true),
                    new Field(name: 'par', label: 'Par', repeatable: true, isReference: true, allowsText: true),
                    new Field(name: 'categorie', label: 'Catégories', repeatable: true, values: self::CATEGORIES),
                    new Field(name: 'concept', label: 'Concepts', repeatable: true, isReference: true),
                    new Field(name: 'voir', label: 'Voir aussi', repeatable: true, isReference: true),
                ],
                attributes: [
                    'statut' => ['en-ligne', 'hors-ligne', 'archive'],
                    // <visuel> says which image; visuel="aucun" says there will
                    // not be one, and that it was decided rather than forgotten.
                    'visuel' => ['aucun'],
                ],
            ),
            new Schema(
                type: 'personne',
                plural: 'personnes',
                fields: [
                    new Field(name: 'lieu', label: 'Lieu'),
                    new Field(name: 'depuis', label: 'Depuis'),
                    new Field(name: 'url', label: 'Site', isUrl: true),
                ],
                attributes: ['genre' => ['personne', 'studio', 'collectif', 'organisation']],
            ),
            new Schema(
                type: 'concept',
                plural: 'concepts',
                fields: [
                    new Field(
                        name: 'genre',
                        label: 'Famille',
                        required: true,
                        values: ['structure', 'forme', 'role', 'interface', 'choix', 'ressource'],
                    ),
                    new Field(name: 'exemple', label: 'Illustré par', repeatable: true, isReference: true),
                    new Field(name: 'voir', label: 'Voir aussi', repeatable: true, isReference: true),
                ],
            ),
            // A page that is not an entity: a listing, an introduction. It has an
            // id so it can be linked to, and blocks like any other page — which is
            // what lets an index be derived instead of maintained by hand.
            //
            // It is also the only type that picks its layout: an entity is
            // always set as an ordinary document.
            new Schema(
                type: 'page',
                plural: 'pages',
                fields: [],
                attributes: ['layout' => Layout::names()],
            ),
        ];

        $this->schemas = array_column($schemas, null, 'type');
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
