<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Models\User;
use App\Core\OAuth2\AdminTokenLifetime;
use App\Core\Providers\Microsoft;
use App\Core\Repositories\Auth\AccessTokenRepositoryInterface;
use App\Core\Repositories\Auth\ClientRepositoryInterface;
use App\Core\Repositories\Auth\RefreshTokenRepositoryInterface;
use App\Core\Repositories\Auth\ScopeRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\System\Cache\Redis;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Exception;
use Throwable;
use GuzzleHttp\Psr7\Response as Psr7Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseInterface;

use function App\Core\System\utils\env;

// from guzzlehttp/psr7
// from guzzlehttp/psr7

class AuthService
{
    // 1) Our locally hosted OAuth2 AuthorizationServer (for password + client credentials).
    private AuthorizationServer $authorizationServer;

    // 2) A reference to your local user repository for session-based lookups or password checks.
    private UserRepositoryInterface $userRepository;

    // 3) Third-party OAuth2 providers (Google, Facebook, etc.). You could store them in an array keyed by provider name.
    private Google $googleProvider;
    private Facebook $facebookProvider;
    private Microsoft $microsoftProvider;

    // Default session duration (seconds); overridden from config in constructor (typically 24h).
    private int $defaultSessionDuration = 86400;
    private ClientRepositoryInterface $clientRepository;
    private RefreshTokenRepositoryInterface $refreshTokenRepository;
    private ScopeRepositoryInterface $scopeRepository;
    private AccessTokenRepositoryInterface $accessTokenRepository;
    private ?string $lastMicrosoftOAuthError = null;
    private CustomerRepositoryInterface $customerRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private PinboardRepositoryInterface $pinboardRepository;

    private Redis $redis;

    /** Redis key namespace for auth payloads (see config auth_session.redis_namespace). */
    private string $redisSessionNamespace;

    /** HttpOnly cookie holding opaque session id. */
    private string $sessionCookieName;

    /** Wall-clock session length (seconds), default 24h. */
    private int $sessionTtlSeconds;

    private string $sessionCookieDomain = '';

    public function __construct(
        AuthorizationServer $authorizationServer,
        UserRepositoryInterface $userRepository,
        ClientRepositoryInterface $clientRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        ScopeRepositoryInterface $scopeRepository,
        AccessTokenRepositoryInterface $accessTokenRepository,
        CustomerRepositoryInterface $customerRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        PinboardRepositoryInterface $pinboardRepository,
        Google $googleProvider,
        Facebook $facebookProvider,
        Microsoft $microsoftProvider,
        Redis $redis,
        array $authSessionConfig = []
    ) {
        $this->authorizationServer = $authorizationServer;
        $this->userRepository      = $userRepository;
        $this->googleProvider      = $googleProvider;
        $this->facebookProvider    = $facebookProvider;
        $this->microsoftProvider   = $microsoftProvider;

        // In a real app, you'd have configured your $authorizationServer with
        // the appropriate grants (PasswordGrant, ClientCredentialsGrant),
        // RSA keys, etc.
        $this->clientRepository = $clientRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->scopeRepository = $scopeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->customerRepository = $customerRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->pinboardRepository = $pinboardRepository;

        $this->redis = $redis;
        $this->sessionTtlSeconds = max(60, (int) ($authSessionConfig['ttl_seconds'] ?? 86400));
        $this->defaultSessionDuration = $this->sessionTtlSeconds;
        $this->sessionCookieName = (string) ($authSessionConfig['cookie_name'] ?? 'krost_auth_sid');
        $this->redisSessionNamespace = (string) ($authSessionConfig['redis_namespace'] ?? 'auth_sess');
        $this->sessionCookieDomain = trim((string) ($authSessionConfig['cookie_domain'] ?? ''));
    }

