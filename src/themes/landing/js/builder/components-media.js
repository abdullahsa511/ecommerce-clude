Vvveb.Components.extend("_base", "html/image-gallery", {
  classes: [""],
  image: "/icons/product_gallery.svg",
  name: "Image Top Icon",
  htmlAttr: "class",
  html: /*html*/ `
      <div>
        working here...
      </div>
   `,
  properties: [],
});

Vvveb.Components.extend("_base", "html/image-top-icon", {
  classes: ["th-add-to-pinboard"],
  image: "/icons/button.svg",
  name: "Image Top Icon",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-add-to-pinboard position-absolute top-right-30 ">
        <i class="fa-solid fa-plus"></i>
    </div>
 `,
  properties: [],
});

Vvveb.Components.extend("_base", "html/image-circle-icon", {
  classes: ["th-add-to-pinboard"],
  image: "/icons/button.svg",
  name: "Image Top Icon",
  htmlAttr: "class",
  html: /*html*/ `
    <div class="th-item-img-card d-flex flex-column align-items-center">
        <div class="th-img-container">
            <img src="/${Vvveb.themeBaseUrl}img/product-detail/first circle.png" alt="" class="th-rounded-circle mb-3" style="max-width: 164px;">
        </div>
    </div>
   `,
  properties: [],
});

Vvveb.ComponentsGroup["Krost Media"] = [
  "html/image-top-icon",
  "html/image-circle-icon",
];
