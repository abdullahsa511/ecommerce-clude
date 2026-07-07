export default {
    name: 'EmailVerificationModal',
    props: {
        customer: {
            type: Object,
            default: () => ({}),
        },
        pinboardTitle: {
            type: String,
            default: '',
        },
        subtitle: {
            type: String,
            default: 'Please enter the code below to save your project.',
        },
        loggedInUser: {
            type: Object,
            default: null,
        },
        showBookingModal: {
            type: Boolean,
            default: false,
        },
        verifyLoading: {
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
            otpDigits: ['', '', '', '', '', ''],
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
        closeModal() {
            if (this.modalInstance) {
                this.modalInstance.hide();
                return;
            }
            this.$emit('close');
        },
        handleOtpInput(index, event) {
            if (this.verifyLoading) return;
            const value = event.target.value;
            if (!/^[0-9]$/.test(value)) {
                this.$set(this.otpDigits, index, '');
                return;
            }
            if (index < this.otpDigits.length - 1) {
                this.$refs.otpInputs[index + 1]?.focus();
            }
        },
        handleBackspace(index) {
            if (this.verifyLoading) return;
            if (!this.otpDigits[index] && index > 0) {
                this.$refs.otpInputs[index - 1]?.focus();
            }
        },
        handleOtpPaste(event, startIndex = 0) {
            if (this.verifyLoading) return;
            event.preventDefault();
            const raw = event.clipboardData.getData('text') || '';
            const digits = raw.replace(/\D/g, '');
            if (!digits) return;

            digits.split('').forEach((digit, i) => {
                const idx = startIndex + i;
                if (idx < this.otpDigits.length) {
                    this.$set(this.otpDigits, idx, digit);
                }
            });

            const focusIndex = Math.min(
                startIndex + digits.length,
                this.otpDigits.length - 1
            );
            this.$nextTick(() => {
                this.$refs.otpInputs[focusIndex]?.focus();
            });
        },
        onVerify() {
            if (this.verifyLoading) return;
            this.$emit('verify', this.otpDigits.join(''));
        },
        handleFormSubmit(event) {
            event.preventDefault();
            if (this.verifyLoading) return;
            this.onVerify();
        },
        handleOtpEnter(event) {
            event.preventDefault();
            if (this.verifyLoading) return;
            this.onVerify();
        },
        onResendEmail() {
            if (this.verifyLoading) return;
            this.$emit('resend-email');
        },
    },
    template: /* html */ `
        <div
            class="modal fade th-pinboard-modal backdrop-static"
            id="guestEmailVerificationModal"
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
                                <div v-if="(!customer?.is_verified && customer?.uuid)">
                                    <form
                                        id="verify-email-form"
                                        class="guest-signup-form"
                                        @submit.prevent="handleFormSubmit"
                                    >
                                        <div class="text-center py-10 mb-20">
                                            <h6>Verify it's you</h6>
                                            <p>
                                                We've sent a verification code to
                                                <strong>{{ customer.email }}</strong>. <br>
                                               {{ subtitle }}
                                            </p>
                                        </div>

                                        <div class="otp-wrapper mb-20">
                                            <input
                                                v-for="(digit, index) in otpDigits"
                                                :key="index"
                                                ref="otpInputs"
                                                type="text"
                                                maxlength="1"
                                                class="otp-input"
                                                v-model="otpDigits[index]"
                                                :disabled="verifyLoading"
                                                @input="handleOtpInput(index, $event)"
                                                @keydown.backspace="handleBackspace(index)"
                                                @keydown.enter="handleOtpEnter"
                                                @paste="handleOtpPaste($event)"
                                            />
                                        </div>

                                        <div
                                            v-if="errors?.createPinboard"
                                            class="col-md-10 text-center mx-auto px-2 px-md-3 text-danger"
                                        >
                                            {{ errors.createPinboard }}
                                        </div>

                                        <button
                                            v-if="!loggedInUser"
                                            type="submit"
                                            class="th-btn-primary text-capitalize w-100 mt-15"
                                            :class="{ 'disabled': verifyLoading }"
                                            :disabled="verifyLoading"
                                            @click="onVerify"
                                            id="verify-email-button"
                                        >
                                            Verify & Continue
                                            <span
                                                v-if="verifyLoading"
                                                class="spinner-border spinner-border-sm"
                                                role="status"
                                            ></span>
                                        </button>

                                        <div class="text-center mt-15">
                                            <small>
                                                Didn't receive the code?
                                                <a
                                                    href="javascript:void(0)"
                                                    class="resend-link"
                                                    :class="{ disabled: verifyLoading }"
                                                    @click.prevent="onResendEmail"
                                                >
                                                    Resend Email
                                                </a>
                                            </small>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};
