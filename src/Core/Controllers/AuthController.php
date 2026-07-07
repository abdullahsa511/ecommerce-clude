<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardItemData;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use App\Core\Repositories\Auth\ScopeRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Services\CsrfService;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * AuthController handles user authentication and registration.
 */
class AuthController extends Controller
{
    private AuthService $authService;
    private UserRepositoryInterface $userRepository;
    private ScopeRepositoryInterface $scopeRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private CsrfService $csrfService;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    public function __construct(
        AuthService $authService,
        UserRepositoryInterface $userRepository,
        ScopeRepositoryInterface $scopeRepository,
        PinboardRepositoryInterface $pinboardRepository,
        CsrfService $csrfService,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        SiteRepositoryInterface $siteRepository
    ) {
        parent::__construct($siteRepository);
        $this->authService = $authService;
        $this->userRepository = $userRepository;
        $this->scopeRepository = $scopeRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->csrfService = $csrfService;
        $this->pinboardItemRepository = $pinboardItemRepository;
    }

    /**
     * Display the registration form.
     */
    public function showRegistrationForm(): Response
    {
        return $this->renderResponse('register', [
            'pageTitle' => 'User Registration',
            'message' => '',
        ]);
    }

    public function registerAdminForm(): Response
    {
        return $this->renderResponse('register-admin', [
            'pageTitle' => 'Admin Registration',
            'message' => '',
        ]);
    }
    public function registerCustomerForm(): Response
    {
        return $this->renderResponse('register-customer', [
            'pageTitle' => 'Customer Registration',
            'message' => '',
        ]);
    }
    public function registerVendorForm(): Response
    {
        return $this->renderResponse('register-vendor', [
            'pageTitle' => 'Vendor Registration',
            'message' => '',
        ]);
    }
    public function registerAdmin(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        return $this->renderResponse('register-admin', [
            'pageTitle' => 'Admin Registration',
            'message' => '',
        ]);
    }
    public function registerCustomer(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        return $this->renderResponse('register-customer', [
            'pageTitle' => 'Customer Registration',
            'message' => '',
        ]);
    }
    public function registerVendor(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        return $this->renderResponse('register-vendor', [
            'pageTitle' => 'Vendor Registration',
            'message' => '',
        ]);
    }

