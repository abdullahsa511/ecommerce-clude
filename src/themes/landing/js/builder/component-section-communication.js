Vvveb.Components.extend("_section", "section/showrooms", {
  classes: ["th-showrooms"],
  name: "Showrooms",
  html: /*html*/ `
      <div class="th-showrooms" style="min-height: 150px;">
            <div class="row th-showrooms-item-row">
                <div class="th-showrooms-item col-4">Item 1</div>
                <div class="th-showrooms-item col-4">Item 2</div>
            </div>
      </div>
      `,
  properties: [
    {
      name: "Showrooms",
      key: "contact-showroom",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/showrooms-item.html",
      apiUrl: "demo/krost/data/showrooms.json",
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
        return node.querySelectorAll(".th-showrooms-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-showrooms-item-row");
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

Vvveb.Components.extend("_section", "section/virtual-showrooms", {
  classes: ["th-virtual-showrooms"],
  name: "Virtual Showrooms",
  html: /*html*/ `
      <div class="th-virtual-showrooms" style="min-height: 150px;">
            <div class="row th-virtual-showrooms-row">
                <div class="th-virtual-showrooms-item col-md-6">Item 1</div>
                <div class="th-virtual-showrooms-item col-md-6">Item 2</div>
                <div class="th-virtual-showrooms-item col-md-6">Item 2</div>
                <div class="th-virtual-showrooms-item col-md-6">Item 2</div>
            </div>
      </div>
      `,
  properties: [
    {
      name: "Virtual Showrooms",
      key: "contact-virtual-showroom",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/virtual-showrooms-item.html",
      apiUrl: "demo/krost/data/virtual-showrooms.json",
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
        return node.querySelectorAll(".th-virtual-showrooms-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-virtual-showrooms-row");
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

Vvveb.Components.extend("_section", "section/sales-team", {
  classes: ["th-sales-team"],
  name: "Sales Team",
  html: /*html*/ `
      <div class="th-sales-team" style="min-height: 150px;">
            <div class="row th-sales-team-item-row">
                <div class="th-sales-team-item col-4">Item 1</div>
                <div class="th-sales-team-item col-4">Item 2</div>
            </div>
      </div>
      `,
  properties: [
    {
      name: "Sales Team",
      key: "sales-team",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/sales-team-item.html",
      apiUrl: "demo/krost/data/sales-team-melbourne.json",
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
        return node.querySelectorAll(".th-sales-team-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-sales-team-item-row");
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

Vvveb.Components.extend("_section", "section/book-now", {
  classes: ["th-booking-form-container"],
  name: "Book Now",
  html: /*html*/ `
  <div class="th-booking-form-container">
    <div class="th-booking-selected-member d-flex align-items-center mb-20">
      <div class="th-booking-member-avatar">
        <img src="/${Vvveb.themeBaseUrl}img/contact/member-avatar.png" alt="Member Avatar">
      </div>
      <div class="th-member-info pb-0">
        <p class="th-member-position">Meet With</p>
        <p class="th-member-name">Devon Lane</p>
      </div>
    </div>
    <form>
      <div class="th-input-group">
        <label for="choose-members">Member</label>
        <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-members" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
          <option value="superman" selected="">Superman</option>
          <option value="batman">Batman</option>
          <option value="galactus">Galactus</option>
          <option value="spawn">Spawn</option>
        </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Member" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-members-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-members-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-members-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-members-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
      </div>
      <div class="th-input-group">
        <label for="choose-location">Location</label>
        <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-location" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
          <option value="superman" selected="">Superman</option>
          <option value="batman">Batman</option>
          <option value="galactus">Galactus</option>
          <option value="spawn">Spawn</option>
        </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Location" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-location-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-location-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-location-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-location-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
      </div>
      <div class="th-input-group">
        <label for="choose-tour-type">Tour Type</label>
        <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-tour-type" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
          <option value="superman" selected="">Superman</option>
          <option value="batman">Batman</option>
          <option value="galactus">Galactus</option>
          <option value="spawn">Spawn</option>
        </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Tour Type" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-tour-type-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-tour-type-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-tour-type-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-tour-type-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
      </div>
    </form>
  </div>
  `,
  onInit: function (node, value, input) {
    this.items = fetch(this.apiUrl).then((res) => res.json());
  },
  buildItemsMenu: function (contentTabMenu) {},
  items: [],
  properties: [
    {
      name: "Avater Style",
      key: "book-now-member-style",
      inputtype: NumberInput,
      onChange: function (node, value, input, component, event) {
        let member = node.querySelector(".th-booking-member-avatar");
        member.querySelector("img").style.borderRadius = value + "%";
      },
    },
    {
      name: "Position Text",
      key: "book-now-member-position",
      inputtype: TextInput,
      onChange: function (node, value) {
        let positionText = node.querySelector(".th-member-position");
        if (positionText) positionText.textContent = value;
      },
    },
    {
      name: "Member Label",
      key: "book-now-member-label",
      inputtype: TextInput,
      onChange: function (node, value) {
        let label = node.querySelector("label[for='choose-members']");
        if (label) label.textContent = value;
      },
    },
    {
      name: "Location Label",
      key: "book-now-location-label",
      inputtype: TextInput,
      onChange: function (node, value) {
        let label = node.querySelector("label[for='choose-location']");
        if (label) label.textContent = value;
      },
    },
    {
      name: "Tour Type Label",
      key: "book-now-tour-type-label",
      inputtype: TextInput,
      onChange: function (node, value) {
        let label = node.querySelector("label[for='choose-tour-type']");
        if (label) label.textContent = value;
      },
    },
  ],
});
Vvveb.Components.extend("_section", "section/design-resources", {
  classes: ["th-design-resources"],
  name: "Design Resource",
  html: /*html*/ `
  <div class="th-design-resources">
    <div class="row th-design-resources-row">
      <div class="th-design-resources-item col-4">item1</div>
      <div class="th-design-resources-item col-4">item1</div>
      <div class="th-design-resources-item col-4">item1</div>   
    </div>
  </div>
  `,
  properties: [
    {
      name: "Design Resource",
      key: "design-resource",
      inputtype: SelectInput,
      validValues: ["2", "3", "4"],
      template: "demo/krost/js/builder/templates/desgin-resource-item.html",
      apiUrl: "demo/krost/data/desgin-resource-items.json",
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
        return node.querySelectorAll(".th-design-resources-item").length;
      },

      onChange: function (node, value, input) {
        let row = node.querySelector(".th-design-resources-row");
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
Vvveb.Components.extend("_section", "section/manufacturing-process", {
  classes: ["th-manufacturing-process"],
  name: "Manufacturing Process",
  html: /*html*/ `
  <div class="row align-items-center th-manufacturing-process">
        <!-- Left Content -->
      <div class="col-12 col-md-6 mb-4 mb-md-0 th-manf-process-content th-manufacturing-process-item">
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
        <div class="col-12 col-md-6 right-img th-manufacturing-process-item">
          <img src="/${Vvveb.themeBaseUrl}img/about/manufacturing.png" alt="Manufacturing Process" class="img-fluid rounded">
        </div>
    </div>
  `,
});

Vvveb.Components.extend("_section", "section/goverment-supplier", {
  classes: ["th-goverment-supplier"],
  name: "Goverment Supplier",
  html: /*html*/ `
  <div
  class="th-goverment-container th-goverment-supplier th-grid th-grid-cols-6 th-grid-rows-10 th-grid-row"
>
  <div
    class="th-grid-area th-goverment-supplier-item th-grid-area-1-1-7-6 background-image"
    style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplyar-img1.png"
  >
  </div>
  <div class="th-grid-area th-goverment-supplier-item th-grid-area-7-1-11-5">
    <h2 class="">Government supplier</h2>
    <h6 class="th-title-22 font-weight-400">
      Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas
      et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
    </h6>

    <div class="th-link ">
      <div class="th-link-text pr-5">Location</div>
      <div class="th-link-icon-btn">
        <i class="fa-regular fa-arrow-up degree-60"></i>
      </div>
    </div>
  </div>
  <div
    class="th-grid-area th-grid-area-3-5-8-7 background-image"
    style="
      background-image: url(/${Vvveb.themeBaseUrl}img/about/supplayer-img2.png);
    "
  ></div>
  <div class="th-grid-area th-goverment-supplier-item th-grid-area-8-5-11-7">
    <h5 class="">Environmental policy</h5>

    <h6 class="th-title-22 font-weight-400">
      Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas
      et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
    </h6>

    <div class="th-link">
      <div class="th-link-text pr-5">Read more</div>
      <div class="th-link-icon-btn">
        <i class="fa-regular fa-arrow-up degree-60"></i>
      </div>
    </div>
  </div>
</div>
  `,
});

Vvveb.Components.extend("_section", "section/th-goverment-supplier", {
  classes: ["th-goverment-supplier"],
  name: "Goverment Supplier",
  html: /*html*/ `
  <div
  class="th-goverment-container th-goverment-supplier th-grid th-grid-cols-6 th-grid-rows-10 th-grid-row"
>
  <div
    class="th-grid-area th-goverment-supplier-item th-grid-area-1-1-7-6 background-image"
    style="background-image: url(/${Vvveb.themeBaseUrl}img/about/supplyar-img1.png"
  >
  </div>
  <div class="th-grid-area th-goverment-supplier-item th-grid-area-7-1-11-5">
    <h2 class="">Government supplier</h2>
    <h6 class="th-title-22 font-weight-400">
      Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas
      et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
    </h6>

    <div class="th-link ">
      <div class="th-link-text pr-5">Location</div>
      <div class="th-link-icon-btn">
        <i class="fa-regular fa-arrow-up degree-60"></i>
      </div>
    </div>
  </div>
  <div
    class="th-grid-area th-grid-area-3-5-8-7 background-image"
    style="
      background-image: url(/${Vvveb.themeBaseUrl}img/about/supplayer-img2.png);
    "
  ></div>
  <div class="th-grid-area th-goverment-supplier-item th-grid-area-8-5-11-7">
    <h5 class="">Environmental policy</h5>

    <h6 class="th-title-22 font-weight-400">
      Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas
      et. Pellentesque libero donec sit egestas orci consequat est mauris duis.
    </h6>

    <div class="th-link">
      <div class="th-link-text pr-5">Read more</div>
      <div class="th-link-icon-btn">
        <i class="fa-regular fa-arrow-up degree-60"></i>
      </div>
    </div>
  </div>
</div>
  `,
});

Vvveb.Components.extend("_section", "section/request-catalogue", {
  classes: ["th-request-catalogue"],
  name: "Request Catalogue",
  html: /*html*/ `
  <div class="row request-catalogue th-request-catalogue">
    <div class="col-md-7 col-sm-12">
      <div class="th-requset-catalouge-content">
        <h2>Request Catalogue</h2>
        <p>Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet
          <br> egestas et. Pellentesque libero donec sit egestas orci.
        </p>
      </div>
      <div class="th-img-container th-request-catalouge-img">
        <img src="/${Vvveb.themeBaseUrl}img/contact/contact-location-one.jpeg" alt="">
      </div>
    </div>
  <div class="col-md-5 col-sm-12">
    <div class="th-request-catalouge-form">
      <div class="form-container th-cf-wrapper overlap th-bg-cf-white">
        <form>
          <div class="form-group">
            <label for="email">Email* (required)</label>
            <input type="email" id="email" class="form-control" placeholder="Email*" required="">
          </div>
          <div class="form-group">
            <label for="company">Company Name* (required)</label>
            <input type="text" id="company" class="form-control" placeholder="Company Name*" required="">
          </div>
          <div class="form-group">
            <label for="full-name">Full Name* (required)</label>
            <input type="text" id="full-name" class="form-control" placeholder="Full Name*" required="">
          </div>
          <div class="form-group">
            <label for="type">Type</label>
            <select id="type" class="form-control">
              <option value="" disabled="" selected="">Type</option>
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
            <button type="submit" class="th-btn-primary mt-30">
              Submit <span>↗</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
  `,

  properties: [
    {
      name: "Change Full Name Label",
      key: "full-name-label",
      inputtype: TextInput,
      onChange: function (node, value) {
        let label = node.querySelector("label[for='full-name']");
        if (label) {
          label.innerText = value;
        }
      },
    },
    {
      name: "Add Custom Field",
      key: "add-custom-field",
      inputtype: TextInput,
      onChange: function (node, value) {
        if (!value) return;

        let form = node.querySelector("form");
        if (!form) return;

        let formGroup = document.createElement("div");
        formGroup.className = "form-group";

        formGroup.innerHTML = `
          <label for="${value.toLowerCase()}">${value}</label>
          <input type="text" id="${value.toLowerCase()}" class="form-control" placeholder="${value}">
        `;

        form.insertBefore(
          formGroup,
          form.querySelector(".form-group:last-child")
        );
      },
    },
  ],
});

Vvveb.Components.extend("_section", "section/contact-us", {
  classes: ["th-contact-us"],
  name: "Contact Us",
  html: /*html*/ `
  <div class="row th-contact-us">
  <div class="col-lg-6 th-connect-us-content-wrapper">
    <div class="th-connect-us-content d-flex flex-column justify-content-between">
      <div class="pb-70">
        <h2 class="section-title ">Connect With Us</h2>
        <div class="section-subtitle ">
          Lorem ipsum dolor sit amet consectetur.
        </div>
      </div>
      <div class="th-img-container mt-auto">
        <img src="/demo/krost/img/contact-us/connect.png" alt="Sydney Showroom">
      </div>
    </div>
  </div>
  <div class="col-lg-6 position-relative d-flex justify-content-center mb-60">
    <div class="form-container th-cf-wrapper overlap @@class">
      <form>
        <div class="form-group">
          <label for="email">Email* (required)</label>
          <input type="email" id="email" class="form-control" placeholder="Email*" required="">
        </div>
        <div class="form-group">
          <label for="company">Company Name* (required)</label>
          <input type="text" id="company" class="form-control" placeholder="Company Name*" required="">
        </div>
        <div class="form-group">
          <label for="full-name">Full Name* (required)</label>
          <input type="text" id="full-name" class="form-control" placeholder="Full Name*" required="">
        </div>
        <div class="form-group">
          <label for="type">Type</label>
          <select id="type" class="form-control">
            <option value="" disabled="" selected="">Type</option>
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
            Submit <span>↗</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
  `,
  properties: [
    
  ],
});

Vvveb.Components.extend("_section", "section/video-gallery", {
  classes: ["th-video-gallery"],
  name: "Contact Us",
  html: /*html*/ `
  <style>
    .inline-video-gallery-container {
      width: 100%;
      aspect-ratio: 16 / 9;
      position: relative;
    }
  </style>
  <div id="th-gallery-id" class="inline-video-gallery-container">
      <div class="inline-video-gallery-thumbnails-left" style="z-index: 1000;"></div>
  </div>

  <script>
      var jsonFile = "/demo/krost/data/gallary-who-we-are.json";
      if(jsonFile){
          fetch(jsonFile)
          .then(response => response.json())
          .then(data => {
                  console.log(data);
                  const thGallery = Vvveb.Builder.frameBody.querySelector("#th-gallery-id");
                  const gallery = lightGallery(thGallery, {
                  container: thGallery,
                  dynamic: !0,
                  thumbnail: !0,
                  swipeToClose: !1,
                  addClass: 'lg-inline',
                  mode: 'lg-scale-up',
                  slideShowAutoplay: !1,
                  autoPlay: !1,
                  hash: !1,
                  pager: !1,
                  closable: !1,
                  showMaximizeIcon: !0,
                  rotate: !0,
                  download: !0,
                  thumbnailsPosition: 'left',
                  plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
                  appendSubHtmlTo: '.lg-outer',
                  autoplayFirstVideo: !1,
                  dynamicEl: data
              });
              gallery.openGallery();
          });
      }
  </script>
  `,
});

Vvveb.Components.extend("_section", "section/section-header", {
  classes: ["th-section-header"],
  name: "Section Header",

  html: /* html */ `
  <div class="th-section-header">
    <div class="th-section-header-wrapper left flex-1">
      <h2 class="th-section-title th-sec-title">Featured Projects</h2>
      <div class="th-section-subtitle">
        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
      </div>
    </div>
    <div class="right">
      <div class="th-section-header-link">
        <span class="th-section-header-link-text View All ProjectsClass">View All Projects</span>
        <span class="th-section-header-link-btn">
          <i class="fa-regular fa-arrow-up degree-60"></i>
        </span>
      </div>
    </div>
  </div>
  `,
  nodes: ["div.th-section-header"],
  properties: [
    {
      name: "Section Heading",
      key: "section-heading-tag",
      inputtype: SelectInput,

      data: {
        options: [
          { value: "1", text: "H1" },
          { value: "2", text: "H2" },
          { value: "3", text: "H3" },
          { value: "4", text: "H4" },
          { value: "5", text: "H5" },
          { value: "6", text: "H6" },
        ],
      },

      init: function (node) {
        const heading = node.querySelector(".th-sec-title");
        if (heading) {
          const match = heading.tagName.match(/H(\d)/);
          if (match) {
            return match[1];
          }
        }
        return "2";
      },

      onChange: function (node, value) {
        const oldHeading = node.querySelector(".th-sec-title");
        const parent = oldHeading.parentNode;
        if (!oldHeading) return;

        const newTag = "h" + value;
        const newHeading = document.createElement(newTag);
        newHeading.innerHTML = oldHeading.innerHTML;
        newHeading.className = oldHeading.className;
        newHeading.classList.remove("th-section-title");
        console.log(newHeading);
        parent.replaceChild(newHeading, oldHeading);
        return node;
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/input-choices", {
  classes: ["th-input-choices"],
  name: "Input Choices",
  html: /*html*/`<div class="th-choices-select"></div>`,
  

});

Vvveb.ComponentsGroup["Krost Communication Section Containers"] = [
  "section/showrooms",
  "section/virtual-showrooms",
  "section/sales-team",
  "section/book-now",
  "section/design-resources",
  "section/manufacturing-process",
  "section/goverment-supplier",
  "section/request-catalogue",
  "section/section-header",
];
