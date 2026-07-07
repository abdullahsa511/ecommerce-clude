// SectionDetails Component
import pinboardStore from "../store/pinboardStore.js";
const SectionDetails = {
  name: "SectionDetails",
  props: {
    sectionName: {
      type: String,
      required: true,
    },
    showroomSlug: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      sectionData: null,
      loading: true,
      error: null,
      pinboardLoadingId: null,
    };
  },
  async mounted() {
    document.addEventListener("keydown", this.handleKeyDown);
    try {
      // Fetch section details from API
      const showroomService = await import("../services/showroomService.js");
      this.sectionData = await showroomService.default.getSectionDetails(
        this.sectionName,
        this.showroomSlug,
      );
      this.loading = false;
    } catch (error) {
      this.error = error.message;
      this.loading = false;
    }
  },
  beforeUnmount() {
    document.removeEventListener("keydown", this.handleKeyDown);
  },
  methods: {
    handleKeyDown(event) {
      if (event.key === "Escape") {
        this.closeDetail();
      }
    },
    closeDetail() {
      // Remove the detail container
      const container = document.getElementById("th-product-detail");
      if (container) {
        container.remove();
      }

      // Remove active classes from all cards
      const cards = document.querySelectorAll(".th-showroom-item-card");
      cards.forEach((c) => {
        c.classList.remove("active-mode", "active", "blur-mode");
      });
    },
    certificationBadgeClass(tag) {
      const t = String(tag || "").trim().toLowerCase();
      if (t.includes("obp")) return "obp-certified";
      if (t.includes("afrdi")) return "afrdi-certified";
      return "afrdi-certified";
    },
    async addToPinboard(loadedList) {
      const { id, name, description, image, product_url } = loadedList;
      if (!id) return;
      if (this.pinboardLoadingId === id) return;

      this.pinboardLoadingId = id;
      const itemData = {
        model_id: parseInt(id),
        model_type: "product",
        title: name || "",
        description,
        image: image || "/img/pinboard/pinboard img 1.png",
        product_url: product_url || "/",
      };
      try {
        await pinboardStore.dispatch("addToPinboard", itemData);
      } finally {
        setTimeout(() => {
          this.pinboardLoadingId = null;
        }, 200);
      }
    },
  },
  template: /* html */ `
            <div class="th-section-detail-container pb-50" id="th-section-detail-container">

                <!-- Loading spinner -->
                <div v-if="loading" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Error message -->
                <div v-else-if="error" class="alert alert-danger">
                    <strong>Error:</strong> {{ error }}
                </div>

                <!-- Section content -->
                <div v-else-if="sectionData">

                    <div class="row">

                        <!-- Left: Image gallery -->
                        <div class="col-lg-6 col-12" id="th-tab-navigation-content">
                            <div class="tab-pane fade show active" id="images" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <div id="th-resources-images" class="th-img-resource-grid">
                                           <div
                                                v-for="(image, index) in sectionData.images"
                                                :key="index"
                                                :class="image.class"
                                              
                                                :data-src="image.src || '/img/showroom/gallery/gallery1.png'"
                                                :data-sub-html="''"
                                                data-bg-src="/img/design-resources/images/img-1.png"
                                                >
                                                   
                                                    <div class="th-masonry-img background-image"
                                                        :style="'background-image: url(' + (image.src || '/img/showroom/gallery/gallery1.png') + ')'"
                                                        :data-bg-src="image.src || '/img/showroom/gallery/gallery1.png'"
                                                        :data-src="image.src || '/img/showroom/gallery/gallery1.png'">
                                                        
                                                        <div class="th-masonry-img-content">
                                                            <h6>{{ image.title }}</h6>
                                                            <div class="th-btn-download-white">
                                                                <i class="fa-solid fa-arrow-down"></i>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            <!-- End dynamic gallery items -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Section details and products -->
                        <div class="col-12 col-lg-6">

                            <!-- Section header -->
                            <div class="th-section-header sectionHeaderClass pb-4">
                                <div class="th-section-header-wrapper left flex-1">
                                    <h2 class="th-title">{{ sectionData.title }}</h2>
                                    <div class="th-section-subtitle" style="font-size: 16px; line-height: 157%;">
                                        {{ sectionData.description }}
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="th-section-header-link-icon">
                                        <span class="th-section-header-icon-btn" @click="closeDetail">
                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Products list -->
                            <div id="th-showroom-section-products" class="">
                                <div class="th-pinboard th-showroom-details-body">

                                    <!-- Dynamic products -->
                                    <div v-for="product in sectionData.products" 
                                        :key="product.product_id || product.id" 
                                        class="row row-1 th-showroom-product-item">

                                        <div class="col-sm-4 col-md-4">
                                            <div class="th-showroom-img position-relative">
                                                <div class="th-img-container itemProjectClass">
                                                    <a :href="product.product_url" target="_blank">
                                                        <img :src="product.image_thumb || '/img/showroom/details/image1.png'" />
                                                    </a>
                                                </div>
                                                <div 
                                                class="th-showroom-details-button position-absolute top-right-10 th-add-to-pinboard"
                                                @click="addToPinboard(product)"
                                                :data-id="product.id" 
                                                data-model="product" 
                                                :data-title="product.name" 
                                                :data-description="product.description" 
                                                :data-image="product.image" 
                                                :data-product-url="product.product_url"
                                                :class="{ 'disabled': pinboardLoadingId === product.id }"
                                                :style="{ pointerEvents: pinboardLoadingId === product.id ? 'none' : 'auto' }"
                                                >
                                                              <div
                                                v-if="pinboardLoadingId === product.id"
                                                class="spinner-border spinner-border-sm text-primary"
                                                role="status"
                                              >
                                                <span class="visually-hidden">Loading...</span>
                                              </div>
                                              <i v-else class="fa-solid fa-plus"></i>
                                              </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-8 col-md-8">
                                            <div class="card-content">
                                                <div class="w-100 th-showroom-product-details">
                                                    <a :href="product.product_url" target="_blank">
                                                        <h3 class="font-weight-600">{{ product.name }}</h3>
                                                    </a>
                                                    <p class="th-item-description th-font-size-13" style="font-size: 13px;">{{ product.tag_line }}</p>
                                                    

                                                    <div
                                                        v-if="product.finishes && product.finishes.length"
                                                        class="product-finishes mt-2"
                                                    >
                                                        <div
                                                            v-for="(finish, index) in product.finishes"
                                                            :key="index"
                                                            class="mb-1 th-font-size-13"
                                                            style="font-size: 13px;"
                                                        >
                                                            <strong>{{ finish.title }}:</strong>
                                                            {{ finish.items.join(', ') }}
                                                        </div>
                                                    </div>

                                      
                                                    <div
                                                        v-if="product.tags && product.tags.length"
                                                        class="certification-badges pt-75"
                                                    >
                                                        <a
                                                            v-for="(tag, tagIdx) in product.tags"
                                                            :key="(product.product_id || product.id) + '-' + tagIdx + '-' + tag"
                                                            :class="certificationBadgeClass(tag)"
                                                        >{{ String(tag).trim() }}</a>
                                                    </div>
                                                    <!-- 
                                                    <span class="leg-powder-coat-textile">
                                                        Price: \${{ product.price }}
                                                    </span>
                                                   <div class="fabric-img pt-3">
                                                        <img :src="product.fabric_image || '/media/design-resource/finishes/finishes_logo.webp'" alt=""
                                                            data-v-showroomsection-product-fabric-img="">
                                                    </div>
                                                    -->
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- No products message -->
                                    <div v-if="!sectionData.products || sectionData.products.length === 0"
                                        class="text-center p-4">
                                        <p>No products available for this section.</p>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- End Right: Section details and products -->

                    </div>
                    <!-- End row -->

                </div>
                <!-- End sectionData v-else-if -->

            </div>
        <!-- End th-section-detail-container -->
    `,
};

export default SectionDetails;
