const ResourceDocuments = {
    name: 'ResourceDocuments',
    props: {

    },
    data() {
        return {
            localError: null
        }
    },
    computed: {
        totalDocumentsCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedDocuments() {
            return this.$store ? this.$store.state.loadedResourceDocuments : [];
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
            return loaded < this.totalDocumentsCount;
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
        getSecureUrl(url) {
            if (!url) return '';
            if (window?.location?.protocol === 'https:' && url.startsWith('http:')) {
                return url.replace(/^http:/, 'https:');
            }
            return url;
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
    },
    template: /* html */`
    <!-- Loop over each image -->
    <div>
      <div 
      v-for="(loadedDocument, index) in storeLoadedDocuments"
      :key="'model-' + (loadedDocument.id || index)"
      class="design-item document-item col-lg-4 col-md-6 col-sm-12 th-card-gap">
      <div class="th-tab-card-wrapper d-flex flex-column h-100">
          <div class="th-res-img-wraper border">
              <img :src="loadedDocument.image" alt="archi-chair" data-v-designresourcedocumentsitem-image />
          </div>
          <div class="th-res-info mt-20 d-flex flex-column flex-grow-1">
                <h4 class="mb-24 th-title-22" data-v-designresourcedocumentsitem-title>{{loadedDocument.title}}</h4>
                <div
                class="th-info-list text-weight-500 mb-2 design-resource-tags"
                v-if="loadedDocument.design_resource_document?.length > 0"
                >
                  <!--:href="tag.name":download="getFileName(tag.name)"-->
                    <a
                        v-for="(tag, index) in loadedDocument.design_resource_document"
                        :key="'document-tag-' + (tag.design_resource_document_id || index)"
                        :href="getSecureUrl(tag.url || tag.name)"
                        :download="getFileName(getSecureUrl(tag.url || tag.name))"
                        class="design-resource-tag block mb-1 underline text-blue-600 hover:text-blue-800"
                    >
                     <img 
                        :src="getFileIcon(tag.name)" 
                        alt="file icon"
                        class="file-icon me-2"
                        style="width: 25px; height: 25px; object-fit: contain;"
                    />
                    {{getFileName(tag.name)}}
                    </a>
                </div>
                <!-- :href="loadedDocument.link_text" :download="getFileName(loadedDocument.link_text)"-->
                <div class="link pr-40 mt-auto">
                    <a class="link-text pr-5" 
                    :href="getSecureUrl(loadedDocument.link_text)"
                    :download="getFileName(getSecureUrl(loadedDocument.link_text))"
                    data-v-designresourcedocumentsitem-link_text>
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

export default ResourceDocuments;



