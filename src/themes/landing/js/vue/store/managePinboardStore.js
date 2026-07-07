import authService from '../services/authService.js';
import pinboardService from '../services/pinboardService.js';
import { Pinboard, PinboardItem, PinboardItemImage } from '../models/Pinboard.js';
import managePinboardService from '../services/managePinboardService.js';
import FeedbackHandler from '../models/FeedbackHandler.js';

export default new Vuex.Store({
    state: {
        error: null,
        actionLoading: false,
        success: false,
        projectLists: [],
        pinboard: new Pinboard(),
        fb: new FeedbackHandler(),
        commentFiles: [],
        loggedInUser: null,
        projectItems: [],
        autocompleteSuggestions: [],
        autocompleteOpen: false,
        bookedData: [],
        nearestShowroom: {},
        showrooms: [],
    },

    mutations: {
        SET_ACTION_LOADING(state, val) {
            state.actionLoading = val;
        },
        SET_SUCCESS(state, success) {
            state.success = success;
        },
        SET_PROJECT_LISTS(state, projectLists) {
            state.projectLists = projectLists;
        },
        async SET_PINBOARD(state, pinboardData){
            state.pinboard = new Pinboard(pinboardData);
            if (!Array.isArray(state.pinboard.pinboard_items)) {
                state.pinboard.pinboard_items = [];
            }
        },
        SET_PINBOARD_ITEMS(state, pinboardItems){
            state.pinboard.pinboard_items = pinboardItems;
        },
        ADD_PINBOARD_ITEM(state, item){
            item = new PinboardItem(item);
            state.pinboard.pinboard_items.push(item);
        },
        ADD_PINBOARD_IMAGE(state, image){
            if (!Array.isArray(state.pinboard.item_images)) {
                state.pinboard.item_images = [];
            }
            state.pinboard.item_images.push(new PinboardItemImage(image));
        },
        REMOVE_PINBOARD_ITEM(state, {index, pinboardItem}){
            if(!(typeof index === 'number' && index > -1)){
                if(pinboardItem.pinboard_item_id){
                    index = state.pinboard.pinboard_items.findIndex(item => item.pinboard_item_id === pinboardItem.pinboard_item_id);
                }else{
                    index = state.pinboard.pinboard_items.findIndex(item => item.model_id === pinboardItem.model_id && item.model_type === pinboardItem.model_type);
                }
            }
            if(index !== -1) {
                state.pinboard.pinboard_items.splice(index, 1);
            }
        },
        UPDATE_PINBOARD_ITEM_QUANTITY(state, {index, quantity}){
            if(index !== -1) {
                const item = state.pinboard.pinboard_items[index];
                item.quantity = quantity;
                state.pinboard.pinboard_items.splice(index, 1, item);
            }
        },
        ADD_PINBOARD_ITEM_COMMENT(state, {index, model_id, model_type, comment}){
            if(!(typeof index === 'number' && index > -1)){
                index = state.pinboard.pinboard_items
                .findIndex(item => item.model_id === model_id && item.model_type === model_type);
            }
            
            if(index !== -1) {
                const item = state.pinboard.pinboard_items[index];
                // Ensure comments is an array before pushing to avoid runtime errors
                item.comments = Array.isArray(item.comments) ? item.comments : [];
                item.comments.push(comment);
                state.pinboard.pinboard_items.splice(index, 1, item);
            }
        },
        UPDATE_PINBOARD_ITEM_DESCRIPTION(state, {index, model_id, model_type, description}){
            if(!(typeof index === 'number' && index > -1)){
                index = state.pinboard.pinboard_items
                .findIndex(item => item.model_id === model_id && item.model_type === model_type);
            }
            
            if(index !== -1) {
                const item = state.pinboard.pinboard_items[index];
                item.description = description;
                state.pinboard.pinboard_items.splice(index, 1, item);
            }
        },
        async SET_LOGGED_IN_USER(state){
            const authDetails = await authService.getUserAuthentication();
            state.loggedInUser = authDetails?.user || null;
        },
        SET_PROJECT_ITEMS(state, items){
            state.projectItems = Array.isArray(items) ? items : [];
        },
        SET_AUTOCOMPLETE_SUGGESTIONS(state, suggestions){
            state.autocompleteSuggestions = Array.isArray(suggestions) ? suggestions : [];
        },
        SET_AUTOCOMPLETE_OPEN(state, open){
            state.autocompleteOpen = Boolean(open);
        },
        ADD_COMMENT_FILES(state, fileObj){
            state.commentFiles.push(fileObj);
        },
        REMOVE_COMMENT_ITEM_IMAGE(state, { file, index }) {
            let removeIndex = index ?? -1;
            if (removeIndex === -1 && file) {
                removeIndex = state.commentFiles.findIndex(f =>
                    f.name === file.name &&
                    f.size === file.size &&
                    f.type === file.type
                );
            }
            if (removeIndex !== -1) {
                state.commentFiles.splice(removeIndex, 1);
            }
        },
        SET_ERROR(state, payload){
            if (payload && typeof payload === 'object' && payload.key) {
                state.fb.errors = {
                    ...state.fb.errors,
                    [payload.key]: payload.error
                };
                state.error = payload.error;
                return;
            }
            state.error = payload;
        },
        CLEAR_ERROR(state, key){
            if (!key) return;
            const { [key]: removed, ...rest } = state.fb.errors;
            state.fb.errors = rest;
        },
        REMOVE_SUCCESS(state, key){
            if (!key) return;
            const { [key]: removed, ...rest } = state.fb.success;
            state.fb.success = rest;
        },
        START_LOADING(state, key){
            if (!key) return;
            state.fb.loading = {
                ...state.fb.loading,
                [key]: true
            };
        },
        FINISH_LOADING(state, key){
            if (!key) return;
            const { [key]: removed, ...rest } = state.fb.loading;
            state.fb.loading = rest;
        },
        SHOW_SUCCESS(state, key){
            if (!key) return;
            state.fb.success = {
                ...state.fb.success,
                [key]: true
            };
        },
        SET_BOOKED_DATA(state, bookedData) {
            state.bookedData = bookedData;
        },
        SET_NEAREST_SHOWROOM(state, nearestShowroom) {
            state.nearestShowroom = nearestShowroom || {};
        },
        UPDATE_PROJECT_ITEM_QUANTITY(state, payload = {}) {
            const pinboardId = payload.pinboard_id ?? state.pinboard?.pinboard_id;
            if (!pinboardId || !Array.isArray(state.projectLists)) return;
        
            const row = state.projectLists.find(
                item => String(item?.pinboard_id) === String(pinboardId)
            );
            if (!row) return;
        
            const changeBy = Number(payload.change_by);
            const amount = Number.isFinite(changeBy) ? changeBy : 1;
            // Format: May 16, 2026
            const date = new Date();
            const formattedDate = date.toLocaleString('en-AU', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                timeZone: 'UTC'
            });
            row.updated_at = formattedDate;
    
        
            switch (payload.status) {
                case 'remove':
                    row.item_count = Math.max(0, (Number(row.item_count) || 0) - amount);
                    break;
        
                case 'set':
                    row.item_count = Math.max(0, amount);
                    break;
        
                case 'add':
                default:
                    row.item_count = (Number(row.item_count) || 0) + amount;
                    break;
            }
        },
        UPDATE_PINBOARD_VISIBILITY_(state, { pinboardId, isVisible }) {
            if (!pinboardId || !Array.isArray(state.projectLists)) return;
        
            const row = state.projectLists.find(
                item => String(item?.pinboard_id) === String(pinboardId)
            );
            if (!row) return;
            row.is_visible = isVisible;
        },
        UPDATE_PINBOARD_VISIBILITY(state, { pinboardId, isVisible }) {
            if (!pinboardId || !Array.isArray(state.projectLists)) return;
        
            const row = state.projectLists.find(
                item => String(item.pinboard_id) === String(pinboardId)
            );
        
            if (!row) return;
        
            row.is_visible = isVisible;
        
            state.projectLists.sort((a, b) => {
                return Number(b.is_visible) - Number(a.is_visible);
            });
        },
        UPDATE_PINBOARD_STATUS(state, { pinboardId }) {
            if (!pinboardId || !Array.isArray(state.projectLists)) return;
            const row = state.projectLists.find(
                item => String(item?.pinboard_id) === String(pinboardId)
            );
            if (!row) return;
            row.pinboard_status_id = 8; // In-discussion
            row.pinboard_status_name = 'In-discussion';
        },
        SET_SHOWROOMS(state, showrooms){
            state.showrooms = showrooms;
        },
        SET_PROJECT_TITLE_UPDATED(state, { pinboardId, pinboardName }){
            const normalizedId = String(pinboardId);
            const nextName = String(pinboardName || '').trim();
            if (!nextName) return;

            state.projectLists = (state.projectLists || []).map((item) =>
                String(item?.pinboard_id) === normalizedId
                    ? { ...item, pinboard_name: nextName }
                    : item
            );

            state.projectItems = (state.projectItems || []).map((item) =>
                String(item?.pinboard_id) === normalizedId
                    ? { ...item, pinboard_name: nextName }
                    : item
            );

            if (state.pinboard && String(state.pinboard.pinboard_id) === normalizedId) {
                state.pinboard.pinboard_name = nextName;
            }
        },

    },

    actions: {
        async getProjectLists({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                if (!userAuthDetails) {
                    commit('SET_ERROR', 'User not authenticated');
                    return { error: 'User not authenticated' };
                }
                const res = await managePinboardService.getProjectList(userAuthDetails.user.user_id);
                const data = res.data.pinboards ?? [];
                commit('SET_PROJECT_LISTS', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                commit('SET_ERROR', 'Get project list failed');
                return { error: e.message || 'Get project list failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },

        async getProjectItemsByPinboardId({ commit, dispatch }, pinboardId) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                if (!userAuthDetails) {
                    commit('SET_ERROR', 'User not authenticated');
                    return { error: 'User not authenticated' };
                }
                const pinboard = await managePinboardService.getProjectByPinboardId(pinboardId, userAuthDetails.user.user_id, true);
                console.log('pinboard = getProjectItemsByPinboardId STORE', pinboard);
                commit('SET_PINBOARD', pinboard);
                await commit('SET_LOGGED_IN_USER');
                await dispatch('projectItems');
                commit('SET_SUCCESS', true);
                return pinboard;
            } catch (e) {
                commit('SET_ERROR', 'Get pinboard by pinboard id failed');
                return { error: e.message || 'Get pinboard by pinboard id failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },
        async getProjectList({ dispatch }, payload = {}) {
            return dispatch('getProjectLists', payload);
        },
        async projectItems({ commit }) {
            const key = 'projectItems';
            commit('CLEAR_ERROR', key);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                if(userAuthDetails && userAuthDetails.customer){
                    const customerId = userAuthDetails.customer.customer_id;
                    const response = await managePinboardService.projectItems(customerId);
                    commit('SET_PROJECT_ITEMS', response?.pinboards || []);
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }
                commit('SET_PROJECT_ITEMS', []);
                return Promise.resolve({ pinboards: [] });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Get project items failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async getProjectByPinboardId({ commit }, pinboardId) {
            const key = 'changeProject';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                let pinboard = {};
                if (userAuthDetails?.user?.user_id) {
                    pinboard = await managePinboardService.getProjectByPinboardId(pinboardId, userAuthDetails.user.user_id, true);
                }
                commit('SET_PINBOARD', pinboard);
                // pinboardService.emitPinboardUpdatedEvent(pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: 'Failed to load pinboard', key});
                return Promise.resolve({ data: null, success: false, error: e?.message || 'Failed to load pinboard' });
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async searchPinboardAutocomplete({ commit }, query) {
            const key = 'searchPinboardAutocomplete';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            if (!query) {
                commit('SET_AUTOCOMPLETE_SUGGESTIONS', []);
                commit('SET_AUTOCOMPLETE_OPEN', false);
                commit('FINISH_LOADING', key);
                return Promise.resolve([]);
            }
            try {
                const response = await managePinboardService.searchPinboardAutocomplete(query);
                const rows = Array.isArray(response?.results)
                    ? response.results
                    : Array.isArray(response?.items)
                        ? response.items
                        : Array.isArray(response)
                            ? response
                            : [];
                commit('SET_AUTOCOMPLETE_SUGGESTIONS', rows);
                commit('SET_AUTOCOMPLETE_OPEN', rows.length > 0);
                return Promise.resolve(rows);
            } catch (e) {
                commit('SET_AUTOCOMPLETE_SUGGESTIONS', []);
                commit('SET_AUTOCOMPLETE_OPEN', false);
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async matchItemWithExistingItem({ state }, itemData) {
            // Pinboard.js camera/reference images share model_id (pinboard id) + model_type `images`; never merge as quantity.
            if (itemData.model_type === 'images') {
                return -1;
            }
            const itemIndex = (state.pinboard?.pinboard_items || []).findIndex((item) => {
                if (item.model_id === itemData.model_id && item.model_type === itemData.model_type) {
                    if (itemData.variant && item.variant) {
                        if (itemData.variant.variant_id === item.variant.variant_id) {
                            if (itemData.options && item.options) {
                                if (JSON.stringify(itemData.options) === JSON.stringify(item.options)) {
                                    return true;
                                }
                            }
                            if (itemData.options) return false;
                            return true;
                        }
                        return false;
                    }
                    if (itemData.variant) return false;
                    return true;
                }
            });
            return itemIndex;
        },
        async addToPinboard_old({ state, commit, dispatch }, itemData) {
            itemData = new PinboardItem(itemData);
            const key = 'addToPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const itemIndex = await dispatch('matchItemWithExistingItem', itemData);
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                let item = null;
                
                if(itemIndex !== -1) {
                    state.pinboard.pinboard_items[itemIndex].quantity += 1;
                    item = state.pinboard.pinboard_items[itemIndex];
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await managePinboardService.updatePinboardItem(state.pinboard, item);
                        //Verify that item and pinboardItem are the same
                    }
                }else{
                    commit('ADD_PINBOARD_ITEM', itemData);
                    item = itemData;
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await managePinboardService.addToPinboard(state.pinboard, item);
                        //Verify that item and pinboardItem are the same
                    }
                }
                commit('UPDATE_PROJECT_ITEM_QUANTITY', { pinboard_id: state.pinboard?.pinboard_id, status: 'add', change_by: 1 });
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: state.pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Add failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async addToPinboard({ state, commit, dispatch }, itemData) {
            itemData = new PinboardItem(itemData);
            const key = 'addToPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const itemIndex = await dispatch('matchItemWithExistingItem', itemData);
                const isLoggedIn = await authService.getUserAuthentication();
                let item = null;

                if (itemIndex !== -1) {
                    state.pinboard.pinboard_items[itemIndex].quantity += 1;
                    item = state.pinboard.pinboard_items[itemIndex];
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await managePinboardService.updatePinboardItem(state.pinboard, item);
                        //Verify that item and pinboardItem are the same
                    }
                } else {
                    commit('ADD_PINBOARD_ITEM', itemData);
                    item = itemData;
                    if (isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await managePinboardService.addToPinboard(state.pinboard, item);
                        if (item.model_type === 'images') {
                            const imageResponse =
                                pinboardItem?.data?.item_image ||
                                pinboardItem?.data?.itemImage ||
                                pinboardItem?.data ||
                                pinboardItem?.item_image ||
                                pinboardItem?.itemImage ||
                                pinboardItem;
                            if (imageResponse && (imageResponse.photo || imageResponse.image || item.photo)) {
                                commit('ADD_PINBOARD_IMAGE', {
                                    ...imageResponse,
                                    photo: imageResponse.photo || imageResponse.image || item.photo,
                                    description: imageResponse.description || item.description || '',
                                });
                            }
                        }
                    }
                }
                commit('UPDATE_PROJECT_ITEM_QUANTITY', { pinboard_id: state.pinboard?.pinboard_id, status: 'add', change_by: 1 });
                // pinboardService.savePinboardToLocalStorage(state.pinboard);
                // pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: state.pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Add failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async removePinboardItem({ commit, state }, { pinboardItem, index }) {
            const key = 'removePinboardItem_'+index;
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id && pinboardItem.pinboard_item_id) {
                    await managePinboardService.removePinboardItem(pinboardItem.pinboard_item_id);
                }
                commit('REMOVE_PINBOARD_ITEM', {index, pinboardItem});
                commit('UPDATE_PROJECT_ITEM_QUANTITY', { pinboard_id: state.pinboard?.pinboard_id, status: 'remove', change_by: 1 });
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Remove failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async reorderPinboardItems({ commit, state }, items) {
            const key = 'reorderPinboardItems';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            const normalizedItems = Array.isArray(items)
                ? items
                : (Array.isArray(items?.items) ? items.items : []);
            try {
                // lastOrderedItems = [...state.pinboard.pinboard_items];
                commit('SET_PINBOARD_ITEMS', normalizedItems);
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await managePinboardService.reorderPinboardItems(state.pinboard, normalizedItems);
                }
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Reorder failed', key});
                commit('SET_PINBOARD_ITEMS', normalizedItems);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async updatePinboardItemQuantity({ commit, state }, payload) {
            const key = 'updatePinboardItemQuantity_'+payload.model_id+'_'+payload.model_type;
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.isLoggedIn();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await managePinboardService.updatePinboardItemQuantity(payload);
                }
                commit('UPDATE_PINBOARD_ITEM_QUANTITY', payload);
                // pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Update failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },

        async addPinboardItemComment({ commit, state }, {pinboard_item_id, comment, index}) {
            const key = 'addPinboardItemComment_'+pinboard_item_id??index;
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await managePinboardService.addPinboardItemComment(pinboard_item_id, comment);
                }
                commit('ADD_PINBOARD_ITEM_COMMENT', {index, comment});
                commit('UPDATE_PROJECT_ITEM_QUANTITY', { pinboard_id: state.pinboard?.pinboard_id, status: 'update', change_by: 0 });
                // pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Update failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async updatePinboardItemDescription({ commit, state }, payload) {
            const key = 'updateDescription_'+payload.model_id+'_'+payload.model_type;
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await managePinboardService.updatePinboardItemDescription(payload);
                }
                commit('UPDATE_PINBOARD_ITEM_DESCRIPTION', payload);
                // pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Update failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async uploadCommentItemImage({ commit, state }, {file, objectURL}) {
            const key = 'uploadCommentItemImage';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                if (!file) {
                    commit('SET_ERROR', {error: 'No file provided for upload', key});
                }

                const generatePhpTmpName = () => {
                    const rand = Math.random().toString(36).slice(2, 12);
                    const timePart = Date.now().toString(36);
                    return `/tmp/php${rand}${timePart}`;
                };

                const fileObj = {
                    name: file.name || '',
                    size: file.size || 0,
                    type: file.type || '',
                    objectURL: objectURL || null,
                    tmp_name: generatePhpTmpName(),
                    _file: file
                };

                Object.defineProperty(fileObj, '_file', {
                    value: file,
                    enumerable: false,
                    writable: false,
                    configurable: true
                });

                // duplicate file object
                const exists = state.commentFiles.some(f =>
                    f.name === fileObj.name &&
                    f.size === fileObj.size &&
                    f.type === fileObj.type
                );
        
                if (exists) {
                    console.warn('Duplicate file ignored:', fileObj.name);
                    return;
                }
               
                console.log('uploadImage fileObj=', fileObj);
                commit('ADD_COMMENT_FILES', fileObj);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Upload failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async removeCommentItemImage({ commit, state }, {file, index}) {
            const key = 'removeCommentItemImage_'+index;
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                commit('REMOVE_COMMENT_ITEM_IMAGE', { file, index });
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Remove failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async addCommentItemToPinboard({ commit, state }, comment) {
            const key = 'addCommentItemToPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const authDetails = await authService.getUserAuthentication();
                const formData = new FormData();
                formData.append('content', comment);

                if (Array.isArray(state.commentFiles) && state.commentFiles.length > 0) {
                    state.commentFiles.forEach((fileObj, idx) => {
                        // fileObj may contain the original file under `_file` (created in uploadImage)
                        const file = fileObj && (fileObj._file || fileObj.file || fileObj);
                        if (file) {
                            // Provide a filename when possible
                            const filename = file.name || fileObj.name || `file${idx}`;
                            formData.append(idx.toString(), file, filename);
                            // formData.append('files', file, filename);
                        }
                    });
                }
                console.log('state.pinboard.pinboard_id=', state.pinboard.pinboard_id);
                if (!state.pinboard.pinboard_id) {
                    const missingPinboardError = 'Pinboard is not selected.';
                    commit('SET_ERROR', { error: missingPinboardError, key });
                    return { data: null, success: false, error: missingPinboardError };
                }
                if(state.pinboard.pinboard_id) {
                    formData.append('pinboard_id', state.pinboard.pinboard_id);
                    formData.append('user_id', authDetails.user.user_id);
                    formData.append('author', authDetails.user.first_name);
                    const result = await managePinboardService.addCommentItemToPinboard(formData);
                    console.log('result=', result);
                    if(result){
                        commit('ADD_PINBOARD_ITEM', result);
                        commit('UPDATE_PROJECT_ITEM_QUANTITY', { pinboard_id: state.pinboard?.pinboard_id, status: 'add', change_by: 1 });
                        // commentFiles should be cleared
                        state.commentFiles = [];
                        commit('SHOW_SUCCESS', key);
                        return { data: result, success: true, error: null };
                    }
                    const errorMessage = result?.error || 'Submit comment failed';
                    commit('SET_ERROR', { error: errorMessage, key });
                    return { data: null, success: false, error: errorMessage };
                }
            } catch (e) {
                const statusCode = e?.response?.status || e?.status;
                const errorMessage = statusCode === 413
                    ? 'Uploaded file is too large. Please keep each image within 5MB.'
                    : (e?.message || 'Submit comment failed');
                commit('SET_ERROR', { error: errorMessage, key });
                return { data: null, success: false, error: errorMessage };
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async createNewProject({ commit, state }, payload) {
            const key = 'createNewProject';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.isLoggedIn();
                if(!isLoggedIn){
                    commit('SET_ERROR', {error: 'User not logged in', key});
                    return Promise.reject({ data: null, success: false, error: 'User not logged in' });
                }
                const pinboard = await managePinboardService.createNewProject(payload);
                await commit('SET_PINBOARD', pinboard);
                const newProjectPinboard = {
                    pinboard_id: pinboard.pinboard_id,
                    user_id: pinboard.user_id,
                    customer_id: pinboard.customer_id,
                    pinboard_status_id: pinboard.pinboard_status_id,
                    is_active: 1,
                    pinboard_name: payload.job_title,
                    customer_name: pinboard.customer_name || '',
                    customer_email: pinboard.customer_email || '',
                    total_price: 0,
                    item_count: 0
                };
                await commit('SET_PROJECT_ITEMS', [...state.projectItems, newProjectPinboard]);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async getNearestShowroom({ commit }) {
            const key = 'getNearestShowroom';
            commit('CLEAR_ERROR', key);
            try {
                const res = await pinboardService.getNearestShowroom();
                if (res && res.data && res.data.nearest_showroom) {
                    commit('SET_NEAREST_SHOWROOM', res.data.nearest_showroom);
                    commit('SET_SHOWROOMS', res.data.all_showrooms || []);
                    return Promise.resolve({ data: res.data, success: true, error: null });
                }
                const errMsg =
                    (res && (res.error || res.message)) || 'Get nearest showroom failed';
                commit('SET_ERROR', { error: errMsg, key });
                return Promise.resolve({ data: null, success: false, error: errMsg });
            } catch (e) {
                const errMsg =
                    e && e.message ? e.message : 'Get nearest showroom failed';
                commit('SET_ERROR', { error: errMsg, key });
                return Promise.resolve({ success: false, error: errMsg });
            }
        },
        async bookNow({ commit }, bookingData) {
            const key = 'bookNow';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const response = await pinboardService.bookNow(bookingData);
                if (response && response.success) {
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }
                commit('SET_ERROR', { error: response.error || 'Book now failed', key });
                return Promise.resolve(response);
            } catch (e) {
                commit('SET_ERROR', { error: e.message || 'Book now failed', key });
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async getBookedData({ commit, state }, bookingArgs = []) {
            const key = 'getBookedData';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            const [date, tourType = 'physicalTour'] = Array.isArray(bookingArgs)
                ? bookingArgs
                : [bookingArgs, 'physicalTour'];
            try {
                const ns = state.nearestShowroom || {};
                const showroomId =
                    ns.showroom_id ?? ns.showrooms_id ?? ns.id ?? 1;
                const response = await pinboardService.getBookedData(date, showroomId, tourType);
                if (response && response.success) {
                    commit('SET_BOOKED_DATA', response.data);
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }
                commit('SET_ERROR', { error: response.error || 'Get booked data failed', key });
                return Promise.reject(response);
            } catch (e) {
                commit('SET_ERROR', { error: e.message || 'Get booked data failed', key });
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async updatePinboardVisibility({ commit, state }, { pinboardId, isVisible }) {
            const key = 'updatePinboardVisibility';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.getUserAuthentication();
                if(!isLoggedIn){
                    commit('SET_ERROR', {error: 'User not logged in', key});
                    return Promise.reject({ data: null, success: false, error: 'User not logged in' });
                }
                const result = await pinboardService.updatePinboardVisibility(pinboardId, isVisible);
                if(result && result.success){
                    commit('UPDATE_PINBOARD_VISIBILITY', { pinboardId, isVisible });
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(result);
                }
                commit('SET_ERROR', {error: result?.error || 'Update visibility failed', key});
                return Promise.reject(result);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Update visibility failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async updateProjectTitle({ commit, state, dispatch }, payload) {
            const key = 'updateProjectTitle';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const response = await pinboardService.updateProjectTitle(payload);
                if(response && response.success){
                    commit('SET_PROJECT_TITLE_UPDATED', {
                        pinboardId: payload.pinboard_id,
                        pinboardName: payload.pinboard_name
                    });
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Update project title failed', key});
                    return Promise.reject(response);
                }
                // projectMenuItems change

            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Update project title failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },


        async submitProjectSubmission({ commit, state }, payload) {
            const key = 'submitProjectSubmission';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const isLoggedIn = await authService.getUserAuthentication();
                if(!isLoggedIn){
                    commit('SET_ERROR', {error: 'User not logged in', key});
                    return Promise.reject({ data: null, success: false, error: 'User not logged in' });
                }
                const response = await pinboardService.submitProjectSubmission(payload);
                if(response && response.success){
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Submit project submission failed', key});
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Submit project submission failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
    },

    getters: {
        error: s => s.error,
        actionLoading: s => s.actionLoading,
        success: s => s.success,
        projectLists: s => s.projectLists,
        pinboard: s => s.pinboard,
        pinboardItems: s => s.pinboard?.pinboard_items || [],
        fb: s => s.fb,
        commentFiles: s => s.commentFiles,
        loggedInUser: s => s.loggedInUser,
        projectItems: s => s.projectItems,
        autocompleteSuggestions: s => s.autocompleteSuggestions,
        autocompleteOpen: s => s.autocompleteOpen,
        bookedData: s => s.bookedData,
        nearestShowroom: s => s.nearestShowroom,
        showrooms: s => s.showrooms,
    }
});
