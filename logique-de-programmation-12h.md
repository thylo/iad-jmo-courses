# Logique de programmation — 12 h (6 × 2 h)

Document de conduite. Public : étudiants en multimédia de l'IAD, 18-22 ans, aucun n'a
jamais programmé. Ils maîtrisent le montage, l'image et le motion design : ils savent
déjà ce qu'est une timeline, un calque et une image par seconde, et ce savoir sert de
point d'accroche à presque toutes les notions du cours.

Outil : **p5.js**, dans l'éditeur en ligne `editor.p5js.org`. Aucune installation, un
compte gratuit, un lien à partager pour chaque sketch. La séance 1 s'ouvre sans machine.

Notions couvertes : variables, types, conditions, boucles, fonctions, portée.

Travail à la maison : quarante-cinq minutes entre deux séances, jamais plus.
Rendu noté à la dernière séance.

---

## Les trois règles du dispositif

**Aucune notion avant le manque.** Chaque séance ouvre sur un exercice que les outils
déjà acquis ne permettent pas de finir. La notion arrive comme réponse, jamais comme
chapitre. La boucle n'est pas « le chapitre 4 » : c'est ce qui sauve celui qui vient
d'écrire quarante fois la même ligne. Le manque de chaque séance est fabriqué la semaine
précédente, dans l'atelier — c'est pour cela que l'objet de la séance 2 sort de l'écran
et ne revient pas.

**Rien ne part de zéro à la maison.** Le devoir est toujours une variation d'un sketch
qui tourne déjà. La page blanche à domicile produit l'abandon chez un débutant ; la
variation produit de l'exploration. Les six devoirs sont bâtis sur le même modèle :
changer une chose à la fois, et dire laquelle.

**Le résultat est visible en moins de cinq minutes.** Aucun exercice de calcul sans
image. Tout ce qui est écrit se voit, bouge ou sonne.

---

## Structure fixe d'une séance de 2 h

Identique les six fois, pour qu'elle devienne un cadre et non une surprise. La séance 1
est la seule exception : son ouverture débranchée dure trente minutes.

| Temps | Bloc |
|---|---|
| 0:00–0:15 | **La reprise** — trois sketches de la semaine projetés, sans commentaire d'abord |
| 0:15–0:35 | **Le manque** — un exercice qui échoue avec les outils actuels |
| 0:35–0:55 | **La notion** — code écrit en direct au tableau, jamais plus de 12 min d'affilée |
| 0:55–1:05 | Pause |
| 1:05–1:45 | **L'atelier** — binômes, conducteur / navigateur, permutation à mi-parcours |
| 1:45–2:00 | **Le mur** — tous les liens affichés côte à côte, deux ou trois montrés |

Le binôme est hétérogène et reformé à chaque séance. Le conducteur tient le clavier, le
navigateur lit et dicte ; ils échangent à mi-atelier, au signal, sans négociation.

Celui qui avance vite ne reçoit jamais « l'exercice suivant » mais **une contrainte
supplémentaire** sur le même exercice — le refaire sans `if`, en dix lignes, en deux
couleurs. Il reste dans le problème de son binôme au lieu de le distancer. Trois
contraintes sont prévues pour chaque atelier.

Le mur n'est pas une évaluation. Les liens sont affichés tous ensemble, on en ouvre deux
ou trois pris au hasard, et l'on ne commente que ce qui a été fait, jamais ce qui manque.

---

## Le protocole de panne

Distribué en séance 1, affiché les six séances. C'est la compétence la plus rentable du
cours : un débutant sans méthode de débogage attribue l'échec à lui-même et arrête.

1. **Lire le message en entier**, et le numéro de ligne. Le numéro indique où la machine
   a buté, pas toujours où est la faute — souvent la faute est une ligne plus haut.
2. **Dire à voix haute** ce que cette ligne est censée faire. La moitié des pannes se
   règlent ici.
3. **Afficher la valeur.** `console.log(maVariable)` — vérifier ce que la variable
   contient vraiment, pas ce qu'on croit qu'elle contient.
4. **Retirer** des lignes jusqu'à ce que ça remarche, puis en remettre une à la fois.

**Règle des dix minutes** : au-delà, on demande. D'abord au binôme voisin, ensuite
seulement au professeur.

**La panne silencieuse.** Toutes les erreurs n'affichent pas un message. Un code qui
tourne et ne fait rien est une panne comme une autre : c'est l'étape 3 qui la trouve.

---

## Décisions d'écriture du code enseigné

Elles tiennent les six séances et ne se discutent pas en cours.

**Une seule façon de déclarer.** `let` partout, jamais `var`. `const` apparaît en séance
5 pour les médias, et seulement là.

**Les noms de variables sont en français.** `positionX`, `vitesse`, `estAllume`. Ce qui
vient du langage — `function`, `let`, `if`, `for`, `return` — reste en anglais. Le
contraste se voit à l'œil, et c'est ce qui apprend le plus vite la seule distinction qui
compte au début : ce que la machine impose, et ce que l'auteur choisit.

**`i = i + 1` plutôt que `i++`.** La forme longue réutilise exactement le geste appris en
séance 2. `i++` est mentionné une fois, en séance 4, pour qu'ils puissent lire le code
des autres — puis abandonné.

**Pas d'objets, pas de classes.** Les tableaux de la séance 4 sont des tableaux parallèles
(`positionsX`, `positionsY`). Ce n'est pas ce qu'on écrirait avec plus de temps, et il
faut le dire en une phrase plutôt que le cacher.

**Les points-virgules sont toujours écrits.**

