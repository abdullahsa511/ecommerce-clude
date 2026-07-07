const categoryStore = new Vuex.Store({
    state: {
        // PRODUCT DESK STARTED HERE
        products: [],
        total: null,
        pagination: {
            category_slug: null,
            current_page: 1,
            per_page: 40,
            offset: 0,
            total: 0,
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
        UPDATE_PAGINATION(state, pagination) {
            this.dispatch('updateUrlPagination', pagination);
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
        async loadCategoryProductListing({ commit, state }, {load_more = false} = {}) {
            // alert('loadProductDeskModesty store');
            // call api to get the product desk modesty panel data
            if(load_more) commit('SET_LOADING', true);
            try {
                const svcModule = await import('../services/productConfiguratorService.js');
                // console.log('state.pagination store', state.pagination);
                const response = await svcModule.default.getProducts(state.pagination);
                // console.log('response store', response);
                commit('SET_LOAD_MORE', response?.load_more);

                // Response shape may be an array or an object like { items: [...], product_count: N }.
                // Ensure we merge correctly when loading more.
                if (load_more) {
                    const existingItems = Array.isArray(state.products) ? state.products : (state.products?.items || []);
                    const incomingItems = Array.isArray(response) ? response : (response?.items || []);

                    const mergedItems = [...existingItems, ...incomingItems];

                    // Preserve metadata (e.g., product_count) if response provides it.
                    if (Array.isArray(state.products)) {
                        // previous state was an array, keep array shape for backward compatibility
                        commit('SET_PRODUCTS', mergedItems);
                    } else {
                        commit('SET_PRODUCTS', {
                            ...(typeof state.products === 'object' ? state.products : {}),
                            items: mergedItems,
                            product_count: response?.product_count ?? state.products?.product_count ?? mergedItems.length
                        });
                    }
                    commit('SET_LOADING', false);
                } else {
                    commit('SET_PRODUCTS', response);
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
            // console.log('categorySlug', categorySlug);
            // read raw params
            const currentPageParam = url.searchParams.get('current_page');
            const perPageParam = url.searchParams.get('per_page');
            const offsetParam = url.searchParams.get('offset');
 
            // coerce to typed values with sensible defaults
            pagination.category_slug = categorySlug;
            pagination.current_page = currentPageParam !== null ? Number(currentPageParam) : 1;
            pagination.per_page = perPageParam !== null ? Number(perPageParam) : 40;
            pagination.offset = offsetParam !== null ? Number(offsetParam) : 0;
            pagination.material_id = url.searchParams.get('material_id') ?? null;
            pagination.material_name = url.searchParams.get('material_name') ?? null;
            pagination.feature_id = url.searchParams.get('feature_id') ?? null;
            pagination.feature_name = url.searchParams.get('feature_name') ?? null;
            pagination.weight_id = url.searchParams.get('weight_id') ?? null;
            pagination.certificate_id = url.searchParams.get('certificate_id') ?? null;
            pagination.certificate_name = url.searchParams.get('certificate_name') ?? null;
            // update store state.pagination if state is available
            if (state && state.pagination) {
                state.pagination = { ...state.pagination, ...pagination };
            }

            return pagination;
        },
        updateUrlPagination({ commit, state }, pagination = {}) {
            // console.log('updateUrlPagination store', pagination);
            const url = new URL(window.location);
            url.searchParams.set('per_page', pagination.per_page);
            url.searchParams.set('current_page', pagination.current_page);
            url.searchParams.set('offset', pagination.offset);

            if(pagination.material_id) url.searchParams.set('material_id', pagination.material_id);
            if(pagination.material_name) url.searchParams.set('material_name', pagination.material_name);
            if(pagination.feature_id) url.searchParams.set('feature_id', pagination.feature_id);
            if(pagination.feature_name) url.searchParams.set('feature_name', pagination.feature_name);
            if(pagination.weight_id) url.searchParams.set('weight_id', pagination.weight_id);
            if(pagination.certificate_id) url.searchParams.set('certificate_id', pagination.certificate_id);
            if(pagination.certificate_name) url.searchParams.set('certificate_name', pagination.certificate_name);
            // if(pagination.total) url.searchParams.set('total', pagination.total);
            pagination.material_id?url.searchParams.set('material_id', pagination.material_id):url.searchParams.delete('material_id');  
            pagination.material_name?url.searchParams.set('material_name', pagination.material_name):url.searchParams.delete('material_name');
            pagination.feature_id?url.searchParams.set('feature_id', pagination.feature_id):url.searchParams.delete('feature_id');  
            pagination.feature_name?url.searchParams.set('feature_name', pagination.feature_name):url.searchParams.delete('feature_name');
            pagination.weight_id?url.searchParams.set('weight_id', pagination.weight_id):url.searchParams.delete('weight_id');  
            pagination.certificate_id?url.searchParams.set('certificate_id', pagination.certificate_id):url.searchParams.delete('certificate_id');  
            pagination.certificate_name?url.searchParams.set('certificate_name', pagination.certificate_name):url.searchParams.delete('certificate_name');

            window.history.replaceState({}, '', url.toString());
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
            commit('SET_FILTER_LOADING', true);
            commit('UPDATE_PAGINATION', filter);
            await dispatch('getCurrentUrlObject');
            // update the state.pagination with the filter
            // filter.category_slug = state.pagination.category_slug;
            // console.log('state.pagination store', state.pagination);
            const svcModule = await import('../services/productConfiguratorService.js');
            const response = await svcModule.default.productFilter(state.pagination);
            commit('SET_PRODUCTS', response);
            commit('SET_LOAD_MORE', response?.load_more);
            commit('SET_FILTER_LOADING', false);
        },
        // PRODUCT DESK ENDED HERE
    },
    
    getters: {
        loading: state => state.loading,
        error: state => state.error,
        modelData: state => state.modelData,
        
        // PRODUCT DESK STARTED HERE
        products: state => state.products,
        loadMore: state => state.load_more,
        materials: state => state.materials,
        features: state => state.features,
        certifications: state => state.certifications,
        selectedFilters: state => state.pagination,
        filterLoading: state => state.filterLoading,
        // PRODUCT DESK ENDED HERE
    }
});

export default categoryStore;
