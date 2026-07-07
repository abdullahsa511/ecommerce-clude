import ResourceDocumentsTab from './ResourceDocumentsTab.js';
import ResourceSidebar from './ResourceSidebar.js';
import ResourceLoadMore from './ResourceLoadMore.js';

const ResourceDocumentPage = {
  name: 'ResourceDocumentPage',

  components: {
    ResourceDocumentsTab,
    ResourceSidebar,
    ResourceLoadMore,
  },

  data() {
    return {
      localError: null,
      selectedTab: 'documents',
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
  computed: {
    pagination(){
      return this.$store?.getters.getDocumentsPagination || {};
    },
    totalModelsCount() {
      return this.$store ? this.$store.getters.getDocumentsPagination.total : 0;
    },
    storeLoadedDocuments() {
      return this.$store
        ? this.$store.getters.getResourceDocuments || []
        : [];
    },
    contextFilters() {
      return this.$store ? this.$store.getters.getContextFilters : [];
    },
    contextCategories() {
      return this.$store.state?.pagination.documents.context_categories || [];
    },
    loading() {
      return this.$store ? this.$store.state.loading : false;
    },

    error() {
      return this.$store ? this.$store.state.error : this.localError;
    },

    hasMoreDocuments() {
      // return this.$store?.state?.pagination?.documents?.total*1 > this.$store?.state?.pagination?.documents?.offset*1;
      return false;
    },
    resourceType() {
      return 'documents';
    }
  },

  methods: {
    async downloadImage(dataSrc) {
    
    },
    changeTab(tab) {
      this.selectedTab = tab;
    },
    async handleLoadMoreDocuments() {
      await this.$store.commit('INCREMENT_PAGE', 'documents');
      await this.$store.dispatch('loadResource', { resource_type: 'documents', force: true });
    },
    // async handleFilter(filter) {
    //   try {
    //     filter.resource_type = 'documents';  
    //     filter.current_page = 0;
    //     filter.offset = 0;
    //     await this.$store.dispatch('loadResource', { resource_type: 'documents', filter: filter, reload: true });
    //   } catch (error) {
    //       console.error('Error in handleFilter:', error);
    //   }
    // },
    async handleFilter(filter) {
      try {
        filter.resource_type = 'documents';
        filter.current_page = 0;
        filter.offset = 0;
        await this.$store.dispatch('filterModels', { resource_type: 'documents', filter: filter.searchValue, reload: true });
        window.dispatchEvent(new CustomEvent('resourceFilter'));
      } catch (error) {
          console.error('Error in handleFilter:', error);
      }
    },
    async onFilterAutocomplete(filter) {
      this.handleFilter(filter);
    },
    // this is context type filter method section
    async onChangeContextType(contextType) {
      try {
          await this.$store.dispatch('loadContextCategories', { contextType: contextType, resourcePage: 'documents' });
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
    // async onFilterAutocomplete(filter) {
    //   console.log('this is filter in vue component', filter);
    //   const { context, category, searchValue } = filter;
    //   try {
    //       const response = await this.$store.dispatch('autocompleteProductName', { context: context, category: category, searchValue: searchValue });
    //       const dropdownMenu = document.getElementById('dropdown-menu');
    //       dropdownMenu.classList.remove('d-none');
    //       dropdownMenu.innerHTML = '';
    //       if (response.length > 0) {
    //         response.forEach(product => {
    //             const li = document.createElement('li');
    //             li.classList.add('dropdown-item');
    //             li.textContent = product.name;
    //             li.setAttribute('data-id', product.id); // add product id
    //             dropdownMenu.appendChild(li);
    //         });
    //       } else {
    //           const li = document.createElement('li');
    //           li.classList.add('dropdown-item', 'disabled');
    //           li.textContent = 'No product found';
    //           dropdownMenu.appendChild(li);
    //       }
    //       return response;
    //   } catch (error) {
    //       console.error('Error in autocompleteProductName:', error);
    //       return error.message || 'Failed to autocomplete product name';
    //   }
    // },
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
      />
    </div>

    <!-- ============================== Design Resource Images ============================== -->
    <div
      class="col-lg-8 col-12 tab-content"
      id="th-tab-navigation-content"
    >
      <ResourceDocumentsTab
        :resourceDocuments="storeLoadedDocuments"
        :loading="loading"
        :error="error"
      />
    </div>
    <div id="loadMore">
       <ResourceLoadMore @load-more="handleLoadMoreDocuments" v-show="hasMoreDocuments" id="loadMoreDocumentsButton" />
    </div>
  </div>
  `,
};

export default ResourceDocumentPage;
