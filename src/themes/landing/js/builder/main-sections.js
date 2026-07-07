
function changeNodeClass(node, className, classArray)
{
  //First remove all classes from the node that match classArray

  //Add the className to the node 

	return node;
}
Vvveb.Sections.add("header/header-transparent-section", {
    name: "Transparent Header",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/header-section.png",
    html: `
    <header class="th-header header-layout">
      <!--==============================
         Top Bar
        ============================== -->
      <div class="th-topbar d-flex align-items-center justify-content-center">
        <p class="font-white m-0">Free shipping on apparel & gear over 75 USD</p>
      </div>
      <!--==============================
          Mobile Menu
        ============================== -->
      <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
          <button class="th-menu-toggle">
            <i class="fal fa-times"></i>
          </button>
          <div class="mobile-logo">
            <a class="icon-masking" href="/">
              <img src="/${Vvveb.themeBaseUrl}img/logo-white.png" alt="KROST">
            </a>
          </div>
  
          <div class="th-mobile-menu">
            <ul>
              <li class="menu-item-has-children mega-menu-wrap">
                <a href="#">Home</a>
                <ul class="mega-menu">
                  <li>
                    <a href="#">Multipage</a>
                    <ul>
                      <li>
                        <a href="index.html">Digital Agency</a>
                      </li>
                      <li>
                        <a href="home-web-development.html">Web Development</a>
                      </li>
                      <li>
                        <a href="home-software-company.html">Software Company</a>
                      </li>
                      <li>
                        <a href="home-software-company-2.html">Software Company Style 2</a>
                      </li>
                      <li>
                        <a href="home-startup-company.html">Startup Company</a>
                      </li>
                      <li>
                        <a href="home-it-solution.html">IT Solution</a>
                      </li>
                      <li>
                        <a href="home-web-agency.html">Web Agency</a>
                      </li>
                      <li>
                        <a href="home-startup.html">Home Startup </a>
                      </li>
                      <li>
                        <a href="home-game-solution.html"> Game Solution</a>
                      </li>
                      <li>
                        <a href="home-sass-marketing.html">Sass Marketing</a>
                      </li>
                      <li>
                        <a href="home-sass-landing.html">Sass Landing</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Multipage</a>
                    <ul>
                      <li>
                        <a href="home-sass-landing-2.html">Sass Landing Style 2</a>
                      </li>
                      <li>
                        <a href="home-app-landing.html">App Landing</a>
                      </li>
                      <li>
                        <a href="home-ai-technology.html">AI Technology</a>
                      </li>
                      <li>
                        <a href="home-cyber-security.html">Cyber Security</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-2.html">Cyber Security Style 2 </a>
                      </li>
                      <li>
                        <a href="home-it-company.html">IT Company</a>
                      </li>
                      <li>
                        <a href="home-digital-marking.html">Digital Marking</a>
                      </li>
                      <li>
                        <a href="home-it-agency.html">IT Agency</a>
                      </li>
                      <li>
                        <a href="home-crm.html">Home CRM</a>
                      </li>
                      <li>
                        <a href="home-it-consulting.html">IT Consulting</a>
                      </li>
                      <li>
                        <a href="home-help-desk.html">Home Help Desk</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Onepage</a>
                    <ul>
                      <li>
                        <a href="home-digital-agency-op.html">Digital Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-web-development-op.html">Web Development Onepage</a>
                      </li>
                      <li>
                        <a href="home-software-company-op.html">Software Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-software-company-2-op.html">Software Company Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-startup-company-op.html">Startup Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-solution-op.html">IT Solution Onepage</a>
                      </li>
                      <li>
                        <a href="home-web-agency-op.html">Web Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-startup-op.html">Home Startup Onepage </a>
                      </li>
                      <li>
                        <a href="home-game-solution-op.html"> Game Solution Onepage</a>
                      </li>
                      <li>
                        <a href="home-sass-marketing-op.html">Sass Marketing Onepage</a>
                      </li>
                      <li>
                        <a href="home-sass-landing-op.html">Sass Landing Onepage</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Onepage</a>
                    <ul>
                      <li>
                        <a href="home-sass-landing-2-op.html">Sass Landing Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-app-landing-op.html">App Landing Onepage</a>
                      </li>
                      <li>
                        <a href="home-ai-technology-op.html">AI Technology Onepage</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-op.html">Cyber Security Onepage</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-2-op.html">Cyber Security Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-company-op.html">IT Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-digital-marking-op.html"> Digital Marking Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-agency-op.html">IT Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-crm-op.html">Home CRM Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-consulting-op.html">IT Consulting Onepage</a>
                      </li>
                      <li>
                        <a href="home-help-desk-op.html">Home Help Desk</a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
              <li>
                <a href="about.html">About Us</a>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Services</a>
                <ul class="sub-menu">
                  <li>
                    <a href="service.html">Services</a>
                  </li>
                  <li>
                    <a href="service-details.html">Services Details</a>
                  </li>
                </ul>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Pages</a>
                <ul class="sub-menu">
                  <li class="menu-item-has-children">
                    <a href="#">Shop</a>
                    <ul class="sub-menu">
                      <li>
                        <a href="shop.html">Shop</a>
                      </li>
                      <li>
                        <a href="shop-details.html">Shop Details</a>
                      </li>
                      <li>
                        <a href="cart.html">Cart Page</a>
                      </li>
                      <li>
                        <a href="checkout.html">Checkout</a>
                      </li>
                      <li>
                        <a href="wishlist.html">Wishlist</a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="team.html">Team</a>
                  </li>
                  <li>
                    <a href="team-details.html">Team Details</a>
                  </li>
                  <li>
                    <a href="project.html">Project</a>
                  </li>
                  <li>
                    <a href="project-details.html">Project Details</a>
                  </li>
                  <li>
                    <a href="gallery.html">Gallery</a>
                  </li>
                  <li>
                    <a href="pricing.html">Pricing</a>
                  </li>
                  <li>
                    <a href="faq.html">Faq Page</a>
                  </li>
                  <li>
                    <a href="error.html">Error Page</a>
                  </li>
                </ul>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Blog</a>
                <ul class="sub-menu">
                  <li>
                    <a href="blog.html">Blog</a>
                  </li>
                  <li>
                    <a href="blog-details.html">Blog Details</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="contact.html">Contact</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
  
      <div class="sticky-wrapper">
        <!-- Main Menu Area -->
        <div class="menu-area">
          <div class="container th-container">
            <div class="row align-items-center justify-content-between ps-50 pe-50">
              <div class="d-flex col-4 align-items-center justify-content-start">
                <nav class="main-menu d-none d-lg-inline-block">
                  <ul>
                    <li class="menu-item-has-children mega-menu-wrap">
                      <a href="#">
                        <span>Products</span>
                      </a>
                      <ul class="mega-menu py-5">
                        <!-- <div class="row justify-content-center"> -->
                        <div class="container th-container">
                          <div class="row">
                            <div class="col-9">
                              <div class="row">
                                <div class="col">
                                  <h5 class=".sub-title">Workstations</h5>
                                  <a href="/shofiul-categories-workstation.html">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>screens</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Screens</h5>
                                  <a href="#">
                                    <span>Screens</span>
                                  </a>
                                  <a href="#">
                                    <span>Acoustic booths</span>
                                  </a>
                                  <a href="#">
                                    <span>Perspex screens</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Desks</h5>
                                  <a href="#">
                                    <span>Executive desks</span>
                                  </a>
                                  <a href="#">
                                    <span>Desk system</span>
                                  </a>
                                  <a href="#">
                                    <span>Modesty panels</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>tables</h5>
                                  <a href="#">
                                    <span>Meeting tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Counter Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Training Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Cafe Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Coffee Tables</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Storage</h5>
                                  <a href="#">
                                    <span>In-office storage</span>
                                  </a>
                                  <a href="#">
                                    <span>Universal storage</span>
                                  </a>
                                  <a href="#">
                                    <span>steel storage units</span>
                                  </a>
                                  <a href="#">
                                    <span>Shelving</span>
                                  </a>
                                  <a href="#">
                                    <span>Custom joinery</span>
                                  </a>
                                </div>
                              </div>
                              <div class="row mt-50">
                                <div class="col">
                                  <h5>Seating</h5>
                                  <a href="/shofiul-categories-seating.html">
                                    <span>All seating</span>
                                  </a>
                                  <a href="/shofiul-category-products.html">
                                    <span>Task seating</span>
                                  </a>
                                  <a href="#">
                                    <span>executive seating</span>
                                  </a>
                                  <a href="#">
                                    <span>training seating</span>
                                  </a>
                                  <a href="#">
                                    <span>Occasional seating</span>
                                  </a>
                                  <a href="#">
                                    <span>Stools</span>
                                  </a>
                                  <a href="#">
                                    <span>Lounges</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Accessories</h5>
                                  <a href="#">
                                    <span>Computer accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>Power accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>General office
                                      accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>Acoustic solutions</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Training</h5>
                                  <a href="#">
                                    <span>Communication
                                      boards</span>
                                  </a>
                                  <a href="#">
                                    <span>screen partitions</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Finishes</h5>
                                  <a href="#">
                                    <span>Materials</span>
                                  </a>
                                  <a href="#">
                                    <span>Fabrics</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Counters</h5>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col">
                                  <a href="/categories">View all product Categories</a>
                                </div>
                              </div>
                            </div>
                            <div class="col-3 d-flex flex-column gap-3">
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/book-metting.png" alt="boot-meetting">
                              </div>
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/contact-sells.png" alt="contact-sells">
                              </div>
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/request-catalog.png" alt="request-catalog">
                              </div>
                            </div>
                          </div>
                        </div>
                      </ul>
                    </li>
                    <li>
                      <a href="shofiul-projects.html">
                        <span>Projects</span>
                      </a>
                    </li>
                    <li>
                      <a href="tuhin-blogs.html">
                        <span>Blog</span>
                      </a>
                    </li>
                    <li>
                      <a href="/zahidul-about.html">
                        <span>About</span>
                      </a>
                    </li>
                  </ul>
                </nav>
                <button type="button" class="th-menu-toggle d-block d-lg-none">
                  <i class="far fa-bars"></i>
                </button>
              </div>
              <div class="d-flex col-4 align-items-center justify-content-center">
                <div class="header-logo">
                  <a class="icon-masking" href="/">
                    <img src="/${Vvveb.themeBaseUrl}img/logo-white.png" alt="KROST">
                  </a>
                  </a>
                </div>
              </div>
              <div class="d-flex col-4 align-items-center justify-content-end">
                <div class="header-button">
                  <a href="tel:+2586232325" class="h-btn">Buy</a>
                </div>
                <a href="" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                  <img src="/${Vvveb.themeBaseUrl}img/pinboard-icon.svg" alt="Pinboard" style="width: 3rem; height: 3rem;">
                </a>
              </div>
  
            </div>
          </div>
        </div>
      </div>
    </header>
  
         
      `,
});
Vvveb.Sections.add("header/transparent-hero-section", {
    name: "Transparent Hero",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/hero-section.png",
    html: `
      <section class="th-hero-wrapper th-hero th-hero-transparent gr-bg4  background-image" id="hero" style="background-image: url(/${Vvveb.themeBaseUrl}img/bg/home/hero_home.jpg);">
      <!--========== Hero Transparent Section Start ==========-->
      <div class="container th-container">
        <div class="row">
          <div class="col-md-12">
  
          </div>
        </div>
        <div class="row">
          <div class="col-xl-7">
            <div class="th-hero-container gr-bg4">
              <div class="th-hero-style">
                <h1 class="th-hero-title">
                  2025 <span class="">Catalogue</span>
                  <span class="line-break">Sent Out</span>
                </h1>
                <span class="th-hero-subtitle">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</span>
  
                <div class="th-hero-wrapp">
  
                  <div class="position-relative pb-3">
                    <a href="contact.html" class="th-btn text-capitalize">
                      <span class="mr-5">
                        Visit Our Showroom
  
                      </span>
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </a>
  
                  </div>
  
  
                  <div class="position-relative">
                    <a href="contact.html" class="th-btn-outline text-capitalize">
                      <span class="mr-5">
                        Contact Sales
  
                      </span>
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </a>
  
                  </div>
  
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-5">
            <div class="th-hero-img"></div>
          </div>
        </div>
      </div>
      <!--========== Hero Transparent Section End ==========-->
    </section>
    `,
});

