/**
 * Gestion de la preview iframe
 * Mise à jour en temps réel du code dans l'iframe
 */

/**
 * Utilitaire de debounce
 * @param {Function} func - Fonction à debouncer
 * @param {number} wait - Délai en ms
 * @returns {Function}
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Crée et configure l'iframe de preview
 * @param {HTMLElement} container - Conteneur de l'iframe
 * @param {Object} initialFiles - Fichiers initiaux {html, css, js}
 * @returns {Object} - { update, reset }
 */
export function createPreview(container, initialFiles) {
  // Création de l'iframe
  const iframe = document.createElement('iframe');
  iframe.setAttribute('sandbox', 'allow-scripts');
  iframe.style.width = '100%';
  iframe.style.height = '100%';
  iframe.style.border = 'none';
  container.appendChild(iframe);

  // Stockage des fichiers initiaux pour le reset
  const originalFiles = { ...initialFiles };

  /**
   * Met à jour le contenu de l'iframe
   * @param {Object} files - {html, css, js}
   */
  function update(files) {
    const htmlContent = files.html || '';
    const cssContent = files.css || '';
    const jsContent = files.js || '';

    // Construction du document HTML complet
    const fullHTML = `
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Reset minimal pour la preview */
    * { box-sizing: border-box; }
    body { margin: 0; padding: 1rem; font-family: system-ui, sans-serif; }
    ${cssContent}
  </style>
</head>
<body>
  ${htmlContent}
  <script>
    // Capture des erreurs pour affichage
    window.addEventListener('error', (e) => {
      console.error('Erreur dans le code:', e.message);
    });
    
    // Code utilisateur
    try {
      ${jsContent}
    } catch (error) {
      console.error('Erreur JavaScript:', error);
    }
  </script>
</body>
</html>
    `;

    // Injection dans l'iframe via srcdoc
    iframe.srcdoc = fullHTML;
  }

  /**
   * Réinitialise la preview aux fichiers d'origine
   * @returns {Object} - Les fichiers d'origine
   */
  function reset() {
    update(originalFiles);
    return originalFiles;
  }

  // Mise à jour initiale
  update(initialFiles);

  return {
    update: debounce(update, 500), // Debounce de 500ms
    updateImmediate: update, // Sans debounce pour le reset
    reset,
  };
}
