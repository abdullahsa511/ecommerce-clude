class ProductConfiguratorService {

    constructor() {
        this.baseURL = '/api/product-configurator';
        this.categoryProductListingURL = '/api/category';
        this.inputAutocompleteURL = '/api/design-resources/resources-by-desk';
        this.searchResultsURL = '/api/global-search-by-context';
        // resource_type=finishes&search_query=a
    }
    async loadMoreProductConfigurator(payload = {}, demoData = true) {
        try {
            const response = demoData ? ProductConfiguratorService.demoData : await fetch(`${this.baseURL}`, {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            
            if (!response.ok && !demoData) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response;
        } catch (error) {
            console.error('Error loading product configurator:', error);
            throw error;
        }
    }
    async getFinishesDataByGrade(grade) {
        try {
            // Make a GET request without a body (bodies are not valid with GET)
            const response = await fetch(`${this.baseURL}/get-finishes-data-by-grade/${grade}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting option by finishes data:', error);
            throw error;
        }
    }

    buildUrl(pagination = {}) {
        const current_page = pagination.current_page ? pagination.current_page : 1;
        // const offset = pagination.offset > 0 ? pagination.offset : 0;
        let url = `${this.categoryProductListingURL}/height-adjustable-workstations/products`;
        // let url = `${this.categoryProductListingURL}/${pagination.category_slug}/products`;
        url += `?per_page=${pagination.per_page}&current_page=${current_page}&offset=${pagination.offset ?? 0}`;
        if(pagination.material_id) url += `&material_id=${pagination.material_id}`;
        if(pagination.feature_id) url += `&feature_id=${pagination.feature_id}`;
        if(pagination.weight_id) url += `&weight_id=${pagination.weight_id}`;
        if(pagination.certificate_id) url += `&certificate_id=${pagination.certificate_id}`;
        
        // console.log('url service buildUrl', url);
        return url;
    }
    async getProducts(pagination = {}) {
        let url = this.buildUrl(pagination);
        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // console.log('response service getProducts', data);
            // return data;
            return data; 
        } catch (error) {
            console.error(`Error loading ${pagination.resource_type}:`, error);
            throw error;
        }
    }
    async getSearchResults(pagination = {}) {
        const contexts = Array.isArray(pagination.contexts) ? pagination.contexts.join(',') : '';
        const q = encodeURIComponent(pagination.search_query ?? '');
        let url = `${this.searchResultsURL}?query=${q}&contexts=${contexts}&per_page=${pagination.per_page}&current_page=${pagination.current_page}&offset=${pagination.offset}`;
        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return {
                results: data?.results ?? [],
                pagination: data?.pagination ?? null,
                total_result: data?.total_result ?? null,
            };
        } catch (error) {
            console.error(`Error loading ${pagination.resource_type}:`, error);
            throw error;
        }
    }
    async productFilter(filter = {}) {
        let url = this.buildUrl(filter);
        // console.log('url service productFilter', url);
        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // console.log('response service getProducts', data);
            // return data;
            return data; 
        } catch (error) {
            console.error(`Error loading product filter:`, error);
            throw error;
        }
    }

    async productSearchFilter(filter = {}) {
        const current_page = filter.current_page ? filter.current_page : 1;
        const contexts = Array.isArray(filter.contexts) ? filter.contexts.join(',') : '';
        const q = encodeURIComponent(filter.search_query ?? '');
        let url = `${this.searchResultsURL}?query=${q}&contexts=${contexts}`;
        url += `&per_page=${filter.per_page}&current_page=${current_page}&offset=${filter.offset ?? 0}`;

        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return {
                results: data?.results ?? [],
                pagination: data?.pagination ?? null,
                total_result: data?.total_result ?? null,
            };
        } catch (error) {
            console.error(`Error loading product filter:`, error);
            throw error;
        }
    }

    async inputAutocomplete(filter = {}) {

        try {
            const response = await fetch(`${this.inputAutocompleteURL}?resource_type=${filter.resource_type}&search_query=${filter.search_query}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // console.log('response service inputAutocomplete', data);
            // return data;
            return data; 
        } catch (error) {
            console.error(`Error loading input autocomplete:`, error);
            throw error;
        }
    }
    async getTextilesDataByType(grade = '') {
        try {
            const response = await fetch(`${this.baseURL}/get-textiles-data-by-grade/${grade}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting tags:', error);
            throw error;
        }
    }
}

const productConfiguratorService = new ProductConfiguratorService();
export default productConfiguratorService;