    private function getSessionIdFromCookie(): string
    {
        return trim((string) ($_COOKIE[$this->sessionCookieName] ?? ''));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readRedisAuthPayload(): ?array
    {
        $sid = $this->getSessionIdFromCookie();
        if ($sid === '') {
            return null;
        }
        $raw = $this->redis->get($this->redisSessionNamespace, $sid);

        return is_array($raw) ? $raw : null;
    }

    /**
     * Active session user id from Redis (no token validation).
     */
    public function getAuthenticatedSessionUserId(): ?int
    {
        $p = $this->readRedisAuthPayload();
        if ($p === null) {
            return null;
        }
        $id = (int) ($p['user_id'] ?? 0);

        return $id > 0 ? $id : null;
    }

    /**
     * OAuth token JSON stored for the current browser session (for cookies / revocation).
     *
     * @return array<string, mixed>|null
     */
    public function getStoredOAuthTokenResponse(): ?array
    {
        $p = $this->readRedisAuthPayload();
        $o = $p['oauth_token_response'] ?? null;

        return is_array($o) ? $o : null;
    }

    /**
     * Comma-separated scope list persisted with the login session.
     */
    public function getSessionScopeString(): string
    {
        $p = $this->readRedisAuthPayload();

        return trim((string) ($p['auth_scopes'] ?? ''));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function writeRedisAuth(string $sessionId, array $payload, int $ttlSeconds): void
    {
        $this->redis->set($this->redisSessionNamespace, $sessionId, $payload, max(60, $ttlSeconds));
    }

    private function setSessionIdCookie(string $sessionId, ?int $ttlSeconds = null): void
    {
        if (headers_sent()) {
            return;
        }
        $ttl = max(60, $ttlSeconds ?? $this->sessionTtlSeconds);
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        $options = [
            'expires' => time() + $ttl,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ];
        if ($this->sessionCookieDomain !== '') {
            $options['domain'] = $this->sessionCookieDomain;
        }
        setcookie($this->sessionCookieName, $sessionId, $options);
        $_COOKIE[$this->sessionCookieName] = $sessionId;
    }

    private function expireSessionIdCookie(): void
    {
        if (headers_sent()) {
            return;
        }
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        setcookie($this->sessionCookieName, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
        unset($_COOKIE[$this->sessionCookieName]);
    }

    /**
     * Remove Redis auth record and session cookie (does not revoke OAuth tokens; use {@see logout()}).
     */
    private function destroyRedisAuthSession(): void
    {
        $sid = $this->getSessionIdFromCookie();
        if ($sid !== '') {
            $this->redis->delete($this->redisSessionNamespace, $sid);
        }
        $this->expireSessionIdCookie();
    }

    /**
     * @param array<int|string, mixed> $loginScopes
     */
    private function resolveScopeCsvForLogin(int $userId, array $loginScopes): string
    {
        if ($loginScopes !== []) {
            $strings = [];
            foreach ($loginScopes as $s) {
                if (is_string($s) && trim($s) !== '') {
                    $strings[] = trim($s);
                }
            }

            return implode(',', array_values(array_unique($strings)));
        }
        $dbScopes = $this->userRepository->getUserScopes($userId);
        if (!is_array($dbScopes)) {
            return '';
        }
        $strings = [];
        foreach ($dbScopes as $s) {
            if (is_string($s) && trim($s) !== '') {
                $strings[] = trim($s);
            }
        }

        return implode(',', array_values(array_unique($strings)));
    }

    /**
     * Replace any existing auth session with a full 24h (config) session + OAuth payload.
     *
     * @param array<string, mixed> $oauthTokenResponse
     * @param array<int|string, mixed> $scopesForCsv
     * @param int|null $sessionTtlSeconds If set, Redis TTL, session cookie expiry, and expire_at use this (minimum 60 seconds); otherwise the configured auth session TTL applies.
     */
    private function replaceFullAuthSession(int $userId, array $oauthTokenResponse, array $scopesForCsv, ?int $sessionTtlSeconds = null): void
    {
        $this->destroyRedisAuthSession();
        $sid = bin2hex(random_bytes(32));
        $now = time();
        $ttl = max(60, $sessionTtlSeconds ?? $this->sessionTtlSeconds);
        $payload = [
            'user_id' => $userId,
            'logged_in_at' => $now,
            'expire_at' => $now + $ttl,
            'oauth_token_response' => $oauthTokenResponse,
            'auth_scopes' => $this->resolveScopeCsvForLogin($userId, $scopesForCsv),
            'auth_session_ttl_seconds' => $ttl,
        ];
        $this->writeRedisAuth($sid, $payload, $ttl);
        $this->setSessionIdCookie($sid, $ttl);
    }

    /**
     * Persist user id (and optional carry-over of tokens) for flows that set OAuth in a second step.
     */
    private function savePartialUserSession(int $userId, ?int $sessionDuration = null): void
    {
        $ttl = $sessionDuration ?? $this->sessionTtlSeconds;
        $now = time();
        $existing = $this->readRedisAuthPayload();
        $sid = $this->getSessionIdFromCookie();
        if ($existing === null || $sid === '') {
            $sid = bin2hex(random_bytes(32));
            $this->setSessionIdCookie($sid);
        }
        $existingLoggedIn = is_array($existing) ? (int) ($existing['logged_in_at'] ?? 0) : 0;
        $existingTtlStored = is_array($existing) ? (int) ($existing['auth_session_ttl_seconds'] ?? 0) : 0;
        $payload = [
            'user_id' => $userId,
            'logged_in_at' => $existingLoggedIn > 0 ? $existingLoggedIn : $now,
            'expire_at' => $now + $ttl,
            'oauth_token_response' => is_array($existing) && is_array($existing['oauth_token_response'] ?? null)
                ? $existing['oauth_token_response']
                : null,
            'auth_scopes' => is_array($existing) ? (string) ($existing['auth_scopes'] ?? '') : '',
            'auth_session_ttl_seconds' => $existingTtlStored > 0 ? $existingTtlStored : max(60, $ttl),
        ];
        $this->writeRedisAuth($sid, $payload, $ttl);
    }

    private function tryRefreshOAuthInRedisSession(array $session): bool
    {
        $refresh = trim((string) ($session['oauth_token_response']['refresh_token'] ?? ''));
        if ($refresh === '') {
            return false;
        }
        try {
            $resp = $this->refreshToken($refresh);
            $body = $resp instanceof ResponseInterface
                ? json_decode((string) $resp->getBody(), true)
                : (is_array($resp) ? $resp : []);
            if (!is_array($body) || !isset($body['access_token']) || !is_string($body['access_token']) || $body['access_token'] === '') {
                return false;
            }
            $session['oauth_token_response'] = array_merge(
                is_array($session['oauth_token_response'] ?? null) ? $session['oauth_token_response'] : [],
                $body
            );
            $slideTtl = max(60, (int) ($session['auth_session_ttl_seconds'] ?? $this->sessionTtlSeconds));
            $session['expire_at'] = time() + $slideTtl;
            $session['auth_session_ttl_seconds'] = $slideTtl;
            $sid = $this->getSessionIdFromCookie();
            if ($sid === '') {
                return false;
            }
            $this->writeRedisAuth($sid, $session, $slideTtl);

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Register a new user and assign the default scope.
     */
    public function registerUser(string $name, string $email, string $password, array $scopes = [], bool $isUserVerified = false): array
    {
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser) {
            throw new Exception('User already exists');
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $displayName = trim($name);
        $nameParts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $emailLocalPart = (string) explode('@', $email)[0];
        $username = $emailLocalPart !== '' ? $emailLocalPart : 'user';

        // Create the user
        $user = $this->userRepository->create([
            'uuid' => $this->generateBinaryUuidV4(),
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $hashedPassword,
            'display_name' => $displayName !== '' ? $displayName : null,
            'is_verified' => $isUserVerified ? 1 : 0,
        ]);

        // CHECK IF CUSTOMER EXISTS
        $customer = $this->customerRepository->findByUserId($user->user_id);
        if (empty($customer)) {
            $customerData = [
                'user_id' => $user->user_id,
                'organisation_id' => 1,
                'org_code' => 'ORG-' . $user->user_id,
                'name' => $firstName ?? $email,
                'gmail_Id' => $email,
                'company_name' => '',
                'is_verified' => $isUserVerified ? 1 : 0
            ];
            // CREATE THE CUSTOMER
            $customer = $this->customerRepository->createCustomer($customerData);
        }

        if (!$user || !$user->user_id) {
            throw new Exception('Failed to create user');
        }
        $userId = $user->user_id;

        //Need to write code to add other scopes such as admin, customer, vendor etc.
        $scopes = array_merge($scopes, ['user']);
        $scopes = implode(',', $scopes);

        // Assign default scope
        $scope = $this->scopeRepository->create([
            'user_id' => $userId,
            'scopes' => $scopes, // Default scope
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
        return ['userId' => $userId, 'customer_id' => $customer['customer_id'], 'scope' => $scope];
    }

    /**
     * Generate RFC4122-like UUID v4 bytes for BINARY(16) columns.
     */
    private function generateBinaryUuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return $bytes;
    }

    /* --------------------------------------------------------
     * 1) Username/Password Flow -> Issue JWT from OAuth2 Server
     * -------------------------------------------------------- */
    public function loginWithPasswordGrant(string $email, string $password): ?array
    {
        // We'll create a server-side request representing the token request:
        $request = (new ServerRequest('POST', '/token'))
            ->withParsedBody([
                'grant_type'    => 'password',
                'username'      => $email,
                'password'      => $password,
                // Possibly add 'client_id' and 'client_secret' if your server requires them
                'client_id'     => $_ENV['OAUTH_CLIENT_ID'] ?? 'your-client-id',
                'client_secret' => $_ENV['OAUTH_CLIENT_SECRET'] ?? 'your-client-secret',
            ]);

        // Create an empty PSR-7 response to hold the output
        $response = new Psr7Response();

        try {
            // The AuthorizationServer will validate credentials via your UserRepository
            $response = $this->authorizationServer->respondToAccessTokenRequest($request, $response);

            $data = json_decode((string)$response->getBody(), true);
            if (isset($data['access_token'])) {
                // Return the entire token response, e.g. { access_token, token_type, expires_in, ...}
                return $data;
            }
            return null;
        } catch (Exception $e) {
            // Log or handle error
            return null;
        }
    }

    /* --------------------------------------------------------
     * 2) Client Credentials -> Issue Token for Server-to-Server
     * -------------------------------------------------------- */
    /**
     * Register a new client.
     */
    public function registerClient(string $name, array $scopes, ?string $redirectUri = null): array
    {
        $secret = bin2hex(random_bytes(32));
        $hashedSecret = password_hash($secret, PASSWORD_BCRYPT);

        $clientId = $this->clientRepository->create([
            'name' => $name,
            'secret' => $hashedSecret,
            'scopes' => json_encode($scopes),
            'redirect_uri' => $redirectUri,
            'is_confidential' => true,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return [
            'client_id' => $clientId,
            'client_secret' => $secret,
        ];
    }

    /**
     * Get a client token using client credentials.
     */
    public function getClientToken(string $clientId, string $clientSecret, array $scopes = []): array|ResponseInterface
    {
        $client = $this->clientRepository->find((int) $clientId);

        if (!$client || !password_verify($clientSecret, $client['secret'])) {
            throw new \Exception('Invalid client credentials');
        }

        return $this->authorizationServer->respondToAccessTokenRequest(
            $this->createClientCredentialsRequest($clientId, $clientSecret, $scopes),
            new \GuzzleHttp\Psr7\Response()
        );
    }

    /**
     * Validate an access token.
     *
     * @param string $token
     * @return array|null Returns token details or null if invalid.
     * @throws Exception If the token is invalid or revoked.
     */
    public function validateToken(string $token): ?array
    {
        // Validate the token via the repository
        $accessToken = $this->accessTokenRepository->validateAccessToken($token);

        if (!$accessToken) {
            throw new Exception('Invalid or expired token.');
        }

        // Check if the token is associated with a user
        if (isset($accessToken['user_id'])) {
            $user = $this->userRepository->find($accessToken['user_id']);
            if (!$user) {
                throw new Exception('User associated with the token not found.');
            }

            return [
                'type' => 'user',
                'entity' => $user,
                'scopes' => $accessToken['scopes'],
            ];
        }

        // Check if the token is associated with a client
        if (isset($accessToken['client_id'])) {
            $client = $this->clientRepository->getClientEntity($accessToken['client_id']);
            if (!$client) {
                throw new Exception('Client associated with the token not found.');
            }

            return [
                'type' => 'client',
                'entity' => $client,
                'scopes' => $accessToken['scopes'],
            ];
        }

        // If no user or client is associated with the token, it is invalid
        throw new Exception('Invalid token.');
    }

    /**
     * Refresh a client token.
     */
    public function refreshToken(string $refreshToken): array|ResponseInterface
    {
        $refreshToken = trim($refreshToken);
        if ($refreshToken === '') {
            throw new \Exception('Invalid or expired refresh token');
        }

        $tokenData = $this->refreshTokenRepository->findValidToken($refreshToken);
        if ($tokenData) {
            $client = $this->clientRepository->find($tokenData['client_id']);
            if (!$client) {
                throw new \Exception('Client not found');
            }

            $accessTokenId = (string) ($tokenData['access_token_id'] ?? '');
            $scopes = $accessTokenId !== ''
                ? $this->accessTokenRepository->findScopesByTokenId($accessTokenId)
                : null;
            if (AdminTokenLifetime::scopesIncludeAdminFromStored($scopes)) {
                $userId = (int) ($tokenData['user_id'] ?? 0);
                if ($userId > 0) {
                    $reissued = $this->issueOAuth2TokensForUserId($userId, ['admin']);
                    if (is_array($reissued) && isset($reissued['access_token'])) {
                        return $reissued;
                    }
                }
            }
        }

        try {
            $response = $this->authorizationServer->respondToAccessTokenRequest(
                $this->createRefreshTokenRequest($refreshToken),
                new \GuzzleHttp\Psr7\Response()
            );
            if ($response instanceof ResponseInterface) {
                $body = json_decode((string) $response->getBody(), true);
                if (is_array($body)) {
                    return $body;
                }
            }

            return $response;
        } catch (\Throwable $e) {
            throw new \Exception('Invalid or expired refresh token');
        }
    }

    private function createClientCredentialsRequest(string $clientId, string $clientSecret, array $scopes): \Psr\Http\Message\ServerRequestInterface
    {
        return (new \GuzzleHttp\Psr7\ServerRequest('POST', '/token'))
            ->withParsedBody([
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => implode(' ', $scopes),
            ]);
    }

    private function createRefreshTokenRequest(string $refreshToken): \Psr\Http\Message\ServerRequestInterface
    {
        $parsedBody = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        $clientId = env('OAUTH_CLIENT_ID');
        $clientSecret = env('OAUTH_CLIENT_SECRET');
        if (is_string($clientId) && $clientId !== '') {
            $parsedBody['client_id'] = $clientId;
        }
        if (is_string($clientSecret) && $clientSecret !== '') {
            $parsedBody['client_secret'] = $clientSecret;
        }

        return (new \GuzzleHttp\Psr7\ServerRequest('POST', '/token'))
            ->withParsedBody($parsedBody);
    }

    /* --------------------------------------------------------
     * 3) Session-Based Authentication (Redis + opaque cookie)
     * -------------------------------------------------------- */

    /**
     * Attempt to log in the user via local session. If success, store user info in Redis.
     */
    public function loginSession(string $email, string $password, ?int $sessionDuration = null): bool
    {
        // Check the user in DB
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }

        // Verify password
        // if (!password_verify($password, $user->getPassword())) {
        //     return false;
        // }

        $duration = $sessionDuration ?? $this->defaultSessionDuration;
        $this->savePartialUserSession((int) $user->user_id, $duration);

        return true;
    }

    /**
     * Start a Redis-backed session for a user id (e.g. after Google/Microsoft OAuth, before tokens are attached).
     */
    public function loginSessionForUserId(int $userId, ?int $sessionDuration = null): bool
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return false;
        }

        $this->savePartialUserSession($userId, $sessionDuration);

        return true;
    }

    /**
     * Issue local OAuth2 access (and refresh) tokens for a user after trusted server-side auth (e.g. social login).
     * Requires OAUTH_INTERNAL_TOKEN_SECRET and first-party client credentials in the environment.
     */
    public function issueOAuth2TokensForUserId(int $userId, array $scopes = []): ?array
    {
        $internalSecret = env('OAUTH_INTERNAL_TOKEN_SECRET') ?? '';
        $clientId = env('OAUTH_CLIENT_ID') ?? '';
        $clientSecret = env('OAUTH_CLIENT_SECRET') ?? '';
        if ($internalSecret === '' || $clientId === '' || $clientSecret === '') {
            return null;
        }

        $request = (new ServerRequest('POST', '/token'))
            ->withParsedBody([
                'grant_type' => 'internal_sso',
                'user_id' => (string) $userId,
                'internal_secret' => $internalSecret,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => implode(' ', array_values(array_unique(array_filter($scopes, static fn ($scope) => is_string($scope) && trim($scope) !== '')))),
            ]);

        $response = new Psr7Response();

        try {
            $response = $this->authorizationServer->respondToAccessTokenRequest($request, $response);
            $data = json_decode((string) $response->getBody(), true);

            return isset($data['access_token']) ? $data : null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Exchange Google authorization code for profile data (email, name).
     */
    public function getGoogleProfileFromAuthCode(string $authCode): ?array
    {
        try {
            $token = $this->googleProvider->getAccessToken('authorization_code', [
                'code' => $authCode,
            ]);
            $owner = $this->googleProvider->getResourceOwner($token);

            return $owner->toArray();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Exchange Microsoft authorization code for profile data (email, name).
     */
    public function getMicrosoftProfileFromAuthCode(string $authCode): ?array
    {
        $this->lastMicrosoftOAuthError = null;

        try {
            $token = $this->microsoftProvider->getAccessToken('authorization_code', [
                'code' => $authCode,
            ]);
            $owner = $this->microsoftProvider->getResourceOwner($token);

            return $owner->toArray();
        } catch (Exception $e) {
            $this->lastMicrosoftOAuthError = $e->getMessage();
            return null;
        }
    }

    public function getLastMicrosoftOAuthError(): ?string
    {
        return $this->lastMicrosoftOAuthError;
    }

    /**
     * Find or register a user from a social profile, then create API tokens + web session.
     *
     * @param  array<string, mixed> $normalizedProfile Must include non-empty 'email'
     * @return array{tokens: ?array, user: User}
     */
    public function completeSocialLoginWithSessionAndTokens(array $normalizedProfile): array
    {
        $email = trim((string) ($normalizedProfile['email'] ?? ''));
        if ($email === '') {
            throw new Exception('Social profile did not include an email address.');
        }

        $name = trim((string) ($normalizedProfile['name'] ?? 'User'));
        if ($name === '') {
            $name = 'User';
        }

        $user = $this->userRepository->findByEmailSimple($email);
        if (!$user) {
            $this->registerUser($name, $email, bin2hex(random_bytes(32)), ['user']);
            $user = $this->userRepository->findByEmailSimple($email);
        }

        if (!$user) {
            throw new Exception('Could not load or create user after social login.');
        }

        $userId = (int) $user->user_id;
        $tokens = $this->issueOAuth2TokensForUserId($userId);

        $oauthPayload = $tokens !== null ? $tokens : [];
        $this->replaceFullAuthSession($userId, $oauthPayload, []);

        return ['tokens' => $tokens, 'user' => $user];
    }

    /**
     * Start a PHP session and issue local OAuth2 tokens for an existing user (e.g. after OTP verification).
     * Persists tokens in Redis for API use and server-side logout revocation.
     * Loads user, customer, and active pinboard from the database for the response payload.
     *
     * @return array{
     *     status: 200,
     *     success: true,
     *     message: string,
     *     user: array<string, mixed>|null,
     *     customer: array<string, mixed>|null,
     *     auth: array<string, mixed>,
     *     pinboard: array<string, mixed>
     * }
     *
     * @throws Exception If the user cannot be logged in or token issuance fails
     */
    public function login(int $userId, array $scopes = [], ?int $sessionTtlSeconds = null): array
    {
        if ($userId <= 0) {
            throw new Exception('Invalid user id');
        }

        $userEntity = $this->userRepository->find($userId);
        if ($userEntity === null) {
            throw new Exception('Failed to create login session');
        }

        try {
            $tokenData = $this->issueOAuth2TokensForUserId($userId, $scopes);
        } catch (Exception $e) {
            throw $e;
        }

        if ($tokenData === null || !isset($tokenData['access_token'])) {
            throw new Exception('Failed to generate OAuth tokens');
        }

        $this->replaceFullAuthSession($userId, $tokenData, $scopes, $sessionTtlSeconds);

        $sessionTtl = max(60, $sessionTtlSeconds ?? $this->sessionTtlSeconds);
        $tokenExpiresIn = isset($tokenData['expires_in']) ? (int) $tokenData['expires_in'] : 0;
        if (AdminTokenLifetime::scopesIncludeAdmin($scopes) && $tokenExpiresIn > 0 && $tokenExpiresIn < AdminTokenLifetime::SESSION_TTL_SECONDS) {
            error_log(sprintf(
                'admin login: access token expires_in=%d is shorter than session TTL=%d; check InternalSsoGrant admin scope',
                $tokenExpiresIn,
                AdminTokenLifetime::SESSION_TTL_SECONDS
            ));
        }

        $userPayload = null;
        if (isset($userEntity->data)) {
            $decoded = json_decode(json_encode($userEntity->data), true);
            $userPayload = is_array($decoded) ? $decoded : null;
        }

        $customerPayload = $this->customerRepository->findByUserId($userId);
        if ($customerPayload !== [] && $userPayload !== null && isset($userPayload['email'])) {
            $customerPayload['email'] = $userPayload['email'];
        } elseif ($customerPayload === []) {
            $customerPayload = null;
        }

        $customerId = isset($customerPayload['customer_id']) ? (int) $customerPayload['customer_id'] : null;
        $pinboardPayload = $customerId !== null && $customerId > 0
            ? $this->pinboardRepository->getCustomerPinboard($customerId)
            : $this->pinboardRepository->getUserPinboard($userId);

        return [
            'status' => 200,
            'success' => true,
            'message' => 'Email verified, authentication completed, and pinboard created successfully',
            'user' => $userPayload,
            'customer' => $customerPayload,
            'auth' => [
                'session' => true,
                'token_type' => $tokenData['token_type'] ?? 'Bearer',
                'access_token' => $tokenData['access_token'] ?? null,
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_in' => isset($tokenData['expires_in']) ? (int) $tokenData['expires_in'] : null,
                'session_ttl_seconds' => $sessionTtl,
                'session_expires_at' => time() + $sessionTtl,
            ],
            'pinboard' => $pinboardPayload,
        ];
    }

    /**
     * Revoke locally issued OAuth2 tokens stored in Redis, then destroy the auth session cookie.
     * Access tokens are JWTs (jti = access_tokens.id); refresh tokens are Defuse-encrypted JSON
     * containing refresh_token_id and access_token_id (same format as League BearerTokenResponse).
     */
    public function logout(): void
    {
        $oauthResponse = $this->getStoredOAuthTokenResponse();
        $this->revokeIssuedOAuthTokensFromStoredResponse($oauthResponse);

        $this->destroyRedisAuthSession();
        $this->expireAuthCookie('access_token', true);
        $this->expireAuthCookie('admin_access_token', true);
        $this->expireAuthCookie('admin_token_type', false);
        $this->expireAuthCookie('auth_present', false);
        $this->expireAuthCookie('admin_refresh_token', true);
    }

    private function expireAuthCookie(string $name, bool $httpOnly): void
    {
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';

        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => $httpOnly,
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
     * Alias for {@see logout()} — used by web controllers and session expiry handling.
     */
    public function logoutSession(): void
    {
        $this->logout();
    }

    /**
     * @param  array<string, mixed>|null $response Token JSON from AuthorizationServer (access_token, refresh_token, …)
     */
    private function revokeIssuedOAuthTokensFromStoredResponse(?array $response): void
    {
        if ($response === null || $response === []) {
            return;
        }

        $accessTokenIds = [];
        $accessJwt = $response['access_token'] ?? null;
        if (is_string($accessJwt) && $accessJwt !== '') {
            $jti = $this->extractJwtJti($accessJwt);
            if ($jti !== null) {
                $accessTokenIds[$jti] = true;
            }
        }

        $refreshId = null;
        $refreshOpaque = $response['refresh_token'] ?? null;
        if (is_string($refreshOpaque) && $refreshOpaque !== '') {
            $decrypted = $this->decryptRefreshTokenPayload($refreshOpaque);
            if ($decrypted !== null) {
                $payload = json_decode($decrypted, true);
                if (is_array($payload)) {
                    $atId = $payload['access_token_id'] ?? null;
                    if (is_string($atId) && $atId !== '') {
                        $accessTokenIds[$atId] = true;
                    }
                    $rtId = $payload['refresh_token_id'] ?? null;
                    if (is_string($rtId) && $rtId !== '') {
                        $refreshId = $rtId;
                    }
                }
            }
        }

        foreach (array_keys($accessTokenIds) as $tokenId) {
            try {
                $this->accessTokenRepository->revokeAccessToken($tokenId);
            } catch (Throwable $e) {
                // Best-effort revocation
            }
        }

        if ($refreshId !== null) {
            try {
                $this->refreshTokenRepository->revokeRefreshToken($refreshId);
            } catch (Throwable $e) {
                // Best-effort revocation
            }
        }
    }

    private function extractJwtJti(string $jwt): ?string
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null;
        }
        $payload = $parts[1];
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
        $decoded = base64_decode(strtr($payload, '-_', '+/'), true);
        if ($decoded === false) {
            return null;
        }
        $data = json_decode($decoded, true);
        if (!is_array($data) || !isset($data['jti']) || !is_string($data['jti']) || $data['jti'] === '') {
            return null;
        }

        return $data['jti'];
    }

    private function decryptRefreshTokenPayload(string $encryptedRefresh): ?string
    {
        $keyPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'encryption.key';
        if (!is_readable($keyPath)) {
            return null;
        }
        try {
            $key = Key::loadFromAsciiSafeString(trim((string) file_get_contents($keyPath)));

            return Crypto::decrypt($encryptedRefresh, $key);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Check if a user is logged in via Redis session + OAuth access (refresh attempted when access JWT expired).
     */
    public function isLoggedIn(): bool
    {
        $session = $this->readRedisAuthPayload();
        if ($session === null) {
            return false;
        }

        if (time() > (int) ($session['expire_at'] ?? 0)) {
            $this->logoutSession();

            return false;
        }

        $userId = (int) ($session['user_id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }

        $user = $this->userRepository->find($userId);
        if ($user === null) {
            return false;
        }

        $oauthResponse = is_array($session['oauth_token_response'] ?? null)
            ? $session['oauth_token_response']
            : null;
        $accessToken = trim((string) ($oauthResponse['access_token'] ?? ''));
        if ($accessToken === '') {
            return false;
        }

        try {
            $token = $this->validateToken($accessToken);
        } catch (Throwable $e) {
            if (!$this->tryRefreshOAuthInRedisSession($session)) {
                return false;
            }
            $session = $this->readRedisAuthPayload();
            if ($session === null) {
                return false;
            }
            $oauthResponse = is_array($session['oauth_token_response'] ?? null)
                ? $session['oauth_token_response']
                : null;
            $accessToken = trim((string) ($oauthResponse['access_token'] ?? ''));
            if ($accessToken === '') {
                return false;
            }
            try {
                $token = $this->validateToken($accessToken);
            } catch (Throwable $e2) {
                return false;
            }
        }

        if (!is_array($token) || ($token['type'] ?? null) !== 'user') {
            return false;
        }

        $tokenUser = $token['entity'] ?? null;
        $tokenUserId = null;
        if (is_object($tokenUser)) {
            if (isset($tokenUser->user_id)) {
                $tokenUserId = (int) $tokenUser->user_id;
            } elseif (isset($tokenUser->data->user_id)) {
                $tokenUserId = (int) $tokenUser->data->user_id;
            }
        }

        return $tokenUserId !== null && $tokenUserId === $userId;
    }

    /**
     * Check if the current session user has the "admin" scope (Redis snapshot or live DB scopes).
     */
    public function isAdmin(): bool
    {
        $session = $this->readRedisAuthPayload();
        if ($session === null) {
            return false;
        }

        $csv = trim((string) ($session['auth_scopes'] ?? ''));
        if ($csv !== '') {
            $authScopes = array_map('trim', explode(',', $csv));

            return in_array('admin', $authScopes, true);
        }

        $userId = (int) ($session['user_id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }

        $scopes = $this->userRepository->getUserScopes($userId);

        return is_array($scopes) && in_array('admin', $scopes, true);
    }

    public function getAuthUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        $id = $this->getAuthenticatedSessionUserId();

        return $id !== null ? $this->userRepository->find($id) : null;
    }

    /* --------------------------------------------------------
     * 4) Third-Party (Google / Facebook) Logins
     * -------------------------------------------------------- */

    /**
     * Return the URL to which we redirect the user to authenticate with Google.
     */
    public function getGoogleAuthUrl(array $scopes = []): string
    {
        if ($scopes !== []) {
            return $this->googleProvider->getAuthorizationUrl(['scope' => $scopes]);
        }

        return $this->googleProvider->getAuthorizationUrl();
    }

    /**
     * Similarly for Facebook.
     */
    public function getFacebookAuthUrl(): string
    {
        return $this->facebookProvider->getAuthorizationUrl();
    }

    public function handleFacebookCallback(string $authCode): ?array
    {
        try {
            $token = $this->facebookProvider->getAccessToken('authorization_code', [
                'code' => $authCode
            ]);
            $owner = $this->facebookProvider->getResourceOwner($token);
            return $owner->toArray();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getMicrosoftAuthUrl(array $scopes = []): string
    {
        if ($scopes !== []) {
            return $this->microsoftProvider->getAuthorizationUrl(['scope' => $scopes]);
        }

        return $this->microsoftProvider->getAuthorizationUrl();
    }

    /**
     * Map raw Google resource owner array to email + display name.
     *
     * @param  array<string, mixed> $data
     * @return array{email: ?string, name: string}
     */
    public function normalizeGoogleProfile(array $data): array
    {
        $email = isset($data['email']) ? strtolower(trim((string) $data['email'])) : null;
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            $name = trim((string) (($data['given_name'] ?? '') . ' ' . ($data['family_name'] ?? '')));
        }

        return ['email' => $email ?: null, 'name' => $name !== '' ? $name : 'User'];
    }

    /**
     * Map raw Microsoft / Live resource owner array to email + display name.
     *
     * @param  array<string, mixed> $data
     * @return array{email: ?string, name: string}
     */
    public function normalizeMicrosoftProfile(array $data): array
    {
        $email = $data['mail']
            ?? $data['userPrincipalName']
            ?? ($data['emails']['preferred'] ?? null);
        $email = $email !== null ? strtolower(trim((string) $email)) : null;
        $name = trim((string) ($data['displayName'] ?? $data['name'] ?? ''));
        if ($name === '') {
            $name = trim((string) (($data['givenName'] ?? $data['first_name'] ?? '') . ' ' . ($data['surname'] ?? $data['last_name'] ?? '')));
        }

        return ['email' => $email ?: null, 'name' => $name !== '' ? $name : 'User'];
    }

    /**
     * Check if an email is already registered.
     *
     * @param string $email
     * @return bool
     */
    public function isEmailRegistered(string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);
        return $user !== null;
    }
}

