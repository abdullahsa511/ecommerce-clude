<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use Exception;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\AuthService;
use App\Core\Services\RecaptchaService;
use App\Core\Services\CsrfService;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Order\OrderRepositoryInterface;
use function App\Core\System\utils\env;
use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Repositories\Site\SiteRepositoryInterface;

/**
 * HomeController handles the home page.
 */
class AccountController extends Controller
{
    private CsrfService $csrfService;
    private AuthService $authService;
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    private OrderRepositoryInterface $orderRepository;
    private RecaptchaService $recaptchaService;
    
    public function __construct(
        CsrfService $csrfService,
        AuthService $authService,
        CustomerRepositoryInterface $customerRepository,
        UserRepositoryInterface $userRepository,
        OrderRepositoryInterface $orderRepository,
        SiteRepositoryInterface $siteRepository,
        ?RecaptchaService $recaptchaService = null
    ) {
        parent::__construct($siteRepository);
        $this->csrfService = $csrfService;
        $this->authService = $authService;
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->recaptchaService = $recaptchaService ?? new RecaptchaService();
    }

    public function profile(): Response
    {
        $authUser = $this->authUser();
        if(!$authUser){
            return $this->redirect('/login');
        }

        $user = $this->userRepository->find($authUser->user_id);
        if (!$user || (int) $user->user_id !== (int) $authUser->user_id) {
            return $this->redirect('/403');
        }

        $csrfToken = $this->csrfService->getToken();
        return $this->renderResponse('profile', [
            'page' => 'profile', 
            'nonce' => $csrfToken, 
            'is_admin' => $this->isAdmin(), 
            'user_id' => $user->user_id,
            'title' => 'Account | Krost Business Furniture',
        ]);
    }

    private function profileViewData(array $extra = []): array
    {
        return array_merge([
            'recaptcha_site_key' => $this->recaptchaService->getSiteKey(),
            'recaptcha_action' => $this->recaptchaService->getContactAction(),
        ], $extra);
    }

    public function updateProfile(Request $request): Response
    {
        // Authentication check
        $authUser = $this->authUser();
        if (!$authUser) {
            return $this->redirect('/login');
        }
    
        // Verify user
        $user = $this->userRepository->find($authUser->user_id);
        if (!$user || (int) $user->user_id !== (int) $authUser->user_id) {
            return $this->redirect('/403');
        }
    
        $data = $request->all();
        $data['user_id'] = $authUser->user_id;
    
        // CSRF validation
        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            return $this->renderProfileWithErrors(
                ['nonce' => ['Invalid CSRF token']],
                $data
            );
        }
    
        // Get client IP
        $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    
        if (is_string($remoteIp) && str_contains($remoteIp, ',')) {
            $remoteIp = trim(explode(',', $remoteIp)[0]);
        }
    
        /*
        // reCAPTCHA validation
        $recaptchaResult = $this->recaptchaService->verify(
            (string) ($data['g-recaptcha-response'] ?? ''),
            is_string($remoteIp) ? $remoteIp : null,
            $this->recaptchaService->getContactAction()
        );
    
        if (!$recaptchaResult['ok']) {
            return $this->returnProfileFormWithErrors(
                ['recaptcha' => $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'],
                $data
            );
        }
        */
        // Phone validation
        // $phone = preg_replace('/\s+/', '', (string)($data['phone'] ?? ''));
        // if ($phone !== '') {
        //     $phonePattern = '/^(?:\+61|0)(?:[2378]\d{8}|4\d{8})$/';

        //     if (!preg_match($phonePattern, $phone)) {
        //         $errors['phone'] = 'Please enter a valid Australian phone number.';
        //     }
        // }

        // // State validation
        // $state = strtoupper(trim((string)($data['state'] ?? '')));

        // // Postcode validation
        // $postcode = trim((string)($data['postcode'] ?? ''));

        // if ($postcode) {

        //     if (!preg_match('/^\d{4}$/', $postcode)) {
        //         $errors['postcode'] = 'Australian postcode must be 4 digits.';
        //     } elseif ($state !== '' && !$this->isValidAustralianPostcodeForState($postcode, $state)) {
        //         $errors['postcode'] = sprintf(
        //             'Postcode %s does not belong to %s.',
        //             $postcode,
        //             $state
        //         );
        //     }

        // }

        // if (!empty($errors)) {
        //     return $this->returnProfileFormWithErrors($errors, $data);
        // }
    
