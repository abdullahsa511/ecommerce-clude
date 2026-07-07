Vvveb.Components.extend("_section", "section/all-projects", {
  classes: ["th-all-projects"],
  name: "All Projects",
  html: /*html*/ `
      <div class="th-all-projects" style="min-height: 150px;">
            <div class="row th-all-projects-item-row">
                <div class="th-all-projects-item col-4">Item 1</div>
                <div class="th-all-projects-item col-4">Item 1</div>
                <div class="th-all-projects-item col-4">Item 1</div>
            </div>
      </div>
      `,
  properties: [
    {
      name: "All Projects",
      key: "all-projects",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/all-projects-item.html",
      apiUrl: "demo/krost/data/all-projects-items.json",
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
            label: "four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-all-projects-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-all-projects-item-row");
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
Vvveb.Components.extend("_section", "section/project-detial-main", {
  classes: ["th-project-detial-main"],
  name: "Project Detail Main",
  html: /*html*/ `
  <div id="lorem-penetrating">
    <div class="th-grid th-project-detial-main">
      <div class="item-1">
        <img src="/demo/krost/img/project-detail/main-banner-2-resize.png" alt="" class="first-img">
      </div>
      <div class="item-2">
        <div class="th-lorem-text">
          <h2 class="pt-40">Lorem Ipsum</h2>
          <span>Pellentesque sodales lacinia diam, vel lacinia lectus ultrices id. Maecenas viverra justo nec mauris pulvinar dignissim. Proin quis arcu eu velit vehicula fermentum. Ut pharetra, nunc sed fringilla fermentum, nulla eros rhoncus felis, ac varius ligula quam nec nisl. Proin ac tellus sed dui ultrices tempus. Duis ut arcu et diam lacinia cursus nec non justo.
            <br>
            <br>
            Aliquam malesuada tortor ut dolor suscipit, id convallis ipsum lacinia. Curabitur feugiat lectus non sem ullamcorper, nec finibus nulla consequat.
          </span>
        </div>
      </div>
      <div class="item-3">
        <img src="/demo/krost/img/project-detail/main-banner-2-resize.png" alt="" class="second-img">
      </div>
    </div>
  </div>
  
      `,
  properties: [
    {
      name: "Swap Image",
      key: "swap_image",
      inputtype: SelectInput,
      validValues: ['left', 'right'],
      data: {
        options: [
          {
            label: "Left",
            value: "left",
          },
          {
            label: "Right",
            value: "right",
          },
        ],
      },
      init: function (node, value, input) {
        return 'right'
      },
      onChange: function (node, value, input) {
        let column = node.getAttribute('style');
        let item2 = node.querySelector(".item-2");
        let item3 = node.querySelector(".item-3");
        item2.setAttribute('class', 'item-3');
        item3.setAttribute('class', 'item-2');
        item2 = node.querySelector(".item-2");
        item3 = node.querySelector(".item-3");
        if(item3.querySelector('.th-lorem-text')){
          item3.querySelector('.th-lorem-text').style.paddingLeft = "40px";
          if(column){
            item3.setAttribute('style', "padding-left: 40px; grid-area: 8 / 2 / 15 / 4; !important");
          }else{
            item3.setAttribute('style', "padding-left: 40px; grid-area: 8 / 2 / 15 / 3; !important");
          }
          item2.setAttribute('style', "grid-area: 6 / 1 / 15 / 2; !important");
        
        }
        if(item2.querySelector('.th-lorem-text')){
          item2.setAttribute('style', "");
          if(column){
            item3.setAttribute('style', "grid-area: 6 / 3 / 15 / 4; !important");
            item2.setAttribute('style', "grid-area: 8 / 1 / 15 / 3; !important");
          }else{
            item3.setAttribute('style', "");
          }
        }
      },
    },
    {
      name: "Grid Size",
      key: "grid_size",
      inputtype: SelectInput,
      validValues: [2, 3],
      data: {
        options: [
          {
            label: "2 Columns",
            value: 2,
          },
          {
            label: "3 Columns",
            value:  3,
          },
        ],
      },
      init: function (node, value, input) {
        let column = node.getAttribute('style');
        if(column){
          return 3;
        }
        return 2;
      },
      onChange: function (node, value, input) {
        let column = node.getAttribute('style');
        let item1 = node.querySelector(".item-1");
        let smallImage = node.querySelector(".item-2");
        let isTwo = true;
        if(!smallImage.querySelector('img')){
          smallImage = node.querySelector(".item-3");
          isTwo = false;
        }
        if(value === "3"){
          node.setAttribute('style', "grid-template-columns: repeat(3, 1fr);");
          item1.setAttribute('style', "grid-area: 1 / 1 / 8 / 4;!important");
          if(!isTwo){
            smallImage.setAttribute('style', "grid-area: 6 / 2 / 15 / 4;!important");
          }
        }else{
          node.setAttribute('style', "");
          if(!isTwo){
            smallImage.setAttribute('style', "");
          }
        }
      },
    },
  ],
});

Vvveb.Components.extend("_section", "section/project-gallery", {
  classes: ["th-projects-gallery"],
  name: "Projects Gallery",
  html: /*html*/ `
  <div class="th-projects-gallery section-body">
    <div class="row th-projects-gallery-row">
      <div class="col-md-2 col-sm-12 th-projects-gallery-item">
        <div class="nav flex-column th-gallery-thumb-container" id="gallery-tabs" role="tablist">
          <button class="th-gallery-thumb active" id="img-1-tab" data-bs-toggle="pill" data-bs-target="#img-1" type="button" role="tab" aria-controls="img-1" aria-selected="true">
            <img src="/demo/krost/img/blog-detail/gallery-img-1.png" alt="gallery-img-1">
          </button>
          <button class="th-gallery-thumb " id="img-2-tab" data-bs-toggle="pill" data-bs-target="#img-2" type="button" role="tab" aria-controls="img-2" aria-selected="false" tabindex="-1">
            <img src="/demo/krost/img/blog-detail/gallery-img-2.png" alt="gallery-img-2">
          </button>
          <button class="th-gallery-thumb " id="img-3-tab" data-bs-toggle="pill" data-bs-target="#img-3" type="button" role="tab" aria-controls="img-3" aria-selected="false" tabindex="-1">
            <img src="/demo/krost/img/blog-detail/gallery-img-3.png" alt="gallery-img-3">
          </button>
          <button class="th-gallery-thumb " id="img-4-tab" data-bs-toggle="pill" data-bs-target="#img-4" type="button" role="tab" aria-controls="img-4" aria-selected="false" tabindex="-1">
            <img src="/demo/krost/img/blog-detail/gallery-img-4.png" alt="gallery-img-4">
          </button>
          <button class="th-gallery-thumb " id="img-5-tab" data-bs-toggle="pill" data-bs-target="#img-5" type="button" role="tab" aria-controls="img-5" aria-selected="false" tabindex="-1">
            <img src="/demo/krost/img/blog-detail/gallery-img-5.png" alt="gallery-img-5">
          </button>
        </div>
      </div>

      <div class="col-md-10 th-projects-gallery-item">
        <div class="tab-content th-gallery-img-container">
          <div class="tab-pane fade th-gallery-img active show" id="img-1" role="tabpanel" aria-labelledby="img-1-tab">
            <img src="/demo/krost/img/blog-detail/gallery-img-1.png" alt="gallery-img-1">
          </div>
          <div class="tab-pane fade th-gallery-img " id="img-2" role="tabpanel" aria-labelledby="img-2-tab">
            <img src="/img/blog-detail/gallery-img-2.png" alt="gallery-img-2">
          </div>
          <div class="tab-pane fade th-gallery-img " id="img-3" role="tabpanel" aria-labelledby="img-3-tab">
            <img src="/demo/krost/img/blog-detail/gallery-img-3.png" alt="gallery-img-3">
          </div>
          <div class="tab-pane fade th-gallery-img " id="img-4" role="tabpanel" aria-labelledby="img-4-tab">
            <img src="/demo/krost/img/blog-detail/gallery-img-4.png" alt="gallery-img-4">
          </div>
          <div class="tab-pane fade th-gallery-img " id="img-5" role="tabpanel" aria-labelledby="img-5-tab">
            <img src="/demo/krost/img/blog-detail/gallery-img-5.png" alt="gallery-img-5">
          </div>
        </div>
      </div>
    </div>
  </div>
        `,
  properties: [
    {
      name: "Projects Gallery",
      key: "projects-gallery-thumbs",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/projects-gallery-item.html",
      apiUrl: "demo/krost/data/project-gallery-thumbs.json",
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
            label: "four",
            value: "4",
          },
        ],
      },
      init: function (node, value, input) {
        return node.querySelectorAll(".th-projects-gallery-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-projects-gallery-row");
        if (!row) {
          console.warn("Row not found!");
          return;
        }

        row.innerHTML = "";

        const apiCalls = async () => {
          try {
            const [templateRes, dataRes] = await Promise.all([
              fetch(this.template),
              fetch(this.apiUrl),
            ]);

            const template = await templateRes.text();
            const data = await dataRes.json();

            const dataArray = data.slice(0, value).map((item, index) => {
              return {
                ...item,
                tabId: `gallery-tab-${index + 1}`,
                tabLabel: `gallery-tab-label-${index + 1}`,
                tabClass: index === 0 ? "active" : "",
              };
            });

            const rendered = Mustache.render(template, {
              data: dataArray,
              baseUrl: Vvveb.themeBaseUrl,
            });

            const parsedHtml = new DOMParser().parseFromString(
              rendered,
              "text/html"
            );

            const newElements = parsedHtml.body.children;
            Array.from(newElements).forEach((el) => {
              row.appendChild(el);
            });
          } catch (error) {
            console.error("Error in onChange:", error);
          }
        };

        apiCalls();
      },

      
    },
  ],
});

