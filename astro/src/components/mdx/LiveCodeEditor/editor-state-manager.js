/**
 * Gestionnaire d'état centralisé pour l'éditeur
 * Responsabilité: coordonner les éditeurs CodeMirror et la preview
 */

import { createEditor, isDarkTheme } from './codemirror-setup.js';
import { createPreview } from './iframe-preview.js';

/**
 * Crée et gère l'état de l'éditeur live
 * @param {HTMLElement} container - Conteneur principal
 * @param {Object} files - Fichiers initiaux {html, css, js}
 * @returns {Object} - { initEditors, initPreview, setupReset }
 */
export function createEditorState(container, files) {
  const currentFiles = { ...files };
  const editors = {};
  let preview = null;

  // Détection du thème
  const isDark = isDarkTheme();

  /**
   * Initialise tous les éditeurs CodeMirror
   */
  function initEditors() {
    const editorWrappers = container.querySelectorAll('.editor-wrapper');
    
    editorWrappers.forEach((wrapper) => {
      const lang = wrapper.getAttribute('data-lang');
      if (!lang || !files[lang]) return;

      // Créer l'éditeur avec callback de changement
      editors[lang] = createEditor(
        wrapper,
        files[lang],
        lang,
        (newContent) => {
          currentFiles[lang] = newContent;
          if (preview) {
            preview.update(currentFiles);
          }
        },
        isDark
      );
    });
  }

  /**
   * Initialise la preview iframe
   */
  function initPreview() {
    const previewContainer = container.querySelector('.preview-container');
    if (previewContainer) {
      preview = createPreview(previewContainer, files);
    }
  }

  /**
   * Configure le bouton de réinitialisation
   */
  function setupReset() {
    const resetBtn = container.querySelector('.reset-btn');
    if (!resetBtn || !preview) return;

    resetBtn.addEventListener('click', () => {
      const originalFiles = preview.reset();
      
      // Réinitialiser les éditeurs
      Object.keys(editors).forEach((lang) => {
        if (originalFiles[lang]) {
          const editor = editors[lang];
          editor.dispatch({
            changes: {
              from: 0,
              to: editor.state.doc.length,
              insert: originalFiles[lang]
            }
          });
          currentFiles[lang] = originalFiles[lang];
        }
      });
    });
  }

  return {
    initEditors,
    initPreview,
    setupReset
  };
}
