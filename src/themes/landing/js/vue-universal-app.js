import { Auth } from './vue/models/User.js';
import authService from './vue/services/authService.js';

/*---------- 02. Mega menu ----------*/
const handleMegaMenu = () => {
  const megaMenuLinks = document.querySelectorAll("li.menu-item-has-children.desktop-menu-item.mega-menu-wrap");
  document.addEventListener("click", (event) => {
      const megaMenu = document.querySelector(".mega-menu.active");
      if (megaMenu && !(event.target.closest(".mega-menu-wrap") || event.target.closest(".mega-menu"))) {
          megaMenu.style.visibility = "hidden";
          megaMenu.style.opacity = 0;
          megaMenu.style.zIndex = -1;
          megaMenu.classList.remove("active");
      }
  });
  megaMenuLinks.forEach(link => {
      link.addEventListener("click", (event) => {
          const megaMenu = link.querySelector(".mega-menu");
          if (megaMenu) {
              if (
                  (event.target.parentElement && event.target.parentElement.classList.contains('mega-menu-wrap')) ||
                  (event.target.parentElement && event.target.parentElement.parentElement && event.target.parentElement.parentElement.classList.contains('mega-menu-wrap'))
              ) {
                  event.preventDefault();
              }
              if (megaMenu.style.visibility === "visible") {
                  megaMenu.style.visibility = "hidden";
                  megaMenu.style.opacity = 0;
                  megaMenu.style.zIndex = -1;
                  megaMenu.classList.remove("active");
              } else {
                  megaMenu.classList.add("active");
                  megaMenu.style.visibility = "visible";
                  megaMenu.style.opacity = 1;
                  megaMenu.style.zIndex = 9;
              }
          }
      });
  });
}
document.addEventListener("DOMContentLoaded", function () {
    handleMegaMenu();
})

/*---------- 04. Sticky fix ----------*/
function handleStickyNavigation() {
  var $topbar = $('.th-topbar');
  var $stickyWrapper = $('.sticky-wrapper');

  if ($topbar.length && $stickyWrapper.length) {
      var topbarHeight = $topbar.outerHeight() || 50; // Default to 50px if height not found

      function checkSticky() {
          var scrollTop = $(window).scrollTop();

          // When scroll position reaches or exceeds topbar height, make sticky-wrapper sticky
          // This happens when the topbar has scrolled up and touches the sticky-wrapper at the top
          if (scrollTop >= topbarHeight) {
              if (!$stickyWrapper.hasClass('sticky')) {
                  $stickyWrapper.addClass('sticky');
                  const megaMenu = document.querySelector(".mega-menu.active");
                  if (megaMenu) {
                      megaMenu.style.visibility = "hidden";
                      megaMenu.style.opacity = 0;
                      megaMenu.style.zIndex = -1;
                      megaMenu.classList.remove("active");
                  }
              }
          } else {
              if ($stickyWrapper.hasClass('sticky')) {
                  $stickyWrapper.removeClass('sticky');
              }
          }
      }

      // Check on scroll
      $(window).on('scroll', function () {
          checkSticky();
      });

      // Check on load and resize
      $(window).on('load resize', function () {
          topbarHeight = $topbar.outerHeight() || 50;
          checkSticky();
      });

      // Initial check
      checkSticky();
  }
}

