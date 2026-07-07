(function ($) {
    "use strict";
    /*=================================
      JS Index Here
    ==================================*/
    /*
    00. Logged In check
    01. On Load Function
    02. Mega menu
    03. Mobile Menu Active
    04. Sticky fix (navigation + resource sidebar)
    05. Scroll To Top
    06. Set Background Image Color & Mask
    07. Global Slider
    08. Custom Animaiton For Slider
    09. Ajax Contact Form
    10. Search Box Popup
    11. Popup Sidemenu
    12. Magnific Popup
    13. Section Position
    14. Filter
    15. Counter Up
    16. AS Tab
    17. Shape Mockup
    18. Progress Bar Animation
    19. Price Slider
    20. Tilt Active
    21. Indicator
    22. Circle Progress
    00. Woocommerce Toggle
    00. Right Click Disable
    23. Catalogue Format
    24. Subscription Form
    25. Verify Email Form in Login Page
    26. Password Toggle (Signup Page)
    27. About Page => Who We Are Section - component videogallerywhoweare
    28. About Page => gallery-manufacturingprocess-section - component manufacturingprocess
    29. Contact Sales => Get in Touch Section - component contact-sales-getin-touch
    30. Contact Sales => Hero Section - component contact-sales-hero
    31. soluation page => Solution About Who You Section - component videogallerywhoweare
    32. navigation => navigation.html
  */
    /*=================================
      JS Index End
  ==================================*/
    /*




    /*---------- 03. Mobile Menu Active ----------*/
    document.addEventListener("DOMContentLoaded", function () {
        $.fn.thmobilemenu = function (options) {
            var opt = $.extend(
                {
                    menuToggleBtn: ".th-menu-toggle",
                    bodyToggleClass: "th-body-visible",
                    subMenuClass: "th-submenu",
                    subMenuParent: "th-item-has-children",
                    subMenuParentToggle: "th-active",
                    meanExpandClass: "th-mean-expand",
                    appendElement: '<span class="th-mean-expand"></span>',
                    subMenuToggleClass: "th-open",
                    toggleSpeed: 400,
                    isMobile: true,
                    accordion: false
                },
                options
            );

            return this.each(function () {
                var menu = $(this); // Select menu

                // Menu Show & Hide
                function menuToggle() {
                    menu.toggleClass(opt.bodyToggleClass);

                    // collapse submenu on menu hide or show
                    var subMenu = "." + opt.subMenuClass;
                    $(subMenu).each(function () {
                        if ($(this).hasClass(opt.subMenuToggleClass)) {
                            $(this).removeClass(opt.subMenuToggleClass);
                            $(this).css("display", "none");
                            $(this).parent().removeClass(opt.subMenuParentToggle);
                        }
                    });
                }

                // Class Set Up for every submenu
                menu.find("li").each(function () {
                    var submenu = $(this).find("ul");
                    submenu.addClass(opt.subMenuClass);
                    submenu.css("display", "none");
                    submenu.parent().addClass(opt.subMenuParent);
                    submenu.prev("a").append(opt.appendElement);
                    submenu.next("a").append(opt.appendElement);
                });

                // Toggle Submenu
                function toggleDropDown($element) {
                    var $currentLi = $($element).closest("li");
                    var $targetSubmenu = $($element).next("ul").length > 0 ? $($element).next("ul") : $($element).prev("ul");
                    if ($targetSubmenu.length === 0) return;

                    // Accordion mode: close other open items before toggling
                    if (opt.accordion) {
                        menu.find("li").not($currentLi).each(function () {
                            var $otherLi = $(this);
                            var $otherSubmenu = $otherLi.find("ul." + opt.subMenuClass).first();
                            if ($otherSubmenu.length && $otherSubmenu.hasClass(opt.subMenuToggleClass)) {
                                $otherLi.removeClass(opt.subMenuParentToggle);
                                $otherSubmenu.slideUp(opt.toggleSpeed);
                                $otherSubmenu.removeClass(opt.subMenuToggleClass);
                            }
                        });
                    }

                    if ($($element).next("ul").length > 0) {
                        $($element).parent().toggleClass(opt.subMenuParentToggle);
                        $($element).next("ul").slideToggle(opt.toggleSpeed);
                        $($element).next("ul").toggleClass(opt.subMenuToggleClass);
                    } else if ($($element).prev("ul").length > 0) {
                        $($element).parent().toggleClass(opt.subMenuParentToggle);
                        $($element).prev("ul").slideToggle(opt.toggleSpeed);
                        $($element).prev("ul").toggleClass(opt.subMenuToggleClass);
                    }
                }

                // Submenu toggle Button (or entire row when accordion mode)
                var expandToggler = "." + opt.meanExpandClass;
                if (opt.accordion) {
                    // Clicking anywhere on the category row (heading or icon) toggles accordion
                    menu.find("." + opt.subMenuParent + " > a").on("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleDropDown($(this));
                    });
                } else {
                    menu.find(expandToggler).each(function () {
                        $(this).on("click", function (e) {
                            e.preventDefault();
                            toggleDropDown($(this).parent());
                        });
                    });
                }

                // Menu Show & Hide On Toggle Btn click
                $(opt.menuToggleBtn).each(function () {
                    $(this).on("click", function () {
                        menuToggle();
                    });
                });

                // Hide Menu On out side click
                menu.on("click", function (e) {
                    e.stopPropagation();
                    menuToggle();
                });

                // Stop Hide full menu on menu click
                menu.find("div").on("click", function (e) {
                    e.stopPropagation();
                });
                if (!opt['isMobile']) {
                    menu.find(".menu-item-has-children > a").on("click", function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    });
                    menu.find(".menu-item-has-children > a > .sub-title").on("click", function (e) {
                        toggleDropDown($(this).parent());
                    });
                }
                if (opt['firstItemExpanded']) {
                    let firstMenuItem = menu.find(".th-menu-container li:first a:first");
                    toggleDropDown(firstMenuItem);
                }


            });
        };

        $(".th-menu-wrapper").thmobilemenu();
        $(".th-home-categories").thmobilemenu({ meanExpandClass: "th-mean-expand", firstItemExpanded: true, isMobile: false });
        $(".th-categories-slider-nav").thmobilemenu({ meanExpandClass: "th-mean-expand", firstItemExpanded: true, isMobile: false, accordion: true });
        

        /*---------- 06. Set Background Image Color & Mask ----------*/
        if ($("[data-bg-src]").length > 0) {
            $("[data-bg-src]").each(function () {
                var src = $(this).attr("data-bg-src");
                $(this).css("background-image", "url(" + src + ")");
                $(this).removeAttr("data-bg-src").addClass("background-image");
            });
        }
    });
    /*----------- 07. Global Slider ----------*/
    document.addEventListener("DOMContentLoaded", function () {
        $('.th-slider').each(function () {

            var thSlider = $(this);
            var settings = $(this).data('slider-options') ?? {};

            // Store references to the navigation Slider
            var prevArrow = thSlider.find('.slider-prev');
            var nextArrow = thSlider.find('.slider-next');
            var paginationEl = thSlider.find('.slider-pagination');

            var autoplayconditon = settings['autoplay'] ?? false;

            var sliderDefault = {
                slidesPerView: 1,
                spaceBetween: settings['spaceBetween'] ? settings['spaceBetween'] : 24,
                loop: settings['loop'] == false ? false : true,
                speed: settings['speed'] ? settings['speed'] : 1000,
                autoplay: autoplayconditon ? autoplayconditon : { delay: 6000, disableOnInteraction: false },
                navigation: {
                    nextEl: nextArrow.get(0),
                    prevEl: prevArrow.get(0),
                },
                pagination: {
                    el: paginationEl.get(0),
                    clickable: true,
                    renderBullet: function (index, className) {
                        return '<span class="' + className + '" aria-label="Go to Slide ' + (index + 1) + '"></span>';
                    },
                },
            };

            var options = JSON.parse(thSlider.attr('data-slider-options'));
            options = $.extend({}, sliderDefault, options);
            var swiper = new Swiper(thSlider.get(0), options); // Assign the swiper variable

            if ($('.slider-area').length > 0) {
                $('.slider-area').closest(".container").parent().addClass("arrow-wrap");
            }

        });
    });

    /** End Global Slider */
    /** Home Categories Slider */
    document.addEventListener("DOMContentLoaded", function () {
        // Category item click → slide to swiper
        document
            .querySelectorAll('.home-product-category-slider-item')
            .forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (
                        window.homeCategoriesSwiper &&
                        typeof window.homeCategoriesSwiper.slideTo === 'function'
                    ) {
                        window.homeCategoriesSwiper.slideTo(
                            parseInt(item.dataset.slideIndex, 10)
                        );
                    }
                });
            });

        // Initialize Swiper
        try {
            window.homeCategoriesSwiper = new Swiper(
                ".home-product-category-slider",
                {
                    effect: "fade",
                    fadeEffect: { crossFade: true },
                    speed: 500,
                    autoplay: false,
                    grabCursor: false
                }
            );
        } catch (err) {
            console.error('Swiper init failed:', err);
            return;
        }

        var homeCategorySlider = document.getElementsByClassName('home-product-category-slider');
        if (homeCategorySlider.length > 0) {
            // Autoplay
            // if (
            //     window.homeCategoriesSwiper.autoplay &&
            //     typeof window.homeCategoriesSwiper.autoplay.start === 'function'
            //   ) {
            //     window.homeCategoriesSwiper.autoplay.start();
            //   }

            // Sync active menu item with swiper
            //   function setActiveCategoryNav(index) {

            //     const menuItems = document.querySelectorAll('.th-menu-container li');

            //     // Reset all menu items
            //     menuItems.forEach(li => {
            //       li.classList.remove('th-slide-active', 'th-active');
            //       // close the submenu if it exists and remove the open class
            //       const submenu = li.querySelector('.th-subcategories-manu');
            //       if (submenu) {
            //         submenu.classList.remove('th-open');
            //         submenu.style.display = 'none';
            //       }
            //     });

            //     // Find current category by slide index
            //     const span = document.querySelector(
            //       '.home-product-category-slider-item[data-slide-index="' + index + '"]'
            //     );

            //     if (!span) return;

            //     const li = span.closest('li');
            //     if (!li) return;

            //     // Activate current menu item
            //     li.classList.add('th-slide-active', 'th-active');

            //     // Open submenu if exists and add the open class
            //     const submenu = li.querySelector('.th-subcategories-manu');
            //     if (submenu) {
            //       submenu.classList.add('th-open');
            //       submenu.style.display = 'block';
            //     }
            //   }

            //   // Initial state + swiper change event
            //   setActiveCategoryNav(window.homeCategoriesSwiper.activeIndex || 0);

            //   window.homeCategoriesSwiper.on('slideChange', function () {
            //     setActiveCategoryNav(this.activeIndex);
            //   });
        }
    });
    window.addEventListener('resize', function () {

    })

    /** End Home Categories Slider */

    // Function to add animation classes
    function animationProperties() {
        $('[data-ani]').each(function () {
            var animationName = $(this).data('ani');
            $(this).addClass(animationName);
        });

        $('[data-ani-delay]').each(function () {
            var delayTime = $(this).data('ani-delay');
            $(this).css('animation-delay', delayTime);
        });
    }
    document.addEventListener("DOMContentLoaded", function () {
        animationProperties();
    });

    // Add click event handlers for external slider arrows based on data attributes
    // $('[data-slider-prev], [data-slider-next]').on('click', function () {
    //     var sliderSelector = $(this).data('slider-prev') || $(this).data('slider-next');
    //     var targetSlider = $(sliderSelector);

    //     if (targetSlider.length) {
    //         var swiper = targetSlider[0].swiper;

    //         if (swiper) {
    //             if ($(this).data('slider-prev')) {
    //                 swiper.slidePrev();
    //             } else {
    //                 swiper.slideNext();
    //             }
    //         }
    //     }
    // });

    document.addEventListener("DOMContentLoaded", function () {
        var swiper = new Swiper(".mySwiper", {
            grabCursor: true,
            spaceBetween: 30,
            effect: "creative",
            creativeEffect: {
                prev: {
                    shadow: true,
                    translate: ["-20%", 0, -1],
                },
                next: {
                    translate: ["100%", 0, 0],
                },
            },
            speed: 1500,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        var swiper = new Swiper(".home-project-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });

        var swiper = new Swiper(".home-blog-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });

        var swiper = new Swiper(".task-seating-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 2.3 },
                576: { slidesPerView: 2.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });

        var swiper = new Swiper(".th-featured-products-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });
        var swiper = new Swiper(".th-featured-projects-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            }
        });
        var swiper = new Swiper(".th-instagram-products-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 1.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });

        var swiper = new Swiper(".workstations-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 3.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });

        var swiper = new Swiper(".featured-material-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 2.3 },
                576: { slidesPerView: 2.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 4.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });
        var swiper = new Swiper(".th-featured-material-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 1.3 },
                576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.3 },
                992: { slidesPerView: 2.3 },
                1200: { slidesPerView: 4.3 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });
        new Swiper(".product-related-slider", {
            grabCursor: true,
            slidesPerView: 3.3,
            spaceBetween: 20, // Space between slides
            breakpoints: {
                0: { slidesPerView: 2.3 },
                576: { slidesPerView: 2.3 }, // Fixed: Number instead of a string
                768: { slidesPerView: 2.7 },
                992: { slidesPerView: 2.7 },
                1200: { slidesPerView: 3.7 }
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                hide: false, // Ensures scrollbar remains visible
                draggable: true
            },
        });
    });
    if (typeof Choices !== 'undefined') {
        if ($('#choose-members').length) {
            new Choices('#choose-members', {
                allowHTML: true,
            });
        }
        if ($('#choose-location').length) {
            new Choices('#choose-location', {
                allowHTML: true,
            });
        }
        if ($('#choose-tour-type').length) {
            new Choices('#choose-tour-type', {
                allowHTML: true,
            });
        }
        if ($('#choose-duration').length) {
            new Choices('#choose-duration', {
                allowHTML: true,
            });
        }
        // if($('#choose-timezone').length){
        //     var items = $.getJSON( "/js/lib/timezones.json").then(res => {
        //         new Choices('#choose-timezone', {
        //             allowHTML: true,
        //             choices: res
        //         });
        //     });
        // }

        // Timezone get by geolocation 
        if ($('#choose-timezone').length) {
            $.getJSON("/js/lib/timezones.json").then(res => {
                var defaultTz = (function () {
                    var AEST = 'AUS Eastern Standard Time';
                    try {
                        var iana = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        if (!iana) return AEST;
                        var match = (res || []).find(function (t) {
                            return t.utc && Array.isArray(t.utc) && t.utc.indexOf(iana) !== -1;
                        });
                        return match ? match.value : AEST;
                    } catch (e) { return AEST; }
                })();
                var choices = new Choices('#choose-timezone', {
                    allowHTML: true,
                    choices: res
                });
                try { choices.setChoiceByValue(defaultTz); } catch (e) { }
            });
        }
    }
    window.initFlatpickr = function () {
        $(".th-booking-calendar").each(function () {
            var $calendar = $(this);

            // Find the input element with data-input attribute
            var $input = $calendar.find('input[data-input]');

            // For inline mode, initialize on the input element if it exists
            // Otherwise initialize on the container
            var targetElement = $input.length ? $input[0] : $calendar[0];

            // Check if flatpickr is already initialized
            if ($(targetElement).data('flatpickr')) {
                return;
            }

            try {
                // Use minimal configuration - similar to working example in js/main.js
                var fp = $(targetElement).flatpickr({
                    inline: true
                });

                // Ensure a valid date is set after initialization
                setTimeout(function () {
                    if (fp && fp.selectedDates && (!fp.selectedDates.length || !fp.selectedDates[0] || isNaN(fp.selectedDates[0].getTime()))) {
                        fp.setDate(new Date(), false);
                    }
                }, 100);

            } catch (e) {
                console.error('Error initializing flatpickr:', e);
            }
        });
    }

    // Initialize on page load
    if ($(".th-booking-calendar").length) {
        window.initFlatpickr();
    }
    window.initTimezoneChoices = function () {
        if ($('#choose-timezone').length) {
            var items = $.getJSON("/js/lib/timezones.json").then(res => {
                new Choices('#choose-timezone', {
                    allowHTML: true,
                    choices: res
                });
            });
        }
    }

})(jQuery);


// Hero Seciton - Need to fix

const shCollapseMenu = document.getElementById('collapseMenu');
const shToggleIcon = document.getElementById('toggleIcon');


if (shToggleIcon) {
    shCollapseMenu.addEventListener('show.bs.collapse', () => {
        shToggleIcon.classList.remove('right');
        shToggleIcon.classList.add('down');
    });

    shCollapseMenu.addEventListener('hide.bs.collapse', () => {
        shToggleIcon.classList.remove('down');
        shToggleIcon.classList.add('right');
    });
}


/** Product Configurator Accordion */
document.addEventListener('DOMContentLoaded', function () {
    // Handle accordion functionality
    
});

function createFeaturedProductSlider() {
    new Swiper(".th-featured-products-slider", {
        grabCursor: true,
        slidesPerView: 3.3,
        spaceBetween: 20, // Space between slides
        breakpoints: {
            0: { slidesPerView: 1.3 },
            576: { slidesPerView: 1.3 }, // Fixed: Number instead of a string
            768: { slidesPerView: 2.3 },
            992: { slidesPerView: 2.3 },
            1200: { slidesPerView: 3.3 }
        },
        scrollbar: {
            el: ".swiper-scrollbar",
            hide: false, // Ensures scrollbar remains visible
            draggable: true
        },
    });
}
/** End Product Configurator Accordion  */


/** Start Waypoints */
document.addEventListener("DOMContentLoaded", function () {

    



});
/** End Waypoints */

/*---------- 23. Catalogue Format Form ----------*/
if(document.getElementById('th-request-catalouge')){
    document.addEventListener("DOMContentLoaded", async function () {
        const form = document.querySelector('#th-request-catalogue form');
        const requestCatalogueButton = document.getElementById('requestCatalogueSubmitButton');
      
        function validateCatalogueInput(input) {
          if (!input) return false;
          const val = (input.value || '').trim();
          if (!val) {
            input.classList.add('is-invalid');
            return false;
          }
          if (input.type === 'email') {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!re.test(val)) {
              input.classList.add('is-invalid');
              return false;
            }
          }
          input.classList.remove('is-invalid');
          return true;
        }
        // catalogue-format 
        const catalogueFormat = document.getElementById('catalogue-format');
        if(catalogueFormat){
          catalogueFormat.addEventListener('change', function() {
            console.log("catalogue format changed", this.value);
            if(this.value === 'physical'){
              document.getElementById('phone-number-group').classList.remove('d-none');
              document.getElementById('request-my-copy-button').classList.remove('d-none');
              document.getElementById('requestCatalogueSubmitButton').classList.add('d-none');
              document.getElementById('company-group').classList.remove('d-none');
              document.getElementById('mailing-address-group').classList.remove('d-none');
              document.getElementById('company')?.addAttribute('required');
              document.getElementById('address')?.addAttribute('required');
            }else if(this.value === 'digital'){
              document.getElementById('phone-number-group').classList.add('d-none');
              document.getElementById('request-my-copy-button').classList.add('d-none');
              document.getElementById('requestCatalogueSubmitButton').classList.remove('d-none');
              document.getElementById('company-group').classList.add('d-none');
              document.getElementById('mailing-address-group').classList.add('d-none');
              
              document.getElementById('company')?.removeAttribute('required');
              document.getElementById('address')?.removeAttribute('required');
            }else{
              document.getElementById('company-group').classList.remove('d-none');
              document.getElementById('mailing-address-group').classList.remove('d-none');
            }
      
          });
        }
      
        // request-my-copy-button 
        // const requestMyCopyButton = document.getElementById('request-my-copy-button');
        // if(requestMyCopyButton){
        //   requestMyCopyButton.addEventListener('click', function() {
        //     window.open('https://www.krost.com.au/MultiMediaFiles/krost25/Krost2025Catalogue.html', '_blank');
        //     // document.getElementById('requestCatalogueSubmitButton').classList.remove('d-none');
        //   });
        // }
      
        //form submit code
        // form?.addEventListener('submit', async function (event) {
        //   event.preventDefault();
      
        //   const catalogueFormat = document.getElementById('catalogue-format');
        //   const firstNameInput = document.getElementById('first-name');
        //   const lastNameInput = document.getElementById('last-name');
        //   const emailInput = document.getElementById('email');
        //   const phoneNumberInput = document.getElementById('phone-number');
        //   const companyInput = document.getElementById('company');
        //   const mailingAddressInput = document.getElementById('mailing-address');
      
        //   if (!validateCatalogueInput(firstNameInput) || !validateCatalogueInput(lastNameInput) || !validateCatalogueInput(emailInput)) {
        //     const firstInvalid = [firstNameInput, lastNameInput, emailInput].find(function (el) { return el?.classList?.contains('is-invalid'); });
        //     if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus();
        //     return;
        //   }
      
        //   var payload = {
        //     full_name: (firstNameInput.value.trim() + ' ' + lastNameInput.value.trim()).trim(),
        //     email: emailInput.value.trim(),
        //     request_type: catalogueFormat?.value || '',
        //     company: companyInput?.value?.trim() || '',
        //     mailing_address: mailingAddressInput?.value?.trim() || '',
        //     phone_number: phoneNumberInput?.value?.trim() || '',
        //   };
      
        //   requestCatalogueButton.disabled = true;
        //   requestCatalogueButton.classList.add('th-btn-disabled');
        //   var originalBtnHtml = requestCatalogueButton.innerHTML;
        //   requestCatalogueButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
      
        //   var alertEl = document.getElementById('request-catalogue-alert-message');
        //   if (alertEl) alertEl.style.display = 'none';
      
        //   try {
        //     var res = await fetch('/api/contact-sales-get-in-touch', {
        //       method: 'POST',
        //       headers: { 'Content-Type': 'application/json' },
        //       body: JSON.stringify(payload)
        //     });
        //     var data = await res.json();
        //     if (!res.ok) {
        //       throw new Error(data.message || data.error || 'Request failed');
        //     }
        //     if (!data.success && data.error) {
        //       throw new Error(data.error);
        //     }
        //     if (data.success) {
        //       if (alertEl) {
        //         alertEl.style.display = 'block';
        //         alertEl.innerText = data.message || 'Catalogue request sent successfully';
        //       }
        //       form.reset();
        //     } else {
        //       alert('Failed to request catalogue: ' + (data.message || 'Unknown error'));
        //     }
        //   } catch (err) {
        //     console.error('requestCatalogue failed', err);
        //     alert('Request catalogue failed: ' + (err && err.message ? err.message : 'Unknown error'));
        //   } finally {
        //     requestCatalogueButton.disabled = false;
        //     requestCatalogueButton.classList.remove('th-btn-disabled');
        //     requestCatalogueButton.innerHTML = originalBtnHtml;
        //   }
        // });
      });
}
/** End Catalogue Format */


/*---------- 24. Subscription Form ----------*/
// document.getElementById('subscription-form')?.addEventListener('submit', function (event) {
//     event.preventDefault();
//     const email = document.getElementById('subscription-form').querySelector('input[type="email"]').value;
//     const emailInput = document.getElementById('subscription-form').querySelector('input[type="email"]');
//     emailInput.addEventListener('input', () => emailInput.classList.remove('is-invalid'));

//     if (!email || !email.includes('@')) {
//         emailInput.classList.add('is-invalid');
//         return;
//     }
//     fetch('/api/subscribe-email', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ email }),
//     })
//         .then(response => response.json())
//         .then(data => {
//             console.log(data);
//             if (data.success) {
//                 // toastr.success('Subscription successful');
//                 alert(data.message || 'Subscription successful');
//                 document.getElementById('subscription-form').reset();
//             } else {
//                 // toastr.error('Subscription failed');
//                 alert(data.message || 'Subscription failed');
//                 document.getElementById('subscription-form').reset();
//             }
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             // toastr.error('Subscription failed');
//             alert(data.message || 'Subscription failed');
//             document.getElementById('subscription-form').reset();
//         });
// });
/** End Subscription Form */


/*---------- 25. verify email form in login page ----------*/
if (document.getElementById('login-page')) {
    document.addEventListener('DOMContentLoaded', async function () {

        let email = '';
        const modalEl = document.getElementById('verifyEmailModal');
        const otpInputs = modalEl?.querySelectorAll('.otp-input');

        const getOtp = () => [...otpInputs].map(i => i.value).join('');

        async function sendOtp(email) {
            const res = await fetch('/api/email-verification-request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });

            const data = await res.json();
            console.log('data send otp', data);
            if (!data.success) return alert(data.message || 'Failed to send OTP');

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            modalEl.querySelector('#verify-email-display').textContent = email;
            modalEl.querySelector('#otp-text').textContent = data.customer?.otp || 'otp';

            otpInputs.forEach(i => i.value = '');
            otpInputs[0]?.focus();
        }

        // signup-email-value 
        const signupEmailValue = document.getElementById('signup-email-value');
        if (signupEmailValue) {
            email = signupEmailValue.value.trim();

            if (!email.includes('@')) return signupEmailValue.classList.add('is-invalid');
            signupEmailValue.classList.remove('is-invalid');
            const $sendOtpButton = $("#send-otp-button");
            const originalSendOtpBtnHtml = $sendOtpButton.html();
            const loadingHtml = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';
        
            $bookBtn.prop('disabled', true).html(loadingHtml);
            await sendOtp(email);
            $bookBtn.prop('disabled', false).html(originalSendOtpBtnHtml);
        }

        // SEND OTP
        document.getElementById('send-otp-button')?.addEventListener('click', async e => {
            e.preventDefault();
            $('#email-feedback').text('');
            const input = document.getElementById('email');
            email = input.value.trim();

            if (!email.includes('@')) {
                input.classList.add('is-invalid');
                $('#email-feedback').innerText('Please enter a valid email address');
                return;
            }
            input.classList.remove('is-invalid');
            const $sendOtpButton = $(this);
            const originalSendOtpBtnHtml = $sendOtpButton.html();
            const loadingHtml = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';
        
            $bookBtn.prop('disabled', true).html(loadingHtml);
            await sendOtp(email);
            $bookBtn.prop('disabled', false).html(originalSendOtpBtnHtml);

            // const res = await fetch('/api/email-verification-request', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ email })
            // });

            // const data = await res.json();
            // console.log('data send otp', data);
            // if (!data.success) return alert(data.message || 'Failed to send OTP');

            // bootstrap.Modal.getOrCreateInstance(modalEl).show();
            // modalEl.querySelector('#verify-email-display').textContent = email;
            // modalEl.querySelector('#otp-text').textContent = data.customer?.otp || 'otp';

            // otpInputs.forEach(i => i.value = '');
            // otpInputs[0]?.focus();
        });



        // OTP INPUT BEHAVIOR
        otpInputs?.forEach((input, i) => {
            input.addEventListener('input', e => {
                if (!/^\d$/.test(e.target.value)) return e.target.value = '';
                otpInputs[i + 1]?.focus();
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !input.value) otpInputs[i - 1]?.focus();
            });

            input.addEventListener('paste', e => {
                e.preventDefault();
                const digits = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                digits.split('').forEach((d, k) => otpInputs[i + k] && (otpInputs[i + k].value = d));
                otpInputs[Math.min(i + digits.length - 1, 5)]?.focus();
            });
        });

        // VERIFY EMAIL
        document.getElementById('verify-email-button')?.addEventListener('click', async () => {
            const otp = getOtp();
            if (otp.length !== 6) return alert('Enter the 6-digit code');

            const btn = document.getElementById('verify-email-button');
            const spinner = document.getElementById('verify-email-spinner');
            btn.disabled = true;
            spinner?.classList.remove('d-none');

            try {
                const res = await fetch('/api/verify-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, otp })
                });
                const data = await res.json();

                if (!data.success) return alert(data.message || 'Verification failed');

                localStorage.setItem('userAuthDetails', JSON.stringify(data.user || {}));
                localStorage.setItem('customer', JSON.stringify(data.customer || {}));

                bootstrap.Modal.getInstance(modalEl)?.hide();
                window.location.href = '/';
                // success message
                alert('Verification successful');
            } catch {
                alert('Verification failed');
            }

            btn.disabled = false;
            spinner?.classList.add('d-none');
        });

        // RESEND
        document.getElementById('resend-email-link')?.addEventListener('click', async e => {
            e.preventDefault();
            if (!email) return;

            const res = await fetch('/api/email-verification-request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await res.json();

            if (data.success) {
                modalEl.querySelector('#otp-text').textContent = data.customer?.otp || 'otp';
                alert('Verification code sent again');
            }
        });
    });
}
/** End verify email form in login page */

/*---------- 26. Password Toggle (Signup Page) ----------*/
if (document.getElementById('signup-page')) {
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var toggleBtn = document.getElementById('password-toggle');
        var toggleIcon = document.getElementById('password-toggle-icon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
        }
    });
}
/** End Password Toggle */


