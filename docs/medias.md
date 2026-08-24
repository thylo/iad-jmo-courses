---
title: Médias
description: D'où viennent les images, où elles vivent, comment elles sont servies.
---

# Médias

Plan de mise en place. Le socle et l'acquisition sont écrits — voir « Où on en
est » ; le rendu ne l'est pas encore.

## Où on en est

`<visuel>` et `<image>` existent, `media:fetch`, `media:build` et `media:review`
tournent, `content:check` compte ce qui reste, et **le rendu est branché** :
image de tête sur la fiche, vignettes en grille sur `/oeuvres`, figure au fil de
la prose.

83 œuvres sur 140 sont illustrées. Il reste 57 images à trouver et 82 alt à
écrire — du contenu, plus du code.

Premier passage réel de `media:fetch` sur les 140 œuvres : **62 illustrées**,
78 sans image. Les échecs se répartissent en 29 pages sans balise, 20 fiches
sans `<url>`, 9 pages disparues (404), 6 images trop petites pour servir,
6 refus de robot (403), 3 erreurs serveur, 3 hôtes qui n'existent plus.

Deux trouvailles au passage : les trois œuvres dont l'`<url>` est une vidéo
YouTube — Fort McMoney, Phallaina, Prison Valley — pointent vers des vidéos
supprimées ; et les hôtes de `ionnyk`, `swap-tales` et `generation-what` ne se
résolvent plus du tout.

## L'état des lieux

141 œuvres, aucune image. 120 ont une `<url>`, 112 sont `en-ligne`, 18
`hors-ligne`, 10 `archive`.

`<video src="youtube:ID"/>` existe déjà — `VideoElement` sort une iframe
`youtube-nocookie` en `loading="lazy"` — et n'est utilisé dans aucune fiche.
Douze fichiers citent pourtant une URL YouTube au fil de leur prose.

`3-elements/media.css` fait trois lignes. Il n'y a pas de dossier d'assets :
`public/build/` appartient à Vite et est gitignoré.

Sur la machine : GD compilé avec JPEG, PNG, WebP et AVIF ; Chrome ; `cwebp`.
Rien à installer.

Les chiffres de ce document viennent d’une mesure réelle sur les 120 URL du
corpus, pas d’une estimation — voir « L’acquisition ».

## Les décisions

**Une image par œuvre, en local.** Pas de hotlink vers le site d'origine. Une
`og:image` change ou disparaît sans prévenir, et le site entier est une
démonstration du contraire — voir Le Dernier Gaulois.

**Les originaux sont du contenu.** Ils vivent dans `media/`, versionnés dans
git, à côté du texte qu'ils illustrent. Une image de 1600px de large en JPEG
pèse ~200 Ko ; 141 d'entre elles, ~25 Mo. C'est acceptable pour un dépôt, et ça
évite d'ajouter une infrastructure (LFS, bucket) à un site qui tient dans un
dossier.

**Les dérivés sont fabriqués.** `public/media/`, gitignoré, produit par une
commande, jamais édité à la main.

**WebP seul, trois largeurs.** Pas d'AVIF : l'encodage GD prend une à trois
secondes par image pour un gain que personne ne verra sur des captures
d'écran. Pas de repli JPEG : WebP est supporté partout depuis 2020.

**La vidéo est une façade.** Vignette locale, bouton, et l'iframe n'apparaît
qu'au clic. Une iframe `lazy` charge quand même YouTube dès qu'on approche du
bas de page. Un cours qui passe son temps à critiquer le web de surveillance ne
peut pas appeler Google sur chaque fiche sans qu'on le lui demande.

**L'alternative textuelle s'écrit à la main.** Toujours. C'est du contenu, pas
un champ technique.

## Le vocabulaire XML

Trois ajouts, dans la logique des trois familles décrites dans
[le format XML](/docs/format-xml).

