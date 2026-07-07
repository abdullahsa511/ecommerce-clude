<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Http\Concerns\SetsAdminAuthCookies;
use App\Core\OAuth2\AdminTokenLifetime;
use App\Core\Repositories\Admin\AdminFailedLoginRepositoryInterface;
use App\Core\Repositories\Admin\AdminLoginCodeRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Admin\AdminRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;
use Exception;

class AdminController extends ApiController
{
    use SetsAdminAuthCookies;

    /** Requested OAuth scopes for admin-issued tokens (required by admin API middleware). */
    private const ADMIN_LOGIN_OAUTH_SCOPES = ['admin'];

    private AdminRepositoryInterface $adminRepository;
    private MediaRepositoryInterface $mediaRepository;
    private UserRepositoryInterface $userRepository;
    private AdminFailedLoginRepositoryInterface $adminFailedLoginRepository;
    private AdminLoginCodeRepositoryInterface $adminLoginCodeRepository;
    private CustomerRepositoryInterface $customerRepository;
    private AuthService $authService;
    public function __construct(
        AdminRepositoryInterface $adminRepository,
        MediaRepositoryInterface $mediaRepository,
        UserRepositoryInterface $userRepository,
        AdminFailedLoginRepositoryInterface $adminFailedLoginRepository,
        AdminLoginCodeRepositoryInterface $adminLoginCodeRepository,
        CustomerRepositoryInterface $customerRepository,
        AuthService $authService,
    )
    {
        parent::__construct();
        $this->adminRepository = $adminRepository;
        $this->mediaRepository = $mediaRepository;
        $this->userRepository = $userRepository;
        $this->adminFailedLoginRepository = $adminFailedLoginRepository;
        $this->adminLoginCodeRepository = $adminLoginCodeRepository;
        $this->customerRepository = $customerRepository;
        $this->authService = $authService;
    }

    /**
     * Admin login with OTP.
     * 1) email only -> validates admin + sends OTP
     * 2) email + otp -> validates OTP + creates Redis-backed browser session, OAuth tokens, and JSON payload
     */
    public function login(Request $request): Response
    {
        $data = $request->all();

        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'otp' => 'nullable|string|min:6|max:6',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $email = (string) $validated['email'];
        $otp = trim((string) ($validated['otp'] ?? ''));

        $admin = $this->adminRepository->findByEmail($email);
        if (!$admin) {
            return $this->renderError(404, 'Admin account not found');
        }

        if (!$this->isActiveAdmin($admin)) {
            $this->logAdminFailedLogin($admin, $request);
            return $this->renderError(403, 'This admin account is inactive');
        }

        if ($otp === '') {
            try {
                $result = $this->customerRepository->sendEmailVerification($email);
            } catch (Exception $e) {
                // return $this->renderError(500, $e->getMessage());
            }
            if ((int) ($result['status'] ?? 500) !== 200) {
                return $this->renderError((int) ($result['status'] ?? 500), (string) ($result['message'] ?? 'Failed to send OTP'));
            }

            return $this->renderResponse([
                'status' => 200,
                'success' => true,
                'message' => 'OTP sent successfully',
                'email' => $email,
            ]);
        }

        $user = $this->userRepository->findByEmailSimple($email);
        if (!$user) {
            $this->logAdminFailedLogin($admin, $request, $email);
            return $this->renderError(404, 'Linked user account not found for this admin');
        }

        $storedOtp = (string) ($user->otp_code ?? '');
        $expiryTime = (string) ($user->otp_expiry_time ?? '');
        if ($storedOtp === '' || $storedOtp !== $otp) {
            $this->logAdminFailedLogin($admin, $request, $email);
            return $this->renderError(400, 'Invalid OTP code');
        }
        if ($expiryTime !== '' && strtotime($expiryTime) < time()) {
            $this->logAdminFailedLogin($admin, $request, $email);
            return $this->renderError(400, 'OTP expired');
        }

        $this->userRepository->update((int) $user->user_id, [
            'otp_code' => '',
            'is_verified' => 1,
        ]);

        try {
            $authPayload = $this->authService->login(
                (int) $user->user_id,
                self::ADMIN_LOGIN_OAUTH_SCOPES,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            );
        } catch (\Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }

        $this->applyAdminAuthCookiesFromLoginPayload($authPayload);

        return $this->renderResponse($authPayload);
    }

