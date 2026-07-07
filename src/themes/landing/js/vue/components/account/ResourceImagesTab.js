const ResourceImagesTab = {
    name: 'ResourceImagesTab',
    emits: ['mounted'],
    props: {
        resourceImages: {
          type: Array,
          required: true
        },
        loading: {
          type: Boolean,
          required: false,
          default: false
        },
        error: {
          type: String,
          required: false,
          default: null
        }
    },
    data() {
        return {
            localError: null
        }
    },
    computed: {
       
    },
    mounted() {
      
    },
    watch: {
        resourceImages: {
            handler(newVal) {
                if (newVal && newVal.length > 0) {
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                // Now the DOM nodes for the v-for actually exist
                                this.$emit('mounted');
                            });
                        });
                    });
                }
            },
            immediate: true
        }
    },
    methods: {
        //   resourceImagesGallery.refresh();
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
        },
        createBackgroundImage(dataBgSrc) {
            const url = `background-image: url("${dataBgSrc}")`;
            // console.log(url);
            return url;
        }
    },
    template: /* html */`
    <div id="conent">
    <div class="tab-pane fade show active" id="images" role="tabpanel">
        <div class="row pl-10 pb-30" id="totalCount">Total Results: {{ resourceImages?.length || 0 }}</div>
        <div class="row row-gap-50">
            <div class="col-12"  >
                <div id="th-resources" class="th-img-masonry-image-resource-grid fade-in">
                    <div
                        v-for="(loadedImage, index) in resourceImages"
                        :key="'post-' + (loadedImage.id || index)"
                        :class="loadedImage.class"
                        :data-src="loadedImage.dataSrc"
                        data-v-designresourceimages-image-src
                    >
                        <div class="th-masonry-img background-image"
                        :class="{'th-product': loadedImage.context === 'Product'}"
                        :style="createBackgroundImage(loadedImage.dataBgSrc)"
                        data-v-designresourceimages-image-bg-src
                        >
                            <div class="th-masonry-img-content gr-bg11">
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
    </div>
    </div>
    `
};

export default ResourceImagesTab;

