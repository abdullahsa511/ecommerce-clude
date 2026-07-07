// import { masonaryOnChange } from "./utils/masonary-handler";

Vvveb.Components.add("_section", {
  name: "Section Containers",
  properties: [
    {
      name: "Section Container",
      key: "th_section_container",
      inputtype: TextInput,
      htmlAttr: "class",
    },
  ],
});

Vvveb.Components.extend("_section", "section/product-slider", {
  classes: ["th-product-slider"],
  name: "Product Slider",
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
      // onChange: function (node, value, input) {
      //   let row = node.querySelector(".th-product-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;
      //   //If column number 2 add class col-6 to the item
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   //
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   //Item 1 and Item 2 Inner html dynamically change
      //   item.setAttribute("class", classes.toString());

      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },

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
Vvveb.Components.extend("_section", "section/feature-product-slider", {
  classes: ["th-feature-product-slider"],
  name: "Feature Product Slider",
  html: /*html*/ `
      <div class="th-feature-product-slider" style="min-height: 150px;">
          <div class="row th-feature-product-slider-row">
              <div class="th-feature-product-slider-item col-4">Item 1</div>
              <div class="th-feature-product-slider-item col-4">Item 2</div>
              <div class="th-feature-product-slider-item col-4">Item 3</div>
          </div>
      </div>`,
  properties: [
    {
      name: "Feature Product Slider Items",
      key: "feature-product_slider_items",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template:
        "demo/krost/js/builder/templates/feature-product-slider-item.html",
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
        return node.querySelectorAll(".th-feature-product-slider-item").length;
      },
      // onChange: function (node, value, input) {
      //   let row = node.querySelector(".th-feature-product-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;

      //   //If column number 2 add class col-6 to the item
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   //
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   //Item 1 and Item 2 Inner html dynamically change
      //   item.setAttribute("class", classes.toString());

      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-feature-product-slider-row");
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
Vvveb.Components.extend("_section", "section/blog-slider", {
  classes: ["th-blog-slider"],
  name: "Blog Slider",
  html: /*html*/ `
    <div class="th-blog-slider" style="min-height: 150px;">
        <div class="th-blog-slider-row row">
            <div class="th-blog-slider-item col-4">Item 1</div>
            <div class="th-blog-slider-item col-4">Item 2</div>
            <div class="th-blog-slider-item col-4">Item 3</div>
        </div>
    </div>
    `,
  properties: [
    {
      name: "Blog slider Item",
      key: "blog-slider-item",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/blog-slider-item.html",
      dataUrl: "demo/krost/data/blog-items.json",
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
        return node.querySelectorAll(".th-blog-slider-item").length;
      },
      //   let row = node.querySelector(".th-blog-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   item.setAttribute("class", classes.toString());
      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },
      // onChange: async function (node, value, input) {
      //   try {
      //     const [templateResponse, dataResponse] = await Promise.all([
      //       fetch(this.template),
      //       fetch(this.dataUrl),
      //     ]);

      //     const template = await templateResponse.text();
      //     const jsonData = await dataResponse.json();

      //     let row = node.querySelector(".th-blog-slider-row");

      //     if (!row) {
      //       row = document.createElement("div");
      //       row.className = "th-blog-slider-row row";
      //       node.appendChild(row);
      //     }

      //     row.innerHTML = "";

      //     let columnNumber = 12 / parseInt(value);
      //     let columnClass = `col-${columnNumber}`;

      //     for (let i = 0; i < value; i++) {
      //       let blogItemData = jsonData[i] || {}; // fallback in case of fewer items
      //       console.log("blog-item data", blogItemData);
      //       let rendered = Mustache.render(template, blogItemData);

      //       let element = new DOMParser().parseFromString(rendered, "text/html")
      //         .body.firstChild;

      //       // Update column class
      //       element.classList.forEach((cls) => {
      //         if (cls.startsWith("col-")) {
      //           element.classList.remove(cls);
      //         }
      //       });
      //       element.classList.add(columnClass);

      //       row.appendChild(element);
      //     }
      //   } catch (err) {
      //     console.error("Error in blog slider onChange:", err);
      //   }
      // },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-blog-slider-row");
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
              fetch(this.dataUrl),
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

Vvveb.Components.extend("_section", "section/feature-material-slider", {
  classes: ["th-featured-material-slider"],
  name: "Material Slider",
  html: /*html*/ `
    <div class="th-featured-material-slider" style="min-height: 150px;">
        <div class="th-featured-material-slider-row row">
            <div class="th-featured-material-slider-item col-4">Item 1</div>
            <div class="th-featured-material-slider-item col-4">Item 2</div>
            <div class="th-featured-material-slider-item col-4">Item 3</div>
        </div>
    </div>
    `,
  properties: [
    {
      name: "Featured Slider Item",
      key: "featured-material-slider-item",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template:
        "demo/krost/js/builder/templates/feature-material-slider-item.html",
      apiUrl: "demo/krost/data/featured-materials-slider.json",
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
        return node.querySelectorAll(".th-featured-material-slider-item")
          .length;
      },
      // onChange: function (node, value, input) {
      //   let row = node.querySelector(".th-featured-material-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   item.setAttribute("class", classes.toString());
      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-featured-material-slider-row");
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

Vvveb.Components.extend("_section", "section/projects-slider", {
  classes: ["th-featured-projects-slider"],
  name: "Project Slider",
  html: /*html*/ `
    <div class="th-featured-projects-slider" style="min-height: 150px;">
        <div class="th-featured-projects-slider-row row">
            <div class="th-featured-projects-slider-item col-4">Item 1</div>
            <div class="th-featured-projects-slider-item col-4">Item 2</div>
            <div class="th-featured-projects-slider-item col-4">Item 3</div>
        </div>
    </div>
    `,
  properties: [
    {
      name: "Featured Project Slider",
      key: "featured-project-slider",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template:
        "demo/krost/js/builder/templates/feature-project-slider-item.html",
      apiUrl: "demo/krost/data/feature-projects-items.json",
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
        return node.querySelectorAll(".th-featured-projects-slider-item")
          .length;
      },
      // onChange: function (node, value, input) {
      //   let row = node.querySelector(".th-featured-projects-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   item.setAttribute("class", classes.toString());
      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-featured-projects-slider-row");
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

Vvveb.Components.extend("_section", "section/instagram-slider", {
  classes: ["th-instagram-slider"],
  name: "Instagram Slider",
  html: /*html*/ `
    <div class="th-instagram-slider" style="min-height: 150px;">
        <div class="th-instagram-slider-row row">
            <div class="th-instagram-slider-item col-4">Item 1</div>
            <div class="th-instagram-slider-item col-4">Item 2</div>
            <div class="th-instagram-slider-item col-4">Item 3</div>
        </div>
    </div>
    `,
  properties: [
    {
      name: "Instagram Slider",
      key: "instagram-slider",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/instagram-slider-item.html",
      apiUrl: "demo/krost/data/products-instagram.json",
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
        return node.querySelectorAll(".th-instagram-slider-item").length;
      },
      // onChange: function (node, value, input) {
      //   let row = node.querySelector(".th-instagram-slider-row");
      //   row.innerHTML = "";
      //   let item = this.item;
      //   let columnNumber = 12 / value;
      //   let columnClass = `col-${columnNumber}`;
      //   item = new DOMParser().parseFromString(item, "text/html").body
      //     .firstChild;
      //   let classes = item.classList;
      //   classes.forEach((cls) => {
      //     if (cls.startsWith("col-")) {
      //       classes.remove(cls);
      //     }
      //   });
      //   classes.add(columnClass);
      //   item.setAttribute("class", classes.toString());
      //   for (let i = 0; i < value; i++) {
      //     item.innerHTML = `Item ${i + 1}`;
      //     row.appendChild(item.cloneNode(true));
      //   }
      //   node.innerHTML = "";
      //   node.appendChild(row);
      // },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-instagram-slider-row");
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

// Masonary
Vvveb.Components.extend("_section", "section/product-story-masonary", {
  classes: ["th-product-story-masonry"],
  name: "Product Story Masonary",
  html: /*html*/ `
  <div class="th-product-story-masonry" style="min-height: 150px;">
    <div class="th-masonry-grid">
        <div class="th-masonry-grid-item grid-col-span-7" style="min-height: 150px; background-color: #f0f0f0;">Item 1</div>
        <div class="th-masonry-grid-item grid-col-span-6" style="min-height: 150px; background-color: #f0f0f0;">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Product Story Masonary",
      key: "product-story-masonary",
      validValues: [2, 4],
      template: "demo/krost/js/builder/templates/masonry-product-story.html",
      dataUrl: "demo/krost/data/product-story-masonry.json",
      config: [
        { span: 7, item: 1 },
        { span: 6, item: 2 },
        { span: 6, item: 3 },
        { span: 7, item: 4 },
      ],
      inputtype: SelectInput,
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
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-masonry-grid-item").length;
      },

      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-masonry-grid");
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
                span: this.config[i].span,
                item: this.config[i].item,
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

Vvveb.Components.extend("_section", "section/featured-products-masonry", {
  classes: ["th-featured-products-masonry"],
  name: "Featured Products Masonry",
  html: /*html*/ `
  <div class="th-featured-products-masonry" style="min-height: 150px;">
    <div class="th-masonry-grid">
        <div class="th-masonry-grid-item grid-col-span-8" style="min-height: 150px; background-color: #f0f0f0;">Item 1</div>
        <div class="th-masonry-grid-item grid-col-span-5" style="min-height: 150px; background-color: #f0f0f0;">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Feature Masonary",
      key: "feature-product-masonary",
      validValues: [2, 4, 6],
      template: "demo/krost/js/builder/templates/masonry-feature-product.html",
      dataUrl: "demo/krost/data/featured-products-masonry.json",
      config: [
        { span: 8, item: 1 },
        { span: 5, item: 2 },
        { span: 5, item: 3 },
        { span: 8, item: 4 },
        { span: 8, item: 5 },
        { span: 5, item: 6 },
      ],
      inputtype: SelectInput,
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
        return node.querySelectorAll(".th-masonry-grid-item").length;
      },

      //     item: "1",
      //     calc: () => 2 + 4,
      //   };

      //   let grid = node.querySelector(".th-masonry-grid");
      //   grid.innerHTML = "";
      //   console.log(new Date().getTime());
      //   fetch(this.template)
      //     .then((response) => response.text())
      //     .then((template) => {
      //       console.log("template::", template);
      //       for (let i = 0; i < value; i++) {
      //         let itemObject = {};
      //         itemObject.span = this.config[i].span;
      //         itemObject.item = this.config[i].item;
      //         let item = Mustache.render(template, itemObject);
      //         item = new DOMParser().parseFromString(item, "text/html").body
      //           .firstChild;
      //         grid.appendChild(item);
      //       }
      //       node.innerHTML = "";
      //       node.appendChild(grid);
      //     });
      // },
      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-masonry-grid");
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
                span: this.config[i].span,
                item: this.config[i].item,
                data: jsonData[i],
                baseUrl: Vvveb.baseUrl,
              };
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

Vvveb.Components.extend("_section", "section/categories-masonry", {
  classes: ["th-categories-masonry"],
  name: "Categories Masonry",
  html: /*html*/ `
  <div class="th-categories-masonry" style="min-height: 150px;">
    <div class="th-masonry-grid">
        <div class="th-masonry-grid-item grid-col-span-8" style="min-height: 150px; background-color: #f0f0f0;">Item 1</div>
        <div class="th-masonry-grid-item grid-col-span-5" style="min-height: 150px; background-color: #f0f0f0;">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Categories Masonry",
      key: "categories-masonary",
      validValues: [2, 4, 6],
      template: "demo/krost/js/builder/templates/masonary-categories-item.html",
      dataUrl: "demo/krost/data/catagories-masonry.json",
      config: [
        { span: 8, item: 1 },
        { span: 5, item: 2 },
        { span: 5, item: 3 },
        { span: 8, item: 4 },
        { span: 8, item: 5 },
        { span: 5, item: 6 },
      ],
      inputtype: SelectInput,
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
        return node.querySelectorAll(".th-masonry-grid-item").length;
      },
      // onChange: masonaryOnChange(
      //   node,
      //   value,
      //   this.template,
      //   this.dataUrl,
      //   this.config
      // ),
      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-masonry-grid");
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
                span: this.config[i].span,
                item: this.config[i].item,
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

Vvveb.Components.extend("_section", "section/feature-projects-masonry", {
  classes: ["th-feature-projects-masonry"],
  name: "Feature Projects Masonary",
  html: /*html*/ `
  <div class="th-feature-projects-masonry" style="min-height: 150px;">
    <div class="th-masonry-grid">
        <div class="th-masonry-grid-item grid-col-span-8" style="min-height: 150px; background-color: #f0f0f0;">Item 1</div>
        <div class="th-masonry-grid-item grid-col-span-5" style="min-height: 150px; background-color: #f0f0f0;">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Feature Projects Masonary",
      key: "feature-projects-masonary",
      validValues: [2, 4, 6],
      template:
        "demo/krost/js/builder/templates/masonary-feature-projects.html",
      dataUrl: "demo/krost/data/feature-project-masonary.json",
      config: [
        { span: 7, item: 1 },
        { span: 6, item: 2 },
        { span: 6, item: 3 },
        { span: 7, item: 4 },
      ],
      inputtype: SelectInput,
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
        return node.querySelectorAll(".th-masonry-grid-item").length;
      },

      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-masonry-grid");
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
                span: this.config[i].span,
                item: this.config[i].item,
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

Vvveb.Components.extend("_section", "section/image-gallery-masonry", {
  classes: ["th-image-gallery-masonry"],
  name: "Image Gallery Masonary",
  html: /*html*/ `
  <div class="th-image-gallery-masonry">
    <div class="th-masonry-grid">
        <div class="th-masonry-grid-item grid-col-span-7">Item 1</div>
        <div class="th-masonry-grid-item grid-col-span-6">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Image Gallery Masonary",
      key: "image-gallery-masonary",
      validValues: ["2", "3", "4"],
      item: /*html*/ `<div class="th-masonry-grid-item grid-col-span-7">Item 1</div>`,
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
      init: function (node, value, input) {},
      onChange: function (node, value, input) {},
    },
  ],
});

Vvveb.Components.extend("_section", "section/our-story-masonry", {
  classes: ["th-our-story-masonry"],
  name: "Our Story Masonary",
  html: /*html*/ `
  <div class="th-our-story-masonry">
    <div class="th-masonry-grid">
      <div class="th-masonry-grid-item grid-col-span-8" style="min-height: 150px; background-color: #f0f0f0;">Item 1</div>
      <div class="th-masonry-grid-item grid-col-span-5" style="min-height: 150px; background-color: #f0f0f0;">Item 2</div>
    </div>
  </div>
  `,
  properties: [
    {
      name: "Our Story Masonary",
      key: "our-story-masonary",
      validValues: [2, 4],
      template: "demo/krost/js/builder/templates/masonary-our-history.html",
      dataUrl: "demo/krost/data/about-our-history-masonry.json",
      config: [
        { span: 7, item: 1 },
        { span: 6, item: 2 },
        { span: 6, item: 3 },
        { span: 7, item: 4 },
      ],
      inputtype: SelectInput,
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
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-masonry-grid-item").length;
      },
      onChange: function (node, value, input) {
        let grid = node.querySelector(".th-masonry-grid");
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
                span: this.config[i].span,
                item: this.config[i].item,
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

// feature and column based
Vvveb.Components.extend("_section", "section/feature-product", {
  classes: ["th-feature-product"],
  name: "Feature Product",
  html: /*html*/ `
  <div class="th-product-slider" style="min-height: 150px;">
        <div class="row th-feature-product-row">
            <div class="th-feature-product-item col-4">Item 1</div>
            <div class="th-feature-product-item col-4">Item 2</div>
            <div class="th-feature-product-item col-4">Item 3</div>
        </div>
    </div>
  `,
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
        return node.querySelectorAll(".th-feature-product-item").length;
      },
      onChange: function (node, value, input) {
        let row = node.querySelector(".th-feature-product-row");
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

Vvveb.ComponentsGroup["Krost Section Containers"] = [
  "section/product-slider",
  "section/feature-product-slider",
  "section/blog-slider",
  "section/feature-material-slider",
  "section/projects-slider",
  "section/instagram-slider",
  "section/product-story-masonary",
  "section/featured-products-masonry",
  "section/categories-masonry",
  "section/feature-projects-masonry",
  "section/image-gallery-masonry",
  "section/our-story-masonry",
  "section/about-our-principles",
  "section/about-our-principles-icon-circle",
];
