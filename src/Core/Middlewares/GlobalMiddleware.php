<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Repositories\Auth\AccessTokenRepository;
use App\Core\Repositories\Auth\ClientRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;


class GlobalMiddleware
{
    protected ClientRepositoryInterface $clientRepository;
    protected AccessTokenRepository $accessTokenRepository;
    private UserRepositoryInterface $userRepository;
    private AuthService $authService;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        AccessTokenRepository $accessTokenRepository,
        UserRepositoryInterface $userRepository,
        AuthService $authService
    ) {
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    /**
     * Handle the incoming request and set `user` and `oauth_scopes` attributes.
     *
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, callable $next): mixed
    {
        $request->setAttribute('user', null);
        $request->setAttribute('oauth_scopes', null);
        // 1. Check session via AuthService to avoid relying on nullable request attributes.
        if ($this->authService->isLoggedIn()) {
            $sessionUserId = $this->authService->getAuthenticatedSessionUserId();
            if ($sessionUserId !== null && $sessionUserId > 0) {
                $user = $this->userRepository->find($sessionUserId);
                if ($user) {
                    $scopes = $this->userRepository->getUserScopes($sessionUserId);
                    $request->setAttribute('user', $user);
                    $request->setAttribute('oauth_scopes', $scopes);
                    $this->ensureAdminAccessTokenCookie($request, $sessionUserId);

                    return $next($request);
                }
            }
        }

        // 2. Check server-to-server request using ClientRepository
        $authHeader = $request->header('Authorization');
        $accessToken = null;
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7); // Remove "Bearer " prefix
            $accessToken = $this->accessTokenRepository->validateAccessToken($token);
        }
        if(!$accessToken){
            $queryToken = $request->query('access_token');
            if ($queryToken) {
                $accessToken = $this->accessTokenRepository->validateAccessToken($queryToken);
            }
        }
        if ($accessToken) {
            if(isset($accessToken['user_id'])){
                $user = $this->userRepository->find($accessToken['user_id']);
                if ($user) {
                    $scopes = $this->userRepository->getUserScopes($user->id);
                    $request->setAttribute('user', $user);
                    $request->setAttribute('oauth_scopes', $scopes);
                    return $next($request);
                }
            }
            if(isset($accessToken['client_id'])){
                $client = $this->clientRepository->getClientEntity($accessToken['client_id']);
                if ($client) {
                    $scopes = $client->getScopes();
                    $request->setAttribute('client', $client);
                    $request->setAttribute('oauth_scopes', $scopes);
                    return $next($request);
                }
            }
        }

        // If all checks fail, continue without setting `user` or `oauth_scopes`
        return $next($request);
    }

    private function ensureAdminAccessTokenCookie(Request $request, int $userId): void
    {
        $currentAdminAccessToken = trim((string) ($request->cookie('admin_access_token') ?? ''));
        if ($currentAdminAccessToken !== '') {
            return;
        }

        $fallbackToken = trim((string) ($request->cookie('access_token') ?? ''));
        if ($fallbackToken === '') {
            $authHeader = (string) ($request->header('Authorization') ?? '');
            if ($authHeader !== '' && str_starts_with($authHeader, 'Bearer ')) {
                $fallbackToken = trim(substr($authHeader, 7));
            }
        }
        if ($fallbackToken === '') {
            $fallbackToken = trim((string) ($request->query('access_token') ?? ''));
        }
        if ($fallbackToken === '') {
            $stored = $this->authService->getStoredOAuthTokenResponse();
            if (is_array($stored)) {
                $fallbackToken = trim((string) ($stored['access_token'] ?? ''));
            }
        }
        if ($fallbackToken === '') {
            $latestToken = $this->accessTokenRepository->findLatestValidTokenByUserId($userId);
            $fallbackToken = trim((string) ($latestToken['token'] ?? ''));
        }
        if ($fallbackToken === '') {
            return;
        }

        $validatedToken = $this->accessTokenRepository->validateAccessToken($fallbackToken);
        if (!$validatedToken || (int) ($validatedToken['user_id'] ?? 0) !== $userId) {
            return;
        }

        $expiresAt = strtotime((string) ($validatedToken['expires_at'] ?? ''));
        $maxAgeSeconds = \App\Core\OAuth2\AdminTokenLifetime::SESSION_TTL_SECONDS;
        if ($expiresAt !== false) {
            $maxAgeSeconds = max(60, $expiresAt - time());
        }

        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        setcookie('admin_access_token', $fallbackToken, [
            'expires' => time() + $maxAgeSeconds,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
        setcookie('auth_present', '1', [
            'expires' => time() + $maxAgeSeconds,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => false,
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
}
