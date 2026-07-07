import Service from './service.js';
import pinboardStore from '../store/pinboardStore.js';


class PinboardService extends Service {
    constructor() {
        super();
        this.localKey = 'pinboard';
        this.pinboardKey = 'pinboard';
        this.localAuthKey = 'userAuthDetails';
        this.customerPinboardUrl = '/api/customer/pinboard';
        this.nearestShowroomUrl = '/api/user-nearest-showroom-by-ip';
        this.bookingPhoneCallUrl = '/api/booking-phone-call';
        this.bookingEmailUrl = '/api/booking-email-service-requests';
        this.updateCommentDescriptionUrl = '/api/pinboard-items/update-comment-description';
        this.baseUrl = '/api';
    }

    // Emit a cross-window/within-window event when pinboard localStorage is updated
    emitPinboardUpdatedEvent(pinboardOrCount) {
        try {
            if (typeof window === 'undefined' || typeof window.dispatchEvent !== 'function') return;
            let count = 0;
            if (typeof pinboardOrCount === 'number') {
                count = pinboardOrCount;
            } else if (Array.isArray(pinboardOrCount)) {
                count = pinboardOrCount.length;
            } else {
                const pb = pinboardOrCount || {};
                count = pb.pinboard_items?.length || 0;
            }
            window.dispatchEvent(new CustomEvent('pinboard:updated', { detail: { count } }));
        } catch (err) {
            throw new Error(err.message);
        }
    }
    async getPinboardForGuest() {
        let pinboard = await localStorage.getItem(this.pinboardKey);
        if(pinboard) pinboard = JSON.parse(pinboard);
        return pinboard;
    }
    async getPinboardForLoggedInUser(userId) {
        try {
            let url = `${this.customerPinboardUrl}`;
            // if (pinboardId != null && pinboardId !== '') {
            //     const q = new URLSearchParams({ pinboard_id: String(pinboardId) });
            //     url += `?${q.toString()}`;
            // }
            const response = await this.get(url);
            if (!response.ok) {
                throw {
                    status: response.status,
                    statusText: response.statusText || '',
                    message: response.error || response.message || 'Network response was not ok',
                };
            }
            const pinboard = await response.json();
            return pinboard;
        } catch (err) {
            throw err;
        }
    }
    async createTemporayPinboard(pinboard) {
        try {
            const url = '/api/pinboards/save-temp';
            const response = await this.post(url, pinboard);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const apiData = await response.json();
            return apiData;
        }catch (err) {
            throw new Error(err.message);
        }
    }
    async createPinboard(pinboard) {
        try {
            const url = '/api/pinboards/save';
            const response = await this.post(url, pinboard);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const apiData = await response.json();
            return apiData;
        }catch (err) {
            throw new Error(err.message);
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
    async updatePinboard(pinboard) {
        try {
            const url = '/api/pinboards/update';
            const response = await this.post(url, pinboard);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const apiData = await response.json();
            return apiData;
        }catch (err) {
            throw new Error(err.message);
        }
    }

    async updateProjectTitle(payload) {
        try {
            const url = '/api/pinboards/update-project-title';
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
    async updatePinboardItemQuantity({ pinboard_item_id, model_id, model_type, quantity }) {
        try {
            const url = '/api/pinboard-items/update-quantity';
            const response = await this.put(url, { pinboard_item_id, model_id, model_type, quantity });
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

    async addCommentItemToPinboard_old(formData) {

        try {
            const url = 'api/pinboards/comments';
            const response = await this.post(url, formData);
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


    async bookingShowroomVisit(bookingData) {
        try {
            const url = this.baseUrl + '/visit-showroom/book-now';
            const response = await this.post(url, bookingData);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async bookingVirtualMeeting(bookingData) {
        try {
            const url = this.baseUrl + '/visit-showroom/book-now';
            const response = await this.post(url, bookingData);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    // final page 
    async bookingPhoneCall(bookingData) {
        try {
            const url = this.bookingPhoneCallUrl;
            const response = await this.post(url, bookingData);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            localStorage.setItem('pinboard_processed', 
                JSON.stringify({
                    pinboard_id: bookingData.pinboard_id, 
                    processed_method: 'phone-call', 
                    date_time: new Date().toISOString()})
                );
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    // final page
    async bookingByEmail(payload) {
        try {
            const url = this.bookingEmailUrl;
            const response = await this.post(url, payload);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const pinboardId = payload instanceof FormData
                ? payload.get('pinboard_id')
                : payload.pinboard_id;
            localStorage.setItem('pinboard_processed', 
                JSON.stringify({
                    pinboard_id: pinboardId, 
                    processed_method: 'email', 
                    date_time: new Date().toISOString()})
                );
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async bookNow(bookingData) {
        try {
            const url = this.baseUrl + '/visit-showroom/book-now';
            const response = await this.post(url, bookingData);
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }
    async getShowrooms() {
        try {
            const url = 'api/showrooms';
            const response = await this.get(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async getNearestShowroom() {
        try {
            const url = this.nearestShowroomUrl;
            const response = await this.get(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
        } catch (err) {
            throw new Error(err.message);
        }
    }

    async getBookedData(date, showroomId, tourType = 'physicalTour') {
        console.log("pinboard service tourType=", tourType);
        try {
            const url = `${this.baseUrl}/fetch-booked-data/${showroomId}/${date}?tour_type=${tourType}`;
            const response = await this.get(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return await response.json();
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
            const url = `${this.baseUrl}/get-project-items/${userId}/${pinboardId}${query}`;
    
            const response = await this.get(url);
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const raw = await response.json();
            const pinboard = { ...raw };
            // show() returns PinboardResponseData with pinboard_item; store/model expect pinboard_items
            if (pinboard.pinboard_item != null && pinboard.pinboard_items == null) {
                pinboard.pinboard_items = Array.isArray(pinboard.pinboard_item)
                    ? pinboard.pinboard_item
                    : [];
            }
            if (!Array.isArray(pinboard.pinboard_items)) {
                pinboard.pinboard_items = [];
            }
            return pinboard;
        } catch (err) {
            throw new Error(err.message);
        }
    }

    // Following methods are for authentication
    async savePinboardToLocalStorage(pinboard) {
        localStorage.setItem(this.localKey, JSON.stringify(pinboard));
    }
    async setCustomer(customer) {
        localStorage.setItem('customer', JSON.stringify(customer));
    }
    async getCustomer() {
        let customer = localStorage.getItem('customer');
        if(customer) customer = JSON.parse(customer);
        return customer || {};
    }

    // configuration page 
    async addToPinboardConfigurator(itemData) {
        let pinboard = JSON.parse(
            localStorage.getItem(this.localKey) || '{"pinboard_items":[]}'
        );
    
        pinboard.pinboard_items ||= [];
        const index = pinboard.pinboard_items.findIndex(
            item =>
                item.model_id === itemData.id &&
                item.model_type === itemData.model
                && JSON.stringify(item.options || null) === JSON.stringify(itemData.options || null)
        );
    
        const existingItem = index !== -1 ? pinboard.pinboard_items[index] : null;

        // add new
        if(existingItem) {
            pinboard.pinboard_items[index] = {
                ...existingItem,
                quantity: itemData.quantity,
                photo: itemData.photo || null,
                comments: itemData.comments || null,
                options: itemData.options || null,
                accessories: itemData.accessories || null
            };
        }
        
        if (pinboard.pinboard_id) {
            const url = this.baseUrl 
            ? `${this.baseUrl}/pinboard-items/add-to-pinboard-configurator`
            : '/api/pinboard-items/add-to-pinboard-configurator';

            itemData.pinboard_id = pinboard.pinboard_id;
            itemData.pinboard_item_id = existingItem?.pinboard_item_id ?? null;
            const response = await this.post(url, itemData);
            if (!response.ok) {
                throw new Error('Network error');
            }
        }

        // add new to local storage and pinboard store
        pinboard.pinboard_items.push(itemData);
        localStorage.setItem(this.localKey, JSON.stringify(pinboard));
        pinboardStore.commit('ADD_PINBOARD_ITEM', itemData);
        this.emitPinboardUpdatedEvent(pinboard);
        return {
            data: pinboard.pinboard_items,
            message: 'Pinboard updated successfully'
        };

        // update existing item
        // add new end
        // compare options
        // const optionsChanged =
        //     JSON.stringify(existingItem?.options || null) !==
        //     JSON.stringify(itemData.options || null);
        //     console.log('optionsChanged', optionsChanged);

    
        // const quantityChanged =
        //     existingItem?.quantity !== itemData.quantity;
        //     console.log('quantityChanged', quantityChanged);

        // const accessoriesChanged =
        //     JSON.stringify(existingItem?.accessories || null) !==
        //     JSON.stringify(itemData.accessories || null);
        //     console.log('accessoriesChanged', accessoriesChanged);
    
        // const callApi = (!existingItem || quantityChanged || optionsChanged || accessoriesChanged) && pinboard.pinboard_id;
        // // console.log('callApi', callApi);

        // console.log('pinboard items', itemData.options);

        // const apiData = {
        //     pinboard_id: pinboard.pinboard_id,
        //     pinboard_item_id: existingItem?.pinboard_item_id ?? null,
        //     model_id: itemData.id,
        //     title: itemData.title || 'test title',
        //     model_type: itemData.model,
        //     quantity: itemData.quantity ?? 1,
        //     unit_price: itemData.unit_price ?? 0,
        //     photo: itemData.image || null,
        //     comments: itemData.comments || null,
        //     options: itemData.options || null,
        //     accessories: itemData.accessories || null,
        //     optionsChanged: optionsChanged,
        //     accessoriesChanged: accessoriesChanged,
        //     quantityChanged: quantityChanged,
        // };

        // if (!callApi) {
        //     // update local pinboard
        //     // pinboard.pinboard_items = [...pinboard.pinboard_items, apiData];
        //     pinboard.pinboard_items.push(apiData);
        //     localStorage.setItem(this.localKey, JSON.stringify(pinboard));
        //     // update pinboard store
        //     pinboardStore.commit('ADD_PINBOARD_ITEM', apiData);
        //     this.emitPinboardUpdatedEvent(pinboard);
           
        //     return {
        //         data: pinboard.pinboard_items,
        //         message: 'No changes detected'
        //     };
        // }
    
        // const url = this.baseUrl 
        // ? `${this.baseUrl}/pinboard-items/add-to-pinboard-configurator`
        // : '/api/pinboard-items/add-to-pinboard-configurator';

        // const response = await fetch(url, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json'
        //     },
        //     body: JSON.stringify(apiData)
        // });
    
        // if (!response.ok) {
        //     throw new Error('Network error');
        // }
    
        // const apiResponse = await response.json();
    
        // if (!apiResponse.success || apiResponse.data?.error) {
        //     return {
        //         data: pinboard.pinboard_items,
        //         error: apiResponse.data?.message || 'API failed'
        //     };
        // }
    
        // const result = apiResponse.data;
    
        // if (existingItem) {
        //     // update existing item
        //     pinboard.pinboard_items[index] = {
        //         ...existingItem,
        //         quantity: result.quantity,
        //         unit_price: result.unit_price ?? existingItem.unit_price,
        //         total_price: (result.unit_price ?? existingItem.unit_price) * result.quantity,
        //         options: itemData.options || null,
        //         accessories: itemData.accessories || null
        //     };
        // } else {
        //     // add new item
        //     pinboard.pinboard_items.push({
        //         pinboard_item_id: result.pinboard_item_id,
        //         model_id: itemData.id,
        //         model_type: itemData.model,
        //         title: itemData.title || '',
        //         description: itemData.description || '',
        //         photo: itemData.image || null,
        //         quantity: result.quantity,
        //         unit_price: result.unit_price ?? 0,
        //         total_price: (result.unit_price ?? 0) * result.quantity,
        //         options: itemData.options || null,
        //         accessories: itemData.accessories || null
        //     });
        // }
    
        // localStorage.setItem(this.localKey, JSON.stringify(pinboard));
        // this._emitPinboardUpdated(pinboard);

        // const pinboardStoreModule = await import('../store/pinboardStore.js');
        // const store = pinboardStoreModule.default;
        // if (store && typeof store.commit === 'function') {
        //     // store expects the full items array for SET_ITEMS
        //     store.commit('SET_ITEMS', pinboard.pinboard_items || []);
        // }
        // return {
        //     data: pinboard.pinboard_items,
        //     message: result.message || 'Pinboard updated successfully'
        // };
    }

    async checkExistingBooking(bookingData) {
        try {
            const url = `${this.baseUrl}/check-existing-booking`;
            const response = await this.post(url, bookingData, {
                'Content-Type': 'application/json'
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} for query: ${bookingData}`);
            }
            const bookingData = await response.json();
            if (!bookingData.success) {
                throw new Error(bookingData.message);
            }
            return bookingData;
        } catch (err) {
            throw new Error(`Error checking existing booking: ${err.message}`);
        }
    }

    /**
     * Virtual Pinboard: product name autocomplete.
     * Currently uses demo data + simulated latency. Swap the implementation for a real API, e.g.:
     *   const url = `${this.baseUrl}/pinboard-products/autocomplete?q=${encodeURIComponent(q)}`;
     *   const response = await this.get(url);
     *   if (!response.ok) throw new Error('Network response was not ok');
     *   const data = await response.json();
     *   return data.items ?? data;
     */
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
            // const productsData = await response.json();
            // const rows = Array.isArray(productsData?.results)
            //     ? productsData.results
            //     : Array.isArray(productsData?.items)
            //         ? productsData.items
            //         : Array.isArray(productsData)
            //             ? productsData
            //             : [];

            // return rows
            //     .map((p, index) => {
            //         const referenceText = String(p?.reference || '');
            //         const referenceMatch = referenceText.match(/(\d+)/);
            //         const normalizedId = Number(
            //             p?.id ||
            //             p?.product_id ||
            //             p?.model_id ||
            //             referenceMatch?.[1] ||
            //             index + 1
            //         );

            //         return {
            //             id: Number.isFinite(normalizedId) ? normalizedId : index + 1,
            //             title: p?.title || '',
            //             sku: p?.sku || p?.reference || '',
            //             photo: p?.photo || p?.dataSrc || p?.dataBgSrc || '',
            //             _searchHay: `${p?.title || ''} ${p?.sku || ''} ${p?.reference || ''} ${p?.id || ''}`.toLowerCase(),
            //         };
            //     })
            //     .filter((p) => p.title && p._searchHay.includes(q))
            //     .slice(0, 10)
            //     .map(({ _searchHay, ...rest }) => rest);
        } catch (error) {
            console.error('Error getting search results:', error);
            throw error;
        }



        // const q = (query || '').trim().toLowerCase();
        // await new Promise((resolve) => setTimeout(resolve, 120));
        // if (!q) {
        //     return [];
        // }
        // return DEMO_PINBOARD_AUTOCOMPLETE_PRODUCTS.filter((p) => {
        //     const hay = `${p.title} ${p.sku || ''} ${p.id}`.toLowerCase();
        //     return hay.includes(q);
        // }).slice(0, 10);
    }

    /**
     * Maps an autocomplete row to the payload expected by the pinboard store `addToPinboard` action.
     * @param {{ id: number, title: string, sku?: string, photo: string }} product
     */
    toAddPinboardPayloadFromAutocomplete(product) {
        return {
            model_id: product.id,
            model_type: 'product',
            title: product.title,
            photo: product.photo,
            quantity: 1,
            unit_price: 0,
            description: product.sku ? `Demo · ${product.sku}` : '',
            language_id: 1,
            comments: [],
        };
    }

    async updatePinboardVisibility(pinboardId, isVisible) {
        try {
            const response = await fetch(`${this.baseUrl}/account-pinboard/update-visibility`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ pinboard_id: pinboardId, is_visible: isVisible }),
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} for query: ${pinboardId}`);
            }
            const apiData = await response.json();
            return apiData ?? {};
        } catch (err) {
            throw new Error(`Error updating pinboard visibility: ${err.message}`);
        }
    }

    async submitProjectSubmission(payload) {
        try {
            const response = await fetch(`${this.baseUrl}/account-pinboard/project-submission`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} for query: ${pinboardId}`);
            }
            const apiData = await response.json();
            return apiData ?? {};
        } catch (err) {
            throw new Error(`Error submitting project submission: ${err.message}`);
        }
    }
}
export default new PinboardService();
