---
title: Vues Tempest
description: Où poser les fichiers de vue, comment les nommer, et pourquoi.
---

# Vues Tempest

Tempest n'impose aucune arborescence. Il scanne, il déduit. C'est confortable,
mais ça veut dire que les conventions sont à nous : rien ne nous corrigera.

Ce document rassemble ce que le framework fait réellement (vérifié dans
`vendor/tempest/framework/packages/view/src`) et ce qu'on en tire comme règles.

## Ce que Tempest impose vraiment

Une seule chose : **un fichier n'existe que s'il est dans un emplacement de
discovery**, c'est-à-dire un dossier déclaré en PSR-4 dans `composer.json`.

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Views\\": "views/"
    }
}
```

`Views\` n'a aucune classe derrière. L'entrée sert uniquement à dire à Tempest
de scanner `views/`. Après toute modification : `composer dump-autoload`.

Le reste — dossiers, sous-dossiers, découpage par domaine ou par couche — lui est
indifférent. La doc officielle montre les deux structures comme équivalentes :

```txt
src/                                 src/
├── Authors/                         ├── Controllers/
│   ├── Author.php                   ├── Models/
│   ├── AuthorController.php         ├── Services/
│   └── authors.view.php             └── Views/
├── Books/                               ├── authors.view.php
│   ├── Book.php                         ├── books.view.php
│   └── books.view.php                   └── x-base.view.php
└── Support/
    └── x-base.view.php
```

Tranches verticales à gauche, couches à droite. Les deux marchent. Choisir, puis
s'y tenir : le coût est dans le mélange, pas dans l'option.

## Les règles dures

Quatre comportements ne se négocient pas. Les connaître évite les surprises.

**1. Le nom d'un composant, c'est son nom de fichier.** Un fichier `x-*.view.php`
est découvert comme composant, et son nom est le basename moins `.view.php`. Le
dossier n'entre pas dans le nom. `views/x-toc.view.php` et
`app/Ui/Nav/x-toc.view.php` produisent tous les deux `<x-toc />`.

**2. L'espace de noms des composants est plat et global.** Il inclut le vendor.
Deux composants projet de même nom lèvent `ViewComponentWasAlreadyRegistered`.
Un composant vendor n'écrase jamais un composant projet — c'est le mécanisme
d'override : `views/x-base.view.php` remplace le `x-base` du framework, sans
configuration.

**3. Les vues ordinaires sont résolues par chemin, pas découvertes.** Un fichier
`document.view.php` n'est enregistré nulle part. Au rendu, Tempest essaie dans
l'ordre : le chemin tel quel, puis relatif au **dossier du fichier appelant**,
puis relatif à chaque emplacement de discovery.

```php
return view('document.view.php');
// app/Http/document.view.php  →  app/document.view.php  →  views/document.view.php
```

La résolution relative à l'appelant est pratique en tranches verticales et
piégeuse autrement : deux `show.view.php` dans deux dossiers ne se voient pas,
jusqu'au jour où l'un disparaît et où l'autre est trouvé en second choix.

**4. Les entrypoints Vite suivent la même règle.** `*.entrypoint.{ts,js,css}`
n'importe où dans un emplacement de discovery. `app/main.entrypoint.css` est
découvert ; `assets/main.entrypoint.css` ne le serait pas.

## Conventions de nommage

- `x-<nom>.view.php` — composant réutilisable, un par fichier, le nom du fichier
  **est** la balise.
- `<nom>.view.php` — page rendue par un contrôleur ou un objet de vue.
- Préfixer les composants susceptibles de collision. `x-card` est un pari sur
  l'avenir ; `x-oeuvre-card` n'en est pas un. Le vendor occupe déjà `x-base`,
  `x-form`, `x-input`, `x-submit`, `x-icon`, `x-markdown`, `x-vite-tags`,
  `x-csrf-token`.
- Le nom dit le rôle, pas la position. `x-masthead`, pas `x-header-top`.

## Objets de vue plutôt que tableaux de données

`view('page.view.php', foo: $bar)` marche, mais la vue devient un sac de
variables sans type. Un objet de vue tient la donnée et la logique d'affichage :

```php
final class DocumentView implements View
{
    use IsView;

    public function __construct(public readonly Document $document)
    {
        $this->path = root_path('views/document.view.php');
    }

