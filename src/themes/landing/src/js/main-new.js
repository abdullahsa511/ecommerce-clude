document.addEventListener("DOMContentLoaded", async function () {
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
    33. product detail tabs => product-detail-tabs.html
    34. Footer Subscription Form Success Feedback
    35. Contact Sales => Book Now Section - change showroom return related members
    36. Account user profile update
    37. Product Instagram => product-instagram.html
  */
    /*=================================
      JS Index End
    ==================================*//*
    */
   /*---------- 0 Global Functions Start here ----------*/
     function setUserAuthentication(auth) {
        try {
            if (!auth || typeof auth !== 'object') {
                throw new Error('Invalid login response payload');
            }
            const activePinboard = JSON.parse(localStorage.getItem('pinboard') || 'null');
            if(auth.pinboard && activePinboard?.pinboard_id && activePinboard.pinboard_id !== auth.pinboard.pinboard_id){
                localStorage.setItem('pinboard', JSON.stringify(auth.pinboard));
            }
            localStorage.setItem('customer', JSON.stringify(auth.customer || {}));
            // Encode key + value via base64, then persist encoded auth payload.
            const encodedAuthKey = btoa('userAuthDetails');
            const authString = JSON.stringify(auth);
            const encodedAuthValue = btoa(authString);
            localStorage.setItem(encodedAuthKey, encodedAuthValue);

            // Decode once with atob for sanity and keep backward compatibility
            // with existing methods (isLoggedIn/getCustomer) that read plain keys.
            const decodedPayload = JSON.parse(atob(encodedAuthValue));

            return decodedPayload;
        } catch (error) {
            throw new Error(error.message || 'Failed to set logging in user');
        }
    }
   function getUserAuthentication() {
        try {
            // Prefer encoded storage: btoa(key) -> btoa(JSON value)
            const localAuthKey = 'userAuthDetails';
            const encodedAuthKey = btoa(localAuthKey);
            const encodedAuthValue = localStorage.getItem(encodedAuthKey);
            if (encodedAuthValue && encodedAuthValue !== "undefined") {
                return JSON.parse(atob(encodedAuthValue));
            }
            return null;
        } catch (error) {
            throw new Error(error.message || 'Failed to get user authentication');
        }
    }


    function logoutUser() {
        const localAuthKey = 'userAuthDetails';
        const encodedAuthKey = btoa(localAuthKey);
        localStorage.removeItem(encodedAuthKey);
        localStorage.removeItem('pinboard');
        localStorage.removeItem('customer');
        localStorage.removeItem('pinboard_processed');

        // Clear client-accessible auth cookies before server logout.
        const expireCookie = (name) => {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax`;
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=None; Secure`;
        };

        ['access_token', 'admin_access_token', 'admin_token_type', 'admin_refresh_token', 'auth_present'].forEach(expireCookie);
        window.location.href = '/logout';
    }
   /*---------- 0 Global Functions End here ----------*/


    /*---------- 03. Mobile Menu Active Start ----------*/
    $.fn.thmobilemenu = function (options) {
        var opt = $.extend(
            {
                menuToggleBtn: ".th-menu-toggle",
                bodyToggleClass: "th-body-visible",
                subMenuClass: "th-submenu",
                subMenuParent: "th-item-has-children",
                subMenuParentToggle: "th-active",
                meanExpandClass: "th-mean-expand",
                appendElement: '',
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
            // if (opt.accordion) {
            //     // Clicking anywhere on the category row (heading or icon) toggles accordion
            //     menu.find("." + opt.subMenuParent + " > a").on("click", function (e) {
            //         e.preventDefault();
            //         e.stopPropagation();
            //         toggleDropDown($(this));
            //     });
            // } else {
            //     menu.find(expandToggler).each(function () {
            //         $(this).on("click", function (e) {
            //             e.preventDefault();
            //             toggleDropDown($(this).parent());
            //         });
            //     });
            // }

            menu.find("." + opt.subMenuParent + " > a").on("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropDown($(this));
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
    $(".th-home-categories").thmobilemenu({ meanExpandClass: "th-mean-expand", firstItemExpanded: false, isMobile: false });
    $(".th-categories-slider-nav").thmobilemenu({ meanExpandClass: "th-mean-expand", firstItemExpanded: false, isMobile: false, accordion: true });
    
    /*---------- 03. Mobile Menu Active End ----------*/


    /*---------- 06. Set Background Image Color & Mask ----------*/
    if ($("[data-bg-src]").length > 0) {
        $("[data-bg-src]").each(function () {
            var src = $(this).attr("data-bg-src");
            $(this).css("background-image", "url(" + src + ")");
            $(this).removeAttr("data-bg-src").addClass("background-image");
        });
    }

    /*---------- 07. Global Slider Start ----------*/
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

    /*---------- 07. Global Slider End ----------*/

    /*---------- 08. Home page categories slider Start ----------*/
    document.querySelectorAll('.home-product-category-slider-item')
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
    /*---------- 08. Home page categories slider End ----------*/

    /*---------- 08.1 Other Custom slider Start ----------*/
    if($(".home-project-slider").length > 0) {
        var homeProjectSlider = new Swiper(".home-project-slider", {
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
    if($(".home-blog-slider").length > 0) {
        var homeBlogSlider = new Swiper(".home-blog-slider", {
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
    if($(".task-seating-slider").length > 0) {
        var taskSeatingSlider = new Swiper(".task-seating-slider", {
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
    }
    if($(".th-featured-products-slider").length > 0) {
        var thFeaturedProductsSlider = new Swiper(".th-featured-products-slider", {
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
    if($(".th-related-articles-slider").length > 0) {
        var thRelatedArticlesSlider = new Swiper(".th-related-articles-slider", {
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
    if($(".th-featured-projects-slider").length > 0) {
        var thFeaturedProjectsSlider = new Swiper(".th-featured-projects-slider", {
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
    }
    // Mobile-first defaults + min-width breakpoints (Swiper 11 uses viewport unless breakpointsBase is set).
    var thInstagramProductsSliderBreakpoints = {
        576: { slidesPerView: 1.3 },
        768: { slidesPerView: 1.3 },
        992: { slidesPerView: 2.3 },
        1200: { slidesPerView: 3.3 }
    };
    if ($(".th-members-slider").length > 0) {
        $(".th-members-slider").each(function () {
            var el = this;
            new Swiper(el, {
                grabCursor: true,
                slidesPerView: 1,
                spaceBetween: 20,
                breakpointsBase: "window",
                breakpoints: thInstagramProductsSliderBreakpoints,
                navigation: {
                    nextEl: el.querySelector(".th-members-slider-next"),
                    prevEl: el.querySelector(".th-members-slider-prev"),
                },
            });
        });
    }
    if ($(".th-instagram-products-slider").not(".th-members-slider").length > 0) {
        $(".th-instagram-products-slider").not(".th-members-slider").each(function () {
            var root = this;
            var scrollbarEl = root.querySelector(".swiper-scrollbar");
            var instagramOpts = {
                grabCursor: true,
                slidesPerView: 1,
                spaceBetween: 20,
                breakpointsBase: "window",
                breakpoints: thInstagramProductsSliderBreakpoints,
            };
            if (scrollbarEl) {
                instagramOpts.scrollbar = {
                    el: scrollbarEl,
                    hide: false,
                    draggable: true,
                };
            }
            new Swiper(root, instagramOpts);
        });
    }
    /*---------- Product Instagram ----------*/
    if ($(".th-product-instagram-slider").length > 0) {
        $(".th-product-instagram-slider").each(function () {
            var root = this;
            var scrollbarEl = root.querySelector(".swiper-scrollbar");
            var productInstagramOpts = {
                grabCursor: true,
                slidesPerView: 1,
                spaceBetween: 20,
                breakpointsBase: "window",
                breakpoints: thInstagramProductsSliderBreakpoints,
            };
            if (scrollbarEl) {
                productInstagramOpts.scrollbar = {
                    el: scrollbarEl,
                    hide: false,
                    draggable: true,
                };
            }
            new Swiper(root, productInstagramOpts);
        });
    }

    (function initProductInstagramModal() {
        var section = document.querySelector("[data-v-component-productinstagram]");
        if (!section) {
            return;
        }

        var modal = section.querySelector("#th-product-instagram-modal");
        if (!modal) {
            return;
        }

        function loadPostsFromDom() {
            var triggers = section.querySelectorAll("[data-v-productinstagram-item-trigger]");
            return Array.prototype.map.call(triggers, function (trigger) {
                var slide = trigger.closest("[data-v-productinstagram-item]");
                var imageNode = slide ? slide.querySelector("[data-v-productinstagram-item-image]") : null;
                var thumbnail = imageNode ? (imageNode.getAttribute("data-bg-src") || "") : "";

                return {
                    thumbnail: thumbnail,
                    thumbnail_url: thumbnail,
                    caption: "",
                    product_url: "",
                    instagram_url: "",
                };
            });
        }

        function loadPosts() {
            var fromJson = [];

            try {
                var parsed = JSON.parse(section.getAttribute("data-product-instagram-posts") || "[]");
                if (Array.isArray(parsed)) {
                    fromJson = parsed;
                }
            } catch (error) {
                fromJson = [];
            }

            var fromDom = loadPostsFromDom();
            var count = Math.max(fromJson.length, fromDom.length);

            if (count === 0) {
                return [];
            }

            var merged = [];
            for (var i = 0; i < count; i++) {
                merged.push(Object.assign({}, fromDom[i] || {}, fromJson[i] || {}));
            }

            return merged;
        }

        var posts = loadPosts();

        if (!Array.isArray(posts) || posts.length === 0) {
            return;
        }

        var currentIndex = 0;
        var imageEl = modal.querySelector("[data-productinstagram-modal-image]");
        var captionEl = modal.querySelector("[data-productinstagram-modal-caption]");
        var productLinkEl = modal.querySelector("[data-productinstagram-modal-product-link]");
        var productTextEl = modal.querySelector("[data-productinstagram-modal-product-text]");
        var instagramLinkEl = modal.querySelector("[data-productinstagram-modal-instagram-link]");
        var hashtagsEl = modal.querySelector("[data-productinstagram-modal-hashtags]");
        var metaEl = modal.querySelector("[data-productinstagram-modal-meta]");
        var prevBtn = modal.querySelector("[data-productinstagram-modal-prev]");
        var nextBtn = modal.querySelector("[data-productinstagram-modal-next]");
        var shareLinks = modal.querySelectorAll("[data-productinstagram-share]");

        function escapeHtml(value) {
            return String(value || "")
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function extractHashtags(text) {
            var matches = String(text || "").match(/#[\w]+/g);
            return matches ? matches.join(" ") : "";
        }

        function stripHashtags(text) {
            return String(text || "")
                .replace(/#[\w]+/g, "")
                .replace(/\s{2,}/g, " ")
                .trim();
        }

        function formatPostDate(post) {
            var rawDate = post.created_at || post.updated_at || "";
            if (!rawDate) {
                return "";
            }

            var date = new Date(rawDate);
            if (Number.isNaN(date.getTime())) {
                return "";
            }

            return date
                .toLocaleDateString("en-AU", {
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                })
                .toUpperCase();
        }

        function getShareUrl(post) {
            return post.instagram_url || window.location.href;
        }

        function updateShareLinks(post) {
            var shareUrl = encodeURIComponent(getShareUrl(post));
            var shareText = encodeURIComponent(stripHashtags(post.caption || "Krost Instagram post"));
            var imageUrl = encodeURIComponent(post.thumbnail_url || post.thumbnail || "");

            shareLinks.forEach(function (node) {
                var type = node.getAttribute("data-productinstagram-share");
                if (type === "facebook") {
                    node.href = "https://www.facebook.com/sharer/sharer.php?u=" + shareUrl;
                    return;
                }
                if (type === "email") {
                    node.href = "mailto:?subject=" + shareText + "&body=" + shareUrl;
                    return;
                }
                if (type === "twitter") {
                    node.href = "https://twitter.com/intent/tweet?url=" + shareUrl + "&text=" + shareText;
                    return;
                }
                if (type === "pinterest") {
                    node.href = "https://pinterest.com/pin/create/button/?url=" + shareUrl + "&media=" + imageUrl + "&description=" + shareText;
                }
            });
        }

        function setNavState(index) {
            var atStart = index <= 0;
            var atEnd = index >= posts.length - 1;

            if (prevBtn) {
                prevBtn.classList.toggle("is-disabled", atStart);
                prevBtn.setAttribute("aria-disabled", atStart ? "true" : "false");
            }

            if (nextBtn) {
                nextBtn.classList.toggle("is-disabled", atEnd);
                nextBtn.setAttribute("aria-disabled", atEnd ? "true" : "false");
            }
        }

        function renderModal(index) {
            var post = posts[index] || {};
            var caption = post.caption || "";
            var hashtags = extractHashtags(caption);
            var captionText = stripHashtags(caption);
            var imageUrl = post.thumbnail_url || post.thumbnail || "";
            var productUrl = post.product_url || "";
            var instagramUrl = post.instagram_url || "";
            var formattedDate = formatPostDate(post);

            if (imageEl) {
                imageEl.src = imageUrl;
                imageEl.alt = captionText ? captionText.slice(0, 120) : "Instagram post";
            }

            if (captionEl) {
                captionEl.innerHTML = escapeHtml(captionText).replace(/\n/g, "<br>");
            }

            if (productLinkEl && productTextEl) {
                if (productUrl) {
                    productLinkEl.href = productUrl;
                    productLinkEl.hidden = false;
                    productTextEl.textContent = "View product";
                } else {
                    productLinkEl.hidden = true;
                }
            }

            if (instagramLinkEl) {
                instagramLinkEl.href = instagramUrl || "#";
                instagramLinkEl.hidden = !instagramUrl;
            }

            if (hashtagsEl) {
                hashtagsEl.textContent = hashtags;
                hashtagsEl.hidden = !hashtags;
            }

            if (metaEl) {
                metaEl.textContent = formattedDate
                    ? "KROSTFURNITURE // INSTAGRAM // " + formattedDate
                    : "KROSTFURNITURE // INSTAGRAM";
            }

            setNavState(index);
            updateShareLinks(post);
        }

        function openModal(index) {
            currentIndex = Math.max(0, Math.min(index, posts.length - 1));
            renderModal(currentIndex);
            modal.hidden = false;
            modal.setAttribute("aria-hidden", "false");
            document.body.classList.add("th-product-instagram-modal-open");
        }

        function closeModal() {
            modal.hidden = true;
            modal.setAttribute("aria-hidden", "true");
            document.body.classList.remove("th-product-instagram-modal-open");
        }

        section.addEventListener("click", function (event) {
            var trigger = event.target.closest("[data-v-productinstagram-item-trigger]");
            if (!trigger) {
                return;
            }

            event.preventDefault();
            var index = parseInt(trigger.getAttribute("data-productinstagram-index"), 10);
            if (Number.isNaN(index)) {
                return;
            }
            openModal(index);
        });

        modal.addEventListener("click", function (event) {
            if (event.target.closest("[data-productinstagram-modal-close]")) {
                closeModal();
                return;
            }

            if (event.target.closest("[data-productinstagram-modal-prev]")) {
                event.preventDefault();
                if (currentIndex > 0) {
                    openModal(currentIndex - 1);
                }
                return;
            }

            if (event.target.closest("[data-productinstagram-modal-next]")) {
                event.preventDefault();
                if (currentIndex < posts.length - 1) {
                    openModal(currentIndex + 1);
                }
            }
        });

        modal.addEventListener("click", function (event) {
            var copyBtn = event.target.closest('[data-productinstagram-share="copy"]');
            if (!copyBtn) {
                return;
            }

            event.preventDefault();
            var post = posts[currentIndex] || {};
            var url = getShareUrl(post);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url);
                return;
            }

            window.prompt("Copy this link:", url);
        });

        document.addEventListener("keydown", function (event) {
            if (modal.hidden) {
                return;
            }

            if (event.key === "Escape") {
                closeModal();
            } else if (event.key === "ArrowLeft" && currentIndex > 0) {
                openModal(currentIndex - 1);
            } else if (event.key === "ArrowRight" && currentIndex < posts.length - 1) {
                openModal(currentIndex + 1);
            }
        });
    })();

    if($(".workstations-slider").length > 0) {
        var workstationsSlider = new Swiper(".workstations-slider", {
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
    if($(".featured-material-slider").length > 0) {
        var featuredMaterialSlider = new Swiper(".featured-material-slider", {
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
    }
    if($(".th-featured-material-slider").length > 0) {
        var thFeaturedMaterialSlider = new Swiper(".th-featured-material-slider", {
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
    }

    // Product You May Also Like slider only for mobile view
    if ($(".th-product-may-like-slider").length > 0) {
        $(".th-product-may-like-slider").each(function () {
            var root = this;
            var scrollbarEl = root.querySelector(".swiper-scrollbar");
            var mayLikeOpts = {
                grabCursor: true,
                slidesPerView: 3.3,
                spaceBetween: 20,
                breakpointsBase: "window",
                breakpoints: {
                    0: { slidesPerView: 2 },
                    576: { slidesPerView: 2 }, // Fixed: Number instead of a string
                    768: { slidesPerView: 2.7 },
                    992: { slidesPerView: 2.7 },
                    1200: { slidesPerView: 3.7 }
                },
            };
            if (scrollbarEl) {
                mayLikeOpts.scrollbar = {
                    el: scrollbarEl,
                    hide: false,
                    draggable: true,
                };
            }
            new Swiper(root, mayLikeOpts);
        });
    }
    // end of Product You May Also Like slider only for mobile view
    if($(".product-related-slider").length > 0) {
        var productRelatedSlider = new Swiper(".product-related-slider", {
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
    }
    /*---------- 08.1 Other Custom slider End ----------*/



    /*---------- 09. Custom Animaiton For Slider Start ----------*/
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
    /*---------- 09. Custom Animaiton For Slider End ----------*/


    /*---------- 10. Choices Start ----------*/
    // keep a reference to the members Choices instance so we can update it later
    let choicesInstance = null;
    if (typeof Choices !== 'undefined') {
        if ($('#choose-members').length) {
            choicesInstance = new Choices('#choose-members', {
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
        // Timezone get by geolocation 
        if ($('#choose-timezone').length) {
            const chooseLocationSelect = document.getElementById('choose-location');
            const showroom_id = chooseLocationSelect?.value;
            // console.log('showroom_id new =', showroom_id);

            $.getJSON("/js/lib/timezones.json").then(res => {
                var defaultTz = (function () {
                    var AEST = 'AUS Eastern Standard Time';
                    try {
                        var iana = Intl.DateTimeFormat().resolvedOptions().timeZone;
                        if (!iana) return AEST;
                        var match = (res || []).find(function (t) {
                            return t.showroom_id === parseInt(showroom_id) && t.utc && Array.isArray(t.utc) && t.utc.indexOf(iana) !== -1;
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

    /*---------- 10. Choices End ----------*/

    /*---------- 11. Flatpickr Start ----------*/
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
                var disableRules = Array.isArray(window.bookingCalendarDisableDates)
                    ? window.bookingCalendarDisableDates
                    : [];
                // Use minimal configuration - similar to working example in js/main.js
                var fp = $(targetElement).flatpickr({
                    inline: true,
                    minDate: 'today',
                    disable: disableRules,
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
    window.updateBookingFlatpickrDisabledDates = function (disableRules) {
        var normalizedDisableRules = Array.isArray(disableRules) ? disableRules : [];
        $(".th-booking-calendar").each(function () {
            var $calendar = $(this);
            var $input = $calendar.find('input[data-input]');
            var targetElement = $input.length ? $input[0] : $calendar[0];
            var fpInstance = targetElement && targetElement._flatpickr ? targetElement._flatpickr : null;
            if (!fpInstance || typeof fpInstance.set !== 'function') {
                return;
            }
            fpInstance.set('disable', normalizedDisableRules);
            if (typeof fpInstance.redraw === 'function') {
                fpInstance.redraw();
            }
        });
    };
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
    /*---------- 11. Flatpickr End ----------*/

    /*---------- 12. Total icons Start ----------*/

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
    /*---------- 12. Total icons End ----------*/

    /*---------- 13. Product Configurator Accordion Start ----------*/
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
    /*---------- 13. Product Configurator Accordion End ----------*/


    /*---------- 14. Waypoints Start ----------*/
    const heroSection = document.querySelector(".th-way-points");
    const wayPoints = heroSection?.querySelectorAll(".way-point");
    // console.log(wayPoints);

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



    // on page load
    if (heroSection && wayPoints.length){
        window.addEventListener("resize", updateAllWayPoints);
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
    };

   
    /*---------- 14. Waypoints End ----------*/



    /*---------- 15. Catalogue Form Validation Start ----------*/
    // let requestCatalogueSection = document.getElementById('th-request-catalouge');
    let requestCatalogueSection = document.getElementById("th-request-catalogue");
    if (requestCatalogueSection) {
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
        if (catalogueFormat) {
            catalogueFormat.addEventListener('change', function() {

                if (this.value === 'physical_catalogue') {
                    document.getElementById('phone-number-group').classList.remove('d-none');
                    document.getElementById('request-my-copy-button').classList.remove('d-none');
                    document.getElementById('requestCatalogueSubmitButton').classList.add('d-none');
                    document.getElementById('company-group').classList.remove('d-none');
                    document.getElementById('mailing-address-group').classList.remove('d-none');
                    
                    document.getElementById('company')?.setAttribute('required', '');
                    document.getElementById('address-input')?.setAttribute('required', '');
                    
                    // Physical Copy state
                    document.getElementById('stateInput').classList.add('d-none');
                    document.getElementById('stateSelect')?.removeAttribute('required');
                    document.getElementById('stateSelect').value = '';
    
                } else if (this.value === 'online_catalogue') {
                    document.getElementById('phone-number-group').classList.add('d-none');
                    document.getElementById('request-my-copy-button').classList.add('d-none');
                    document.getElementById('requestCatalogueSubmitButton').classList.remove('d-none');
                    document.getElementById('company-group').classList.add('d-none');
                    document.getElementById('mailing-address-group').classList.add('d-none');
                    
                    document.getElementById('company')?.removeAttribute('required');
                    document.getElementById('address-input')?.removeAttribute('required');
    
                    // Digital Version state 
                    document.getElementById('stateInput').classList.remove('d-none');
                    document.getElementById('stateSelect')?.setAttribute('required', '');
    
                } else {
                    document.getElementById('company-group').classList.remove('d-none');
                    document.getElementById('mailing-address-group').classList.remove('d-none');
                    
                    // other require remove 
                    document.getElementById('stateInput').classList.add('d-none');
                    document.getElementById('stateSelect')?.removeAttribute('required');
                    document.getElementById('stateSelect').value = '';
                }
            });
    
            // onload change value
            catalogueFormat.dispatchEvent(new Event('change'));
        }
    }
    /*---------- 15. Catalogue Form Validation End ----------*/

    /*---------- 16. Sign up Form Validation start ----------*/
    if(document.getElementById('th-request-catalouge')){
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
    }
    /*---------- 16. Sign up Form Validation End ----------*/

    /*---------- 18. Password Toggle Start ----------*/
    if (document.getElementById('signup-page')) {
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
    }
    /*---------- 18. Password Toggle End ----------*/

    /*---------- 19. About Page => Who We Are Section Start ----------*/
    if (document.getElementById('whoWeAre')) {
        const whoWeAre = document.getElementById("whoWeAre");
        const whoWeAreGallery = lightGallery(whoWeAre, {
          container: whoWeAre,
          dynamic: !0,
          thumbnail: !1,
          swipeToClose: !1,
          addClass: 'lg-inline',
          mode: 'lg-scale-up',
          slideShowAutoplay: !1,
          autoPlay: !1,
          hash: !1,
          pager: !1,
          closable: !1,
          showMaximizeIcon: !0,
          rotate: !1,
          download: !1,
          thumbnailsPosition: 'bottom',
          plugins: [lgVideo],
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


        (function () {
            var id = 'about-who-you', hash = '#' + id;
            
            function flash(el) {
              if (!el) return;
              el.classList.add('about-who-you-anchor--flash');
              setTimeout(function () { 
                el.classList.remove('about-who-you-anchor--flash'); 
              }, 1200);
            }
            
            document.addEventListener('click', function (e) {
              var a = e.target.closest && e.target.closest('a[href]');
              if (!a) return;
              var href = (a.getAttribute('href') || '').trim();
              if (href !== hash && href.slice(-hash.length) !== hash) return;
              var el = document.getElementById(id);
              if (!el) return;
              
              e.preventDefault();
              history.pushState ? history.pushState(null, '', hash) : (location.hash = id);
              el.scrollIntoView({ behavior: 'smooth', block: 'start' });
              flash(el);
            });
            
            function onHash() {
              if (location.hash !== hash) return;
              flash(document.getElementById(id));
            }
            
            window.addEventListener('load', onHash);
            window.addEventListener('hashchange', onHash);
          })();


    }
    /*---------- 19. About Page => Who We Are Section End ----------*/

    /*---------- 20. About Page => gallery-manufacturingprocess-section Start ----------*/
    if (document.getElementById('manufacturingProcess') && typeof manufacturingProcessData !== 'undefined') {
        
            const manufacturingProcess = document.getElementById("manufacturingProcess");
            const manufacturingProcessGallery = lightGallery(manufacturingProcess, {
                container: manufacturingProcess,
                dynamic: !0,
                thumbnail: !1,
                swipeToClose: !1,
                addClass: 'lg-inline',
                mode: 'lg-scale-up',
                slideShowAutoplay: !1,
                autoPlay: !1,
                hash: !1,
                pager: !1,
                closable: !1,
                showMaximizeIcon: !0,
                rotate: !1,
                download: !1,
                plugins: [lgVideo],
                autoplayFirstVideo: !1,
                thumbnailsPosition: 'bottom',
                controls: !1,
                dynamicEl: manufacturingProcessData.map(function (item) {
                    return {
                      ...item,
                      subHtml: ''
                    };
                })
            });
            manufacturingProcessGallery.openGallery();
    }
    /*---------- 20. About Page => gallery-manufacturingprocess-section End ----------*/

    /*---------- 21. Quote Details Start ----------*/
    if (document.getElementById('account-show-quote')) {
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
    }
    /*---------- 21. Quote Details End ----------*/

    /*---------- 22. Contact Sales Get in Touch Section Start ----------*/
    if (document.getElementById('contact-getin-touch')) {
        // const uploadZone = document.getElementById('upload-zone');
        // const fileInput = document.getElementById('attachments');
        // const removeBtn = document.getElementById('remove-image');
        // const filenameEl = document.getElementById('upload-filename');
        // const filesFeedback = document.getElementById('files-feedback');

        // function clearFilesFeedback() {
        //     if (filesFeedback) {
        //         filesFeedback.textContent = '';
        //     }
        //     uploadZone.classList.remove('is-invalid');
        // }

        // function setFilesError(message) {
        //     if (filesFeedback) {
        //         filesFeedback.textContent = message;
        //     }
        //     uploadZone.classList.add('is-invalid');
        // }
      
        // // Click anywhere to open file
        // uploadZone.addEventListener('click', (e) => { if (e.target !== removeBtn) fileInput.click(); });
      
        // // When image selected
        // fileInput.addEventListener('change', function () {
        //   const file = this.files[0];
        //   if (!file) return;
      

        //   //  PDF, zip, docx file type validation
        // //   if (!file.type.startsWith('image/') && !file.type.startsWith('application/pdf') && !file.type.startsWith('application/zip') && !file.type.startsWith('application/docx')) {
        // //     setFilesError('Please select a valid image or pdf file.');
        // //     fileInput.value = '';
        // //     return;
        // //   }

        //   // file size up to 5mb
        // //   if (file.size > 5 * 1024 * 1024) {
        // //     setFilesError('File size is too large. Please select a file smaller than 5MB.');
        // //     alert('File size is too large. Please select a file smaller than 5MB.');
        // //     fileInput.value = '';
        // //     return;
        // //   }

        // //   clearFilesFeedback();
      
        //   // Show file name for both image and PDF
        //   filenameEl.textContent = file.name;
        //   uploadZone.classList.add('has-filename');
      
        //   // For PDF: show filename only (cannot preview PDF as background)
        //   if (file.type.startsWith('application/pdf')) {
        //     removeBtn.style.display = 'block';
        //     // return;
        //   }
      
        //   const reader = new FileReader();
        //   reader.onload = function (e) {
        //     var fileExt = file.name.split('.').pop();
        //     console.log(fileExt);
        //     uploadZone.style.backgroundSize = 'contain';
        //     if(fileExt === 'pdf'){
        //       uploadZone.style.backgroundImage = `url(/media/design-resource/icons/pdf.png)`;
        //     } else if(fileExt === 'zip'){
        //       uploadZone.style.backgroundImage = `url(/media/design-resource/icons/zip.png)`;
        //     } else if(fileExt === 'docx'){
        //       uploadZone.style.backgroundImage = `url(/media/design-resource/icons/docx.png)`;
        //     } else{
        //       uploadZone.style.backgroundImage = `url(${e.target.result})`;
        //     }
        //     uploadZone.classList.add('has-image');
        //     removeBtn.style.display = 'block';
        //   };
        //   reader.readAsDataURL(file);
        // });
      
        // // Remove image
        // removeBtn.addEventListener('click', function (e) {
        //   e.stopPropagation();
        //   fileInput.value = '';
        //   uploadZone.style.backgroundImage = '';
        //   uploadZone.classList.remove('has-image', 'has-filename');
        //   filenameEl.textContent = '';
        //   removeBtn.style.display = 'none';
        //   clearFilesFeedback();
        // });



        const uploadZone = document.getElementById('upload-zone');
        const fileInput = document.getElementById('attachments');
        const previewContainer = document.getElementById('upload-preview-list');
        const filesFeedback = document.getElementById('files-feedback');

        const MAX_FILES = 3;
        const MAX_FILE_SIZE = 15 * 1024 * 1024;
        let files = [];

        if (!uploadZone || !fileInput) {
            return;
        }

        const formatSize = (size) => {
            if (size < 1024 * 1024) {
                return Math.max(1, Math.round(size / 1024)) + ' KB';
            }
            return (size / (1024 * 1024)).toFixed(1) + ' MB';
        };

        const getExt = (name) => {
            const parts = String(name || '').split('.');
            if (parts.length < 2) return 'FILE';
            return parts[parts.length - 1].toUpperCase().slice(0, 4);
        };

        const setError = (msg = '') => {
            if (filesFeedback) {
                filesFeedback.textContent = msg;
                filesFeedback.style.display = msg ? 'block' : 'none';
            }
            uploadZone.classList.toggle('is-invalid', !!msg);
        };

        const syncFiles = () => {
            const dt = new DataTransfer();
            files.forEach((f) => dt.items.add(f));
            fileInput.files = dt.files;
        };

        const render = () => {
            if (!previewContainer) return;

            if (!files.length) {
                previewContainer.style.display = 'none';
                previewContainer.innerHTML = '';
                return;
            }

            previewContainer.style.display = 'block';
            previewContainer.innerHTML = files
                .map(
                    (file, i) => `
                <li>
                    <span class="th-file-type">${getExt(file.name)}</span>
                    <div class="th-file-content">
                        <strong>${file.name}</strong>
                        <small>${formatSize(file.size)} · ready to upload</small>
                    </div>
                    <button type="button" aria-label="Remove file" data-remove-index="${i}">×</button>
                </li>
            `
                )
                .join('');

            previewContainer.querySelectorAll('[data-remove-index]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const idx = Number(btn.getAttribute('data-remove-index'));
                    files.splice(idx, 1);
                    if (files.length < MAX_FILES) {
                        setError('');
                    }
                    syncFiles();
                    render();
                });
            });
        };

        const addFiles = (incomingList) => {
            if (!incomingList || !incomingList.length) return;

            const incoming = Array.from(incomingList);
            const spaceLeft = MAX_FILES - files.length;

            if (spaceLeft <= 0) {
                setError(`Maximum ${MAX_FILES} files allowed`);
                return;
            }

            let oversized = 0;
            let added = 0;

            incoming.forEach((file) => {
                if (added >= spaceLeft) return;
                if (file.size > MAX_FILE_SIZE) {
                    oversized += 1;
                    return;
                }
                const isDuplicate = files.some(
                    (f) => f.name === file.name && f.size === file.size
                );
                if (isDuplicate) return;
                files.push(file);
                added += 1;
            });

            const messages = [];
            if (oversized) {
                messages.push(
                    `Each file must be 15 MB or less (${oversized} skipped).`
                );
            }
            if (added < incoming.length - oversized) {
                messages.push(`Only ${MAX_FILES} files are allowed.`);
            }
            setError(messages.join(' '));

            syncFiles();
            render();
        };

        uploadZone.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            // Reset so the same file can be chosen again; in-memory list is restored via syncFiles on change/submit.
            fileInput.value = '';
            fileInput.click();
        });

        uploadZone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.value = '';
                fileInput.click();
            }
        });

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('is-drag-over');
        });

        uploadZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('is-drag-over');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('is-drag-over');
            addFiles(e.dataTransfer ? e.dataTransfer.files : null);
        });

        fileInput.addEventListener('change', () => {
            addFiles(fileInput.files);
        });

        const contactForm = document.getElementById('getin-touch-form');
        if (contactForm) {
            contactForm.addEventListener('submit', () => {
                syncFiles();
            });
        }

    }
    /*---------- 22. Contact Sales Get in Touch Section End ----------*/

    /*---------- 23. Contact Sales Hero Section Start ----------*/
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
    /*---------- 23. Contact Sales Hero Section End ----------*/

    /*---------- 23.1 Booking Reschedule Vue Modal Start ----------*/
    if (document.getElementById('reschedule-booking-button')) {
        const rescheduleBookingButton = document.getElementById('reschedule-booking-button');
        const resolveShowroomId = function (dataEl) {
            const explicitId = dataEl?.dataset?.showroomId || dataEl?.getAttribute('data-showroom-id') || '';
            if (explicitId) return String(explicitId);

            const showroomRaw = String(dataEl?.dataset?.showroom || '').toLowerCase();
            const locationRaw = String(dataEl?.dataset?.location || '').toLowerCase();
            const timeZoneRaw = String(dataEl?.dataset?.timeZone || '').toLowerCase();
            const pinboardId = dataEl?.dataset?.pinboardId || '';
            const bag = `${showroomRaw} ${locationRaw} ${timeZoneRaw}`;

            if (bag.includes('sydney')) return '1';
            if (bag.includes('melbourne')) return '2';
            if (bag.includes('brisbane')) return '3';
            return '';
        };
        const openRescheduleVueModal = async function (event) {
            if (!rescheduleBookingButton) return;
            event.preventDefault();
            event.stopPropagation();
            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }

            const getData = document.getElementById('get-showroom-visit-data');
            if (!getData) return;
            const visitData = {
                showroomId: resolveShowroomId(getData),
                visitShowroomId: getData?.dataset?.visitShowroomId || '',
                selectedDate: getData?.dataset?.date || '',
                email: getData?.dataset?.guestEmail || '',
                locationAddress: getData?.dataset?.location || '',
                meetingTime: getData?.dataset?.meetingTime || '',
                timeZone: getData?.dataset?.timeZone || '',
                tourType: getData?.dataset?.tourType || '',
                showroomContactId: getData?.dataset?.showroomContactId || '',
                customerId: getData?.dataset?.customerId || '',
                guestName: getData?.dataset?.guestName || '',
                googleMapLink: getData?.dataset?.mapLink || '',
                pinboardId: getData?.dataset?.pinboardId || '',
            };

            const bookingModule = await import('/js/vue/booking.js');
            const bookingApp = bookingModule?.default || window.bookingApp;
            if (!bookingApp || typeof bookingApp.openRescheduleBookingModal !== 'function') {
                return;
            }
            await bookingApp.openRescheduleBookingModal(visitData);
        };
        rescheduleBookingButton.addEventListener('click', openRescheduleVueModal, true);
    }
    /*---------- 23.1 Booking Reschedule Vue Modal End ----------*/

    /*---------- 24. Account Navigation Start ----------*/
    if (document.getElementById('user-profile-info')) {
        
          const logoutButton = document.getElementById('logout-button');
        
          logoutButton?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            logoutUser();
        });
    }

    /*---------- 24. Account Navigation End ----------*/
    /*---------- 33. Product Detail Tabs Start ----------*/
    const downloadAllBtn = document.getElementById('download-all-link');

    if (downloadAllBtn && downloadAllBtn.classList.contains('th-link-text')) {
      downloadAllBtn.addEventListener('click', function () {
  
        const downloadLinks = document.querySelectorAll('.design-resource-tag');
  
        if (!downloadLinks.length) return;
  
        downloadLinks.forEach((link, index) => {
          const url = link.getAttribute('href');
  
          if (!url) return;
  
          setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = getFileName(url);
            a.style.display = 'none';
  
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
          }, index * 300);
        });
  
      });
    }

    // mobile download
    const downloadAllBtnMobile = document.getElementById('download-all-link-mobile');

    if (downloadAllBtnMobile && downloadAllBtnMobile.classList.contains('th-link-text')) {
        downloadAllBtnMobile.addEventListener('click', function () {
  
        const downloadLinks = document.querySelectorAll('.download-mobile-link');
  
        if (!downloadLinks.length) return;
  
        downloadLinks.forEach((link, index) => {
          const url = link.getAttribute('href');
  
          if (!url) return;
  
          setTimeout(() => {
            const a = document.createElement('a');
            a.href = url;
            a.download = getFileName(url);
            a.style.display = 'none';
  
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
          }, index * 300);
        });
  
      });
    }
  
    const imageButtons = document.querySelectorAll('[data-v-designresourceimages-image-download-btn]');
  
    if (imageButtons.length) {
      imageButtons.forEach(btn => {
  
        // optional class check
        if (!btn.classList.contains('th-btn-download-white')) return;
  
        btn.addEventListener('click', function () {
          const url = this.getAttribute('data-src');
  
          if (!url) return;
  
          const a = document.createElement('a');
          a.href = url;
          a.download = getFileName(url);
          a.style.display = 'none';
  
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        });
  
      });
    }
  
    function getFileName(url) {
      return url.split('/').pop().split('?')[0];
    }



    
/**
 * ICS Generator Start
 */
(function (global) {
    "use strict";
  
    function escapeIcsText(str) {
        if (!str) return "";
        return String(str).replace(/\\/g, "\\\\").replace(/;/g, "\\;").replace(/,/g, "\\,").replace(/\n/g, "\\n");
    }
  
    function toIcsDateLocal(date) {
        const d = new Date(date);
        const pad = n => String(n).padStart(2, "0");
        return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}T${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
    }
  
    function generateUid() {
        return Date.now().toString(36) + "-" + Math.random().toString(36).slice(2) + "@ics-generator";
    }
  
    function buildIcs(event, target = "universal") {
        const dtstamp = toIcsDateLocal(new Date());
        const dtstart = toIcsDateLocal(event.start);
        const dtend = toIcsDateLocal(event.end);
        const uid = event.uid || generateUid();
        let prodId = "-//ICS Generator//EN";
  
        if (target === "outlook") prodId = "-//Microsoft Corporation//Outlook 16.0 MIMEDIR//EN";
        if (target === "mac") prodId = "-//Apple Inc.//Mac OS X 10.15//EN";
  
        const lines = [
            "BEGIN:VCALENDAR",
            "VERSION:2.0",
            "PRODID:" + prodId,
            "CALSCALE:GREGORIAN",
            "METHOD:PUBLISH",
            "BEGIN:VEVENT",
            "UID:" + uid,
            "DTSTAMP:" + dtstamp,
            "DTSTART:" + dtstart,
            "DTEND:" + dtend,
            "SUMMARY:" + escapeIcsText(event.title || "Untitled Event"),
            "DESCRIPTION:" + escapeIcsText(event.description || ""),
            "LOCATION:" + escapeIcsText(event.location || ""),
            "ORGANIZER:" + (event.organizer || ""),
            "URL:" + (event.url || ""),
            "END:VEVENT",
            "END:VCALENDAR"
        ];
  
        return lines.join("\r\n");
    }
  
    function downloadIcs(icsContent, filename) {
        const blob = new Blob([icsContent], { type: "text/calendar;charset=utf-8" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename || "event.ics";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
  
    global.ICSGenerator = { buildIcs, downloadIcs };
  })(window);
  
  // Convert date + time string + timezone to local Date object
  function parseDateTime(dateStr, timeStr, timeZone) {
    const [monthStr, dayYear] = dateStr.split(" ");
    const [day, year] = dayYear.split(",").map(s => parseInt(s.trim()));
    let [hourMin, ampm] = timeStr.split(" ");
    let [hour, min] = hourMin.split(":").map(Number);
  
    if (ampm.toUpperCase() === "PM" && hour < 12) hour += 12;
    if (ampm.toUpperCase() === "AM" && hour === 12) hour = 0;
  
    const monthIndex = new Date(`${monthStr} 1, 2026`).getMonth();
    const localDate = new Date(year, monthIndex, day, hour, min);
  
    try {
        const tzDate = new Date(localDate.toLocaleString("en-US", { timeZone: timeZone }));
        return tzDate;
    } catch (e) {
        console.warn("Invalid timezone, using local date");
        return localDate;
    }
  }
  
  // Main function
  window.downloadIcsFile = function (el, target = "universal") {
    const title = el.dataset.projectName || "Booking Event";
    const customerName = el.dataset.customerName || "";

    const customerEmail = el.dataset.customerEmail || "";
    const startTime = el.dataset.meetingTime || "09:00 AM";
    const dateStr = el.dataset.visitShowroomDate || "Mar 20,2026";
    const timeZone = el.dataset.timeZone || "Asia/Dhaka";
    const customerPhone = el.dataset.customerPhone || "";
    const location = el.dataset.location || "";
    const tourType = el.dataset.tourType == "physicalTour" ? "Krost:Visit Showroom Booking for Physical Tour" : "Krost:Visit Virtual Booking for Virtual Tour";
    // console.log(tourType);
    const startDate = parseDateTime(dateStr, startTime, timeZone);
    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000); // 1 hour
    const description =tourType + ' - sales@krost.com.au';

    const event = {
        title: description,
        description: description,
        location: location,
        start: startDate,
        end: endDate,
        organizer: "Krost: Sales Team <sales@krost.com.au>",
        url: window.location.href
    };
  
    const icsContent = ICSGenerator.buildIcs(event, target);
    ICSGenerator.downloadIcs(icsContent, "booking.ics");
  };

    // Attach click to all links
    if (document.getElementById("download-ics-file-outlook")) {
        document.getElementById("download-ics-file-outlook").addEventListener("click", e => {
            e.preventDefault();
            // empty local storage for pinboard_processed
            localStorage.removeItem('pinboard_processed');
            downloadIcsFile(e.target, "outlook");
        });
        }
        if (document.getElementById("download-ics-file-mac")) {
        document.getElementById("download-ics-file-mac").addEventListener("click", e => {
            e.preventDefault();
            // empty local storage for pinboard_processed
            localStorage.removeItem('pinboard_processed');
            downloadIcsFile(e.target, "mac");
        });
    }
  /**
 * ICS Generator End
 */

    // send message function
    if (document.getElementById("booking-showroom-visit-note")) {
       const SHOWROOM_NOTE_MAX = 600;
       // message input field
       const getShowroomVisitData = document.getElementById("get-showroom-visit-data");
       const showroomNote = document.getElementById("showroomNote");
       const messageInput = document.getElementById("booking-showroom-visit-note");
       const noteCharCounter = document.getElementById("booking-showroom-visit-note-counter");
       // click send-booking-showroom-visit-note-button 
       const sendButton = document.getElementById("send-booking-showroom-visit-note-button");
       const visitShowroomId = getShowroomVisitData?.dataset?.visitShowroomId;
       const pinboardId = getShowroomVisitData?.dataset?.pinboardId ?? '';
       const email = getShowroomVisitData?.dataset?.guestEmail;
       const location = getShowroomVisitData?.dataset?.location;
       const noteFeedbackEl = document.getElementById("booking-showroom-visit-note-feedback");
       function syncShowroomNoteCharCount() {
         if (noteCharCounter) {
           noteCharCounter.textContent = `${messageInput.value.length} / ${SHOWROOM_NOTE_MAX} characters`;
         }
       }
       function clearShowroomNoteFeedback() {
         messageInput.classList.remove("is-invalid");
         if (noteFeedbackEl) {
           noteFeedbackEl.textContent = "";
           noteFeedbackEl.classList.remove("text-danger", "text-success");
         }
       }
       // if on blur 
       function handleInputEvent() {
        showroomNote.classList.remove("th-opacity-text");
        messageInput.classList.add("th-text-primary");
        sendButton.classList.add("th-text-primary");
      }
      
      if (messageInput.value.length > SHOWROOM_NOTE_MAX) {
        messageInput.value = messageInput.value.slice(0, SHOWROOM_NOTE_MAX);
      }
      syncShowroomNoteCharCount();
      messageInput.addEventListener("input", () => {
        clearShowroomNoteFeedback();
        syncShowroomNoteCharCount();
      });
      // both events separately
    //   messageInput.addEventListener("keyup", handleInputEvent);
      messageInput.addEventListener("click", handleInputEvent);
       sendButton.addEventListener("click", e => {

        e.preventDefault();
        // make validation for messageInput
        // empty local storage for pinboard_processed
        localStorage.removeItem('pinboard_processed');
        const message = messageInput.value.trim();

        // Empty validation
        if (message === "") {
          messageInput.classList.add("is-invalid");
          noteFeedbackEl.classList.add("text-danger");
          noteFeedbackEl.textContent = "Message is required";
          return;
        }
  
        if (message.length > SHOWROOM_NOTE_MAX) {
          messageInput.classList.add("is-invalid");
          noteFeedbackEl.classList.add("text-danger");
          noteFeedbackEl.textContent =
            `Message must be ${SHOWROOM_NOTE_MAX} characters or fewer`;
          return;
        }
        messageInput.classList.remove("is-invalid");

        sendButton.disabled = true;
        const originalHtml = sendButton.innerHTML;
        sendButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Send Message';

        const apiUrl = "/api/visit-showroom/send-message";
        const apiData = {
          note: messageInput.value,
          visit_showroom_id: parseInt(visitShowroomId),
          pinboard_id: null,
          email: email,
          location: location,
        };
        
        fetch(apiUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json", 
            "Accept": "application/json",
          },
          body: JSON.stringify(apiData),
        })
        .then(response => response.json())
        .then(data => {
          if(data.success) {
            document.getElementById("booking-showroom-visit-note-feedback").textContent = "Message sent successfully";
            // add feedback class text-success
            document.getElementById("booking-showroom-visit-note-feedback").classList.add("text-success");
            showroomNote.classList.add("th-opacity-text"); // #231f20
            messageInput.classList.remove("th-text-primary"); // #231f20
            sendButton.classList.remove("th-text-primary"); // #231f20
            sendButton.disabled = false;
            sendButton.innerHTML = originalHtml;
          } else {
            document.getElementById("booking-showroom-visit-note-feedback").textContent = data.message;
            // add feedback class text-danger
            document.getElementById("booking-showroom-visit-note-feedback").classList.add("text-danger");
          }
          
        })
        .catch(error => {
          console.error(error);
        });

       });

    }

    // product detail tabs media gallery
    if(document.getElementById("product-media-gallery")) {
        lightGallery(
            document.getElementById("product-media-gallery"),
            {
                mousewheel: true,
                // dynamic: true,
                autoplayFirstVideo: false,
                pager: false,
                galleryId: "nature",
                plugins: [lgZoom, lgThumbnail],
                // plugins: [lgZoom, lgThumbnail],
                mobileSettings: {
                    controls: true,
                    showCloseIcon: true,
                    download: false,
                    rotate: false
                }
            }
        );
        // use custom slider in product video and gallery in product tabs
        const gallery = document.getElementById("product-media-gallery");
        const prevBtn = document.getElementById("slidePrev");
        const nextBtn = document.getElementById("slideNext");
    
        if (gallery && prevBtn && nextBtn) {
    
          // Exact slide distance: Item width (150px) + gap (15px) multiplied by 2 items width
          const slideDistance = (150 + 15) * 5;
    
          // Next Button Click Logic
          nextBtn.onclick = function (e) {
            e.preventDefault();
            e.stopPropagation(); // Prevents theme layout frameworks from breaking click events
    
            gallery.scrollTo({
              left: gallery.scrollLeft + slideDistance,
              behavior: "smooth"
            });
          };
    
          // Prev Button Click Logic
          prevBtn.onclick = function (e) {
            e.preventDefault();
            e.stopPropagation();
    
            gallery.scrollTo({
              left: gallery.scrollLeft - slideDistance,
              behavior: "smooth"
            });
          };

            // Hide arrows if no scroll needed
            function toggleArrows() {
                const items = gallery.querySelectorAll(".th-product-media-item");
                // Reset classes
                gallery.classList.remove("single-row-mode", "two-row-mode");

                // Toggle layout mode
                if (items.length <= 4) {
                    gallery.classList.add("single-row-mode");
                } else {
                    gallery.classList.add("two-row-mode");
                }

                // Each column holds 2 items (because height: 540px and item height: 250px)
                const itemsPerColumn = 2;
                const visibleColumns = 4;// You show 4 columns at a time
                const maxVisibleItems = itemsPerColumn * visibleColumns; // = 8
                
                if (items.length <= maxVisibleItems) {
                  prevBtn.style.display = "none";
                  nextBtn.style.display = "none";
                  gallery.style.setProperty("flex-direction", "row", "important");
                } else {
                  prevBtn.style.display = "flex";
                  nextBtn.style.display = "flex";
                }
            }

            setTimeout(toggleArrows, 300);
            window.addEventListener("resize", toggleArrows);
            
            document.querySelector('#media-tab')?.addEventListener('click', () => {
                setTimeout(toggleArrows, 100);
            });

        }


        // Drag to scroll
        let isDown = false;
        let startX;
        let scrollLeft;

        gallery.style.cursor = "grab";

        gallery.addEventListener("mousedown", (e) => {
            isDown = true;
            gallery.classList.add("dragging");
            gallery.style.cursor = "grabbing";

            startX = e.pageX - gallery.offsetLeft;
            scrollLeft = gallery.scrollLeft;
        });

        document.addEventListener("mouseup", () => {
            isDown = false;
            gallery.classList.remove("dragging");
            gallery.style.cursor = "grab";
        });

        gallery.addEventListener("mouseleave", () => {
            isDown = false;
            gallery.classList.remove("dragging");
            gallery.style.cursor = "grab";
        });

        gallery.addEventListener("mousemove", (e) => {
            if (!isDown) return;

            e.preventDefault();

            const x = e.pageX - gallery.offsetLeft;
            const walk = (x - startX) * 5; // drag speed
            gallery.scrollLeft = scrollLeft - walk;
        });

        
    }

    // =========================== mobile slider start =============================
    if(document.getElementById("product-media-gallery-mobile")) {
        lightGallery(
            document.getElementById("product-media-gallery-mobile"),
            {
                // dynamic: true,
                autoplayFirstVideo: false,
                pager: false,
                galleryId: "nature",
                plugins: [lgZoom, lgThumbnail],
                // plugins: [lgZoom, lgThumbnail],
                mobileSettings: {
                    controls: true,
                    showCloseIcon: true,
                    download: false,
                    rotate: false
                }
            }
        );
        // if(window.innerWidth <= 768){
            const container = document.getElementById('product-media-gallery-mobile');
            const nextBtna = document.getElementById('slideNextMobile');
            const prevBtna = document.getElementById('slidePrevMobile');

            const scrollAmount = 150;

            nextBtna.addEventListener('click', () => {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            prevBtna.addEventListener('click', () => {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });

            // Hide arrows if no scroll needed
            function toggleArrows() {
                const itemsMobile = container.querySelectorAll(".th-product-media-item");
                // Reset classes
                container.classList.remove("single-row-mode", "two-row-mode");

                // Toggle layout mode
                if (itemsMobile.length <= 2) {
                    container.classList.add("single-row-mode");
                } else {
                    container.classList.add("two-row-mode");
                }

                // Each column holds 2 items (because height: 285px and item height: 120px)
                const itemsPerColumn = 2;
                const visibleColumns = 2;
                const maxVisibleItems = itemsPerColumn * visibleColumns; // = 4
                
                if (itemsMobile.length <= maxVisibleItems) {
                prevBtna.style.display = "none";
                nextBtna.style.display = "none";
                container.style.setProperty("flex-direction", "row", "important");
                } else {
                prevBtna.style.display = "flex";
                nextBtna.style.display = "flex";
                }
            }

            setTimeout(toggleArrows, 300);
            window.addEventListener("resize", toggleArrows);
            
            document.querySelector('#media-tab')?.addEventListener('click', () => {
                setTimeout(toggleArrows, 100);
            });
        // } 
    }



// =========================== mobile slider end =============================
    // =========================== mobile slider start =============================


    // end
    /*---------- 34. Footer Subscription Form Success Feedback Start ----------*/
    const locationSelect = document.getElementById('choose-location');
    const timezoneSelect = document.getElementById('choose-timezone');
    if(locationSelect) {
    const selectedLocationName = document.querySelector('[data-v-booknow-name]');
    const selectedLocationImage = document.querySelector('[data-v-booknow-member_image]');
  
    function syncSelectedMemberDetails() {
      if (!locationSelect) return;
      const selectedOption = locationSelect.options[locationSelect.selectedIndex];
      if (!selectedOption) return;
  
      const locationId = selectedOption.value || '';
      const locationName = selectedOption.textContent || '';
      const locationImage = selectedOption.getAttribute('data-image') || '';
  
      if (selectedLocationName) {
        selectedLocationName.textContent = locationName;
      }
  
      if (selectedLocationImage && locationImage) {
        selectedLocationImage.setAttribute('src', locationImage);
        selectedLocationImage.setAttribute('alt', locationName || 'Member Avatar');
      }

    }
  
      locationSelect?.addEventListener('change', syncSelectedMemberDetails);
    }


 
    // if th-phone-icon element exists, then add tooltip event listener
    if (document.querySelector('.th-phone-icon')) {

        const hasTooltip = window.jQuery && typeof window.jQuery.fn.tooltip === 'function';
    
        // Init tooltip if jQuery exists
        if (hasTooltip) {
          window.jQuery('[data-toggle="tooltip"]').tooltip();
        }
    
        document.querySelectorAll('.th-phone-icon').forEach(el => {
            // console.log("phone click ",el);
          // Hover
          el.addEventListener('mouseenter', () => {
            const phone = el.getAttribute('data-member-phone') || '';
            if (!phone) return;
    
            if (hasTooltip) {
              window.jQuery(el)
                .attr('data-original-title', phone)
                .tooltip('show');
            } else {
              el.setAttribute('title', phone);
            }
          });
    
          // Leave
          el.addEventListener('mouseleave', () => {
            if (hasTooltip) {
              window.jQuery(el)
                .tooltip('hide')
                .attr('data-original-title', 'Click to copy phone');
            }
          });
    
          // Click
          el.addEventListener('click', async () => {
            const phone = el.getAttribute('data-member-phone') || '';
            if (!phone) return;
    
            try {
              await navigator.clipboard.writeText(phone);
              showSimplePrompt(`Copied phone number: ${phone}`, el);
    
              setTimeout(() => {
                window.location.href = `tel:${phone}`;
              }, 800);
    
            } catch (err) {
              fallbackCopy(phone, el);
            }
          });
    
          // Keyboard support
          el.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              el.click();
            }
          });
    
        });
    
        // Fallback copy
        function fallbackCopy(text, el) {
          const ta = document.createElement('textarea');
          ta.value = text;
          ta.style.position = 'fixed';
          ta.style.left = '-9999px';
    
          document.body.appendChild(ta);
          ta.select();
    
          try {
            document.execCommand('copy');
            showSimplePrompt('Copied!', el);
          } catch (e) {
            console.error('Copy failed', e);
          }
    
          document.body.removeChild(ta);
        }
    
        // Toast message
        function showSimplePrompt(text, targetEl) {
            console.log("sssssssssss",targetEl);
            const id = 'krost-copy-prompt';
            let existing = document.getElementById(id);
          
            if (!existing) {
              existing = document.createElement('div');
              existing.id = id;
          
              Object.assign(existing.style, {
                position: 'absolute',
                background: 'rgba(0,0,0,0.8)',
                color: '#fff',
                padding: '6px 12px',
                borderRadius: '16px',
                fontSize: '13px',
                zIndex: 9999,
                whiteSpace: 'nowrap',
                pointerEvents: 'none',
                boxShadow: '0 4px 10px rgba(0,0,0,0.2)'
              });

            //   const dd = document.getElementById('sales-team-sydney');
            //   console.log("sssssssssss",dd);
            //   dd.appendChild(existing);
              document.body.appendChild(existing);
            }
          
            existing.textContent = text;
          
            // Position beside element
            if (targetEl) {
              const rect = targetEl.getBoundingClientRect();
          
              existing.style.top = `${rect.top + window.scrollY}px`;
              existing.style.left = `${rect.right + 10 + window.scrollX}px`;
            }
          
            clearTimeout(existing._timeout);
            existing._timeout = setTimeout(() => {
              existing.remove();
            }, 1500);
          }
        }
    

        document.getElementById("recent-documents-expand-toggle")?.addEventListener("click", e => {
          e.preventDefault();
          const documentsList = document.getElementById("th-documents-list");
          const recentOrdersItems = documentsList?.querySelectorAll("[data-v-recent-order-item]");
          recentOrdersItems?.forEach(item => {
            item.classList.remove("d-none");
          });
          const recentOrdersDivider = documentsList?.querySelectorAll("[data-v-recent-orders-divider]");
          recentOrdersDivider?.forEach(divider => {
            divider.classList.remove("d-none");
          });

          const activeQuotes = documentsList?.querySelectorAll("[data-v-active-quote-item]");
          activeQuotes.forEach(item => {
            item.classList.remove("d-none");
          });
          const activeQuotesDivider = document.querySelectorAll("[data-v-active-quotes-divider]");
          activeQuotesDivider.forEach(divider => {
            divider.classList.remove("d-none");
          });
          document.getElementById("recent-documents-expand-toggle").classList?.add("d-none");
        });   
                
        
        (function initContactSalesBookNowDeepLink() {
            const HEADER_OFFSET = 100;

            function isBookNowDeepLink() {
                const hash = window.location.hash || '';
                return hash === '#book-now' || hash.indexOf('#book-now') === 0;
            }

            if (!isBookNowDeepLink() || !document.getElementById('book-now')) return;

            document.documentElement.classList.add('is-book-now-deeplink');

            function scrollToBookNow() {
                const target = document.getElementById('book-now');
                if (!target) return false;
                const top = target.getBoundingClientRect().top + window.pageYOffset - HEADER_OFFSET;
                window.scrollTo(0, Math.max(0, top));
                return true;
            }

            scrollToBookNow();
            window.addEventListener('load', scrollToBookNow, { once: true });
        })();


        // add to pinboard tooltip 
        (function initFeaturedProjectsPinboardTooltip() {
            function applyTooltip() {
                var selectors = '.th-pinboard-tooltip[data-toggle="tooltip"]';
                var tooltipEls = document.querySelectorAll(selectors);
                if (!tooltipEls.length) return;
        
                var hasJqueryTooltip = window.jQuery && typeof window.jQuery.fn.tooltip === 'function';
        
                tooltipEls.forEach(function (el) {
                    var tooltipText = el.getAttribute('data-bs-original-title')
                    || el.getAttribute('data-original-title')
                    || 'Add to Pinboard';
        
                    if (hasJqueryTooltip) {
                    window.jQuery(el)
                        .attr('title', '')
                        .attr('data-original-title', tooltipText)
                        .tooltip();
                    } else {
                    // Fallback to native browser tooltip.
                    el.setAttribute('title', tooltipText);
                    }
                });
            }
      
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', applyTooltip);
            } else {
                applyTooltip();
            }
        })();



 
        // 36. Account user profile update
        if(document.getElementById("profile-state-input")) {
     
            const state = document.getElementById('profile-state-input')?.value || '';

            const stateSelect = document.querySelector('[data-v-accountprofile-state]');
            if (stateSelect) {
                stateSelect.value = state;
            }

            const notifyOrders =
            document.querySelector('[data-v-accountprofile-notify_orders]')?.value;
      
          const notifyQuotes =
            document.querySelector('[data-v-accountprofile-notify_quotes]')?.value;
      
          const notifyProductNews =
            document.querySelector('[data-v-accountprofile-notify_product_news]')?.value;
      
          const ordersCheckbox =
            document.querySelector('[data-v-accountprofile-notify-orders]');
      
          const quotesCheckbox =
            document.querySelector('[data-v-accountprofile-notify-quotes]');
      
          const productNewsCheckbox =
            document.querySelector('[data-v-accountprofile-notify-product-news]');
      
          if (ordersCheckbox) {
            ordersCheckbox.checked =
              notifyOrders === '1' || notifyOrders === 'true';
          }
      
          if (quotesCheckbox) {
            quotesCheckbox.checked =
              notifyQuotes === '1' || notifyQuotes === 'true';
          }
      
          if (productNewsCheckbox) {
            productNewsCheckbox.checked =
              notifyProductNews === '1' || notifyProductNews === 'true';
          }

        }

        // form validation accout user profile
        if(document.getElementById("account-profile-form")) {
            const form = document.getElementById('account-profile-form');
            const phoneField = document.getElementById('profile-phone');
            const postcodeField = document.getElementById('profile-postcode');
            const stateField = document.getElementById('profile-state');
            const submitBtn = document.querySelector('[data-v-accountprofile-save]');
        
            if (!form || !submitBtn) return;
        
            function updateSubmitButton() {

                const hasErrors = document.querySelectorAll('.field-error').length > 0;
            
                submitBtn.disabled = hasErrors;
            
                if (hasErrors) {
                    submitBtn.classList.add('is-disabled');
                } else {
                    submitBtn.classList.remove('is-disabled');
                }
            }
        
            function removeError(field) {
        
                const existingError = field.parentNode.querySelector('.field-error');
        
                if (existingError) {
                    existingError.remove();
                }
        
                field.classList.remove('is-invalid');
        
                updateSubmitButton();
            }
        
            function showError(field, message) {
        
                removeError(field);
        
                field.classList.add('is-invalid');
        
                const error = document.createElement('div');
                error.className = 'field-error';
                error.textContent = message;
        
                field.parentNode.appendChild(error);
        
                updateSubmitButton();
            }
        
            function isValidAustralianPhone(phone) {
        
                phone = phone.replace(/\s+/g, '');
        
                // return /^(?:\+61|0)(?:[2378]\d{8}|4\d{8})$/.test(phone);
                return true;
            }
        
            function isValidAustralianPostcodeForState(postcode, state) {
        
                postcode = parseInt(postcode, 10);
        
                const ranges = {
                    NSW: [
                        [1000, 1999],
                        [2000, 2599],
                        [2619, 2899],
                        [2921, 2999]
                    ],
                    ACT: [
                        [200, 299],
                        [2600, 2618],
                        [2900, 2920]
                    ],
                    VIC: [
                        [3000, 3999],
                        [8000, 8999]
                    ],
                    QLD: [
                        [4000, 4999],
                        [9000, 9999]
                    ],
                    SA: [
                        [5000, 5999]
                    ],
                    WA: [
                        [6000, 6999]
                    ],
                    TAS: [
                        [7000, 7999]
                    ],
                    NT: [
                        [800, 999]
                    ]
                };
        
                if (!ranges[state]) {
                    return true;
                }
        
                return ranges[state].some(([min, max]) =>
                    postcode >= min && postcode <= max
                );
            }
        
            function validatePhone() {
        
                if (!phoneField) return true;
        
                const phone = phoneField.value.trim();
        
                removeError(phoneField);
        
                if (!phone) {
                    return true;
                }
                if (!/^\d{10}$/.test(phone)) {
        
                    showError(
                        phoneField,
                        'Requires 10 digits.'
                    );
        
                    return false;
                }
        
                // if (!isValidAustralianPhone(phone)) {
        
                //     showError(
                //         phoneField,
                //         'Please enter a valid Australian phone number.'
                //     );
        
                //     return false;
                // }
        
                return true;
            }
        
            function validatePostcode() {
        
                if (!postcodeField) return true;
        
                const postcode = postcodeField.value.trim();
                const state = stateField ? stateField.value : '';
        
                removeError(postcodeField);
        
                if (!postcode) {
                    return true;
                }
        
                if (!/^\d{4}$/.test(postcode)) {
        
                    showError(
                        postcodeField,
                        'Requires 4 digits.'
                    );
        
                    return false;
                }
        
                // if (
                //     state &&
                //     !isValidAustralianPostcodeForState(postcode, state)
                // ) {
        
                //     showError(
                //         postcodeField,
                //         `Postcode ${postcode} does not belong to ${state}.`
                //     );
        
                //     return false;
                // }
        
                return true;
            }
        
            // Blur validation
            phoneField?.addEventListener('blur', validatePhone);
            postcodeField?.addEventListener('blur', validatePostcode);
        
            // Live validation
            phoneField?.addEventListener('input', validatePhone);
            postcodeField?.addEventListener('input', validatePostcode);
        
            stateField?.addEventListener('change', validatePostcode);
        
            // Final submit validation
            form.addEventListener('submit', function (e) {
        
                const phoneValid = validatePhone();
                const postcodeValid = validatePostcode();
        
                if (!phoneValid || !postcodeValid) {
        
                    e.preventDefault();
        
                    const firstError = document.querySelector('.field-error');
        
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
        
                    return false;
                }
        
                const label = submitBtn.querySelector('[data-submit-label]');
                const spinner = submitBtn.querySelector('[data-submit-spinner]');
        
                if (label) {
                    label.textContent = 'Saving...';
                }
        
                if (spinner) {
                    spinner.classList.remove('d-none');
                    spinner.setAttribute('aria-hidden', 'false');
                }
        
                submitBtn.disabled = true;
            });
        
            updateSubmitButton();
        }

});



function removeDivOnMobile() {
    if (window.innerWidth <= 768) {
        const tutorialDiv = document.getElementById("user-tutorial");
        if (tutorialDiv) {
            tutorialDiv.remove();
        }
    }
}
// 1. Will run the first time the page loads.
window.addEventListener('DOMContentLoaded', removeDivOnMobile);

//2. It will still run if the user resizes the browser window.
window.addEventListener('resize', removeDivOnMobile);