**`<visuel>` — une donnée.** L'image de tête d'une œuvre. Comme `<titre>` et
`<resume>`, elle ne se rend pas là où elle est écrite : elle sert l'en-tête de
la fiche *et* la vignette dans les index. C'est ce qui la distingue d'un bloc.

```xml
<visuel src="unlock.jpg"
        alt="Des cartes numérotées étalées sur une table, un téléphone au milieu affiche un chronomètre"
        credit="Space Cowboys"
        source="https://www.spacecowboys-games.com/game/unlock/"/>
```

`src` est un nom de fichier, résolu dans `media/oeuvres/`. `alt` est
obligatoire. `credit` nomme l'ayant droit, `source` dit d'où vient le fichier —
les deux sont vérifiables, contrairement à l'alt.

**`visuel="aucun"` sur la racine** — l'élément dit quelle image ; l'attribut dit
qu'il n'y en aura pas. Sans lui, une œuvre que personne n'a jamais photographiée
reste dans la file pour toujours et le compte ne veut plus rien dire.

**`<image>` — un bloc.** Une capture au fil de la prose, dans une `<section>`.
Mêmes attributs, plus `caption` au lieu de `credit` seul. Rendu sur place.

**`<video poster="…">` — un attribut de plus.** Le nom de la vignette locale.
Sans lui, la façade n'a rien à afficher.

### Le point de modèle à trancher d'abord

`format-xml.md` nomme déjà le problème à propos du type `seance` : un champ de
données ne sait pas porter trois attributs. `collect()` ne lit que le texte de
l'élément, ou son `ref=`. `<visuel>` tombe exactement dans ce trou.

Deux façons de sortir, et l'ordre compte.

*Maintenant, le minimum :* `visuel` est déclaré comme `Field` avec
`inFiche: false`, ce qui suffit à ce que le parseur l'accepte et le valide. Sa
valeur collectée est le `src`. Le reste — alt, crédit, source — est relu depuis
`$source->root` par un petit objet `Visual::of($source)`, comme `Intro` relit le
HTML rendu. Zéro changement au modèle de données.

C'est ce qui est fait. `Field` a gagné un attribut `attribute: 'src'` — « la
valeur est portée par cet attribut plutôt que par le texte » — et le parseur
refuse un `<visuel>` sans `src` comme il refuse un `<par>` sans `ref`.

*Plus tard, le vrai :* généraliser `Field` aux valeurs structurées. C'est ce que
`seance` réclame de toute façon. Le faire d'abord pour les images, c'est
décider du modèle sur le cas le plus pauvre.

Donc : le minimum maintenant, et `<visuel>` devient un cas d'usage de plus à
poser sur la table le jour où `seance` force la décision.

## Le stockage

```
media/
  oeuvres/unlock.jpg          # l'original, versionné
  oeuvres/unlock-detail.jpg   # une seconde image, pour <image>
  videos/unlock.jpg           # la vignette YouTube rapatriée
public/media/                 # gitignoré, fabriqué
  oeuvres/unlock-320.webp
  oeuvres/unlock-640.webp
  oeuvres/unlock-1280.webp
  index.json
```

Le nom du fichier reprend l'`id` de l'entité. Pas de sous-dossier par œuvre :
141 dossiers à un fichier ne rangent rien.

Les originaux sont normalisés à l'entrée : 1600px de large au maximum, JPEG
qualité 85, métadonnées EXIF retirées. Un PNG de capture d'écran fait 3 Mo là
où le JPEG en fait 200 Ko, et personne ne verra la différence sur une capture
de site.

## La fabrication — `media:build`

Une commande console, dans la lignée de `content:check`.

1. Parcourt `media/`, lit chaque original avec GD.
2. Produit `320`, `640`, `1280` en WebP qualité 78. Jamais d'agrandissement :
   une source de 900px ne donne pas de variante 1280.
3. Écrit `public/media/index.json` : pour chaque asset, ses largeurs
   disponibles, ses dimensions intrinsèques, et le hash du fichier source.
