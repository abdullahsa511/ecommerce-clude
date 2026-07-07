import ResourceService from '../services/designResourcesService.js';
const designResourcesStore = new Vuex.Store({
    state: {
        images: [],
        models: [],
        documents: [],
        finishes: [],
        textiles: [],
        resources:{
            images:[],
            models:[],
            documents:[],
            finishes:[],
            textiles:[],
        },
        products: [],
        resourcePage: 'images',
        total: null,
        loading: false,
        error: null,
        hasMore: true,

        pagination: {
            images: {
                resource_type: 'images',
                current_page: 0,
                per_page: 60,
                offset: 0,
                total: 0,
                context: '',
                category: '',
                model_id: '',
                model_name: '',
                searchValue: '',
                context_categories: [],
            },
            models: {
                resource_type: 'models',
                current_page: 0,
                per_page: 200,
                offset: 0,
                total: 0,
                context: 'product',
                category: '',
                model_id: '',
                model_name: '',
                searchValue: '',
                context_categories: [],
            },
            documents: {
                resource_type: 'documents',
                current_page: 0,
                per_page: 150,
                offset: 0,
                total: 0,
                context: 'product',
                category: '',
                model_id: '',
                model_name: '',
                searchValue: '',
                context_categories: [],
            },
            finishes: {
                resource_type: 'finishes',
                current_page: 0,
                per_page: 150,
                offset: 0,
                total: 0,
                context: 'brand',
                category: '',
                model_id: '',
                model_name: '',
                searchValue: '',
                context_categories: [],
            },
            textiles: {
                resource_type: 'textiles',
                current_page: 0,
                per_page: 150,
                offset: 0,
                total: 0,
                context: 'brand',
                category: '',
                model_id: '',
                model_name: '',
                searchValue: '',
                context_categories: [],
            },
        },
        resetFilters: {
            context: '',
            category: '',
            model_id: '',
            model_name: '',
        }
    },

    mutations: {
        SET_PRODUCTS(state, products) {
            state.products = products ? products : [];
        },
        SET_LOADING(state, loading) {
            state.loading = loading;
        },

        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_RESOURCES(state, {resource_type, resources}) {
            state.resources[resource_type] = resources;
            state[resource_type] = resources;
            if(['finishes', 'textiles'].includes(resource_type)){
               this.dispatch('filterModels', state.pagination[resource_type]);
            }
        },
        UPDATE_RESOURCE(state, {resource_type, resources}) {
            state.resources[resource_type].push(...resources);
            state[resource_type].push(...resources);
        },
        INCREMENT_PAGE(state, resource_type) {
            if (resource_type in state.pagination) {
                state.pagination[resource_type].current_page = state.pagination[resource_type].current_page* 1 + 1;
            }
        },
        async SET_INITITAL_PAGINATION(state, offset = 0) {
            let pagination = await this.dispatch('getCurrentUrlObject');
            state.resourcePage = pagination.resource_type;
            pagination.offset = offset;
            this.commit('UPDATE_PAGINATION', { ...pagination });
        },
        async UPDATE_PAGINATION(state, pagination) {
            let resource_type = pagination.resource_type;
            switch(resource_type) {
                case 'models':
                    pagination.context = pagination.context??'product';
                    state.pagination.models = Object.assign(state.pagination.models, pagination);
                    await this.dispatch('loadContextCategories', { contextType: pagination.context, resourcePage: 'models' });
                    break;
                case 'documents':
                    pagination.context = pagination.context??'product';
                    state.pagination.documents = Object.assign(state.pagination.documents, pagination);
                    await this.dispatch('loadContextCategories', { contextType: pagination.context, resourcePage: 'documents' });
                    break;
                case 'finishes':
                    pagination.context = pagination.context??'brand';
                    state.pagination.finishes = Object.assign(state.pagination.finishes, pagination);
                    await this.dispatch('loadContextCategories', { contextType: pagination.context, resourcePage: 'finishes' });
                    break;
                case 'textiles':
                    pagination.context = pagination.context??'brand';
                    state.pagination.textiles = Object.assign(state.pagination.textiles, pagination);
                   
                    break;
                default:
                    state.pagination.images = Object.assign(state.pagination.images, pagination);
                    break;
            }
            this.dispatch('updateUrlPagination', pagination);
        },

        SET_CONTEXT_CATEGORIES(state, {context, categories, resourcePage}) {
            state.pagination[resourcePage].context = context;
            state.pagination[resourcePage].context_categories = categories;
        },
        SET_RESET_FILTERS(state, {context, category, model_id, model_name}) {
            state.resetFilters = {context, category, model_id, model_name};
        }
    },

    actions: {
        getCurrentUrlObject() {
            const url = new URL(window.location);
            const match = url?.pathname?.match(/\/resources\/([^\/\?]+)/);
            let resource_type = 'images';
            if (match && match[1]) {
                resource_type = match[1];
            }
            const currentPage = url.searchParams.get('current_page')??document.getElementById('current-page').textContent;
            const perPage = url.searchParams.get('per_page')??document.getElementById('per-page').textContent;
            const offset = url.searchParams.get('offset')??document.getElementById('offset').textContent;
            const context = url.searchParams.get('context')??document.getElementById('context').textContent??null;
            const category = url.searchParams.get('category')??document.getElementById('category').textContent??null;
            const model_id = url.searchParams.get('model_id')??document.getElementById('model_id').textContent??null;
            const model_name = url.searchParams.get('model_name')??document.getElementById('model_name').textContent??null;
            const pagination = {};
            pagination.resource_type = resource_type;
            if(currentPage) pagination.current_page = currentPage;
            if(perPage) pagination.per_page = perPage;
            if(offset) pagination.offset = offset;
            if(context) pagination.context = context;
            if(category) pagination.category = category;
            if(model_id) pagination.model_id = model_id;
            if(model_name) pagination.model_name = model_name;
            if(model_name) pagination.searchValue = model_name;
            return pagination;
        },
        async updateUrlPagination({ commit, state }, pagination = {}) {
            const url = new URL(window.location);
            if(pagination.resource_type == 'finishes' || pagination.resource_type == 'textiles'){
                // pagination.context = pagination.context == 'product' ? 'product' : 'brand';
                await this.dispatch('loadContextCategories', { contextType: pagination.context, resourcePage: pagination.resource_type });
            }
            url.searchParams.set('per_page', pagination.per_page??state.pagination[pagination.resource_type].per_page);
            url.searchParams.set('current_page', pagination.current_page??state.pagination[pagination.resource_type].current_page);
            url.searchParams.set('offset', pagination.offset??state.pagination[pagination.resource_type].offset);
            // if(pagination.total) url.searchParams.set('total', pagination.total);
            pagination.context?url.searchParams.set('context', pagination.context):url.searchParams.delete('context');  
            pagination.category?url.searchParams.set('category', pagination.category):url.searchParams.delete('category');  
            pagination.model_id?url.searchParams.set('model_id', pagination.model_id):url.searchParams.delete('model_id');   
            pagination.model_name?url.searchParams.set('model_name', pagination.model_name):url.searchParams.delete('model_name');
            window.history.replaceState({}, '', url.toString());
        },
        async loadResource({ commit, state }, {resource_type='images', filter = false, reload = false } = {}) {
            if(filter){
                commit('UPDATE_PAGINATION', {...filter, resource_type: resource_type});
            }
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                // Dynamic import service
                const svc = await import('../services/designResourcesService.js');
                const response = await svc.default.loadMoreResource(
                    state.pagination[resource_type]
                );

                console.log('this is response', response);

                if (response.items && response.items.length > 0) {
                    if(reload){
                        commit('SET_RESOURCES', {resource_type: resource_type, resources: response.items});
                    }else{
                        commit('UPDATE_RESOURCE', {resource_type: resource_type, resources: response.items});
                    }
                }else{
                    commit('SET_RESOURCES', {resource_type: resource_type, resources: []});
                }

                if(state.pagination[resource_type].context && !state.pagination[resource_type].context_categories.length){
                    await this.dispatch('loadContextCategories', { contextType: response.pagination.context, resourcePage: resource_type });
                }
                commit('UPDATE_PAGINATION', {...response.pagination, resource_type: resource_type});

                commit('SET_LOADING', false);

                return response;
            } catch (error) {
                console.error(`Error loading ${resource_type}:`, error);
                commit('SET_ERROR', error.message || `Failed to load ${resource_type}`);
                commit('SET_LOADING', false);
                throw error;
            }
        },
        async filterModels({ commit, state }, {filter, resource_type, context, category}) {
            try {

                state.resources[resource_type] = state[resource_type].filter(item => {
                    let condition = true;
                    switch(resource_type){
                        case 'models':
                            // return item.title.toLowerCase().includes(filter?.toLowerCase());
                            // Category filter
                            if (category != null && category !== '') {
                                condition = Number(item.category_id) === Number(category);
                                if (!condition) return false;
                            }

                            // Search filter
                            if (filter?.trim()) {
                                condition = item.title?.toLowerCase().includes(filter.trim().toLowerCase());
                                if (!condition) return false;
                            }
                        case 'documents':
                            return item.title.toLowerCase().includes(filter?.toLowerCase());
                        case 'finishes':
                        case 'textiles':
                            if(category){
                                if(context) condition = item[context].toLowerCase().includes(category?.toLowerCase());
                                if(!condition) return condition;
                            }
                            if(filter) {
                               condition = ( item.title?.toLowerCase().includes(filter?.toLowerCase()) 
                                    || item.type?.toLowerCase().includes(filter?.toLowerCase())
                                    || item.brand?.toLowerCase().includes(filter?.toLowerCase())
                                    || item.description?.toLowerCase().includes(filter?.toLowerCase())
                                );
                            }
                            
                    }
                    return condition;
                });
                commit('UPDATE_PAGINATION', {...state.pagination[resource_type], context: context, category: category, searchValue: filter});
            } catch (error) {
                console.error('Error loading models:', error);
                throw error;
            }
        },
        async filterModels_new({ commit, state }, { filter, resource_type, context, category }) {
            try {
                state.resources[resource_type] = state[resource_type].filter(item => {
                    switch (resource_type) {
                        case 'models':
                        case 'documents':
                            return !filter || item.title?.toLowerCase().includes(filter.toLowerCase());
        
                        case 'finishes':
                        case 'textiles': {
                            const matchesCategory = !category || (
                                context
                                    ? item[context]?.toLowerCase().includes(category.toLowerCase())
                                    : item.type?.toLowerCase().includes(category.toLowerCase()) ||
                                      item.brand?.toLowerCase().includes(category.toLowerCase())
                            );
        
                            const matchesSearch = !filter ||
                                item.title?.toLowerCase().includes(filter.toLowerCase());
        
                            return matchesCategory && matchesSearch;
                        }
        
                        default:
                            return true;
                    }
                });
        
                commit('UPDATE_PAGINATION', {
                    ...state.pagination[resource_type],
                    context,
                    category,
                    searchValue: filter
                });
        
            } catch (error) {
                console.error('Error filtering resources:', error);
                throw error;
            }
        },
        async loadDocuments({ commit, state }, { force = false, payload = {}, filter = false } = {}) {
          
            if (!force && state.loadedResourceDocuments.length > 0) {
                console.log('Documents already loaded — showing cached data.');
                return {
                    items: state.loadedResourceDocuments,
                    total: state.total || state.loadedResourceDocuments.length,
                    cached: true
                };
            }

            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                // Dynamic import service
                const svc = await import('../services/designResourcesService.js');
                let currentPage = null;
                if(payload.filter){
                    currentPage = payload.current_page;
                    if(payload.current_page!== undefined && payload.current_page > -1){
                        currentPage = payload.current_page;
                    }else{
                        currentPage =  state.pagination.documents.current_page - 1;
                    }
                }else{
                    currentPage =  state.pagination.documents.current_page;
                }
                const response = await svc.default.loadMoreDesignResourceDocuments(state.pagination.documents.per_page, currentPage, payload);
                if (response.items && response.items.length > 0) {
                    commit('SET_LOADED_RESOURCE_DOCUMENTS', {documents: response.items, filter: filter});
                    if(!filter){
                        commit('INCREMENT_PAGE', 'documents');
                    }
                }else{
                    commit('SET_LOADED_RESOURCE_DOCUMENTS', {documents: [], filter: filter});
                }

                commit('SET_DOCUMENTS_PAGINATION',{
                    current_page: currentPage+1,
                    per_page: state.pagination.documents.per_page,
                    total: response.total,
                    offset: response.offset,
                    total_results: response.total_results,
                    category:payload.category,
                    context:'product',
                    model_id:payload.model_id,
                    model_name:payload.model_name,
                })

                commit('SET_LOADING', false);

                return response;
            } catch (error) {
                console.error('Error loading documents:', error);
                commit('SET_ERROR', error.message || 'Failed to load documents');
                commit('SET_LOADING', false);
                throw error;
            }
        },
        
        async loadFinishes({ commit, state }, { force = false,payload = {} } = {}) {
            if (!force && state.loadedResourceFinishes.length > 0) {
                console.log('Finishes already loaded — showing cached data.');
                return {
                    items: state.loadedResourceFinishes,
                    total: state.total || state.loadedResourceFinishes.length,
                    cached: true
                };
            }

            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                // Dynamic import service
                const svc = await import('../services/designResourcesService.js');
                const response = await svc.default.loadMoreDesignResourceFinishes(state.pagination.finishes.per_page, state.pagination.finishes.current_page,payload);

                if (response.items && response.items.length > 0) {
                    commit('SET_LOADED_RESOURCE_FINISHES', response.items);
                    commit('INCREMENT_PAGE', 'finishes');
                }else{
                    commit('SET_LOADED_RESOURCE_FINISHES', []);
                }
                commit('SET_FINISHES_PAGINATION', {
                    current_page: state.pagination.finishes.current_page,
                    per_page: state.pagination.finishes.per_page,
                    
                    total: response.total
                })

                commit('SET_LOADING', false);

                return response;
            } catch (error) {
                console.error('Error loading finishes:', error);
                commit('SET_ERROR', error.message || 'Failed to load finishes');
                commit('SET_LOADING', false);
                throw error;
            }
        },
        
        async loadTextiles({ commit, state }, { force = false,payload = {} } = {}) {

            if (!force && state.loadedResourceTextiles.length > 0) {
                console.log('Textiles already loaded — showing cached data.');
                return {
                    items: state.loadedResourceTextiles,
                    total: state.total || state.loadedResourceTextiles.length,
                    cached: true
                };
            }

            commit('SET_LOADING', true);
            commit('SET_ERROR', null);

            try {
                // Dynamic import service
                const svc = await import('../services/designResourcesService.js');
                const response = await svc.default.loadMoreDesignResourceTextiles(state.pagination.textiles.per_page, state.pagination.textiles.current_page,payload);

                if (response.items && response.items.length > 0) {
                    commit('SET_LOADED_RESOURCE_TEXTILES', response.items);
                    commit('INCREMENT_PAGE', 'textiles');
                }else{
                    commit('SET_LOADED_RESOURCE_TEXTILES', []);
                }
                commit('SET_TEXTILES_PAGINATION',{
                    current_page: state.pagination.textiles.current_page,
                    per_page: state.pagination.textiles.per_page,
                    
                    total: response.total
                })

                commit('SET_LOADING', false);

                return response;
            } catch (error) {
                console.error('Error loading textiles:', error);
                commit('SET_ERROR', error.message || 'Failed to load textiles');
                commit('SET_LOADING', false);
                throw error;
            }
        },

        // this is context type filter method section
        async loadContextCategories({ commit, state }, { contextType, resourcePage } = {}) {
            if(!contextType){
                commit('SET_CONTEXT_CATEGORIES', {context: "", categories: [], resourcePage: resourcePage});
                return;
            };
            if(state.pagination[resourcePage].context == contextType && state.pagination[resourcePage].context_categories.length > 0){
                return;
            }
            const response = await ResourceService.getCategoriesByContextType(contextType, resourcePage);
            commit('SET_CONTEXT_CATEGORIES', {context: contextType, categories: response, resourcePage: resourcePage});
        },
        async selectCategoryFilter({ commit, state }, { category } = {}) {
            commit('SET_CATEGORY_BY_CONTEXT_TYPE', categoryByContextType);
            const svc = await import('../services/designResourcesService.js');
            const response = await svc.default.getCategoryByContextType(categoryByContextType);
            if (response) {
                commit('SET_CATEGORY_BY_CONTEXT_TYPE_ITEMS', response);
            }
        },
        async filterImagesFormSubmit({ commit, state }, { payload } = {}) {
            // alert('filterImagesFormSubmit');
            const svc = await import('../services/designResourcesService.js');
            const response = await svc.default.loadMoreDesignResourceImages(state.pagination.images.per_page, state.pagination.images.current_page,payload);

        },
        async searchProductNameImageFilter({ state, commit }, { searchValue } = {}) {

            try {
                const filter = searchValue;
                const originalData = state.images; // always original images
        
                // reset (input empty হলে সব show)
                if (!filter || filter.trim() === '') {
                    state.resources.images = originalData;
                    return originalData;
                }
        
                // filter শুধু images এর জন্য
                const filtered = originalData.filter(item =>
                    item.title.toLowerCase().includes(filter.toLowerCase()) ||
                    item.context_reference.toLowerCase().includes(filter.toLowerCase())
                );
        
                // update filtered data
                state.resources.images = filtered;
        
                return filtered;
        
            } catch (error) {
                console.error('Error filtering images:', error);
                throw error;
            }
        },
        async searchFinishTextileFilter({ state }, { searchValue = '', resource_type } = {}) {
            try {
                const originalData =
                    resource_type === 'finishes'
                        ? state.finishes
                        : state.textiles;
        
                const search = searchValue?.trim().toLowerCase();
        
                // Reset data when search is empty
                if (!search) {
                    state.resources[resource_type] = [...originalData];
                    return originalData;
                }
        
                // Support multiple keywords
                const keywords = search.split(/\s+/);
        
                const filtered = originalData.filter(item => {
                    const searchableText = [
                        item.title,
                        item.brand,
                        item.type,
                        item.description,
                        item.grade,
                        item.slug
                    ]
                        .filter(Boolean)
                        .join(' ')
                        .toLowerCase();
        
                    // Every keyword must exist
                    return keywords.every(keyword =>
                        searchableText.includes(keyword)
                    );
                });
        
                state.resources[resource_type] = filtered;
        
                return filtered;
            } catch (error) {
                console.error('Error filtering resources:', error);
                throw error;
            }
        },
        async autocompleteProductName({ commit, state }, { context, category, searchValue } = {}) {
            // if(!context || !category || !searchValue || context !== 'brand'){
            //     commit('SET_PRODUCTS', []);
            //     return [];
            // }
            // console.log('this is response', response);
            const svc = await import('../services/designResourcesService.js');
            const response = await svc.default.autocompleteProductName(context, category, searchValue);
            commit('SET_PRODUCTS', response);
            return response;
        },
    },
    getters: {
        getProducts: state => state.products,
        getResourceImages: state => state.resources.images,
        getResourceModels: state => state.resources.models,
        getResourceDocuments: state => state.resources.documents,
        getResourceFinishes: state => state.resources.finishes,
        getResourceTextiles: state => state.resources.textiles,
        getImagesPagination: state => ({ ...state.pagination.images }),
        getModelsPagination: state => ({ ...state.pagination.models }),
        getDocumentsPagination: state => ({ ...state.pagination.documents }),
        getFinishesPagination: state => ({ ...state.pagination.finishes }),
        getTextilesPagination: state => ({ ...state.pagination.textiles }),
        getContextCategories: state => state.pagination[state.resourcePage].context_categories,
        getResetFilters: state => state.resetFilters,
        getContextFilters: state => {
            switch (state.resourcePage) {
                case 'models':
                    return [
                        { label: "Product", value: "product", selected: true}
                    ];
                case 'documents':
                    return [
                        { label: "Product", value: "product", selected: true}
                    ];
                case 'finishes':
                    return [
                        { label: "Brand", value: "brand", selected: true },
                        { label: "Type", value: "type" }
                    ];
                case 'textiles':
                    return [
                        { label: "Brand", value: "brand", selected: true },
                        { label: "Type", value: "type" }
                    ];
                default:
                    return [
                        // { label: "All Contexts", value: "" },
                        { label: "Product", value: "product" },
                        { label: "Project", value: "project" },
                        { label: "Post", value: "post" },
                        { label: "Showroom", value: "showrooms" }
                    ];
            }
        },
        getResourceType: state => state.resourcePage,
    }
});
export default designResourcesStore;
