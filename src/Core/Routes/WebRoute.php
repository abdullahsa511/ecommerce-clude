<?php

namespace App\Core\Routes;

use App\Core\Controllers\AuthController;
use App\Core\Controllers\Web\HomeController;
use App\Core\Controllers\Web\AboutController;
use App\Core\Controllers\Web\BlogController;
use App\Core\Controllers\Web\PageController;
use App\Core\Controllers\Web\ProductController;
use App\Core\Controllers\Web\CategoryController;
use App\Core\Controllers\Web\CatalogueController;
use App\Core\Controllers\Web\AccountController;
use App\Core\Controllers\Web\ContactController;
use App\Core\Controllers\Web\DomController;
use App\Core\Controllers\Web\EmailTemplateController;
use App\Core\Controllers\Web\ErrorController;
use App\Core\Controllers\Web\ProjectController;
use App\Core\Controllers\Web\SolutionController;
use App\Core\Controllers\Web\LoginController;
use App\Core\Controllers\Web\AdminController;
use App\Core\Controllers\Web\PinboardBookingController;
use App\Core\Controllers\Web\PaymentController;
use App\Core\Controllers\Web\ShowroomController;
use App\Core\Controllers\Web\SearchController;
use App\Core\Controllers\Web\RobotsController;
use App\Core\Middlewares\WebAuthMiddleware;
use App\Core\System\Event;

class WebRoute{

