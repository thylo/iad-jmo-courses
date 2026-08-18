# Project guidelines

## But du cours

Ce cours enseigne la narration interactive web à des étudiants en cinéma (MM3B et MM2B) de l'IAD Louvain-la-Neuve. L'objectif est d'apprendre à créer des récits interactifs en utilisant JavaScript, en présentant le web comme un "cinéma dont vous êtes le héros".

**Public cible:** Étudiants en cinéma qui maîtrisent After Effects, Nuke et Maya mais ont peu ou pas d'expérience en programmation.

**Approche pédagogique:** 
- Privilégier la narration sur la technique
- Établir des ponts entre concepts cinématographiques et interactions web
- Utiliser des références de films et de réalisateurs reconnus
- Éviter le jargon technique au profit d'un vocabulaire narratif
- Eviter les métphores et les analogies trop forcées et kitsch
- Ecrire le cours à destination des étudiants pas comme notes du professeur
- Ecrire un cours instructif, pas un timing (10minutes pour ça, 20 minutes pour ça...)

## Guidelines techniques

- Use clean readable css. Do not use tailwind.
- Follow ITCSS rules
- Code comments in french
- Project goal is to be as pedagogic as possible
- Avoid jargon
- Project use Astro ^5.6
- Garder le code propre
- DRY and KISS
- Respecter le SRP
- Ne pas créer de navigation en bas de page
- La navigation est gerée par le theme Starlight de Astro.

### MDX et JSX inline styles

Quand vous utilisez des balises JSX avec des styles inline dans les fichiers MDX (.mdx), **mettez tout le contenu sur une seule ligne** pour éviter les problèmes de rendu.

**❌ Éviter:**
```jsx
<p style={{ fontFamily: 'Georgia, serif' }}>
  Ceci est un texte exemple.
</p>
```

**✅ Préférer:**
```jsx
<p style={{ fontFamily: 'Georgia, serif' }}>Ceci est un texte exemple.</p>
```

Raison: MDX peut mal interpréter les sauts de ligne dans les balises JSX, causant un rendu où le contenu apparaît en dehors des balises HTML.
