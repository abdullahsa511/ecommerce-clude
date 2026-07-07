Vvveb.Components.extend("_base", "html/btn-gray-load-more", {
  image: "/icons/container.svg",
  name: "Btn Gray Load More",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="d-flex justify-content-center">
        <a href="contact.html" class="th-btn-common th-btn-gray th-btn-gray-load-more text-capitalize">
            <span class="mr-5">Load More</span>
        </a>
    </div>`,
});

Vvveb.Components.extend("_base", "html/btn-gray", {
  image: "/icons/container.svg",
  name: "Btn Gray",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="d-flex justify-content-center">
        <a href="contact.html" class="th-btn-common th-btn-gray text-capitalize">
            <span class="mr-5">Accept Quote</span>
        </a>
    </div>`,
});
Vvveb.Components.extend("_base", "html/btn-outline", {
  image: "/icons/container.svg",
  name: "Btn Outline",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="position-relative">
        <a href="contact.html" class="th-btn-common th-btn-outline text-capitalize">
        <span class="mr-5">
            Contact Sales
        </span>
        <i class="fa-regular fa-arrow-up degree-60"></i>
        </a>
    </div>`,
});

Vvveb.Components.extend("_base", "html/btn-play", {
  image: "/icons/button.svg",
  name: "Btn Play",
  htmlAttr: "class",
  html: /*html*/ `
    <button class="th-btn-play">
       <i class="fa-solid fa-play"></i>
    </button>
    `,
});

Vvveb.Components.extend("_base", "html/btn-pinboard", {
  image: "/icons/button.svg",
  name: "Btn Pinboard",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-btn-common th-add-to-pinboard position-absolute top-right-30">
      <i class="fa-solid fa-plus"></i>
    </div>
  `,
});
Vvveb.Components.extend("_base", "html/btn-primary", {
  image: "/icons/button.svg",
  name: "Btn Primary",
  htmlAttr: "class",
  html: /*html*/ `
    <a href="contact.html" class="th-btn-common th-btn-primary text-capitalize">
        <span class="mr-5">Comment</span>
    </a>`,
});
Vvveb.Components.extend("_base", "html/btn-white", {
  image: "/icons/button.svg",
  name: "Btn White",
  htmlAttr: "class",
  html: /*html*/ `
    <a href="contact.html" class="th-btn-common th-btn-white text-capitalize">
        <span class="mr-5">Comment</span>
    </a>`,
});
Vvveb.Components.extend("_base", "html/btn-white-large", {
  classes: ["th-btn-white"],
  image: "/icons/button.svg",
  name: "Btn White Large",
  htmlAttr: "class",
  html: /*html*/ `
        <a href="contact.html" class="th-btn-common th-btn-white text-capitalize">
            <span class="mr-5">Comment</span>
        </a>`,
});

Vvveb.Components.extend("_base", "html/btn-icon-link", {
  classes: ["th-btn-white"],
  image: "/icons/button.svg",
  name: "Btn Icon Link",
  htmlAttr: "class",
  html: /*html*/ `
        <a href="contact.html" class="th-btn-common th-btn-white text-capitalize">
            <span class="mr-5">Comment</span>
        </a>`,
});
Vvveb.Components.extend("_base", "html/btn-icon-link-deg", {
  image: "/icons/button.svg",
  name: "Btn Icon Link Deg",
  htmlAttr: "class",
  html: /*html*/ `
  <div class="th-link th-link-common">
        <div class="th-link-text pr-5">
            Take a Tour
        </div>
        <div class="th-link-icon">
            <i class="fa-regular fa-arrow-up degree-60"></i>
        </div>
    </div>`,
});
Vvveb.Components.extend("_base", "html/btn-link-html", {
  image: "/icons/button.svg",
  name: "Icon Html",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-link th-link-common">
        <div class="th-link-text pr-5">View Catalogue</div>
        <div class="th-link-icon">
            <i class="fa-regular fa-arrow-right"></i>
        </div>
    </div>`,
});

Vvveb.Components.extend("_base", "html/btn-title-icon-link", {
  image: "/icons/button.svg",
  name: "Btn Icon Link Large",
  htmlAttr: "class",
  html: /*html*/ `
  <div class="th-link th-link-common">
    <a href="link">
      <h6 class="th-title-20 font-weight-700"> Model Library </h6>
    </a>
  </div>
  `,
});

Vvveb.Components.extend("_base", "html/title-link", {
  image: "/icons/button.svg",
  name: "Btn Icon Link Large",
  htmlAttr: "class",
  html: /*html*/ `
  <div class="th-link th-link-common">
  <a href="link">
    <h6 class="th-title-20 font-weight-700"> Model Library </h6>
  </a>
</div>
  `,
});
Vvveb.Components.extend("_base", "html/btn-icon-link-small", {
  image: "/icons/button.svg",
  name: "Btn Icon Link Small",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-link th-link-common">
        <div class="th-link-text pr-5">
            Read More
        </div>
        <div class="th-link-icon-btn">
            <i class="fa-regular fa-arrow-up degree-60"></i>
        </div>
    </div>`,
});
Vvveb.Components.extend("_base", "html/link", {
  image: "/icons/button.svg",
  name: "link",
  htmlAttr: "class",
  html: /*html*/ `
  <div class="th-link th-link-common">
    <div class="th-link-text pr-5">Take a Tour</div>
  </div>
`,
});
Vvveb.Components.extend("_base", "html/btn-icon-link-large", {
  image: "/icons/button.svg",
  name: "Btn Icon Link large",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-section-header-link th-link-common">
      <span class="th-section-header-link-text All CategoriesClass">All Categories</span>
      <span class="th-section-header-link-btn">
        <i class="fa-regular fa-arrow-up degree-60"></i>
      </span>
    </div>  
  `,
});

Vvveb.Components.extend("_base", "html/btn-socil-icon", {
  image: "/icons/button.svg",
  name: "Btn Social Icon",
  htmlAttr: "class",
  html: /*html*/ `
    <li>
        <span class="link">
            <a href="https://www.linkedin.com/company/krost-furniture/">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
        </span>
    </li>
   `,
});

Vvveb.Components.extend("_base", "html/btn-icon-circular", {
  image: "/icons/button.svg",
  name: "Btn Icon Circular",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-btn-circle-icon th-link-common">
         <i class="fa-solid fa-phone"></i>
    </div>`,
});
Vvveb.Components.extend("_base", "html/btn-tab", {
  image: "/icons/button.svg",
  name: "Tab Btn",
  htmlAttr: "class",
  html: /*html*/ `
    <button class="nav-link th-tab-color active" id="contact-tab" data-bs-toggle="tab" data-bs-target="#model-3d" type="button" role="tab" aria-selected="true">
        2D / 3D MODELS
    </button>`,
});

