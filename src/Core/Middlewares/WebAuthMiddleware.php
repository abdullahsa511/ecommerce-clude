<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\AuthService;

class WebAuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request using Redis-backed session authentication (opaque cookie).
     */
    public function handle(Request $request, callable $next): ?Response
    {
        $auth = $request->getAttribute('auth');
        $user = is_array($auth) ? ($auth['entity'] ?? null) : $request->getAttribute('user');

        if (!$user) {
            if (!$this->authService->isLoggedIn()) {
                return $this->redirectToLogin();
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
        } else {
            $request->setAttribute('auth', [
                'type' => 'request',
                'entity' => $user,
                'scopes' => [],
            ]);
        }

        return $next($request);
    }

    private function redirectToLogin(): Response
    {
        return new Response('', 302, ['Location' => '/login']);
    }
}
