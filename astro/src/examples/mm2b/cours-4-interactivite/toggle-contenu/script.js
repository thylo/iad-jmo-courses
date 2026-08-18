// ========================================
// TOGGLE DE CONTENU INTERACTIF
// ========================================

// Fonction pour gérer le toggle
function setupToggle(buttonSelector, contentSelector) {
  const buttons = document.querySelectorAll(buttonSelector);

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      // Récupérer l'ID de la cible depuis data-target
      const targetId = button.getAttribute('data-target');
      const targetContent = document.getElementById(targetId);

      if (targetContent) {
        // Alterner la classe hidden
        targetContent.classList.toggle('hidden');

        // Mettre à jour le bouton
        const isExpanded = !targetContent.classList.contains('hidden');
        button.setAttribute('aria-expanded', isExpanded);

        // Mettre à jour le texte du bouton si c'est un bouton de chapitre
        if (button.classList.contains('toggle-btn')) {
          const btnText = button.querySelector('.btn-text');
          if (btnText) {
            btnText.textContent = isExpanded ? 'Voir moins' : 'Voir plus';
          }
          // Ajouter/retirer la classe is-active pour l'animation de l'icône
          button.classList.toggle('is-active');
        }
      }
    });
  });
}

// Initialiser les toggles pour les chapitres
setupToggle('.toggle-btn', '.chapter-details');

// Initialiser les toggles pour la FAQ
setupToggle('.faq-question', '.faq-answer');

// ========================================
// BONUS : Gestion au clavier (Enter/Space)
// ========================================

document.querySelectorAll('.toggle-btn, .faq-question').forEach((button) => {
  button.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      button.click();
    }
  });
});
