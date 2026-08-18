---
title: Liens et sommaire
description: Liens internes et table des matières générée depuis les titres.
---

# Liens et sommaire

Cette page a assez de titres pour produire un sommaire à deux niveaux.

## Liens internes

Le contenu existant compte 293 liens absolus de la forme `/rtmf1m/...`. Garder le
schéma d'URL de Starlight les maintient tous valides, sans réécriture.

### Vers une page

Un lien vers la [page frontmatter](/demo/01-frontmatter).

### Vers une section

Un lien vers l'[index de la démonstration](/demo), et un vers l'[accueil](/).

## Sommaire

Les titres `h2` et `h3` sont extraits du HTML rendu. `tempest/markdown` pose les
identifiants lui-même, donc les ancres fonctionnent sans post-traitement.

### Deux niveaux

Les `h3` se rangent sous le `h2` qui les précède.

### Un dernier titre

De quoi vérifier que l'imbrication tient sur plusieurs entrées.

## Liens externes

Un lien vers [la documentation de Tempest](https://tempestphp.com), qui ne doit pas
être signalé comme mort par `content:check`.
