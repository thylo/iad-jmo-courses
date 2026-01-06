# Instructions pour CodePen

## Comment importer ce projet dans CodePen

### 1. Créer un nouveau Pen

Allez sur [CodePen.io](https://codepen.io) et créez un nouveau Pen.

### 2. Importer le HTML

1. Ouvrez le fichier `examen.html`
2. Copiez **uniquement le contenu du `<body>`** (pas le `<!DOCTYPE>`, `<html>`, `<head>`)
3. Collez dans la section HTML de CodePen

### 3. Intégrer le SVG de la carte du monde

**IMPORTANT** : Le fichier `worldLow.svg` est trop volumineux pour être collé directement.

**Option 1 : Utiliser le SVG complet**
1. Ouvrez le fichier `worldLow.svg` dans un éditeur de texte
2. Copiez tout le contenu
3. Dans le HTML de CodePen, remplacez la balise `<svg>` vide par le contenu complet du SVG
4. Assurez-vous que la balise `<svg>` conserve l'attribut `id="world-map"`

**Option 2 : Utiliser un SVG hébergé**
Si le SVG est trop gros, vous pouvez utiliser un `<object>` :
```html
<object id="world-map" data="URL_VERS_LE_SVG" type="image/svg+xml"></object>
```

### 4. Importer le CSS

1. Ouvrez le fichier `examen.css`
2. Copiez tout le contenu
3. Collez dans la section CSS de CodePen

### 5. Importer le JavaScript

1. Ouvrez le fichier `examen.js`
2. Copiez tout le contenu (ou supprimez les commentaires si vous préférez partir de zéro)
3. Collez dans la section JS de CodePen

### 6. Configuration CodePen (optionnel)

Dans les paramètres de CodePen (icône d'engrenage) :
- **HTML** : Pas de préprocesseur nécessaire
- **CSS** : Pas de préprocesseur nécessaire
- **JS** : Pas de bibliothèque nécessaire (JavaScript pur)

## Structure des fichiers

```
examen/
├── examen.html         # Structure HTML complète
├── examen.css          # Styles complets
├── examen.js           # Template JavaScript (à compléter)
├── worldLow.svg        # Carte du monde SVG (à intégrer dans le HTML)
├── consignes.mdx       # Consignes de l'examen
├── revision.mdx        # Document de révision
└── README-CODEPEN.md   # Ce fichier
```

## Vérification

Après avoir tout importé :

1. ✅ Vous devez voir le panneau d'accueil centré avec le bouton "Lancer le jeu"
2. ✅ La carte du monde doit apparaître en arrière-plan
3. ✅ Les pays de la carte doivent changer de couleur au survol (hover)
4. ✅ Le timer doit être visible en bas de page
5. ✅ La modal et le panneau game over doivent être cachés (classe `.hidden`)

Si tout est visible et fonctionnel (sauf le JavaScript qui n'est pas encore écrit), vous êtes prêt à commencer !

## Astuce

Pour tester rapidement si le SVG est bien intégré, ouvrez la console et tapez :
```javascript
document.querySelectorAll('#world-map path[data-title]').length
```

Vous devriez obtenir un nombre > 0 (le nombre de pays dans le SVG).

## Problèmes courants

**Le SVG ne s'affiche pas**
- Vérifiez que vous avez bien collé le contenu du SVG
- Vérifiez que la balise `<svg>` a l'attribut `id="world-map"`
- Vérifiez dans l'inspecteur que les `<path>` existent bien

**Les pays n'ont pas de `data-title`**
- Le fichier `worldLow.svg` doit contenir des attributs `data-title` sur chaque `<path>`
- Si ce n'est pas le cas, contactez l'enseignant

**La barre de progression ne s'affiche pas**
- Vérifiez que vous avez bien importé le CSS
- La barre est vide au départ, c'est normal

---

**Bon courage pour l'examen !**