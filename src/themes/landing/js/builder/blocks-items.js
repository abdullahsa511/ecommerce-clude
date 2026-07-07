Vvveb.Blocks.add("krost/item-about-masonry-large", {
  name: "About Masonry Large Item",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/about-masonry-large.png",
  html: /*html*/ `
    <div class="th-masonry-grid-item grid-col-span-7 " style="transform: translateY(0 px);padding-top:0 px">
        <div class="th-item-img">
        <img src="/${Vvveb.themeBaseUrl}img/about/gallery-image1.png" alt="Product 1">
        </div>
        <div class="th-product-info-wrapper">
        <div class="th-item-info">
            <h3 class="withOutArrow">World-class product display</h3>
            <p class="th-item-description">A full collection of workstations from leg-based systems to panel constructions and height-adjustable offerings. Find the perfect configuration and aesthetic for your space.</p>
            <p class="th-category-items">

            </p>
        </div>
        <div class="th-link">
            <div class="th-link-text pr-5"> View all World-class product display </div>
            <div class="th-link-icon">
            <i class="fa-regular fa-arrow-right"></i>
            </div>
        </div>

        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-about-masonry-small", {
  name: "About Masonry Large Small",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/about-masonry-small.png",
  html: /*html*/ `
    <div class="th-masonry-grid-item grid-col-span-6 " style="transform: translateY(49 px);padding-top:0 px">
          <div class="th-item-img">
            <img src="/${Vvveb.themeBaseUrl}img/about/gallery-image2.png" alt="Product 1">
          </div>
          <div class="th-product-info-wrapper">
            <div class="th-item-info">
              <h3 class="withOutArrow">unparalleled service</h3>
              <p class="th-item-description">Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis</p>
              <p class="th-category-items">

              </p>
            </div>
            <div class="th-link">
              <div class="th-link-text pr-5"> View all unparalleled service </div>
              <div class="th-link-icon">
                <i class="fa-regular fa-arrow-right"></i>
              </div>
            </div>

          </div>
        </div>
    `,
});
Vvveb.Blocks.add("krost/item-blog-slider", {
  name: "Blog Slider Item",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-blog-slider.png",
  html: /*html*/ `
    <div class="swiper-slide">
        <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-1.jpg">
        <div class="add-to-pinboard position-absolute top-right-30">
            <i class="fa-solid fa-plus"></i>
        </div>
        <div class="slider-footer">

            <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
            <p class="th-blog-slider-description">
            Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </p>

            <div class="link">
            <div class="link-text pr-5">Read More</div>
            <div class="link-icon-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
            </div>
            </div>
        </div>
    </div>`,
});
Vvveb.Blocks.add("krost/item-blog", {
  name: "Blog Item",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-blog.png",
  html: /*html*/ `
    <div class="th-item-blog">
        <div class="th-list-img position-relative">
            <div class="th-img-container">
                <img src="/${Vvveb.themeBaseUrl}img/blog-page/News 1.png" alt="">
            </div>
            <div class="add-to-pinboard position-absolute top-right-30">
                <i class="fa-solid fa-plus"></i>
            </div>
        </div>
        <div class="card-footer">
        <h3 class="font-weight-400 th-title">Krost's Sydney Office Update With 3d Tour</h3>
        <div class="link th-read-more-btn">
            <div class="link-text pr-5">Read More</div>
            <div class="link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
            </div>
        </div>
        </div>
    </div>
    `,
  properties: [
    {
      name: "Title",
      key: "title",
      htmlAttr: "data-title",
      inputtype: TextInput,
    },
  ],
});
Vvveb.Blocks.add("krost/item-card", {
  name: "Item Standard",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-card.png",
  html: /*html*/ `
    <div class="th-item-card">
        <div class="th-img-container">
            <img src="/${Vvveb.themeBaseUrl}img/product-detail/feature img 1.png" alt="Adjustable Ergonomics">
        </div>
        <div class="th-item-card-content mt-15">
            <div class="th-link mb-10">
            <h6 class="th-link-text pr-5 th-title-20">Adjustable Ergonomics</h6>

            </div>
            <div class="th-description">
            Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-featured-material-slider", {
  name: "Item Featured Material Slider",
  image:
    Vvveb.themeBaseUrl + "screenshots/blocks/item-featured-material-slider.png",
  html: /*html*/ `
    <div class="swiper-slide">
        <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 1.png">
        <div class="slider-footer">
            <p>Finish</p>
            <h3 class="title">ABBEY</h3>
            <span>Lorem Ipsum</span>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-government-large", {
  name: "Item Government Supplier Large",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-government-large.png",
  html: /*html*/ `
    <div class="th-grid-area th-grid-area-1-1-7-6 background-image" 
        style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplyar-img1.png);">
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-government-small", {
  name: "Item Government Supplier Small",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-government-small.png",
  html: /*html*/ `
    <div class="th-grid-area th-grid-area-3-5-8-7 background-image" 
        style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplayer-img2.png);">
    </div>
      `,
});
Vvveb.Blocks.add("krost/item-government-details", {
  name: "Item Government Supplier Details",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-government-details.png",
  html: /*html*/ `
    <div class="th-grid-area th-grid-area-7-1-11-5">
        <h2 class="">Government supplier</h2>
        <h6 class="th-title-22 font-weight-400">
            Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
        </h6>
        <div class="th-link @@classPadding">
            <div class="th-link-text pr-5">
                Location
            </div>
            <div class="th-link-icon-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-environment-policy-details", {
  name: "Item Environment Policy Details",
  image:
    Vvveb.themeBaseUrl +
    "screenshots/blocks/item-environment-policy-details.png",
  html: /*html*/ `
    <div class="th-grid-area th-grid-area-8-5-11-7">
        <h5 class="">Environmental policy</h5>
        <h6 class="th-title-22 font-weight-400">
            Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
        </h6>
        <div class="th-link @@classPadding">
            <div class="th-link-text pr-5">
            Read more

            </div>
            <div class="th-link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-help-card", {
  name: "Item Help",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-help.png",
  html: /*html*/ `
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
    `,
});
Vvveb.Blocks.add("krost/item-img-circle", {
  name: "Item Circle Image",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-img-circle.png",
  html: /*html*/ `
    <div class="th-item-img-card d-flex flex-column align-items-center">
        <div class="th-img-container">
            <img src="/${Vvveb.themeBaseUrl}img/product-detail/first circle.png" alt="" class="th-rounded-circle mb-3" style="max-width: 164px;">
        </div>
        <div class="th-item-img-content mt-30">
        <h6 class="th-title-22 font-weight-700">Shop Archi</h6>
            <div class="th-link mt-30">
            <a href="#" class="th-link-text pr-5 ">
                <div class="th-link-text pr-5 ">Buy Now</div>
                <div class="th-link-icon-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
            </a>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-instagram-slider", {
  name: "Item Instagram Slider",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-instagram-slider.png",
  html: /*html*/ `
    <div class="swiper-slide">
        <div class="th-item-card">
            <div class="th-instagram-img-container background-image" style="background-image: url(/${Vvveb.themeBaseUrl}img/product-detail/insta-1.png);">
                <div class="th-item-card-content">
                <div class="th-instagram-link">
                    <a href="https://www.instagram.com/archi_furniture/" target="_blank">
                    <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-member-card", {
  name: "Item Member Card",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-member-card.png",
  html: /*html*/ `
    <div class="th-member-wrapper col-sm-6 col-lg-3">
        <div class="th-member background-image" style="background-image: url(/${Vvveb.themeBaseUrl}img/contact/member-0.jpg);">
        <div class="th-member-icons">
            <span>
            <i class="fa-solid fa-calendar"></i>
            </span>
            <span>
            <i class="fa-solid fa-envelope"></i>
            </span>
            <span>
            <i class="fa-solid fa-phone"></i>
            </span>
        </div>
        <div class="th-member-info-container gr-bg6">
            <div class="th-member-info">
            <p class="th-member-name">Devon Lane</p>
            <p class="th-member-position">Director</p>
            </div>
        </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-number-card", {
  name: "Item Number Card",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-number-card.png",
  html: /*html*/ `
    <div class="th-item-number-card card h-100 shadow-none border-0">
        <div class="th-item-number-card-container">
        <div class="th-item-card-number d-flex align-items-center">
            <h3 class="display-1 fw-bold">01</h3>
            <h5 class="mb-30">
            OFFER THE BEST SERVICE
            </h5>
        </div>
        <p class="th-item-number-card-text">
            Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.
        </p>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-product", {
  name: "Item Product",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-product.png",
  html: /*html*/ `
    <div class="th-item-product">
        <div class="th-img-container">
            <img src="/img/products/chair.png ">
        </div>
        <div class="th-add-to-pinboard position-absolute top-right-30 ">
            <i class="fa-solid fa-plus"></i>
        </div>
        <div class="th-item-footer">
        <!-- <div class="label">Build</div> -->
        <h3 class="th-title mt-25">Miro </h3>
        <p class="th-description mb-10">A comfortable and versatile chair option for various environments.</p>
        <div class="th-tag-name">
            <div class="th-tag">AFRDI Certified</div>
            <div class="th-tag">OBP Certified</div>
        </div>
        <div class="th-item-finish-circle">
            <div class="th-circle black-fabric">Black Fabric</div>
            <div class="th-circle black-premium">Black Premium Polyurethane</div>
            <div class="th-circle mocha-premium">Mocha Premium Polyurethane</div>
            <div class="th-circle white background-image" style="background-image: url(&quot;/img/finishes/finish-2.jpg&quot;);">Cream Premium Polyurethane</div>
        </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-project", {
  name: "Item Project",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-project.png",
  html: /*html*/ `
    <div class="th-item-project">
        <div class="th-img-container th-list-img">
        <img src="/${Vvveb.themeBaseUrl}img/projects/p-chwyla.png">
        <div class="th-add-to-pinboard position-absolute top-right-30">
            <i class="fa-solid fa-plus"></i>
        </div>

        </div>
        <div class="th-item-footer">
        <div class="th-label">build</div>
        <h3 class="th-title">Chwyla</h3>
        <div class="th-description">Lorem ipsum dolor sit amet consectetur.</div>
        <div class="th-link @@classPadding">
            <div class="th-link-text pr-5">

            Read More
            </div>
            <div class="th-link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
            </div>
        </div>

        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-resource", {
  name: "Item Resource",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-resource.png",
  html: /*html*/ `
    <div class="th-item-resource">
        <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_dr-fabrics.jpg" alt="Model Library">
        <div class="th-item-resource-content">
            <div class="th-link">
            <a href="@@link">
                <h6 class="th-title-20 font-weight-700"> Model Library </h6>
            </a>
            </div>
            <div class="description">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-showroom", {
  name: "Item Showroom",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-showroom.png",
  html: /*html*/ `
    <div class="th-item-showroom pb-0">
        <div class="th-img-container">
            <img src="/${Vvveb.themeBaseUrl}img/contact/contact-location-one.jpeg " alt="Showroom Location">
        </div>
        <div class="th-item-showroom-information">
            <h3 class="title th-showroom-title">Sydney Showroom </h3>
            <p class="showroom-opening font-weight-500">Open Weekdays, 8am to 5pm</p>
            <div class="showroom-address-container d-flex align-items-center mt-5">
                <i class="fa-solid fa-location pr-5"></i>
                <p class="showroom-address">33 Ricketty Street Mascot NSW, 2020</p>
            </div>
            <div class="th-call-to-action d-flex mt-30">
                <div class="link pr-40">
                    <div class="link-text pr-5">Book a Tour </div>
                    <div class="link-icon">
                        <i class="fa-regular fa-arrow-right"></i>
                    </div>
                </div>
                <div class="link">
                    <div class="link-text pr-5">View On Map </div>
                    <div class="link-icon">
                        <i class="fa-regular fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-showroom-virtual", {
  name: "Item Showroom Virtual",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-showroom-virtual.png",
  html: /*html*/ `
    <div class="th-item-showroom th-item-showroom-virtual pb-0">
        <div class="th-img-container">
            <img src="/${Vvveb.themeBaseUrl}img/contact-us/explore-1.png" alt="Showroom Location">
        </div>
        <div class="th-item-showroom-information">
            <h3 class="title th-showroom-title">Sydney Showroom </h3>
            <p class="showroom-opening font-weight-500">Open Weekdays, 8am to 5pm</p>
            <div class="showroom-address-container d-flex align-items-center mt-5">
                <i class="fa-solid fa-location pr-5"></i>
                <p class="showroom-address">33 Ricketty Street Mascot NSW, 2020</p>
            </div>
            <div class="th-call-to-action d-flex mt-30">
                <div class="link pr-40">
                    <div class="link-text pr-5">Book a Tour </div>
                    <div class="link-icon">
                        <i class="fa-regular fa-arrow-right"></i>
                    </div>
                </div>
                <div class="link">
                    <div class="link-text pr-5">View On Map </div>
                    <div class="link-icon">
                        <i class="fa-regular fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `,
});
Vvveb.Blocks.add("krost/item-number", {
  name: "Item Number Card",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/card-item.png",
  html: /*html*/ `
    <div class="th-item-number-card-container">
        <div class="th-item-number-card-content">
    </div>
    `,
});
Vvveb.Blocks.add("korst/item-categories-masonry-large", {
  name: "Item Categories Masonry Large",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-categories-masonry.png",
  html: /*html*/ `
  <div class="th-masonry-grid-item grid-col-span-8 " style="transform: translateY(0px); padding-top: 0px">
    <div class="th-item-img">
      <img src="/${Vvveb.themeBaseUrl}img/categories/workstations.png" alt="Product 1">
    </div>
    <div class="th-product-info-wrapper">
      <div class="th-item-info">
        <h6 class="withOutArrow">Workstations</h6>
        <p class="th-item-description">A full collection of workstations from leg-based systems to panel constructions and height-adjustable offerings. Find the perfect configuration and aesthetic for your space.</p>
        <div class="th-category-items">
          <p class="">Workstations</p>
          <p class="">Workstation screens</p>
          <p class="">test-workstations</p>
        </div>
      </div>
      <div class="th-link">
        <div class="th-link-text pr-5">
          Read More
        </div>
        <div class="th-link-icon-btn">
          <i class="fa-regular fa-arrow-up degree-60"></i>
        </div>
      </div>
    </div>
  </div>
  `,
});
Vvveb.Blocks.add("korst/item-categories-masonry-small", {
  name: "Item Categories Masonry Small",
  image:
    Vvveb.themeBaseUrl + "screenshots/blocks/item-category-masonry-small.png",
  html: /*html*/ `
  <div class="th-masonry-grid-item grid-col-span-5 " style="transform: translateY(-187px); padding-top: 0px">
    <div class="th-item-img">
      <img src="/${Vvveb.themeBaseUrl}img/categories/desks.png" alt="Product 1">
    </div>
    <div class="th-product-info-wrapper">
      <div class="th-item-info">
        <h6 class="withOutArrow">Desks</h6>
        <p class="th-item-description">Tailored to cater to the high-performance executive. From single desk modules to integrated storage solutions, select your style.</p>
        <div class="th-category-items">
          <p class="">Executive desks</p>
          <p class="">Desk system</p>
          <p class="">Modesty panels</p>
        </div>
      </div>
      <div class="th-link">
        <div class="th-link-text pr-5">
          Read More
        </div>
        <div class="th-link-icon-btn">
          <i class="fa-regular fa-arrow-up degree-60"></i>
        </div>
      </div>
    </div>
  </div>
  `,
});
Vvveb.Blocks.add("krost/item-project-masonry", {
  name: "Item Project Masonry",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-project-masonry-lg.png",
  html: /*html*/ `
  <div class="th-masonry-grid-item grid-col-span-6 " style="transform: translateY(0 px);padding-top:0 px">
  <div class="th-item-img">
    <img src="/${Vvveb.themeBaseUrl}img/projects/hyundai.png" alt="Product 1">
  </div>
  <div class="th-product-info-wrapper">
    <div class="th-item-info">
      <h3 class="th-title">
        <div class="th-link">
          <div class="th-link-text pr-5"> hyundai office: stage 2 </div>
          <div class="th-link-icon">
            <i class="fa-regular fa-arrow-right"></i>
          </div>
        </div>

      </h3>
      <p class="th-item-description">
        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
      </p>
    </div>
  </div>
</div>
    `,
});

Vvveb.Blocks.add("krost/item-project-masonry-small", {
  name: "Item Project Masonry Small",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-project-masonry.png",
  html: /*html*/ `
  <div class="th-masonry-grid-item grid-col-span-6 " style="transform: translateY(0 px);padding-top:0 px">
        <div class="th-item-img">
            <img src="/${Vvveb.themeBaseUrl}img/projects/hyundai.png" alt="Product 1">
        </div>
        <div class="th-product-info-wrapper">
            <div class="th-item-info">
            <h3 class="th-title">
                <div class="th-link">
                <div class="th-link-text pr-5"> hyundai office: stage 2 </div>
                <div class="th-link-icon">
                    <i class="fa-regular fa-arrow-right"></i>
                </div>
                </div>

            </h3>
            <p class="th-item-description">
                Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </p>
        </div>
  </div>
</div>
      `,
});

Vvveb.Blocks.add("krost/item-product-masonry", {
  name: "Item Product Masonry",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-product-masonry-lg.png",
  html: /*html*/ `
<div class="th-masonry-grid-item grid-col-span-7 " style="transform: translateY(0 px);padding-top:0 px">
    <div class="th-item-img">
        <img src="/img/product-detail/Comfort Meets Support.png" alt="Product 1">
    </div>
    <div class="th-product-info-wrapper">
        <div class="th-item-info">
            <h6 class="th-title-17">
                <div class="th-link">
                <h6 class="th-title-20 font-weight-700"> Where comfort meets support </h6>

                <div class="th-link-icon">
                    <i class="fa-regular fa-arrow-right"></i>
                </div>

                </div>

            </h6>
            <p class="item-description">
                Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </p>
            <div class="th-link pt-10">
                <div class="th-link-text pr-5">

                Read More
                </div>
                <div class="th-link-icon-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
            </div>
        </div>
    </div>
</div>
 `,
});

Vvveb.Blocks.add("krost/item-product-masonry-small", {
  name: "Item Product Masonry small",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-product-masonry.png",
  html: /*html*/ `
    <div class="th-masonry-grid-item grid-col-span-7 " style="transform: translateY(0 px);padding-top:0 px">
    <div class="th-item-img">
      <img src="/${Vvveb.themeBaseUrl}img/product-detail/Comfort Meets Support.png" alt="Product 1">
    </div>
    <div class="th-product-info-wrapper">
      <div class="th-item-info">
        <h6 class="th-title-17">
          <div class="th-link">
            <h6 class="th-title-20 font-weight-700"> Where comfort meets support </h6>

            <div class="th-link-icon">
              <i class="fa-regular fa-arrow-right"></i>
            </div>

          </div>

        </h6>
        <p class="item-description">
          Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
        </p>
        <div class="th-link pt-10">
          <div class="th-link-text pr-5">

            Read More
          </div>
          <div class="th-link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
          </div>
        </div>

      </div>
    </div>
  </div>
      `,
});

Vvveb.Blocks.add("krost/item-feature-project-slider", {
  name: "Item Feature Project Slider",
  image:
    Vvveb.themeBaseUrl + "screenshots/blocks/item-feature-project-slider.png",
  html: /*html*/ `
  <div class="swiper-slide" role="group" aria-label="3 / 4" style="width: 392.121px; margin-right: 20px;">
  <div class="th-item-project">
    <div class="th-img-container ">
      <img src="/img/bg/home/home_feature_project_3.jpg ">
    </div>
    <div class="th-add-to-pinboard position-absolute top-right-30 ">
      <i class="fa-solid fa-plus"></i>
    </div>
    <div class="th-item-footer">
      <div class="th-label">Merit Interiors</div>
      <h3 class="th-title">Berry Street</h3>
      <div class="th-description">This is the third project description.</div>
      <div class="th-link">
        <div class="th-link-text pr-5">

          Read More
        </div>
        <div class="th-link-icon-btn">
          <i class="fa-regular fa-arrow-up degree-60"></i>
        </div>
      </div>

    </div>
  </div>
</div>
      `,
});
Vvveb.Blocks.add("krost/item-finishes-card", {
  name: "Item Finishes Card",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-finishes.png",
  html: /*html*/ `
    <div class="swiper-slide" role="group" aria-label="3 / 4" style="width: 392.121px; margin-right: 20px;">
    <div class="th-item-project">
      <div class="th-img-container @@itemProjectClass">
        <img src="/img/bg/home/home_feature_project_3.jpg ">
      </div>
      <div class="th-add-to-pinboard position-absolute top-right-30 ">
        <i class="fa-solid fa-plus"></i>
      </div>
      <div class="th-item-footer">
        <div class="th-label">Merit Interiors</div>
        <h3 class="th-title">Berry Street</h3>
        <div class="th-description">This is the third project description.</div>
        <div class="th-link">
          <div class="th-link-text pr-5">
  
            Read More
          </div>
          <div class="th-link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
          </div>
        </div>
  
      </div>
    </div>
  </div>
        `,
});
Vvveb.Blocks.add("krost/item-sector-slider", {
  name: "Item Sector slider",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/item-sector-slider.png",
  html: /*html*/ `
  <div class="swiper-slide gr-bg6 swiper-slide-visible swiper-slide-active swiper-slide-fully-visible" role="group" aria-label="1 / 6" style="width: 999px; z-index: 6; transform: translate3d(calc(0px), calc(0px), calc(0px)) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scale(1); opacity: 1; margin-right: 30px; transition-duration: 0ms;">
  <img src="/img/bg/home/home_cat_workstation.jpg">
  <div class="slider-content gr-bg5 slideinleft" data-ani="slideinleft" data-ani-delay=".1s" style="animation-delay: 0.1s;">
    <h4 class="title font-white slideinleft" data-ani="slideinleft" data-ani-delay=".2s" style="animation-delay: 0.2s;">Corporate</h4>
    <h6 class="th-title-20 font-white pb-15 slideinleft" data-ani="slideinleft" data-ani-delay="0.3s" style="animation-delay: 0.3s;">Transform your workplace with innovative solutions</h6>
    <div class="btn-group slideinleft" data-ani="slideinleft" data-ani-delay="0.4s" style="animation-delay: 0.4s;">
      <div class="position-relative">
        <a href="contact.html" class="th-btn-outline text-capitalize">
          <span class="mr-5">Contact Sales</span>
          <i class="fa-regular fa-arrow-up degree-60"></i>
        </a>
      </div>
    </div>
  </div>
<div class="swiper-slide-shadow swiper-slide-shadow-creative" style="opacity: 0; transition-duration: 0ms;"></div></div>
          `,
});

Vvveb.BlocksGroup["Krost Item Blocks"] = [
  "krost/item-about-masonry-large",
  "krost/item-about-masonry-small",
  "krost/item-blog-slider",
  "krost/item-blog",
  "krost/item-card",
  "krost/item-featured-material-slider",
  "krost/item-government-large",
  "krost/item-government-small",
  "krost/item-government-details",
  "krost/item-environment-policy-details",
  "krost/item-help-card",
  "krost/item-img-circle",
  "krost/item-instagram-slider",
  "krost/item-member-card",
  "krost/item-number-card",
  "krost/item-product",
  "krost/item-project",
  "krost/item-resource",
  "krost/item-showroom",
  "krost/item-showroom-virtual",
  "krost/item-sector-slider",
  "krost/item-categories-masonry-large",
  "krost/item-categories-masonry-small",
  "krost/item-project-masonry",
  "krost/item-project-masonry-small",
  "krost/item-product-masonry",
  "krost/item-product-masonry-small",
  "krost/item-feature-project-slider",
  "krost/item-finishes-card",
];
