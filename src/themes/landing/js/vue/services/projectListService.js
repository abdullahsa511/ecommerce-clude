
class ProjectListService {
    constructor() {
        this.baseURL = '/api/project-pagination';
    }
    async loadMoreProjectList(per_page = 21, current_page = 1) {
        try {
            const response = await fetch(`${this.baseURL}?per_page=${per_page}&current_page=${current_page}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading project lists:', error);
            throw error;
        }
    }
}

const projectListService = new ProjectListService();
export default projectListService;
