import bookingService from '../services/bookingService.js';
import FeedbackHandler from '../models/FeedbackHandler.js';

const defaultFeedback = () => new FeedbackHandler();

const store = new Vuex.Store({
    state: {
        nearestShowroom: null,
        nearestShowroomLoading: true,
        showroomsData: [],
        bookedData: [],
        customer: {},
        fb: defaultFeedback(),
    },
    getters: {
        nearestShowroom: (state) => state.nearestShowroom || {},
        nearestShowroomLoading: (state) => Boolean(state.nearestShowroomLoading),
        showroomsData: (state) => state.showroomsData || [],
        bookedData: (state) => state.bookedData || [],
        customer: (state) => state.customer || {},
        fb: (state) => state.fb,
    },
    mutations: {
        SET_NEAREST_SHOWROOM_LOADING(state, isLoading) {
            state.nearestShowroomLoading = Boolean(isLoading);
        },
        SET_NEAREST_SHOWROOM(state, payload) {
            state.nearestShowroom = payload || null;
        },
        SET_SHOWROOMS_DATA(state, payload) {
            state.showroomsData = Array.isArray(payload) ? payload : [];
        },
        SET_BOOKED_DATA(state, payload) {
            state.bookedData = Array.isArray(payload) ? payload : [];
        },
        SET_CUSTOMER(state, payload) {
            state.customer = payload || {};
        },
        START_LOADING(state, key) {
            if (!state.fb.loading) state.fb.loading = {};
            state.fb.loading[key] = true;
            if (!state.fb.errors) state.fb.errors = {};
            state.fb.errors[key] = null;
        },
        SET_SUCCESS(state, key) {
            if (!state.fb.loading) state.fb.loading = {};
            state.fb.loading[key] = false;
            if (!state.fb.errors) state.fb.errors = {};
            state.fb.errors[key] = null;
        },
        SET_ERROR(state, payload) {
            if (!payload || typeof payload !== 'object' || !payload.key) return;
            const { key, error } = payload;
            if (!state.fb.loading) state.fb.loading = {};
            if (!state.fb.errors) state.fb.errors = {};
            state.fb.loading[key] = false;
            state.fb.errors[key] = error || 'Request failed';
        },
    },
    actions: {
        async hydrateCustomer({ commit }) {
            try {
                const auth = await bookingService.getUserAuthentication();
                const customer = auth?.customer || {};
                commit('SET_CUSTOMER', customer);
                return Promise.resolve(customer);
            } catch (error) {
                commit('SET_CUSTOMER', {});
                return Promise.resolve({});
            }
        },
        async getNearestShowroom({ commit }) {
            commit('SET_NEAREST_SHOWROOM_LOADING', true);
            commit('START_LOADING', 'nearestShowroom');
            try {
                const response = await bookingService.getNearestShowroom();
                const data = response?.data || response || {};
                const allShowrooms = Array.isArray(data.all_showrooms) ? data.all_showrooms : [];
                const nearest = data.nearest_showroom || {};

                if (response?.success || allShowrooms.length || Object.keys(nearest).length) {
                    commit('SET_SHOWROOMS_DATA', allShowrooms);
                    commit('SET_NEAREST_SHOWROOM', nearest);
                    commit('SET_SUCCESS', 'nearestShowroom');
                    return Promise.resolve({ success: true, data });
                }

                const errMsg = response?.message || 'Failed to load nearest showroom';
                commit('SET_ERROR', { key: 'nearestShowroom', error: errMsg });
                return Promise.resolve({ success: false, message: errMsg });
            } catch (error) {
                commit('SET_ERROR', { key: 'nearestShowroom', error: error?.message || 'Failed to load nearest showroom' });
                return Promise.resolve({ success: false, message: error?.message || 'Failed to load nearest showroom' });
            } finally {
                commit('SET_NEAREST_SHOWROOM_LOADING', false);
            }
        },
        async getBookedData({ commit, state }, bookingArgs = []) {
            commit('START_LOADING', 'bookedData');
            try {
                const [selectedDate, tourType = 'physicalTour'] = Array.isArray(bookingArgs)
                    ? bookingArgs
                    : [bookingArgs, 'physicalTour'];
                const showroomId =
                    state.nearestShowroom?.showroom_id ||
                    state.nearestShowroom?.showrooms_id ||
                    state.nearestShowroom?.id ||
                    null;
                if (!showroomId || !selectedDate) {
                    commit('SET_BOOKED_DATA', []);
                    commit('SET_SUCCESS', 'bookedData');
                    return Promise.resolve({ success: true, data: [] });
                }
                const response = await bookingService.getBookedData(selectedDate, showroomId, tourType);
                if (response?.success) {
                    commit('SET_BOOKED_DATA', response.data || []);
                    commit('SET_SUCCESS', 'bookedData');
                    return Promise.resolve(response);
                }
                const errMsg = response?.message || 'Failed to load booked slots';
                commit('SET_ERROR', { key: 'bookedData', error: errMsg });
                return Promise.resolve({ success: false, message: errMsg });
            } catch (error) {
                commit('SET_ERROR', { key: 'bookedData', error: error?.message || 'Failed to load booked slots' });
                return Promise.resolve({ success: false, message: error?.message || 'Failed to load booked slots' });
            }
        },
        async checkExistingBooking({ commit }, payload) {
            commit('START_LOADING', 'checkExistingBooking');
            try {
                const response = await bookingService.checkExistingBooking(payload);
                if (response?.success) {
                    commit('SET_SUCCESS', 'checkExistingBooking');
                    return Promise.resolve(response);
                }
                const errMsg = response?.message || 'Booking validation failed';
                commit('SET_ERROR', { key: 'checkExistingBooking', error: errMsg });
                return Promise.resolve({ success: false, message: errMsg });
            } catch (error) {
                commit('SET_ERROR', { key: 'checkExistingBooking', error: error?.message || 'Booking validation failed' });
                return Promise.resolve({ success: false, message: error?.message || 'Booking validation failed' });
            }
        },
        async bookNow({ commit }, payload) {
            commit('START_LOADING', 'bookNow');
            try {
                payload.source = 'Pinboard';
                const response = await bookingService.bookNow(payload);
                if (response?.success) {
                    commit('SET_SUCCESS', 'bookNow');
                    return Promise.resolve(response);
                }
                const errMsg = response?.message || 'Booking failed';
                commit('SET_ERROR', { key: 'bookNow', error: errMsg });
                return Promise.resolve({ success: false, message: errMsg });
            } catch (error) {
                commit('SET_ERROR', { key: 'bookNow', error: error?.message || 'Booking failed' });
                return Promise.resolve({ success: false, message: error?.message || 'Booking failed' });
            }
        },
        selectShowroom({ commit, state }, showroomId) {
            const selectedId = String(showroomId || '');
            const selected = (state.showroomsData || []).find(
                (item) =>
                    String(item.showroom_id || item.showrooms_id || item.id || '') === selectedId,
            );
            if (selected) {
                commit('SET_NEAREST_SHOWROOM', {
                    ...state.nearestShowroom,
                    ...selected,
                    showroom_id: selected.showroom_id || selected.showrooms_id || selected.id || '',
                    showrooms_id: selected.showrooms_id || selected.showroom_id || selected.id || '',
                });
            }
        },
    },
});

export default store;

