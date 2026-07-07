import BookingCalendar from './BookingCalendar.js';
import BookingCalendarModal from '../pinboard/BookingCalendarModal.js';
import BookingTimeModal from '../pinboard/BookingTimeModal.js';
import EmailVerificationModal from '../pinboard/EmailVerificationModal.js';
import { attachBookingRecaptcha } from '../../../recaptcha-v3.js';

export default {
    name: 'booking-root',
    components: {
        BookingCalendar,
        BookingCalendarModal,
        BookingTimeModal,
        EmailVerificationModal,
    },
    data() {
        return {
            showBookingCalendarModal: false,
            showBookingTimeModal: false,
            selectedDate: '',
            selectedTourType: 'physicalTour',
            pinboardTitle: 'Book Now',
            pinboardId: null,
            showEmailVerificationModal: false,
            verifyLoading: false,
            pendingBookingData: null,
            verificationCustomer: {},
            verificationErrors: {},
            source: 'Contact Sales',
        };
    },
    computed: {
        nearestShowroom() {
            return this.$store.getters.nearestShowroom || {};
        },
        showroomsData() {
            return this.$store.getters.showroomsData || [];
        },
        authenticatedCustomer() {
            return this.$store.getters.customer || {};
        },
    },
    methods: {
        handleSelectShowroom(showroomId) {
            this.$store.dispatch('selectShowroom', showroomId);
        },
        async openBookingCalendar(payload = {}) {
            this.pinboardTitle = payload.pinboardTitle || this.pinboardTitle;
            this.pinboardId = payload.pinboardId || this.pinboardId;
            this.showBookingTimeModal = false;
            this.selectedDate = '';
            await this.$store.dispatch('getNearestShowroom');
            this.showBookingCalendarModal = true;
        },
        async openBookingTime(payload = {}) {
            const selectedDate = payload.selectedDate || '';
            const selectedTourType = payload.tourType || 'physicalTour';
            this.pinboardTitle = payload.pinboardTitle || this.pinboardTitle;
            this.pinboardId = payload.pinboardId || this.pinboardId;
            await this.$store.dispatch('getNearestShowroom');
            this.selectedDate = selectedDate;
            this.selectedTourType = selectedTourType;
            this.showBookingCalendarModal = false;
            this.showBookingTimeModal = true;
        },
        openBookingTimeModal(payload) {
            if (payload && typeof payload === 'object') {
                this.selectedDate = payload.selectedDate || '';
                this.selectedTourType = payload.tourType || 'physicalTour';
            } else {
                this.selectedDate = payload || '';
                this.selectedTourType = 'physicalTour';
            }
            this.showBookingCalendarModal = false;
            this.showBookingTimeModal = true;
        },
        openEmailVerification(payload = {}) {
            this.pendingBookingData = payload.bookingData || null;
            this.verificationCustomer = {
                ...(payload.customer || {}),
                email: payload.email || payload?.customer?.email || '',
                uuid: payload?.customer?.uuid || payload?.customer?.customer_id || 'booking-customer',
                is_verified: false,
            };
            this.verificationErrors = {};
            this.showEmailVerificationModal = true;
        },
        closeEmailVerification() {
            this.showEmailVerificationModal = false;
            this.verificationErrors = {};
        },
        async resendVerificationEmail() {
            const email = this.verificationCustomer?.email;
            const customerName = this.verificationCustomer?.customer_name || '';
            if (!email) return;
            await fetch('/api/send-email-verification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email,
                    customer_name: customerName,
                    subject: 'Resend Booking OTP with Krost',
                }),
            });
        },
        async handleVerifyOtp(otp) {
            const email = this.verificationCustomer?.email;
            if (!email || !otp || String(otp).length !== 6) {
                this.verificationErrors = { createPinboard: 'Please enter the 6-digit code' };
                return;
            }
            this.verifyLoading = true;
            this.verificationErrors = {};
            try {
                const verifyResponseRaw = await fetch('/api/verify-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, otp }),
                });
                const verifyResponse = await verifyResponseRaw.json();
                if (!verifyResponse?.success) {
                    this.verificationErrors = {
                        createPinboard: verifyResponse?.message || 'OTP verification failed',
                    };
                    return;
                }

                if (!this.pendingBookingData) {
                    this.closeEmailVerification();
                    return;
                }

                let bookingPayload = this.pendingBookingData;
                try {
                    bookingPayload = await attachBookingRecaptcha(this.pendingBookingData);
                } catch (recaptchaErr) {
                    this.verificationErrors = {
                        createPinboard:
                            recaptchaErr?.message ||
                            'reCAPTCHA verification failed. Please refresh the page and try again.',
                    };
                    return;
                }

                const bookingResponse = await this.$store.dispatch('bookNow', bookingPayload);
                if (!bookingResponse?.success) {
                    const errMsg = bookingResponse?.message || 'Booking failed';
                    this.verificationErrors = {
                        createPinboard: errMsg,
                    };
                    return;
                }

                this.closeEmailVerification();
                this.closeAll();
                const visitShowroomId = bookingResponse?.data?.visit_showroom_id;
                if (visitShowroomId) {
                    const uuid = bookingResponse?.data?.uuid;
                    const tourType = this.selectedTourType || 'physicalTour';
                    if (tourType === "physicalTour") {
                        window.location.href = `/contact-us/book-physical-showroom-visit/${uuid}`;
                    }else{
                        window.location.href = `/contact-us/virtual-meeting-booking/${uuid}`;
                    }
                }
            } catch (e) {
                this.verificationErrors = { createPinboard: e?.message || 'OTP verification failed' };
            } finally {
                this.verifyLoading = false;
            }
        },
        closeBookingCalendarModal() {
            this.showBookingCalendarModal = false;
            this.selectedDate = '';
        },
        closeBookingTimeModal() {
            this.showBookingTimeModal = false;
            this.selectedDate = '';
        },
        backToCalendar() {
            this.showBookingTimeModal = false;
            this.showBookingCalendarModal = true;
        },
        closeAll() {
            this.showBookingCalendarModal = false;
            this.showBookingTimeModal = false;
            this.showEmailVerificationModal = false;
            this.selectedDate = '';
        },
    },
    template: `
        <div class="booking-vue-root">
            <booking-calendar
                :nearest-showroom="nearestShowroom"
                :showrooms-data="showroomsData"
                @open-time-slots="openBookingTimeModal"
                @select-showroom="handleSelectShowroom"
            />
            <booking-calendar-modal
                v-if="showBookingCalendarModal && !showBookingTimeModal"
                :pinboard-title="pinboardTitle"
                :pinboard-id="pinboardId"
                :nearest-showroom="nearestShowroom"
                @close-booking="closeBookingCalendarModal"
                @open-time-slots="openBookingTimeModal"
            />
            <booking-time-modal
                v-if="showBookingTimeModal"
                :pinboard-title="pinboardTitle"
                :pinboard-id="pinboardId"
                :selected-date="selectedDate"
                :tour-type="selectedTourType"
                :customer="authenticatedCustomer"
                :nearest-showroom="nearestShowroom"
                :enable-email-verification="true"
                :source="source"
                @close-time="closeBookingTimeModal"
                @back-to-calendar="backToCalendar"
                @booking-success="closeAll"
                @request-email-verification="openEmailVerification"
            />
            <email-verification-modal
                v-if="showEmailVerificationModal"
                :customer="verificationCustomer"
                :pinboard-title="pinboardTitle"
                :verify-loading="verifyLoading"
                :errors="verificationErrors"
                @verify="handleVerifyOtp"
                @resend-email="resendVerificationEmail"
                @close="closeEmailVerification"
                subtitle="Please enter the code below to secure your booking."
            />
        </div>
    `,
};

