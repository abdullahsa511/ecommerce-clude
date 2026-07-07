class SearchbarService {

    static demoData = {
        "total_result": "Results: 8 Results",
        "results": [
            {
                "title": "kenni",
                "dataSrc": "/media/Products/image/Kenni_Image.jpg",
                "dataBgSrc": "/media/Products/image/Kenni_Image.jpg",
                "context": "Product",
                "context_reference": 115,
                "description": "Kenni is a refined seating solution for meeting rooms, reception areas, and breakout spaces. Available as an arm chair or side chair, its design is suitable for a range of professional applications."
            },
            {
                "title": "PDF",
                "dataSrc": "Krost User Guide - Kenni.pdf",
                "dataBgSrc": "Krost User Guide - Kenni.pdf",
                "context": "Media",
                "context_reference": 52,
                "description": ""
            },
            {
                "title": "image/jpeg",
                "dataSrc": "Kenni_Image.jpg",
                "dataBgSrc": "Kenni_Image.jpg",
                "context": "Media",
                "context_reference": 53,
                "description": ""
            },
            {
                "title": "image/jpeg",
                "dataSrc": "Kenni_Image.jpg",
                "dataBgSrc": "Kenni_Image.jpg",
                "context": "Media",
                "context_reference": 777,
                "description": ""
            },
            {
                "title": "image/png",
                "dataSrc": "/media/Products/image/Kenni_Image.jpg",
                "dataBgSrc": "/media/Products/image/Kenni_Image.jpg",
                "context": "Media",
                "context_reference": 2465,
                "description": ""
            },
            {
                "title": "5B",
                "dataSrc": "/media/Projects/banner/5B_Banner.jpg",
                "dataBgSrc": "/media/Projects/banner/5B_Banner.jpg",
                "context": "Project",
                "context_reference": 36,
                "description": "5B's new office space features a contemporary furniture selection, providing a multifunctional environment that facilitates productivity and collaboration."
            },
            {
                "title": "Hyundai Office: Stage 2",
                "dataSrc": "/media/Projects/banner/Huyndai-Office_Banner.jpg",
                "dataBgSrc": "/media/Projects/banner/Huyndai-Office_Banner.jpg",
                "context": "Project",
                "context_reference": 41,
                "description": "Apex Interiors have transformed Hyundai’s new office space into a hybrid commercial environment that promotes productivity and collaboration."
            },
            {
                "title": "Kenni",
                "dataSrc": "/media/Products/image//Kenni_Image.jpg",
                "dataBgSrc": "/media/Products/image//Kenni_Image.jpg",
                "context": "DesignResource",
                "context_reference": 100,
                "description": ""
            }
        ]
    }

    constructor() {
        this.baseURL = '/api/global-search';
    }
    async loadSearchbar(payload = {}, demoData = true) {
        try {
            const response = demoData ? SearchbarService.demoData : await fetch(`${this.baseURL}`, {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            
            if (!response.ok && !demoData) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response;
        } catch (error) {
            console.error('Error loading searchbar:', error);
            throw error;
        }
    }
    async getSearchResults(searchValue) {
        // return SearchbarService.demoData;
        try {
            // /api/global-search?query=Kenni
            const response = await fetch(`${this.baseURL}?query=${searchValue}`, {
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
            console.error('Error getting search results:', error);
            throw error;
        }
    }
    async tagSearch() {
        try {
            const response = await fetch(`/api/popular-search`, {
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
            console.error('Error getting tag search results:', error);
            throw error;
        }
    }
}

const searchbarService = new SearchbarService();
export default searchbarService;