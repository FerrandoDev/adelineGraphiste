import Swiper from 'swiper';
import {Pagination, Navigation, Keyboard} from 'swiper/modules';
// import {breakpoint} from '../util/variables';
import 'swiper/css';
Swiper.use([Pagination, Navigation, Keyboard]);

class AllSwiper {

  constructor() {
    this.selector = '[data-js-slideshow]';
    this.selectorRebondSwiper = document.querySelector('.rebond.swiper');
    this.swiperInstance = null;
    // this.trigger = {
    //   pause: '[data-js-swiper-pause]',
    // };
  }

  init() {
    this.handleSwiper();
    window.addEventListener('resize', this.handleSwiper.bind(this));
  }

  /**
   * Méthode qui crée ou détruit le slider selon le viewport
   */
  handleSwiper() {
    // if (window.innerWidth <= breakpoint.laptop) {
      // Si on est en mobile et que le slider n'est pas encore instancié
      if (!this.swiperInstance && this.selectorRebondSwiper) {
        console.log('Initialisation du slider mobile');
        this.swiperInstance = new Swiper(this.selectorRebondSwiper, {
          direction: 'horizontal',
          slidesPerView: 1.2,
          slidesPerGroup: 1,
          spaceBetween: 20,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          keyboard: {
            enabled: true,
            onlyInViewport: true,
          },
          breakpoints: {
            425: {
              slidesPerView: 1.8,
              slidesPerGroup: 1,

              spaceBetween: 30,
            },
            768: {
              slidesPerView: 2.3,
              slidesPerGroup: 2,
              spaceBetween: 50,
            },
            960: {
              slidesPerView: 3.1,
              slidesPerGroup: 3,
              spaceBetween: 70,
            },
          },
          on: {
            // Quand le slide change (on peut aussi utiliser slideChangeTransitionEnd)
            slideChange() {
              const paginationContainer = this.pagination.el;
              // Sélectionne la bullet active
              if (paginationContainer) {

                const activeBullet = paginationContainer.querySelector('.swiper-pagination-bullet-active');
                if (activeBullet) {
                  // Calcule la position pour centrer la bullet active
                  const containerWidth = paginationContainer.offsetWidth;
                  const bulletLeft = activeBullet.offsetLeft;
                  const bulletWidth = activeBullet.offsetWidth;
                  const newScrollLeft = bulletLeft - containerWidth / 2 + bulletWidth / 2;

                  // Scrolle la pagination (smooth si le navigateur supporte scroll-behavior)
                  paginationContainer.scrollTo({
                    left: newScrollLeft,
                    behavior: 'smooth',
                  });
                }
              }
            },
          },

          // on: {
          //   init: function () {
          //     self.updateTabIndex.call(this);
          //   },
          //   slideChangeTransitionEnd: function () {
          //     self.updateTabIndex.call(this);
          //   }
          // },
          // Vous pouvez ajouter d'autres options ici si nécessaire
        });
      // }
    // } else {
    //   // Si on est en desktop et que le slider est instancié, on le détruit
    //   if (this.swiperInstance) {
    //     console.log('Détruire le slider en desktop');
    //     this.swiperInstance.destroy(true, true);
    //     this.swiperInstance = null;
    //   }
    }
  }


  // updateTabIndex() {
  //   // 'this.el' est l'élément conteneur du slider courant
  //   const sliderEl = this.el;
  //   const activeSlide = sliderEl.querySelector('.swiper-slide-active');
  //   const allLinks = sliderEl.querySelectorAll('a');
  //   allLinks.forEach(link => link.setAttribute("tabindex", "-1"));
  //   if (activeSlide) {
  //     activeSlide.querySelectorAll("a").forEach(link => link.setAttribute("tabindex", "0"));
  //   }
  // }


  // pause(swiper) {
  //   const self = this;
  //   const trigger = swiper.el.querySelector(self.trigger.pause);
  //   if (!trigger) return false;
  //
  //   // Initialisation des attributs d’accessibilité
  //   trigger.setAttribute('aria-label', 'Mettre en pause le diaporama');
  //   trigger.setAttribute('aria-pressed', 'false');
  //
  //   // Liaison de l'événement sur le bouton pause directement
  //   $(trigger).on('click', (e) => {
  //     const $btn = $(e.currentTarget);
  //     const isPaused = $btn.attr('data-js-swiper-pause') === 'true';
  //
  //     // Mise à jour des attributs d'accessibilité
  //     $btn.attr('aria-pressed', isPaused ? 'false' : 'true');
  //     $btn.attr('aria-label', isPaused ? 'Mettre en pause le diaporama' : 'Reprendre le diaporama');
  //
  //     // Pause ou reprise de l'autoplay
  //     if (!isPaused) {
  //       $btn.attr('data-js-swiper-pause', 'true');
  //       swiper.autoplay.stop();
  //       // console.log('pause');
  //     } else {
  //       $btn.attr('data-js-swiper-pause', 'false');
  //       swiper.autoplay.start();
  //       // console.log('play');
  //     }
  //   });
  // }
}

export default (new AllSwiper());

