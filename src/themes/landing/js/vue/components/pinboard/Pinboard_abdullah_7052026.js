import RegistrationModal from './RegistrationModal.js';
import EmailVerificationModal from './EmailVerificationModal.js';
import CommunicationModal from './CommunicationModal.js';
import BookingCalendarModal from './BookingCalendarModal.js';
import BookingTimeModal from './BookingTimeModal.js';
import CreateProjectModal from './CreateProjectModal.js';
import LiveCameraModal from './LiveCameraModal.js';
import AddImageModal from './AddImageModal.js';
import ProjectPhotosGrid from './ProjectPhotosGrid.js';
import Customer from '../../models/Customer.js';

export default {
    name: 'Pinboard',
    components: {
        RegistrationModal,
        EmailVerificationModal,
        CommunicationModal,
        BookingCalendarModal,
        BookingTimeModal,
        CreateProjectModal,
        LiveCameraModal,
        AddImageModal,
        ProjectPhotosGrid,
    },
    data() {
        return {
            showProjectModal: false,
            showBookingModal: false,
            showBookingTimeModal: false,
            bookingSelectedDate: '',
            tourType: 'physicalTour',
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
            selectedProjectId: '',
            projectSearchQuery: '',
            isEditingProjectTitle: false,
            editableProjectTitle: '',
            isSavingProjectTitle: false,
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
            _mediaStream: null,

            isMobile: window.innerWidth < 540,
            isTablet: window.innerWidth >= 540 && window.innerWidth < 1200,
            // screenSize: this.getScreenSize()
        }
    },
    computed: {
        pinboard: {
            get() {
                return this.$store.getters.pinboard;
            },
            set(value) {
                this.$store.dispatch('setPinboard', value);
            }
        },
        projectMenuItems() {
            return this.$store.getters.projectItems;
        },
        filteredProjectMenuItems() {
            const query = String(this.projectSearchQuery || '').trim().toLowerCase();
            if (!query) return this.projectMenuItems || [];
            return (this.projectMenuItems || []).filter((item) =>
                String(item?.pinboard_name || '').toLowerCase().includes(query)
            );
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
                this.$store.dispatch('setCustomer', value);
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
        showrooms() {
            return this.$store.getters.showrooms || [];
        },
        displayProjectTitle() {
            // console.log('displayProjectTitle component=', this.pinboard);
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
            // console.log("autocompleteSuggestions component=", this.$store.getters.autocompleteSuggestions);
            return this.$store.getters.autocompleteSuggestions;
        },
        autocompleteOpen() {
            // console.log("autocompleteOpen component=", this.$store.getters.autocompleteOpen);
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
            if (this.isEditingProjectTitle) {
                const input = this.$refs.projectTitleInput;
                if (input && !input.contains(e.target)) {
                    this.saveProjectTitle();
                }
            }
            if (!this.showProjectDropdown) return;
            const wraps = this.$el && this.$el.querySelectorAll('.pinboard-header-project-wrap');
            if (!wraps || !wraps.length) return;
            const inside = Array.from(wraps).some((w) => w.contains(e.target));
            if (!inside) this.showProjectDropdown = false;
        };
        document.addEventListener('click', this._closeProjectMenuOnOutsideClick);

        window.addEventListener('resize', this.handleScreenResize);

        // selected project id
        this.selectedProjectId = this.pinboard?.pinboard_id;
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.handleScreenResize);
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
        normalizeTourType(type) {
            const value = String(type || '').trim();
            if (value === 'virtualMeeting') return 'virtualMeeting';
            return 'physicalTour';
        },
        getLiveCameraVideoElement() {
            return this.$refs.liveCameraModal && this.$refs.liveCameraModal.$refs
                ? this.$refs.liveCameraModal.$refs.liveCameraVideo
                : null;
        },
        handleScreenResize() {
            this.isMobile = window.innerWidth < 540;
            this.isTablet = window.innerWidth >= 540 && window.innerWidth < 1200;
            // this.screenSize = this.getScreenSize();
        },
        // getScreenSize() {
        //     const width = window.innerWidth;
        //     if (width < 540) return 'mobile';
        //     if (width < 1200) return 'tablet';
        //     return 'desktop';
        // },

        clearError(field) {
            this.$store.commit('CLEAR_ERROR', field);
            if (field === 'addCommentItemToPinboard' && this.$refs.addCommentTextarea) {
                this.$refs.addCommentTextarea.classList.remove('is-invalid');
            }
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
        setCustomer(fields = {}) {
            const customerModel = {
                ...(this.customer || {}),
                ...(fields || {}),
            };
            this.customer = customerModel;
        },
        setPinboard(fields = {}) {
            const pinboardModel = {
                ...(this.pinboard || {}),
                ...(fields || {}),
            };
            this.pinboard = pinboardModel;
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
        handleCloseCreateNewProjectModal() {
            this.showCreateNewProjectModal = false;
        },
        async handleCreateNewProject(payload) {
            try {
                const response = await this.$store.dispatch('createNewProject', payload);
                if (response && response.success && response.data?.pinboard_id) {
                    this.selectedProjectId = response.data.pinboard_id;
                    this.showCreateNewProjectModal = false;
                    return;
                }
                console.error('Failed to create new project', response?.error || 'Unknown error');
            } catch (e) {
                console.error('Failed to create new project', e);
            }
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
            this.tourType = 'physicalTour';
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
        async handleShowroomVisitBooking(type = 'physicalTour') {
            this.showBookingModal = true;
            this.showBookingTimeModal = false;
            this.tourType = this.normalizeTourType(type);
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
            this.showBookingModal = false;
            // this.$emit('close-time');
        },
        closeBookingCalendarModal() {
            this.showBookingModal = false;
            this.showBookingTimeModal = false;
            this.bookingSelectedDate = '';
            this.tourType = 'physicalTour';
        },
        updateTourType(tourType) {
            this.tourType = this.normalizeTourType(tourType);
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
            // console.log('comment component :- ', comment);
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
            const commentTextarea = this.$refs.addCommentTextarea;
            if (commentTextarea) {
                commentTextarea.classList.remove('is-invalid');
            }
            // add validation in-valid class to the comment text area
            if (!commentText) {
                if (commentTextarea) {
                    commentTextarea.classList.add('is-invalid');
                }
                return;
            }
            if (commentText.length < 2) {
                if (commentTextarea) {
                    commentTextarea.classList.add('is-invalid');
                }
                this.$store.commit('SET_ERROR', {
                    key: 'addCommentItemToPinboard',
                    error: 'Please enter at least 2 characters.',
                });
                return;
            }
            this.$store.commit('CLEAR_ERROR', 'addCommentItemToPinboard');
            try {
                const res = await this.$store.dispatch('addCommentItemToPinboard', commentText);
                if (res?.success) {
                    this.comment = '';
                    if (commentTextarea) {
                        commentTextarea.classList.remove('is-invalid');
                    }
                } else if (commentTextarea) {
                    commentTextarea.classList.add('is-invalid');
                }
            } catch (err) {
                console.error('Failed to submit comment', err);
                if (commentTextarea) {
                    commentTextarea.classList.add('is-invalid');
                }
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
        // ------------------------- camera capture functions -------------------------
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
            const video = this.getLiveCameraVideoElement();
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
            const video = this.getLiveCameraVideoElement();
            if (video && video.srcObject) {
                video.srcObject = null;
            }
            this.showLiveCamera = false;
        },
        cancelLiveCamera() {
            this.stopLiveCameraStream();
        },
        snapLiveCameraPhoto() {
            const video = this.getLiveCameraVideoElement();
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
            this.prepareCameraImageFromFile(file);
        },
        handleLiveCameraFileSelect(file) {
            this.stopLiveCameraStream();
            this.prepareCameraImageFromFile(file);
        },
        prepareCameraImageFromFile(file) {
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
                model_id: this.pinboard.pinboard_id,
                model_type: 'images',
                title,
                photo,
                quantity: 1,
                unit_price: 0,
                description: title || comment,
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
        // ------------------------- end camera capture functions -------------------------
        getProjectInitial(projectName) {
            const safeName = String(projectName || '').trim();
            return safeName ? safeName.charAt(0).toUpperCase() : '?';
        },
        openProjectDropdownFromHeader() {
            if (this.isEditingProjectTitle || this.isSavingProjectTitle) return;
            this.showProjectDropdown = !this.showProjectDropdown;
        },
        changeProject(pinboardId) {
            this.selectedProjectId = pinboardId;
            this.projectSearchQuery = '';
            this.showProjectDropdown = false;
            this.$store.dispatch('getProjectByPinboardId', pinboardId);
        },
        openProjectTitleEditor() {
            if (this.isSavingProjectTitle) return;
            this.showProjectDropdown = false;
            this.isEditingProjectTitle = true;
            this.editableProjectTitle = this.displayProjectTitle || '';
            this.$nextTick(() => {
                const input = this.$refs.projectTitleInput;
                if (input && typeof input.focus === 'function') {
                    input.focus();
                    if (typeof input.select === 'function') input.select();
                }
            });
        },
        async saveProjectTitle() {

            try {
                const payload = {
                    pinboard_id: this.pinboard?.pinboard_id,
                    pinboard_name: this.editableProjectTitle,
                };
              const response =   await this.$store.dispatch('updateProjectTitle', payload);
              if(response && response.success){
                this.displayProjectTitle = this.editableProjectTitle;
                this.isEditingProjectTitle = false;
              }else{
                console.error('Project title update failed', response.message);
                this.editableProjectTitle = this.displayProjectTitle;
                this.isEditingProjectTitle = false;
              }
            } catch (e) {
                console.error('Project title update failed', e);
            }
        },

    },
    filters: {
        capitalize(value) {
            return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
        }
    },
    watch: {
        // selected project id
        // pinboard: {
        //     immediate: true,
        //     handler(newVal) {
        //         this.displayProjectTitle = newVal?.pinboard_name || '';
        //     }
        // }
    },
    template: /* html */ `
    <div class="bg-gray pinboard-app-root">
        <div class="pinboard-close-btn">
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close Virtual Pinboard" class="btn btn-link p-1 border-0 text-body pinboard-offcanvas-close flex-shrink-0 d-xl-none">
                <i aria-hidden="true" class="fa-solid fa-xmark fa-lg"></i>
            </button>
        </div>
        <!-- Desktop: original single-row header (md and up) -->
        <div class="offcanvas-header th-header-upper d-md-flex justify-content-between align-items-center" v-if="!isTablet">
            <div class="d-flex align-items-center">
                <div class="offcanvas-header th-header-lower">
                    <h5 id="offcanvasRightLabel2">Virtual Pinboard</h5>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end pinboard-header-info"
                v-if="loggedInUser && loggedInUser.email">
                <div class="pinboard-header-project-wrap text-end position-relative">
                    <div class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-end">

                        <div class="d-inline-flex align-items-center">
                            <i class="fa-solid fa-pencil"
                                style="font-size: 12px; margin-right: 5px; cursor: pointer;"
                                @click.stop="openProjectTitleEditor"></i>
                            <input
                                v-if="isEditingProjectTitle"
                                ref="projectTitleInput"
                                type="text"
                                class="form-control form-control-sm pinboard-project-name-input"
                                v-model="editableProjectTitle"
                                @click.stop
                                @keydown.enter.prevent="saveProjectTitle"
                                @blur="saveProjectTitle"
                                :disabled="isSavingProjectTitle"
                                style="height: 30px; min-width: 160px; max-width: 230px; padding: 2px 8px; font-size: 16px; font-weight: 700; border-radius: 6px; border: 1px solid #d5d9e2;"
                            />
                            <span v-else class="pinboard-project-name fw-bold" role="button" tabindex="0"
                                @click.stop="openProjectDropdownFromHeader"
                                @keydown.enter.prevent="openProjectDropdownFromHeader"
                                @keydown.space.prevent="openProjectDropdownFromHeader">{{ displayProjectTitle }}</span>
                        </div>


                        <button type="button" class="pinboard-project-chevron-btn btn btn-link p-0 border-0 align-baseline"
                            :aria-expanded="showProjectDropdown ? 'true' : 'false'" aria-haspopup="true"
                            aria-label="Choose project" @click.stop="showProjectDropdown = !showProjectDropdown">
                            <i class="fa-solid fa-chevron-down pinboard-project-chevron" :class="{ 'is-open': showProjectDropdown }"></i>
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
                            <div class="px-2 pb-2">
                                <div class="position-relative">
                                    <i class="fa-solid fa-search text-muted"
                                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; pointer-events: none;"></i>
                                    <input
                                        v-model="projectSearchQuery"
                                        type="text"
                                        class="form-control form-control-sm"
                                        placeholder="Search project"
                                        style="padding-left: 30px; border-radius: 10px; border: 1px solid #d9dee7;"
                                    />
                                </div>
                            </div>
                            <button v-for="item in filteredProjectMenuItems" :key="'desk-' + item.pinboard_id" type="button"
                                class="pinboard-project-menu-item d-flex align-items-center gap-2"
                                :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                                @click="changeProject(item.pinboard_id)">
                                <span
                                    style="width: 28px; height: 28px; border-radius: 8px; background: #eef2f8; color: #3a4b67; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 28px;">
                                    {{ getProjectInitial(item.pinboard_name) }}
                                </span>
                                <span class="pinboard-project-menu-label text-truncate">{{ item.pinboard_name }}</span>
                            </button>
                            <div v-if="!filteredProjectMenuItems.length" class="px-3 py-2 small text-muted">
                                No project found
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>


        <!-- TABLET HEADER (<=1200px) -->
        <div class="pinboard-header-tablet d-flex align-items-center justify-content-between" v-if="loggedInUser && isTablet">

            <!-- LEFT: Title -->
            <div class="pinboard-header-left">
                <h6 class="mb-0 text-truncate">Virtual Pinboard</h6>
            </div>

            <!-- CENTER: Project -->
            <div class="pinboard-header-center text-center position-relative">

                <div class="d-inline-flex align-items-center gap-1">
                   <div class="d-inline-flex align-items-center">
                            <i class="fa-solid fa-pencil"
                                style="font-size: 12px; margin-right: 5px; cursor: pointer;"
                                @click.stop="openProjectTitleEditor"></i>
                            <input
                                v-if="isEditingProjectTitle"
                                ref="projectTitleInput"
                                type="text"
                                class="form-control form-control-sm pinboard-project-name-input"
                                v-model="editableProjectTitle"
                                @click.stop
                                @keydown.enter.prevent="saveProjectTitle"
                                @blur="saveProjectTitle"
                                :disabled="isSavingProjectTitle"
                                style="height: 30px; min-width: 160px; max-width: 230px; padding: 2px 8px; font-size: 16px; font-weight: 700; border-radius: 6px; border: 1px solid #d5d9e2;"
                            />
                            <span v-else class="pinboard-project-name fw-bold" role="button" tabindex="0"
                                @click.stop="openProjectDropdownFromHeader"
                                @keydown.enter.prevent="openProjectDropdownFromHeader"
                                @keydown.space.prevent="openProjectDropdownFromHeader">{{ displayProjectTitle }}</span>
                        </div>

                    <button
                        type="button"
                        class="btn btn-link p-0 border-0"
                        @click.stop="showProjectDropdown = !showProjectDropdown"
                    >
                        <i class="fa-solid fa-chevron-down"
                        :class="{ 'is-open': showProjectDropdown }"></i>
                    </button>
                </div>

                <!-- DROPDOWN -->
                <transition name="pinboard-project-menu-fade">
                    <div v-if="showProjectDropdown"
                        class="pinboard-project-menu text-start"
                        @click.stop>

                        <button
                            class="pinboard-project-menu-item"
                            @click="openNewProjectModal()"
                        >
                            + Create New Project…
                        </button>

                        <div class="pinboard-project-menu-divider"></div>

                        <div class="px-2 pb-2">
                            <div class="position-relative">
                                <i class="fa-solid fa-search text-muted"
                                    style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; pointer-events: none;"></i>
                                <input
                                    v-model="projectSearchQuery"
                                    type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Search project"
                                    style="padding-left: 30px; border-radius: 10px; border: 1px solid #d9dee7;"
                                />
                            </div>
                        </div>

                        <button
                            v-for="item in filteredProjectMenuItems"
                            :key="'tab-' + item.pinboard_id"
                            class="pinboard-project-menu-item d-flex align-items-center gap-2"
                            :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                            @click="changeProject(item.pinboard_id)"
                        >
                            <span
                                style="width: 28px; height: 28px; border-radius: 8px; background: #eef2f8; color: #3a4b67; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 28px;">
                                {{ getProjectInitial(item.pinboard_name) }}
                            </span>
                            <span class="text-truncate pinboard-project-menu-label">
                                {{ item.pinboard_name }}
                            </span>
                        </button>
                        <div v-if="!filteredProjectMenuItems.length" class="px-3 py-2 small text-muted">
                            No project found
                        </div>

                    </div>
                </transition>

            </div>

            <!-- RIGHT: Email + Close -->
            <div class="pinboard-header-right d-flex align-items-center gap-2">
                <span class="pinboard-email small text-muted text-truncate">
                    {{ loggedInUser.email }}
                </span>
            </div>
        </div>
        <!-- Tablet header end here -->




        <!-- Search + list + footer: md–lg = 2 columns; stacked on mobile & lg+ -->
        <div class="offcanvas-body">
            <div class="th-pinboard th-pinboard--tablet-split">
                <div
                    class="row">
                    <div class="col-12  col-lg-12 pinboard-offcanvas-col pinboard-offcanvas-col--main">
                        <!-- Pinboard Search Bar Start here -->
                        <div class="mb-20 pb-0 pinboard-offcanvas-search" v-show="loggedInUser && loggedInUser.email">
                            <div class="autocomplete position-relative w-100">
                                <i class="fa-solid fa-search text-muted"
                                    style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); z-index: 11; pointer-events: none;"
                                    aria-hidden="true"></i>
                                <input type="text" class="form-control th-choices-select z-index-10 font-size-16"
                                    id="choose-product-name" :placeholder="autoCompletePlaceholderText" autocomplete="off"
                                    :disabled="disableAutocomplete" @input="handleAutocomplete" @focus="onAutocompleteFocus"
                                    v-model="localFilter.searchValue" style="padding:11px 36px 11px 45px;" />
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
                                            class="rounded flex-shrink-0" style="object-fit: contain;" />
                                        <span class="text-truncate small">
                                            <span class="d-block fw-semibold">{{ row.title }}</span>
                                            <span class="text-muted" v-if="row.sku">{{ row.sku }}</span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Pinboard Search Bar End here -->
                        <div class="row pinboard-main">
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
                                                <a :href="item.product_url" target="_blank" v-if="item.product_url">
                                                    <img :src="item.photo" class="thumb" />
                                                </a>
                                                <img :src="item.photo" class="thumb" v-else />
                                                <span class="title">{{ item.title }}</span>
                                                <i class="fa-solid fa-xmark close-icon"></i>
                                            </div>
                                            <!-- NORMAL ITEM -->
                                            <template v-else>
                                                <div class="pinboard col-md-12">

                                                    <!-- CARD ITEM -->
                                                    <div class="card-item">
                                                        <div class="card-left ml-5 mr-5">
                                                            <a :href="item.product_url" target="_blank" v-if="item.product_url">
                                                                <img :src="item.photo" :alt="item.title" />
                                                            </a>
                                                            <img :src="item.photo" :alt="item.title" v-else />
                                                        </div>

                                                        <div class="card-content">
                                                            <div class="card-header">
                                                                <div>
                                                                    <h3><a :href="item.product_url" target="_blank" v-if="item.product_url">{{ item.title | capitalize }}</a><span v-else>{{ item.title | capitalize }}</span></h3>
                                                                    <p class="type">{{ item.model_type | capitalize }}</p>
                                                                </div>

                                                                <div class="card-actions pr-10 align-items-center">
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

                            <!-- Ipad photos UI Start here-->
                            <project-photos-grid :images="pinboard.item_images"></project-photos-grid>
                            <!-- Ipad photos UI End here-->
                        </div>
                    </div>
                    <div class="col-12 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--actions position-absolute bottom-0">
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
                                                    :class="{ 'is-invalid': !!fb.errors.addCommentItemToPinboard }"
                                                    @input="clearError('addCommentItemToPinboard')"
                                                    ref="addCommentTextarea"
                                                    class="comment-box th-offcanvas-comment-box th-off-large-commentbox th-add-comment-textarea"
                                                    placeholder="Add A Comment"
                                                ></textarea>
                                                <div v-if="fb.errors.addCommentItemToPinboard" class="invalid-feedback d-block mt-1">
                                                    {{ fb.errors.addCommentItemToPinboard }}
                                                </div>

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
                                                                style="position: absolute; top: 6px; right: 6px; z-index: 5; background: rgba(207, 30, 30, 0.9); padding: 2px 6px; border-radius: 12px; cursor: pointer; line-height: 1;"
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
                                                            Add To Pinboard <i class="fa fa-circle-notch ms-2" v-if="fb.loading.addCommentItemToPinboard"></i>
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
                                        :value="pinboard?.job_title || ''"
                                        @input="setPinboard({ job_title: $event.target.value })"
                                        :class="{'is-invalid': fb.errors.job_title }">
                                        <span class="invalid-feedback" v-if="fb.errors.job_title">{{ fb.errors.job_title }}</span>
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="email" 
                                        class="form-control" 
                                        name="email" placeholder="Email Address"
                                        id="email" 
                                        :value="customer?.email || ''"
                                        @input="setCustomer({ email: $event.target.value })"
                                        :class="{ 'is-invalid': fb.errors.email }">
                                        <span class="invalid-feedback" v-if="fb.errors.email">{{ fb.errors.email }}</span>
                                    </div>
                                </div>
                                <!-- <end> project name and email input field -->
                                <div v-if="loggedInUser">
                                    <div class="d-flex flex-column gap-25 pinboard-offcanvas-footer-btns" v-if="!isMobile">
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
                                                :class="isTablet ? 'th-tablet-btn' : 'th-btn-primary text-capitalize border w-100'"
                                                id="pinboard-link"
                                            >
                                                <span class="mr-5">Manage Pinboard</span>
                                            </a>
                                            <!-- <a href="/" class="th-btn-gray text-capitalize mr-10 border w-100" id="pinboard-browse-link">
                                                <span class="mr-5">Continue Browsing</span>
                                            </a> -->
                                            <button
                                                type="button"
                                                :class="isTablet ? 'th-add-comment-toggle-btn' : 'text-reset th-btn-gray text-capitalize border w-100 text-decoration-none'"
                                                data-bs-dismiss="offcanvas"
                                                aria-label="Close"
                                            >
                                                Continue Browsing
                                            </button>
                                        </div>
                                    </div>


                                    <!-- MOBILE (icons) -->
                                    <div class="d-flex justify-content-between align-items-center pinboard-offcanvas-footer-icons" v-if="isMobile">

                                        <!-- Add Comment -->
                                        <button
                                            v-if="!showAddComment"
                                            class="icon-btn"
                                            @click.prevent="showAddComment = true"
                                        >
                                            <i class="fa-solid fa-comment m-0"></i>
                                        </button>

                                        <!-- Add Image -->
                                        <button
                                            class="icon-btn"
                                            @click.prevent="triggerCameraCapture"
                                        >
                                            <i class="fa-solid fa-image m-0"></i>
                                        </button>

                                        <!-- Manage Pinboard -->
                                        <a href="/account/pinboards" class="icon-btn">
                                            <i class="fa-solid fa-thumbtack m-0"></i>
                                        </a>

                                        <!-- Continue Browsing -->
                                        <button
                                            class="icon-btn"
                                            data-bs-dismiss="offcanvas"
                                        >
                                            <i class="fa-solid fa-xmark m-0"></i>
                                        </button>

                                    </div>
                                    <!-- end MOBILE (icons) -->
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
                        <!-- Pinboard Bottom End here -->
                    </div>
                </div>
            </div>
        </div>   

        <live-camera-modal
            v-if="showLiveCamera"
            ref="liveCameraModal"
            @cancel="cancelLiveCamera"
            @take-photo="snapLiveCameraPhoto"
            @select-file="handleLiveCameraFileSelect"
        ></live-camera-modal>

        <add-image-modal
            v-if="showAddImageModal"
            :camera-image-preview="cameraImagePreview"
            :add-image-title="addImageTitle"
            :add-image-comment="addImageComment"
            :add-image-modal-error="addImageModalError"
            @cancel="cancelAddCameraImage"
            @confirm="confirmAddCameraImage"
            @update-title="addImageTitle = $event; addImageModalError = false"
            @update-comment="addImageComment = $event; addImageModalError = false"
        ></add-image-modal>

        <create-project-modal
            :show="showCreateNewProjectModal"
            :logged-in-user="loggedInUser"
            :customer="customer"
            :loading="loading('createNewProject')"
            :error-message="fb.errors.createNewProject"
            @close="handleCloseCreateNewProjectModal"
            @create-project="handleCreateNewProject"
        ></create-project-modal>

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
            @update-customer="setCustomer"
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
            :pinboard-uuid="pinboard.uuid"
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
            :tour-type="tourType"
            @close-booking="closeBookingCalendarModal"
            @open-time-slots="openBookingTimeModal"
            @update-tour-type="updateTourType"
            :showrooms="showrooms"
        ></booking-calendar-modal>

        <booking-time-modal
            v-if="showProjectModal && customer?.is_verified && showBookingModal && showBookingTimeModal"
            :pinboard-title="pinboard.job_title"
            :pinboard-id="pinboard.pinboard_id || pinboard.pinboard_temp_id"
            :pinboard-uuid="pinboard.uuid"
            :selected-date="bookingSelectedDate"
            :tour-type="tourType"
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