---

## Avant la séance 1

**Les comptes.** Envoyer la veille un message avec une seule consigne : créer un compte
sur `editor.p5js.org`. Sans cela, vingt minutes de la première séance partent en mots de
passe oubliés.

**La page de liens.** Un document collaboratif, un tableau à deux colonnes — nom, lien —
ouvert les six séances et projeté à chaque mur.

**Le poste du professeur.** Augmenter la taille de police de l'éditeur dans ses réglages,
avant la séance et pas devant la classe. Le code projeté doit se lire du fond.

**Le matériel, séance par séance.** S1 : six enveloppes avec les figures imprimées,
feuilles A4, feutres, tableau, le protocole de panne et la grille de notation imprimés en
autant d'exemplaires que d'étudiants. S3 : les papiers de règles de l'exercice debout.
S5 : une image et un son libres de droits prêts à téléverser, pour ceux qui auront oublié
les leurs. S6 : les six sketches cassés, préparés et testés.

**Le repli réseau.** L'éditeur en ligne ne fonctionne pas hors connexion. Garder sur clé
une copie de la bibliothèque et un fichier de départ, pour continuer en local.

**Le seul bloc à risque** est l'atelier de la séance 4. Le refaire soi-même et le
chronométrer avant la séance 1, comme les cinq autres.

---

# Séance 1 — L'instruction littérale, et le premier pixel

**Notions** : système de coordonnées, appel de fonction et arguments, ordre d'exécution,
lecture d'un message d'erreur.

| Temps | Bloc |
|---|---|
| 0:00–0:30 | La dictée de figures — aucune machine |
| 0:30–0:40 | Sol LeWitt, et la mise en route de l'éditeur |
| 0:40–1:00 | Le plan de l'écran |
| 1:00–1:10 | Pause |
| 1:10–1:45 | Atelier — la figure imposée |
| 1:45–1:55 | Le bloc de la panne |
| 1:55–2:00 | Le mur, et la grille de notation |

Pas d'introduction, pas de tour de table, pas de présentation du cours. Ils entrent, on
distribue les enveloppes.

## 0:00 — La dictée de figures

Par binômes. Chaque binôme reçoit une enveloppe contenant une figure imprimée, que les
autres ne voient pas.

> **Consigne.** Dix minutes pour écrire, sur une feuille, les instructions permettant de
> reproduire cette figure au tableau. Celui qui exécutera ne verra jamais l'original, ne
> pourra poser aucune question, et appliquera à la lettre. Ce qui n'est pas écrit
> n'existe pas.

Les six figures, par difficulté croissante :

1. Un carré avec une diagonale.
2. Trois cercles alignés, de tailles décroissantes.
3. Une maison : un carré surmonté d'un triangle.
4. Un damier 3 × 3, une case sur deux noircie.
5. Un visage : un ovale, deux points, un trait.
6. Un escalier de cinq marches.

Puis les feuilles d'instructions circulent — pas les enveloppes. Chaque binôme exécute au
tableau les instructions d'un autre. Le professeur fait respecter une seule règle :
**aucune interprétation**. Si l'instruction dit « trace un trait », le trait fait la
longueur que veut l'exécutant.

Le résultat rate, toujours. C'est le matériau de la séance.

**Le dépouillement — quatre pannes à faire nommer par la classe.** Les écrire au tableau,
elles restent affichées les six séances.

| Panne | Ce qui manque | Exemple entendu |
|---|---|---|
| **L'ambiguïté** | Un mot qui a deux sens | « un cercle au milieu » — au milieu de quoi ? |
| **L'ordre** | Deux instructions justes, dans le mauvais ordre | la couleur déclarée après le tracé |
| **L'unité** | Un nombre | « grand », « à droite », « un peu plus bas » |
| **L'implicite** | Ce que l'auteur sait et n'a pas écrit | « évidemment, les traits se touchent » |

Ces quatre pannes sont les mêmes que celles des messages d'erreur, et l'on y renverra à
chaque séance. La figure 4, le damier, ne s'écrit pas sans répéter dix fois la même
phrase : elle est laissée de côté explicitement, et rouverte en séance 4.

## 0:30 — Sol LeWitt

Trois minutes, pas dix. *Wall Drawing #118* (1971) : le mur n'est pas peint par LeWitt,
il est décrit — cinquante points placés au hasard, tous reliés par des lignes droites —
et exécuté par d'autres, à partir d'un certificat. Montrer deux exécutions différentes du
même texte, dans deux musées. L'instruction est l'œuvre ; l'exécution varie.

C'est la seule justification dont le cours a besoin : écrire du code, c'est écrire une
partition. La suite est technique.

Puis l'éditeur : ouvrir un compte, créer un sketch, appuyer sur le bouton d'exécution.
Dix minutes, et le professeur circule — c'est ici que se règlent les comptes non créés.

## 0:40 — Le plan de l'écran

Code écrit en direct, dans cet ordre exact. Ne rien projeter d'écrit à l'avance : ils
doivent voir apparaître les lignes.

```js
function setup() {
  createCanvas(800, 600);
}
```

Un rectangle gris. Deux nombres, une surface. Faire changer les deux nombres.

```js
function setup() {
  createCanvas(800, 600);
  background(20);
  circle(0, 0, 100);
}
```

Le cercle est coupé, en haut à gauche. **L'origine est en haut à gauche, et l'axe
vertical descend.** Contre-intuitif pour qui se souvient des mathématiques, familier pour
qui a déplacé un calque dans un logiciel de montage — le dire dans ce sens-là.

