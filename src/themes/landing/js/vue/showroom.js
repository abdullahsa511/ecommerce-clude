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

// Import components
import SectionDetails from './components/SectionDetails.js';

// Import store and services
import store from './store/showroomStore.js';
import showroomService from './services/showroomService.js';



// Register components globally
Vue.component('section-details', SectionDetails);

// Main Vue instance - will be mounted dynamically
const app = new Vue({
    store,
    data: {
        clickedMessage: '',
        showMessage: false,
        selectedSection: null,
        currentSectionName: '',
        showDetail: false
    },
    methods: {
        async greet(sectionName) {
            this.clickedMessage = sectionName;
            this.showMessage = true;
            this.selectedSection = sectionName;
            
            // Fetch section details from API
            try {
                const sectionData = await showroomService.getSectionDetails(sectionName);
                this.$store.dispatch('setSelectedSection', sectionData);
            } catch (error) {
                console.error('Error fetching section details:', error);
            }
        },
        
        // Method to create and mount detail component
        createDetailComponent(container, sectionName, showroomSlug) {
            const SectionDetails = this.$options.components['section-details'];
            const detailComponent = new SectionDetails({
                propsData: {
                    sectionName: sectionName,
                    showroomSlug: showroomSlug
                }
            });
            
            detailComponent.$mount();
            container.appendChild(detailComponent.$el);
            
            return detailComponent;
        }
    }
});

export default app;