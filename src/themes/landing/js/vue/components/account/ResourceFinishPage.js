import ResourceFinishesTab from './ResourceFinishesTab.js';
import ResourceSidebar from './ResourceSidebar.js';
import ResourceLoadMore from './ResourceLoadMore.js';

const ResourceFinishPage = {
  name: 'ResourceFinishPage',

  components: {
    ResourceFinishesTab,
    ResourceSidebar,
    ResourceLoadMore,
  },

  data() {
    return {
      localError: null,
      selectedTab: 'finishes',
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
      return this.$store?.getters.getFinishesPagination || {};
    },
    totalFinishesCount() {
      return this.$store ? this.$store.getters.getFinishesPagination.total : 0;
    },
    storeLoadedFinishes() {
      return this.$store
        ? this.$store.getters.getResourceFinishes || []
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

    hasMoreFinishes() {
      // return this.$store?.state?.pagination?.finishes?.total*1 > this.$store?.state?.pagination?.finishes?.offset*1;
      return false;
    },
    resourceType() {
      return 'finishes';
    }
  },

  methods: {
    async downloadImage(dataSrc) {
    
    },
    changeTab(tab) {
      this.selectedTab = tab;
    },
    async handleLoadMoreFinishes() {
      await this.$store.commit('INCREMENT_PAGE', 'finishes');
      await this.$store.dispatch('loadResource', { resource_type: 'finishes', force: true });
    },
    // async handleFilter(filter) {
    //   try {
    //     filter.resource_type = 'finishes';  
    //     filter.current_page = 0;
    //     filter.offset = 0;
    //     await this.$store.dispatch('loadResource', { resource_type: 'finishes', filter: filter, reload: true });
    //   } catch (error) {
    //       console.error('Error in handleFilter:', error);
    //   }
    // },
    async handleFilter(filter) {
      try {
      // await this.$store.dispatch('searchFinishTextileFilter', {
      //   filter: filter.searchValue,
      //   resource_type: 'finishes'
      // });

      // return false
        filter.resource_type = 'finishes';
        filter.current_page = 0;
        filter.offset = 0;

        await this.$store.dispatch('filterModels', { 
          resource_type: 'finishes', 
          context: filter.context, 
          category: filter.category,
          filter: filter.searchValue, 
          reload: true }
        );
        window.dispatchEvent(new CustomEvent('resourceFilter'));
      } catch (error) {
          console.error('Error in handleFilter:', error);
      }
    },
    async handleResetFilters(filter) {
      try {
        await this.$store.dispatch('filterModels', { 
          resource_type: 'finishes', 
          context: 'brand', 
          category: '',
          filter: '', 
          reload: true }
        );
        window.dispatchEvent(new CustomEvent('resourceFilter'));
      } catch (error) {
          console.error('Error in handleResetFilters:', error);
      }
    },
    async onFilterAutocomplete(filter) {
      this.handleFilter(filter);
    },
    // this is context type filter method section
    async onChangeContextType(contextType) {
      try {
          await this.$store.dispatch('loadContextCategories', { contextType: contextType, resourcePage: 'finishes' });
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
    initializeFinishesGallery() {
      const finishesContainer = document.getElementById('th-resources');

      if (this.lightGalleryInstance) {
        try {
          this.lightGalleryInstance.destroy();
        } catch (error) {
          console.warn('Error destroying lightGallery instance:', error);
        }
        this.lightGalleryInstance = null;
      }

      if (!finishesContainer) {
        console.warn('Element #th-resources not found');
        return;
      }

      this.lightGalleryInstance = lightGallery(finishesContainer, {
        thumbnail: !1,
        pager: !1,
        plugins: [lgZoom, lgFullscreen, lgRotate, lgShare],
        hash: !1,
        preload: 0
      });
    },
  //   async onFilterAutocomplete(filter) {
  //     console.log('this is filter in vue component', filter);
  //     const { context, category, searchValue } = filter;
  //     try {
  //         const response = await this.$store.dispatch('autocompleteProductName', { context: context, category: category, searchValue: searchValue });
  //         const dropdownMenu = document.getElementById('dropdown-menu');
  //         dropdownMenu.classList.remove('d-none');
  //         dropdownMenu.innerHTML = '';
  //         if (response.length > 0) {
  //           response.forEach(product => {
  //               const li = document.createElement('li');
  //               li.classList.add('dropdown-item');
  //               li.textContent = product.name;
  //               li.setAttribute('data-id', product.id); // add product id
  //               dropdownMenu.appendChild(li);
  //           });
  //         } else {
  //             const li = document.createElement('li');
  //             li.classList.add('dropdown-item', 'disabled');
  //             li.textContent = 'No product found';
  //             dropdownMenu.appendChild(li);
  //         }
  //         return response;
  //     } catch (error) {
  //         console.error('Error in autocompleteProductName:', error);
  //         return error.message || 'Failed to autocomplete product name';
  //     }
  //   },
  },

  template: /* html */ `
  <div>
    <div class="col-lg-4 col-12" id="filter-navigation-container">
      <ResourceSidebar 
      :selectedTab="selectedTab" 
      :contextFilters="contextFilters"
      :contextCategories="contextCategories"
      :appliedFilters="pagination"
      :resourceType="resourceType"
      @onContextChange="onChangeContextType"
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
      <ResourceFinishesTab
        :resourceFinishes="storeLoadedFinishes"
        :loading="loading"
        :error="error"
        @mounted="initializeFinishesGallery"
      />
    </div>
    <div id="loadMore">
       <ResourceLoadMore @load-more="handleLoadMoreFinishes" v-show="hasMoreFinishes" id="loadMoreFinishesButton" />
    </div>
  </div>
  `,
};

export default ResourceFinishPage;