Faire trouver à la classe les nombres qui placent le cercle au centre. `circle(400, 300,
100)`. Puis les trois arguments : une position horizontale, une position verticale, un
diamètre, **dans cet ordre, et l'ordre n'est pas négociable**. C'est la dictée de figures,
en machine.

Les formes, ajoutées une par une : `rect`, `square`, `ellipse`, `line`, `triangle`. Puis la
couleur :

```js
function setup() {
  createCanvas(800, 600);
  background(20);
  circle(400, 300, 100);
  fill(230, 60, 40);
}
```

Rien ne change. Déplacer le `fill` d'une ligne vers le haut : le cercle devient rouge.
**La panne de l'ordre, revenue en machine.** Ne pas l'expliquer avant de l'avoir montrée.

Finir par les paires `fill` / `noFill` et `stroke` / `noStroke`, par `strokeWeight()`, et
par `width` et `height` comme simple confort : `circle(width / 2, height / 2, 100)`.

## 1:10 — Atelier : la figure imposée

Projeter une composition simple et la laisser affichée toute la durée de l'atelier : un
fond sombre, un grand rectangle clair décentré, trois disques de tailles différentes
alignés en diagonale, une ligne fine qui traverse.

> **Consigne.** Reproduire la composition projetée. Le nombre de lignes n'a aucune
> importance. Ce qui compte, c'est que ça ressemble.

**Contraintes supplémentaires**, pour ceux qui ont fini :

- (a) La même composition sans utiliser `rect`.
- (b) La même composition en deux couleurs seulement.
- (c) La même composition retournée — le haut en bas.

## 1:45 — Le bloc de la panne

Trois erreurs provoquées sur le poste du professeur, dans cet ordre. Les faire prédire
avant d'exécuter : « qu'est-ce qui va se passer ? »

```js
circle(400, 300);
```
> `circle() was expecting 3 arguments but received 2` — il manque un nombre. La machine
> dit précisément ce qui manque, et c'est rare : en profiter.

```js
Circle(400, 300, 100);
```
> `Circle is not defined` — la majuscule. La machine ne devine pas, elle ne corrige pas,
> elle ne connaît aucun mot approchant.

```js
circle(400, 300, 100
```
> `SyntaxError: missing ) after argument list` — et le numéro de ligne indiqué est celui
> d'**après**. C'est la leçon la plus utile de la séance : le numéro dit où la machine a
> buté, pas où est la faute.

Distribuer le protocole de panne. Le lire à voix haute, en entier, une seule fois.

## 1:55 — Le mur

Les liens de tous les sketches, affichés côte à côte sur la page partagée. Deux ouverts
au hasard, trente secondes chacun, sans commentaire de qualité.

Distribuer la grille de notation du rendu final. Elle est donnée maintenant, jamais plus
tard : un critère découvert à la séance 6 est une surprise déloyale.

## Devoir — 45 minutes

Trois variantes de la composition de l'atelier. **Chaque variante ne change qu'une seule
catégorie de chose** : les positions, ou les couleurs, ou les tailles — pas deux.

Poster les trois liens sur la page partagée, et écrire à côté ce qui a changé.

---

# Séance 2 — La variable, et le mouvement

**Notions** : `draw()` comme timeline, la variable, les types (nombre, texte, booléen),
première rencontre avec la portée.

| Temps | Bloc |
|---|---|
| 0:00–0:15 | La reprise |
| 0:15–0:35 | Le manque — faire traverser l'écran |
| 0:35–0:55 | La notion — `draw()`, la variable, les types |
| 0:55–1:05 | Pause |
| 1:05–1:45 | Atelier — la traversée |
| 1:45–2:00 | Le mur |

## 0:00 — La reprise

Trois séries de variantes projetées, prises au hasard, l'auteur ne parle pas. Une seule
question à la classe pour chacune : « qu'est-ce qui a changé entre les trois ? » S'ils le
voient, la consigne a été tenue.

## 0:15 — Le manque

> **Consigne.** Un disque part du bord gauche et arrive au bord droit. Vingt minutes,
> avec les outils de la séance 1.

Laisser faire huit minutes sans intervenir. Ils écrivent des lignes de cercles :

```js
circle(0, 300, 80);
circle(10, 300, 80);
circle(20, 300, 80);
```

Tout s'affiche en même temps. Il n'y a pas de temps dans `setup()`. Faire formuler le
constat par un étudiant avant de passer à la suite.

## 0:35 — La notion

```js
function setup() {
  createCanvas(800, 600);
}

function draw() {
  background(20);
  circle(400, 300, 80);
}
```

Rien ne semble changer. Remplacer le premier nombre par `random(800)` : ça grésille.
**`draw()` est rejoué soixante fois par seconde.** C'est leur timeline, et ils la
connaissent déjà — une image, puis une autre, puis une autre.

Retirer `background(20)` : la trace apparaît. Le fond n'est pas un décor, c'est un
effacement. Laisser tourner dix secondes, c'est un moment visuel et il vaut mieux qu'un
paragraphe.

Remettre le fond. Poser le vrai manque : pour que le disque avance, il faut un nombre qui
change entre deux images. Or tout ce qui est écrit dans `draw()` recommence à
l'identique.

```js
let positionX = 0;

function setup() {
  createCanvas(800, 600);
}

function draw() {
  background(20);
  circle(positionX, 300, 80);
  positionX = positionX + 2;
}
```

