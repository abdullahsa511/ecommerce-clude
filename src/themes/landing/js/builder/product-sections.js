function checkPluginIsWorking() {
  console.log("Plugin is working");
}

Vvveb.Sections.add("product/product-story-masonry", {
  name: "Product Story",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-story-masonry.png",
  html: `
   <section id="feature-products" class="container th-container border-bottom">
    <div class="row featured-product">
      <div class="col-12">
        <div class="th-product-story-masonry section-body th-masonry-col-2">
          <div class="th-masonry-grid">

            <div class="th-masonry-grid-item grid-col-span-7 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                <img src="/${Vvveb.themeBaseUrl}img/product-detail/Comfort Meets Support.png" alt="Product 1" />
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

            <div class="th-masonry-grid-item grid-col-span-6 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                 <img src="/${Vvveb.themeBaseUrl}img/categories/screens.png" alt="Product 1" />
              </div>
              <div class="th-product-info-wrapper">
                <div class="th-item-info">
                  <h6 class="th-title-17">
                    <div class="th-link">
                      <h6 class="th-title-20 font-weight-700"> Sustainable from the start </h6>

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

          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/product-features", {
  name: "Product Features",
  image:
    Vvveb.themeBaseUrl + "/screenshots/sections/product/product-features.png",
  items: {
    api: "product/product-features",
    json: [],
  },
  html: `
    <section id="th-product-features" class="gr-bg8">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Features </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Experience the ultimate blend of <span class='font-weight-700'>comfort</span> and <span class='font-weight-700'></span>functionality</span> with Archi, where <span class='font-weight-700'>cutting-edge design</span> meets adjustable precision for every workspace need
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text Order OnlineClass">Order Online</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="section-body">
          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/feature img 1.png  
      " alt="Adjustable Ergonomics" />
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
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/feature img 2.png  
      " alt="Sleek and Modern Design" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">Sleek and Modern Design</h6>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/feature img 3.png  
      " alt="High-Performance Mesh Upholstery" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">High-Performance Mesh Upholstery</h6>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/product-configurator", {
  name: "Product Configurator",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-configurator.png",
  html: `
  <section id="th-product-configurator" class="th-product-configurator">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header " style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h2 class="  th-section-title "> Product Configurator </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

        </div>


        <div class="section-body">
          <div class="row">
            <div class="col-md-6">
              <div class="threed-section">
                <p class="scan-instruction">Scan QR to view in your space</p>
                <div class="qr-code">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/Product Configurator Chair.png" alt="Scan QR Code to View in Your Space">

                  <!-- 3D Symbol below the QR code -->
                  <div class="threed-container">
                     <img src="/${Vvveb.themeBaseUrl}img/product-detail/3d-logo.png" alt="">
                  </div>
                </div>


                <!-- Thumbnail Selection for 3D View -->
                <div class="thumbnail-selection">
                  <div class="thumbnail selected">
                     <img src="/${Vvveb.themeBaseUrl}img/product-detail/Product Configurator Chair.png" alt="Thumbnail 1">
                  </div>
                  <div class="thumbnail">
                     <img src="/${Vvveb.themeBaseUrl}img/product-detail/Product Configurator Chair View 2.png" alt="Thumbnail 2">
                  </div>
                </div>
                <div class="img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/love-logo.png" alt="">
                </div>
              </div>
            </div>

            <div class="th-product-model col-md-6">
              <div class="model-column">
                <div class="accordion th-product-accordion" id="productConfigAccordion">
                  <!-- Model Number Accordion Item -->
                  <div class="accordion-item th-accordion-item">
                    <h4 class="accordion-header th-accordion-header" id="modelNumberHeading">
                      <button class="accordion-button th-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#modelNumberCollapse" aria-expanded="false" aria-controls="modelNumberCollapse">
                        <div class="th-config-label">Model Number <span class="th-config-value">4550</span>
                        </div>
                        <span class="th-config-icon">+</span>
                      </button>
                    </h4>
                    <div id="modelNumberCollapse" class="accordion-collapse th-accordion-collapse collapse" aria-labelledby="modelNumberHeading" data-bs-parent="#productConfigAccordion">
                      <div class="accordion-body th-accordion-body">
                        <div class="th-model-details">
                          <p>Model specifications and additional details can be displayed here.</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Colour Accordion Item -->
                  <div class="accordion-item th-accordion-item">
                    <h4 class="accordion-header th-accordion-header" id="colourHeading">
                      <button class="accordion-button th-accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colourCollapse" aria-expanded="true" aria-controls="colourCollapse">
                        <div class="th-config-label">Colour <span class="th-config-value">black</span>
                        </div>
                        <span class="th-config-icon">−</span>
                      </button>
                    </h4>
                    <div id="colourCollapse" class="accordion-collapse th-accordion-collapse collapse show" aria-labelledby="colourHeading" data-bs-parent="#productConfigAccordion">
                      <div class="accordion-body th-accordion-body">
                        <div class="th-colors-selection">
                          <div class="th-color-option">
                            <div class="th-color-swatch th-red-lava"></div>
                            <div class="th-color-name">RED LAVA</div>
                          </div>
                          <div class="th-color-option active">
                            <div class="th-color-swatch th-black"></div>
                            <div class="th-color-name">BLACK</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- First Lorem Ipsum Accordion Item -->
                  <div class="accordion-item th-accordion-item">
                    <h4 class="accordion-header th-accordion-header" id="loremHeading1">
                      <button class="accordion-button th-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#loremCollapse1" aria-expanded="false" aria-controls="loremCollapse1">
                        <div class="th-config-label">Lorem ipsum <span class="th-config-value">dolor sit, amet</span>
                        </div>
                        <span class="th-config-icon">+</span>
                      </button>
                    </h4>
                    <div id="loremCollapse1" class="accordion-collapse th-accordion-collapse collapse" aria-labelledby="loremHeading1" data-bs-parent="#productConfigAccordion">
                      <div class="accordion-body th-accordion-body">
                        <ul class="th-config-options">
                          <li>
                            <a href="service.html">Desks</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <!-- Second Lorem Ipsum Accordion Item -->
                  <div class="accordion-item th-accordion-item">
                    <h4 class="accordion-header th-accordion-header" id="loremHeading2">
                      <button class="accordion-button th-accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#loremCollapse2" aria-expanded="false" aria-controls="loremCollapse2">
                        <div class="th-config-label">Lorem ipsum <span class="th-config-value">dolor sit, amet</span>
                        </div>
                        <span class="th-config-icon">+</span>
                      </button>
                    </h4>
                    <div id="loremCollapse2" class="accordion-collapse th-accordion-collapse collapse" aria-labelledby="loremHeading2" data-bs-parent="#productConfigAccordion">
                      <div class="accordion-body th-accordion-body">
                        <ul class="th-config-options">
                          <li>
                            <a href="service.html">Tables</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="th-product-order-online">
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      Order Online

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>

                <div class="file-formats">
                  <p>
                    <span>.Png</span>
                    <span>Jpg</span>
                    <span>Dwg</span>
                    <span>.3ds</span>
                    <span>.5kp</span>
                    <span>.Rfa_</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/product-detail-tabs", {
  name: "Product Detail Tabs",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-detail-tabs.png",
  html: `
    <section class="th-product-detail-tabs py-5 bg-gray">
    <div class="container th-container">
      <!-- Tab navigation for larger screens (d-none d-md-flex hides on mobile) -->
      <div class="nav nav-tabs mb-4 d-none d-md-flex border-0" id="product-tabs" role="tablist">
        <button class="nav-link active px-4 py-3 me-2" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="true">Specs</button>
        <button class="nav-link px-4 py-3 me-2" id="dimensions-tab" data-bs-toggle="tab" data-bs-target="#dimensions" type="button" role="tab" aria-controls="dimensions" aria-selected="false">Dimensions</button>
        <button class="nav-link px-4 py-3 me-2" id="downloads-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="false">Downloads</button>
        <button class="nav-link px-4 py-3 me-2" id="certifications-tab" data-bs-toggle="tab" data-bs-target="#certifications" type="button" role="tab" aria-controls="certifications" aria-selected="false">Certifications</button>
        <button class="nav-link px-4 py-3" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false">Pictures & Videos</button>
      </div>

      <!-- Accordion for mobile screens only (d-md-none hides on desktop) -->
      <div class="accordion d-md-none mb-4" id="accordionProductDetails">
        <div class="accordion-item border-0 mb-2">
          <h2 class="accordion-header" id="headingSpecs">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs" aria-expanded="true" aria-controls="collapseSpecs">
              Specs
            </button>
          </h2>
          <div id="collapseSpecs" class="accordion-collapse collapse show" aria-labelledby="headingSpecs" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">
              <!-- This will be filled with the same content as the tab -->
              <div class="row">
                <div class="col-lg-6">
                  <h3>Product Details</h3>
                  <ul class="list-unstyled">
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit</li>
                    <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit amet.</li>
                    <li>• Lorem ipsum dolor sit geho urna pellentesque.</li>
                  </ul>
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      View Catalogue

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>
                <div class="col-lg-6">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Product Specifications" class="img-fluid mt-4 mt-lg-0">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item border-0 mb-2">
          <h2 class="accordion-header" id="headingDimensions">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDimensions" aria-expanded="false" aria-controls="collapseDimensions">
              Dimensions
            </button>
          </h2>
          <div id="collapseDimensions" class="accordion-collapse collapse" aria-labelledby="headingDimensions" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">
              <!-- This will be filled with the same content as the tab -->
              <div class="row">
                <div class="col-lg-6">
                  <h3>Product Dimensions</h3>
                  <ul class="list-unstyled">
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit</li>
                    <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit amet.</li>
                  </ul>
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      View Catalogue

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>
                <div class="col-lg-6">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Product Dimensions" class="img-fluid mt-4 mt-lg-0">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item border-0 mb-2">
          <h2 class="accordion-header" id="headingDownloads">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDownloads" aria-expanded="false" aria-controls="collapseDownloads">
              Downloads
            </button>
          </h2>
          <div id="collapseDownloads" class="accordion-collapse collapse" aria-labelledby="headingDownloads" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">
              <!-- This will be filled with the same content as the tab -->
              <div class="row">
                <div class="col-lg-6">
                  <h3>Available Downloads</h3>
                  <ul class="list-unstyled">
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit</li>
                    <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                  </ul>
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      Download All

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>
                <div class="col-lg-6">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Downloads" class="img-fluid mt-4 mt-lg-0">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item border-0 mb-2">
          <h2 class="accordion-header" id="headingCertifications">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCertifications" aria-expanded="false" aria-controls="collapseCertifications">
              Certifications
            </button>
          </h2>
          <div id="collapseCertifications" class="accordion-collapse collapse" aria-labelledby="headingCertifications" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">
              <!-- This will be filled with the same content as the tab -->
              <div class="row">
                <div class="col-lg-6">
                  <h3>Product Certifications</h3>
                  <ul class="list-unstyled">
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit</li>
                    <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                  </ul>
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      View Certifications

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>
                <div class="col-lg-6">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Certifications" class="img-fluid mt-4 mt-lg-0">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="accordion-item border-0">
          <h2 class="accordion-header" id="headingMedia">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedia" aria-expanded="false" aria-controls="collapseMedia">
              Pictures & Videos
            </button>
          </h2>
          <div id="collapseMedia" class="accordion-collapse collapse" aria-labelledby="headingMedia" data-bs-parent="#accordionProductDetails">
            <div class="accordion-body pt-0">
              <!-- This will be filled with the same content as the tab -->
              <div class="row">
                <div class="col-lg-6">
                  <h3>Media Gallery</h3>
                  <ul class="list-unstyled">
                    <li>• Lorem ipsum dolor sit amet consectetur.</li>
                    <li>• Lorem ipsum dolor sit</li>
                    <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                  </ul>
                  <div class="th-link Padding">
                    <div class="th-link-text pr-5">
                      View Gallery

                    </div>
                    <div class="th-link-icon-btn">
                      <i class="fa-regular fa-arrow-up degree-60"></i>
                    </div>
                  </div>

                </div>
                <div class="col-lg-6">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Media Gallery" class="img-fluid mt-4 mt-lg-0">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab content for larger screens -->
      <div class="tab-content d-none d-md-block">
        <div class="tab-pane fade show active" id="specs" role="tabpanel" aria-labelledby="specs-tab">
          <div class="row">
            <div class="col-lg-6">
              <h3>Lorem Ipsum Dolor Sit</h3>
              <ul class="list-unstyled">
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit</li>
                <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit amet.</li>
                <li>• Lorem ipsum dolor sit geho urna pellentesque.</li>
              </ul>
              <div class="th-link Padding">
                <div class="th-link-text pr-5">
                  View Catalogue

                </div>
                <div class="th-link-icon-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Product Specifications" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="dimensions" role="tabpanel" aria-labelledby="dimensions-tab">
          <div class="row">
            <div class="col-lg-6">
              <h3>Product Dimensions</h3>
              <ul class="list-unstyled">
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit</li>
                <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit amet.</li>
              </ul>
              <div class="th-link Padding">
                <div class="th-link-text pr-5">
                  View Catalogue

                </div>
                <div class="th-link-icon-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Product Dimensions" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="downloads" role="tabpanel" aria-labelledby="downloads-tab">
          <div class="row">
            <div class="col-lg-6">
              <h3>Available Downloads</h3>
              <ul class="list-unstyled">
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit</li>
                <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
              </ul>
              <div class="th-link Padding">
                <div class="th-link-text pr-5">
                  Download All

                </div>
                <div class="th-link-icon-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Downloads" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="certifications" role="tabpanel" aria-labelledby="certifications-tab">
          <div class="row">
            <div class="col-lg-6">
              <h3>Product Certifications</h3>
              <ul class="list-unstyled">
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit</li>
                <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
              </ul>
              <div class="th-link Padding">
                <div class="th-link-text pr-5">
                  View Certifications

                </div>
                <div class="th-link-icon-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Certifications" class="img-fluid">
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
          <div class="row">
            <div class="col-lg-6">
              <h3>Media Gallery</h3>
              <ul class="list-unstyled">
                <li>• Lorem ipsum dolor sit amet consectetur.</li>
                <li>• Lorem ipsum dolor sit</li>
                <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
              </ul>
              <div class="th-link Padding">
                <div class="th-link-text pr-5">
                  View Gallery

                </div>
                <div class="th-link-icon-btn">
                  <i class="fa-regular fa-arrow-up degree-60"></i>
                </div>
              </div>

            </div>
            <div class="col-lg-6">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Media Gallery" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});

Vvveb.Sections.add("product/product-sustainability", {
  name: "Product Sustainability",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-sustainability.png",
  html: `
   <section id="ocean" class="bg-white">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-6">
          <div class="left-column">
             <img src="/${Vvveb.themeBaseUrl}img/product-detail/ocean image.png" alt="Product Image">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mt-50">
            <div class="th-section-header flex-column pb-0">
              <div class="th-section-header-wrapper ">
                <h2 class="  th-section-title "> Made With Ocean Bound Plastic </h2>
                <div class="th-section-subtitle" style="font-size: ">
                  Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. Pellentesque libero donec sit egestas orci consequat est mauris duis.
                </div>
              </div>

              <div class="">
                <div class="th-section-header-link">
                  <span class="th-section-header-link-text d-block">View Catalogue</span>
                  <span class="th-section-header-link-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </span>
                </div>
              </div>

            </div>


            <div class="th-product-sustainability-img">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/ocean bound plastic chair.png" alt="Product Image">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});

Vvveb.Sections.add("product/product-call-to-action", {
  name: "Product Call To Action",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-call-to-action.png",
  html: `
  <section id="circle" class="mb-8 bg-white">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-4 col-sm-12 text-center">
          <div class="th-item-img-card d-flex flex-column align-items-center">
            <div class="th-img-container">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/first circle.png" alt="" class="th-rounded-circle mb-3" style="max-width: 164px;">
            </div>
            <div class="th-item-img-content mt-30">
              <h6 class="th-title-22 font-weight-700">Shop Archi</h3>
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
        </div>
        <div class="col-md-4 col-sm-12 text-center">
          <div class="th-item-img-card d-flex flex-column align-items-center">
            <div class="th-img-container">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="" class="th-rounded-circle mb-3" style="max-width: 164px;">
            </div>
            <div class="th-item-img-content mt-30">
              <h6 class="th-title-22 font-weight-700">View Catalogue</h3>
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
        </div>
        <div class="col-md-4 col-sm-12 text-center">
          <div class="th-item-img-card d-flex flex-column align-items-center">
            <div class="th-img-container">
               <img src="/${Vvveb.themeBaseUrl}img/product-detail/third circle.png" alt="" class="th-rounded-circle mb-3" style="max-width: 164px;">
            </div>
            <div class="th-item-img-content mt-30">
              <h6 class="th-title-22 font-weight-700">Book A Showroom Tour</h3>
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
        </div>
      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/product-instagram-slider", {
  name: "Product Instagram Slider",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-instagram-slider.png",
  commands: [
    {
      execute: function () {
        createSlider("th-instagram-products-slider");
      },
    },
  ],
  html: `
   <!-- ============== Product Instagram Slider Section Start ============== -->
  <section id="th-product-slider" class="">
    <div class="container th-container">
      <div class="row">
        <div class="col-md-12 text-center mb-4">
          <h2 class="section-title">#ARCHI</h2>
        </div>

        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-instagram-products-slider swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" style="cursor: grab;" id="swiper-wrapper-617caa109d7c7214b" aria-live="polite">
                  <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 4" style="width: 446.667px; margin-right: 20px;">
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
                  <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 4" style="width: 446.667px; margin-right: 20px;">
                    <div class="th-item-card">
                      <div class="th-instagram-img-container background-image" style="background-image: url(/${Vvveb.themeBaseUrl}img/product-detail/insta-2.png);">
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
                  <div class="swiper-slide" role="group" aria-label="3 / 4" style="width: 446.667px; margin-right: 20px;">
                    <div class="th-item-card">
                      <div class="th-instagram-img-container background-image" style="background-image: url(/${Vvveb.themeBaseUrl}img/product-detail/insta-3.png);">
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
                  <div class="swiper-slide" role="group" aria-label="4 / 4" style="width: 446.667px; margin-right: 20px;">
                    <div class="th-item-card">
                      <div class="th-instagram-img-container background-image" style="background-image: url(/${Vvveb.themeBaseUrl}img/product-detail/insta-4.png);">
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

                </div>
                <div class="swiper-scrollbar swiper-scrollbar-horizontal"><div class="swiper-scrollbar-drag" style="width: 1226.43px; transform: translate3d(0px, 0px, 0px);"></div></div>
              <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ============== Product Instagram Slider Section End ============== -->
  `,
});
Vvveb.Sections.add("product/product-may-like", {
  name: "Product May Like",
  image:
    Vvveb.themeBaseUrl + "/screenshots/sections/product/product-may-like.png",
  html: `
    <section id="th-product-may-like" class="may-also-like">
    <div class="container th-container">
      <div class="row justify-content-center">
        <div class="col-md-12 text-center mb-60">
          <h2 class="section-title">You May Also Like</h2>
          <div class="section-subtitle">
            Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
          </div>
        </div>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-3 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/also like 1.png  
      " alt="Lorem Ipsum" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">Lorem Ipsum</h6>

                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/also like 2.png  
      " alt="Lorem Ipsum" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">Lorem Ipsum</h6>

                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/also like 3.png  
      " alt="Lorem Ipsum" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">Lorem Ipsum</h6>

                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-12">
              <div class="th-item-card">
                <div class="th-img-container">
                   <img src="/${Vvveb.themeBaseUrl}img/product-detail/also like 1.png  
      " alt="Lorem Ipsum" />
                </div>
                <div class="th-item-card-content mt-15">
                  <div class="th-link mb-10">
                    <h6 class="th-link-text pr-5 th-title-20">Lorem Ipsum</h6>

                    <div class="th-link-icon">
                      <i class="fa-regular fa-arrow-right"></i>
                    </div>

                  </div>
                  <div class="th-description">
                    Lorem ipsum dolor sit amet consectetur
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/product-related-family", {
  name: "Product Related Family",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-related-family.png",
  commands: [
    {
      execute: function () {
        createSlider("product-related-slider");
      },
    },
  ],
  html: `
  <!-- ============== Product Related Family Section Start ============== -->
   <section id="th-product-related-family" class="gr-bg8">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Feet The Family </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Experience the ultimate blend of <span class='font-weight-700'>comfort</span> and <span class='font-weight-700'></span>functionality</span> with Archi, where <span class='font-weight-700'>cutting-edge design</span> meets adjustable precision for every workspace need
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text View All ProductsClass">View All Products</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper product-related-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container bg-white">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Archi.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Archi </h6>
                          <div class="th-tag-name">


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container bg-white">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro </h6>
                          <div class="th-tag-name">


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container bg-white">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro S.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro S </h6>
                          <div class="th-tag-name">


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container bg-white">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Kove.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Kove </h6>
                          <div class="th-tag-name">


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>

                </div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script>
    
  </script>
  <!-- ============== Product Related Family Section End ============== -->
  `,
});

Vvveb.Sections.add("product/product-slider-section", {
  name: "Product Slider Section",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/product-slider-section.png",
  commands: [
    {
      execute: function () {
        createSlider("th-featured-products-slider");
      },
    },
  ],
  html: `
  <!-- ============== Product Slider Section Start ============== -->
   <section id="th-product-slider" class="">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Products Slider </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text View All ProductsClass">View All Products</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>


        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-product-slider th-instagram-slider th-featured-products-slider">
                <div class="swiper-wrapper">

                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Archi.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Archi </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>


                          </div>
                          <div class="th-item-finish-circle">

                            <div class="th-circle black-fabric">Black Fabric</div>
                            <div class="th-circle black-premium">Black Premium Polyurethane</div>
                            <div class="th-circle mocha-premium">Mocha Premium Polyurethane</div>
                            <div class="th-circle white" data-bg-src="/img/finishes/finish-2.jpg">Cream Premium Polyurethane</div>



                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro S.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro S </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Kove.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Kove </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>


                </div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script>
    
  </script>
  <!-- ============== Product Slider Section End ============== -->
  `,
});
Vvveb.Sections.add("product/featured-product-slider-section", {
  name: "Featured Product Slider Section",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/featured-product-slider-section.png",
  commands: [
    {
      execute: function () {
        createSlider("th-featured-products-slider");
      },
    },
  ],
  tuhin: [
    {
      execute: function () {
        checkPluginIsWorking();
        console.log("tuhin first execute");
      },
    },
    {
      execute: function () {
        console.log("tuhin second execute");
      },
    },
  ],
  html: `
  <!-- ============== Featured Product Slider Section Start ============== -->
  <section id="th-product-slider" class="gr-bg8">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Featured Products </h2>
            <div class="th-section-subtitle" style="font-size:  20px !important ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text View All ProductsClass">View All Products</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>


        <div class="th-section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-featured-products-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Archi.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Archi </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>


                          </div>
                          <div class="th-item-finish-circle">

                            <div class="th-circle black-fabric">Black Fabric</div>
                            <div class="th-circle black-premium">Black Premium Polyurethane</div>
                            <div class="th-circle mocha-premium">Mocha Premium Polyurethane</div>
                            <div class="th-circle white" data-bg-src="/img/finishes/finish-2.jpg">Cream Premium Polyurethane</div>



                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Miro S.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Miro S </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="th-item-product">
                      <div class="th-img-container ">
                         <img src="/${Vvveb.themeBaseUrl}img/category-seating/Kove.png " />
                      </div>
                      <div class="th-add-to-pinboard position-absolute top-right-30 ">
                        <i class="fa-solid fa-plus"></i>
                      </div>
                      <div class="th-item-footer">
                        <!-- <div class="label">Build</div> -->
                        <h3 class="th-title mt-20">Kove </h6>
                          <div class="th-tag-name">

                            <div class="th-tag">AFRDI Certified</div>
                            <div class="th-tag">OBP Certified</div>
                            <div class="th-tag">Some Tag Name Here</div>
                            <div class="th-tag">Tag Name Here</div>
                            <div class="th-tag">Tag Name Here As Well</div>


                          </div>
                          <div class="th-item-finish-circle">


                          </div>

                      </div>
                    </div>
                  </div>

                </div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      createFeaturedProductSlider();
      console.log("Product Slider Added");
    </script>
  </section>
  <!-- ============== Featured Product Slider Section End ============== -->
  `,
});
Vvveb.Sections.add("product/featured-material-slider-section", {
  name: "Featured Material Slider Section",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/featured-material-slider-section.png",
  commands: [
    {
      execute: function () {
        createSlider("th-featured-material-slider");
      },
    },
  ],
  html: `
  <section id="th-featured-material" class="featured-material gr-bg8">
    <div class="container th-container">
      <div class="row">
        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Featured Materials </h2>
            <div class="th-section-subtitle" style="font-size:  20px !important ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text View All ProductsClass">View All Products</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-featured-material-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                     <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 1.png" />
                    <div class="slider-footer">
                      <p>Finish</p>
                      <h3 class="title">ABBEY</h3>
                      <span>Lorem Ipsum</span>
                    </div>
                  </div>
                  <div class="swiper-slide">
                     <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 2.png" />
                    <div class="slider-footer">
                      <p>Textile</p>
                      <h3 class="title">Access Mesh</h3>
                      <span>Lorem Ipsum</span>
                    </div>
                  </div>
                  <div class="swiper-slide">
                     <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 3.png" />
                    <div class="slider-footer">
                      <p>Textile</p>
                      <h3 class="title">Afghan Seating</h3>
                      <span>Lorem Ipsum</span>
                    </div>
                  </div>
                  <div class="swiper-slide">
                     <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 4.png" />
                    <div class="slider-footer">
                      <p>Finish</p>
                      <h3 class="title">Amethyst</h3>
                      <span>Lorem Ipsum</span>
                    </div>
                  </div>
                  <div class="swiper-slide">
                     <img src="/${Vvveb.themeBaseUrl}img/project-detail/material 5.png" />
                    <div class="slider-footer">
                      <p>Finish</p>
                      <h3 class="title">Amethyst</h3>
                      <span>Lorem Ipsum</span>
                    </div>
                  </div>
                </div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});
Vvveb.Sections.add("product/featured-product-masonry", {
  name: "Featured Product Masonry",
  image:
    Vvveb.themeBaseUrl +
    "/screenshots/sections/product/featured-product-masonry.png",
  html: `
  <section id="feature-products" class="container th-container">
    <div class="row featured-product">
      <div class="col-12">
        <div class="th-section-header " style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h2 class="  th-section-title "> Feature Products </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

        </div>


        <div class="section-body th-masonry-col-2 th-featured-products-masonry">
          <div class="th-masonry-grid">
            <div class="th-masonry-grid-item grid-col-span-8 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                 <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_fp_1.jpg" alt="Product 1" />
              </div>
              <div class="th-product-info-wrapper">
                <div class="th-item-info">
                  <h6 class="th-title-17">
                    <div class="th-link">
                      <h6 class="th-title-20 font-weight-700"> Chwyla </h6>

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

            <div class="th-masonry-grid-item grid-col-span-5 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                 <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_fp_2.jpg" alt="Product 1" />
              </div>
              <div class="th-product-info-wrapper">
                <div class="th-item-info">
                  <h6 class="th-title-17">
                    <div class="th-link">
                      <h6 class="th-title-20 font-weight-700"> Kobe </h6>

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

            <div class="th-masonry-grid-item grid-col-span-5 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                 <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_fp_3.jpg" alt="Product 1" />
              </div>
              <div class="th-product-info-wrapper">
                <div class="th-item-info">
                  <h6 class="th-title-17">
                    <div class="th-link">
                      <h6 class="th-title-20 font-weight-700"> Desks </h6>

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

            <div class="th-masonry-grid-item grid-col-span-8 " style="transform: translateY(0 px);padding-top:0 px">
              <div class="th-item-img">
                 <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_fp_4.jpg" alt="Product 1" />
              </div>
              <div class="th-product-info-wrapper">
                <div class="th-item-info">
                  <h6 class="th-title-17">
                    <div class="th-link">
                      <h6 class="th-title-20 font-weight-700"> Sofas </h6>

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

          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});

Vvveb.Sections.add("custom/connect-us-section", {
  name: "Connect Us Section",
  image:
    Vvveb.themeBaseUrl + "/screenshots/sections/product/connect-us-section.png",
  html: `
   <section id="th-connect-us" class="bg-gray pb-0 pt-60">
    <div class="container th-container">
      <div class="row">
        <div class="col-lg-6 th-connect-us-content-wrapper">
          <div class="th-connect-us-content d-flex flex-column justify-content-between">
            <div class="pb-70">
              <h2 class="section-title ">Connect With Us</h2>
              <div class="section-subtitle ">
                Lorem ipsum dolor sit amet consectetur.
              </div>
            </div>
            <div class="th-img-container mt-auto">
               <img src="/${Vvveb.themeBaseUrl}img/contact-us/connect.png" alt="Sydney Showroom" />
            </div>
          </div>
        </div>
        <div class="col-lg-6 position-relative d-flex justify-content-center mb-60">
          <div class="form-container th-cf-wrapper overlap ">
            <form>
              <div class="form-group">
                <label for="email">Email* (required)</label>
                <input type="email" id="email" class="form-control" placeholder="Email*" required>
              </div>
              <div class="form-group">
                <label for="company">Company Name* (required)</label>
                <input type="text" id="company" class="form-control" placeholder="Company Name*" required>
              </div>
              <div class="form-group">
                <label for="full-name">Full Name* (required)</label>
                <input type="text" id="full-name" class="form-control" placeholder="Full Name*" required>
              </div>
              <div class="form-group">
                <label for="type">Type</label>
                <select id="type" class="form-control">
                  <option value="" disabled selected>Type</option>
                  <option value="option1">Option 1</option>
                  <option value="option2">Option 2</option>
                  <option value="option3">Option 3</option>
                </select>
              </div>
              <!-- File Upload -->
              <div class="upload-section">
                <p>Drag and Drop or Click to Upload File</p>
                <input type="file" class="file-input">
              </div>
              <!-- Text Area -->
              <div class="form-group">
                <label for="message">Add Text</label>
                <div class="text-section">
                  <textarea class="form-control" placeholder="Enter your message"></textarea>
                </div>
              </div>
              <!-- Submit Button -->
              <div class="form-group">
                <button type="submit" class="th-btn-submit-lg mt-30">
                  Submit <span>&#8599;</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  `,
});

Vvveb.SectionsGroup["Product sections"] = [
  "product/product-story-masonry",
  "product/product-features",
  "product/product-configurator",
  "product/product-detail-tabs",
  "product/product-sustainability",
  "product/product-call-to-action",
  "product/product-instagram-slider",
  "product/product-may-like",
  "product/product-related-family",
  "product/product-slider-section",
  "product/featured-product-slider-section",
  "product/featured-material-slider-section",
  "product/featured-product-masonry",
  "product/connect-us-section",
];
