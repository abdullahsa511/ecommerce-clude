<!DOCTYPE html><html lang="en"><?php  if(isset($current_component)) $previous_component = $current_component; $head = $current_component = isset($this->_component['head']) ? $this->_component['head']??[] : [];  ?><head data-v-component-head="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">


  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin="">
  <link rel="preconnect" href="https://unpkg.com" crossorigin="">

  <base href="">

  <title data-v-head-title=""><?php  echo isset($head['title']) ? $head['title'] : '';  ?></title>

  <meta name="description" content="Krost Business Furniture" data-v-head-meta-description="">
  <meta name="keywords" content="Krost Business Furniture, Business Furniture, Office Furniture, Home Furniture, Furniture" data-v-head-meta-keywords="">
  <meta name="author" content="Shofiul Alam" data-v-head-meta-author="">

  <style>
 <?php echo App\Core\System\utils\__('html {
 box-sizing: border-box;
 scroll-behavior: smooth
 }
 *,
 *::before,
 *::after {
 box-sizing: inherit
 }
 body {
 margin: 0;
 font-family: "Open Sans", sans-serif;
 line-height: 1.5;
 -webkit-font-smoothing: antialiased
 }'); ?>
 </style>



  <link rel="preload" href="/fonts/opensans/OpenSans-Regular.ttf" as="font" type="font/ttf" crossorigin="">
  <link rel="preload" href="/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin="">
  <link rel="alternate" type="application/rss+xml" title="Feed" href="/feed/posts">
  <link rel="alternate" type="application/rss+xml" title="Comments Feed" href="/feed/comments">
  <link rel="icon" type="image/x-icon" href="../../media/favicon.ico" data-v-global-site.favicon="">




  <link rel="preload" href="/css/lib/swiper-bundle.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="stylesheet" href="/css/stylesheet.min.css">
  <link rel="stylesheet" href="/css/solid.min.css">


  <link rel="preload" href="/css/lib/choices.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/css/lib/flatpickr.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/css/lib/lightgallery-bundle.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="/css/lib/lg-transitions.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

  <style>
    .booking-calendar-wrapper {
      width: 100%;
      padding: 0 60px;
    }

    @media (max-width: 768px) {
      .booking-calendar-wrapper {
        padding: 70px 0;
      }
    }

    .th-booking-calendar .flatpickr-calendar.open,
    .th-booking-calendar .flatpickr-calendar.inline {
      max-height: 100%;
      width: 100%;
      background: none;
      -webkit-box-shadow: none;
      box-shadow: none;
    }

    .th-booking-calendar .flatpickr-months {
      width: 100%;
      justify-content: center;
      padding-bottom: 45px;
    }

    .th-booking-calendar .flatpickr-months .flatpickr-prev-month.flatpickr-prev-month,
    .th-booking-calendar .flatpickr-months .flatpickr-prev-month,
    .th-booking-calendar .flatpickr-months .flatpickr-prev-month.flatpickr-next-month,
    .th-booking-calendar .flatpickr-months .flatpickr-next-month {
      position: relative;
      left: inherit;
    }

    .th-booking-calendar .flatpickr-months .flatpickr-month {
      -webkit-box-flex: inherit;
      -webkit-flex: inherit;
      -ms-flex: inherit;
      flex: inherit;
      align-items: center;
    }

    .th-booking-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month {
      display: flex;
      text-align: center;
      width: 250px;
      position: relative;
    }

    .th-booking-calendar .flatpickr-rContainer {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    .th-booking-calendar .flatpickr-days {
      width: 100%;
    }

    .th-booking-calendar .dayContainer {
      width: 100%;
      max-width: 100%;
      min-width: 100%;
      display: grid;
      grid-template-columns: repeat(7, 1fr);
    }

    .th-booking-calendar .flatpickr-day {
      margin: 0;
      text-align: center;
      -webkit-box-flex: 1;
      -webkit-flex: 1;
      -ms-flex: 1;
      flex: 1;
      font-weight: bolder;
      width: 100%;
      max-width: inherit;
      max-height: inherit;
      border-radius: 0;
      height: 60px;
      display: flex;
      align-items: center;
    }
  </style>



  <script  >
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-NRR9X3MQ');
</script>

</head>

<body class="bg-white">


  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NRR9X3MQ" height="0" width="0" style="display:none;visibility:hidden"></iframe>
  </noscript>

  <div id="krost-recaptcha-config" class="d-none" aria-hidden="true" data-recaptcha-site-key="<?php  echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_SITE_KEY', ''), ENT_QUOTES, 'UTF-8');  ?>" data-recaptcha-action-contact="<?php  echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION', 'contact_submit'), ENT_QUOTES, 'UTF-8');  ?>" data-recaptcha-action-service="<?php  echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION_SERVICE', 'service_request'), ENT_QUOTES,'UTF-8');  ?>" data-recaptcha-action-project="<?php  echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION_PROJECT', 'project_submission'), ENT_QUOTES, 'UTF-8');  ?>" data-recaptcha-action-booking=""></div>

  <style>
    .th-pinboard-count-badge {
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      top: -10px;
      right: -10px;
      background-color: rgb(0, 0, 0);
      opacity: 0.8;
      color: #fff;
      border-radius: 50%;
      padding: 0;
      font-size: 12px;
      line-height: 1;
      font-weight: 700;
      box-sizing: border-box;
    }

    .th-pinboard-count-badge #pinboard-item-count {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .th-pinboard-count-badge-text {
      position: relative;
      left: -2px;
    }

    /* User dropdown - shows on click of user icon */
    .th-user-dropdown {
      position: relative;
    }

    .th-user-dropdown .dropdown-toggle {
      color: inherit;
      text-decoration: none;
      padding: 0;
      border: none;
      background: transparent;
      cursor: pointer;
      transition: opacity 0.2s ease;
    }

    .th-user-dropdown .dropdown-toggle:hover {
      opacity: 0.85;
    }

    .th-user-dropdown .dropdown-toggle::after {
      display: none;
      /* Hide default Bootstrap caret - icon is enough */
    }

    .th-user-dropdown .dropdown-menu {
      min-width: 180px;
      padding: 0px 0px;
      margin-top: 10px;
      border: 1px solid rgba(0, 0, 0, 0.08);
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .th-user-dropdown .dropdown-item {
      padding: 10px 16px;
      font-size: 15px;
      font-weight: 500;
      color: #2E2E2E;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    .th-user-dropdown .dropdown-item:hover {
      background-color: rgba(0, 0, 0, 0.04);
      color: #231F20;
    }

    .th-user-dropdown .dropdown-divider {
      margin: 6px 0;
    }

    .th-user-dropdown .dropdown-item#logout-button {
      color: #6B7280;
    }

    .th-user-dropdown .dropdown-item#logout-button:hover {
      color: #DC2626;
      background-color: rgba(220, 38, 38, 0.06);
    }
  </style>

  <?php  if(isset($current_component)) $previous_component = $current_component; $header = $current_component = $this->_component['header']?? []; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  ?><div class="th-header header-layout menu-white" data-v-component-header="" role="header">
    <div class="th-topbar d-flex justify-content-center flex-column">
      <div class="position-relative container th-container p-0 th-container-md">
        <div class="d-flex align-items-center justify-content-center">
          <p class="font-white th-topbar-text" data-v-header-topbar_message=""><?php  echo isset($header['topbar_message']) ? $header['topbar_message'] : '';  ?></p>
        </div>
        <div class="contact-sales d-flex align-items-center justify-content-center">
          <i class="fal fa-envelope font-white" style="font-size: 18px;"></i>
          <a href="<?php  echo isset($header['topbar_link']) ? $header['topbar_link'] : '';  ?>" target="_blank" class="font-white th-topbar-link" data-v-header-topbar-link=""data-v-header-topbar-link-text=""><?php  echo isset($header['topbar_link_text']) ? $header['topbar_link_text'] : '';  ?></a>
        </div>
      </div>
    </div>

    <div class="th-menu-wrapper mobile-menu" role="navigation">
      <div class="th-menu-area text-center">
        <button class="th-menu-toggle">
          <i class="fal fa-times"></i>
        </button>
        <div class="mobile-logo">
          <a class="icon-masking" href="/">
            <img src="/img/logo-white.png" alt="KROST">
          </a>
        </div>

        <div class="th-mobile-menu">
          <ul class="mobile-menu-list" data-v-header-mobile-menu-list=""><?php  if(isset($header['mobile_menu'])){ foreach ($header['mobile_menu'] as $menu) {  ?>
            <li class="<?php  echo ($menu["class"]?? '').' mobile-menu-item';  ?>" data-v-header-mobile-menu-item="">
              <a href="<?php  echo ($menu["href"]?? '');  ?>" data-v-header-mobile-menu-item-href="">
                <span data-v-header-mobile-menu-item-title=""><?php  echo isset($menu["title"]) ? $menu["title"] : '';  ?></span>
              </a>
              <?php if (isset($menu['has_children']) && $menu['has_children']) { ?><ul class="sub-menu" data-v-header-mobilemenu-children=""><?php  if(isset($menu['children'])){ foreach ($menu['children'] as $key => $child) {  ?>
                <li>
                  <a href="<?php  echo ($child["href"]?? '');  ?>" data-v-header-mobilemenu-childitem=""><?php  echo $child["title"] ?? '';  ?></a>
                </li>
              <?php  }};  ?></ul><?php } ?>
            </li>





          <?php  }};  ?></ul>
        </div>
      </div>
    </div>

    <div class="sticky-wrapper desktop-menu" role="navigation">

      <div class="menu-area">
        <div class="container th-container p-0 th-container-md">
          <div class="row align-items-center justify-content-between ps-50 pe-50">
            <div class="d-flex flex-6-md col-4 align-items-center justify-content-start th-nav-section-px">
              <div class="main-menu d-none d-lg-inline-block">
                <ul class="desktop-menu-list" data-v-header-desktop-menu-list=""><?php  if(isset($header['desktop_menu'])){ foreach ($header['desktop_menu'] as $menu) {  ?>
                  <li class="<?php  echo isset($menu["class"]) ? $menu["class"] : '';  ?>" data-v-header-desktop-menu-item="">
                    <a href="<?php  echo isset($menu["href"]) ? $menu["href"] : '';  ?>" class="th-nav-link th-nav-link-py">
                      <span data-v-header-desktop-menu-item-title=""><?php  echo isset($menu["title"]) ? $menu["title"] : '';  ?></span>
                    </a>
                    <?php if (isset($menu['mega_menu']) && $menu['mega_menu']) { ?><ul class="mega-menu py-5" data-v-header-mega-menu="">

                      <div class="container">
                        <div class="row">
                          <div class="col-9" data-v-header-mega-menu-rows="" style="flex: 5"><?php  if(isset($menu['rows'])){ foreach ($menu['rows'] as $key => $row) {  ?>

                            <div class="<?php  if($key == 0){ echo 'row mega-menu-row category-list'; }else{ echo 'row mega-menu-row mt-50 category-list'; }  ?>" data-v-header-mega-menu-row=""><?php  if(isset($row)){ foreach ($row as $item) {  ?>
                              <div class="col mega-menu-item category-item" data-v-header-mega-menu-item="" style="<?php  echo isset($item['style']) ? $item['style'] : '';  ?>">
                                <a href="<?php  echo isset($item['href']) ? $item['href'] : '';  ?>" class="category-link mega-menu-item-link" data-v-header-mega-submenu-item-link="">
                                  <h5 class="hover" data-v-header-mega-menu-item-title=""><?php  echo isset($item['title']) ? $item['title'] : '';  ?></h5>
                                </a>
                                <nobr>
                                  <span class="category-links mega-menu-item-links" data-v-header-mega-menu-item-links=""><?php  if(isset($item['links'])){ foreach ($item['links'] as $link) {  ?>
                                    <a href="<?php  echo isset($link['href']) ? $link['href'] : '';  ?>" class="category-link mega-menu-item-link" data-v-header-mega-menu-item-link="">
                                      <span class="hover" data-v-header-categorylink-title=""><?php  echo isset($link['label_name']) ? $link['label_name'] : '';  ?></span>
                                    </a>

                                  <?php  }}  ?></span>
                                </nobr>
                              </div>

                            <?php  }}  ?></div>




                          <?php  }};  ?></div>


                        </div>
                        <div class="row">
                          <div class="col">
                            <div class="row">
                              <div class="col">
                                <a class="font-weight-700 hover" href="/categories"><?php echo App\Core\System\utils\__('View All Categories'); ?></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </ul><?php } ?>
                  </li>



                <?php  }};  ?></ul>
              </div>
              <button type="button" class="th-menu-toggle d-block d-lg-none">
                <i class="far fa-bars"></i>
              </button>
            </div>
            <div class="d-flex flex-2-md col-4 align-items-center justify-content-center">
              <div class="header-logo">
                <a class="icon-masking" href="/">
                  <img src="/img/logo_black.png" alt="KROST">
                </a>
              </div>

            </div>
            <div class="d-flex flex-6-md col-4 align-items-center justify-content-end th-nav-section-px">
              <div class=" d-none d-lg-inline-block">
                <a class="th-nav-link th-nav-link" href="/account/resources">
                  <span class="manu-name hover pr-20 pr-20-ml th-nav-link-right-pr"><?php echo App\Core\System\utils\__('Resources'); ?></span>
                </a>
                <a class="th-nav-link th-nav-link" href="/contact-us">
                  <span class="manu-name hover pr-20 pr-20-ml th-nav-link-right-pr"><?php echo App\Core\System\utils\__('Contact Us'); ?></span>
                </a>
              </div>
              <div class="header-button  d-none d-lg-inline-block">
                <a href="https://store.krost.com.au" class="h-btn d-flex align-items-center justify-content-center th-nav-link">
                  <span><?php echo App\Core\System\utils\__('Shop Now'); ?></span>
                </a>
              </div>

              <div class="d-flex align-items-end gap-3 gap-3-ml" style="margin-bottom: 1px;">


                <div class="th-header-search" id="th-header-global-search">
                  <a class="d-flex th-nav-link" href="" title="Search" role="button" data-bs-toggle="offcanvas" data-bs-target="#search_offcanvasRight" aria-controls="search_offcanvasRight">
                    <i class="fa-regular fa-magnifying-glass" style="font-size: 26px;"></i>
                  </a>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="search_offcanvasRight" aria-labelledby="search_offcanvasRightLabelLabel">

                  <div class="offcanvas-body d-none d-lg-block" id="searchbar-app">
                  </div>


                  <div class="offcanvas-wrapper mobile-searchbar d-lg-none">
                    <div class="offcanvas-header header-upper d-flex align-items-center justify-content-between">
                      <h3 id="search_offcanvasRightLabelLabel"><?php echo App\Core\System\utils\__('Search'); ?></h3>
                      <i class="fa-solid fa-xmark" data-bs-dismiss="offcanvas" aria-label="Close"></i>
                    </div>
                    <div class="offcanvas-body" id="searchbar-app-mobile">
                    </div>
                  </div>
                </div>



                <style>
 <?php echo App\Core\System\utils\__('.th-mouse-hover {
 &:hover {
 color: #0a0a14 !important;
 cursor: pointer;
 font-weight: 600;
 }
 }'); ?>
 </style>

                <a href="" id="pinboard-button th-nav-link" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" style="margin-right: 1px; width: 26px;">
                  <span class="" style="position: relative;">
                    <img src="/img/pinboard-icon.png" alt="Pinboard" style="width: 26px; height: 27px;">
                    <div class="th-pinboard-count-badge" style="display: none;">
                      <span id="pinboard-item-count"></span>
                    </div>
                  </span>
                </a>

                <div class="dropdown th-user-dropdown" id="user-profile-info">
                  <a href="#" class="d-flex align-items-center dropdown-toggle th-nav-link" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" title="Accountmenu">
                    <i class="fa-solid fa-user" style="font-size: 26px;"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" data-v-header-navigation-menus=""><?php  if(isset($header['account_menu'])){ foreach ($header['account_menu'] as $menu) {  ?>
                    <li data-v-header-navigation-menu-item="">
                      <a class="dropdown-item" href="<?php  echo isset($menu["href"]) ? $menu["href"] : '';  ?>" id="<?php  echo isset($menu["id"]) ? $menu["id"] : '';  ?>"><?php  echo isset($menu["title"]) ? $menu["title"] : '';  ?></a>
                    </li>



                  <?php  }};  ?></ul>

                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php  $is_logged_in = !empty($this->parameters['is_logged_in']); $is_logged_in_js = $is_logged_in ? 'true' : 'false';  ?><script type="text/javascript" id="is-logged-in"  ><?php  echo "window.__AUTH_PRESENT__ = " . $is_logged_in_js . ";";  ?></script>



  <?php  $heroproduct = $current_component = $this->_component['heroproduct']?? []; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;$bannerRaw = $heroproduct['banner_image'] ?? []; $banner = isset($bannerRaw[0]['objectURL']) ? $bannerRaw[0]['objectURL'] : (is_string($bannerRaw) ? $bannerRaw : null); $productImageRaw = $heroproduct['image'] ?? []; if (is_string($productImageRaw)) { $productImageRaw = json_decode($productImageRaw, true); } $productImage = isset($productImageRaw[0]['objectURL']) ? $productImageRaw[0]['objectURL'] : null; $bannerVideos = $heroproduct['banner_videos'] ?? []; $showVideoBanner = !empty($bannerVideos) && $bannerVideos != "[]"; $showBannerImage = !$showVideoBanner && !!$banner; $showProductImage = !$showVideoBanner && !$showBannerImage && !!$productImage; $bannerClass = $showBannerImage ? '' : 'th-no-hero-image'; $showHeroSection = $showVideoBanner || $showBannerImage || $showProductImage; $storeLinkTrimmed = isset($heroproduct['store_link']) && $heroproduct['store_link']!== null ? trim((string) $heroproduct['store_link']) : ''; $productAvailableOnStore = $storeLinkTrimmed !== ''; $buttonContactSales = !$productAvailableOnStore; $buttonOrderOnline = $productAvailableOnStore; $catalogueLinkTrimmed = isset($heroproduct['catalogue_link']) ? trim((string) $heroproduct['catalogue_link']) : ''; $buttonViewCatalogue = $catalogueLinkTrimmed !== ''; $breadcrumbs = $heroproduct['breadcrumbs'] ?? []; $waypoints = $heroproduct['way_points'] ?? [];  ?><?php if (isset($showHeroSection) && $showHeroSection) { ?><section data-v-component-heroproduct="" data-v-heroproduct-hero_image="" class="<?php  echo ($showProductImage?'th-hero-wrapper th-hero th-hero-primary-color th-breadcrumb-wrapper th-hero-product-centered th-way-points ' : 'th-hero-wrapper th-hero th-hero-transparent gr-bg4 th-breadcrumb-wrapper th-hero-product-centered th-way-points') ;  ?>" id="hero-product" data-bg-src="<?php  echo ($showBannerImage ? str_replace(' ', '%20', $banner??"") : '');  ?>" data-class="<?php  echo $bannerClass??"";  ?>" data-bg="<?php echo ($showBannerImage ? ($banner??"") : '');  ?>">

    <?php if (isset($showVideoBanner) && $showVideoBanner) { ?><div class="th-hero-video-banner" data-v-heroproduct-video-collage="" data-videos="<?php  echo $showVideoBanner && !empty($bannerVideos) ? htmlspecialchars(json_encode($bannerVideos), ENT_QUOTES, 'UTF-8') : '[]';  ?>">
      <video class="th-hero-video-layer th-hero-video-active" data-v-heroproduct-video-banner-player="" autoplay="" muted="" playsinline="" preload="auto"></video>
      <video class="th-hero-video-layer" data-v-heroproduct-video-banner-player="" muted="" playsinline="" preload="auto"></video>
      <div class="th-hero-video-overlay"></div>
    </div><?php } ?>

    <div class="container th-container">
      <div class="row">
        <div class="col-md-12">
          <div class="th-breadcrumb mt-0 d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb ">
              <ol class="breadcrumb font-white" data-v-product-breadcrumbs=""><?php  if($breadcrumbs){ foreach ($breadcrumbs as $breadcrumb){  ?>

                <li class="breadcrumb-item font-white" data-v-product-breadcrumb-list="">
                  <a href="<?php  echo isset($breadcrumb['link']) ? $breadcrumb['link'] : '';  ?>" class="font-white" data-v-product-breadcrumb-link=""><?php  echo isset($breadcrumb['name']) ? $breadcrumb['name'] : '';  ?></a>
                </li>








              <?php  }}  ?></ol>
            </nav>
            <div class="th-admin-links d-flex" style="gap: 10px;">
              <?php if (isset($is_admin) && $is_admin) { ?><div class="text-white th-component-edit-link" data-v-product-edit-link="" style="z-index: 10; cursor: pointer;">
                <i class="fa-solid fa-pencil pr-5" style="font-size: .8rem"></i>
                <a href="<?php  echo isset($heroproduct['edit_link']) ? $heroproduct['edit_link'] : '';  ?>" target="_blank" class="text-white"><?php echo App\Core\System\utils\__('Edit Product'); ?></a>
              </div><?php } ?>

              <?php if (isset($is_admin ) && $is_admin ) { ?><?php if (isset($is_admin) && $is_admin) { ?><div class="text-white th-component-edit-link" data-v-component-link="" style="z-index: 10; cursor: pointer;">
                <i class="fa-solid fa-pencil pr-5" style="font-size: .8rem"></i>
                <a href="<?php  echo isset($heroproduct['component_link']) ? $heroproduct['component_link'] : '';  ?>" target="_blank" class="text-white"><?php echo App\Core\System\utils\__('Component Edit'); ?></a>
              </div><?php } ?><?php } ?>

              <div class="position-absolute top-right-20 top-50">
                <div data-v-heroproduct-add-to-pinboard="" data-id="<?php  echo isset($heroproduct['product_id']) ? $heroproduct['product_id'] : '';  ?>" data-model="product" data-title="<?php  echo isset($heroproduct['title']) ? $heroproduct['title'] : '';  ?>" data-description="<?php  echo isset($heroproduct['tag_line']) ? $heroproduct['tag_line']: '';  ?>" data-image="<?php  echo isset($productImage) ? $productImage : '';  ?>" class="th-add-to-pinboard position-absolute top-right-30" data-product-url="<?php  echo isset($heroproduct['product_url']) ? $heroproduct['product_url'] : '';  ?>">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-6">
          <div class="<?php  echo ($showProductImage?"th-hero-container th-hero-primary-color":"th-hero-container gr-bg4");  ?>" data-v-heroproduct-banner-style="">

            <div class="way-points-container" data-v-heroproduct-waypoints=""><?php  if($waypoints){ foreach ($waypoints as $item){  ?>
              <div id="<?php  echo isset($item["id"]) ? 'way-point-'. $item["id"] : '';  ?>" class="way-point" style="<?php  echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : '';  ?>" data-v-heroproduct-waypoint="">
                <div class="circle-point" style="position: absolute;"></div>
                <div class="circle-point" style="position: absolute;"></div>

                <span class="way-point-link">
                  <a href="<?php  echo isset($item["href"]) ? trim($item["href"]) : '';  ?>" target="_blank" data-v-heroproduct-waypoint-link="" id="<?php  echo isset($item["id"]) ? $item["id"] : '';  ?>"><?php  echo isset($item["label"]) ? $item["label"] : '';  ?></a>
                </span>
              </div>
            <?php  }}  ?></div>

            <div class="th-hero-style th-mt-100">
              <h1 class="th-hero-title th-hero-title-mb" data-v-heroproduct-hero_title="" style="<?php  echo isset($heroproduct['title']) && $heroproduct['title'][0] == 'J'? "text-indent: 5px;": "text-indent: 0px;";  ?>"><?php  echo $heroproduct['title']??$heroproduct['name'];  ?></h1>
              <span class="th-hero-subtitle th-hero-subtitle-my" data-v-heroproduct-hero_description=""><?php  echo $heroproduct['tag_line']??$heroproduct['description'];  ?></span>

              <div class="th-hero-wrapp" data-v-product-btn-group=""><?php  if(isset($heroproduct['buttons'])){ foreach ($heroproduct['buttons'] as $key => $button) { if($productAvailableOnStore && stripos($button['title'], 'Order') !== false) $link = $storeLinkTrimmed; else if ($buttonViewCatalogue && stripos($button['title'], 'Catalogue') !== false) $link = $catalogueLinkTrimmed; else $link = $button['url']; $k = count($heroproduct['buttons']) - 1; if($showProductImage){ $class = $key == $k ? 'th-btn th-btn-outline-black text-capitalize' : 'th-btn th-btn-primary text-capitalize'; }else{ $class = $key == $k ? 'th-btn-outline text-capitalize th-btn-text' :'th-btn-gray text-capitalize th-btn-text'; } if($productAvailableOnStore && stripos($button['title'], 'Book') !== false) continue; else if(!$productAvailableOnStore && stripos($button['title'], 'Order') !== false) continue; else if (!$buttonViewCatalogue && stripos($button['title'], 'Catalogue') !== false) continue;  ?>
                <div class="position-relative th-pb-16" data-v-product-btn="">
                  <a href="<?php  echo $link;  ?>" class="<?php  echo $class;  ?>" data-v-product-btn-link="">
                    <span class="th-mr-5"><?php  echo $button['title'];  ?></span>
                  </a>
                </div>
              <?php  }}  ?></div>

            </div>

          </div>
        </div>
        <div class="col-xl-6 th-hero-media-col">
          <div class="th-hero-img">
            <?php if (isset($showProductImage) && $showProductImage) { ?><img data-v-heroproduct-hero-img-src="" src="<?php  echo $productImage;  ?>" alt="Product Image"><?php} ?>
          </div>
        </div>
      </div>
    </div>
  </section><?php } ?>





  <style>
    /* .th-hero-title {
    display: inline-block;
    padding-right: 5px;
    text-indent: -5px;
    padding-left: 5px;
} */

    .th-hero-title {
      max-width: 100%;
      line-height: 1.2;
      word-break: break-word;
      padding-right: 5px;
      color: #ad2121;
    }

    /* Full-screen video banner - like banner image */
    .th-hero-video-banner {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }

    .th-hero-video-banner video.th-hero-video-layer {
      position: absolute;
      top: 50%;
      left: 50%;
      min-width: 100%;
      min-height: 100%;
      width: auto;
      height: auto;
      transform: translate(-50%, -50%);
      object-fit: cover;
      opacity: 0;
      z-index: 0;
      transition: none;
    }

    .th-hero-video-banner video.th-hero-video-layer.th-hero-video-active {
      opacity: 1;
      z-index: 1;
    }

    .th-hero-video-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.35);
      z-index: 1;
      pointer-events: none;
    }

    /* .th-hero-content-wrapper {
    position: relative;
    z-index: 10;
  } */
    /* Hide product image when video banner is present */
    section:has(.th-hero-video-banner) .th-hero-media-col .th-hero-img {
      display: none;
    }

    /* Video banner: full-height hero section */
    section:has(.th-hero-video-banner) {
      min-height: 70vh;
      position: relative;
    }
  </style>

  <script  >
  document.querySelectorAll('[data-bg-src]').forEach(function(el) {
    var src = el.getAttribute('data-bg-src');
    el.style.backgroundImage = 'url(' + src + ')';
    el.classList.add('background-image');
  });
  /* Sequential banner videos: dual-buffer + preload for gapless playback */
  (function() {
    var banner = document.querySelector('[data-v-heroproduct-video-collage]');
    if (!banner) return;
    var raw = banner.getAttribute('data-videos');
    if (!raw) return;
    var players = banner.querySelectorAll('video[data-v-heroproduct-video-banner-player]');
    if (players.length < 2) return;
    var urls;
    try {
      urls = JSON.parse(raw);
    } catch (e) {
      return;
    }
    if (!urls || !urls.length) return;

    var active = 0;
    var currentIdx = 0;

    function resolveUrl(path) {
      if (!path) return path;
      if (/^https?:\/\//i.test(path)) return path;
      return (path.charAt(0) === '/' ? '' : '/') + path;
    }

    function nextIdx(i) {
      return (i + 1) % urls.length;
    }

    function setActive(which) {
      for (var i = 0; i < players.length; i++) {
        players[i].classList.toggle('th-hero-video-active', i === which);
      }
      active = which;
    }

    function preload(player, url) {
      url = resolveUrl(url);
      if (player._loadedUrl === url && player.readyState >= 3) {
        return Promise.resolve(player);
      }
      return new Promise(function(resolve) {
        function done() {
          player.removeEventListener('canplaythrough', done);
          player.removeEventListener('loadeddata', done);
          player.removeEventListener('error', onError);
          player._loadedUrl = url;
          resolve(player);
        }
        function onError() {
          player.removeEventListener('canplaythrough', done);
          player.removeEventListener('loadeddata', done);
          player.removeEventListener('error', onError);
          resolve(player);
        }
        player.addEventListener('canplaythrough', done, { once: true });
        player.addEventListener('loadeddata', done, { once: true });
        player.addEventListener('error', onError, { once: true });
        player.src = url;
        player.load();
      });
    }

    function warmCache() {
      urls.forEach(function(u) {
        var href = resolveUrl(u);
        if (document.querySelector('link[rel="preload"][href="' + href + '"]')) return;
        var link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'video';
        link.href = href;
        document.head.appendChild(link);
      });
    }

    function queueAhead() {
      if (urls.length < 2) return;
      var ahead = players[1 - active];
      preload(ahead, urls[nextIdx(currentIdx)]);
    }

    function playCurrent() {
      var player = players[active];
      player.currentTime = 0;
      setActive(active);
      var p = player.play();
      if (p && p.catch) p.catch(function() {});
      queueAhead();
    }

    function switchToNext() {
      if (urls.length === 1) {
        players[active].currentTime = 0;
        players[active].play().catch(function() {});
        return;
      }
      var nextIndex = nextIdx(currentIdx);
      var nextPlayer = players[1 - active];

      function startNext() {
        var oldActive = active;
        currentIdx = nextIndex;
        nextPlayer.currentTime = 0;
        setActive(1 - oldActive);
        var p = nextPlayer.play();
        if (p && p.catch) p.catch(function() {});
        players[oldActive].pause();
        queueAhead();
      }

      if (nextPlayer._loadedUrl === resolveUrl(urls[nextIndex]) && nextPlayer.readyState >= 2) {
        startNext();
      } else {
        preload(nextPlayer, urls[nextIndex]).then(startNext);
      }
    }

    players.forEach(function(player) {
      player.muted = true;
      player.setAttribute('muted', '');
      player.setAttribute('playsinline', '');
      player.addEventListener('ended', function() {
        if (players[active] !== player) return;
        switchToNext();
      });
      player.addEventListener('timeupdate', function() {
        if (players[active] !== player || urls.length < 2) return;
        if (!player.duration || player.duration - player.currentTime > 1.5) return;
        queueAhead();
      });
    });

    warmCache();

    if (urls.length === 1) {
      preload(players[0], urls[0]).then(playCurrent);
      return;
    }

    Promise.all([
      preload(players[0], urls[0]),
      preload(players[1], urls[1])
    ]).then(playCurrent);
  })();
</script>


  <?php  $productstorymasonry = $current_component = $this->_component['productstorymasonry']?? []; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  ?><section id="product-story-masonry" class="position-relative th-section-py" data-v-component-productstorymasonry="">
    <?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end th-pr-40 align-items-center position-absolute" style="right: 40px" data-v-productstorymasonry-component-link="">
      <i class="fa-solid fa-pencil th-pr-5" style="font-size: .8rem"></i>
      <a href="<?php  echo isset($productstorymasonry['component_link']) ? $productstorymasonry['component_link'] : '';  ?>" target="_blank" class=""><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?>
    <div class="container th-container">
      <div class="row featured-product">
        <div class="col-12">
          <div class="section-body th-masonry-col-2">
            <div class="th-masonry-grid th-column-gap-@@column-gap"><?php  if(isset($productstorymasonry['items']) && is_array($productstorymasonry['items'])) { foreach ($productstorymasonry['items'] as $item) {  ?>

              <div data-v-productstorymasonry-item="" class="<?php  echo isset($item['class']) ? $item['class'] : '';  ?>" style="transform: translateY(0 px);padding-top:0 px">
                <div class="th-item-img">
                  <img src="<?php  echo isset($item['img']) ? $item['img'] : '';  ?>" alt="<?php  echo isset($item['heading']) ? htmlspecialchars($item['heading']) : '';  ?>" data-v-productstorymasonry-img="">
                </div>
                <div class="th-product-info-wrapper">
                  <div class="th-item-info th-mt-15">
                    <h6 class="th-title-18 font-weight-700" data-v-productstorymasonry-heading=""><?php  echo isset($item['heading']) ? $item['heading'] : '';  ?></h6>





                    <p class="item-description th-pt-15" data-v-productstorymasonry-des=""><?php  echo isset($item['des']) ? $item['des'] : '';  ?></p>
                    <div class="th-link th-pt-10">


                    </div>

                  </div>
                </div>
              </div>



            <?php  } }  ?></div>
          </div>
        </div>
      </div>
    </div>

  </section>


  <?php  $productfeature = $current_component = $this->_component['productfeature']?? []; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  ?><section id="th-product-features" class="gr-bg8 position-relative th-section-py" data-v-component-productfeature="">
    <?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end th-pr-40 align-items-center position-absolute" style="right: 40px" data-v-productfeature-component-link="">
      <i class="fa-solid fa-pencil th-pr-5" style="font-size: .8rem"></i>
      <a href="<?php  echo isset($productfeature['component_link']) ? $productfeature['component_link'] : '';  ?>" target="_blank" class=""><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?>
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header th-section-header-pb-50">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="th-section-title th-section-title-mb" data-v-productfeature-sectiontitle=""> <?php echo App\Core\System\utils\__('Features'); ?> </h2>
            <div class="th-section-subtitle th-section-subtitle-mb" data-v-productfeature-section-subtitle=""><?php  echo isset($productfeature['sectionSubtitle']) ? $productfeature['sectionSubtitle'] : '';  ?></div>
          </div>

          <?php if (isset($buttonOrderOnline) && $buttonOrderOnline) { ?><div class="right" data-v-productfeature-section="">
            <a href="<?php  echo isset($storeLinkTrimmed) ? $storeLinkTrimmed : 'll';  ?>" target="_blank" id="online-order-link" data-v-productfeature-sectionlinktext-link="">
              <div class="th-section-header-link">
                <span class="th-section-header-link-text th-section-link Order OnlineClass th-mr-7" data-v-productfeature-sectionlinktext="">
 <?php echo App\Core\System\utils\__('Order Online'); ?>
 </span>
                <span class="th-section-header-link-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </span>
              </div>
            </a>
          </div><?php } ?>
        </div>

        <div class="section-body">
          <div class="row" data-v-productfeature-items=""><?php  if(isset($productfeature['items']) && is_array($productfeature['items'])) { foreach ($productfeature['items'] as $item) {  ?>
            <div class="col-md-4 col-sm-12 mb-15" data-v-productfeature-item="">
              <div class="th-item-card">
                <div class="th-img-container">
                  <img src="<?php  echo isset($item['img']) ? $item['img'] : '';  ?>" alt="<?php  echo isset($item['title']) ? htmlspecialchars($item['title']) : '';  ?>" data-v-productfeature-img="">
                </div>
                <div class="th-item-card-content th-mt-15">
                  <div class="mb-10">
                    <h6 class="th-link-text th-pr-5 th-title-18 font-weight-700" data-v-productfeature-title=""><?php  echo isset($item['title']) ? $item['title'] : '';  ?></h6>

                  </div>
                  <div class="th-description" data-v-productfeature-description=""><?php  echo isset($item['description']) ? $item['description'] : '';  ?></div>
                </div>
              </div>
            </div>



          <?php  } }  ?></div>
        </div>
      </div>
    </div>
  </section>

  <?php  $productconfigurator = $current_component = $this->_component['productconfigurator']?? []; $showConfigurator = isset($productconfigurator['show_configurator']) && $productconfigurator['show_configurator'] == 1 ? true : false;  ?><?php if (isset($showConfigurator) && $showConfigurator) { ?><section id="th-product-configurator" class="th-product-configurator th-section-py" data-v-component-productconfigurator="">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header th-section-header-pb-50 @@sectionHeaderClass" style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h3 class=" th-section-title th-section-title-mb" data-v-productconfigurator-section-title=""> <?php echo App\Core\System\utils\__('Product Configurator'); ?> </h3>
            <div class="th-section-subtitle th-section-subtitle-mb" data-v-productconfigurator-section-subtitle_n="">
 <?php echo App\Core\System\utils\__('Tailor this product to your project. Select your preferred finishes and configurations to suit your space.'); ?>
 </div>
          </div>
        </div>
        <div class="section-body" id="th-product-configurator-app"></div>
      </div>
    </div>
  </section><?php } ?>

  <script data-v-productconfigurator-script=""  ><?php  echo "window.configuration = " . json_encode($productconfigurator['variants']??[]) . ";";  ?><?php  echo "window.modelData = " . json_encode($productconfigurator['modelData']??[]) . ";";  ?><?php  echo "window.accessories = " . json_encode($productconfigurator['accessories']??[]) . ";";  ?><?php  echo "window.product = " . json_encode(['product_id' => $productconfigurator['product_id'], 'product_code' => $productconfigurator['product_code'], 'description' => $productconfigurator['description'], 'image' => isset($productconfigurator['image']) ? $productconfigurator['image'] : '']) . ";";  ?>
  // Make sure it's on window
  // if (typeof Sortable !== 'undefined') {
  //     window.Sortable = window.Sortable || Sortable;
  // }
  // // Also try to make it available for CommonJS/UMD module systems
  // if (typeof module !== 'undefined' && module.exports && typeof window.Sortable !== 'undefined') {
  // module.exports.Sortable = window.Sortable;
  // }
</script>

  <?php  $specs = $current_component = $this->_component['productspecifications']?? []; $specificationImage = isset($specs['img']) ? $specs['img'] : $productImage; $showSpecifications = isset($specs['specifications']) && count($specs['specifications']) > 0 ? 1 : 0; $dimensions = $current_component = $this->_component['productdimenssions']?? []; $showDimensionsImage = isset($dimensions['dimensions_image']) && !empty($dimensions['dimensions_image']) ? true : false; $showDimensions = (isset($dimensions) && ($dimensions['display_width']??$dimensions['display_height']??$dimensions['display_depth']??false)) || $showDimensionsImage ? true : false; $downloads = $current_component = $this->_component['productdownloads']?? []; $showDownloads = isset($downloads) && count($downloads) > 0 ? true : false; $certifications = $current_component = $this->_component['productcertifications']?? []; $showProductCertifications = isset($certifications) && count($certifications) > 0 ? true : false; $media = $current_component = $this->_component['productmedia']?? []; $showProductMedia = isset($media['items']) && count($media['items']) > 0 ? true : false; $class = "nav-link th-tab-title th-tabs-title-p"; $activeClass = "active nav-link th-tab-title th-tabs-title-p"; $isSpecTabActive = $showSpecifications == 1 ? true : false; $isDimensionsTabActive = $showDimensions && !$showSpecifications == 1 ? true : false; $isDownloadsTabActive = $showDownloads && !$showSpecifications && !$showDimensions ? true : false; $isCertificationsTabActive = $showProductCertifications && !$showSpecifications && !$showDimensions && !$showDownloads ? true : false; $isMediaTabActive = $showProductMedia && !$showProductCertifications && !$showSpecifications && !$showDimensions && !$showDownloads ? true : false;  ?><section class="th-product-detail-tabs th-product-tabs-section-p" id="product-detail-tabs">
    <div class="container th-container">

      <div class="nav nav-tabs mb-4 d-none d-md-flex border-0" id="product-tabs" role="tablist">

        <?php if (isset($showSpecifications) && $showSpecifications) { ?><button class="<?php  echo $isSpecTabActive ? $activeClass : $class;  ?>" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="true" data-v-productspecifications-tab-button=""><?php echo App\Core\System\utils\__('Specs'); ?></button><?php } ?>

        <button class="<?php  echo !$showSpecifications && $showDimensions ? $activeClass : $class . ($showDimensions == 0 ? ' d-none' : '');  ?>" id="dimensions-tab" data-bs-toggle="tab" data-bs-target="#dimensions" type="button" role="tab" aria-controls="dimensions" aria-selected="false" data-v-productdimensions-tab-button=""><?php echo App\Core\System\utils\__('Dimensions'); ?></button>

        <?php if (isset($showDownloads) && $showDownloads) { ?><button class="<?php  echo $isDownloadsTabActive ? $activeClass : $class;  ?>" id="downloads-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="false" data-v-productdownloads-tab-button=""><?php echo App\Core\System\utils\__('Downloads'); ?></button><?php } ?>

        <?php if (isset($showProductCertifications) && $showProductCertifications) { ?><button class="<?php  echo $isCertificationsTabActive ? $activeClass : $class;  ?>" id="certifications-tab" data-bs-toggle="tab" data-bs-target="#certifications" type="button" role="tab" aria-controls="certifications" aria-selected="false" data-v-productcertifications-tab-button=""><?php echo App\Core\System\utils\__('Certifications'); ?></button><?php } ?>

        <?php if (isset($showProductMedia) && $showProductMedia) { ?><button class="<?php  echo $isMediaTabActive ? $activeClass : $class;  ?>" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false" data-v-productmedia-tab-button=""><?php echo App\Core\System\utils\__('Pictures &
 Videos'); ?></button><?php } ?>

      </div>


      <div class="accordion d-md-none mb-4" id="accordionProductDetails">


        <?php if (isset($showSpecifications) && $showSpecifications) { ?><div class="accordion-item border-0 mb-2" data-v-component-productspecifications-mobile="">
          <h2 class="accordion-header" id="headingSpecs">
            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs" aria-expanded="false" aria-controls="collapseSpecs">
 <?php echo App\Core\System\utils\__('Specs'); ?>
 </button>
          </h2>
          <div id="collapseSpecs" class="accordion-collapse collapse" aria-labelledby="headingSpecs" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">

              <div class="row">
                <div class="col-lg-6 px-0">
                  <ul class="" data-v-productspecifications-items-mobile=""><?php  if(isset($specs['specifications'])) { foreach ($specs['specifications'] as $item) {  ?>
                    <li class="font-size-14 pb-10" data-v-productspecifications-item-mobile=""><?php  echo $item ?? '';  ?></li>





                  <?php  } }  ?></ul>
                  <div class="th-link @@classPadding">
                    <div class="th-link-text pr-5">
                      <?php if (isset($buttonViewCatalogue) && $buttonViewCatalogue) { ?><a href="<?php  echo $heroproduct['catalogue_link'] ?? '#';  ?>" data-v-productspecifications-link-mobile="" target="<?php  echo '_blank';  ?>"><?php  echo 'View in Catalogue';  ?></a><?php } ?>
                    </div>
                  </div>

                </div>

              </div>
            </div>
          </div>
        </div><?php } ?>


        <div class="<?php  echo $isDimensionsTabActive ? 'tab-pane fade show active' : 'tab-pane fade d-none';  ?>" id="dimensionsMobile" data-v-component-productdimenssions-mobile="">
          <h2 class="accordion-header" id="headingDimensions">
            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDimensions" aria-expanded="false" aria-controls="collapseDimensions">
 <?php echo App\Core\System\utils\__('Dimensions'); ?>
 </button>
          </h2>
          <div id="collapseDimensions" class="accordion-collapse collapse" aria-labelledby="headingDimensions" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">

              <div class="row">
                <div class="col-lg-6 px-0">
                  <ul class="">
                    <li class="mb-10"><?php echo App\Core\System\utils\__('Width:'); ?> <span id="width-dimension">100mm</span>
                    </li>
                    <li class="mb-10"><?php echo App\Core\System\utils\__('Height:'); ?> <span id="height-dimension">100mm</span>
                    </li>
                    <li class="mb-10"><?php echo App\Core\System\utils\__('Depth:'); ?> <span id="depth-dimension">100mm</span>
                    </li>
                  </ul>
                  <div class="th-link @@classPadding">
                    <div class="th-link-text pr-5">
                      <a href="<?php  echo $heroproduct['catalogue_link'] ?? '#';  ?>" data-v-productdimensions-link-mobile="" target="_blank">
                        <span data-v-productdimensions-link-text-mobile=""><?php echo App\Core\System\utils\__('View in Catalogue'); ?></span>
                      </a>
                    </div>
                  </div>

                </div>

              </div>
            </div>
          </div>
        </div>


        <?php if (isset($showDownloads) && $showDownloads) { ?><div class="accordion-item border-0 mb-2" data-v-component-productdownloads-mobile="">
          <h2 class="accordion-header" id="headingDownloads">
            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDownloads" aria-expanded="false" aria-controls="collapseDownloads">
 <?php echo App\Core\System\utils\__('Downloads'); ?>
 </button>
          </h2>
          <div id="collapseDownloads" class="accordion-collapse collapse" aria-labelledby="headingDownloads" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">

              <div class="row">
                <div class="col-lg-6 px-0">
                  <ul class="list-unstyled" data-v-downloads-items-mobile="">
                    <li data-v-downloads-item-mobile=""><?php  if(isset($downloads)) { foreach ($downloads as $item) { ?>
                      <div class="download-item-container d-flex text-center align-items-center justify-content-start gap-3 mb-10">
                        <img src="<?php  echo $item['objectURL'] ?? $specificationImage;  ?>" alt="Download Icon" data-v-downloads-item-icon-src-mobile="" style="width: 50px; height: 50px;">
                        <a href="<?php  echo $item['url'] ?? '#';  ?>" download="" data-v-downloads-item-name-mobile="" data-v-downloads-item-link-mobile="" class="design-resource-tag block mb-1 text-blue-600 hover:text-blue-800 download-item-link"><?php  echo $item['name'] ?? '';  ?></a>
                      </div>
                    <?php  }} ?></li>
                  </ul>
                  <div class="th-link @@classPadding">
                    <?php if (isset($showDownloads) && $showDownloads) { ?><div class="th-link-text pr-5" data-v-productdownloads-link-mobile="">
 <?php echo App\Core\System\utils\__('Download All'); ?>
 </div><?php } ?>
                  </div>

                </div>

              </div>
            </div>
          </div>
        </div><?php } ?>


        <?php if (isset($showProductCertifications) && $showProductCertifications) { ?><div class="accordion-item border-0 mb-2" data-v-component-productcertifications-mobile="">
          <h2 class="accordion-header" id="headingCertifications">
            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCertifications" aria-expanded="false" aria-controls="collapseCertifications">
 <?php echo App\Core\System\utils\__('Certifications'); ?>
 </button>
          </h2>
          <div id="collapseCertifications" class="accordion-collapse collapse" aria-labelledby="headingCertifications" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">

              <div class="row">
                <div class="col-lg-6 px-0">
                  <ul class="list-unstyled" data-v-productcertifications-items-mobile="">
                    <li data-v-productcertifications-item-mobile=""><?php  if(isset($certifications)) { foreach ($certifications as $item) { ?>
                      <div class="download-item-container d-flex text-center align-items-center justify-content-start gap-4 mb-10">
                        <img src="<?php  echo $item['logo'] ?? $specificationImage;  ?>" alt="Download Icon" data-v-productcertifications-icon-src-mobile="" class="flex-shrink-0 me-2" style="width: 40px; height: 50px;">
                        <a href="<?php  echo $item['certificateDownloadLink'] ?? '#';  ?>" download="" data-v-productcertifications-item-title-mobile="" data-v-productcertifications-item-link-mobile="" class="block mb-1 text-blue-600 hover:text-blue-800 download-item-link ps-1"><?php  echo $item['title'] ?? '';  ?></a>
                      </div>
                    <?php  }} ?></li>
                  </ul>
                </div>

              </div>
            </div>
          </div>
        </div><?php } ?>


        <?php if (isset($showProductMedia) && $showProductMedia) { ?><div class="accordion-item border-0" data-v-component-productmedia-mobile="">
          <h2 class="accordion-header" id="headingMedia">
            <button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedia" aria-expanded="false" aria-controls="collapseMedia">
 <?php echo App\Core\System\utils\__('Pictures & Videos'); ?>
 </button>
          </h2>
          <div id="collapseMedia" class="accordion-collapse collapse" aria-labelledby="headingMedia" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">

              <div id="product-media-gallery" style="min-width: 200px!important;" class="th-product-gallery-container d-flex flex-items-center justify-content-start" data-v-designresourceimages-images-mobile=""><?php  if(isset($media['items'])){ foreach ($media['items'] as $image) {  ?>
                <div class="th-product-media-item" data-src="<?php  echo isset($image["dataSrc"]) ? $image["dataSrc"] : '';  ?>" data-v-designresourceimages-image-data-src-mobile="">
                  <img src="<?php  echo isset($image["dataSrc"]) ? $image["dataSrc"] : '';  ?>" alt="Product Media" class="img-fluid" data-v-designresourceimages-image-src-mobile="">
                </div>
              <?php  }}  ?></div>

            </div>
          </div>
        </div><?php } ?>
      </div>


      <div class="tab-content d-none d-md-block">

        <?php   ?><?php if (isset($showSpecifications) && $showSpecifications) { ?><div class="<?php  echo $isSpecTabActive ? 'tab-pane fade show active' : 'tab-pane fade';  ?>" id="specs" role="tabpanel" aria-labelledby="specs-tab" data-v-component-productspecifications="">
          <div class="row flex-1">
            <div class="col-lg-6">

              <ul class="" data-v-productspecifications-items=""><?php  if(isset($specs['specifications'])) { foreach ($specs['specifications'] as $item) {  ?>
                <li data-v-productspecifications-item="" class="mb-10"><?php  echo '• ' . $item ?? '';  ?></li>





              <?php  } }  ?></ul>
              <div class="th-link pt-20 pl-10">
                <div class="th-link-text pr-5">
                  <?php if (isset($buttonViewCatalogue) && $buttonViewCatalogue) { ?><a href="<?php  echo $heroproduct['catalogue_link'] ?? '#';  ?>" data-v-productspecifications-link="" target="<?php  echo '_blank';  ?>"><?php  echo 'View in Catalogue';  ?></a><?php } ?>
                </div>

              </div>

            </div>
            <div class="col-lg-6" data-v-productspecifications-image-container="">
              <img data-v-productspecifications-image="" src="<?php  echo !empty($specificationImage) ? $specificationImage : $productImage;  ?>" alt="Product Specifications" class="img-fluid">
            </div>
          </div>
        </div><?php } ?>


        <?php   ?><div class="<?php  echo $isDimensionsTabActive ? 'tab-pane fade show active' : 'tab-pane fade';  ?>" id="dimensions" role="tabpanel" aria-labelledby="dimensions-tab" data-v-component-productdimenssions="">
          <div class="row flex-1">
            <div class="col-lg-6">

              <ul class="">
                <li class="mb-10"><?php echo App\Core\System\utils\__('Width:'); ?> <span id="width-dimension"><?php  echo $dimensions['display_width'] ?? '';  ?></span>
                </li>
                <li class="mb-10"><?php echo App\Core\System\utils\__('Height:'); ?> <span id="height-dimension"><?php  echo $dimensions['display_height'] ?? '';  ?></span>
                </li>
                <li class="mb-10"><?php echo App\Core\System\utils\__('Depth:'); ?> <span id="depth-dimension"><?php  echo $dimensions['display_depth'] ?? '';  ?></span>
                </li>


              </ul>
              <div class="th-link pt-20">
                <div class="th-link-text pr-5 pl-5">
                  <a href="<?php  echo $heroproduct['catalogue_link'] ?? '#';  ?>" data-v-productdimensions-link="" target="_blank">
                    <span data-v-productdimensions-link-text=""><?php echo App\Core\System\utils\__('View in Catalogue'); ?></span>
                  </a>

                </div>
              </div>

            </div>
            <div class="col-lg-6" data-v-productdimensions-image-container="">
              <?php if (isset($showDimensionsImage) && $showDimensionsImage) { ?><img data-v-productdimensions-image="" src="<?php  echo isset($dimensions['dimensions_image'])? $dimensions['dimensions_image'] : $specificationImage;  ?>" alt="Product Dimensions" id="dimensions-image" class="img-fluid"><?php } ?>
            </div>
          </div>
        </div>


        <?php   ?><?php if (isset($showDownloads) && $showDownloads) { ?><div class="<?php  echo $isDownloadsTabActive ? 'tab-pane fade show active' : 'tab-pane fade';  ?>" id="downloads" role="tabpanel" aria-labelledby="downloads-tab" data-v-component-productdownloads="">
          <div class="row flex-1">
            <div class="col-lg-6">

              <ul class="list-unstyled" data-v-downloads-items="">
                <li data-v-downloads-item=""><?php  if(isset($downloads)) { foreach ($downloads as $item) { ?>
                  <div class="download-item-container d-flex">
                    <img src="<?php  echo $item['objectURL'] ?? $specificationImage;  ?>" alt="Download Icon" data-v-downloads-item-icon-src="" style="width: 70px; height: 50px;">
                    <a href="<?php  echo $item['url'] ?? '#';  ?>" download="" data-v-downloads-item-name="" data-v-downloads-item-link="" class="design-resource-tag block mb-1 text-blue-600 hover:text-blue-800 download-item-link"><?php  echo $item['name'] ?? '';  ?></a>
                  </div>
                <?php  }} ?></li>
              </ul>
              <div class="th-link @@classPadding pl-35 mt-15">
                <?php if (isset($showDownloads) && $showDownloads) { ?><div class="th-link-text pr-5" id="download-all-link" data-v-productdownloads-link="">
 <?php echo App\Core\System\utils\__('Download All'); ?>
 </div><?php } ?>
              </div>
            </div>
            <div class="col-lg-6">
              <img src="<?php  echo isset($specs['img']) ? $specs['img'] : '';  ?>" alt="Downloads" class="img-fluid" data-v-productdownloads-image="">
            </div>
          </div>
        </div><?php } ?>


        <?php   ?><?php if (isset($showProductCertifications) && $showProductCertifications) { ?><div class="<?php  echo $isCertificationsTabActive ? 'tab-pane fade show active' : 'tab-pane fade';  ?>" id="certifications" role="tabpanel" aria-labelledby="certifications-tab" data-v-component-productcertifications="">
          <div class="row flex-1">
            <div class="col-lg-6">
              <div class="product-certificates">
                <ul class="list-unstyled" data-v-productcertifications-items=""><?php  if(isset($certifications)) { foreach ($certifications as $item) { ?>
                  <li data-v-productcertifications-item="">
                    <div class="download-item-container d-flex">
                      <img src="<?php  echo $item['logo'] ?? $specificationImage;  ?>" alt="Download Icon" data-v-productcertifications-icon-src="" style="width: 70px; height:50px;">
                      <a href="<?php  echo $item['certificateDownloadLink'] ?? '#';  ?>" download="" data-v-productcertifications-item-link="" class="design-resource-tag blockmb-1 text-blue-600 hover:text-blue-800 download-item-link"><?php  echo $item['title'] ?? '';  ?></a>
                    </div>
                  </li>
                <?php  }} ?></ul>
              </div>
            </div>
            <div class="col-lg-6">
              <img src="<?php  echo isset($specs['img']) ? $specs['img'] : '';  ?>" alt="Certifications" class="img-fluid" data-v-productcertifications-image="">
            </div>
          </div>
        </div><?php } ?>


        <?php   ?><?php if (isset($showProductMedia) && $showProductMedia) { ?><div class="<?php  echo $isMediaTabActive ? 'tab-pane fade show active' : 'tab-pane fade';  ?>" id="media" role="tabpanel" aria-labelledby="media-tab" data-v-component-productmedia="">
          <div class="row flex-1">
            <div class="col-12">

              <div id="product-media-gallery" style="min-width: 200px!important;" class="th-product-gallery-container d-flex flex-items-center justify-content-start" data-v-designresourceimages-images=""><?php  if(isset($media['items'])){ foreach ($media['items'] as $image) {  ?>
                <div class="th-product-media-item" data-src="<?php  echo isset($image["dataSrc"]) ? $image["dataSrc"] : '';  ?>" data-v-designresourceimages-image-data-src="">
                  <img src="<?php  echo isset($image["dataSrc"]) ? $image["dataSrc"] : '';  ?>" alt="Product Media" class="img-fluid" data-v-designresourceimages-image-src="">
                </div>
              <?php  }}  ?></div>

            </div>
          </div>
        </div><?php } ?>

      </div>
    </div>
  </section>

  <style>
    .th-media-item {
      width: 200px;
      height: 200px;
      object-fit: cover;
      object-position: center;
      border-radius: 4px;
      overflow: hidden;
      box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
      cursor: pointer;

      &:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
      }
    }

    .product-certificates {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 10px 10px;
      /* background: #e5ddd6; */
      /* light beige like image */
      border-radius: 4px;
      flex-wrap: wrap;
    }

    .certificate-item {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 80px;
    }

    .product-certificate {
      max-height: 100px;
      width: auto;
      object-fit: contain;
      transition: transform 0.2s ease;
    }

    .product-certificate:hover {
      transform: scale(1.05);
      filter: brightness(1.1);
      opacity: 0.8;
      border-radius: 4px;
      padding: 5px;
    }


    /* Custom styles for the product detail tabs */

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
      .th-product-detail-tabs .accordion-body {
        padding: 1rem;
      }
    }

    /* Custom icon styles */
    .th-product-detail-tabs a i {
      transform: rotate(45deg);
      transition: all 0.2s ease;
    }

    .th-product-detail-tabs a:hover i {
      transform: rotate(45deg) translateX(3px);
    }
  </style>

  <?php  $relatedFamilyProducts = $current_component = $this->_component['productrelatedfamily']?? []; $isRelatedFamilyProductsExist = isset($relatedFamilyProducts['items']) && count($relatedFamilyProducts['items']) > 0 ? true : false;  ?><?php if (isset($isRelatedFamilyProductsExist) && $isRelatedFamilyProductsExist) { ?><section id="th-product-related-family" class="bg-gray-lighter th-product-related-family position-relative th-section-py" data-v-component-productrelatedfamily="" data-v-category-product-family-container="">
    <?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end th-pr-40 align-items-center position-absolute" style="right: 40px" data-v-productrelatedfamily-component-link="">
      <i class="fa-solid fa-pencil th-pr-5" style="font-size: .8rem"></i>
      <a href="<?php  echo isset($relatedFamilyProducts['component_link']) ? $relatedFamilyProducts['component_link'] : '';  ?>" target="_blank" class=""><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?>
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header th-section-header-pb-50">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title th-section-title-mb"> <?php echo App\Core\System\utils\__('Meet the Family'); ?> </h2>

          </div>



        </div>

        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper product-related-slider">
                <div class="swiper-wrapper" data-v-related-family-items=""><?php  if(isset($relatedFamilyProducts['items'])) { foreach ($relatedFamilyProducts['items'] as $item) {  ?>
                  <div class="swiper-slide">
                    <div class="th-item-product p-20 border">
                      <div class="th-img-container bg-white border-0">
                        <a href="<?php  echo $item['link'] ?? '';  ?>" data-v-category-product-url="" target="_blank">
                          <img data-v-related-family-imagesrc="/img/category-seating/Archi.png " src="<?php  echo $item['image'] ?? '';  ?>">
                        </a>
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">

                        <a href="<?php  echo $item['link'] ?? '';  ?>" data-v-category-product-url="" target="_blank">
                          <h3 class="th-item-title th-prouct-title-my" data-v-related-family-title=""><?php  echo $item['title'] ?? '';  ?></h3>
                        </a>
                        <div class="th-tag-name" data-v-related-family-tags=""><?php  if(isset($item['tags'])) { foreach ($item['tags'] as $tag) {  ?>
                          <div class="th-tag th-tag-p" data-v-related-family-tag-item=""><?php  echo $tag ?? '';  ?></div>

                        <?php  } }  ?></div>
                      </div>
                    </div>
                  </div>




                <?php  } }  ?></div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><?php } ?>



  <?php  if(isset($current_component)) $previous_component = $current_component; $featured_projects = $current_component = $this->_component['productfeaturedprojectslider']?? []; $product_name = isset($heroproduct['title']) ? $heroproduct['title'] : (isset($heroproduct['name']) ? $heroproduct['name'] : ''); $subtitle = isset($featured_projects['section_subtitle']) ? $featured_projects['section_subtitle'] . ' ' . $product_name. '.' : ''; $showFeatureProjects = isset($featured_projects['items']) && count($featured_projects['items']) > 0 ? true : false;  ?><?php if (isset($showFeatureProjects) && $showFeatureProjects) { ?><section id="th-featured-projects-slider" class="product-detail-featured-projects-section position-relative" data-v-component-productfeaturedprojectslider="">
    <?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end pr-40 align-items-center position-absolute" style="right: 40px" data-v-productfeaturedprojectslider-component-link="">
      <i class="fa-solid fa-pencil pr-5" style="font-size: .8rem"></i>
      <a href="<?php  echo isset($featured_projects['component_link']) ? $featured_projects['component_link'] : '';  ?>" target="_blank" class=""><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?>
    <div class="container th-container">
      <div class="row featured-projects-row">
        <div class="th-section-header sectionHeaderClass">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title " data-v-productfeaturedprojectslider-section_title=""><?php  echo isset($featured_projects['section_title']) ? $featured_projects['section_title'] : '';  ?></h2>
            <div class="th-section-subtitle" data-v-productfeaturedprojectslider-section_subtitle=""><?php  echo $subtitle;  ?></div>
          </div>

          <div class="right">
            <a href="/projects" target="_blank">
              <div class="th-section-header-link">
                <span class="th-section-header-link-text View All ProjectsClass"><?php echo App\Core\System\utils\__('View All Projects'); ?></span>
                <span class="th-section-header-link-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </span>
              </div>
            </a>
          </div>
        </div>

        <div class="section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-featured-projects-slider th-pb-40">
                <div class="swiper-wrapper"><?php  if(isset($featured_projects['items'])){ foreach ($featured_projects['items'] as $key => $project) {  ?>
                  <div class="swiper-slide d-flex flex-column justify-content-between">
                    <div class="th-item-project d-flex flex-column" style="flex: 1;">
                      <div class="th-img-container itemProjectClass">
                        <a href="<?php  echo isset($project["slug"]) ? "/projects"."/".$project["slug"] : '';  ?>" data-v-featuredprojectslideritem-link="">
                          <img src="<?php  echo isset($project["image"]) ? $project["image"] : '';  ?>" data-v-featuredprojectslideritem-image="">
                        </a>
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30" data-id="<?php  echo isset($project["project_id"]) ? $project["project_id"] : '';  ?>" data-model="project" data-title="<?php  echo isset($project["title"]) ? $project["title"] : '';  ?>" data-description="<?php  echo isset($project["preview_text"]) ? $project["preview_text"] : '';  ?>" data-image="<?php  echo isset($project["image"]) ? $project["image"] : '';  ?>" data-v-featuredproductslideritem-add-to-pinboard="">
                        <i class="fa-regular fa-plus"></i>
                      </div>
                      <div class="th-item-footer d-flex flex-column justify-content-between" style="flex: 1;">
                        <div class="th-mt-20">
                          <div class="th-label" data-v-featuredprojectslideritem-location=""><?php  echo isset($project["location"]) ? $project["location"] : '';  ?></div>
                          <h3 class="th-title th-item-title th-title-mb-15" data-v-featuredprojectslideritem-title="">
                            <a href="<?php  echo isset($project["slug"]) ? "/projects"."/".$project["slug"] : '';  ?>" data-v-featuredprojectslideritem-link=""><?php  echo isset($project["title"]) ? $project["title"] : '';  ?></a>
                          </h3>
                        </div>

                      </div>
                    </div>
                  </div>




                <?php  }}  ?></div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><?php } ?>


  <?php  $productsustainablity = $current_component = $this->_component['productsustainablity']?? []; $oceanPlasticUsed = $productsustainablity['ocean_plastic_used'] ?? 0;  ?><section id="ocean" class="<?php  echo $oceanPlasticUsed ? 'container-show bg-white' : 'container-hide';  ?>" data-v-component-productsustainablity="" data-v-ocean-plastic-used="">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-6">
          <div class="left-column">
            <img src="<?php  echo $productsustainablity['img'] ?? '';  ?>" alt="Product Image" data-v-productsustainablity-img1="">
          </div>
        </div>

        <div class="col-md-6">
          <div class="th-mt-50">
            <div class="th-section-header flex-column pb-0">
              <div class="th-section-header-wrapper @@sectionClass">
                <h2 class="  th-section-title th-section-title-mb" data-v-productsustainablity-title=""><?php  echo $productsustainablity['title'] ?? '';  ?></h2>
                <div class="th-section-subtitle th-section-subtitle-mb" data-v-productsustainablity-subtitle=""><?php  echo $productsustainablity['subtitle'] ?? '';  ?></div>
              </div>

              <div class="@@sectionRightClass">
                <div class="th-section-header-link">
                  <span class="th-section-header-link-text d-block th-mr-7" data-v-productsustainablity-link-text=""><?php  echo $productsustainablity['linkText'] ?? 'View Catalogue';  ?></span>
                  <span class="th-section-header-link-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </span>
                </div>
              </div>
            </div>

            <div class="th-product-sustainability-img">
              <img src="<?php  echo $productsustainablity['img2'] ?? '';  ?>" alt="Product Image" data-v-productsustainablity-img2="">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>



  <?php  $productsYouMayAlsoLike = $current_component = $this->_component['productalsolike']?? []; $showProductsYouMayAlsoLike = isset($productsYouMayAlsoLike['items']) && count($productsYouMayAlsoLike['items']) > 0 ? true : false; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  ?><?php if (isset($showProductsYouMayAlsoLike) && $showProductsYouMayAlsoLike) { ?><section id="th-product-may-like" class="may-also-like position-relative th-section-py" data-v-component-productalsolike="">
    <?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end pr-40 align-items-center position-absolute" style="right: 40px" data-v-productalsolike-component-link="">
      <i class="fa-solid fa-pencil pr-5" style="font-size: .8rem"></i>
      <a href="" target="_blank"><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?>
    <div class="container th-container">
      <div class="row justify-content-center">
        <div class="col-md-12 text-center th-mb-60">
          <h2 class="section-title th-section-title-mb" data-v-productalsolike-section_title=""><?php  echo $productsYouMayAlsoLike['section_title'] ?? '';  ?></h2>
          <div class="section-subtitle th-section-subtitle-mb" data-v-productalsolike-section_subtitle=""><?php  echo $productsYouMayAlsoLike['section_subtitle'] ?? '';  ?></div>
        </div>
        <div class="col-md-12">
          <div class="swiper th-product-may-like-slider th-pb-40">
            <div class="swiper-wrapper" data-v-alsolike-items=""><?php  if(isset($productsYouMayAlsoLike['items']) && count($productsYouMayAlsoLike['items'])) { foreach ($productsYouMayAlsoLike['items'] as $likeItem) {  ?>
              <div class="swiper-slide" data-v-alsolike-item="">
                <div class="th-item-product">
                  <div class="th-img-container">
                    <a href="<?php  echo $likeItem['link'] ?? '#';  ?>" data-v-alsolike-item-link="">
                      <img data-v-alsolike-item-image="" src="<?php  echo $likeItem['image'] ?? '';  ?>" alt="<?php  echo $likeItem['title'] ?? '';  ?>">
                    </a>
                  </div>
                  <div class="th-add-to-pinboard position-absolute top-right-15" data-id="<?php  echo isset($likeItem["id"]) ? $likeItem["id"] : '';  ?>" data-model="product" data-title="<?php  echo isset($likeItem["title"]) ? $likeItem["title"] : '';  ?>" data-description="<?php  echo isset($likeItem["description"]) ? $likeItem["description"] : ''; ?>" data-image="<?php  echo isset($likeItem["image"]) ? $likeItem["image"] : '';  ?>" data-v-productalsolike-item-add-to-pinboard="">
                    <i class="fa-solid fa-plus"></i>
                  </div>
                  <div class="th-item-card-content mt-15">
                    <div class="th-link th-mb-10">
                      <a href="<?php  echo $likeItem['link'] ?? '#';  ?>" data-v-alsolike-item-link="">
                        <h3 class="th-title font-20 th-pr-5" data-v-alsolike-item-title=""><?php  echo $likeItem['title'] ?? '';  ?></h3>
                      </a>
                    </div>
                    <div class="th-description" data-v-alsolike-item-description=""><?php  echo $likeItem['description'] ?? '';  ?></div>
                  </div>
                </div>
              </div>

            <?php  } }  ?></div>
            <div class="swiper-scrollbar"></div>
          </div>
        </div>
      </div>
    </div>
  </section><?php } ?>




  <?php  $productInstagramSlider = $current_component = $this->_component['productinstagramslider']?? []; $showProductInstagramSlider = isset($productInstagramSlider['items'])&& count($productInstagramSlider['items']) > 0;  ?><?php if (isset($showProductInstagramSlider) && $showProductInstagramSlider) { ?><section id="th-product-slider" class="th-section-py" data-v-component-productinstagramslider="">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-12 text-center mb-4">
          <h2 class="section-title th-section-title-mb" data-v-productinstagramslider-title=""><?php  echo isset($productInstagramSlider['title']) ? '# ' . ucfirst($productInstagramSlider['title']) : '';  ?></h2>
        </div>

        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-instagram-products-slider th-pb-80">
                <div class="swiper-wrapper" data-v-productinstagramslider-items=""><?php  if (!empty($productInstagramSlider['items'])) { foreach ($productInstagramSlider['items'] as $item) {  ?>
                  <div class="swiper-slide" data-v-productinstagramslider-item="">
                    <div class="th-item-card">
                      <div class="th-instagram-img-container" data-bg-src="<?php  echo isset($item['img']) ? $item['img'] : '';  ?>" data-v-productinstagramslider-item-image="">
                        <div class="th-item-card-content">
                          <div class="th-instagram-link">
                            <a href="<?php  echo !empty($item['link']) ? 'https://www.instagram.com'. htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') : '#';  ?>" target="_blank" data-v-productinstagramslider-item-link="">
                              <i class="fa-brands fa-instagram"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>




                <?php  } }  ?></div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><?php } ?>



  <section id="need-help-section">
    <?php if (isset($is_admin ) && $is_admin ) { ?><?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end pr-40 align-items-center position-absolute th-component-edit-link" data-v-component-link="" style="right: 40px">
      <i class="fa-solid fa-pencil pr-5" style="font-size: .8rem"></i>
      <a href="http://localhost:5173/components" target="_blank"><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?><?php } ?>
    <div class="container th-container">
      <?php  if(isset($current_component)) $previous_component = $current_component; $need_help = $current_component = $this->_component['needhelp']?? []; $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  ?><div class="row need-help-row" data-v-component-needhelp="">
        <div class="th-section-header sectionHeaderClass th-section-header-pb-50" style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h2 class="th-section-title th-section-title-mb" data-v-needhelp-section_title=""><?php if (isset($need_help['section_title'])) echo htmlspecialchars($need_help['section_title']); ?></h2>
            <div class="th-section-subtitle th-section-subtitle-mb" data-v-needhelp-section_subtitle=""><?php if (isset($need_help['section_subtitle'])) echo htmlspecialchars($need_help['section_subtitle']); ?></div>
          </div>

        </div>

        <div class="section-body">
          <div class="col-md-12">
            <div class="<?php if (isset($need_help['section_class'])) echo htmlspecialchars($need_help['section_class']); ?>" data-v-needhelp-wrapper=""><?php  if(isset($need_help['items'])){ foreach ($need_help['items'] as $blog) {  ?>
              <div class="th-item-help d-flex flex-column align-items-center">
                <div class="d-flex flex-column justify-content-between align-items-center">
                  <div class="th-btn-circle-icon">
                    <i class="<?php  echo $blog["icon"];  ?>" data-v-needhelpitem-icon=""></i>
                  </div>
                  <h6 class="th-item-title-bold" style="min-height: 65px; margin-bottom: 0;" data-v-needhelpitem-title=""><?php  echo $blog["title"];  ?></h6>
                </div>
                <div class="d-flex flex-column justify-content-between align-items-center" style="flex: 1;">
                  <p class="th-description th-need-to-help-des-my" data-v-needhelpitem-description=""><?php  echo $blog["description"];  ?></p>
                  <div class="th-link">
                    <a href="<?php  echo $blog["link"];  ?>" class="th-link-text pr-5" data-v-needhelpitem-link=""><?php  echo $blog["link_text"];  ?></a>

                  </div>
                </div>
              </div>




            <?php  }}  ?></div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <section id="footer-section-container" class="th-footer bg-gray th-footer-section-py" title="footer" data-v-save-global="index.html,.footer-1">
    <?php if (isset($is_admin ) && $is_admin ) { ?><?php if (isset($is_admin) && $is_admin) { ?><div class="d-flex justify-content-end pr-40 align-items-center position-absolute th-component-edit-link" data-v-component-link="" style="right: 40px">
      <i class="fa-solid fa-pencil pr-5 th-pr-5" style="font-size: .8rem"></i>
      <a href="http://localhost:5173/components" target="_blank"><?php echo App\Core\System\utils\__('Edit'); ?></a>
    </div><?php } ?><?php } ?>
    <div class="container th-container" data-v-slug="main-footer">
      <div class="row component-footer" data-v-component-footer="">
        <div class="col-lg-8 border-right-dark border-right-md-0">
          <h4 class="text-weight-700 th-footer-title th-title-mb-15"><?php echo App\Core\System\utils\__('Contact Us'); ?></h4>
          <div class="footer-left-content">
            <div class="text-weight-600 pt-10">
              <div class="d-flex align-items-center">
                <i class="fa-solid fa-envelope pr-5 th-pr-5"></i>
                <p data-v-footer-contact_email="">
                  <a href="mailto:sales@krost.com.au"><?php echo App\Core\System\utils\__('sales@krost.com.au'); ?></a>
                </p>
              </div>
              <div class="d-flex align-items-center">
                <i class="fa-solid fa-phone pr-5 th-pr-5"></i>
                <p data-v-footer-contact_phone="">
                  <a href="tel:1800157678" class="footer-phone-link">1800 1KROST</a>
                </p>
              </div>
            </div>

            <div class="d-grid grid-col-3">
              <div class="th-showroom-address">
                <p class="th-footer-heading th-footer-title-mb" data-v-footer-sydney_office_name=""><?php echo App\Core\System\utils\__('Sydney Office'); ?></p>
                <p class="th-address-line" data-v-footer-sydney_office_address="">
                  33 Ricketty St
                  <br>
                  <span><?php echo App\Core\System\utils\__('Mascot NSW, 2020'); ?></span>
                </p>
                <div class="d-flex align-items-center text-weight-600 pt-10">
                  <i class="fa-solid fa-phone pr-5 th-pr-5"></i>
                  <p data-v-footer-sydney_office_phone="">
                    <a href="tel:0295573055" class="footer-phone-link">02 9557 3055</a>
                  </p>
                </div>
                <p class="th-opening-hours" data-v-footer-sydney_office_hours=""><?php echo App\Core\System\utils\__('Mon-Fri, 8AM-5PM'); ?></p>
              </div>

              <div class="th-showroom-address">
                <p class="text-weight-600 mb-15 th-footer-heading th-footer-title-mb" data-v-footer-melbourne_office_name=""><?php echo App\Core\System\utils\__('Melbourne Office'); ?></p>
                <p class="th-address-line" data-v-footer-melbourne_office_address="">
                  617-643 Spencer St
                  <br>
                  <span><?php echo App\Core\System\utils\__('West Melbourne VIC, 3000'); ?></span>
                </p>
                <div class="d-flex align-items-center text-weight-600 pt-10">
                  <i class="fa-solid fa-phone pr-5 th-pr-5"></i>
                  <p data-v-footer-melbourne_office_phone="">
                    <a href="tel:0396828280" class="footer-phone-link">03 9682 8280</a>
                  </p>
                </div>
                <p class="th-opening-hours" data-v-footer-melbourne_office_hours=""><?php echo App\Core\System\utils\__('Mon-Fri, 9AM-5PM'); ?></p>
              </div>

              <div class="th-showroom-address">
                <p class="text-weight-600 mb-15 th-footer-heading th-footer-title-mb" data-v-footer-brisbane_office_name=""><?php echo App\Core\System\utils\__('Brisbane Office'); ?></p>
                <p class="th-address-line" data-v-footer-brisbane_office_address="">
                  936 Stanley St E
                  <br>
                  <span><?php echo App\Core\System\utils\__('East Brisbane QLD, 4169'); ?></span>
                </p>
                <div class="d-flex align-items-center text-weight-600 pt-10">
                  <i class="fa-solid fa-phone pr-5 th-pr-5"></i>
                  <p data-v-footer-brisbane_office_phone="">
                    <a href="tel:0733386000" class="footer-phone-link">07 3338 6000</a>
                  </p>
                </div>
                <p class="th-opening-hours" data-v-footer-brisbane_office_hours=""><?php echo App\Core\System\utils\__('Mon-Fri, 8:30AM-5PM'); ?></p>
              </div>
            </div>


          </div>
        </div>
        <div class="col-lg-4 d-flex flex-column">
          <div class="th-subscription pl-20 th-pl-20 pl-md-0">
            <div class="th-subscription-heading">
              <div class="th-subscription-details">

                <h4 class="text-weight-700 th-footer-title" data-v-footer-subscription_title=""><?php echo App\Core\System\utils\__('Krost News'); ?></h4>
                <div class="th-description description" data-v-footer-subscription_description=""><?php echo App\Core\System\utils\__('Receive the latest news and updates'); ?></div>
              </div>
            </div>
            <form action="/#subscription-form" method="POST" id="subscription-form">
              <input type="hidden" name="nonce" id="subscription-form-nonce" value="" data-v-nonce="">
              <div class="th-subscription-form" id="subscription-form-container">
                <label class="pr-10">
                  <input type="email" class="form-control" name="email" id="subscriptionEmail" placeholder="Enter your email" data-v-footer-subscription_placeholder="">
                  <div class="invalid-feedback" id="email-feedback"></div>
                  <div class="valid-feedback" id="success-feedback"></div>
                </label>
                <div class="link">
                  <button id="subscription-form-button" type="submit" class="link-text pr-5" data-v-footer-subscription_button_text=""><?php echo App\Core\System\utils\__('Subscribe'); ?></button>
                </div>
              </div>
            </form>

          </div>
          <div class="th-footer-navigation">
            <ul>

              <li class="pl-0">
                <span class="link pl-0">
                  <a href="/about" data-v-footer-footer_navigation_our_store=""><?php echo App\Core\System\utils\__('About'); ?></a>
                </span>
              </li>
              <li class="class">
                <span class="link class">
                  <a href="/catalogue" data-v-footer-footer_navigation_visit_us=""><?php echo App\Core\System\utils\__('Catalogue'); ?></a>
                </span>
              </li>
              <li class="border-right-0">
                <span class="link border-right-0">
                  <a href="/contact-us" data-v-footer-footer_navigation_contact_us=""><?php echo App\Core\System\utils\__('Contact Us'); ?></a>
                </span>
              </li>

            </ul>
          </div>

          <div class="th-footer-follow th-pt-30 mt-auto th-pl-20 pl-md-0">
            <p class="text-weight-600 th-pb-10"><?php echo App\Core\System\utils\__('Follow Us:'); ?></p>
            <div class="social-media">
              <ul class="social-media-list" data-v-footer-socila-media-list="">

                <li class="social-media-item" data-v-footer-socila-media-item="">
                  <span class="link">
                    <a href="https://www.linkedin.com/company/krost-furniture/" target="_blank" data-v-url="" area-label="LinkedIn">
                      <i class="fa-brands fa-linkedin-in" data-v-icon=""></i>
                    </a>
                  </span>
                </li>

                <li class="social-media-item">
                  <span class="link">
                    <a href="https://www.facebook.com/krostfurniture/" target="_blank" area-label="Facebook">
                      <i class="fa-brands fa-facebook-f"></i>
                    </a>
                  </span>
                </li>
                <li class="social-media-item">
                  <span class="link">
                    <a href="https://www.instagram.com/krostfurniture/" target="_blank" area-label="Instagram">
                      <i class="fa-brands fa-instagram"></i>
                    </a>
                  </span>
                </li>
                <li class="social-media-item">
                  <span class="link">
                    <a href="https://www.pinterest.com/krostfurniture/" target="_blank" area-label="Pinterest">
                      <i class="fa-brands fa-pinterest-p"></i>
                    </a>
                  </span>
                </li>

              </ul>
            </div>
          </div>



        </div>
      </div>
    </div>

    <div class="th-footer-copyright mt-30">
      <div class="container th-container">
        <div class="d-flex flex-column flex-md-row th-footer-copyright-content">
          <div class="text-muted flex-grow-1">
            <a class="btn-link text-muted" href="/privacy-policy" target="_blank" data-v-footer-copyright_privacy_url=""><?php echo App\Core\System\utils\__('Terms & Privacy Policy'); ?></a>
          </div>
          <div class="text-muted copyright-text-right">
 <?php echo App\Core\System\utils\__('©'); ?>

            <span class="text-muted" data-v-footer-copyright_powered_by_url="" area-label="KROST"><?php echo App\Core\System\utils\__('Krost Business Furniture'); ?></span>
            <span data-v-footer-copyright_year="">2026</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <style>
 <?php echo App\Core\System\utils\__('.footer-logo-container {
 display: flex;
 align-items: right;
 justify-content: right;
 margin-top: -65px;
 }
 .footer-logo {
 top: 239px;
 left: 1396px;
 width: 74px;
 height: 61px;
 }'); ?>
 </style>
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

  <div class="offcanvas offcanvas-end pinboard" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div id="pinboard-app">

    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js" async="" data-quill="true"  ></script>

    <style>
      /* new pinboard sidebar section start */



      /* .pinboard {
    width: 350px;
    margin: 20px auto;
} */

      .pinboard.offcanvas {
        z-index: 1055;
      }

      .card-item {
        display: flex;
        background: #fff;
        border-radius: 3px;
        padding: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        align-items: self-start;
      }

      .card-left {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;

        img {
          width: 100%;
          height: 100%;
          object-fit: contain;
          border-radius: 3px;
        }
      }


      .card-content {
        flex: 1;
        margin-left: 12px;
      }

      .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
      }

      .type {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--gray-600);
        letter-spacing: 1px;
      }

      .card-actions {
        margin-top: -25px;
        display: flex;
        align-items: center;
      }

      .card-actions i {
        margin-left: 10px;
        color: #999;
        cursor: pointer;
      }

      .card-actions i:hover {
        color: #333;
      }

      .card-footer {
        margin-top: 8px;
        font-size: 14px;
        color: #555;
        cursor: pointer;
      }

      .card-footer:hover {
        color: #000;
      }

      .pinboard-header-info {
        gap: 10px;
      }

      .pinboard-offcanvas-close {
        line-height: 1;
        text-decoration: none !important;
      }

      .pinboard-offcanvas-close:focus {
        box-shadow: none;
      }

      .pinboard-offcanvas-close:focus-visible {
        outline: 2px solid rgba(0, 0, 0, 0.25);
        outline-offset: 2px;
        border-radius: 4px;
      }

      /* Project name + chevron dropdown (logged-in header; desktop) */
      .pinboard-header-project-wrap {
        min-width: 0;
        max-width: min(100%, 280px);
      }

      /* Mobile-only header duplicate: full width + menu aligned under project row */
      .pinboard-header-project-wrap--mobile {
        max-width: 100%;
      }

      .pinboard-project-menu--mobile {
        left: 0;
        right: auto;
      }

      .pinboard-project-name {
        font-size: 16px;
        line-height: 1.2;
      }

      .pinboard-project-email {
        font-size: 14px;
        margin-top: 2px;
      }

      /* .pinboard-project-chevron-btn {
  line-height: 1;
  text-decoration: none !important;
}
.pinboard-project-chevron-btn:focus {
  box-shadow: none;
  outline: none;
}
.pinboard-project-chevron-btn:focus-visible {
  outline: 2px solid rgba(0, 0, 0, 0.25);
  outline-offset: 2px;
  border-radius: 2px;
} */
      .pinboard-project-chevron {
        font-size: 12px;
        transition: transform 0.2s ease;
        display: inline-block;
      }

      .pinboard-project-chevron.is-open {
        transform: rotate(180deg);
      }

      /* .pinboard-project-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  min-width: 260px;
  max-width: min(92vw, 320px);
  z-index: 1060;
  padding: 8px 0;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 2px 6px rgba(0, 0, 0, 0.06);
  border: 1px solid rgba(0, 0, 0, 0.06);
  text-align: left;
} */
      .pinboard-project-menu-item {
        display: block;
        width: 100%;
        padding: 10px 16px;
        font-size: 14px;
        line-height: 1.35;
        color: #212529;
        background: transparent;
        border: 0;
        text-align: left;
        cursor: pointer;
        transition: background 0.15s ease;
      }

      .pinboard-project-menu-item:hover,
      .pinboard-project-menu-item:focus {
        background: #f3f4f6;
        outline: none;
      }

      .pinboard-project-menu-item.is-active {
        background: #eceff1;
      }

      .pinboard-project-menu-create {
        font-weight: 500;
      }

      .pinboard-project-menu-divider {
        height: 1px;
        margin: 6px 0;
        background: rgba(0, 0, 0, 0.08);
      }

      .pinboard-project-check {
        font-size: 13px;
        color: #212529;
      }

      .pinboard-project-menu-label {
        min-width: 0;
      }

      .pinboard-info {
        text-align: end;

        h5 {
          font-size: 16px;
          font-weight: 600;
          margin: 0
        }

        h6 {
          font-size: 12px;
          font-weight: 400;
          margin: 0
        }
      }

      .user-profile-img {
        height: 50px;
        width: 50px;
        border-radius: 50%;
        overflow: hidden;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          border-radius: 50%;
        }
      }

      /* new pinboard sidebar section end */
















      .th-pinboard-edit-wrapper {
        border: 1px solid #dee2e6;
        border-radius: 10px !important;
        font-size: 14px !important;
      }

      /* Keep consistent height when switching between view and edit mode */
      .th-pinboard-edit-content .item-comment-box {
        min-height: 46px !important;
        resize: none;
      }

      /* textarea.form-control,
    textarea {
      min-height: 154px !important;
      padding-top: 16px !important;
      padding-bottom: 17px !important;
      font-size: 14px !important;
    } */

      .th-display-pre-line {
        white-space: pre-line !important;
      }

      .th-pinboard-view-text {
        padding-top: 0 !important;
        margin-top: -15px !important;
      }

      .th-pinboard-item-comment>.item-comment-box,
      .th-pinboard-item-edit .item-comment-box {
        /* min-height: 62px !important;
      max-height: 150px !important; */
        overflow-y: auto !important;
        font-size: 14px !important;
        border-radius: 10px !important;
      }

      /* New comment textarea - empty/placeholder state */
      .th-pinboard-item-comment .item-comment-box {
        min-height: 90px !important;
        scrollbar-width: thin;
        line-height: 25px;
        font-size: 14px;
        padding: 5px 5px;
        border-radius: 10px !important;
      }

      /* Thin scrollbar in editing mode, remove inner border */
      .th-pinboard-item-edit .item-comment-box {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
        line-height: 25px;
      }

      .th-pinboard-item-edit .item-comment-box::-webkit-scrollbar {
        width: 4px;
      }

      .th-pinboard-item-edit .item-comment-box::-webkit-scrollbar-track {
        background: transparent;
      }

      .th-pinboard-item-edit .item-comment-box::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
      }

      /* textarea.form-control,
    textarea {
      min-height: 70px;
      padding-top: 16px;
      padding-bottom: 17px;
    } */



      .th-btn-primary-post {
        width: 50px !important;
        height: 35px !important;
        background-color: #000;
        color: #fff !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        border: none;
        margin-right: 10px;

        &:hover {
          background-color: rgb(9, 7, 7);
          text-shadow: 0px 0px 1px rgba(255, 255, 255, 0.8);
        }
      }

      #save-pinboard-form {
        @media screen and (max-width: 500px) {
          .th-modal-body-padding {
            padding: 0px !important;
          }
        }
      }

      .otp-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
      }

      .otp-input {
        width: 48px;
        height: 55px;
        text-align: center;
        font-size: 22px;
        font-weight: 600;
        border-radius: 12px;
        border: 1px solid #ddd;
        transition: all 0.2s ease;
      }

      .otp-input:focus {
        border-color: #000;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
        outline: none;
      }

      .resend-link {
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
      }

      /* Pinboard drag & drop placeholder and preview */
      .pinboard-ghost {
        opacity: 0.9;
        height: 150px;
        margin: 0 0 12px 0 !important;
        border: 1px dashed #cfcfcf;
        background: var(--gray-light) !important;
        border-radius: 6px;
        overflow: hidden;
      }

      .pinboard-ghost>* {
        opacity: 0 !important;
      }

      .pinboard-drag-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        max-width: 280px;
      }

      .pinboard-drag-preview .thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
      }

      .pinboard-drag-preview .title {
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .pinboard-drag-preview .close-icon {
        margin-left: auto;
        font-size: 14px;
        color: #999;
      }

      /* Add Comment (toggle button + panel) */
      .th-add-comment-toggle-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px;
        background: var(--gray-200);
        border: none;
        border-radius: 999px;
        color: #4a4a4a;
        font-size: 16px;
        font-weight: 400;
        text-decoration: none;
        line-height: 1;
        position: relative;
      }

      .th-add-comment-toggle-btn::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border: 2px dashed var(--gray-400);
        border-radius: 999px;
        pointer-events: none;
      }

      .th-add-comment-toggle-plus {
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
      }

      .th-add-comment-panel {
        width: 100%;
        background: #fff;
        border: 1px solid #e6e6e6;
        border-radius: 3px;
        padding: 18px 18px 16px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
      }

      .th-add-comment-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* margin-bottom: 14px; */
        gap: 12px;
      }

      .th-add-comment-panel-title {
        font-size: 20px;
        font-weight: 600;
        color: #111;
      }

      .th-add-comment-panel-collapse {
        color: #6f6f6f;
        font-size: 16px;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
        white-space: nowrap;
      }

      .th-add-comment-textarea {
        width: 100%;
        border: 1px solid #d7d7d7 !important;
        border-radius: 3px !important;
        padding: 16px 16px !important;
        min-height: 110px !important;
        font-size: 16px !important;
        color: #222;
        resize: none;
        outline: none;
      }

      .th-add-comment-textarea::placeholder {
        color: #9a9a9a;
        opacity: 1;
      }

      .th-add-comment-preview-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin: 12px 0;
      }

      .th-add-comment-bottom-row {
        margin-top: 14px;
        gap: 14px;
        align-items: stretch;
      }

      .th-add-comment-upload-label {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #d7d7d7;
        border-radius: 12px;
        padding: 12px 10px;
        background: #fff;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        color: #000;
        text-decoration: none;
        line-height: 1;
      }

      .th-add-comment-upload-label input {
        display: none;
      }

      .th-add-comment-submit-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        padding: 12px 10px;
        background: #000;
        color: #fff !important;
        font-size: 16px !important;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid #000;
      }
    </style>
    <div class="bg-image"></div>

    <script type="text/javascript" src="/js/lib/jquery-3.7.1.min.js?v=1.0.1"  ></script>

    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js" defer=""  ></script>
    <script src="https://cdn.jsdelivr.net/npm/vuex@3/dist/vuex.js" defer=""  ></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.10.2/Sortable.min.js" defer=""  ></script>
    <script src="https://unpkg.com/vuedraggable@2.20.0/dist/vuedraggable.umd.min.js" defer=""  ></script>

    <script type="text/javascript" src="/js/lib/player.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lightgallery.min.js?v=1.0.1"  ></script>
    <script type="text/javascript" src="/js/lib/lg-thumbnail.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-zoom.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-autoplay.min.js" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-fullscreen.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-share.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-rotate.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-video.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/lg-pager.min.js?v=1.0.1" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/choices.min.js?v=1.0.1" defer=""  ></script>

    <script src="/js/vue-universal-app.js" type="module" defer=""  ></script>
    <script src="/js/vue-product-app.js" type="module" defer=""  ></script>

    <script type="text/javascript" src="/js/lib/bootstrap.bundle.min.js" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/swiper-bundle.min.js" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/choices.min.js" defer=""  ></script>
    <script type="text/javascript" src="/js/lib/flatpickr-4.6.13.min.js" defer=""  ></script>
    <script type="text/javascript" src="/js/bundle.js" defer=""  ></script>