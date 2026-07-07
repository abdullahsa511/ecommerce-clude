(function ($) {
    "use strict";
    var SWIPER_PERF_OPTIONS = {
        preloadImages: false,
        lazy: true,
        observer: true,
        observeParents: true
    };
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
  */
    /*=================================
      JS Index End
  ==================================*/
    /*


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
    $(document).ready(function () {
        handleMegaMenu();
    })

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
                requestStickyCheck();
            });

            // Initial check
            requestStickyCheck();
        }
    }

    // sticky resource tab navigation
    function handleStickyResourceTabNavigation() {
        var $topbar = $('.th-topbar');
        var $stickyWrapper = $('.sticky-wrapper');
        var $tabNav = $('.th-head-tab-navigation-container');
        var $tabNavWrapper = $tabNav.parent();
        var $footer = $('.th-footer');

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
    $(document).ready(function () {
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
     */
    function createStickySidebar(opts) {
        var columnSel = typeof opts.column === 'string' ? opts.column : null;
        var $column = columnSel ? $(opts.column) : opts.column;
        var sidebarSel = typeof opts.sidebar === 'string' ? opts.sidebar : null;
        var $stickyWrapper = (opts.stickyWrapper ? (typeof opts.stickyWrapper === 'string' ? $(opts.stickyWrapper) : opts.stickyWrapper) : $('.sticky-wrapper'));
        var $topbar = opts.topbar ? (typeof opts.topbar === 'string' ? $(opts.topbar) : opts.topbar) : $('.th-topbar');
        var $footer = opts.footer ? (typeof opts.footer === 'string' ? $(opts.footer) : opts.footer) : $('.th-footer');
        var footerTouchBottom = opts.footerTouchBottom != null ? opts.footerTouchBottom : 0;
        var getColumnMinHeight = opts.getColumnMinHeight;
        var guard = opts.guard ? (typeof opts.guard === 'string' ? $(opts.guard) : opts.guard) : null;
        var retry = opts.retry || null;
        var columnFromSidebar = opts.columnFromSidebar;

        if (guard && !guard.length) return;

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
            return topbarHeight + navHeight;
        }

        function checkSticky() {
            var $sb = $sidebar();
            var $colEl = $col();
            if (!$sb.length || !$colEl.length) return;

            var scrollTop = $(window).scrollTop();
            var headerHeight = getHeaderHeight();
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

            var stickyThreshold = sidebarOffsetTop - headerHeight;
            if (scrollTop < stickyThreshold) {
                makeUnsticky($sb, $colEl);
                return;
            }

            if (footerTouchBottom && $footer.length) {
                var sidebarHeight = $sb.outerHeight();
                var footerTouchLimit = $footer.offset().top - headerHeight - sidebarHeight;
                if (scrollTop >= footerTouchLimit) {
                    makeStickyAtFooter($sb, $colEl, footerTouchBottom, getColumnMinHeight);
                    return;
                }
            }

            makeSticky($sb, $colEl, headerHeight, getColumnMinHeight);
        }

        function makeSticky($sb, $colEl, headerHeight, getMinH) {
            if ($sb.hasClass('sticky') && $sb.data('sticky-mode') === 'fixed') return;
            $sb.addClass('sticky').data('sticky-mode', 'fixed');
            var rect = $sb[0].getBoundingClientRect();
            $sb.css({
                'position': 'fixed',
                'top': headerHeight + 'px',
                'bottom': '',
                'left': rect.left + 'px',
                'width': rect.width + 'px'
            });
            if (getMinH) {
                $colEl.css('min-height', getMinH($colEl, $sb) + 'px');
            }
        }

        function makeStickyAtFooter($sb, $colEl, bottomPx, getMinH) {
            if ($sb.hasClass('sticky') && $sb.data('sticky-mode') === 'footer') {
                $sb.css('width', $colEl.outerWidth() + 'px');
                return;
            }
            $sb.addClass('sticky').data('sticky-mode', 'footer');
            $colEl.css('position', 'relative');
            $sb.css({
                'position': 'absolute',
                'top': '',
                'bottom': bottomPx + 'px',
                'left': '0',
                'width': $colEl.outerWidth() + 'px'
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
                $sb.css('width', $colEl.outerWidth() + 'px');
            } else {
                var headerHeight = getHeaderHeight();
                var rect = $colEl[0].getBoundingClientRect();
                $sb.css({
                    'top': headerHeight + 'px',
                    'left': rect.left + 'px',
                    'width': rect.width + 'px'
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

    function handleStickyAccountNavigation() {
        var $column = $('#account-navigation');
        var $documentList = $('#th-documents-list');
        if (!$column.length) return;
        $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');
        createStickySidebar({
            column: $column,
            sidebar: '#account-sidebar-list',
            footerTouchBottom: 100,
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
        if (!$column.length) return;

        $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');

        createStickySidebar({
            column: $column,
            sidebar: $sidebar,
            footerTouchBottom: 100,
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
        $column.css('min-height', 'inherit');
        if ($column.length && $documentList.length && $sidebar.length) {
            $column.css('min-height', ($documentList.length ? $documentList.outerHeight() : 0) + 'px');
        }
        createStickySidebar({
            column: $column,
            sidebar: $sidebar,
            footerTouchBottom: 100,
            getColumnMinHeight: function ($col, $sb) {
                return $documentList.length ? $documentList.outerHeight() : $col.outerHeight();
            }
        });
    }



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
        options = $.extend({}, SWIPER_PERF_OPTIONS, options);
        var swiper = new Swiper(thSlider.get(0), options); // Assign the swiper variable

        if ($('.slider-area').length > 0) {
            $('.slider-area').closest(".container").parent().addClass("arrow-wrap");
        }

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
                        console.log(item);
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
    const accordionButtons = document.querySelectorAll('.th-accordion-button');

    accordionButtons.forEach(button => {
        button.addEventListener('click', function () {
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

function createFeaturedProductSlider() {
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


/** Start Waypoints */
document.addEventListener("DOMContentLoaded", function () {

    const heroSection = document.querySelector(".th-way-points");
    const wayPoints = heroSection?.querySelectorAll(".way-point");
    // console.log(wayPoints);

    if (!heroSection || !wayPoints.length) return;

    function getImageDimensions() {
        const rect = heroSection.getBoundingClientRect();
        return {
            width: rect.width,
            height: rect.height
        };
    }

    function updateWayPointPosition(wayPoint) {
        const { width, height } = getImageDimensions();
        if (!width || !height) return;

        // read percentage from inline style
        const leftPercent = parseFloat(wayPoint.style.left) || 0;
        const topPercent = parseFloat(wayPoint.style.top) || 0;

        // calculate pixel positions
        const leftPx = (leftPercent / 100) * width;
        const topPx = (topPercent / 100) * height;

        // clamp inside container
        const clampedX = Math.max(0, Math.min(leftPx, width));
        const clampedY = Math.max(0, Math.min(topPx, height));

        // reapply %
        const newLeftPercent = ((clampedX / width) * 100).toFixed(7);
        const newTopPercent = ((clampedY / height) * 100).toFixed(7);

        wayPoint.style.left = newLeftPercent + "%";
        wayPoint.style.top = newTopPercent + "%";
    }

    function updateAllWayPoints() {
        // alert('on window resize');
        // console.log('way point will be updated on window resize/ main js 767');
        wayPoints.forEach(point => {
            // console.log(point);
            updateWayPointPosition(point);
        });
    }

    window.addEventListener("resize", updateAllWayPoints);

    // on page load
    updateAllWayPoints();

    // click any where any plane
    wayPoints.forEach(point => {
        point.addEventListener("click", (event) => {
            const wayPointLink = point.querySelector(".way-point-link");
            if (wayPointLink) {
                wayPointLink.classList.toggle("active");
            }
        });
    });



});
/** End Waypoints */

/*---------- 23. Catalogue Format Form ----------*/
if(document.getElementById('th-request-catalouge')){
    document.addEventListener("DOMContentLoaded", async function () {
        const form = document.querySelector('#th-request-catalouge form');
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
            }else if(this.value === 'digital'){
              document.getElementById('phone-number-group').classList.add('d-none');
              document.getElementById('request-my-copy-button').classList.add('d-none');
              document.getElementById('requestCatalogueSubmitButton').classList.remove('d-none');
              document.getElementById('company-group').classList.add('d-none');
              document.getElementById('mailing-address-group').classList.add('d-none');
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
    document.addEventListener('DOMContentLoaded', function () {

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
            sendOtp(email);
        }

        // SEND OTP
        document.getElementById('send-otp-button')?.addEventListener('click', async e => {
            e.preventDefault();
            alert("adadasdf")
            const input = document.getElementById('email');
            email = input.value.trim();

            if (!email.includes('@')) return input.classList.add('is-invalid');
            input.classList.remove('is-invalid');
            sendOtp(email);

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


/** End Subscription Form */



/** *************************** Sections Scripts Start here *************************** */
/** *********************************************************************************** */
document.addEventListener("DOMContentLoaded", function() {

    //################# account-design-resources-section.html Start Here #################
    // var res = JSON.parse('[{"label":"Select Resource Type","value":""},{"label":"America","value":"America"},{"label":"Europe","value":"Europe"},{"label":"Asia","value":"Asia"},{"label":"Africa","value":"Africa"},{"label":"Australia","value":"Australia"}]');
    // var inputId = 'choose-resorce-type';
    // if(document.getElementById(inputId)){
    //     new Choices('#'+inputId, {
    //         allowHTML: true,
    //         choices: res
    //     });
    // }

    // var res = JSON.parse('[{"label":"Product Category","value":""},{"label":"America","value":"America"},{"label":"Europe","value":"Europe"},{"label":"Asia","value":"Asia"},{"label":"Africa","value":"Africa"},{"label":"Australia","value":"Australia"}]');
    // var productCategoryInputId = 'choose-product-category';
    // if(document.getElementById(productCategoryInputId)){
    //     new Choices('#'+productCategoryInputId, {
    //         allowHTML: true,
    //         choices: res
    //     });
    // }

    // var res = JSON.parse('[{"label":"Product Name","value":""},{"label":"America","value":"America"},{"label":"Europe","value":"Europe"},{"label":"Asia","value":"Asia"},{"label":"Africa","value":"Africa"},{"label":"Australia","value":"Australia"}]');
    // var productNameInputId = 'choose-product-name';
    // if(document.getElementById(productNameInputId)){
    //     new Choices('#'+productNameInputId, {
    //         allowHTML: true,
    //         choices: res
    //     });
    // }

    //################# account-design-resources-section.html End Here #################


    //################# images-resource-gallery.html Start Here #################
    const masonryImages = document.getElementById('th-resources-images');
    if(masonryImages){
        lightGallery(masonryImages, {
            thumbnail: !1,
            pager: !1,
            plugins: [lgZoom, lgAutoplay, lgFullscreen, lgRotate, lgShare, lgThumbnail, lgVideo],
            hash: !1,
            preload: 0
    
        });
    }
    //################# images-resource-gallery.html End Here #################
    


    //################# product-detail-tabs.html Start Here #################
    const productMasonryImages = document.getElementById('th-resources');
    if(productMasonryImages){
        lightGallery(productMasonryImages, {
            thumbnail: !1,
            pager: !1,
            plugins: [lgZoom, lgAutoplay, lgFullscreen, lgRotate, lgShare, lgThumbnail, lgVideo],
            hash: !1,
            preload: 0
    
        });
    }

    document.querySelectorAll('.th-btn-download-white').forEach(btn => {
        btn.addEventListener('click', async () => {
          const dataSrc = btn.getAttribute('data-src');
          // get filename from URL
          const fileName = dataSrc.split('/').pop().split('?')[0];
  
          try {
            const response = await fetch(dataSrc);
            const blob = await response.blob();
  
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;
  
            document.body.appendChild(link);
            link.click();
  
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
          } catch (error) {
            console.error('Download failed:', error);
          }
        });
      });
    //################# product-detail-tabs.html End Here #################
});



//################# pinboard-details-section.html Start Here #################
function closeStaticModal() {
    // Wait a tiny bit so the click action works first (tel:, mailto:, link, etc.)
    setTimeout(() => {
      const modalEl = document.getElementById('staticBackdrop');
      const myModal = bootstrap.Modal.getInstance(modalEl);
      if(myModal) myModal.hide();
    }, 100);
  }
  
//################# pinboard-details-section.html End Here #################


//################# contact-members-section.html Start Here #################
if(document.querySelector('.th-contact-members')){
    const memberSwiperSlider = new Swiper('.th-members-slider', {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: true,
        navigation: {
          nextEl: '.th-members-slider-next',
          prevEl: '.th-members-slider-prev',
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          992: { slidesPerView: 3 }
        }
      });
}