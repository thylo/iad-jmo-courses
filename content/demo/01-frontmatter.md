---
title: Frontmatter
description: Cette phrase doit apparaître dans la balise meta description de la page.
tags: ["socle", "démo"]
---

# Frontmatter

Le bloc YAML en tête de fichier alimente la page. `title` devient le `<title>` et le
`<h1>` de la navigation ; `description` devient la `<meta name="description">`.

Les autres clés sont conservées telles quelles et restent accessibles. Ici, `tags`
contient deux entrées. Les 124 fiches d'œuvres du site actuel ont un frontmatter
autrement plus riche — `creators`, `year`, `relatedWorks` — et il passera sans
transformation, `symfony/yaml` faisant le travail.

## Repli

Si `title` manque, le titre est repris du premier `<h1>` du document. Si le document
n'en a pas, le nom de fichier sert de dernier recours, débarrassé de son préfixe
numérique.
