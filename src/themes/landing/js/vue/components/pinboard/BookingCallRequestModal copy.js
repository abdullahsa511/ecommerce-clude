// managePinboardStore 
import managePinboardStore from '../../store/managePinboardStore.js';
export default {
  name: "BookingCallRequestModal",
  props: {
    pinboardTitle: { type: String, default: "" },
    pinboardId: { type: [Number, String], default: null },
    pinboardUuid: { type: String, default: null },
    loggedInUser: { type: Object, default: null },
    nearestShowroom: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      modalInstance: null,
      onHidden: null,
      isSubmitting: false,
      message: "",
      countryDialCode: "+61",
      phoneNumber: "",
      additionalNotes: "",
      agreeToBeContacted: false,
    };
  },
  computed: {
    notesCharCount() {
      return `${String(this.additionalNotes || "").length} / 600 characters`;
    },
  },
  mounted() {
    this.$nextTick(() => {
      if (!window.bootstrap || !this.$el) return;
      this.modalInstance = new bootstrap.Modal(this.$el, {
        backdrop: "static",
        keyboard: false,
      });
      this.onHidden = () => this.$emit("close");
      this.$el.addEventListener("hidden.bs.modal", this.onHidden);
      this.modalInstance.show();
    });
  },
  beforeDestroy() {
    if (this.$el && this.onHidden) {
      this.$el.removeEventListener("hidden.bs.modal", this.onHidden);
    }
    if (this.modalInstance) {
      try {
        this.modalInstance.dispose();
      } catch (e) {
        /* ignore */
      }
      this.modalInstance = null;
    }
  },
  methods: {
    blurModalFocus() {
      const ae = document.activeElement;
      if (
        ae &&
        this.$el &&
        typeof this.$el.contains === "function" &&
        this.$el.contains(ae)
      ) {
        ae.blur();
      }
    },
    closeModal() {
      this.blurModalFocus();
      if (this.modalInstance) {
        this.modalInstance.hide();
        return;
      }
      this.$emit("close");
    },
    normalizePhoneDigits(value) {
      return String(value || "").replace(/\D/g, "");
    },
    async submitCallRequest() {
      this.message = "";
      if (!this.pinboardId) {
        this.message = "Pinboard not found";
        return;
      }
      const phoneDigits = this.normalizePhoneDigits(this.phoneNumber);
      if (!phoneDigits) {
        this.message = "Phone number is required";
        return;
      }
      if (!this.agreeToBeContacted) {
        this.message = "Please agree to be contacted";
        return;
      }

      this.isSubmitting = true;
      try {
        const payload = {
          pinboard_id: this.pinboardId,
          phone_number: `${String(this.countryDialCode || "").trim()}${phoneDigits}`,
          name:
            this.loggedInUser?.name ||
            this.loggedInUser?.first_name ||
            this.loggedInUser?.full_name ||
            "",
          note: this.additionalNotes || "",
        };
        const response = await fetch("/api/booking-phone-call", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        });
        const contentType = response?.headers?.get("content-type") || "";
        const result = contentType.toLowerCase().includes("application/json")
          ? await response.json()
          : { success: false, message: "Unexpected server response" };
        if (!result?.success) {
          this.message = result?.message || "Request failed";
          return;
        }
        await managePinboardStore.commit('UPDATE_PINBOARD_STATUS', { pinboardId: this.pinboardId });
        // redirect to the pinboard page
        window.location.href = `/pinboards/${this.pinboardUuid}/phone-call-request`;
        // this.$emit("submit-success");
        // this.closeModal();
      } catch (e) {
        this.message = e?.message || "Request failed";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  template: /* html */ `
        <div
            class="modal fade th-pinboard-modal th-call-request-modal backdrop-static"
            id="pinboardBookingCallRequestModal"
            tabindex="-1"
            aria-labelledby="pinboardBookingCallRequestModalLabel"
            aria-hidden="true"
            data-bs-backdrop="false"
            aria-modal="true"
            role="dialog"
        >
            <div class="pinboard-modal-container">
                <div class="modal-dialog modal-lg modal-dialog-centered crm-dialog">
                    <div class="modal-content modelBorderRadius crm-content">
                        <div class="modal-header crm-header">
                            <button
                                type="button"
                                class="btn-close crm-close"
                                aria-label="Close"
                                @click="closeModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="crm-inner sendToSell-modal-body">
                                <h5 class="crm-eyebrow">Request a call back</h5>
                                <h2 class="crm-title" id="pinboardBookingCallRequestModalLabel">
                                    Leave your number, we'll be in touch shortly.
                                </h2>

                                <div class="crm-label-row">
                                    <div class="crm-label">Phone number</div>
                                    <div class="crm-meta">REQUIRED</div>
                                </div>

                                <div class="crm-field-wrap">
                                    <div class="crm-phone-input">
                                        <div class="crm-dial-code">
                                            <svg
                                                class="crm-flag"
                                                viewBox="0 0 1200 600"
                                                aria-hidden="true"
                                                focusable="false"
                                            >
                                                <rect width="1200" height="600" fill="#00008b"></rect>
                                                <svg x="0" y="0" width="600" height="300" viewBox="0 0 600 300">
                                                    <g>
                                                        <rect width="600" height="300" fill="#012169"></rect>
                                                        <path d="M0 0L600 300M600 0L0 300" stroke="#fff" stroke-width="60"></path>
                                                        <path d="M0 0L600 300M600 0L0 300" stroke="#c8102e" stroke-width="32"></path>
                                                        <path d="M300 0V300M0 150H600" stroke="#fff" stroke-width="100"></path>
                                                        <path d="M300 0V300M0 150H600" stroke="#c8102e" stroke-width="60"></path>
                                                    </g>
                                                </svg>
                                                <path fill="#fff" d="M300 360l18 55 58-1-47 34 19 55-48-34-47 34 18-55-47-34 58 1zM900 120l13 39 41-1-33 24 13 39-34-24-33 24 13-39-33-24 41 1zM800 260l10 31 33-1-27 19 11 31-27-19-26 19 10-31-27-19 33 1zM990 250l10 31 33-1-27 19 11 31-27-19-26 19 10-31-27-19 33 1zM900 430l10 31 33-1-27 19 11 31-27-19-26 19 10-31-27-19 33 1zM1060 340l8 24 25-1-20 15 8 24-21-15-20 15 8-24-20-15 25 1z"></path>
                                            </svg>
                                            <span class="crm-country">AU</span>
                                            <span class="crm-dial-code-text">{{ countryDialCode }}</span>
                                        </div>
                                        <input
                                            type="tel"
                                            class="form-control crm-phone-field"
                                            placeholder="0400 000 000"
                                            v-model="phoneNumber"
                                        />
                                    </div>
                                </div>

                                <div class="crm-label-row">
                                    <div class="crm-label">Additional notes</div>
                                    <div class="crm-meta">OPTIONAL</div>
                                </div>

                                <div class="crm-notes-wrap">
                                    <textarea
                                        class="form-control crm-notes"
                                        placeholder="Anything you'd like us to know before the call."
                                        v-model="additionalNotes"
                                        maxlength="600"
                                        rows="4"
                                    ></textarea>
                                    <div class="crm-counter">
                                        {{ notesCharCount }}
                                    </div>
                                </div>

                                <div class="crm-agree-row">
                                    <input
                                        class="form-check-input crm-agree-input"
                                        type="checkbox"
                                        id="pinboardCallRequestAgree"
                                        v-model="agreeToBeContacted"
                                    />
                                    <label class="form-check-label crm-agree-label" for="pinboardCallRequestAgree">
                                        I agree to be contacted by Krost about this enquiry.
                                        <a href="content/page.html" target="_blank" rel="noopener">Privacy policy.</a>
                                    </label>
                                </div>

                                <div class="crm-actions">
                                    <button
                                        type="button"
                                        class="crm-btn crm-btn--cancel"
                                        @click.prevent="closeModal"
                                    >
                                        CANCEL
                                    </button>
                                    <button
                                        type="button"
                                        class="crm-btn crm-btn--submit"
                                        :disabled="isSubmitting"
                                        @click.prevent="submitCallRequest"
                                    >
                                        <span
                                            v-if="isSubmitting"
                                            class="spinner-border spinner-border-sm me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        SEND REQUEST
                                    </button>
                                </div>

                                <div v-if="message" class="crm-message">
                                    <div class="text-danger" role="alert" aria-live="polite">
                                      {{ message }}
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