**Le geste à décomposer.** `positionX = positionX + 2` n'est pas une égalité. Lu de droite
à gauche : *prends ce qu'il y a dans la boîte, ajoute 2, remets le résultat dans la
boîte*. C'est la première erreur de lecture de tous les débutants, qui lisent `=` comme en
mathématiques. Le faire dire à voix haute par trois étudiants différents, dans leurs
propres mots. Ne pas passer à la suite avant.

**La portée, en observation.** Déplacer `let positionX = 0;` à l'intérieur de `draw()` :
plus rien ne bouge. Pourquoi : la ligne est rejouée à chaque image, et la variable est
recréée à zéro soixante fois par seconde.

Ne pas prononcer le mot « portée ». Écrire le constat au tableau, tel quel :

> Ce qui est déclaré dans `draw()` ne survit pas à l'image suivante.

Il sera repris en séance 4, puis nommé en séance 5.

**Les types**, amenés par quatre besoins et non par une liste :

```js
let positionX = 0;              // un nombre
let titre = "Sans titre";       // du texte
let enPause = false;            // un booléen : vrai ou faux, rien d'autre
```

La démonstration qui rend le type visible — remplacer `0` par `"0"` :

```js
let positionX = "0";
positionX = positionX + 2;      // "02", puis "022", puis "0222"
```

Le disque ne bouge plus, et aucune erreur ne s'affiche. Du texte s'additionne autrement
qu'un nombre : le `+` ne fait pas la même chose selon ce qu'on lui donne. C'est la
définition d'un type, et c'est aussi une panne silencieuse — la troisième étape du
protocole la trouve en une ligne.

Finir par les variables que le monde fournit et qu'on ne déclare pas :

```js
circle(mouseX, mouseY, 80);
```

## 1:05 — Atelier : la traversée

Le squelette est fourni, troué. On ne part pas d'une page blanche à ce stade.

```js
let positionX = 0;
let vitesse = ___;

function setup() {
  createCanvas(800, 600);
}

function draw() {
  background(20);
  fill(230, 200, 60);
  circle(___, 300, 60);
  positionX = ___;
}
```

> **Consigne.** Faire traverser l'écran à l'objet. Puis lui donner une allure : sa forme,
> sa couleur, sa vitesse sont des décisions, pas des valeurs par défaut.

**Contraintes supplémentaires :**

- (a) L'objet accélère au lieu d'aller à vitesse constante.
- (b) Il laisse une trace qui s'efface lentement — remplacer `background(20)` par
  `background(20, 20)` et comprendre ce que fait le second nombre.
- (c) Deux objets qui traversent à des vitesses différentes.

**L'objet sort de l'écran et ne revient pas.** C'est voulu, et c'est le manque de la
séance 3. Si la question est posée, répondre qu'il n'y a pas encore de quoi la résoudre,
et l'écrire au tableau pour la semaine suivante.

## Devoir — 45 minutes

Trois variations du même mouvement. L'une des trois doit être pilotée par `mouseX`.
Poster les trois liens.

---

# Séance 3 — La condition, et l'aléatoire choisi

**Notions** : `if` / `else`, comparaisons, `&&` et `||`, le booléen comme mémoire d'état,
`random()` avec des bornes.

| Temps | Bloc |
|---|---|
| 0:00–0:15 | La reprise |
| 0:15–0:35 | Le manque, et l'exercice des règles |
| 0:35–0:55 | La notion — décider pendant que ça tourne |
| 0:55–1:05 | Pause |
| 1:05–1:45 | Atelier — la scène qui décide |
| 1:45–2:00 | Le mur |

## 0:15 — Le manque

Projeter un sketch de la semaine et le laisser tourner sans rien dire. Au bout de quelques
secondes, l'écran est vide et le reste. Attendre que quelqu'un le fasse remarquer.

Il faut décider quelque chose **pendant** que ça tourne.

**L'exercice des règles**, huit minutes, debout, aucune machine. Chaque étudiant reçoit un
papier avec une règle, qu'il ne montre à personne :

- Si quelqu'un lève la main, lève la tienne.
- Si plus de trois personnes sont debout, assieds-toi.
- Si personne ne bouge pendant cinq secondes, lève-toi.
- Si la personne à ta gauche s'assoit, lève-toi.
- Si tu es debout depuis dix secondes, assieds-toi.

On déclenche, on laisse courir deux minutes, on arrête. Le groupe a produit un
comportement que personne n'a écrit et que personne ne contrôle. Deux idées en une : la
condition, et le système qui émerge de règles simples. Ne pas commenter plus de deux
minutes.

## 0:35 — La notion

```js
if (positionX > 800) {
  positionX = 0;
}
```

Lire le `if` comme une **question posée à chaque image**, et non comme une action. La
question est reposée soixante fois par seconde, et la réponse peut changer.

Puis le rebond, qui demande une deuxième variable :

```js
let positionX = 0;
let vitesse = 3;

function setup() {
  createCanvas(800, 600);
}

function draw() {
  background(20);
  circle(positionX, 300, 80);
  positionX = positionX + vitesse;

  if (positionX > width || positionX < 0) {
    vitesse = -vitesse;
  }
}
```

`vitesse = -vitesse` est le deuxième geste contre-intuitif du cours, à décomposer comme
celui de la séance 2 : *prends ce qu'il y a dans la boîte, change son signe, remets-le*.

**Les comparaisons** : `>`, `<`, `>=`, `<=`, `===`, `!==`. Écrire au tableau, côte à côte
et en grand :

```js
compteur = 5     // range 5 dans la boîte
compteur === 5   // demande si la boîte contient 5
```

C'est l'erreur la plus fréquente des quatre séances suivantes. Elle revient dans les
sketches cassés de la séance 6.

