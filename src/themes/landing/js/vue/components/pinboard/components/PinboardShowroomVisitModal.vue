<template>
    <div class="modal fade backdrop-static" id="timeSlotsModal" tabindex="-1" aria-labelledby="timeSlotsModalLabel"
        data-bs-backdrop="false" style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;"
        aria-modal="true" role="dialog" ref="timeSlotsModal">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
            <div class="modal-content px-80 py-60">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="timeSlotsModalLabel">
                        Booking for Showroom Tour
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="handleCloseTimeSlotsModal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body" id="booking-form-container">

                    <div class="gap-10">

                        <!-- Date -->
                        <div class="th-form-row">
                            <label for="ts-selected-date">Date</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center">
                                    <i class="fa-solid fa-calendar th-input-icon"></i>
                                    <input type="date" id="ts-selected-date" class="form-control th-date-input"
                                        :value="selectedDate" @change="changeDate($event.target.value)" />
                                </div>
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div class="th-form-row th-timezone-selector">
                            <label for="choose-timezone">Time Zone</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center">
                                    <i class="fa-solid fa-globe th-input-icon"></i>
                                    <select class="form-control th-choices-select" id="choose-timezone"
                                        disabled></select>
                                </div>
                            </div>
                        </div>
                        <!-- Name -->
                        <div class="th-form-row">
                            <label for="ts-name">Name</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center" id="ts-name-container">
                                    <i class="fa-solid fa-user th-input-icon"></i>
                                    <input type="text" id="ts-name" class="form-control" placeholder="Enter your name"
                                        style="border: none !important;" v-model="bookingName">
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="th-form-row">
                            <label for="ts-email-not-logged-in-email">Email</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center"
                                    id="ts-email-not-logged-in-email-container">
                                    <i class="fa-solid fa-envelope th-input-icon"></i>
                                    <input type="email" id="ts-email-not-logged-in-email" class="form-control"
                                        placeholder="Enter your email" style="border: none !important;"
                                        v-model="bookingEmail" />
                                </div>
                            </div>
                        </div>

                        <!-- Pick Time -->
                        <div class="th-form-row th-pt-20">
                            <label>Pick a time</label>
                            <div class="th-field">
                                <strong class="d-block">Choose a slot below</strong>
                            </div>
                        </div>

                    </div>

                    <!-- Time Slots -->
                    <div class="ts-slots-blocks th-pt-20">

                        <!-- Morning -->
                        <div class="ts-slots-block ts-slots-morning">
                            <div id="ts-slots-morning" class="ts-slots-grid d-flex flex-wrap gap-2">
                                <div v-for="slot in morningSlots" :key="slot.id" class="th-time-slot" :class="{
                                    active: selectedSlot === slot.id,
                                    'no-hover': selectedSlot !== slot.id,
                                    selected: selectedSlot === slot.id
                                }" @click="toggleSlot($event, slot.id, slot.value)">
                                    <input type="checkbox" class="d-none" :id="slot.id" :value="slot.value"
                                        v-model="checkedSlot" />
                                    <label :for="slot.id">{{ slot.label }}</label>
                                </div>
                            </div>
                        </div>

                        <!-- Evening -->
                        <div class="ts-slots-block ts-slots-evening">
                            <div id="ts-slots-evening" class="ts-slots-grid d-flex flex-wrap gap-2">
                                <div v-for="slot in eveningSlots" :key="slot.id" class="th-time-slot" :class="{
                                    active: selectedSlot === slot.id,
                                    'no-hover': selectedSlot !== slot.id,
                                    selected: selectedSlot === slot.id
                                }" @click="toggleSlot($event, slot.id, slot.value)">
                                    <input type="checkbox" class="d-none" :id="slot.id" :value="slot.value"
                                        v-model="checkedSlot" />
                                    <label :for="slot.id">{{ slot.label }}</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- No Slots -->
                    <div id="ts-no-slots" style="display: none;">
                        No available slots for the selected date/time zone.
                    </div>

                    <!-- Submit -->
                    <div class="pt-60">
                        <button type="button" id="th-book-time-btn" class="th-btn-primary text-capitalize"
                            @click.prevent="handleBookingClick('showroom-visit', $event)">
                            Book
                        </button>
                    </div>

                </div>
                <div id="book-now-verify-email-form-container" class="d-none">
                    <form id="verify-email-form" class="guest-signup-form">
                        <div class="text-center py-10 mb-20">
                            <h6>Verify it's you</h6>
                            <p>
                                We've sent a verification code to
                                <strong id="verify-email-display">{{ bookingEmail }}</strong>. <br>
                                Please enter the code below to save your project.
                            </p>
                            <!-- <strong class="d-block mb-20 text-center" id="otp-text"> otp </strong> -->
                        </div>

                        <!-- OTP BOXES -->
                        <div class="otp-wrapper mb-20">
                            <input v-for="(digit, index) in otpDigits" :key="index" ref="otpInputs" type="text"
                                maxlength="1" class="otp-input" v-model="otpDigits[index]"
                                @input="handleOtpInput(index, $event)" @keydown.backspace="handleBackspace(index)"
                                @paste="handleOtpPaste($event)" />
                        </div>

                        <!-- PRIMARY BUTTON -->
                        <button type="button" class="th-btn-primary text-capitalize w-100 mt-15"
                            id="verify-email-button-time-slots" @click.prevent="handleTimeSlotsModalVerifyOtp">
                            Verify & Continue
                        </button>

                        <div class="text-center mt-15">
                            <span id="otp-timer-text">
                                Resend in 00:00
                            </span>
                        </div>


                        <!-- RESEND LINK -->
                        <div class="text-center mt-15">
                            <small>
                                Didn't receive the code?
                                <a href="javascript:void(0)" class="resend-link" id="resend-otp-button">
                                    Resend OTP
                                </a>
                            </small>
                        </div>

                    </form>
                </div>

                <!-- Message -->
                <div class="col-md-10 text-center mx-auto px-2 px-md-3" id="show-message-container"></div>

            </div>
        </div>
    </div>
</template>