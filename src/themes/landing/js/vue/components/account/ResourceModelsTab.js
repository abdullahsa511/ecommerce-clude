const ResourceModelsTab = {
    name: 'ResourceModelsTab',
    props: {
        resourceModels: {
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
        contextCategories() {
            return this.$store.state?.pagination.models.context_categories || [];
        },
    },
    mounted() {
        // if(this.contextCategories.length == 0) {
        //     this.$store.dispatch('loadContextCategories', { contextType: 'product', resourcePage: 'models' });
        // }
    },
    methods: {
        getFileName(path) {
            if (!path) return '';
            const fileWithExt = path.split('/').pop();
            return fileWithExt;
        },
        getSecureUrl(url) {
            if (!url) return '';
            if (window?.location?.protocol === 'https:' && url.startsWith('http:')) {
                return url.replace(/^http:/, 'https:');
            }
            return url;
        },
        downloadAll(loadedModel) {
            const documents = loadedModel?.design_resource_documents || [];
            if (documents.length === 0) return;

            documents.forEach((doc, index) => {
                const url = this.getSecureUrl(doc.url);
                if (!url) return;

                setTimeout(() => {
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = this.getFileName(url);
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }, index * 300);
            });
        },
        getFileIcon(tag) {
            const file = tag?.name;
            const FILE_FORMAT_IMAGES = {
                GSM: '/media/design-resource/icons/gsm.png',
                DWG: '/media/design-resource/icons/dwg.png',
                MAX: '/media/design-resource/icons/max.png',
                SKP: '/media/design-resource/icons/skp.png',
                RFA: '/media/design-resource/icons/rfa.png',
                ZIP: '/media/design-resource/icons/zip.png',
                PDF: '/media/design-resource/icons/pdf.png',
                DOC: '/media/design-resource/icons/doc.png',
                DOCX: '/media/design-resource/icons/docx.png',
                XLS: '/media/design-resource/icons/xls.png',
                XLSX: '/media/design-resource/icons/xlsx.png',
                PPT: '/media/design-resource/icons/ppt.png',
                PPTX: '/media/design-resource/icons/pptx.png',
                JPG: '/media/design-resource/icons/jpg.png',
            };
        
            if (!file && !tag?.format) return '';
            if(tag?.format){
                return FILE_FORMAT_IMAGES[tag.format] || '/media/design-resource/icons/default.png';
            }
        
            const allowedFormats = ['SKP', 'DWG', 'MAX', 'GSM'];
        
            // Get the base name without extension
            const baseName = file.substring(0, file.lastIndexOf('.')) || file;
        
            // Extract all words with 2+ letters (case-insensitive)
            const matches = baseName.match(/\b([A-Z]{2,})\b/gi) || [];
        
            // Convert matches to uppercase and filter allowed formats
            const foundFormats = matches.map(f => f.toUpperCase())
                                        .filter(f => allowedFormats.includes(f));
        
            let formatKey;
            if (foundFormats.length > 0) {
                // Take the first matched format
                formatKey = foundFormats[0];
            } else {
                // Fallback to extension
                formatKey = file.split('.').pop()?.toUpperCase();
            }
        
            return FILE_FORMAT_IMAGES[formatKey] || '/media/design-resource/icons/default.png';
        },
        getProductUrl(model) {
            return `/products/${model.slug}/${model.product_slug}`;
        },
        
    },
    template: /* html */`
    <div>
    <!-- Loop over each image -->
    <div class="tab-pane fade show active" id="models" role="tabpanel">
        <div class="row pl-10 pb-30" id="totalCount">
            Total Results: {{ resourceModels?.length || 0 }}
        </div>
        <div class="row model-list" id="th-resources">
            <div 
                v-for="(loadedModel, index) in resourceModels"
                :key="'model-' + (loadedModel.id || index)"
                class="design-item model-item col-lg-4 col-md-6 col-sm-12 th-card-gap mb-50">
                <div class="th-tab-card-wrapper d-flex flex-column h-100">
                    <a :href="getProductUrl(loadedModel)">
                        <div class="th-res-img-wraper border">
                                <img :src="loadedModel.image" :alt="loadedModel.title" />
                        </div>
                    </a>
                    <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                        <h4 class="mb-24 th-title-22"><a :href="getProductUrl(loadedModel)">{{loadedModel.title}}</a></h4>
                        
                        <div class="th-info-list text-weight-500 mb-2 design-resource-tags"
                        v-if="loadedModel.design_resource_documents && loadedModel.design_resource_documents.length">
                            <a
                                v-for="(tag, index) in loadedModel.design_resource_documents"
                                :key="'model-tag-' + (index)"
                                :href="getSecureUrl(tag.url)"
                                :aria-label="'Download ' + (loadedModel.title || '')"
                                :download="getFileName(getSecureUrl(tag.url))"
                                class="design-resource-tag block mb-1 underline text-blue-600 hover:text-blue-800"
                            >
                            <img 
                                :src="getFileIcon(tag)" 
                                alt="file icon"
                                class="file-icon me-2"
                                style="width: 25px; height: 25px; object-fit: contain;"
                            />
                            {{getFileName(tag.name)}}
                            </a>
                        </div>
                        <div class="link pr-40 mt-auto">
                            <a class="link-text pr-5 font-size-16" 
                            href="#"
                            @click.prevent="downloadAll(loadedModel)"
                            >
                                Download All {{ loadedModel.title }}
                            </a>
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

export default ResourceModelsTab;


