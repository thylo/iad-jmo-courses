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

**Rendu** — `<markdown>`, `<section>`, `<note>`, `<video>`, `<liste>`. Elles
produisent du HTML sur place, dans l'ordre du document.

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

**`oeuvre`** — attribut `statut`, parmi `en-ligne`, `hors-ligne`, `archive`.
Champs : `resume`, `annee`, `url`, `par`, `concept`, `voir`.

**`personne`** — attribut `genre`, parmi `personne`, `studio`, `collectif`.
Champs : `resume`, `lieu`, `depuis`, `url`.

`id` et `<titre>` sont obligatoires partout. `par`, `concept` et `voir` sont
répétables et portent leur valeur dans `ref=`.

## Ce qui est refusé au chargement

Une balise inconnue, un attribut non prévu, une valeur hors énumération, un
`<titre>` manquant, du texte hors `<markdown>`, deux entités avec le même `id`,
un `.md` et un `.xml` sur la même URL. Le message nomme le fichier et la ligne.

Une page à moitié rendue est pire qu'une erreur franche.

## Ce qui n'y est pas encore

Les types `concept` et `seance`. Le second est le plus intéressant — un déroulé
en activités typées et minutées rend vérifiables les règles du cours : durée
totale, ratio théorie/pratique, pas de magistral au-delà de quinze minutes. Il
viendra quand `oeuvre` et `personne` auront été jugés sur pièces.
