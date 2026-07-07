import { Pinboard, PinboardItem, PinboardItemImage } from '../models/Pinboard.js';
import pinboardService from '../services/pinboardService.js';
import authService from '../services/authService.js';
import FeedbackHandler from '../models/FeedbackHandler.js';
import Customer from '../models/Customer.js';
export default new Vuex.Store({
    state: {
        pinboard: new Pinboard(),
        bookedData: [],
        customer:{
            name: '',
            projectName: '',
            companyName: '',
            email: '',
            phone: '',
            otp: '',
        },
        showrooms: [],
        showroomsData: [],
        nearestShowroom: [],
        commentFiles:[],       
        loggedInUser: false,
        isUserVerified: false,
        fb: new FeedbackHandler(),
        pinboardCheckIntervals:{},
        projectItems:[],
        // pinboard autocomplete
        autocompleteSuggestions: [],
        autocompleteOpen: false,
    },

    mutations: {
        async SET_LOGGED_IN_USER(state){
            const authDetails = await authService.getUserAuthentication();
            state.loggedInUser = authDetails?.user || null;
        },
        async SET_CUSTOMER(state, customer){
            if (customer && typeof customer === 'object') {
                state.customer = {
                    ...(state.customer || {}),
                    ...customer,
                };
                await authService.setCustomer(state.customer);
                return;
            }
            const userAuthDetails = await authService.getUserAuthentication();
            state.customer = { ...(userAuthDetails?.customer || {}) };
        },
        async SET_PINBOARD(state, pinboardData){
            state.pinboard = new Pinboard(pinboardData);
            await pinboardService.savePinboardToLocalStorage(state.pinboard);
        },
        SET_PINBOARD_ITEMS(state, pinboardItems){
            state.pinboard.pinboard_items = pinboardItems;
        },
        ADD_PINBOARD_ITEM(state, item){
            item = new PinboardItem(item);
            state.pinboard.pinboard_items.push(item);
        },
        MERGE_PINBOARD_ITEM_SERVER_FIELDS(state, { index, pinboard_item_id, quantity }) {
            if (typeof index !== 'number' || index < 0 || index >= state.pinboard.pinboard_items.length) {
                return;
            }
            const row = state.pinboard.pinboard_items[index];
            if (pinboard_item_id != null) {
                row.pinboard_item_id = pinboard_item_id;
            }
            if (quantity != null) {
                row.quantity = quantity;
            }
            state.pinboard.pinboard_items.splice(index, 1, row);
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
        SET_PINBOARD_CHECK_INTERVALS(state, {type, intervalId}){
            // Replace the entire object to ensure Vue reactivity
            state.pinboardCheckIntervals = {
                ...state.pinboardCheckIntervals,
                [type]: intervalId
            };
        },
        DELETE_PINBOARD_CHECK_INTERVAL(state, type){
            // Create a new object without the key to ensure Vue reactivity
            const { [type]: removed, ...rest } = state.pinboardCheckIntervals;
            state.pinboardCheckIntervals = rest;
        },
        SET_IS_USER_VERIFIED(state, isVerified){
            state.isUserVerified = isVerified;
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
        SET_SHOWROOMS(state, showrooms){
            state.showrooms = showrooms;
        },

        //Error handling
        SET_ERROR(state, payload){
            if (!payload || typeof payload !== 'object' || payload.key == null || payload.key === '') {
                return;
            }
            const { error, key } = payload;
            // Replace the entire errors object to ensure Vue reactivity
            state.fb.errors = {
                ...state.fb.errors,
                [key]: error
            };
            console.log(state.fb.errors);
        },
        CLEAR_ERROR(state, key){
            // Create a new errors object without the key to ensure Vue reactivity
            const { [key]: removed, ...rest } = state.fb.errors;
            state.fb.errors = rest;
        },
        REMOVE_SUCCESS(state, key){
            // Create a new success object without the key to ensure Vue reactivity
            const { [key]: removed, ...rest } = state.fb.success;
            state.fb.success = rest;
        },
        START_LOADING(state, key){
            state.fb.loading = {
                ...state.fb.loading,
                [key]: true
            };
        },
        SHOW_SUCCESS(state, key){
            // Replace the entire success object to ensure Vue reactivity
            state.fb.success = {
                ...state.fb.success,
                [key]: true
            };
        },
        FINISH_LOADING(state, key){
            // Create a new loading object without the key to ensure Vue reactivity
            const { [key]: removed, ...rest } = state.fb.loading;
            state.fb.loading = rest;
        },
        SET_BOOKED_DATA(state, bookedData){
            state.bookedData = bookedData;
        },
        SET_NEAREST_SHOWROOM(state, nearestShowroom){
            state.nearestShowroom = nearestShowroom;
        },
        SET_SHOWROOMS_DATA(state, showroomsData){
            state.showroomsData = showroomsData;
        },
        SET_PROJECT_ITEMS(state, projectItems){
            state.projectItems = projectItems;
        },
        // pinboard autocomplete
        SET_AUTOCOMPLETE_SUGGESTIONS(state, suggestions){
            state.autocompleteSuggestions = suggestions;
        },
        SET_AUTOCOMPLETE_OPEN(state, open){
            state.autocompleteOpen = open;
        },
        SET_PROJECT_TITLE_UPDATED(state, { pinboardId, pinboardName }){
            state.projectItems = state.projectItems.map(item =>
                item.pinboard_id === pinboardId ? { ...item, pinboard_name: pinboardName } : item
            );
        },
    },

    actions: {
        async matchItemWithExistingItem({state}, itemData) {
            //Check if item matched with existing item then increase and update pinboard
            const itemIndex = (state.pinboard?.pinboard_items||[]).findIndex(item => {
                if(item.model_id === itemData.id && item.model_type === itemData.model) {
                    if(itemData.variant && item.variant) {
                        if(itemData.variant.variant_id === item.variant.variant_id) {
                            if(itemData.options && item.options) {
                                if(JSON.stringify(itemData.options) === JSON.stringify(item.options)) {
                                    return true;
                                }
                            }
                            if(itemData.options) return false;
                            return true;
                        }
                        return false;
                    }
                    if(itemData.variant) return false;
                    return true;
                }
            });
            return itemIndex;
        },
        async setCustomer({ commit, state }, customer) {
            if (customer && typeof customer === 'object') {
                const customerModel = {
                    ...(state.customer || {}),
                    ...customer,
                };
                await commit('SET_CUSTOMER', customerModel);
                return;
            }
            await commit('SET_CUSTOMER', customer);
        },
        async setPinboard({ commit, state }, pinboard) {
            if (pinboard && typeof pinboard === 'object') {
                const pinboardModel = {
                    ...(state.pinboard || {}),
                    ...pinboard,
                };
                await commit('SET_PINBOARD', pinboardModel);
                return;
            }
            await commit('SET_PINBOARD', pinboard);
        },
        async checkExistingCustomer({ commit, state }, email) {
            const key = 'createPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                let res = await authService.checkExistingCustomer(email);
                if(res && res.success) {
                    let customer = new Customer(res.data);
                    authService.setUserAuthentication({customer});
                    commit('SET_CUSTOMER', customer);
                    const response = await authService.sendEmailVerification(email);
                    if(response && response.success) {
                        customer = {...state.customer, ...response.customer};
                        authService.setUserAuthentication({customer});
                        commit('SET_CUSTOMER', customer);
                    }else{
                        commit('SET_ERROR', {error: response.error || 'Send email verification failed', key});
                        return Promise.reject(response);
                    }
                    return Promise.resolve({ data: customer, success: true, error: null });
                }
                return Promise.resolve({ data: null, success: false, error: 'Customer not found' });
            } catch (e) {
                const errMsg = e && e.message ? e.message : 'Check existing customer failed';
                commit('SET_ERROR', {error: errMsg, key});
                return Promise.resolve({ data: null, success: false, error: errMsg });
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async registerCustomer({ commit, state, dispatch }, customer) {
            const key = 'registerCustomer';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                customer = {...state.customer, ...customer};
                await authService.registerCustomer(customer);
                await commit('SET_CUSTOMER');
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Register failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async verifyEmail({ commit, state }, {email, otp}) {
            const key = 'verifyEmail';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                await authService.verifyEmail(email, otp);
                await commit('SET_LOGGED_IN_USER');
                await commit('SET_CUSTOMER');
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: true, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Verify email failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async createTemporayPinboard({ commit, state }) {
            const key = 'createPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                if(state.pinboard.pinboard_temp_id) {
                    return Promise.resolve({ data: state.pinboard, success: true, error: 'Pinboard already created' });
                }
                state.pinboard.customer_email = state.customer.email;
                state.pinboard.pinboard_name = state.customer.projectName;
                const pinboard = await pinboardService.createTemporayPinboard(state.pinboard);
                // console.log("nazmul create", pinboard);
                
                await commit('SET_PINBOARD', pinboard);
                // pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async verifyEmailAthenticateAndCreatePinboard({ commit, state }, otp) {
            const key = 'createPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const result = await authService.verifyEmailAthenticateAndCreatePinboard(state.customer.email, otp, state.pinboard);
                const pinboard = new Pinboard(result.pinboard?.data||{});
                await commit('SET_PINBOARD', pinboard);
                const customer = new Customer(result.customer || result.customer);
                await commit('SET_CUSTOMER', customer);
                await commit('SET_LOGGED_IN_USER');
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },

        async createPinboard({ commit, state }) {
            const key = 'createPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                //Customer needs to logged in 
                const isLoggedIn = await authService.isLoggedIn();
                if(state.pinboard.pinboard_id) {
                    commit('SET_ERROR', {error: 'Pinboard already created', key});
                    return Promise.resolve({ data: null, success: false, error: 'Pinboard already created' });
                }

                state.pinboard.customer_email = state.customer.email;
                state.pinboard.pinboard_name = state.customer.projectName;
                state.pinboard.name = state.customer.name;
                state.pinboard.company_name = state.customer.companyName;
                const pinboard = await pinboardService.createPinboard(state.pinboard);
                await commit('SET_PINBOARD', pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async updatePinboard({ commit, state }, pinboard) {
            const key = 'updatePinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                //Customer needs to logged in 
                const isLoggedIn = await authService.isLoggedIn();
               
                const updatedPinboard = await pinboardService.updatePinboard(pinboard);
                await commit('SET_PINBOARD', updatedPinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: updatedPinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async getPinboard({ commit, state, dispatch }) {
            await commit('SET_LOGGED_IN_USER');
            const key = 'getPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                await commit('SET_CUSTOMER');
                let userAuthDetails = await authService.getUserAuthentication();
                let pinboard = await pinboardService.getPinboardForGuest();
                // console.log('pinboard getPinboardForGuest=', pinboard);
                if(userAuthDetails && userAuthDetails.user?.user_id){
                    pinboard = await pinboardService.getPinboardForLoggedInUser(userAuthDetails.user.user_id);
                    // console.log('pinboard getPinboardForLoggedInUser=', pinboard);
                    pinboard.pinboard_items = [...(pinboard.pinboardItems || pinboard.pinboard_items || [])]
                }
                await commit('SET_PINBOARD', pinboard);

                const nearestShowroom = await pinboardService.getNearestShowroom();
                await commit('SET_SHOWROOMS_DATA', nearestShowroom.data|| []);
                await commit('SET_NEAREST_SHOWROOM', nearestShowroom.data.nearest_showroom || []);

                pinboardService.emitPinboardUpdatedEvent(pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {
                    error: (e && e.message) || 'Failed to load pinboard',
                    key,
                });
                if (e && (e.status === 401 || (e.response && e.response.status === 401))) {
                    // Unauthorized, trigger logout
                    await authService.logout();
                }        
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async addToPinboard_old({ state, commit, dispatch }, itemData) {
            // console.log('addToPinboard itemData from productConfiguratorStore =', itemData);
            itemData = new PinboardItem(itemData);
            const key = 'addToPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const itemIndex = await dispatch('matchItemWithExistingItem', itemData);
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                // console.log('isLoggedIn=', isLoggedIn);
                let item = null;
                
                if(itemIndex !== -1) {
                    state.pinboard.pinboard_items[itemIndex].quantity += 1;
                    item = state.pinboard.pinboard_items[itemIndex];
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await pinboardService.updatePinboardItem(state.pinboard, item);
                        //Verify that item and pinboardItem are the same
                    }
                }else{
                    commit('ADD_PINBOARD_ITEM', itemData);
                    item = itemData;
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await pinboardService.addToPinboard(state.pinboard, item);
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
                //Save pinboard to local storage
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: state.pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Add failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async addToPinboard({ state, commit, dispatch }, itemData) {
            // console.log('addToPinboard itemData from productConfiguratorStore =', itemData);
            itemData = new PinboardItem(itemData);
            const key = 'addToPinboard';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const itemIndex = await dispatch('matchItemWithExistingItem', itemData);
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                // console.log('isLoggedIn=', isLoggedIn);
                let item = null;
                
                if(itemIndex !== -1) {
                    state.pinboard.pinboard_items[itemIndex].quantity += 1;
                    item = state.pinboard.pinboard_items[itemIndex];
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await pinboardService.updatePinboardItem(state.pinboard, item);
                        const apiPayload = pinboardItem?.data ?? pinboardItem;
                        const serverItemId = apiPayload?.pinboard_item_id ?? pinboardItem?.pinboard_item_id;
                        const serverQty = apiPayload?.quantity;
                        if (serverItemId != null || serverQty != null) {
                            commit('MERGE_PINBOARD_ITEM_SERVER_FIELDS', {
                                index: itemIndex,
                                pinboard_item_id: serverItemId,
                                quantity: serverQty,
                            });
                        }
                    }
                }else{
                    commit('ADD_PINBOARD_ITEM', itemData);
                    item = itemData;
                    if(isLoggedIn && state.pinboard.pinboard_id) {
                        const pinboardItem = await pinboardService.addToPinboard(state.pinboard, item);
                        const apiPayload = pinboardItem?.data ?? pinboardItem;
                        const serverItemId = apiPayload?.pinboard_item_id ?? pinboardItem?.pinboard_item_id;
                        const serverQty = apiPayload?.quantity;
                        const newItemIndex = state.pinboard.pinboard_items.length - 1;
                        if (newItemIndex >= 0 && (serverItemId != null || serverQty != null)) {
                            commit('MERGE_PINBOARD_ITEM_SERVER_FIELDS', {
                                index: newItemIndex,
                                pinboard_item_id: serverItemId,
                                quantity: serverQty,
                            });
                        }
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
                //Save pinboard to local storage
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
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
                    await pinboardService.removePinboardItem(pinboardItem.pinboard_item_id);
                }
                commit('REMOVE_PINBOARD_ITEM', {index, pinboardItem});
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
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
                    await pinboardService.reorderPinboardItems(state.pinboard, normalizedItems);
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
                    await pinboardService.updatePinboardItemQuantity(payload);
                }
                commit('UPDATE_PINBOARD_ITEM_QUANTITY', payload);
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
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
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await pinboardService.addPinboardItemComment(pinboard_item_id, comment);
                }
                commit('ADD_PINBOARD_ITEM_COMMENT', {index, comment});
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
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
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                if(isLoggedIn && state.pinboard.pinboard_id) {
                    await pinboardService.updatePinboardItemDescription(payload);
                }
                commit('UPDATE_PINBOARD_ITEM_DESCRIPTION', payload);
                pinboardService.savePinboardToLocalStorage(state.pinboard);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
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
                const isLoggedIn = await authService.getUserAuthentication();
                const formData = new FormData();
                formData.append('content', comment);

                if (Array.isArray(state.commentFiles) && state.commentFiles.length > 0) {
                    state.commentFiles.forEach((fileObj, idx) => {
                        const file = fileObj && (fileObj._file || fileObj.file || fileObj);
                        if (file) {
                            const filename = file.name || fileObj.name || `file${idx}`;
                            formData.append(idx.toString(), file, filename);
                        }
                    });
                }
                if (!state.pinboard.pinboard_id) {
                    const missingPinboardError = 'Pinboard is not selected.';
                    commit('SET_ERROR', { error: missingPinboardError, key });
                    return { data: null, success: false, error: missingPinboardError };
                }

                formData.append('pinboard_id', state.pinboard.pinboard_id);
                formData.append('user_id', isLoggedIn?.user?.user_id ?? 0);
                formData.append('author', isLoggedIn?.customer?.name ?? '');
                const result = await pinboardService.addCommentItemToPinboard(formData);
                if (result) {
                    commit('ADD_PINBOARD_ITEM', result);
                    state.commentFiles = [];
                    pinboardService.savePinboardToLocalStorage(state.pinboard);
                    pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                    commit('SHOW_SUCCESS', key);
                    return { data: result, success: true, error: null };
                }
                const errMsg = result?.error || 'Submit comment failed';
                commit('SET_ERROR', { error: errMsg, key });
                return { data: null, success: false, error: errMsg };
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
        async bookingShowroomVisit({ commit, state }, bookingData) {
            const key = 'bookingShowroomVisit';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            commit('SET_SUCCESS', false);
            try {
                // get pinboard from store
                const pinboard = state.pinboardData;
                if(pinboard && pinboard.pinboard_id) {
                    bookingData.pinboard_id = pinboard.pinboard_id;
                }
                await pinboardService.bookingShowroomVisit(bookingData);
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Book showroom visit failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async bookingVirtualMeeting({ commit, state }, bookingData) {
            const key = 'bookingVirtualMeeting';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            commit('SET_SUCCESS', false);
            try {
                // get pinboard from store
                const pinboard = state.pinboardData;
                if(pinboard && pinboard.pinboard_id) {
                    bookingData.pinboard_id = pinboard.pinboard_id;
                }
                console.log('bookNow store action bookingData=', bookingData);
                await pinboardService.bookingVirtualMeeting(bookingData);
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Book virtual meeting failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async bookingPhoneCall({ commit, state }, bookingData) {
            const key = 'bookingPhoneCall';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                // get pinboard from store
                const pinboard = state.pinboardData;
                if(pinboard && pinboard.pinboard_id) {
                    bookingData.pinboard_id = pinboard.pinboard_id;
                }

                const response = await pinboardService.bookingPhoneCall(bookingData);
                console.log('bookingPhoneCall response store=', response);
                if(response && response.success){
                    return Promise.resolve(response);
                }else{
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Book phone call failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async bookingByEmail({ commit, state }, bookingData) {
            const key = 'bookingByEmail';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                // get pinboard from store
                const pinboard = state.pinboardData;
                if(pinboard && pinboard.pinboard_id) {
                    bookingData.pinboard_id = pinboard.pinboard_id;
                }
                const response = await pinboardService.bookingByEmail(bookingData);
                console.log('bookingByEmail response store=', response);
                if(response && response.success){
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Book email failed', key});
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Book phone call failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async bookingAutoRedirect({ commit, state }, type){
            const url = `/pinboards/${state.pinboard.pinboard_id}/booking/${type}`;
            const intervalId = setInterval(() => {
                try {
                    console.log('bookingAutoRedirect intervalId=', intervalId);
                    const storedData = localStorage.getItem('pinboard_processed');
                    const pinboardId = state.pinboard.pinboard_id;
                    if (storedData) {
                        const parsedData = JSON.parse(storedData);
                        
                        // Check if the stored data matches our pinboard and method
                        if (parsedData.pinboard_id == pinboardId && 
                            (parsedData.processed_method === type || 
                             parsedData.processed_method === 'phone' && type === 'phone-call')) {
                            
                            // Clear the interval
                            clearInterval(intervalId);
                            commit('DELETE_PINBOARD_CHECK_INTERVAL', type);
                            
                            // Remove the processed data from localStorage
                            localStorage.removeItem('pinboard_processed');
                            
                            // Refresh and redirect to the booking page
                            window.location.href = url;
                        }
                    }
                } catch (err) {
                    console.error('Error checking pinboard_processed:', err);
                }
            }, 1000); // Check every second
            
            // Store the interval ID
            commit('SET_PINBOARD_CHECK_INTERVALS', {type, intervalId});
            
            // Set a timeout to clear the interval after a reasonable time (e.g., 5 minutes)
            setTimeout(() => {
                if (state.pinboardCheckIntervals[type]) {
                    clearInterval(state.pinboardCheckIntervals[type]);
                    commit('DELETE_PINBOARD_CHECK_INTERVAL', type);
                }
            }, 300000); 
            window.open(url, '_blank');
        },
        async getVirtualPinboard({ commit }, payload = {}) {
            const { userId , silent = true } = payload;
            if (!silent) commit('SET_ACTION_LOADING', true);
            try {
                // const service = await import('../services/pinboardService.js');
                const res = await pinboardService.getPinboard(userId);
                const authData = await pinboardService.checkUserLogin();
                if(authData && authData.data) {
                    commit('SET_IS_USER_LOGGED_IN', authData.data ? true : false);
                    commit('SET_USER_DATA', authData.data);
                }else{
                    commit('SET_IS_USER_LOGGED_IN', false);
                    commit('SET_USER_DATA', {});
                }
                commit('SET_ITEMS', res.data.pinboard_items || res.data || []);

            } catch (e) {
                commit('SET_ERROR', { error: e && e.message ? e.message : 'Failed to load pinboard', key: 'getVirtualPinboard' });
            } finally {
                if (!silent) commit('SET_ACTION_LOADING', false);
            }
        },
        async getShowrooms({ commit }, showroomId) {
            commit('SET_ACTION_LOADING', true);
            try {
                // const service = await import('../services/pinboardService.js');
                const res = await pinboardService.getShowrooms(showroomId);
                commit('SET_SHOWROOMS', res.data || []);
            } catch (e) {
                commit('SET_ERROR', { error: e && e.message ? e.message : 'Get showroom failed', key: 'getShowrooms' });
            } finally {
                commit('SET_ACTION_LOADING', false);
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
        async checkUserLogin({ commit, state }) {
            const key = 'checkUserLogin';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                await authService.checkUserLogin();
                await commit('SET_LOGGED_IN_USER');
                commit('SHOW_SUCCESS', key);
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Check user login failed', key});
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async bookNow({ commit, state }, bookingData) {
            const key = 'bookNow';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const response = await pinboardService.bookNow(bookingData);
                // console.log('bookNow response store=', response);
                if(response && response.success){
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Book now failed', key});
                    return Promise.resolve(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Book now failed', key});
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
            console.log("getBookedData date=", date);
            try {
                // const nearestShowroom = state.nearestShowroom;
                // const showroomId = nearestShowroom?.showroom_id ?? 1;
                const ns = state.nearestShowroom || {};
                const showroomId = ns.showroom_id ?? ns.showrooms_id ?? ns.id ?? 1;
                console.log("getBookedData showroomId=", showroomId);
                // date.showroom_id = showroomId;
                // git data from state 
                console.log("getBookedData tourType=", tourType);
                const response = await pinboardService.getBookedData(date, showroomId, tourType);
                console.log("getBookedData response store=", response);
                if(response && response.success){
                    commit('SET_BOOKED_DATA', response.data);
                    commit('SHOW_SUCCESS', key);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Get booked data failed', key});
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Get booked data failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        // project items
        async projectItems({ commit, state }) {
            const key = 'projectItems';
            try {
                let userAuthDetails = await authService.getUserAuthentication();
                if(userAuthDetails && userAuthDetails.customer){
                    const customerId = userAuthDetails.customer.customer_id;
                    const response = await pinboardService.projectItems(customerId);
                    // console.log("get project list store=", response);
                    if(response && response){
                        commit('SET_PROJECT_ITEMS', response.pinboards);
                        commit('SHOW_SUCCESS', key);
                        return Promise.resolve(response);
                    }else{
                        commit('SET_ERROR', {error: response.error || 'Get booked data failed', key});
                        return Promise.reject(response);
                    }
                }else{
                    commit('SET_PROJECT_ITEMS', []);
                }
                
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Get booked data failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async getProjectByPinboardId({ commit, state, dispatch }, pinboardId) {
            const key = 'changeProject';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                let pinboard = {};
                if (userAuthDetails) {
                    const userId = userAuthDetails.user.user_id;
                    pinboard = await pinboardService.getProjectByPinboardId(pinboardId, userId);
                }
                commit('SET_PINBOARD', pinboard);

                pinboardService.emitPinboardUpdatedEvent(pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: 'Failed to load pinboard', key});
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
                return Promise.resolve([]);
            }
            try {
                const response = await pinboardService.searchPinboardAutocomplete(query);
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
                console.error('Pinboard autocomplete search failed', e);
                commit('SET_AUTOCOMPLETE_SUGGESTIONS', []);
                commit('SET_AUTOCOMPLETE_OPEN', false);
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async checkExistingBooking({ commit, state }, bookingData) {
            const key = 'checkExistingBooking';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const response = await pinboardService.checkExistingBooking(bookingData);
                if(response && response.success){
                    const customer = new Customer(response.data);
                    authService.setUserAuthentication({customer});
                    commit('SET_CUSTOMER', customer);
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Check existing booking failed', key});
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Check existing booking failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        // send email verification
        async sendEmailVerification({ commit, state }, {email, customer_name}) {
            const key = 'sendEmailVerification';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                const response = await authService.sendEmailVerification(email);
                if(response && response.success){
                    return Promise.resolve(response);
                }else{
                    commit('SET_ERROR', {error: response.error || 'Send email verification failed', key});
                    return Promise.reject(response);
                }
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Send email verification failed', key});
                return Promise.reject(e);
            } finally {
                commit('FINISH_LOADING', key);
            }
        },
        async createNewProject({ commit, state, dispatch }, payload) {
            const key = 'createNewProject';
            commit('CLEAR_ERROR', key);
            commit('REMOVE_SUCCESS', key);
            commit('START_LOADING', key);
            try {
                //Customer needs to logged in 
                // const isLoggedIn = await authService.isLoggedIn();
                const isLoggedIn = await authService.getUserAuthentication();
                if(!isLoggedIn){
                    commit('SET_ERROR', {error: 'User not logged in', key});
                    return Promise.reject({ data: null, success: false, error: 'User not logged in' });
                }
                const pinboard = await pinboardService.createNewProject(payload);
                await commit('SET_PINBOARD', pinboard);
                const newProjectPinboard = {
                    pinboard_id: pinboard.pinboard_id,
                    user_id: pinboard.user_id,
                    customer_id: pinboard.customer_id,
                    pinboard_status_id: pinboard.pinboard_status_id,
                    is_active: 1,
                    pinboard_name: payload.job_title,
                    customer_name: state.customer.name,
                    customer_email: state.customer.email,
                    total_price: 0,
                    item_count: 0
                }
                const nextProjectItems = (state.projectItems || []).filter(
                    (item) => String(item.pinboard_id) !== String(newProjectPinboard.pinboard_id)
                );
                await commit('SET_PROJECT_ITEMS', [newProjectPinboard, ...nextProjectItems]);
                // dispatch('projectItems', state.customer.customer_id);
                pinboardService.emitPinboardUpdatedEvent(state.pinboard);
                commit('SHOW_SUCCESS', key);
                return Promise.resolve({ data: pinboard, success: true, error: null });
            } catch (e) {
                commit('SET_ERROR', {error: e.message || 'Save failed', key});
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
        }
    },

    getters: {
        pinboard: s => s.pinboard,
        pinboardItems: s => {
            const pinboard = s.pinboard;
            return pinboard && pinboard.pinboard_items ? pinboard.pinboard_items : [];
        },
        customer: s => s.customer,
        showrooms: s => s.showrooms,
        nearestShowroom: s => s.nearestShowroom,
        fb: s => s.fb,
        loggedInUser: s => s.loggedInUser,
        commentFiles: s => s.commentFiles,
        isUserVerified: s => s.isUserVerified,
        pinboardCheckIntervals: s => s.pinboardCheckIntervals,
        bookedData: s => s.bookedData,
        projectItems: s=> s.projectItems,
        autocompleteSuggestions: s => s.autocompleteSuggestions,
        autocompleteOpen: s => s.autocompleteOpen,
    }
});
