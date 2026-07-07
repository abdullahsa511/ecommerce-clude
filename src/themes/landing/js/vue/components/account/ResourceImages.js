const ResourceImages = {
    name: 'ResourceImages',
    props: {
      
    },
    data() {
        return {
            localError: null
        }
    },
    computed: {
        totalImagesCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedImages() {
            return this.$store ? this.$store.state.loadedResourceImages : [];
        },
        loading() {
            return this.$store ? this.$store.state.loading : false;
        },
        error() {
            return this.$store ? this.$store.state.error : this.localError;
        },
        currentPage() {
            return this.$store ? this.$store.state.current_page : 1;
        },
        perPage() {
            return this.$store ? this.$store.state.per_page : 21;
        },
        hasMore() {
            const loaded = this.currentPage * this.perPage;
            return loaded < this.totalImagesCount;
        },
    },
    mounted() {
       
    },
    methods: {
      async downloadImage(dataSrc) {
        // extract filename from URL
        const fileName = dataSrc.split('/').pop().split('?')[0];
    
        const response = await fetch(dataSrc);
        const blob = await response.blob();
    
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        // link.download = `${Date.now()}_${fileName}`;
        link.download = `${fileName}`;
        document.body.appendChild(link);
        link.click();
    
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      }
    },
    template: /* html */`
      <!-- Loop over each image -->
      <div>
        <div
        v-for="(loadedImage, index) in storeLoadedImages"
        :key="'post-' + (loadedImage.id || index)"
        :class="loadedImage.class"
        :data-src="loadedImage.dataSrc"
        data-v-designresourceimages-image-src
      >
        <div class="th-masonry-img background-image"
          :class="{'th-product': loadedImage.context === 'Product'}"
          :style="{ backgroundImage: 'url(' + loadedImage.dataBgSrc + ')' }"
          data-v-designresourceimages-image-bg-src
          >
          <div class="th-masonry-img-content gr-bg11">
            <div class="d-flex flex-column">
              <div class="d-flex justify-content-between">  
                <div class="d-flex flex-column">
                  <p class="small" style="color: #231f20;">{{ loadedImage.context }}</p>
                  <h6 style="color: #231f20;">{{ loadedImage.context_reference }}</h6>
                </div>
                <div class="th-btn-download-white" @click="downloadImage(loadedImage.dataSrc)">
                    <i class="fa-solid fa-arrow-down"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="loading" class="text-center p-4">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div v-if="error" class="alert alert-danger">
        <strong>Error:</strong> {{ error }}
      </div>
    </div>
    `
};

export default ResourceImages;
