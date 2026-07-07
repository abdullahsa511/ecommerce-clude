Vvveb.Components.extend("_section", "section/about-our-principles", {
  classes: ["th-about-our-principle"],
  name: "About Our Principle",
  html: /*html*/ `
    <div class="th-about-our-principle" style="min-height: 150px;">
          <div class="row th-about-our-principle-row">
              <div class="th-about-our-principle-item col-4">Item 1</div>
              <div class="th-about-our-principle-item col-4">Item 2</div>
              <div class="th-about-our-principle-item col-4">Item 3</div>
          </div>
    </div>
    `,
  properties: [
    {
      name: "About Our Principle",
      key: "about-our-principle",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/about-our-principle-item.html",
      apiUrl: "demo/krost/data/about-our-principle.json",
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
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-about-our-principle-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-about-our-principle-row");
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
Vvveb.Components.extend(
  "_section",
  "section/about-our-principles-icon-circle",
  {
    classes: ["th-about-our-principle-icon-circle"],
    name: "About Our Principle Icon",
    html: /*html*/ `
    <div class="th-about-our-principle-icon-circle" style="min-height: 150px;">
          <div class="row th-about-our-principle-icon-circle-row">
              <div class="th-about-our-principle-icon-circle-item col-4">Item 1</div>
              <div class="th-about-our-principle-icon-circle-item col-4">Item 2</div>
              <div class="th-about-our-principle-icon-circle-item col-4">Item 3</div>
          </div>
    </div>
    `,
    properties: [
      {
        name: "About Our Principle",
        key: "about-our-principle",
        inputtype: SelectInput,
        validValues: ["2", "3", "4"],
        template:
          "demo/krost/js/builder/templates/about-our-principle-icon-circle.html",
        apiUrl: "demo/krost/data/about-our-principle-icon-circle.json",
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
          ],
        },
        init: function (node, value, input) {
          return node.querySelectorAll(
            ".th-about-our-principle-icon-circle-item"
          ).length;
        },

        onChange: function (node, value, input) {
          let row = node.querySelector(
            ".th-about-our-principle-icon-circle-row"
          );
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
                let item = new DOMParser().parseFromString(
                  rendered,
                  "text/html"
                ).body.firstChild;

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
  }
);

Vvveb.Components.extend("_section", "section/about-our-sector-curosel", {
  classes: ["th-about-our-sector-curosel"],
  name: "About Our Sectors Curosel",
  html: /*html*/ `
      <div class="th-about-our-sector-curosel" style="min-height: 150px;">
            <div class="row th-about-our-sector-curosel-row">
                <div class="th-about-our-sector-curosel-item col-4">Item 1</div>
                <div class="th-about-our-sector-curosel-item col-4">Item 2</div>
                <div class="th-about-our-sector-curosel-item col-4">Item 3</div>
            </div>
      </div>
      `,
});

Vvveb.Components.extend("_section", "section/about-our-story", {
  classes: ["th-about-our-story"],
  name: "About Our Story",
  html: /*html*/ `
        <div class="th-about-our-story" style="min-height: 150px;">
              <div class="row th-about-our-story-row">
                  <div class="th-about-our-story-item col-6">Item 1</div>
                  <div class="th-about-our-story-item col-6">Item 2</div>
              </div>
        </div>
        `,
});

Vvveb.Components.extend("_section", "section/our-design-process", {
  classes: ["th-our-design-process"],
  name: "Our Design Process",
  html: /*html*/ `
    <div class="th-our-design-process" style="min-height: 150px;">
          <div class="row th-our-design-process-row"></div>
          <div class="row th-our-design-process-row"></div>
          <div class="row th-our-design-process-row"></div>
    </div>
    `,
  properties: [
    {
      name: "Our Design Process",
      key: "our-design-process",
      inputtype: SelectInput,
      validValues: [2, 4, 6],
      template: "demo/krost/js/builder/templates/our-design-process.html",
      apiUrl: "demo/krost/data/design-process.json",
      data: {
        options: [
          {
            label: "One Row",
            value: 2,
          },
          {
            label: "Two Rows",
            value: 4,
          },
          {
            label: "Three Rows",
            value: 6,
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-our-design-process-row").length;
      },

      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-our-design-process");
        grid.innerHTML = "";
        let apiCalls = async () => {
          try {
            const [templateResponse, dataResponse] = await Promise.all([
              fetch(this.template),
              fetch(this.dataUrl),
            ]);
            const template = await templateResponse.text();
            const jsonData = await dataResponse.json();

            for (let i = 0; i < value; i++) {
              const itemObject = {
                data: jsonData[i],
                baseUrl: Vvveb.themeBaseUrl,
              };
              console.log("item object", itemObject);
              let item = Mustache.render(template, itemObject);
              item = new DOMParser().parseFromString(item, "text/html").body
                .firstChild;
              grid.appendChild(item);
            }

            node.innerHTML = "";
            node.appendChild(grid);
          } catch (error) {
            console.error("Error fetching template or JSON:", error);
          }
        };
        apiCalls();
      },
    },
  ],
});

Vvveb.Components.extend("_section", "section/government-supplier", {
  classes: ["th-government-supplier"],
  name: "Government Supplier",
  html: /*html*/ `
  <div class="th-goverment-container th-grid th-grid-cols-6 th-grid-rows-10 th-grid-row">
    <div class="th-grid-area th-grid-area-1-1-7-6 background-image" 
        style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplyar-img1.png);">
    </div>
    <div class="th-grid-area th-grid-area-7-1-11-5">
      <h2 class="">Government supplier</h2>
      <h6 class="th-title-22 font-weight-400">
        Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. 
        Pellentesque libero donec sit egestas orci consequat est mauris duis.
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
    <div class="th-grid-area th-grid-area-3-5-8-7 background-image" 
      style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplayer-img2.png);">
    </div>
    <div class="th-grid-area th-grid-area-8-5-11-7">
      <h5 class="">Environmental policy</h5>
      <h6 class="th-title-22 font-weight-400">
        Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. 
        Pellentesque libero donec sit egestas orci consequat est mauris duis.
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
  </div>
  `,
  properties: [
    {
      name: "Large Image",
      key: "large-image",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        // Change area 1-1-7-6 setup
      },
    },
    {
      name: "Small Image",
      key: "small-image",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        // Change area 1-1-7-6 setup
      },
    },
    {
      name: "Gov. Supplier",
      key: "gov-supplier",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        // Change area 1-1-7-6 setup
      },
    },
    {
      name: "Environmental Policy",
      key: "environmental-policy",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
       // Change area 1-1-7-6 setup
      },
    }
  ],
});

Vvveb.Components.extend("_section", "section/manufacturing-process", {
  classes: ["th-manufacturing-process"],
  name: "Manufacturing Process",
  html: /*html*/ `
  <div class="th-manfacturing-container container th-container">
    <div class="section-body">
      <div class="row align-items-center">
        <!-- Left Content -->
        <div class="col-12 col-md-6 mb-4 mb-md-0 th-manf-process-content">
          <h3>Manufacturing Process</h3>
          <p class="th-manufacturing-details">
            Lorem ipsum dolor sit amet consectetur. Egestas orci gravida
            amet egestas et. Pellentesque libero donec sit egestas orci
            consequat est mauris duis. In viverra hac vestibulum pretium.
          </p>
          <div class="link link-gap">
            <div class="link-text pr-5">Visit showroom</div>
            <div class="link-icon">
              <i class="fa-regular fa-arrow-right"></i>
            </div>
          </div>


          <div class="link">
            <div class="link-text pr-5">Learn More</div>
            <div class="link-icon">
              <i class="fa-regular fa-arrow-right"></i>
            </div>
          </div>
        </div>
        <!-- Right Image -->
        <div class="col-12 col-md-6 right-img">
          <img src="/${Vvveb.themeBaseUrl}img/about/manufacturing.png" alt="Manufacturing Process" class="img-fluid rounded">
        </div>
      </div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Manufacturing Process",
      key: "manufacturing-process",
      inputtype: TextInput,
    },
  ],
});

Vvveb.Components.extend("_section", "section/design-resources", {
  classes: ["th-design-resources"],
  name: "Design Resources",
  html: /*html*/ `
  <div class="th-design-resources section-body">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3">
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
        </div>
        <div class="col-md-3">
          <div class="th-item-resource">
            <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_dr-models.jpg" alt="Sample Image">
            <div class="th-item-resource-content">
              <div class="th-link">
                <a href="@@link">
                  <h6 class="th-title-20 font-weight-700"> Image Gallery </h6>
                </a>
              </div>
              <div class="description">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="th-item-resource">
            <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_dr-models.jpg" alt="Sample Image">
            <div class="th-item-resource-content">
              <div class="th-link">
                <a href="@@link">
                  <h6 class="th-title-20 font-weight-700"> Image Gallery </h6>
                </a>
              </div>
              <div class="description">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="th-item-resource">
            <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_dr-finish.jpg" alt="Sample Image">
            <div class="th-item-resource-content">
              <div class="th-link">
                <a href="@@link">
                  <h6 class="th-title-20 font-weight-700"> Fabrics </h6>
                </a>
              </div>
              <div class="description">Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Design Resources",
      key: "design-resources",
      inputtype: TextInput,
    },
  ],
});

