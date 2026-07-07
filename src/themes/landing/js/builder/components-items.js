Vvveb.Components.extend("_base", "html/masonry-item", {
  classes: ["th-masonry-grid-item"],
  image: "/icons/container.svg",
  html: /*html*/ `<div class="th-masonry-grid-item"></div>`,
  name: "Masonry GridItem",
  htmlAttr: "class",
  properties: [
    {
      name: "Top Position",
      key: "transform-top-bottom",
      htmlAttr: "style",
      inputtype: NumberInput,
      data: {
        min: -100,
        max: 100,
      },
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.style.transform = `translateY(${value}px)`;
      },
    },
    {
      name: "Padding Top",
      key: "padding-top",
      htmlAttr: "style",
      inputtype: NumberInput,
      data: {
        min: -100,
        max: 100,
      },
      onChange: function (node, value, input, component, event) {
        let element = node;
        element.style.paddingTop = `${value}px`;
      },
    },
    {
      name: "Padding",
      key: "masonary-item-padding",
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
    {
      name: "Background",
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

Vvveb.ComponentsGroup["Krost Item Components"] = ["html/masonry-item"];
