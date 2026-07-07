Vvveb.Components.extend("_base", "html/a-tag", {
  nodes: ["a"],
  image: "/icons/container.svg",
  name: "Link Tag",
  htmlAttr: "class",
  html: /*html*/ `<a href="#">Link</a>`,
  properties: [],
});
Vvveb.Components.extend("_base", "html/p-tag", {
  nodes: ["p"],
  image: "/icons/container.svg",
  name: "P Tag",
  htmlAttr: "class",
  html: /*html*/ `<p class="th-p th-tag-common">Paragraph</p>`,
  properties: [],
});
Vvveb.Components.extend("_base", "html/span-tag", {
  nodes: ["span"],
  image: "/icons/container.svg",
  name: "Span Tag",
  html: /*html*/ `<span class="th-span">Span</span>`,
  properties: [
    {
      name: "Font Size",
      key: "span-font-size",
      htmlAttr: "style",
      inputtype: NumberInput,
      data: {
        min: 0,
        max: 100,
      },
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.style.fontSize = `${value}px`;
      },
    },
    {
      name: "Text Algin",
      key: "span-text-algin",
      inputtype: SelectInput,
      validValues: blockQuoteAligns,
      data: {
        options: blockQuoteAlignSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, blockQuoteAligns);
      },
    },
    {
      name: "Background",
      key: "span-background",
      htmlAttr: "class",
      validValues: krBackgrounds,
      inputtype: SelectInput,
      data: {
        options: krBackgroundsOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBackgrounds);
      },
    },
    {
      name: "Text Color",
      key: "span-text-color",
      htmlAttr: "style",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.style.color = value;
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/product-tag", {
  classes: ["th-tag"],
  image: "/icons/container.svg",
  name: "Product Tag",
  htmlAttr: "class",
  html: /*html*/ `<div class="th-tag th-tag-common">AFRDI Certified</div>`,
  properties: [],
});

Vvveb.Components.extend("_base", "html/h1-tag", {
  nodes: ["h1"],
  image: "/icons/container.svg",
  name: "h1 Tag",
  html: /*html*/ `<h1 class="th-h1 th-tag-common">Heading One Tag</h1>`,
  properties: [],
});

Vvveb.Components.extend("_base", "html/li-tag", {
  nodes: ["li"],
  image: "/icons/container.svg",
  name: "li Tag",
  html: /*html*/ `<li class="th-tag-common">List item tag</li>`,
  properties: [],
});

// Need to know and fix this tag
Vvveb.Components.extend("_base", "html/input-text", {
  nodes: ["input"],
  image: "/icons/container.svg",
  name: "input text",
  html: /*html*/ `<input type="text" placeholder="Your Email Address Please">`,
  properties: [],
});
Vvveb.Components.extend("_base", "html/input-select", {
  nodes: [""],
  image: "/icons/container.svg",
  name: "input select",
  html: /*html*/ ``,
  properties: [],
});

Vvveb.Components.extend("_base", "html/item-tag", {
  image: "/icons/container.svg",
  name: " Item Tag",
  html: /*html*/ `
  <div class="th-tag th-tag-common">Some Tag Name Here</div>
  `,
  properties: [],
});

Vvveb.Components.extend("_base", "html/tag", {
  image: "/icons/container.svg",
  name: " Tag",
  html: /*html*/ `
  <div class="th-search-offcanvas-tag-container th-tag-common">
    <h6 class="th-search-offcanvas-tag-title th-title-22 mb-20">
      Popular Search Terms
    </h6>
    <div class="th-search-offcanvas-tag-item">
      <span>Lorem Ipsum</span>
      <span>Lorem Ipsum</span>
      <span>Lorem Ipsum</span>
      <span>Lorem Ipsum</span>
      <span>Lorem Ipsum</span>
    </div>
  </div>
  `,
  properties: [],
});

Vvveb.Components.extend("_base", "html/textarea", {
  class: ["text-section"],
  image: "/icons/container.svg",
  name: "Text Section",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="text-section th-tag-common">
        <textarea class="form-control" placeholder="Enter your message"></textarea>
    </div>
    `,
  properties: [],
});

Vvveb.Components.extend("_base", "html/th-tag-common", {
  classes: ["th-tag-common"],
  image: "/icons/button.svg",
  name: "Tag Common",
  htmlAttr: "class",
  html: /*html*/ `<div class="th-tag-common"></div>`,
  properties: [
    {
      name: "Font Size",
      key: "font-size",
      htmlAttr: "style",
      inputtype: NumberInput,
      data: {
        min: 0,
        max: 100,
      },
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.style.fontSize = `${value}px`;
      },
    },
    {
      name: "Text Algin",
      key: "text-algin",
      inputtype: SelectInput,
      validValues: blockQuoteAligns,
      data: {
        options: blockQuoteAlignSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, blockQuoteAligns);
      },
    },
    {
      name: "Background",
      key: "background",
      htmlAttr: "class",
      validValues: krBackgrounds,
      inputtype: SelectInput,
      data: {
        options: krBackgroundsOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBackgrounds);
      },
    },
    {
      name: "Text Color",
      key: "text-color",
      htmlAttr: "style",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.style.color = value;
      },
    },
  ],
});

Vvveb.ComponentsGroup["Krost Tags"] = [
  "html/p-tag",
  "html/span-tag",
  "html/product-tag",
  "html/h1-tag",
  "html/li-tag",
  "html/textarea",
  "html/a-tag",
  "html/input-text",
  "html/input-select",
  "html/item-tag",
  "html/tag",
];