Vvveb.Components.extend("_section", "section/contact-us-info", {
  classes: ["th-contact-us-info"],
  name: "Contact Us Info",
  html: /*html*/ `
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
  `,
  properties: [
    {
      name: "Contact Us Info",
      key: "contact-us-info",
      inputtype: TextInput,
    }
  ],
});

Vvveb.Components.extend("_section", "section/contact-us-form", {
  classes: ["th-connect-us-content"],
  name: "Contact Us Content",
  html: /*html*/ `
  <div class="th-connect-us-content d-flex flex-column justify-content-between">
    <div class="pb-70">
      <h2 class="section-title ">Connect With Us</h2>
      <div class="section-subtitle ">
        Lorem ipsum dolor sit amet consectetur.
      </div>
    </div>
    <div class="th-img-container mt-auto">
      <img src="/${Vvveb.themeBaseUrl}img/contact-us/connect.png" alt="Sydney Showroom">
    </div>
  </div>
  `,
  properties: [
    {
      name: "Contact Us Content",
      key: "contact-us-content",
      inputtype: TextInput,
    }
  ]
});

Vvveb.Components.extend("_section", "section/video-gallery", {
  classes: ["th-video-gallery"],
  name: "Video Gallery",
  template: "demo/krost/js/builder/templates/video-gallery.html",
  html: /*html*/ `
  <div class="th-video-gallery section-body">
    <div class="row">
      <div class="th-video-gallery-container col-md-12">
        <div class="bg-black">
          <div class="th-video-player-logo  text-center text-white">
            <img src="/${Vvveb.themeBaseUrl}img/logo_white.png" alt="" style="width: 155px">
          </div>
        </div>
        
      
      </div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Video Gallery",
      key: "video-gallery",
      inputtype: TextInput,
    }
  ]
});

Vvveb.ComponentsGroup["Krost About Section Containers"] = [
  "section/about-our-principles",
  "section/about-our-principles-icon-circle",
  "section/about-our-sector-curosel",
  "section/about-our-story",
  "section/our-design-process",
  "section/government-supplier",
  "section/manufacturing-process",
  "section/design-resources",
  "section/contact-us-info",
  "section/contact-us-form",
  "section/video-gallery"
];
