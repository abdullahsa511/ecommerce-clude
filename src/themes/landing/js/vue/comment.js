
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
import Comment from './components/comment/CommentPanel.js';

// Import store and services
import store from './store/commentStore.js';

// Register components globally
Vue.component('comment', Comment);

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
        async loadComments(container,payload = {}) {
            const normalizedPayload = payload && typeof payload === 'object' ? payload : {};
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

                if (normalizedPayload) {
                    await this.$store.dispatch('loadComments', normalizedPayload);
                } 

                // Check if component already exists
                if (this.vueComponents.length === 0) {
                    // Create component only on first load
                    const Comment = this.$options.components['comment'];
                    const detailComponent = new Comment({
                        parent: this,
                        store: this.$store,
                        propsData: {
                            modelId: normalizedPayload.model_id || '',
                            modelUuid: normalizedPayload.model_uuid || '',
                            modelRef: normalizedPayload.model_ref || '',
                            modelType: normalizedPayload.model_type || ''
                        }
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
                console.error('Error in loadComments:', error);
                return {
                    error: error.message || 'Failed to load comments',
                    loading: false
                };
            }
        }
    }
});

export default app;
