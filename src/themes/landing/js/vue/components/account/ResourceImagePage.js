
import ResourceImagesTab from './ResourceImagesTab.js';
import ResourceSidebar from './ResourceSidebar.js';
import ResourceLoadMore from './ResourceLoadMore.js';

const ResourceImagePage = {
  name: 'ResourceImagePage',

  components: {
    ResourceImagesTab,
    ResourceSidebar,
    ResourceLoadMore,
  },

  data() {
    return {
      localError: null,
      selectedTab: 'images',
      selectedContext: '', // product, project, post, showrooms
      selectedCategory: 0, // category id
      selectedModelId: 0, // model id
      selectedModelName: '', // model name
      lightGalleryInstance: null, // Store lightGallery instance
    };
  },
  mounted() {
    this.$nextTick(() => {
      const column = this.$el.querySelector('#filter-navigation-container');
      const documentList = this.$el.querySelector('#th-tab-navigation-content');
      const sidebar = this.$el.querySelector('#th-resource-sidebar-sticky');
      setTimeout(() => {
          if (window.handleStickyResourceSidebar && column && sidebar) {
            window.handleStickyResourceSidebar(column, documentList, sidebar);
          }
      }, 400);
    });
  },
  beforeUnmount() {
    // Clean up lightGallery instance when component is destroyed
    if (this.lightGalleryInstance) {
      try {
        this.lightGalleryInstance.destroy();
      } catch (error) {
        console.warn('Error destroying lightGallery instance on component destroy:', error);
      }
      this.lightGalleryInstance = null;
    }
  },
  computed: {
    pagination(){
      return this.$store?.getters.getImagesPagination || {};
    },
    totalImagesCount() {
      return this.$store ? this.$store.state.total : 0;
    },
    storeLoadedImages() {
      return this.$store
        ? this.$store.getters.getResourceImages || []
        : [];
    },
    contextFilters() {
      return this.$store ? this.$store.getters.getContextFilters : [];
    },
    contextCategories() {
      return this.$store ? this.$store.getters.getContextCategories : [];
    },
    loading() {
      return this.$store ? this.$store.state.loading : false;
    },

    error() {
      return this.$store ? this.$store.state.error : this.localError;
    },

    hasMoreImages() {
      return this.$store?.state?.pagination?.images?.total*1 > this.$store?.state?.pagination?.images?.offset*1;
    },
    resourceType() {
      return 'images';
    }
  },

  methods: {
    async downloadImage(dataSrc) {
      try {
        const fileName = dataSrc.split('/').pop().split('?')[0];

        const response = await fetch(dataSrc);
        const blob = await response.blob();

        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = fileName;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error(error);
        this.localError = 'Image download failed';
      }
    },
    changeTab(tab) {
      this.selectedTab = tab;
    },
    async handleLoadMoreImages() {
      await this.$store.commit('INCREMENT_PAGE', 'images');
      await this.$store.dispatch('loadResource', { resource_type: 'images', force: true });
      // setTimeout(() => {
      //   this.initializeImagesGallery();
      // }, 400);
    },
    async handleFilter(filter) {
      try {
        filter.resource_type = 'images';
        filter.current_page = 0;
        filter.offset = 0;
        await this.$store.dispatch('loadResource', { resource_type: 'images', filter: filter, reload: true });
        // setTimeout(() => {
        //   this.initializeImagesGallery();
        // }, 400);
        window.dispatchEvent(new CustomEvent('resourceFilter'));
      } catch (error) {
          console.error('Error in filterImagesFormSubmit:', error);
      }
    },
    async handleResetFilters(filter) {
      try {
        filter.resource_type = 'images';
        filter.current_page = 0;
        filter.offset = 0;
        filter.context = '';
        filter.category = '';
        filter.model_id = '';
        filter.model_name = '';
        await this.$store.dispatch('loadResource', { resource_type: 'images', filter: filter, reload: true });
        window.dispatchEvent(new CustomEvent('resourceFilter'));
      } catch (error) {
          console.error('Error in filterImagesFormSubmit:', error);
      }
    },
    // this is context type filter method section
    async onChangeContextType(contextType) {
      try {
          await this.$store.dispatch('loadContextCategories', { contextType: contextType, resourcePage: 'images' });
          if(contextType === 'showrooms'){
             return this.$store.state.showroomsNames;
          }else{
              return this.$store.state.contextType;
          }
          // return this.$store.state.contextType;
      } catch (error) {
          console.error('Error in onChangeContextType:', error);
      }
    },
    async onChangeCategoryByContextType(categoryByContextType) {
        try {
            await this.$store.dispatch('loadCategoryByContextType', { categoryByContextType: categoryByContextType });
            return this.$store.state.categoryByContextType;
        } catch (error) {
            console.error('Error in onChangeCategoryByContextType:', error);
        }
    },
    async onFilterAutocomplete(filter) {
      // console.log('this is filter in vue component', filter);
      const { context, category, searchValue } = filter;
      await this.$store.dispatch('searchProductNameImageFilter', { context: context, category: category, searchValue: searchValue });
    },

    async onFilterAutocomplete_backupCode(filter) {
      // console.log('this is filter in vue component', filter);
      const { context, category, searchValue } = filter;
      try {
          const response = await this.$store.dispatch('autocompleteProductName', { context: context, category: category, searchValue: searchValue });
          const dropdownMenu = document.getElementById('dropdown-menu');
          dropdownMenu.classList.remove('d-none');
          dropdownMenu.innerHTML = '';
          if (response.length > 0) {
            response.forEach(product => {
                const li = document.createElement('li');
                li.classList.add('dropdown-item');
                li.textContent = product.name;
                li.setAttribute('data-id', product.id); // add product id
                dropdownMenu.appendChild(li);
            });
          } else {
              const li = document.createElement('li');
              li.classList.add('dropdown-item', 'disabled');
              li.textContent = 'No product found';
              dropdownMenu.appendChild(li);
          }
          return response;
      } catch (error) {
          console.error('Error in autocompleteProductName:', error);
          return error.message || 'Failed to autocomplete product name';
      }
    },
    initializeImagesGallery() {
       setTimeout(() => {
          const masonryImages = document.getElementById('th-resources');

          // Destroy existing lightGallery instance if it exists
          if (this.lightGalleryInstance) {
            try {
              this.lightGalleryInstance.destroy();
            } catch (error) {
              console.warn('Error destroying lightGallery instance:', error);
            }
            this.lightGalleryInstance = null;
          }
    
          // Check if element exists before initializing
          if (!masonryImages) {
            console.warn('Element #th-resources not found');
            return;
          }
    
          // Initialize new lightGallery instance and store it
          this.lightGalleryInstance = lightGallery(masonryImages, {
              thumbnail: !1,
              pager: !1,
              plugins: [lgZoom, lgFullscreen, lgRotate, lgShare],
              hash: !1,
              preload: 0
    
          });
       }, 200);
    }
  },

  template: /* html */ `
    <div>
      <div class="col-lg-4 col-12" id="filter-navigation-container">
        <ResourceSidebar 
         :selectedTab="selectedTab" 
         :contextFilters="contextFilters"
         :contextCategories="contextCategories"
         :appliedFilters="pagination"
         @onContextChange="onChangeContextType"
         :resourceType="resourceType"
         @onCategoryChange="onChangeCategoryByContextType"
         @onAutocomplete="onFilterAutocomplete"
         @filter="handleFilter" 
         @resetFilters="handleResetFilters"
         />
      </div>

      <!-- ============================== Design Resource Images ============================== -->
      <div
        class="col-lg-8 col-12 tab-content"
        id="th-tab-navigation-content"
      >
        <ResourceImagesTab
          :resourceImages="storeLoadedImages"
          :loading="loading"
          :error="error"
          @mounted="initializeImagesGallery"
        />
      </div>
      <div id="loadMore">
        <ResourceLoadMore @load-more="handleLoadMoreImages" v-show="hasMoreImages" id="loadMoreImagesButton" />
      </div>
    </div>
  `,
};

export default ResourceImagePage;
