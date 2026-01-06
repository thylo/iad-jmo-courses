# README Enseignant - Examen Jeu de Géographie

Guide rapide pour organiser et évaluer l'examen.

---

## Vue d'ensemble de l'examen

**Format :** 2h de code + 10 min de défense orale par étudiant
**Objectif :** Évaluer la compréhension du DOM, des événements, et de la temporalité en JavaScript
**Accès autorisé :** MDN, Stack Overflow, ChatGPT, Claude, tout internet

**⚠️ Philosophie de l'examen :**
L'IA peut générer le code, mais pas l'expliquer ni le modifier. La défense orale est le vrai test de compréhension.

---

## Fichiers fournis aux étudiants

Dans le dossier `examen/` :

1. **examen.html** - Structure HTML complète avec tous les panneaux
2. **examen.css** - Styles complets (dark theme, animations)
3. **examen.js** - Fichier quasi-vide (juste un commentaire)
4. **worldLow.svg** - Carte du monde avec attributs `id` et `data-title`
5. **consignes.mdx** - Consignes détaillées pour les étudiants
6. **revision.mdx** - Document de révision

### Fichiers pour l'enseignant uniquement

7. **guide-defense-orale.md** - Questions et grille d'évaluation
8. **README-ENSEIGNANT.md** - Ce fichier

---

## Préparation de l'examen

### 1. Créer le CodePen de base

1. Créer un nouveau CodePen
2. Coller le contenu de `examen.html` (body uniquement)
3. Intégrer le SVG `worldLow.svg` dans la balise `<svg>`
4. Coller le contenu de `examen.css`
5. Laisser le JavaScript vide
6. Sauvegarder et partager le lien avec les étudiants

**IMPORTANT :** Vérifier que :
- Les pays de la carte ont bien les attributs `id` et `data-title`
- Le hover sur les pays fonctionne (changement de couleur)
- Les panneaux sont bien cachés au départ (classe `.hidden`)

### 2. Distribuer les consignes

Partager `consignes.mdx` avec les étudiants au moins 1 semaine avant l'examen.

Insister sur :
- La défense orale obligatoire
- Les modifications en direct
- L'importance de comprendre le code (même généré par IA)

---

## Déroulement le jour de l'examen

### Phase 1 : Code (2 heures)

**Instructions orales à donner :**

> "Vous avez 2 heures pour écrire le JavaScript qui rend le jeu fonctionnel. Vous avez accès à tout internet, y compris ChatGPT et Claude. **Mais attention : vous devrez modifier votre code en direct devant moi. Si vous ne comprenez pas ce que vous avez écrit, vous aurez zéro.**"

**Pendant les 2 heures :**
- Circuler et observer qui code vraiment vs qui copie-colle
- Noter mentalement les red flags (voir guide-defense-orale.md)
- Répondre aux questions techniques UNIQUEMENT (pas de debugging de code)

**Fin des 2 heures :**
- Les étudiants envoient le lien CodePen
- Ordre de passage pour les défenses orales

### Phase 2 : Défenses orales (10 min/étudiant)

**Structure :**
1. Explication générale (2 min)
2. Questions d'explication (3 min)
3. Modifications en direct (5 min)

Voir `guide-defense-orale.md` pour :
- Liste complète des questions
- 4 niveaux de modifications (facile → expert)
- Grille d'évaluation détaillée
- Red flags de triche

---

## Barème global (100 points)

### 1. Code fonctionnel (30 points)

Vérifier que le jeu fonctionne du début à la fin :
- Écran d'accueil → cache au clic : 2 pts
- Sélection aléatoire d'un pays : 4 pts
- Modal avec nom du pays : 4 pts
- Modal disparaît après 5 sec : 2 pts
- Décompte numérique (setInterval) : 3 pts
- Barre fluide (requestAnimationFrame) : 5 pts
- Clic sur bon pays → nouveau pays : 5 pts
- Game over avec score : 3 pts
- Retrait de temps : 2 pts

### 2. Compréhension (40 points)

**Partie 1 : Explication (20 pts)**
- Logique générale : 5 pts
- Sélection éléments : 5 pts
- Gestion événements : 5 pts
- Gestion temps (setInterval vs requestAnimationFrame) : 5 pts

**Partie 2 : Modifications en direct (20 pts)**
- Localiser/modifier une valeur : 5 pts
- Ajouter console.log : 5 pts
- Modifier un comportement : 5 pts
- Compréhension globale : 5 pts

### 3. Qualité du code (20 points)

- Noms de variables clairs : 5 pts
- Indentation/lisibilité : 5 pts
- Utilisation appropriée des outils : 5 pts
- Structure logique : 5 pts

### 4. Autonomie (10 points)

- Utilisation de la console : 3 pts
- Tests progressifs : 3 pts
- Résolution autonome : 4 pts

---

## Gestion de l'IA (ChatGPT, Claude)

### Position officielle

