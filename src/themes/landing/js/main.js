document.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => {
        alert('test');
        const swiper = new Swiper(SWIPER_PERF_OPTIONS);
    });
});

(function ($) {
    "use strict";
    alert('test');
    var SWIPER_PERF_OPTIONS = {
        preloadImages: false,
        lazy: true,
        loop: false,           // disable if not needed
        autoHeight: false,     // remove dynamic height
        observer: false,       // avoid extra DOM observation
        observeParents: false,
        watchSlidesProgress: false,
        slidesPerView: 1,
        virtual: true,
    };
    /*=================================
      JS Index Here
    ==================================*/
    /*
    01. On Load Function
    02. Preloader
    03. Mobile Menu Active
    04. Sticky fix
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
  */
    /*=================================
      JS Index End
  ==================================*/
    /*
/*---------- 03. Mobile Menu Active ----------*/
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
                isMobile: true
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

            // Submenu toggle Button
            var expandToggler = "." + opt.meanExpandClass;
            menu.find(expandToggler).each(function () {
                $(this).on("click", function (e) {
                    e.preventDefault();
                    toggleDropDown($(this).parent());
                });
            });

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
            if(!opt['isMobile']){
                menu.find(".menu-item-has-children > a").on("click", function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                });
                menu.find(".menu-item-has-children > a > .sub-title").on("click", function (e) {
                    toggleDropDown($(this).parent());
                });
            }
            if(opt['firstItemExpanded']){
               let firstMenuItem = menu.find(".th-menu-container li:first a:first");
               toggleDropDown(firstMenuItem);
            }


        });
    };

    $(".th-menu-wrapper").thmobilemenu();
    $(".th-home-categories").thmobilemenu({meanExpandClass: "th-mean-expand", firstItemExpanded: true, isMobile: false});
    $(".th-categories-slider-nav").thmobilemenu({meanExpandClass: "th-mean-expand", firstItemExpanded: true, isMobile: false});

    /*---------- 04. Sticky fix ----------*/
    function handleStickyNavigation() {
        var $topbar = $('.th-topbar');
        var $stickyWrapper = $('.sticky-wrapper');
        
        if ($topbar.length && $stickyWrapper.length) {
            var topbarHeight = $topbar.outerHeight() || 50; // Default to 50px if height not found
            var stickyTicking = false;
            
            function checkSticky() {
                var scrollTop = $(window).scrollTop();
                
                // When scroll position reaches or exceeds topbar height, make sticky-wrapper sticky
                // This happens when the topbar has scrolled up and touches the sticky-wrapper at the top
                if (scrollTop >= topbarHeight) {
                    if (!$stickyWrapper.hasClass('sticky')) {
                        $stickyWrapper.addClass('sticky');
                    }
                } else {
                    if ($stickyWrapper.hasClass('sticky')) {
                        $stickyWrapper.removeClass('sticky');
                    }
                }
            }

            function requestStickyCheck() {
                if (stickyTicking) {
                    return;
                }
                stickyTicking = true;
                window.requestAnimationFrame(function () {
                    checkSticky();
                    stickyTicking = false;
                });
            }
            
            // Check on scroll
            $(window).on('scroll', function() {
                requestStickyCheck();
            });
            
            // Check on load and resize
            $(window).on('load resize', function() {
                topbarHeight = $topbar.outerHeight() || 50;
                requestStickyCheck();
            });
            
            // Initial check
            requestStickyCheck();
        }
    }
    
    // Initialize sticky navigation on document ready
    $(document).ready(function() {
        handleStickyNavigation();
    });

    

    /*---------- 06. Set Background Image Color & Mask ----------*/
    if ($("[data-bg-src]").length > 0) {
        $("[data-bg-src]").each(function () {
            var src = $(this).attr("data-bg-src");
            $(this).css("background-image", "url(" + src + ")");
            $(this).removeAttr("data-bg-src").addClass("background-image");
        });
    }

    /*----------- 07. Global Slider ----------*/

    $('.th-slider').each(function () {

        var thSlider = $(this);
        var settings = $(this).data('slider-options')??{};

        // Store references to the navigation Slider
        var prevArrow = thSlider.find('.slider-prev');
        var nextArrow = thSlider.find('.slider-next');
        var paginationEl = thSlider.find('.slider-pagination');

        var autoplayconditon = settings['autoplay']??false;

        var sliderDefault = {
            slidesPerView: 1,
            spaceBetween: settings['spaceBetween'] ? settings['spaceBetween'] : 24,
            loop: settings['loop'] == false ? false : true,
            speed: settings['speed'] ? settings['speed'] : 1000,
            autoplay: autoplayconditon ? autoplayconditon : {delay: 6000, disableOnInteraction: false},
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
        options = $.extend({}, SWIPER_PERF_OPTIONS, options);
        var swiper = new Swiper(thSlider.get(0), options); // Assign the swiper variable

        if ($('.slider-area').length > 0) {
            $('.slider-area').closest(".container").parent().addClass("arrow-wrap");
        }

    });

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
    animationProperties();

    // Add click event handlers for external slider arrows based on data attributes
    $('[data-slider-prev], [data-slider-next]').on('click', function () {
        var sliderSelector = $(this).data('slider-prev') || $(this).data('slider-next');
        var targetSlider = $(sliderSelector);

        if (targetSlider.length) {
            var swiper = targetSlider[0].swiper;

            if (swiper) {
                if ($(this).data('slider-prev')) {
                    swiper.slidePrev();
                } else {
                    swiper.slideNext();
                }
            }
        }
    });

    var swiper = new Swiper(".mySwiper", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".home-project-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".home-blog-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".task-seating-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".th-featured-products-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));
    var swiper = new Swiper(".th-featured-projects-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));
    var swiper = new Swiper(".th-instagram-products-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".workstations-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));

    var swiper = new Swiper(".featured-material-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));
    var swiper = new Swiper(".th-featured-material-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));
    new Swiper(".product-related-slider", $.extend({}, SWIPER_PERF_OPTIONS, {
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
    }));
    if(typeof Choices !== 'undefined'){
        if($('#choose-members').length){
            new Choices('#choose-members', {
                allowHTML: true,
            });
        }
        if($('#choose-location').length){
            new Choices('#choose-location', {
                allowHTML: true,
            });
        }
        if($('#choose-tour-type').length){
            new Choices('#choose-tour-type', {
                allowHTML: true,
            });
        }
        if($('#choose-timezone').length){
            var items = $.getJSON( "/js/lib/timezones.json").then(res => {
                new Choices('#choose-timezone', {
                    allowHTML: true,
                    choices: res
                });
            });
        }
    }

    if($(".th-booking-calendar").length){
        $(".th-booking-calendar").flatpickr({
            inline: true
        });
    }

})(jQuery);


