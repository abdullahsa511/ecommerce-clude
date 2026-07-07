const categoryStore = new Vuex.Store({
    state: {
        // PRODUCT DESK STARTED HERE
        products: [],
        total: null,
        pagination: {
            search_query: null,
            contexts: [],
            category_slug: null,
            current_page: 1,
            per_page: 40,
            offset: 0,
            loaded_data: 0,
            total: 0,
            last_page: 1,
            has_more: false,
            material_id: null,
            material_name: null,
            feature_id: null,
            feature_name: null,
            weight_id: null,
            certificate_id: null,
        },
        load_more: false,
        materials: [],
        features: [],
        certifications: [],
        selectedFilters: {},
        filterLoading: false,
        resetFilterLoading: false,
        // PRODUCT DESK ENDED HERE
    },
    
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_MODEL_DATA(state, modelData) {
            state.modelData = modelData ? modelData : [];
        },
        SET_FILTER_LOADING(state, filterLoading) {
            state.filterLoading = filterLoading;
        },
        SET_RESET_FILTER_LOADING(state, resetFilterLoading) {
            state.resetFilterLoading = resetFilterLoading;
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_PRODUCTS(state, products) {
            state.products = products ? products : [];
        },
        // PRODUCT DESK STARTED HERE
        async SET_INITITAL_PAGINATION(state, offset = 0) {
            let pagination = await this.dispatch('getCurrentUrlObject');
            this.commit('UPDATE_PAGINATION', { ...pagination });
        },
        UPDATE_PAGINATION(state, partial) {
            state.pagination = { ...state.pagination, ...partial };
            this.dispatch('updateUrlPagination', state.pagination);
        },
        MERGE_SEARCH_PAGINATION(state, apiPagination) {
            if (!apiPagination) {
                return;
            }
            state.pagination = {
                ...state.pagination,
                per_page: apiPagination.per_page ?? state.pagination.per_page,
                current_page: apiPagination.current_page ?? state.pagination.current_page,
                offset: apiPagination.offset ?? state.pagination.offset,
                loaded_data: apiPagination.loaded_data ?? state.pagination.loaded_data,
                total: apiPagination.total ?? state.pagination.total,
                last_page: apiPagination.last_page ?? state.pagination.last_page,
                has_more: Boolean(apiPagination.has_more),
            };
        },
        INCREMENT_PAGE(state) {
            state.pagination.current_page = state.pagination.current_page* 1 + 1;
            state.pagination.offset = (state.pagination.per_page * state.pagination.current_page) - state.pagination.per_page;
            this.dispatch('updateUrlPagination', state.pagination);
        },
        SET_LOAD_MORE(state, load_more) {
            state.load_more = load_more ? true : false;
        },
        SET_MATERIALS(state, materials) {
            state.materials = materials ? materials : [];
        },
        SET_FEATURES(state, features) {
            state.features = features ? features : [];
        },
        SET_CERTIFICATIONS(state, certifications) {
            state.certifications = certifications ? certifications : [];
        },
        // PRODUCT DESK ENDED HERE
    },
    
    actions: {
        // PRODUCT DESK STARTED HERE
        /**
         * Awaitable bootstrap for search results page: read URL into state, then for page>1 force offset=0
         * so the API returns a cumulative window (per_page * current_page items), e.g. page=2 → 80 rows.
         */
        async bootstrapSearchPaginationFromUrl({ dispatch, state }) {
            await dispatch('getCurrentUrlObject');
            if (Number(state.pagination.current_page) > 1) {
                state.pagination.offset = 0;
            }
            await dispatch('updateUrlPagination', { ...state.pagination });
        },
        async loadCategoryProductListing({ commit, state, dispatch }, { load_more = false } = {}) {
            // Reload / hydration: URLs with page>1 omit offset ⇒ backend loads pages 1..N cumulatively (offset starts at query offset 0).
            // Normalize offset so load-more bookmarks (?current_page=2&offset=40) upgrade to cumulative refresh (?offset=0).
            if (!load_more && Number(state.pagination.current_page) > 1) {
                state.pagination.offset = 0;
                await dispatch('updateUrlPagination', { ...state.pagination });
            }

            if (load_more) commit('SET_LOADING', true);
            try {
                const svcModule = await import('../services/productConfiguratorService.js');
                const payload = await svcModule.default.getSearchResults(state.pagination);
                const incomingItems = Array.isArray(payload?.results) ? payload.results : [];

                commit('MERGE_SEARCH_PAGINATION', payload.pagination);
                commit('SET_LOAD_MORE', payload.pagination?.has_more ?? false);

                if (load_more) {
                    const existingItems = Array.isArray(state.products) ? state.products : (state.products?.items || []);
                    const mergedItems = [...existingItems, ...incomingItems];

                    if (Array.isArray(state.products)) {
                        commit('SET_PRODUCTS', mergedItems);
                    } else {
                        commit('SET_PRODUCTS', {
                            ...(typeof state.products === 'object' ? state.products : {}),
                            items: mergedItems,
                            product_count: payload.pagination?.total ?? state.products?.product_count ?? mergedItems.length,
                        });
                    }
                    commit('SET_LOADING', false);
                } else {
                    commit('SET_PRODUCTS', incomingItems);
                }
            } catch (error) {
                console.error('Error getting product desk modesty panel data:', error);
            }
        },
        getCurrentUrlObject({ state } = {}) {
            const url = new URL(window.location);
            const pagination = {};
        
            // Extract category slug from URL of the form /products/[category-slug]
            let categorySlug = '';
            const pathParts = url.pathname.split('/').filter(Boolean);
            const productsIdx = pathParts.indexOf('products');
            if (productsIdx !== -1 && pathParts.length > productsIdx + 1) {
                categorySlug = pathParts[productsIdx + 1];
            }
        
            // Read raw params (page aliases current_page — page wins when both present)
            const pageAlias = url.searchParams.get('page');
            const currentPageParam = url.searchParams.get('current_page');
            const perPageParam = url.searchParams.get('per_page');
            const offsetParam = url.searchParams.get('offset');
        
            // Coerce to typed values with sensible defaults
            pagination.category_slug = categorySlug;
            if (pageAlias !== null && pageAlias !== '') {
                pagination.current_page = Number(pageAlias);
            } else if (currentPageParam !== null && currentPageParam !== '') {
                pagination.current_page = Number(currentPageParam);
            } else {
                pagination.current_page = 1;
            }
            pagination.per_page = perPageParam !== null ? Number(perPageParam) : 40;
            pagination.offset = offsetParam !== null ? Number(offsetParam) : 0;
            pagination.search_query = url.searchParams.get('query') ?? null;
        
            // Fix: parse contexts into array
            const contextsRaw = url.searchParams.get('contexts');
            pagination.contexts = contextsRaw ? contextsRaw.split(',') : [];
        
            // Update store state.pagination if state is available
            if (state && state.pagination) {
                state.pagination = { ...state.pagination, ...pagination };
            }
        
            return pagination;
        },
        updateUrlPagination({ commit, state }, pagination = {}) {
            const baseUrl = window.location.origin + window.location.pathname;
        
            const params = [];
        
            if (pagination.per_page) {
                params.push(`per_page=${pagination.per_page}`);
            }
        
            if (pagination.current_page) {
                params.push(`current_page=${pagination.current_page}`);
                params.push(`page=${pagination.current_page}`);
            }
        
            if (pagination.offset !== undefined && pagination.offset !== null) {
                params.push(`offset=${pagination.offset}`);
            }
        
            if (pagination.search_query) {
                params.push(`query=${encodeURIComponent(pagination.search_query)}`);
            }
        
            if (pagination.contexts && Array.isArray(pagination.contexts) && pagination.contexts.length) {
                // 🔥 No encoding for comma
                params.push(`contexts=${pagination.contexts.join(',')}`);
            }
        
            const finalUrl = params.length ? `${baseUrl}?${params.join('&')}` : baseUrl;
        
            window.history.replaceState({}, '', finalUrl);
        },
        async inputAutocomplete({ commit, state }, filter) {
            // console.log('inputAutocomplete store', filter);
            const svcModule = await import('../services/productConfiguratorService.js');
            // console.log('state.pagination store', state.pagination);
            const response = await svcModule.default.inputAutocomplete(filter);
            // console.log('response store', response);
            if(filter.resource_type === 'finishes') {
                commit('SET_MATERIALS', response);
            } else if(filter.resource_type === 'variants') {
                commit('SET_FEATURES', response);
            } else if(filter.resource_type === 'documents') {
                commit('SET_CERTIFICATIONS', response);
            }
        },
        async productFilter({ commit, dispatch, state }, filter) {
            if(filter.reset) {
                commit('SET_FILTER_LOADING', false);
                commit('SET_RESET_FILTER_LOADING', true);
            }else{
                commit('SET_FILTER_LOADING', true);
                commit('SET_RESET_FILTER_LOADING', false);
            }

            commit('UPDATE_PAGINATION', {
                ...filter,
                current_page: filter.current_page ?? 1,
                offset: filter.offset ?? 0,
            });
            await dispatch('getCurrentUrlObject');
            const svcModule = await import('../services/productConfiguratorService.js');
            const payload = await svcModule.default.productSearchFilter(state.pagination);
            const items = Array.isArray(payload?.results) ? payload.results : [];
            commit('SET_PRODUCTS', items);
            commit('MERGE_SEARCH_PAGINATION', payload.pagination);
            commit('SET_LOAD_MORE', payload.pagination?.has_more ?? false);
            commit('SET_FILTER_LOADING', false);
            commit('SET_RESET_FILTER_LOADING', false);
        },
        // PRODUCT DESK ENDED HERE
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        modelData: state => state.modelData,
        products: state => state.products,
        loadMore: state => state.load_more,
        materials: state => state.materials,
        features: state => state.features,
        certifications: state => state.certifications,
        searchPagination: state => state.pagination,
        selectedFilters: state => state.pagination,
        filterLoading: state => state.filterLoading,
        resetFilterLoading: state => state.resetFilterLoading,
        // PRODUCT DESK ENDED HERE
    }
});

export default categoryStore;