Vvveb.Components.extend("_section", "section/product-slider-test", {
  classes: ["th-product-slider"],
  name: "Product Slider",
  html: /*html*/ `
  <div class="th-grid ">
  <div class="item-1">
    <img src="/${Vvveb.themeBaseUrl}img/blog-detail/gallery-img-1.png" alt="" class="first-img">
  </div>
  <div class="item-2">
    <div class="th-lorem-text">
      <h2 class="pt-40">Lorem Ipsum</h2>
      <span>Pellentesque sodales lacinia diam, vel lacinia lectus ultrices id. Maecenas viverra justo nec mauris pulvinar dignissim. Proin quis arcu eu velit vehicula fermentum. Ut pharetra, nunc sed fringilla fermentum, nulla eros rhoncus felis, ac varius ligula quam nec nisl. Proin ac tellus sed dui ultrices tempus. Duis ut arcu et diam lacinia cursus nec non justo.
        <br>
        <br>
        Aliquam malesuada tortor ut dolor suscipit, id convallis ipsum lacinia. Curabitur feugiat lectus non sem ullamcorper, nec finibus nulla consequat.
      </span>
    </div>
  </div>
  <div class="item-3">
    <img src="/${Vvveb.themeBaseUrl}img/blog-detail/gallery-img-1.png" alt="" class="second-img">
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

Vvveb.ComponentsGroup["Krost Projects Section Containers"] = [
  "section/all-projects",
  "section/project-detial-main",
  "section/project-gallery",
  "section/product-slider-test",
];
