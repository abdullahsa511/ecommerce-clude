const Vue = window.Vue;
const Vuex = window.Vuex;

if (Vue && Vuex) {
    if (!window.__vuexInstalled) {
        // Vue.use(Vuex);
        window.__vuexInstalled = true;
    }
} else {
    console.error('Vue or Vuex is not available. Make sure both CDN scripts are loaded.');
}

// Register Vue.Draggable globally if available
// Vue.Draggable UMD build exposes it as a component definition object
if (Vue) {
    let draggable = window.vuedraggable.default || window.vuedraggable;
  
    // Vue.Draggable is a component definition object, not a function
    if (draggable) {
        try {
            // Register directly as a component definition
            Vue.component('draggable', draggable);
        } catch (e) {
            console.error('Error registering Vue.Draggable:', e);
            console.log('Draggable value:', draggable);
        }
    }
    
    // Vue.Draggable expects Sortable to be available on window
    if (typeof window.Sortable === 'undefined' && typeof Sortable !== 'undefined') {
        window.Sortable = Sortable;
    }
    
}

import OrderTracking from './components/account/order/OrderTracking.js';
import PinboardList from './components/account/pinboard/PinboardList.js';
import store from './store/accountStore.js';

Vue.component('order-tracking', OrderTracking);
Vue.component('account-pinboard-list', PinboardList);
const app = new Vue({
    store,

    data() {
        return {
            vueComponents: []
        };
    },

    computed: {
        error() {
            return this.$store.getters.error;
        },
        actionLoading() {
            return this.$store.getters.actionLoading;
        },
        success() {
            return this.$store.getters.success;
        },
        orderTracking() {
            // return this.$store.getters.trackingOrders;
            return this.$store.state.orderTracking;
        }
    },

    methods: {
        async getPinboardList(container, payload = {}) {
            try {
                if (!container) {
                    throw new Error('Missing target container element for pinboard list.');
                }

                const target =
                    container instanceof Element
                        ? container
                        : (typeof container.length === 'number' && container.length > 0
                            ? container[0]
                            : null);

                if (target && !(target instanceof Element)) {
                    throw new Error('Provided container is not a DOM element.');
                }

                if (!target) {
                    throw new Error('Unable to resolve pinboard list mount element.');
                }

                // ############ call get pinboard list function ############
                if (payload) {
                    await this.$store.dispatch('getProjectList', payload);
                } else if (!this.$store.state.pinboardList.length) {
                    await this.$store.dispatch('getProjectList');
                }

                // ############ create pinboard list component ############
                if (this.vueComponents.length === 0) {
                    const Raw = this.$options.components['account-pinboard-list'];
                    const ComponentClass = Vue.extend(Raw);

                    const instance = new ComponentClass({
                        parent: this,
                        store: this.$store
                    });

                    instance.$mount();
                    container.appendChild(instance.$el);
                    this.vueComponents.push(instance);
                }
                // ensure store has data (returns service response)
                return {
                    error: this.error,
                    loading: this.loading
                };
                // const response = await this.$store.dispatch('getPinboardList', payload);
                // console.log('response=', response);
                // return response;
            } catch (e) {
                console.error('getPinboardList failed', e);
            }
        },
        async getOrderTracking(container, payload = {}) {
            try {
                if (!container) {
                    throw new Error('Missing target container element for order tracking.');
                }

                const target =
                    container instanceof Element
                        ? container
                        : (typeof container.length === 'number' && container.length > 0
                            ? container[0]
                            : null);

                if (target && !(target instanceof Element)) {
                    throw new Error('Provided container is not a DOM element.');
                }

                if (!target) {
                    throw new Error('Unable to resolve order tracking mount element.');
                }

                // ############ call get order tracking function ############
                if (payload) {
                    await this.$store.dispatch('getOrderTracking', payload);
                } else if (!this.$store.state.orderTracking.length) {
                    await this.$store.dispatch('getOrderTracking');
                }

                // ############ create order tracking component ############
                if (this.vueComponents.length === 0) {
                    const Raw = this.$options.components['order-tracking'];
                    const ComponentClass = Vue.extend(Raw);

                    const instance = new ComponentClass({
                        parent: this,
                        store: this.$store
                    });

                    instance.$mount();
                    container.appendChild(instance.$el);
                    this.vueComponents.push(instance);
                }
                // ensure store has data (returns service response)
                return {
                    error: this.error,
                    loading: this.loading
                };
                // const response = await this.$store.dispatch('getOrderTracking', payload);
                // console.log('response=', response);
                // return response;
            } catch (e) {
                console.error('getOrderTracking failed', e);
            }
        },
        async getQuoteAcceptance(payload = {}) {
            try {
                const response = await this.$store.dispatch('getQuoteAcceptance', payload);
               const data = response && response.data ? response.data : [];
                if(response && response.error){
                    return { error: response.error || 'Get quote acceptance failed' };
                }
                return data;
            } catch (e) {
                console.error('getQuoteAcceptance failed', e);
                return { error: e.message || 'Get quote acceptance failed' };
            }
        },
        async createRequest(payload = {}) {
            try {
                const response = await this.$store.dispatch('createRequest', payload);
                if (response && response.error) {
                    return { error: response.error || 'Create request failed' };
                }
                return response && response.data ? response.data : null;
            } catch (e) {
                console.error('createRequest failed', e);
                return { error: e.message || 'Create request failed' };
            }
        },
        async contactSalesGetInTouch(payload = {}) {
            try {
                const response = await this.$store.dispatch('contactSalesGetInTouch', payload);
                if (response && response.error) {
                    return { error: response.error || 'Contact sales get in touch failed' };
                }
                return response && response.data ? response.data : null;
            } catch (e) {
                console.error('contactSalesGetInTouch failed', e);
                return { error: e.message || 'Contact sales get in touch failed' };
            }
        },
    }
});

window.accountApp = app;
export default app;
