<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Components\Footer;
use App\Core\Components\Header;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardItemData;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Services\CsrfService;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use function App\Core\System\utils\env;

/**
 * LoginController handles the login page.
 */
class LoginController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private CsrfService $csrfService;
    private AuthService $authService;
    private Header $headerComponent;
    private Footer $footerComponent;
    private ?Environment $twig = null;
    public function __construct(
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository,
        PinboardRepositoryInterface $pinboardRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        CsrfService $csrfService,
        AuthService $authService,
        Header $headerComponent,
        Footer $footerComponent,
        SiteRepositoryInterface $siteRepository
    ) {
        parent::__construct($siteRepository);
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->csrfService = $csrfService;
        $this->authService = $authService;
        $this->headerComponent = $headerComponent;
        $this->footerComponent = $footerComponent;
    }

    public function logout(): Response
    {
        $this->authService->logout();
        return Response::redirect('/login');
    }

    public function signup(Request $request): Response
    {
        if ($this->authService->isLoggedIn()) {
            return Response::redirect('/account/profile');
        }

        $otpRequestedParam = (string) ($request->query('otp_requested') ?? '0');
        $otpRequested = in_array(strtolower($otpRequestedParam), ['1', 'true', 'yes'], true);

        return $this->renderTwig('signup.html.twig', [
            'nonce' => $this->csrfService->getToken(),
            'errors' => [],
            'error' => (string) ($request->query('error') ?? ''),
            'message' => (string) ($request->query('message') ?? ''),
            'data' => [
                'email' => (string) ($request->query('email') ?? ''),
            ],
            'otp_requested' => $otpRequested,
            'title' => 'Sign Up | Krost Business Furniture',
        ]);
    }

    public function login(Request $request): Response
    {
        if($this->authService->isLoggedIn()){
            return Response::redirect('/');
        }
        $otpRequestedParam = (string) ($request->query('otp_requested') ?? '0');
        $otpRequested = in_array(strtolower($otpRequestedParam), ['1', 'true', 'yes'], true);

        $baseUrl = env('APP_URL');
        $currentUrl =
         'https'
        . '://'
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];
        $imageUrl = $baseUrl . '/img/bg/Krost_Business_Furniture_2026.png';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Home Page',
            'name' => "Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Log in to your Krost portal to manage pinboards, orders and service requests',
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


        return $this->renderTwig('index.html.twig', [
            'nonce' => $this->csrfService->getToken(),
            'errors' => [],
            'error' => (string) ($request->query('error') ?? ''),
            'message' => (string) ($request->query('message') ?? ''),
            'data' => [
                'email' => (string) ($request->query('email') ?? ''),
            ],
            'otp_requested' => $otpRequested,
            'title' => "Login | Krost Business Furniture",
            'metaData' => [
                'meta_title' =>  'Krost Business Furniture',
                'meta_description' => 'Log in to your Krost portal to manage pinboards, orders and service requests',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'is_admin' => $this->isAdmin(), 
            'og_image'=> $imageUrl,
            'type'=> 'website',
            'product_schema' => $productSchema,
             '' => 'tel:1800157678'
            ]);
    }

    public function loginUser(Request $request): Response
    {
        $data = $request->all();

        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $this->csrfService->getToken(),
                'errors' => ['general' => 'Invalid CSRF token. Please refresh and try again.'],
                'data' => $data,
                'otp_requested' => false,
            ]);
        }

        $csrfToken = $this->csrfService->getToken();

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => $this->flattenErrors($e->getErrors()),
                'data' => $data,
                'otp_requested' => false,
            ]);
        }

        $user = $this->userRepository->findByEmail((string) $validated['email']);
        if (!$user) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['email' => 'User not found'],
                'data' => $validated,
                'otp_requested' => false,
            ]);
        }

        $otpResult = $this->customerRepository->sendEmailVerification((string) $validated['email']);
        if ((int) ($otpResult['status'] ?? 500) !== 200) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => (string) ($otpResult['message'] ?? 'Failed to send OTP.')],
                'data' => $validated,
                'otp_requested' => false,
            ]);
        }

        return Response::redirect('/login?' . http_build_query([
            'email' => (string) $validated['email'],
            'otp_requested' => 1,
            'message' => 'OTP sent successfully.',
        ]));
    }

    public function verifyEmailAthenticateAndCreatePinboard(Request $request): Response
    {
        $data = $request->all();
        $nonce = (string) ($data['nonce'] ?? '');

        if ($nonce === '' || !$this->csrfService->validateToken($nonce)) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $this->csrfService->getToken(),
                'errors' => ['general' => 'Invalid CSRF token. Please refresh and try again.'],
                'error' => 'Invalid CSRF token. Please refresh and try again.',
                'data' => ['email' => (string) ($data['email'] ?? '')],
                'otp_requested' => true,
            ]);
        }

        $csrfToken = $this->csrfService->getToken();

        $email = isset($data['email']) ? (string) $data['email'] : (isset($data['gmail_Id']) ? (string) $data['gmail_Id'] : '');
        $otpCode = isset($data['otp']) ? (string) $data['otp'] : '';
        $subject = isset($data['subject']) ? (string) $data['subject'] : 'OTP Verification with Krost';
        if ($email === '' || $otpCode === '') {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'Email or OTP code is required'],
                'error' => 'Email or OTP code is required',
                'data' => ['email' => $email],
                'otp_requested' => true,
            ]);
        }

        $result = $this->customerRepository->verifyEmail($email, $otpCode, $subject);
        if ((int) ($result['status'] ?? 500) !== 200) {
            $message = (string) ($result['message'] ?? 'Verification failed');
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['otp' => $message],
                'error' => $message,
                'data' => ['email' => $email],
                'otp_requested' => true,
            ]);
        }

        $userId = (int) ($result['user']['user_id'] ?? 0);
        if ($userId <= 0) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'User not found after verification'],
                'error' => 'User not found after verification',
                'data' => ['email' => $email],
                'otp_requested' => true,
            ]);
        }

        try {
            $pinboardPayload = $request->input('pinboard');
            if (is_string($pinboardPayload)) {
                $decoded = json_decode($pinboardPayload, true);
                $pinboardPayload = is_array($decoded) ? $decoded : null;
            }
            if (!is_array($pinboardPayload)) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'pinboard payload is required'],
                    'error' => 'pinboard payload is required',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }

            $customer = $this->customerRepository->findByUserId($userId);
            $customerId = (int) ($customer['customer_id'] ?? 0);
            if ($customerId < 1) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'Customer not found'],
                    'error' => 'Customer not found',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }

            $pinboardPayload['user_id'] = $userId;
            $pinboardPayload['customer_id'] = $customerId;
            $incomingReference = trim((string) ($pinboardPayload['pinboard_reference'] ?? $pinboardPayload['reference_number'] ?? ''));
            if ($incomingReference === '') {
                $incomingReference = 'VPB-' . strtoupper(substr(md5((string) microtime(true) . '-' . (string) $userId), 0, 10));
            }
            $pinboardPayload['reference_number'] = $incomingReference;
            // Keep alias for frontend compatibility if any consumer expects this key.
            $pinboardPayload['pinboard_reference'] = $incomingReference;

            $pinboardName = trim((string) ($pinboardPayload['pinboard_name'] ?? ''));
            if ($pinboardName === '') {
                $pinboardPayload['pinboard_name'] = 'Virtual Pinboard';
                $pinboardPayload['job_title'] = 'Virtual Pinboard';
            }

            $pinboardPayload = $this->applyPinboardDefaults($pinboardPayload);
            $pinboardPayload['is_active'] = 1;
            // $pinboardPayload['created_at'] = date('Y-m-d H:i:s');
            // $pinboardPayload['updated_at'] = date('Y-m-d H:i:s');
            $pinboardData = new PinboardData($pinboardPayload);
            $itemProducts = isset($pinboardPayload['pinboard_items']) && is_array($pinboardPayload['pinboard_items'])
                ? $pinboardPayload['pinboard_items']
                : [];
        } catch (ValidationException $e) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => $this->flattenErrors($e->getErrors()),
                'error' => $e->getMessage(),
                'data' => ['email' => $email],
                'otp_requested' => true,
            ]);
        }

        $hasIncomingItems = !empty($itemProducts);
        $existingActivePinboard = $this->findActivePinboardForCustomer($userId, (int) $pinboardPayload['customer_id']);
        $shouldCreatePinboard = $hasIncomingItems || $existingActivePinboard === null;

        $pinboard = null;
        $createdPinboardItems = [];
        if ($shouldCreatePinboard) {
            $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
            if (!$pinboard) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'Failed to create pinboard'],
                    'error' => 'Failed to create pinboard',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }

            if ($hasIncomingItems) {
                $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);

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

        try {
            $payload = $this->authService->login($userId);
            if ($pinboard !== null) {
                $pinboardPayload = $this->normalizePinboardPayload($pinboard);
                if ($hasIncomingItems) {
                    $pinboardPayload['pinboard_items'] = $createdPinboardItems;
                }
                $payload['pinboard'] = $pinboardPayload;
            }
        } catch (\Exception $e) {
            return $this->renderTwig('index.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => $e->getMessage()],
                'error' => $e->getMessage(),
                'data' => ['email' => $email],
                'otp_requested' => true,
            ]);
        }

        $accessToken = (string) ($payload['auth']['access_token'] ?? '');
        $tokenType = (string) ($payload['auth']['token_type'] ?? 'Bearer');
        $expiresIn = (int) ($payload['auth']['expires_in'] ?? 3600);
        if ($accessToken !== '') {
            $this->setAuthCookie('admin_access_token', $accessToken, $expiresIn);
            $this->setAuthCookie('admin_token_type', $tokenType, $expiresIn, false);
            $this->setAuthCookie('auth_present', '1', $expiresIn, false);
            $this->expireAuthCookie('admin_refresh_token');
        }
        // $p = $payload;
        // $a = $this->normalizeLatin1Payload($payload);

        // issue:-  Failed to execute 'btoa' on 'Window': The string to be encoded contains characters outside of the Latin1 range.

        // return $this->renderTwig('auth.html.twig', $payload);
        return $this->renderTwig('auth.html.twig', $this->normalizeLatin1Payload($payload));
    }


    public function googleLogin(Request $request): Response
    {
        $pinboardPayload = $this->extractPinboardPayload($request);
        if (session_status() === PHP_SESSION_NONE) {
            // SameSite=Strict session cookies are not sent on the Google → app redirect, so the callback would see a new session.
            @ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
        $_SESSION['google_login_pinboard_payload'] = $pinboardPayload;

        return $this->response->withStatus(302)->withHeader('Location', $this->authService->getGoogleAuthUrl());
    }

    public function microsoftLogin(Request $request): Response
    {
        $pinboardPayload = $this->extractPinboardPayload($request);
        if (session_status() === PHP_SESSION_NONE) {
            // SameSite=Strict session cookies are not sent on the Google → app redirect, so the callback would see a new session.
            @ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
        $_SESSION['microsoft_login_pinboard_payload'] = $pinboardPayload;
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
            $csrfToken = $this->csrfService->getToken();

            if ($userId <= 0) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'User not found after verification'],
                    'error' => 'User not found after verification',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }
            $pinboardPayload = $this->extractPinboardPayloadFromRequestOrSession($request, 'google_login_pinboard_payload');

            if (is_string($pinboardPayload)) {
                $decoded = json_decode($pinboardPayload, true);
                $pinboardPayload = is_array($decoded) ? $decoded : null;
            }
            // if (!is_array($pinboardPayload)) {
            //     return $this->renderTwig('index.html.twig', [
            //         'nonce' => $csrfToken,
            //         'errors' => ['general' => 'pinboard payload is required'],
            //         'error' => 'pinboard payload is required',
            //         'data' => ['email' => $email],
            //         'otp_requested' => true,
            //     ]);
            // }

            $customer = $this->customerRepository->findByUserId($userId);
            $customerId = (int) ($customer['customer_id'] ?? 0);
            if ($customerId < 1) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'Customer not found'],
                    'error' => 'Customer not found',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }
            $pinboard = null;
            $hasIncomingItems = false;
            $createdPinboardItems = [];
            $existingActivePinboard = $this->findActivePinboardForCustomer($userId, $customerId);
            if($pinboardPayload && is_array($pinboardPayload)){
                $pinboardPayload['user_id'] = $userId;
                $pinboardPayload['customer_id'] = $customerId;
                $incomingReference = trim((string) ($pinboardPayload['pinboard_reference'] ?? $pinboardPayload['reference_number'] ?? ''));
                if ($incomingReference === '') {
                    $incomingReference = 'VPB-' . strtoupper(substr(md5((string) microtime(true) . '-' . (string) $userId), 0, 10));
                }
                $pinboardPayload['reference_number'] = $incomingReference;
                // Keep alias for frontend compatibility if any consumer expects this key.
                $pinboardPayload['pinboard_reference'] = $incomingReference;
    
                $pinboardName = trim((string) ($pinboardPayload['pinboard_name'] ?? ''));
                if ($pinboardName === '') {
                    $pinboardPayload['pinboard_name'] = 'Virtual Pinboard';
                    $pinboardPayload['job_title'] = 'Virtual Pinboard';
                }
    
                $pinboardPayload = $this->applyPinboardDefaults($pinboardPayload);
                $pinboardPayload['is_active'] = 1;
                // $pinboardPayload['created_at'] = date('Y-m-d H:i:s');
                // $pinboardPayload['updated_at'] = date('Y-m-d H:i:s');
                $pinboardData = new PinboardData($pinboardPayload);
                $itemProducts = isset($pinboardPayload['pinboard_items']) && is_array($pinboardPayload['pinboard_items'])
                    ? $pinboardPayload['pinboard_items']
                    : [];
    
                $hasIncomingItems = !empty($itemProducts);
                $shouldCreatePinboard = $hasIncomingItems || $existingActivePinboard === null;
        
                $pinboard = null;
                $createdPinboardItems = [];
                if ($shouldCreatePinboard) {
                    $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
                    if (!$pinboard) {
                        return $this->renderTwig('index.html.twig', [
                            'nonce' => $csrfToken,
                            'errors' => ['general' => 'Failed to create pinboard'],
                            'error' => 'Failed to create pinboard',
                            'data' => ['email' => $email],
                            'otp_requested' => true,
                        ]);
                    }
        
                    if ($hasIncomingItems) {
                        $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);
        
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
            }else {
                $pinboard = $existingActivePinboard;
            }
            
    
            try {
                $payload = $this->authService->login($userId);
                if ($pinboard !== null) {
                    $pinboardPayload = $this->normalizePinboardPayload($pinboard);
                    if ($hasIncomingItems) {
                        $pinboardPayload['pinboard_items'] = $createdPinboardItems;
                    }
                    $payload['pinboard'] = $pinboardPayload;
                }
            } catch (\Exception $e) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => $e->getMessage()],
                    'error' => $e->getMessage(),
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }
    
            $accessToken = (string) ($payload['auth']['access_token'] ?? '');
            $tokenType = (string) ($payload['auth']['token_type'] ?? 'Bearer');
            $expiresIn = (int) ($payload['auth']['expires_in'] ?? 3600);
            if ($accessToken !== '') {
                $this->setAuthCookie('admin_access_token', $accessToken, $expiresIn);
                $this->setAuthCookie('admin_token_type', $tokenType, $expiresIn, false);
                $this->setAuthCookie('auth_present', '1', $expiresIn, false);
                $this->expireAuthCookie('admin_refresh_token');
            }
            return $this->renderTwig('auth.html.twig', $this->normalizeLatin1Payload($payload));

        } catch (Exception $e) {
            return Response::redirect('/login?' . http_build_query(['error' => [$e->getMessage()]]));
        }

    }
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

            $userId = (int) $user->user_id;
            $csrfToken = $this->csrfService->getToken();

            if ($userId <= 0) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'User not found after verification'],
                    'error' => 'User not found after verification',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }
            $pinboardPayload = $this->extractPinboardPayloadFromRequestOrSession($request, 'microsoft_login_pinboard_payload');

            if (is_string($pinboardPayload)) {
                $decoded = json_decode($pinboardPayload, true);
                $pinboardPayload = is_array($decoded) ? $decoded : null;
            }
        

            $customer = $this->customerRepository->findByUserId($userId);
            $customerId = (int) ($customer['customer_id'] ?? 0);
            if ($customerId < 1) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => 'Customer not found'],
                    'error' => 'Customer not found',
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }

            $pinboard = null;
            $hasIncomingItems = false;
            $createdPinboardItems = [];
            $existingActivePinboard = $this->findActivePinboardForCustomer($userId, $customerId);
            if($pinboardPayload && is_array($pinboardPayload)){

                $pinboardPayload['user_id'] = $userId;
                $pinboardPayload['customer_id'] = $customerId;
                $incomingReference = trim((string) ($pinboardPayload['pinboard_reference'] ?? $pinboardPayload['reference_number'] ?? ''));
                if ($incomingReference === '') {
                    $incomingReference = 'VPB-' . strtoupper(substr(md5((string) microtime(true) . '-' . (string) $userId), 0, 10));
                }
                $pinboardPayload['reference_number'] = $incomingReference;
                // Keep alias for frontend compatibility if any consumer expects this key.
                $pinboardPayload['pinboard_reference'] = $incomingReference;

                $pinboardName = trim((string) ($pinboardPayload['pinboard_name'] ?? ''));
                if ($pinboardName === '') {
                    $pinboardPayload['pinboard_name'] = 'Virtual Pinboard';
                    $pinboardPayload['job_title'] = 'Virtual Pinboard';
                }

                $pinboardPayload = $this->applyPinboardDefaults($pinboardPayload);
                $pinboardPayload['is_active'] = 1;
                // $pinboardPayload['created_at'] = date('Y-m-d H:i:s');
                // $pinboardPayload['updated_at'] = date('Y-m-d H:i:s');
                $pinboardData = new PinboardData($pinboardPayload);
                $itemProducts = isset($pinboardPayload['pinboard_items']) && is_array($pinboardPayload['pinboard_items'])
                    ? $pinboardPayload['pinboard_items']
                    : [];

                $hasIncomingItems = !empty($itemProducts);
                $shouldCreatePinboard = $hasIncomingItems || $existingActivePinboard === null;
        
                $pinboard = null;
                $createdPinboardItems = [];
                if ($shouldCreatePinboard) {
                    $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
                    if (!$pinboard) {
                        return $this->renderTwig('index.html.twig', [
                            'nonce' => $csrfToken,
                            'errors' => ['general' => 'Failed to create pinboard'],
                            'error' => 'Failed to create pinboard',
                            'data' => ['email' => $email],
                            'otp_requested' => true,
                        ]);
                    }
        
                    if ($hasIncomingItems) {
                        $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);
        
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
            }else {
                $pinboard = $existingActivePinboard;
            }
        
            try {
                $payload = $this->authService->login($userId);
                if ($pinboard !== null) {
                    $pinboardPayload = $this->normalizePinboardPayload($pinboard);
                    if ($hasIncomingItems) {
                        $pinboardPayload['pinboard_items'] = $createdPinboardItems;
                    }
                    $payload['pinboard'] = $pinboardPayload;
                }
            } catch (\Exception $e) {
                return $this->renderTwig('index.html.twig', [
                    'nonce' => $csrfToken,
                    'errors' => ['general' => $e->getMessage()],
                    'error' => $e->getMessage(),
                    'data' => ['email' => $email],
                    'otp_requested' => true,
                ]);
            }
    
            $accessToken = (string) ($payload['auth']['access_token'] ?? '');
            $tokenType = (string) ($payload['auth']['token_type'] ?? 'Bearer');
            $expiresIn = (int) ($payload['auth']['expires_in'] ?? 3600);
            if ($accessToken !== '') {
                $this->setAuthCookie('admin_access_token', $accessToken, $expiresIn);
                $this->setAuthCookie('admin_token_type', $tokenType, $expiresIn, false);
                $this->setAuthCookie('auth_present', '1', $expiresIn, false);
                $this->expireAuthCookie('admin_refresh_token');
            }
            return $this->renderTwig('auth.html.twig', $this->normalizeLatin1Payload($payload));

        } catch (Exception $e) {
            return Response::redirect('/login?' . http_build_query(['error' => [$e->getMessage()]]));
        }
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
    private function extractPinboardPayloadFromRequestOrSession(Request $request, string $sessionKey): ?array
    {
        $pinboardPayload = $this->extractPinboardPayload($request);
        if ($pinboardPayload !== null) {
            return $pinboardPayload;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $storedPayload = $_SESSION[$sessionKey] ?? null;
        unset($_SESSION[$sessionKey]);

        return is_array($storedPayload) ? $storedPayload : null;
    }

    public function registerUser(Request $request): Response
    {
        $data = $request->all();
        $csrfToken = $this->csrfService->getToken();
        $title = 'Sign Up | Krost Business Furniture';

        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'Invalid CSRF token. Please refresh and try again.'],
                'data' => $data,
                'otp_requested' => false,
                'title' => $title,
            ]);
        }

        $otpCode = trim((string) ($data['otp'] ?? ''));
        if ($otpCode !== '') {
            return $this->completeSignupVerification($data, $otpCode, $csrfToken, $title);
        }

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => $this->flattenErrors($e->getErrors()),
                'data' => $data,
                'otp_requested' => false,
                'title' => $title,
            ]);
        }

        $email = (string) $validated['email'];
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['email' => 'An account with this email already exists. Please log in.'],
                'data' => $validated,
                'otp_requested' => false,
                'title' => $title,
            ]);
        }

        try {
            $emailLocalPart = (string) explode('@', $email)[0];
            $this->authService->registerUser($emailLocalPart, $email, bin2hex(random_bytes(32)), ['user'], false);
        } catch (Exception $e) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => $e->getMessage()],
                'data' => $validated,
                'otp_requested' => false,
                'title' => $title,
            ]);
        }

        $otpResult = $this->customerRepository->sendEmailVerification($email);
        if ((int) ($otpResult['status'] ?? 500) !== 200) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => (string) ($otpResult['message'] ?? 'Failed to send OTP.')],
                'data' => $validated,
                'otp_requested' => false,
                'title' => $title,
            ]);
        }

        return Response::redirect('/signup?' . http_build_query([
            'email' => $email,
            'otp_requested' => 1,
            'message' => 'OTP sent successfully.',
        ]));
    }

    /**
     * Step 2–3: verify signup OTP, authenticate, and redirect to profile completion.
     *
     * @param array<string, mixed> $data
     */
    private function completeSignupVerification(array $data, string $otpCode, string $csrfToken, string $title): Response
    {
        $email = trim((string) ($data['email'] ?? ''));
        if ($email === '') {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'Email is required'],
                'data' => ['email' => $email],
                'otp_requested' => true,
                'title' => $title,
            ]);
        }

        $result = $this->customerRepository->verifyEmail($email, $otpCode);
        if ((int) ($result['status'] ?? 500) !== 200) {
            $message = (string) ($result['message'] ?? 'Verification failed');

            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['otp' => $message],
                'error' => $message,
                'data' => ['email' => $email],
                'otp_requested' => true,
                'title' => $title,
            ]);
        }

        $userId = (int) ($result['user']['user_id'] ?? 0);
        if ($userId <= 0) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'User not found after verification'],
                'data' => ['email' => $email],
                'otp_requested' => true,
                'title' => $title,
            ]);
        }

        try {
            $payload = $this->authService->login($userId);
        } catch (Exception $e) {
            return $this->renderTwig('signup.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => $e->getMessage()],
                'data' => ['email' => $email],
                'otp_requested' => true,
                'title' => $title,
            ]);
        }

        $accessToken = (string) ($payload['auth']['access_token'] ?? '');
        $tokenType = (string) ($payload['auth']['token_type'] ?? 'Bearer');
        $expiresIn = (int) ($payload['auth']['expires_in'] ?? 3600);
        if ($accessToken !== '') {
            $this->setAuthCookie('admin_access_token', $accessToken, $expiresIn);
            $this->setAuthCookie('admin_token_type', $tokenType, $expiresIn, false);
            $this->setAuthCookie('auth_present', '1', $expiresIn, false);
            $this->expireAuthCookie('admin_refresh_token');
        }

        return $this->renderTwig('signup-auth.html.twig', $this->normalizeLatin1Payload($payload));
    }

    /**
     * @param array<string, mixed> $errors
     * @return array<string, string>
     */
    private function flattenErrors(array $errors): array
    {
        $flattened = [];
        foreach ($errors as $field => $fieldErrors) {
            if (is_array($fieldErrors)) {
                $flattened[$field] = implode(PHP_EOL, array_map('strval', $fieldErrors));
                continue;
            }

            $flattened[$field] = (string) $fieldErrors;
        }

        return $flattened;
    }

    private function setAuthCookie(string $name, string $value, int $maxAgeSeconds, bool $httpOnly = true): void
    {
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        $expires = time() + max(60, $maxAgeSeconds);

        setcookie($name, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);
    }

    private function expireAuthCookie(string $name): void
    {
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
    }

    private function isHttpsRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        if ($forwardedProto === 'https') {
            return true;
        }

        $forwardedSsl = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? ''));
        if ($forwardedSsl === 'on') {
            return true;
        }

        return (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
    }

    /**
     * @return array<string, mixed>
     */
    private function getSharedTwigContext(): array
    {
        $isLoggedIn = $this->authService->isLoggedIn();

        return [
            'header' => $this->headerComponent->results(['is_logged_in' => $isLoggedIn]),
            'footer' => $this->footerComponent->results(),
            'is_logged_in' => $isLoggedIn,
            'is_admin' => false,
            'app_version' => (string) env('APP_VERSION', '1.0.0'),
            'recaptcha_site_key' => (string) env('RECAPTCHA_SITE_KEY', ''),
            'recaptcha_action_contact' => (string) env('RECAPTCHA_ACTION', 'contact_submit'),
            'recaptcha_action_service' => (string) env('RECAPTCHA_ACTION_SERVICE', 'service_request'),
            'recaptcha_action_project' => (string) env('RECAPTCHA_ACTION_PROJECT', 'project_submission'),
        ];
    }

    private function phoneTelHref(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        return 'tel:' . preg_replace('/\D/', '', $phone);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function renderTwig(string $template, array $payload): Response
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/login');
            $this->twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
            $this->twig->addFunction(new TwigFunction('phone_tel_href', function (?string $phone): string {
                return $this->phoneTelHref($phone);
            }));
        }

        $html = $this->twig->render($template, array_merge($this->getSharedTwigContext(), $payload));

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
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
     * Convert payload strings to Latin1-safe values for the auth page btoa() call.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalizeLatin1Payload(array $payload): array
    {
        $normalized = [];
        foreach ($payload as $key => $value) {
            $normalized[$key] = $this->normalizeLatin1Value($value);
        }

        return $normalized;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeLatin1Value($value)
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeLatin1Value($item);
            }

            return $normalized;
        }

        if (is_object($value)) {
            $normalized = [];
            foreach ((array) $value as $key => $item) {
                $normalized[$key] = $this->normalizeLatin1Value($item);
            }

            return $normalized;
        }

        if (!is_string($value) || $value === '') {
            return $value;
        }

        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $value);
        if ($converted === false) {
            return $value;
        }

        return $converted;
    }

    /**
     * Fill nullable pinboard fields with safe defaults before persistence.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function applyPinboardDefaults(array $payload): array
    {
        $defaults = [
            // keep these aligned with pinboard migration + PinboardDataValidation
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
            // required/non-null columns with defaults
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
}
