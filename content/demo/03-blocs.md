---
title: Blocs
description: Les encarts tip, note et caution, équivalents des asides Starlight.
---

# Blocs

Le contenu actuel utilise 50 encarts Starlight : 36 `:::tip`, 9 `:::note`, 5 `:::caution`.
`tempest/markdown` les gère nativement, avec la même syntaxe.

:::tip
Une petite chose bien pensée, bien faite, intéressante, vaut mille fois mieux qu'une
grosse chose qui ne fonctionne pas.
:::

:::note
Chaque bloc devient un `<div>` portant sa classe — `tip`, `note`, `caution`. La
distinction visuelle viendra avec le CSS.
:::

:::caution
Sans feuille de style, les trois se ressemblent. Vérifiez la classe dans le HTML,
pas l'apparence.
:::

La syntaxe est identique à celle de Starlight, donc les 50 encarts existants
migreront sans retouche.
