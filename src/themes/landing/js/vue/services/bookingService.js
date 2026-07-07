import pinboardService from './pinboardService.js';
import authService from './authService.js';

export default {
    async getNearestShowroom() {
        return pinboardService.getNearestShowroom();
    },
    async getBookedData(date, showroomId, tourType = 'physicalTour') {
        console.log("booking service tourType=", tourType);
        return pinboardService.getBookedData(date, showroomId, tourType);
    },
    async checkExistingBooking(payload) {
        return pinboardService.checkExistingBooking(payload);
    },
    async bookNow(payload) {
        return pinboardService.bookNow(payload);
    },
    async sendEmailVerification(email, customerName = '') {
        return authService.sendEmailVerification({
            email,
            customer_name: customerName,
        });
    },
    async verifyEmail(email, otp) {
        return authService.verifyEmail(email, otp);
    },
    async getUserAuthentication() {
        return authService.getUserAuthentication();
    },
};

