export default {
    name: 'CommunicationModal',
    props: {
        customer: {
            type: Object,
            default: () => ({}),
        },
        pinboardTitle: {
            type: String,
            default: '',
        },
        pinboardId: {
            type: [Number, String],
            default: null,
        },
        pinboardUuid: {
            type: String,
            default: null,
        },
        loggedInUser: {
            type: Object,
            default: null,
        },
        showBookingModal: {
            type: Boolean,
            default: false,
        },
        showProjectSuccessMessage: {
            type: Boolean,
            default: true,
        },
        /** When true, hide this Bootstrap modal without emitting `close` (e.g. project submission on top). */
        suppressForOverlay: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            modalInstance: null,
            onHidden: null,
            suppressCloseEmit: false,
        };
    },
    watch: {
        suppressForOverlay(suppressed) {
            this.$nextTick(() => {
                if (!this.modalInstance) return;
                if (suppressed) {
                    this.suppressCloseEmit = true;
                    this.modalInstance.hide();
                } else {
                    this.modalInstance.show();
                }
            });
        },
    },
    mounted() {
        this.$nextTick(() => {
            if (!window.bootstrap || !this.$el) return;
            this.modalInstance = new bootstrap.Modal(this.$el, {
                backdrop: false,
            });
            this.onHidden = () => {
                if (this.suppressCloseEmit) {
                    this.suppressCloseEmit = false;
                    return;
                }
                this.$emit('close');
            };
            this.$el.addEventListener('hidden.bs.modal', this.onHidden);
            if (this.suppressForOverlay) {
                // Submission overlay is already active; keep this instance in the DOM without showing.
            } else {
                this.modalInstance.show();
            }
        });
    },
    beforeDestroy() {
        this.blurModalFocus();
        if (this.$el && this.onHidden) {
            this.$el.removeEventListener('hidden.bs.modal', this.onHidden);
        }
        if (this.modalInstance) {
            this.modalInstance.dispose();
            this.modalInstance = null;
        }
    },
    methods: {
        blurModalFocus() {
            const ae = document.activeElement;
            if (ae && this.$el && typeof this.$el.contains === 'function' && this.$el.contains(ae)) {
                ae.blur();
            }
        },
        closeModal() {
            this.blurModalFocus();
            if (this.modalInstance) {
                this.modalInstance.hide();
                return;
            }
            this.$emit('close');
        },
        finalHref(type) {
            if (!this.pinboardUuid) return '#';
            return `/pinboards/${this.pinboardUuid}/booking/${type}`;
        },
        finalPageRedirect(type, event) {
            if (event) event.preventDefault();
            const href = this.finalHref(type);
            if (!href || href === '#') return;
            window.open(href, '_blank');
        },
        handleShowroomVisitBooking(event, tourType = 'physicalTour') {
            if (event) event.preventDefault();
            this.$emit('book-showroom', tourType);
        },
        handleProjectSubmissionPopUp(event, type = 'email') {
            if (event) event.preventDefault();
            this.$emit('project-submission-popup', type);
        },
    },
    template: /* html */ `
        <div
            class="modal fade th-pinboard-modal backdrop-static"
            id="pinboardCommunicationModal"
            tabindex="-1"
            aria-labelledby="exampleModalLongTitle"
            aria-hidden="true"
            data-bs-backdrop="false"
            style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1060;"
        >
            <div class="pinboard-modal-container">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content modelBorderRadius">
                        <div class="modal-header">
                           <!-- <span v-if="!(loggedInUser && customer?.is_verified && !showBookingModal)">
                                {{ pinboardTitle }}
                            </span> -->
                            <button
                                type="button"
                                class="btn-close"
                                aria-label="Close"
                                @click="closeModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div id="bookingConfirmationModal">
                                <div class="px-30 pb-40 sendToSell-modal-body">
                                    <div class="text-left py-40" v-if="showProjectSuccessMessage">
                                        <h2 class="font-weight-700">Project Saved Successfully</h2>
                                        <p class="font-weight-400">We’ve sent a copy of your pinboard to your inbox for safekeeping.</p>
                                    </div>
                                    <h6>
                                        Connect with a Krost Consultant to get a quote or <br>
                                        refine your layout.
                                    </h6>

                                    <div class="d-flex flex-column gap-4 button-group">
                                        <button type="button" class="text-start d-flex align-items-center"
                                            @click.prevent="$emit('open-booking-call-request', $event)"
                                        >
                                            <i class="fa fa-phone" style="width: 25px;"></i>
                                            <span>Request a Call Back</span>
                                        </button>

                                        <!-- <a class="text-start d-flex align-items-center"
                                            @click.prevent="finalPageRedirect('email', $event)"
                                            :href="finalHref('email')">
                                            <i class="fa-solid fa-envelope" style="width: 25px;"></i>
                                            <span>Email a Consultant</span>
                                        </a> -->


                                        <button type="button" id="ProdjectSubmissionButton"
                                            class="text-start d-flex align-items-center"
                                            @click.prevent="handleProjectSubmissionPopUp($event, 'email')">
                                             <i class="fa-solid fa-envelope" style="width: 25px;"></i>
                                            <span>Email a Consultant</span>
                                        </button>



                                        <button type="button" id="bookShowroomVisitButton"
                                            class="text-start d-flex align-items-center"
                                            @click.prevent="handleShowroomVisitBooking($event, 'physicalTour')">
                                            <i class="fa-solid fa-calendar-days" style="width: 25px;"></i>
                                            <span>Book a Showroom Tour</span>
                                        </button>

                                        <button type="button" class="text-start d-flex align-items-center"
                                            @click.prevent="handleShowroomVisitBooking($event, 'virtualMeeting')">
                                            <i class="fa fa-video" style="width: 25px; font-size: 15px;"></i>
                                            <span>Book a Video Consultation</span>
                                        </button>

                                        <div class="d-flex flex-column flex-md-row justify-content-between w-100" style="gap: 8px;" v-if="showProjectSuccessMessage">
                                            <a href="/" class="th-btn-gray py-10 border-0 bg-gray w-100"
                                                style="padding: 16px 30px; border-radius: 0px; display: flex; align-items: center; justify-content: center;">
                                                Continue Pinning
                                            </a>
                                            <a href="/account/virtual-pinboards"
                                                class="th-btn-primary py-10 border-0 bg-primary w-100"
                                                style="padding: 16px 30px; border-radius: 0px; display: flex; align-items: center; justify-content: center;">
                                                View Pinboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};