4. Le hash sert au ré-encodage : ce qui n'a pas bougé n'est pas refait. Le
   premier passage prend quelques minutes, les suivants quelques secondes.

Les largeurs viennent de la mise en page, pas d'une convention. La colonne de
texte fait `62ch`, soit ~530px ; la grille complète en fait ~780. Donc 640
couvre la colonne en 1×, 1280 la couvre en 2× et la grille en 1,7×. 320 est la
vignette d'index en 2×.

**`MediaLibrary`**, singleton, lit `index.json` une fois par requête et répond à
« donne-moi le `srcset` et les dimensions de `oeuvres/unlock` ».

Le plan disait qu'un asset absent du manifeste devait lever une erreur, au nom
de « une page à moitié rendue est pire qu'une erreur franche ». **Décision
inversée pour les images.** Cette règle vaut pour le texte : une fiche sans son
titre n'est pas une fiche. Une image est un ajout — et avec 141 vignettes sur
`/oeuvres`, une seule image non construite ferait tomber la page entière, donc
la sanction serait mille fois plus grosse que la faute.

`find()` rend donc `null` et l'œuvre s'affiche sans image. Ce qui manque n'est
pas silencieux pour autant : `content:check` compte les fichiers absents et les
images non construites. Le contrôle passe du rendu à l'outil de contrôle, ce qui
est sa place.

`media:build` s'ajoute au déploiement, à côté de `npm run build`.

## Le rendu

Un seul format, donc pas de `<picture>` : un `<img>` avec `srcset` suffit.

```html
<img src="/media/oeuvres/unlock-640.webp"
     srcset="/media/oeuvres/unlock-320.webp 320w,
             /media/oeuvres/unlock-640.webp 640w,
             /media/oeuvres/unlock-1280.webp 1280w"
     sizes="(min-width: 48em) 33rem, calc(100vw - 3.5rem)"
     width="1600" height="900"
     alt="…" loading="lazy" decoding="async">
```

`width` et `height` viennent du manifeste et portent les dimensions
*intrinsèques* : le navigateur en déduit le ratio et réserve la place avant le
téléchargement. Sans eux, chaque image fait sauter la page.

`loading="lazy"` partout sauf sur l'image de tête d'une fiche, qui est visible
au chargement — la retarder est un ralentissement, pas une économie.

Trois emplacements :

- **En tête de fiche**, entre le `<h1>`/résumé et la `<dl class="fiche">`.
  C'est la seule place où l'image dit quelque chose avant qu'on ait lu.
- **Dans les index** (`/oeuvres`, `/panorama`) : une vignette par ligne.
  141 vignettes sur une page, donc `lazy` obligatoire et variante 320.
  C'est le changement le plus visible du lot : un index d'œuvres visuelles qui
  n'est que du texte demande de cliquer pour savoir de quoi on parle.
- **Au fil de la prose**, via `<image>`.

Côté CSS : `5-components/figure.css` pour la figure, la légende et le crédit ;
`3-elements/media.css` reste le socle. La vignette d'index se pose dans
`5-components/index.css` — l'index passe d'une `<ul>` à une grille, et c'est le
seul endroit où la mise en page change vraiment.

## La vidéo

`VideoElement` gagne la façade :

```html
<figure class="video">
  <button type="button" data-video="ID" aria-label="Lire la vidéo : …">
    <img src="/media/videos/unlock-640.webp" …>
  </button>
  <figcaption>…</figcaption>
</figure>
```

Une poignée de lignes dans `main.entrypoint.ts` remplace le bouton par l'iframe
au clic, avec `autoplay=1` pour que le geste ne soit pas à refaire. Sans
JavaScript, le bouton ne fait rien — donc `<figcaption>` porte un lien vers la
vidéo sur YouTube. C'est le repli honnête.

