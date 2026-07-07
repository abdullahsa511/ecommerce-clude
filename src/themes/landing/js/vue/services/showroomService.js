/**
 * Showroom Service
 * Handles API calls for showroom data
 */

class ShowroomService {
    constructor() {
        this.baseURL = '/api/showroom'; // Adjust this to your actual API endpoint
    }

    /**
     * Get section details by name/slug
     * @param {string} sectionName - The name of the section
     * @returns {Promise<Object>} Section details
     */
    async getSectionDetails(sectionName, showroomSlug) {
        try {
            // alert(sectionName);
            // Convert section name to slug format
            const slug = this.convertToSlug(sectionName);
            
            // test 
            // const data = await fetch(`${this.baseURL}/sections/${slug}`);
            // console.log(data.json());
            // return false;

            // For now, return mock data. Replace with actual API call
            // const response = await this.mockApiCall(slug);
            // console.log('Section Details:', response);
            // return response;
            
            // Uncomment below for actual API integration:
            const response = await fetch(`${this.baseURL}/${showroomSlug}/sections/${slug}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
            
        } catch (error) {
            console.error('Error fetching section details:', error);
            throw error;
        }
    }

    /**
     * Get all showroom sections
     * @returns {Promise<Array>} Array of sections
     */
    async getAllSections() {
        try {
            // Mock data for now
            const sections = [
                {
                    id: 1,
                    name: 'Collaborative Hub ',
                    slug: 'collaborative-hub',
                    description: 'A space designed for teamwork and collaboration',
                    image: '/img/showroom/collaborative-Hub.png',
                    features: ['Flexible seating', 'Interactive displays', 'Meeting pods']
                },
                {
                    id: 2,
                    name: 'Work Hub',
                    slug: 'work-hub',
                    description: 'Dedicated workspace for focused productivity',
                    image: '/img/showroom/work-hub.png',
                    features: ['Ergonomic workstations', 'Privacy screens', 'Adjustable desks']
                },
                {
                    id: 3,
                    name: 'Design Library',
                    slug: 'design-library',
                    description: 'Resource center for design inspiration and materials',
                    image: '/img/showroom/design-library.png',
                    features: ['Material samples', 'Design books', 'Digital catalogs']
                },
                {
                    id: 4,
                    name: 'Adaptive Solutions',
                    slug: 'adaptive-solutions',
                    description: 'Flexible furniture solutions for changing needs',
                    image: '/img/showroom/adaptive-solutions.png',
                    features: ['Modular furniture', 'Convertible spaces', 'Smart storage']
                }
            ];
            
            return sections;
            
            // Uncomment for actual API integration:
            // const response = await fetch(`${this.baseURL}/sections`);
            // if (!response.ok) {
            //     throw new Error(`HTTP error! status: ${response.status}`);
            // }
            // return await response.json();
            
        } catch (error) {
            console.error('Error fetching sections:', error);
            throw error;
        }
    }

    /**
     * Get section products/furniture
     * @param {string} sectionSlug - The slug of the section
     * @returns {Promise<Array>} Array of products
     */
    async getSectionProducts(sectionSlug) {
        try {
            // Mock data for now
            const products = [
                {
                    id: 1,
                    name: 'Collaborative Table',
                    description: 'Large table for team meetings',
                    price: 2500,
                    image: '/img/products/collaborative-table.jpg'
                },
                {
                    id: 2,
                    name: 'Ergonomic Chair',
                    description: 'Comfortable chair for long work sessions',
                    price: 800,
                    image: '/img/products/ergonomic-chair.jpg'
                }
            ];
            
            return products;
            
            // Uncomment for actual API integration:
            // const response = await fetch(`${this.baseURL}/sections/${sectionSlug}/products`);
            // if (!response.ok) {
            //     throw new Error(`HTTP error! status: ${response.status}`);
            // }
            // return await response.json();
            
        } catch (error) {
            console.error('Error fetching section products:', error);
            throw error;
        }
    }

    /**
     * Convert string to URL-friendly slug
     * @param {string} text - Text to convert
     * @returns {string} URL-friendly slug
     */
    convertToSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    }

    /**
     * Mock API call for development
     * @param {string} slug - Section slug
     * @returns {Promise<Object>} Mock section data
     */
    async mockApiCall(slug) {
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        const mockData = {
            'collaborative-hub': {
                id: 1,
                name: 'Collaborative Hub',
                slug: 'collaborative-hub',
                description: 'A dynamic space designed to foster teamwork and innovation. Features flexible seating arrangements and interactive technology displays.',
                image: '/img/showroom/collaborative-Hub.png',
                features: [
                    'Flexible seating configurations',
                    'Interactive digital displays',
                    'Meeting pods for private discussions',
                    'Whiteboard walls for brainstorming',
                    'Wireless charging stations'
                ],
                products: [
                    { id: 1, name: 'Collaborative Table', price: 2500 },
                    { id: 2, name: 'Meeting Pod', price: 3500 }
                ]
            },
            'work-hub': {
                id: 2,
                name: 'Work Hub',
                slug: 'work-hub',
                description: 'A dedicated workspace designed for focused productivity and individual work.',
                image: '/img/showroom/work-hub.png',
                features: [
                    'Ergonomic workstations',
                    'Privacy screens',
                    'Adjustable height desks',
                    'Task lighting',
                    'Cable management systems'
                ],
                products: [
                    { id: 3, name: 'Standing Desk', price: 1200 },
                    { id: 4, name: 'Ergonomic Chair', price: 800 }
                ]
            }
        };
        
        return mockData[slug] || {
            id: 0,
            name: 'Unknown Section',
            slug: slug,
            description: 'Section details not available.',
            image: '/img/showroom/default.png',
            features: [],
            products: []
        };
    }
}

// Create and export a singleton instance
const showroomService = new ShowroomService();
export default showroomService;
