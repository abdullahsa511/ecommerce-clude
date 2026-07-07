
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
import ProductConfigurator from './components/productconfigurator/ProductConfigurator.js';
// Import store and services
import store from './store/productConfiguratorStore.js';

// Register components globally
Vue.component('product-configurator', ProductConfigurator);
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
        },
        loadedProductConfigurator() {
            return this.$store.state.loadedProductConfigurator;
        }
    },
    methods: {    
        // Method to load more lists and render them
        async loadProductConfigurator(container, configuration = null, product = null, modelData = null, accessories = null) {
            try {
                if (!container) {
                    throw new Error('Missing target container element for product configurator.');
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
                    throw new Error('Unable to resolve product configurator mount element.');
                }

                if (configuration) {
                    await this.$store.dispatch('loadProductConfigurator', {
                        product,
                        configuration: configuration || [],
                        reset: true,
                        useDemoData: false,
                        modelData,
                        accessories
                    });
                } else if (!this.$store.state.loadedProductConfigurator?.length) {
                    await this.$store.dispatch('loadProductConfigurator');
                }

                // Check if component already exists
                if (this.vueComponents.length === 0) {
                    // Create component only on first load
                    const ProductConfigurator = this.$options.components['product-configurator'];
                    const detailComponent = new ProductConfigurator({
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
                console.error('Error in loadProductConfigurator:', error);
                return {
                    error: error.message || 'Failed to load product configurator',
                    loading: false
                };
            }
        }
    }
});

export default app;