// sticky resource tab navigation
function handleStickyResourceTabNavigation() {
  var $topbar = $('.th-topbar');
  var $stickyWrapper = $('.sticky-wrapper');
  var $tabNav = $('.th-head-tab-navigation-container');
  var $tabNavWrapper = $tabNav.parent();
  var $footer = $('.th-footer');
  // var STICKY_MIN_WIDTH = 992;

  if (!$tabNav.length) return;

  function getHeaderHeight() {
      var topbarHeight = $topbar.length ? $topbar.outerHeight() : 0;
      var navHeight = $stickyWrapper.length ? $stickyWrapper.outerHeight() : 80;
      if ($stickyWrapper.hasClass('sticky')) {
          return navHeight;
      }
      return topbarHeight + navHeight;
  }

  function checkSticky() {
      var scrollTop = $(window).scrollTop();
      var headerHeight = getHeaderHeight();
      var desktopMenuSticky = $stickyWrapper.hasClass('sticky');
      var viewportWidth = window.innerWidth || $(window).width();

      // if (viewportWidth < STICKY_MIN_WIDTH) {
      //     makeUnsticky();
      //     return;
      // }

      if (!desktopMenuSticky) {
          makeUnsticky();
          return;
      }

      var tabNavOffsetTop;
      if ($tabNav.hasClass('sticky')) {
          tabNavOffsetTop = $tabNav.data('sticky-original-top');
          if (tabNavOffsetTop == null) {
              makeUnsticky();
              return;
          }
      } else {
          tabNavOffsetTop = $tabNav.offset().top;
          $tabNav.data('sticky-original-top', tabNavOffsetTop);
      }

      var stickyThreshold = tabNavOffsetTop - headerHeight;
      if (scrollTop < stickyThreshold) {
          makeUnsticky();
          return;
      }

      // Unstick when tab nav would touch footer
      if ($footer.length) {
          var tabNavHeight = $tabNav.outerHeight();
          var footerTouchLimit = $footer.offset().top - headerHeight - tabNavHeight;
          if (scrollTop >= footerTouchLimit) {
              makeUnsticky();
              return;
          }
      }

      makeSticky(headerHeight);
  }

  function makeSticky(headerHeight) {
      if ($tabNav.hasClass('sticky')) return;
      var tabNavHeight = $tabNav.outerHeight();
      $tabNav.addClass('sticky');
      $tabNav.css({
          'position': 'fixed',
          'top': headerHeight + 'px',
          'left': '0',
          'right': '0'
      });
      $tabNavWrapper.css('min-height', tabNavHeight + 'px');
  }

  function makeUnsticky() {
      if (!$tabNav.hasClass('sticky')) return;
      $tabNav.removeClass('sticky');
      $tabNav.removeData('sticky-original-top');
      $tabNav.css({ 'position': '', 'top': '', 'left': '', 'right': '' });
      $tabNavWrapper.css('min-height', '');
  }

  function updateStickyPosition() {
      if (!$tabNav.hasClass('sticky')) return;
      var headerHeight = getHeaderHeight();
      $tabNav.css('top', headerHeight + 'px');
  }

  $(window).on('scroll', function () { checkSticky(); });
  $(window).on('load resize', function () {
      checkSticky();
      updateStickyPosition();
  });
  checkSticky();
}

// Initialize sticky navigation on document ready
document.addEventListener("DOMContentLoaded", function () {
  handleStickyNavigation();
  handleStickyAccountNavigation();
  handleStickyResourceTabNavigation();
  // handleStickyResourceSidebar();
});

