# CLAUDE.md - Résumé de la documentation du projet

Document synthétique pour comprendre le contexte, les principes et les guidelines du projet IAD Courses.

---

## Vue d'ensemble du projet

**Objectif global :** Enseigner la narration interactive web à des étudiants en cinéma et audiovisuel de l'IAD Louvain-la-Neuve.

**Public :**
- MM3B : 3eme année de bachelier en multimedia
- RTMF1M : Sites Web Dynamiques 2 (Réalisation Technique Multimédias et Audiovisuelles)
- MM2B : 2eme année de bechelier en multimedia

**Stack technique :**
- Framework : Astro ^5.6 avec Starlight theme
- Styling : CSS pur (ITCSS, BEM) - pas Tailwind
- Interactivité : Alpine.js, Svelte
- Commentaires de code : Français

---

## Les 3 cours et leurs philosophies

### 1. MM3B - Décrypter l'interactivité (4 séances × 4h)

**Public :** Étudiants en multimedia, niveau analyse/compréhension
**Approche :** Théorie + regard critique
**Langage :** Narratif avant technique

**Les 5 formes d'interactivité enseignées :**
1. **Le regard** - Où je regarde influence ce que je vois
2. **Le choix** - Mes décisions changent le récit
3. **L'exploration** - L'espace contient la narration
4. **Le rythme** - Ma vitesse influence l'émotion
5. **La participation** - Ma présence modifie l'œuvre

**Structure du cours :**
- Panorama d'introduction d'oeuvres interactives
- Découverte des 5 formes
- Concevoir sans contraintes
- Analyse critique + présentation finale

**Ratio théorie/pratique :** 20/80
**Livrables :** Fiches d'analyse, projet final (dossier de conception)

---

### 2. RTMF1M - Sites Web Dynamiques 2 (32 heures)

**Public :** Réalisateurs techniques, expérience en VFX/3D/Motion
**Approche :** Apprentissage par la pratique, créativité au centre
**Langage :** Pragmatique, centré sur l'expérience utilisateur

**Les 5 territoires de projets (étudiants en choisissent 1) :**
1. **Expériences muséales/spatiales** - Contemplation augmentée, narration spatiale
2. **Jeux de société hybrides** - Sociabilité + assistance numérique
3. **Expériences de lecture augmentées** - Narration non-linéaire
4. **Interfaces expérimentales/ludiques** - Interaction pure, réinvention des gestes
5. **Outils créatifs et générateurs** - Utilitaire réinventé, génération procédurale

**Structure :**
- Module 1 (6h) : Cartographie du possible
- Module 2 (8h) : Fondamentaux techniques (Svelte)
- Module 3 (6h) : Idéation et prototypage
- Module 4 (10h) : Réalisation accompagnée

**Ressources clés :**
- **15 recettes de code** (Svelte) : briques réutilisables et adaptables
- **Survival Guide** : Quick start, troubleshooting, glossaire
- **Playlist d'inspiration** : Œuvres pour chaque territoire

**Utilisation de l'IA :**
- ✅ Encouragée (ChatGPT, Claude) comme assistant
- ⚠️ Règle d'or : "Si tu ne peux pas expliquer le code que tu as obtenu, ne l'utilise pas"
- La défense orale vérifie la compréhension

---

### 3. MM2B et autres (à adapter)

Structure similaire à MM3B mais adaptée au public spécifique.

---

## Principes pédagogiques transversaux

### 1. Narration avant technique

- Partir de l'émotion, de l'intention narrative
- Utiliser le vocabulaire cinéma (cadrage, montage, mise en scène)
- Démystifier le code sans l'imposer
- "Comment réaliser votre vision ?" plutôt que "Voici la théorie"

### 2. Faire plutôt qu'écouter

- Ratio : Beaucoup de pratique, peu de magistral (max 15 min de suite)
- Activités privilégiées : découverte, groupe, présentation, discussion
- À éviter : cours magistraux passifs, exercices répétitifs

### 2b. Réalisme du scope

**Petit projet bien exécuté > gros projet en ruine.**

Les étudiants pensent: "Je vais faire un jeu. Une app. Un truc énorme."

Ensuite, après 3 semaines: c'est un monstre qui ne marche pas, jamais terminé.

**La vraie approche :**
- ✅ Petit projet qui FONCTIONNE
- ✅ Code lisible et réfléchi
- ✅ Bien léché (polished, vraiment)
- ✅ "J'ai une vision réaliste de ce que je peux faire en 3 semaines"

- ❌ Gros projet ambitieux qui n'existe qu'à 40%
- ❌ "J'aurais voulu mais..."
- ❌ Code brut, inachevé, qui ne tourne pas

