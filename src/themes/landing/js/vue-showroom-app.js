  document.addEventListener("DOMContentLoaded", function () {
    // Handle map region clicks
    const mapRegions = document.querySelectorAll(".th-map-region");
    mapRegions.forEach((region) => {
      region.addEventListener("click", function (event) {
        event.stopPropagation();
        const regionNumber = this.getAttribute("data-region");
        console.log("Map region clicked:", regionNumber);
        
        // Add active class to clicked region
        mapRegions.forEach((r) => r.classList.remove("active"));
        this.classList.add("active");
        
        // You can add custom logic here, such as:
        // - Scroll to corresponding card
        // - Show region details
        // - Trigger navigation
        // Example: Find and click corresponding card if exists
        const correspondingCard = document.querySelector(`[data-v-section-item][item-number="${regionNumber}"]`);
        if (correspondingCard) {
          correspondingCard.click();
        }
      });
    });

    const cards = document.querySelectorAll("[data-v-section-item]");
    let id = null;
    let highlight = null;
    let highlightRaf = null;
    let pendingHighlightData = null;
    cards.forEach((card) => {
      card.addEventListener("click", function (event) {
        bootstrapVueApp(card);
      });
    });

    async function bootstrapVueApp(card) {
      try {
        const itemNumber = card.getAttribute("item-id");
        // Remove any existing vue js root element #th-product-detail
        const existingDetail = document.getElementById("th-product-detail");
        if (existingDetail) {
          existingDetail.remove();
        }

        // Get section name from the clicked card
        const sectionName = card.querySelector('.th-tour-card-label').textContent.trim();
        const showroomSlug = document.getElementById("th-showroom-slug").textContent.trim();

        // Add a div with #th-product-detail (id) below the selected card row
        const detailContainer = document.createElement('div');
        detailContainer.id = 'th-product-detail';
        detailContainer.className = 'col-12 mt-4';
        // device responsive classes can be added as needed
        let closesetClass = 'row'; // init class
        let device = 'desktop'; // init device
        const width = window.innerWidth;
        if (width <= 767) {
          device = 'mobile';   // phones
        } else if (width <= 1024) {
          device = 'tab';   // tablets (col-md-6) // even ? odd
        } 

        // Insert the detail container after the current row
        const currentRow = card.closest('.' + closesetClass);
        switch(device){
          case 'mobile':
            card.after(detailContainer);
            break;
          case 'tab':
            if(itemNumber % 2 === 0){
              card.after(detailContainer);
            } else {
              let sibling = card.nextSibling;
              sibling.nextSibling.after(detailContainer);
            }
            break;
          case 'desktop':
          default:
            currentRow.after(detailContainer);
            break;
        }

        // Execute a command to initialize the vue js app to ensure vue js component will load
        initializeVueApp(detailContainer, sectionName, showroomSlug);

        // Ensure you passed the section id in the vue app so you can call an api with that slug to retrieve section details
        // The section id will be used in vue store and service 
        // After retrieving data pass them to the vue component to render html content properly.

        const isActive = card.classList.contains("active-mode");

        // Remove all active and blur classes first
        cards.forEach((c) => {
          c.classList.remove("active-mode", "active", "blur-mode");
        });

        // If the clicked card wasn't active, activate it
        if (!isActive) {
          card.classList.add("active-mode", "active");
          //  currentRow.classList.before.remove("pb-25"); 

          // Apply blur-mode to all other cards
          cards.forEach((c) => {
            if (c !== card) {
              c.classList.add("blur-mode");
            }
          });
        };

        //Smooth scroll to the clicked card
        card.scrollIntoView({ behavior: 'smooth' });

        setTimeout(() => {
          const masonryImages = document.getElementById('th-resources-images');
          // const resourceImagesGallery = lightGallery(masonryImages, {
          //   thumbnail: !1,
          //   pager: !1,
          //   plugins: [lgZoom, lgAutoplay, lgFullscreen, lgRotate, lgShare, lgThumbnail, lgVideo],
          //   hash: !1,
          //   preload: 0
          const lg = window.lightGallery;
          if (!masonryImages || typeof lg !== 'function') {
            return;
          }
          lg(masonryImages, {
            thumbnail: false,
            pager: false,
            plugins: [
              window.lgZoom,
              window.lgAutoplay,
              window.lgFullscreen,
              window.lgRotate,
              window.lgShare,
              window.lgThumbnail,
              window.lgVideo,
            ],
            hash: false,
            preload: 0,
          });
        }, 1000);
      }catch (error) 
      {
        console.error('Error bootstrapping Vue app:', error);
      }
    }

    // Function to initialize Vue app with dynamic content
    async function initializeVueApp(container, sectionName, showroomSlug) {
      try {
        // Import the Vue app
        const { default: showroomApp } = await import('/js/vue/showroom.js');

        // Use the existing Vue instance to create the detail component
        const detailComponent = showroomApp.createDetailComponent(container, sectionName, showroomSlug);

        console.log('Vue detail component created for section:', sectionName);
      } catch (error) {
        console.error('Error initializing Vue detail app:', error);
        container.innerHTML = '<div class="alert alert-danger">Error loading section details</div>';
      }
    }


  function updateImageMapCoords() {
    const img = document.getElementById("office-layout");
    const areas = document.querySelectorAll(
      'map[name="officeMap"] area[data-percent-coords]'
    );

    if (!img || !img.complete) return;

    const imgWidth = img.offsetWidth;
    const imgHeight = img.offsetHeight;

    areas.forEach((area) => {
      const percentCoords = area
        .getAttribute("data-percent-coords")
        .split(",");
      const left = Math.round(
        (parseFloat(percentCoords[0]) / 100) * imgWidth
      );
      const top = Math.round(
        (parseFloat(percentCoords[1]) / 100) * imgHeight
      );
      const right = Math.round(
        (parseFloat(percentCoords[2]) / 100) * imgWidth
      );
      const bottom = Math.round(
        (parseFloat(percentCoords[3]) / 100) * imgHeight
      );

      area.setAttribute("coords", `${left},${top},${right},${bottom}`);
    });
  }

  // Update coordinates when image loads
  const img = document.getElementById("office-layout");
  if (img) {
    if (img.complete) {
      updateImageMapCoords();
    } else {
      img.addEventListener("load", updateImageMapCoords);
    }
  }

  // Update coordinates on window resize (with debounce)
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(updateImageMapCoords, 100);
  });



  const areas = document.querySelectorAll("map[name='officeMap'] area");

  function ensureHighlight(img) {
    if (highlight && highlight.isConnected) {
      return highlight;
    }

    highlight = document.createElement("div");
    highlight.id = "highlight-region";
    Object.assign(highlight.style, {
      position: "absolute",
      background: "rgba(0,0,0,0.3)",
      pointerEvents: "none",
      boxShadow: "0 4px 16px 0 rgba(0, 0, 0, 1)",
      zIndex: 1000,
      opacity: "0",
      transition: "opacity 0.5s, box-shadow 0.5s",
    });

    const overlayParent = img.parentNode;
    if (overlayParent && overlayParent.style.position !== "relative") {
      overlayParent.style.position = "relative";
    }
    overlayParent.appendChild(highlight);
    return highlight;
  }

  function scheduleHighlightUpdate(data) {
    pendingHighlightData = data;
    if (highlightRaf) {
      return;
    }

    highlightRaf = requestAnimationFrame(() => {
      highlightRaf = null;
      if (!pendingHighlightData) {
        return;
      }

      const { img, left, top, width, height } = pendingHighlightData;
      const regionHighlight = ensureHighlight(img);
      regionHighlight.style.left = `${img.offsetLeft + left}px`;
      regionHighlight.style.top = `${img.offsetTop + top}px`;
      regionHighlight.style.width = `${width}px`;
      regionHighlight.style.height = `${height}px`;
      regionHighlight.style.opacity = "1";
    });
  }

  areas.forEach(area => {
    area.addEventListener("click", function (event) {
      event.preventDefault(); // prevent jumping to href="#"

      // 3. Read the region id (see next section)
      const regionId = this.dataset.regionId;

      // Call your custom handler
      handleRegionClick(regionId, event);
    });

    area.addEventListener("mousemove", function () {
     
      console.log("Mouse moved over region:", id);
      if(id !== this.getAttribute('data-region-id')){
        console.log("Execute");
        id = this.getAttribute('data-region-id');
          // Parse the coordinates and shape
        const shape = this.getAttribute('shape').toLowerCase();
        const coords = this.getAttribute('coords').split(',').map(Number);

        // Get image for reference and size
        const img = document.getElementById('office-layout');
        if (!img) return;

        // Calculate overlay box properties
        let left = 0, top = 0, width = 0, height = 0;
        if (shape === 'rect') {
          // x1,y1,x2,y2
          [left, top, width, height] = [
            coords[0],
            coords[1],
            coords[2] - coords[0],
            coords[3] - coords[1]
          ];
        } else if (shape === 'poly') {
          // Compute bounding box of polygon
          const xs = [];
          const ys = [];
          for (let i = 0; i < coords.length; i += 2) {
            xs.push(coords[i]);
            ys.push(coords[i + 1]);
          }
          const minX = Math.min(...xs), maxX = Math.max(...xs);
          const minY = Math.min(...ys), maxY = Math.max(...ys);
          left = minX;
          top = minY;
          width = maxX - minX;
          height = maxY - minY;
        } else if (shape === 'circle') {
          // x, y, r
          left = coords[0] - coords[2];
          top = coords[1] - coords[2];
          width = height = coords[2] * 2;
        }


        scheduleHighlightUpdate({ img, left, top, width, height });
      }
      

     

      // Remove highlight on mouseleave
      // this.addEventListener("mouseleave", () => {
      //   const hl = document.getElementById('highlight-region');
      //   if (hl) hl.remove();
      // }, { once: true });
    });
  });


  const imgMouse = document.getElementById("office-layout");

  if (imgMouse) {
    imgMouse.addEventListener("click", function (e) {
        const rect = imgMouse.getBoundingClientRect();

        // Mouse position relative to the image
        const x = Math.round(e.clientX - rect.left);
        const y = Math.round(e.clientY - rect.top);

        // Calculate position as percentage based on image size
        const imgWidth = imgMouse.offsetWidth;
        const imgHeight = imgMouse.offsetHeight;
        const xPercent = ((x / imgWidth) * 100).toFixed(2);
        const yPercent = ((y / imgHeight) * 100).toFixed(2);

        console.log("Mouse position (pixels):", x, y);
        console.log("Mouse position (percentages):", xPercent + "%", yPercent + "%");
    });
  }


  
  // Your custom handler function
  function handleRegionClick(regionId, event) {
      console.log("Clicked region:", regionId);
      let card = document.querySelector(`[data-v-section-item][item-id="${regionId}"]`);
      if(card){
          bootstrapVueApp(card);
      }
  }
});