    public function hasToc(): bool { /* … */ }
}
```

Et dans le template, une annotation suffit à ce que l'IDE suive :

```php
<?php /** @var \App\View\DocumentView $this */ ?>
```

Trois conséquences pratiques :

- Le contrôleur redevient lisible : il trouve, il retourne. Pas de préparation
  d'affichage.
- La logique conditionnelle (`hasToc()`, `isHome()`) est dans une classe
  testable, pas dans un `:if` de trois lignes.
- Le chemin passe par `root_path()`, donc explicite. Pas de résolution
  implicite, pas d'ambiguïté quand le fichier bouge.

Règle de partage : **classes dans `app/View/`, templates dans `views/`.** Un
objet de vue est du PHP, pas du HTML.

## Données communes : les view processors

Ce dont toutes les pages ont besoin — les sections du site, l'utilisateur
connecté, un compteur — ne doit pas transiter par chaque action de contrôleur.
Un `ViewProcessor` est découvert par son interface, où qu'il soit, et voit passer
toutes les vues :

```php
final readonly class NavigationViewProcessor implements ViewProcessor
{
    public function process(View $view): View
    {
        if (! $view instanceof DocumentView) {
            return $view;
        }

        return $view->data(sections: $this->content->tree());
    }
}
```

Le `instanceof` n'est pas une précaution : c'est le filtre. Sans lui, le
processor s'applique aussi aux pages d'erreur et aux vues du framework.

## Données et composants : ce qui traverse, ce qui ne traverse pas

Un composant se comporte comme une closure : il ne voit que ce qu'on lui passe.
Une exception, et elle est structurante : **les données de la vue sont visibles
dans les composants**, comme variables locales.

```php
$view->data(sections: $tree);   // dans <x-masthead> : $sections
<x-toc :entries="$this->toc" /> // prop explicite
```

Le choix entre les deux est un choix de conception :

- **Donnée de vue** pour ce qui est ambiant et identique partout — le titre, la
  navigation. Le composant intermédiaire n'a pas à porter une prop qu'il
  n'utilise pas.
- **Prop** pour ce qui est propre à cet appel-là. Le composant reste lisible
  seul : sa signature est dans son en-tête.

Documenter les deux en tête de fichier, avec des `@var`. C'est la seule
signature qu'un template possède.

## Slots

Un slot par défaut (`<x-slot />`), des slots nommés pour le reste :

```html
<x-slot name="styles" />
```

Utile surtout pour le `<head>` : une page qui a besoin d'une feuille de style
propre l'injecte sans que le layout ait à connaître tous les cas.

## Production

Deux caches, deux commandes.

```env
VIEW_CACHE=true
DISCOVERY_CACHE=full
```

```sh
./tempest discovery:generate --no-interaction
```

En développement, `DISCOVERY_CACHE=partial` : le code applicatif est rescanné,
le vendor non. D'où la règle : après `composer require` ou après avoir ajouté un
`x-*.view.php` dans un package, régénérer.

Un composant qui « n'existe pas » alors que le fichier est là, c'est presque
toujours un cache de discovery, ou un dossier absent du PSR-4.

## Ce que ce projet fait

```txt
app/
├── Http/ContentController.php       route unique, retourne un objet de vue
├── View/HasNavigation.php           le contrat : « cette page porte le cadre »
├── View/DocumentView.php            l'objet de vue d'une page de contenu
├── View/NotFoundView.php            l'objet de vue du 404
├── View/NavigationViewProcessor.php les sections, pour toute vue sous contrat
└── main.entrypoint.css              découvert par Vite
views/
├── x-base.view.php                  override du x-base du framework
├── x-masthead.view.php
├── x-toc.view.php
├── x-colophon.view.php
├── document.view.php                rendue par DocumentView
└── not-found.view.php               rendue par NotFoundView
```

Le 404 mérite une ligne. Un `new NotFound()` sans corps n'est pas une page
vide : `HandleRouteExceptionMiddleware` relance toute réponse 4xx en exception,
et le renderer la remplace par la page d'erreur du framework — anglais, Tailwind
depuis un CDN. Passer une vue au constructeur suffit à la garder :
`new NotFound(new NotFoundView($path))`.

Structure en couches, templates séparés du PHP. Les composants sont assez peu
nombreux pour tenir à plat dans `views/` ; s'ils se multiplient, les regrouper
par domaine (`views/nav/`, `views/oeuvre/`) ne change rien à leur nom — le
dossier est pour nous, pas pour Tempest.
