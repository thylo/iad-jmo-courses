# CLAUDE.md - Résumé de la documentation du projet

Document synthétique pour comprendre le contexte, les principes et les guidelines du projet IAD Courses.

---

## Avant toute décision de forme

**Lire `docs/direction-artistique.md` en entier avant d'écrire la moindre ligne de CSS ou de
markup structurant.** Sans exception, et sans se fier au souvenir d'une lecture précédente.

Ça vaut pour : une page ou un gabarit, un composant, une grille, une feuille de style, une
typographie, une palette, un espacement, une refonte, un ajustement visuel demandé en une
phrase. Si le résultat change ce que le lecteur voit, la DA s'applique.

Ce qu'elle impose en pratique :

- Le protocole du §12 avant de coder : nommer le sujet, trouver la contrainte, poser le
  système, nommer le geste, s'autocritiquer. Pas de maquette produite d'abord et justifiée
  après.
- Les cinq critères du §2 comme grille de jugement : honnêteté, durabilité, économie, patine,
  décision. Une proposition correcte mais sans décision est un échec.
- La liste des refus du §11 comme filtre avant de montrer quoi que ce soit.
- Toute valeur vient des tokens du système, jamais d'un nombre choisi sur le moment.

La DA ne se cite pas dans la réponse et ne se résume pas au lecteur : elle se digère, on décide,
on livre. Une page qui affiche sa méthodologie a échoué.

---

## Vue d'ensemble du projet

**Objectif global :** Enseigner la narration interactive web à des étudiants en cinéma et audiovisuel de l'IAD Louvain-la-Neuve.

**Stack technique :**
- Styling : CSS pur (ITCSS, BEM) - pas Tailwind
- Interactivité : Alpine.js, Svelte
- Commentaires de code : Anglais

---