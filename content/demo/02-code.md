---
title: Code
description: Coloration syntaxique côté serveur, sans JavaScript.
---

# Code

La coloration est faite par `tempest/highlight`, au moment du rendu, côté serveur.
Aucun JavaScript n'est envoyé au navigateur pour ça.

## PHP

```php
final readonly class Document
{
    public function __construct(
        public string $slug,
        public string $title,
    ) {}
}
```

## JavaScript

```js
// Le contenu du cours contient surtout du CSS, du JS et du HTML.
const cookies = 0;

document.querySelector('.cookie').addEventListener('click', () => {
    cookies += 1;
});
```

## CSS

```css
:root {
    --iad-gap-m: 1rem;
}
```

Sans feuille de style, le balisage est bien là mais les couleurs ne s'affichent pas :
`tempest/highlight` produit des `<span class="hl-keyword">` qui attendent leur thème CSS.
C'est attendu à ce stade.
