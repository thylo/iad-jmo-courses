/**
 * Utilitaire de chargement des fichiers d'exemples
 * Responsabilité: charger les fichiers HTML/CSS/JS depuis src/examples/
 */

/**
 * Charge les fichiers d'un exemple
 * @param {string} examplePath - Chemin relatif depuis src/examples/
 * @returns {Object} - { html: string, css: string, js: string, hasFiles: boolean }
 */
export function loadExampleFiles(examplePath) {
  // Charger tous les fichiers de l'exemple
  const allFiles = import.meta.glob('/src/examples/**/*', { 
    query: '?raw',
    import: 'default',
    eager: true 
  });

  // Filtrer les fichiers pour cet exemple spécifique
  const fullPath = `/src/examples/${examplePath}`;
  const files = {
    html: '',
    css: '',
    js: ''
  };

  Object.entries(allFiles).forEach(([path, content]) => {
    if (path.startsWith(fullPath) && typeof content === 'string') {
      if (path.endsWith('.html')) files.html = content;
      if (path.endsWith('.css')) files.css = content;
      if (path.endsWith('.js')) files.js = content;
    }
  });

  // Vérifier qu'au moins un fichier a été trouvé
  const hasFiles = !!(files.html || files.css || files.js);

  return { ...files, hasFiles };
}