La vignette vient de `https://i.ytimg.com/vi/<ID>/maxresdefault.jpg`, rapatriée
dans `media/videos/`. Elle sert deux fois : la façade, et l'image de tête pour
les œuvres qui n'en ont pas d'autre.

## L'acquisition

Mesuré, pas supposé : les 120 `<url>` du corpus ont été appelées, les
`og:image` extraites, téléchargées et regardées en planche-contact.

```
120 œuvres avec une <url>
 → 105 répondent 200        15 en échec (403, 404, 500, timeout)
 →  80 exposent og:image ou twitter:image
 →  74 se téléchargent      6 échouent (hotlink refusé, lien mort)
 →  50 font 1200px de large ou plus
 → ~63 montrent réellement l'œuvre
```

**Un peu plus d'une œuvre sur deux est illustrée correctement sans intervention
humaine.** C'est beaucoup mieux que ce que j'avais supposé en écrivant la
première version de ce plan, où l'`og:image` était reléguée en troisième
recours. Elle est le premier.

Et souvent c'est la *bonne* image, pas un pis-aller. Une affiche de Late Shift,
le lettrage de The Boat, la grille du Chrome Music Lab, la boîte de MicroMacro,
le bouton de Click Click Click : ce sont des images choisies par les auteurs
pour représenter leur travail. Pour un jeu de plateau, une installation ou un
film, elles battent une capture d'écran de site.

### Ce que ça rate

Quatre cas nets, tous visibles d'un coup d'œil sur une planche-contact :

- **Le domaine expiré.** `audience` et `rain-room` renvoient la réclame
  « It all starts with a domain name » de HugeDomains. L'œuvre est morte et
  déclarée `en-ligne` dans nos fiches.
- **Le logo de l'éditeur.** Alice is Missing donne le logo Hunters
  Entertainment, Le Dernier Gaulois celui d'Immersive Garden.
- **La mauvaise page.** Descent renvoie la vignette d'une chaîne de tests,
  `heredity` un site qui n'a rien à voir.
- **L'icône d'application.** Protanopia donne son icône App Store, correcte
  mais muette.

### Les autres sources, dans l'ordre

**Les vignettes YouTube**, pour les 40 œuvres sans balise. Trois d'entre elles
ont déjà une vidéo comme `<url>` — Fort McMoney, Phallaina, Prison Valley — et
l'identifiant suffit à récupérer une image en 1280×720. Les douze URL YouTube
citées dans la prose des fiches ajoutent autant de candidates.

**La capture à la main**, pour ce qui reste : les sites d'artistes qui n'ont
jamais entendu parler d'Open Graph et sont précisément ce que le cours veut
montrer — Niklas Roy (quatre œuvres), Matthew Rayfield, Foddy, The Telegarden,
neal.fun.

Le plan prévoyait Chrome en headless. Abandonné : automatiser la capture, c'est
piloter un navigateur, attendre que la page ait fini de bouger, cadrer une
fenêtre — beaucoup de machinerie pour choisir *quand* appuyer, ce qui est
justement le seul moment intéressant. Ces pages-là demandent qu'on les regarde
jouer. `media:review` ouvre l'adresse dans la fiche, on capture soi-même, on
glisse le fichier dans le terminal. Deux secondes de plus par œuvre, zéro
dépendance, et le cadrage est choisi par quelqu'un.

**La Wayback Machine**, pour les 28 œuvres hors ligne ou archivées, plus celles
que la mesure vient de révéler mortes. Résultat souvent cassé — CSS manquant,
images absentes — et c'est parfois le propos : une œuvre morte a le droit
d'avoir l'air morte.

### L'effet de bord qui vaut le détour

L'appel des 120 URL est aussi un audit de pourriture de liens, et il rapporte
tout de suite :