Puis `&&` et `||`, sur un exemple qui se voit :

```js
if (mouseX > width / 2 && mouseY > height / 2) {
  background(200, 40, 40);
}
```

**Le booléen qui mémorise.** C'est ici que le type booléen cesse d'être un mot de
vocabulaire.

```js
let estAllume = false;

function setup() {
  createCanvas(800, 600);
}

function draw() {
  if (estAllume) {
    background(240);
  } else {
    background(20);
  }
  circle(mouseX, mouseY, 60);
}

function mousePressed() {
  estAllume = !estAllume;
}
```

Deux choses nouvelles, à nommer séparément :

- `!estAllume` — le contraire. Un interrupteur complet tient en une ligne.
- `mousePressed()` — une fonction qu'on écrit et que l'on n'appelle jamais soi-même.
  C'est p5 qui l'appelle, quand l'événement arrive. Première rencontre avec l'idée, elle
  sera reprise en séance 5.

**Erreur à provoquer.** Déplacer `estAllume = !estAllume;` dans `draw()` : l'écran
clignote soixante fois par seconde. `draw()` est rejoué, l'événement non. Rien ne distingue
mieux « à chaque image » de « quand ça arrive ».

**L'aléatoire.** `random(50, 200)` — et immédiatement le vrai enseignement, projeté côte à
côte :

```js
// du hasard sans décision
fill(random(255), random(255), random(255));

// du hasard décidé : une seule famille de bleus
fill(random(40, 90), random(90, 140), random(180, 240));
```

Le second est une image, le premier est du bruit. Le hasard n'est pas l'absence de choix :
c'est un choix qui porte sur un intervalle. Vera Molnár et Casey Reas en appui, deux
minutes chacun, pas plus.

**La panne annoncée** : `random()` écrit dans `draw()` est retiré à chaque image, et tout
grésille. Trois issues, à donner tout de suite : tirer les valeurs dans `setup()`, ou
appeler `noLoop()`, ou fixer `randomSeed(1)`.

## 1:05 — Atelier : la scène qui décide

> **Consigne.** Une scène qui contient au moins : un objet qui rebondit sur les bords, un
> état qui bascule au clic, et une couleur tirée au sort dans un intervalle choisi. La
> scène doit tenir dix secondes d'attention.

**Contraintes supplémentaires :**

- (a) Trois états au lieu de deux.
- (b) L'état bascule aussi tout seul, régulièrement — `if (frameCount % 120 === 0)`.
- (c) La même scène sans jamais écrire `else`.

## Devoir — 45 minutes

Deux humeurs de la même scène, basculées par une seule condition. Nommer chaque humeur en
un mot, et écrire les deux mots à côté du lien.

---

# Séance 4 — La boucle, et la multitude

**Notions** : `for`, compteur, boucles imbriquées, le tableau.

| Temps | Bloc |
|---|---|
| 0:00–0:15 | La reprise |
| 0:15–0:30 | Le manque — la grille à la main |
| 0:30–0:55 | La notion — la craie avant la machine |
| 0:55–1:05 | Pause |
| 1:05–1:45 | Atelier — le système |
| 1:45–2:00 | Le mur |

## 0:15 — Le manque

Projeter une grille de vingt colonnes sur quinze rangées de disques.

> **Consigne.** Écrivez-la.

Compter à voix haute avec la classe : trois cents lignes. Personne ne commence, et c'est
le but. Rouvrir alors la figure 4 de la dictée de la séance 1 — le damier — qui avait été
mise de côté pour exactement cette raison.

## 0:30 — La notion : la craie avant la machine

**Ne rien exécuter avant que le tableau soit rempli.** C'est le cœur de la séance : un
débutant qui n'a pas vu une boucle se dérouler pas à pas ne s'en fait aucune image
mentale, et recopie une formule qu'il ne relira jamais.

Écrire au tableau, sans lancer :

```js
for (let i = 0; i < 5; i = i + 1) {
  console.log(i);
}
```

Puis trois colonnes, remplies tour par tour avec la classe :

| `i` vaut | `i < 5` ? | ce qui s'affiche |
|---|---|---|
| 0 | oui | 0 |
| 1 | oui | 1 |
| 2 | oui | 2 |
| 3 | oui | 3 |
| 4 | oui | 4 |
| 5 | **non** | — la boucle s'arrête |

Les trois morceaux du `for`, nommés une fois le tableau rempli : **d'où l'on part**,
**jusqu'à quand on continue**, **ce qu'on fait entre deux tours**. Le troisième morceau
est exactement le geste de la séance 2.

Signaler que `i++` existe et veut dire la même chose — ils le liront ailleurs. Puis ne
plus jamais l'écrire.

Exécuter enfin. Puis passer à l'image :

```js
for (let x = 0; x < 800; x = x + 40) {
  circle(x, 300, 20);
}
```

Une ligne de disques. Faire changer le pas en direct : 40, puis 20, puis 10, puis 3.

**Les boucles imbriquées.** Ajouter une boucle autour de l'autre :

```js
function setup() {
  createCanvas(800, 600);
  background(20);
  noStroke();
  fill(240);

  for (let x = 0; x < width; x = x + 40) {
    for (let y = 0; y < height; y = y + 40) {
      circle(x, y, 20);
    }
  }
}
```

La grille apparaît. C'est le moment le plus spectaculaire des douze heures et il faut
l'exploiter comme tel : laisser le silence, puis modifier trois fois de suite, en direct,
sans rien expliquer entre deux.

