// Éléments de la scène
const personnage = document.querySelector('#personnage');
const texteEmotion = document.querySelector('#emotion');

// États émotionnels possibles
const emotions = {
  proche: "Vous êtes si près... Je me sens observé.",
  distant: "Vous vous éloignez... Je suis seul.",
  centre: "Votre regard me réchauffe.",
  bord: "Vous regardez ailleurs, comme toujours."
};

// QUAND le spectateur bouge sa souris
document.addEventListener('mousemove', (geste) => {
  
  // Capturer la position de son regard
  const regardX = geste.clientX;
  const regardY = geste.clientY;
  
  // Le personnage est au centre de l'écran
  const centreX = window.innerWidth / 2;
  const centreY = window.innerHeight / 2;
  
  // Calculer la distance entre le regard et le personnage
  const distanceX = regardX - centreX;
  const distanceY = regardY - centreY;
  const distance = Math.sqrt(distanceX * distanceX + distanceY * distanceY);
  
  // EMBRANCHEMENT NARRATIF : réaction selon la distance
  personnage.classList.remove('proche', 'distant');
  
  if (distance < 100) {
    // Regard très proche : intimité
    personnage.classList.add('proche');
    texteEmotion.textContent = emotions.proche;
    
  } else if (distance < 300) {
    // Regard à distance moyenne : présence
    texteEmotion.textContent = emotions.centre;
    
  } else {
    // Regard éloigné : solitude
    personnage.classList.add('distant');
    texteEmotion.textContent = emotions.distant;
  }
  
  // Le personnage suit le regard selon un axe de rotation
  const rotationX = (distanceY / window.innerHeight) * 10;
  const rotationY = (distanceX / window.innerWidth) * 10;
  personnage.style.transform = `rotateX(${rotationX}deg) rotateY(${rotationY}deg)`;
  
  // Les yeux suivent la souris
  const yeux = document.querySelectorAll('.oeil');
  yeux.forEach(oeil => {
    const oeilRect = oeil.getBoundingClientRect();
    const oeilX = oeilRect.left + oeilRect.width / 2;
    const oeilY = oeilRect.top + oeilRect.height / 2;
    
    const angle = Math.atan2(regardY - oeilY, regardX - oeilX);
    const distance = Math.min(5, Math.hypot(regardX - oeilX, regardY - oeilY) / 50);
    
    const moveX = Math.cos(angle) * distance;
    const moveY = Math.sin(angle) * distance;
    
    oeil.style.transform = `translateY(-50%) translate(${moveX}px, ${moveY}px)`;
  });
});
