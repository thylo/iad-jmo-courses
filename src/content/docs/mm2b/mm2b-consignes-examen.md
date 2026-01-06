# Consignes d'examen — MM2B : Site web narratif pour enfants

## Objectif

Vous allez créer un site web narratif destiné aux enfants. Ce sera un travail de groupe qui me permettra de voir ce que vous avez retenu du cours HTML/CSS.

## Format

Vous travaillez en groupe (3-4 personnes maximum). Le livrable est un site web qui fonctionne, soit hébergé en ligne, soit remis dans un dossier ZIP.

Chaque membre du groupe viendra me présenter individuellement une partie du code qu'il a réalisée (environ 5 min par personne). Je vous poserai des questions pour vérifier que vous comprenez ce que vous avez fait.



## Exigences techniques

### Code HTML/CSS

Votre site doit mettre en pratique ce qu'on a vu ensemble. Ça veut dire du HTML sémantique (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`), du code proprement indenté avec des noms de classes clairs, et du CSS organisé dans un fichier séparé sans répétitions inutiles.

Pour la mise en page, vous devez utiliser Flexbox ou Grid (au moins l'un des deux). Le site doit être responsive et fonctionner aussi bien sur mobile que sur desktop. Pensez à la typographie (choix de polices, tailles, hiérarchie) et aux couleurs (une palette cohérente qui parle aux enfants).

### Contraintes

Pas de frameworks CSS type Bootstrap ou Tailwind. Pas de JavaScript non plus, sauf si vous m'en parlez avant. On reste en CSS pur. Pensez à ajouter des commentaires dans votre code pour expliquer les sections importantes.



## Public cible : enfants

Votre site raconte une histoire pour des enfants entre 6 et 12 ans. Ça change tout : le vocabulaire doit rester simple, la mise en page claire, les couleurs vives. La navigation doit être intuitive, pas de menus cachés ou de structures compliquées. Privilégiez le visuel (images, illustrations) sur le texte.

Le récit peut prendre plusieurs formes : une histoire courte découpée en chapitres, un conte où on choisit son chemin, une exploration d'un univers (les animaux de la forêt, un voyage dans l'espace), ou même un parcours pédagogique ludique. Ce qui compte, c'est que l'intention narrative soit claire.



## Critères d'évaluation

### 1. Qualité du code HTML/CSS (40%)

Je regarde si votre code est lisible et bien indenté, si vos noms de classes sont explicites (pas de `.box1`, `.truc`, `.container2`), si la structure HTML est sémantique, et si le CSS est organisé sans répétitions inutiles. Le responsive doit fonctionner.

Je ne demande pas un code parfait ou ultra-optimisé. Pas besoin d'architecture CSS complexe ou d'animations avancées. Juste du code propre et compréhensible.

### 2. Utilisation des concepts du cours (30%)

Vous devez utiliser ce qu'on a vu ensemble : HTML sémantique, Flexbox ou Grid, responsive design avec media queries, typographie réfléchie, gestion des couleurs, variables CSS..

Pendant la présentation, vous devez pouvoir m'expliquer pourquoi vous avez choisi telle balise HTML, comment fonctionne votre layout CSS, et pourquoi vous avez opté pour telle approche responsive.

### 3. Originalité et intention narrative (20%)

Est-ce que le site raconte vraiment une histoire ? Est-ce que l'univers visuel est cohérent ? Avez-vous réfléchi à l'expérience de l'enfant qui va le visiter ? Est-ce que votre projet se démarque d'un template classique ?

### 4. Compréhension individuelle (10%)

Lors de la présentation, vous me montrez une partie du code que vous avez écrite vous-même. Vous m'expliquez ce qu'elle fait, ligne par ligne. Je vous pose des questions pour vérifier que vous comprenez.

Règle simple : si vous ne pouvez pas expliquer le code que vous avez utilisé, ne l'utilisez pas.



## Répartition du travail

Chaque membre du groupe doit coder une partie identifiable du projet. Par exemple : une personne fait la page d'accueil et la navigation, une autre les chapitres 1 et 2, une autre le chapitre 3 et le footer, et la dernière s'occupe des styles globaux et du responsive.

Pendant la présentation, vous devez clairement identifier ce que vous avez codé vous-même.

### Utilisation de l'IA

Vous avez le droit d'utiliser ChatGPT, Claude ou autre. Mais vous devez comprendre et adapter le code généré. Si le code est illisible, demandez à l'IA de le clarifier. Ne collez pas du code que vous ne comprenez pas.



## Conseils pratiques

### Organisation du groupe

Commencez par définir l'histoire ensemble, en brainstorming. Ensuite, créez une maquette simple (croquis, wireframe) pour visualiser le projet. Répartissez-vous les pages ou sections (qui fait quoi). Mettez-vous d'accord sur des conventions : nommage des classes, structure des fichiers. Et surtout, testez régulièrement ensemble pour vérifier que tout fonctionne.

### Structure de fichiers recommandée

```
mon-projet/
├── index.html
├── page2.html
├── page3.html
├── css/
│   └── style.css
├── images/
│   ├── hero.jpg
│   └── ...
└── README.txt (qui a fait quoi)
```

### Code lisible

**Bon exemple :**
```css
.section-histoire {
  display: flex;
  gap: 2rem;
  padding: 2rem;
  background-color: #f0f8ff;
}

.section-histoire__titre {
  font-size: 2rem;
  color: #2c3e50;
}
```

**Mauvais exemple :**
```css
.box1 { display: flex; gap: 2rem; padding: 2rem; background-color: #f0f8ff; }
.t1 { font-size: 2rem; color: #2c3e50; }
```



## Échéances

**Date de rendu :** [À compléter]

**Présentation orale :** [À compléter]

Vous remettez soit l'URL du site hébergé (Netlify, GitHub Pages, etc.), soit un fichier ZIP contenant tout le projet. Ajoutez un fichier README.txt qui précise qui a codé quoi.

## Présentation orale

La présentation se déroule en deux temps. D'abord, vous présentez le concept narratif ensemble (5 min). Ensuite, chaque personne présente individuellement la partie qu'elle a codée (10-15 min par personne). Vous montrez votre code, vous l'expliquez, et je vous pose des questions.

Exemples de questions que je poserai : "Pourquoi as-tu utilisé Flexbox ici plutôt que Grid ?", "Comment fonctionne ce media query ?", "Que fait ce sélecteur CSS ?", "Pourquoi as-tu choisi cette balise HTML ?"

Ce que j'évalue : vous comprenez votre code, vous pouvez expliquer vos choix, vous êtes capable d'identifier et de corriger un bug simple.



## En résumé

Ce qui compte : du code lisible et bien structuré, une utilisation correcte des concepts HTML/CSS vus en cours, une histoire adaptée aux enfants, et surtout que vous compreniez ce que vous avez codé.

Ce qui ne compte pas : le nombre de pages, la complexité technique excessive, la perfection visuelle.

Questions ? Posez-les maintenant ou par email.