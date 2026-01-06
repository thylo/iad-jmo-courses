# Guide de défense orale - Jeu de géographie

Document pour l'enseignant : questions et modifications à demander pendant la défense orale.

---

## Structure de la défense (10 minutes)

**Partie 1 : Explication du code (5 minutes)**
- Demander à l'étudiant d'expliquer son code ligne par ligne
- Poser des questions ciblées sur les choix techniques

**Partie 2 : Modifications en direct (5 minutes)**
- Demander 2-3 modifications à faire en direct
- Observer la capacité à naviguer et comprendre le code

---

## Questions d'explication

### Sur la structure générale

- "Explique-moi le déroulement général de ton jeu, en suivant le code"
- "Pourquoi tu as organisé ton code comme ça ?"
- "Quelle partie du code se lance en premier ?"

### Sur la sélection d'éléments

- "Pourquoi tu as utilisé `querySelector` ici et `querySelectorAll` là ?"
- "Qu'est-ce qui se passe si l'élément que tu cherches n'existe pas ?"
- "Comment tu sais que tu as bien sélectionné tous les pays ?"

### Sur les événements

- "Pourquoi tu as besoin d'`addEventListener` ici ?"
- "Qu'est-ce qui se passe quand on clique sur le bouton ?"
- "Comment tu vérifies que c'est le bon pays qui a été cliqué ?"

### Sur la gestion du temps

- "Pourquoi tu as utilisé `setInterval` pour le décompte ?"
- "Pourquoi tu as utilisé `requestAnimationFrame` pour la barre ?"
- "Quelle est la différence entre `setInterval` et `requestAnimationFrame` ?"
- "Comment tu arrêtes le décompte quand le jeu est fini ?"
- "Comment tu calcules le pourcentage de la barre de progression ?"

### Questions pièges (pour détecter la non-compréhension)

- "Pourquoi tu as utilisé `Date.now()` et pas `performance.now()` ?" (piège : les deux marchent, mais s'ils ne savent pas ce que c'est...)
- "Qu'est-ce qui se passe si deux joueurs cliquent en même temps ?" (piège : le jeu est solo)
- "Pourquoi tu n'as pas utilisé `innerHTML` pour modifier le nom du pays ?" (réponse attendue : `textContent` suffit, pas besoin de HTML)

---

## Modifications en direct

### Niveau 1 : Modifications de valeurs (facile)

**Objectif :** Vérifier que l'étudiant sait localiser une variable dans son code.

1. **Changer le temps total**
   - "Change le temps total du jeu de 20 à 30 secondes"
   - Attendu : Modifier la variable initiale du temps (ex: `let tempsTotal = 20` → `let tempsTotal = 30`)

2. **Changer le délai de la modal**
   - "Fais en sorte que la modal disparaisse après 3 secondes au lieu de 5"
   - Attendu : Modifier le `setTimeout` (ex: `setTimeout(..., 5000)` → `setTimeout(..., 3000)`)

3. **Changer le temps retiré**
   - "Au lieu de retirer 2 secondes quand on trouve un pays, retire 3 secondes"
   - Attendu : Modifier la valeur de retrait de temps (ex: `tempsRestant -= 2` → `tempsRestant -= 3`)

### Niveau 2 : Ajout de débogage (moyen)

**Objectif :** Vérifier que l'étudiant comprend le flux du code.

4. **Afficher le nom du pays cliqué**
   - "Ajoute un `console.log` qui affiche le nom du pays sur lequel l'utilisateur clique"
   - Attendu : Ajouter `console.log(path.getAttribute('data-title'))` dans le gestionnaire de clic

5. **Afficher le temps écoulé**
   - "Affiche dans la console combien de temps s'est écoulé depuis le début du jeu"
   - Attendu : Ajouter `console.log(Date.now() - tempsDebut)` quelque part

6. **Vérifier la barre de progression**
   - "Affiche le pourcentage de la barre de progression dans la console à chaque frame"
   - Attendu : Ajouter `console.log(pourcentage)` dans la fonction `requestAnimationFrame`

### Niveau 3 : Modifications de comportement (difficile)

**Objectif :** Vérifier la compréhension profonde et la capacité d'adaptation.

7. **Barre de progression rouge en fin de partie**
   - "Fais en sorte que la barre devienne rouge quand il reste moins de 5 secondes"
   - Attendu : Ajouter une condition dans `requestAnimationFrame` :
     ```javascript
     if (tempsRestant < 5) {
       barreProgression.style.backgroundColor = 'red';
     }
     ```

8. **Feedback visuel sur le pays trouvé**
   - "Ajoute une classe CSS `correct` au pays quand il est trouvé"
   - Attendu : Ajouter `path.classList.add('correct')` quand le bon pays est cliqué

9. **Désactiver les clics après la fin**
   - "Empêche l'utilisateur de cliquer sur les pays après que le jeu soit terminé"
   - Attendu : Utiliser une variable booléenne `jeuActif` pour bloquer les clics