Vvveb.Sections.add("header/header-white-section", {
    name: "White Header",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/header-white.png",
    html: `
     <header class="th-header header-layout menu-white">
      <!--==============================
         Top Bar
        ============================== -->
      <div class="th-topbar d-flex align-items-center justify-content-center">
        <p class="font-white m-0">Free shipping on apparel & gear over 75 USD</p>
      </div>
      <!--==============================
          Mobile Menu
        ============================== -->
      <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
          <button class="th-menu-toggle">
            <i class="fal fa-times"></i>
          </button>
          <div class="mobile-logo">
            <a class="icon-masking" href="/">
              <img src="/${Vvveb.themeBaseUrl}img/logo-white.png" alt="KROST">
            </a>
          </div>
  
          <div class="th-mobile-menu">
            <ul>
              <li class="menu-item-has-children mega-menu-wrap">
                <a href="#">Home</a>
                <ul class="mega-menu">
                  <li>
                    <a href="#">Multipage</a>
                    <ul>
                      <li>
                        <a href="index.html">Digital Agency</a>
                      </li>
                      <li>
                        <a href="home-web-development.html">Web Development</a>
                      </li>
                      <li>
                        <a href="home-software-company.html">Software Company</a>
                      </li>
                      <li>
                        <a href="home-software-company-2.html">Software Company Style 2</a>
                      </li>
                      <li>
                        <a href="home-startup-company.html">Startup Company</a>
                      </li>
                      <li>
                        <a href="home-it-solution.html">IT Solution</a>
                      </li>
                      <li>
                        <a href="home-web-agency.html">Web Agency</a>
                      </li>
                      <li>
                        <a href="home-startup.html">Home Startup </a>
                      </li>
                      <li>
                        <a href="home-game-solution.html"> Game Solution</a>
                      </li>
                      <li>
                        <a href="home-sass-marketing.html">Sass Marketing</a>
                      </li>
                      <li>
                        <a href="home-sass-landing.html">Sass Landing</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Multipage</a>
                    <ul>
                      <li>
                        <a href="home-sass-landing-2.html">Sass Landing Style 2</a>
                      </li>
                      <li>
                        <a href="home-app-landing.html">App Landing</a>
                      </li>
                      <li>
                        <a href="home-ai-technology.html">AI Technology</a>
                      </li>
                      <li>
                        <a href="home-cyber-security.html">Cyber Security</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-2.html">Cyber Security Style 2 </a>
                      </li>
                      <li>
                        <a href="home-it-company.html">IT Company</a>
                      </li>
                      <li>
                        <a href="home-digital-marking.html">Digital Marking</a>
                      </li>
                      <li>
                        <a href="home-it-agency.html">IT Agency</a>
                      </li>
                      <li>
                        <a href="home-crm.html">Home CRM</a>
                      </li>
                      <li>
                        <a href="home-it-consulting.html">IT Consulting</a>
                      </li>
                      <li>
                        <a href="home-help-desk.html">Home Help Desk</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Onepage</a>
                    <ul>
                      <li>
                        <a href="home-digital-agency-op.html">Digital Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-web-development-op.html">Web Development Onepage</a>
                      </li>
                      <li>
                        <a href="home-software-company-op.html">Software Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-software-company-2-op.html">Software Company Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-startup-company-op.html">Startup Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-solution-op.html">IT Solution Onepage</a>
                      </li>
                      <li>
                        <a href="home-web-agency-op.html">Web Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-startup-op.html">Home Startup Onepage </a>
                      </li>
                      <li>
                        <a href="home-game-solution-op.html"> Game Solution Onepage</a>
                      </li>
                      <li>
                        <a href="home-sass-marketing-op.html">Sass Marketing Onepage</a>
                      </li>
                      <li>
                        <a href="home-sass-landing-op.html">Sass Landing Onepage</a>
                      </li>
  
                    </ul>
                  </li>
                  <li>
                    <a href="#">Onepage</a>
                    <ul>
                      <li>
                        <a href="home-sass-landing-2-op.html">Sass Landing Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-app-landing-op.html">App Landing Onepage</a>
                      </li>
                      <li>
                        <a href="home-ai-technology-op.html">AI Technology Onepage</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-op.html">Cyber Security Onepage</a>
                      </li>
                      <li>
                        <a href="home-cyber-security-2-op.html">Cyber Security Style 2 Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-company-op.html">IT Company Onepage</a>
                      </li>
                      <li>
                        <a href="home-digital-marking-op.html"> Digital Marking Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-agency-op.html">IT Agency Onepage</a>
                      </li>
                      <li>
                        <a href="home-crm-op.html">Home CRM Onepage</a>
                      </li>
                      <li>
                        <a href="home-it-consulting-op.html">IT Consulting Onepage</a>
                      </li>
                      <li>
                        <a href="home-help-desk-op.html">Home Help Desk</a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
              <li>
                <a href="about.html">About Us</a>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Services</a>
                <ul class="sub-menu">
                  <li>
                    <a href="service.html">Services</a>
                  </li>
                  <li>
                    <a href="service-details.html">Services Details</a>
                  </li>
                </ul>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Pages</a>
                <ul class="sub-menu">
                  <li class="menu-item-has-children">
                    <a href="#">Shop</a>
                    <ul class="sub-menu">
                      <li>
                        <a href="shop.html">Shop</a>
                      </li>
                      <li>
                        <a href="shop-details.html">Shop Details</a>
                      </li>
                      <li>
                        <a href="cart.html">Cart Page</a>
                      </li>
                      <li>
                        <a href="checkout.html">Checkout</a>
                      </li>
                      <li>
                        <a href="wishlist.html">Wishlist</a>
                      </li>
                    </ul>
                  </li>
                  <li>
                    <a href="team.html">Team</a>
                  </li>
                  <li>
                    <a href="team-details.html">Team Details</a>
                  </li>
                  <li>
                    <a href="project.html">Project</a>
                  </li>
                  <li>
                    <a href="project-details.html">Project Details</a>
                  </li>
                  <li>
                    <a href="gallery.html">Gallery</a>
                  </li>
                  <li>
                    <a href="pricing.html">Pricing</a>
                  </li>
                  <li>
                    <a href="faq.html">Faq Page</a>
                  </li>
                  <li>
                    <a href="error.html">Error Page</a>
                  </li>
                </ul>
              </li>
              <li class="menu-item-has-children">
                <a href="#">Blog</a>
                <ul class="sub-menu">
                  <li>
                    <a href="blog.html">Blog</a>
                  </li>
                  <li>
                    <a href="blog-details.html">Blog Details</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="contact.html">Contact</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
  
      <div class="sticky-wrapper">
        <!-- Main Menu Area -->
        <div class="menu-area">
          <div class="container th-container">
            <div class="row align-items-center justify-content-between ps-50 pe-50">
              <div class="d-flex col-4 align-items-center justify-content-start">
                <nav class="main-menu d-none d-lg-inline-block">
                  <ul>
                    <li class="menu-item-has-children mega-menu-wrap">
                      <a href="#">
                        <span>Products</span>
                      </a>
                      <ul class="mega-menu py-5">
                        <!-- <div class="row justify-content-center"> -->
                        <div class="container th-container">
                          <div class="row">
                            <div class="col-9">
                              <div class="row">
                                <div class="col">
                                  <h5 class=".sub-title">Workstations</h5>
                                  <a href="/shofiul-categories-workstation.html">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>screens</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Screens</h5>
                                  <a href="#">
                                    <span>Screens</span>
                                  </a>
                                  <a href="#">
                                    <span>Acoustic booths</span>
                                  </a>
                                  <a href="#">
                                    <span>Perspex screens</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Desks</h5>
                                  <a href="#">
                                    <span>Executive desks</span>
                                  </a>
                                  <a href="#">
                                    <span>Desk system</span>
                                  </a>
                                  <a href="#">
                                    <span>Modesty panels</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>tables</h5>
                                  <a href="#">
                                    <span>Meeting tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Counter Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Training Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Cafe Tables</span>
                                  </a>
                                  <a href="#">
                                    <span>Coffee Tables</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Storage</h5>
                                  <a href="#">
                                    <span>In-office storage</span>
                                  </a>
                                  <a href="#">
                                    <span>Universal storage</span>
                                  </a>
                                  <a href="#">
                                    <span>steel storage units</span>
                                  </a>
                                  <a href="#">
                                    <span>Shelving</span>
                                  </a>
                                  <a href="#">
                                    <span>Custom joinery</span>
                                  </a>
                                </div>
                              </div>
                              <div class="row mt-50">
                                <div class="col">
                                  <h5>Seating</h5>
                                  <a href="/shofiul-categories-seating.html">
                                    <span>All seating</span>
                                  </a>
                                  <a href="/shofiul-category-products.html">
                                    <span>Task seating</span>
                                  </a>
                                  <a href="#">
                                    <span>executive seating</span>
                                  </a>
                                  <a href="#">
                                    <span>training seating</span>
                                  </a>
                                  <a href="#">
                                    <span>Occasional seating</span>
                                  </a>
                                  <a href="#">
                                    <span>Stools</span>
                                  </a>
                                  <a href="#">
                                    <span>Lounges</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Accessories</h5>
                                  <a href="#">
                                    <span>Computer accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>Power accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>General office
                                      accessories</span>
                                  </a>
                                  <a href="#">
                                    <span>Acoustic solutions</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Training</h5>
                                  <a href="#">
                                    <span>Communication
                                      boards</span>
                                  </a>
                                  <a href="#">
                                    <span>screen partitions</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                                <div class="col">
                                  <h5>Finishes</h5>
                                  <a href="#">
                                    <span>Materials</span>
                                  </a>
                                  <a href="#">
                                    <span>Fabrics</span>
                                  </a>
  
                                </div>
                                <div class="col">
                                  <h5>Counters</h5>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                  <a href="#">
                                    <span>Workstations</span>
                                  </a>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col">
                                  <a href="/categories">View all product Categories</a>
                                </div>
                              </div>
                            </div>
                            <div class="col-3 d-flex flex-column gap-3">
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/book-metting.png" alt="boot-meetting">
                              </div>
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/contact-sells.png" alt="contact-sells">
                              </div>
                              <div class="nav-image">
                                <img src="/${Vvveb.themeBaseUrl}img/navbar-img/request-catalog.png" alt="request-catalog">
                              </div>
                            </div>
                          </div>
                        </div>
                      </ul>
                    </li>
                    <li>
                      <a href="shofiul-projects.html">
                        <span>Projects</span>
                      </a>
                    </li>
                    <li>
                      <a href="tuhin-blogs.html">
                        <span>Blog</span>
                      </a>
                    </li>
                    <li>
                      <a href="/zahidul-about.html">
                        <span>About</span>
                      </a>
                    </li>
                  </ul>
                </nav>
                <button type="button" class="th-menu-toggle d-block d-lg-none">
                  <i class="far fa-bars"></i>
                </button>
              </div>
              <div class="d-flex col-4 align-items-center justify-content-center">
                <div class="header-logo">
                  <a class="icon-masking" href="/">
                    <img src="/${Vvveb.themeBaseUrl}img/logo_black.png" alt="KROST">
                  </a>
                  </a>
                </div>
              </div>
              <div class="d-flex col-4 align-items-center justify-content-end">
                <div class="header-button">
                  <a href="tel:+2586232325" class="h-btn">Buy</a>
                </div>
                <a href="" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                  <img src="/${Vvveb.themeBaseUrl}img/pinboard-icon.svg" alt="Pinboard" style="width: 3rem; height: 3rem;">
                </a>
              </div>
  
            </div>
          </div>
        </div>
      </div>
    </header>
    `,
});

