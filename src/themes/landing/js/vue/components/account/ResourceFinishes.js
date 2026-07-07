const ResourceFinishes = {
    name: 'ResourceFinishes',
    props: {
      
    },
    data() {
        return {
            localError: null
        }
    },
    computed: {
        totalFinishesCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedFinishes() {
            return this.$store ? this.$store.state.loadedResourceFinishes : [];
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
            return loaded < this.totalFinishesCount;
        },
    },
    mounted() {
       
    },
    methods: {
        getFileName(path) {
            if (!path) return '';
            const fileWithExt = path.split('/').pop();
            return fileWithExt;
          }
      
    },
    template: /* html */`
    <!-- Loop over each image -->
    <div>
        <div 
            v-for="(loadedFinishe, index) in storeLoadedFinishes"
            :key="'finishe-' + (loadedFinishe.id || index)"
            class="th-finishe-item-card finishes-item col-lg-4 col-md-6 col-sm-12 th-card-gap">
                <div class="th-tab-card-wrapper d-flex flex-column h-100">
                    <div class="th-res-img-wraper border">
                        <img :src="loadedFinishe.image" alt="archi-chair" data-v-designresourcefinishesitem-image />
                    </div>
                    <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                        <h6 class="th-finishes-title th-title-22 mb-24" data-v-designresourcefinishesitem-title>{{loadedFinishe.title}}</h6>
                        <span data-v-designresourcefinishesitem-grade="">{{loadedFinishe.grade}}</span>
                      
                        <div class="link pr-40 mt-auto">
                            <a class="link-text pr-5" 
                            :href="loadedFinishe.link_text"
                            :download="getFileName(loadedFinishe.link_text)"
                            data-v-designresourcefinishesitem-link_text>
                                Download All
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
  
  export default ResourceFinishes;
  
  
  
  