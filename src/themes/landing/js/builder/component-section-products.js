Vvveb.Components.extend("_section", "section/products-detail-under", {
  classes: ["th-products-detail-under"],
  name: "Products Details Under",
  html: /*html*/ `
      <div class="th-products-detail-under" style="min-height: 150px;">
          <div class="row th-products-detail-under-row">
              <div class="th-products-detail-under-item col-4">Item 1</div>
              <div class="th-products-detail-under-item col-4">Item 2</div>
              <div class="th-products-detail-under-item col-4">Item 3</div>
          </div>
      </div>`,
  properties: [
    {
      name: "Products Detail Under",
      key: "products-detail-under",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template:
        "demo/krost/js/builder/templates/product-detail-under-item.html",
      apiUrl: "demo/krost/data/product-detail-under.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-products-detail-under-item").length;
      },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-products-detail-under-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data
              console.log("itemData", itemData);
              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});
Vvveb.Components.extend("_section", "section/product-related-slider", {
  classes: ["product-related-slider"],
  name: "Product Related Slider",
  html: /*html*/ `
      <div class="th-product-slider" style="min-height: 150px;">
          <div class="row th-product-slider-row">
              <div class="th-product-slider-item col-4">Item 1</div>
              <div class="th-product-slider-item col-4">Item 2</div>
              <div class="th-product-slider-item col-4">Item 3</div>
          </div>
      </div>`,
  properties: [
    {
      name: "Product Slider Items",
      key: "product_slider_items",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/product-slider-item.html",
      apiUrl: "demo/krost/data/products-slider.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-product-slider-item").length;
      },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-product-slider-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data
              console.log("itemData", itemData);
              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});