    /**
     * Validate admin bearer token for SPA route guards.
     * Checks token validity, admin scope, and active admin account status.
     */
    public function validateToken(Request $request): Response
    {
        $token = $this->extractAccessToken($request);
        if ($token === '') {
            return $this->renderError(401, 'Missing access token (Authorization header or admin_access_token cookie).');
        }

        try {
            $auth = $this->authService->validateToken($token);
        } catch (\Throwable $e) {
            return $this->renderError(401, 'Invalid or expired token.');
        }

        if (!is_array($auth) || (($auth['type'] ?? null) !== 'user')) {
            return $this->renderError(403, 'User token is required.');
        }

        $scopes = $this->normalizeScopes($auth['scopes'] ?? []);

        $user = $auth['entity'] ?? null;
        if (!is_object($user) || !isset($user->email)) {
            return $this->renderError(403, 'Invalid authenticated user.');
        }

        $admin = $this->adminRepository->findByEmail((string) $user->email);
        if (!$admin || !$this->isActiveAdmin($admin)) {
            return $this->renderError(403, 'Admin account is not active.');
        }

        return $this->renderResponse([
            'valid' => true,
            'message' => 'Token is valid',
            'admin' => [
                'admin_id' => $admin->admin_id ?? $admin->data->admin_id ?? null,
                'email' => $admin->email ?? $admin->data->email ?? null,
                'status' => $admin->status ?? $admin->data->status ?? null,
            ],
            'scopes' => $scopes,
        ]);
    }

    /**
     * Exchange one-time login code for the same auth payload shape as {@see login()} (OTP step 2).
     *
     * Web flow alignment:
     * 1) GET /admin/login — form
     * 2) POST /admin/login — email, OTP sent
     * 3) POST /admin/auth/login — OTP verified; {@see \App\Core\Controllers\Web\AdminController::completeLogin}
     *    calls {@see \App\Core\Controllers\Web\AdminController::issueExchangeCode}, then renders
     *    `oauthLogin.html.twig`, which redirects the browser to `APP_ADMIN_URL` with the plaintext
     *    code in the `code` query param (same value as `exchange_code` in the embedded JSON).
     *
     * The SPA should POST (or GET) here with `code` or `exchange_code` matching that plaintext.
     * Cookies are set when the response is same-site; the JSON body includes full `auth` so the SPA
     * can store tokens the same way as after API OTP login.
     */
    public function exchangeCode(Request $request): Response
    {
        $data = $request->all();
        $code = $this->extractExchangeCodeFromRequest($request, $data);
        if ($code === '') {
            return $this->renderError(422, 'code or exchange_code is required');
        }

        $row = $this->adminLoginCodeRepository->consumeCode($code);
        if (!$row) {
            return $this->renderError(401, 'Invalid or expired login code. POST to the same API host that issued the code (use the exchange_api query param from the admin redirect, or exchange_api_url from oauth JSON).');
        }

        $userId = (int) ($row['user_id'] ?? 0);
        if ($userId < 1) {
            return $this->renderError(401, 'Invalid login code payload');
        }

        try {
            $payload = $this->authService->login(
                $userId,
                self::ADMIN_LOGIN_OAUTH_SCOPES,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            );
        } catch (\Throwable $e) {
            error_log('admin exchangeCode login: ' . $e->getMessage());

            return $this->renderError(500, 'Failed to complete authentication');
        }

        $this->applyAdminAuthCookiesFromLoginPayload($payload);

        $admin = $this->adminRepository->findByEmail((string) ($payload['user']['email'] ?? ''));

        $responsePayload = $payload;
        $responsePayload['success'] = true;
        $responsePayload['status'] = 200;
        $responsePayload['message'] = 'Code exchange successful';
        $responsePayload['admin'] = $admin?->data ?? null;
        $responsePayload['oauth_source'] = (string) ($row['source'] ?? '');

        return $this->renderResponse($responsePayload);
    }

