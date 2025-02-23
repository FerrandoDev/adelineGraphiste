import Swiper from 'swiper';
import {Keyboard, Navigation, Pagination} from 'swiper/modules';
// import {breakpoint} from '../util/variables';
import 'swiper/css';

Swiper.use([Pagination, Navigation, Keyboard]);

class AllSwiper {

  constructor() {
    this.selector = '[data-js-slideshow]';
    this.selectorRebondSwiper = document.querySelector('.rebond.swiper');
    this.selectorTestimonialSwiper = document.querySelector('.testimonials__swiper.swiper');
    this.swiperInstance = null;

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
      this.swiperInstance = new Swiper(this.selectorRebondSwiper, {
        direction: 'horizontal',
        slidesPerView: 1.2,
        slidesPerGroup: 1,
        spaceBetween: 20,
        centeredSlides: true,
        loop: true,
        pagination: {
          el: '.rebond .swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.rebond .swiper-button-next',
          prevEl: '.rebond .swiper-button-prev',
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
    if (this.selectorTestimonialSwiper) {

      new Swiper('.testimonials__swiper', {
        loop: true,
        centeredSlides: true,
        slidesPerView: 1., // Affiche un morceau des slides voisines
        spaceBetween: 30,   // Espace entre les slides
        effect: 'slide',    //
        breakpoints: {
          960: {
            slidesPerView: 1.9,
            spaceBetween: 70,
          },
        },
        // Navigation
        navigation: {
          nextEl: '.testimonials .swiper-button-next',
          prevEl: '.testimonials .swiper-button-prev',
        },
      });
    }

  }
}

export default (new AllSwiper());

