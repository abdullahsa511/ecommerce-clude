import Customer from '../../models/Customer.js';

const debounce = (fn, wait) => {
    let timeout = null;
    return function(...args) {
        const context = this;
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            timeout = null;
            fn.apply(context, args);
        }, wait);
    };
};

function validateBookingEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

async function postBookingJson(url, payload) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
    });
    return res.json();
}

function buildMeetingDates(date, meetingTime, duration) {
    const start = new Date(`${date}T${meetingTime}`);
    const end = new Date(start.getTime() + duration * 60000);
    return { start, end };
}

function makeMeetingLink({ start, end, title, location }) {
    const format = (d) => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
    const startStr = format(start);
    const endStr = format(end);
    const add = 'c_18895tnlfoecignhmo94g3jqjc402@resource.calendar.google.com';
    return `https://calendar.google.com/calendar/u/0/r/eventedit?text=${encodeURIComponent(title)}&dates=${startStr}/${endStr}&location=${encodeURIComponent(location || '')}&add=${encodeURIComponent(add)}`;
}

export default {
    name: 'Pinboard',

    data() {
        return {
            showProjectModal: false,
            showBookingModal: false,
            showAddComment: false,
            editingItems:{},
            comment:'',
            otpDigits: ['', '', '', '', '', ''],
            customerData:{
                name: '',
                projectName: '',
                job_title: '',
                companyName: '',
                email: '',
                phone: '',
                otp: '',
            },
            // time slot booking
            selectedDate: null,
            email: '',
            selectedDateFormatted: '',
            modalInstance: null,
            selectedSlot: null, // currently active slot ID
            selectedSlotValue: '', // currently active slot value
            checkedSlot: null, // v-model bound value
            morningSlots: [
                { id: "time-slot-1", value: "09:00:00", label: "9:00 AM" },
                { id: "time-slot-2", value: "09:30:00", label: "9:30 AM" },
                { id: "time-slot-3", value: "10:00:00", label: "10:00 AM" },
                { id: "time-slot-4", value: "10:30:00", label: "10:30 AM" },
                { id: "time-slot-5", value: "11:00:00", label: "11:00 AM" },
                { id: "time-slot-6", value: "11:30:00", label: "11:30 AM" },
                { id: "time-slot-7", value: "12:00:00", label: "12:00 PM" },
                { id: "time-slot-8", value: "12:30:00", label: "12:30 PM" },
            ],
            showProjectDropdown: false,
            showCreateNewProjectModal: false,
            newProjectName: '',
            selectedProjectId: '',
            localFilter: {
                searchValue: '',
            },
            _autocompleteDebounceId: null,
            eveningSlots: [
                { id: "time-slot-9", value: "13:00:00", label: "1:00 PM" },
                { id: "time-slot-10", value: "13:30:00", label: "1:30 PM" },
                { id: "time-slot-11", value: "14:00:00", label: "2:00 PM" },
                { id: "time-slot-12", value: "14:30:00", label: "2:30 PM" },
                { id: "time-slot-13", value: "15:00:00", label: "3:00 PM" },
                { id: "time-slot-14", value: "15:30:00", label: "3:30 PM" },
                { id: "time-slot-15", value: "16:00:00", label: "4:00 PM" },
                { id: "time-slot-16", value: "16:30:00", label: "4:30 PM" },
              ],
            showAddImageModal: false,
            addImageModalError: false,
            cameraImagePreview: '',
            addImageTitle: '',
            addImageComment: '',
            _cameraBlobUrl: null,
            showLiveCamera: false,
            _mediaStream: null,
            bookingEmail: '',
            bookingName: '',
            pendingBookingData: null,
            bookingOtpTimerRef: null,
        };  
    },
    computed: {
        pinboard(){
            return this.$store.getters.pinboard;
        },
        projectMenuItems() {
            return this.$store.getters.projectItems;
        },
        // items() {
        //     return this.$store.getters.pinboardItems || [];
        // },
        items: {
            get() {
                // console.log('items computed = ', this.$store.getters.pinboardItems);
                return this.$store.getters.pinboardItems || [];
            },
            set(value) {
                console.log('items set component :- ', value);
                this.$store.dispatch('reorderPinboardItems', value);
            }
        },
        commentFiles() {
            return this.$store.getters.commentFiles || [];
        },
        customer: {
            get() {
                return this.$store.getters.customer || {};
            },
            set(value) {
                // normalize to string and commit to store so inputs update reactively
                this.$store.commit('SET_CUSTOMER', value);
            }
        },
        loggedInCustomer() {
            return this.$store.getters.customer || {};
        },
        loggedInUser() {
            this.bookingEmail = this.$store.getters.loggedInUser?.email || '';
            return this.$store.getters.loggedInUser;
        },
        fb() {
            return this.$store.getters.fb;
        },
        disableCreateProjectButton() {
            return this.fb.errors.projectName || this.fb.errors.email;
        },
        bookedData() {
            return this.$store.getters.bookedData || [];
        },
        nearestShowroom() {
            return this.$store.getters.nearestShowroom || [];
        },
        displayProjectTitle() {
            const sid = this.selectedProjectId;
            if (sid === '' || sid === null || sid === undefined) {
                return 'Project';
            }
            const p = this.projectMenuItems.find(
                (x) => String(x.pinboard_id) === String(sid)
            );
            return p && p.pinboard_name ? p.pinboard_name : 'Project';
        },
        autoCompletePlaceholderText() {
            return 'Search to add products...';
        },
        disableAutocomplete() {
            return this.loading('getPinboard');
        },
        autocompleteSuggestions() {
            console.log("autocompleteSuggestions component=", this.$store.getters.autocompleteSuggestions);
            return this.$store.getters.autocompleteSuggestions;
        },
        autocompleteOpen() {
            console.log("autocompleteOpen component=", this.$store.getters.autocompleteOpen);
            return this.$store.getters.autocompleteOpen;
        },
    },

    async beforeCreate() {
        // await this.$store.dispatch('getPinboard', { userId: 1 });
    },
    mounted() {
        // Clean up intervals when component is destroyed
        if (this.$refs.timeSlotsModal && window.bootstrap) {
            this.modalInstance = new bootstrap.Modal(
                this.$refs.timeSlotsModal
            );
        }

        this._closeProjectMenuOnOutsideClick = (e) => {
            if (!this.showProjectDropdown) return;
            const wraps = this.$el && this.$el.querySelectorAll('.pinboard-header-project-wrap');
            if (!wraps || !wraps.length) return;
            const inside = Array.from(wraps).some((w) => w.contains(e.target));
            if (!inside) this.showProjectDropdown = false;
        };
        document.addEventListener('click', this._closeProjectMenuOnOutsideClick);
    },

    beforeDestroy() {
        if (this._closeProjectMenuOnOutsideClick) {
            document.removeEventListener('click', this._closeProjectMenuOnOutsideClick);
        }
        if (this._autocompleteDebounceId != null) {
            clearTimeout(this._autocompleteDebounceId);
        }
        this.stopLiveCameraStream();
        this.revokeCameraBlobUrl();
        if (this.bookingOtpTimerRef) {
            clearInterval(this.bookingOtpTimerRef);
            this.bookingOtpTimerRef = null;
        }
    },

    methods: {
        getKey(item, index) {
            return `${item.model_type}-${item.model_id}-${index}`;
        },
        loading(key) {
            return this.$store.getters.fb.loading[key];
        },
        clearError(key) {
            this.$store.commit('CLEAR_ERROR', key);
        },
        cloneItem(item) {
            return {...item, _isDragPreview: true}
        },
        toggleItemNote(item) {
            const next = !Boolean(item?._showNote);
            // Vue 2 reactivity: ensure property exists
            this.$set(item, '_showNote', next);
        },
        closeModal() {
            this.showProjectModal = false;
            this.showBookingModal = false;
            this.modalInstance.hide();
        },
        async checkAndCreateTemporaryProject() {
            if(!this.validateInput('job_title', this.pinboard.job_title, '', true)){
                return;
            }
            if(!this.validateInput('email', this.customer.email, 'email', true)){
                return;
            }
            let createResult = await this.$store.dispatch('createTemporayPinboard');

            let customer = new Customer({});
            if (createResult && createResult.success) {
                //Check existing customer should return customer and customer.hasOpenPinboard
                const existingCustomer = await this.$store.dispatch('checkExistingCustomer', this.customer.email);
                if(existingCustomer?.success){
                    customer = Object.assign(customer, existingCustomer.data );
                    this.$store.dispatch('setCustomer', customer);
                }else{
                    customer = Object.assign(customer, {email: this.customer.email});
                    await this.$store.dispatch('setCustomer', customer);
                }
                window.setPinboardModalWidth();
                this.showProjectModal = true;   
            }
        },
        async verifyEmailAthenticateAndCreatePinboard() {
            await this.$store.dispatch('verifyEmailAthenticateAndCreatePinboard', this.customerData.otp);
            window.dispatchEvent(new CustomEvent('user:isLoggedIn', { detail: true }));
        },
        async signup(){
            if(!this.validateInput('email', this.customer.email, 'email', true)){
                return;
            }
            if(!this.validateInput('job_title', this.pinboard.job_title, '', true)){
                return;
            }
            if(!this.validateInput('name', this.customer.name, 'string', true)){
                return;
            }
            // if(!this.validateInput('companyName', this.customer.companyName, 'string', true)){
            //     return;
            // }
            //Signup the customer
            await this.$store.dispatch('registerCustomer', {...this.customer, job_title: this.pinboard.job_title});
        },
        async login(){
            const authUser = await this.$store.dispatch('verifyEmail', { email: this.customer.email, otp: this.customerData.otp });
            return authUser;
        },
        async saveProject(){

        },
        async removePinboardItem(pinboardItem, index) {
            await this.$store.dispatch('removePinboardItem', { pinboardItem, index });
        },
        async reorderPinboardItems(event) {
            await this.$store.dispatch('reorderPinboardItems', { items: event.items });
        },
        async updateQuantity(model_id, model_type, quantity) {
            await this.$store.dispatch('updatePinboardItemQuantity', { model_id, model_type, quantity });
        },
        async addPinboardItemComment(item, index, newComments = false) {
            const key = this.getKey(item, index);
            const comment = newComments ? item.newComments[0] : (item.comments && item.comments[0]) || '';
            console.log('comment component :- ', comment);
            await this.$store.dispatch('addPinboardItemComment', { pinboard_item_id: item.pinboard_item_id, index, comment });
            // Reset editing state after saving
            this.$set(this.editingItems, key, false);
        },
        editItemComment(item, index) {
            const key = this.getKey(item, index);
            this.$set(this.editingItems, key, true);
            this.$nextTick(() => {
                const wrapper = this.$el.querySelector(`[data-edit-key="${key}"]`);
                if (wrapper) {
                    const textarea = wrapper.querySelector('.item-comment-box');
                    if (textarea) {
                        textarea.style.height = 'auto';
                        textarea.style.height = Math.max(textarea.scrollHeight, 46) + 'px';
                    }
                }
            });
        },
        updateItemComment(item, property, value) {
            this.$set(item[property], 0, value);
        },
        onDragStart(event) {
            this.editingItem = event.item.data;
        },
        onDragEnd(event) {
            this.editingItem = null;
        },
        handleCloseTimeSlotsModal() {
            const messageContainer = document.getElementById('show-message-container');
            messageContainer.innerHTML = '';
            this.pendingBookingData = null;
            if (this.bookingOtpTimerRef) {
                clearInterval(this.bookingOtpTimerRef);
                this.bookingOtpTimerRef = null;
            }
        },
        nearestShowroomRecord() {
            const raw = this.nearestShowroom;
            if (!raw || Array.isArray(raw)) {
                return {};
            }
            return raw;
        },
        buildShowroomBookingPayload(customerIdFromAuth) {
            const ns = this.nearestShowroomRecord();
            const duration = 30;
            const tourType = 'physicalTour';
            const timeZoneEl = document.getElementById('choose-timezone');
            const timeZone =
                timeZoneEl && timeZoneEl.value ? timeZoneEl.value : 'Asia/Dhaka';
            const locationAddress = ns.address || '';
            const { start, end } = buildMeetingDates(
                this.selectedDate,
                this.selectedSlotValue,
                duration
            );
            const meetingLink = makeMeetingLink({
                start,
                end,
                title: 'Meeting',
                location: locationAddress,
            });
            const cid =
                customerIdFromAuth !== undefined && customerIdFromAuth !== ''
                    ? customerIdFromAuth
                    : '';
            return {
                showroom_contact_id: ns.showroom_contact_id ?? 1,
                customer_id: cid,
                customer_name: this.bookingName,
                email: this.bookingEmail,
                label: 'Meeting',
                showroom_id: ns.showroom_id ?? 1,
                tour_type: tourType,
                date: this.selectedDate,
                meeting_time: this.selectedSlotValue,
                duration,
                time_zone: timeZone,
                location: locationAddress,
                meeting_link: meetingLink,
                pinboard_id: this.pinboard.pinboard_id,
            };
        },
        startBookingOtpTimer(duration = 120) {
            const resendOtpButton = document.getElementById('resend-otp-button');
            const otpTimerText = document.getElementById('otp-timer-text');
            if (!otpTimerText) {
                return;
            }
            if (this.bookingOtpTimerRef) {
                clearInterval(this.bookingOtpTimerRef);
                this.bookingOtpTimerRef = null;
            }
            if (resendOtpButton) {
                resendOtpButton.disabled = true;
            }
            let timeLeft = duration;
            this.bookingOtpTimerRef = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                otpTimerText.textContent = `Resend in ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                if (timeLeft <= 0) {
                    clearInterval(this.bookingOtpTimerRef);
                    this.bookingOtpTimerRef = null;
                    otpTimerText.textContent = '';
                    if (resendOtpButton) {
                        resendOtpButton.disabled = false;
                    }
                }
                timeLeft--;
            }, 1000);
        },
        async submitShowroomBooking(messageContainer, bookingData) {
            const response = await this.$store.dispatch('bookNow', bookingData);
            if (response && response.success) {
                this.pendingBookingData = null;
                if (this.bookingOtpTimerRef) {
                    clearInterval(this.bookingOtpTimerRef);
                    this.bookingOtpTimerRef = null;
                }
                const modalEl = this.$refs.timeSlotsModal;
                if (modalEl && window.bootstrap) {
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                }
                this.checkPinboardProcess('showroom-visit');
            } else {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${response?.message || this.fb.errors?.bookNow || 'Booking failed'}</div>`;
            }
        },
        async handleTimeSlotsModalVerifyOtp() {
            const messageContainer = document.getElementById('show-message-container');
            const verifyEmailButton = document.getElementById('verify-email-button-time-slots');
            this.updateOtp();
            const otp = this.otpDigits.join('');
            if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please enter the 6-digit code</div>`;
                return;
            }
            const bookingData = this.pendingBookingData;
            if (!bookingData) {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Your session expired. Please book again.</div>`;
                return;
            }
            messageContainer.innerHTML = '';
            // const otpResponse = await postBookingJson('/api/verify-email', {
            //     email: this.bookingEmail,
            //     otp,
            //     customer_name: this.bookingName,
            // });
            const otpResponse = await this.$store.dispatch('verifyEmail', { email: this.bookingEmail, otp: this.customerData.otp });
            if (!otpResponse || !otpResponse.success) {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${otpResponse?.message || 'OTP verification failed'}</div>`;
                return;
            }
            messageContainer.innerHTML = '';
            if (verifyEmailButton) {
                verifyEmailButton.disabled = true;
                const originalVerifyBtnHtml = verifyEmailButton.innerHTML;
                verifyEmailButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Booking...';
                try {
                    await this.submitShowroomBooking(messageContainer, bookingData);
                } finally {
                    verifyEmailButton.disabled = false;
                    verifyEmailButton.innerHTML = originalVerifyBtnHtml;
                }
            } else {
                await this.submitShowroomBooking(messageContainer, bookingData);
            }
        },
        async handleBookingClick(type) {
            let userAuthDetails = {};
            try {
                userAuthDetails = JSON.parse(
                    localStorage.getItem('userAuthDetails') || '{}'
                );
            } catch (e) {
                userAuthDetails = {};
            }

            const messageContainer = document.getElementById('show-message-container');
            const bookingFormContainer = document.getElementById('booking-form-container');
            const verifyEmailFormContainer = document.getElementById(
                'book-now-verify-email-form-container'
            );

            const emailEl = document.getElementById('ts-email-not-logged-in-email');
            const nameEl = document.getElementById('ts-name');
            const nameInputContainer = document.getElementById('ts-name-container');
            const emailInputContainer = document.getElementById(
                'ts-email-not-logged-in-email-container'
            );

            const email = this.email || (emailEl ? emailEl.value.trim() : '');
            const name = this.bookingName || (nameEl ? nameEl.value.trim() : '');

            if (!name) {
                if (nameInputContainer) {
                    nameInputContainer.classList.remove('invalid-email');
                    nameInputContainer.classList.add('invalid-email');
                    if (nameEl) {
                        nameEl.focus();
                    }
                }
                return;
            }

            if (!email) {
                if (emailInputContainer) {
                    emailInputContainer.classList.remove('invalid-email');
                    emailInputContainer.classList.add('invalid-email');
                    if (emailEl) {
                        emailEl.focus();
                    }
                }
                return;
            }

            if (!validateBookingEmail(email)) {
                if (emailInputContainer) {
                    emailInputContainer.classList.add('invalid-email');
                    if (emailEl) {
                        emailEl.focus();
                    }
                }
                return;
            }

            if (emailInputContainer) {
                emailInputContainer.classList.remove('invalid-email');
            }
            if (nameInputContainer) {
                nameInputContainer.classList.remove('invalid-email');
            }

            if (!this.selectedSlotValue) {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please select a time slot</div>`;
                return;
            }

            messageContainer.innerHTML = '';

            this.bookingEmail = emailEl ? emailEl.value.trim() : email;
            this.bookingName = nameEl ? nameEl.value.trim() : name;

            const $bookBtn = document.getElementById('th-book-time-btn');
            const originalBtnHtml = $bookBtn ? $bookBtn.innerHTML : '';
            const loadingHtml =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';
            if ($bookBtn) {
                $bookBtn.disabled = true;
                $bookBtn.innerHTML = loadingHtml;
            }

            const customerId = userAuthDetails.customer_id
                ? userAuthDetails.customer_id
                : '';

            try {
                const bookingData = this.buildShowroomBookingPayload(customerId);

                const checkExistingBooking = await postBookingJson(
                    '/api/check-existing-booking',
                    bookingData
                );
                // const checkExistingBooking = await this.$store.dispatch('checkExistingBooking', bookingData);
                if (!checkExistingBooking || !checkExistingBooking.success) {
                    messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${checkExistingBooking?.message || 'Booking check failed'}</div>`;
                    return;
                }

                messageContainer.innerHTML = '';

                const loggedUser = this.$store.getters.loggedInUser;
                const loggedEmail =
                    loggedUser && loggedUser.email
                        ? String(loggedUser.email).trim().toLowerCase()
                        : '';
                const bookingEmailNorm = String(this.bookingEmail)
                    .trim()
                    .toLowerCase();
                const emailMatchesLoggedIn = Boolean(
                    loggedEmail && loggedEmail === bookingEmailNorm
                );

                if (emailMatchesLoggedIn) {
                    bookingFormContainer.classList.remove('d-none');
                    verifyEmailFormContainer.classList.add('d-none');
                    await this.submitShowroomBooking(messageContainer, bookingData);
                    return;
                }

                this.otpDigits = ['', '', '', '', '', ''];

                const verifySendResponse = await this.$store.dispatch('sendEmailVerification', { email: this.bookingEmail, customer_name: this.bookingName });
                if (!verifySendResponse || !verifySendResponse.success) {
                    messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${verifySendResponse?.message || 'Unable to send verification code'}</div>`;
                    return;
                }

                messageContainer.innerHTML = '';

                if (
                    verifySendResponse.customer &&
                    verifySendResponse.customer.customer_id
                ) {
                    bookingData.customer_id =
                        verifySendResponse.customer.customer_id;
                }

                this.pendingBookingData = { ...bookingData };
                this.startBookingOtpTimer();

                bookingFormContainer.classList.add('d-none');
                verifyEmailFormContainer.classList.remove('d-none');
            } catch (error) {
                messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${error?.message || 'Booking failed'}</div>`;
            } finally {
                if ($bookBtn) {
                    $bookBtn.disabled = false;
                    $bookBtn.innerHTML = originalBtnHtml;
                }
            }
        },
        async handleShowroomVisitBooking(event) {
            event.preventDefault();
            this.showBookingModal = true;
            // nearest showroom record
            // dispatch get nearest showroom
            // await this.$store.dispatch('getNearestShowroom');
            setTimeout(() => {
                window.initFlatpickr();
                window.initTimezoneChoices();
                const calendarContainer = document.querySelector(".flatpickr-days");
                const component = this;
                if (calendarContainer) {
                  calendarContainer.addEventListener("click", async function (e) {
                    const day = e.target.closest(".flatpickr-day");
                    if (day &&
                        !day.classList.contains("disabled") &&
                        !day.classList.contains("flatpickr-prev-month") &&
                        !day.classList.contains("flatpickr-current-month") &&
                        !day.classList.contains("flatpickr-next-month")) {
                        // Get date from aria-label
                        const selectedDate = day.getAttribute("aria-label");
                        const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB');
                        const parts = formattedDate.split('/');
                        const selectedDateFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        component.selectedDate = selectedDateFormatted;
                        console.log("selectedDateFormatted =", selectedDateFormatted);
                        document.getElementById("ts-selected-date").textContent = selectedDateFormatted;
                        // component.selectedDate = formattedDate;
                        // console.log("component.selectedDate =", component.selectedDate);
                        await component.$store.dispatch('getBookedData', selectedDateFormatted);
                        component.openModal();
                        console.log(component.selectedDate, "component.selectedDate");
                       
                        const bookedTimes = component.bookedData.map((row) => (row && row.meeting_time ? row.meeting_time : ''))
                          .filter(Boolean);
                          console.log("bookedTimes =", bookedTimes);
                        //   component.openModal();
                        component.markBookedTimeSlots(bookedTimes);
                    }
                  });
                }
            }, 200);
        },
        async changeDate(date) {
            if(!date) return;
            this.selectedDate = date;
            document.getElementById("ts-selected-date").textContent = date;
            await this.$store.dispatch('getBookedData', date);
            const bookedTimes = this.$store.getters.bookedData?.map((row) => (row && row.meeting_time ? row.meeting_time : ''))
              .filter(Boolean);
            this.markBookedTimeSlots(bookedTimes);
        },
        checkPinboardProcess(type) {
            // Save data
            localStorage.setItem(
              'pinboard_processed',
              JSON.stringify({
                pinboard_id: this.pinboard.pinboard_id,
                processed_method: type
              })
            );
          
            // Open new tab
            window.open(`/pinboards/${this.pinboard.pinboard_id}/booking/${type}`, '_blank');
          
            // Check every 10 seconds for up to 5 minutes
            const maxTime = 5 * 60 * 1000; // 5 minutes
            const intervalTime = 10 * 1000; // 10 seconds
            let elapsed = 0;
          
            const interval = setInterval(() => {
              const data = localStorage.getItem('pinboard_processed');
          
              if (!data) {
                // Data missing → reload immediately
                location.reload();
                clearInterval(interval);
                return;
              }
          
              elapsed += intervalTime;
              if (elapsed >= maxTime) {
                // Stop after 5 minutes
                clearInterval(interval);
              }
            }, intervalTime);
        },
        finalHref(type) {
            return `/pinboards/${this.pinboard.pinboard_id}/booking/${type}`;
        },
        finalPageRedirect(type, event) {
            event.preventDefault();
            this.checkPinboardProcess(type);
            // console.log("finalPageRedirect type =", type);
            // window.open(this.finalHref(type), '_blank');
        },
        validateInput(field, val, inputType, required = false) {
            this.clearError(field);
            if (!val && required) {
                this.addError(field, 'This field is required');
                return false;
            }
           
            switch(inputType){
                case 'string':
                    // Only allow a-z, A-Z, 0-9, and special characters: #, _, @
                    const allowedRegex = /^[\w\s#_@-]+$/;
                    if (!allowedRegex.test(val)) {
                        this.addError(field, 'Only letters, numbers, spaces, #, _ and @ are allowed');
                        return false;
                    }
                    break;
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
                    if (!emailRegex.test(val)) {
                        this.addError(field, 'Enter a valid email address');
                        return false;
                    }
                    break;
                case 'number':
                    if (!/^\d+$/.test(val)) {
                        this.addError(field, 'Enter a valid number');
                        return false;
                    }
                    break;
                default:
                    break;
            }
            return true;
        },
        addError(field, message) {
            this.$store.commit('SET_ERROR', { key: field, error: message });
        },
        clearError(field) {
            this.$store.commit('CLEAR_ERROR', field);
        },
        handleOtpInput(index, event) {
            const value = event.target.value;
        
            // Allow only numbers
            if (!/^[0-9]$/.test(value)) {
                this.otpDigits[index] = '';
                return;
            }
        
            // Move to next box
            if (index < 5) {
                this.$refs.otpInputs[index + 1].focus();
            }
        
            this.updateOtp();
        },
        handleBackspace(index) {
            if (!this.otpDigits[index] && index > 0) {
                this.$refs.otpInputs[index - 1].focus();
            }
        },
        updateOtp() {
            this.customerData.otp = this.otpDigits.join('');
        },
        handleOtpPaste(event, startIndex = 0) {
            event.preventDefault();
        
            const raw = event.clipboardData.getData('text') || '';
            const digits = raw.replace(/\D/g, '');
        
            if (!digits) return;
        
            digits.split('').forEach((d, i) => {
                const idx = startIndex + i;
                if (idx < this.otpDigits.length) {
                    this.$set(this.otpDigits, idx, d);
                }
            });
        
            const focusIndex = Math.min(startIndex + digits.length, this.otpDigits.length - 1);
        
            this.$nextTick(() => {
                this.$refs.otpInputs[focusIndex]?.focus();
            });
        
            this.updateOtp();
        },
        resendEmail() {
            console.log("Resend email clicked");
        },
        // time slot booking
        async handleDayClick(e) {
            this.openModal();
        },
        openModal() {
            const modalEl = this.$refs.timeSlotsModal;
        
            if (modalEl && window.bootstrap) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        },
        resetAllTimeSlots() {
            this.bookedTimes = [];
            // your existing reset logic
        },
        markBookedTimeSlots(bookedTimes) {
            console.log("Booked bookedTimes:", bookedTimes);
          
            const slots = document.querySelectorAll('.th-time-slot');
          
            // STEP 1: Reset সব slot (IMPORTANT)
            slots.forEach((slot) => {
              const checkbox = slot.querySelector('input[type="checkbox"]');
              const icon = slot.querySelector('i');
          
              slot.classList.remove('active', 'disabled', 'time-slot-disabled');
              slot.removeAttribute('disabled');
              slot.classList.add('no-hover');
          
              if (checkbox) {
                checkbox.disabled = false;
                checkbox.classList.remove('d-none', 'time-slot-disabled');
              }
          
              if (icon) {
                icon.classList.add('d-none');
              }
            });
          
            // STEP 2: booked list prepare
            const bookedSet = new Set(
              (Array.isArray(bookedTimes) ? bookedTimes : [])
                .map((time) => String(time || '').trim())
                .filter(Boolean)
            );
          
            // যদি empty হয়, এখানেই stop (reset already done)
            if (bookedSet.size === 0) return;
          
            // STEP 3: apply booked state
            slots.forEach((slot) => {
              const checkbox = slot.querySelector('input[type="checkbox"]');
              const icon = slot.querySelector('i');
          
              if (!checkbox) return;
          
              const value = String(checkbox.value || '').trim();
          
              if (!bookedSet.has(value)) return;
          
              slot.classList.add('active', 'disabled', 'time-slot-disabled');
              slot.setAttribute('disabled', 'disabled');
              slot.classList.remove('no-hover');
          
              checkbox.checked = false;
              checkbox.disabled = true;
              checkbox.classList.add('d-none', 'time-slot-disabled');
          
              if (icon) icon.classList.remove('d-none');
            });
        },
        markBookedTimeSlots_new(bookedTimes) {
        const bookedSet = new Set(
            (Array.isArray(bookedTimes) ? bookedTimes : [])
            .map(time => String(time).trim())
            .filter(Boolean)
        );
        
        document.querySelectorAll('.th-time-slot').forEach((slot) => {
            const checkbox = slot.querySelector('input[type="checkbox"]');
            if (!checkbox) return;
        
            const timeValue = String(checkbox.value || '').trim();
        
            if (bookedSet.has(timeValue)) {
            // booked → hide
            slot.classList.add('d-none');
            checkbox.checked = false;
            checkbox.disabled = true;
            } else {
            // available → show
            slot.classList.remove('d-none');
            checkbox.disabled = false;
            }
        });
        },
        toggleSlot(event, slotId, slotValue) {
            event.preventDefault();
            const slot = event.currentTarget.closest('.th-time-slot');
            if (!slot || slot.classList.contains('disabled') || slot.classList.contains('time-slot-disabled')) {
                return;
            }

            document.querySelectorAll('.th-time-slot:not(.disabled):not(.time-slot-disabled)')
                .forEach((el) => {
                    el.classList.remove('active', 'selected');
                });

            slot.classList.add('selected', 'active');

            this.selectedSlot = slotId;
            this.checkedSlot = slotId;
            this.selectedSlotValue = slotValue;
        },
        async submitComment() {
            const commentText = (this.comment || '').trim();
            if (commentText.length < 2) {
                this.$store.commit('SET_ERROR', {
                    key: 'addCommentItemToPinboard',
                    error: 'Please enter at least 2 characters.',
                });
                return;
            }
            this.$store.commit('CLEAR_ERROR', 'addCommentItemToPinboard');
            try {
                await this.$store.dispatch('addCommentItemToPinboard', commentText);
                this.comment = '';
            } catch (err) {
                console.error('Failed to submit comment', err);
            }
        },
        removeCommentImage(file, index) {
            this.$store.dispatch('removeCommentItemImage', { file, index });
        },
        uploadCommentImage(event) {
            const file = event?.target?.files && event.target.files[0];
            if (!file) return;
            const objectURL = URL.createObjectURL(file);
            this.$store.dispatch('uploadCommentItemImage', { file, objectURL });
            if (event.target) {
                event.target.value = '';
            }
        },
        scheduleAutocompleteSearch() {
            if (this._autocompleteDebounceId != null) {
                clearTimeout(this._autocompleteDebounceId);
            }
            this._autocompleteDebounceId = setTimeout(() => {
                this._autocompleteDebounceId = null;
                this.runAutocompleteSearch();
            }, 200);
        },
        async runAutocompleteSearch() {
            const q = (this.localFilter.searchValue || '').trim();
            await this.$store.dispatch('searchPinboardAutocomplete', q);
        },
        handleAutocomplete() {
            this.scheduleAutocompleteSearch();
        },
        onAutocompleteFocus() {
            if ((this.localFilter.searchValue || '').trim()) {
                this.scheduleAutocompleteSearch();
            }
        },
        handleClearAutocomplete() {
            this.localFilter.searchValue = '';
            this.$store.commit('SET_AUTOCOMPLETE_SUGGESTIONS', []);
            this.$store.commit('SET_AUTOCOMPLETE_OPEN', false);
            if (this._autocompleteDebounceId != null) {
                clearTimeout(this._autocompleteDebounceId);
                this._autocompleteDebounceId = null;
            }
        },
        async selectAutocompleteProduct(product) {
            if (!product) return;
            const parts = product.model_type.split('-');
            const modelId = parts.pop(); // "4"
            const modelType = parts.join('-').toLowerCase(); // "product"

            try {
                const payload = {
                    model_id: modelId,
                    model_type: modelType,
                    title: product.title,
                    photo: product.dataSrc,
                    quantity: 1,
                    unit_price: 0,
                    description: product.sku ? `Demo · ${product.sku}` : '',
                    language_id: 1,
                    comments: [],
                };
                await this.$store.dispatch('addToPinboard', payload);
            } catch (e) {
                console.error('addToPinboard from autocomplete failed', e);
            }
            this.handleClearAutocomplete();
        },
        //camera capture start
        triggerCameraCapture() {
            this.openLiveCameraOrFallback();
        },

        openLiveCameraOrFallback() {
            const canUseLiveCamera =
                typeof navigator !== 'undefined' &&
                navigator.mediaDevices &&
                typeof navigator.mediaDevices.getUserMedia === 'function' &&
                (typeof window === 'undefined' || window.isSecureContext !== false);
            if (!canUseLiveCamera) {
                this.openNativeCameraFilePicker();
                return;
            }
            navigator.mediaDevices
                .getUserMedia({
                    video: { facingMode: { ideal: 'environment' } },
                    audio: false,
                })
                .then((stream) => {
                    this._mediaStream = stream;
                    this.showLiveCamera = true;
                    this.$nextTick(() => this.attachLiveCameraStream(stream));
                })
                .catch(() => {
                    this.openNativeCameraFilePicker();
                });
        },

        attachLiveCameraStream(stream) {
            const video = this.$refs.liveCameraVideo;
            if (!video) return;
            video.srcObject = stream;
            const play = () => {
                video.play().catch(() => {});
            };
            if (video.readyState >= 2) {
                play();
            } else {
                video.addEventListener('loadedmetadata', play, { once: true });
            }
        },

        stopLiveCameraStream() {
            if (this._mediaStream) {
                this._mediaStream.getTracks().forEach((t) => t.stop());
                this._mediaStream = null;
            }
            const video = this.$refs.liveCameraVideo;
            if (video && video.srcObject) {
                video.srcObject = null;
            }
            this.showLiveCamera = false;
        },

        cancelLiveCamera() {
            this.stopLiveCameraStream();
        },

        snapLiveCameraPhoto() {
            const video = this.$refs.liveCameraVideo;
            if (!video || !video.videoWidth) {
                this.cancelLiveCamera();
                return;
            }
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            canvas.toBlob(
                (blob) => {
                    this.stopLiveCameraStream();
                    if (!blob || !blob.type.startsWith('image/')) return;
                    this.revokeCameraBlobUrl();
                    this._cameraBlobUrl = URL.createObjectURL(blob);
                    this.cameraImagePreview = this._cameraBlobUrl;
                    this.addImageTitle = '';
                    this.addImageComment = '';
                    this.addImageModalError = false;
                    this.showAddImageModal = true;
                },
                'image/jpeg',
                0.92
            );
        },

        openNativeCameraFilePicker() {
            const input = this.$refs.cameraCaptureInput;
            if (input) input.click();
        },

        revokeCameraBlobUrl() {
            if (this._cameraBlobUrl) {
                URL.revokeObjectURL(this._cameraBlobUrl);
                this._cameraBlobUrl = null;
            }
        },

        onCameraCaptureChange(event) {
            const input = event.target;
            const file = input.files && input.files[0];
            input.value = '';
            if (!file || !file.type.startsWith('image/')) return;

            this.revokeCameraBlobUrl();
            this._cameraBlobUrl = URL.createObjectURL(file);
            this.cameraImagePreview = this._cameraBlobUrl;
            this.addImageTitle = '';
            this.addImageComment = '';
            this.addImageModalError = false;
            this.showAddImageModal = true;
        },
        cancelAddCameraImage() {
            this.showAddImageModal = false;
            this.cameraImagePreview = '';
            this.addImageTitle = '';
            this.addImageComment = '';
            this.addImageModalError = false;
            this.revokeCameraBlobUrl();
        },
        async confirmAddCameraImage() {
            const titleIn = (this.addImageTitle || '').trim();
            const comment = (this.addImageComment || '').trim();
            if (!titleIn && !comment) {
                this.addImageModalError = true;
                return;
            }
            this.addImageModalError = false;
            const photo = this._cameraBlobUrl;
            if (!photo) return;

            const title = titleIn || (comment ? comment.slice(0, 72) : 'Pinboard image');
            const payload = {
                model_id: Date.now(),
                model_type: 'image',
                title,
                photo,
                quantity: 1,
                unit_price: 0,
                description: comment || title,
                language_id: 1,
                comments: comment ? [comment] : [],
            };
            try {
                await this.$store.dispatch('addToPinboard', payload);
            } catch (e) {
                console.error('addToPinboard camera image failed', e);
                return;
            }
            this.showAddImageModal = false;
            this.cameraImagePreview = '';
            this.addImageTitle = '';
            this.addImageComment = '';
            this.addImageModalError = false;
            this._cameraBlobUrl = null;
        },
        changeProject(pinboardId) {
            this.selectedProjectId = pinboardId;
            this.showProjectDropdown = false;
            this.$store.dispatch('getProjectByPinboardId', pinboardId);
        },
        openNewProjectModal() {
            this.showProjectDropdown = false;
            this.showCreateNewProjectModal = true;
        },
        handleCloseCreateNewProjectModal() {
            this.showCreateNewProjectModal = false;
            this.newProjectName = '';
        },
        handleCreateNewProjectClick(event) {
            event.preventDefault();
            const user_id = this.loggedInUser.user_id;
            const customer_id = this.loggedInCustomer.customer_id;
            const name = (this.newProjectName || '').trim();
            const nameInput = document.getElementById('new-project-name');
            if (!name) {
                nameInput?.classList.add('is-invalid');
                return;
            }
            nameInput?.classList.remove('is-invalid');
            const payload = {
                job_title: name,
                customer_id: customer_id,
                user_id: user_id,
                pinboard_items: [],
            };
           const response = this.$store.dispatch('createNewProject', payload);
           if (response) {
            this.selectedProjectId = response.pinboard_id;   
            this.displayProjectTitle = name;         
            this.showCreateNewProjectModal = false;
           } else {
            console.error('Failed to create new project', response.error);
           }
            this.showCreateNewProjectModal = false;
        },
    },
    //camera capture end
    filters: {
        capitalize(value) {
            return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
        }
    },
    watch: {
        fb: function(newVal) {
            // console.log(newVal);
        },
        projectMenuItems: {
            handler(items) {
                if (!items || !items.length) {
                    return;
                }
                const sid = this.selectedProjectId;
                const inList = (id) =>
                    id !== null &&
                    id !== undefined &&
                    id !== '' &&
                    items.some((x) => String(x.pinboard_id) === String(id));
                if (inList(sid)) {
                    return;
                }
                const pbId = this.pinboard && this.pinboard.pinboard_id;
                if (inList(pbId)) {
                    this.selectedProjectId = pbId;
                    return;
                }
                this.selectedProjectId = items[0].pinboard_id;
            },
            immediate: true,
        },
        'pinboard.pinboard_id'(id) {
            const items = this.projectMenuItems;
            if (!items || !items.length || id === null || id === undefined || id === '') {
                return;
            }
            if (!items.some((x) => String(x.pinboard_id) === String(id))) {
                return;
            }
            this.selectedProjectId = id;
        },
    },

    template: /* html */ `
    <div class="bg-gray pinboard-app-root">
        <!-- Desktop: original single-row header (md and up) -->
        <div class="offcanvas-header th-header-upper d-none d-md-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="offcanvas-header th-header-lower">
                    <h5 id="offcanvasRightLabel2">Virtual Pinboard</h5>
                </div>
            </div>
            <div
                class="d-flex align-items-center justify-content-end pinboard-header-info"
                v-if="loggedInUser && loggedInUser.email"
            >
                <div class="pinboard-header-project-wrap text-end position-relative">
                    <div class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-end">
                        <button
                            type="button"
                            class="bg-transparent pinboard-project-chevron-btn p-0 border-0 align-baseline"
                            :aria-expanded="showProjectDropdown ? 'true' : 'false'"
                            aria-haspopup="true"
                            aria-label="Choose project"
                            @click.stop="showProjectDropdown = !showProjectDropdown"
                        >
                        <span class="pinboard-project-name fw-bold">{{ displayProjectTitle }}</span>
                            <i
                                class="fa-solid fa-chevron-down pinboard-project-chevron"
                                :class="{ 'is-open': showProjectDropdown }"
                            ></i>
                        </button>
                    </div>
                    <div class="pinboard-project-email text-muted small">{{ loggedInUser.email }}</div>

                    <div
                        v-show="showProjectDropdown"
                        class="pinboard-project-menu"
                        @click.stop
                    >
                        <button
                            type="button"
                            class="pinboard-project-menu-item pinboard-project-menu-create"
                            @click="openNewProjectModal()"
                        >
                            + Create New Project…
                        </button>
                        <div class="pinboard-project-menu-divider" aria-hidden="true"></div>
                        <button
                            v-for="item in projectMenuItems"
                            :key="'desk-' + item.pinboard_id"
                            type="button"
                            class="pinboard-project-menu-item d-flex justify-content-between align-items-center gap-2"
                            :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                            @click="changeProject(item.pinboard_id)"
                        >
                            <span class="pinboard-project-menu-label text-truncate">{{ item.pinboard_name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile: title + close, then project block (below md only) -->
        <div class="offcanvas-header th-header-upper d-flex d-md-none flex-column gap-2 align-items-stretch">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="offcanvas-header th-header-lower mb-0 flex-grow-1 min-w-0">
                    <h5 class="mb-0">Virtual Pinboard</h5>
                </div>
                <button
                    type="button"
                    class="btn btn-link p-1 border-0 text-body pinboard-offcanvas-close flex-shrink-0"
                    data-bs-dismiss="offcanvas"
                    aria-label="Close Virtual Pinboard"
                >
                    <i class="fa-solid fa-xmark fa-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div
                class="d-flex pinboard-header-info w-100"
                v-if="loggedInUser && loggedInUser.email"
            >
                <div class="pinboard-header-project-wrap pinboard-header-project-wrap--mobile text-start position-relative w-100">
                    <div class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-start">
                    <button
                    type="button"
                    class="bg-transparent pinboard-project-chevron-btn p-0 border-0 align-baseline"
                    :aria-expanded="showProjectDropdown ? 'true' : 'false'"
                    aria-haspopup="true"
                    aria-label="Choose project"
                    @click.stop="showProjectDropdown = !showProjectDropdown"
                    >
                    <span class="pinboard-project-name fw-bold">{{ displayProjectTitle }}</span>
                            <i
                                class="fa-solid fa-chevron-down pinboard-project-chevron"
                                :class="{ 'is-open': showProjectDropdown }"
                            ></i>
                        </button>
                    </div>
                    <div class="pinboard-project-email text-muted small">{{ loggedInUser.email }}</div>

                    <div
                        v-show="showProjectDropdown"
                        class="pinboard-project-menu pinboard-project-menu--mobile"
                        @click.stop
                    >
                        <button
                            type="button"
                            class="pinboard-project-menu-item pinboard-project-menu-create"
                            @click="openNewProjectModal()"
                        >
                            + Create New Project…
                        </button>
                        <div class="pinboard-project-menu-divider" aria-hidden="true"></div>
                        <button
                            v-for="item in projectMenuItems"
                            :key="'mob-' + item.pinboard_id"
                            type="button"
                            class="pinboard-project-menu-item d-flex justify-content-between align-items-center gap-2"
                            :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                            @click="changeProject(item.pinboard_id)"
                        >
                            <span class="pinboard-project-menu-label text-truncate">{{ item.pinboard_name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Search + list + footer: md–lg = 2 columns; stacked on mobile & lg+ -->
        <div class="offcanvas-body">
            <div class="th-pinboard th-pinboard--tablet-split">
                <div class="row gx-0 gx-md-3 gx-lg-0 gy-0 align-items-md-stretch align-items-lg-start mx-0 pinboard-offcanvas-tablet-row">
                    <div class="col-12 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--main">
                        <div class="mb-20 pb-0 pinboard-offcanvas-search" v-show="loggedInUser && loggedInUser.email">
                            <div class="autocomplete position-relative w-100">
                            <i
                                class="fa-solid fa-search text-muted"
                                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 11; pointer-events: none;"
                                aria-hidden="true"
                            ></i>
                            <input
                                type="text"
                                class="form-control th-choices-select z-index-10 font-size-16"
                                id="choose-product-name"
                                :placeholder="autoCompletePlaceholderText"
                                autocomplete="off"
                                :disabled="disableAutocomplete"
                                @input="handleAutocomplete"
                                @focus="onAutocompleteFocus"
                                v-model="localFilter.searchValue"
                                style="padding:11px 36px 11px 38px;"
                            />
                            <i
                                class="fa fa-close hover"
                                @click.prevent="handleClearAutocomplete"
                                v-show="localFilter.searchValue"
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 11;"
                                role="button"
                                aria-label="Clear search"
                            ></i>
                            <ul
                                v-show="autocompleteOpen && autocompleteSuggestions.length"
                                class="dropdown-menu show pinboard-autocomplete-list w-100 shadow-sm mt-1"
                                style="max-height: 260px; overflow-y: auto; z-index: 1060;"
                            >
                                <li
                                v-for="row in autocompleteSuggestions"
                                :key="'ac-' + row.id"
                                class="dropdown-item py-2 d-flex align-items-center gap-2"
                                style="cursor: pointer;"
                                @mousedown.prevent="selectAutocompleteProduct(row)"
                                >
                                <img
                                    :src="row.dataSrc"
                                    :alt="row.title"
                                    width="48"
                                    height="36"
                                    class="rounded flex-shrink-0"
                                    style="object-fit: cover;"
                                />
                                <span class="text-truncate small">
                                    <span class="d-block fw-semibold">{{ row.title }}</span>
                                    <span class="text-muted" v-if="row.sku">{{ row.sku }}</span>
                                </span>
                                </li>
                            </ul>
                            </div>
                        </div>
                        <div id="pinboard-items" class="th-pinboard-upper th-ofc-pinboard-item-upper pinboard-items-scroll">
                            <!-- Placeholder (loading state) -->
                            <div v-if="loading('getPinboard')" class="pinboard-loading-placeholder">
                                <div
                                    v-for="n in 3"
                                    :key="'pinboard-loading-' + n"
                                    style="height:150px;border:1px solid #cfcfcf; background:white;border-radius:6px;margin-bottom:12px;"
                                ></div>
                            </div>
                            <draggable 
                                v-if="!loading('getPinboard') && Array.isArray(items)"
                                v-model="items" 
                                :handle="'.draggable-handle'"
                                :clone="cloneItem"
                                ghost-class="pinboard-ghost"
                                chosen-class="pinboard-chosen"
                                drag-class="pinboard-drag"
                                @start="onDragStart"
                                @end="onDragEnd"
                                tag="div"
                            >
                            <transition-group>
                                <div
                                    v-for="(item, index) in items"
                                    :key="getKey(item, index)"
                                    class="row th-pinboard-item"
                                >
                                    <!-- DRAG PREVIEW: thumbnail + title + close -->
                                    <div v-if="item._isDragPreview" class="pinboard-drag-preview">
                                        <img :src="item.photo" class="thumb" />
                                        <span class="title">{{ item.title }}</span>
                                        <i class="fa-solid fa-xmark close-icon"></i>
                                    </div>
                                    <!-- NORMAL ITEM -->
                                    <template v-else>
                                        <div class="pinboard col-md-12">
                                            <!-- CARD ITEM -->
                                            <div class="card-item">
                                                <div class="card-left">
                                                    <img :src="item.photo" :alt="item.title" />
                                                </div>

                                                <div class="card-content">
                                                    <div class="card-header">
                                                        <div>
                                                            <h3>{{ item.title | capitalize }}</h3>
                                                            <p class="type">{{ item.model_type | capitalize }}</p>
                                                        </div>

                                                        <div class="card-actions">
                                                            <div class="text-darkgrey draggable-handle">
                                                            <i class="fa fa-list"></i>
                                                            </div>
                                                            <div class="remove-pinboard-btn text-darkgrey border-0 bg-transparent">
                                                            <i class="fa fa-times"
                                                            :data-id="item.model_id"
                                                            :data-model="item.model_type"
                                                            @click.prevent="removePinboardItem(item, index)"
                                                            ></i>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- start pinboard item options -->
                                                        <div class="th-item-product" v-if="item?.options?.variant?.item?.options?.length">
                                                            <!--<span class="mb-2 th-title-20 text-success">Options:</span>-->
                                                            <div class="th-item-footer">
                                                                <div class="th-tag-name">
                                                                    <div
                                                                        class="th-tag"
                                                                        v-for="option in item.options?.variant?.item?.options"
                                                                        :key="option.product_option_id"
                                                                    >
                                                                        {{ option.option_name }} 
                                                                        <span v-if="option.subOption && option.subOption.name">
                                                                            - 
                                                                            <span class="text-muted text-small text-success">( {{ option.subOption.name }} )</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- PINBOARD ITEM ACCESSORIES -->
                                                        <div class="th-item-product" v-if="item.accessories && item.accessories.length > 0">
                                                            <span class="mb-2 th-title-20 text-success">Accessories:</span>
                                                            <div class="th-item-footer">
                                                                <div class="th-tag-name">
                                                                    <div 
                                                                        class="th-tag" 
                                                                        v-for="accessory in item.accessories" 
                                                                        :key="accessory.product_accessories_id"
                                                                    >
                                                                        {{ accessory.title }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end pinboard item options -->



                                                    <div class="card-footer">
                                                        <span @click="toggleItemNote(item)" v-if="!item.comments[0]">
                                                            {{ item._showNote ? '− Hide Note' : '+ Add Note' }}
                                                        </span>
                                                        <!-- note section start -->
                                                            <div v-if="item._showNote || item.comments[0]">
                                                                <div class="th-pinboard-item-edit mt-3" v-if="item.comments[0]" :data-edit-key="getKey(item, index)">
                                                                    <div class="th-pinboard-edit-wrapper d-flex justify-content-between w-100">
                                                                        <div class="w-100 th-pinboard-edit-content">
                                                                            <div v-if="editingItems[getKey(item, index)]" class="p-2">
                                                                                <textarea
                                                                                class="form-control item-comment-box border-0 p-0"
                                                                                rows="1"
                                                                                :value="item.comments[0] || ''"
                                                                                @input="updateItemComment(item, 'comments', $event.target.value)"
                                                                                ></textarea>
                                                                            </div>
                                                                            <div v-else class="p-2 text-muted th-display-pre-line th-pinboard-view-text">
                                                                                {{ item.comments[0] || '' }}
                                                                            </div>
                                                                        </div>

                                                                        <button class="btn" style="width: 100px;">
                                                                            <span v-if="!editingItems[getKey(item, index)]"  @click="editItemComment(item, index)"><i class="fa-solid fa-pencil"></i> Edit</span>
                                                                            <span v-else  @click="addPinboardItemComment(item, index)"><i class="fa-solid fa-check"></i> Post</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="th-pinboard-item-comment mt-3" v-else>
                                                                    <div class="d-flex align-items-start gap-2">
                                                                        <!-- TEXTAREA -->
                                                                        <textarea
                                                                            class="form-control item-comment-box"
                                                                            placeholder="Add a Note"
                                                                            rows="1"
                                                                            @input="updateItemComment(item, 'newComments', $event.target.value)"
                                                                            :value="item.comments[0] || ''"
                                                                        ></textarea>

                                                                        <!-- POST BUTTON -->
                                                                        <button

                                                                            class="th-btn-primary-post text-capitalize "
                                                                            @click.prevent="addPinboardItemComment(item, index, true)">
                                                                            Post
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <!-- end note section -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </transition-group>
                            </draggable>
                
                            <div v-if="!loading('getPinboard') && items.length === 0" class="text-center py-4">
                                No items found
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--actions">
                        <div class="th-pinboard-bottom">
                            <div v-show="loggedInUser">
                                <transition name="pinboard-add-comment-collapse">
                                    <div class="th-pinboard-lower mb-15" id="th-pinboard-user" v-show="showAddComment">
                                        <div class="th-offcanvas-containr">
                                            <div class="th-add-comment-panel">
                                                <div class="th-add-comment-panel-header">
                                                    <div class="th-add-comment-panel-title">Add Comment</div>
                                                    <a
                                                        href="javascript:void(0)"
                                                        class="th-add-comment-panel-collapse"
                                                        @click.prevent="showAddComment = false"
                                                    >
                                                        &mdash; Collapse
                                                    </a>
                                                </div>

                                                <textarea
                                                    v-model="comment"
                                                    :style="fb.errors.addCommentItemToPinboard ? { border: '1px solid red' } : {}"
                                                    @input="clearError('addCommentItemToPinboard')"
                                                    ref="addCommentTextarea"
                                                    class="comment-box th-offcanvas-comment-box th-off-large-commentbox th-add-comment-textarea"
                                                    placeholder="Add A Comment"
                                                ></textarea>

                                                <div class="th-doc-actions d-flex flex-column th-add-comment-actions">
                                                    <div class="d-flex w-100 th-add-comment-preview-row" v-if="commentFiles.length">
                                                        <div
                                                            v-for="(file, idx) in commentFiles"
                                                            :key="file.tmp_name || idx"
                                                            class="d-flex"
                                                            style="width: 100px; height: 100px; background-color: #f0f0f0; position: relative; overflow: hidden;"
                                                        >
                                                            <span
                                                                class="remove-btn"
                                                                :id="'removeBtn-' + idx"
                                                                @click="removeCommentImage(file, idx)"
                                                                style="position: absolute; top: 0px; right: 0px; z-index: 5; background: rgba(207, 30, 30, 0.9); padding: 2px 4px; border-radius: 12px; cursor: pointer;"
                                                            ><i class="fa-solid fa-xmark"></i></span>
                                                            <img :src="file.objectURL" alt="Image" :title="file.name" style="width: 100%; height: 100%; object-fit: cover;"/>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between w-100 th-add-comment-bottom-row">
                                                        <label
                                                            class="th-add-comment-upload-label th-btn-gray text-capitalize mr-10"
                                                            style="cursor:pointer; margin-bottom:0;"
                                                        >
                                                            Upload Image +
                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                style="display:none;"
                                                                @change="uploadCommentImage($event)"
                                                            />
                                                        </label>

                                                        <a
                                                            id="add-comment-button"
                                                            class="th-add-comment-submit-btn th-btn-primary text-capitalize"
                                                            @click.prevent="submitComment"
                                                        >
                                                            Add To Pinboard
                                                        </a>

                                                        <a id="update-pinboard-button" class="th-btn-gray text-capitalize border" style="display: none;">
                                                            <span class="mr-5">Update Pinboard</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                            <div id="th-pinboard-guest" class="">
                                <!-- <start> project name and email input field -->
                                <div class="d-flex flex-column gap-2" v-if="!loggedInUser">
                                    <!-- project name input field -->
                                    <div class="form-group mb-0">
                                        <input 
                                        type="text" 
                                        class="form-control" 
                                        id="project-name" 
                                        name="project-name" 
                                        placeholder="Project Name"
                                        v-model="pinboard.job_title" 
                                        :class="{'is-invalid': fb.errors.job_title }">
                                        <span class="invalid-feedback" v-if="fb.errors.job_title">{{ fb.errors.job_title }}</span>
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="email" 
                                        class="form-control" 
                                        name="email" placeholder="Email Address"
                                        id="email" 
                                        v-model="customer.email" 
                                        :class="{ 'is-invalid': fb.errors.email }">
                                        <span class="invalid-feedback" v-if="fb.errors.email">{{ fb.errors.email }}</span>
                                    </div>
                                </div>
                                <!-- <end> project name and email input field -->
                                <div class="">
                                    <div class="d-flex flex-column gap-25 pinboard-offcanvas-footer-btns" v-if="loggedInUser">
                                        <div class="d-flex flex-row gap-20">
                                            <button
                                                v-show="loggedInUser"
                                                v-if="!showAddComment"
                                                type="button"
                                                class="th-add-comment-toggle-btn"
                                                @click.prevent="showAddComment = true"
                                            >
                                                <span class="th-add-comment-toggle-plus">+</span>
                                                <span class="th-add-comment-toggle-text">Add Comment</span>
                                            </button>

                                            <input
                                                ref="cameraCaptureInput"
                                                type="file"
                                                accept="image/*"
                                                capture="environment"
                                                class="d-none"
                                                @change="onCameraCaptureChange"
                                            />
                                            <button
                                                v-show="loggedInUser"
                                                type="button"
                                                class="th-add-comment-toggle-btn"
                                                @click.prevent="triggerCameraCapture"
                                            >
                                                <span class="th-add-comment-toggle-plus"><i class="fa-solid fa-image"></i></span>
                                                <span class="th-add-comment-toggle-text">Add Image</span>
                                            </button>
                                        </div>
                                        <div class="d-flex flex-row gap-20">
                                            <a
                                                href="/account/pinboards"
                                                class="th-btn-primary text-capitalize border w-100"
                                                id="pinboard-link"
                                            >
                                                <span class="mr-5">Manage Pinboard</span>
                                            </a>
                                            <!-- <a href="/" class="th-btn-gray text-capitalize mr-10 border w-100" id="pinboard-browse-link">
                                                <span class="mr-5">Continue Browsing</span>
                                            </a> -->
                                            <button
                                                type="button"
                                                class="text-reset th-btn-gray text-capitalize border w-100 text-decoration-none"
                                                data-bs-dismiss="offcanvas"
                                                aria-label="Close"
                                            >
                                                Continue Browsing
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <button type="button"
                                        id="create-new-project-button" 
                                        class="th-btn-primary text-capitalize w-100" 
                                        :class="{'disabled': fb.loading.createPinboard, 'is-invalid': fb.errors.createPinboard}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#guestSignupModal"
                                        :disabled="fb.loading.createPinboard"
                                        @click="checkAndCreateTemporaryProject()" style="margin-top: 8px;">
                                            <span class="mr-5" id="create-new-project-button-text">Save Project and Continue</span>
                                            <span v-if="fb.loading.createPinboard" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </button>
                                        <span class="invalid-feedback mt-2" v-if="fb.errors.createPinboard">{{ fb.errors.createPinboard }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal section  -->
        <div v-show="showProjectModal">
            <div class="modal fade th-pinboard-modal backdrop-static" id="guestSignupModal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-bs-backdrop="false" style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1040;">
                <div class="pinboard-modal-container">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content modelBorderRadius">
                            <div class="modal-header">
                                <span v-if="!(loggedInUser && customer?.is_verified && !showBookingModal)">{{ pinboard.job_title }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="save-pinboard-form-container">
                                    <form id="save-pinboard-form" class="guest-signup-form" v-if="!customer?.uuid">
                                        <div class="p-80 th-modal-body-padding">
                                            <div class="form-group">
                                                <input type="text" 
                                                class="form-control" 
                                                v-model="customer.name" 
                                                id="name" name="name" 
                                                placeholder="Name"
                                                :class="{'is-invalid': fb.errors.name }">
                                                <span class="invalid-feedback" v-if="fb.errors.name">{{ fb.errors.name }}</span>
                                            </div>
                                            <div class="form-group">
                                                <input 
                                                type="text" 
                                                class="form-control" 
                                                id="customer.companyName" 
                                                name="organization-name" 
                                                placeholder="Organisation Name (optional)"
                                                v-model="customer.companyName"
                                                :class="{'is-invalid': fb.errors.companyName }">
                                                <span class="invalid-feedback" v-if="fb.errors.companyName">{{ fb.errors.companyName }}</span>
                                            </div>
                                            <div class="form-group">
                                                <input type="phone" class="form-control" id="customer.phone" v-model="customer.phone" name="phone" placeholder="Phone Number (optional)">
                                            </div>
                                            <div>
                                                <button type="submit" class="th-btn-primary text-capitalize w-100 mt-15" 
                                                :class="{'disabled': fb.loading.registerCustomer}"
                                                id="save-pinboard-button" 
                                                :disabled="fb.loading.registerCustomer"
                                                @click.prevent="signup()">
                                                Create Account to Save
                                                <span v-if="fb.loading.registerCustomer" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                <div v-if="(!customer?.is_verified && customer?.uuid)">
                                    <form id="verify-email-form" class="guest-signup-form">
                                    
                                        <div class="text-center py-10 mb-20">
                                            <h6>Verify it's you</h6>
                                            <p>
                                                We've sent a verification code to 
                                                <strong>{{ customer.email }}</strong>. <br>
                                                Please enter the code below to save your project.
                                            </p>
                                        </div>
                                    
                                        <!-- OTP BOXES -->
                                        <div class="otp-wrapper mb-20">
                                            <input
                                                v-for="(digit, index) in otpDigits"
                                                :key="index"
                                                ref="otpInputs"
                                                type="text"
                                                maxlength="1"
                                                class="otp-input"
                                                v-model="otpDigits[index]"
                                                @input="handleOtpInput(index, $event)"
                                                @keydown.backspace="handleBackspace(index)"
                                                @paste="handleOtpPaste($event)"
                                            />
                                        </div>

                                        <div v-if="fb.errors?.createPinboard" class="col-md-10 text-center mx-auto px-2 px-md-3 text-danger" ref="pinboard-create-error">{{ fb.errors?.createPinboard }}</div>
                                    
                                        <!-- PRIMARY BUTTON -->
                                        <button
                                        v-if="!loggedInUser"
                                        type="button"
                                        class="th-btn-primary text-capitalize w-100 mt-15"
                                        :class="{'disabled': fb.loading.verifyEmail}"
                                        :disabled="fb.loading.verifyEmail"
                                        @click="verifyEmailAthenticateAndCreatePinboard"
                                        id="verify-email-button"
                                        >
                                            Verify & Continue
                                            <span
                                                v-if="fb.loading.verifyEmail"
                                                class="spinner-border spinner-border-sm"
                                                role="status"
                                            ></span>
                                        </button>
                                    
                                        <!-- RESEND LINK -->
                                        <div class="text-center mt-15">
                                            <small>
                                                Didn't receive the code?
                                                <a href="javascript:void(0)" @click="resendEmail" class="resend-link">
                                                Resend Email
                                                </a>
                                            </small>
                                        </div>
                                  
                                     </form>
                                   </div>
                                </div>
                                <!-- booking confirmation modal -->
                                <div id="bookingConfirmationModal" v-if="loggedInUser && customer?.is_verified && !showBookingModal">
                                    <div class="px-30 pb-40 sendToSell-modal-body">
                                        <div class="text-left py-40">
                                            <h2 class="font-weight-700">Project Saved Successfully</h2>
                                            <p class="font-weight-400">We’ve sent a copy of your pinboard to your inbox for safekeeping.</p>
                                        </div>
                                        <h6>
                                            Connect with a Krost Consultant to get a quote or <br>
                                            refine your layout.
                                        </h6>

                                    <div class="d-flex flex-column gap-4 button-group">
                                        <!-- Talk on the Phone -->
                                        <a class="text-start d-flex align-items-center" @click.prevent="finalPageRedirect('phone-call', $event)" :href="finalHref('phone-call')">
                                            <i class="fa fa-phone" style="margin-right: 10px;"></i>
                                            <span>Request a Call Back</span>
                                        </a>

                                        <!-- Discuss via Email -->
                                        <a class="text-start d-flex align-items-center" @click.prevent="finalPageRedirect('email', $event)" :href="finalHref('email')">
                                            <i class="fa-solid fa-envelope" style="margin-right: 10px;"></i>
                                            <span>Email a Consultant</span>
                                        </a>

                                        <!-- Book Showroom Visit (another modal) -->
                                        <button type="button" id="bookShowroomVisitButton" class="text-start d-flex align-items-center" @click.prevent="handleShowroomVisitBooking($event)">
                                            <i class="fa fa-building" style="margin-right: 10px;"></i>
                                            <span>Book a Showroom Tour</span>
                                        </button>

                                        <!-- Book a Virtual Meeting -->
                                        <a class="text-start d-flex align-items-center" @click.prevent="finalPageRedirect('virtual-meeting', $event)" :href="finalHref('virtual-meeting')">
                                            <i class="fa fa-video" style="margin-right: 10px;"></i>
                                            <span>Book a Video Consultation</span>
                                        </a>

                                        <div class="d-flex flex-column flex-md-row justify-content-between w-100" style="gap: 8px;">
                                            <a href="/" 
                                               class="th-btn-gray py-10 border-0 bg-gray w-100" 
                                               style="padding: 16px 30px; border-radius: 0px; display: flex; align-items: center; justify-content: center;">
                                                Continue Pinning 
                                            </a>
                                            <a 
                                                href="/account/pinboards"
                                                class="th-btn-primary py-10 border-0 bg-primary w-100" 
                                                style="padding: 16px 30px; border-radius: 0px; display: flex; align-items: center; justify-content: center;">
                                                View Pinboard
                                            </a>
                                        </div>
                                        
                                    </div>
                                    </div>
                                </div>

                                <form id="bookingModalForm" class="booking-modal-form" v-if="showBookingModal">
                                    <div class="booking-modal-content">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <div class="th-booking-left-section">
                                            <div class="th-booking-selected-member-image">
                                                <img src="/img/logo_black.png" alt="">
                                            </div>
                                            <div class="th-booking-location-details">
                                            <div id="showroomLoader" class="d-none">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <!-- member details -->
                                            <div class="th-booking-selected-member d-flex flex-column mb-20">
                                                <div class="th-member-info p-0">
                                                   <h3 class="font-size-20" data-v-booknow-section_title="">Meet With Our Consultant</h3>
                                                    <!--<p class="th-member-name" data-v-booknow-name>{{ nearestShowroom?.contact_name }}</p>-->
                                                </div>
                                                <div class="th-booking-member-avatar">
                                                    <img :src="nearestShowroom?.image" alt="Member Avatar" data-v-booknow-member_image  style="width: 270px; height: 215px; border-radius: 10px;"/>
                                                </div>
                                            </div>
                                            <!-- showroom details -->
                                            <h4 class="font-weight-600" id="showroomName">{{ nearestShowroom?.title }}</h4>
                                            <p class="font-weight-400 color-black">
                                                <i class="fa-solid fa-location-dot"></i>
                                                <span id="showroomAddress"> {{ nearestShowroom?.address }}</span>
                                            </p>
                                            <div class="d-flex th-booking-tour-option my-15">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="radio" id="physicalTour" name="tour_type" checked>
                                                    <label for="physicalTour">Physical Tour</label>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="th-booking-description">
                                           <!-- <p class="font-weight-400 color-black mb-15">Lorem ipsum dolor sit amet consectetur. Nam dignissim
                                                at vitae faucibus. Lectus ac elit morbi nisl.</p>
                                            <p class="font-weight-400 color-black">Vestibulum neque nunc mattis mauris. Vitae lorem volutpat
                                                gravida augue aliquet at nulla.</p>
                                            -->
                                           
                                            </div>
                                        </div>

                                        </div>
                                        <div class="col-md-6 th-booking-calendar-section">
                                        <h3 class="mt-50 font-weight-600">Select a Date & Time</h3>
                                        <div class="booking-calendar-wrapper p-0">
                                            <div class="th-booking-calendar" @click="handleDayClick($event)">
                                               <input class="d-none" type="text" placeholder="Select Date.." data-input />
                                            </div>
                                        </div>
                                           <!-- <div class="th-booking-timezone">
                                                <h3 class="font-weight-600 mt-50">Time Zone</h3>
                                                <div class="th-input-group">
                                                <i class="fa-solid fa-globe"></i>
                                                <select class="form-control th-choices-select" name="choose-members" id="choose-timezone"
                                                    placeholder="This is a placeholder">
                                                </select>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                    </div>
                                    <!-- <div class="py-50">
                                         <button id="bookNowButton" @click.prevent="handleBookingClick('showroom-visit', $event)" type="submit" class="th-btn-primary text-capitalize w-100">Book Now <i
                                        class="fa-regular fa-arrow-up degree-60"></i></button>
                                    </div>
                                    -->
                                </form>

                                <div id="pinboardSuccessMessage" class="th-pinboard-success-message d-none">
                                    <div class="text-center">
                                    <h3 class="font-weight-700">Welcome To Krost</h3>
                                    <p class="font-weight-400">Your account is ready. We’ve successfully saved your selection to
                                        your new </br> <strong id="pinboardName"></strong> board</p>
                                    </div>
                                    <div class="text-center py-50">
                                    <a href="/" class="th-btn-primary text-capitalize w-100">Continue Pinning </a>
                                    <button onclick="window.location.href='/projects'"
                                        class="th-btn-transparent text-capitalize w-100 py-2 mt-30">View Project 1 <i
                                        class="fa-regular fa-arrow-up degree-60"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade backdrop-static"
            id="timeSlotsModal"
            tabindex="-1"
            aria-labelledby="timeSlotsModalLabel"
            data-bs-backdrop="false"
            style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
            aria-modal="true"
            role="dialog"
            ref="timeSlotsModal"
            >
            <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
                <div class="modal-content px-80 py-60">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="timeSlotsModalLabel">
                    Booking for Showroom Tour
                    </h5>
                    <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                    @click="handleCloseTimeSlotsModal"
                    ></button>
                </div>

                <!-- Body -->
                <div class="modal-body" id="booking-form-container">

                    <div class="gap-10">

                    <!-- Date -->
                    <div class="th-form-row">
                        <label for="ts-selected-date">Date</label>
                        <div class="th-field">
                        <div class="th-input-group d-flex align-items-center">
                            <i class="fa-solid fa-calendar th-input-icon"></i>
                            <input
                            type="date"
                            id="ts-selected-date"
                            class="form-control th-date-input"
                            :value="selectedDate"
                            @change="changeDate($event.target.value)"
                            />
                        </div>
                        </div>
                    </div>

                    <!-- Timezone -->
                    <div class="th-form-row th-timezone-selector">
                        <label for="choose-timezone">Time Zone</label>
                        <div class="th-field">
                        <div class="th-input-group d-flex align-items-center">
                            <i class="fa-solid fa-globe th-input-icon"></i>
                            <select
                            class="form-control th-choices-select"
                            id="choose-timezone"
                            disabled
                            ></select>
                        </div>
                        </div>
                    </div>
                    <!-- Name -->
                    <div class="th-form-row">
                       <label for="ts-name">Name</label>
                        <div class="th-field">
                            <div class="th-input-group d-flex align-items-center" id="ts-name-container">
                                <i class="fa-solid fa-user th-input-icon"></i>
                                <input type="text" id="ts-name" class="form-control"
                                    placeholder="Enter your name" style="border: none !important;" v-model="bookingName">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="th-form-row">
                        <label for="ts-email-not-logged-in-email">Email</label>
                        <div class="th-field">
                        <div
                            class="th-input-group d-flex align-items-center"
                            id="ts-email-not-logged-in-email-container"
                        >
                            <i class="fa-solid fa-envelope th-input-icon"></i>
                            <input
                            type="email"
                            id="ts-email-not-logged-in-email"
                            class="form-control"
                            placeholder="Enter your email"
                            style="border: none !important;"
                            v-model="bookingEmail"
                            />
                        </div>
                        </div>
                    </div>

                    <!-- Pick Time -->
                    <div class="th-form-row th-pt-20">
                        <label>Pick a time</label>
                        <div class="th-field">
                        <strong class="d-block">Choose a slot below</strong>
                        </div>
                    </div>

                    </div>

                    <!-- Time Slots -->
                    <div class="ts-slots-blocks th-pt-20">

                    <!-- Morning -->
                    <div class="ts-slots-block ts-slots-morning">
                        <div
                        id="ts-slots-morning"
                        class="ts-slots-grid d-flex flex-wrap gap-2"
                        >
                        <div
                            v-for="slot in morningSlots"
                            :key="slot.id"
                            class="th-time-slot"
                            :class="{
                            active: selectedSlot === slot.id,
                            'no-hover': selectedSlot !== slot.id,
                            selected: selectedSlot === slot.id
                            }"
                            @click="toggleSlot($event, slot.id, slot.value)"
                        >
                            <input
                            type="checkbox"
                            class="d-none"
                            :id="slot.id"
                            :value="slot.value"
                            v-model="checkedSlot"
                            />
                            <label :for="slot.id">{{ slot.label }}</label>
                        </div>
                        </div>
                    </div>

                    <!-- Evening -->
                    <div class="ts-slots-block ts-slots-evening">
                        <div
                        id="ts-slots-evening"
                        class="ts-slots-grid d-flex flex-wrap gap-2"
                        >
                        <div
                            v-for="slot in eveningSlots"
                            :key="slot.id"
                            class="th-time-slot"
                            :class="{
                            active: selectedSlot === slot.id,
                            'no-hover': selectedSlot !== slot.id,
                            selected: selectedSlot === slot.id
                            }"
                            @click="toggleSlot($event, slot.id, slot.value)"
                        >
                            <input
                            type="checkbox"
                            class="d-none"
                            :id="slot.id"
                            :value="slot.value"
                            v-model="checkedSlot"
                            />
                            <label :for="slot.id">{{ slot.label }}</label>
                        </div>
                        </div>
                    </div>

                    </div>

                    <!-- No Slots -->
                    <div id="ts-no-slots" style="display: none;">
                    No available slots for the selected date/time zone.
                    </div>

                    <!-- Submit -->
                    <div class="pt-60">
                    <button
                        type="button"
                        id="th-book-time-btn"
                        class="th-btn-primary text-capitalize"
                        @click.prevent="handleBookingClick('showroom-visit', $event)"
                    >
                        Book
                    </button>
                    </div>

                </div>
                <div id="book-now-verify-email-form-container" class="d-none">
                    <form id="verify-email-form" class="guest-signup-form">
                        <div class="text-center py-10 mb-20">
                            <h6>Verify it's you</h6>
                            <p>
                                We've sent a verification code to
                                <strong id="verify-email-display">{{ bookingEmail }}</strong>. <br>
                                Please enter the code below to save your project.
                            </p>
                            <!-- <strong class="d-block mb-20 text-center" id="otp-text"> otp </strong> -->
                        </div>

                        <!-- OTP BOXES -->
                        <div class="otp-wrapper mb-20">
                            <input
                                v-for="(digit, index) in otpDigits"
                                :key="index"
                                ref="otpInputs"
                                type="text"
                                maxlength="1"
                                class="otp-input"
                                v-model="otpDigits[index]"
                                @input="handleOtpInput(index, $event)"
                                @keydown.backspace="handleBackspace(index)"
                                @paste="handleOtpPaste($event)"
                            />
                        </div>
                        <div class="col-md-10 text-center mx-auto px-2 px-md-3 text-danger" ref="verify-email-error">Error</div>

                        <!-- PRIMARY BUTTON -->
                        <button type="button" class="th-btn-primary text-capitalize w-100 mt-15"
                            id="verify-email-button-time-slots"
                            @click.prevent="handleTimeSlotsModalVerifyOtp">
                            Verify & Continue
                        </button>

                        <div class="text-center mt-15">
                            <span id="otp-timer-text">
                                Resend in 00:00
                            </span>
                        </div>


                        <!-- RESEND LINK -->
                        <div class="text-center mt-15">
                            <small>
                                Didn't receive the code?
                                <a href="javascript:void(0)" class="resend-link" id="resend-otp-button">
                                    Resend OTP
                                </a>
                            </small>
                        </div>

                    </form>
                </div>

                <!-- Message -->
                <div
                    class="col-md-10 text-center mx-auto px-2 px-md-3"
                    id="show-message-container"
                ></div>
                </div>
            </div>
        </div>
    <!-- Live camera (opens on Add Image; then title/note modal) -->
    <div
        v-if="showLiveCamera"
        class="position-fixed top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3"
        style="z-index: 1070; background: rgba(0,0,0,0.85);"
        @click.self="cancelLiveCamera"
    >
        <div class="w-100" style="max-width: 520px;" @click.stop>
            <video
                ref="liveCameraVideo"
                class="w-100 rounded bg-dark"
                style="max-height: min(55vh, 420px); object-fit: cover;"
                playsinline
                muted
                autoplay
            ></video>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="button" class="btn btn-sm btn-outline-light" @click="cancelLiveCamera">Cancel</button>
                <button type="button" class="btn btn-sm th-btn-primary text-capitalize" @click="snapLiveCameraPhoto">Take photo</button>
            </div>
        </div>
    </div>

    <!-- Add image: title / note -->
    <div
        v-if="showAddImageModal"
        class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3"
        style="z-index: 1060; background: rgba(0,0,0,0.45);"
        @click.self="cancelAddCameraImage"
    >
        <div class="bg-white rounded shadow-sm p-3 w-100" style="max-width: 400px;" @click.stop>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Add image to pinboard</h6>
                <button type="button" class="btn-close" aria-label="Close" @click="cancelAddCameraImage"></button>
            </div>
            <div v-if="cameraImagePreview" class="text-center mb-3">
                <img :src="cameraImagePreview" alt="" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" />
            </div>
            <div class="mb-2">
                <label class="form-label small text-muted mb-0">Title</label>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    v-model="addImageTitle"
                    placeholder="e.g. Site photo, Finish idea"
                    @input="addImageModalError = false"
                />
            </div>
            <div class="mb-2">
                <label class="form-label small text-muted mb-0">Note</label>
                <textarea
                    class="form-control form-control-sm"
                    rows="2"
                    v-model="addImageComment"
                    placeholder="Optional details"
                    @input="addImageModalError = false"
                ></textarea>
            </div>
            <p v-if="addImageModalError" class="text-danger small mb-2">Add a title or a note (or both).</p>
            <div class="d-flex justify-content-end gap-2 mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="cancelAddCameraImage">Cancel</button>
                <button type="button" class="btn btn-sm th-btn-primary text-capitalize" @click="confirmAddCameraImage">Add</button>
            </div>
        </div>
    </div>

    <div
    v-if="showCreateNewProjectModal"
    class="modal fade backdrop-static show d-block"
    id="createNewProjectModal"
    tabindex="-1"
    aria-labelledby="createNewProjectModalLabel"
    data-bs-backdrop="false"
    style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
    aria-modal="true"
    role="dialog"
    ref="createNewProjectModal"
  >
    <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
      <div class="modal-content px-80 py-60">
  
        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="createNewProjectModalLabel">
            Create New Project
          </h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="handleCloseCreateNewProjectModal"
          ></button>
        </div>
  
        <!-- Body -->
        <div class="modal-body" id="create-new-project-form-container">
          <div class="gap-10">
  
            <!-- Project Name -->
            <div class="th-form-row">
              <div class="th-field">
                <div class="th-input-group d-flex align-items-center">
                  <input
                    type="text"
                    id="new-project-name"
                    class="form-control"
                    placeholder="Enter your project name"
                    v-model="newProjectName"
                    @input="$event.target.classList.remove('is-invalid')"
                  />
                </div>
              </div>
            </div>
  
            <!-- Submit -->
            <div class="pt-20">
              <button
                type="button"
                id="create-new-project-btn"
                class="th-btn-primary text-capitalize w-100"
                @click.prevent="handleCreateNewProjectClick($event)"
              >
                Save Project and continue
              </button>
            </div>
  
          </div>
        </div>
  
      </div>
    </div>
  </div>

    <!--============================== End Virtual Pinboard ==============================-->





    </div>

`,
};
