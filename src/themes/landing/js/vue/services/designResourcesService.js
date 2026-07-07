
class DesignResourcesService {
    constructor() {
        this.baseURL = '/api/design-resources'; 
    }
    buildUrl(pagination = {}) {
        const current_page = pagination.current_page > 0 ? (pagination.current_page*1) : 1;
        let url = `${this.baseURL}/${pagination.resource_type}?per_page=${pagination.per_page}&current_page=${current_page}&offset=${pagination.offset}`;
        if(pagination.context) url += `&context=${pagination.context}`;
        if(pagination.category) url += `&category=${pagination.category}`;
        if(pagination.model_id) url += `&model_id=${pagination.model_id}`;
        if(pagination.model_name) url += `&model_name=${pagination.model_name}`;
        if(pagination.searchValue) url += `&searchValue=${pagination.searchValue}`;
        if (pagination.resource_type == 'models') {
            url = `${this.baseURL}/${pagination.resource_type}?per_page=${pagination.per_page}&current_page=${current_page}&offset=0`; 
            return url;
        }
        return url;
    }
    async loadMoreResource(pagination = {}) {
        let url = this.buildUrl(pagination);
        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // return data;
            return data; 
        } catch (error) {
            console.error(`Error loading ${pagination.resource_type}:`, error);
            throw error;
        }
    }

    // this is context type filter method section
    async getCategoriesByContextType(contextType, resourceType = '') {
        try {
            let url = `${this.baseURL}/categories/${contextType}`;
            if(resourceType){
                url += `/${resourceType}`;
            }
            const response = await fetch(url);
           
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error getting context type:', error);
            throw error;
        }
    }

    async getCategoryByContextType(nameByCategoryId) {
        try {
            const response = await fetch(`${this.baseURL}/context-category-by-name/${nameByCategoryId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        }
        catch (error) {
            console.error('Error getting category by context type:', error);
            throw error;
        }
    }

    async autocompleteProductName(context, category, searchValue) {
        try {
            // get request
            const response = await fetch(`${this.baseURL}/model-name-by-model-id?context=${context}&category=${category}&search=${searchValue}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;

        }
        catch (error) {
            console.error('Error getting autocomplete product name:', error);
            throw error;
        }
    }
}
export default new DesignResourcesService();
