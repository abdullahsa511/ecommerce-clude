// Vue and Vuex are already loaded globally from CDN
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

// page components
import ResourceImagePage from './components/account/ResourceImagePage.js';
import ResourceModelPage from './components/account/ResourceModelPage.js';
import ResourceDocumentPage from './components/account/ResourceDocumentPage.js';
import ResourceFinishPage from './components/account/ResourceFinishPage.js';
import ResourceTextilePage from './components/account/ResourceTextilePage.js';




// Import Load More components // not use it
import ResourceImages from './components/account/ResourceImages.js';
import ResourceModels from './components/account/ResourceModels.js';
import ResourceDocuments from './components/account/ResourceDocuments.js';
import ResourceFinishes from './components/account/ResourceFinishes.js';
import ResourceTextiles from './components/account/ResourceTextiles.js';
// import ResourceDocuments from './components/account/ResourceDocuments.js';
// import ResourceFinishes from './components/account/ResourceFinishes.js';
// import ResourceTextiles from './components/account/ResourceTextiles.js';


// Import Tab Content components // not use it
import ResourceImagesTab from './components/account/ResourceImagesTab.js';
import ResourceModelsTab from './components/account/ResourceModelsTab.js';
import ResourceDocumentsTab from './components/account/ResourceDocumentsTab.js';
import ResourceFinishesTab from './components/account/ResourceFinishesTab.js';
import ResourceTextilesTab from './components/account/ResourceTextilesTab.js';

// Import store and services
import store from './store/designResourcesStore.js';

// new Register components
Vue.component('resource-image-page', ResourceImagePage);
Vue.component('resource-model-page', ResourceModelPage);
Vue.component('resource-document-page', ResourceDocumentPage);
Vue.component('resource-finish-page', ResourceFinishPage);
Vue.component('resource-textile-page', ResourceTextilePage);

// Register components globally
Vue.component('resource-images', ResourceImages);
Vue.component('resource-models', ResourceModels);
Vue.component('resource-documents', ResourceDocuments);
Vue.component('resource-finishes', ResourceFinishes);
Vue.component('resource-textiles', ResourceTextiles);
//Register tab components
Vue.component('resource-images-tab', ResourceImagesTab);
Vue.component('resource-models-tab', ResourceModelsTab);
Vue.component('resource-documents-tab', ResourceDocumentsTab);
Vue.component('resource-finishes-tab', ResourceFinishesTab);
Vue.component('resource-textiles-tab', ResourceTextilesTab);