10. **Afficher le pays actuel dans le timer**
    - "Affiche le nom du pays à trouver à côté du timer au lieu de dans la modal"
    - Attendu : Modifier `timerElement.textContent` pour inclure le nom du pays

### Niveau 4 : Débogage (expert)

**Objectif :** Tester la capacité à diagnostiquer et corriger.

11. **"Mon code ne marche pas, aide-moi"**
    - Introduisez une erreur volontaire (ex: `querySelector` au lieu de `querySelectorAll`)
    - Demandez à l'étudiant de trouver et corriger l'erreur

12. **"La barre ne se vide pas progressivement"**
    - Si l'étudiant a utilisé `setInterval` au lieu de `requestAnimationFrame`, demandez-lui de corriger

---

## Grille d'évaluation rapide

### Partie 1 : Explication (20 points)

| Critère | 0-2 pts | 3-4 pts | 5 pts |
|---------|---------|---------|-------|
| Logique générale | Ne peut pas expliquer | Explication partielle | Explication claire |
| Sélection éléments | Confus sur querySelector | Comprend la sélection | Explique les différences |
| Gestion événements | Ne sait pas pourquoi addEventListener | Comprend les événements | Explique le flux complet |
| Gestion du temps | Confond setInterval/requestAnimationFrame | Comprend la différence | Explique pourquoi chaque choix |

### Partie 2 : Modifications (20 points)

| Critère | 0-2 pts | 3-4 pts | 5 pts |
|---------|---------|---------|-------|
| Localiser une valeur | Ne trouve pas la variable | Trouve après aide | Trouve rapidement |
| Ajouter console.log | Ne sait pas où placer | Place au mauvais endroit | Place correctement |
| Modifier comportement | Code casse après modification | Fonctionne partiellement | Fonctionne parfaitement |
| Compréhension globale | Perdu dans son propre code | Navigue avec hésitation | Navigue avec assurance |

---

## Red flags (indices de copier-coller)

⚠️ **Signes qu'un étudiant ne comprend pas son code :**

1. **Ne peut pas localiser une variable simple**
   - "Change le temps à 30 secondes" → "Euh... c'est où ça ?"

2. **Lit le code pour la première fois**
   - Scrolle lentement, découvre le code en même temps que vous

3. **Ne peut pas expliquer une ligne spécifique**
   - "Qu'est-ce que fait cette ligne ?" → "Je sais pas, ça marchait comme ça"

4. **Vocabulaire inexact ou absent**
   - Appelle une fonction "le truc qui fait ça"
   - Ne connaît pas les termes "événement", "sélecteur", "callback"

5. **Code trop complexe pour son niveau**
   - Utilise des patterns avancés (closures complexes, async/await, etc.)
   - Code parfaitement optimisé avec des commentaires en anglais

6. **Panique face à une modification simple**
   - "Change 20 en 30" → commence à tout réécrire ou demande de l'aide à l'IA

---

## Conseils pour l'enseignant

### Adapter les questions selon le niveau

- **Étudiant faible :** Questions niveau 1 + explication basique
- **Étudiant moyen :** Questions niveau 1-2 + une question niveau 3
- **Étudiant fort :** Questions niveau 2-3 + une question niveau 4

### Si l'étudiant bloque

- Ne donnez pas la réponse immédiatement
- Posez des questions guidantes : "Où est-ce que tu stockes le temps ?" "Comment tu affiches le timer ?"
- Si toujours bloqué après 2 minutes → note réduite sur ce critère

### Si suspicion de triche

- Posez une question piège
- Demandez une modification qui casse le code s'il ne comprend pas
- Notez sévèrement la partie "Compréhension"

---

## Exemples de bonnes et mauvaises réponses

### Question : "Pourquoi tu as utilisé `requestAnimationFrame` pour la barre ?"

❌ **Mauvaise réponse :**
> "Parce que ChatGPT m'a dit de faire ça"

⚠️ **Réponse insuffisante :**
> "Pour que ça soit plus fluide"

✅ **Bonne réponse :**
> "Parce que `requestAnimationFrame` s'exécute à chaque rafraîchissement d'écran, environ 60 fois par seconde. Ça permet à la barre de se vider progressivement. Si j'avais utilisé `setInterval`, la barre se viderait par saccades toutes les secondes."

### Question : "Change le temps total à 30 secondes"

❌ **Mauvaise exécution :**
> Change uniquement l'affichage HTML sans toucher à la logique

⚠️ **Exécution partielle :**
> Change la variable initiale mais oublie de mettre à jour les calculs de la barre

✅ **Bonne exécution :**
> Change la variable `tempsTotal = 20` en `tempsTotal = 30` et vérifie que ça fonctionne en testant

---

**Durée totale recommandée : 10 minutes par étudiant**

- 2 min : Présentation générale du code
- 3 min : Questions d'explication
- 5 min : Modifications en direct