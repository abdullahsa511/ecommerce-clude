const resourceImageStore = new Vuex.Store({
    state: {
        loadedPosts: [],
        current_page: 1,
        per_page: 5,
        total: null,
        loading: false,
        error: null,
        hasMore: true
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_LOADED_POSTS(state, posts) {
            // Append posts to existing list
            state.loadedPosts.push(...posts);
        },
        RESET_LOADED_POSTS(state) {
            state.loadedPosts = [];
        },
        INCREMENT_PAGE(state) {
            state.current_page += 1;
        },
        SET_PAGINATION(state, pagination) {
            state.per_page = pagination.per_page;
            state.total = pagination.total;
            
            // Update hidden fields
            const totalEl = document.getElementById('total-images-count');
            const currentPageEl = document.getElementById('current-page');
            const perPageEl = document.getElementById('per-page');
            
            if (totalEl) totalEl.innerHTML = state.total;
            if (currentPageEl) currentPageEl.innerHTML = state.current_page;
            if (perPageEl) perPageEl.innerHTML = state.per_page;
            
            // Hide load more button if no more posts
            const loadedCount = state.current_page * state.per_page;
            if (loadedCount >= state.total) {
                const loadMoreBtn = document.getElementById('load_more_button');
                if (loadMoreBtn) {
                    loadMoreBtn.style.display = "none";
                }
            }
        }
    },
    
    actions: {
        async loadPosts({ commit, state }) {
            // Increment page before loading
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            
            try {
                // Dynamic import service
                const svc = await import('../services/resourceImageListService.js');
                const response = await svc.default.loadMoreResourceImages(state.per_page, state.current_page);
                
                // Commit loaded posts (will append to existing)
                commit('SET_LOADED_POSTS', response.list);
                commit('INCREMENT_PAGE');
                commit('SET_PAGINATION', {
                    per_page: state.per_page, 
                    total: response.total
                });
                commit('SET_LOADING', false);
                
                return response;
            } catch (error) {
                console.error('Error loading posts:', error);
                commit('SET_ERROR', error.message || 'Failed to load posts');
                commit('SET_LOADING', false);
                throw error;
            }
        }
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        per_page: state => state.per_page,
        current_page: state => state.current_page
    }
});

export default resourceImageStore;