Vvveb.Sections.add("header/hero-section", {
    name: "Hero For White Header",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/hero-2-section.png",
    html: `
  <section class="th-hero-wrapper th-hero gr-bg4 background-image" id="hero" style="background-image: url(/${Vvveb.themeBaseUrl}img/about/about-hero.png);">
    <!--========== Hero For White Header Section Start ==========-->
    <div class="container th-container th-hero-section">
      <div class="row">
        <div class="col-xl-6">

          <div class="th-breadcrumb">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb font-white">

                <li class="breadcrumb-item  font-white">

                  <a href="/" class="font-white">
                    Home
                  </a>


                </li>
                <li class="breadcrumb-item  active font-white">


                  <span class="active font-white" aria-current="page">
                    Contact Sales
                  </span>

                </li>

              </ol>
            </nav>
          </div>

          <div class="th-hero-container gr-bg4">
            <div class="th-hero-style">


              <h1 class="th-hero-title Contact Sales_class">
                Contact Sales
              </h1>

              <span class="th-hero-subtitle">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</span>


              <div class="th-hero-wrapp">

                <div class="position-relative pb-3">
                  <a href="contact.html" class="th-btn text-capitalize">
                    <span class="mr-5">
                      Contact Sales

                    </span>
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </a>

                </div>


              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="th-hero-img">

          </div>
        </div>
      </div>
    </div>
    <!--========== Hero For White Header Section End ==========-->
  </section>
    `,
});

