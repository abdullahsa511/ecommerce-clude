import FilterableSearch from './FilterableSearch.js';
import SearchLoadMore from './SearchLoadMore.js';
export default {
    name: 'CategoryProductListing',
    props: {},
    components: {
        FilterableSearch,
        SearchLoadMore,
    },

    data() {
        return {
            selectedVariant: null,
            selectedOptions: {}, // { groupId: option }
            selectedItems: {},
            selectedItem: {},
            itemsAddingToPinboard: [],
        };
    },

    computed: {
        searchPagination() {
            return this.$store ? this.$store.getters.searchPagination : {};
        },
        storeProducts() {
            return this.$store ? this.$store.getters.products || [] : [];
        },
        /**
         * Total matches from API (search pagination), fallback to loaded row count before first merge.
         */
        totalResultsCount() {
            const apiTotal = this.searchPagination.total;
            if (apiTotal !== undefined && apiTotal !== null && !Number.isNaN(Number(apiTotal))) {
                return Number(apiTotal);
            }
            return Array.isArray(this.storeProducts) ? this.storeProducts.length : 0;
        },
        /**
         * Cumulative rows loaded (from API pagination.loaded_data or current list length).
         */
        loadedResultsCount() {
            const apiLoaded = this.searchPagination.loaded_data;
            if (apiLoaded !== undefined && apiLoaded !== null && !Number.isNaN(Number(apiLoaded))) {
                return Number(apiLoaded);
            }
            return Array.isArray(this.storeProducts) ? this.storeProducts.length : 0;
        },
        /** e.g. "80 of 349 Results Loaded" */
        resultsLoadedHeading() {
            const total = this.totalResultsCount;
            const rawLoaded = this.loadedResultsCount;
            const loaded = total > 0 ? Math.min(rawLoaded, total) : rawLoaded;
            if (total <= 0 && loaded <= 0) {
                return '0 Results Loaded';
            }
            if (total <= 0) {
                return `${loaded} Results Loaded`;
            }
            return `${loaded} of ${total} Results Loaded`;
        },
        hasMoreResults() {
            return Boolean(this.$store?.getters.searchPagination?.has_more);
        },
        materials() {
            return this.$store.getters.materials || [];
        },
        features() {
            return this.$store.getters.features || [];
        },
        certifications() {
            return this.$store.getters.certifications || [];
        },
        selectedFilters() {
            return this.$store.getters.selectedFilters || {};
        },
        filterLoading() {
            return this.$store.getters.filterLoading;
        },
        resetFilter() {
            return this.$store.getters.resetFilterLoading;
        },
        loading() {
            return this.$store.getters.loading;
        },
        error() {
            return this.$store.getters.error;
        },
    },

    mounted() {
        this.$store.dispatch("loadCategoryProductListing");
        this.$nextTick(() => {
            this.initPinboardTooltips();
          });
    },

    methods: {
        async handleLoadMoreProducts() {
            await this.$store.commit('INCREMENT_PAGE');
            await this.$store.dispatch('loadCategoryProductListing', {load_more: true});
        },
        async handleInputChange(filter) {
          await this.$store.dispatch('inputAutocomplete', filter);
        //   console.log(filter.resource_type, this.certifications);
        },
        async handleApplyFilters(filter) {
            // if empty show invalid class 
            // if(!filter.search_query) {
            //     document.getElementById('quick-search-input').classList.add('is-invalid');
            // } else {
            //     document.getElementById('quick-search-input').classList.remove('is-invalid');
            // }
          document.getElementById('global-search-results-query-title').innerText = 'Search results for ';
          document.getElementById('global-search-results-query').innerText = filter.search_query;
          // head title tag
          const titleTag = document.querySelector('title[data-v-head-title]');
          if (titleTag) {
            titleTag.textContent = `Search Results - ${filter.search_query} | Krost Business Furniture`;
          }
          // Update meta title
          const metaTitle = document.querySelector('meta[name="title"]');
          if (metaTitle) {
            metaTitle.setAttribute('content', `Search results for ${filter.search_query} | Krost Business Furniture`);
          }
          await this.$store.dispatch('productFilter', filter);
        },
        async handleResetFilters(filter) {
          await this.$store.dispatch('productFilter', filter);
        },
        getProductUrl(categorySlug, productSlug) {
            return '/products/'+categorySlug+'/'+productSlug;
        },
        async handleAddToPinboard(product, index) {
            this.$set(this.itemsAddingToPinboard, index, true);
            this.$set(product, 'adding_to_pinboard', true);
            const pinboardItem = {
                model_id: this.getAfterDash(product.model_type),
                model_type: this.getBeforeDash(product.model_type).toLowerCase() =='blog' ? 'post' : this.getBeforeDash(product.model_type).toLowerCase(), // small letter
                title: product.title,
                product_url: product.product_url,
                description: product.description,
                image: product.dataSrc || '/img/pinboard/pinboard img 1.png',
            }
            await window.pinboardApp.addToPinboard(pinboardItem);
            setTimeout(() => {
                this.$set(this.itemsAddingToPinboard, index, false);
            }, 100);
        },
        getBeforeDash(text) {
            if (!text) return '';
            const parts = text.split('-');
            return parts[0] ? parts[0].trim() : '';
        },
        getAfterDash(text) {
            if (!text) return '';
            const parts = text.split('-');
            return parts[1] ? parts[1].trim() : '';
        },
        initPinboardTooltips() {
            const root = this.$el;
            if (!root) return;
    
            const tooltipEls = root.querySelectorAll(
              '.th-pinboard-tooltip[data-toggle="tooltip"]'
            );
            if (!tooltipEls || tooltipEls.length === 0) return;
    
            const hasJqueryTooltip =
              window.jQuery && typeof window.jQuery.fn.tooltip === 'function';
    
            tooltipEls.forEach((el) => {
              const tooltipText =
                el.getAttribute('data-bs-original-title') ||
                el.getAttribute('data-original-title') ||
                el.getAttribute('title') ||
                'Add to Pinboard';
    
              if (hasJqueryTooltip) {
                const $el = window.jQuery(el);
                const alreadyInit = $el.data('bs.tooltip') || $el.data('bs.Tooltip');
    
                $el
                  .attr('title', '')
                  .attr('data-original-title', tooltipText);
    
                if (!alreadyInit) {
                  $el.tooltip();
                }
                return;
              }
    
              // Native fallback tooltip
              el.setAttribute('title', tooltipText);
            });
          },
    },

    watch: {
        selectedFilters: {
                // alert('selectedFilters watcher');
            handler(newVal) {
                // alert('test watcher');
                console.log('selectedFilters watcher', newVal);
            },
            deep: true
        }
    },
    updated() {
        // Re-init tooltips after reactive DOM updates.
        if (this._pinboardTooltipRaf) return;
        this._pinboardTooltipRaf = window.requestAnimationFrame(() => {
          this._pinboardTooltipRaf = null;
          this.initPinboardTooltips();
        });
      },

    template: /* html */ `
    <div id="vue-app-product-desk-modesty-panel">
        <div class="row">
            <div class="col-12">
            <div class="pb-30 th-padding-bottom-0-mobile" style="display: block">
                <div class="th-section-header-wrapper">
                <h2 class="th-section-title">{{ resultsLoadedHeading }}</h2>

               <!-- <div class="th-section-subtitle" style="font-size: 16px;">
                    Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                </div> -->
                </div>
            </div>

            <!-- ============================== Product Filters ============================== -->
            <FilterableSearch 
                    :selectedFilters="selectedFilters"
                    :resetFilter="resetFilter"
                    :filterLoading="filterLoading"
                    @onchange-input="handleInputChange"
                    @onreset-filters="handleResetFilters"
                    @onapply-filters="handleApplyFilters" />
            <!-- ========================== End Product Filters ========================== -->
            </div>
        </div>

        <div class="row th-products-list-wrapper">
            <div class="th-item-product col-6 col-sm-6 col-md-4 col-lg-4" v-for="(product, index) in storeProducts" :key="'search-' + index + '-' + (product.slug || '') + '-' + (product.href || '')">
                <div class="th-img-container th-cateogry-product-image">
                    <a :href="product.href" class="d-block">
                        <img :src="product.dataSrc" alt="Product Image" class="product-img">
                    </a>
                </div>
                <div class="th-add-to-pinboard th-pinboard-tooltip position-absolute top-right-30" @click="handleAddToPinboard(product, index)"
                    :data-id="product.reference"
                    data-toggle="tooltip"
                    data-bs-toggle="tooltip"
                    data-placement="top"
                    role="button"
                    tabindex="0"
                    data-bs-original-title="Add to Pinboard"
                    data-original-title="Add to Pinboard"
                    data-model="product" 
                    :data-title="product.title" 
                    :data-description="product.description" 
                    :data-image="product.image"
                    :data-product-url = product.product_url
                    >
                        <i class="fa-regular fa-plus" v-if="!itemsAddingToPinboard[index]"></i>
                        <div class="spinner-border" v-if="itemsAddingToPinboard[index]" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                <div class="th-item-footer">
                    <a :href="product.href" data-v-productlistitem-link="">
                        <h3 class="th-title mt-25 font-weight-600" data-v-productlistitem-name="" style="font-size: 22px!important">{{ product.title }}</h3>
                    </a>
                    <p class="th-item-subTitle">{{ getBeforeDash(product.model_type) }}</p>
                </div>
            </div>
        </div>
        <div id="global-search-load-more">
            <SearchLoadMore id="globalSearchLoadMoreButton" @load-more="handleLoadMoreProducts" v-show="hasMoreResults && !loading" />
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
