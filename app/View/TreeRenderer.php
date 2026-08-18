<?php

declare(strict_types=1);

namespace App\View;

use App\Content\NavNode;
use App\Content\TocEntry;

/**
 * Rend les deux arbres de la page : la navigation et le sommaire.
 *
 * Pourquoi une classe plutôt qu'un composant de vue : Tempest développe les
 * composants à la compilation, donc un composant qui s'appelle lui-même boucle
 * à l'infini. Et le compilateur isole les blocs <?php ?> du gabarit, ce qui
 * exclut aussi une fonction récursive déclarée sur place.
 */
final readonly class TreeRenderer
{
    /** @param NavNode[] $nodes */
    public function nav(array $nodes): string
    {
        if ($nodes === []) {
            return '';
        }

        $items = '';

        foreach ($nodes as $node) {
            $label = $this->escape($node->title);

            $items .= '<li>';
            $items .= $node->slug !== ''
                ? '<a href="' . $this->escape($node->slug) . '">' . $label . '</a>'
                : '<span>' . $label . '</span>';
            $items .= $this->nav($node->children);
            $items .= '</li>';
        }

        return '<ul>' . $items . '</ul>';
    }

    /** @param TocEntry[] $entries */
    public function toc(array $entries): string
    {
        if ($entries === []) {
            return '';
        }

        $items = '';

        foreach ($entries as $entry) {
            $items .= '<li>';
            $items .= '<a href="#' . $this->escape($entry->id) . '">' . $this->escape($entry->label) . '</a>';
            $items .= $this->toc($entry->children);
            $items .= '</li>';
        }

        return '<ul>' . $items . '</ul>';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
