export default class ActiveLinkManager {
  constructor(navSelector = '.nav-links', activeClass = 'active') {
    this.navLinks = document.querySelectorAll(navSelector);
    this.activeClass = activeClass;

    this.init();
  }

  init() {
    console.log(this.navLinks);

    if (this.navLinks.length > 0) {
      this.setActiveOnLoad();

    }
  }

  setActiveOnLoad() {
    const currentURL = window.location.href;
console.log(this.navLinks);
console.log(currentURL);
    // Parcourt tous les liens pour trouver celui qui correspond à l'URL active
    this.navLinks.forEach(link => {
      if (link.href === currentURL) {
        link.classList.add(this.activeClass);
      } else {
        link.classList.remove(this.activeClass);
      }
    });
  }
}

