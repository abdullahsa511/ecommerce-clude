import FilterableProduct from './FilterableProduct.js';
export default {
    name: 'CategoryProductListing',
    props: {},
    components: {
        FilterableProduct
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
        storeProducts() {
            // console.log('storeProducts', this.$store.getters.products);
            return this.$store ? this.$store.getters.products || [] : [];
        },
        productCount() {
        return this.$store.getters.products?.items?.length || 0;
        },
        isLoadingMore() {
        // console.log('isLoadingMore', this.$store.getters.loadMore);
        return this.$store.getters.loadMore;
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
        return this.$store.getters.resetFilter;
        },
        loading() {
        console.log('loading', this.$store.getters.loading);
        return this.$store.getters.loading;
        },
        error() {
        return this.$store.getters.error;
        }

      },

    mounted() {
        this.$store.dispatch("loadCategoryProductListing");
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
                model_id: product.id,
                model_type: 'product',
                title: product.title,
                description: product.description,
                image: product.image || '/img/pinboard/pinboard img 1.png',
                url: this.getProductUrl(product.category_slug, product.slug)
            }
            await window.pinboardApp.addToPinboard(pinboardItem);
            setTimeout(() => {
                this.$set(this.itemsAddingToPinboard, index, false);
            }, 100);
        }
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

    template: /* html */ `
    <div id="vue-app-product-desk-modesty-panel">
        <div class="row">
            <div class="col-12">
            <div class="th-section-header" style="display: block">
                <div class="th-section-header-wrapper">
                <h2 class="th-section-title">{{ productCount }} Results Found</h2>

               <!-- <div class="th-section-subtitle" style="font-size: 16px;">
                    Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                </div> -->
                </div>
            </div>

            <!-- ============================== Product Filters ============================== -->
            <FilterableProduct 
                    :productCount="productCount" 
                    :materials="materials"
                    :features="features"
                    :certifications="certifications"
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
            <div class="th-item-product col-6 col-sm-6 col-md-4 col-lg-4" v-for="(product, index) in storeProducts.items" :key="product.product_id">
                <div class="th-img-container th-cateogry-product-image">
                    <a :href="getProductUrl(product.category_slug, product.slug)" class="justify-content-center">
                        <img :src="product.image" alt="Product Image" />
                    </a>
                </div>

                <div class="th-add-to-pinboard position-absolute top-right-30" @click="handleAddToPinboard(product, index)"
                 :data-id="product.id" data-model="product" :data-title="product.name" :data-description="product.description" :data-image="product.image">
                    <i class="fa-solid fa-plus" v-if="!itemsAddingToPinboard[index]"></i>
                    <div class="spinner-border" v-if="itemsAddingToPinboard[index]" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="th-item-footer">
                    <!-- <div class="label">Build</div> -->

                    <h3 class="th-title mt-25">
                        <a :href="getProductUrl(product.category_slug, product.slug)">
                             {{ product.name }}
                        </a>
                    </h3>

                    <p class="th-description mb-10">
                    {{ product.description  }}
                    </p>

                    <div class="th-tag-name">
                        <div class="th-tag" v-for="tag in product.tags" :key="tag.name">{{ tag.name }}</div>
                    </div>

                    <div class="th-item-finish-circle">
                        <div class="th-circle" v-for="finish in product.finishes" :key="finish.name">{{ finish.name }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================== Pagination ============================== -->
        <div class="row" v-if="isLoadingMore">
            <div class="col-lg-4"></div>

            <div class="col-lg-6 col-12">
            <div class="d-flex justify-content-center">
                <button
                    @click.prevent="handleLoadMoreProducts"
                    class="th-btn-gray text-capitalize mt-50"
                >
                <span class="mr-5">Load More</span>
                </button>
            </div>
            </div>
        </div>
        <!-- ============================== End Pagination ============================== -->
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
