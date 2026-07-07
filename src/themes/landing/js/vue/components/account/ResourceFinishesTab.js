const ResourceFinishesTab = {
    name: 'ResourceFinishesTab',
    emits: ['mounted'],
    props: {
        resourceFinishes: {
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
        }
    },
    computed: {
        
    },
    mounted() {

    },
    watch: {
        resourceFinishes: {
            handler(newVal) {
                if (newVal && newVal.length > 0) {
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
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
        /** Basename with extension for the download attribute (query string stripped). */
        getDownloadFileName(path) {
            if (!path) return '';
            const noQuery = String(path).split('?')[0];
            const base = noQuery.split('/').pop();
            return base || '';
        },
        /**
         * Site-root asset URLs: values like "media/..." must be "/media/..." or the
         * browser resolves them against /account/resources/... and returns HTML (saved as .htm).
         */
        getSecureUrl(url) {
            if (!url) return '';
            let normalized = String(url).trim();
            if (normalized.startsWith('//')) {
                normalized = `${window?.location?.protocol === 'https:' ? 'https:' : 'http:'}${normalized}`;
            } else if (!/^https?:\/\//i.test(normalized) && !normalized.startsWith('/')) {
                normalized = `/${normalized.replace(/^\/+/, '')}`;
            }
            if (window?.location?.protocol === 'https:' && normalized.startsWith('http:')) {
                normalized = normalized.replace(/^http:/, 'https:');
            }
            return normalized;
        },
        finishImageHref(item) {
            if (!item) return '';
            const raw = item.image_url || item.image_thumb_url || item.image || '';
            return this.getSecureUrl(raw);
        }
    },
    template: /* html */`
    <div>
    <!-- Loop over each finishe -->
    <div class="tab-pane fade show active" id="finishe" role="tabpanel">
        <div class="row pl-10 pb-30" id="totalCount">
            Total Results: {{ resourceFinishes?.length || 0 }}
        </div>
        <div class="row finishe-list row-gap-50" id="th-resources">
            <div 
                v-for="(loadedFinishe, index) in resourceFinishes"
                :key="'finishe-' + (loadedFinishe.id || index)"
                class="th-finishe-item-card finishes-item col-md-2 th-card-gap"
                :data-src="finishImageHref(loadedFinishe)">
                <div class="th-tab-card-wrapper d-flex flex-column h-100">
                    <div class="th-res-img-wraper-textile border th-cursor-pointer">
                        <img :src="loadedFinishe.image_thumb_url" :alt="loadedFinishe.title" data-v-designresourcefinishesitem-image />
                    </div>
                    <div class="th-res-info mt-10 d-flex flex-column flex-grow-1">
                        <h6 class="th-finishes-title font-weight-700 font-size-14 mb-24 th-cursor-pointer" data-v-designresourcefinishesitem-title>{{ loadedFinishe.title }}</h6>
                        <span data-v-designresourcefinishesitem-grade="" @click.stop>{{loadedFinishe.grade}}</span>
                        <span class="th-resourece-subtitle-textile font-size-16" v-if="loadedFinishe.brand || loadedFinishe.description || loadedFinishe.type" @click.stop>
                            {{ loadedFinishe.brand ? loadedFinishe.brand : '' }}
                            <template v-if="loadedFinishe.brand && loadedFinishe.type"> - </template>
                            <span v-if="loadedFinishe.type" @click.stop>{{ loadedFinishe.type }}</span>
                            <template v-if="loadedFinishe.brand && loadedFinishe.tag"> - </template>
                            <span v-if="loadedFinishe.tag" @click.stop>{{ loadedFinishe.tag }}</span>
                        </span>
                      
                        <div class="link  mt-auto">
                            <a class="font-weight-600 pr-5 font-size-13" 
                            :href="finishImageHref(loadedFinishe)"
                            :aria-label="'Download ' + (loadedFinishe.title || '')"
                            :download="getDownloadFileName(finishImageHref(loadedFinishe))"
                            @click.stop
                            data-v-designresourcefinishesitem-link_text>
                                Download {{ loadedFinishe.title }}
                            </a>
                          <!--  <div class="link-icon">
                                <i class="fa-regular fa-arrow-right"></i>
                            </div> -->
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
     `
};

export default ResourceFinishesTab;


