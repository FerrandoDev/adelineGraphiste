import ActiveLinkManager from '../components/nav-links';
export default {
  init() {
    // JavaScript to be fired on all pages
    new ActiveLinkManager();
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