    private static $routes = [
        ['GET',  '/robots.txt', RobotsController::class, 'index'],
        ['GET',  '/', HomeController::class, 'index'],
        ['POST',  '/', HomeController::class, 'subscribeEmail'],
        // ['GET',  '/subscribe-email', HomeController::class, 'index'],
        // ['GET',  '/subscribe-email', HomeController::class, 'redirectToIndex'],

        ['GET',  "", HomeController::class, 'index'],
        // ['GET',  "/test", HomeController::class, 'test'],
        ['GET',  '/products/{category}/{product}', ProductController::class, 'product'],
        ['GET',  '/products/{category}', ProductController::class, 'category'],
        ['GET',  '/products-list', ProductController::class, 'productList'],
        ['GET',  '/search', ProductController::class, 'searchResults'],
        ['GET',  '/search/results', ProductController::class, 'searchResults'],

        ['GET',  '/categories', CategoryController::class, 'index'],
        ['GET',  '/categories/{category}/{subcategory?}', CategoryController::class, 'details'],
        ['GET',  '/auth/login', AuthController::class, 'showLoginForm'],
        // ['GET',  '/auth/google', AuthController::class, 'googleLogin'],
        // ['GET',  '/auth/google/callback', AuthController::class, 'handleGoogleCallback'],
        ['POST',  '/auth/google', LoginController::class, 'googleLogin'],
        ['GET',  '/auth/google/callback', LoginController::class, 'handleGoogleCallback'],
        ['GET',  '/auth/microsoft', LoginController::class, 'microsoftLogin'],
        ['POST',  '/auth/microsoft', LoginController::class, 'microsoftLogin'],
        ['GET',  '/auth/microsoft/callback', LoginController::class, 'handleMicrosoftCallback'],
        ['GET',  '/about', AboutController::class, 'index'],

        ['GET',  '/blog', BlogController::class, 'index'],
        ['GET',  '/blog/{slug}', BlogController::class, 'detail'],

        ['GET',  '/projects', ProjectController::class, 'index'],
        ['GET',  '/projects/{slug}', ProjectController::class, 'projectDetail'],
        //showroom details route
        ['GET',  '/showroom/{slug}', ShowroomController::class, 'index'],
        // ['GET',  '/showroom/{name}', ShowroomController::class, 'details'],

        ['GET',  '/solutions', SolutionController::class, 'index'],


        ['GET',  '/catalogue', CatalogueController::class, 'index'],
        ['POST',  '/catalogue', CatalogueController::class, 'requestCatalogue'],



        ['GET',  '/catalogue-confirmation/{uuid}', CatalogueController::class, 'catalogueConfirmation'], // old route
        ['GET',  '/contact-get-in-touch/{uuid}', CatalogueController::class, 'catalogueConfirmation'], // contact-get-in-touch for contact us page
        ['GET',  '/catalogue-online-confirmation/{uuid}', CatalogueController::class, 'catalogueConfirmation'], // contact-get-in-touch for Online Catalogue or digital 
        ['GET',  '/catalogue-mail-confirmation/{uuid}', CatalogueController::class, 'catalogueConfirmation'], // contact-get-in-touch for Mail Out Catalogue or physical 


        ['GET',  '/state_code', ContactController::class, 'state'],

        // ['GET',  '/pay', PaymentController::class, 'showPay'],
        // ['GET',  '/payment', PaymentController::class, 'showPayment'],
        // ['GET',  '/makepayment', PaymentController::class, 'showMakePayment'],
        // ['POST', '/api/payment/capture-context', PaymentController::class, 'captureContext'],
        // ['POST', '/api/payment/intent', PaymentController::class, 'createIntent'],
        // ['POST', '/api/payment/pay', PaymentController::class, 'pay'],

        ['GET',  '/contact-sales', ContactController::class, 'index'],
        // ['POST',  '/contact-us', ContactController::class, 'contactSales'],
        ['GET',  '/service-request/file', ContactController::class, 'downloadRequestImages'],
        
        ['POST',  '/contact-us', ContactController::class, 'contactUsGetInTouch'],
        ['GET',  '/contact-us', ContactController::class, 'contactUs'],


        ['GET',  '/account/profile', AccountController::class, 'profile'],
        ['POST',  '/update-profile', AccountController::class, 'updateProfile'],
        
        ['GET',  '/account/delivery-install', AccountController::class, 'deliveryInstall'],
        ['GET',  '/account/upcoming-appointment', AccountController::class, 'upcomingAppointment'],
        ['GET',  '/account/resources', AccountController::class, 'designResourceRedirect'],
        ['GET',  '/account/resources/{resource}', AccountController::class, 'designResource'],
        ['GET',  '/resources', AccountController::class, 'designResourceRedirect'],
        ['GET',  '/resources/{resource}', AccountController::class, 'designResource'],
        ['GET',  '/account/recent-orders', AccountController::class, 'recentOrders'],
        // ['GET',  '/account/recent-orders/{customer_id}', AccountController::class, 'recentOrders'],
        ['GET',  '/account/track-orders', AccountController::class, 'showTrackOrdersForm'],
        ['POST',  '/account/track-orders', AccountController::class, 'trackOrders'],
        ['GET',  '/account/orders/{uuid}', AccountController::class, 'showOrder'],
        ['GET',  '/account/active-quotes', AccountController::class, 'activeQuotes'],
        // ['GET',  '/account/active-quotes/{customer_id}', AccountController::class, 'activeQuotes'],
        ['GET',  '/account/quotes/{uuid}', AccountController::class, 'showQuote'],

        // ['GET',  '/account/pinboards', AccountController::class, 'pinboard'],
        // ['GET',  '/account/pinboards/{pinboard_id}', AccountController::class, 'pinboardDetail'],

        ['GET',  '/account/pinboards', AccountController::class, 'pinboard', [WebAuthMiddleware::class]],
        ['GET',  '/account/virtual-pinboards', AccountController::class, 'pinboard', [WebAuthMiddleware::class]],
        ['GET',  '/account/pinboards/{pinboard_id}', AccountController::class, 'pinboardDetail', [WebAuthMiddleware::class]],

        ['GET',  '/account/virtual-pinboard', AccountController::class, 'virtualPinboard'],

        ['GET',  '/booking/showroom-visit/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        ['GET',  '/pinboards/{id}/booking/showroom-visit', PinboardBookingController::class, 'bookingShowroomVisit'],
        ['GET',  '/pinboards/{uuid}/booking/virtual-meeting', PinboardBookingController::class, 'bookingVirtualMeeting'],
        // ['GET',  '/pinboards/{uuid}/booking/email', PinboardBookingController::class, 'bookingEmail'],
        // ['GET',  '/pinboards/{uuid}/booking/phone-call', PinboardBookingController::class, 'bookingPhoneCall'], // old route
        // ['GET',  '/pinboards/{uuid}/phone-call-request', PinboardBookingController::class, 'bookingPhoneCall'], // old route  
        
        // ======================== start pinboard booking routes ========================
        ['GET',  '/pinboards/phone-call-request/{uuid}', PinboardBookingController::class, 'bookingPhoneCall'],
        ['GET',  '/pinboards/email-confirmation/{uuid}', PinboardBookingController::class, 'submissionConfirmation'],
        // booking now
        ['GET',  '/pinboards/book-showroom-visit/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        ['GET',  '/pinboards/virtual-meeting/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        // booking reschedule
        ['GET',  '/pinboards/rescheduled-showroom-visit/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        ['GET',  '/pinboards/rescheduled-virtual-meeting/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        // cancelled booking
        ['GET',  '/pinboards/cancelled-showroom-visit/{uuid}', PageController::class, 'bookingCancel'],
        ['GET',  '/pinboards/cancelled-virtual-meeting/{uuid}', PageController::class, 'bookingCancel'],
        // ======================== end pinboard booking routes ========================
       
        // ======================== start contact us page booking routes ========================
        // booking now
        ['GET',  '/contact-us/book-physical-showroom-visit/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        ['GET',  '/contact-us/virtual-meeting-booking/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        // booking reschedule
        ['GET',  '/contact-us/rescheduled-physical-showroom-visit/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        ['GET',  '/contact-us/rescheduled-virtual-meeting-booking/{uuid}', PinboardBookingController::class, 'bookingShowroomVisitContactSales'],
        // cancelled booking
        ['GET',  '/contact-us/cancelled-physical-showroom-visit/{uuid}', PageController::class, 'bookingCancel'],
        ['GET',  '/contact-us/cancelled-virtual-meeting-booking/{uuid}', PageController::class, 'bookingCancel'],
        // ======================== end contact us page booking routes ========================

        ['POST',  '/booking/cancel-phone-call', PinboardBookingController::class, 'cancelPhoneCall'],

        ['GET',  '/account/create-request', AccountController::class, 'createRequest'],
        
        ['GET',  '/login', LoginController::class, 'login'],
        // Admin OTP login (3 steps): GET form → POST email (OTP sent) → POST email+OTP → oauthLogin.html.twig → SPA exchange-code.
        ['GET',  '/admin/login', AdminController::class, 'showLogin'],
        ['POST',  '/admin/login', AdminController::class, 'login'],
        ['POST',  '/admin/auth/login', AdminController::class, 'completeLogin'],
        ['GET',  '/admin/auth/google', AdminController::class, 'googleLogin'],
        ['GET',  '/admin/auth/microsoft', AdminController::class, 'microsoftLogin'],
        ['GET',  '/admin/auth/google/callback', AdminController::class, 'handleGoogleCallback'],
        ['GET',  '/admin/auth/microsoft/callback', AdminController::class, 'handleMicrosoftCallback'],
        ['GET',  '/admin/logout', AdminController::class, 'logout'],
        ['GET',  '/signup', LoginController::class, 'signup'],

        ['POST',  '/login', LoginController::class, 'loginUser'],
        ['POST',  '/login/verify-email', LoginController::class, 'verifyEmailAthenticateAndCreatePinboard'],
        ['POST',  '/signup', LoginController::class, 'registerUser'],

        // ['GET',  '/signup', SignupController::class, 'signup'],
        ['GET',  '/logout', LoginController::class, 'logout'],
        ['GET',  '/dom', DomController::class, 'index'],


        ['GET',  '/400', ErrorController::class, 'fourZeroZero'],
        ['GET',  '/401', ErrorController::class, 'fourZeroOne'],
        ['GET',  '/403', ErrorController::class, 'fourZeroThree'],
        ['GET',  '/404', ErrorController::class, 'fourZeroFour'],
        ['GET',  '/409', ErrorController::class, 'fourZeroNine'],
        ['GET',  '/410', ErrorController::class, 'fourZeroTen'],
        ['GET',  '/426', ErrorController::class, 'fourTwentySix'],
        ['GET',  '/429', ErrorController::class, 'fourTwentyNine'],
        ['GET',  '/500', ErrorController::class, 'fiveZeroZero'],
        ['GET',  '/503', ErrorController::class, 'fiveZeroThree'],

        ['GET', '/physical-tour', EmailTemplateController::class, 'physicalTour'],
        ['GET', '/consultation-tomorrow', EmailTemplateController::class, 'consultationTomorrow'],
        ['GET', '/showroom-tomorrow', EmailTemplateController::class, 'showroomTomorrow'],
        ['GET', '/online-meeting', EmailTemplateController::class, 'onlineMeeting'],
        ['GET', '/physical-showroom-tour', EmailTemplateController::class, 'physicalShowroomTour'],
        ['GET', '/virtual-meeting', EmailTemplateController::class, 'virtualMeeting'],
        ['GET', '/client-after-submission', EmailTemplateController::class, 'clientAfterSubmission'],
        ['GET', '/pinboard-submission', EmailTemplateController::class, 'pinboardSubmission'],
        ['GET', '/catalogue-request-client', EmailTemplateController::class, 'catalogueRequestClient'],
        ['GET', '/physical-mailout', EmailTemplateController::class, 'physicalMailout'],
        ['GET', '/online-version', EmailTemplateController::class, 'onlineVersion'],

        ['GET',  '/details-future', PageController::class, 'detailsFuture'],
        ['GET',  '/{page_slug}', PageController::class, 'details'],
        ['GET',  '/booking-cancel/{uuid}', PageController::class, 'bookingCancel'],
        // ['GET',  '/booking-cancel', PageController::class, 'bookingCancelLandingPage'],
    ];

    public static function registerRoutes(){
        Event::on(Route::class, 'add-routes', __CLASS__, function($routes){
            return [array_merge($routes, self::$routes)];
        }, 20);
    }
}