Vvveb.Sections.add("header/hero-category-section", {
    name: "Hero For Category Page",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/hero-category.png",
    html: `
<section class="pt-0">
  <!-- Hero For Category Page Mobile Start -->
  <div class="th-hero-mobile-categories">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-12">
          <div class="th-hero-mobile-categories-indside">
            <h6 class="mb-0">
              <a class="text-decoration-none d-flex justify-content-between align-items-center h6" data-bs-toggle="collapse" href="#collapseMenu" role="button" aria-expanded="false" aria-controls="collapseMenu">
                SEATING
                <i id="toggleIcon" class="fa-solid fa-chevron-down down"></i>
              </a>
            </h6>

            <div class="collapse show" id="collapseMenu">
              <div class="card-body">
                <ul class="list-unstyled mb-0">

                  <li class="">Task Seating</li>
                  <li class="">Execute Seating</li>
                  <li class="">Training Seating</li>
                  <li class="">Occasional Seating</li>
                  <li class="">Stools</li>
                  <li class="">Lounges</li>

                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>  
  <!-- Hero For Category Page Destop Start -->
  <div class="th-hero-wrapper th-hero gr-bg4 background-image" id="hero" style="background-image: url(/${Vvveb.themeBaseUrl}img/category-seating/hero.png);">
    <div class="container th-container th-hero-section">
      <div class="row">
        <div class="col-xl-6">

          <div class="th-breadcrumb">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb font-white">

                <li class="breadcrumb-item  font-white">

                  <a href="/" class="font-white">
                    Home
                  </a>


                </li>
                <li class="breadcrumb-item  font-white">

                  <a href="/" class="font-white">
                    Our Categories
                  </a>


                </li>
                <li class="breadcrumb-item  active font-white">


                  <span class="active font-white" aria-current="page">
                    Seating
                  </span>

                </li>

              </ol>
            </nav>
          </div>

          <div class="th-hero-container gr-bg4">
            <div class="th-hero-style th-hero-style-category">
              <h1 class="th-hero-title">
                SEATING
              </h1>
              <span class="th-hero-subtitle">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</span>

              <div class="th-hero-wrapp">


              </div>
            </div>
            <div class="th-hero-categories-list">
              <div class="th-hero-categories-list-inside">
                <h4>Seating Categories</h4>
                <ul>

                  <li class="">Task Seating</li>
                  <li class="">Execute Seating</li>
                  <li class="">Training Seating</li>
                  <li class="">Occasional Seating</li>
                  <li class="">Stools</li>
                  <li class="">Lounges</li>

                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="th-hero-img">

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    `
});

