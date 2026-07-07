<?php

declare(strict_types=1);

namespace App\Core\Http\Concerns;

use App\Core\OAuth2\AdminTokenLifetime;

/**
 * HttpOnly admin auth cookies for API exchange/login responses.
 */
trait SetsAdminAuthCookies
{
    /**
     * @param array<string, mixed> $payload Auth payload from {@see \App\Core\Services\AuthService::login()}
     */
    protected function applyAdminAuthCookiesFromLoginPayload(array $payload, int $sessionTtlSeconds = AdminTokenLifetime::SESSION_TTL_SECONDS): void
    {
        $auth = $payload['auth'] ?? null;
        if (!is_array($auth)) {
            return;
        }

        $accessToken = (string) ($auth['access_token'] ?? '');
        $refreshToken = (string) ($auth['refresh_token'] ?? '');
        $tokenType = (string) ($auth['token_type'] ?? 'Bearer');
        $expiresIn = (int) ($auth['expires_in'] ?? 0);
        $cookieMaxAge = AdminTokenLifetime::cookieMaxAgeSeconds($expiresIn, $sessionTtlSeconds);

        if ($accessToken !== '') {
            $this->setAdminAuthCookie('admin_access_token', $accessToken, $cookieMaxAge);
        }

        if ($refreshToken !== '') {
            $this->setAdminAuthCookie(
                'admin_refresh_token',
                $refreshToken,
                AdminTokenLifetime::REFRESH_COOKIE_TTL_SECONDS
            );
        } else {
            $this->expireAdminAuthCookie('admin_refresh_token');
        }

        $this->setAdminAuthCookie('admin_token_type', $tokenType, $cookieMaxAge, false);
        $this->setAdminAuthCookie('auth_present', '1', $cookieMaxAge, false);
    }

    protected function setAdminAuthCookie(string $name, string $value, int $maxAgeSeconds, bool $httpOnly = true): void
    {
        if (headers_sent()) {
            return;
        }

        $isSecure = $this->isAdminAuthHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        $options = [
            'expires' => time() + max(60, $maxAgeSeconds),
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ];

        $domain = $this->adminAuthCookieDomain();
        if ($domain !== '') {
            $options['domain'] = $domain;
        }

        setcookie($name, $value, $options);
    }

    protected function expireAdminAuthCookie(string $name): void
    {
        if (headers_sent()) {
            return;
        }

        $isSecure = $this->isAdminAuthHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        $options = [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ];

        $domain = $this->adminAuthCookieDomain();
        if ($domain !== '') {
            $options['domain'] = $domain;
        }

        setcookie($name, '', $options);
    }

    protected function adminAuthCookieDomain(): string
    {
        $domain = trim((string) ($_ENV['AUTH_COOKIE_DOMAIN'] ?? ''));

        return $domain;
    }

    protected function isAdminAuthHttpsRequest(): bool
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