/*---------- 04a. Generic Sticky Left Navigation ----------*/
/**
* Makes any left navigation sidebar sticky when scrolling. Syncs with desktop menu
* sticky state and supports footer touch (position absolute with bottom offset).
* @param {Object} opts - Configuration
* @param {jQuery|string} opts.column - Column container (or sidebar selector for columnFromSidebar)
* @param {jQuery|string} opts.sidebar - Sidebar element that becomes sticky
* @param {jQuery|string} [opts.columnFromSidebar] - If truthy, column = sidebar.closest(this)
* @param {jQuery|string} [opts.stickyWrapper] - Header nav for sticky state sync (default: .sticky-wrapper)
* @param {jQuery|string} [opts.topbar] - Topbar for header height (default: .th-topbar)
* @param {jQuery|string} [opts.footer] - Footer for touch behavior (empty = disabled)
* @param {number} [opts.footerTouchBottom] - Bottom px when touching footer (0 = disable)
* @param {function} [opts.getColumnMinHeight] - () => number for column min-height
* @param {jQuery|string} [opts.guard] - Must exist to run (e.g. #vue-resource-app)
* @param {Object} [opts.retry] - { selector, interval, max } for async sidebar mount
* @param {jQuery|string} [opts.tabNav] - Tab nav element; sidebar sticks below its bottom (e.g. .th-head-tab-navigation-container)
* @param {boolean} [opts.compactHeaderHeight] - Match resource tab nav header height when main nav is sticky
* @param {number|function} [opts.stickyWidth] - Fixed width in px when sticky, or ($col, $sb) => number
* @param {number|function} [opts.stickyLeft] - Fixed left offset in px when sticky, or ($col, $sb) => number
*/
function createStickySidebar(opts) {
  var columnSel = typeof opts.column === 'string' ? opts.column : null;
  var $column = columnSel ? $(opts.column) : opts.column;
  var sidebarSel = typeof opts.sidebar === 'string' ? opts.sidebar : null;
  var $stickyWrapper = (opts.stickyWrapper ? (typeof opts.stickyWrapper === 'string' ? $(opts.stickyWrapper) : opts.stickyWrapper) : $('.sticky-wrapper'));
  var $topbar = opts.topbar ? (typeof opts.topbar === 'string' ? $(opts.topbar) : opts.topbar) : $('.th-topbar');
  var $footer = opts.footer ? (typeof opts.footer === 'string' ? $(opts.footer) : opts.footer) : $('.th-footer');
  var $tabNav = opts.tabNav ? (typeof opts.tabNav === 'string' ? $(opts.tabNav) : opts.tabNav) : null;
  var compactHeaderHeight = !!opts.compactHeaderHeight;
  var footerTouchBottom = opts.footerTouchBottom != null ? opts.footerTouchBottom : 0;
  var getColumnMinHeight = opts.getColumnMinHeight;
  var guard = opts.guard ? (typeof opts.guard === 'string' ? $(opts.guard) : opts.guard) : null;
  var retry = opts.retry || null;
  var columnFromSidebar = opts.columnFromSidebar;
  var disableOnMobile = !!opts.disableOnMobile;
  var mobileMaxWidth = opts.mobileMaxWidth != null ? opts.mobileMaxWidth : 540;
  var stickyWidth = opts.stickyWidth != null ? opts.stickyWidth : null;
  var stickyLeft = opts.stickyLeft != null ? opts.stickyLeft : null;

  if (guard && !guard.length) return;

  function resolveStickyWidth($colEl, $sb) {
      if (typeof stickyWidth === 'function') return stickyWidth($colEl, $sb);
      if (stickyWidth != null) return stickyWidth;
      return $colEl[0].getBoundingClientRect().width;
  }

  function resolveStickyLeft($colEl, $sb) {
      if (typeof stickyLeft === 'function') return stickyLeft($colEl, $sb);
      if (stickyLeft != null) return stickyLeft;
      return $colEl[0].getBoundingClientRect().left;
  }

  function $sidebar() {
      return sidebarSel ? $(sidebarSel) : opts.sidebar;
  }
  function $col() {
      var $s = $sidebar();
      if (columnFromSidebar && $s.length) {
          return $s.closest(typeof columnFromSidebar === 'string' ? columnFromSidebar : '.col-lg-4');
      }
      return columnSel ? $(columnSel) : $column;
  }

  if (!columnFromSidebar && !retry) {
      var $c = $col();
      var $s = $sidebar();
      if (!$c.length || !$s.length) return;
  }
  if (!$stickyWrapper.length) return;

  function getHeaderHeight() {
      var topbarHeight = $topbar.length ? $topbar.outerHeight() : 0;
      var navHeight = $stickyWrapper.length ? $stickyWrapper.outerHeight() : 80;
      if (compactHeaderHeight && $stickyWrapper.hasClass('sticky')) {
          return navHeight;
      }
      return topbarHeight + navHeight;
  }

  /** Viewport offset where the sidebar may stick (below tab nav when present). */
  function getStickyTopOffset(headerHeight) {
      if (!$tabNav || !$tabNav.length) {
          return headerHeight;
      }
      if ($tabNav.hasClass('sticky')) {
          return headerHeight + $tabNav.outerHeight();
      }
      var tabNavBottom = $tabNav[0].getBoundingClientRect().bottom;
      return Math.max(headerHeight, tabNavBottom);
  }

  function checkSticky() {
      var $sb = $sidebar();
      var $colEl = $col();
      if (!$sb.length || !$colEl.length) return;

      if (disableOnMobile && window.innerWidth <= mobileMaxWidth) {
          $colEl.css('min-height', '');
          makeUnsticky($sb, $colEl);
          return;
      }

      var scrollTop = $(window).scrollTop();
      var headerHeight = getHeaderHeight();
      var stickyTopOffset = getStickyTopOffset(headerHeight);
      var desktopMenuSticky = $stickyWrapper.hasClass('sticky');

      if (!desktopMenuSticky) {
          makeUnsticky($sb, $colEl);
          return;
      }
      var sidebarOffsetTop;
      if ($sb.hasClass('sticky')) {
          sidebarOffsetTop = $sb.data('sticky-original-top');
          if (sidebarOffsetTop == null) {
              makeUnsticky($sb, $colEl);
              return;
          }
      } else {
          sidebarOffsetTop = $sb.offset().top;
          $sb.data('sticky-original-top', sidebarOffsetTop);
      }

      var stickyThreshold = sidebarOffsetTop - stickyTopOffset;
      if (scrollTop < stickyThreshold) {
          makeUnsticky($sb, $colEl);
          return;
      }

      if (footerTouchBottom && $footer.length) {
          var sidebarHeight = $sb.outerHeight();
          var footerTouchLimit = $footer.offset().top - stickyTopOffset - sidebarHeight;
          if (scrollTop >= footerTouchLimit) {
              makeStickyAtFooter($sb, $colEl, footerTouchBottom, getColumnMinHeight);
              return;
          }
      }

      makeSticky($sb, $colEl, stickyTopOffset, getColumnMinHeight);
  }

  function makeSticky($sb, $colEl, stickyTopOffset, getMinH) {
      if ($sb.hasClass('sticky') && $sb.data('sticky-mode') === 'fixed') {
          $sb.css({
              'top': stickyTopOffset + 'px',
              'left': resolveStickyLeft($colEl, $sb) + 'px',
              'width': resolveStickyWidth($colEl, $sb) + 'px'
          });
          return;
      }
      $sb.addClass('sticky').data('sticky-mode', 'fixed');
      $sb.css({
          'position': 'fixed',
          'top': stickyTopOffset + 'px',
          'bottom': '',
          'left': resolveStickyLeft($colEl, $sb) + 'px',
          'width': resolveStickyWidth($colEl, $sb) + 'px'
      });
      if (getMinH) {
          $colEl.css('min-height', getMinH($colEl, $sb) + 'px');
      }
  }

  function makeStickyAtFooter($sb, $colEl, bottomPx, getMinH) {
      var footerWidth = resolveStickyWidth($colEl, $sb);
      if ($sb.hasClass('sticky') && $sb.data('sticky-mode') === 'footer') {
          $sb.css('width', footerWidth + 'px');
          return;
      }
      $sb.addClass('sticky').data('sticky-mode', 'footer');
      $colEl.css('position', 'relative');
      $sb.css({
          'position': 'absolute',
          'top': '',
          'bottom': bottomPx + 'px',
          'left': '0',
          'width': footerWidth + 'px'
      });
      if (getMinH) {
          $colEl.css('min-height', getMinH($colEl, $sb) + 'px');
      }
  }

  function makeUnsticky($sb, $colEl) {
      if (!$sb.hasClass('sticky')) return;
      $sb.removeClass('sticky');
      $sb.removeData('sticky-original-top');
      $sb.removeData('sticky-mode');
      $sb.css({ 'position': '', 'top': '', 'bottom': '', 'left': '', 'width': '' });
      $colEl.css('min-height', '');
  }

  function updateStickyPosition() {
      var $sb = $sidebar();
      var $colEl = $col();
      if (!$sb.length || !$sb.hasClass('sticky')) return;
      var mode = $sb.data('sticky-mode');
      if (mode === 'footer') {
          $sb.css('width', resolveStickyWidth($colEl, $sb) + 'px');
      } else {
          var stickyTopOffset = getStickyTopOffset(getHeaderHeight());
          $sb.css({
              'top': stickyTopOffset + 'px',
              'left': resolveStickyLeft($colEl, $sb) + 'px',
              'width': resolveStickyWidth($colEl, $sb) + 'px'
          });
      }
      if (getColumnMinHeight) {
          $colEl.css('min-height', getColumnMinHeight($colEl, $sb) + 'px');
      }
  }

  $(window).on('scroll', function () { checkSticky(); });
  $(window).on('load resize', function () {
      checkSticky();
      updateStickyPosition();
  });
  checkSticky();

  if (retry && retry.selector) {
      var retryCount = 0;
      var retryInterval = setInterval(function () {
          checkSticky();
          if ($(retry.selector).length || ++retryCount > (retry.max || 20)) {
              clearInterval(retryInterval);
          }
      }, retry.interval || 500);
  }
}

