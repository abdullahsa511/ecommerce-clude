
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
import Searchbar from './components/searchbar/Searchbar.js';

// Import store and services
import store from './store/searchbarStore.js';

// Register components globally
Vue.component('searchbar', Searchbar);

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
        loadedSearchbar() {
            return this.$store.state.loadedSearchbar;
        }
    },
    methods: {    
        // Method to load more lists and render them
        async loadSearchbar(container, searchValue = null, options = {}) {
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
                    throw new Error('Unable to resolve searchbar mount element.');
                }

                const variant = options.variant || 'desktop';
                const alreadyMounted = this.vueComponents.some(
                    (component) => component.$mountTarget === target
                );

                if (!this._searchbarDataLoaded) {
                    if (searchValue) {
                        await this.$store.dispatch('loadSearchbar', {
                            searchValue,
                        });
                    } else if (!this.$store.state.loadedSearchbar.length) {
                        await this.$store.dispatch('loadSearchbar');
                    }
                    this._searchbarDataLoaded = true;
                }

                if (!alreadyMounted) {
                    const SearchbarComponent = this.$options.components['searchbar'];
                    const detailComponent = new SearchbarComponent({
                        parent: this,
                        store: this.$store,
                        propsData: { variant },
                    });
                    detailComponent.$mountTarget = target;
                    detailComponent.$mount();
                    target.appendChild(detailComponent.$el);
                    this.vueComponents.push(detailComponent);
                }
                
                return {
                    error: this.error,
                    loading: this.loading
                };
            } catch (error) {
                console.error('Error in loadSearchbar:', error);
                return {
                    error: error.message || 'Failed to load searchbar',
                    loading: false
                };
            }
        },
        async tagSearch() {
            try {
               console.log('tagSearch function');
               const searchResults = await this.$store.dispatch('tagSearch');
               console.log('searchResults =', searchResults);
            } catch (error) {
                console.error('Error in tagSearch:', error);
            }
        }
    }
});

export default app;
