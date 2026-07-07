export default {
  name: "BookingRescheduleTimeModal",
  props: {
    visitData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      modalInstance: null,
      onHidden: null,
      localSelectedDate: "",
      email: "",
      message: "",
      isSubmitting: false,
      bookingInProgress: false,
      selectedSlotId: "",
      selectedSlotValue: "",
      bookedSlotSet: new Set(),
      otpValues: ["", "", "", "", "", ""],
      otpTimerText: "Resend in 00:00",
      otpSecondsLeft: 0,
      otpTimerRef: null,
      showVerifyForm: false,
      morningSlots: [
        { id: "reschedule-time-slot-1", value: "09:00:00", label: "9:00 AM" },
        { id: "reschedule-time-slot-2", value: "09:30:00", label: "9:30 AM" },
        { id: "reschedule-time-slot-3", value: "10:00:00", label: "10:00 AM" },
        { id: "reschedule-time-slot-4", value: "10:30:00", label: "10:30 AM" },
        { id: "reschedule-time-slot-5", value: "11:00:00", label: "11:00 AM" },
        { id: "reschedule-time-slot-6", value: "11:30:00", label: "11:30 AM" },
        { id: "reschedule-time-slot-7", value: "12:00:00", label: "12:00 PM" },
        { id: "reschedule-time-slot-8", value: "12:30:00", label: "12:30 PM" },
      ],
      eveningSlots: [
        { id: "reschedule-time-slot-9", value: "13:00:00", label: "1:00 PM" },
        { id: "reschedule-time-slot-10", value: "13:30:00", label: "1:30 PM" },
        { id: "reschedule-time-slot-11", value: "14:00:00", label: "2:00 PM" },
        { id: "reschedule-time-slot-12", value: "14:30:00", label: "2:30 PM" },
        { id: "reschedule-time-slot-13", value: "15:00:00", label: "3:00 PM" },
        { id: "reschedule-time-slot-14", value: "15:30:00", label: "3:30 PM" },
        { id: "reschedule-time-slot-15", value: "16:00:00", label: "4:00 PM" },
        { id: "reschedule-time-slot-16", value: "16:30:00", label: "4:30 PM" },
      ],
      fallbackHolidays: {
        SYDNEY: ["2026-01-01", "2026-01-26", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-04-27", "2026-06-08", "2026-10-05", "2026-12-25", "2026-12-26", "2026-12-28"],
        MELBOURNE: ["2026-01-01", "2026-01-26", "2026-03-09", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-06-08", "2026-11-03", "2026-12-25", "2026-12-26", "2026-12-28"],
        BRISBANE: ["2026-01-01", "2026-01-26", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-05-04", "2026-08-12", "2026-10-05", "2026-12-24", "2026-12-25", "2026-12-26", "2026-12-28"],
      },
    };
  },
  computed: {
    showroomId() {
      return this.visitData?.showroomId || "";
    },
    visitShowroomId() {
      return this.visitData?.visitShowroomId || "";
    },
    existingDate() {
      return this.visitData?.selectedDate || "";
    },
    existingMeetingTime() {
      return this.visitData?.meetingTime || "";
    },
    normalizedExistingDate() {
      return this.normalizeDateIso(this.existingDate);
    },
    guestName() {
      return this.visitData?.guestName || "";
    },
    showroomContactId() {
      return this.visitData?.showroomContactId || "";
    },
    customerId() {
      return this.visitData?.customerId || "";
    },
    title() {
      const tourType = this.visitData?.tourType || "physicalTour";
      return tourType === "physicalTour"
        ? "Reschedule for Showroom Tour"
        : "Reschedule for Virtual Meeting";
    },
  },
  async mounted() {
    this.email = this.visitData?.email || "";
    if (!this.showroomId) {
      this.message = "Showroom is missing. Please refresh and try again.";
      return;
    }
    this.localSelectedDate = this.contactSalesNextAllowedIso(
      this.existingDate || this.formatIso(new Date()),
      this.showroomId,
    );
    await this.fetchBookedSlotsForDate(this.localSelectedDate);
    this.markExistingMeetingSlot();

    this.$nextTick(() => {
      if (!window.bootstrap || !this.$el) return;
      this.modalInstance = new window.bootstrap.Modal(this.$el, {
        backdrop: "static",
        keyboard: false,
      });
      this.onHidden = () => {
        this.$emit("close");
      };
      this.$el.addEventListener("hidden.bs.modal", this.onHidden);
      this.modalInstance.show();
      setTimeout(() => {
        this.initDateFlatpickr();
      }, 150);
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
        /* noop */
      }
      this.modalInstance = null;
    }
    this.destroyDateFlatpickr();
    this.clearOtpTimer();
  },
  methods: {
    closeModal() {
      if (this.modalInstance) {
        this.modalInstance.hide();
      } else {
        this.$emit("close");
      }
    },
    to24HourWithSeconds(time12h) {
      if (!time12h) return "";
      const raw = String(time12h).trim();
      if (/^\d{2}:\d{2}:\d{2}$/.test(raw)) return raw;
      if (/^\d{2}:\d{2}$/.test(raw)) return `${raw}:00`;

      const compact = raw.replace(/\s+/g, "");
      const ampmMatch = compact.match(/^(\d{1,2}):(\d{2})(AM|PM)$/i);
      if (!ampmMatch) return "";
      let hours = parseInt(ampmMatch[1], 10);
      const minutes = parseInt(ampmMatch[2], 10);
      const modifier = String(ampmMatch[3] || "").toUpperCase();
      if (Number.isNaN(hours) || Number.isNaN(minutes)) return "";
      if (modifier === "PM" && hours !== 12) hours += 12;
      if (modifier === "AM" && hours === 12) hours = 0;
      return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:00`;
    },
    normalizeDateIso(inputDate) {
      const raw = String(inputDate || "").trim();
      if (!raw) return "";
      const isoMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
      if (isoMatch) return `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}`;
      const parsed = new Date(raw);
      if (!Number.isNaN(parsed.getTime())) {
        return this.formatIso(parsed);
      }
      return "";
    },
    formatIso(dateObj) {
      return `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, "0")}-${String(dateObj.getDate()).padStart(2, "0")}`;
    },
    parseIso(iso) {
      const p = String(iso || "").split("-");
      if (p.length !== 3) return new Date();
      return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
    },
    getHolidayListByShowroomId(showroomId) {
      const holidayWindow = window.HOLIDAYS_2026 || {};
      if (String(showroomId) === "1") return holidayWindow.SYDNEY || [];
      if (String(showroomId) === "2") return holidayWindow.MELBOURNE || [];
      if (String(showroomId) === "3") return holidayWindow.BRISBANE || [];
      return [];
    },
    resolvedHolidayList(showroomId) {
      const list = this.getHolidayListByShowroomId(showroomId);
      if (list.length) return list;
      if (String(showroomId) === "2") return this.fallbackHolidays.MELBOURNE;
      if (String(showroomId) === "3") return this.fallbackHolidays.BRISBANE;
      return this.fallbackHolidays.SYDNEY;
    },
    isDisabledBookingDay(dateObj, showroomId) {
      const wd = dateObj.getDay();
      if (wd === 0 || wd === 6) return true;
      const iso = this.formatIso(dateObj);
      return this.resolvedHolidayList(showroomId).includes(iso);
    },
    contactSalesNextAllowedIso(fromIso, showroomId) {
      let d = this.parseIso(fromIso);
      for (let i = 0; i < 400; i++) {
        if (!this.isDisabledBookingDay(d, showroomId)) return this.formatIso(d);
        d.setDate(d.getDate() + 1);
      }
      return this.formatIso(d);
    },
    contactSalesDisableRules(showroomId) {
      const set = new Set(this.resolvedHolidayList(showroomId));
      return [
        function (date) {
          const wd = date.getDay();
          if (wd === 0 || wd === 6) return true;
          const y = date.getFullYear();
          const m = String(date.getMonth() + 1).padStart(2, "0");
          const d = String(date.getDate()).padStart(2, "0");
          return set.has(`${y}-${m}-${d}`);
        },
      ];
    },
    destroyDateFlatpickr() {
      const input = this.$refs.dateInput;
      if (input && input._flatpickr) {
        try {
          input._flatpickr.destroy();
        } catch (e) {
          /* noop */
        }
      }
    },
    initDateFlatpickr() {
      if (typeof window.jQuery === "undefined" || !window.jQuery.fn.flatpickr) return;
      const input = this.$refs.dateInput;
      if (!input || input.type === "date") return;
      this.destroyDateFlatpickr();
      const allowed = this.contactSalesNextAllowedIso(this.localSelectedDate, this.showroomId);
      this.localSelectedDate = allowed;
      window.jQuery(input).flatpickr({
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: this.contactSalesDisableRules(this.showroomId),
        defaultDate: allowed,
        allowInput: false,
        onChange: (_d, dateStr) => {
          if (!dateStr) return;
          this.localSelectedDate = dateStr;
          this.handleDateChange();
        },
      });
    },
    async parseApiResponse(response) {
      const contentType = response?.headers?.get("content-type") || "";
      const isJson = contentType.toLowerCase().includes("application/json");
      if (isJson) {
        try {
          return await response.json();
        } catch (e) {
          return { success: false, message: "Unexpected response" };
        }
      }
      const text = await response.text();
      return { success: response?.ok, message: text || "Request failed" };
    },
    async postData(url, payload) {
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      return this.parseApiResponse(response);
    },
    async fetchBookedTimes(showroomId, selectedDate, visitShowroomId) {
      const url = `/api/fetch-booked-data/${showroomId}/${selectedDate}?id=${visitShowroomId}`;
      const response = await fetch(url, { method: "GET" });
      if (!response.ok) {
        throw new Error(`API Error: ${response.status}`);
      }
      const result = await response.json();
      const rows = Array.isArray(result?.data) ? result.data : Object.values(result?.data || {});
      return rows.map((row) => row?.meeting_time || "").filter(Boolean);
    },
    async fetchBookedSlotsForDate(dateIso) {
      if (!this.showroomId) {
        this.bookedSlotSet = new Set();
        return;
      }
      const bookedTimes = await this.fetchBookedTimes(this.showroomId, dateIso, this.visitShowroomId);
      console.log("bookedTimes", bookedTimes);
      this.bookedSlotSet = new Set(bookedTimes.map((time) => String(time || "").trim()).filter(Boolean));
    },
    isSlotBooked(value) {
      return this.bookedSlotSet.has(String(value || "").trim());
    },
    isExistingSlot(value) {
      if (!this.existingMeetingTime || !this.normalizedExistingDate) return false;
      if (this.normalizeDateIso(this.localSelectedDate) !== this.normalizedExistingDate) return false;
      return this.to24HourWithSeconds(this.existingMeetingTime) === value;
    },
    markExistingMeetingSlot() {
      const normalized = this.to24HourWithSeconds(this.existingMeetingTime);
      if (!normalized || this.normalizeDateIso(this.localSelectedDate) !== this.normalizedExistingDate) return;
      if (this.isSlotBooked(normalized)) return;
      const matched = [...this.morningSlots, ...this.eveningSlots].find((slot) => slot.value === normalized);
      if (!matched) return;
      this.selectedSlotId = matched.id;
      this.selectedSlotValue = matched.value;
    },
    async handleDateChange() {
      this.selectedSlotId = "";
      this.selectedSlotValue = "";
      this.message = "";
      await this.fetchBookedSlotsForDate(this.localSelectedDate);
      this.markExistingMeetingSlot();
    },
    selectSlot(slot) {
      if (!slot || this.isSlotBooked(slot.value)) return;
      this.selectedSlotId = slot.id;
      this.selectedSlotValue = slot.value;
      this.message = "";
    },
    validateEmail(email) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    buildMeetingDates(date, meetingTime, duration) {
      const start = new Date(`${date}T${meetingTime}`);
      const end = new Date(start.getTime() + duration * 60000);
      return { start, end };
    },
    makeMeetingLink({ start, end, title, location }) {
      const format = (date) => date.toISOString().replace(/[-:]/g, "").split(".")[0] + "Z";
      const startStr = format(start);
      const endStr = format(end);
      const add = "c_18895tnlfoecignhmo94g3jqjc402@resource.calendar.google.com";
      return `https://calendar.google.com/calendar/u/0/r/eventedit?text=${encodeURIComponent(title)}&dates=${startStr}/${endStr}&location=${encodeURIComponent(location || "")}&add=${encodeURIComponent(add)}`;
    },
    clearOtpTimer() {
      if (this.otpTimerRef) {
        clearInterval(this.otpTimerRef);
        this.otpTimerRef = null;
      }
      this.otpSecondsLeft = 0;
      this.otpTimerText = "Resend in 00:00";
    },
    startOtpTimer(duration = 120) {
      this.clearOtpTimer();
      this.otpSecondsLeft = duration;
      this.otpTimerRef = setInterval(() => {
        const minutes = Math.floor(this.otpSecondsLeft / 60);
        const seconds = this.otpSecondsLeft % 60;
        this.otpTimerText = `Resend in ${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
        if (this.otpSecondsLeft <= 0) {
          this.clearOtpTimer();
        } else {
          this.otpSecondsLeft -= 1;
        }
      }, 1000);
    },
    focusOtpInput(index) {
      if (index < 0 || index >= this.otpValues.length) return;
      this.$nextTick(() => {
        let el = this.$refs[`otp-${index}`];
        if (Array.isArray(el)) el = el[0];
        if (el && typeof el.focus === "function") el.focus();
      });
    },
    onOtpFocus(event) {
      const el = event?.target;
      if (!el || typeof el.setSelectionRange !== "function") return;
      this.$nextTick(() => {
        try {
          const len = (el.value || "").length;
          el.setSelectionRange(len, len);
        } catch (e) {
          /* ignore */
        }
      });
    },
    onOtpInput(index, event) {
      if (this.bookingInProgress) return;
      const raw = (event?.target?.value || "").replace(/\D/g, "");
      const char = raw.slice(-1);
      this.$set(this.otpValues, index, char);
      if (char && index < this.otpValues.length - 1) {
        this.focusOtpInput(index + 1);
      }
    },
    handleFormSubmit(event) {
      event.preventDefault();
      if (this.bookingInProgress) return;
      this.handleVerifyAndReschedule();
    },
    handleOtpEnter(event) {
      event.preventDefault();
      if (this.bookingInProgress) return;
      this.handleVerifyAndReschedule();
    },
    onOtpKeyDown(index, event) {
      if (this.bookingInProgress) return;
      if (event.key === "ArrowLeft" && index > 0) {
        event.preventDefault();
        this.focusOtpInput(index - 1);
        return;
      }
      if (event.key === "ArrowRight" && index < this.otpValues.length - 1) {
        event.preventDefault();
        this.focusOtpInput(index + 1);
        return;
      }

      if (event.key === "Delete" && this.otpValues[index]) {
        event.preventDefault();
        this.$set(this.otpValues, index, "");
        return;
      }

      if (event.key !== "Backspace") return;

      if (this.otpValues[index]) {
        event.preventDefault();
        this.$set(this.otpValues, index, "");
        return;
      }

      if (index > 0) {
        event.preventDefault();
        const prev = index - 1;
        this.$set(this.otpValues, prev, "");
        this.focusOtpInput(prev);
      }
    },
    onOtpPaste(event) {
      if (this.bookingInProgress) return;
      event.preventDefault();

      const clipboardData = event.clipboardData || window.clipboardData;
      const raw = clipboardData ? clipboardData.getData("text") : "";
      const digits = (raw || "").replace(/\D/g, "").slice(0, this.otpValues.length);
      if (!digits) return;

      digits.split("").forEach((digit, index) => {
        if (index < this.otpValues.length) {
          this.$set(this.otpValues, index, digit);
        }
      });

      const firstEmpty = this.otpValues.findIndex((v) => !v);
      const focusIndex = firstEmpty === -1 ? this.otpValues.length - 1 : firstEmpty;

      this.focusOtpInput(focusIndex);
    },
    async handleResendOtp() {
      if (this.otpSecondsLeft > 0 || this.isSubmitting || this.bookingInProgress) return;
      this.message = "";
      const response = await this.postData("/api/send-email-verification", {
        email: this.email,
        customer_name: this.guestName,
        subject: "Resend Booking OTP with Krost",
      });
      if (!response?.success) {
        this.message = response?.message || "Unable to resend OTP";
        return;
      }
      this.startOtpTimer();
    },
    async handleSendOtp() {
      this.message = "";
      const trimmedEmail = String(this.email || "").trim();
      if (!trimmedEmail) {
        this.message = "Please enter your email";
        return;
      }
      if (!this.validateEmail(trimmedEmail)) {
        this.message = "Please enter a valid email";
        return;
      }
      if (!this.selectedSlotValue) {
        this.message = "Please select a time slot";
        return;
      }

      this.isSubmitting = true;
      try {
        const response = await this.postData("/api/send-email-verification", {
          email: trimmedEmail,
          customer_name: this.guestName,
          subject: "Booking Verification Code with Krost",
        });
        if (!response?.success) {
          this.message = response?.message || "Unable to send verification code";
          return;
        }
        this.showVerifyForm = true;
        this.otpValues = ["", "", "", "", "", ""];
        this.startOtpTimer();
      } catch (e) {
        this.message = e?.message || "Unable to send verification code";
      } finally {
        this.isSubmitting = false;
      }
    },
    async handleVerifyAndReschedule() {
      if (this.bookingInProgress) return;
      const otp = this.otpValues.join("");
      if (otp.length !== 6 || Number.isNaN(Number(otp))) {
        this.message = "Please enter the 6-digit code";
        return;
      }
      this.bookingInProgress = true;
      this.message = "";
      let rescheduleSucceeded = false;
      try {
        const otpResponse = await this.postData("/api/verify-email", {
          email: this.email,
          otp,
        });
        if (!otpResponse?.success) {
          this.message = otpResponse?.message || "OTP verification failed";
          return;
        }

        const duration = 30;
        const { start, end } = this.buildMeetingDates(
          this.localSelectedDate,
          this.selectedSlotValue,
          duration,
        );
        const meetingLink = this.makeMeetingLink({
          start,
          end,
          title: "Meeting",
          location: this.visitData?.locationAddress || "",
        });

        const bookingData = {
          visit_showroom_id: this.visitShowroomId,
          showroom_contact_id: this.showroomContactId,
          customer_id: this.customerId,
          email: this.email,
          customer_name: this.guestName,
          showroom_id: this.showroomId,
          tour_type: this.visitData?.tourType || "",
          date: this.localSelectedDate,
          meeting_time: this.selectedSlotValue,
          duration,
          time_zone: this.visitData?.timeZone || "",
          label: "Meeting",
          location: this.visitData?.locationAddress || "",
          meeting_link: meetingLink,
          google_map_link: this.visitData?.googleMapLink || "",
          pinboard_id: this.visitData?.pinboardId || "",
        };

        const rescheduleResponse = await this.postData("/api/reschedule-booking", bookingData);
        if (!rescheduleResponse?.success) {
          this.message = rescheduleResponse?.message || "Booking failed";
          return;
        }
        const uuid = rescheduleResponse?.data?.uuid;
        if (uuid) {
          rescheduleSucceeded = true;
          const tourType = this.visitData?.tourType || "physicalTour";
          if (this.visitData?.pinboardId) {
            if (tourType === "physicalTour") {
              window.location.href = `/pinboards/rescheduled-showroom-visit/${uuid}`;
            } else {
              window.location.href = `/pinboards/rescheduled-virtual-meeting/${uuid}`;
            }
          } else {
            if (tourType === "physicalTour") {
              window.location.href = `/contact-us/rescheduled-physical-showroom-visit/${uuid}`;
            } else {
              window.location.href = `/contact-us/rescheduled-virtual-meeting-booking/${uuid}`;
            }
          }
        }

      } catch (e) {
        this.message = e?.message || "Booking failed";
      } finally {
        if (!rescheduleSucceeded) {
          this.bookingInProgress = false;
        }
      }
    },
  },
  template: /* html */ `
    <div
      class="modal fade"
      id="timeSlotsModal"
      tabindex="-1"
      aria-labelledby="timeSlotsModalLabelContactSalesVue"
      aria-modal="true"
      role="dialog"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
        <div class="modal-content px-80 py-60">
          <div class="modal-header">
            <h5 class="modal-title" id="timeSlotsModalLabelContactSalesVue" v-if="!showVerifyForm">{{ this.title }}</h5>
            <button type="button" class="btn-close" aria-label="Close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div v-if="!showVerifyForm" id="booking-form-container">
              <div class="gap-10">
                <div class="th-form-row">
                  <label for="ts-selected-date-vue">Date</label>
                  <div class="th-field">
                    <div class="th-input-group d-flex align-items-center">
                      <i class="fa-solid fa-calendar th-input-icon"></i>
                      <input
                        ref="dateInput"
                        type="text"
                        id="ts-selected-date-vue"
                        class="form-control th-date-input"
                        placeholder="Select date"
                        readonly
                        autocomplete="off"
                        v-model="localSelectedDate"
                      />
                    </div>
                  </div>
                </div>

                <div class="th-form-row">
                  <label>Time Zone</label>
                  <div class="th-field">
                    <div class="th-input-group d-flex align-items-center">
                      <i class="fa-solid fa-globe th-input-icon"></i>
                      <input type="text" class="form-control" :value="visitData.timeZone" disabled />
                    </div>
                  </div>
                </div>

                <div class="th-form-row">
                  <label for="ts-email-not-logged-in-email-vue">Email</label>
                  <div class="th-field">
                    <div
                      class="th-input-group d-flex align-items-center"
                      :class="{ 'invalid-email': !!message && !validateEmail(email || '') }"
                    >
                      <i class="fa-solid fa-envelope th-input-icon"></i>
                      <input
                        type="email"
                        id="ts-email-not-logged-in-email-vue"
                        class="form-control"
                        placeholder="Enter your email"
                        style="border: none !important;"
                        v-model="email"
                      />
                    </div>
                  </div>
                </div>

                <div class="th-form-row th-pt-20">
                  <label>Pick a time</label>
                  <div class="th-field">
                    <strong class="d-block">Choose a slot below</strong>
                  </div>
                </div>
              </div>

              <div class="ts-slots-blocks pt-10">
                <div class="ts-slots-block ts-slots-morning">
                  <div class="ts-slots-grid d-flex flex-wrap gap-2">
                    <div
                      v-for="slot in morningSlots"
                      :key="slot.id"
                      class="th-time-slot"
                      :class="{
                        active: selectedSlotId === slot.id || isSlotBooked(slot.value),
                        disabled: isSlotBooked(slot.value),
                        'time-slot-disabled': isSlotBooked(slot.value),
                        'existing-booking-slot': isExistingSlot(slot.value),
                        'th-booked-time-slot': isSlotBooked(slot.value)
                      }"
                      :style="isExistingSlot(slot.value) ? { borderColor: '#dc3545', color: '#dc3545' } : null"
                      @click="selectSlot(slot)"
                    >
                      <i class="fa-light fa-arrow-right" :class="{ 'd-none': !isSlotBooked(slot.value) }"></i>
                      <input class="d-none" type="checkbox" :id="slot.id" :value="slot.value" :disabled="isSlotBooked(slot.value)" />
                      <label :for="slot.id" :style="isExistingSlot(slot.value) ? { color: '#dc3545' } : null">{{ slot.label }}</label>
                    </div>
                  </div>
                </div>

                <div class="ts-slots-block ts-slots-evening">
                  <div class="ts-slots-grid d-flex flex-wrap gap-2">
                    <div
                      v-for="slot in eveningSlots"
                      :key="slot.id"
                      class="th-time-slot"
                      :class="{
                        active: selectedSlotId === slot.id || isSlotBooked(slot.value),
                        disabled: isSlotBooked(slot.value),
                        'time-slot-disabled': isSlotBooked(slot.value),
                        'existing-booking-slot': isExistingSlot(slot.value),
                        'th-booked-time-slot': isSlotBooked(slot.value)
                      }"
                      :style="isExistingSlot(slot.value) ? { borderColor: '#dc3545', color: '#dc3545' } : null"
                      @click="selectSlot(slot)"
                    >
                      <i class="fa-light fa-arrow-right" :class="{ 'd-none': !isSlotBooked(slot.value) }"></i>
                      <input class="d-none" type="checkbox" :id="slot.id" :value="slot.value" :disabled="isSlotBooked(slot.value)" />
                      <label :for="slot.id" :style="isExistingSlot(slot.value) ? { color: '#dc3545' } : null">{{ slot.label }}</label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="th-pt-20">
                <button
                  type="button"
                  class="th-btn-primary w-100 text-capitalize"
                  :disabled="isSubmitting"
                  @click="handleSendOtp"
                >
                  <span
                    v-if="isSubmitting"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  Book
                </button>
              </div>
            </div>

            <div v-else id="book-now-verify-email-form-container">
              <form class="guest-signup-form" @submit.prevent="handleFormSubmit">
                <div class="text-center py-10 mb-20">
                  <h6>Verify it's you</h6>
                  <p>
                    We've sent a verification code to
                    <strong>{{ email }}</strong>.
                  </p>
                </div>

                <div class="otp-wrapper mb-20">
                  <input
                    v-for="(digit, index) in otpValues"
                    :key="'otp-' + index"
                    :ref="'otp-' + index"
                    type="text"
                    maxlength="1"
                    class="otp-input"
                    :value="digit"
                    :disabled="bookingInProgress"
                    @focus="onOtpFocus"
                    @input="onOtpInput(index, $event)"
                    @keydown="onOtpKeyDown(index, $event)"
                    @keydown.enter="handleOtpEnter"
                    @paste="onOtpPaste"
                  />
                </div>

                <button
                  type="submit"
                  class="th-btn-primary text-capitalize w-100 mt-15"
                  :class="{ disabled: bookingInProgress }"
                  :disabled="bookingInProgress"
                  :aria-disabled="bookingInProgress"
                >
                  <span
                    v-if="bookingInProgress"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  Verify & Continue
                </button>

                <div class="text-center mt-15">
                  <span>{{ otpTimerText }}</span>
                </div>

                <div class="text-center mt-15">
                  <small>
                    Didn't receive the code?
                    <a
                      href="javascript:void(0)"
                      class="resend-link"
                      :class="{ disabled: otpSecondsLeft > 0 || bookingInProgress }"
                      @click.prevent="handleResendOtp"
                    >
                      Resend OTP
                    </a>
                  </small>
                </div>
              </form>
            </div>

            <div v-if="message" class="col-md-10 text-center mx-auto px-2 px-md-3 pt-20">
              <div class="alert alert-danger" role="alert" aria-live="polite">{{ message }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
};