Vvveb.SectionsGroup["Header Sections"] = [
  "header/header-transparent-section",
  "header/transparent-hero-section",
  "header/header-white-section",
  "header/hero-section",
  "header/hero-category-section"
];

Vvveb.Sections.add("main/categories-slider-section", {
  name: "Categories Slider",
  image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/categories-slider.png",
  html: `
  <section id="home-categories">
  <!--========== Categories Slider Section Start ==========-->
    <div class="container th-container">
      <div class="row">
        <div class="col-12">
          <div class="th-section-header ">
            <div class="th-section-header-wrapper left flex-1">
              <h2 class="  th-section-title "> Product Categories </h2>
              <div class="th-section-subtitle" style="font-size: ">
                Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
              </div>
            </div>

            <div class="right">
              <div class="th-section-header-link">
                <span class="th-section-header-link-text All CategoriesClass">All Categories</span>
                <span class="th-section-header-link-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </span>
              </div>
            </div>

          </div>

        </div>
        <div class="col-12">
          <div class="section-body">
            <div class="row">
              <div class="col-md-3">
                <div class="th-categories-slider-nav">
                  <div class="th-categories-menu">
                    <ul class="th-menu-container">
                      <li class="th-item-has-children th-active">
                        <a href="#">
                          <h6>
                            <span>Workstations</span>
                          </h6>
                        <span class="th-mean-expand"></span></a>
                        <ul class="sub-menu th-submenu th-open" style="">
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Workstations</h6>
                            </a>
                          </li>
                          <li>
                            <a href="service-details.html">
                              <h6 class="th-title-17">Workstations Screens</h6>
                            </a>
                          </li>
                          <li>
                            <div class="th-link ">
                              <div class="th-link-text pr-5">

                                Read More
                              </div>
                              <div class="th-link-icon-btn">
                                <i class="fa-regular fa-arrow-up degree-60"></i>
                              </div>
                            </div>

                          </li>
                        </ul>
                      </li>
                      <li class="th-item-has-children">
                        <a href="#">
                          <h6>
                            <span>Desks</span>
                          </h6>
                        <span class="th-mean-expand"></span></a>
                        <ul class="sub-menu th-submenu" style="display: none;">
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Desks</h6>
                            </a>
                          </li>
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Desks System</h6>
                            </a>
                          </li>
                          <li>
                            <div class="th-link ">
                              <div class="th-link-text pr-5">

                                Read More
                              </div>
                              <div class="th-link-icon-btn">
                                <i class="fa-regular fa-arrow-up degree-60"></i>
                              </div>
                            </div>

                          </li>
                        </ul>
                      </li>
                      <li class="th-item-has-children">
                        <a href="#">
                          <h6>
                            <span>Tables</span>
                          </h6>
                        <span class="th-mean-expand"></span></a>
                        <ul class="sub-menu th-submenu" style="display: none;">
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Tables</h6>
                            </a>
                          </li>
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Boardroom Tables</h6>
                            </a>
                          </li>
                          <li>
                            <div class="th-link ">
                              <div class="th-link-text pr-5">

                                Read More
                              </div>
                              <div class="th-link-icon-btn">
                                <i class="fa-regular fa-arrow-up degree-60"></i>
                              </div>
                            </div>

                          </li>
                        </ul>
                      </li>
                      <li class="th-item-has-children">
                        <a href="#">
                          <h6>
                            <span>Seating</span>
                          </h6>
                        <span class="th-mean-expand"></span></a>
                        <ul class="sub-menu th-submenu" style="display: none;">
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Seating</h6>
                            </a>
                          </li>
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Boardroom Tables</h6>
                            </a>
                          </li>
                          <li>
                            <div class="th-link ">
                              <div class="th-link-text pr-5">

                                Read More
                              </div>
                              <div class="th-link-icon-btn">
                                <i class="fa-regular fa-arrow-up degree-60"></i>
                              </div>
                            </div>

                          </li>
                        </ul>
                      </li>
                      <li class="th-item-has-children">
                        <a href="#">
                          <h6>
                            <span>Storage</span>
                          </h6>
                        <span class="th-mean-expand"></span></a>
                        <ul class="sub-menu th-submenu" style="display: none;">
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Storage</h6>
                            </a>
                          </li>
                          <li>
                            <a href="service.html">
                              <h6 class="th-title-17">Boardroom Tables</h6>
                            </a>
                          </li>
                          <li>
                            <div class="link">
                              <div class="link-text pr-5">View All</div>
                              <div class="link-icon-btn">
                                <i class="fa-regular fa-arrow-up degree-60"></i>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <div class="slider-container home-category-slider">
                  <div class="slider-area">
                    <div class="swiper mySwiper swiper-creative swiper-3d swiper-initialized swiper-horizontal swiper-watch-progress">
                      <div class="swiper-wrapper" id="swiper-wrapper-fe45d9765bd1881c" aria-live="polite" style="cursor: grab;">
                        <div class="swiper-slide gr-bg6 swiper-slide-visible swiper-slide-fully-visible swiper-slide-active" role="group" aria-label="1 / 3" style="width: 1134px; z-index: 3; transform: translate3d(calc(0px), calc(0px), calc(0px)) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1); opacity: 1; margin-right: 30px;">
                          <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_cat_workstation.jpg" alt="Workstation">
                          <div class="slider-content gr-bg5 slideinleft" data-ani="slideinleft" data-ani-delay=".1s" style="animation-delay: 0.1s;">
                            <h4 class="title font-white slideinleft" data-ani="slideinleft" data-ani-delay=".2s" style="animation-delay: 0.2s;">
                              Workstation
                            </h4>
                            <h6 class="th-title-20 font-white pb-15 slideinleft" data-ani="slideinleft" data-ani-delay="0.3s" style="animation-delay: 0.3s;">
                              Energistically harness
                            </h6>
                            <div class="btn-group slideinleft" data-ani="slideinleft" data-ani-delay="0.4s" style="animation-delay: 0.4s;">
                              <div class="position-relative">
                                <a href="contact.html" class="th-btn-outline text-capitalize">
                                  <span class="mr-5">Contact Sales</span>
                                  <i class="fa-regular fa-arrow-up degree-60"></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="swiper-slide gr-bg6 swiper-slide-next" role="group" aria-label="2 / 3" style="width: 1134px; z-index: 2; transform: translate3d(calc(100% - 1164px), calc(0px), calc(0px)) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1); opacity: 1; margin-right: 30px;">
                          <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_cat_chair.jpg" alt="Chair">
                          <div class="slider-content gr-bg5 slideinleft" data-ani="slideinleft" data-ani-delay=".1s" style="animation-delay: 0.1s;">
                            <h4 class="font-white slideinleft" data-ani="slideinleft" data-ani-delay=".2s" style="animation-delay: 0.2s;">
                              Chair
                            </h4>
                            <h6 class="th-title-20 font-white pb-15 slideinleft" data-ani="slideinleft" data-ani-delay=".3s" style="animation-delay: 0.3s;">
                              Energistically harness
                            </h6>
                            <div class="btn-group slideinleft" data-ani="slideinleft" data-ani-delay=".4s" style="animation-delay: 0.4s;">
                              <div class="position-relative">
                                <a href="contact.html" class="th-btn-outline text-capitalize">
                                  <span class="mr-5">Contact Sales</span>
                                  <i class="fa-regular fa-arrow-up degree-60"></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="swiper-slide gr-bg6" role="group" aria-label="3 / 3" style="width: 1134px; z-index: 1; transform: translate3d(calc(100% - 2328px), calc(0px), calc(0px)) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1); opacity: 1; margin-right: 30px;">
                          <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_cat_seating.jpg" alt="Seating">
                          <div class="slider-content gr-bg5 slideinleft" data-ani="slideinleft" data-ani-delay=".1s" style="animation-delay: 0.1s;">
                            <h4 class="title font-white slideinleft" data-ani="slideinleft" data-ani-delay=".2s" style="animation-delay: 0.2s;">
                              Seating
                            </h4>
                            <h6 class="th-title-20 font-white pb-15 slideinleft" data-ani="slideinleft" data-ani-delay=".3s" style="animation-delay: 0.3s;">
                              Energistically harness
                            </h6>
                            <div class="btn-group slideinleft" data-ani="slideinleft" data-ani-delay=".4s" style="animation-delay: 0.4s;">
                              <div class="position-relative">
                                <a href="contact.html" class="th-btn-outline text-capitalize">
                                  <span class="mr-5">Contact Sales</span>
                                  <i class="fa-regular fa-arrow-up degree-60"></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide" aria-controls="swiper-wrapper-fe45d9765bd1881c" aria-disabled="false"></div>
                      <div class="swiper-button-prev swiper-button-disabled" tabindex="-1" role="button" aria-label="Previous slide" aria-controls="swiper-wrapper-fe45d9765bd1881c" aria-disabled="true"></div>
                      <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1" aria-current="true"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 3"></span></div>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--========== Categories Slider Section End ==========-->
  </section>
  `,
});

