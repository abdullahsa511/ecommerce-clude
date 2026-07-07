function createSlider(className, setup = {}) {
  // Update className with random ID
  const randomId = Math.floor(100000 + Math.random() * 900000); // Generate 6 digit random number
  const ignoreClassName = "ignore-slider";
  let newClassName = className + randomId;
  const element = Vvveb.Builder.frameBody.querySelector(
    "." + className + ":not(." + ignoreClassName + ")"
  );
  element.classList.remove(className);
  element.classList.add(newClassName);
  const newClassNameSelector = "." + newClassName;

  new Swiper(Vvveb.Builder.frameBody.querySelector(newClassNameSelector), {
    grabCursor: true,
    slidesPerView: setup.slidesPerView || 3.3,
    spaceBetween: 20, // Space between slides
    breakpoints: setup.breakpoints || {
      0: { slidesPerView: 2.3 },
      576: { slidesPerView: 2.3 }, // Fixed: Number instead of a string
      768: { slidesPerView: 2.3 },
      992: { slidesPerView: 2.3 },
      1200: { slidesPerView: 3.3 },
    },
    scrollbar: {
      el: ".swiper-scrollbar",
      hide: false, // Ensures scrollbar remains visible
      draggable: true,
    },
  });
  element.classList.remove(newClassName);
  element.classList.add(className);
  element.classList.add(ignoreClassName);
}

function createInputChoices(selector, setup = {}) {
  if(typeof Choices !== 'undefined'){
    let elements = Vvveb.Builder.frameBody.querySelectorAll(selector)
    if(elements){
      elements.forEach(element => {
        new Choices(element, setup);
      });
    }
  }
}

function createLightGallery(jsonFile, selector) {
  if(jsonFile){
    fetch(jsonFile)
    .then(response => response.json())
    .then(data => {
            const thGallery = Vvveb.Builder.frameDoc.querySelector(selector);
            const context = Vvveb.Builder.frameDoc;
            const gallery = window.lightGallery(context, thGallery, {
            licenseKey: 'AK890-BC3456-EN999-33BR77',
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
            plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
            appendSubHtmlTo: '.lg-outer',
            autoplayFirstVideo: !1,
            dynamicEl: data
        });
      gallery.openGallery();
    });
  }
}

function addLightGalleryScript(jsonFile, selector){
  let scriptTag = document.getElementById("video-gallery-script");
    if(!scriptTag){
      scriptTag = document.createElement("script");
      scriptTag.id = "video-gallery-script";
      document.body.appendChild(scriptTag);
    }
    let scripts = `
        const galleryContainer = document.querySelector("${selector}");
        galleryContainer.innerHTML = '<div class="inline-video-gallery-thumbnails-left" style="z-index: 1000;"></div>';
        var jsonFile = "${jsonFile}";
        if(jsonFile){
            fetch(jsonFile)
            .then(response => response.json())
            .then(data => {
                  const thGallery = document.querySelector("${selector}");
                  const gallery = lightGallery(thGallery, {
                  licenseKey: 'AK890-BC3456-EN999-33BR77',
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
                  plugins: [lgZoom, lgFullscreen, lgPager, lgRotate, lgShare, lgThumbnail, lgVideo],
                  appendSubHtmlTo: '.lg-outer',
                  autoplayFirstVideo: !1,
                  dynamicEl: data
              });
              gallery.openGallery();
            });
        }
    `
    scriptTag.innerHTML = scripts;
}

const VvvebSectionListInit = Vvveb.SectionList.init;
const ExtendedVvvebSectionList = {
  ...Vvveb.SectionList,
  init: function () {
    VvvebSectionListInit.call(this);
    this.executeScripts();
    this.onSectionHover();
  },
  executeScripts: function () {
    document
      .querySelector(".sections-list")
      .addEventListener("click", function (e) {
        let element = e.target.closest(".add-section-btn");
        if (element) {
          let item = element.closest("li");
          let section = Vvveb.Sections.get(item.dataset.type);
          if (section.commands && Array.isArray(section.commands)) {
            section.commands.forEach((cmd) => {
              if (cmd.execute && typeof cmd.execute === "function") {
                setTimeout(() => {
                  cmd.execute();
                }, 200);
              }
            });
          }
        }
      });
  },
  onSectionHover: function () {
    document.querySelector(".block-preview").addEventListener("mouseover", function () {
      let img = document.querySelector(".block-preview img");
      img.setAttribute("src", "");
      img.style.display = "none";
    });
  },
};
const VvvebBuilderInit = Vvveb.Builder.init;
const ExtendedVvvebBuilder = {
  ...Vvveb.Builder,
  init: function (url, callback) {
    VvvebBuilderInit.call(this, url, callback);
    const self = this;
    window.addEventListener("vvveb.iframe.loaded", function (event) {
      self._executeScriptsOnMouseUp(self);
    });
    setTimeout(() => {
      this._initDragDropExtended(self);
    }, 10);
  },
  _initDragDropExtended: function (self) {
    document.addEventListener("mousedown", function (event) {
    let element = event.target.closest(".drag-elements-sidepane ul > li > ol > li[data-drag-type]");
    if (element && event.which == 1) {
      if (self.designerMode == false) {
          document.querySelector("#dragElement-clone").remove();
          self.iconDrag = Object.assign(document.createElement("img"), {
            id: "dragElement-clone",
            src: self.component.image,
            style: `
                  z-index: 100;
                  position: absolute;
                  width: 64px;
                  height: 64px;
                  top: ${event.clientY}px;
                  left: ${event.clientX}px;
              `,
          });
          document.body.append(self.iconDrag);
        }
    }
    });
  },
  _executeScriptsOnMouseUp: function (self) {
    const executeScripts = function () {
      console.log("executeScripts");

      let section = self.component;

      if (section.commands && Array.isArray(section.commands)) {
        section.commands.forEach((cmd) => {
          if (cmd.execute && typeof cmd.execute === "function") {
            setTimeout(() => {
              cmd.execute();
              checkPluginIsWorking();
            }, 200);
          }
        });
      }
    };
    self.frameBody.addEventListener("mouseup", executeScripts);
  },
};

Vvveb.SectionList = ExtendedVvvebSectionList;
Vvveb.Builder = ExtendedVvvebBuilder;

//Comment adedd for zahidul branch to fix
//

// This is how you can extend javascript objects.
// You can extend any object, not just Vvveb.Builder.
// This is useful for adding new methods to objects.
// This is a concept of abstraction in object oriented programming.
// You can extend the base object and add new methods to it.

// Example: You can use the extended object by calling the init method.
// The init method will call the parent method and then the new method.
// This is a way to extend the base object without modifying the original object.

// Create base object
var ob = {
  name: "John",
  init: function () {
    console.log("init First", this.name);
  },
};
ob.init();

// Store reference to original init method
const parentInit = ob.init;

// Extend object by creating new object that inherits from original
const extendedOb = {
  ...ob,
  name: "Jane",
  init: function (url) {
    // Call parent method using stored reference
    parentInit.call(url);
    console.log("init Init Second");
  },
};

// Assign extended object back to original reference
ob = extendedOb;
ob.init();
