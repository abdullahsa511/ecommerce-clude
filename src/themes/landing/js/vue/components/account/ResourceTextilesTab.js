const ResourceTextilesTab = {
     name: 'ResourceTextilesTab',
     emits: ['mounted'],
     props: {
        resourceTextiles: {
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
     methods: {
        getDownloadFileName(path) {
            if (!path) return '';
            const noQuery = String(path).split('?')[0];
            const base = noQuery.split('/').pop();
            return base || '';
        },
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
        textileImageHref(item) {
            if (!item) return '';
            const raw = item.image_url || item.image_thumb_url || item.image || '';
            return this.getSecureUrl(raw);
        },
        getAfterDash(text) {
            if (!text) return '';
            const parts = text.split('-');
            return parts[1] ? parts[1].trim() : '';
          },
          getBeforeDash(text) {
            if (!text) return '';
            const parts = text.split('-');
            return parts[0] ? parts[0].trim() : '';
          }

     },

     watch: {
        resourceTextiles: {
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


     template: /* html */`
    <!-- Loop over each textile -->
    <div class="tab-pane fade show active" id="textile" role="tabpanel" data-v-component-designresourcetextiles>
        <div class="row pl-10 pb-30" data-v-designresourcetextiles-total_result id="totalCount">
            Total Results: {{ resourceTextiles?.length || 0 }}
        </div>
        <div class="row textile-list" id="th-resources">
            <div 
                v-for="(loadedTextile, index) in resourceTextiles"
                :key="'textile-' + (loadedTextile.id || index)"
                class="th-finishe-item-card textiles-item col-md-2 mb-50"
                :data-src="textileImageHref(loadedTextile)">
                <div class="th-tab-card-wrapper d-flex flex-column h-100">
                    <div class="th-res-img-wraper-textile border th-cursor-pointer">
                        <img :src="loadedTextile.image_thumb_url" :alt="loadedTextile.title" data-v-designresourcetextilesitem-image />
                    </div>
                    <div class="th-res-info mt-10 d-flex flex-column flex-grow-1">
                        <h6 class="th-textiles-title font-size-14 font-weight-700 mb-24 th-cursor-pointer" data-v-designresourcetextilesitem-title>{{ loadedTextile.title }}</h6>
                        <!--<span data-v-designresourcetextilesitem-grade="">{{loadedTextile.grade}}</span>-->
                        <span class="th-resourece-subtitle-textile" v-if="loadedTextile.brand || loadedTextile.description || loadedTextile.type" @click.stop>
                            {{ loadedTextile.brand ? loadedTextile.brand : '' }}

                            <!--<template v-if="loadedTextile.brand && loadedTextile.description"> - </template>
                            {{ getBeforeDash(loadedTextile.description) }} -->

                            <template v-if="loadedTextile.description && loadedTextile.type"> - </template>
                            ({{ loadedTextile.type }}) <br>

                            <!--<template v-if="(loadedTextile.brand || loadedTextile.description) && loadedTextile.type"> - </template> -->
                            <span v-if="loadedTextile.type">
                                {{ loadedTextile.description ? loadedTextile.description : '' }}
                            </span> 
                        </span>             
                        <!-- <span class="th-resourece-subtitle" v-if="loadedTextile.description">
                            {{ getAfterDash(loadedTextile.description) }}
                            </span> -->
                        <div class="link pr-40 mt-auto">
                            <a class="font-weight-600 pr-5 font-size-13 " 
                            :href="textileImageHref(loadedTextile)"
                            :aria-label="'Download ' + (loadedTextile.title || '')"
                            :download="getDownloadFileName(textileImageHref(loadedTextile))"
                            @click.stop
                            data-v-designresourcetextilesitem-link_text>
                                Download {{ loadedTextile.title }}
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
     `
 };
 
 export default ResourceTextilesTab;
 
 
 