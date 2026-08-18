# StackBlitzButton - Documentation

## Description

Composant Astro qui génère un bouton pour ouvrir du code Svelte dans StackBlitz.

## Utilisation

### Dans un fichier MDX

```mdx
---
title: Mon cours Svelte
---

import StackBlitzButton from '@/components/mdx/StackBlitzButton.astro';

export const monCodeSvelte = `<script>
  let count = 0;
</script>

<button on:click={() => count++}>
  Clics: {count}
</button>`;

## Exemple de compteur

Voici le code :

```svelte
{monCodeSvelte}
```

<StackBlitzButton title="Compteur Svelte">
{monCodeSvelte}
</StackBlitzButton>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | `string` | `'Svelte Example'` | Titre du projet StackBlitz |
| `label` | `string` | `'Ouvrir dans StackBlitz'` | Texte du bouton |

## Avantages

- ✅ Code défini une fois dans le MDX
- ✅ Même code affiché et testé
- ✅ Pas de duplication
- ✅ Édition centralisée
- ✅ Projet Svelte 5 + Vite configuré automatiquement

## Projet généré

Le composant crée automatiquement :
- `package.json` avec Svelte 5 + Vite
- `vite.config.js` avec le plugin Svelte
- `index.html` avec point d'entrée
- `src/App.svelte` avec votre code
- `src/main.js` pour bootstrapper l'app
- `src/app.css` avec styles de base

## Exemples complets

### Exemple simple

```mdx
export const simpleCounter = `<script>
  let n = 0;
</script>

<button on:click={() => n++}>
  {n}
</button>`;

<StackBlitzButton title="Simple Counter">
{simpleCounter}
</StackBlitzButton>
```

### Exemple avec réactivité

```mdx
export const reactiveExample = `<script>
  let firstName = 'Ada';
  let lastName = 'Lovelace';
  
  // Variable réactive dérivée
  $: fullName = \`\${firstName} \${lastName}\`;
</script>

<input bind:value={firstName} placeholder="Prénom" />
<input bind:value={lastName} placeholder="Nom" />
<p>Bonjour {fullName} !</p>`;

<StackBlitzButton title="Réactivité Svelte">
{reactiveExample}
</StackBlitzButton>
```

### Exemple avec composant enfant

```mdx
export const parentChild = `<script>
  let count = 0;
  
  function increment() {
    count += 1;
  }
</script>

<h1>Parent: {count}</h1>
<button on:click={increment}>+1</button>`;

<StackBlitzButton title="Composant Parent">
{parentChild}
</StackBlitzButton>
