/* Mobile Product Categories pill strip
   Only runs on screens ≤768px. Reads category names from the existing
   desktop nav triggers, injects a scrollable pill row above the swiper,
   and wires each pill to directly control slide visibility. */
(function () {
  function isMobile() { return window.matchMedia('(max-width: 768px)').matches; }

  function buildPillStrip() {
    if (!isMobile()) return;
    if (document.getElementById('th-cat-pill-strip')) return;

    var triggers = document.querySelectorAll('.home-product-category-slider-item[data-slide-index]');
    if (!triggers.length) return;

    var swiperEl = document.querySelector('.home-product-category-slider');
    if (!swiperEl) return;

    var strip = document.createElement('div');
    strip.id = 'th-cat-pill-strip';
    strip.className = 'th-cat-pill-strip';

    triggers.forEach(function (trigger, i) {
      var nameEl = trigger.querySelector('[data-v-categoriesslidernav-collapse-items-title]');
      var label = nameEl ? nameEl.textContent.trim() : trigger.textContent.trim();

      var pill = document.createElement('button');
      pill.type = 'button';
      pill.className = 'th-cat-pill' + (i === 0 ? ' th-cat-pill--active' : '');
      pill.textContent = label;
      pill.dataset.slideIndex = trigger.dataset.slideIndex;

      pill.addEventListener('click', function () {
        var idx = parseInt(pill.dataset.slideIndex, 10);
        // Swiper initialised at desktop width (987px), so slides have stale
        // transforms (e.g. translate3d(-987px,0,0)) that push them off-screen.
        // Directly override opacity AND reset transform so the target slide
        // is both visible and positioned at the origin.
        var allSlides = document.querySelectorAll('.home-product-category-slider .swiper-slide');
        // allSlides.forEach(function (s, i) {
        //   if (i === idx) {
        //     s.style.opacity = '1';
        //     s.style.transform = 'translate3d(0px,0px,0px)';
        //     s.style.pointerEvents = '';
        //     s.style.zIndex = '1';
        allSlides.forEach(function (s, slideIdx) {
          if (slideIdx === idx) {
            s.style.setProperty('opacity', '1', 'important');
          } else {
            // s.style.opacity = '0';
            // s.style.pointerEvents = 'none';
            // s.style.zIndex = '0';
            s.style.setProperty('opacity', '0', 'important');
          }
        });
        
        strip.querySelectorAll('.th-cat-pill').forEach(function (p) {
          p.classList.remove('th-cat-pill--active');
        });
        pill.classList.add('th-cat-pill--active');
        pill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      });

      strip.appendChild(pill);
    });

    swiperEl.parentNode.insertBefore(strip, swiperEl);

    // if (window.homeCategoriesSwiper) {
    //   window.homeCategoriesSwiper.on('slideChange', function () {
    //     var idx = window.homeCategoriesSwiper.activeIndex;
    //     strip.querySelectorAll('.th-cat-pill').forEach(function (p, i) {
    //       p.classList.toggle('th-cat-pill--active', i === idx);
    //     });
    //     var activePill = strip.querySelector('.th-cat-pill--active');
    //     if (activePill) activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    //   });
    // }
  }

  if (document.readyState === 'complete') {
    buildPillStrip();
  } else {
    window.addEventListener('load', buildPillStrip);
  }

  window.matchMedia('(max-width: 768px)').addEventListener('change', function (e) {
    if (e.matches) buildPillStrip();
  });
})();