/*---------- 27. Abdout Page => Who We Are Section ----------*/
if (document.getElementById('whoWeAre')) {
    document.addEventListener('DOMContentLoaded', function () {
        const whoWeAre = document.getElementById("whoWeAre");
        const whoWeAreGallery = lightGallery(whoWeAre, {
          container: whoWeAre,
          dynamic: !0,
          thumbnail: !0,
          swipeToClose: !1,
          addClass: 'lg-inline',
          mode: 'lg-scale-up',
          slideShowAutoplay: !1,
          autoPlay: !1,
          hash: !1,
          pager: !1,
          closable: !1,
          showMaximizeIcon: !0,
          rotate: !0,
          download: !0,
          thumbnailsPosition: 'bottom',
          plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
          // appendSubHtmlTo: '.lg-outer',
          autoplayFirstVideo: !1,
          controls: !1,
          dynamicEl: whoWeAreData.map(function (item) {
            return {
              ...item,
              subHtml: ''
            };
          })
        });
        whoWeAreGallery.openGallery();
    });
}
/** End About Who We Are Section */

/*---------- 28. Abdout Page => gallery-manufacturingprocess-section ----------*/
if (document.getElementById('gallery-manufacturingprocess')) {
    document.addEventListener('DOMContentLoaded', function () {
        const manufacturingProcess = document.getElementById("manufacturingProcess");
        const manufacturingProcessGallery = lightGallery(manufacturingProcess, {
            container: manufacturingProcess,
            dynamic: !0,
            thumbnail: !0,
            swipeToClose: !1,
            addClass: 'lg-inline',
            mode: 'lg-scale-up',
            slideShowAutoplay: !1,
            autoPlay: !1,
            hash: !1,
            pager: !1,
            closable: !1,
            showMaximizeIcon: !0,
            rotate: !0,
            download: !0,
            thumbnailsPosition: 'left',
            plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
            appendSubHtmlTo: '.lg-outer',
            autoplayFirstVideo: !1,
            dynamicEl: manufacturingProcessData
        });
        manufacturingProcessGallery.openGallery();
    });
}
/** End gallery-manufacturingprocess-section */
if (document.getElementById('account-show-quote')) {
    document.addEventListener("DOMContentLoaded", async function () {
        const module = await import('/js/vue/account.js');
        const accountApp = module.default;

        // ############ order track button function ############
        const quoteTrackButton = document.querySelectorAll('.quote-track-btn');
        quoteTrackButton.forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const quoteId = btn.getAttribute('data-quote-id');
            const acceptQuoteText = btn.querySelector('.accept-quote-text');
            if (!quoteId) {
                // console.error('Quote ID is required');
                return;
            }
            // console.log('Track order for quote:', quoteId);

            // ############ call get tracking orders function ############
            if (!accountApp || typeof accountApp.getQuoteAcceptance !== 'function') {
            throw new Error('accountApp or its getOrderTracking method is not available');
            }

            const response = await accountApp.getQuoteAcceptance({ quoteId: quoteId });
            // console.log('response=', response);
            // 
            if (response && response.error) {
            console.error('Error from accountApp:', response.error);
            }

            if (response && response.success) {
                btn.setAttribute('data-quote-id', '');
                acceptQuoteText.innerText = 'Accepted';
                btn.disabled = true;
                btn.classList.remove('bg-secondary');
                btn.classList.remove('text-white');
                btn.classList.add('bg-gray');
                btn.classList.add('text-black');
            }

        });
        });
        // ############ end quote track button function ############
    });
}

