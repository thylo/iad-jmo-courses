// ---------- Boîte à outils : ne pas modifier ----------
// Chargé par index.html, juste avant sketch.js.

let billy;
let raoul;

async function chargerPersonnages() {
  billy = await loadImage("billy.png");
  raoul = await loadImage("raoul.png");
}

function dessinerPersonnage(personnage, x, y, largeur) {
  let hauteur = largeur * personnage.height / personnage.width;
  image(personnage, x - largeur / 2, y - hauteur, largeur, hauteur);
}

function dessinerBilly(x, y, largeur) {
  dessinerPersonnage(billy, x, y, largeur);
}

function dessinerRaoul(x, y, largeur) {
  dessinerPersonnage(raoul, x, y, largeur);
}

function dessinerChateau(x, y, largeur) {
  push();
  translate(x, y);
  scale(largeur / 400);
  stroke(40);
  strokeWeight(4);
  strokeJoin(ROUND);

  // le mur, avec ses créneaux
  fill(175, 178, 186);
  rect(-120, -220, 240, 220);
  rect(-105, -245, 30, 25);
  rect(-45, -245, 30, 25);
  rect(15, -245, 30, 25);
  rect(75, -245, 30, 25);

  // la porte, herse baissée
  fill(45, 40, 40);
  rect(-50, -150, 100, 150, 50, 50, 0, 0);
  stroke(120, 124, 135);
  strokeWeight(6);
  line(-25, -138, -25, -3);
  line(0, -144, 0, -3);
  line(25, -138, 25, -3);
  line(-44, -95, 44, -95);
  line(-46, -45, 46, -45);

  // les deux tours
  stroke(40);
  strokeWeight(4);
  fill(175, 178, 186);
  rect(-190, -320, 70, 320);
  rect(120, -320, 70, 320);
  fill(45, 40, 40);
  rect(-161, -270, 12, 44, 6, 6, 0, 0);
  rect(149, -270, 12, 44, 6, 6, 0, 0);

  // les toits et les drapeaux
  fill(165, 42, 42);
  triangle(-200, -320, -155, -410, -110, -320);
  triangle(110, -320, 155, -410, 200, -320);
  line(-155, -410, -155, -460);
  line(155, -410, 155, -460);
  fill(220, 180, 60);
  triangle(-155, -460, -120, -448, -155, -436);
  triangle(155, -460, 190, -448, 155, -436);

  pop();
}

function parler(texte, x, y) {
  push();
  textSize(18);
  let largeur = textWidth(texte) + 30;
  noStroke();
  fill(250);
  rect(x, y - 50, largeur, 40, 10);
  triangle(x + 15, y - 11, x + 35, y - 11, x + 15, y + 5);
  fill(20);
  textAlign(LEFT, CENTER);
  text(texte, x + 15, y - 30);
  pop();
}

function lancerDe(faces) {
  return floor(random(1, faces + 1));
}

function sontProches(x1, y1, x2, y2, distance) {
  return dist(x1, y1, x2, y2) < distance;
}
