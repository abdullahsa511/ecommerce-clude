export default {
  name: 'ResourceSidebar',

  props: {
    selectedTab: { type: String, required: false },
    appliedFilters: { type: Object, default: () => ({}), required: false },
    contextFilters: { type: Array, required: false },
    contextCategories: { type: Array, required: false },
    resourceType: { type: String, required: false },
  },
  emits: ['filter', 'onContextChange', 'onCategoryChange', 'onAutocomplete', 'onAutoSuggestionClick', 'resetFilters'],
  data() {
    return {
      categoryByContextTypeChoices: null,
      localFilter: {},
      choicesReady: false
    }
  },
  created() {

  },
  computed: {
    disableContextSelection() {
      // console.log('this is resourceType', this.resourceType);
      return ['models', 'documents'].includes(this.resourceType) ? true : false;
    },
    categories(){
      return this.$store ? this.$store.getters.getContextCategories : [];
    },
    isContextSelected() {
      return !!this.localFilter.context;
    },
    isCategorySelected() {
      return !!this.localFilter.category;
    },
    disableAutocomplete() {
      return !!!(
        (this.isContextSelected && this.localFilter.context === 'product' && this.isCategorySelected)
        || this.isContextSelected
        || this.localFilter.context === 'project' 
        || this.localFilter.context === 'showrooms'
      )
    },
    autoCompletePlaceholderText(){
      var text = "Search " + this.resourceType.charAt(0).toUpperCase() + this.resourceType.slice(1);
      // if(this.resourceType && ['finishes', 'textiles'].includes(this.resourceType)){
      //   text += " by " + this.localFilter?.context?.charAt(0).toUpperCase() + this.localFilter?.context?.slice(1);
      // }
      
      return text;
    },
    filter: {
      get() {
        // Sync Choice.js components when reading filter
        // this.syncChoicesWithFilter();
        return this.localFilter;
      }
    },
    isContextVisible() {
      return ['images', 'finishes', 'textiles'].includes(this.resourceType);
    },
    isResetVisible() {
      const { context, category, model_id, model_name, searchValue } = this.localFilter || {};

      // only for specific resource types
      if (!['images', 'finishes', 'textiles'].includes(this.resourceType)) {
        return false;
      }
    
      // if any filter applied
      let isVisible = !!(context || category || model_id || model_name || searchValue);
    
      // special rule for brand
      if (context === 'brand') {
        const hasBrandSearch = !!(model_id || model_name || searchValue);
        const hasCategory = !!category;
    
        // show reset only if category OR search exists
        isVisible = hasCategory || hasBrandSearch;
      }
    
      return isVisible;
    },
    isCategoryVisible() {
     return this.isContextSelected && ['product', 'showrooms', 'brand', 'type'].includes(this.localFilter.context) && !['models', 'documents'].includes(this.resourceType);
    },
    isAutocompleteVisible() {
      return this.isContextSelected && (this.localFilter.context !== 'product' || this.isCategorySelected || ['models','documents'].includes(this.resourceType));
    }
  },
  methods: {
    syncChoicesWithFilter() {
      // Sync Choice.js components with localFilter values (only after Choices are initialized)
      if (!this.choicesReady) return;
      try {
        if (window.contextTypeChoices && typeof window.contextTypeChoices.setChoiceByValue === 'function' && this.localFilter.context) {
          window.contextTypeChoices.setChoiceByValue(this.localFilter.context);
        }
      } catch (e) {
        // Choices instance may not be initialized yet (e.g. destroyed or not ready)
      }
      try {
        if (window.categoryByContextTypeChoices && typeof window.categoryByContextTypeChoices.setChoiceByValue === 'function' && this.localFilter.category) {
          window.categoryByContextTypeChoices.setChoiceByValue(this.localFilter.category);
        }
      } catch (e) {
        // Choices instance may not be initialized yet
      }
      const autoCompleteInputEl = document.getElementById('choose-product-name');
      if (autoCompleteInputEl && this.localFilter.model_name) {
        autoCompleteInputEl.value = this.localFilter.model_name;
      }
    },
    handleFilter() {
      if (this.resourceType === 'images') {
        this.filter.searchValue = '';
      }
      this.$emit('filter', this.filter);
    },
    handleResetFilters() {
      this.localFilter = {};
      if (window.contextTypeChoices) {
        window.contextTypeChoices.clearStore();
        window.contextTypeChoices.setChoices(this.contextFilters, 'value', 'label', true);
      }
    
      if (window.categoryByContextTypeChoices) {
        window.categoryByContextTypeChoices.clearStore();
        window.categoryByContextTypeChoices.setChoices(this.categories, 'id', 'name', true);
      }
      const resetProductNameEl = document.getElementById('choose-product-name');
      if (resetProductNameEl) resetProductNameEl.value = '';

      this.$emit('resetFilters', this.localFilter);
    },
    handleContextChange_b(event) {
      this.$emit('onContextChange', event?.detail?.value);
      try {
        if (window.categoryByContextTypeChoices) {
          window.categoryByContextTypeChoices.clearStore();
          window.categoryByContextTypeChoices.setChoices(this.categories, 'id', 'name', true);
          const productNameEl = document.getElementById('choose-product-name');
          if (productNameEl) productNameEl.value = '';
        }
        if (window.categoryByContextTypeChoices) {
          window.categoryByContextTypeChoices.enable();
        }
      } catch (error) {
        console.error('Error in enable categoryByContextTypeChoices:', error);
      }
    },
    handleContextChange(event) {
      this.$emit('onContextChange', event?.detail?.value);
      
      try {
        // 1. Determine the dynamic placeholder text
        const placeholder = this.localFilter.context === 'showrooms' ? 'Select Showroom' : 'Select Category';
    
        // 2. If an instance already exists, destroy it completely to prevent memory leaks/bugs
        if (window.categoryByContextTypeChoices) {
          window.categoryByContextTypeChoices.clearStore(); // must be use for clear previous category
          window.categoryByContextTypeChoices.destroy();
        }
    
        // 3. Clear the product name input if it exists
        const productNameEl = document.getElementById('choose-product-name');
        if (productNameEl) productNameEl.value = '';
    
        // 4. Find the target element and initialize Choices with the new placeholder
        const categoryInputEl = this.$el.querySelector('#choose-product-category');
        if (categoryInputEl) {
          window.categoryByContextTypeChoices = new Choices(categoryInputEl, {
            allowHTML: true,
            placeholder: true,
            placeholderValue: placeholder, // Dynamically applied here
            choices: []
          });
          
          // Load your categories into the new instance
          window.categoryByContextTypeChoices.setChoices(this.categories, 'id', 'name', true);
        }
    
        // 5. Ensure it's enabled
        if (window.categoryByContextTypeChoices) {
          window.categoryByContextTypeChoices.enable();
        }
      } catch (error) {
        console.error('Error in handleContextChange:', error);
      }
    },
    handleCategoryChange(event) {
      // this.$emit('onCategoryChange', event?.detail?.value);
      this.localFilter.category = event?.detail?.value;
    },
    handleAutocomplete(event) {
      this.localFilter.model_id = '';
      this.localFilter.model_name = '';
      // console.log('this is context', this.localFilter.context);
      if (!this._debouncedEmitAutocomplete) {
        this._debouncedEmitAutocomplete = (function(fn, delay) {
          let timeout;
          return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
          };
        })(value => {
          this.$emit('onAutocomplete', value);
        }, 300);
      }
      // setTimeout(() => {
        this.filter.searchValue = event.target.value;
        this.localFilter.searchValue = event.target.value;
        this._debouncedEmitAutocomplete(this.filter);
      // }, 100);
    },
    handleAutocompleteClick(event) {
      event.preventDefault();
      event.stopPropagation();
      const li = event.target;
      const id = li.getAttribute('data-id');
      const name = li.textContent;
      this.localFilter.model_id = id;
      this.localFilter.model_name = name;
      this.localFilter.searchValue = name;
      const dropdownMenu = document.getElementById('dropdown-menu');
      if (dropdownMenu) {
        dropdownMenu.innerHTML = '';
        dropdownMenu.classList.add('d-none');
      }
      const productNameInput = document.getElementById('choose-product-name');
      if (productNameInput) productNameInput.value = name;
    },
    handleClearAutocomplete() {
      this.localFilter.model_id = '';
      this.localFilter.model_name = '';
      this.localFilter.searchValue = '';
      this.handleFilter();
    }
  },

  mounted() {
   
    // Sync Choices.js with localFilter after initialization
    this.$nextTick(() => {
      if(window.contextTypeChoices){
        window.contextTypeChoices.destroy();
      }
      const inputEl = this.$el.querySelector('#choose-context-type');
      const categoryInputEl = this.$el.querySelector('#choose-product-category');

      setTimeout(() => {
        if (inputEl) {
          window.contextTypeChoices = new Choices(inputEl, {
              allowHTML: true,
              choices: this.contextFilters,
              shouldSort: false,
              placeholderValue: 'All Context',
              placeholder: true,
          });
        }
        if (categoryInputEl) {
          const placeholder = this.localFilter.context === 'showrooms' ? 'Select Showroom' : 'Select Category';
          // console.log('context ', this.localFilter.context);
          // console.log('place holder ', placeholder);
          window.categoryByContextTypeChoices = new Choices(categoryInputEl, {
            allowHTML: true,
            placeholder: true,
            placeholderValue: placeholder,
            choices: [
               
            ]
          });
          window.categoryByContextTypeChoices.setChoices(this.categories, 'id', 'name', true);
        }
        this.choicesReady = true;
        this.syncChoicesWithFilter();
      }, 200);
      this.localFilter = {...(this.appliedFilters || {})};
      // console.log('this is localFilter', this.localFilter);
    });
  },
  watch: {
    appliedFilters: {
      handler(filters) {
        // console.log('this is appliedFilters', filters);
       this.localFilter = {...(filters || {})};
       this.syncChoicesWithFilter();
      //  console.log('this is localFilter', this.localFilter);
      },
      deep: true,
      immediate: true
    },
    categories: {
      handler(newVal) {
        if(window.categoryByContextTypeChoices){
          window.categoryByContextTypeChoices.setChoices(newVal, 'id', 'name', true);
        }
      },
      deep: true,
      immediate: true
    }
  },

  template: /* html */ `
    <div class="th-sidebar-container pr-md-40" id="th-resource-sidebar-sticky">
      <!-- show error message if any -->
      <div class="border-danger font-size-20" id="filter-images-error" aria-live="polite">
      </div>

      <form class="th-from-margin th-sidebar-form" id="filter-resource-form">

        <!-- Context Type Select -->
        <div class="th-input-group pl-0 th-border-top" v-show="isContextVisible">
          <select class="form-control th-choices-select z-index-10" 
                  id="choose-context-type" 
                  @change="handleContextChange" 
                  v-model="localFilter.context"
                  :disabled="disableContextSelection"
                  >
          </select>
        </div>

        <!-- Product Category Select -->
        <div class="th-input-group pl-0" v-show="isCategoryVisible"
            id="choose-product-category-container">
            <select class="form-control th-choices-select z-index-10"
                name="product_category" id="choose-product-category"
                placeholder="Product Category" 
                :disabled="!isContextSelected"
                @change="handleCategoryChange"
                :choices="categories"
                v-model="localFilter.category">
            </select>
        </div>

        <!-- Autocomplete -->
        <div class="autocomplete" v-show="isAutocompleteVisible">
          <input type="text" 
                 class="form-control th-choices-select z-index-10 font-size-16" 
                 id="choose-product-name" 
                 :placeholder="autoCompletePlaceholderText" 
                 autocomplete="off" 
                 :disabled="disableAutocomplete"
                 @keyup="handleAutocomplete" 
                 v-model="localFilter.searchValue" style="padding:11px;"/>
          <i class="fa fa-close hover" @click="handleClearAutocomplete" v-show="localFilter.searchValue"
                style="position: absolute; right: 10px;top: 22px;"></i>
          <ul class="dropdown-menu show d-none" id="dropdown-menu" @click="handleAutocompleteClick">
          </ul>
        </div>

        <button v-show="isContextVisible" type="button" id="filter-resource-button" @click.prevent="handleFilter"
                class="th-btn-primary text-capitalize w-100 mt-50 font-size-16">
          <span class="mr-5">Filter</span>
          <i class="fa-regular fa-filter"></i>
        </button>

        <button v-show="isResetVisible" type="button" id="reset-resource-button" @click.prevent="handleResetFilters"
                class="th-btn-outline-black text-capitalize w-100 mt-5 font-size-16">
          <span class="mr-5">Reset</span>
          <i class="fa-regular fa-eraser"></i>
        </button>

      </form>
    </div>
  `,
};

