function changeClass(node, value, classArray) {
  let _class = node.getAttribute("class");
  let classList = _class.split(" ");
  classList = classList.filter((item) => !classArray.includes(item));
  if (value) classList.push(value);
  node.setAttribute("class", classList.join(" "));

  return node;
}
krBgcolorClasses = [
  "bg-white",
  "gradient-body",
  "gr-bg1",
  "gr-bg2",
  "gr-bg3",
  "gr-bg4",
  "gr-bg5",
  "gr-bg6",
  "gr-bg7",
  "gr-bg8",
  "gr-bg8-sm",
  "gr-bg8-botto",
  "gr-bg9",
  "gr-bg10",
];

krBgcolorClasses = [
  "bg-white",
  "gradient-body",
  "gr-bg1",
  "gr-bg2",
  "gr-bg3",
  "gr-bg4",
  "gr-bg5",
  "gr-bg6",
  "gr-bg7",
  "gr-bg8",
  "gr-bg8-sm",
  "gr-bg8-botto",
  "gr-bg9",
  "gr-bg10",
];

krBootstrapColumns = [
  "col-sm-6",
  "col-sm-4",
  "col-sm-3",
  "col-sm-2",
  "col-sm-1",
  "col-sm-6 col-lg-3",
  "col-sm-6 col-lg-4",
];
krBootstrapColumnsOptions = [
  {
    value: "col-sm-6 col-lg-3",
    text: "Default",
  },
  {
    value: "col-sm-6 col-lg-4",
    text: "Large 3 & Small 2",
  },
];

blockQuoteAligns = ["left", "right", "center"];

blockQuoteAlignSelectOptions = [
  {
    value: "left",
    text: "Left",
  },
  {
    value: "right",
    text: "Right",
  },
  {
    value: "center",
    text: "Center",
  },
];

krBgcolorSelectOptions = [
  {
    value: "Default",
    text: "bg-white",
  },
  {
    value: "gradient-body",
    text: "Gradient Body",
  },
  {
    value: "gr-bg1",
    text: "Gradient 1",
  },
  {
    value: "gr-bg2",
    text: "Gradient 2",
  },
  {
    value: "gr-bg3",
    text: "Gradient 3",
  },
  {
    value: "gr-bg4",
    text: "Gradient 4",
  },
  {
    value: "gr-bg5",
    text: "Gradient 5",
  },
  {
    value: "gr-bg6",
    text: "Gradient 6",
  },
  {
    value: "gr-bg7",
    text: "Gradient 7",
  },
  {
    value: "gr-bg8",
    text: "Gradient 8",
  },
  {
    value: "gr-bg8-sm",
    text: "Gradient 8 Small",
  },
  {
    value: "gr-bg8-bottom",
    text: "Gradient 8 Bottom",
  },
  {
    value: "gr-bg9",
    text: "Gradient 9",
  },
  {
    value: "gr-bg10",
    text: "Gradient 10",
  },
];

krBootstrapColumns = [
  "col-sm-6",
  "col-sm-4",
  "col-sm-3",
  "col-sm-2",
  "col-sm-1",
  "col-sm-6 col-lg-3",
  "col-sm-6 col-lg-4",
];
krBootstrapColumnsOptions = [
  {
    value: "col-sm-6 col-lg-3",
    text: "Default",
  },
  {
    value: "col-sm-6 col-lg-4",
    text: "Large 3 & Small 2",
  },
];
krTextAlginOptions = [];

krBackgrounds = [
  "bg-white",
  "bg-black",
  "bg-gray",
  "bg-gray-light",
  "bg-gray-dark",
  "bg-gray-darker",
  "bg-gray-darkest",
  "bg-gray-darkest",
];

