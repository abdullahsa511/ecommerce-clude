import {
  attachBookingRecaptcha,
  getRecaptchaConfig,
  preloadRecaptcha,
} from "../../../recaptcha-v3.js";

export default {
  name: "BookingTimeModal",
  props: {
    pinboardTitle: {
      type: String,
      default: "",
    },
    pinboardId: {
      type: [Number, String],
      default: null,
    },
    pinboardUuid: {
      type: String,
      default: null,
    },
    selectedDate: {
      type: String,
      default: "",
    },
    tourType: {
      type: String,
      default: "physicalTour",
    },
    customer: {
      type: Object,
      default: () => ({}),
    },
    loggedInUser: {
      type: Object,
      default: null,
    },
    nearestShowroom: {
      type: Object,
      default: () => ({}),
    },
    enableEmailVerification: {
      type: Boolean,
      default: false,
    },
    page: {
      type: String,
      default: "virtual_pinboard",
    },
    source: {
      type: String,
      default: "Pinboard",
    }
  },
  data() {
    return {
      modalInstance: null,
      onHidden: null,
      localSelectedDate: "",
      selectedSlot: null,
      selectedSlotValue: "",
      checkedSlot: null,
      bookingEmail: "",
      bookingName: "",
      bookingEnquiryType: "",
      message: "",
      isSubmitting: false,
      recaptchaError: "",
      bookedSlotSet: new Set(),
      bookingTimeZone: "",
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
      holidays: {
        SYDNEY: [
          '2026-01-01', '2026-01-26', '2026-04-03', '2026-04-04', '2026-04-05',
          '2026-04-06', '2026-04-25', '2026-04-27', '2026-06-08', '2026-10-05',
          '2026-12-25', '2026-12-26', '2026-12-28',
        ],
        MELBOURNE: [
          '2026-01-01', '2026-01-26', '2026-03-09', '2026-04-03', '2026-04-04',
          '2026-04-05', '2026-04-06', '2026-04-25', '2026-06-08', '2026-11-03',
          '2026-12-25', '2026-12-26', '2026-12-28',
        ],
        BRISBANE: [
          '2026-01-01', '2026-01-26', '2026-04-03', '2026-04-04', '2026-04-05',
          '2026-04-06', '2026-04-25', '2026-05-04', '2026-08-12', '2026-10-05',
          '2026-12-24', '2026-12-25', '2026-12-26', '2026-12-28',
        ],
      },
    };
  },
  computed: {
    recaptchaSiteKey() {
      return getRecaptchaConfig().siteKey;
    },
    recaptchaEnabled() {
      return this.recaptchaSiteKey !== "";
    },
    showroom() {
      const showroom =
        this.nearestShowroom &&
          !Array.isArray(this.nearestShowroom) &&
          Object.keys(this.nearestShowroom).length
          ? this.nearestShowroom
          : {};
      return {
        showroom_contact_id: showroom.showroom_contact_id ?? 1,
        showroom_id: showroom.showroom_id ?? showroom.showrooms_id ?? 1,
        image: showroom.image || "/img/logo_black.png",
        title: showroom.title || "Krost Showroom",
        address:
          showroom.address || "Our consultant will share your visit details.",
      };
    },
    minSelectableDate() {
      return new Date().toISOString().slice(0, 10);
    },
  },
  watch: {
    selectedDate: {
      immediate: true,
      handler(value) {
        this.localSelectedDate = value || this.minSelectableDate;
        this.fetchBookedDataForDate();
        console.log("pinboard id:", this.pinboardId);
        console.log("pinboardUuid:", this.pinboardUuid);
      },
    },
    showroom: {
      immediate: true,
      async handler(value) {
        // console.log("showroom: watcher", value);
        await this.updateBookingTimeZone(value);
        this.$nextTick(async () => {
          this.syncTimeModalFlatpickrRules();
          await this.ensureValidDateForShowroom();
        });
      },
    },
    localSelectedDate(val) {
      if (!this._timeModalFp || !val) return;
      const cur = this._timeModalFp.selectedDates[0];
      const curIso = cur ? this.formatLocalDateIso(cur) : "";
      if (curIso !== val) {
        this._timeModalFp.setDate(val, false);
      }
    },
  },
  mounted() {
    if (this.recaptchaEnabled) {
      preloadRecaptcha(this.recaptchaSiteKey);
    }

    this.bookingEmail = this.loggedInUser?.email || this.customer?.email || "";
    this.bookingName = this.customer?.name || "";
    this.$nextTick(() => {
      if (!window.bootstrap || !this.$el) return;
      this.modalInstance = new bootstrap.Modal(this.$el, {
        backdrop: "static",
        keyboard: false,
      });
      this.onHidden = () => {
        this.$emit("close-time");
      };
      this.$el.addEventListener("hidden.bs.modal", this.onHidden);
      this.modalInstance.show();
      setTimeout(async () => {
        await this.ensureValidDateForShowroom();
        this.initTimeModalFlatpickr();
      }, 200);
    });
  },
  beforeDestroy() {
    if (this.$el && this.onHidden) {
      this.$el.removeEventListener("hidden.bs.modal", this.onHidden);
    }
    this.blurModalFocus();
    if (this.modalInstance) {
      try {
        this.modalInstance.dispose();
      } catch (e) {
        /* ignore teardown races */
      }
      this.modalInstance = null;
    }
    this.destroyTimeModalFlatpickr();
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
    getHolidayRegionByShowroom() {
      const searchableText = `${this.showroom.title || ""} ${this.showroom.address || ""}`.toUpperCase();
      if (searchableText.includes("SYDNEY")) return "SYDNEY";
      if (searchableText.includes("MELBOURNE")) return "MELBOURNE";
      if (searchableText.includes("BRISBANE")) return "BRISBANE";
      return "";
    },
    getShowroomIdForRules() {
      return String(this.showroom?.showroom_id ?? "");
    },
    getHolidayDatesForShowroomId(showroomId) {
      const bookingHolidays = window.HOLIDAYS_2026 || {};
      if (String(showroomId) === "1") return bookingHolidays.SYDNEY || [];
      if (String(showroomId) === "2") return bookingHolidays.MELBOURNE || [];
      if (String(showroomId) === "3") return bookingHolidays.BRISBANE || [];
      return [];
    },
    getCalendarDisableRules() {
      const showroomId = this.getShowroomIdForRules();
      let holidayDates = this.getHolidayDatesForShowroomId(showroomId);
      if (!holidayDates.length) {
        const holidayRegion = this.getHolidayRegionByShowroom();
        holidayDates =
          holidayRegion && Array.isArray(this.holidays[holidayRegion])
            ? this.holidays[holidayRegion]
            : [];
      }
      const holidayDateSet = new Set(holidayDates);

      return [
        (date) => {
          const day = date.getDay();
          if (day === 0 || day === 6) {
            return true;
          }
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, "0");
          const dayOfMonth = String(date.getDate()).padStart(2, "0");
          const isoDate = `${year}-${month}-${dayOfMonth}`;
          return holidayDateSet.has(isoDate);
        },
      ];
    },
    parseLocalIsoDate(iso) {
      const parts = String(iso || "").split("-");
      if (parts.length !== 3) return new Date();
      const y = parseInt(parts[0], 10);
      const m = parseInt(parts[1], 10) - 1;
      const d = parseInt(parts[2], 10);
      return new Date(y, m, d);
    },
    formatLocalDateIso(date) {
      const y = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${y}-${month}-${day}`;
    },
    isBlockedBookingDate(dateObj, dateIso) {
      const dayOfWeek = dateObj.getDay();
      if (dayOfWeek === 0 || dayOfWeek === 6) {
        return true;
      }
      const showroomId = this.getShowroomIdForRules();
      let holidayDates = this.getHolidayDatesForShowroomId(showroomId);
      if (!holidayDates.length) {
        const holidayRegion = this.getHolidayRegionByShowroom();
        holidayDates =
          holidayRegion && Array.isArray(this.holidays[holidayRegion])
            ? this.holidays[holidayRegion]
            : [];
      }
      return holidayDates.includes(dateIso);
    },
    async ensureValidDateForShowroom() {
      const baseIso = this.localSelectedDate || this.minSelectableDate;
      let d = this.parseLocalIsoDate(baseIso);
      let guard = 0;
      while (
        guard++ < 400 &&
        this.isBlockedBookingDate(d, this.formatLocalDateIso(d))
      ) {
        d.setDate(d.getDate() + 1);
      }
      const nextIso = this.formatLocalDateIso(d);
      if (nextIso !== this.localSelectedDate) {
        await this.changeDate(nextIso);
      }
    },
    destroyTimeModalFlatpickr() {
      const el = this.$refs.bookingTimeDateInput;
      if (el && el._flatpickr) {
        try {
          el._flatpickr.destroy();
        } catch (e) {
          /* ignore */
        }
      }
      this._timeModalFp = null;
    },
    syncTimeModalFlatpickrRules() {
      if (!this._timeModalFp || typeof this._timeModalFp.set !== "function") {
        return;
      }
      const disableRules = this.getCalendarDisableRules();
      this._timeModalFp.set("disable", disableRules);
      if (typeof this._timeModalFp.redraw === "function") {
        this._timeModalFp.redraw();
      }
    },
    initTimeModalFlatpickr() {
      if (typeof window.jQuery === "undefined") return;
      const $input = window.jQuery(this.$refs.bookingTimeDateInput);
      if (!$input.length) return;

      if ($input[0]._flatpickr) {
        try {
          $input[0]._flatpickr.destroy();
        } catch (e) {
          /* ignore */
        }
      }

      const self = this;
      const disableRules = this.getCalendarDisableRules();
      const initial = this.localSelectedDate || this.minSelectableDate;

      this._timeModalFp = $input.flatpickr({
        dateFormat: "Y-m-d",
        minDate: "today",
        disable: disableRules,
        defaultDate: initial,
        allowInput: false,
        clickOpens: true,
        onChange(_selectedDates, dateStr) {
          if (dateStr) {
            self.changeDate(dateStr);
          }
        },
      });
    },
    validateBookingEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    async parseApiResponse(response) {
      const contentType = response?.headers?.get("content-type") || "";
      const isJson = contentType.toLowerCase().includes("application/json");
      const fallbackMessage = response?.ok
        ? "Request failed"
        : `Request failed (${response?.status || "unknown"})`;

      if (isJson) {
        try {
          const data = await response.json();
          return data && typeof data === "object"
            ? data
            : { success: response.ok, message: fallbackMessage };
        } catch (e) {
          return { success: false, message: fallbackMessage };
        }
      }

      try {
        const text = (await response.text()) || "";
        if (text.trim().startsWith("<")) {
          return {
            success: false,
            message:
              "Server returned an unexpected error page. Please try again.",
          };
        }
        return { success: response.ok, message: text.trim() || fallbackMessage };
      } catch (e) {
        return { success: false, message: fallbackMessage };
      }
    },
    async postBookingJson(url, payload) {
      const res = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      return this.parseApiResponse(res);
    },
    buildMeetingDates(date, meetingTime, duration) {
      const start = new Date(`${date}T${meetingTime}`);
      const end = new Date(start.getTime() + duration * 60000);
      return { start, end };
    },
    makeMeetingLink({ start, end, title, location }) {
      const format = (d) =>
        d.toISOString().replace(/[-:]/g, "").split(".")[0] + "Z";
      const startStr = format(start);
      const endStr = format(end);
      const add =
        "c_18895tnlfoecignhmo94g3jqjc402@resource.calendar.google.com";
      return `https://calendar.google.com/calendar/u/0/r/eventedit?text=${encodeURIComponent(title)}&dates=${startStr}/${endStr}&location=${encodeURIComponent(location || "")}&add=${encodeURIComponent(add)}`;
    },
    async updateBookingTimeZone(showroom = {}) {
      // console.log("showroom:", showroom);
      const showroomId = showroom?.showroom_id;
      this.bookingTimeZone = await this.getBookingTimeZone(showroomId);
    },
    async getBookingTimeZone(showroomId) {
      const fallbackTimeZone = "";
      if (!showroomId) {
        return fallbackTimeZone;
      }
      try {
        // const response = await fetch("/api/visit-showroom/timezone?at=2025-04-05");
        const response = await fetch("/api/visit-showroom/timezone");
        const timezonesData = await response.json();
        const match = (Array.isArray(timezonesData) ? timezonesData : []).find(
          (timezone) => Number(timezone?.showroom_id) === Number(showroomId),
        );
        // console.log("match:", match);
        return match?.label || fallbackTimeZone;
      } catch (e) {
        return fallbackTimeZone;
      }
    },
    closeModal() {
      this.blurModalFocus();
      if (this.modalInstance) {
        this.modalInstance.hide();
        return;
      }
      this.$emit("close-time");
    },
    goBackToCalendar() {
      this.$emit("back-to-calendar", this.localSelectedDate);
    },
    clearMessage() {
      this.message = "";
    },
    async changeDate(date) {
      this.localSelectedDate = date || this.minSelectableDate;
      this.selectedSlot = null;
      this.selectedSlotValue = "";
      this.checkedSlot = null;
      await this.fetchBookedDataForDate();
    },
    async fetchBookedDataForDate() {
      if (!this.localSelectedDate) return;
      try {
        await this.$store.dispatch("getBookedData", [
          this.localSelectedDate,
          this.tourType || 'physicalTour',
        ]);
      } catch (e) {
        // ignore, fallback to empty
      }
      const bookedTimes = (this.$store.getters.bookedData || [])
        .map((row) =>
          row && row.meeting_time ? String(row.meeting_time).trim() : "",
        )
        .filter(Boolean);
      this.markBookedTimeSlots(bookedTimes);
    },
    markBookedTimeSlots(bookedTimes) {
      // console.log("Booked bookedTimes:", bookedTimes);

      const slots = document.querySelectorAll(".th-time-slot");

      // STEP 1: Reset সব slot (IMPORTANT)
      slots.forEach((slot) => {
        const checkbox = slot.querySelector('input[type="checkbox"]');
        const icon = slot.querySelector("i");

        slot.classList.remove("active", "disabled", "time-slot-disabled", "th-booked-time-slot");
        slot.removeAttribute("disabled");
        slot.classList.add("no-hover");

        if (checkbox) {
          checkbox.disabled = false;
          checkbox.classList.remove("d-none", "time-slot-disabled");
        }

        if (icon) {
          icon.classList.add("d-none");
        }
      });

      // STEP 2: booked list prepare
      const bookedSet = new Set(
        (Array.isArray(bookedTimes) ? bookedTimes : [])
          .map((time) => String(time || "").trim())
          .filter(Boolean),
      );

      // যদি empty হয়, এখানেই stop (reset already done)
      if (bookedSet.size === 0) return;

      // STEP 3: apply booked state
      slots.forEach((slot) => {
        const checkbox = slot.querySelector('input[type="checkbox"]');
        const icon = slot.querySelector("i");

        if (!checkbox) return;

        const value = String(checkbox.value || "").trim();

        if (!bookedSet.has(value)) return;

        slot.classList.add("active", "disabled", "time-slot-disabled", "th-booked-time-slot");
        slot.setAttribute("disabled", "disabled");
        slot.classList.remove("no-hover");

        checkbox.checked = false;
        checkbox.disabled = true;
        checkbox.classList.add("d-none", "time-slot-disabled");

        if (icon) icon.classList.remove("d-none");
      });
    },
    isSlotBooked(slotValue) {
      return this.bookedSlotSet.has(String(slotValue || "").trim());
    },
    toggleSlot_old(event, slotId, slotValue) {
      event.preventDefault();
      const slot = event.currentTarget.closest(".th-time-slot");
      if (
        !slot ||
        slot.classList.contains("disabled") ||
        slot.classList.contains("time-slot-disabled")
      ) {
        return;
      }

      document
        .querySelectorAll(
          ".th-time-slot:not(.disabled):not(.time-slot-disabled)",
        )
        .forEach((el) => {
          el.classList.remove("active", "selected");
        });

      slot.classList.add("selected", "active");

      this.selectedSlot = slotId;
      this.checkedSlot = slotId;
      this.selectedSlotValue = slotValue;
      this.clearMessage();
    },
    toggleSlot(slotId, slotValue, event) {
      if (this.isSlotBooked(slotValue)) return;
      const slot = event.currentTarget.closest(".th-time-slot");
      if (
        !slot ||
        slot.classList.contains("disabled") ||
        slot.classList.contains("time-slot-disabled")
      ) {
        return;
      }

      this.selectedSlot = slotId;
      this.selectedSlotValue = slotValue;

      this.clearMessage();
    },
    nearestShowroomRecord() {
      return this.showroom;
    },
    buildShowroomBookingPayload(customerIdFromAuth) {
      const ns = this.nearestShowroomRecord();
      const duration = 60;
      const { start, end } = this.buildMeetingDates(
        this.localSelectedDate,
        this.selectedSlotValue,
        duration,
      );
      const meetingLink = this.makeMeetingLink({
        start,
        end,
        title: "Meeting",
        location: ns.address || "",
      });
      const cid =
        customerIdFromAuth !== undefined && customerIdFromAuth !== ""
          ? customerIdFromAuth
          : "";

      return {
        showroom_contact_id: ns.showroom_contact_id ?? 1,
        customer_id: cid,
        customer_name: this.bookingName,
        email: this.bookingEmail,
        enquiry_type: this.bookingEnquiryType,
        label: this.tourType === "physicalTour" ? "Showroom Tour" : "Virtual Meeting",
        showroom_id: ns.showroom_id ?? 1,
        tour_type: this.tourType || "physicalTour",
        date: this.localSelectedDate,
        meeting_time: this.selectedSlotValue,
        duration,
        time_zone: this.bookingTimeZone,
        location: ns.address || "",
        meeting_link: meetingLink,
        pinboard_id: this.pinboardId,
      };
    },
    redirectToBookingConfirmation(type = "showroom-visit", uuid = "") {
      if (!this.pinboardUuid) return;
      localStorage.setItem(
        "pinboard_processed",
        JSON.stringify({
          // pinboard_id: this.pinboardId,
          pinboard_uuid: this.pinboardUuid,
          processed_method: type,
          uuid: uuid,
        }),
      );

      if (uuid) {
        if (this.page === 'manage_pinboard' || this.page === 'virtual_pinboard') {
          // manage pinboard page and virtual pinboard page
          const tourType = this.tourType || "physicalTour";
          if (tourType === "physicalTour") {
            window.location.href = `/pinboards/book-showroom-visit/${uuid}`;
          } else {
            window.location.href = `/pinboards/virtual-meeting/${uuid}`;
          }
        } else {
          // contact sales page
          const tourType = this.tourType || "physicalTour";
          if (tourType === "physicalTour") {
            window.location.href = `/contact-us/book-physical-showroom-visit/${uuid}`;
          } else {
            window.location.href = `/contact-us/virtual-meeting-booking/${uuid}`;
          }
        }
      } else {
        const url = `/pinboards/${this.pinboardUuid}/booking/${type}`;
        window.location.href = url;
      }
    },
    async attachRecaptchaToBookingData(bookingData) {
      if (!this.recaptchaEnabled) {
        return bookingData;
      }

      try {
        return await attachBookingRecaptcha(bookingData);
      } catch (recaptchaErr) {
        console.error("reCAPTCHA execute failed", recaptchaErr);
        throw new Error(
          "reCAPTCHA verification failed. Please refresh the page and try again.",
        );
      }
    },
    async submitShowroomBooking(bookingData) {
      this.recaptchaError = "";
      const tourType = this.tourType || "physicalTour";
      const type = tourType === "virtualMeeting" ? "virtual-meeting" : "showroom-visit";
      let payload = bookingData;
      payload.source = this.source;
      try {
        payload = await this.attachRecaptchaToBookingData(bookingData);
      } catch (recaptchaErr) {
        this.recaptchaError = recaptchaErr?.message || "reCAPTCHA verification failed.";
        return;
      }

      const response = await this.$store.dispatch("bookNow", payload);
      const uuid = response?.data?.uuid;
      if (response && response.success) {
        this.clearMessage();
        this.redirectToBookingConfirmation(type, uuid);
        this.$emit("booking-success");
        return;
      }
      const errMsg =
        response?.message ||
        this.$store.getters.fb?.errors?.bookNow ||
        "Booking failed";
      if (String(errMsg).toLowerCase().includes("recaptcha")) {
        this.recaptchaError = errMsg;
        return;
      }
      this.message = errMsg;
    },
    online24hValidation_bangladesh_timezone() {
      if (!this.localSelectedDate || !this.selectedSlotValue) {
        return false;
      }

      // Create booking datetime
      const bookingDateTime = new Date(
        `${this.localSelectedDate}T${this.selectedSlotValue}`
      );

      const now = new Date();

      // Difference in milliseconds
      const diffMs = bookingDateTime.getTime() - now.getTime();

      // 24 hours = 24 * 60 * 60 * 1000
      return diffMs >= 24 * 60 * 60 * 1000;
    },
    online24hValidation() { // Australia/Sydney
      if (!this.localSelectedDate || !this.selectedSlotValue) {
        return false;
      }
    
      // Australia/Sydney current time
      const nowSydney = new Date(
        new Date().toLocaleString("en-US", {
          timeZone: "Australia/Sydney",
        })
      );
    
      const bookingDateTime = new Date(
        `${this.localSelectedDate}T${this.selectedSlotValue}`
      );
    
      const diffMs = bookingDateTime.getTime() - nowSydney.getTime();
    
      return diffMs >= 24 * 60 * 60 * 1000;
    },
    async handleBookingClick() {
      this.clearMessage();
      this.recaptchaError = "";
      if (!this.bookingName || !String(this.bookingName).trim()) {
        this.message = "Please enter your name";
        return;
      }
      if (!this.bookingEmail || !String(this.bookingEmail).trim()) {
        this.message = "Please enter your email";
        return;
      }
      if (!this.validateBookingEmail(this.bookingEmail)) {
        this.message = "Please enter a valid email";
        return;
      }
      // validation enquiry type
      // console.log('tour type: - ', this.tourType);
      if (
          this.tourType === "physicalTour" &&
          !String(this.bookingEnquiryType || "").trim()
      ) {
          this.message = "Please select an enquiry type";
          return;
      }
      if (!this.selectedSlotValue) {
        this.message = "Please select a time slot";
        return;
      }

      // 24-hour validation
      if (this.tourType != "physicalTour" && !this.online24hValidation()) {
        this.message =
          "Bookings must be made at least 24 hours before the selected time.";
        return;
      }

      let userAuthDetails = {};
      try {
        userAuthDetails = JSON.parse(
          localStorage.getItem("userAuthDetails") || "{}",
        );
      } catch (e) {
        userAuthDetails = {};
      }

      this.isSubmitting = true;
      try {
        const customerId = userAuthDetails.customer_id || "";
        const bookingData = this.buildShowroomBookingPayload(customerId);
        const checkExistingBooking = await this.postBookingJson(
          "/api/check-existing-booking",
          bookingData,
        );
        if (!checkExistingBooking || !checkExistingBooking.success) {
          this.message =
            checkExistingBooking?.message || "Booking check failed";
          return;
        }

        if (this.enableEmailVerification) {
          const verifySendResponse = await this.postBookingJson(
            "/api/send-email-verification",
            {
              email: this.bookingEmail,
              customer_name: this.bookingName,
              subject: "Booking Verification Code with Krost",
            },
          );
          if (!verifySendResponse || !verifySendResponse.success) {
            this.message =
              verifySendResponse?.message || "Unable to send verification code";
            return;
          }
          this.$emit("request-email-verification", {
            email: this.bookingEmail,
            customerName: this.bookingName,
            bookingData,
            customer: verifySendResponse?.customer || {},
          });
          return;
        }

        await this.submitShowroomBooking(bookingData);
      } catch (error) {
        this.message = error?.message || "Booking failed";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  template: /* html */ `
        <div
            class="modal fade backdrop-static"
            id="timeSlotsModal"
            tabindex="-1"
            aria-labelledby="timeSlotsModalLabel"
            data-bs-backdrop="false"
            style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1060;"
            aria-modal="true"
            role="dialog"
        >
            <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
                <div class="modal-content px-80 py-60">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeSlotsModalLabel">
                            Booking for {{ this.tourType === 'physicalTour' ? 'Showroom Tour' : 'Virtual Meeting' }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="Close"
                            @click="closeModal"
                        ></button>
                    </div>

                    <div class="modal-body">
                        <div id="booking-form-container">
                            <div class="gap-10">
                                <div class="th-form-row">
                                    <label for="ts-time-modal-date">Date</label>
                                    <div class="th-field">
                                        <div class="th-input-group d-flex align-items-center">
                                            <i class="fa-solid fa-calendar th-input-icon"></i>
                                            <input
                                                ref="bookingTimeDateInput"
                                                type="text"
                                                id="ts-time-modal-date"
                                                class="form-control th-date-input"
                                                placeholder="Select date"
                                                readonly
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="th-form-row th-timezone-selector">
                                    <label for="choose-timezone">Time Zone</label>
                                    <div class="th-field">
                                        <div class="th-input-group d-flex align-items-center">
                                            <i class="fa-solid fa-globe th-input-icon"></i>
                                            <input
                                                id="choose-timezone"
                                                class="form-control th-choices-select"
                                                :value="bookingTimeZone"
                                                disabled
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="th-form-row">
                                    <label for="ts-name">Name</label>
                                    <div class="th-field">
                                        <div class="th-input-group d-flex align-items-center">
                                            <i class="fa-solid fa-user th-input-icon"></i>
                                            <input
                                                type="text"
                                                id="ts-name"
                                                class="form-control"
                                                placeholder="Enter your name"
                                                style="border: none !important;"
                                                v-model="bookingName"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="th-form-row">
                                    <label for="ts-email-not-logged-in-email">Email</label>
                                    <div class="th-field">
                                        <div class="th-input-group d-flex align-items-center">
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
                                <div class="th-form-row">
                                    <label for="ts-booking-enquiry-type">Enquiry type</label>
                                    <div class="th-field">
                                        <div class="th-input-group d-flex align-items-center">
                                            <i class="fa-solid fa-clipboard-list th-input-icon"></i>
                                            <select
                                                id="ts-booking-enquiry-type"
                                                class="form-control"
                                                style="border: none !important;"
                                                v-model="bookingEnquiryType"
                                            >
                                                <option value="">Select enquiry type</option>
                                                <option value="Office Upgrades & Refurbishments">Office Upgrades & Refurbishments</option>
                                                <option value="Full Office Fitout & Space Planning">Full Office Fitout & Space Planning</option>
                                                <option value="Multi-Level Commercial Projects">Multi-Level Commercial Projects</option>
                                                <option value="General Browsing & Inspiration">General Browsing & Inspiration</option>
                                                <option value="Home Office">Home Office</option>
                                            </select>
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

                            <div class="ts-slots-blocks th-pt-20">
                              <div class="ts-slots-block ts-slots-morning">
                                <div class="ts-slots-grid d-flex flex-wrap gap-2">
                                  
                                  <div
                                    v-for="slot in morningSlots"
                                    :key="slot.id"
                                    class="th-time-slot"
                                    :class="{
                                      active: selectedSlot === slot.id,
                                      selected: selectedSlot === slot.id,
                                      disabled: isSlotBooked(slot.value),
                                      'time-slot-disabled': isSlotBooked(slot.value),
                                      'no-hover': selectedSlot !== slot.id,
                                      'th-booked-time-slot': isSlotBooked(slot.value)
                                    }"
                                    @click="toggleSlot(slot.id, slot.value, $event)"
                                  >
                                    <input type="checkbox" class="d-none" :id="slot.id" :value="slot.value" />
                                    <label :for="slot.id">{{ slot.label }}</label>
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
                                      active: selectedSlot === slot.id,
                                      selected: selectedSlot === slot.id,
                                      disabled: isSlotBooked(slot.value),
                                      'time-slot-disabled': isSlotBooked(slot.value),
                                      'no-hover': selectedSlot !== slot.id,
                                      'th-booked-time-slot': isSlotBooked(slot.value)
                                    }"
                                    @click="toggleSlot(slot.id, slot.value, $event)"
                                  >
                                    <input type="checkbox" class="d-none" :id="slot.id" :value="slot.value" />
                                    <label :for="slot.id">{{ slot.label }}</label>
                                  </div>

                                </div>
                              </div>
                            </div>

                            <div class="pt-60 d-flex gap-2">
                                <button type="button" class="th-btn-gray text-capitalize w-100" @click.prevent="closeModal">
                                    Back
                                </button>
                                <button type="button" id="th-book-time-btn" class="th-btn-primary text-capitalize w-100"
                                    :disabled="isSubmitting" @click.prevent="handleBookingClick">
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
                        <p v-if="recaptchaError" class="text-danger pt-20 mb-0 text-center" role="alert" aria-live="polite">{{ recaptchaError }}</p>
                        <div v-if="message" class="col-md-12 pt-20 text-center">
                            <div class="text-danger" role="alert" aria-live="polite">{{ message }}</div>
                        </div>
                        <p v-if="recaptchaEnabled" class="small text-muted pt-20 mb-0 text-center">
                            This site is protected by reCAPTCHA and the Google
                            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                            and
                            <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">Terms of Service</a>
                            apply.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `,
};