// Main Vue instance - will be mounted dynamically
const app = new Vue({
    store,
    data: {
        vueComponents: [] // Track created components
    },
    computed: {
        pagination() {
            return this.$store.state.pagination;
        },
        error() {
            return this.$store.state.error;
        },
        loading() {
            return this.$store.state.loading;
        }
    },
    methods: {
        // this is loadmore method section
        async loadMoreDesignResourceImages(container) {
            try {
                this.$store.commit('SET_INITITAL_PAGINATION', this.pagination.images.offset);
                let resourceImagePageComponent;
               
                // Create component only on first load
                const ResourceImagePage = this.$options.components['resource-image-page'];
                resourceImagePageComponent = new ResourceImagePage({
                    parent: this,
                    store: this.$store,
                });
                resourceImagePageComponent.$mount();
                if (!this.vueComponents.some(vc => vc.$options.name === 'ResourceImagePage')) {
                    this.vueComponents.push(resourceImagePageComponent);
                }else{
                    const componentIndex = this.vueComponents.findIndex(vc => vc.$options.name === 'ResourceModelPage');
                    this.vueComponents[componentIndex] = resourceImagePageComponent;
                    resourceImagePageComponent.$forceUpdate();
                }
                if(!this.$store.state.resources.images.length){
                    await this.$store.dispatch('loadResource', { resource_type: 'images', reload: true });
                }

                // Wait for Vue to render the loaded content
                await this.$nextTick();
                
                // Extract children from the component's element
                const children = Array.from(resourceImagePageComponent.$el.children);
                
                children.forEach(child => {
                    // Only append if the child isn't already present in the target
                    if (!Array.from(container.children).includes(child)) {
                        container.appendChild(child);
                    }
                });

                
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination
                };
            } catch (error) {
                console.error('Error in load more design resource images:', error);
                return {
                    error: error.message || 'Failed to load design resource images',
                    loading: false
                };
            }
        },
        async loadMoreDesignResourceModels(container) {
            
            try {
                this.$store.commit('SET_INITITAL_PAGINATION', this.pagination.models.offset);
                let resourceModelPageComponent;
                
          
                    // Create component only on first load
                const ResourceModelPage = this.$options.components['resource-model-page'];
                resourceModelPageComponent = new ResourceModelPage({
                    parent: this,
                    store: this.$store,
                });
                resourceModelPageComponent.$mount();
                if (!this.vueComponents.some(vc => vc.$options.name === 'ResourceModelPage')) {
                    this.vueComponents.push(resourceModelPageComponent);
                }else{
                    const componentIndex = this.vueComponents.findIndex(vc => vc.$options.name === 'ResourceModelPage');
                    this.vueComponents[componentIndex] = resourceModelPageComponent;
                    resourceModelPageComponent.$forceUpdate();
                } 
                
                if(!this.$store.state.resources.models.length){
                    await this.$store.dispatch('loadResource', { resource_type: 'models', reload: true });
                }
                
                // Wait for Vue to render the loaded content
                await this.$nextTick();

                // Now extract and append children after data is loaded
                const children = Array.from(resourceModelPageComponent.$el.children);
                    
                children.forEach(child => {
                    // Only append if the child isn't already present in the target
                    if (!Array.from(container.children).includes(child)) {
                        container.appendChild(child);
                    }
                });

                
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination
                };
            } catch (error) {
                console.error('Error in load more design resource models:', error);
                return {
                    error: error.message || 'Failed to load design resource models',
                    loading: false
                };
            }
        },
        async loadMoreDesignResourceDocuments(container) {
            try {
                this.$store.commit('SET_INITITAL_PAGINATION', this.pagination.documents.offset);
                let resourceDocumentPageComponent;
                
          
                    // Create component only on first load
                const ResourceDocumentPage = this.$options.components['resource-document-page'];
                resourceDocumentPageComponent = new ResourceDocumentPage({
                    parent: this,
                    store: this.$store,
                });
                resourceDocumentPageComponent.$mount();
                if (!this.vueComponents.some(vc => vc.$options.name === 'ResourceDocumentPage')) {
                    this.vueComponents.push(resourceDocumentPageComponent);
                }else{
                    const componentIndex = this.vueComponents.findIndex(vc => vc.$options.name === 'ResourceDocumentPage');
                    this.vueComponents[componentIndex] = resourceDocumentPageComponent;
                    resourceDocumentPageComponent.$forceUpdate();
                } 
                
                if(!this.$store.state.resources.documents.length){
                    await this.$store.dispatch('loadResource', { resource_type: 'documents', reload: true });
                }
                
                // Wait for Vue to render the loaded content
                await this.$nextTick();

                // Now extract and append children after data is loaded
                const children = Array.from(resourceDocumentPageComponent.$el.children);
                    
                children.forEach(child => {
                    // Only append if the child isn't already present in the target
                    if (!Array.from(container.children).includes(child)) {
                        container.appendChild(child);
                    }
                });

                
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination
                };
            } catch (error) {
                console.error('Error in load more design resource documents:', error);
                return {
                    error: error.message || 'Failed to load design resource documents',
                    loading: false
                };
            }
        },
        async loadMoreDesignResourceFinishes(container) {
            try {
                this.$store.commit('SET_INITITAL_PAGINATION', this.pagination.finishes.offset);
                let resourceFinishPageComponent;
                
          
                    // Create component only on first load
                const ResourceFinishPage = this.$options.components['resource-finish-page'];
                resourceFinishPageComponent = new ResourceFinishPage({
                    parent: this,
                    store: this.$store,
                });
                resourceFinishPageComponent.$mount();
                if (!this.vueComponents.some(vc => vc.$options.name === 'ResourceFinishPage')) {
                    this.vueComponents.push(resourceFinishPageComponent);
                }else{
                    const componentIndex = this.vueComponents.findIndex(vc => vc.$options.name === 'ResourceFinishPage');
                    this.vueComponents[componentIndex] = resourceFinishPageComponent;
                    resourceFinishPageComponent.$forceUpdate();
                } 
                
                if(!this.$store.state.resources.finishes.length){
                    await this.$store.dispatch('loadResource', { resource_type: 'finishes', reload: true });
                }
                
                // Wait for Vue to render the loaded content
                await this.$nextTick();

                // Now extract and append children after data is loaded
                const children = Array.from(resourceFinishPageComponent.$el.children);
                    
                children.forEach(child => {
                    // Only append if the child isn't already present in the target
                    if (!Array.from(container.children).includes(child)) {
                        container.appendChild(child);
                    }
                });

                
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination
                };
            } catch (error) {
                console.error('Error in load more design resource documents:', error);
                return {
                    error: error.message || 'Failed to load design resource finishes',
                    loading: false
                };
            }
        },
        async loadMoreDesignResourceTextiles(container) {
            try {
                this.$store.commit('SET_INITITAL_PAGINATION', this.pagination.textiles.offset);
                let resourceTextilePageComponent;
                
          
                    // Create component only on first load
                const ResourceTextilePage = this.$options.components['resource-textile-page'];
                resourceTextilePageComponent = new ResourceTextilePage({
                    parent: this,
                    store: this.$store,
                });
                resourceTextilePageComponent.$mount();
                if (!this.vueComponents.some(vc => vc.$options.name === 'ResourceTextilePage')) {
                    this.vueComponents.push(resourceTextilePageComponent);
                }else{
                    const componentIndex = this.vueComponents.findIndex(vc => vc.$options.name === 'ResourceTextilePage');
                    this.vueComponents[componentIndex] = resourceTextilePageComponent;
                    resourceTextilePageComponent.$forceUpdate();
                } 
                
                if(!this.$store.state.resources.textiles.length){
                    await this.$store.dispatch('loadResource', { resource_type: 'textiles', reload: true });
                }
                
                // Wait for Vue to render the loaded content
                await this.$nextTick();

                // Now extract and append children after data is loaded
                const children = Array.from(resourceTextilePageComponent.$el.children);
                    
                children.forEach(child => {
                    // Only append if the child isn't already present in the target
                    if (!Array.from(container.children).includes(child)) {
                        container.appendChild(child);
                    }
                });

                
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination
                };
            } catch (error) {
                console.error('Error in load more design resource textiles:', error);
                return {
                    error: error.message || 'Failed to load design resource textiles',
                    loading: false
                };
            }
        },

        // this is tabload method section
        async intializeVueAppAndLoadTabContent(container, method, component, payload = {}) {
            try {
                console.log('this is payload in intializeVueAppAndLoadTabContent', payload);
                const componentClass = this.$options.components[component];
                const componentInstance = new componentClass({
                    parent: this,
                    store: this.$store,
                });
                
                // Check if component already exists
                if (!this.vueComponents.some(vc => vc.$options.name === componentInstance.$options.name)) {
                    componentInstance.$mount();
                    this.vueComponents.push(componentInstance);
                }
                else(componentInstance.$el === 'undefined')
                {
                    componentInstance.$mount();
                }
                
                // The component will automatically update reactively.
                // console.log('this is instance', componentInstance.$el);
               
                // await this.$store.dispatch(method);
                const response = await this.$store.dispatch(method, { force: false, payload:payload || {} });
                // console.log('this is response in intializeVueAppAndLoadTabContent', response);

                await this.$nextTick();
                container.appendChild(componentInstance.$el);
                return {
                    error: this.error,
                    loading: this.loading,
                    pagination: this.pagination,
                    total_items: response?.items?.length || 0,
                };
            } catch (error) {
                console.error('Error in load more design resource images:', error);
                return {
                    error: error.message || 'Failed to load design resource images',
                    loading: false
                };
            } 
        }
    }
});

export default app;