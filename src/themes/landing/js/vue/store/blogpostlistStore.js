const Vue = window.Vue;
const Vuex = window.Vuex;

if (Vue && Vuex) {
    if (!window.__vuexInstalled) {
        Vue.use(Vuex);
        window.__vuexInstalled = true;
    }
} else {
    console.error('Vue or Vuex is not available. Make sure both CDN scripts are loaded.');
}


const showroomStore = new Vuex.Store({
    state: {
        loadedPosts: [],
        current_page: 1,
        per_page: 30,
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
        SET_PAGINATION_backup(state, pagination) {
            state.per_page = pagination.per_page;
            const totalEl = document.getElementById('total-posts-count');
            const currentPageEl = document.getElementById('current-page');
            const perPageEl = document.getElementById('per-page');

            state.total = parseInt(totalEl.textContent); 

            if (perPageEl) {
                state.current_page = parseInt(currentPageEl.textContent) + 1;
            }
            
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
            this.dispatch('updateUrlPagination', state);
        },
        SET_PAGINATION(state, pagination) {
            state.per_page = pagination.per_page;
          
            const paginationEl = document.getElementById('all-blog-pagination');
          
            if (paginationEl) {
              state.total = parseInt(
                paginationEl.dataset.totalBlogsCount || '0',
                10
              );
          
              state.current_page =
                parseInt(paginationEl.dataset.currentPage || '0', 10) + 1;
          
              paginationEl.dataset.currentPage = state.current_page;
              paginationEl.dataset.perPage = state.per_page;
              paginationEl.dataset.totalBlogsCount = state.total;
            }
          
            // Hide load more if finished
            const loadedCount = state.current_page * state.per_page;
          
            if (loadedCount >= state.total) {
              const loadMoreBtn = document.getElementById('load_more_button');
              if (loadMoreBtn) {
                loadMoreBtn.style.display = 'none';
              }
            }
          
            this.dispatch('updateUrlPagination', state);
          }
    },
    
    actions: {
        updateUrlPagination({ commit, state }) {
            const url = new URL(window.location);
            console.log('state.per_page updateUrlPagination', state.per_page);
            console.log('state.current_page updateUrlPagination', state.current_page);
            url.searchParams.set('per_page', state.per_page);
            url.searchParams.set('current_page', state.current_page);
            window.history.replaceState({}, '', url.toString());
        },
        async loadPosts({ commit, state }) {
            // Increment page before loading
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            commit('INCREMENT_PAGE');
            commit('SET_PAGINATION', {
                per_page: state.per_page, 
                total: 20 // test total
            });
            
            try {
                // Dynamic import service
                const svc = await import('../services/blogPostListService.js');
                console.log('state.per_page loadPosts', state.per_page);
                console.log('state.current_page loadPosts', state.current_page);
                const response = await svc.default.loadMoreBlogPost(state.per_page, state.current_page);
                
                commit('SET_LOADED_POSTS', response.list);
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

export default showroomStore;