/*---------- 29. Contact Sales Get in Touch Section ----------*/
if (document.getElementById('contact-getin-touch')) {
    document.addEventListener('DOMContentLoaded', function () {
        const uploadZone = document.getElementById('upload-zone');
        const fileInput = document.getElementById('attachments');
        const removeBtn = document.getElementById('remove-image');
        const filenameEl = document.getElementById('upload-filename');
      
        // Click anywhere to open file
        uploadZone.addEventListener('click', (e) => { if (e.target !== removeBtn) fileInput.click(); });
      
        function showSelectedFile(file) {
          filenameEl.textContent = file.name;
          uploadZone.classList.add('has-filename');
          removeBtn.style.display = 'block';
        }
      
        // When image selected
        fileInput.addEventListener('change', function () {
          const file = this.files[0];
          if (!file) return;
      
          if (!file.type.startsWith('image/') && !file.type.startsWith('application/pdf')) {
            alert('Please select a valid image or pdf file.');
            fileInput.value = '';
            return;
          }
      
          // Show file name for both image and PDF
          filenameEl.textContent = file.name;
          uploadZone.classList.add('has-filename');
      
          // For PDF: show filename only (cannot preview PDF as background)
          if (file.type.startsWith('application/pdf')) {
            removeBtn.style.display = 'block';
            // return;
          }
      
      
          const reader = new FileReader();
          reader.onload = function (e) {
            var fileExt = file.name.split('.').pop();
            console.log(fileExt);
            uploadZone.style.backgroundSize = 'contain';
            if(fileExt === 'pdf'){
              uploadZone.style.backgroundImage = `url(/media/design-resource/icons/pdf.png)`;
            }else{
              uploadZone.style.backgroundImage = `url(${e.target.result})`;
            }
            uploadZone.classList.add('has-image');
            removeBtn.style.display = 'block';
          };
          reader.readAsDataURL(file);
        });
      
        // Remove image
        removeBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          fileInput.value = '';
          uploadZone.style.backgroundImage = '';
          uploadZone.classList.remove('has-image', 'has-filename');
          filenameEl.textContent = '';
          removeBtn.style.display = 'none';
        });
    });
}
/** End Contact Sales Get in Touch Section */   