    /**
     * Handle the registration process.
     */
    public function register(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($this->authService->isEmailRegistered($data['email'])) {
            return $this->response->withStatus(400)->withBody('Email is already in use.');
        }

        try {
            // Need to add scopes such as admin, customer, vendor etc.
            //Possibly add a form to select the scopes
            //Example Register User form 
            //Or Register Admin Form
            //Or Register Customer Form
            //Or Register Vendor Form and so on....

            $this->authService->registerUser($data['name'], $data['email'], $data['password']);
            return Response::redirect('/auth/login');
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred during registration.');
        }
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): Response
    {
        // Session login from HTML form is CSRF-protected; password grant (JSON/API) is not form-based.
        $grantType = $request->input('grant_type');
        if ($grantType !== 'password') {
            if (!$this->csrfService->validateToken((string) ($request->input('csrf_token') ?? ''))) {
                $error = http_build_query(['error' => ['Invalid CSRF token']]);

                return Response::redirect('/auth/login?' . $error);
            }
        }

        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'grant_type' => 'nullable|string|in:password,session',
        ]);

        try {
            if ($data['grant_type'] === 'password') {
                $result = $this->authService->loginWithPasswordGrant($data['email'], $data['password']);
                return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody(json_encode($result));
            }

            $this->authService->loginSession($data['email'], $data['password']);
            return Response::redirect('/dashboard');
        } catch (Exception $e) {
            $errors = [$e->getMessage()];
            // Convert errors array to query string for redirect
            $error = http_build_query(['error' => $errors]);
            return Response::redirect('/auth/login?'.$error);
        }
    }

    public function microsoftLogin(): Response
    {
        return $this->response->withStatus(302)->withHeader('Location', $this->authService->getMicrosoftAuthUrl());
    }

    /**
     * OAuth redirect from Google: issue local OAuth2 tokens + PHP session, then redirect to the app.
     */
    public function handleGoogleCallback(Request $request): Response
    {
        $error = $request->query('error');
        if ($error !== null && $error !== '') {
            return Response::redirect('/login?' . http_build_query(['error' => [(string) $error]]));
        }

        $code = $request->query('code');
        if ($code === null || $code === '') {
            return Response::redirect('/login?' . http_build_query(['error' => ['Missing authorization code']]));
        }

        try {
            $raw = $this->authService->getGoogleProfileFromAuthCode((string) $code);
            if ($raw === null) {
                return Response::redirect('/login?' . http_build_query(['error' => ['Could not verify Google login']]));
            }

            $profile = $this->authService->normalizeGoogleProfile($raw);
            $email = trim((string) ($profile['email'] ?? ''));
            if ($email === '') {
                return Response::redirect('/login?' . http_build_query(['error' => ['Google account email is required']]));
            }

            $name = trim((string) ($profile['name'] ?? 'User'));
            if ($name === '') {
                $name = 'User';
            }

            $user = $this->userRepository->findByEmailSimple($email);
            if (!$user) {
                $this->authService->registerUser($name, $email, bin2hex(random_bytes(32)), ['user'], true);
                $user = $this->userRepository->findByEmailSimple($email);
            }

            if (!$user || !isset($user->user_id)) {
                return Response::redirect('/login?' . http_build_query(['error' => ['Could not load or create user after Google login']]));
            }

            $userId = (int) $user->user_id;
            $payload = $this->authService->login($userId);
            $customerId = $customerId = $user->customer_id;;
            $pinboardPayload = $this->extractPinboardPayloadFromRequestOrSession($request);
            $processedPinboard = $this->pinboardProcess($userId, $customerId, $pinboardPayload);
            if ($processedPinboard !== null) {
                $payload['pinboard'] = $processedPinboard;
            }

            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/auth');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
            $html = $twig->render('oauthLogin.html.twig', $payload);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return Response::redirect('/login?' . http_build_query(['error' => [$e->getMessage()]]));
        }
    }

    /**
     * @param array<string, mixed>|null $pinboardPayload
     * @return array<string, mixed>|null
     */
    private function pinboardProcess(int $userId, ?int $customerId, ?array $pinboardPayload): ?array
    {
        if ($customerId === null || $customerId < 1) {
            return null;
        }

        $pinboardPayload = is_array($pinboardPayload) ? $pinboardPayload : [];
        $pinboardPayload['user_id'] = $userId;
        $pinboardPayload['customer_id'] = $customerId;

        $incomingReference = trim((string) ($pinboardPayload['pinboard_reference'] ?? $pinboardPayload['reference_number'] ?? ''));
        if ($incomingReference === '') {
            $incomingReference = 'VPB-' . strtoupper(substr(md5((string) microtime(true) . '-' . (string) $userId), 0, 10));
        }
        $pinboardPayload['reference_number'] = $incomingReference;
        $pinboardPayload['pinboard_reference'] = $incomingReference;

        $pinboardName = trim((string) ($pinboardPayload['pinboard_name'] ?? ''));
        if ($pinboardName === '') {
            $pinboardPayload['pinboard_name'] = 'Virtual Pinboard';
            $pinboardPayload['job_title'] = 'Virtual Pinboard';
        }

        $pinboardPayload = $this->applyPinboardDefaults($pinboardPayload);
        $pinboardPayload['is_active'] = 1;
        $pinboardPayload['created_at'] = date('Y-m-d H:i:s');
        $pinboardPayload['updated_at'] = date('Y-m-d H:i:s');
        $pinboardData = new PinboardData($pinboardPayload);
        $itemProducts = isset($pinboardPayload['pinboard_items']) && is_array($pinboardPayload['pinboard_items'])
            ? $pinboardPayload['pinboard_items']
            : [];

        $hasIncomingItems = !empty($itemProducts);
        $existingActivePinboard = $this->findActivePinboardForCustomer($userId, $customerId);
        $shouldCreatePinboard = $hasIncomingItems || $existingActivePinboard === null;

        $pinboard = null;
        $createdPinboardItems = [];
        if ($shouldCreatePinboard) {
            $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
            if (!$pinboard) {
                return null;
            }

            if ($hasIncomingItems) {
                $pinboardItems = array_map(function ($product) use ($pinboard) {
                    $product['uuid'] = $this->generateUuid();
                    $product['pinboard_id'] = $pinboard->pinboard_id;
                    $pinboardItem = new PinboardItemData($product);
                    return $pinboardItem->toArray();
                }, $itemProducts);

                $this->pinboardItemRepository->createPinboardItems($pinboardItems);
                $createdPinboardItems = $pinboardItems;
            }
        } else {
            $pinboard = $existingActivePinboard;
        }

        if ($pinboard === null) {
            return null;
        }

        $normalized = $this->normalizePinboardPayload($pinboard);
        if ($hasIncomingItems) {
            $normalized['pinboard_items'] = $createdPinboardItems;
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPinboardPayload(Request $request): ?array
    {
        $requestData = $request->request;
        $pinboardPayload = $requestData['pinboard'] ?? null;
        if (is_string($pinboardPayload)) {
            $decoded = json_decode($pinboardPayload, true);
            $pinboardPayload = is_array($decoded) ? $decoded : null;
        }

        return is_array($pinboardPayload) ? $pinboardPayload : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPinboardPayloadFromRequestOrSession(Request $request): ?array
    {
        $pinboardPayload = $this->extractPinboardPayload($request);
        if ($pinboardPayload !== null) {
            return $pinboardPayload;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $storedPayload = $_SESSION['google_login_pinboard_payload'] ?? null;
        unset($_SESSION['google_login_pinboard_payload']);

        return is_array($storedPayload) ? $storedPayload : null;
    }

    private function extractCustomerIdFromPayload(array $payload): ?int
    {
        $customerId = $payload['customer']['customer_id'] ?? null;
        if (!is_numeric($customerId)) {
            return null;
        }

        $id = (int) $customerId;
        return $id > 0 ? $id : null;
    }

    private function generateUuid(): string
    {
        $uuid = uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }

    private function findActivePinboardForCustomer(int $userId, int $customerId): ?object
    {
        $pinboards = $this->pinboardRepository->findByUserId($userId);
        foreach ($pinboards as $pinboard) {
            if (is_object($pinboard)) {
                $payload = $this->normalizePinboardPayload($pinboard);
            } elseif (is_array($pinboard)) {
                $payload = $pinboard;
            } else {
                $payload = [];
            }

            $pinboardCustomerId = isset($payload['customer_id']) ? (int) $payload['customer_id'] : 0;
            $isActive = isset($payload['is_active']) ? (int) $payload['is_active'] : 0;
            if ($pinboardCustomerId === $customerId && $isActive === 1) {
                return is_object($pinboard) ? $pinboard : (object) $payload;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizePinboardPayload(object $pinboard): array
    {
        if (isset($pinboard->data) && is_object($pinboard->data)) {
            return (array) $pinboard->data;
        }

        return (array) $pinboard;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function applyPinboardDefaults(array $payload): array
    {
        $defaults = [
            'contact_number' => [],
            'dispatch_location_id' => 0,
            'pinboard_description' => '',
            'account_manager_id' => 0,
            'project_manager_id' => 0,
            'customer_po_number' => '',
            'expiry_date' => null,
            'organisation_code' => '',
            'organisation_id' => 1,
            'organisation_name' => '',
            'zoho_id' => '',
            'terms' => '',
            'deposit_percentage' => 0.00,
            'gst' => '',
            'bill_to' => '',
            'ship_to' => '',
            'site_contacts' => '',
            'customer_balance' => 0.00,
            'sales_price_list' => '',
            'total_bp_ex_gst' => 0.00,
            'total_bp_inc_gst' => 0.00,
            'total_sp_ex_gst' => 0.00,
            'total_sp_inc_gst' => 0.00,
            'order_discount' => 0.00,
            'discount_rate' => 0.00,
            'discount_amount' => 0.00,
            'grand_total_sp_ex_gst' => 0.00,
            'grand_total_sp_inc_gst' => 0.00,
            'pinboard_status_id' => 0,
            'total' => 0.00,
            'bill_instructions' => '',
            'bill_address' => '',
            'bill_suburb' => '',
            'bill_state' => '',
            'bill_postcode' => '',
            'bill_country' => '',
            'ship_building_name' => '',
            'ship_instructions' => '',
            'ship_address' => '',
            'ship_address_two' => '',
            'ship_suburb' => '',
            'ship_state' => '',
            'ship_postcode' => '',
            'ship_country' => '',
            'company_id' => 1,
            'job_id' => 1,
        ];

        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $payload) || $payload[$key] === null || $payload[$key] === '') {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }

    /**
     * OAuth redirect from Microsoft: issue local OAuth2 tokens + PHP session, then redirect to the app.
     */
    public function handleMicrosoftCallback(Request $request): Response
    {
        $error = $request->query('error');
        if ($error !== null && $error !== '') {
            return Response::redirect('/login?' . http_build_query(['error' => [(string) $error]]));
        }

        $code = $request->query('code');
        if ($code === null || $code === '') {
            return Response::redirect('/login?' . http_build_query(['error' => ['Missing authorization code']]));
        }

        try {
            $raw = $this->authService->getMicrosoftProfileFromAuthCode((string) $code);
            if ($raw === null) {
                $oauthError = $this->authService->getLastMicrosoftOAuthError() ?? 'Could not verify Microsoft login';
                return Response::redirect('/login?' . http_build_query(['error' => [$oauthError]]));
            }

            $profile = $this->authService->normalizeMicrosoftProfile($raw);
            $email = trim((string) ($profile['email'] ?? ''));
            if ($email === '') {
                return Response::redirect('/login?' . http_build_query(['error' => ['Microsoft account email is required']]));
            }

            $name = trim((string) ($profile['name'] ?? 'User'));
            if ($name === '') {
                $name = 'User';
            }

            $user = $this->userRepository->findByEmailSimple($email);
            if (!$user) {
                $this->authService->registerUser($name, $email, bin2hex(random_bytes(32)), ['user'], true);
                $user = $this->userRepository->findByEmailSimple($email);
            }

            if (!$user || !isset($user->user_id)) {
                return Response::redirect('/login?' . http_build_query(['error' => ['Could not load or create user after Microsoft login']]));
            }

            $payload = $this->authService->login((int) $user->user_id);

            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/auth');
            $twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
            $html = $twig->render('oauthLogin.html.twig', $payload);

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'text/html; charset=UTF-8')
                ->withBody($html);
        } catch (Exception $e) {
            return Response::redirect('/login?' . http_build_query(['error' => [$e->getMessage()]]));
        }
    }

    /**
     * Display the login form.
     */
    public function showLoginForm(Request $request): Response
    {
        if($this->authService->isLoggedIn()){
            $this->redirect('/dashboard');
        }
        $errors = $request->query('error');
        $this->view->title = "User Login";

        // Generate CSRF token
        $csrfToken = $this->csrfService->getToken();

        return $this->renderResponse('showLogin', [
            'pageTitle' => 'User Login',
            'title' => "Login",
            'message' => 'Please log in to access your account.',
            'csrf_token' => $csrfToken,  // Add this
            'errors' => $errors,
        ]);
       
    }

    /**
     * Handle the logout process.
     */
    public function logout(): Response
    {
        $this->authService->logoutSession();
        return Response::redirect('/auth/login');
    }

    /**
     * Register a new client by the admin.
     */
    public function registerClient(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'scopes' => 'required|array',
            'admin_id' => 'required|int',
            'redirect_uri' => 'nullable|url',
        ]);

        if (!$this->authService->isAdmin()) {
            return $this->response
                ->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['error' => 'Unauthorized']));
        }

        try {
            $result = $this->authService->registerClient(
                $data['name'],
                $data['scopes'],
                $data['admin_id'],
                $data['redirect_uri']
            );

            return $this->response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode($result));
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while registering the client.');
        }
    }

    /**
     * OAuth2 token endpoint for machine clients (e.g. ERP): client_credentials or refresh_token.
     * POST /api/oauth/token with JSON or form body.
     */
    public function getClientToken(Request $request): Response
    {
        $data = $request->all();

        try {
            if (isset($data['refresh_token'])) {
                $result = $this->authService->refreshToken((string) $data['refresh_token']);
            } elseif (isset($data['client_id'], $data['client_secret'])) {
                $scopes = [];
                if (isset($data['scope'])) {
                    $scope = $data['scope'];
                    $scopes = is_array($scope)
                        ? $scope
                        : preg_split('/\s+/', trim((string) $scope), -1, PREG_SPLIT_NO_EMPTY);
                } elseif (isset($data['scopes']) && is_array($data['scopes'])) {
                    $scopes = $data['scopes'];
                }

                $result = $this->authService->getClientToken(
                    (string) $data['client_id'],
                    (string) $data['client_secret'],
                    $scopes
                );
            } else {
                return $this->response->withStatus(400)
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody(json_encode([
                        'error' => 'invalid_request',
                        'error_description' => 'Provide client_id and client_secret, or refresh_token.',
                    ]));
            }

            if ($result instanceof PsrResponseInterface) {
                return $this->psrTokenResponseToAppResponse($result);
            }

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode($result));
        } catch (Exception $e) {
            return $this->handleException($e, 'Failed to retrieve client token.');
        }
    }

    /**
     * Return authenticated user details from access token.
     */
    public function auth(Request $request): Response
    {
        $authHeader = (string) ($request->header('Authorization') ?? '');
        $accessToken = '';
        if ($authHeader !== '' && str_starts_with($authHeader, 'Bearer ')) {
            $accessToken = trim(substr($authHeader, 7));
        }
        if ($accessToken === '') {
            $accessToken = trim((string) ($request->cookie('admin_access_token') ?? ''));
        }
        if ($accessToken === '') {
            $accessToken = trim((string) ($request->cookie('access_token') ?? ''));
        }

        if ($accessToken === '') {
            return $this->response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Missing access token (Authorization header, admin_access_token cookie, or access_token cookie).',
                    'errors' => ['auth' => ['Missing access token (Authorization header, admin_access_token cookie, or access_token cookie).']],
                    'data' => [],
                ]));
        }

        try {
            $validated = $this->authService->validateToken($accessToken);
            if (!is_array($validated) || ($validated['type'] ?? null) !== 'user') {
                return $this->response
                    ->withStatus(403)
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody(json_encode([
                        'status' => 403,
                        'success' => false,
                        'message' => 'Token is not associated with a user.',
                        'errors' => ['auth' => ['Token is not associated with a user.']],
                        'data' => [],
                    ]));
            }

            $userEntity = $validated['entity'] ?? null;
            $userPayload = null;
            if (is_object($userEntity) && isset($userEntity->data)) {
                $decoded = json_decode(json_encode($userEntity->data), true);
                $userPayload = is_array($decoded) ? $decoded : null;
            }

            $userPinboard = null;
            $userId = isset($userPayload['user_id']) ? (int) $userPayload['user_id'] : 0;
            if ($userId > 0) {
                $pinboard = $this->pinboardRepository->getUserPinboard($userId);
                if (is_object($pinboard) && isset($pinboard->data)) {
                    $decodedPinboard = json_decode(json_encode($pinboard->data), true);
                    $userPinboard = is_array($decodedPinboard) ? $decodedPinboard : null;
                } elseif (is_object($pinboard)) {
                    $decodedPinboard = json_decode(json_encode($pinboard), true);
                    $userPinboard = is_array($decodedPinboard) ? $decodedPinboard : null;
                }
            }

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode([
                    'status' => 200,
                    'success' => true,
                    'message' => 'Authentication completed successfully.',
                    'auth' => [
                        'session' => true,
                        'token_type' => 'Bearer',
                        'access_token' => $accessToken,
                        'refresh_token' => null,
                        'expires_in' => null,
                    ],
                    'user' => $userPayload,
                    'customer' => null,
                    'userPinboard' => $userPinboard,
                    'errors' => [],
                    'data' => [
                        'user' => $userPayload,
                        'userPinboard' => $userPinboard,
                    ],
                ]));
        } catch (Exception $e) {
            return $this->response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode([
                    'status' => 401,
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => ['auth' => [$e->getMessage()]],
                    'data' => [],
                ]));
        }
    }

    /**
     * @param PsrResponseInterface $psr Response from league/oauth2-server (JSON body, OAuth error format on failure)
     */
    private function psrTokenResponseToAppResponse(PsrResponseInterface $psr): Response
    {
        $body = (string) $psr->getBody();
        $out = new Response($body, $psr->getStatusCode());
        foreach ($psr->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $out = $out->withHeader((string)$name, (string) $value);
            }
        }

        return $out;
    }

    /**
     * Redirect the user to the OAuth provider's authorization page.
     */
    public function redirectToProvider(): Response
    {
        $authorizationUrl = $this->authService->getAuthorizationUrl();
        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $authorizationUrl)
            ->withBody('Redirecting to provider.');
    }

    /**
     * Handle the OAuth callback and authenticate the user.
     */
    public function handleProviderCallback(Request $request): Response
    {
        try {
            $authorizationCode = $request->query('code');

            if (!$authorizationCode) {
                return $this->response->withStatus(400)->withBody('Authorization code not provided.');
            }

            $accessToken = $this->authService->getAccessToken($authorizationCode);
            $userData = $this->authService->authenticate($accessToken);

            $user = $this->userRepository->findByEmail($userData['email']);
            if (!$user) {
                $this->userRepository->create([
                    'name' => $userData['name'] ?? 'Unknown',
                    'email' => $userData['email'],
                    'password' => bin2hex(random_bytes(8)),
                ]);
            }

            return $this->response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['message' => 'User authenticated successfully.']));
        } catch (Exception $e) {
            return $this->handleException($e, 'Authentication failed.');
        }
    }

    


}
