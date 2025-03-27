import ActiveLinkManager from '../components/nav-links';
import Swiper from '../components/AllSwiper';
import GsapEntreprise from '../components/InfiniteMarquee';
import GsapBurgerMenu from '../components/burgerMenu';

export default {
  init() {
    // JavaScript to be fired on all pages
    new ActiveLinkManager();
    Swiper.init();
    const entrepriseHome = new GsapEntreprise('.entreprise .flex', 20, 'linear');
    const burgerMenu = new GsapBurgerMenu();
    entrepriseHome.init();
    burgerMenu.init();
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