// Hero Seciton - Need to fix

const shCollapseMenu = document.getElementById('collapseMenu');
const shToggleIcon = document.getElementById('toggleIcon');


if(shToggleIcon){
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
    const accordionButtons = document.querySelectorAll('.th-accordion-button');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            console.log(isExpanded);

            const iconSpan = this.querySelector('.th-config-icon');
            // Update all other accordion icons
            document.querySelectorAll('.th-accordion-button').forEach(otherButton => {
                if (otherButton !== this) {
                    const otherIconSpan = otherButton.querySelector('.th-config-icon');
                    const otherCollapse = document.querySelector(otherButton.getAttribute('data-bs-target'));
                    if (otherCollapse && !otherCollapse.classList.contains('show')) {
                        otherIconSpan.textContent = '+';
                    }
                }
            });
            
            // Toggle icon based on accordion state
            if (isExpanded) {
                iconSpan.textContent = '−'; // Change to plus when collapsing
            } else {
                iconSpan.textContent = '+'; // Change to minus when expanding
            }
        });
    });
});

function createFeaturedProductSlider(){
    new Swiper(".th-featured-products-slider", {
        preloadImages: false,
        lazy: true,
        observer: true,
        observeParents: true,
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



// ################  scripts from sections ################  

document.ready(function(){
    // images-resource-gallery.html 
    const masonryImages = document.getElementById('th-resources-images');
    if(masonryImages){
        const resourceImagesGallery = lightGallery(masonryImages, {
            thumbnail: !1,
            pager: !1,
            plugins: [lgZoom, lgAutoplay, lgFullscreen, lgRotate, lgShare, lgThumbnail, lgVideo],
            hash: !1,
            preload: 0
        });
    }
});