```js
circle(x, y, x * 0.03);              // les tailles suivent la position
circle(x, y, 20 + random(-8, 8));    // un désordre borné
if ((x + y) % 80 === 0) {            // le damier de la séance 1, enfin écrit
  circle(x, y, 20);
}
```

**La portée, deuxième observation.** Après la boucle, écrire `console.log(x)` :
`x is not defined`. Le `x` déclaré dans le `for` n'existe pas en dehors du `for`. Écrire
le constat sous celui de la séance 2, sans encore le nommer :

> Ce qui est déclaré entre deux accolades n'existe pas en dehors d'elles.

**Le tableau.** Amené par un besoin : cinquante éléments qui ont chacun leur position, et
qui bougent chacun de leur côté.

```js
let positionsX = [];
let positionsY = [];

function setup() {
  createCanvas(800, 600);
  for (let i = 0; i < 50; i = i + 1) {
    positionsX.push(random(width));
    positionsY.push(random(height));
  }
}

function draw() {
  background(20, 40);
  for (let i = 0; i < positionsX.length; i = i + 1) {
    circle(positionsX[i], positionsY[i], 12);
    positionsY[i] = positionsY[i] + 2;
  }
}
```

Trois points à nommer : les crochets `[i]` qui désignent une case, `.length` qui donne le
nombre de cases, et **l'index qui commence à zéro** — la case 50 d'un tableau de 50 cases
n'existe pas.

Dire en une phrase que deux tableaux en parallèle ne sont pas ce qu'on écrirait avec plus
de temps, et que la manière propre existe. Ne pas la montrer.

**Erreur à provoquer** : remplacer `positionsX.length` par `51`. Aucun message d'erreur,
un cercle manquant que personne ne voit. `console.log(positionsX[50])` donne `undefined`.
C'est la panne silencieuse par excellence, celle qui coûtera le plus de temps en séance 6.

## 1:05 — Atelier : le système

Squelette fourni, deux boucles trouées.

> **Consigne.** Cinquante éléments qui tombent, et qui réapparaissent en haut quand ils
> sortent par le bas. Puis en faire une image : forme, couleur, densité, vitesse sont des
> décisions.

**Contraintes supplémentaires :**

- (a) Chaque élément a sa propre vitesse — un troisième tableau.
- (b) Les éléments réagissent à la position de la souris.
- (c) Le même résultat obtenu avec deux boucles imbriquées plutôt qu'avec un tableau.

## Devoir — 45 minutes

Trois affiches à partir de la même grille. **Une seule valeur change entre deux affiches.**
Poster les trois liens et écrire, à côté de chacun, la valeur qui a changé.

---

# Séance 5 — La fonction, et la portée pour de vrai

**Notions** : définir une fonction, paramètres, valeur de retour, portée globale et
locale, chargement des médias.

| Temps | Bloc |
|---|---|
| 0:00–0:15 | La reprise |
| 0:15–0:30 | Le manque — relire son propre code |
| 0:30–0:55 | La notion, et la portée |
| 0:55–1:05 | Pause |
| 1:05–1:35 | Atelier — refactorer son sketch |
| 1:35–1:50 | Les médias |
| 1:50–2:00 | Le lancement du rendu |

## 0:15 — Le manque

Chacun rouvre son sketch de la séance 4 et écrit sur papier, en une phrase, ce que fait la
ligne 14. Cinq minutes. La plupart n'y arrivent pas — c'est leur propre code, écrit sept
jours plus tôt.

Deuxième question : combien de blocs quasi identiques à deux ou trois valeurs près ?
Compter, à main levée.

## 0:30 — La notion

```js
function dessinerArbre(x, y, hauteur) {
  stroke(120, 80, 50);
  strokeWeight(hauteur * 0.1);
  line(x, y, x, y - hauteur);

  noStroke();
  fill(60, 140, 70);
  circle(x, y - hauteur, hauteur * 0.8);
}
```

Trois choses distinctes, que les débutants confondent systématiquement, à nommer
séparément et dans cet ordre :

- **Définir** — `function dessinerArbre(...) { }`. Écrire la recette. Rien ne se passe.
- **Appeler** — `dessinerArbre(100, 500, 200);`. Exécuter la recette, maintenant.
- **Les paramètres** — `x`, `y`, `hauteur`. Les cases vides de la recette, remplies au
  moment de l'appel, et différentes à chaque appel.

**Erreur à provoquer** : définir la fonction et ne jamais l'appeler. Écran vide, aucune
erreur. Deuxième panne silencieuse du cours.

Puis appeler trois fois avec trois jeux de valeurs. Puis appeler dans une boucle :

```js
function draw() {
  background(200, 220, 240);
  for (let i = 0; i < 12; i = i + 1) {
    dessinerArbre(random(width), height, random(80, 260));
  }
}
```

La fonction et la boucle se rencontrent. C'est là que le cours se referme sur lui-même :
tout ce qui a été vu en quatre séances tient dans ces cinq lignes.

**La valeur de retour.**

```js
function surface(largeur, hauteur) {
  return largeur * hauteur;
}
```

Une fonction qui **fait** quelque chose — elle dessine — et une fonction qui **donne**
quelque chose — elle retourne une valeur qu'on peut ranger dans une variable. Puis le
retournement : `random()` et `dist()` sont des fonctions qui donnent, et ils s'en servent
depuis quatre séances sans l'avoir su.

## La portée, frontalement

Trois démonstrations, dans cet ordre. C'est le seul bloc du cours qui va de la règle vers
l'exemple, et il est court.

**1. Ce qui est déclaré dans une fonction n'existe pas en dehors.**