**En pratique :**
- Avant de coder: Quoi exactement? Combien de temps? Combien de features? (3 max? 1 bien exécutée?)
- Pendant le projet: Si c'est trop gros → couper. Si c'est fini tôt → polir, tester, affiner
- L'objectif: **un truc réel qui marche**

**Le message :**
> "Une petite chose bien pensée, bien faite, intéressante, vaut mille fois mieux qu'une grosse chose qui ne fonctionne pas."

### 3. Autonomie progressive

- Fournir des outils (recettes, templates, ressources)
- Pas de solutions toutes faites
- L'enseignant facilite, n'impose pas

### 3b. Face au niveau faible - Une stratégie progressive

**Contexte :** Beaucoup d'étudiants ont un niveau code faible et zéro autonomie créative. Ils n'écoutent pas longtemps. Pas de magistral > 15min.

**Stratégie : Trois phases. Peu de théorie, beaucoup de FAIRE.**

**Phase 0 (cultiver le regard) - 1-2 séances**
Aucun code. Juste apprendre à VER.
- Analyse collective d'œuvres interactives
- Dessin/storyboard d'interactions (formalisez l'intention)
- Jeux/expériences vécues en vrai
- **Objectif :** Chacun a des questions ("Pourquoi ça marche? Comment elle l'a construit?")

**Phase 1 (fondations) - Max 30min de théorie**
Pas de cours HTML/CSS abstrait. Live coding, mais avec une différence : **Ils recodent après.**

- 30min max : Vous codez devant eux (un formulaire simple, une mise en page simple)
- **IMMÉDIATEMENT après** : Ils recodent pareil PENDANT que vous êtes là
- Vous circulez, corrigez, pointez les erreurs

Règle : Si c'est plus qu'une démo simple, ils sont perdus. Garder ça mega basique.

**Phase 2-3 (petit projet + jam sessions)**
- Chacun choisit un projet simple (ex: portfolio, galerie, formulaire interactif)
- Tempo similaire, mais itinéraires flexibles
- **Jam sessions 1x/semaine :** Tout le monde code ensemble, vous circulez, on discute collectivement des blocages
- Règle : Pas question idiote. Tous les blocages sont collectifs.

**Ce qui change vraiment**
- ❌ Pas de magistral > 15min
- ❌ Pas de "Voici HTML, voici CSS, voici JavaScript"
- ✅ **Live coding + recodage immédiat = mémorisation**
- ✅ **Jam sessions = moins isolés, apprentissage social**
- ✅ **Accepter qu'on ne couvre pas tout** - c'est OK

### 4. Authenticité et co-construction

- L'enseignant ne doit pas prétendre être expert de tout
- Dialogue avec les étudiants : "Tu connais mieux que moi, explique-moi ta logique VFX"
- Apprendre ensemble

### 5. Compréhension > Production

**Ce qui compte : la compréhension réelle. Code lisible. Pas la perfection.**

Le code doit être :
- ✅ **Lisible.** On suit le flux sans se perdre.
- ✅ **Clair.** Noms explicites, pas de `x`, `toto`, `resultat`
- ✅ **Vous pouvez l'expliquer.** Ligne par ligne, sans notes.
- ❌ **Pas du spaghetti.** Code enchevêtré, variables mystérieuses, logique confuse.

