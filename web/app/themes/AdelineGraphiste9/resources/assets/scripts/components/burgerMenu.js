import gsap from 'gsap';

/**
 * Classe pour animer le burger menu avec GSAP.
 */
export default class BurgerMenu {

  constructor() {
    this.burgerMenu = document.querySelector('.burger-menu');
    if (!this.burgerMenu) {
      console.error(`impossible de trouver l'élément : ${this.burgerMenu}`);
    }
  }

  init() {
    if (!this.burgerMenu) return;

    // Sélection des éléments avec querySelector / querySelectorAll
    this.burgerTop = document.querySelector('.burger-menu-piece.top');
    this.burgerBot = document.querySelector('.burger-menu-piece.bottom');
    this.burgerMid = document.querySelector('.burger-menu-piece.middle');
    this.burgerSidebar = document.querySelector('.sidebar');
    this.sideText = document.querySelectorAll('.sidetext');
    this.burgerWhole = document.querySelectorAll('.burger-menu-piece.top, .burger-menu-piece.middle, .burger-menu-piece.bottom');
    this.navigation = document.querySelector('.menu-menu-header-container');
    // Création de la timeline GSAP modernisée
    const tl = gsap.timeline({paused: true, reversed: true, defaults: {ease: 'power1.inOut'}});
    tl
      .to(this.burgerTop, {duration: 0.5, y: 11, yoyo: true})
      .to(this.burgerBot, {duration: 0.5, y: -11, yoyo: true}, '<')
      .to(this.burgerTop, {duration: 0.5, rotation: 585})
      .to(this.burgerMid, {duration: 0.5, rotation: 585}, '<')
      .to(this.burgerBot, {duration: 0.5, rotation: 675}, '<')
      .to(this.burgerSidebar, {duration: 0.5, x: '100vw', ease: 'power2.out'}, '>')
      .to(this.burgerWhole, {duration: 0.2, borderColor: '#895440'}, '<')
    // .to(this.sideText, { duration: 0.2, opacity: 1, y: 0, stagger: 0.1, ease: 'power1.out'}, '<')
    // .to(this.sideText, { duration: 0.2, color: '#895440'});

    // Ajout de l'écouteur d'événement pour le clic sur la zone de déclenchement
    const clickRegion = document.querySelector('.burger-click-region');
    if (clickRegion) {
      clickRegion.addEventListener('click', () => {
        tl.reversed() ? tl.play() : tl.reverse();
        if (!this.clickRegion.classList.contains('active')) {
          this.navigation.style.display = 'block';
          this.clickRegion.classList.add('active');
        } else {
          this.clickRegion.classList.remove('active');
          this.navigation.style.display = 'none';
        }
      });
    }
  }
}
