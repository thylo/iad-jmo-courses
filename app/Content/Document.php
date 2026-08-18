<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Une page de contenu : un fichier markdown parsé.
 */
final readonly class Document
{
    public function __construct(
        /** Slug canonique, sans slash final. La racine vaut '/'. */
        public string $slug,
        public string $title,
        public ?string $description,
        /** HTML rendu depuis le markdown. */
        public string $html,
        /** Frontmatter brut, pour les champs qu'on n'a pas typés. */
        public array $frontmatter,
        /** Chemin absolu du fichier source, utile pour les messages d'erreur. */
        public string $path,
    ) {}
}