/*---------- 30. Contact Sales Hero Section ----------*/
if (document.getElementById('hero-contactsales')) {

    const btn = document.getElementById('book-a-visit');
    const target = document.querySelector('#book-now');

    if (btn && target) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const headerOffset = 100;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        });
    }
}
/** End Contact Sales Hero Section */

/*---------- 31. soluation page => Solution About Who You Section ----------*/
if (document.getElementById('whoWeAre')) {
    document.addEventListener('DOMContentLoaded', function () {
        const whoWeAre = document.getElementById("whoWeAre");
            const whoWeAreGallery = lightGallery(whoWeAre, {
            container: whoWeAre,
            dynamic: !0,
            thumbnail: !0,
            swipeToClose: !1,
            addClass: 'lg-inline',
            mode: 'lg-scale-up',
            slideShowAutoplay: !1,
            autoPlay: !1,
            hash: !1,
            pager: !1,
            closable: !1,
            showMaximizeIcon: !0,
            rotate: !0,
            download: !0,
            thumbnailsPosition: 'left',
            plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
            appendSubHtmlTo: '.lg-outer',
            autoplayFirstVideo: !1,
            dynamicEl: whoWeAreData});
            whoWeAreGallery.openGallery();
    });
}
/** End Solution About Who You Section */