/** Sticky metrics from in-flow .col-12 (Bootstrap gutter padding), not the outer column edge. */
function getAccountStickyMetrics($col, $sb) {
  var $innerCol = $col.find('> .row.th-sidebard > .col-12, > .th-sidebard > .col-12').first();
  if (!$innerCol.length) {
      $innerCol = $col.children('.row').children('.col-12').first();
  }
  if ($innerCol.length) {
      var el = $innerCol[0];
      var rect = el.getBoundingClientRect();
      var style = window.getComputedStyle(el);
      var padLeft = parseFloat(style.paddingLeft) || 0;
      var padRight = parseFloat(style.paddingRight) || 0;
      return {
          left: rect.left + padLeft,
          width: rect.width - padLeft - padRight
      };
  }
  if ($sb.length) {
      var sbRect = $sb[0].getBoundingClientRect();
      return { left: sbRect.left, width: sbRect.width };
  }
  var colRect = $col[0].getBoundingClientRect();
  return { left: colRect.left, width: colRect.width };
}

function handleStickyAccountNavigation() {
  var $column = $('#account-navigation');
  var $documentList = $('#th-documents-list');
  if (!$column.length) return;
  if (window.innerWidth > 540) {
      $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');
  } else {
      $column.css('min-height', '');
  }
  createStickySidebar({
      column: $column,
      sidebar: '#account-sidebar-list',
      stickyLeft: function ($col, $sb) {
          return getAccountStickyMetrics($col, $sb).left;
      },
      stickyWidth: function ($col, $sb) {
          return getAccountStickyMetrics($col, $sb).width;
      },
      footerTouchBottom: 100,
      disableOnMobile: true,
      mobileMaxWidth: 540,
      getColumnMinHeight: function ($col, $sb) {
          return $documentList.length ? $documentList.outerHeight() : $col.outerHeight();
      }
  });
}

