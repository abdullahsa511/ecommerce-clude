const searchbarStore = new Vuex.Store({
    state: {
        searchValues: [],
        loading: false,
        error: null,
        loadedSearchbar: false,
        searchResults: [],
        popularSearch: [
            {
                popular_search_id: 1,
                search_key: 'JJ',
                search_value: 'JJ',
            },
            {
                popular_search_id: 2,
                search_key: 'Ace',
                search_value: 'Ace',
            },
            {
                popular_search_id: 3,
                search_key: 'Anders',
                search_value: 'Anders',
            },
            {
                popular_search_id: 4,
                search_key: 'Casali',
                search_value: 'Casali',
            },
            {
                popular_search_id: 5,
                search_key: 'Vada Wall Panelling',
                search_value: 'Vada Wall Panelling',
            }
        ],
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_SEARCH_VALUES(state, searchValues) {
            state.searchValues = searchValues ? searchValues : '';
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_SEARCH_RESULTS(state, searchResults) {
            state.searchResults = searchResults ? searchResults : [];
        },
        SET_POPULAR_SEARCH(state) {
            state.popularSearch = state.popularSearch;
        },
    },
    
    actions: {
        async loadSearchbar({ commit, state }, searchValues) {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
        
            try {
                const searchResults = state.searchResults.filter(result => {
                    // Make sure title and description exist before calling toLowerCase
                    const title = result.title ? result.title : '';
                    const description = result.description ? result.description : '';
                    return title.includes(searchValues) || description.includes(searchValues);
                });
         
                commit('SET_SEARCH_VALUES', state.searchResults);
                // console.log('loadSearchbar state.searchResults', state.searchResults);
                commit('SET_LOADING', false);
            } catch (error) {
                console.error('Error loading searchbar:', error);
                commit('SET_ERROR', error.message || 'Failed to load searchbar');
                commit('SET_LOADING', false);
                throw error;
            }
        },        

        search({ commit, state }, searchValues) {
            commit('SET_SEARCH_VALUES', searchValues||'');
        },

            // api calling for get the product configuration
        getSearchResults({ commit, state }, searchValues) {
            (async () => {
                try {
                    // console.log('searchValues', searchValues);
                    if (searchValues) {
                        const svcModule = await import('../services/searchbarService.js');
                        const response = await svcModule.default.getSearchResults(searchValues);
                        commit('SET_SEARCH_VALUES', response.results);
                        // commit('SET_POPULAR_SEARCH', response.popular_search);
                        // commit('SET_SEARCH_RESULTS', response.results);
                    }else{
                        commit('SET_SEARCH_VALUES', []);
                    }
                } catch (error) {
                    console.error('Error getting search results:', error);
                }
            })();

        },
        clearSearchResults({ commit }) {
            commit('SET_SEARCH_VALUES', []);
        },

        async tagSearch({ commit, state }) {
            try {
                // console.log('tagSearch function');
                // const svcModule = await import('../services/searchbarService.js');
                // const response = await svcModule.default.tagSearch();
                // console.log('response tagSearch =', response);
                // commit('SET_POPULAR_SEARCH');
            } catch (error) {
                console.error('Error getting tag search results:', error);
            }
        }
       
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        searchValues: state => state.searchValues,
        searchResults: state => state.searchResults,
        popularSearch: state => state.popularSearch,
    }
});

export default searchbarStore;
