---
title: Contenu XML
description: Le format des entités typées — vocabulaire, règles, exemples.
---

# Contenu XML

Le markdown porte les pages qui sont de la prose. Le XML porte les entités : une
œuvre, une personne, un studio. Un fichier, une entité.

La différence tient en une phrase : une entité a une identité stable et des
relations, donc elle peut être interrogée.

## Un fichier

```xml
<oeuvre id="click-click-click" statut="en-ligne">
  <titre>Click Click Click</titre>
  <annee>2017</annee>
  <par ref="studio-moniker"/>
  <url>https://clickclickclick.click/</url>

  <resume>Chaque clic est analysé, commenté et jugé en temps réel.</resume>

  <section titre="Pourquoi c'est inspirant">
    <markdown>
      Révèle par l'absurde ce que nos interactions livrent sur nous.
      Même parti pris chez [[studio-moniker]].
    </markdown>
  </section>

  <retroliens/>
</oeuvre>
```

## Trois familles d'éléments

**Données** — `<titre>`, `<annee>`, `<par ref>`. Elles ne se rendent pas là où
elles sont écrites : elles alimentent le graphe et la fiche en tête de page.
L'ordre dans le fichier n'a pas d'importance.

**Rendu** — `<markdown>`, `<section>`, `<note>`, `<video>`, `<image>`, `<liste>`.
Elles produisent du HTML sur place, dans l'ordre du document.

**Dérivés** — `<index/>`, `<retroliens/>`. Elles produisent du HTML à partir
d'une requête sur le graphe. Rien à tenir à jour.

## La prose

`<markdown>` est opaque aux balises : ce qu'il contient est du texte, pas un
arbre. L'indentation XML est retirée avant lecture, donc on peut aligner
tranquillement.

Pour référencer une entité au fil d'une phrase, on n'écrit pas de balise :

- `[[studio-moniker]]` — lien vers l'entité, affiche son titre
- `[[studio-moniker|le studio]]` — lien avec un autre libellé
- `![[click-click-click]]` — transclusion : le lien plus le résumé de la cible

Ces liens visent un `id`, pas un chemin. Déplacer ou renommer un fichier ne les
casse pas. Une cible qui n'existe pas encore n'est pas une erreur : le lien
s'affiche en `lien-manquant` et `content:check` l'ajoute à la liste des entités
à écrire. On écrit avant de créer.

Pour une adresse extérieure, les trois écritures marchent :

- `[le titre](https://exemple.org)` — lien avec un libellé
- `<https://exemple.org>` — l'adresse affichée telle quelle
- `https://exemple.org` — pareil, sans les chevrons

Les deux dernières sont reconstruites avant lecture, comme les `[[…]]` : le
parseur markdown n'a de règle que pour la première. Ce qui est déjà un lien
n'est jamais retouché, et une adresse dans un bloc de code reste du code. La
ponctuation de fin de phrase n'est pas avalée — `voir https://exemple.org.`
donne un lien puis un point.

Rien d'autre n'est inventé. Une adresse mail s'écrit comme le lien qu'elle est,
`<a href="mailto:julien@thylo.be">julien@thylo.be</a>` : le HTML au fil du texte
passe tel quel.

## Les requêtes

```xml
<index de="oeuvre" ou="par = self" tri="-annee"/>
```

`de` filtre par type, `ou` par champ, `tri` ordonne — préfixe `-` pour
décroissant. Le mot-clé `self` désigne l'entité de la page courante.

C'est ce qui fait qu'une page de studio ne liste jamais ses œuvres : les œuvres
déclarent leur créateur, et la page lit la relation dans l'autre sens.

`<retroliens/>` affiche qui pointe ici, tous liens confondus — `ref=` comme
`[[…]]`.

## Les types

