<?php

namespace App\Core\Routes;

use App\Core\Controllers\Api\AdminController;
use App\Core\Controllers\Api\LanguageController;
use App\Core\Controllers\Api\RegionGroupController;
use App\Core\Controllers\Api\RegionController;
use App\Core\Controllers\Api\CountryController;
use App\Core\Controllers\Api\CurrencyController;
use App\Core\Controllers\Api\SiteController;
use App\Core\Controllers\Api\LengthTypeController;
use App\Core\Controllers\Api\WeightTypeController;
use App\Core\Controllers\Api\TaxController;
use App\Core\Controllers\Api\SubscriptionController;
use App\Core\Controllers\Api\PostController;
use App\Core\Controllers\Api\TaxonomyController;
use App\Core\Controllers\Api\CommentController;
use App\Core\Controllers\Api\ComponentController;
use App\Core\Controllers\Api\ListController;
use App\Core\Controllers\Api\MediaController;
use App\Core\Controllers\Api\RoleController;
use App\Core\Controllers\Api\PostTypeController;
use App\Core\Controllers\Api\ProductTypeController;
use App\Core\Controllers\Api\StatusController;
use App\Core\Controllers\Api\UserController;
use App\Core\Controllers\Api\UserGroupController;
use App\Core\Controllers\Api\TagController;
use App\Core\Controllers\Api\ModelController;
use App\Core\Controllers\Api\ProductController;
use App\Core\Controllers\Api\ProjectController;
use App\Core\Controllers\Api\PageController;
use App\Core\Controllers\Api\QuoteController;
use App\Core\Controllers\Api\OrderController;
use App\Core\Controllers\Api\QuoteItemController;
use App\Core\Controllers\Api\OrderItemController;
use App\Core\Controllers\Api\JobController;
use App\Core\Controllers\Api\ProductQuestionController;
use App\Core\Controllers\Api\ProductReviewController;
use App\Core\Controllers\Api\PostTagController;
use App\Core\Controllers\Api\CouponController;
use App\Core\Controllers\Api\CouponItemController;
use App\Core\Controllers\Api\DesignResourceController;
use App\Core\Controllers\Api\PinboardController;
use App\Core\Controllers\Api\PinboardItemController;
use App\Core\Controllers\Api\ProductAttributeController;
use App\Core\Controllers\Api\AttributeController;
use App\Core\Controllers\Api\AttributeGroupController;
use App\Core\Controllers\Api\CompanyController;
use App\Core\Controllers\Api\CustomerController;
use App\Core\Controllers\Api\InstagramController;
use App\Core\Controllers\Api\ItemController;
use App\Core\Controllers\Api\ItemOptionController;
use App\Core\Controllers\Api\OptionController;
use App\Core\Controllers\Api\ProductDiscountController;
use App\Core\Controllers\Api\ProductOptionController;
use App\Core\Controllers\Api\ProductOptionGroupController;
use App\Core\Controllers\Api\TaxonomyItemController;
use App\Core\Controllers\Api\ShowroomController;
use App\Core\Controllers\Api\TypeController;
use App\Core\Controllers\Api\ProductVariantController;
use App\Core\Controllers\Api\VariantItemController;
use App\Core\Controllers\Api\VendorController;
// use App\Core\Controllers\Api\VariantsItemController;
use App\Core\Controllers\Web\BlogController;
use App\Core\Models\Admin\Admin;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Routes\Base\Api;
use App\Core\System\Event;
use App\Core\Controllers\Api\DashboardController;
use App\Core\Controllers\Api\LoginController;
use App\Core\Controllers\Api\ManufacturerController;
use App\Core\Controllers\Api\ProductAccessoriesController;
use App\Core\Controllers\Api\SettingController;
use App\Core\Controllers\Api\TimezoneController;
use App\Core\Controllers\Api\RegisterController;
use App\Core\Controllers\Api\VisitShowroomController;
use App\Core\Controllers\Api\ServiceRequestController;
use App\Core\Controllers\AuthController;

use function App\Core\System\utils\controller;

class ApiRoute extends Api
{
    public static array $routes = [
        // OAuth2 token (ERP / machine clients: client_credentials, refresh_token)
        [
            'method' => 'POST',
            'uri' => '/api/oauth/token',
            'controller' => AuthController::class,
            'action' => 'getClientToken',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/auth',
            'controller' => AuthController::class,
            'action' => 'auth',
        ],
        // ============== start dashboard ===========
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/revenue-card-widget',
            'controller' => DashboardController::class,
            'action' => 'revenueCardWidget',
        ],
        [ // revenue-card-widget/3 dots }
            'method' => 'GET',
            'uri' => '/api/dashboard/revenue-card-details',
            'controller' => DashboardController::class,
            'action' => 'revenueCardDetails',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/overview-widget',
            'controller' => DashboardController::class,
            'action' => 'overviewWidget',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/pinboards-widget',
            'controller' => DashboardController::class,
            'action' => 'pinboardsWidget',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/recent-quotes-widget',
            'controller' => DashboardController::class,
            'action' => 'recentQuotesWidget',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/recent-orders-widget',
            'controller' => DashboardController::class,
            'action' => 'recentOrdersWidget',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/dashboard/quote-order-details',
            'controller' => DashboardController::class,
            'action' => 'quoteOrderDetails',
        ],
        // ============== end dashboard ===========

