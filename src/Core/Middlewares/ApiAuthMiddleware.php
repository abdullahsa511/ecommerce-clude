<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\AuthService;

class ApiAuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an API request with token authentication.
     */
    public function handle(Request $request, callable $next): Response
    {
        $token = $this->extractAccessToken($request);
        if ($token === '') {
            return $this->unauthorizedResponse('Missing access token (Authorization header or admin_access_token cookie).');
        }

        try {
            $auth = $this->authService->validateToken($token);
        } catch (\Throwable $e) {
            return $this->unauthorizedResponse('Invalid or expired token.');
        }

        if (!is_array($auth) || (($auth['type'] ?? null) !== 'user')) {
            return $this->forbiddenResponse('User token is required.');
        }

        $scopes = $this->normalizeScopes($auth['scopes'] ?? []);

        $user = $auth['entity'] ?? null;
        if (!$this->isAdminUser($user)) {
            return $this->forbiddenResponse('Admin user is required.');
        }

        $request->setAttribute('auth', [
            'type' => 'api',
            'entity' => $user,
            'scopes' => $scopes,
        ]);
        $request->setAttribute('user', $user);

        return $next($request);
    }

    private function extractAccessToken(Request $request): string
    {
        $authHeader = (string) ($request->header('Authorization') ?? '');
        if ($authHeader !== '' && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }

        return trim((string) ($request->cookie('admin_access_token') ?? ''));
    }

    /**
     * Return a 401 Unauthorized response.
     */
    private function unauthorizedResponse(string $message): Response
    {
        $body = json_encode(['error' => 'unauthorized', 'message' => $message]);
        return new Response($body, 401, ['Content-Type' => 'application/json']);
    }

    private function forbiddenResponse(string $message): Response
    {
        $body = json_encode(['error' => 'forbidden', 'message' => $message]);
        return new Response($body, 403, ['Content-Type' => 'application/json']);
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
     * @param mixed $user
     */
    private function isAdminUser(mixed $user): bool
    {
        if (!is_object($user)) {
            return false;
        }

        if (isset($user->is_admin) && (bool) $user->is_admin) {
            return true;
        }

        if (isset($user->data->is_admin) && (bool) $user->data->is_admin) {
            return true;
        }

        return false;
    }
}