/**
* Normalizes DOM element, array, or jQuery to jQuery object.
* Vue passes querySelector/querySelectorAll results which lack .length, .css(), etc.
*/
function toJQuery(el) {
  if (!el) return $();
  if (el.jquery) return el;
  if (Array.isArray(el)) return $(el.length ? el[0] : []);
  return $(el);
}

window.handleStickyResourceSidebar = function (column, documentList, navigation) {
  var $column = toJQuery(column);
  var $documentList = toJQuery(documentList);
  var $sidebar = toJQuery(navigation);
  if (window.innerWidth <= 992) return;
  if (!$column.length) return;

  $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');

  createStickySidebar({
      column: $column,
      sidebar: $sidebar,
      tabNav: '.th-head-tab-navigation-container',
      compactHeaderHeight: true,
      footerTouchBottom: 100,
      disableOnMobile: true,
      mobileMaxWidth: 992,
      getColumnMinHeight: function ($col, $sb) {
          return $documentList.length ? $documentList.outerHeight() : $col.outerHeight();
      }
  });
};
$(window).on('resourceFilter', function () {
  // Scroll window to top when resourceFilter event is triggered
  if (window.scrollY > 300) {
      window.scrollTo({ top: 190, behavior: 'smooth' });
  }
  setTimeout(() => {
      window.handleStickyOnResourceFilter();
      $(window).trigger('resize'); // Re-check tab nav sticky after content updates
  }, 1000);
});
window.handleStickyOnResourceFilter = function () {
  const $column = $('#filter-navigation-container');
  const $documentList = $('#th-tab-navigation-content');
  const $sidebar = $('#th-resource-sidebar-sticky');
  if (window.innerWidth <= 992) {
      $sidebar.removeClass('sticky');
      $sidebar.css({ 'position': '', 'top': '', 'left': '', 'width': '' });
      $column.css('min-height', '');
      return;
  }
  $column.css('min-height', 'inherit');
  if ($column.length && $documentList.length && $sidebar.length) {
      $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');
  }
  createStickySidebar({
      column: $column,
      sidebar: $sidebar,
      tabNav: '.th-head-tab-navigation-container',
      compactHeaderHeight: true,
      footerTouchBottom: 100,
      disableOnMobile: true,
      mobileMaxWidth: 992,
      getColumnMinHeight: function ($col, $sb) {
          return $documentList.length ? $documentList.outerHeight() : $col.outerHeight();
      }
  });
}


