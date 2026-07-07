
class BlogPostListService {
    constructor() {
        this.baseURL = '/api/post-pagination';
    }
    async loadMoreBlogPost(per_page, current_page) {
        console.log('per_page loadMoreBlogPost', per_page);
        console.log('current_page loadMoreBlogPost', current_page);
        
        try {
            const response = await fetch(`${this.baseURL}?per_page=${per_page}&current_page=${current_page}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading blog posts:', error);
            throw error;
        }
    }
}

const blogPostListService = new BlogPostListService();
export default blogPostListService;
