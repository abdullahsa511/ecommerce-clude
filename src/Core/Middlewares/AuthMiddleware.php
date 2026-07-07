<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\AuthService;

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        $auth = $request->getAttribute('auth');
        $user = is_array($auth) ? ($auth['entity'] ?? null) : $request->getAttribute('user');

        // Check for user authentication
        if (!$user) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
                $token = substr($authHeader, 7); // Remove "Bearer " prefix
                try {
                    $auth = $this->authService->validateToken($token);
                    $request->setAttribute('auth', $auth);
                    $request->setAttribute('user', $auth['entity'] ?? null);
                }catch (\Exception $exception){
                    return $this->unauthorizedResponse('Invalid or expired token.');
                }
            } else {
                if (!$this->authService->isLoggedIn()) {
                    return $this->unauthorizedResponse('Not authenticated.');
                }

                $sessionUser = $this->authService->getAuthUser();
                $scopeStr = $this->authService->getSessionScopeString();
                $auth = [
                    'type' => 'session',
                    'entity' => $sessionUser,
                    'scopes' => $scopeStr !== ''
                        ? array_values(array_filter(array_map('trim', explode(',', $scopeStr))))
                        : [],
                ];

                $request->setAttribute('auth', $auth);
                $request->setAttribute('user', $sessionUser);
            }
        } else {
            $request->setAttribute('auth', [
                'type' => 'request',
                'entity' => $user,
                'scopes' => [],
            ]);
        }

        return $next($request);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    private function unauthorizedResponse(string $message): Response
    {
        $body = json_encode(['error' => 'unauthorized', 'message' => $message]);
        return new Response($body, 401, ['Content-Type' => 'application/json']);
    }
}
