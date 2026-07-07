<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\System\Cache\Redis;

/**
 * CSRF protection using Redis (one-hour TTL) and an HttpOnly client cookie.
 * Does not use PHP sessions.
 */
class CsrfService
{
    private const TOKEN_LENGTH = 32;
    private const TOKEN_NAME = 'csrf_token';

    /** Redis value for an unused token (json-encoded by {@see Redis::set}). */
    private const TOKEN_PLACEHOLDER = 1;

    private Redis $redis;

    private int $tokenTtlSeconds;

    private string $clientCookieName;

    private string $redisNamespace;

    public function __construct(Redis $redis, array $csrfConfig = [])
    {
        $this->redis = $redis;
        $this->tokenTtlSeconds = max(60, (int) ($csrfConfig['ttl_seconds'] ?? 3600));
        $this->clientCookieName = (string) ($csrfConfig['client_cookie_name'] ?? 'krost_csrf_cid');
        $this->redisNamespace = (string) ($csrfConfig['redis_namespace'] ?? 'csrf');
    }

    /**
     * Generate a new CSRF token and persist it in Redis until TTL or successful validation.
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $this->redis->set($this->redisNamespace, $token, self::TOKEN_PLACEHOLDER, $this->tokenTtlSeconds);

        return $token;
    }

    /**
     * Validate a CSRF token (one-time use). Token must exist in Redis and not be expired.
     */
    public function validateToken(string $token): bool
    {
        $token = trim($token);
        if ($token === '') {
            return false;
        }

        $exists = $this->redis->get($this->redisNamespace, $token);
        if ($exists === null) {
            return false;
        }

        $this->redis->delete($this->redisNamespace, $token);

        $cid = $this->getClientIdFromCookie();
        if ($cid !== '') {
            $current = $this->redis->get($this->redisNamespace, $this->currentPointerKey($cid));
            if (is_string($current) && $current === $token) {
                $this->redis->delete($this->redisNamespace, $this->currentPointerKey($cid));
            }
        }

        return true;
    }

    /**
     * Return the current unused token for this browser, or create one.
     */
    public function getToken(): string
    {
        $cid = $this->ensureClientIdCookie();

        $current = $this->redis->get($this->redisNamespace, $this->currentPointerKey($cid));
        if (is_string($current) && $current !== '') {
            $meta = $this->redis->get($this->redisNamespace, $current);
            if ($meta !== null) {
                return $current;
            }
        }

        $token = $this->generateToken();
        $this->redis->set(
            $this->redisNamespace,
            $this->currentPointerKey($cid),
            $token,
            $this->tokenTtlSeconds
        );

        return $token;
    }

    public function getTokenName(): string
    {
        return self::TOKEN_NAME;
    }

    /**
     * Individual keys expire via Redis TTL; nothing to sweep here.
     */
    public function cleanupExpiredTokens(): void
    {
    }

    private function currentPointerKey(string $clientId): string
    {
        return 'cid:' . $clientId;
    }

    private function getClientIdFromCookie(): string
    {
        return trim((string) ($_COOKIE[$this->clientCookieName] ?? ''));
    }

    /**
     * Opaque id tying "current token" pointers in Redis to this browser.
     */
    private function ensureClientIdCookie(): string
    {
        $cid = $this->getClientIdFromCookie();
        if ($cid !== '') {
            $this->refreshClientIdCookie($cid);

            return $cid;
        }

        $cid = bin2hex(random_bytes(16));
        if (!headers_sent()) {
            $isSecure = $this->isHttpsRequest();
            $sameSite = $isSecure ? 'None' : 'Lax';
            setcookie($this->clientCookieName, $cid, [
                'expires' => time() + $this->tokenTtlSeconds,
                'path' => '/',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => $sameSite,
            ]);
        }
        $_COOKIE[$this->clientCookieName] = $cid;

        return $cid;
    }

    private function refreshClientIdCookie(string $cid): void
    {
        if (headers_sent()) {
            return;
        }
        $isSecure = $this->isHttpsRequest();
        $sameSite = $isSecure ? 'None' : 'Lax';
        setcookie($this->clientCookieName, $cid, [
            'expires' => time() + $this->tokenTtlSeconds,
            'path' => '/',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
        $_COOKIE[$this->clientCookieName] = $cid;
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
