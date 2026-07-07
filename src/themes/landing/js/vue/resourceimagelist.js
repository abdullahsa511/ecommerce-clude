
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
import ResourceImageList from './components/account/ResourceImageList.js';

// Import store and services
import store from './store/resourceimagelistStore.js';

// Register components globally
Vue.component('th-masonry-img-item', ResourceImageList);

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
        // Method to load more posts and render them
        async loadMoreResourceImages(container) {
            try {
                // Check if component already exists
                if (this.vueComponents.length === 0) {
                    // Create component only on first load
                    const ResourceImageList = this.$options.components['th-masonry-img-item'];
                    const detailComponent = new ResourceImageList({
                        parent: this,
                        store: this.$store,
                    });
                    detailComponent.$mount();
                    this.vueComponents.push(detailComponent);
                }
                
                // Dispatch action to load next page of posts
                // The component will automatically update reactively
                await this.$store.dispatch('loadPosts');
                
                // Wait for Vue to render the loaded content
                await this.$nextTick();
                
                // Now extract and append children after data is loaded
                const detailComponent = this.vueComponents[0];
                
                // Extract children from the component's element
                const children = Array.from(detailComponent.$el.children);
                
                // Append only the children, not the wrapper
                children.forEach(child => {
                    container.appendChild(child);
                });
                
                return {
                    error: this.error,
                    loading: this.loading
                };
            } catch (error) {
                console.error('Error in loadMoreResourceImages:', error);
                return {
                    error: error.message || 'Failed to load posts',
                    loading: false
                };
            }
        }
    }
});

export default app;
