
const Vue = window.Vue;
const Vuex = window.Vuex;
if (Vue && Vuex) {
    if (!window.__vuexInstalled) {
        // Vue.use(Vuex);
        window.__vuexInstalled = true;
    }
} else {
    console.error('Vue or Vuex is not available. Make sure both CDN scripts are loaded.');
}

// Import components
import SearchList from './components/search/SearchList.js';
// Import store and services
import store from './store/searchStore.js';

// Register components globally
Vue.component('search-list', SearchList);
// Vue.component('product-desk-modesty-panel', ProductDeskModestyPanel);

const app = new Vue({
    store,
    data: {
        vueComponents: [] // Track created components
    },
    computed: {
        error() {
            return this.$store.state.error;
        },
        loading() {
            return this.$store.state.loading;
        }
    },
    methods: {    
        // Method to load more lists and render them
        async loadCategoryProductListing(container, configuration = null) {
            try {
                await this.$store.dispatch('bootstrapSearchPaginationFromUrl');
                if (!container) {
                    throw new Error('Missing target container element for product desk modesty panel.');
                }

                const target =
                    container instanceof Element
                        ? container
                        : (typeof container.length === 'number' && container.length > 0
                            ? container[0]
                            : null);

                if (target && !(target instanceof Element)) {
                    throw new Error('Provided container is not a DOM element.');
                }

                if (!target) {
                    throw new Error('Unable to resolve product desk modesty panel mount element.');
                }

                // if (configuration) {
                //     await this.$store.dispatch('loadCategoryProductListing', {
                //         reset: true,
                //         useDemoData: false,
                //     });
                // } else if (!this.$store.state.products.length) {
                //     await this.$store.dispatch('loadCategoryProductListing');
                // }

                // alert('create component vue panel desk modesty 1');

                // Check if component already exists
                if (this.vueComponents.length === 0) {
                
                    // Create component only on first load
                    const SearchList = this.$options.components['search-list'];
                    const detailComponent = new SearchList({
                        parent: this,
                        store: this.$store,
                    });
                    detailComponent.$mount();
                    target.appendChild(detailComponent.$el);
                    this.vueComponents.push(detailComponent);
                }
                
                return {
                    error: this.error,
                    loading: this.loading
                };
            } catch (error) {
                console.error('Error in loadProductDeskModesty:', error);
                return {
                    error: error.message || 'Failed to load product desk modesty panel',
                    loading: false
                };
            }
        }
    }
});

export default app;