Le code ne doit PAS être :
- ❌ Optimisé à mort (ça, c'est un problème APRÈS)
- ❌ Suivre des patterns académiques complexes
- ❌ Parfait ou élégant

**Soutenance :**
- Défense orale du projet (min 15min)
- Vous montrez le code et l'expliquez
- Si vous ne comprenez pas votre propre code → problème

**Utilisation de l'IA :**
- ✅ Encouragée (ChatGPT, Claude)
- ✅ Génère le code rapidement
- ⚠️ **Ensuite : refactorisez pour lisibilité, testez, adaptez**
- ⚠️ **Si le code généré est du spaghetti, demandez à l'IA de le clarifier**
- ❌ Ne collez pas du code illisible

**Le message aux étudiants :**
> "Je n'évalue pas si votre code est parfait. J'évalue si VOUS le comprenez. Et si quelqu'un d'autre peut le lire sans migraine."

---

## Identité de parole (Dieter Rams appliqué)

**Profil enseignant :**
- Timide mais franc et sympathique
- Doux rêveur idéaliste avec pragmatisme cynique
- Tendances anarchistes et écolo
- Attaché à l'inclusivité naturelle

**Principes de ton :**

1. **Honnête** : Dire ce qui est, sans enjoliver
2. **Compréhensible** : Pas de jargon, phrases claires
3. **Minimaliste** : Chaque mot doit être utile ("Less, but better")
4. **Fonctionnel** : Le texte sert un but précis
5. **Durable** : Contenu qui reste pertinent

**Ce qu'on fait :**
- ✅ Direct et franc (pas de fioritures)
- ✅ Bienveillant mais pas paternaliste
- ✅ Pragmatique avec fond idéaliste
- ✅ Humour subtil (GIFs, easter eggs, clins d'œil)
- ✅ Critique du mainstream sans prêcher
- ✅ Phrases courtes, structure claire

**Ce qu'on évite :**
- ❌ Envolées lyriques ou philosophiques
- ❌ Ton révérenciel
- ❌ Métaphores forcées et kitsch
- ❌ Jargon corporate ("leverage", "engagement", "synergy")
- ❌ Formules creuses ("Certainement", "Super", "Génial")

**Vocabulaire :**
- Privilégier : Vocabulaire clair, universel, narratif
- Bannir : Jargon sans explication, adjectifs superflus

**Références culturelles :**
- Utiliser de façon FONCTIONNELLE, pas décoratives
- Cinéma : Wong Kar-wai, Nolan, Kurosawa (langage des étudiants)
- Musique : Dylan, Pink Floyd pour rythme/émotion
- Design : Dieter Rams, minimalisme
- Philosophie : wabi-sabi (imperfection, simplicité)

---

## Position critique du cours

**Ce cours n'est pas neutre. Il défend une idée du web.**

### La critique des "linéaires"

Les professeurs de cinéma/vidéo/VFX enseignent un prisme unique: **le cinéma classique. Plans qui s'enchaînent. Spectateur passif.**

C'est une forme extraordinaire. Mais c'est UNE forme.

Le web permet quelque chose de radicalement différent:
- **Le spectateur agit.** Ses choix changent le récit.
- **L'espace contient la narration.** Pas juste la succession.
- **Le temps n'est pas imposé.** À chacun son rythme.
- **C'est dialogique.** Pas un monologue.

Les "linéaires" manquent ça. Ils apportent la technologie du cinéma au web (caméra, plans, montage) sans voir que le medium a changé la NARRATION elle-même.

**Ce cours :** On enseigne la narration du web. Pas le cinéma sur une page.

### Sur les outils qu'on utilise

**Astro vs Next.js/Vercel**
- Astro = statique-first, déployable partout, pas de dépendance à une plateforme
- Philosophie: Vous construisez VOTRE site, pas un site pour un géant technique

**CSS pur vs Tailwind**
- CSS pur = vous contrôlez vraiment ce que vous faites
- Philosophie: Comprendre avant d'accélérer

**Alpine/Svelte vs React**
- Léger, lisible, pas de bloat
- Philosophie: Outils qui servent le projet, pas l'industrie

### Critique du design mainstream

- Interfaces rigides, surveillance intégrée, "dark patterns"
- Données utilisateur = produit de vente
- Hype constant ("Il faut apprendre X pour être moderne")

**Nous :** Design honnête. Outils stables. Pas de FOMO.

---

## Système CSS IAD Interactive

**Objectif :** Cohérence visuelle unifiée pour tous les composants de démo interactifs.

**Architecture ITCSS :**
```
1-settings/     → Tokens CSS (variables)
2-elements/     → Styles de base (formulaires)
3-components/   → Composants spécifiques (boutons, conteneurs, code)
```

**Tokens CSS clés :**
- Conteneurs : `--iad-demo-bg`, `--iad-demo-padding`, `--iad-demo-border-radius`
- Contrôles : `--iad-control-bg`, `--iad-control-border`, `--iad-control-text`
- États interactifs : `--iad-hover-bg`, `--iad-focus-outline`, `--iad-active-bg`
- Code : `--iad-code-bg`, `--iad-code-text`, `--iad-code-keyword`
- Espacement : `--iad-gap-xs` à `--iad-gap-xl`

**Classes principales :**
- `.iad-demo` : Conteneur racine
- `.iad-controls`, `.iad-controls-grid` : Zones de contrôles
- `.iad-visualization` : Zone d'affichage
- `.iad-button`, `.iad-selector-button`, `.iad-switch` : Boutons variés
- `.iad-slider`, `.iad-select`, `.iad-radio-group` : Formulaires
- `.iad-code-output`, `.iad-code-block` : Code affiché

**Bonnes pratiques :**
1. Toujours utiliser `.iad-demo` comme conteneur racine
2. Utiliser les tokens CSS pour styles personnalisés
3. Conserver les styles spécifiques dans le composant
4. Tester en mode dark et light
5. Utiliser Alpine.js pour la réactivité

---

## Guidelines techniques globales

### Code et structure

- **Framework :** Astro ^5.6 avec Starlight theme
- **Styling :** CSS pur (ITCSS, BEM) - pas Tailwind
- **Code comments :** Français, clairs et fonctionnels
- **Principes :** DRY, KISS, SRP
- **Variables :** Nommage clair (ex: `dureeAnimation` pas `toto`)

### Fichiers MDX

- JSX inline : Tout sur une seule ligne pour éviter problèmes de rendu
- ❌ Sauts de ligne dans balises JSX
- ✅ Contenu compact sur une ligne

### Navigation

- Gérée par le theme Starlight
- Pas de navigation en bas de page

### Approche pédagogique

- Privilégier : Concepts narratifs, exemples concrets, découverte
- Éviter : Jargon, lyrisme, magistral > 15 min

---

## Checklist pour contenu nouveau

**Structure :**
- [ ] Objectifs clairs en début
- [ ] Déroulé détaillé avec timing
- [ ] Activités variées (max 15 min de magistral)
- [ ] Livrable explicite

**Ton :**
- [ ] Direct et minimaliste
- [ ] Vocabulaire narratif > technique
- [ ] Pas de jargon sans explication
- [ ] Pas de formules creuses

**Contenu :**
- [ ] Exemples concrets et testables
- [ ] Références fonctionnelles, pas décoratives
- [ ] Activités de groupe privilégiées
- [ ] Discussion collective intégrée

**Pédagogie :**
- [ ] Ratio 20/80 (théorie/pratique) respecté
- [ ] Démystification du code
- [ ] Encouragement sans infantilisation
- [ ] Critique constructive

---

## Ressources clés

### Pour MM3B
- **Fichier :** `docs/MM3B-INTERACTIVITE-CONTEXTE.md`
- **Templates :** Fiche d'analyse, storyboard interactif, brief de conception
- **Catalogue :** 40+ œuvres cataloguées par forme d'interactivité
- **Symboles visuels :** SVG pour storyboards

### Pour RTMF1M
- **Fichier :** `docs/RTMF1M-CONTEXTE-COURS.md`
- **Template :** Projet Svelte configuré et fonctionnel
- **Recettes :** 15+ recettes de code documentées (fondations, interactions, feedback, persistance, avancé)
- **Survival Guide :** Quick start, troubleshooting, glossaire

### CSS et composants
- **Fichier :** `docs/IAD_INTERACTIVE_DOCS.md`
- **Location :** `src/styles/iad-interactive.css`
- **Exemples :** `src/components/mdx/`

---

## Messages clés à transmettre

**Aux étudiants MM3B :**
> "Le web n'est pas juste pour des sites corporate. C'est un espace de création unique qui combine texte, image, son, interactivité. Vous allez explorer ce potentiel et analyser comment les créateurs construisent des expériences mémorables."

**Aux étudiants RTMF1M :**
> "Le web n'est pas juste pour des sites corporate. C'est un espace de création unique qui combine texte, image, son, interactivité, temporalité. Vous allez explorer ce potentiel et créer des expériences que personne n'a jamais vues."

**Sur la technique (RTMF1M) :**
> "Vous n'allez pas devenir experts Svelte en 32h. Mais vous allez apprendre à utiliser les outils disponibles (recettes, IA, documentation) pour réaliser votre vision. C'est ça, la vraie compétence."

**Sur l'évaluation :**
> "Ce qui compte n'est pas QUI a écrit chaque ligne de code, mais est-ce que VOUS comprenez ce qu'il fait, et est-ce qu'il sert votre intention créative."

**Sur le rôle de l'enseignant :**
> "Je ne suis pas là pour vous enseigner la syntaxe ligne par ligne. Je suis là pour vous aider à réaliser vos visions créatives en utilisant le web comme medium."

---

## Points d'attention pour la génération de contenu

1. **Pas de faux amis :** "Certainement", "Super", "Génial" à bannir
2. **Jargon technique :** Toujours expliquer
3. **Références :** Doivent servir la compréhension, pas décorer
4. **Inclusivité :** Naturelle, pas performative
5. **Code :** Toujours commenté en français
6. **Métaphores :** À éviter si forcées (même analogies cinéma doivent être pertinentes)
7. **Structures :** Hiérarchie claire, listes scannables
8. **Exemples :** Testables et contextualisés

---

## Posture de l'enseignant (résumé)

L'enseignant n'est PAS un :
- Professeur qui dicte de la syntaxe
- Expert qui impose une approche

L'enseignant EST :
- **Curateur** : "Regardez ce qui existe, ce qui est possible"
- **Facilitateur** : "Comment réaliser votre vision ?"
- **Critique** : "Est-ce que cette interaction sert votre narration ?"
- **Guide technique** : "Voici les outils, à vous de les maîtriser"
- **Coach créatif** : Accompagnement individuel des projets

---

**Version :** 1.0
**Date :** 2025-11-22
**Principes directeurs :** Less, but better. Narration avant technique. Faire plutôt qu'écouter.