    /**
     * Get all admins.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $admins = $this->adminRepository->findAll();
        return $this->renderResponse($admins);
    }

    /**
     * Show an admin.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $admin = $this->adminRepository->find((int)$id);
            
        if (!$admin) {
            return $this->renderError(404, 'Admin not found');
        }
        
        if (!$admin->data->avatar) {
            $admin->data->avatar = [];
        }else{
            $admin->data->avatar = [
                [
                    'admin_image_id' => $id,
                    'image' => $admin->data->avatar,
                    'size' => 256,
                    'type' => 'image/jpeg',
                    'objectURL' => $admin->data->avatar,
                    'status' => [
                        'name' => 'Uploaded',
                        'severity' => 'success'
                    ],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }

        return $this->renderResponse($admin->data);
    }

    /**
     * Create a new admin.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'username' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'password' => 'required|string',
                'email' => 'required|string|email',
                'phone_number' => 'required|string',
                'url' => 'string|nullable',
                'display_name' => 'string|nullable',
                // 'avatar' => 'string|nullable',
                'bio' => 'string|nullable',
                'role_id' => 'integer|nullable',
                'site_access' => 'required|string',
                'status' => 'required|integer',
                'token' => 'string|nullable',
            ]);
            unset($data['avatar']);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        // Check if email already exists
        if ($this->adminRepository->findOneBy(['email' => $data['email']])) {
            return $this->renderError(400, 'Email is already in use.', ['email' => 'The email '.$data['email']." is already in use."]);
        }

        // Check if username already exists
        if ($this->adminRepository->findOneBy(['username' => $data['username']])) {
            return $this->renderError(400, 'Username is already in use.', ['username' => 'The username '.$data['username']." is already in use."]);
        }

        $admin = $this->adminRepository->create($data);
        return $this->renderResponse($admin->data);
    }

    /**
     * Update an admin.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'username' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'string|nullable',
                'password' => 'required|string',
                'email' => 'required|string|email',
                'phone_number' => 'required|string',
                'url' => 'string|nullable',
                'display_name' => 'string|nullable',
                // 'avatar' => 'string|nullable',
                'bio' => 'string|nullable',
                'role_id' => 'integer|nullable',
                'site_access' => 'required|string',
                'status' => 'integer|nullable',
                'token' => 'string|nullable',
            ]);
            unset($data['avatar']);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingAdmin = $this->adminRepository->find((int)$id);
        if (!$existingAdmin) {
            return $this->renderError(404, 'Admin not found');
        }

        $admin = $this->adminRepository->update((int) $id, $data);
        if (!$admin) {
            return $this->renderError(500, 'Failed to update admin');
        }
        
        return $this->renderResponse($admin->data);
    }

    /**
     * Delete an admin.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->adminRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Admin deleted successfully']);
    }

    // import admins
    public function importAdmins(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        try {
            $result = $this->adminRepository->importAdmins($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request, int $admin_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/admins/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->adminRepository->updateAdminImage($result['files'], $admin_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete admin image
    public function deleteImage(Request $request, int $admin_id): Response
    {
        $deleted = $this->adminRepository->deleteAdminImage($admin_id);
        return $this->renderResponse(['deleted' => $deleted]);
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
            // best-effort logging
        }
    }

    /**
     * @param mixed $rawScopes
     * @return array<int, string>
     */
    private function normalizeScopes(mixed $rawScopes): array
    {
        if (is_string($rawScopes)) {
            $decoded = json_decode($rawScopes, true);
            if (is_array($decoded)) {
                $rawScopes = $decoded;
            } else {
                $rawScopes = explode(',', $rawScopes);
            }
        }

        if (!is_array($rawScopes)) {
            return [];
        }

        $scopes = [];
        foreach ($rawScopes as $scope) {
            if (is_string($scope)) {
                $chunks = preg_split('/[\s,]+/', $scope, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                foreach ($chunks as $chunk) {
                    $scopes[] = trim($chunk);
                }
            }
        }

        return array_values(array_unique(array_filter($scopes)));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractExchangeCodeFromRequest(Request $request, array $data): string
    {
        foreach (['code', 'exchange_code'] as $key) {
            foreach ([$data[$key] ?? null, $request->query($key)] as $raw) {
                $s = $this->coerceScalarRequestValue($raw);
                if ($s !== '') {
                    return $s;
                }
            }
        }

        return '';
    }

    private function coerceScalarRequestValue(mixed $raw): string
    {
        if (is_array($raw)) {
            $raw = $raw[0] ?? null;
        }
        if ($raw === null || $raw === '' || is_bool($raw)) {
            return '';
        }
        if (is_int($raw) || is_float($raw)) {
            return trim((string) $raw);
        }

        return trim((string) $raw);
    }

    private function extractAccessToken(Request $request): string
    {
        $authHeader = (string) ($request->header('Authorization') ?? '');
        if ($authHeader !== '' && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }

        return trim((string) ($request->cookie('admin_access_token') ?? ''));
    }
} 