**`oeuvre`** — attributs `statut`, parmi `en-ligne`, `hors-ligne`, `archive`, et
`visuel="aucun"`, qui dit que l'œuvre n'aura pas d'image et que c'est décidé.
Champs : `annee`, `visuel`, `url`, `par`, `categorie`, `concept`, `voir`.

`<visuel>` porte sa valeur dans un attribut plutôt que dans son texte :

```xml
<visuel src="unlock.jpg"
        alt="Des cartes numérotées étalées sur une table"
        credit="Space Cowboys"
        source="https://www.spacecowboys-games.com/game/unlock/"/>
```

`src` est un nom de fichier, cherché dans `media/oeuvres/`. `alt` s'écrit
toujours à la main. Voir [les médias](/docs/medias).

À ne pas confondre avec `<image>`, qui est un bloc : une capture au fil de la
prose, rendue là où elle est écrite, avec une `legende` en plus. `<visuel>` ne
se rend pas là où il est écrit — il sert l'en-tête de la fiche *et* la vignette
dans les index, comme `<titre>` sert deux endroits.

Une image dont le fichier n'a pas encore été fabriqué ne s'affiche pas et ne
casse rien : c'est `content:check` qui en tient le compte.

**`personne`** — attribut `genre`, parmi `personne`, `studio`, `collectif`,
`organisation`. Champs : `lieu`, `depuis`, `url`.

**`concept`** — le vocabulaire d'analyse. Champs : `genre` (obligatoire, parmi
`structure`, `forme`, `role`, `interface`, `choix`, `ressource`), `exemple`,
`voir`.

**`page`** — une page qui n'est pas une entité : une liste, une introduction.
Aucun champ, juste des blocs. C'est ce qui permet à `/oeuvres` d'être calculée
plutôt que tenue à la main.

`id` et `<titre>` sont obligatoires partout, `<resume>` optionnel : les trois
sont fournis à tous les types, aucun schéma ne les redéclare. `par`, `categorie`,
`concept`, `voir` et `exemple` sont répétables.

`categorie` et `genre` n'acceptent qu'une liste de valeurs connues — écrire
`<categorie>Fiktion</categorie>` est une erreur au chargement, pas une catégorie
de plus.

## Référencer ou nommer

`concept`, `voir` et `exemple` visent forcément une entité : `<voir ref="…"/>`.

`par` accepte les deux. Un créateur qui a une page se cite par référence, un
créateur qui n'en a pas s'écrit en toutes lettres :

```xml
<par ref="blast-theory"/>
<par>Doublespeak Games</par>
```

Le rendu suit le graphe, pas la syntaxe : une valeur qui se résout devient un
lien, une valeur qui ne se résout pas reste du texte. Écrire la page plus tard
transforme le texte en lien sans toucher aux fiches.

`id` et `<titre>` sont obligatoires partout. `par`, `concept` et `voir` sont
répétables et portent leur valeur dans `ref=`.

## Ce qui est refusé au chargement

Une balise inconnue, un attribut non prévu, une valeur hors énumération, un
`<titre>` manquant, du texte hors `<markdown>`, deux entités avec le même `id`,
un `.md` et un `.xml` sur la même URL. Le message nomme le fichier et la ligne.

Une page à moitié rendue est pire qu'une erreur franche.

## Ce qui n'y est pas encore

Le type `seance`. C'est le plus intéressant — un déroulé en activités typées et
minutées rendrait vérifiables les règles du cours : durée totale, ratio
théorie/pratique, pas de magistral au-delà de quinze minutes.

Il demande une décision de modèle avant d'être écrit. Un
`<activite duree="15" type="magistral">` n'entre dans aucune des deux cases
actuelles : comme champ il ne peut pas porter trois attributs ni se répéter avec
une structure, comme bloc il est invisible aux requêtes — or tout son intérêt
*est* une requête.

Les pages de prose, elles, restent en markdown. C'est le partage : le XML porte
ce qui a une identité et des relations, le markdown porte ce qui se lit.
