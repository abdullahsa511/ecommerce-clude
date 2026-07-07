
const ResourceTextiles = {
  name: 'ResourceTextiles',
  props: {

  },
  data() {
    return {
      localError: null
    }
  },
  computed: {
    totalTextilesCount() {
      return this.$store ? this.$store.state.total : 0;
    },
    storeLoadedTextiles() {
      return this.$store ? this.$store.state.loadedResourceTextiles : [];
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
      return loaded < this.totalTextilesCount;
    },
  },
  mounted() {

  },
  methods: {
    getFileName(path) {
        if (!path) return '';
        const fileWithExt = path.split('/').pop();
        return fileWithExt.replace(/\.[^/.]+$/, "");
    }
  },
  template: /* html */`
    <!-- Loop over each textiles -->
    <div>
        <div 
            v-for="(loadedTextile, index) in storeLoadedTextiles"
            :key="'textile-' + (loadedTextile.id || index)"
            class="th-finishe-item-card textiles-item col-md-4">
            <div class="th-tab-card-wrapper d-flex flex-column h-100">
                <div class="th-res-img-wraper border">
                    <img :src="loadedTextile.image" alt="archi-chair" data-v-designresourcetextilesitem-image />
                </div>
                <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                    <h6 class="th-textiles-title th-title-22 mb-24" data-v-designresourcetextilesitem-title>{{loadedTextile.title}}</h6>
                    <span data-v-designresourcetextilesitem-grade="">{{loadedTextile.grade}}</span>
                    
                    <div class="link pr-40 mt-auto">
                        <a class="link-text pr-5" 
                        :href="loadedTextile.link_text"
                        :download="getFileName(loadedTextile.link_text)"
                        data-v-designresourcetextilesitem-link_text>
                            Download
                        </a>
                        <div class="link-icon">
                            <i class="fa-regular fa-arrow-right"></i>
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

export default ResourceTextiles;
