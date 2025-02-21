// InfiniteMarquee.js
import gsap from 'gsap';

/**
 * Classe pour animer un défilement horizontal infini (type "marquee")
 * avec GSAP.
 */
export default class InfiniteMarquee {
  /**
   * @param {string} selector - Sélecteur du conteneur à animer (ex: '.entreprise .flex')
   * @param {number} duration - Durée de l'animation (en secondes)
   * @param {string} ease - Type d'easing GSAP (ex: 'linear', 'power1.inOut', etc.)
   */
  constructor(selector, duration = 15, ease = 'linear') {
    this.selector = selector
    this.duration = duration
    this.ease = ease

    // Sélectionne l'élément DOM
    this.container = document.querySelector(this.selector)

    if (!this.container) {
      console.error(`Impossible de trouver l'élément : ${this.selector}`)
    }
  }

  /**
   * Initialise le défilement infini : duplique le contenu et lance l'animation GSAP.
   */
  init() {
    console.log(this.container);
    if (!this.container) return

    // Duplique le contenu pour permettre un défilement en continu
    this.container.innerHTML += this.container.innerHTML

    // Largeur de la liste "simple" (la liste doublée fait 2x cette taille)
    const largeurOriginale = this.container.scrollWidth / 2

    // Crée l'animation GSAP
    gsap.to(this.container, {
      x: -largeurOriginale,
      duration: this.duration,
      ease: this.ease,
      repeat: -1, // Boucle infinie
      onRepeat: () => {
        // Réinitialise la position X à 0 à la fin de chaque cycle
        gsap.set(this.container, {x: 0})
      },
    })
  }
}