Vvveb.Components.extend("_base", "html/btn-submit", {
  image: "/icons/button.svg",
  name: "Btn Submit",
  htmlAttr: "class",
  html: /*html*/ `
  <button type="submit" class="th-btn-primary th-btn-common mt-30">
    Submit <span>↗</span>
  </button>
  `,
});

Vvveb.Components.extend("_base", "html/th-btn-common", {
  classes: ["th-btn-common"],
  image: "/icons/button.svg",
  name: "Btn Common",
  htmlAttr: "class",
  html: /*html*/ `<a href="#" class="th-btn-common" data-hover-color="#bcbcbc"></a>`,
  properties: [
    {
      name: "Link",
      key: "btn-link",
      htmlAttr: "href",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.setAttribute("href", value);
        element.href = value;
        element.setAttribute(
          "onclick",
          "window.open('" + value + "', '_blank');"
        );
      },
    },
    {
      name: "Hover Style",
      key: "btn-hover-style",
      htmlAttr: "data-hover-color",
      inputtype: TextInput,
      init: function () {
        return "red";
      },
      onChange: function (node, value, input, component, event) {
        node.querySelector("style")?.remove();
        let style = document.createElement("style");
        style.innerHTML = `
          .th-btn-common:hover {
            background-color: ${value};
          }
        `;
        node.appendChild(style);
        return value;
      },
    },
    {
      name: "Border Color",
      key: "btn-border-color",
      htmlAttr: "style",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.style.borderColor = value;
      },
    },
    {
      name: "Background",
      key: "btn-background",
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
      name: "Btn Text Color",
      key: "btn-text-color",
      htmlAttr: "style",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.style.color = value;
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/th-link-common", {
  classes: ["th-link-common"],
  image: "/icons/button.svg",
  name: "Link Btn Common",
  htmlAttr: "class",
  html: /*html*/ `<div class="th-link-common"></div>`,
  properties: [
    {
      name: "Icon Background",
      key: "link-btn-background",
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
      name: "Icon Text Color",
      key: "icon-btn-text-color",
      htmlAttr: "style",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.style.color = value;
      },
    },
    {
      name: "Icon btn Hover Style",
      key: "icon-btn-hover-style",
      htmlAttr: "data-hover-color",
      inputtype: TextInput,
      init: function () {
        return "red";
      },
      onChange: function (node, value, input, component, event) {
        node.querySelector("style")?.remove();
        let style = document.createElement("style");
        style.innerHTML = `
          .th-btn-common:hover {
            background-color: ${value};
          }
        `;
        node.appendChild(style);
        return value;
      },
    },
    {
      name: "Padding",
      key: "icon-padding",
      htmlAttr: "style",
      inputtype: NumberInput,
      data: {
        min: -100,
        max: 100,
      },
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.style.padding = `${value}px`;
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/th-img-gallery-item", {
  classes: ["th-gallery-thumb"],
  image: "/icons/button.svg",
  name: "Img Gallery Item",
  htmlAttr: "class",
  html: /*html*/`
    <button class="th-gallery-thumb active" id="img-1-tab" data-bs-toggle="pill" data-bs-target="#img-1" type="button" role="tab" aria-controls="img-1" aria-selected="true">
      <img src="/${Vvveb.themeBaseUrl}img/blog-detail/gallery-img-1.png" alt="gallery-img-1">
    </button>
  `,
  properties: [
    {
      name: "Image",
      key: "img-gallery-item-image",
      htmlAttr: "src",
      inputtype: TextInput,
      onChange: function (node, value, input, component, event) {
        node.querySelector("img").src = value;
      },
    }
  ]
})

Vvveb.ComponentsGroup["Krost Buttons/Links"] = [
  "html/btn-gray-load-more",
  "html/btn-gray",
  "html/btn-outline",
  "html/btn-play",
  "html/btn-pinboard",
  "html/btn-primary",
  "html/btn-white",
  "html/btn-white-large",
  "html/btn-icon-link",
  "html/btn-icon-link-deg",
  "html/btn-title-icon-link",
  "html/title-link",
  "html/btn-icon-link-small",
  "html/link",
  "html/btn-icon-link-large",
  "html/btn-socil-icon",
  "html/btn-icon-circular",
  "html/btn-tab",
  "html/btn-link-html",
  "html/btn-submit",
  "html/th-img-gallery-item",
];
