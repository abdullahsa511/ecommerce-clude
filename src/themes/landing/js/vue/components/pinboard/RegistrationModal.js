export default {
    name: 'RegistrationModal',
    props: {
        customer: {
            type: Object,
            default: () => ({}),
        },
        pinboardTitle: {
            type: String,
            default: '',
        },
        loggedInUser: {
            type: Object,
            default: null,
        },
        showBookingModal: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        errors: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            modalInstance: null,
            onHidden: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            if (!window.bootstrap || !this.$el) return;
            this.modalInstance = new bootstrap.Modal(this.$el, {
                backdrop: 'static',
                keyboard: false,
            });
            this.onHidden = () => {
                this.$emit('close');
            };
            this.$el.addEventListener('hidden.bs.modal', this.onHidden);
            this.modalInstance.show();
        });
    },
    beforeDestroy() {
        if (this.$el && this.onHidden) {
            this.$el.removeEventListener('hidden.bs.modal', this.onHidden);
        }
        if (this.modalInstance) {
            this.modalInstance.dispose();
            this.modalInstance = null;
        }
    },
    methods: {
        onFieldInput(field, event) {
            this.$emit('update-customer', { [field]: event.target.value });
        },
        onSignup() {
            this.$emit('signup');
        },
        closeModal() {
            if (this.modalInstance) {
                this.modalInstance.hide();
                return;
            }
            this.$emit('close');
        },
    },
    template: /* html */ `
        <div
            class="modal fade th-pinboard-modal backdrop-static"
            id="guestSignupModal"
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
                           <!-- <span v-if="!(loggedInUser && customer?.is_verified && !showBookingModal)">{{ pinboardTitle }}</span> -->
                            <button
                                type="button"
                                class="btn-close"
                                aria-label="Close"
                                @click="closeModal"
                            ></button>
                        </div>

                        <div class="modal-body">
                            <div id="save-pinboard-form-container">
                                <form id="save-pinboard-form" class="guest-signup-form">
                                    <div class="p-80 th-modal-body-padding">
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                placeholder="Name"
                                                :value="customer?.name || ''"
                                                @input="onFieldInput('name', $event)"
                                                :class="{ 'is-invalid': errors?.name }"
                                            >
                                            <span class="invalid-feedback" v-if="errors?.name">{{ errors.name }}</span>
                                        </div>

                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="customer.companyName"
                                                name="organization-name"
                                                placeholder="Organisation Name (optional)"
                                                :value="customer?.companyName || ''"
                                                @input="onFieldInput('companyName', $event)"
                                                :class="{ 'is-invalid': errors?.companyName }"
                                            >
                                            <span class="invalid-feedback" v-if="errors?.companyName">{{ errors.companyName }}</span>
                                        </div>

                                        <div class="form-group">
                                            <input
                                                type="phone"
                                                class="form-control"
                                                id="customer.phone"
                                                name="phone"
                                                placeholder="Phone Number (optional)"
                                                :value="customer?.phone || ''"
                                                @input="onFieldInput('phone', $event)"
                                            >
                                        </div>

                                        <div>
                                            <button
                                                type="submit"
                                                class="th-btn-primary text-capitalize w-100 mt-15"
                                                :class="{ 'disabled': loading }"
                                                id="save-pinboard-button"
                                                :disabled="loading"
                                                @click.prevent="onSignup"
                                            >
                                                Create Account to Save
                                                <span
                                                    v-if="loading"
                                                    class="spinner-border spinner-border-sm"
                                                    role="status"
                                                    aria-hidden="true"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};
