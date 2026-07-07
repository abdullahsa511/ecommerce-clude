import { Pinboard } from '../models/Pinboard.js';
import Service from './service.js';

class managePinboardService extends Service {
    constructor() {
        super();
        this.projectListUrl = '/api/account/project-list';
        this.localKey = 'pinboard';
        this.pinboardKey = 'pinboard';
        this.localAuthKey = 'userAuthDetails';
        this.apiUrl = '/api/pinboard/';
        this.nearestShowroomUrl = '/api/user-nearest-showroom-by-ip';
        this.bookingPhoneCallUrl = '/api/booking-phone-call';
        this.bookingEmailUrl = '/api/booking-email-service-requests';
        this.updateCommentDescriptionUrl = '/api/pinboard-items/update-comment-description';
        this.baseUrl = '/api';
    }

    async getProjectList(userId) {
        try {
            const url = this.projectListUrl + '/' + userId;
            const response = await fetch(url);
            const apiData = await response.json();
            return { data: apiData };
        } catch (err) {
            console.error('API fetch failed, error=', err);
            return { error: err.message };
        }
    }

    async createNewProject(payload) {
        try {
            const url = '/api/pinboards/create-new-project';
            const response = await this.post(url, payload);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const apiData = await response.json();
            return apiData;
        }catch (err) {
            throw new Error(err.message);
        }
    }

    async addToPinboard(pinboard, item) {
        item.pinboard_id = pinboard.pinboard_id;
        try {
            if (item.model_type === 'images' && typeof item.photo === 'string' && item.photo.startsWith('blob:')) {
                item.photo = await this.uploadPinboardBlobImage(item.photo);
            }
            const url = item.model_type === 'images' ? '/api/pinboard-items/add-to-pinboard-images' : '/api/pinboard-items/add-to-pinboard';

            const response = await this.post(url, item);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();

        } catch (err) {
            throw new Error(err.message);
        }
    }

    async uploadPinboardBlobImage(blobUrl) {
        let blobResponse;
        try {
            blobResponse = await fetch(blobUrl);
        } catch (err) {
            throw new Error('Failed to read captured image');
        }

        if (!blobResponse.ok) {
            throw new Error('Failed to read captured image');
        }

        const blob = await blobResponse.blob();
        const extension = this.getImageExtensionFromType(blob.type);
        const file = new File([blob], `pinboard-image-${Date.now()}.${extension}`, {
            type: blob.type || 'image/jpeg'
        });

        const formData = new FormData();

        formData.append('0', file, file.name);
        formData.append('upload_dir', 'media/pinboard');

        const uploadResponse = await fetch('/api/media/upload', {
            method: 'POST',
            body: formData
        });

        if (!uploadResponse.ok) {
            throw new Error('Failed to upload captured image');
        }

        const uploadResult = await uploadResponse.json();
        const uploadedFile =
            uploadResult?.files?.[0] ||
            uploadResult?.data?.files?.[0] ||
            uploadResult?.result?.files?.[0] ||
            null;
        const uploadedPath = uploadedFile?.objectURL || uploadedFile?.path || uploadedFile?.image || null;
        if (!uploadedPath) {
            throw new Error('Upload succeeded but image path was missing');
        }

        return uploadedPath;
    }

    getImageExtensionFromType(mimeType) {
        if (!mimeType || typeof mimeType !== 'string') return 'jpg';
        if (mimeType.includes('png')) return 'png';
        if (mimeType.includes('webp')) return 'webp';
        if (mimeType.includes('gif')) return 'gif';
        return 'jpg';
    }

    async updatePinboardItem(pinboard, item) {
        try {
            const url = '/api/pinboard-items/update-pinboard-item';
            const response = await this.put(url, item);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const pinboardItem = await response.json();
            this.emitPinboardUpdatedEvent(pinboard);
            return pinboardItem;
        } catch (err) {
            throw new Error(err.message);
        }
    }
    async removePinboardItem(pinboardItemId) {
        try {
            const url = `/api/pinboard-items/${pinboardItemId}`;
            const response = await this.delete(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        }catch(err){
            throw new Error(err.message);
        } 
    }
    async reorderPinboardItems(pinboard, items) {
        try {
            const url = '/api/pinboard-items/reorder';
            const safeItems = Array.isArray(items) ? items : [];
            const itemsToSync = safeItems.map((item, index) => ({
                pinboard_item_id: item?.pinboard_item_id,
                pinboard_id: item?.pinboard_id,
                model_id: item?.model_id,
                model_type: item?.model_type,
                sort_order: index + 1
            }));
            const response = await this.post(url, itemsToSync);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            return await response.json();
        } catch (err) {
           throw new Error(err.message);
        }
    }

    async addPinboardItemComment(pinboard_item_id, comment) {
        try {
            const url = this.updateCommentDescriptionUrl;
            const response = await this.post(url, { pinboard_item_id, comment });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }
    
    async updatePinboardItemDescription(pinboard_item_id, description) {
        try {
            const url = this.updateCommentDescriptionUrl;
            const response = await this.post(url, { pinboard_item_id, description });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async addCommentItemToPinboard(formData) {

        try {
            const url = `${this.baseUrl}/pinboards/comments`;
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const responseData = await response.json().catch(() => null);
            if (!response.ok) {
                const apiMessage =
                    responseData?.message ||
                    responseData?.error ||
                    'Network response was not ok';
                throw new Error(apiMessage);
            }
            return responseData;
        } catch (err) {
            throw new Error(err.message);
        }

    }

    async projectItems(customerId) {
        try {
            const url = `${this.baseUrl}/account/pinboard-list?customer_id=${customerId}`;
            const response = await this.get(url);
            // console.log(response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async getProjectByPinboardId(pinboardId, userId, projectListPage = false) {
        try {
            const query = projectListPage ? '?project_list=true' : '';
            const url = `${this.baseUrl}/pinboards/${pinboardId}`;
    
            const response = await this.get(url);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const raw = await response.json();
            const pinboard = { ...raw };
            // // show() returns PinboardResponseData with pinboard_item; store/model expect pinboard_items
            // if (pinboard.pinboard_item != null && pinboard.pinboard_items == null) {
            //     pinboard.pinboard_items = Array.isArray(pinboard.pinboard_item)
            //         ? pinboard.pinboard_item
            //         : [];
            // }
            // if (!Array.isArray(pinboard.pinboard_items)) {
            //     pinboard.pinboard_items = [];
            // }
            return new Pinboard(pinboard);
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async searchPinboardAutocomplete(query) {
        const q = (query || '').trim().toLowerCase();
        if (!q) {
            return [];
        }

        try {
            const response = await fetch(`${this.baseUrl}/search-pinboard-products?query=${encodeURIComponent(q)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} for query: ${q}`);
            }
            const productsData = await response.json();
            return productsData ?? [];
        } catch (error) {
            console.error('Error getting search results:', error);
            throw error;
        }
    }
}
export default new managePinboardService();
