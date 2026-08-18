/**
 * Configuration CodeMirror 6
 * Inspiré de l'approche MDN pour l'édition de code web
 */

import { EditorState } from '@codemirror/state';
import { EditorView, keymap } from '@codemirror/view';
import { defaultKeymap } from '@codemirror/commands';
import { html } from '@codemirror/lang-html';
import { css } from '@codemirror/lang-css';
import { javascript } from '@codemirror/lang-javascript';
import { oneDark } from '@codemirror/theme-one-dark';

/**
 * Crée un éditeur CodeMirror
 * @param {HTMLElement} parent - Élément parent
 * @param {string} content - Contenu initial
 * @param {string} language - Langage (html, css, js)
 * @param {Function} onChange - Callback appelé à chaque modification
 * @param {boolean} isDark - Thème sombre
 * @returns {EditorView}
 */
export function createEditor(parent, content, language, onChange, isDark) {
  // Sélection de l'extension de langage
  const langExtension = {
    html: html(),
    css: css(),
    js: javascript(),
  }[language] || html();

  // Extensions de base
  const extensions = [
    langExtension,
    keymap.of(defaultKeymap),
    EditorView.lineWrapping,
    EditorView.updateListener.of((update) => {
      if (update.docChanged) {
        onChange(update.state.doc.toString());
      }
    }),
  ];

  // Ajout du thème sombre si nécessaire
  if (isDark) {
    extensions.push(oneDark);
  }

  const state = EditorState.create({
    doc: content,
    extensions,
  });

  return new EditorView({
    state,
    parent,
  });
}

/**
 * Détecte le thème système
 * @returns {boolean}
 */
export function isDarkTheme() {
  if (typeof window === 'undefined') return false;
  return window.matchMedia('(prefers-color-scheme: dark)').matches;
}
