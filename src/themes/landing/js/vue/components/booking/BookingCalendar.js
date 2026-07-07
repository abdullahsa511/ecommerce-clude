export default {
    name: 'booking-calendar',
    props: {
        nearestShowroom: {
            type: Object,
            default: () => ({}),
        },
        showroomsData: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            selectedLocationId: '',
            selectedHostId: '',
            tourType: 'physicalTour',
            selectedDate: '',
            onFlatpickrDayClick: null,
            flatpickrDaysContainers: [],
            choicesInstances: {},
            holidays: {
                SYDNEY: [
                    '2026-01-01', '2026-01-26', '2026-04-03', '2026-04-04', '2026-04-05',
                    '2026-04-06', '2026-04-25', '2026-04-27', '2026-06-08', '2026-10-05',
                    '2026-12-25', '2026-12-26', '2026-12-28'
                ],
                MELBOURNE: [
                    '2026-01-01', '2026-01-26', '2026-03-09', '2026-04-03', '2026-04-04',
                    '2026-04-05', '2026-04-06', '2026-04-25', '2026-06-08', '2026-11-03',
                    '2026-12-25', '2026-12-26', '2026-12-28'
                ],
                BRISBANE: [
                    '2026-01-01', '2026-01-26', '2026-04-03', '2026-04-04', '2026-04-05',
                    '2026-04-06', '2026-04-25', '2026-05-04', '2026-08-12', '2026-10-05',
                    '2026-12-24', '2026-12-25', '2026-12-26', '2026-12-28'
                ]
            }
        };
    },
    computed: {
        isNearestShowroomLoading() {
            return Boolean(this.$store?.getters?.nearestShowroomLoading);
        },
        hasHydratedShowroomData() {
            const hasNearest = Boolean(
                this.showroom?.showroom_id ||
                this.showroom?.showrooms_id,
            );
            const hasAllShowrooms =
                Array.isArray(this.showroomsData) &&
                this.showroomsData.length > 0;
            return hasNearest || hasAllShowrooms;
        },
        isSectionLoading() {
            return this.isNearestShowroomLoading && !this.hasHydratedShowroomData;
        },
        showroomOptions() {
            if (Array.isArray(this.showroomsData) && this.showroomsData.length) {
                return this.showroomsData.map((item) => ({
                    showroom_id: item.showroom_id || item.showrooms_id || item.id || '',
                    showroom_contact_id: item.showroom_contact_id || '',
                    image: item.image || '/img/logo_black.png',
                    title: item.title || 'Krost Showroom',
                    group: item.group || '',
                    address: item.address || '',
                    map_link: item.map_link || item.mapLink || '',
                }));
            }
            return [this.showroom].filter((item) => item.showroom_id);
        },
        showroom() {
            const source =
                this.nearestShowroom &&
                !Array.isArray(this.nearestShowroom) &&
                Object.keys(this.nearestShowroom).length
                    ? this.nearestShowroom
                    : {};
            return {
                showroom_id: source.showroom_id || source.showrooms_id || source.id || '',
                showroom_contact_id: source.showroom_contact_id || '',
                image: source.image || '/img/logo_black.png',
                title: source.title || 'Krost Showroom',
                group: source.group || '',
                address: source.address || '',
                map_link: source.map_link || source.mapLink || '',
            };
        },
    },
    watch: {
        nearestShowroom: {
            immediate: true,
            handler() {
                this.selectedLocationId = this.showroom.showroom_id || '';
                this.selectedHostId = this.showroom.showroom_contact_id || '';
                this.$nextTick(() => {
                    this.initChoicesDropdowns();
                });
            },
        },
        showroomsData: {
            deep: true,
            handler() {
                this.$nextTick(() => {
                    this.initChoicesDropdowns();
                });
            },
        },
        selectedLocationId(value) {
            this.$emit('select-showroom', value);
            this.applyCalendarDisabledDates();
        },
        isSectionLoading(value) {
            if (!value) {
                this.$nextTick(() => {
                    this.initCalendar();
                    this.initChoicesDropdowns();
                    this.applyCalendarRules();
                });
            }
        },
    },
    mounted() {
        if (!this.isSectionLoading) {
            this.initCalendar();
            this.$nextTick(() => {
                this.initChoicesDropdowns();
                    this.applyCalendarRules();
            });
        }
    },
    beforeDestroy() {
        if (this.flatpickrDaysContainers.length && this.onFlatpickrDayClick) {
            this.flatpickrDaysContainers.forEach((container) => {
                container.removeEventListener('click', this.onFlatpickrDayClick);
            });
        }
        this.flatpickrDaysContainers = [];
        this.onFlatpickrDayClick = null;
        this.destroyChoicesDropdowns();
    },
    methods: {
        getHolidayRegionByShowroom() {
            const selectedShowroom = this.showroomOptions.find(
                (item) => String(item.showroom_id) === String(this.selectedLocationId),
            );
            if (!selectedShowroom) return '';

            const searchableText = `${selectedShowroom.title || ''} ${selectedShowroom.address || ''}`.toUpperCase();
            if (searchableText.includes('SYDNEY')) return 'SYDNEY';
            if (searchableText.includes('MELBOURNE')) return 'MELBOURNE';
            if (searchableText.includes('BRISBANE')) return 'BRISBANE';
            return '';
        },
        getCalendarDisableRules() {
            const holidayRegion = this.getHolidayRegionByShowroom();
            const holidayDates = holidayRegion && Array.isArray(this.holidays[holidayRegion])
                ? this.holidays[holidayRegion]
                : [];
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

            const selectRefs = [
                ['showroom', this.$refs.showroomSelect],
                ['tourType', this.$refs.tourTypeSelect],
            ];

            selectRefs.forEach(([key, selectEl]) => {
                if (!selectEl) return;
                this.choicesInstances[key] = new window.Choices(selectEl, {
                    allowHTML: true,
                });
            });
        },
        initCalendar() {
            this.applyCalendarDisabledDates();
            setTimeout(() => {
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
        normalizeShowroomGroup(rawGroup) {
            const normalized = String(rawGroup || '').trim().toUpperCase();
            if (!normalized) return '';
            if (normalized === 'NSW' || normalized === 'SYDNEY') return 'SYDNEY';
            if (normalized === 'VIC' || normalized === 'MELBOURNE') return 'MELBOURNE';
            if (normalized === 'QLD' || normalized === 'BRISBANE') return 'BRISBANE';
            return normalized;
        },
        mapShowroomIdToGroup(showroomId) {
            const normalizedId = String(showroomId || '').trim();
            if (normalizedId === '1') return 'SYDNEY';
            if (normalizedId === '2') return 'MELBOURNE';
            if (normalizedId === '3') return 'BRISBANE';
            return '';
        },
        detectShowroomGroup(showroom = {}) {
            const groupFromId = this.mapShowroomIdToGroup(
                showroom.showroom_id || showroom.showrooms_id || showroom.id || this.selectedLocationId,
            );
            if (groupFromId) return groupFromId;

            const groupFromApi = this.normalizeShowroomGroup(showroom.group);
            if (groupFromApi) return groupFromApi;

            const probeText = `${showroom.title || ''} ${showroom.address || ''}`.toUpperCase();
            if (probeText.includes('SYDNEY') || probeText.includes('NSW')) return 'SYDNEY';
            if (probeText.includes('MELBOURNE') || probeText.includes('VIC')) return 'MELBOURNE';
            if (probeText.includes('BRISBANE') || probeText.includes('QLD')) return 'BRISBANE';
            return 'SYDNEY';
        },
        getSelectedShowroomRecord() {
            const selectedId = String(this.selectedLocationId || '');
            const allShowrooms = this.showroomOptions || [];
            const matched = allShowrooms.find(
                (item) => String(item.showroom_id || item.showrooms_id || item.id || '') === selectedId,
            );
            return matched || this.showroom;
        },
        applyCalendarRules() {
            if (typeof window.setBookingCalendarRules !== 'function') return;
            const showroom = this.getSelectedShowroomRecord();
            const showroomId = String(
                showroom.showroom_id ||
                showroom.showrooms_id ||
                showroom.id ||
                this.selectedLocationId ||
                '',
            );
            const showroomGroup = this.detectShowroomGroup(showroom);
            const publicHolidays = this.getHolidayDatesForShowroomId(showroomId);
            window.setBookingCalendarRules({
                showroomId,
                showroomGroup,
                publicHolidays,
            });
        },
        getLocalTodayIsoDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        getShowroomIdForRules() {
            const showroom = this.getSelectedShowroomRecord();
            return String(
                showroom?.showroom_id ||
                showroom?.showrooms_id ||
                showroom?.id ||
                this.selectedLocationId ||
                '',
            );
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
            const showroomId = this.getShowroomIdForRules();
            const holidayDates = this.getHolidayDatesForShowroomId(showroomId);
            return holidayDates.includes(dateIso);
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
                    !day ||
                    day.classList.contains('disabled') ||
                    day.classList.contains('flatpickr-disabled') ||
                    day.classList.contains('flatpickr-prev-month') ||
                    day.classList.contains('flatpickr-current-month') ||
                    day.classList.contains('flatpickr-next-month')
                ) {
                    return;
                }

                const selectedDateLabel = day.getAttribute('aria-label');
                if (!selectedDateLabel) return;
                const parsedDate = new Date(selectedDateLabel);
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
                // console.log("selectedDateFormatted =", selectedDateFormatted);
                console.log("tourType =", this.tourType || 'physicalTour');

                await this.$store.dispatch('getBookedData', [
                    selectedDateFormatted,
                    this.tourType || 'physicalTour',
                ]);
                this.$emit('open-time-slots', {
                    selectedDate: selectedDateFormatted,
                    tourType: this.tourType || 'physicalTour',
                });
            };

            this.flatpickrDaysContainers.forEach((container) => {
                container.addEventListener('click', this.onFlatpickrDayClick);
            });
        },
    },
    template: `
        <div class="container th-container">
            <div class="row">
                <div class="col-md-5 border-right-gray th-pr-25">
                    <template v-if="isSectionLoading">
                        <div class="placeholder-glow">
                            <span class="placeholder col-8 mb-4" style="height: 34px;"></span>
                            <div class="th-booking-form-container">
                                <div class="th-booking-selected-member d-flex align-items-center th-mb-30">
                                    <div class="th-booking-member-avatar w-100">
                                        <span class="placeholder col-12" style="height: 190px; border-radius: 12px;"></span>
                                    </div>
                                </div>
                                <div class="th-input-group">
                                    <span class="placeholder col-12" style="height: 46px;"></span>
                                </div>
                                <div class="th-input-group">
                                    <span class="placeholder col-12" style="height: 46px;"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="th-section-header-wrapper">
                            <h3 class="title th-mb-30" data-v-booknow-section_title>Book Now</h3>
                        </div>
                        <div class="th-booking-form-container">
                            <div class="th-booking-selected-member d-flex align-items-center th-mb-30">
                                <div class="th-booking-member-avatar">
                                    <img :src="showroom.image" alt="Member Avatar" data-v-booknow-member_image />
                                </div>
                            </div>
                            <form @submit.prevent>
                                <div class="th-input-group">
                                    <label for="choose-location">Location</label>
                                    <select
                                        class="form-control th-choices-select"
                                        name="choose-location"
                                        id="choose-location"
                                        ref="showroomSelect"
                                        v-model="selectedLocationId"
                                    >
                                        <option
                                            v-for="item in showroomOptions"
                                            :key="item.showroom_id"
                                            :value="item.showroom_id"
                                            data-v-booknow-location
                                            :data-map-link="item.map_link"
                                            :data-address="item.address"
                                            :data-image="item.image"
                                        >
                                            {{ item.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="th-input-group" style="display: none;">
                                    <label for="choose-members">Host</label>
                                    <select
                                        class="form-control th-choices-select"
                                        name="choose-members"
                                        id="choose-members"
                                        v-model="selectedHostId"
                                    >
                                        <option :value="showroom.showroom_contact_id" selected data-v-booknow-member>
                                            {{ showroom.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="th-input-group">
                                    <label for="choose-tour-type">Meeting Type</label>
                                    <select
                                        class="form-control th-choices-select"
                                        name="choose-meeting-type"
                                        id="choose-tour-type"
                                        ref="tourTypeSelect"
                                        v-model="tourType"
                                    >
                                        <option value="physicalTour">Physical Tour</option>
                                        <option value="virtualTour">Virtual Meeting</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>
                <div class="col-md-7">
                    <div v-if="isSectionLoading" class="th-calendar-container placeholder-glow">
                        <div class="booking-calendar-wrapper">
                            <span class="placeholder col-12" style="height: 340px; border-radius: 12px;"></span>
                        </div>
                    </div>
                    <div v-else class="th-calendar-container">
                        <div class="booking-calendar-wrapper">
                            <div class="th-booking-calendar">
                                <input class="d-none" type="text" placeholder="Select Date.." data-input />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
};