Vvveb.Components.add("main/block-quote-section", {
    name: "Block Quote Section",
    image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/block-quote-section.png",
    html: `
     <section id="th-block-quote" class="container th-container">
      <!--========== Block Quote Section Start ==========-->
      <div class="row">
        <div class="col-md-12">
          <div class="th-block-quote">
            <p>
              Our Legacy Isn’t Only About Refined And Functional Furniture, Fit For Australian Organizations,
              It’s About The Trust We’ve Fostered For Decades
            </p>
            <span class="th-quote-author">-
              <img src="/${Vvveb.themeBaseUrl}img/logo_black.png" alt="krost-logo">
            </span>
          </div>
        </div>
      </div>
      <!--========== Block Quote Section End ==========-->
    </section>
    `,
    properties: [
      {
        name: "BackgroundClasses",
        key: "kr-bg-classes",
        inputtype: SelectInput,
        htmlAttr: "class",
        validValues: [
          "bg-gray",
          "bg-white"
        ],
        data: {
          options: [
            {
              value: "bg-gray",
              text: "Gray Background"
            },
            {
               value: "bg-white",
              text: "White Background"
            }
          ]
        }
      }
    ]
});

Vvveb.Sections.add("main/need-help-section", {
  name: "Need Help Section",
  image:  Vvveb.themeBaseUrl +"screenshots/sections/main/need-help-section.png",
  html: `
  <section id="need-help-section">
    <!--========== Need Help Section Start ==========-->
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header " style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h2 class="  th-section-title "> Need Help? </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

        </div>

        <div class="section-body">
          <div class="col-md-12">
            <div class="th-need-help-container">
              <div class="th-item-help">
                <div class="th-btn-circle-icon">
                  <i class="fa-solid fa-phone"></i>
                </div>

                <h6 class="font-weight-700">Any Questions?</h6>
                  <p class="th-description my-15">Visit our showrooms, call or email us!</p>
                  <div class="th-link">
                    <div class="th-link-text pr-5">Contact Us</div>
                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>
                  </div>

              </div>
              <div class="th-item-help">
                <div class="th-btn-circle-icon">
                  <i class="fa-duotone fa-book-open-cover"></i>
                </div>

                <h6 class="font-weight-700">See Our Latest Collection?</h6>
                  <p class="th-description my-15">Check out our latest projects</p>
                  <div class="th-link">
                    <div class="th-link-text pr-5">View Catalogue</div>
                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>
                  </div>

              </div>
              <div class="th-item-help">
                <div class="th-btn-circle-icon">
                  <i class="fa-solid fa-lightbulb"></i>
                </div>

                <h6 class="font-weight-700">Need some inspiration?</h6>
                  <p class="th-description my-15">Check out our latest projects</p>
                  <div class="th-link">
                    <div class="th-link-text pr-5">View Projects</div>
                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>
                  </div>

              </div>
              <div class="th-item-help">
                <div class="th-btn-circle-icon">
                  <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <h6 class="font-weight-700">Need something now?</h6>
                  <p class="th-description my-15">Visit our online store!</p>
                  <div class="th-link">
                    <div class="th-link-text pr-5">Shop Now</div>
                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>
                  </div>

              </div>
              <div class="th-item-help">
                <div class="th-btn-circle-icon">
                  <i class="fa-solid fa-360-degrees"></i>
                </div>

                <h6 class="font-weight-700">Can’t make it to our showrooms?</h6>
                  <p class="th-description my-15">Visit our online store!</p>
                  <div class="th-link">
                    <div class="th-link-text pr-5">Take a Tour</div>
                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>
                  </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--========== Need Help Section End ==========-->
  </section>
  `
});

