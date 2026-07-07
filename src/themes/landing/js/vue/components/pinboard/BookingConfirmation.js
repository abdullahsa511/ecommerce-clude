export default {
  name: "BookingConfirmationModal",
  props: {
    pinboardTitle: { type: String, default: "" },
    pinboardId: { type: [Number, String], default: null },
    pinboardUuid: { type: String, default: null },
    loggedInUser: { type: Object, default: null },
    nearestShowroom: { type: Object, default: () => ({}) },
    page: { type: String, default: "virtual_pinboard" },
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
    closeModal() {
      if (this.modalInstance) {
        this.modalInstance.hide();
        return;
      }
      this.$emit("close");
    },
  },
  template: /* html */ `
        <div
            class="modal fade th-pinboard-modal backdrop-static"
            id="pinboardBookingConfirmationModal"
            tabindex="-1"
            aria-labelledby="pinboardBookingConfirmationModalLabel"
            aria-hidden="true"
            data-bs-backdrop="false"
            style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1060;"
            aria-modal="true"
            role="dialog"
        >
            <div class="pinboard-modal-container">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modelBorderRadius">
                        <div class="modal-header">
                            <button
                                type="button"
                                class="btn-close"
                                aria-label="Close"
                                @click="closeModal"
                                style="border: 1px solid black; border-radius: 50%; padding: 10px;"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="px-30 pb-30 text-center">
                                <div class="d-flex justify-content-center align-items-center mb-10">
                                    <div
                                        style="width:38px; height:38px; border-radius:50%; background:#171717; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px;"
                                    >✓</div>
                                </div>
                                <div style="letter-spacing:0.16em; font-size:10px; color:#8e8e8e;" class="mb-10">
                                    REQUEST RECEIVED
                                </div>
                                <h3 id="pinboardBookingConfirmationModalLabel" style="font-weight:700; margin-bottom:8px;">
                                    We'll be in touch shortly.
                                </h3>
                                <p style="font-size:13px; color:#666; margin-bottom:24px;">
                                    Your request has been received.<br />
                                    Our consultant will be in touch shortly.
                                </p>

                                <a
                                    class="th-btn-primary text-capitalize w-100"
                                    style="height:44px; background:#171717; border-color:#171717; font-size:12px; letter-spacing:0.08em;"
                                    href="/account/virtual-pinboards"
                                >
                                    BACK TO PINBOARD <i class="fa-solid fa-arrow-right" style="padding-left: 16px;"></i>
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-link mt-10"
                                    style="font-size:12px; color:#666; text-decoration:none;"
                                    @click.prevent="closeModal"
                                >
                                    Cancel request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};
