
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
import ProjectList from './components/project/ProjectList.js';

// Import store and services
import store from './store/projectListStore.js';

// Register components globally
Vue.component('project-list', ProjectList);

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
        async loadMoreProjectList(container) {
            try {
                // Check if component already exists
                if (this.vueComponents.length === 0) {
                    // Create component only on first load
                    const ProjectList = this.$options.components['project-list'];
                    const detailComponent = new ProjectList({
                        parent: this,
                        store: this.$store,
                    });
                    detailComponent.$mount();
                    container.appendChild(detailComponent.$el);
                    this.vueComponents.push(detailComponent);
                    console.log('ProjectList component created');
                }
                
                // Dispatch action to load next page of lists
                // The component will automatically update reactively
                await this.$store.dispatch('loadLists');
                
                return {
                    error: this.error,
                    loading: this.loading
                };
            } catch (error) {
                console.error('Error in loadMoreProjectList:', error);
                return {
                    error: error.message || 'Failed to load lists',
                    loading: false
                };
            }
        }
    }
});

export default app;
