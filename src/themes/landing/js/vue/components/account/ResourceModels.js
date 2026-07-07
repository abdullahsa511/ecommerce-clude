const ResourceModels = {
    name: 'ResourceModels',
    props: {
      
    },
    data() {
        return {
            localError: null
        }
    },
    computed: {
        totalModelsCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedModels() {
            return this.$store ? this.$store.state.loadedResourceModels : [];
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
            return loaded < this.totalModelsCount;
        },
    },
    mounted() {
       
    },
    methods: {
        getFileName(path) {
            if (!path) return '';
            const fileWithExt = path.split('/').pop();
            return fileWithExt;
        },
        getProductUrl(model) {
            return `/products/${model.slug}/${model.product_slug}`;
        },
    },
    template: /* html */`
      <!-- Loop over each image -->
      <div>
      
        <div 
        v-for="(loadedModel, index) in storeLoadedModels"
        :key="'model-' + (loadedModel.id || index)"
        class="design-item model-item col-lg-4 col-md-6 col-sm-12 th-card-gap mb-50">
        <div class="th-tab-card-wrapper d-flex flex-column h-100">
            <div class="th-res-img-wraper border">
                <img :src="loadedModel.image" alt="archi-chair" />
            </div>
            <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                <h4 class="mb-24 th-title-22"><a :href="getProductUrl(loadedModel)">{{loadedModel.title}}</a></h4>
                <div class="th-info-list text-weight-500 mb-2 design-resource-tags"
                v-if="loadedModel.design_resource_documents && loadedModel.design_resource_documents.length">
                    <a
                        v-for="(tag, index) in loadedModel.design_resource_documents"
                        :key="'model-tag-' + (tag.design_resource_document_id || index)"
                        :href="tag.name"
                        :download="getFileName(tag.name)"
                        class="design-resource-tag block mb-1 underline text-blue-600 hover:text-blue-800"
                    >
                    {{getFileName(tag.name)}}
                    </a>
                    <div class="link-icon">
                        <i class="fa-regular fa-arrow-right"></i>
                    </div>
                </div>
                <div v-else class="link pr-40 mt-auto">
                    <a class="link-text pr-5" 
                    :href="loadedModel.link_text"
                    :download="getFileName(loadedModel.link_text)">
                        Download All
                    </a>
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

export default ResourceModels;