```js
function dessinerArbre(x, y, hauteur) {
  let tronc = hauteur * 0.1;
  line(x, y, x, y - hauteur);
}

function draw() {
  dessinerArbre(100, 500, 200);
  console.log(tronc);          // ReferenceError: tronc is not defined
}
```

**2. Le paramètre est une copie.**

```js
let compteur = 0;

function augmenter(nombre) {
  nombre = nombre + 1;
}

function setup() {
  augmenter(compteur);
  console.log(compteur);       // 0, et non 1
}
```

Celle-ci est rarement enseignée à ce niveau, et elle explique la moitié des « ça ne marche
pas » de la séance 6. La fonction a reçu la valeur, pas la boîte.

**3. Le global est lisible partout, et c'est le problème.**

```js
let x = 0;                     // la position du personnage

function dessinerFond() {
  for (x = 0; x < width; x = x + 40) {   // le `let` a été oublié
    line(x, 0, x, height);
  }
}
```

Le personnage est téléporté au bord droit par une fonction qui dessine le fond. Aucun
message d'erreur. Laisser la classe chercher deux minutes avant de montrer le `let`
manquant.

**La règle, écrite au tableau sous les deux constats des séances 2 et 4** — trois
observations qui n'en font qu'une :

> Une variable n'existe qu'entre les accolades où elle est déclarée. Elle se déclare au
> plus près de là où elle sert. Un `for` déclare toujours son compteur avec `let`.

## 1:05 — Atelier : refactorer son sketch

> **Consigne.** Refactorer son propre sketch de la séance 4 en au moins deux fonctions,
> dont une avec paramètres. **Le résultat à l'écran doit être identique.** C'est la
> définition du refactoring, et elle se vérifie à l'œil, côte à côte avec l'ancien lien.

**Contraintes supplémentaires :**

- (a) `draw()` ne contient plus que des appels de fonctions, aucun dessin direct.
- (b) Écrire une fonction qui retourne une valeur, et s'en servir.
- (c) Obtenir une composition entièrement différente en ne changeant qu'une seule ligne
  d'appel.

## 1:35 — Les médias

```js
let photo;
let bruitage;

function preload() {
  photo = loadImage("plan.jpg");
  bruitage = loadSound("porte.mp3");
}

function setup() {
  createCanvas(800, 600);
  image(photo, 0, 0, width, height);
}
```

Pourquoi `preload()` existe, en une phrase et pas une de plus : charger un fichier prend
du temps, et `setup()` n'attend pas.

Trois pannes à annoncer **avant** qu'elles arrivent, parce qu'elles arriveront :

- Le fichier n'a pas été téléversé dans le sketch — il ne suffit pas qu'il soit sur le
  bureau.
- Le nom ne correspond pas, à la majuscule près.
- Le son ne démarre pas tant que l'utilisateur n'a pas cliqué au moins une fois. C'est une
  règle des navigateurs, pas une erreur de code.

Puis `tint()`, `image()` avec quatre arguments, `bruitage.play()`.

## 1:50 — Le lancement du rendu

Relire l'énoncé du rendu et ressortir la grille de notation distribuée en séance 1.
Chacun écrit trois lignes sur papier, ramassées avant de sortir :

1. Ce qu'on voit.
2. Ce que le spectateur peut faire.
3. Ce qui change quand il le fait.

C'est le seul filet contre les projets impossibles, et il coûte cinq minutes.

## Devoir — 45 minutes

Rassembler et téléverser les médias du rendu, et poster le sketch de départ — même vide,
même s'il ne montre qu'un fond.

---

# Séance 6 — Assembler, et montrer

Aucune notion nouvelle.

| Temps | Bloc |
|---|---|
| 0:00–0:30 | Le bug d'un autre |
| 0:30–0:55 | Atelier de finition |
| 0:55–1:05 | Pause |
| 1:05–1:20 | Le test en silence |
| 1:20–2:00 | La projection |

## 0:00 — Le bug d'un autre

Six sketches cassés, préparés à l'avance, un par binôme. Chacun contient **une seule**
panne, prise dans les six types rencontrés pendant le cours.

| # | La panne | Ce qu'elle apprend |
|---|---|---|
| 1 | `Circle(400, 300, 100)` | La casse compte ; la machine ne devine pas |
| 2 | `circle(400, 300)` | Un argument manquant, et un message qui le dit |
| 3 | `if (compteur = 5)` | `=` range, `===` compare |
| 4 | `let positionX = 0` déclaré dans `draw()` | Ce qui est dedans ne survit pas |
| 5 | `for (let i = 0; i < 5; i = i - 1)` | La condition qui ne devient jamais fausse — l'onglet gèle |
| 6 | Une fonction définie et jamais appelée | La panne silencieuse |

Quinze minutes pour trouver. Puis chaque binôme raconte en une minute **comment** il a
trouvé — pas ce qu'il a trouvé. C'est la méthode qui est en jeu, et c'est elle qu'ils
emporteront.

Lire le code d'un autre est ce qui consolide le plus vite la lecture de son propre code.
Cela désamorce aussi l'idée que l'erreur est une faute personnelle : les six sketches
viennent du professeur.

## 1:05 — Le test en silence

Chaque sketch est essayé par un autre binôme, sur une autre machine, **sans un mot de son
auteur**. L'auteur regarde et note ce qu'il voit sans intervenir : où l'autre hésite, ce
qu'il essaie, ce qu'il ne trouve pas.

C'est le premier critère de la grille de notation, et c'est ici qu'il se joue. Vingt
minutes restent ensuite pour corriger ce que le test a révélé.