// Vue.Draggable 2.20.0 expects Sortable to be available
// Make sure it's on window
if (typeof Sortable !== 'undefined') {
    window.Sortable = window.Sortable || Sortable;
}
// Also try to make it available for CommonJS/UMD module systems
if (typeof module !== 'undefined' && module?.exports && typeof window.Sortable !== 'undefined') {
    module.exports.Sortable = window.Sortable;
}

// ----- set busy function to set busy state to the element -----
const setBusy = (el, busy) => {
  el.toggleAttribute('aria-busy', busy);
  el.style.pointerEvents = busy ? 'none' : '';
  if (el.tagName === 'BUTTON') el.disabled = busy;
};

// ------ toggle icon function to toggle icon visibility -----
const toggleIcon = (el, show) => {
  const icon = el.querySelector('i');
  if (icon) icon.style.display = show ? '' : 'none';
};

// ------ get spinner function to get spinner element -----
const getSpinner = (el, color = 'text-primary') => {
  let spinner = el.querySelector('.spinner-border');
  if (spinner) return { spinner, created: false };

  spinner = document.createElement('div');
  spinner.className = `spinner-border ${color}`;
  spinner.setAttribute('role', 'status');
  spinner.innerHTML = `<span class="visually-hidden ${color}">Loading...</span>`;
  el.appendChild(spinner);

  return { spinner, created: true };
};

// ------ is item added function to check if item is added -----
const isItemAdded = ({ id, model }) => {
  const items = pinboardApp?.$store?.getters?.items || [];
  return items.some(it =>
    String(it.model_type) === String(model) &&
    parseInt(it.model_id) === parseInt(id)
  );
};
const setPinboardModalWidth = () => {
  const offcanvas = document.getElementById('offcanvasRight');
  const offcanvasWidth = offcanvas.offsetWidth;
  const pinboardModalContainers = document.getElementsByClassName('pinboard-modal-container');
  if (pinboardModalContainers.length > 0 && window.innerWidth >= 1080) {
    Array.from(pinboardModalContainers)?.forEach(container => {
      const widthPercent = ((window.innerWidth - offcanvasWidth) / window.innerWidth) * 100;
      // container.style.width = `${widthPercent}%`;
    });
  }else{
    Array.from(pinboardModalContainers)?.forEach(container => {
      container.style.width = '100%';
    });
  }
}
window.addEventListener('resize', setPinboardModalWidth);
window.setPinboardModalWidth = setPinboardModalWidth;

// get pinboard function to get pinboard items
async function getPinboard(pinboardApp, container, payload = {}) {
  try {

    if (pinboardApp && typeof pinboardApp.getPinboard === 'function' && container) {
      const response = await pinboardApp.getPinboard(container, payload);
    }

  } catch (error) {
    console.error('Error loading pinboard:', error);
    if (container) {
      container.innerHTML = '<div class="alert alert-danger">Error loading pinboard items</div>';
    }
    throw error;
  }
}
 // add to pinboard function to add item to pinboard
async function addToPinboard(pinboardApp, itemData) {
  try {
    await pinboardApp.addToPinboard(itemData);
  } catch (error) {
    console.error('Error adding pinboard item:', error);
  }
}

