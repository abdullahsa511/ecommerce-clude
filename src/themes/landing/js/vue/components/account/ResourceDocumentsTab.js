const ResourceDocumentsTab = {
    name: 'ResourceDocumentsTab',
    props: {
        resourceDocuments: {
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
        // console.log("resourceDocuments",this.resourceDocuments);
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
        downloadAll(documentItem) {
            const documents = documentItem?.design_resource_documents || [];
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
        getFileIcon(file) {
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
    
            if (!file) return '';
            // Extract extension safely
            const extension = file.split('.').pop()?.toUpperCase();
            return FILE_FORMAT_IMAGES[extension] || '/media/design-resource/icons/default.png';
        },
        getProductUrl(model) {
            return `/products/${model.slug}/${model.product_slug}`;
        },
    },
    template: /* html */`
    <div>
      <!-- Loop over each image -->
    <div class="tab-pane fade show active" id="document" role="tabpanel" data-v-component-designresourcedocuments>
        <div class="row pl-10 pb-30" data-v-designresourcedocuments-total_result id="totalCount">
            Total Results: {{ resourceDocuments?.length || 0 }}
        </div>
        <div class="row document-list" id="th-resources">
            <div 
                v-for="(document, index) in resourceDocuments"
                :key="'document-' + (document.id || index)"
                class="design-item document-item col-lg-4 col-md-6 col-sm-12 th-card-gap mb-50">
                <div class="th-tab-card-wrapper d-flex flex-column h-100">
                    <a :href="getProductUrl(document)">
                        <div class="th-res-img-wraper border">
                            <img :src="document.image" :alt="document.title" data-v-designresourcedocumentsitem-image />
                        </div>
                    </a>
                    <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                        <h4 class="mb-24 th-title-22"><a :href="getProductUrl(document)">{{document.title}}</a></h4>
                        
                        <div
                        class="th-info-list text-weight-500 mb-2 design-resource-tags"
                        v-if="document.design_resource_documents && document.design_resource_documents?.length > 0"
                        >
                            <a
                                v-for="(tag, index) in document.design_resource_documents"
                                :key="'document-tag-' + (tag.design_resource_document_id || index)"
                                :href="getSecureUrl(tag.url)"
                                :aria-label="'Download ' + (document.title || '')"
                                :download="getFileName(getSecureUrl(tag.url))"
                                class="design-resource-tag block mb-1 underline text-blue-600 hover:text-blue-800"
                            >
                            <img 
                                v-if="getFileIcon(tag.name)"
                                :src="getFileIcon(tag.name)" 
                                alt="file icon"
                                class="file-icon me-2"
                                style="width: 25px; height: 25px; object-fit: contain;"
                            />
                            {{getFileName(tag.name)}}
                            </a>
                            <div class="link-icon">
                                <!-- <i class="fa-regular fa-arrow-right"></i> -->
                            </div>
                        </div>
                        <div class="link pr-40 mt-auto" v-if="document.design_resource_documents && document.design_resource_documents?.length > 0">
                            <a class="link-text pr-5 font-size-16" 
                            href="#"
                            @click.prevent="downloadAll(document)"
                            data-v-designresourcedocumentsitem-link_text>
                                Download All {{ document.title }}
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

export default ResourceDocumentsTab;