krBackgroundsOptions = [
  {
    value: "bg-white",
    text: "White",
  },
  {
    value: "bg-black",
    text: "Black",
  },
  {
    value: "bg-gray",
    text: "Gray",
  },
  {
    value: "bg-gray-light",
    text: "Gray Light",
  },
  {
    value: "bg-gray-dark",
    text: "Gray Dark",
  },
];
Vvveb.Components.extend("_base", "html/section", {
  classes: ["section"],
  image: "/icons/container.svg",
  html: `<div class="section" style="min-height:150px;"></div>`,
  name: "Section",
  properties: [
    {
      name: "Gradient Background",
      key: "gradient-background",
      htmlAttr: "class",
      validValues: krBgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: krBgcolorSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBgcolorClasses);
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/container", {
  classes: ["container", "th-container"],
  image: "icons/container.svg",
  html: '<div class="container th-container" style="min-height:150px;"></div>',
  name: "Container",
  properties: [
    {
      name: "Gradient Background",
      key: "gradient-background",
      htmlAttr: "class",
      validValues: krBgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: krBgcolorSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBgcolorClasses);
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/blockquote-container", {
  classes: ["th-block-quote"],
  image: "/icons/container.svg",
  html: '<div class=""th-block-quote"></div>',
  name: "Blockquote Container",
  htmlAttr: "class",
  properties: [
    {
      name: "Position",
      key: "position",
      htmlAttr: "data-position",
      inputtype: SelectInput,
      validValues: blockQuoteAligns,
      data: {
        options: blockQuoteAlignSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, blockQuoteAligns);
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/item-member", {
  classes: ["th-member-wrapper"],
  image: "/icons/container.svg",
  html: '<div class="th-member-wrapper col-sm-6 col-lg-3"></div>',
  name: "Item Member Container",
  htmlAttr: "class",
  properties: [
    {
      name: "Column Size",
      key: "column-size",
      htmlAttr: "class",
      validValues: krBootstrapColumns,
      inputtype: SelectInput,
      data: {
        options: krBootstrapColumnsOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBootstrapColumns);
      },
    },
  ],
});

Vvveb.Components.extend("_base", "html/blog-slider-container", {
  classes: ["swiper", "home-blog-slide"],
  image: "icons/container.svg",
  html: /*html*/ `<div class="swiper home-blog-slider">
    <div class="swiper-wrapper"></div>
  </div>`,
  name: "Blog Slider Container",
  htmlAttr: "class",
  properties: [
    {
      name: "Gradient Background",
      key: "gradient-background",
      htmlAttr: "class",
      validValues: krBgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: krBgcolorSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBootstrapColumns);
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/instagram-slider-container", {
  classes: ["swiper", "th-instagram-products-slider"],
  image: "icons/container.svg",
  html: /*html*/ `<div class="swiper th-instagram-products-slider">
    <div class="swiper-wrapper"></div>
  </div>`,
  name: "Instagram Slider Container",
  htmlAttr: "class",
  properties: [
    {
      name: "Gradient Background",
      key: "gradient-background",
      htmlAttr: "class",
      validValues: krBgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: krBgcolorSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBootstrapColumns);
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/booking-form-container", {
  classes: ["th-booking-form-container"],
  image: "icons/container.svg",
  html: /*html*/ `<div class="th-booking-form-container" style="min-height:150px;"></div>`,
  name: "Booking Form Container",
  htmlAttr: "class",
  properties: [],
});
Vvveb.Components.extend("_base", "html/bootstrap-row", {
  classes: ["row"],
  image: "/icons/container.svg",
  html: /*html*/ `<div class="row" style="min-height:150px;"><div class="col-12"></div></div>`,
  name: "Bootstrap Row",
  htmlAttr: "class",
  properties: [],
});
Vvveb.Components.extend("_base", "html/section-body", {
  classes: ["section-body"],
  image: "/icons/panel.svg",
  html: /*html*/ `<div class="section-body" style="min-height:150px;"></div>`,
  name: "Section Body",
  htmlAttr: "class",
  properties: [
    {
      name: "Gradient Background",
      key: "gradient-background",
      htmlAttr: "class",
      validValues: krBgcolorClasses,
      inputtype: SelectInput,
      data: {
        options: krBgcolorSelectOptions,
      },
      onChange: function (node, value, input) {
        return changeClass(node, value, krBgcolorClasses);
      },
    },
  ],
});
Vvveb.Components.extend("_base", "html/bootstrap-col", {
  classes: ["col-12"],
  image: "/icons/panel.svg",
  html: /*html*/ `<div class="col-12" style="min-height:150px;"></div>`,
  name: "Bootstrap Col",
  htmlAttr: "class",
});

Vvveb.ComponentsGroup["Krost Containers"] = [
  "html/section",
  "html/section-body",
  "html/container",
  "html/blog-slider-container",
  "html/blockquote-container",
  "html/booking-form-container",
  "html/instagram-slider-container",
  "html/bootstrap-row",
  "html/bootstrap-col",
];

Vvveb.Components.extend("_base", "html/img", {
  nodes: ["img"],
  image: "/icons/container.svg",
  html: /*html*/ `<img src="" alt="image">`,
  name: "Image",
  htmlAttr: "class",
  properties: [
    {
      name: "Image",
      key: "src",
      child:"img",
      htmlAttr: "src",
      inputtype: ImageInput,
      onChange: function (node, value, input) {
        console.log(node);
      },
    },
    {
      name: "Image Alt",
      key: "image-alt",
      htmlAttr: "alt",
      inputtype: TextInput,
    },
  ],
});

Vvveb.ComponentsGroup["Krost Html Components"] = ["html/img"];
