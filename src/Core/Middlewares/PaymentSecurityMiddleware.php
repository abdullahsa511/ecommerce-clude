<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Origin validation and security headers for payment endpoints.
 */
class PaymentSecurityMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        if ($request->getMethod() === 'POST' && !$this->isAllowedOrigin($request)) {
            return new Response(
                json_encode(['ok' => false, 'error' => 'Invalid request origin.']),
                403,
                ['Content-Type' => 'application/json']
            );
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->applySecurityHeaders($request, $response);
    }

    public function applySecurityHeaders(Request $request, Response $response): Response
    {
        $host = $this->requestHost($request);
        $cybersource = 'https://testflex.cybersource.com https://flex.cybersource.com https://testup.cybersource.com https://up.cybersource.com https://apitest.cybersource.com https://api.cybersource.com';
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' {$cybersource}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data:",
            "frame-src {$cybersource}",
            "connect-src 'self' https://{$host} http://{$host} {$cybersource}",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);

        $response = $response
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->withHeader('Content-Security-Policy', $csp)
            ->withHeader('Permissions-Policy', 'payment=(self)');

        if ($this->isHttpsRequest()) {
            $response = $response->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function isAllowedOrigin(Request $request): bool
    {
        $host = strtolower($this->requestHost($request));
        if ($host === '') {
            return false;
        }

        $origin = trim((string) ($request->header('Origin') ?? ''));
        if ($origin !== '') {
            return $this->hostMatchesOrigin($host, $origin);
        }

        $referer = trim((string) ($request->header('Referer') ?? ''));
        if ($referer !== '') {
            $refererHost = parse_url($referer, PHP_URL_HOST);

            return is_string($refererHost) && strtolower($refererHost) === $host;
        }

        return false;
    }

    private function hostMatchesOrigin(string $host, string $origin): bool
    {
        $originHost = parse_url($origin, PHP_URL_HOST);

        return is_string($originHost) && strtolower($originHost) === $host;
    }

    private function isHttpsRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));

        return $forwardedProto === 'https';
    }

    private function requestHost(Request $request): string
    {
        $host = trim((string) ($request->header('Host') ?? ''));
        if ($host !== '') {
            return explode(':', $host)[0];
        }

        $serverHost = trim((string) ($request->getServerParams()['HTTP_HOST'] ?? ''));

        return explode(':', $serverHost)[0];
    }
}