Vvveb.Sections.add("main/blank", {
  name: "Blank Section",
  image: Vvveb.themeBaseUrl + "screenshots/sections/main/blank-section.png",
  html:`
  <section class="section">
    <div class="container th-container " style="min-height:150px;"></div>
  </section>
  `
})

Vvveb.SectionsGroup["Main Sections"] = [
  "main/categories-slider-section",
  "main/need-help-section",
  "main/blank"
];


Vvveb.Sections.add("footer/footer-section", {
  name: "Footer Section",
  image:  Vvveb.themeBaseUrl +"/screenshots/sections/main/footer-section.png",
  html: `
  <footer class="th-footer bg-gray" title="footer" data-v-save-global="index.html,.footer-1">
    <!--========== Footer Section Start ==========-->
    <section class="container th-container" data-v-component-menu="footer" data-v-slug="main-footer">
      <div class="row" data-v-menu-items="">
        <div class="col-lg-6 border-right-dark border-right-md-0">
          <h4>Contact Us</h4>
          <div class="d-grid grid-col-2">
            <div class="d-flex align-items-center text-weight-600 pt-10">
              <i class="fa-solid fa-envelope pr-5"></i>
              <p>sales@krost.com.au</p>
            </div>
            <div class="d-flex align-items-center text-weight-600 pt-10">
              <i class="fa-solid fa-phone pr-5"></i>
              <p>1800 1KROST</p>
            </div>

            <div class="th-showroom-address">
              <p class="text-weight-600">Sydney Office</p>
              <p>33 Ricketty Street</p>
              <p>Mascot NSW, 2020</p>
              <div class="d-flex align-items-center text-weight-600 pt-10">
                <i class="fa-solid fa-phone pr-5"></i>
                <p>02 9557 3055</p>
              </div>
              <p>Open weekdays, 8am to 5pm</p>
            </div>
            <div class="th-showroom-address">
              <p class="text-weight-600">Melbourne Office</p>
              <p>17-643 spencer st</p>
              <p>West Melbourne VIC, 3003</p>
              <div class="d-flex align-items-center text-weight-600 pt-10">
                <i class="fa-solid fa-phone pr-5"></i>
                <p>03 9682 8280</p>
              </div>
              <p>open weekdays, 9am to 5pm</p>
            </div>

          </div>
        </div>
        <div class="col-lg-6">
          <div class="th-subscription pl-50 pl-md-0">
            <div class="th-subscription-heading">
              <div class="th-subscription-icon">
                <i class="fa-thin fa-envelope"></i>
              </div>
              <div class="th-subscription-details">
                <div class="sub-title m-0">Get krost product updates</div>
                <div class="description">Receive the latest news &amp; updates from Krost</div>
              </div>
            </div>
            <div class="th-subscription-form">
              <label class="pr-15">
                <input type="text" placeholder="Your Email Address Please">
              </label>
              <div class="link">
                <div class="link-text pr-5">Subscribe Now</div>
                <div class="link-icon">
                  <i class="fa-regular fa-arrow-right"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="th-footer-navigation">
            <ul>

              <li class="pl-0">
                <span class="link pl-0">
                  <a href="#"> Our Store</a>
                </span>
              </li>
              <li class="">
                <span class="link ">
                  <a href="#"> Visit Us</a>
                </span>
              </li>
              <li class="border-right-0">
                <span class="link border-right-0">
                  <a href="#"> Contact Us</a>
                </span>
              </li>

            </ul>
          </div>

          <div class="th-footer-follow pt-30">
            <p class="text-weight-600">Follow Us:</p>
            <div class="social-media">
              <ul>

                <li>
                  <span class="link">
                    <a href="https://www.linkedin.com/company/krost-furniture/">
                      <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                  </span>
                </li>
                <li>
                  <span class="link">
                    <a href="https://www.facebook.com/krostfurniture/">
                      <i class="fa-brands fa-facebook-f"></i>
                    </a>
                  </span>
                </li>
                <li>
                  <span class="link">
                    <a href="https://www.instagram.com/krostfurniture/">
                      <i class="fa-brands fa-instagram"></i>
                    </a>
                  </span>
                </li>
                <li>
                  <span class="link">
                    <a href="https://www.pinterest.com/krostfurniture/">
                      <i class="fa-brands fa-pinterest-p"></i>
                    </a>
                  </span>
                </li>

              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="th-footer-copyright mt-20">
      <div class="container th-container">
        <div class="d-flex flex-column flex-md-row">
          <div class="text-muted flex-grow-1">
            <a class="btn-link text-muted" href="content/page.html">Terms and conditions</a> |
            <a class="btn-link text-muted" href="content/page.html">Privacy Policy</a>
          </div>
          <div class="text-muted">
            © <span data-v-year="">2025</span>
            <span data-v-global-site.description.title="">KROST</span>. <span>Powered by</span>
            <a href="https://krost.com.au" class="btn-link text-muted" target="_blank">KROST</a>
          </div>
        </div>
      </div>
    </div>
    <!--========== Footer Section End ==========-->
  </footer>`
});

Vvveb.SectionsGroup["Footer Sections"] = [
  "footer/footer-section"
];