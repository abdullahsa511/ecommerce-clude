
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
import BlogPostList from './components/blog/BlogPostList.js';

// Import store and services
import store from './store/blogpostlistStore.js';

// Register components globally
Vue.component('blog-post-list', BlogPostList);

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
        async loadMoreBlogPost(container) {
            try {
                // Check if component already exists
                if (this.vueComponents.length === 0) {
                    // Create component only on first load
                    const BlogPostList = this.$options.components['blog-post-list'];
                    const detailComponent = new BlogPostList({
                        parent: this,
                        store: this.$store,
                    });
                    detailComponent.$mount();
                    container.appendChild(detailComponent.$el);
                    this.vueComponents.push(detailComponent);
                    console.log('BlogPostList component created');
                }
                
                // Dispatch action to load next page of posts
                // The component will automatically update reactively
                await this.$store.dispatch('loadPosts');
                
                return {
                    error: this.error,
                    loading: this.loading
                };
            } catch (error) {
                console.error('Error in loadMoreBlogPost:', error);
                return {
                    error: error.message || 'Failed to load posts',
                    loading: false
                };
            }
        }
    }
});

export default app;
