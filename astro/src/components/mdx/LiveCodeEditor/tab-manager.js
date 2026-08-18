/**
 * Gestion du système d'onglets
 * Responsabilité: switching entre les différents éditeurs (HTML/CSS/JS)
 */

/**
 * Initialise le système d'onglets
 * @param {HTMLElement} container - Conteneur principal de l'éditeur
 */
export function initTabSwitching(container) {
  const tabs = container.querySelectorAll('.tab-btn');
  const editorWrappers = container.querySelectorAll('.editor-wrapper');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const targetLang = tab.getAttribute('data-tab');
      
      // Mise à jour des boutons
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      
      // Mise à jour des éditeurs
      editorWrappers.forEach(w => {
        if (w.getAttribute('data-lang') === targetLang) {
          w.classList.add('active');
        } else {
          w.classList.remove('active');
        }
      });
    });
  });

  // Activer le premier onglet par défaut
  if (tabs.length > 0) {
    tabs[0].click();
  }
}
