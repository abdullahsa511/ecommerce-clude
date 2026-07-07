<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\CsrfService;

class CsrfMiddleware
{
    private CsrfService $csrfService;
    private array $exemptRoutes = [
        '/api/',  // Exempt API routes if needed
    ];

    public function __construct(CsrfService $csrfService)
    {
        $this->csrfService = $csrfService;
    }

    public function handle(Request $request, callable $next): Response
    {
        // Only check POST requests
        if ($request->getMethod() !== 'POST') {
            return $next($request);
        }

        // Check if route is exempt
        $uri = $request->getUri();
        foreach ($this->exemptRoutes as $exemptRoute) {
            if (strpos($uri, $exemptRoute) === 0) {
                return $next($request);
            }
        }

        // Accept either csrf_token (middleware / API-style) or nonce (Blade/HTML forms).
        $csrfToken = (string) ($request->input($this->csrfService->getTokenName()) ?? '');
        if ($csrfToken === '') {
            $csrfToken = (string) ($request->input('nonce') ?? '');
        }
        if (!$this->csrfService->validateToken($csrfToken)) {
            return new Response(
                'CSRF token validation failed',
                403,
                ['Content-Type' => 'text/html']
            );
        }

        return $next($request);
    }
}