✅ **Autorisé et encouragé** pour :
- Générer du code
- Déboguer des erreurs
- Comprendre des concepts
- Trouver la syntaxe

❌ **Inacceptable** :
- Copier-coller sans lire
- Ne pas pouvoir expliquer le code
- Ne pas pouvoir le modifier

### Comment détecter le copier-coller bête

**Pendant le code :**
- L'étudiant ne teste pas, juste copie-colle
- Le code apparaît d'un coup, complet
- L'étudiant ne lit pas le code qu'il colle

**Pendant la défense :**
- Ne peut pas localiser une variable simple
- Découvre son code en même temps que vous
- Panique face à une modification basique

**Actions à prendre :**
- Note sévère sur "Compréhension" (0-10/40)
- Note moyenne sur "Code fonctionnel" (le code marche mais c'est pas le sien)
- Note faible sur "Qualité" et "Autonomie"

**Résultat typique :**
- Code fonctionnel : 25/30 (ça marche)
- Compréhension : 5/40 (ne comprend rien)
- Qualité : 10/20 (code propre mais pas le sien)
- Autonomie : 2/10 (pas autonome)
- **Total : 42/100 = échec**

---

## Variations (optionnel)

Pour éviter que tous les étudiants aient exactement le même examen, vous pouvez varier :

### Variations simples (temps/délais)

- Étudiant A : 20 secondes, retirer 2 secondes
- Étudiant B : 25 secondes, retirer 3 secondes
- Étudiant C : 15 secondes, retirer 1 seconde

### Variations moyennes (comportement)

- Étudiant A : Modal disparaît après 5 secondes
- Étudiant B : Modal disparaît après 3 secondes
- Étudiant C : Modal reste visible jusqu'au prochain pays

### Variations avancées (features)

- Étudiant A : Barre verte → rouge quand < 5 secondes
- Étudiant B : Timer clignote quand < 5 secondes
- Étudiant C : Bouton "Rejouer" sur le game over

**Avantage :** Force l'adaptation du code généré par l'IA

---

## FAQ enseignant

**Q : Et si un étudiant rend un code parfait mais ne peut rien expliquer ?**
R : C'est prévu. Note faible sur Compréhension (40 pts) = échec probable. Le barème est fait pour ça.

**Q : Combien de temps pour les défenses orales ?**
R : 10 min/étudiant. Pour 20 étudiants = 3h20. Prévoir une pause.

**Q : Que faire si un étudiant avoue avoir tout copié de ChatGPT ?**
R : C'est autorisé ! Mais il doit quand même pouvoir expliquer et modifier. S'il ne peut pas → note en conséquence.

**Q : Un étudiant demande de l'aide pendant le code, je fais quoi ?**
R : Questions techniques OK ("Comment je sélectionne un élément ?"). Debugging NON ("Pourquoi mon code marche pas ?").

**Q : Le code d'un étudiant ne marche pas du tout, quelle note ?**
R : Code fonctionnel : 0-10/30. Mais s'il comprend ce qu'il a essayé de faire, il peut avoir des points en Compréhension.

**Q : Combien de modifications demander en défense ?**
R : 2-3 modifications, de difficulté croissante. Adaptez selon le niveau de l'étudiant.

---

## Checklist jour de l'examen

### Avant

- [ ] CodePen de base créé et testé
- [ ] Lien CodePen partagé avec les étudiants
- [ ] Consignes distribuées et lues
- [ ] Guide de défense orale imprimé/ouvert
- [ ] Grille d'évaluation prête

### Pendant (phase code)

- [ ] Annoncer le timing (2h)
- [ ] Rappeler la défense orale obligatoire
- [ ] Circuler et observer
- [ ] Noter mentalement les red flags

### Pendant (défenses orales)

- [ ] 10 min par étudiant, chronomètre
- [ ] 2-3 questions d'explication
- [ ] 2-3 modifications en direct
- [ ] Remplir la grille pendant la défense

### Après

- [ ] Compiler les notes
- [ ] Feedback individuel si demandé

---

## Conseils pédagogiques

### Soyez juste mais ferme

- L'IA est autorisée → ne pas pénaliser son utilisation
- Mais la compréhension est obligatoire → pénaliser le copier-coller bête

### Adaptez les questions

- Étudiant faible : questions niveau 1
- Étudiant moyen : questions niveau 2
- Étudiant fort : questions niveau 3-4

### Valorisez la démarche

- Un étudiant qui a codé lui-même avec des bugs > un code parfait copié-collé
- Un étudiant qui comprend son code imparfait > un étudiant qui ne comprend pas son code parfait

### Soyez transparent

- Expliquez pourquoi la défense orale compte 40%
- Montrez que le système est conçu pour détecter la triche
- Encouragez l'IA comme outil d'apprentissage, pas de triche

---

**Bon courage pour l'examen !**

Si questions ou problèmes, référez-vous au `guide-defense-orale.md` pour plus de détails.