async function getUserAuthentication() {
  try {
      const localAuthKey = 'userAuthDetails';
      // Prefer encoded storage: btoa(key) -> btoa(JSON value)
      const encodedAuthKey = btoa(localAuthKey);
      const encodedAuthValue = localStorage.getItem(encodedAuthKey);
      if (encodedAuthValue && encodedAuthValue !== "undefined") {
          const decoded = JSON.parse(atob(encodedAuthValue));
          return new Auth(decoded);
      }
      return null;
  } catch (error) {
      throw new Error(error.message || 'Failed to get user authentication');
  }
}




document.addEventListener("DOMContentLoaded", function () {
  // Defer searchbar + pinboard (long import chain) off critical path to improve LCP
  const runWhenIdle = window.requestIdleCallback || function (cb) { setTimeout(cb, 0); };
  runWhenIdle(async function () {

    //################## Search Bar Start here ########################
    try {
        const searchbarModule = await import('/js/vue/searchbar.js');
        const searchbarApp = searchbarModule.default;
        const desktopContainer = document.getElementById('searchbar-app');
        const mobileContainer = document.getElementById('searchbar-app-mobile');

        if (desktopContainer) {
            await searchbarApp.loadSearchbar(desktopContainer);
        }
        if (mobileContainer) {
            await searchbarApp.loadSearchbar(mobileContainer, null, { variant: 'mobile' });
        }
        if (!desktopContainer && !mobileContainer) {
            console.warn('Searchbar container not found.');
        }
    } catch (error) {
        console.error('Error loading searchbar:', error);
    }  

    const globalSearchIcon = document.getElementById('th-header-global-search');

    function getActiveSearchInput() {
      const isMobileView = window.matchMedia('(max-width: 991.98px)').matches;
      return document.getElementById(
        isMobileView ? 'search-product-name-mobile' : 'search-product-name'
      );
    }

    if (globalSearchIcon){
      globalSearchIcon.addEventListener('click', async function () {
        try {
          setTimeout(() => {
            getActiveSearchInput()?.focus();
          }, 400);
        } catch (error) {
          console.error('Error in globalSearchIcon:', error);
        }
      });
    }

    const pinboardModule = await import('/js/vue/pinboard.js');
    const pinboardApp = pinboardModule.default;
    let appContainer = document.getElementById('pinboard-app');

    const encodedAuthKey = btoa(authService.localAuthKey);
    const encodedAuthValue = localStorage.getItem(encodedAuthKey);
    if ((!encodedAuthValue || encodedAuthValue === "undefined") && authService.hasAuthCookieSignal()) {
      await authService.authenticateFromAccessTokenCookie();
    }

    const userAuthDetails = await getUserAuthentication();
    const userId = userAuthDetails?.user?.user_id || null;
    const customerId = userAuthDetails?.customer?.customer_id || null;

    await getPinboard(pinboardApp, appContainer, { userId: userId, customerId: customerId, silent: true }); // get pinboard items

    // const pinboardButton = document.getElementById('pinboard-button');
    // pinboardButton?.addEventListener('click', async function (e) {
    //   e.preventDefault();
    //   try {
    //     let thPinboardGuest = document.getElementById('th-pinboard-guest');
    //     let thPinboardUser = document.getElementById('th-pinboard-user');
    //     userId ? thPinboardGuest?.classList.add('d-none') : thPinboardUser?.classList.add('d-none');

    //   } catch (error) {
    //     console.error('Error loading pinboard:', error);
    //   }
    // })
    //################## Pinboard Button Click Event End here ########################


    //################## Add to Pinboard Button Click Event Start here ########################

    const addToPinboardButtons = document.getElementsByClassName('th-add-to-pinboard');
    Array.from(addToPinboardButtons).forEach(button => {
      button.addEventListener('click', async function (e) {
        e.preventDefault();
        try{
            const { spinner, created } = getSpinner(this);
            // set busy state to the item
            setBusy(this, true);
            // toggle icon visibility
            toggleIcon(this, false);
            spinner.style.display = 'inline-block';

            const { id, model, title, description, image, productUrl } = this.dataset;
            if (!id || !model) return;
            const itemData = {
              model_id: parseInt(id),
              model_type: model,
              title,
              description,
              image: image || '/img/pinboard/pinboard img 1.png',
              product_url: productUrl,
            };
            await addToPinboard(pinboardApp, itemData);
            setTimeout(() => {
              spinner?.remove();
              toggleIcon(this, true);
            }, 200)
        }catch(error){
          console.error('Error adding to pinboard:', error);
          toggleIcon(item, true);
        }finally{
          setBusy(this, false);
        }
      });
    });

    //################## Add to Pinboard Button Click Event End here ########################


    //################## Load Virtual Pinboard Start Here ########################
    let virtualPinboardContainer = document.getElementById('vue-virtual-pinboard-container-app');

    try {

      if (pinboardApp && typeof pinboardApp.getVirtulPinboard === 'function' && virtualPinboardContainer) {
        const responseVirtualPinboard = await pinboardApp.getVirtulPinboard(virtualPinboardContainer, { userId: 1, silent: true });

        if (responseVirtualPinboard && responseVirtualPinboard.error) {
          console.error('Error from virtualPinboardApp:', responseVirtualPinboard.error);
        }
      }
    } catch (error) {
      if (virtualPinboardContainer) {
        virtualPinboardContainer.innerHTML = '<div class="alert alert-danger">Error loading virtual pinboard items</div>';
      }
      throw error;
    }
    //################## Load Virtual Pinboard End Here ########################

    // ############## Pinboard count badge script start here ########################


    const logOutButton = document.getElementById('login-button');
    logOutButton?.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('logOutButton clicked');
      localStorage.removeItem('userAuthDetails');
      let pinboard = localStorage.getItem('pinboard');
      if (pinboard){
        pinboard = JSON.parse(pinboard);
        pinboard.pinboard_id = null;
      }
      localStorage.setItem('pinboard', JSON.stringify(pinboard));
      window.location.assign(window.location.origin + '/login');
    });
    //logout-button
    const logoutButton = document.getElementById('logout-button');


    // update when service dispatches an in-window event
    window.addEventListener('pinboard:updated', function(e) {
      try {
        const count = e?.detail?.count;
        if (typeof count === 'number') {
          const badge = document.getElementsByClassName('th-pinboard-count-badge');
          const el = document.getElementById('pinboard-item-count');
          if(!count || count === 0) {
            el.innerHTML = '';
            badge[0].style.display = 'none';
            return;
          }
          badge[0].style.display = 'block';
          if (el) el.innerHTML = count;
          if (badge && badge.length > 0) badge[0].classList.add('annimation');
          setTimeout(() => {
            if (badge && badge.length > 0) badge[0].classList.remove('annimation');
          }, 2000);
        } else {
          updatePinboardCountFromStorage();
        }
      } catch (err) {
        console.error('pinboard:updated handler error', err);
      }
    });

    function updatePinboardCountFromStorage() {
      try {
        const localItems = JSON.parse(localStorage.getItem('pinboard') || '{"pinboard_items":[]}');
        const pinboardItems = localItems?.pinboard_items || [];
        const count = Array.isArray(pinboardItems) ? pinboardItems.length : 0;
        const el = document.getElementById('pinboard-item-count');
        if (el) el.innerHTML = count;
        window.dispatchEvent(new CustomEvent('pinboard:updated', { detail: { count } }));
      } catch (err) {
        console.error('Failed to update pinboard count', err);
      }
    }

    // initial set
    updatePinboardCountFromStorage();



    // update if another tab/window modifies localStorage
    window.addEventListener('storage', function(e) {
      if (e.key === 'pinboardItems') updatePinboardCountFromStorage();
    });

    // ############## Pinboard count badge script end here ########################

  }, { timeout: 2500 });
});


    
document.addEventListener("DOMContentLoaded", async function () {

    const uploadBtn = document.getElementById('upload-btn');

    uploadBtn?.addEventListener('click', () => {
      if (!fileInput.files.length) {
        alert('Please select an image first');
        return;
      }

      const formData = new FormData();
      formData.append('image', fileInput.files[0]);

      fetch('/upload-image', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          console.log('Upload success:', data);
          alert('Image uploaded successfully');
        })
        .catch(err => {
          console.error('Upload failed', err);
        });
    });


});