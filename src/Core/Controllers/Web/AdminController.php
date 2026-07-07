<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardItemData;
use App\Core\Repositories\Admin\AdminFailedLoginRepositoryInterface;
use App\Core\Repositories\Admin\AdminLoginCodeRepositoryInterface;
use App\Core\Repositories\Admin\AdminRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\OAuth2\AdminTokenLifetime;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Services\CsrfService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminController extends Controller
{
    /** Requested OAuth scopes for admin-issued tokens (required by admin API middleware). */
    private const ADMIN_LOGIN_OAUTH_SCOPES = ['admin'];

    private const ADMIN_LOGIN_REDIRECT_SESSION_KEY = 'admin_login_redirect';

    private UserRepositoryInterface $userRepository;
    private AdminRepositoryInterface $adminRepository;
    private AdminFailedLoginRepositoryInterface $adminFailedLoginRepository;
    private AdminLoginCodeRepositoryInterface $adminLoginCodeRepository;
    private CustomerRepositoryInterface $customerRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private CsrfService $csrfService;
    private AuthService $authService;
    private ?Environment $twig = null;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AdminRepositoryInterface $adminRepository,
        AdminFailedLoginRepositoryInterface $adminFailedLoginRepository,
        AdminLoginCodeRepositoryInterface $adminLoginCodeRepository,
        CustomerRepositoryInterface $customerRepository,
        PinboardRepositoryInterface $pinboardRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        CsrfService $csrfService,
        AuthService $authService,
        SiteRepositoryInterface $siteRepository
    ) {
        parent::__construct($siteRepository);
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
        $this->adminFailedLoginRepository = $adminFailedLoginRepository;
        $this->adminLoginCodeRepository = $adminLoginCodeRepository;
        $this->customerRepository = $customerRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->csrfService = $csrfService;
        $this->authService = $authService;
    }

    public function showLogin(Request $request): Response
    {
        $otpRequestedParam = (string) ($request->query('otp_requested') ?? '0');
        $otpRequested = in_array(strtolower($otpRequestedParam), ['1', 'true', 'yes'], true);
        $this->rememberPostLoginRedirect($request);
        $redirect = $this->storedPostLoginRedirect() ?? '';

        return $this->renderTwig('login.html.twig', [
            'nonce' => $this->csrfService->getToken(),
            'errors' => [],
            'error' => (string) ($request->query('error') ?? ''),
            'message' => (string) ($request->query('message') ?? ''),
            'data' => [
                'email' => (string) ($request->query('email') ?? ''),
            ],
            'otp_requested' => $otpRequested,
            'redirect' => $redirect,
        ]);
    }

    public function login(Request $request): Response
    {
        $data = $request->all();
        $this->rememberPostLoginRedirect($request);

        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $this->csrfService->getToken(),
                'errors' => ['general' => 'Invalid CSRF token. Please refresh and try again.'],
                'data' => $data,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $csrfToken = $this->csrfService->getToken();

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => $this->flattenErrors($e->getErrors()),
                'data' => $data,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $admin = $this->adminRepository->findByEmail((string) $validated['email']);
        if (!$admin) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['email' => 'Admin account not found'],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        if (!$this->isActiveAdmin($admin)) {
            $this->logAdminFailedLogin($admin, $request);
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'This admin account is inactive.'],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $otpResult = $this->customerRepository->sendEmailVerification((string) $validated['email']);
        if ((int) ($otpResult['status'] ?? 500) !== 200) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => (string) ($otpResult['message'] ?? 'Failed to send OTP.')],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, [
            'email' => (string) $validated['email'],
            'otp_requested' => 1,
            'message' => 'OTP sent successfully.',
        ]));
    }

    public function completeLogin(Request $request): Response
    {
        $data = $request->all();
        $this->rememberPostLoginRedirect($request);

        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $this->csrfService->getToken(),
                'errors' => ['general' => 'Invalid CSRF token. Please refresh and try again.'],
                'data' => $data,
                'otp_requested' => true,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $csrfToken = $this->csrfService->getToken();

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'otp' => 'required|string|min:6|max:6',
            ]);
        } catch (ValidationException $e) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => $this->flattenErrors($e->getErrors()),
                'data' => $data,
                'otp_requested' => true,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $admin = $this->adminRepository->findByEmail((string) $validated['email']);
        if (!$admin || !$this->isActiveAdmin($admin)) {
            $this->logAdminFailedLogin($admin, $request, (string) $validated['email']);
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'Unauthorized admin account.'],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $user = $this->userRepository->findByEmailSimple((string) $validated['email']);
        if (!$user) {
            $this->logAdminFailedLogin($admin, $request, (string) $validated['email']);
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => 'Linked user account not found for this admin.'],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $storedOtp = (string) ($user->otp_code ?? '');
        $expiryTime = (string) ($user->otp_expiry_time ?? '');
        if ($storedOtp === '' || $storedOtp !== (string) $validated['otp']) {
            $this->logAdminFailedLogin($admin, $request, (string) $validated['email']);
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['otp' => 'Invalid OTP code.'],
                'data' => $validated,
                'otp_requested' => true,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        if ($expiryTime !== '' && strtotime($expiryTime) < time()) {
            $this->logAdminFailedLogin($admin, $request, (string) $validated['email']);
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['otp' => 'OTP expired. Please request a new OTP.'],
                'data' => $validated,
                'otp_requested' => false,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        $this->userRepository->update((int) $user->user_id, [
            'otp_code' => '',
            'is_verified' => 1,
        ]);

        try {
            $payload = $this->authService->login(
                (int) $user->user_id,
                self::ADMIN_LOGIN_OAUTH_SCOPES,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            );
        } catch (\Exception $e) {
            return $this->renderTwig('login.html.twig', [
                'nonce' => $csrfToken,
                'errors' => ['general' => $e->getMessage()],
                'data' => $validated,
                'otp_requested' => true,
                'redirect' => $this->storedPostLoginRedirect() ?? '',
            ]);
        }

        [$pinboard, $hasIncomingItems, $createdPinboardItems] = $this->resolveOrCreatePinboardForUser(
            (int) $user->user_id,
            $request->input('pinboard')
        );
        $payload['admin_redirect_url'] = $this->resolveAdminRedirectUrl($request);
        $payload['oauth_source'] = 'otp';
        $payload['exchange_code'] = $this->issueExchangeCode((int) $user->user_id, (string) $validated['email'], 'otp', $request);
        $payload['exchange_api_url'] = $this->exchangeApiUrlForCurrentRequest($request);
        $payload = $this->attachPinboardToPayload($payload, $pinboard, $hasIncomingItems, $createdPinboardItems);

        return $this->renderTwig('oauthLogin.html.twig', $payload);
    }

    public function googleLogin(Request $request): Response
    {
        $this->rememberPostLoginRedirect($request);

        $scopes = [
            'openid',
            'email',
            'profile',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ];

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $this->authService->getGoogleAuthUrl($scopes));
    }

    public function microsoftLogin(Request $request): Response
    {
        $this->rememberPostLoginRedirect($request);

        $scopes = ['openid', 'profile', 'email', 'offline_access', 'User.Read'];

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $this->authService->getMicrosoftAuthUrl($scopes));
    }

    public function logout(): Response
    {
        $this->authService->logout();

        return Response::redirect('/admin/login');
    }

    public function handleGoogleCallback(Request $request): Response
    {
        $error = $request->query('error');
        if ($error !== null && $error !== '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => (string) $error]));
        }

        $code = $request->query('code');
        if ($code === null || $code === '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Missing authorization code']));
        }

        $raw = $this->authService->getGoogleProfileFromAuthCode((string) $code);
        if ($raw === null) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Could not verify Google login']));
        }

        $profile = $this->authService->normalizeGoogleProfile($raw);
        $email = trim((string) ($profile['email'] ?? ''));
        if ($email === '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Google account email is required']));
        }

        $admin = $this->adminRepository->findByEmail($email);
        if (!$admin || !$this->isActiveAdmin($admin)) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'This account is not an admin']));
        }

        $user = $this->userRepository->findByEmailSimple($email);
        if (!$user) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Linked user account not found for this admin']));
        }

        try {
            $payload = $this->authService->login(
                (int) $user->user_id,
                self::ADMIN_LOGIN_OAUTH_SCOPES,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            );
        } catch (\Throwable $e) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Could not complete sign-in. Please try again.']));
        }
        [$pinboard, $hasIncomingItems, $createdPinboardItems] = $this->resolveOrCreatePinboardForUser((int) $user->user_id, null);
        $payload['admin_redirect_url'] = $this->resolveAdminRedirectUrl($request);
        $payload['oauth_source'] = 'google';
        $payload['exchange_code'] = $this->issueExchangeCode((int) $user->user_id, $email, 'google', $request);
        $payload['exchange_api_url'] = $this->exchangeApiUrlForCurrentRequest($request);
        $payload = $this->attachPinboardToPayload($payload, $pinboard, $hasIncomingItems, $createdPinboardItems);

        return $this->renderAdminOAuthResponse($payload);
    }

    public function handleMicrosoftCallback(Request $request): Response
    {
        $error = $request->query('error');
        if ($error !== null && $error !== '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => (string) $error]));
        }

        $code = $request->query('code');
        if ($code === null || $code === '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Missing authorization code']));
        }

        $raw = $this->authService->getMicrosoftProfileFromAuthCode((string) $code);
        if ($raw === null) {
            $oauthError = $this->authService->getLastMicrosoftOAuthError() ?? 'Could not verify Microsoft login';
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => $oauthError]));
        }

        $profile = $this->authService->normalizeMicrosoftProfile($raw);
        $email = trim((string) ($profile['email'] ?? ''));
        if ($email === '') {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Microsoft account email is required']));
        }

        $admin = $this->adminRepository->findByEmail($email);
        if (!$admin || !$this->isActiveAdmin($admin)) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'This account is not an admin']));
        }

        $user = $this->userRepository->findByEmailSimple($email);
        if (!$user) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Linked user account not found for this admin']));
        }

        try {
            $payload = $this->authService->login(
                (int) $user->user_id,
                self::ADMIN_LOGIN_OAUTH_SCOPES,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            );
        } catch (\Throwable $e) {
            return Response::redirect('/admin/login?' . $this->loginRedirectQuery($request, ['error' => 'Could not complete sign-in. Please try again.']));
        }
        [$pinboard, $hasIncomingItems, $createdPinboardItems] = $this->resolveOrCreatePinboardForUser((int) $user->user_id, null);
        $payload['admin_redirect_url'] = $this->resolveAdminRedirectUrl($request);
        $payload['oauth_source'] = 'microsoft';
        $payload['exchange_code'] = $this->issueExchangeCode((int) $user->user_id, $email, 'microsoft', $request);
        $payload['exchange_api_url'] = $this->exchangeApiUrlForCurrentRequest($request);
        $payload = $this->attachPinboardToPayload($payload, $pinboard, $hasIncomingItems, $createdPinboardItems);

        return $this->renderAdminOAuthResponse($payload);
    }

    private function rememberPostLoginRedirect(Request $request): void
    {
        $redirect = $this->resolvePostLoginRedirect($request);
        if ($redirect !== null) {
            $this->session->set(self::ADMIN_LOGIN_REDIRECT_SESSION_KEY, $redirect);
        }
    }

    private function storedPostLoginRedirect(): ?string
    {
        $stored = $this->session->get(self::ADMIN_LOGIN_REDIRECT_SESSION_KEY);
        if (!is_string($stored) || $stored === '') {
            return null;
        }

        return $this->isAllowedAdminRedirect($stored) ? $stored : null;
    }

    private function consumePostLoginRedirect(): ?string
    {
        $redirect = $this->storedPostLoginRedirect();
        if ($redirect !== null) {
            $this->session->delete(self::ADMIN_LOGIN_REDIRECT_SESSION_KEY);
        }

        return $redirect;
    }

    private function resolveAdminRedirectUrl(Request $request): string
    {
        $redirect = $this->consumePostLoginRedirect();
        if ($redirect !== null) {
            return $redirect;
        }

        return (string) ($_ENV['APP_ADMIN_URL'] ?? '');
    }

    /**
     * @param array<string, scalar> $params
     */
    private function loginRedirectQuery(Request $request, array $params = []): string
    {
        $this->rememberPostLoginRedirect($request);
        $redirect = $this->storedPostLoginRedirect();
        if ($redirect !== null) {
            $params['redirect'] = $redirect;
        }

        return http_build_query($params);
    }

    private function resolvePostLoginRedirect(Request $request): ?string
    {
        $redirect = trim((string) ($request->query('redirect') ?? $request->input('redirect') ?? ''));
        if ($redirect === '') {
            return null;
        }

        return $this->normalizeAdminRedirect($redirect);
    }

    private function normalizeAdminRedirect(string $redirect): ?string
    {
        $adminBase = rtrim((string) ($_ENV['APP_ADMIN_URL'] ?? ''), '/');
        if ($adminBase === '') {
            return null;
        }

        if (str_starts_with($redirect, '/')) {
            return $this->isAllowedAdminRedirect($adminBase . $redirect) ? $adminBase . $redirect : null;
        }

        return $this->isAllowedAdminRedirect($redirect) ? $redirect : null;
    }

    private function isAllowedAdminRedirect(string $redirect): bool
    {
        $adminBase = rtrim((string) ($_ENV['APP_ADMIN_URL'] ?? ''), '/');
        if ($adminBase === '') {
            return false;
        }

        $adminParts = parse_url($adminBase);
        $redirectParts = parse_url($redirect);
        if ($adminParts === false || $redirectParts === false) {
            return false;
        }

        $adminHost = strtolower((string) ($adminParts['host'] ?? ''));
        $redirectHost = strtolower((string) ($redirectParts['host'] ?? ''));
        if ($adminHost === '' || $redirectHost !== $adminHost) {
            return false;
        }

        $adminScheme = strtolower((string) ($adminParts['scheme'] ?? 'https'));
        $redirectScheme = strtolower((string) ($redirectParts['scheme'] ?? 'https'));
        if ($redirectScheme !== $adminScheme) {
            return false;
        }

        $adminPath = rtrim((string) ($adminParts['path'] ?? ''), '/');
        $redirectPath = (string) ($redirectParts['path'] ?? '');
        if ($adminPath !== '' && $adminPath !== '/' && !str_starts_with($redirectPath, $adminPath)) {
            return false;
        }

        return true;
    }

    private function isActiveAdmin(object $admin): bool
    {
        if (isset($admin->status)) {
            return (int) $admin->status === 1;
        }

        return isset($admin->data->status) && (int) $admin->data->status === 1;
    }

    private function logAdminFailedLogin(?object $admin, Request $request, ?string $email = null): void
    {
        $server = $request->getServerParams();
        $ip = (string) ($server['REMOTE_ADDR'] ?? '0.0.0.0');
        $username = null;
        $adminId = null;

        if ($admin !== null) {
            $username = isset($admin->username) ? (string) $admin->username : (string) ($admin->data->username ?? '');
            $adminId = isset($admin->admin_id) ? (int) $admin->admin_id : (int) ($admin->data->admin_id ?? 0);
        } elseif ($email !== null) {
            $username = $email;
        }

        try {
            $this->adminFailedLoginRepository->logFailed(
                substr($ip, 0, 16),
                date('Y-m-d H:i:s'),
                $adminId > 0 ? $adminId : null,
                $username !== '' ? $username : null
            );
        } catch (\Throwable $e) {
            // best-effort logging, do not block authentication response
        }
    }

    /**
     * @param array<string, mixed> $errors
     * @return array<string, string>
     */
    private function flattenErrors(array $errors): array
    {
        $flat = [];
        foreach ($errors as $key => $error) {
            if (is_array($error)) {
                $flat[$key] = implode(PHP_EOL, $error);
            } else {
                $flat[$key] = (string) $error;
            }
        }

        return $flat;
    }

    private function issueExchangeCode(int $userId, string $email, string $source, Request $request): string
    {
        $server = $request->getServerParams();
        $ip = (string) ($server['REMOTE_ADDR'] ?? '');
        $userAgent = (string) ($server['HTTP_USER_AGENT'] ?? '');

        return $this->adminLoginCodeRepository->issueCode($userId, $email, $source, $ip, $userAgent);
    }

    /**
     * Public URL for POST /api/admin/exchange-code on the same deployment that stored the one-time code.
     * The admin SPA must call this host (not a hard-coded production API) when APP_ADMIN_URL points at local dev.
     */
    private function exchangeApiUrlForCurrentRequest(Request $request): string
    {
        $explicit = rtrim((string) ($_ENV['API_PUBLIC_URL'] ?? ''), '/');
        if ($explicit !== '') {
            return $explicit . '/api/admin/exchange-code';
        }

        $server = $request->getServerParams();
        $https = (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off')
            || strtolower((string) ($server['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https'
            || strtolower((string) ($server['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on'
            || (int) ($server['SERVER_PORT'] ?? 0) === 443;
        $scheme = $https ? 'https' : 'http';

        $forwardedHost = trim((string) ($server['HTTP_X_FORWARDED_HOST'] ?? ''));
        $host = $forwardedHost !== '' ? trim(explode(',', $forwardedHost)[0]) : trim((string) ($server['HTTP_HOST'] ?? ''));
        if ($host === '') {
            $host = trim((string) ($server['SERVER_NAME'] ?? ''));
        }
        if ($host === '') {
            return rtrim((string) ($_ENV['APP_URL'] ?? 'http://localhost'), '/') . '/api/admin/exchange-code';
        }

        return $scheme . '://' . $host . '/api/admin/exchange-code';
    }

    /**
     * Render admin OAuth completion page using Twig directly
     * (same approach as AuthController social callback actions).
     *
     * @param array<string, mixed> $payload
     */
    private function renderAdminOAuthResponse(array $payload): Response
    {
        $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/admin');
        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);
        $html = $twig->render('oauthLogin.html.twig', $payload);

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
    }

    /**
     * Optimized shared Twig renderer with lazy init.
     *
     * @param array<string, mixed> $payload
     */
    private function renderTwig(string $template, array $payload): Response
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/admin');
            $this->twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
        }

        $html = $this->twig->render($template, $payload);

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, array<string, mixed>> $createdPinboardItems
     * @return array<string, mixed>
     */
    private function attachPinboardToPayload(array $payload, ?object $pinboard, bool $hasIncomingItems, array $createdPinboardItems): array
    {
        if ($pinboard === null) {
            return $payload;
        }

        $pinboardData = $this->normalizePinboardPayload($pinboard);
        if ($hasIncomingItems) {
            $pinboardData['pinboard_items'] = $createdPinboardItems;
        }

        $payload['pinboard'] = $pinboardData;
        return $payload;
    }

    /**
     * @param mixed $rawPinboardPayload
     * @return array{0:?object,1:bool,2:array<int, array<string, mixed>>}
     */
    private function resolveOrCreatePinboardForUser(int $userId, $rawPinboardPayload): array
    {
        $customer = $this->customerRepository->findByUserId($userId);
        $customerId = (int) ($customer['customer_id'] ?? 0);
        if ($customerId < 1) {
            return [null, false, []];
        }

        $pinboardPayload = $this->buildPinboardPayload($userId, $customerId, $rawPinboardPayload);
        $itemProducts = isset($pinboardPayload['pinboard_items']) && is_array($pinboardPayload['pinboard_items'])
            ? $pinboardPayload['pinboard_items']
            : [];
        $hasIncomingItems = !empty($itemProducts);

        $existingActivePinboard = $this->findActivePinboardForCustomer($userId, $customerId);
        $shouldCreatePinboard = $hasIncomingItems || $existingActivePinboard === null;

        $pinboard = null;
        $createdPinboardItems = [];
        if ($shouldCreatePinboard) {
            $pinboardData = new PinboardData($pinboardPayload);
            $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
            if (!$pinboard) {
                return [null, false, []];
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

        return [$pinboard, $hasIncomingItems, $createdPinboardItems];
    }

    /**
     * @param mixed $rawPinboardPayload
     * @return array<string, mixed>
     */
    private function buildPinboardPayload(int $userId, int $customerId, $rawPinboardPayload): array
    {
        $pinboardPayload = [];
        if (is_string($rawPinboardPayload)) {
            $decoded = json_decode($rawPinboardPayload, true);
            if (is_array($decoded)) {
                $pinboardPayload = $decoded;
            }
        } elseif (is_array($rawPinboardPayload)) {
            $pinboardPayload = $rawPinboardPayload;
        }

        $pinboardPayload['user_id'] = $userId;
        $pinboardPayload['customer_id'] = $customerId;

        $incomingReference = trim((string) ($pinboardPayload['pinboard_reference'] ?? $pinboardPayload['reference_number'] ?? ''));
        if ($incomingReference === '') {
            $incomingReference = 'VPB-' . strtoupper(substr(md5((string) microtime(true) . '-' . (string) $userId), 0, 10));
        }
        $pinboardPayload['reference_number'] = $incomingReference;
        $pinboardPayload['pinboard_reference'] = $incomingReference;

        $pinboardName = trim((string) ($pinboardPayload['pinboard_name'] ?? ''));
        $pinboardPayload['created_at'] = date('Y-m-d H:i:s');
        $pinboardPayload['updated_at'] = date('Y-m-d H:i:s');
        if ($pinboardName === '') {
            $pinboardPayload['pinboard_name'] = 'Virtual Pinboard';
            $pinboardPayload['job_title'] = 'Virtual Pinboard';
        }

        $pinboardPayload = $this->applyPinboardDefaults($pinboardPayload);
        $pinboardPayload['is_active'] = 1;

        return $pinboardPayload;
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
}
