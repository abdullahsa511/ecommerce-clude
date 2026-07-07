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

import Pinboard from './components/pinboard/Pinboard.js';
import VirtualPinboard from './components/pinboard/VirtualPinboard.js';
import store from './store/pinboardStore.js';

Vue.component('pinboard', Pinboard);
Vue.component('virtualPinboard', VirtualPinboard);

const app = new Vue({
    store,

    data() {
        return {
            vueComponents: [],
            suppressGlobalLoader: false
        };
    },

    methods: {
        async getPinboard(container) {
            try {
                const Raw = this.$options.components['pinboard'];
                const componentName = (Raw && Raw.name) ? Raw.name : 'pinboard';
                const alreadyMounted = this.vueComponents.some(inst => inst.$options && inst.$options.name === componentName);
                await this.$store.dispatch('getPinboard');
                if (!alreadyMounted) {
                    const ComponentClass = Vue.extend(Raw);

                    const instance = new ComponentClass({
                        parent: this,
                        store: this.$store
                    });

                    instance.$mount();
                    if (container) container.appendChild(instance.$el);
                    this.vueComponents.push(instance);
                }

            } catch (e) {
                console.error('getPinboard failed', e);
            }
        },
        async getVirtulPinboard(container) {
            try {
                const Raw = this.$options.components['virtualPinboard'];
                const componentName = (Raw && Raw.name) ? Raw.name : 'virtualPinboard';
                const alreadyMounted = this.vueComponents.some(inst => inst.$options && inst.$options.name === componentName);
                if (!alreadyMounted) {
                    const ComponentClass = Vue.extend(Raw);

                    const instance = new ComponentClass({
                        parent: this,
                        store: this.$store
                    });

                    instance.$mount();
                    if (container) container.appendChild(instance.$el);
                    this.vueComponents.push(instance);
                }

                // await this.$store.dispatch('getVirtualPinboard', payload);
            } catch (e) {
                console.error('getVirtualPinboard failed', e);
            }
        },
        async getNearestShowroom() {
            try {
                await this.$store.dispatch('getNearestShowroom');
            } catch (e) {
                console.error('getNearestShowroom failed', e);
            }
        },

        async checkUserLogin() {
            try {
                await this.$store.dispatch('checkUserLogin');
            } catch (e) {
                console.error(e);
            }
        },

        async addToPinboard(itemData) {
            try {
                await this.$store.dispatch('addToPinboard', itemData);
            } catch (e) {
                console.error(e);
            }
        },

        async bookingPhoneCall(payload) {
            try {
                const response = await this.$store.dispatch('bookingPhoneCall', payload);
                // console.log('bookingPhoneCall response pinboardApp=', response);
                if(response && response.success){
                    return Promise.resolve(response);
                }else{
                    return Promise.reject(response);
                }
            } catch (e) {
                console.error(e);
                return Promise.resolve({ success: false, error: e.message });
            }
        },

        async bookingEmail(payload) {
            try {
                const response = await this.$store.dispatch('bookingByEmail', payload);
                if(response && response.success){
                    return Promise.resolve(response);
                }else{
                    return Promise.reject(response);
                }
            } catch (e) {
                console.error(e);
                return Promise.reject(e);
            }
        },
        async bookNow(payload) {
            try {
                const response = await this.$store.dispatch('bookNow', payload);
                if(response && response.success){
                    return Promise.resolve(response);
                }else{
                    return Promise.reject(response);
                }
            } catch (e) {
                console.error(e);
                return Promise.reject(e);
            }
        }
    
    }
});

window.pinboardApp = app;
export default app;
