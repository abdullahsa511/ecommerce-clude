export default {
  name: 'FilterableSearch',

  emits: ['onchange-input', 'onapply-filters', 'onreset-filters'],

  props: {
    productCount: { type: Number, default: 0, required: false },
    selectedFilters: { type: Object, default: () => ({}), required: false },
    filterLoading: { type: Boolean, default: false, required: false },
    resetFilter: { type: Boolean, default: false, required: false }
  },

  data() {
    return {
      searchQuery: this.selectedFilters.search_query || '',
      selectedContexts: this.selectedFilters.contexts || [],
      showDropdown: false,
      loadingAction: null,

      contextOptions: [
        { value: 'product', label: 'Product' },
        { value: 'project', label: 'Project' },
        { value: 'post', label: 'Blog' },
        { value: 'showrooms', label: 'Showroom' }
      ]
    };
  },

  computed: {
    showFilterLoading() {
      return this.filterLoading && this.loadingAction !== 'reset';
    },

    showResetLoading() {
      return this.resetFilter || (this.filterLoading && this.loadingAction === 'reset');
    }
  },

  watch: {
    filterLoading(value) {
      if (!value && !this.resetFilter) {
        this.loadingAction = null;
      }
    },

    resetFilter(value) {
      if (!value && !this.filterLoading) {
        this.loadingAction = null;
      }
    }
  },

  mounted() {
    document.addEventListener('click', this.closeDropdown);
  },

  beforeUnmount() {
    document.removeEventListener('click', this.closeDropdown);
  },

  methods: {

    /* ---------------- SEARCH ---------------- */
    handleSearchInput(event) {
      this.searchQuery = event.target.value;
      this.$emit('onchange-input', {
        search_query: this.searchQuery
      });
    },

    /* ---------------- DROPDOWN ---------------- */
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },

    closeDropdown() {
      this.showDropdown = false;
    },

    /* ---------------- APPLY ---------------- */
    handleApplyFilters() {
      this.$emit('onapply-filters', {
        per_page: 40,
        current_page: 1,
        offset: 0,
        search_query: this.searchQuery,
        contexts: this.selectedContexts
      });
    },

    /* ---------------- RESET ---------------- */
    handleResetFilters() {
      this.searchQuery = '';
      this.selectedContexts = [];
      this.showDropdown = false;

      this.$emit('onreset-filters', {
        per_page: 40,
        current_page: 1,
        offset: 0,
        search_query: '',
        contexts: [],
        reset: true
      });
    }
  },

  template: /* html */ `
    <div class="th-filters-wrapper mb-30">
      <div class="th-filters-form">
        <div class="row align-items-end">

          <!-- QUICK SEARCH -->
          <div class="col-12 col-md-6 mb">
            <input
              type="text"
              class="form-control form-control-lg bg-white th-margin-bottom-20"
              id="quick-search-input"
              placeholder="Quick Search"
              autocomplete="off"
              v-model="searchQuery"
              @keydown.enter.prevent="handleApplyFilters"
            />
          </div>

          <!-- CHECKBOX MULTISELECT -->
          <div class="col-12 col-md-4">
            <div class="custom-multiselect th-margin-bottom-20" @click.stop="toggleDropdown">

              <!-- DISPLAY BOX -->
              <div class="multiselect-display">
                <div class="selected-tags" v-if="selectedContexts.length">
                  <span
                    class="tag"
                    v-for="item in selectedContexts"
                    :key="item"
                  >
                    {{ contextOptions.find(o => o.value === item)?.label }}
                  </span>
                </div>

                <span v-else class="context-placeholder">
                  Select Context
                </span>

                <span class="arrow" :class="{ open: showDropdown }"></span>
              </div>

              <!-- DROPDOWN -->
              <div
                v-if="showDropdown"
                class="multiselect-dropdown shadow"
                @click.stop
              >
                <label
                  class="multiselect-dropdown-item"
                  v-for="option in contextOptions"
                  :key="option.value"
                >
                  <input
                    type="checkbox"
                    :value="option.value"
                    v-model="selectedContexts"
                  />
                  {{ option.label }}
                </label>
              </div>

            </div>
          </div>

          <!-- FILTER BUTTON -->
          <div class="col-6 col-md-1">
            <button
              class="th-custom-filter-btn w-100 py-2 search-filter-button d-inline-flex align-items-center justify-content-center"
              @click="handleApplyFilters">

              <span v-if="!showFilterLoading">
                <i class="fa fa-filter"></i>
              </span>

              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Loading...</span>
              </span>

            </button>
          </div>

          <!-- RESET BUTTON -->
          <div class="col-6 col-md-1">
            <button
              class="th-secondary-btn w-100 py-2 search-filter-button d-inline-flex align-items-center justify-content-center"
              @click="handleResetFilters">

              <span v-if="!showResetLoading">
                <i class="fa fa-eraser"></i>
              </span>

              <span v-else>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Resetting...</span>
              </span>

            </button>
          </div>

        </div>
      </div>
    </div>
  `
};