/*---------- 32. navigation => navigation.html ----------*/
if(document.getElementById('user-profile-info')) {

document.addEventListener('DOMContentLoaded', function () {

    function updateUserMenu() {
      const accountButton = document.getElementById('account-button');
      const loginButton = document.getElementById('login-button');
      const signupButton = document.getElementById('signup-button');
      const logoutButton = document.getElementById('logout-button');
  
      const userAuthDetails = localStorage.getItem('userAuthDetails');
  
      if (userAuthDetails) {
        if(accountButton) accountButton.style.display = 'block';
        if(loginButton) loginButton.style.display = 'none';
        if(signupButton) signupButton.style.display = 'none';
        if(logoutButton) logoutButton.style.display = 'block';
      } else {
        if(accountButton) accountButton.style.display = 'none';
        if(loginButton) loginButton.style.display = 'block';
        if(signupButton) signupButton.style.display = 'block';
        if(logoutButton) logoutButton.style.display = 'none';
      }
    }
  
    // Run when page loads
    updateUserMenu();
  
    // Optional: listen to event if used somewhere else
    window.addEventListener('user:isLoggedIn', updateUserMenu);
  
    const logoutButton = document.getElementById('logout-button');
  
    logoutButton?.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
  
      localStorage.removeItem('userAuthDetails');
      localStorage.removeItem('pinboard');
      localStorage.removeItem('customer');
  
      window.location.reload();
    });
  
  });
}