Vvveb.Components.extend("_section", "section/product-also-like", {
  classes: ["th-products-also-like"],
  name: "Product Also Like",
  html: /*html*/ `
  <div class="th-products-also-like" style="min-height: 150px;">
      <div class="row th-products-also-like-row">
          <div class="th-products-also-like-item col-4">Item 1</div>
          <div class="th-products-also-like-item col-4">Item 2</div>
          <div class="th-products-also-like-item col-4">Item 3</div>
      </div>
  </div>
  `,
  properties: [
    {
      name: "Product Also Like",
      key: "product-also-like",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/product-also-like-item.html",
      apiUrl: "demo/krost/data/product-may-like.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-products-also-like-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-products-also-like-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data

              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});
Vvveb.Components.extend("_section", "section/product-features", {
  classes: ["th-product-features"],
  name: "Product Features",
  html: /*html*/ `
  <div class="th-product-features section-body">
    <div class="row th-product-features-row">
      <div class="th-product-features-item col-4">item1</div>
      <div class="th-product-features-item col-4">item2</div>
      <div class="th-product-features-item col-4">item3</div>
    </div>
  </div>
    `,
  properties: [
    {
      name: "Product Feature",
      key: "product-feature",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/product-feature-item.html",
      apiUrl: "demo/krost/data/product-features.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-product-features-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-product-features-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data

              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});

// Vvveb.Components.extend("_section", "section/product-details-tabs", {
//   classes: ["th-product-details-tabs"],
//   name: "Product Details Tabs",
//   html: /*html*/ `
// <div class="th-product-details-tabs">
//   <div class="th-product-details-container container th-container">
//     <!-- Tab navigation for larger screens (d-none d-md-flex hides on mobile) -->
//     <div class="th-nav-items nav nav-tabs mb-4 d-none d-md-flex border-0" id="product-tabs" role="tablist">
//         <button class="nav-link active px-4 py-3 me-2" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="true">Specs</button>
//         <button class="nav-link px-4 py-3 me-2" id="dimensions-tab" data-bs-toggle="tab" data-bs-target="#dimensions" type="button" role="tab" aria-controls="dimensions" aria-selected="false" tabindex="-1">Dimensions</button>
//         <button class="nav-link px-4 py-3 me-2" id="downloads-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="false" tabindex="-1">Downloads</button>
//         <button class="nav-link px-4 py-3 me-2" id="certifications-tab" data-bs-toggle="tab" data-bs-target="#certifications" type="button" role="tab" aria-controls="certifications" aria-selected="false" tabindex="-1">Certifications</button>
//         <button class="nav-link px-4 py-3" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false" tabindex="-1">Pictures &amp; Videos</button>
//     </div>

//     <!-- Accordion for mobile screens only (d-md-none hides on desktop) -->
//     <div class="th-nav-contents accordion d-md-none mb-4" id="accordionProductDetails">
//         <div class="accordion-item border-0 mb-2">
//             <h2 class="accordion-header" id="headingSpecs">
//                 <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecs" aria-expanded="true" aria-controls="collapseSpecs">
//                 Specs
//                 </button>
//             </h2>
//             <div id="collapseSpecs" class="accordion-collapse collapse show" aria-labelledby="headingSpecs" data-bs-parent="#accordionProductDetails">
//                 <div class="accordion-body pt-0">
//                 <!-- This will be filled with the same content as the tab -->
//                 <div class="row">
//                     <div class="col-lg-6">
//                     <h3>Product Details</h3>
//                     <ul class="list-unstyled">
//                         <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                         <li>• Lorem ipsum dolor sit</li>
//                         <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                         <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                         <li>• Lorem ipsum dolor sit amet.</li>
//                         <li>• Lorem ipsum dolor sit geho urna pellentesque.</li>
//                     </ul>
//                     <div class="th-link @@classPadding">
//                         <div class="th-link-text pr-5">
//                         View Catalogue

//                         </div>
//                         <div class="th-link-icon-btn">
//                         <i class="fa-regular fa-arrow-up degree-60"></i>
//                         </div>
//                     </div>

//                     </div>
//                     <div class="col-lg-6">
//                     <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Product Specifications" class="img-fluid mt-4 mt-lg-0">
//                     </div>
//                 </div>
//                 </div>
//             </div>
//         </div>

//         <div class="accordion-item border-0 mb-2">
//         <h2 class="accordion-header" id="headingDimensions">
//             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDimensions" aria-expanded="false" aria-controls="collapseDimensions">
//             Dimensions
//             </button>
//         </h2>
//         <div id="collapseDimensions" class="accordion-collapse collapse" aria-labelledby="headingDimensions" data-bs-parent="#accordionProductDetails">
//             <div class="accordion-body pt-0">
//             <!-- This will be filled with the same content as the tab -->
//             <div class="row">
//                 <div class="col-lg-6">
//                 <h3>Product Dimensions</h3>
//                 <ul class="list-unstyled">
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                     <li>• Lorem ipsum dolor sit</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                     <li>• Lorem ipsum dolor sit amet.</li>
//                 </ul>
//                 <div class="th-link @@classPadding">
//                     <div class="th-link-text pr-5">
//                     View Catalogue

//                     </div>
//                     <div class="th-link-icon-btn">
//                     <i class="fa-regular fa-arrow-up degree-60"></i>
//                     </div>
//                 </div>

//                 </div>
//                 <div class="col-lg-6">
//                 <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Product Dimensions" class="img-fluid mt-4 mt-lg-0">
//                 </div>
//             </div>
//             </div>
//         </div>
//         </div>

//         <div class="accordion-item border-0 mb-2">
//         <h2 class="accordion-header" id="headingDownloads">
//             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDownloads" aria-expanded="false" aria-controls="collapseDownloads">
//             Downloads
//             </button>
//         </h2>
//         <div id="collapseDownloads" class="accordion-collapse collapse" aria-labelledby="headingDownloads" data-bs-parent="#accordionProductDetails">
//             <div class="accordion-body pt-0">
//             <!-- This will be filled with the same content as the tab -->
//             <div class="row">
//                 <div class="col-lg-6">
//                 <h3>Available Downloads</h3>
//                 <ul class="list-unstyled">
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                     <li>• Lorem ipsum dolor sit</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 </ul>
//                 <div class="th-link @@classPadding">
//                     <div class="th-link-text pr-5">
//                     Download All

//                     </div>
//                     <div class="th-link-icon-btn">
//                     <i class="fa-regular fa-arrow-up degree-60"></i>
//                     </div>
//                 </div>

//                 </div>
//                 <div class="col-lg-6">
//                 <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Downloads" class="img-fluid mt-4 mt-lg-0">
//                 </div>
//             </div>
//             </div>
//         </div>
//         </div>

//         <div class="accordion-item border-0 mb-2">
//         <h2 class="accordion-header" id="headingCertifications">
//             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCertifications" aria-expanded="false" aria-controls="collapseCertifications">
//             Certifications
//             </button>
//         </h2>
//         <div id="collapseCertifications" class="accordion-collapse collapse" aria-labelledby="headingCertifications" data-bs-parent="#accordionProductDetails">
//             <div class="accordion-body pt-0">
//             <!-- This will be filled with the same content as the tab -->
//             <div class="row">
//                 <div class="col-lg-6">
//                 <h3>Product Certifications</h3>
//                 <ul class="list-unstyled">
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                     <li>• Lorem ipsum dolor sit</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 </ul>
//                 <div class="th-link @@classPadding">
//                     <div class="th-link-text pr-5">
//                     View Certifications

//                     </div>
//                     <div class="th-link-icon-btn">
//                     <i class="fa-regular fa-arrow-up degree-60"></i>
//                     </div>
//                 </div>

//                 </div>
//                 <div class="col-lg-6">
//                 <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Certifications" class="img-fluid mt-4 mt-lg-0">
//                 </div>
//             </div>
//             </div>
//         </div>
//         </div>

//         <div class="accordion-item border-0">
//         <h2 class="accordion-header" id="headingMedia">
//             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedia" aria-expanded="false" aria-controls="collapseMedia">
//             Pictures &amp; Videos
//             </button>
//         </h2>
//         <div id="collapseMedia" class="accordion-collapse collapse" aria-labelledby="headingMedia" data-bs-parent="#accordionProductDetails">
//             <div class="accordion-body pt-0">
//             <!-- This will be filled with the same content as the tab -->
//             <div class="row">
//                 <div class="col-lg-6">
//                 <h3>Media Gallery</h3>
//                 <ul class="list-unstyled">
//                     <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                     <li>• Lorem ipsum dolor sit</li>
//                     <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                 </ul>
//                 <div class="th-link @@classPadding">
//                     <div class="th-link-text pr-5">
//                     View Gallery

//                     </div>
//                     <div class="th-link-icon-btn">
//                     <i class="fa-regular fa-arrow-up degree-60"></i>
//                     </div>
//                 </div>

//                 </div>
//                 <div class="col-lg-6">
//                 <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Media Gallery" class="img-fluid mt-4 mt-lg-0">
//                 </div>
//             </div>
//             </div>
//         </div>
//         </div>
//     </div>

//     <!-- Tab content for larger screens -->
//     <div class="tab-content d-none d-md-block">
//         <div class="tab-pane fade show active" id="specs" role="tabpanel" aria-labelledby="specs-tab">
//         <div class="row">
//             <div class="col-lg-6">
//             <h3>Lorem Ipsum Dolor Sit</h3>
//             <ul class="list-unstyled">
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit amet.</li>
//                 <li>• Lorem ipsum dolor sit geho urna pellentesque.</li>
//             </ul>
//             <div class="th-link @@classPadding">
//                 <div class="th-link-text pr-5">
//                 View Catalogue

//                 </div>
//                 <div class="th-link-icon-btn">
//                 <i class="fa-regular fa-arrow-up degree-60"></i>
//                 </div>
//             </div>

//             </div>
//             <div class="col-lg-6">
//             <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Product Specifications" class="img-fluid">
//             </div>
//         </div>
//         </div>

//         <div class="tab-pane fade" id="dimensions" role="tabpanel" aria-labelledby="dimensions-tab">
//         <div class="row">
//             <div class="col-lg-6">
//             <h3>Product Dimensions</h3>
//             <ul class="list-unstyled">
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit amet.</li>
//             </ul>
//             <div class="th-link @@classPadding">
//                 <div class="th-link-text pr-5">
//                 View Catalogue

//                 </div>
//                 <div class="th-link-icon-btn">
//                 <i class="fa-regular fa-arrow-up degree-60"></i>
//                 </div>
//             </div>

//             </div>
//             <div class="col-lg-6">
//             <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Product Dimensions" class="img-fluid">
//             </div>
//         </div>
//         </div>

//         <div class="tab-pane fade" id="downloads" role="tabpanel" aria-labelledby="downloads-tab">
//         <div class="row">
//             <div class="col-lg-6">
//             <h3>Available Downloads</h3>
//             <ul class="list-unstyled">
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//             </ul>
//             <div class="th-link @@classPadding">
//                 <div class="th-link-text pr-5">
//                 Download All

//                 </div>
//                 <div class="th-link-icon-btn">
//                 <i class="fa-regular fa-arrow-up degree-60"></i>
//                 </div>
//             </div>

//             </div>
//             <div class="col-lg-6">
//             <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Downloads" class="img-fluid">
//             </div>
//         </div>
//         </div>

//         <div class="tab-pane fade" id="certifications" role="tabpanel" aria-labelledby="certifications-tab">
//         <div class="row">
//             <div class="col-lg-6">
//             <h3>Product Certifications</h3>
//             <ul class="list-unstyled">
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//             </ul>
//             <div class="th-link @@classPadding">
//                 <div class="th-link-text pr-5">
//                 View Certifications

//                 </div>
//                 <div class="th-link-icon-btn">
//                 <i class="fa-regular fa-arrow-up degree-60"></i>
//                 </div>
//             </div>

//             </div>
//             <div class="col-lg-6">
//             <img src="/${Vvveb.themeBaseUrl}img/product-detail/second circle.png" alt="Certifications" class="img-fluid">
//             </div>
//         </div>
//         </div>

//         <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
//         <div class="row">
//             <div class="col-lg-6">
//             <h3>Media Gallery</h3>
//             <ul class="list-unstyled">
//                 <li>• Lorem ipsum dolor sit amet consectetur.</li>
//                 <li>• Lorem ipsum dolor sit</li>
//                 <li>• Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</li>
//             </ul>
//             <div class="th-link @@classPadding">
//                 <div class="th-link-text pr-5">
//                 View Gallery

//                 </div>
//                 <div class="th-link-icon-btn">
//                 <i class="fa-regular fa-arrow-up degree-60"></i>
//                 </div>
//             </div>

//             </div>
//             <div class="col-lg-6">
//             <img src="/${Vvveb.themeBaseUrl}img/product-detail/tab image.png" alt="Media Gallery" class="img-fluid">
//             </div>
//         </div>
//         </div>
//     </div>
//   </div>
// </div>
// `,
//   itemTemplate: "",
//   navItemTemplate: "",
//   properties: [
//     {
//       name: "Tab Items",
//       key: "tabItems",
//       inputtype: ArrayInput,
//       onChange: function (node, value, input, component, event) {
//         let wrapper = node.querySelector(".th-product-details-container");
//         let navItemsWraper = node.querySelector(".th-nav-items");
//         let navItemContentWrapper = node.querySelector(".th-nav-contents");
//         wrapper.innerHTML = "";
//         navItemWraper.innerHTML = "";
//         navItemContentWrapper.innerHTML = "";

//         let data = [];

//         for (let i = 0; i < value.length; i++) {
//           let itemWithContent = data[i];
//           let item = new Mustache.render(itemTemplate, itemWithContent);
//           item = new DOMParser().parseFromString(item, "text/html").body
//             .firstChild;
//           let itemContent = new Mustache.render(itemTemplate, itemWithContent);
//           itemContent = new DOMParser().parseFromString(
//             itemContent,
//             "text/html"
//           ).body.firstChild;
//           navItemsWraper.appendChild(item);
//           navItemContentWrapper.appendChild(itemContent);
//         }
//         wrapper.appendChild(navItemsWraper);
//         wrapper.appendChild(navItemContentWrapper);
//         node.innerHTML = wrapper.innerHTML;
//       },
//     },
//   ],
// });

Vvveb.Components.extend("_section", "section/product-call-to-action", {
  classes: ["th-product-call-to-action"],
  name: "Product Call to Action",
  html: /*html*/ `
  <div class="th-product-call-to-action">
    <div class="row th-product-call-to-action-row">
      <div class="th-product-call-to-action-item col-4">item1</div>
      <div class="th-product-call-to-action-item col-4">item1</div>
      <div class="th-product-call-to-action-item col-4">item1</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Product Call Action",
      key: "product-call-to-action",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/product-call-action-item.html",
      apiUrl: "demo/krost/data/product-call-to-action.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-product-call-to-action-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-product-call-to-action-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data

              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});

Vvveb.Components.extend("_section", "section/product-related-family", {
  classes: ["th-product-related-family"],
  name: "Product Related family",
  html: /*html*/ `
  <div class="th-product-related-family">
    <div class="row th-product-related-family-row">
      <div class="th-product-related-family-item col-4">item1</div>
      <div class="th-product-related-family-item col-4">item1</div>
      <div class="th-product-related-family-item col-4">item1</div>   
    </div>
  </div>
  `,
  properties: [
    {
      name: "Product Related family",
      key: "product-related-family",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template:
        "demo/krost/js/builder/templates/product-related-family-item.html",
      apiUrl: "demo/krost/data/product-related-family.json",
      data: {
        options: [
          {
            label: "Two",
            value: "2",
          },
          {
            label: "Three",
            value: "3",
          },
          {
            label: "Four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-product-related-family-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-product-related-family-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }
        row.innerHTML = "";
        let apiCalls = async () => {
          try {
            // Fetch both template and JSON in parallel
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            let columnNumber = 12 / value;
            let columnClass = `col-${columnNumber}`;

            for (let i = 0; i < value; i++) {
              const itemData =
                {
                  data: data[i],
                  baseUrl: Vvveb.themeBaseUrl,
                } || {};
              // Use empty object if not enough data

              // Prepare item content using Mustache template
              const rendered = Mustache.render(template, itemData);

              // Convert string to DOM node
              let item = new DOMParser().parseFromString(rendered, "text/html")
                .body.firstChild;

              // Clear existing column class and set new one
              let classes = item.classList;
              [...classes].forEach((cls) => {
                if (cls.startsWith("col-")) {
                  classes.remove(cls);
                }
              });
              classes.add(columnClass);

              row.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(row);
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});

Vvveb.Components.extend("_section", "section/products-sustainablity", {
  classes: ["th-products-sustainablity"],
  name: "Products Sustainablity",
  html: /*html*/ `
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
              <div class="th-section-header-wrapper @@sectionClass">
                <h2 class="  th-section-title "> Made With Ocean Bound Plastic </h2>
                <div class="th-section-subtitle" style="font-size: ">
                  Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. Pellentesque libero donec sit egestas orci consequat est mauris duis.
                </div>
              </div>
              <div class="@@sectionRightClass">
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
  `,
});

Vvveb.ComponentsGroup["Krost Product Components"] = [
  "section/products",
  "section/product-related-slider",
  "section/product-also-like",
  "section/product-features",
  // "section/product-details-tabs",
  "section/product-call-to-action",
  "section/product-related-family",
  "section/products-sustainablity",
  "section/products-detail-under",
];
