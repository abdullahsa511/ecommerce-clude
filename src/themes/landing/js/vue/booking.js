const Vue = window.Vue;
const Vuex = window.Vuex;

if (!Vue || !Vuex) {
    console.error('Vue or Vuex is not available. Make sure both CDN scripts are loaded.');
}

import BookingRoot from './components/booking/BookingRoot.js';
import store from './store/bookingStore.js';

let bookingRootInstance = null;
let rescheduleModalVm = null;

function getRequestedShowroomId() {
    try {
        const searchParams = new URLSearchParams(window.location.search || '');
        // console.log('searchParams', searchParams);
        let showroomId = searchParams.get('showroom');


        // Support URLs like /contact-sales#book-now?showroom=5
        if (!showroomId && window.location.hash && window.location.hash.includes('?')) {
            const hashQuery = window.location.hash.split('?')[1] || '';
            const hashParams = new URLSearchParams(hashQuery);
            showroomId = hashParams.get('showroom');
        }
        return showroomId ? String(showroomId) : '';
    } catch (e) {
        return '';
    }
}

function shouldScrollToBookNow() {
    const hash = window.location.hash || '';
    return hash === '#book-now' || hash.startsWith('#book-now?');
}

function scrollToBookNowSection() {
    const target = document.getElementById('book-now');
    if (!target) return;
    const headerOffset = 100;
    const elementPosition = target.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
    window.scrollTo({ top: offsetPosition, behavior: 'auto' });
}

function scheduleBookNowScroll() {
    if (!shouldScrollToBookNow()) return;
    requestAnimationFrame(scrollToBookNowSection);
}

const app = new Vue({
    store,
    methods: {
        destroyRescheduleBookingModal() {
            if (rescheduleModalVm) {
                try {
                    rescheduleModalVm.$destroy();
                } catch (e) {
                    /* noop */
                }
                if (rescheduleModalVm.$el && rescheduleModalVm.$el.parentNode) {
                    rescheduleModalVm.$el.parentNode.removeChild(rescheduleModalVm.$el);
                }
                rescheduleModalVm = null;
            }
        },
        async openRescheduleBookingModal(visitData = {}) {
            if (!window.Vue) return null;
            const module = await import('/js/vue/components/pinboard/BookingRescheduleTimeModal.js');
            const BookingRescheduleTimeModal = module.default;
            this.destroyRescheduleBookingModal();
            const ComponentClass = window.Vue.extend(BookingRescheduleTimeModal);
            rescheduleModalVm = new ComponentClass({
                propsData: { visitData },
            });
            rescheduleModalVm.$on('close', () => {
                this.destroyRescheduleBookingModal();
            });
            rescheduleModalVm.$mount();
            document.body.appendChild(rescheduleModalVm.$el);
            return rescheduleModalVm;
        },
        async mount(container, payload = {}) {
            if (!container) return null;

            if (!bookingRootInstance) {
                const ComponentClass = Vue.extend(BookingRoot);
                bookingRootInstance = new ComponentClass({
                    parent: this,
                    store: this.$store,
                });
                bookingRootInstance.$mount();
                container.appendChild(bookingRootInstance.$el);
            }

            await this.$store.dispatch('hydrateCustomer');
            await this.$store.dispatch('getNearestShowroom');

            const requestedShowroomId = getRequestedShowroomId();
            if (requestedShowroomId) {
                this.$store.dispatch('selectShowroom', requestedShowroomId);
            }

            scheduleBookNowScroll();

            if (payload?.openCalendar) {
                await bookingRootInstance.openBookingCalendar(payload);
            }
            if (payload?.openTime) {
                await bookingRootInstance.openBookingTime(payload);
            }

            return bookingRootInstance;
        },
        async openBookingCalendar(payload = {}) {
            if (bookingRootInstance) {
                return bookingRootInstance.openBookingCalendar(payload);
            }
            return null;
        },
        async openBookingTime(payload = {}) {
            if (bookingRootInstance) {
                return bookingRootInstance.openBookingTime(payload);
            }
            return null;
        },
        closeBookingFlow() {
            if (bookingRootInstance) {
                bookingRootInstance.closeAll();
            }
        },
    },
});

window.bookingApp = app;
export default app;
