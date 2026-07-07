import authService from '../services/authService.js';
import pinboardService from '../services/pinboardService.js';

export default new Vuex.Store({
    state: {
        error: null,
        actionLoading: false,
        success: false,
        orderTracking: {},
        orderTrackingFilter: {},
        createRequestResult: null,
        quoteAcceptance: [],
        projectLists: [],
        pinboard: null,
    },

    mutations: {
        SET_ORDER_TRACKING(state, orderTracking) {
            state.orderTracking = orderTracking;
        },
        SET_ORDER_TRACKING_FILTER(state, orderTrackingFilter) {
            state.orderTrackingFilter = orderTrackingFilter;
        },
        SET_ERROR(state, error) {
            state.error = error;
        },
        SET_ACTION_LOADING(state, val) {
            state.actionLoading = val;
        },
        SET_SUCCESS(state, success) {
            state.success = success;
        },
        SET_QUOTE_ACCEPTANCE(state, quoteAcceptance) {
            state.quoteAcceptance = quoteAcceptance;
        },
        SET_CREATE_REQUEST_RESULT(state, payload) {
            state.createRequestResult = payload;
        },
        SET_PROJECT_LISTS(state, projectLists) {
            state.projectLists = projectLists;
        },
        SET_PINBOARD(state, pinboard) {
            state.pinboard = pinboard;
        },
    },

    actions: {
        // account related actions
        async getOrderTracking({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                commit('SET_ORDER_TRACKING_FILTER', payload);
                const service = await import('../services/accountService.js');
                const res = await service.default.getOrderTracking(payload);
                const data = res.data || [];
                console.log('data = getOrderTracking STORE', data);
                commit('SET_ORDER_TRACKING', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                commit('SET_ERROR', 'Get order tracking failed');
                return { error: e.message || 'Get order tracking failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },
        async getProjectLists({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                if (!userAuthDetails) {
                    commit('SET_ERROR', 'User not authenticated');
                    return { error: 'User not authenticated' };
                }
                const service = await import('../services/accountService.js');
                const res = await service.default.getProjectList(userAuthDetails.user.user_id);
                const data = res.data.pinboards ?? [];
                commit('SET_PROJECT_LISTS', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                commit('SET_ERROR', 'Get pinboard list failed');
                return { error: e.message || 'Get pinboard list failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },

        async getProjectItemsByPinboardId({ commit }, pinboardId) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const userAuthDetails = await authService.getUserAuthentication();
                if (!userAuthDetails) {
                    commit('SET_ERROR', 'User not authenticated');
                    return { error: 'User not authenticated' };
                }
                const pinboard = await pinboardService.getProjectByPinboardId(pinboardId, userAuthDetails.user.user_id, true);
                console.log('pinboard = getProjectItemsByPinboardId STORE', pinboard);
                commit('SET_PINBOARD', pinboard);
                commit('SET_SUCCESS', true);
                return pinboard;
            } catch (e) {
                commit('SET_ERROR', 'Get pinboard by pinboard id failed');
                return { error: e.message || 'Get pinboard by pinboard id failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },

        async getQuoteAcceptance({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const service = await import('../services/accountService.js');
                const res = await service.default.getQuoteAcceptance(payload);
                const data = res.data || [];
                commit('SET_QUOTE_ACCEPTANCE', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                console.error('getQuoteAcceptance failed', e);
            }
        },
        async createRequest({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const service = await import('../services/accountService.js');
                const res = await service.default.createRequest(payload);
                if (res && res.error) {
                    commit('SET_ERROR', res.error);
                    return res;
                }
                const data = res.data || null;
                console.log('data = createRequest STORE', data);
                commit('SET_CREATE_REQUEST_RESULT', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                console.error('createRequest failed', e);
                commit('SET_ERROR', e.message || 'Create request failed');
                return { error: e.message || 'Create request failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },
        async contactSalesGetInTouch({ commit }, payload = {}) {
            commit('SET_ACTION_LOADING', true);
            commit('SET_ERROR', null);
            try {
                const service = await import('../services/accountService.js');
                const res = await service.default.contactSalesGetInTouch(payload);
                if (res && res.error) {
                    commit('SET_ERROR', res.error);
                    return res;
                }
                const data = res.data || null;
                commit('SET_CREATE_REQUEST_RESULT', data);
                commit('SET_SUCCESS', true);
                return res;
            } catch (e) {
                commit('SET_ERROR', e.message || 'Contact sales get in touch failed');
                return { error: e.message || 'Contact sales get in touch failed' };
            } finally {
                commit('SET_ACTION_LOADING', false);
            }
        },
    },

    getters: {
        // account related getters
        error: s => s.error,
        actionLoading: s => s.actionLoading,
        success: s => s.success,
        trackingOrders: s => s.orderTracking,
        orderTrackingFilter: s => s.orderTrackingFilter,
        createRequestResult: s => s.createRequestResult,
        quoteAcceptance: s => s.quoteAcceptance,
        projectLists: s => s.projectLists,
        pinboard: s => s.pinboard,
    }
});
