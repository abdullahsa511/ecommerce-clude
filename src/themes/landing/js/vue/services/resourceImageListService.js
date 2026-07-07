
class ResourceImageListService {
    constructor() {
        this.baseURL = '/api/account/resource/images';
    }
    async loadMoreResourceImages(per_page = 5, current_page = 1) {
        try {
            const response = await fetch(`${this.baseURL}?per_page=${per_page}&current_page=${current_page}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log(data);
            
            return data;
        } catch (error) {
            console.error('Error loading images:', error);
            throw error;
        }
    }
}

const resourceImageListService = new ResourceImageListService();
export default resourceImageListService;