- morts pour de bon : `alma` (NFB, 404), `gaza-sderot` (INA, 404),
  `fantasy-bnf-demeure-du-guide` (404), `plonge-be-family-bash` (404),
  `riding-the-new-silk-road` (404), `highrise` (NFB, 500),
  `babel` et `the-climate-show` (500), `generation-what` et `swap-tales`
  (injoignables) ;
- vivants mais fermés aux robots : NYT, Shadertoy, Space Cowboys, Asmodée
  répondent 403 à `curl` et s'ouvrent normalement dans un navigateur. Il faut
  les distinguer des vrais morts avant de toucher au `statut`.

Une dizaine de fiches marquées `en-ligne` ne le sont plus. Ça, c'est du contenu
faux, et ça valait déjà le voyage.

### Ce que la commande écrit

`media:fetch` enchaîne les quatre sources et écrit le fichier plus un
`<visuel>` **sans `alt`**, avec `source=` renseigné automatiquement — c'est
vérifiable, contrairement à une description. Elle ne remplit jamais `alt` ni
`credit` toute seule.

Écrite : les deux premières sources tournent, les deux autres sont une classe
et une ligne dans `VisualSources`. Options : `--only=id` pour une fiche,
`--limit=N` pour s'arrêter tôt, `--force` pour reprendre celles qui ont déjà un
visuel, `--dry` pour télécharger et vérifier sans rien écrire.

L'insertion dans le XML est textuelle, pas un aller-retour DOM : resérialiser
reformaterait cent fiches et noierait la ligne qui compte. Le fichier est relu
juste après ; s'il ne se parse plus, il est remis comme il était.

Le travail est dans `VisualFetcher::fetch()` et `MediaBuilder::build()`, qui ne
parlent jamais à la console : elles remplissent un `MediaReport` et passent
chaque résultat à un callback si quelqu'un regarde. C'est ce qui permet à un
formulaire d'appeler la même méthode plus tard.

Le choix des vidéos ne s'automatise pas non plus. Une recherche YouTube renvoie
des parties filmées de deux heures et des bandes-annonces trompeuses. La
commande récolte les douze URL déjà citées dans la prose, les convertit en
`<video src="youtube:ID"/>`, et propose trois candidats par œuvre pour le reste.

## Le contrôle

**Une section de plus dans `content:check`** — c'est ce qui a été fait plutôt
qu'une commande séparée, le contrôle du contenu se lit d'un seul endroit :

- les œuvres sans `<visuel>` — la file « à illustrer », comme la file
  « entités à écrire » qui existe déjà ;
- les `<visuel>` sans `alt` ;
- les `src` qui pointent vers un fichier absent de `media/` ;
- les fichiers de `media/` que plus aucun XML ne cite. C'est le même problème
  que les 155 fichiers orphelins du corpus Astro, et il vaut mieux l'attraper à
  la troisième image qu'à la centième.

**Le tri : `media:review`.** Pas une planche-contact web, finalement. Une page
`/medias` aurait montré les 62 images côte à côte, et le tri aurait quand même
dû se faire ailleurs — retrouver la fiche, ouvrir le fichier, écrire l'alt.

La commande fait des deux choses une seule : elle prend les œuvres une par une,
affiche ce qu'on sait (année, auteur, adresse, statut, résumé), ouvre l'image
dans l'aperçu, et demande. Garder, rejeter, remplacer par un fichier ou par une
adresse, ou décider qu'il n'y aura pas d'image. Puis l'alternative textuelle et
le crédit. Chaque réponse est écrite dans le XML avant l'œuvre suivante, donc
s'arrêter après cinq fiches et revenir demain ne perd rien : le seul état, c'est
le contenu.

```
php ./tempest media:review [--only=id] [--limit=N] [--missing] [--doubtful] [--all] [--no-preview]
```

**Ce que la machine analyse quand même.** Trois signaux, tous trouvés sur le
corpus réel plutôt qu'imaginés :

- **deux œuvres qui partagent le même fichier octet pour octet** — ce sont
  `audience` et `rain-room`, les deux domaines expirés, qui servent la même
  réclame HugeDomains ;
