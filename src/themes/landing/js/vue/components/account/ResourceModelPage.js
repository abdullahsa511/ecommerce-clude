import ResourceModelsTab from './ResourceModelsTab.js';
import ResourceSidebar from './ResourceSidebar.js';
import ResourceLoadMore from './ResourceLoadMore.js';

const ResourceModelPage = {
  name: 'ResourceModelPage',

  components: {
    ResourceModelsTab,
    ResourceSidebar,
    ResourceLoadMore,
  },

  data() {
    return {
      localError: null,
      selectedTab: 'models',
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
        if (window.innerWidth <= 992) return;
        if (window.handleStickyResourceSidebar && column && sidebar) {
          window.handleStickyResourceSidebar(column, documentList, sidebar);
        }
      }, 400);
    });
  },
  computed: {
    pagination(){
      return this.$store?.getters.getModelsPagination || {};
    },
    totalModelsCount() {
      return this.$store ? this.$store.getters.getModelsPagination.total : 0;
    },
    storeLoadedModels() {
      return this.$store
        ? this.$store.getters.getResourceModels || []
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

    hasMoreModels() {
      // return this.$store?.state?.pagination?.models?.total*1 > this.$store?.state?.pagination?.models?.offset*1;
      return false;
    },
    resourceType() {
      return 'models';
    }
  },

  methods: {
    async downloadImage(dataSrc) {
    
    },
    changeTab(tab) {
      this.selectedTab = tab;
    },
    async handleLoadMoreModels() {
      await this.$store.commit('INCREMENT_PAGE', 'models');
      await this.$store.dispatch('loadResource', { resource_type: 'models', force: true });
    },
    async handleFilter(filter) {
      try {
        filter.resource_type = 'models';
        filter.current_page = 0;
        filter.offset = 0;
        await this.$store.dispatch('filterModels', { resource_type: 'models', filter: filter.searchValue, category: filter.category, reload: true });
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
          await this.$store.dispatch('loadContextCategories', { contextType: contextType, resourcePage: 'models' });
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
      <ResourceModelsTab
        :resourceModels="storeLoadedModels"
        :loading="loading"
        :error="error"
      />
    </div>
    <div id="loadMore" v-if="false">
       <ResourceLoadMore @load-more="handleLoadMoreModels" v-show="hasMoreModels" id="loadMoreModelsButton" />
    </div>
  </div>
  `,
};

export default ResourceModelPage;
