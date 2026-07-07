export default {
    name: 'BookingCalendarModal',
    props: {
        pinboardTitle: {
            type: String,
            default: '',
        },
        pinboardId: {
            type: [Number, String],
            default: null,
        },
        nearestShowroom: {
            type: Object,
            default: () => ({}),
        },
        tourType: {
            type: String,
            default: 'physicalTour',
        },
        showrooms: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            modalInstance: null,
            onHidden: null,
            onFlatpickrDayClick: null,
            flatpickrDaysContainers: [],
            selectedDate: '',
            openingTimeSlots: false,
            localTourType: this.tourType || 'physicalTour',
            selectedLocationId: '',
            choicesInstances: {},
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
            selectedShowroom: {
                showroom_id: 1,
                image: '/img/logo_black.png',
                title: 'Krost Showroom',
                address: 'Our consultant will share your visit details.',
            },
        };
    },
    computed: {
        
        showroom() {
            const source =
                this.nearestShowroom &&
                !Array.isArray(this.nearestShowroom) &&
                Object.keys(this.nearestShowroom).length
                    ? this.nearestShowroom
                    : {};
            return {
                showroom_id: source.showroom_id || source.showrooms_id || source.id || '',
                image: source.image || '/img/logo_black.png',
                title: source.title || 'Krost Showroom',
                address: source.address || 'Our consultant will share your visit details.',
            };
        },
        minSelectableDate() {
            return new Date().toISOString().slice(0, 10);
        },
        selectedTourTypeLabel() {
            return this.localTourType === 'virtualMeeting' || this.localTourType === 'virtualTour'
                ? 'Virtual Meeting'
                : 'Physical Tour';
        },
    },
    watch: {
        tourType: {
            immediate: true,
            handler(value) {
                this.localTourType = value || 'physicalTour';
            },
        },
        nearestShowroom: {
            immediate: true,
            deep: true,
            handler() {
                this.applyNearestShowroom();
                this.$nextTick(() => {
                    this.initChoicesDropdowns();
                });
            },
        },
        showrooms: {
            deep: true,
            handler() {
                this.$nextTick(() => {
                    this.initChoicesDropdowns();
                });
            },
        }
    },
    mounted() {
        this.selectedDate = this.minSelectableDate;
        this.$nextTick(() => {
            if (!window.bootstrap || !this.$el) return;
            this.modalInstance = new bootstrap.Modal(this.$el, {
                backdrop: 'static',
                keyboard: false,
            });
            this.onHidden = () => {
                this.$emit('close-booking');
            };
            this.$el.addEventListener('hidden.bs.modal', this.onHidden);
            this.modalInstance.show();
            this.initFlatpickrCalendar();
            this.initChoicesDropdowns();
        });
    },
    beforeDestroy() {
        this.blurModalFocus();
        if (this.$el && this.onHidden) {
            this.$el.removeEventListener('hidden.bs.modal', this.onHidden);
        }
        if (this.flatpickrDaysContainers.length && this.onFlatpickrDayClick) {
            this.flatpickrDaysContainers.forEach((container) => {
                container.removeEventListener('click', this.onFlatpickrDayClick);
            });
        }
        this.flatpickrDaysContainers = [];
        this.onFlatpickrDayClick = null;
        this.destroyChoicesDropdowns();
        if (this.modalInstance) {
            try {
                this.modalInstance.dispose();
            } catch (e) {
                /* instance may be mid-teardown */
            }
            this.modalInstance = null;
        }
    },
    methods: {
        getHolidayRegionByShowroom() {
            const searchableText = `${this.selectedShowroom.title || ''} ${this.selectedShowroom.address || ''}`.toUpperCase();
            if (searchableText.includes('SYDNEY')) return 'SYDNEY';
            if (searchableText.includes('MELBOURNE')) return 'MELBOURNE';
            if (searchableText.includes('BRISBANE')) return 'BRISBANE';
            return '';
        },
        getCalendarDisableRules() {
            const showroomId = this.getShowroomIdForRules();
            let holidayDates = this.getHolidayDatesForShowroomId(showroomId);
            if (!holidayDates.length) {
                const holidayRegion = this.getHolidayRegionByShowroom();
                holidayDates = holidayRegion && Array.isArray(this.holidays[holidayRegion])
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
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const dayOfMonth = String(date.getDate()).padStart(2, '0');
                    const isoDate = `${year}-${month}-${dayOfMonth}`;
                    return holidayDateSet.has(isoDate);
                },
            ];
        },
        applyCalendarDisabledDates() {
            const disableRules = this.getCalendarDisableRules();
            window.bookingCalendarDisableDates = disableRules;
            if (typeof window.updateBookingFlatpickrDisabledDates === 'function') {
                window.updateBookingFlatpickrDisabledDates(disableRules);
            }
        },
        destroyChoicesDropdowns() {
            Object.keys(this.choicesInstances).forEach((key) => {
                const instance = this.choicesInstances[key];
                if (instance && typeof instance.destroy === 'function') {
                    try {
                        instance.destroy();
                    } catch (e) {
                        /* ignore teardown errors */
                    }
                }
            });
            this.choicesInstances = {};
        },
        initChoicesDropdowns() {
            if (typeof window.Choices !== 'function') return;

            this.destroyChoicesDropdowns();

            const selectEl = this.$refs.showroomSelect;
            if (!selectEl) return;

            this.choicesInstances.showroom = new window.Choices(selectEl, {
                allowHTML: true,
            });

            if (this.selectedLocationId) {
                try {
                    this.choicesInstances.showroom.setChoiceByValue(
                        String(this.selectedLocationId),
                    );
                } catch (e) {
                    /* ignore: option may not yet be in DOM */
                }
            }
        },
        applyNearestShowroom() {
            const source =
                this.nearestShowroom &&
                !Array.isArray(this.nearestShowroom) &&
                Object.keys(this.nearestShowroom).length
                    ? this.nearestShowroom
                    : null;
            if (!source) return;

            const showroomId = String(
                source.showroom_id || source.showrooms_id || source.id || '',
            );
            if (!showroomId) return;

            this.selectedLocationId = showroomId;
            this.selectedShowroom = {
                showroom_id: showroomId,
                image: source.image || '/img/logo_black.png',
                title: source.title || 'Krost Showroom',
                address: source.address || 'Our consultant will share your visit details.',
            };
        },
        syncSelectedShowroom(event) {
            const id = String(event.target.value || '');
            if (!id) return;
            const match = (this.showrooms || []).find(
                (item) => String(item.showroom_id || item.showrooms_id || item.id || '') === id,
            );
            if (!match) return;
            this.selectedShowroom = {
                showroom_id: id,
                image: match.image || '/img/logo_black.png',
                title: match.title || 'Krost Showroom',
                address: match.address || 'Our consultant will share your visit details.',
            };
        },
        updateTourType(value) {
            this.localTourType = value || 'physicalTour';
            this.$emit('update-tour-type', this.localTourType);
        },
        syncSelectedShowroomToStore() {
            if (!this.$store || typeof this.$store.commit !== 'function') return;
            const selected = this.selectedShowroom || {};
            if (!selected.showroom_id) return;
            this.$store.commit('SET_NEAREST_SHOWROOM', selected);
        },
        blurModalFocus() {
            const ae = document.activeElement;
            if (ae && this.$el && typeof this.$el.contains === 'function' && this.$el.contains(ae)) {
                ae.blur();
            }
        },
        initFlatpickrCalendar() {
            setTimeout(() => {
                this.applyCalendarDisabledDates();
                this.applyCalendarRules();
                if (typeof window.initFlatpickr === 'function') {
                    window.initFlatpickr();
                }
                if (typeof window.initTimezoneChoices === 'function') {
                    window.initTimezoneChoices();
                }
                this.bindFlatpickrDayClick();
            }, 200);
        },
        getShowroomIdForRules() {
            return String(this.selectedShowroom?.showroom_id || '');
        },
        getHolidayDatesForShowroomId(showroomId) {
            const bookingHolidays = window.HOLIDAYS_2026 || {};
            if (String(showroomId) === '1') return bookingHolidays.SYDNEY || [];
            if (String(showroomId) === '2') return bookingHolidays.MELBOURNE || [];
            if (String(showroomId) === '3') return bookingHolidays.BRISBANE || [];
            return [];
        },
        isBlockedBookingDate(dateObj, dateIso) {
            const dayOfWeek = dateObj.getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                return true;
            }
            const holidayDates = this.getHolidayDatesForShowroomId(this.getShowroomIdForRules());
            return holidayDates.includes(dateIso);
        },
        applyCalendarRules() {
            if (typeof window.setBookingCalendarRules !== 'function') return;
            const showroomId = this.getShowroomIdForRules();
            window.setBookingCalendarRules({
                showroomId,
                publicHolidays: this.getHolidayDatesForShowroomId(showroomId),
            });
        },
        getLocalTodayIsoDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        bindFlatpickrDayClick(attempt = 0) {
            if (this.flatpickrDaysContainers.length && this.onFlatpickrDayClick) {
                this.flatpickrDaysContainers.forEach((container) => {
                    container.removeEventListener('click', this.onFlatpickrDayClick);
                });
            }
            this.flatpickrDaysContainers = Array.from(document.querySelectorAll('.flatpickr-days'));
            if (!this.flatpickrDaysContainers.length) {
                if (attempt < 10) {
                    setTimeout(() => this.bindFlatpickrDayClick(attempt + 1), 120);
                }
                return;
            }

            this.onFlatpickrDayClick = async (e) => {
                const day = e.target.closest('.flatpickr-day');
                if (
                    this.openingTimeSlots ||
                    !day ||
                    day.classList.contains('disabled') ||
                    day.classList.contains('flatpickr-disabled') ||
                    day.classList.contains('flatpickr-prev-month') ||
                    day.classList.contains('flatpickr-current-month') ||
                    day.classList.contains('flatpickr-next-month')
                ) {
                    return;
                }

                const selectedDate = day.getAttribute('aria-label');
                if (!selectedDate) return;

                const parsedDate = new Date(selectedDate);
                if (Number.isNaN(parsedDate.getTime())) return;
                const formattedDate = parsedDate.toLocaleDateString('en-GB');
                const parts = formattedDate.split('/');
                if (parts.length !== 3) return;

                const selectedDateFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                if (selectedDateFormatted < this.getLocalTodayIsoDate()) {
                    return;
                }
                if (this.isBlockedBookingDate(parsedDate, selectedDateFormatted)) {
                    return;
                }
                this.selectedDate = selectedDateFormatted;

                const selectedDateElement = this.$el?.querySelector('#ts-selected-date');
                if (selectedDateElement) {
                    selectedDateElement.value = selectedDateFormatted;
                }

                this.openingTimeSlots = true;
                try {
                    this.syncSelectedShowroomToStore();
                    await this.$store.dispatch('getBookedData', [
                        selectedDateFormatted,
                        this.localTourType,
                    ]);
                    // Do not call bootstrap Modal.hide() here: parent sets showBookingTimeModal and
                    // unmounts this component — hide() would race _hideModal with Vue teardown (null style error).
                    this.$emit('open-time-slots', selectedDateFormatted);
                } finally {
                    this.openingTimeSlots = false;
                }
            };

            this.flatpickrDaysContainers.forEach((container) => {
                container.addEventListener('click', this.onFlatpickrDayClick);
            });
        },
        closeModal() {
            this.blurModalFocus();
            if (this.modalInstance) {
                this.modalInstance.hide();
                return;
            }
            this.$emit('close-booking');
        },
        async handleDayClick() {
            if (!this.selectedDate || this.openingTimeSlots) return;
            this.openingTimeSlots = true;
            try {
                this.syncSelectedShowroomToStore();
                await this.$store.dispatch('getBookedData', [
                    this.selectedDate,
                    this.localTourType,
                ]);
                this.$emit('open-time-slots', this.selectedDate);
            } finally {
                this.openingTimeSlots = false;
            }
        },
        chooseTodayAndContinue() {
            this.selectedDate = this.minSelectableDate;
            this.handleDayClick();
        },
    },
    template: /* html */ `
        <div
            class="modal fade th-pinboard-modal backdrop-static"
            id="pinboardBookingCalendarModal"
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
                           <!-- <span>{{ pinboardTitle }}</span> -->
                            <button
                                type="button"
                                class="btn-close"
                                aria-label="Close"
                                @click="closeModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <form id="bookingModalForm" class="booking-modal-form">
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
                                                    <div class="th-booking-selected-member d-flex flex-column mb-20">
                                                        <div class="th-member-info p-0">
                                                            <h3 class="font-size-20" data-v-booknow-section_title="">
                                                                Meet With Our Consultant
                                                            </h3>
                                                        </div>
                                                        <div class="">
                                                            <img :src="selectedShowroom.image" alt="Member Avatar"
                                                                data-v-booknow-member_image
                                                                style="width: 270px; height: 215px; border-radius: 10px;" />
                                                        </div>
                                                    </div>
                                                    <h4 class="font-weight-600" id="showroomName">{{ selectedShowroom.title }}</h4>
                                                    <p class="font-weight-400 color-black">
                                                        <i class="fa-solid fa-map-pin th-pr-10"></i>
                                                        <span id="showroomAddress"> {{ selectedShowroom.address }}</span>
                                                    </p>

                                                    <!-- showroom dropdown -->
                                                    <div class="th-input-group flex-column pt-15 align-items-start" id="th-booking-choices">
                                                        <label for="choose-location" class="font-size-16">Location</label>
                                                        <select
                                                            class="form-control th-choices-select"
                                                            name="choose-location"
                                                            id="choose-location"
                                                            ref="showroomSelect"
                                                            v-model="selectedLocationId"
                                                            @change="syncSelectedShowroom"
                                                        >
                                                            <option
                                                                v-for="item in showrooms"
                                                                :key="item.showrooms_id"
                                                                :value="item.showrooms_id"
                                                                data-v-booknow-location
                                                                :data-address="item.address"
                                                                :data-image="item.image"
                                                            >
                                                                {{ item.title }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="d-flex th-booking-tour-option my-15">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="hidden" name="tour_type" :value="localTourType">
                                                            <span>{{ selectedTourTypeLabel }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="th-booking-description"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 th-booking-calendar-section">
                                            <h3 class="mt-50 font-weight-600">Select a Date & Time</h3>
                                            <div class="booking-calendar-wrapper p-0 d-flex flex-column gap-3">
                                                <div class="th-booking-calendar">
                                                    <input
                                                        id="ts-selected-date"
                                                        class="d-none"
                                                        type="text"
                                                        placeholder="Select Date.."
                                                        data-input
                                                        :value="selectedDate"
                                                    />
                                                </div>
                                               <!--<button
                                                    type="button"
                                                    class="th-btn-primary text-capitalize w-100"
                                                    @click.prevent="handleDayClick"
                                                >
                                                    Continue to Time Slots
                                                </button>
                                                -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="py-30">
                                    <button
                                        type="button"
                                        class="th-btn-gray text-capitalize w-100"
                                        @click.prevent="chooseTodayAndContinue"
                                    >
                                        Quick Continue (Today)
                                    </button>
                                </div>-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};