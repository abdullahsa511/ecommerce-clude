document.addEventListener('DOMContentLoaded', async function () {
    const bookNowSection = document.getElementById('book-now');
    if (!bookNowSection) return;

    const module = await import('/js/vue/booking.js');
    const bookingApp = module.default;

    const appContainer = document.getElementById('booking-vue-app');
    if (!appContainer) return;

    await bookingApp.mount(appContainer);
});