## 1:20 — La projection

Quatre minutes par étudiant :

- Montrer. Le sketch tourne, on le laisse tourner.
- Expliquer **une** décision de code — pas le code entier, une décision.
- Dire ce qu'il volerait chez un autre.

Aucune conclusion de cours après la dernière présentation.

---

# Le rendu noté — 20 points

**L'objet.** Une machine à images : un sketch qui tourne dans un navigateur, qui tient
trente secondes d'attention, et qui répond à au moins une action du spectateur.

Ni un jeu, ni un site, ni une histoire. Une image qui se fabrique et qui écoute.

| Critère | Points |
|---|---|
| Le sketch tourne sans erreur sur une machine qui n'est pas la sienne | 4 |
| Variable, condition, boucle et fonction écrites par l'étudiant, présentes parce qu'elles servent | 5 |
| Le spectateur agit, et l'objet répond de façon lisible | 4 |
| Le code se lit : noms explicites, une fonction par intention | 3 |
| Une décision d'auteur assumée et défendue à l'oral | 4 |

Le premier critère impose le test sur la machine d'un autre, séance 6. Le quatrième note
la lisibilité, pas l'élégance : un code long et clair vaut mieux qu'un code court et
compact. Le second ne récompense pas l'accumulation — quatre notions plaquées sans raison
valent moins que quatre notions qui servent.

La grille est distribuée en séance 1 et affichée les six séances.

---

# Les références projetées

Deux à trois minutes chacune, jamais plus. Elles ne sont pas de la culture générale : elles
servent à faire admettre une idée précise, et on les coupe dès que l'idée est passée.

| Séance | Référence | Ce qu'elle montre |
|---|---|---|
| 1 | Sol LeWitt, *Wall Drawing #118* (1971), et deux exécutions différentes | L'instruction est l'œuvre ; l'exécution varie |
| 1 | Moniker, *The Human Fax Machine Experiment* | Une chaîne d'exécutants littéraux, et ce qui se dégrade en route |
| 2 | Zach Lieberman, ses sketches quotidiens | Un geste minuscule par jour, tenu des années |
| 3 | Vera Molnár, ses séries de carrés | Le hasard borné comme décision d'auteur |
| 3 | Casey Reas, la série *Process* | Des règles simples, un résultat que l'auteur ne prévoit pas |
| 4 | Saskia Freeke, *Daily Art* | La grille comme matériau, pas comme gabarit |
| 5 | Tyler Hobbs, *Fidenza* | Une fonction appelée mille fois |

---

# Annexe — Le protocole de panne

*À imprimer en autant d'exemplaires que d'étudiants, et à afficher au mur.*

**1. Lire le message en entier**, et le numéro de ligne. Le numéro indique où la machine a
buté — la faute est souvent une ligne plus haut.

**2. Dire à voix haute** ce que cette ligne est censée faire.

**3. Afficher la valeur.** `console.log(maVariable)`. Vérifier ce que la variable contient
vraiment, pas ce qu'on croit.

**4. Retirer** des lignes jusqu'à ce que ça remarche, puis en remettre une à la fois.

**Règle des dix minutes** : au-delà, demander. D'abord au binôme voisin.

**Toutes les pannes n'affichent pas de message.** Un code qui tourne et ne fait rien est
une panne comme une autre. Les six causes les plus fréquentes :

- une majuscule là où il n'en faut pas ;
- un argument manquant ;
- `=` écrit à la place de `===` ;
- une variable déclarée dans `draw()` alors qu'elle devrait vivre au-dessus ;
- une fonction définie et jamais appelée ;
- un index de tableau qui n'existe pas.

---

# Annexe — Aide-mémoire

*Une page. Ne contient que ce qui a été vu en cours.*

```js
// Le cadre
function setup() { }          // une fois, au démarrage
function draw() { }           // 60 fois par seconde
function mousePressed() { }   // quand la souris est enfoncée

createCanvas(800, 600);
width, height                 // les dimensions du canvas

// Dessiner — la couleur se déclare AVANT la forme
background(20);
fill(230, 60, 40);            // rouge, vert, bleu, de 0 à 255
noFill();
stroke(255);
noStroke();
strokeWeight(4);

circle(x, y, diametre);
ellipse(x, y, largeur, hauteur);
rect(x, y, largeur, hauteur);
square(x, y, cote);
line(x1, y1, x2, y2);
triangle(x1, y1, x2, y2, x3, y3);

// Les variables
let positionX = 0;            // un nombre
let titre = "Sans titre";     // du texte
let estAllume = false;        // un booléen
let positions = [];           // un tableau

positionX = positionX + 2;    // prends, ajoute, remets
positions.push(12);           // ajoute une case
positions.length              // combien de cases
positions[0]                  // la première case

mouseX, mouseY                // fournies par le monde
frameCount                    // le numéro de l'image en cours

// Décider
if (positionX > width) {
  vitesse = -vitesse;
} else {
  fill(240);
}
// >  <  >=  <=  ===  !==      &&  ||  !

// Répéter
for (let i = 0; i < 10; i = i + 1) {
  circle(i * 40, 300, 20);
}

// Nommer un morceau
function dessinerArbre(x, y, hauteur) {
  line(x, y, x, y - hauteur);
}
dessinerArbre(100, 500, 200);

function surface(largeur, hauteur) {
  return largeur * hauteur;
}

// Le hasard, avec des bornes choisies
random(50, 200);
randomSeed(1);

// Les médias
let photo;
function preload() {
  photo = loadImage("plan.jpg");
}
image(photo, 0, 0, width, height);
```