- **une page qui a répondu depuis un autre domaine que celui de la fiche** —
  HugeDomains encore, plus Alice is Missing qui mène au logo de son éditeur,
  Late Shift, The Johnny Cash Project, The Fallen of World War II ;
- **une image plus étroite que la colonne de texte** — 640px. En dessous, ce
  n'est la capture de rien.

Douze œuvres sur 62, en 0,08 seconde. Aucun de ces signaux ne prouve qu'une
image est mauvaise ; ils mettent devant les huit qu'il faut vraiment ouvrir.
`--doubtful` ne passe en revue que celles-là, et `content:check` en donne le
compte.

## Droit et crédit

Ce sont les images d'autres personnes, sur un site de cours public.

Chaque image affiche son crédit et pointe vers sa source — c'est ce que
`credit` et `source` servent à rendre systématique plutôt que dépendant de la
bonne volonté du jour. Les images restent petites : une capture de 1280px n'est
pas une reproduction exploitable de l'œuvre, et elle ne remplace pas une visite.

Ça n'est pas un avis juridique. C'est la posture minimale à tenir : citer,
créditer, renvoyer chez l'auteur, et retirer sans discuter si quelqu'un le
demande. Une ligne dans le colophon suffit à le dire.

## L'ordre

La mesure change l'ordre. Les 74 images sont déjà téléchargées : il y a de quoi
travailler à vrai contenu dès la première ligne de code, plutôt que sur un
fichier de test.

1. ~~**Le socle.**~~ Fait. `<visuel>` comme `Field`, `Visual`, `MediaLibrary`,
   `media:build`, `media:fetch`, la section « Images » de `content:check`.
   62 images sont dans `media/oeuvres/` avec leur `source=`. `figure.css` est
   remis à l'étape 3, avec le rendu : une feuille que rien n'utilise est du
   code mort.
2. ~~**Le tri d'abord, le rendu ensuite.**~~ L'outil est là : `media:review`
   trie, illustre et décrit en une passe, `--doubtful` sort les douze à
   vérifier. Reste à le faire — c'est du contenu, pas du code.
3. ~~**Le rendu.**~~ Fait. `ImageTag` rend les trois emplacements, `figure.css`
   et `index.css` les habillent. La grille n'apparaît que si au moins une entrée
   porte une vignette, donc un corpus à moitié illustré ne se rend jamais comme
   une grille de trous.
4. **La façade vidéo.** `VideoElement`, le JS, le repli sans JS. Les douze URL
   YouTube citées dans la prose deviennent des `<video>`, et leurs vignettes
   comblent une partie des 40 manquantes.
5. **Le reste des images.** `media:review --missing` déroule les 78 restantes :
   capture à la main pour les sites d'artistes, Wayback pour les morts, et
   « aucune image » quand c'est la bonne réponse. C'est le travail long, et il
   porte sur ~78 œuvres, pas 141.
6. **Les statuts.** Une dizaine de fiches disent `en-ligne` pour une œuvre
   morte. À corriger avec la même passe, en distinguant le 404 réel du 403
   anti-robot.
7. **Les alt.** 141 phrases à écrire. Par paquets, entre deux autres choses.
   `content:check` tient le compte.

Les étapes 1 à 4 sont du code et tiennent dans une session. Les étapes 5 à 7
sont du contenu et prendront le temps qu'elles prendront — c'est pour ça que
`content:check` doit les compter dès l'étape 1.

## Ce qui ne sera pas automatisé

L'alternative textuelle. Le choix de la vidéo. Le tri des images ratées. Le
recadrage. Décider qu'une œuvre n'a pas d'image et que c'est très bien ainsi.

Une machine sait poser 74 fichiers dans un dossier en trois minutes — elle
vient de le faire. Elle ne sait pas que la réclame HugeDomains n'est pas une
œuvre.