        try {
            $this->userRepository->updateUserProfile($data);
            return $this->redirect('/account/profile');
    
        } catch (ValidationException $e) {
    
            $errors = [];
            foreach ($e->getErrors() as $field => $messages) {
                $errors[$field] = implode(PHP_EOL, $messages);
            }
    
            return $this->returnProfileFormWithErrors($errors, $data);
    
        } catch (\Exception $e) {
    
            return $this->returnProfileFormWithErrors(
                ['general' => $e->getMessage()],
                $data
            );
        }
    }

    private function isValidAustralianPostcodeForState(string $postcode, string $state): bool
    {
        $postcode = (int) $postcode;

        $ranges = [
            'NSW' => [
                [1000, 1999],
                [2000, 2599],
                [2619, 2899],
                [2921, 2999],
            ],
            'ACT' => [
                [200, 299],
                [2600, 2618],
                [2900, 2920],
            ],
            'VIC' => [
                [3000, 3999],
                [8000, 8999],
            ],
            'QLD' => [
                [4000, 4999],
                [9000, 9999],
            ],
            'SA' => [
                [5000, 5799],
                [5800, 5999],
            ],
            'WA' => [
                [6000, 6797],
                [6800, 6999],
            ],
            'TAS' => [
                [7000, 7799],
                [7800, 7999],
            ],
            'NT' => [
                [800, 999],
            ],
        ];

        foreach ($ranges[$state] ?? [] as [$min, $max]) {
            if ($postcode >= $min && $postcode <= $max) {
                return true;
            }
        }

        return false;
    }

    private function returnProfileFormWithErrors(array $errors, array $data): Response
    {
        $csrfToken = $this->csrfService->getToken();

        return $this->renderResponse('profile', $this->profileViewData([
            'page' => 'profile',
            'nonce' => $csrfToken,
            'errors' => $errors,
            'data' => $data,
        ]));
    }

    public function recentOrdersRedirect(): Response
    {
        $customer_id = 1;
        return $this->redirect('/account/recent-orders/'.$customer_id);
    }

    public function recentOrders(Request $request, ?string $customer_id = null): Response
    {

        $authUser = $this->authUser();
        if(!$authUser){
            return $this->redirect('/login');
        }

        $user = $this->userRepository->find($authUser->user_id);
        if($authUser && $user->user_id !== $authUser->user_id){
            return $this->redirect('/403');
        }


        $queryParams = $request->query();
        $sort = isset($queryParams['sort']) ? $queryParams['sort'] : 'created_at';
        $order = isset($queryParams['order']) ? $queryParams['order'] : 'desc';
        // If a customer id wasn't passed as a route parameter, try request input/query or session
        // if ($customer_id === null) {
        //     $customer_id = $request->input('customer_id') ?? ($request->query()['customer_id'] ?? null);
        // }

        // // fallback default customer id
        // if (!$customer_id) {
        //     $customer_id = '1';
        // }

        return $this->renderResponse('recent-orders', ['user_id' => $user->user_id, 'sort' => $sort, 'order' => $order, 'title' => 'Account | Krost Business Furniture']);
    }

    public function showOrder(Request $request, string $uuid): Response
    {
        $user = $this->authService->getAuthUser();
        if (!$user) {
            return $this->redirect('/login');
        }

        $order = $this->orderRepository->getOrderByUuid($uuid);
        if (empty($order)) {
            return $this->redirect('/404');
        }

        if ($user->user_id !== $order['user_id']) {
            return $this->redirect('/403');
        }

        return $this->renderResponse('show-order', ['uuid' => $uuid, 'title' => 'Account | Krost Business Furniture']);
    }

    public function activeQuotesRedirect(): Response
    {
        $customer_id = 1;
        return $this->redirect('/account/active-quotes/'.$customer_id);
    }

    public function activeQuotes(Request $request, ?string $customer_id = null): Response
    {

         // $isLoggedIn = $this->authService->isLoggedIn();
        // $isLoggedIn = $this->authService->getAuthUser();
        $authUser = $this->authUser();
        if(!$authUser){
            return $this->redirect('/login');
        }

        $user = $this->userRepository->find($authUser->user_id);
        if($authUser && $user->user_id !== $authUser->user_id){
            return $this->redirect('/403');
        }




        $queryParams = $request->query();
        $sort = isset($queryParams['sort']) ? $queryParams['sort'] : 'created_at';
        $order = isset($queryParams['order']) ? $queryParams['order'] : 'desc';
        // If a customer id wasn't passed as a route parameter, try request input/query or session
        // if ($customer_id === null) {
        //     $customer_id = $request->input('customer_id') ?? ($request->query()['customer_id'] ?? null);
        // }

        // // fallback default customer id
        // if (!$customer_id) {
        //     $customer_id = '1';
        // }

        return $this->renderResponse('active-quotes', ['user_id' => $user->user_id, 'sort' => $sort, 'order' => $order, 'title' => 'Account | Krost Business Furniture']);
    }

    public function showQuote(Request $request, string $uuid): Response
    {
        return $this->renderResponse('show-quote', ['uuid' => $uuid, 'title' => 'Account | Krost Business Furniture']);
    }

    public function designResourceRedirect(): Response
    {
        return $this->redirect('/resources/models');
    }
    
    public function designResource(Request $request, string $resource): Response
    {

        $currentUrl = env('APP_URL') . '/resources';
        $queryParams = $request->query();
        $params = [];
        $params['per_page'] = (int) ($queryParams['per_page'] ?? 60);
        $params['current_page'] = (int) ($queryParams['current_page'] ?? 0);
        $params['offset'] = 0;
        $params['context'] = $queryParams['context'] ?? null;
        $params['category'] = $queryParams['category'] ?? null;
        $params['model_id'] = $queryParams['model_id'] ?? null;
        $params['model_name'] = $queryParams['model_name'] ?? null;
        $params['slug'] = $resource;
        $params['is_admin'] = $this->isAdmin();
        $params['title'] = "Resources | Krost Business Furniture";
        $imageUrl = $params['og_image'] = env('APP_URL') . '/img/bg/Krost_Business_Furniture_2026.png';
        $params['metaData'] = [
            'meta_title' =>  'Krost Resources',
            'meta_description' => 'Krost Business Furniture - Australian commercial furniture manufacturer since 1989. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story',
            'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
        ];     
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Resources',
            'name' => "Resources | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Krost Business Furniture - Australian commercial furniture manufacturer since 1989. Sydney, Melbourne & Brisbane showrooms. ISO certified. Explore our story',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        $params['product_schema'] = $productSchema;

        if($resource == 'images'){
            $params['canonical'] = $currentUrl . '/images';
            $params['url'] = $currentUrl . '/images';
            $params['type'] = 'Resources | Images';
            return $this->renderResponse('resource-images', $params);
        }else if($resource == 'documents'){
            $params['canonical'] = $currentUrl . '/documents';
            $params['url'] = $currentUrl . '/documents';
            $params['type'] = 'Resources | Documents';
            return $this->renderResponse('resource-documents', $params);
        }else if($resource == 'finishes'){
            $params['canonical'] = $currentUrl . '/finishes';
            $params['url'] = $currentUrl . '/finishes';
            $params['type'] = 'Resources | Finishes';
            return $this->renderResponse('resource-finishes', $params);
        }else if($resource == 'textiles'){
            $params['canonical'] = $currentUrl . '/textiles';
            $params['url'] = $currentUrl . '/textiles';
            $params['type'] = 'Resources | Textiles';
            return $this->renderResponse('resource-textiles', $params);
        }else if($resource == 'models'){
            $params['canonical'] = $currentUrl . '/models';
            $params['url'] = $currentUrl . '/models';
            $params['type'] = 'Resources | CAD/Revit Models';
            return $this->renderResponse('resource-models', $params);
        }else {
            return $this->notFound();
        }
    }

    public function deliveryInstall(Request $request): Response
    {

        $user = $this->authService->getAuthUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        $user_id = $user->user_id;
        $customer = $this->customerRepository->findByUserId($user_id);
        if (!$customer) {
            return $this->redirect('/404');
        }
        $customer_id = $customer['customer_id'];
        $queryParams = $request->query();
        $sort = isset($queryParams['sort']) ? $queryParams['sort'] : 'created_at';
        $order = isset($queryParams['order']) ? $queryParams['order'] : 'desc';
        // $customer_id = 16;
        return $this->renderResponse('delivery-install', ['customer_id' => $customer_id, 'sort' => $sort, 'order' => $order, 'title' => 'Account | Krost Business Furniture']);
    }

    public function upcomingAppointment(): Response
    {
        return $this->renderResponse('upcoming-appointment');
    }

    public function pinboard(Request $request): Response
    {
        $success = $request->query('success') ?? null;
        $message = $request->query('message') ?? null;
        $customer_id = $request->query('customer_id') ?? 0;
        return $this->renderResponse('pinboard', ['customer_id' => $customer_id, 'success' => $success, 'message' => $message, 'title' => 'Account | Krost Business Furniture']); // pinboard is pinboard.html
    }

    public function pinboardDetail(Request $request, string $pinboard_id): Response
    {
        return $this->renderResponse('pinboard-details', ['pinboard_id' => $pinboard_id]);
    }

    public function virtualPinboard(): Response
    {
        $customer_id = 16;
        $pinboard_id = 2;
        return $this->renderResponse('virtual-pinboard', ['customer_id'=> $customer_id, 'pinboard_id' => $pinboard_id]);
    }

    public function createRequest(): Response
    {
        $user = $this->authService->getAuthUser();
        if (!$user) {
            return $this->redirect('/login');
        }
        return $this->renderResponse('create-request', [
            'title' => 'Account | Krost Business Furniture',
            'recaptcha_site_key' => $this->recaptchaService->getSiteKey(),
            'recaptcha_action' => $this->recaptchaService->getServiceAction(),
        ]);
    }

    public function showTrackOrdersForm(): Response
    {
        $authUser = $this->authUser();
        if(!$authUser){
            return $this->redirect('/login');
        }

        $user = $this->userRepository->find($authUser->user_id);
        if($authUser && $user->user_id !== $authUser->user_id){
            return $this->redirect('/403');
        }

        return $this->renderResponse('track-order', ['title' => 'Account | Krost Business Furniture']);
    }

    public function trackOrders(Request $request): Response
    {
        $order_id = $request->input('order_id');
        return $this->renderResponse('track-order', ['order_id' => $order_id, 'title' => 'Account | Krost Business Furniture']);
    }
}
