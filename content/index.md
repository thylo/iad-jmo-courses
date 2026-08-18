---
title: Narration Interactive
description: Le web comme médium narratif — site de cours, IAD Louvain-la-Neuve.
---

# Narration Interactive

Ce site tourne sur [Tempest](https://tempestphp.com). Le contenu est sur le
disque, dans `content/` : du Markdown pour les pages de prose, du [XML](/demo/05-xml)
pour les entités typées — œuvres, personnes, studios.

Cette version est un socle : elle sert à vérifier que la chaîne complète fonctionne
avant d'y verser les cours. Le contenu Astro existant attend dans `astro/`, intact.

## Ce qui marche déjà

- Le contenu est lu à la requête, pas construit à l'avance.
- La navigation se déduit de l'arborescence des fichiers.
- Le frontmatter alimente le titre et la description de la page.
- Les entités XML forment un graphe : les liens `[[…]]` visent un identifiant,
  les rétroliens et les index se calculent tout seuls.

## Ce qui n'est pas là

Pas de CSS, volontairement. Pas de recherche. Pas de composants interactifs.
Ces décisions viendront après, une fois le socle jugé sur pièces.

Voir la [démonstration](/demo).
