// import debounce from 'lodash/debounce';
const debounce = (fn, wait) => {
    let timeout = null;
    return function(...args) {
        const context = this;
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            timeout = null;
            fn.apply(context, args);
        }, wait);
    };
};

export default {
    name: 'Searchbar',
    props: {
        variant: {
            type: String,
            default: 'desktop',
            validator: (value) => ['desktop', 'mobile'].includes(value),
        },
    },
    data() {
        return {
            searchTerm: '',
            showSuggestions: false,
        };
    },
    computed: {
        popularSearch() {
            return this.$store ? this.$store.getters.popularSearch : [];
        },
        searchResults() {
            return this.$store ? this.$store.getters.searchResults : [];
        },
        searchValues() {
            return this.$store ? this.$store.getters.searchValues : [];
        },
        suggestions() {
            const results = this.searchResults;

            if (Array.isArray(results)) {
                return results;
            }

            return [];
        },
        isMobile() {
            return this.variant === 'mobile';
        },
    },
    created() {
        this.debouncedHandleInput = debounce(() => {
            this.handleInput();
        }, 300);
    },
    methods: {
        focusInput() {
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        },
        handleInput() {
            const value = this.searchTerm.trim();
            this.$store.dispatch('getSearchResults', value);
            this.showSuggestions = true;
        },
        clearInput() {
            this.$store.dispatch('clearSearchResults');
            this.searchTerm = '';
            this.showSuggestions = false;
            this.focusInput();
        },
        onInput() {
            if (typeof this.debouncedHandleInput === 'function') {
                this.debouncedHandleInput();
            } else {
                this.handleInput();
            }
        },
        handleSearch() {
            if (typeof this.debouncedHandleInput === 'function') {
                this.debouncedHandleInput();
            } else {
                this.handleInput();
            }
        },
        selectSuggestion(item) {
            this.searchTerm = item.title;
            this.$store.dispatch('loadSearchbar', this.searchTerm);
            this.showSuggestions = false;
        },
        getBeforeDash(text) {
            if (!text) return '';
            const parts = text.split('-');
            return parts[0] ? parts[0].trim() : '';
        },
        getResultActionLabel(searchValue) {
            const type = this.getBeforeDash(searchValue.model_type).toLowerCase();
            if (type === 'product') return 'Configure Product';
            if (type === 'project') return 'Explore Project';
            return 'View Details';
        },
        getResultActionClass(searchValue) {
            const type = this.getBeforeDash(searchValue.model_type).toLowerCase();
            return type === 'project' ? 'text-white' : '';
        },
        viewAllResults() {
            window.open('/search/results?query=' + this.searchTerm, '_blank');
        },
        searchPopular(searchKey) {
            this.searchTerm = searchKey;
            this.$store.dispatch('getSearchResults', searchKey);
            this.showSuggestions = true;
            this.focusInput();
        }
    },
    watch: {
        searchTerm(value) {
            if (!value.trim()) {
                this.showSuggestions = false;
            }
        },
        suggestions(newVal) {
            if (this.searchTerm.trim()) {
                this.showSuggestions = newVal.length > 0;
            }
        }
    },
    template: /* html */ `
    <div>
        <!-- Desktop -->
        <div v-if="!isMobile" class="th-search-result-container">
            <form action="">
                <div class="autocomplete">
                    <div class="autocomplete-input-wrapper">
                        <input
                            ref="searchInput"
                            type="text"
                            class="form-control th-choices-select"
                            id="search-product-name"
                            placeholder="Search..."
                            autocomplete="off"
                            aria-label="Search..."
                            v-model="searchTerm"
                            @input="onInput"
                            @keydown.enter.prevent="viewAllResults"
                        />
                        <span class="icon search"><i class="fa-solid fa-search" @click="handleSearch"></i></span>
                        <span class="icon clear" id="clearBtn" @click="clearInput">✕</span>
                    </div>
                </div>
            </form>
            
            <div class="th-search-result-content">
                <div class="th-item-product pt-20" v-if="popularSearch && popularSearch.length > 0">
                    <div class="th-item-footer">
                        <h5 class="th-title-18">Popular search terms</h5>
                        <div class="th-tag-name">
                            <div 
                                class="th-tag th-cursor-pointer th-tag-p th-mouse-hover"
                                v-for="item in popularSearch"
                                :key="item.popular_search_id"
                                @click="searchPopular(item.search_key)"
                            >
                                {{ item.search_key }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="searchValues.length > 0">
                    <div class="th-search-result-count">
                        <span>{{searchValues.length}}</span> Items Found
                    </div>

                    <div class="d-flex gap-4 th-search-result-grid">
                        <div class="th-search-result-item" v-for="searchValue in searchValues" :key="searchValue.id">
                            <a :href="searchValue.href" target="_blank">
                                <div class="th-search-result-item-image">
                                    <img :src="searchValue.dataSrc" :alt="searchValue.title">
                                </div>
                                <div class="th-item-details pl-0">
                                    <h3 class="th-item-title">
                                        {{ searchValue.title }} 
                                    </h3>
                                    <p class="th-item-subTitle">{{ getBeforeDash(searchValue.model_type) }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="th-view-all-results">
                        <button class="th-btn-secondary" @click.prevent="viewAllResults">View All</button>
                    </div>
                </div>
                <div v-else style="display: flex; align-items: center; justify-content: center; height: 200px;">
                    <div class="th-search-result-count" style="text-align: center;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile -->
        <div v-else class="th-search-result-container p-0">
            <div class="autocomplete">
                <div class="autocomplete-input-wrapper">
                    <input
                        ref="searchInput"
                        type="text"
                        class="form-control th-choices-select"
                        id="search-product-name-mobile"
                        placeholder="Search..."
                        aria-label="Search..."
                        autocomplete="off"
                        v-model="searchTerm"
                        @input="onInput"
                        @keydown.enter.prevent="viewAllResults"
                    />
                    <span class="icon search"><i class="fa-solid fa-search" @click="handleSearch"></i></span>
                    <span class="icon clear" @click="clearInput">✕</span>
                </div>
            </div>

            <div class="th-search-result-content">
                <div class="th-item-product pt-20" v-if="popularSearch && popularSearch.length > 0">
                    <div class="th-item-footer">
                        <h5 class="th-title-18">Popular search terms</h5>
                        <div class="th-tag-name">
                            <div 
                                class="th-tag th-cursor-pointer th-tag-p th-mouse-hover"
                                v-for="item in popularSearch"
                                :key="item.popular_search_id"
                                @click="searchPopular(item.search_key)"
                            >
                                {{ item.search_key }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="searchValues.length > 0">
                    <div class="th-search-result-count mb-30">
                        <span>{{ searchValues.length }}</span> Products Found
                    </div>

                    <div class="d-flex th-search-result-grid">
                        <div class="th-search-result-item" v-for="searchValue in searchValues" :key="searchValue.id">
                            <a :href="searchValue.href" target="_blank">
                                <div class="th-search-result-item-image">
                                    <img :src="searchValue.dataSrc" :alt="searchValue.title">
                                </div>
                                <div class="th-item-details">
                                    <h3 class="th-item-title">
                                        {{ searchValue.title }}
                                    </h3>
                                    <p  class="th-item-subTitle">{{ getBeforeDash(searchValue.model_type) }}</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="th-view-all-results">
                        <button class="th-btn-secondary" @click.prevent="viewAllResults">View All</button>
                    </div>
                </div>
                <div v-else style="display: flex; align-items: center; justify-content: center; height: 200px;">
                    <div class="th-search-result-count" style="text-align: center;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
}
