# Documentation IAD Interactive CSS

Guide complet pour utiliser le système de styles unifiés des composants interactifs.

## Table des matières

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Structure](#structure)
4. [Classes disponibles](#classes-disponibles)
5. [Tokens CSS](#tokens-css)
6. [Exemples d'utilisation](#exemples-dutilisation)
7. [Bonnes pratiques](#bonnes-pratiques)

---

## Introduction

IAD Interactive CSS est un système de styles unifiés pour les composants de démonstration interactifs. Il fournit :

- **Cohérence visuelle** entre tous les composants
- **Compatibilité dark/light** automatique via les tokens Starlight
- **Tokens sémantiques** pour faciliter la maintenance
- **Architecture ITCSS** pour une cascade CSS prévisible

---

## Installation

Le système est déjà configuré dans `astro.config.mjs` :

```js
customCss: [
  './src/styles/iad-interactive.css',
]
```

Aucune action supplémentaire n'est nécessaire.

---

## Structure

```
src/styles/
├── 1-settings/
│   └── iad-tokens.css          # Tokens CSS (variables)
├── 2-elements/
│   └── iad-forms.css           # Styles de base pour les formulaires
├── 3-components/
│   ├── iad-demo-container.css  # Conteneurs de démo
│   ├── iad-button.css          # Boutons
│   └── iad-code-output.css     # Blocs de code
└── iad-interactive.css         # Point d'entrée (importe tout)
```

---

## Classes disponibles

### Conteneurs

#### `.iad-demo`
Conteneur principal pour tous les composants de démonstration.

```html
<div class="iad-demo">
  <!-- Contenu du composant -->
</div>
```

**Propriétés :**
- Padding, background, border, border-radius
- Adapté automatiquement au thème dark/light

---

#### `.iad-demo-header`
En-tête de démo avec titre et éventuellement des contrôles.

```html
<div class="iad-demo-header">
  <h3>Titre de la démo</h3>
  <button class="iad-switch">Toggle</button>
</div>
```

**Propriétés :**
- Flexbox : `justify-content: space-between`
- Margin bottom pour espacement avec le contenu

---

#### `.iad-section`
Section interne d'une démo (contrôles, visualisation, etc.).

```html
<div class="iad-section">
  <h4>Propriétés du conteneur</h4>
  <!-- Contenu de la section -->
</div>
```

---

#### `.iad-controls`
Zone contenant les contrôles interactifs.

```html
<div class="iad-controls">
  <div class="iad-control-group">
    <!-- Contrôle -->
  </div>
</div>
```

---

#### `.iad-controls-grid`
Grille responsive pour organiser plusieurs contrôles.

```html
<div class="iad-controls-grid">
  <div class="iad-control-group"><!-- Contrôle 1 --></div>
  <div class="iad-control-group"><!-- Contrôle 2 --></div>
  <div class="iad-control-group"><!-- Contrôle 3 --></div>
</div>
```

**Propriétés :**
- Grid avec `minmax(200px, 1fr)`
- Gap responsive

---

#### `.iad-visualization`
Zone d'affichage de la visualisation/prévisualisation.

```html
<div class="iad-visualization">
  <!-- Élément à visualiser -->
</div>
```

**Propriétés :**
- Background blanc (ou adapté au thème)
- Padding, border-radius
- Overflow auto

---

### Formulaires

#### `.iad-control-group`
Groupe de contrôle (label + input).

```html
<div class="iad-control-group">
  <label class="iad-label">Largeur</label>
  <input type="range" class="iad-slider" />
</div>
```

---

#### `.iad-slider-group`
Groupe spécifique pour slider avec label inline.

```html
<div class="iad-slider-group">
  <label class="iad-label-inline">
    <span>Hue</span>
    <span class="iad-label-value">180°</span>
  </label>
  <input type="range" class="iad-slider" />
</div>
```

---

#### `.iad-label`
Label standard (block).

```html
<label class="iad-label">Nom du contrôle</label>
```

---

#### `.iad-label-inline`
Label inline avec valeur à droite.

```html
<label class="iad-label-inline">
  <span>Hue (Teinte)</span>
  <span class="iad-label-value" x-text="hue + '°'"></span>
</label>
```

---

#### `.iad-slider`
Slider (input range) avec styles unifiés.

```html
<input type="range" class="iad-slider" min="0" max="100" />
```

**Propriétés :**
- Thumb uniforme (20px, blanc, ombre)
- Hover : scale(1.1)
- Compatible WebKit et Firefox

**Note :** Pour des sliders avec gradients spécifiques (HSL, RGBA), ajouter une classe complémentaire :

```html
<input type="range" class="iad-slider hue-slider" />
```

```css
.hue-slider {
  background: linear-gradient(to right, 
    hsl(0, 100%, 50%), 
    hsl(120, 100%, 50%), 
    hsl(240, 100%, 50%), 
    hsl(360, 100%, 50%)
  );
}
```

---

#### `.iad-select`
Select avec styles unifiés.

```html
<select class="iad-select" x-model="value">
  <option value="option1">Option 1</option>
  <option value="option2">Option 2</option>
</select>
```

**États :**
- Hover : background change
- Focus : outline accent

---

#### `.iad-radio-group`
Groupe de radio buttons.

```html
<div class="iad-radio-group">
  <label class="iad-radio-label">
    <input type="radio" class="iad-radio" name="choice" value="1">
    Option 1
  </label>
  <label class="iad-radio-label">
    <input type="radio" class="iad-radio" name="choice" value="2">
    Option 2
  </label>
</div>
```

---

### Boutons

#### `.iad-button`
Bouton générique.

```html
<button class="iad-button">Action</button>
```

---

#### `.iad-copy-button`
Bouton de copie (souvent avec Alpine.js).

```html
<button 
  class="iad-copy-button"
  @click="navigator.clipboard.writeText(text); copied = true"
  x-text="copied ? '✓' : '📋'"
></button>
```

---

#### `.iad-selector-button`
Bouton de sélection avec état actif.

```html
<button 
  class="iad-selector-button"
  :class="{ active: selectedItem === 1 }"
  @click="selectedItem = 1"
>
  Item 1
</button>
```

**États :**
- Normal : border, background secondaire
- Hover : border accent
- Active : background accent, texte blanc

---

#### `.iad-switch`
Switch/toggle.

```html
<button 
  class="iad-switch" 
  :class="{ active: isDark }"
  @click="isDark = !isDark"
>
  <span class="iad-switch-icon" x-text="isDark ? '🌙' : '☀️'"></span>
</button>
```

**Fonctionnement :**
- Width 60px, height 32px
- Icon translates de 28px quand actif

---

### Code output

#### `.iad-code-output`
Conteneur pour le code généré.

```html
<div class="iad-code-output">
  <p><strong>Code CSS généré :</strong></p>
  <div class="iad-code-block">
    <!-- Code -->
  </div>
</div>
```

**Propriétés :**
- Background sombre (#1e1e1e)
- Texte clair, monospace
- Coloration syntaxique simple (keywords, values)

---

#### `.iad-code-block`
Bloc de code à l'intérieur de `.iad-code-output`.

```html
<div class="iad-code-block">
  <div>.container {</div>
  <div>  width: <span x-text="width + 'px'"></span>;</div>
  <div>}</div>
</div>
```

---

#### `.iad-output-list`
Liste de valeurs de sortie (ex: HSL, RGB, HEX).

```html
<div class="iad-output-list">
  <div class="iad-output-item">
    <code x-text="hslColor"></code>
    <button @click="copyColor('hsl')">📋</button>
  </div>
  <div class="iad-output-item">
    <code x-text="rgbColor"></code>
    <button @click="copyColor('rgb')">📋</button>
  </div>
</div>
```

---

### Grilles et layouts

#### `.iad-grid`
Grille générique responsive.

```html
<div class="iad-grid">
  <div class="iad-grid-item">Item 1</div>
  <div class="iad-grid-item">Item 2</div>
  <div class="iad-grid-item">Item 3</div>
</div>
```

---

#### `.iad-measurements`
Zone d'affichage de mesures/valeurs calculées.

```html
<div class="iad-measurements">
  <div class="iad-measurement-item">
    <span class="iad-measurement-label">Largeur totale :</span>
    <strong class="iad-measurement-value" x-text="totalWidth + 'px'"></strong>
  </div>
</div>
```

---

## Tokens CSS

### Conteneurs

```css
--iad-demo-bg             /* Background principal de démo */
--iad-demo-border         /* Bordure de démo */
--iad-demo-border-radius  /* Border radius principal (0.75rem) */
--iad-demo-padding        /* Padding principal (1.5rem) */

--iad-viz-bg              /* Background zone de visualisation (blanc) */
--iad-viz-border          /* Bordure visualisation */
--iad-viz-border-radius   /* Border radius visualisation (0.5rem) */
--iad-viz-padding         /* Padding visualisation (1.5rem) */
```

### Contrôles de formulaire

```css
--iad-control-bg          /* Background contrôles (select, slider track) */
--iad-control-border      /* Bordure contrôles */
--iad-control-text        /* Texte contrôles */
--iad-control-border-radius /* Border radius contrôles (0.5rem) */
```

### États interactifs

```css
--iad-hover-bg            /* Background au hover */
--iad-focus-outline       /* Couleur outline au focus */
--iad-active-bg           /* Background état actif */
```

### Texte

```css
--iad-text-primary        /* Texte principal */
--iad-text-secondary      /* Texte secondaire (gris) */
--iad-text-label          /* Texte des labels */
```

### Boutons

```css
--iad-button-bg           /* Background bouton (transparent) */
--iad-button-border       /* Bordure bouton */
--iad-button-text         /* Texte bouton */
--iad-button-border-radius /* Border radius (0.25rem) */
--iad-button-padding      /* Padding (0.25rem 0.5rem) */
```

### Code

```css
--iad-code-bg             /* Background code (#1e1e1e) */
--iad-code-text           /* Texte code (#d4d4d4) */
--iad-code-keyword        /* Keywords code (#4EC9B0) */
--iad-code-value          /* Values code (#ffc107) */
--iad-code-border-radius  /* Border radius (0.5rem) */
--iad-code-padding        /* Padding (1rem) */
```

### Espacement

```css
--iad-gap-xs              /* 0.25rem */
--iad-gap-sm              /* 0.5rem */
--iad-gap-md              /* 1rem */
--iad-gap-lg              /* 1.5rem */
--iad-gap-xl              /* 2rem */
```

### Transitions

```css
--iad-transition-fast     /* 0.1s ease */
--iad-transition-normal   /* 0.2s ease */
--iad-transition-smooth   /* 0.3s cubic-bezier(0.4, 0, 0.2, 1) */
```

---

## Exemples d'utilisation

### Exemple 1 : Composant de slider simple

```html
<div class="iad-demo" x-data="{ value: 50 }">
  <div class="iad-controls">
    <div class="iad-slider-group">
      <label class="iad-label-inline">
        <span>Valeur</span>
        <span class="iad-label-value" x-text="value"></span>
      </label>
      <input type="range" class="iad-slider" x-model="value" min="0" max="100" />
    </div>
  </div>
  
  <div class="iad-visualization">
    <p>Résultat : <strong x-text="value"></strong></p>
  </div>
</div>
```

---

### Exemple 2 : Composant avec sélection et code output

```html
<div class="iad-demo" x-data="{ mode: 'cover' }">
  <div class="iad-controls">
    <div class="iad-control-group">
      <label class="iad-label">object-fit :</label>
      <select class="iad-select" x-model="mode">
        <option value="cover">cover</option>
        <option value="contain">contain</option>
        <option value="fill">fill</option>
      </select>
    </div>
  </div>
  
  <div class="iad-code-output">
    <p><strong>Code CSS généré :</strong></p>
    <div class="iad-code-block">
      <div>img {</div>
      <div>  object-fit: <span x-text="mode"></span>;</div>
      <div>}</div>
    </div>
  </div>
</div>
```

---

### Exemple 3 : Composant avec sélection d'items

```html
<div class="iad-demo" x-data="{ selected: 1 }">
  <div class="iad-item-selector">
    <template x-for="i in 3" :key="i">
      <button 
        class="iad-selector-button"
        :class="{ active: selected === i }"
        @click="selected = i"
      >
        Item <span x-text="i"></span>
      </button>
    </template>
  </div>
  
  <div class="iad-visualization">
    <p>Item sélectionné : <strong x-text="selected"></strong></p>
  </div>
</div>
```

---

## Bonnes pratiques

### 1. Toujours utiliser `.iad-demo` comme conteneur racine

```html
<!-- ✅ Bon -->
<div class="iad-demo">
  <!-- Contenu -->
</div>

<!-- ❌ Mauvais -->
<div class="my-custom-demo">
  <!-- Contenu -->
</div>
```

---

### 2. Utiliser les tokens CSS pour les styles personnalisés

```css
/* ✅ Bon */
.my-custom-element {
  padding: var(--iad-gap-md);
  background: var(--iad-control-bg);
  border-radius: var(--iad-control-border-radius);
}

/* ❌ Mauvais */
.my-custom-element {
  padding: 1rem;
  background: #353841;
  border-radius: 0.5rem;
}
```

---

### 3. Conserver les styles spécifiques dans le composant

Les sliders avec gradients, visualisations spécifiques, etc. doivent rester dans le composant :

```astro
<input type="range" class="iad-slider hue-slider" />

<style>
  /* Gradient spécifique au composant HSL */
  .hue-slider {
    background: linear-gradient(to right, 
      hsl(0, 100%, 50%), 
      hsl(60, 100%, 50%), 
      hsl(120, 100%, 50%), 
      hsl(180, 100%, 50%), 
      hsl(240, 100%, 50%), 
      hsl(300, 100%, 50%), 
      hsl(360, 100%, 50%)
    );
  }
</style>
```

---

### 4. Tester en mode dark et light

Tous les composants doivent fonctionner en mode sombre et clair. Les tokens Starlight s'adaptent automatiquement.

---

### 5. Utiliser Alpine.js pour la réactivité

Les classes IAD sont conçues pour fonctionner avec Alpine.js :

```html
<div class="iad-demo" x-data="{ value: 50 }">
  <input type="range" class="iad-slider" x-model="value" />
  <p x-text="value"></p>
</div>
```

---

## Support et ressources

- **Migration depuis les anciens composants :** `docs/MIGRATION_GUIDE_IAD_INTERACTIVE.md`
- **Tokens complets :** `src/styles/1-settings/iad-tokens.css`
- **Exemples de composants :**
  - `src/components/mdx/ColorComponents/HSLPlayground.astro`
  - `src/components/mdx/LayoutComponents/FlexboxPlayground.astro`

---

**Version :** 1.0  
**Dernière mise à jour :** 2025-11-06
