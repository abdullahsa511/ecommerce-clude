import RegistrationModal from './RegistrationModal.js';
import EmailVerificationModal from './EmailVerificationModal.js';
import CommunicationModal from './CommunicationModal.js';
import BookingCalendarModal from './BookingCalendarModal.js';
import BookingTimeModal from './BookingTimeModal.js';
import Customer from '../../models/Customer.js';

export default {
    name: 'Pinboard',
    components: {
        RegistrationModal,
        EmailVerificationModal,
        CommunicationModal,
        BookingCalendarModal,
        BookingTimeModal,
    },
    data() {
        return {
            showProjectModal: false,
            showBookingModal: false,
            showBookingTimeModal: false,
            bookingSelectedDate: '',
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
            showProjectDropdown: false,
            showCreateNewProjectModal: false,
            newProjectName: '',
            selectedProjectId: '',
            localFilter: {
                searchValue: '',
            },
            _autocompleteDebounceId: null,
            showAddImageModal: false,
            addImageModalError: false,
            cameraImagePreview: '',
            addImageTitle: '',
            addImageComment: '',
            _cameraBlobUrl: null,
            showLiveCamera: false,
            _mediaStream: null
        }
    },
    computed: {
        pinboard(){
            return this.$store.getters.pinboard;
        },
        projectMenuItems() {
            return this.$store.getters.projectItems;
        },
        items: {
            get() {
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
        loggedInUser() {
            this.bookingEmail = this.$store.getters.loggedInUser?.email || '';
            return this.$store.getters.loggedInUser;
        },
        fb() {
            return this.$store.getters.fb;
        },
        nearestShowroom() {
            return this.$store.getters.nearestShowroom || {};
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
        await this.$store.dispatch('projectItems');
    },
    mounted() {
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
        clearError(field) {
            this.$store.commit('CLEAR_ERROR', field);
        },
        addError(field, message) {
            this.$store.commit('SET_ERROR', { key: field, error: message });
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
        updateCustomer(fields = {}) {
            this.customer = {
                ...(this.customer || {}),
                ...fields,
            };
        },
        async handleRegistrationSignup() {
            await this.signup();
        },
        async handleEmailVerification(otp) {
            this.customerData.otp = otp || '';
            await this.verifyEmailAthenticateAndCreatePinboard();
        },
        getKey(item, index) {
            return `${item.model_type}-${item.model_id}-${index}`;
        },
        openNewProjectModal() {
            this.showProjectDropdown = false;
            this.showCreateNewProjectModal = true;
        },
        loading(key) {
            return this.$store.getters.fb.loading[key];
        },
        toggleItemNote(item) {
            const next = !Boolean(item?._showNote);
            // Vue 2 reactivity: ensure property exists
            this.$set(item, '_showNote', next);
        },
        closeModal() {
            this.showProjectModal = false;
            this.showBookingModal = false;
            this.showBookingTimeModal = false;
            this.bookingSelectedDate = '';
            if (this.modalInstance && typeof this.modalInstance.hide === 'function') {
                this.modalInstance.hide();
            }
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
                    window.setPinboardModalWidth();
                    this.showProjectModal = true;
                    return;
                }

                // Only open registration modal for explicit not-found case.
                if (existingCustomer && existingCustomer.success === false && existingCustomer.error === 'Customer not found') {
                    customer = Object.assign(customer, {email: this.customer.email});
                    await this.$store.dispatch('setCustomer', customer);
                    window.setPinboardModalWidth();
                    this.showProjectModal = true;
                    return;
                }

                // Backend/system error: keep modal flow closed and show fb.errors.createPinboard.
                return;
            }
        },
        async verifyEmailAthenticateAndCreatePinboard() {
            await this.$store.dispatch('verifyEmailAthenticateAndCreatePinboard', this.customerData.otp);
            window.dispatchEvent(new CustomEvent('user:isLoggedIn', { detail: true }));
        },
        async handleShowroomVisitBooking() {
            this.showBookingModal = true;
            this.showBookingTimeModal = false;
            try {
                await this.$store.dispatch('getNearestShowroom');
            } catch (e) {
                console.error('Failed to load nearest showroom', e);
            }
        },
        openBookingTimeModal(selectedDate) {
            this.bookingSelectedDate = selectedDate || '';
            this.showBookingTimeModal = true;
        },
        closeBookingTimeModal() {
            this.showBookingTimeModal = false;
        },
        closeBookingCalendarModal() {
            this.showBookingModal = false;
            this.showBookingTimeModal = false;
            this.bookingSelectedDate = '';
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
            await this.$store.dispatch('registerCustomer', {...this.customer, job_title: this.pinboard.job_title});
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
        async resendEmail() {
            if (!this.customer?.email) return;
            try {
                await this.$store.dispatch('sendEmailVerification', {
                    email: this.customer.email,
                    customer_name: this.customer.name || '',
                });
            } catch (e) {
                console.error('Failed to resend email verification', e);
            }
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
        async runAutocompleteSearch() {
            const q = (this.localFilter.searchValue || '').trim();
            await this.$store.dispatch('searchPinboardAutocomplete', q);
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
        cloneItem(item) {
            return {...item, _isDragPreview: true}
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

    },
    filters: {
        capitalize(value) {
            return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
        }
    },
    watch: {

    },
    template: /* html */ `
    <div class="bg-gray pinboard-app-root">
    <!-- Desktop: original single-row header (md and up) -->
        <div class="offcanvas-header th-header-upper d-md-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="offcanvas-header th-header-lower">
                    <h5 id="offcanvasRightLabel2">Virtual Pinboard</h5>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end pinboard-header-info"
                v-if="loggedInUser && loggedInUser.email">
                <div class="pinboard-header-project-wrap text-end position-relative">
                    <div
                        class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-end">
                        <span class="pinboard-project-name fw-bold">{{ displayProjectTitle }}</span>
                        <button type="button" class="pinboard-project-chevron-btn btn btn-link p-0 border-0 align-baseline"
                            :aria-expanded="showProjectDropdown ? 'true' : 'false'" aria-haspopup="true"
                            aria-label="Choose project" @click.stop="showProjectDropdown = !showProjectDropdown">
                            <i class="fa-solid fa-chevron-down pinboard-project-chevron"
                                :class="{ 'is-open': showProjectDropdown }"></i>
                        </button>
                    </div>
                    <div class="pinboard-project-email text-muted small">{{ loggedInUser.email }}</div>

                    <transition name="pinboard-project-menu-fade">
                        <div v-if="showProjectDropdown" class="pinboard-project-menu" @click.stop>
                            <button type="button" class="pinboard-project-menu-item pinboard-project-menu-create"
                                @click="openNewProjectModal()">
                                + Create New Project…
                            </button>
                            <div class="pinboard-project-menu-divider" aria-hidden="true"></div>
                            <button v-for="item in projectMenuItems" :key="'desk-' + item.pinboard_id" type="button"
                                class="pinboard-project-menu-item d-flex justify-content-between align-items-center gap-2"
                                :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                                @click="changeProject(item.pinboard_id)">
                                <span class="pinboard-project-menu-label text-truncate">{{ item.pinboard_name }}</span>
                            </button>
                        </div>
                    </transition>
                </div>
            </div>

            <div>
                <button type="button" data-bs-dismiss="offcanvas" aria-label="Close Virtual Pinboard" class="btn btn-link p-1 border-0 text-body pinboard-offcanvas-close flex-shrink-0 d-lg-none">
                    <i aria-hidden="true" class="fa-solid fa-xmark fa-lg"></i>
                </button>
            </div>

        </div>
        <!-- Mobile: title + close, then project block (below md only) -->

        <!-- Search + list + footer: md–lg = 2 columns; stacked on mobile & lg+ -->
        <div class="offcanvas-body">
            <div class="th-pinboard th-pinboard--tablet-split">
                <div
                    class="row gx-0 gx-md-3 gx-lg-0 gy-0 align-items-md-stretch align-items-lg-start mx-0 pinboard-offcanvas-tablet-row">
                    <div class="col-12 col-md-6 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--main">
                        <!-- Pinboard Search Bar Start here -->
                        <div class="mb-20 pb-0 pinboard-offcanvas-search" v-show="loggedInUser && loggedInUser.email">
                            <div class="autocomplete position-relative w-100">
                                <i class="fa-solid fa-search text-muted"
                                    style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 11; pointer-events: none;"
                                    aria-hidden="true"></i>
                                <input type="text" class="form-control th-choices-select z-index-10 font-size-16"
                                    id="choose-product-name" :placeholder="autoCompletePlaceholderText" autocomplete="off"
                                    :disabled="disableAutocomplete" @input="handleAutocomplete" @focus="onAutocompleteFocus"
                                    v-model="localFilter.searchValue" style="padding:11px 36px 11px 38px;" />
                                <i class="fa fa-close hover" @click.prevent="handleClearAutocomplete"
                                    v-show="localFilter.searchValue"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 11;"
                                    role="button" aria-label="Clear search"></i>
                                <ul v-show="autocompleteOpen && autocompleteSuggestions.length"
                                    class="dropdown-menu show pinboard-autocomplete-list w-100 shadow-sm mt-1"
                                    style="max-height: 260px; overflow-y: auto; z-index: 1060;">
                                    <li v-for="row in autocompleteSuggestions" :key="'ac-' + row.id"
                                        class="dropdown-item py-2 d-flex align-items-center gap-2" style="cursor: pointer;"
                                        @mousedown.prevent="selectAutocompleteProduct(row)">
                                        <img :src="row.dataSrc" :alt="row.title" width="48" height="36"
                                            class="rounded flex-shrink-0" style="object-fit: cover;" />
                                        <span class="text-truncate small">
                                            <span class="d-block fw-semibold">{{ row.title }}</span>
                                            <span class="text-muted" v-if="row.sku">{{ row.sku }}</span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Pinboard Search Bar End here -->

                        <!-- Pinboard Items Start here -->
                        <div id="pinboard-items" class="th-pinboard-upper th-ofc-pinboard-item-upper pinboard-items-scroll">
                            <!-- Placeholder (loading state) -->
                            <div v-if="loading('getPinboard')" class="pinboard-loading-placeholder">
                                <div v-for="n in 3" :key="'pinboard-loading-' + n"
                                    style="height:150px;border:1px solid #cfcfcf; background:white;border-radius:6px;margin-bottom:12px;">
                                </div>
                            </div>
                            <draggable v-if="!loading('getPinboard') && Array.isArray(items)" v-model="items"
                                :handle="'.draggable-handle'" :clone="cloneItem" ghost-class="pinboard-ghost"
                                chosen-class="pinboard-chosen" drag-class="pinboard-drag" @start="onDragStart"
                                @end="onDragEnd" tag="div">
                                <transition-group>
                                    <div v-for="(item, index) in items" :key="getKey(item, index)"
                                        class="row th-pinboard-item">
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
                                                                <div
                                                                    class="remove-pinboard-btn text-darkgrey border-0 bg-transparent">
                                                                    <i class="fa fa-times" :data-id="item.model_id"
                                                                        :data-model="item.model_type"
                                                                        @click.prevent="removePinboardItem(item, index)"></i>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-- start pinboard item options -->
                                                        <div class="th-item-product"
                                                            v-if="item?.options?.variant?.item?.options?.length">
                                                            <!--<span class="mb-2 th-title-20 text-success">Options:</span>-->
                                                            <div class="th-item-footer">
                                                                <div class="th-tag-name">
                                                                    <div class="th-tag"
                                                                        v-for="option in item.options?.variant?.item?.options"
                                                                        :key="option.product_option_id">
                                                                        {{ option.option_name }}
                                                                        <span
                                                                            v-if="option.subOption && option.subOption.name">
                                                                            -
                                                                            <span
                                                                                class="text-muted text-small text-success">(
                                                                                {{ option.subOption.name }} )</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- PINBOARD ITEM ACCESSORIES -->
                                                        <div class="th-item-product"
                                                            v-if="item.accessories && item.accessories.length > 0">
                                                            <span class="mb-2 th-title-20 text-success">Accessories:</span>
                                                            <div class="th-item-footer">
                                                                <div class="th-tag-name">
                                                                    <div class="th-tag"
                                                                        v-for="accessory in item.accessories"
                                                                        :key="accessory.product_accessories_id">
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
                                                                <div class="th-pinboard-item-edit mt-3"
                                                                    v-if="item.comments[0]"
                                                                    :data-edit-key="getKey(item, index)">
                                                                    <div
                                                                        class="th-pinboard-edit-wrapper d-flex justify-content-between w-100">
                                                                        <div class="w-100 th-pinboard-edit-content">
                                                                            <div v-if="editingItems[getKey(item, index)]"
                                                                                class="p-2">
                                                                                <textarea
                                                                                    class="form-control item-comment-box border-0 p-0"
                                                                                    rows="1" :value="item.comments[0] || ''"
                                                                                    @input="updateItemComment(item, 'comments', $event.target.value)"></textarea>
                                                                            </div>
                                                                            <div v-else
                                                                                class="p-2 text-muted th-display-pre-line th-pinboard-view-text">
                                                                                {{ item.comments[0] || '' }}
                                                                            </div>
                                                                        </div>

                                                                        <button class="btn" style="width: 100px;">
                                                                            <span v-if="!editingItems[getKey(item, index)]"
                                                                                @click="editItemComment(item, index)"><i
                                                                                    class="fa-solid fa-pencil"></i>
                                                                                Edit</span>
                                                                            <span v-else
                                                                                @click="addPinboardItemComment(item, index)"><i
                                                                                    class="fa-solid fa-check"></i>
                                                                                Post</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <div class="th-pinboard-item-comment mt-3" v-else>
                                                                    <div class="d-flex align-items-start gap-2 cccc">
                                                                        <!-- TEXTAREA -->
                                                                        <textarea class="form-control item-comment-box"
                                                                            placeholder="Add a Note" rows="1"
                                                                            @input="updateItemComment(item, 'newComments', $event.target.value)"
                                                                            :value="item.comments[0] || ''"></textarea>

                                                                        <!-- POST BUTTON -->
                                                                        <button class="th-btn-primary-post text-capitalize "
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
                        <!-- Pinboard Items End here -->
                    </div>
                    <div class="col-12 col-md-7 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--actions">
                        <!-- Pinboard Bottom Start here -->
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
                        <!-- Pinboard Bottom End here -->
                    </div>
                </div>
            </div>
        </div>

        <registration-modal
            v-if="showProjectModal && !customer?.uuid"
            :customer="customer"
            :pinboard-title="pinboard.job_title"
            :logged-in-user="loggedInUser"
            :show-booking-modal="showBookingModal"
            :loading="loading('registerCustomer')"
            :errors="fb.errors"
            @close="closeModal"
            @signup="handleRegistrationSignup"
            @update-customer="updateCustomer"
        ></registration-modal>

        <email-verification-modal
            v-if="showProjectModal && !customer?.is_verified && customer?.uuid"
            :customer="customer"
            :pinboard-title="pinboard.job_title"
            :logged-in-user="loggedInUser"
            :show-booking-modal="showBookingModal"
            :verify-loading="loading('createPinboard')"
            :errors="fb.errors"
            @close="closeModal"
            @verify="handleEmailVerification"
            @resend-email="resendEmail"
        ></email-verification-modal>

        <communication-modal
            v-if="showProjectModal && customer?.is_verified && !showBookingModal"
            :customer="customer"
            :pinboard-title="pinboard.job_title"
            :pinboard-id="pinboard.pinboard_id || pinboard.pinboard_temp_id"
            :logged-in-user="loggedInUser"
            :show-booking-modal="showBookingModal"
            @close="closeModal"
            @book-showroom="handleShowroomVisitBooking"
        ></communication-modal>

        <booking-calendar-modal
            v-if="showProjectModal && customer?.is_verified && showBookingModal && !showBookingTimeModal"
            :pinboard-title="pinboard.job_title"
            :pinboard-id="pinboard.pinboard_id || pinboard.pinboard_temp_id"
            :nearest-showroom="nearestShowroom"
            @close-booking="closeBookingCalendarModal"
            @open-time-slots="openBookingTimeModal"
        ></booking-calendar-modal>

        <booking-time-modal
            v-if="showProjectModal && customer?.is_verified && showBookingModal && showBookingTimeModal"
            :pinboard-title="pinboard.job_title"
            :pinboard-id="pinboard.pinboard_id || pinboard.pinboard_temp_id"
            :selected-date="bookingSelectedDate"
            :customer="customer"
            :logged-in-user="loggedInUser"
            :nearest-showroom="nearestShowroom"
            @close-time="closeBookingTimeModal"
            @back-to-calendar="closeBookingTimeModal"
            @booking-success="closeModal"
        ></booking-time-modal>
        
        
    </div>
    `
}

