// ========================================
// MENU BURGER INTERACTIF
// ========================================

// Sélectionner les éléments
const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('.main-nav');

// Vérifier que les éléments existent
if (menuToggle && mainNav) {
  // Écouter le click sur le bouton
  menuToggle.addEventListener('click', () => {
    // Alterner la classe 'is-open' sur la navigation
    mainNav.classList.toggle('is-open');

    // Alterner la classe 'is-active' sur le bouton (pour l'animation X)
    menuToggle.classList.toggle('is-active');

    // Mettre à jour l'attribut aria-expanded pour l'accessibilité
    const isOpen = mainNav.classList.contains('is-open');
    menuToggle.setAttribute('aria-expanded', isOpen);
  });

  // Fermer le menu si on clique sur un lien (optionnel)
  const navLinks = document.querySelectorAll('.main-nav a');
  navLinks.forEach((link) => {
    link.addEventListener('click', () => {
      mainNav.classList.remove('is-open');
      menuToggle.classList.remove('is-active');
      menuToggle.setAttribute('aria-expanded', 'false');
    });
  });

  // Fermer le menu si on redimensionne la fenêtre (passage au desktop)
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      mainNav.classList.remove('is-open');
      menuToggle.classList.remove('is-active');
      menuToggle.setAttribute('aria-expanded', 'false');
    }
  });
}