        // ============== start login ===========
        [
            'method' => 'POST',
            'uri' => '/api/login',
            'controller' => LoginController::class,
            'action' => 'login',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/email-verification-request',
            'controller' => LoginController::class,
            'action' => 'emailVerificationRequest',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/register-customer',
            'controller' => RegisterController::class,
            'action' => 'registerCustomer',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/save-temp',
            'controller' => PinboardController::class,
            'action' => 'saveTempPinboard',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/save',
            'controller' => PinboardController::class,
            'action' => 'savePinboard',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/create-new-project',
            'controller' => PinboardController::class,
            'action' => 'createNewProject',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/comments',
            'controller' => PinboardController::class,
            'action' => 'saveComment',
        ],
        [
            'method' => 'POST',
            'uri' => '/account/api/pinboards/comments',
            'controller' => PinboardController::class,
            'action' => 'saveComment',
        ],
        [
            'method' => 'GET', 
            'uri' => '/api/search-pinboard-products',
            'controller' => PinboardController::class,
            'action' => 'searchPinboardProducts',
        ],
        // create lead route start
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/{pinboard_id}/lead',
            'controller' => PinboardController::class,
            'action' => 'createLead',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/lead-created',
            'controller' => PinboardController::class,
            'action' => 'leadCreated'
        ],
        // create lead route end
        // contact request route start
        [
            'method' => 'GET',
            'uri' => '/api/contact-requests',
            'controller' => VisitShowroomController::class,
            'action' => 'getContactRequestList',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/contact-requests/{id}',
            'controller' => VisitShowroomController::class,
            'action' => 'getContactRequestById',
        ],

        [
            'method' => 'PUT',
            'uri' => '/api/contact-requests/{id}',
            'controller' => VisitShowroomController::class,
            'action' => 'updateContactRequest',
        ],

        [
            'method' => 'DELETE',
            'uri' => '/api/contact-requests/{visit_showroom_id}',
            'controller' => VisitShowroomController::class,
            'action' => 'deleteContactRequest',
        ],
        // contact request route end
        [
            'method' => 'POST',
            'uri' => '/api/visit-showroom/book-now',
            'controller' => VisitShowroomController::class,
            'action' => 'bookNow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/check-existing-booking',
            'controller' => VisitShowroomController::class,
            'action' => 'checkExistingBooking',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/reschedule-booking',
            'controller' => VisitShowroomController::class,
            'action' => 'rescheduleBooking',
        ],
        // cancel booking route
        [
            'method' => 'POST',
            'uri' => '/api/cancel-booking',
            'controller' => VisitShowroomController::class,
            'action' => 'cancelBooking',
        ],
        [ // add note in visit showroom
            'method' => 'POST',
            'uri' => '/api/visit-showroom/send-message',
            'controller' => VisitShowroomController::class,
            'action' => 'sendMessage',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/visit-showroom/timezone',
            'controller' => VisitShowroomController::class,
            'action' => 'getShowroomDateTimes',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/fetch-booked-data/{showroom_id}/{date}',
            'controller' => VisitShowroomController::class,
            'action' => 'fetchBookedDataByShowroomId',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/booking-management/{userId}/{showroom_id}',
            'controller' => VisitShowroomController::class,
            'action' => 'bookingManagement',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/booking-management/add',
            'controller' => VisitShowroomController::class,
            'action' => 'addVisitShowroom',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/booking-management/{visit_showroom_id}',
            'controller' => VisitShowroomController::class,
            'action' => 'updateVisitShowroom',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/send-one-day-prior-visit-showroom-notification',
            'controller' => VisitShowroomController::class,
            'action' => 'sendOneDayPriorVisitShowroomNotification',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/send-one-day-prior-online-meeting-notification',
            'controller' => VisitShowroomController::class,
            'action' => 'sendOneDayPriorOnlineMeetingNotification',
        ],
        // cancel booking by admin
        [
            'method' => 'POST',
            'uri' => '/api/cancel-booking-by-admin/{visit_showroom_id}',
            'controller' => VisitShowroomController::class,
            'action' => 'cancelBookingByAdmin',
        ],
        // absent customer notification
        [
            'method' => 'POST',
            'uri' => '/api/send-absent-customer-notification/{visit_showroom_id}',
            'controller' => VisitShowroomController::class,
            'action' => 'sendAbsentCustomerNotification',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/automatic-send-sales-team',
            'controller' => PinboardController::class,
            'action' => 'automaticSendSalesTeam',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/automatic-send-email-client',
            'controller' => PinboardController::class,
            'action' => 'automaticSendEmailClient',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/booking-phone-call',
            'controller' => PinboardController::class,
            'action' => 'bookingPhoneCall',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/user-nearest-showroom-by-ip',
            'controller' => PinboardController::class,
            'action' => 'getUserNearestShowroomByIp',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/account/pinboard-list',
            'controller' => PinboardController::class,
            'action' => 'accountPinboardList',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/account/project-list/{user_id}',
            'controller' => PinboardController::class,
            'action' => 'getProjectList',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/account-pinboard/update-visibility',
            'controller' => PinboardController::class,
            'action' => 'updatePinboardVisibility',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/account-pinboard/project-submission',
            'controller' => PinboardController::class,
            'action' => 'submitProjectSubmission',
        ],
        // ============== end login ===========
        // ============== start timezone ===========
        [
            'method' => 'GET',
            'uri' => '/api/timezones',
            'controller' => TimezoneController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/timezones/{id}',
            'controller' => TimezoneController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/timezones',
            'controller' => TimezoneController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/timezones/{id}',
            'controller' => TimezoneController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/timezones/{id}',
            'controller' => TimezoneController::class,
            'action' => 'delete',
        ],
        // ============== end timezone ===========
        [
            'method' => 'GET',
            'uri' => '/api/languages',
            'controller' => LanguageController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/languages/{id}',
            'controller' => LanguageController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/languages',
            'controller' => LanguageController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/languages/{id}',
            'controller' => LanguageController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/languages/{id}',
            'controller' => LanguageController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/region-groups',
            'controller' => RegionGroupController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/region-groups/{id}',
            'controller' => RegionGroupController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/region-groups',
            'controller' => RegionGroupController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/region-groups/{id}',
            'controller' => RegionGroupController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/region-groups/{id}',
            'controller' => RegionGroupController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/regions',
            'controller' => RegionController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/regions/{id}',
            'controller' => RegionController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/regions',
            'controller' => RegionController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/regions/{id}',
            'controller' => RegionController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/regions/{id}',
            'controller' => RegionController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/countries',
            'controller' => CountryController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/countries/{id}',
            'controller' => CountryController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/countries',
            'controller' => CountryController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/countries/{id}',
            'controller' => CountryController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/countries/{id}',
            'controller' => CountryController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/currencies',
            'controller' => CurrencyController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/currencies/{id}',
            'controller' => CurrencyController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/currencies',
            'controller' => CurrencyController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/currencies/{id}',
            'controller' => CurrencyController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/currencies/{id}',
            'controller' => CurrencyController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/sites',
            'controller' => SiteController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/sites/{id}',
            'controller' => SiteController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/sites',
            'controller' => SiteController::class,
            'action' => 'create',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/settings/email-settings',
            'controller' => SettingController::class,
            'action' => 'getEmailSettings',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/settings/email-settings',
            'controller' => SettingController::class,
            'action' => 'createEmailSettings',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/sites/{id}',
            'controller' => SiteController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/sites/{id}',
            'controller' => SiteController::class,
            'action' => 'delete',
        ],
        // ============== statr lenth type ===========
        [
            'method' => 'GET',
            'uri' => '/api/length-types',
            'controller' => LengthTypeController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/length-types/{id}',
            'controller' => LengthTypeController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/length-types',
            'controller' => LengthTypeController::class,
            'action' => 'createLengthType',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/length-types/{id}',
            'controller' => LengthTypeController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/length-types/{id}',
            'controller' => LengthTypeController::class,
            'action' => 'delete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/import-length-types',
            'controller' => LengthTypeController::class,
            'action' => 'importLengthTypes',
        ],

        // ============== end lenth type ===========




        // ============== start weight type ===========


        [
            'method' => 'GET',
            'uri' => '/api/weight-types',
            'controller' => WeightTypeController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/weight-types/{id}',
            'controller' => WeightTypeController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/weight-types',
            'controller' => WeightTypeController::class,
            'action' => 'createWeightType',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/weight-types/{id}',
            'controller' => WeightTypeController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/weight-types/{id}',
            'controller' => WeightTypeController::class,
            'action' => 'delete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/import-weight-types',
            'controller' => WeightTypeController::class,
            'action' => 'importWeightTypes',
        ],


        // ============== endweight type ===========

        [
            'method' => 'GET',
            'uri' => '/api/tax-rates',
            'controller' => TaxController::class,
            'action' => 'rateIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/tax-rates/{id}',
            'controller' => TaxController::class,
            'action' => 'rateShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/tax-rates',
            'controller' => TaxController::class,
            'action' => 'rateCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/tax-rates/{id}',
            'controller' => TaxController::class,
            'action' => 'rateUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/tax-rates/{id}',
            'controller' => TaxController::class,
            'action' => 'rateDelete',
        ],
        // tax rate import api
        [
            'method' => 'POST',
            'uri' => '/api/tax-rates/import',
            'controller' => TaxController::class,
            'action' => 'importTaxRates',
        ],
        // end tax rate import api
        [
            'method' => 'GET',
            'uri' => '/api/tax-rules',
            'controller' => TaxController::class,
            'action' => 'ruleIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/tax-rules/{id}',
            'controller' => TaxController::class,
            'action' => 'ruleShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/tax-rules',
            'controller' => TaxController::class,
            'action' => 'ruleCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/tax-rules/{id}',
            'controller' => TaxController::class,
            'action' => 'ruleUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/tax-rules/{id}',
            'controller' => TaxController::class,
            'action' => 'ruleDelete',
        ],
        [ // ecommerce => tax type
            'method' => 'GET',
            'uri' => '/api/tax-types',
            'controller' => TaxController::class,
            'action' => 'typeIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/tax-types/{id}',
            'controller' => TaxController::class,
            'action' => 'typeShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/tax-types',
            'controller' => TaxController::class,
            'action' => 'typeCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/tax-types/{id}',
            'controller' => TaxController::class,
            'action' => 'typeUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/tax-types/{id}',
            'controller' => TaxController::class,
            'action' => 'typeDelete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/tax-types/import',
            'controller' => TaxController::class,
            'action' => 'importTaxTypes',
        ],
        // end ecommerce => tax type
        // ============== statr subscription  ===========
        [
            'method' => 'GET',
            'uri' => '/api/subscriptions',
            'controller' => SubscriptionController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscriptions/subscribe-requests',
            'controller' => SubscriptionController::class,
            'action' => 'subscribeRequests',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscriptions/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/subscriptions',
            'controller' => SubscriptionController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/subscriptions/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/subscriptions/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscription-plans',
            'controller' => SubscriptionController::class,
            'action' => 'planIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscription-plans/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'planShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/subscription-plans',
            'controller' => SubscriptionController::class,
            'action' => 'planCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/subscription-plans/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'planUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/subscription-plans/{id}',
            'controller' => SubscriptionController::class,
            'action' => 'planDelete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/subscribe-email',
            'controller' => SubscriptionController::class,
            'action' => 'subscribeEmail',
        ],

        // ============== end subscription  ===========



        [
            'method' => 'GET',
            'uri' => '/api/posts',
            'controller' => PostController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/posts/{id}',
            'controller' => PostController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/posts',
            'controller' => PostController::class,
            'action' => 'create',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/posts/{id}',
            'controller' => PostController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/posts/{id}',
            'controller' => PostController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/posts/{post_id}/delete-image/{property}',
            'controller' => PostController::class,
            'action' => 'deletePostBannerFeatureImage',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/posts/delete-image/{post_image_id}',
            'controller' => PostController::class,
            'action' => 'deletePostImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/posts/{post_id}/upload',
            'controller' => PostController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/posts-statuses',
            'controller' => PostController::class,
            'action' => 'getStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/posts-import',
            'controller' => PostController::class,
            'action' => 'importPosts',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-image-import',
            'controller' => PostController::class,
            'action' => 'importPostImages',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/posts/update-way-points',
            'controller' => PostController::class,
            'action' => 'updateWayPoints',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/posts/remove-way-point',
            'controller' => PostController::class,
            'action' => 'removeWayPoint',
        ],
        // reorder post images
        [
            'method' => 'POST',
            'uri' => '/api/reorder-post-images/{post_id}',
            'controller' => PostController::class,
            'action' => 'reorderImages',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/posts/gallary-image/delete-by-ids',
            'controller' => PostController::class,
            'action' => 'deletePostGalleryImage',
        ],

        // ============= end post route ===================
        [
            'method' => 'GET',
            'uri' => '/api/post-tags',
            'controller' => PostTagController::class,
            'action' => 'getAllPostTags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/post-tags/{id}',
            'controller' => PostTagController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-tags',
            'controller' => PostTagController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/post-tags/{id}',
            'controller' => PostTagController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/post-tags/{id}',
            'controller' => PostTagController::class,
            'action' => 'delete',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/post-tags/import-post-tags',
            'controller' => PostTagController::class,
            'action' => 'importPostTags',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-tags/{id}/upload',
            'controller' => PostTagController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pages',
            'controller' => PageController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pages/{id}',
            'controller' => PageController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pages',
            'controller' => PageController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/pages/{id}',
            'controller' => PageController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/pages/{id}',
            'controller' => PageController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pages-statuses',
            'controller' => PageController::class,
            'action' => 'getStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pages/{page_id}/upload',
            'controller' => PageController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pages/delete-by-path',
            'controller' => PageController::class,
            'action' => 'deleteByPath',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pages-import',
            'controller' => PageController::class,
            'action' => 'importPages',
        ],
        // [
        //     'method' => 'GET',
        //     'uri' => '/api/post-tags',
        //     'controller' => TagController::class,
        //     'action' => 'index',
        // ],
        // [
        //     'method' => 'GET',
        //     'uri' => '/api/post-tags/{id}',
        //     'controller' => TagController::class,
        //     'action' => 'show',
        // ],
        // [
        //     'method' => 'POST',
        //     'uri' => '/api/post-tags',
        //     'controller' => TagController::class,
        //     'action' => 'create',
        // ],
        // [
        //     'method' => 'PUT',
        //     'uri' => '/api/post-tags/{id}',
        //     'controller' => TagController::class,
        //     'action' => 'update',
        // ],
        // [
        //     'method' => 'DELETE',
        //     'uri' => '/api/post-tags/{id}',
        //     'controller' => TagController::class,
        //     'action' => 'delete',
        // ],
        [
            'method' => 'GET',
            'uri' => '/api/taxonomies',
            'controller' => TaxonomyController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/taxonomies/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/taxonomies',
            'controller' => TaxonomyController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/taxonomies/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/taxonomies/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'delete',
        ],
        [ // import taxonomies
            'method' => 'POST',
            'uri' => '/api/taxonomies/import',
            'controller' => TaxonomyController::class,
            'action' => 'importTaxonomies',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/taxonomies/import',
            'controller' => TaxonomyController::class,
            'action' => 'importTaxonomies',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/taxonomy-types',
            'controller' => TaxonomyController::class,
            'action' => 'getTaxonomyTypes',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/category-import',
            'controller' => TaxonomyController::class,
            'action' => 'importTaxonomyItems',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/comments',
            'controller' => CommentController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/comments/{uuid}',
            'controller' => CommentController::class,
            'action' => 'getCommentsById',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/comments',
            'controller' => CommentController::class,
            'action' => 'create',
        ],
      
        [
            'method' => 'POST',
            'uri' => '/api/comments/save',
            'controller' => CommentController::class,
            'action' => 'saveComment',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/comments/{uuid}/pinboard',
            'controller' => CommentController::class,
            'action' => 'getCommentsById',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/comments/pinboard-comment-save',
            'controller' => CommentController::class,
            'action' => 'savePinboardComment',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/comments/upvote',
            'controller' => CommentController::class,
            'action' => 'upvoteComment',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/comments/checked',
            'controller' => CommentController::class,
            'action' => 'checkedComment',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/comments/reply',
            'controller' => CommentController::class,
            'action' => 'submitReplyComment',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/comments/{id}',
            'controller' => CommentController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/comments/{id}',
            'controller' => CommentController::class,
            'action' => 'deleteComment',
        ],

        //product accessories route start

        [
            'method' => 'GET',
            'uri' => '/api/product-accessories/list',
            'controller' => ProductAccessoriesController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-accessories/{id}',
            'controller' => ProductAccessoriesController::class,
            'action' => 'getAccessoriesById',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-accessories/delete/{id}',
            'controller' => ProductAccessoriesController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-accessories/import',
            'controller' => ProductAccessoriesController::class,
            'action' => 'importAccessories',
        ],

        //product accessories route end

        [
            'method' => 'GET',
            'uri' => '/api/product-questions',
            'controller' => ProductQuestionController::class,
            'action' => 'index',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-questions/{id}',
            'controller' => ProductQuestionController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-questions/{id}',
            'controller' => ProductQuestionController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-reviews',
            'controller' => ProductReviewController::class,
            'action' => 'index',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-reviews/{id}',
            'controller' => ProductReviewController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-reviews/{id}',
            'controller' => ProductReviewController::class,
            'action' => 'delete',
        ],

        // ================ start products variants item route ===================
        [
            'method' => 'GET',
            'uri' => '/api/variant-items',
            'controller' => VariantItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/variant-items/{variant_id}',
            'controller' => VariantItemController::class,
            'action' => 'getVariantByVariantId',
        ],
        // [
        //     'method' => 'POST',
        //     'uri' => '/api/variant-items',
        //     'controller' => VariantItemController::class,
        //     'action' => 'create',
        // ],
        // [
        //     'method' => 'PUT',
        //     'uri' => '/api/variant-items/{id}',
        //     'controller' => VariantItemController::class,
        //     'action' => 'update',
        // ],
        [
            'method' => 'DELETE',
            'uri' => '/api/variant-items/{id}',
            'controller' => VariantItemController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/variant-items/import',
            'controller' => VariantItemController::class,
            'action' => 'importVariantItem',
        ],

        [  // search
            'method' => 'GET',
            'uri' => '/api/item-variants/search',
            'controller' => ProductVariantController::class,
            'action' => 'searchVariantItems',
        ],
        // ========= variant item route ===================
         [
            'method' => 'POST',
            'uri' => '/api/variant-items',
            'controller' => VariantItemController::class,
            'action' => 'createItemVariant',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/variant-items/{id}',
            'controller' => VariantItemController::class,
            'action' => 'updateItemVariant',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/variant-items/{item_id}',
            'controller' => VariantItemController::class,
            'action' => 'getVariantByItem',
        ],
        [  // product options import csv
            'method' => 'GET',
            'uri' => '/api/variant-item-option/search',
            'controller' => VariantItemController::class,
            'action' => 'searchItemOptions',
        ],
        // ================ end products variants item route ===================

        [
            'method' => 'POST',
            'uri' => '/api/media-way-points',
            'controller' => MediaController::class,
            'action' => 'mediaWayPoint',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/media',
            'controller' => MediaController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media/{id}',
            'controller' => MediaController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/upload',
            'controller' => MediaController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/upload-to-folder/{folder_id}',
            'controller' => MediaController::class,
            'action' => 'uploadToFolder',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/delete-by-filename',
            'controller' => MediaController::class,
            'action' => 'deleteByFilename',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/delete-by-path',
            'controller' => MediaController::class,
            'action' => 'deleteByPath',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media-folders',
            'controller' => MediaController::class,
            'action' => 'getFolders',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media-folders/{id}',
            'controller' => MediaController::class,
            'action' => 'getSubFolders',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media-files',
            'controller' => MediaController::class,
            'action' => 'getFiles',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media-files/{id}',
            'controller' => MediaController::class,
            'action' => 'getFiles',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media-folders',
            'controller' => MediaController::class,
            'action' => 'createFolder',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/media-folders/{id}',
            'controller' => MediaController::class,
            'action' => 'deleteFolder',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/files/{id}',
            'controller' => MediaController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/media/delete/files',
            'controller' => MediaController::class,
            'action' => 'deleteFiles',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/media-categories',
            'controller' => MediaController::class,
            'action' => 'getCategories',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/roles',
            'controller' => RoleController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/roles/{id}',
            'controller' => RoleController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/roles',
            'controller' => RoleController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/roles/{id}',
            'controller' => RoleController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/roles/{id}',
            'controller' => RoleController::class,
            'action' => 'delete',
        ],
        // role import api
        [
            'method' => 'POST',
            'uri' => '/api/roles/import',
            'controller' => RoleController::class,
            'action' => 'importRoles',
        ],
        // end role import api
        [
            'method' => 'GET',
            'uri' => '/api/post-types',
            'controller' => PostTypeController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/post-types/{id}',
            'controller' => PostTypeController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-types',
            'controller' => PostTypeController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/post-types/{id}',
            'controller' => PostTypeController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/post-types/{id}',
            'controller' => PostTypeController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-types/{post_type_id}/upload',
            'controller' => PostTypeController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-types/{post_type_id}/delete',
            'controller' => PostTypeController::class,
            'action' => 'deleteImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/post-types/import',
            'controller' => PostTypeController::class,
            'action' => 'importPostTypes',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-design-resource',
            'controller' => DesignResourceController::class,
            'action' => 'importDesignResources',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/finishes-import',
            'controller' => DesignResourceController::class,
            'action' => 'importFinishes',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/textiles-import',
            'controller' => DesignResourceController::class,
            'action' => 'importTextiles',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/colors-import',
            'controller' => DesignResourceController::class,
            'action' => 'importColors',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/documents-import',
            'controller' => DesignResourceController::class,
            'action' => 'importDocuments',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/models-import',
            'controller' => DesignResourceController::class,
            'action' => 'importModels',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/update-model-document-format',
            'controller' => DesignResourceController::class,
            'action' => 'updateModelDocumentFormat',
        ],
        // ----------- start account resource images api -------------- website api
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/types',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceDocumentTypes',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/account/resource/images',
            'controller' => DesignResourceController::class,
            'action' => 'getResourceImages',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/images',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceImages',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/models',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceModels',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/finish_textile',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceFinishesTextiles',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/design-resources/documents',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceDocuments',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/design-resources/finishes',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceFinishes',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/design-resources/textiles',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourcetextiles',
        ],

        // [
        //     'method' => 'GET',
        //     'uri' => '/api/design-resources/finish_textile',
        //     'controller' => DesignResourceController::class,
        //     'action' => 'getDesignResourceFinishesTextiles',
        // ],

        // ----------- end account resource images api --------------
        // =============== design resource api for admin ===============
        // [
        //     'method' => 'GET',
        //     'uri' => '/api/design-resources-documents',
        //     'controller' => DesignResourceController::class,
        //     'action' => 'getDesignResource',
        // ],
        // get document by id for all resource types
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/documents/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceById',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'getDesignResourceByIdByResourceType',
        ],
        // upload design resource
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/upload/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'uploadDesignResource',
        ],
        // document create
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/documents/create',
            'controller' => DesignResourceController::class,
            'action' => 'createDesignResourceDocument',
        ],
        // document update
        [
            'method' => 'PUT',
            'uri' => '/api/design-resources/documents/update/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'updateDesignResourceDocument',
        ],
        // document delete
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/documents/delete/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceDocument',
        ],
        // model create
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/models/create',
            'controller' => DesignResourceController::class,
            'action' => 'createDesignResourceModel',
        ],
        // model update
        [
            'method' => 'PUT',
            'uri' => '/api/design-resources/models/update/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'updateDesignResourceModel',
        ],
        // model delete
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/models/delete/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceModel',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/models/delete-by-ids',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceByIds',
        ],
        // finish create
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/finishes/create',
            'controller' => DesignResourceController::class,
            'action' => 'createDesignResourceFinish',
        ],
        // finish update
        [
            'method' => 'PUT',
            'uri' => '/api/design-resources/finishes/update/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'updateDesignResourceFinish',
        ],
        // finish delete
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/finishes/delete/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceFinish',
        ],

        // get option by finishes data
        [
            'method' => 'GET',
            'uri' => '/api/product-configurator/get-textiles-data-by-grade/{grade}',
            'controller' => DesignResourceController::class,
            'action' => 'getTextilesDataByType',
        ],


        // textile create
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/textiles/create',
            'controller' => DesignResourceController::class,
            'action' => 'createDesignResourceTextile',
        ],
        // textile update
        [
            'method' => 'PUT',
            'uri' => '/api/design-resources/textiles/update/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'updateDesignResourceTextile',
        ],
        // textile delete
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/textiles/delete/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceTextile',
        ],
        // document delete
        [
            'method' => 'POST',
            'uri' => '/api/design-resources/document-record/{id}',
            'controller' => DesignResourceController::class,
            'action' => 'deleteDesignResourceDocRecord',
        ],


         // design resource filter context type by category name api
         [
            'method' => 'GET',
            'uri' => '/api/design-resources/categories/{context_type}/{resource_type?}',
            'controller' => DesignResourceController::class,
            'action' => 'filterCategoriesByContextType',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/context-category-by-name/{category_id}',
            'controller' => DesignResourceController::class,
            'action' => 'filterCategroyIdByCategoryName',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/model-name-by-model-id',
            'controller' => DesignResourceController::class,
            'action' => 'filterModelNameByModelId',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/global-search',
            'controller' => DesignResourceController::class,
            'action' => 'globalSearch',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/popular-search',
            'controller' => DesignResourceController::class,
            'action' => 'getPopularSearch',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/global-search-by-context',
            'controller' => DesignResourceController::class,
            'action' => 'globalSearchByContext',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/design-resources/resources-by-desk',
            'controller' => DesignResourceController::class,
            'action' => 'getResourcesByDesk', // finishes by desk
        ],

        // ================================= end design resource admin api =================================
        [
            'method' => 'GET',
            'uri' => '/api/projects',
            'controller' => ProjectController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/project-pagination',
            'controller' => ProjectController::class,
            'action' => 'getProjectPaginationData',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/projects/{id}',
            'controller' => ProjectController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects',
            'controller' => ProjectController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/projects/{id}',
            'controller' => ProjectController::class,
            'action' => 'update',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects/update-way-points',
            'controller' => ProjectController::class,
            'action' => 'updateWayPoints',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects/remove-way-point',
            'controller' => ProjectController::class,
            'action' => 'removeWayPoint',
        ],

        [
            'method' => 'DELETE',
            'uri' => '/api/projects/{id}',
            'controller' => ProjectController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects/delete-by-ids',
            'controller' => ProjectController::class,
            'action' => 'deleteMultipleImagesById',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/projects/delete-image/{project_image_id}',
            'controller' => ProjectController::class,
            'action' => 'deleteProjectImage',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/projects-statuses',
            'controller' => ProjectController::class,
            'action' => 'getStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/project-import',
            'controller' => ProjectController::class,
            'action' => 'importProjects',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/project-image-import',
            'controller' => ProjectController::class,
            'action' => 'importProjectImages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects/{project_id}/upload',
            'controller' => ProjectController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/projects/delete-by-path',
            'controller' => ProjectController::class,
            'action' => 'deleteByPath',
        ],
        // reorder project images
        [
            'method' => 'POST',
            'uri' => '/api/reorder-project-images/{project_image_id}',
            'controller' => ProjectController::class,
            'action' => 'reorderImages',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/products',
            'controller' => ProductController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/products/{id}',
            'controller' => ProductController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products',
            'controller' => ProductController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/products/{id}',
            'controller' => ProductController::class,
            'action' => 'update',
        ],
        [ // Message: No route found for [POST /api/products/109/upload]
            'method' => 'POST',
            'uri' => '/api/products/{product_id}/upload',
            'controller' => ProductController::class,
            'action' => 'upload',
        ],
        [ // Message: No route found for [POST /api/products/109/upload]
            'method' => 'get',
            'uri' => '/api/products/{product_id}/variants',
            'controller' => ProductVariantController::class,
            'action' => 'productVariants',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/products/{id}',
            'controller' => ProductController::class,
            'action' => 'delete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/products/update-way-points',
            'controller' => ProductController::class,
            'action' => 'updateWayPoints',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/remove-way-point',
            'controller' => ProductController::class,
            'action' => 'removeWayPoint',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/update-product-document-format',
            'controller' => ProductController::class,
            'action' => 'updateProductDocumentFormat',
        ],


        // product upload mange
        [
            'method' => 'POST',
            'uri' => '/api/product/upload',
            'controller' => ProductController::class,
            'action' => 'uploadMedia',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product/delete-by-path',
            'controller' => ProductController::class,
            'action' => 'deleteByPath',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/products-categories',
            'controller' => ProductController::class,
            'action' => 'getCategories',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/categories/update-category-order',
            'controller' => ProductController::class,
            'action' => 'updateCategoryOrder',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/category-banner-way-points/update',
            'controller' => ProductController::class,
            'action' => 'updateCategoryBannerWayPoints',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/products-list',
            'controller' => ProductController::class,
            'action' => 'productList',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-related-search',
            'controller' => ProductController::class,
            'action' => 'relatedProductSearch',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-related-project-search',
            'controller' => ProjectController::class,
            'action' => 'relatedProjectSearch',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-related-resource-search',
            'controller' => DesignResourceController::class,
            'action' => 'relatedResourceSearch',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/get-products-by-category',
            'controller' => ProductController::class,
            'action' => 'getProductsByCategory',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-variant-search',
            'controller' => ProductController::class,
            'action' => 'variantProductSearch',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/digital-asset-search',
            'controller' => ProductController::class,
            'action' => 'digitalAssetSearch',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-options',
            'controller' => ProductController::class,
            'action' => 'getOptions',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-import',
            'controller' => ProductController::class,
            'action' => 'importProducts',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-meta',
            'controller' => ProductController::class,
            'action' => 'importProductMeta',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-images',
            'controller' => ProductController::class,
            'action' => 'importProductsImages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-related',
            'controller' => ProductController::class,
            'action' => 'importRelatedProducts',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-sort-by-category',
            'controller' => ProductController::class,
            'action' => 'importProductsSortByCategory',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-digital-assets',
            'controller' => ProductController::class,
            'action' => 'importProductsDigitalAssets',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-attributes',
            'controller' => ProductController::class,
            'action' => 'importProductsAttributes',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-variants',
            'controller' => ProductController::class,
            'action' => 'importProductsVariants',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-options',
            'controller' => ProductController::class,
            'action' => 'importProductsOptions',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-products-tags',
            'controller' => ProductController::class,
            'action' => 'importProductsTags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/status',
            'controller' => InstagramController::class,
            'action' => 'status',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/token/pages',
            'controller' => InstagramController::class,
            'action' => 'listPages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/instagram/token/page-credentials',
            'controller' => InstagramController::class,
            'action' => 'resolvePageToken',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/token/debug',
            'controller' => InstagramController::class,
            'action' => 'debugToken',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/hashtags/search',
            'controller' => InstagramController::class,
            'action' => 'searchHashtags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/hashtags/{hashtagId}/posts',
            'controller' => InstagramController::class,
            'action' => 'hashtagPosts',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/instagram/account/posts',
            'controller' => InstagramController::class,
            'action' => 'accountPosts',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/products/{product_id}/instagram-links',
            'controller' => InstagramController::class,
            'action' => 'productLinks',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/{product_id}/instagram-links',
            'controller' => InstagramController::class,
            'action' => 'createProductLink',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/products/{product_id}/instagram-links/{link_id}',
            'controller' => InstagramController::class,
            'action' => 'updateProductLink',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/products/{product_id}/instagram-links/{link_id}',
            'controller' => InstagramController::class,
            'action' => 'deleteProductLink',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/{product_id}/instagram-links/sync',
            'controller' => InstagramController::class,
            'action' => 'syncProductLinks',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/{product_id}/instagram-links/reorder',
            'controller' => InstagramController::class,
            'action' => 'reorderProductLinks',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-manufacturer-vendor',
            'controller' => ProductController::class,
            'action' => 'importManufacturerVendors',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-product-certificates',
            'controller' => ProductController::class,
            'action' => 'importProductCertificates',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-product-related-projects',
            'controller' => ProductController::class,
            'action' => 'importProductRelatedProjects',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/products/delete-image/{product_image_id}',
            'controller' => ProductController::class,
            'action' => 'deleteProductImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products/gallary-image/delete-by-ids',
            'controller' => ProductController::class,
            'action' => 'deleteProductGalleryImage',
        ],
        // delete related product
        [
            'method' => 'DELETE',
            'uri' => '/api/products/delete-related-product/{product_id}/{related_product_id}',
            'controller' => ProductController::class,
            'action' => 'deleteRelatedProduct',
        ],
        // delete related product
        [
            'method' => 'DELETE',
            'uri' => '/api/products/remove-product-from-family/{product_id}/{related_product_id}',
            'controller' => ProductController::class,
            'action' => 'removeProductFromFamily',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/products/remove-product-related-project/{product_id}/{project_id}',
            'controller' => ProductController::class,
            'action' => 'removeProductRelatedProject',
        ],
        // get products by desk
        [
            'method' => 'GET',
            'uri' => '/api/category/{category}/products',
            'controller' => ProductController::class,
            'action' => 'getProductsByCategorySlug',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-search-for-waypoints',
            'controller' => ProductController::class,
            'action' => 'productSearchForWaypoints',
        ],
        // get all vendors
        [
            'method' => 'GET',
            'uri' => '/api/vendors',
            'controller' => VendorController::class,
            'action' => 'index',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/vendors/search-vendors',
            'controller' => VendorController::class,
            'action' => 'searchVendors',
        ],

        [
            'method' => 'GET',
            'uri' => '/api/vendors/{id}',
            'controller' => VendorController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/vendors',
            'controller' => VendorController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/vendors/{id}',
            'controller' => VendorController::class,
            'action' => 'update',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/vendors/{vendor_id}/upload',
            'controller' => VendorController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/vendors/{vendor_id}/delete',
            'controller' => VendorController::class,
            'action' => 'deleteImage',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/vendors/{id}',
            'controller' => VendorController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-vendors',
            'controller' => VendorController::class,
            'action' => 'importVendors',
        ],

        // get all manufacturers
        [
            'method' => 'GET',
            'uri' => '/api/manufacturers',
            'controller' => ManufacturerController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/manufacturers/{id}',
            'controller' => ManufacturerController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/manufacturers',
            'controller' => ManufacturerController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/manufacturers/{id}',
            'controller' => ManufacturerController::class,
            'action' => 'update',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/manufacturers/{manufacturer_id}/upload',
            'controller' => ManufacturerController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/manufacturers/{manufacturer_id}/delete',
            'controller' => ManufacturerController::class,
            'action' => 'deleteImage',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/manufacturers/{id}',
            'controller' => ManufacturerController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-manufacturers',
            'controller' => ManufacturerController::class,
            'action' => 'importManufacturers',
        ],



        /**
         * ----------- start product item api --------------
         * This api is for product item related api
         * This api is used to manage product item related data
         * */
        [
            'method' => 'GET',
            'uri' => '/api/items',
            'controller' => ItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/items/{id}',
            'controller' => ItemController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/items',
            'controller' => ItemController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/items/{id}',
            'controller' => ItemController::class,
            'action' => 'update',
        ],
        [ // Message: No route found for [POST /api/products/109/upload]
            'method' => 'POST',
            'uri' => '/api/items/{item_id}/upload',
            'controller' => ItemController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/items/{id}',
            'controller' => ItemController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/items-import',
            'controller' => ItemController::class,
            'action' => 'importItems',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/dimensions-import',
            'controller' => ItemController::class,
            'action' => 'importDimensions',
        ],
        // product upload mange
        [
            'method' => 'POST',
            'uri' => '/api/item/upload',
            'controller' => ItemController::class,
            'action' => 'uploadMedia',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/items/delete-by-path/{item_id}',
            'controller' => ItemController::class,
            'action' => 'deleteByPath',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/items-list-search',
            'controller' => ItemController::class,
            'action' => 'searchItemlists',
        ],
        // ========= item variant route ===================
        [
            'method' => 'POST',
            'uri' => '/api/item-variants',
            'controller' => ItemController::class,
            'action' => 'createItemVariant',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/item-variants/{id}',
            'controller' => ItemController::class,
            'action' => 'updateItemVariant',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/item-variants/{item_id}',
            'controller' => ItemController::class,
            'action' => 'getVariantByItem',
        ],
        [  // product options import csv
            'method' => 'GET',
            'uri' => '/api/item-variant-option/search',
            'controller' => ItemController::class,
            'action' => 'searchItemOptions',
        ],


        /**
         * end product item api
         * */
        // ITEM OPTION ROUTE
        [
            'method' => 'GET',
            'uri' => '/api/item-options',
            'controller' => ItemOptionController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/item-options/{id}',
            'controller' => ItemOptionController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/item-options',
            'controller' => ItemOptionController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/item-options/{id}',
            'controller' => ItemOptionController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/item-options/{id}',
            'controller' => ItemOptionController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/item-options/delete-option-group',
            'controller' => ItemOptionController::class,
            'action' => 'deleteItemOptionGroup',
        ],
        // ITEM OPTION IMPORT ROUTE
        [
            'method' => 'POST',
            'uri' => '/api/item-options/import',
            'controller' => ItemOptionController::class,
            'action' => 'importItemOptions',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/item-options/{item_option_id}/upload',
            'controller' => ItemOptionController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/item-options/{item_option_id}/delete',
            'controller' => ItemOptionController::class,
            'action' => 'deleteImage',
        ],
        /**
         * END ITEM OPTION ROUTE
         * */
        [
            'method' => 'GET',
            'uri' => '/api/product-types',
            'controller' => ProductTypeController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-types/{id}',
            'controller' => ProductTypeController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-types',
            'controller' => ProductTypeController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-types/{id}',
            'controller' => ProductTypeController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-types/{id}',
            'controller' => ProductTypeController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-types/{product_type_id}/upload',
            'controller' => ProductTypeController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-types/{product_type_id}/delete',
            'controller' => ProductTypeController::class,
            'action' => 'deleteImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-types/{id}/upload',
            'controller' => ProductTypeController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-types/import',
            'controller' => ProductTypeController::class,
            'action' => 'importProductTypes',
        ],
        // statuses api
        [
            'method' => 'POST',
            'uri' => '/api/stock-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importStockStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/subscription-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importSubscriptionStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/order-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importOrderStatuses',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/payment-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importPaymentStatuses',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/stock-statuses',
            'controller' => StatusController::class,
            'action' => 'stockStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/stock-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'stockStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/stock-statuses',
            'controller' => StatusController::class,
            'action' => 'stockStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/stock-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'stockStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/stock-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'stockStatusDelete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscription-statuses',
            'controller' => StatusController::class,
            'action' => 'subscriptionStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/subscription-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'subscriptionStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/subscription-statuses',
            'controller' => StatusController::class,
            'action' => 'subscriptionStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/subscription-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'subscriptionStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/subscription-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'subscriptionStatusDelete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/order-statuses',
            'controller' => StatusController::class,
            'action' => 'orderStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/order-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'orderStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/order-statuses',
            'controller' => StatusController::class,
            'action' => 'orderStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/order-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'orderStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/order-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'orderStatusDelete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/payment-statuses',
            'controller' => StatusController::class,
            'action' => 'paymentStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/payment-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'paymentStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/payment-statuses',
            'controller' => StatusController::class,
            'action' => 'paymentStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/payment-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'paymentStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/payment-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'paymentStatusDelete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/shipping-statuses',
            'controller' => StatusController::class,
            'action' => 'shippingStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/shipping-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'shippingStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/shipping-statuses',
            'controller' => StatusController::class,
            'action' => 'shippingStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/shipping-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'shippingStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/shipping-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'shippingStatusDelete',
        ],
        // shipping import api
        [
            'method' => 'POST',
            'uri' => '/api/shipping-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importShippingStatuses',
        ],
        // end shipping statuses api
        [
            'method' => 'GET',
            'uri' => '/api/return-statuses',
            'controller' => StatusController::class,
            'action' => 'returnStatusIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/return-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'returnStatusShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/return-statuses',
            'controller' => StatusController::class,
            'action' => 'returnStatusCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/return-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'returnStatusUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/return-statuses/{id}',
            'controller' => StatusController::class,
            'action' => 'returnStatusDelete',
        ],
        // return statuses import api
        [
            'method' => 'POST',
            'uri' => '/api/return-statuses/import',
            'controller' => StatusController::class,
            'action' => 'importReturnStatuses',
        ],
        // end return statuses import api
        [
            'method' => 'GET',
            'uri' => '/api/return-resolutions',
            'controller' => StatusController::class,
            'action' => 'returnResolutionIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/return-resolutions/{id}',
            'controller' => StatusController::class,
            'action' => 'returnResolutionShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/return-resolutions',
            'controller' => StatusController::class,
            'action' => 'returnResolutionCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/return-resolutions/{id}',
            'controller' => StatusController::class,
            'action' => 'returnResolutionUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/return-resolutions/{id}',
            'controller' => StatusController::class,
            'action' => 'returnResolutionDelete',
        ],
        // return resolutions import api
        [
            'method' => 'POST',
            'uri' => '/api/return-resolutions/import',
            'controller' => StatusController::class,
            'action' => 'importReturnResolutions',
        ],
        // end return resolutions import api
        [
            'method' => 'GET',
            'uri' => '/api/return-reasons',
            'controller' => StatusController::class,
            'action' => 'returnReasonIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/return-reasons/{id}',
            'controller' => StatusController::class,
            'action' => 'returnReasonShow',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/return-reasons',
            'controller' => StatusController::class,
            'action' => 'returnReasonCreate',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/return-reasons/{id}',
            'controller' => StatusController::class,
            'action' => 'returnReasonUpdate',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/return-reasons/{id}',
            'controller' => StatusController::class,
            'action' => 'returnReasonDelete',
        ],
        // return reasons import api
        [
            'method' => 'POST',
            'uri' => '/api/return-reasons/import',
            'controller' => StatusController::class,
            'action' => 'importReturnReasons',
        ],
        // end return reasons import api
        [
            'method' => 'GET',
            'uri' => '/api/admins',
            'controller' => AdminController::class,
            'action' => 'index',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/admin/login',
            'controller' => AdminController::class,
            'action' => 'login',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/admin/validate-token',
            'controller' => AdminController::class,
            'action' => 'validateToken',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/admin/exchange-code',
            'controller' => AdminController::class,
            'action' => 'exchangeCode',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/admin/exchange-code',
            'controller' => AdminController::class,
            'action' => 'exchangeCode',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/admins/{id}',
            'controller' => AdminController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/admins',
            'controller' => AdminController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/admins/{id}',
            'controller' => AdminController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/admins/{id}',
            'controller' => AdminController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/admins/{admin_id}/upload',
            'controller' => AdminController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/admins/{admin_id}/delete',
            'controller' => AdminController::class,
            'action' => 'deleteImage',
        ],
        // admin import api
        [
            'method' => 'POST',
            'uri' => '/api/admins/import',
            'controller' => AdminController::class,
            'action' => 'importAdmins',
        ],
        // end admin import api
        [
            'method' => 'GET',
            'uri' => '/api/users',
            'controller' => UserController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/users/{id}',
            'controller' => UserController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/users',
            'controller' => UserController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/users/{id}',
            'controller' => UserController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/users/{id}',
            'controller' => UserController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/users/{user_id}/upload',
            'controller' => UserController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/users/{user_id}/delete',
            'controller' => UserController::class,
            'action' => 'deleteImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/customers-search',
            'controller' => UserController::class,
            'action' => 'customerSearch',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/users/import',
            'controller' => UserController::class,
            'action' => 'importUsers',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/create-request',
            'controller' => ServiceRequestController::class,
            'action' => 'create',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/account/create-request',
            'controller' => ServiceRequestController::class,
            'action' => 'accountCreateRequest',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/account/service-requests',
            'controller' => ServiceRequestController::class,
            'action' => 'serviceRequests',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/service-requests',
            'controller' => ServiceRequestController::class,
            'action' => 'serviceRequests',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/service-requests/search',
            'controller' => ServiceRequestController::class,
            'action' => 'searchServiceRequests',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/service-requests/{id}',
            'controller' => ServiceRequestController::class,
            'action' => 'updateServiceRequest',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/service-requests/{id}',
            'controller' => ServiceRequestController::class,
            'action' => 'getServiceRequestById',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/service-requests/{id}',
            'controller' => ServiceRequestController::class,
            'action' => 'deleteServiceRequest',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/user-groups',
            'controller' => UserGroupController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/user-groups/{id}',
            'controller' => UserGroupController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/user-groups',
            'controller' => UserGroupController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/user-groups/{id}',
            'controller' => UserGroupController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/user-groups/{id}',
            'controller' => UserGroupController::class,
            'action' => 'delete',
        ],
        [ // import user groups
            'method' => 'POST',
            'uri' => '/api/user-groups/import',
            'controller' => UserGroupController::class,
            'action' => 'importUserGroups',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/user-import',
            'controller' => UserController::class,
            'action' => 'import',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/user-export',
            'controller' => UserController::class,
            'action' => 'export',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/models',
            'controller' => ModelController::class,
            'action' => 'getModels',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/models',
            'controller' => ModelController::class,
            'action' => 'getFields',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/models/table-columns',
            'controller' => ModelController::class,
            'action' => 'getTableColumns',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/models/related-models',
            'controller' => ModelController::class,
            'action' => 'getRelatedModels',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/models/join-table-columns',
            'controller' => ModelController::class,
            'action' => 'getJoinedTableColumns',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/components',
            'controller' => ComponentController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/components/{id}',
            'controller' => ComponentController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/components',
            'controller' => ComponentController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/components/{id}',
            'controller' => ComponentController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/components/{id}',
            'controller' => ComponentController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/component-items',
            'controller' => ComponentController::class,
            'action' => 'indexItem',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/component-items/{id}',
            'controller' => ComponentController::class,
            'action' => 'showItem',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/component/add-items',
            'controller' => ComponentController::class,
            'action' => 'addComponentItem',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/component/update-items/{id}',
            'controller' => ComponentController::class,
            'action' => 'updateComponentItem',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/component/delete-items/{id}',
            'controller' => ComponentController::class,
            'action' => 'deleteItem',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/components/update-way-points',
            'controller' => ComponentController::class,
            'action' => 'updateWayPoints',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/components/{component_id}/upload',
            'controller' => ComponentController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/components/{component_id}/delete-by-path',
            'controller' => ComponentController::class,
            'action' => 'deleteByPath',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/types',
            'controller' => ListController::class,
            'action' => 'types',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/sites',
            'controller' => ListController::class,
            'action' => 'sites',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/manufacturers',
            'controller' => ListController::class,
            'action' => 'manufacturers',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/vendors',
            'controller' => ListController::class,
            'action' => 'vendors',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/product-tags',
            'controller' => ListController::class,
            'action' => 'productTags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/list/product-finishes',
            'controller' => ListController::class,
            'action' => 'productFinishes',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-tags',
            'controller' => TaxonomyController::class,
            'action' => 'getProductTags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-tags/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'showProductTag',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-tags',
            'controller' => TaxonomyController::class,
            'action' => 'createProductTag',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/products-categories',
            'controller' => TaxonomyController::class,
            'action' => 'createProductCategory',
        ],
        [
            'method' => 'put',
            'uri' => '/api/categories/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'updateProductCategory',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-tags/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'updateProductTag',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-tags/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'deleteProductTag',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-finishes',
            'controller' => TaxonomyController::class,
            'action' => 'getProductFinishes',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-finishes/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'showProductFinish',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-finishes',
            'controller' => TaxonomyController::class,
            'action' => 'createProductFinish',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-finishes/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'updateProductFinish',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-finishes/{id}',
            'controller' => TaxonomyController::class,
            'action' => 'deleteProductFinish',
        ],

        // upload taxonomy item image
        [
            'method' => 'POST',
            'uri' => '/api/categories/{taxonomy_item_id}/upload',
            'controller' => TaxonomyController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/categories/{taxonomy_item_id}/delete',
            'controller' => TaxonomyController::class,
            'action' => 'deleteImage',
        ],
        // --------------------------------------------------------------
        // Attribute setup-related routes (e.g., attribute, attribute_content, etc.)
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/attributes',
            'controller' => AttributeController::class,
            'action' => 'getAllAttributes',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/attributes/{id}',
            'controller' => AttributeController::class,
            'action' => 'getAllAttributeById',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/attributes',
            'controller' => AttributeController::class,
            'action' => 'create',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/attributes/{id}',
            'controller' => AttributeController::class,
            'action' => 'update',
        ],
        [  // delete
            'method' => 'POST',
            'uri' => '/api/attribute-delete/{id}',
            'controller' => AttributeController::class,
            'action' => 'delete',
        ],
        [  // multiple delete
            'method' => 'DELETE',
            'uri' => '/api/attributes/{id}',
            'controller' => AttributeController::class,
            'action' => 'deleteMultiple',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/attributes/import',
            'controller' => AttributeController::class,
            'action' => 'importAttributes',
        ],

        // --------------------------------------------------------------
        // Company setup-related routes
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/companies',
            'controller' => CompanyController::class,
            'action' => 'getAllCompanies',
        ],

        // [  // vendor search
        //     'method' => 'GET',
        //     'uri' => '/api/companies/search-vendors',
        //     'controller' => CompanyController::class,
        //     'action' => 'searchVendors',
        // ],

        [  // id data
            'method' => 'GET',
            'uri' => '/api/companies/{id}',
            'controller' => CompanyController::class,
            'action' => 'getCompanyById',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/companies',
            'controller' => CompanyController::class,
            'action' => 'create',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/companies/{id}',
            'controller' => CompanyController::class,
            'action' => 'update',
        ],
        [  // delete
            'method' => 'POST',
            'uri' => '/api/company-delete/{id}',
            'controller' => CompanyController::class,
            'action' => 'delete',
        ],
        [  // multiple delete
            'method' => 'DELETE',
            'uri' => '/api/companies/{id}',
            'controller' => CompanyController::class,
            'action' => 'deleteMultiple',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/companies/import',
            'controller' => CompanyController::class,
            'action' => 'importCompanies',
        ],
        // --------------------------------------------------------------
        // End of company setup routes
        // --------------------------------------------------------------
        // --------------------------------------------------------------
        // Customer setup-related routes
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/customers',
            'controller' => CustomerController::class,
            'action' => 'getAllCustomers',
        ],
        [  // list
            'method' => 'GET',
            'uri' => '/api/customers/search',
            'controller' => CustomerController::class,
            'action' => 'searchCustomers',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/customers/{id}',
            'controller' => CustomerController::class,
            'action' => 'getCustomerById',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/customers',
            'controller' => CustomerController::class,
            'action' => 'create',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/customers/{id}',
            'controller' => CustomerController::class,
            'action' => 'update',
        ],
        [  // delete
            'method' => 'POST',
            'uri' => '/api/customer-delete/{id}',
            'controller' => CustomerController::class,
            'action' => 'delete',
        ],
        [  // multiple delete
            'method' => 'DELETE',
            'uri' => '/api/customers/{id}',
            'controller' => CustomerController::class,
            'action' => 'deleteMultiple',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/customers/import',
            'controller' => CustomerController::class,
            'action' => 'importCustomers',
        ],
        [  // check existing customer
            'method' => 'POST',
            'uri' => '/api/check-existing-customer',
            'controller' => CustomerController::class,
            'action' => 'checkExistingCustomer',
        ],
        [  // send email verification
            'method' => 'POST',
            'uri' => '/api/send-email-verification',
            'controller' => CustomerController::class,
            'action' => 'sendEmailVerification',
        ],
        [  // verify email
            'method' => 'POST',
            'uri' => '/api/verify-email',
            'controller' => CustomerController::class,
            'action' => 'verifyEmail',
        ],
        [  // verify email
            'method' => 'POST',
            'uri' => '/api/verify-email-authenticate-and-create-pinboard',
            'controller' => CustomerController::class,
            'action' => 'verifyEmailAthenticateAndCreatePinboard',
        ],
        [  // pinboard
            'method' => 'GET',
            'uri' => '/api/customer/pinboard',
            'controller' => CustomerController::class,
            'action' => 'pinboard',
        ],
        // --------------------------------------------------------------
        // End of customer setup routes
        // --------------------------------------------------------------

        /**
         * Attribute Group Apis
         */
        [  // group list
            'method' => 'GET',
            'uri' => '/api/attribute-groups',
            'controller' => AttributeGroupController::class,
            'action' => 'getAllAttributeGroups',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/attribute-groups/{id}',
            'controller' => AttributeGroupController::class,
            'action' => 'getAllAttributeGroupById',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/attribute-groups',
            'controller' => AttributeGroupController::class,
            'action' => 'create',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/attribute-groups/{id}',
            'controller' => AttributeGroupController::class,
            'action' => 'update',
        ],
        [  // delete
            'method' => 'POST',
            'uri' => '/api/attribute/{attribute_id}',
            'controller' => AttributeGroupController::class,
            'action' => 'delete',
        ],
        [  // multiple delete
            'method' => 'DELETE',
            'uri' => '/api/attribute-groups/{id}',
            'controller' => AttributeGroupController::class,
            'action' => 'delete',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/attribute-groups/import-attribute-groups',
            'controller' => AttributeGroupController::class,
            'action' => 'importAttributeGroups',
        ],
        // --------------------------------------------------------------
        // End of attribute setup routes
        // --------------------------------------------------------------
        // --------------------------------------------------------------
        // option setup-related routes (e.g., option, option_content, etc.)
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/options',
            'controller' => OptionController::class,
            'action' => 'getAllOptions',
        ],
        [  // list
            'method' => 'GET',
            'uri' => '/api/option-types',
            'controller' => OptionController::class,
            'action' => 'getAllOptionTypes',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/options/{id}',
            'controller' => OptionController::class,
            'action' => 'getOptionById',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/options',
            'controller' => OptionController::class,
            'action' => 'create',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/options/{id}',
            'controller' => OptionController::class,
            'action' => 'update',
        ],
        [  // delete
            'method' => 'POST',
            'uri' => '/api/options-delete/{id}',
            'controller' => OptionController::class,
            'action' => 'deleteOption',
        ],
        // [  // multiple delete
        //     'method' => 'DELETE',
        //     'uri' => '/api/options/{id}',
        //     'controller' => OptionController::class,
        //     'action' => 'deleteMultiple',
        // ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/options/import',
            'controller' => OptionController::class,
            'action' => 'importOptions',
        ],
        [  // product options import csv
            'method' => 'GET',
            'uri' => '/api/product-options/list',
            'controller' => ProductOptionController::class,
            'action' => 'getProductOptions',
        ],
        [  // product options import csv
            'method' => 'GET',
            'uri' => '/api/product-options/search',
            'controller' => ProductOptionController::class,
            'action' => 'searchProductOptions',
        ],
        [  // product options id data
            'method' => 'GET',
            'uri' => '/api/product-options/detail/{id}',
            'controller' => ProductOptionController::class,
            'action' => 'getProductOptionById',
        ],
        [  // product options add
            'method' => 'POST',
            'uri' => '/api/product-options/create',
            'controller' => ProductOptionController::class,
            'action' => 'createProductOption',
        ],
        [  // product options edit
            'method' => 'PUT',
            'uri' => '/api/product-options/update/{id}',
            'controller' => ProductOptionController::class,
            'action' => 'updateProductOption',
        ],
        [  // product options delete
            'method' => 'DELETE',
            'uri' => '/api/product-options/delete/{id}',
            'controller' => ProductOptionController::class,
            'action' => 'deleteProductOption',
        ],
        [  // product options import csv
            'method' => 'POST',
            'uri' => '/api/product-options/import',
            'controller' => ProductOptionController::class,
            'action' => 'importProductOptions',
        ],

        // --------------------------------------------------------------
        // type setup-related routes (e.g., type, type_content, etc.)
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/types',
            'controller' => TypeController::class,
            'action' => 'getTypes',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/types',
            'controller' => TypeController::class,
            'action' => 'createType',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/types/{id}',
            'controller' => TypeController::class,
            'action' => 'getTypeById',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/types/{id}',
            'controller' => TypeController::class,
            'action' => 'updateType',
        ],
        [  // delete
            'method' => 'delete',
            'uri' => '/api/types/{id}',
            'controller' => TypeController::class,
            'action' => 'deleteType',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/type-import',
            'controller' => TypeController::class,
            'action' => 'importTypes',
        ],
        // --------------------------------------------------------------
        // End of type setup routes
        // --------------------------------------------------------------
        // --------------------------------------------------------------
        // variant setup-related routes (e.g., variant, variant_content, etc.)
        // --------------------------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/variants',
            'controller' => ProductVariantController::class,
            'action' => 'getVariants',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/variants/search',
            'controller' => ProductVariantController::class,
            'action' => 'searchVariants',
        ],
        [  // add 
            'method' => 'POST',
            'uri' => '/api/variants',
            'controller' => ProductVariantController::class,
            'action' => 'createVariant',
        ],
        [  // id data
            'method' => 'GET',
            'uri' => '/api/variants/{id}',
            'controller' => ProductVariantController::class,
            'action' => 'getVariantById',
        ],
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/variants/{id}',
            'controller' => ProductVariantController::class,
            'action' => 'updateVariant',
        ],
        [  // upload
            'method' => 'POST',
            'uri' => '/api/variants/{id}/upload',
            'controller' => ProductVariantController::class,
            'action' => 'upload',
        ],
        [  // upload
            'method' => 'POST',
            'uri' => '/api/variants/{product_option_id}/product-option-upload-image',
            'controller' => ProductVariantController::class,
            'action' => 'uploadProductOptionImage',
        ],
        [  // delete
            'method' => 'DELETE',
            'uri' => '/api/variants/{id}',
            'controller' => ProductVariantController::class,
            'action' => 'deleteVariant',
        ],
        [  // variant image delete
            'method' => 'DELETE',
            'uri' => '/api/variants/delete-image/{product_variant_id}',
            'controller' => ProductVariantController::class,
            'action' => 'deleteVariantImage',
        ],
        [  // variant image delete
            'method' => 'DELETE',
            'uri' => '/api/variants/delete-option-image/{product_variant_option_id}',
            'controller' => ProductVariantController::class,
            'action' => 'deleteVariantOptionImage',
        ],
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/variants/import',
            'controller' => ProductVariantController::class,
            'action' => 'importVariants',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/item-option-vairant-search',
            'controller' => ProductVariantController::class,
            'action' => 'searchItemOptionVariants',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/item-option-group-search',
            'controller' => ProductOptionGroupController::class,
            'action' => 'searchItemOptionGroupsByQuery',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/item-option-search',
            'controller' => ProductOptionController::class,
            'action' => 'searchItemOptionsByQuery',
        ],
        // --------------------------------------------------------------
        // End of variant setup routes
        // --------------------------------------------------------------
        [
            'method' => 'GET',
            'uri' => '/api/list/post-tags',
            'controller' => ListController::class,
            'action' => 'postTags',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/quotes',
            'controller' => QuoteController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/quotes/{id}',
            'controller' => QuoteController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/quotes',
            'controller' => QuoteController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/quotes/{id}',
            'controller' => QuoteController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/quotes/{id}',
            'controller' => QuoteController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-quotes',
            'controller' => QuoteController::class,
            'action' => 'importQuotes',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/quote-acceptance',
            'controller' => QuoteController::class,
            'action' => 'getQuoteAcceptance',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/quote-items',
            'controller' => QuoteItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/quote-items/{id}',
            'controller' => QuoteItemController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/quotes/add-items',
            'controller' => QuoteItemController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/quote-items/{id}',
            'controller' => QuoteItemController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/quote-items/{id}',
            'controller' => QuoteItemController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/items-product-list',
            'controller' => QuoteItemController::class,
            'action' => 'productList',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/orders',
            'controller' => OrderController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/orders/{id}',
            'controller' => OrderController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/orders',
            'controller' => OrderController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/orders/{id}',
            'controller' => OrderController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/orders/{id}',
            'controller' => OrderController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/order-items',
            'controller' => OrderItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/order-items/{id}',
            'controller' => OrderItemController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/order-items',
            'controller' => OrderItemController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/order-items/{id}',
            'controller' => OrderItemController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/order-items/{id}',
            'controller' => OrderItemController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/order-tracking',
            'controller' => OrderController::class,
            'action' => 'getOrderTracking',
        ],
        [ // list jobs
            'method' => 'GET',
            'uri' => '/api/jobs',
            'controller' => JobController::class,
            'action' => 'jobList',
        ],
        [ // job details // or find by id
            'method' => 'GET',
            'uri' => '/api/jobs/{id}',
            'controller' => JobController::class,
            'action' => 'jobDetails',
        ],
        // job create
        [
            'method' => 'POST',
            'uri' => '/api/jobs',
            'controller' => JobController::class,
            'action' => 'create',
        ],
        // job update
        [
            'method' => 'PUT',
            'uri' => '/api/jobs/{id}',
            'controller' => JobController::class,
            'action' => 'update',
        ],
        // job delete
        [
            'method' => 'DELETE',
            'uri' => '/api/jobs/delete/{id}',
            'controller' => JobController::class,
            'action' => 'delete',
        ],
        // import jobs
        [
            'method' => 'POST',
            'uri' => '/api/jobs/import-jobs',
            'controller' => JobController::class,
            'action' => 'importJobs',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/coupons',
            'controller' => CouponController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/coupons/{id}',
            'controller' => CouponController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/coupons',
            'controller' => CouponController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/coupons/{id}',
            'controller' => CouponController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/coupons/{id}',
            'controller' => CouponController::class,
            'action' => 'deleteCoupon',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/import-coupons',
            'controller' => CouponController::class,
            'action' => 'importCoupons',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/coupon-products',
            'controller' => CouponItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/coupon-products/{id}',
            'controller' => CouponItemController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/coupon-products',
            'controller' => CouponItemController::class,
            'action' => 'create',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/items-product-list/{id}',
            'controller' => CouponItemController::class,
            'action' => 'productList',
        ],


        // ============== statr product discount route ===========
        [
            'method' => 'GET',
            'uri' => '/api/product-discounts',
            'controller' => ProductDiscountController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/product-discounts/{id}',
            'controller' => ProductDiscountController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/product-discounts',
            'controller' => ProductDiscountController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/product-discounts/{id}',
            'controller' => ProductDiscountController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/product-discounts/{id}',
            'controller' => ProductDiscountController::class,
            'action' => 'delete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/import-product-discounts',
            'controller' => ProductDiscountController::class,
            'action' => 'importProductDiscounts',
        ],
        // ============== end product discount route ===========
        [
            'method' => 'GET',
            'uri' => '/api/pinboards',
            'controller' => PinboardController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pinboards/temporary-pinboards',
            'controller' => PinboardController::class,
            'action' => 'temporaryPinboardIndex',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pinboards/{pinboard_id}',
            'controller' => PinboardController::class,
            'action' => 'show',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pinboards/{id}/item',
            'controller' => PinboardController::class,
            'action' => 'showPinboardItem',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/status/{id}/{pinboard_status_id}',
            'controller' => PinboardController::class,
            'action' => 'updatePinboardStatus',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards',
            'controller' => PinboardController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/pinboards/{id}',
            'controller' => PinboardController::class,
            'action' => 'update',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/pinboards/{id}',
            'controller' => PinboardController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/pinboards-temporary/{id}',
            'controller' => PinboardController::class,
            'action' => 'deleteTemporaryPinboard',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pinboard-items',
            'controller' => PinboardItemController::class,
            'action' => 'index',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/pinboard-items/{id}',
            'controller' => PinboardItemController::class,
            'action' => 'show',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items',
            'controller' => PinboardItemController::class,
            'action' => 'create',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/pinboard-items/{id}',
            'controller' => PinboardItemController::class,
            'action' => 'update',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/update-project-title',
            'controller' => PinboardController::class,
            'action' => 'updateProjectTitle',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/update-status',
            'controller' => PinboardController::class,
            'action' => 'updateStatus',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboards/import',
            'controller' => PinboardController::class,
            'action' => 'importPinboards',
        ],
        [ // fetch pinboard list
            'method' => 'GET',
            'uri' => '/api/fetch-pinboard-list/{customer_id}',
            'controller' => PinboardController::class,
            'action' => 'getPinboardList',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/pinboard-items/{id}',
            'controller' => PinboardItemController::class,
            'action' => 'delete',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/get-project-items/{user_id}/{pinboard_id}',
            'controller' => PinboardItemController::class,
            'action' => 'getProjectItemsByPinboardId',
        ],
        // [
        //     'method' => 'GET',
        //     'uri' => '/api/user-pinboard-items/{user_id}',
        //     'controller' => PinboardItemController::class,
        //     'action' => 'getPinboardItems',
        // ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items/reorder',
            'controller' => PinboardItemController::class,
            'action' => 'reorderPinboardItems',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items/add-to-pinboard',
            'controller' => PinboardItemController::class,
            'action' => 'addToPinboard',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items/add-to-pinboard-images',
            'controller' => PinboardItemController::class,
            'action' => 'addToPinboardItemImages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items/add-to-pinboard-configurator',
            'controller' => PinboardItemController::class,
            'action' => 'addToPinboardProductConfigurator',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/pinboard-items/update-comment-description',
            'controller' => PinboardItemController::class,
            'action' => 'updateCommentDescription',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/post-pagination',
            'controller' => PostController::class,
            'action' => 'getPaginationData',
        ],
        [ // new route for section details
            'method' => 'GET',
            'uri' => '/api/showroom/{showroom_slug}/sections/{slug}',
            'controller' => ShowroomController::class,
            'action' => 'sectonDetails',
        ],
        // ------- start showroom api ---------------
        [ // showroom list
            'method' => 'GET',
            'uri' => '/api/showrooms',
            'controller' => ShowroomController::class,
            'action' => 'list',
        ],
        [ // showroom add
            'method' => 'POST',
            'uri' => '/api/showrooms',
            'controller' => ShowroomController::class,
            'action' => 'store',
        ],
        [ // showroom show single data
            'method' => 'GET',
            'uri' => '/api/showrooms/{id}',
            'controller' => ShowroomController::class,
            'action' => 'show',
        ],
        [ // showroom update
            'method' => 'PUT',
            'uri' => '/api/showrooms/{id}',
            'controller' => ShowroomController::class,
            'action' => 'update',
        ],
        [ // showroom store/create
            'method' => 'POST',
            'uri' => '/api/showrooms/{id}',
            'controller' => ShowroomController::class,
            'action' => 'delete',
        ],

        [
            'method' => 'POST',
            'uri' => '/api/showroom/update-way-points',
            'controller' => ShowroomController::class,
            'action' => 'updateWayPoints',
        ],


        [
            'method' => 'DELETE',
            'uri' => '/api/showrooms/{showroom_id}/delete-image/{property}',
            'controller' => ShowroomController::class,
            'action' => 'deleteImageByProperty',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/showrooms/{id}/sections',
            'controller' => ShowroomController::class,
            'action' => 'showroomSectionLists',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showrooms/{id}/sections', // SECTION
            'controller' => ShowroomController::class,
            'action' => 'addSection',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/get-showroom', // get showroom for pinboard booking
            'controller' => ShowroomController::class,
            'action' => 'getShowroomForPinboard',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/sections/{id}', // get section by id
            'controller' => ShowroomController::class,
            'action' => 'sectonDetailById',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/sections/{id}',
            'controller' => ShowroomController::class,
            'action' => 'updateSection',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/sections/{id}',
            'controller' => ShowroomController::class,
            'action' => 'deleteSection',
        ],
        [ // section image
            'method' => 'GET',
            'uri' => '/api/sections/{id}/images',
            'controller' => ShowroomController::class,
            'action' => 'sectionImages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/sections/{id}/images',
            'controller' => ShowroomController::class,
            'action' => 'addSectionImage',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/sections/{id}/images/{imageId}',
            'controller' => ShowroomController::class,
            'action' => 'updateSectionImage',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/sections/{id}/images/{imageId}',
            'controller' => ShowroomController::class,
            'action' => 'deleteSectionImage',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/sections/{id}/products',
            'controller' => ShowroomController::class,
            'action' => 'sectionProducts',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/sections/{id}/products',
            'controller' => ShowroomController::class,
            'action' => 'addSectionProduct',
        ],

        [
            'method' => 'DELETE',
            'uri' => '/api/sections/{id}/section-product/{project_section_products_id}',
            'controller' => ShowroomController::class,
            'action' => 'deleteSectionProductById',
        ],


        [
            'method' => 'POST',
            'uri' => '/api/showrooms-files-upload/{context}/{id}/{showroom_id}',
            'controller' => ShowroomController::class,
            'action' => 'upload',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showrooms-files-upload/{id}/{showroom_id}',
            'controller' => ShowroomController::class,
            'action' => 'addSectionImage',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/showroom/showroom-details',
            'controller' => ShowroomController::class,
            'action' => 'showroomSectionProductImage',
        ],
        [ // import showroom related sections, images and products
            'method' => 'POST',
            'uri' => '/api/section-import',
            'controller' => ShowroomController::class,
            'action' => 'importSections',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/section-image-import',
            'controller' => ShowroomController::class,
            'action' => 'importSectionsImages',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/section-product-import',
            'controller' => ShowroomController::class,
            'action' => 'importSectionsProducts',
        ],
        // start showroom contact
        [
            'method' => 'GET',
            'uri' => '/api/showroom-contacts',
            'controller' => ShowroomController::class,
            'action' => 'getShowroomContactList',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}',
            'controller' => ShowroomController::class,
            'action' => 'getShowroomContactById',
        ],
        [
            'method' => 'DELETE',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}',
            'controller' => ShowroomController::class,
            'action' => 'deleteShowroomContactById',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showroom-contacts',
            'controller' => ShowroomController::class,
            'action' => 'createShowroomContact',
        ],
        [
            'method' => 'PUT',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}',
            'controller' => ShowroomController::class,
            'action' => 'updateShowroomContactById',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showroom-contacts/import',
            'controller' => ShowroomController::class,
            'action' => 'importShowroomContact',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}/upload',
            'controller' => ShowroomController::class,
            'action' => 'uploadShowroomContactImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}/delete',
            'controller' => ShowroomController::class,
            'action' => 'deleteShowroomContactImage',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}/update-slot',
            'controller' => ShowroomController::class,
            'action' => 'updateSlot',
        ],
        [
            'method' => 'GET',
            'uri' => '/api/showroom-contacts/{showroom_contact_id}/slots',
            'controller' => ShowroomController::class,
            'action' => 'getSlot',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/members-by-showroom-id/{showroom_id}',
            'controller' => ShowroomController::class,
            'action' => 'getMembersByShowroomId',
        ],
        // end showroom contact
        // ----------- end showroom api --------------
        // product option group setup-related routes
        // -------------------------------------------
        [  // list
            'method' => 'GET',
            'uri' => '/api/product-option-groups',
            'controller' => ProductOptionGroupController::class,
            'action' => 'getProductOptionGroups',
        ],
        [  // list
            'method' => 'GET',
            'uri' => '/api/product-option-groups/list',
            'controller' => ProductOptionGroupController::class,
            'action' => 'getProductOptionGroups',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/product-option-groups/search',
            'controller' => ProductOptionGroupController::class,
            'action' => 'searchProductOptionGroups',
        ],
        [  // search
            'method' => 'GET',
            'uri' => '/api/item-option-groups/search',
            'controller' => ProductOptionGroupController::class,
            'action' => 'searchItemOptionGroups',
        ],
        // --------------------------------------------------------------
        [  // id data
            'method' => 'GET',
            'uri' => '/api/product-option-groups/{id}',
            'controller' => ProductOptionGroupController::class,
            'action' => 'getProductOptionGroupById',
        ],
        // --------------------------------------------------------------
        [  // add 
            'method' => 'POST',
            'uri' => '/api/product-option-groups',
            'controller' => ProductOptionGroupController::class,
            'action' => 'createProductOptionGroup',
        ],
        // --------------------------------------------------------------
        [  // edit
            'method' => 'PUT',
            'uri' => '/api/product-option-groups/{id}',
            'controller' => ProductOptionGroupController::class,
            'action' => 'updateProductOptionGroup',
        ],
        // --------------------------------------------------------------
        [  // delete
            'method' => 'Delete',
            'uri' => '/api/product-option-groups/{id}',
            'controller' => ProductOptionGroupController::class,
            'action' => 'deleteProductOptionGroup',
        ],
        // --------------------------------------------------------------
        [  // import csv
            'method' => 'POST',
            'uri' => '/api/product-option-groups/import',
            'controller' => ProductOptionGroupController::class,
            'action' => 'importProductOptionGroups',
        ],
        // --------------------------------------------------------------
        // End of product option group setup routes
        // --------------------------------------------------------------

        // ----------- start service request routes --------------
        [
            'method' => 'POST',
            'uri' => '/api/booking-email-service-requests',
            'controller' => ServiceRequestController::class,
            'action' => 'create',
        ],
        [
            'method' => 'POST',
            'uri' => '/api/contact-sales-get-in-touch',
            'controller' => ServiceRequestController::class,
            'action' => 'contactSalesGetInTouch',
        ],
        
        // ----------- end service request routes --------------
    ];
    public static function registerRoutes()
    {
        Event::on(Route::class, 'add-routes', __CLASS__, function ($routes) {
            return [array_merge($routes, self::$routes)];
        }, 20);
    }
}
