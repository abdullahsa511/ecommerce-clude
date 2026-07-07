import { debounce } from 'lodash';
export default {
    name: 'Searchbar',
    props: {},
    data() {
        return {
            searchTerm: '',
            showSuggestions: false,
        };
    },
    computed: {
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

            // if (results && Array.isArray(results.results)) {
            //     return results.results;
            // }

            return [];
        }
    },
    methods: {
        handleInput() {
            const value = this.searchTerm.trim();
        
            if (!value) {
                this.showSuggestions = false;
                return;
            }
        
            this.$store.dispatch('getSearchResults', value);
            this.showSuggestions = true;
        },
        selectSuggestion(item) {
            this.searchTerm = item.title;
            this.$store.dispatch('loadSearchbar', this.searchTerm);
            this.showSuggestions = false;
        },
        clearInput() {
            this.searchTerm = '';
            this.showSuggestions = false;
        },
        searchWayPointLabel(event) {
            const query = event.data?.toLowerCase();
            emit('search:way-points-suggestions', query);
            // console.log('query', query);
            // // static data for suggestions with filter by query
            // wayPointLabelSuggestions.value = dummyData.value.filter(item => item.label.toLowerCase().includes(query));
            // console.log('wayPointLabelSuggestions', wayPointLabelSuggestions.value);
        }

        // const onSearchInput = debounce(searchWayPointLabel, 300)

        
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
        },
        searchValues(newVal) {
            
        }
    },


    template: /* html */ `
    <div>
        
        <div class="th-search-result-container">
            <form action="">
                <div class="autocomplete">
                    <div class="autocomplete-input-wrapper">
                        <input
                            type="text"
                            class="form-control th-choices-select"
                            id="search-product-name"
                            placeholder="Search..."
                            autocomplete="off"
                            v-model="searchTerm"
                            @input="handleInput"
                        />
                        <span class="icon search"><i class="fa-solid fa-search"></i></span>
                        <span class="icon clear" id="clearBtn" @click="clearInput">✕</span>
                    </div>
                
                    <ul
                        class="autocomplete-list"
                        id="autocompleteList"
                        v-show="showSuggestions"
                    >
                        <li
                            v-for="(item, index) in suggestions"
                            :key="index"
                            @click="selectSuggestion(item)"
                        >
                            {{ item.title }}
                        </li>
                    </ul>
                </div>
            </form>
            
            <div class="th-search-result-content" v-if="searchValues.length > 0">
                <div class="th-search-result-count">
                    <span>{{searchValues.length}}</span> Products Found
                </div>

                <div class="d-flex gap-4 th-search-result-grid">
                    <div class="th-search-result-item" v-for="searchValue in searchValues" :key="searchValue.id">
                        <div class="th-search-result-item-image">
                            <img :src="searchValue.dataSrc" :alt="searchValue.title">
                            <h5>
                                {{ searchValues.title }}
                                <i class="fa-regular fa-arrow-right"></i>
                            </h5>
                        </div>
                        <div class="th-item-details">
                            <h3 class="th-item-title">{{ searchValues.title }} - <span class="th-item-subTitle">{{ searchValues.description }}</span></h3>
                            <p>{{ searchValues.context }}</p>
                        </div>
                    </div>


                    
                    <!-- <div class="th-search-result-item">
                        <div class="th-search-result-item-image">
                            <img src="/media/Products/banner/krost-furniture-collection.png" alt="Product Image">
                            <h5 class="text-white">
                                Explore Project
                                <i class="fa-regular fa-arrow-right"></i>
                            </h5>
                        </div>
                        <div class="th-item-details">
                            <h3 class="th-item-title">Krost Features on Indesignlive Collection - <span class="th-item-subTitle">Architects</span></h3>
                            <p>Project</p>
                        </div>
                    </div>
                    <div class="th-search-result-item">
                        <div class="th-search-result-item-image">
                            <img src="/img/category-seating/Archi.png" alt="Product Image">
                            <h5>
                                Configure Product
                                <i class="fa-regular fa-arrow-right"></i>
                            </h5>
                        </div>
                        <div class="th-item-details">
                            <h3 class="th-item-title">Archi - <span class="th-item-subTitle">Next Level Flexibility</span></h3>
                            <p>Product</p>
                        </div>
                    </div>

                    <div class="th-search-result-item">
                        <div class="th-search-result-item-image">
                            <img src="/media/Products/banner/krost-furniture-collection.png" alt="Product Image">
                            <h5 class="text-white">
                                Explore Project
                                <i class="fa-regular fa-arrow-right"></i>
                            </h5>
                        </div>
                        <div class="th-item-details">
                            <h3 class="th-item-title">Krost Features on Indesignlive Collection - <span class="th-item-subTitle">Architects</span></h3>
                            <p>Project</p>
                        </div>
                    </div>
                    <div class="th-search-result-item">
                        <div class="th-search-result-item-image">
                            <img src="/media/Products/banner/krost-furniture-collection.png" alt="Product Image">
                            <h5 class="text-white">
                                Explore Project
                                <i class="fa-regular fa-arrow-right"></i>
                            </h5>
                        </div>
                        <div class="th-item-details">
                            <h3 class="th-item-title">Krost Features on Indesignlive Collection - <span class="th-item-subTitle">Architects</span></h3>
                            <p>Project</p>
                        </div>
                    </div> -->
                </div>

                <div class="th-view-all-results">
                    <button class="th-btn-secondary">View All</button>
                </div>


            </div>

            <div v-else style="display: flex; align-items: center; justify-content: center; height: 200px;">
                <div class="th-search-result-count" style="text-align: center;">
                    <span>No results found</span>
                </div>
            </div>

        </div>
       
    </div>
    `
}
