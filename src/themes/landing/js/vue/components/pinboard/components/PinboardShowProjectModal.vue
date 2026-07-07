<template>
    <div v-show="showProjectModal">
        <div class="modal fade th-pinboard-modal backdrop-static" id="guestSignupModal" tabindex="-1"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-bs-backdrop="false"
            style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1040;">
            <div class="pinboard-modal-container">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content modelBorderRadius">
                        <div class="modal-header">
                            <span v-if="!(loggedInUser && customer?.is_verified && !showBookingModal)">{{
                                pinboard.job_title }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="closeModal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- booking confirmation modal -->
                            <form id="bookingModalForm" class="booking-modal-form" v-if="showBookingModal">
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
                                                    <!-- member details -->
                                                    <div class="th-booking-selected-member d-flex flex-column mb-20">
                                                        <div class="th-member-info p-0">
                                                            <h3 class="font-size-20" data-v-booknow-section_title="">
                                                                Meet With Our Consultant</h3>
                                                            <!--<p class="th-member-name" data-v-booknow-name>{{ nearestShowroom?.contact_name }}</p>-->
                                                        </div>
                                                        <div class="th-booking-member-avatar">
                                                            <img :src="nearestShowroom?.image" alt="Member Avatar"
                                                                data-v-booknow-member_image
                                                                style="width: 270px; height: 215px; border-radius: 10px;" />
                                                        </div>
                                                    </div>
                                                    <!-- showroom details -->
                                                    <h4 class="font-weight-600" id="showroomName">{{
                                                        nearestShowroom?.title }}</h4>
                                                    <p class="font-weight-400 color-black">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                        <span id="showroomAddress"> {{ nearestShowroom?.address
                                                            }}</span>
                                                    </p>
                                                    <div class="d-flex th-booking-tour-option my-15">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="radio" id="physicalTour" name="tour_type"
                                                                checked>
                                                            <label for="physicalTour">Physical Tour</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="th-booking-description">
                                                    <!-- <p class="font-weight-400 color-black mb-15">Lorem ipsum dolor sit amet consectetur. Nam dignissim
                                        at vitae faucibus. Lectus ac elit morbi nisl.</p>
                                    <p class="font-weight-400 color-black">Vestibulum neque nunc mattis mauris. Vitae lorem volutpat
                                        gravida augue aliquet at nulla.</p>
                                    -->

                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6 th-booking-calendar-section">
                                            <h3 class="mt-50 font-weight-600">Select a Date & Time</h3>
                                            <div class="booking-calendar-wrapper p-0">
                                                <div class="th-booking-calendar" @click="handleDayClick($event)">
                                                    <input class="d-none" type="text" placeholder="Select Date.."
                                                        data-input />
                                                </div>
                                            </div>
                                            <!-- <div class="th-booking-timezone">
                                        <h3 class="font-weight-600 mt-50">Time Zone</h3>
                                        <div class="th-input-group">
                                        <i class="fa-solid fa-globe"></i>
                                        <select class="form-control th-choices-select" name="choose-members" id="choose-timezone"
                                            placeholder="This is a placeholder">
                                        </select>
                                        </div>
                                    </div> -->
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="py-50">
                                 <button id="bookNowButton" @click.prevent="handleBookingClick('showroom-visit', $event)" type="submit" class="th-btn-primary text-capitalize w-100">Book Now <i
                                class="fa-regular fa-arrow-up degree-60"></i></button>
                            </div>
                            -->
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>