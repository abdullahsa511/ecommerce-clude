<?php

declare(strict_types=1);

namespace App\Core\OAuth2;

use DateInterval;

/**
 * OAuth access-token and admin browser-session lifetimes for admin-scoped tokens.
 */
final class AdminTokenLifetime
{
    public const SESSION_TTL_SECONDS = 86400;

    /** Refresh-token cookie lifetime (matches internal_sso refresh TTL, 30 days). */
    public const REFRESH_COOKIE_TTL_SECONDS = 2592000;

    /**
     * @param array<int, mixed> $scopes Scope entities or scope identifier strings
     */
    public static function accessTokenInterval(array $scopes, DateInterval $default): DateInterval
    {
        if (self::scopesIncludeAdmin($scopes)) {
            return new DateInterval('PT24H');
        }

        return $default;
    }

    /**
     * @param array<int, mixed> $scopes
     */
    public static function scopesIncludeAdmin(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            $id = is_object($scope) && method_exists($scope, 'getIdentifier')
                ? (string) $scope->getIdentifier()
                : (is_string($scope) ? $scope : '');
            if ($id === 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * Cookie max-age should cover the full admin session even when expires_in is missing or short.
     */
    public static function cookieMaxAgeSeconds(int $tokenExpiresIn, int $sessionTtl = self::SESSION_TTL_SECONDS): int
    {
        $sessionTtl = max(60, $sessionTtl);
        if ($tokenExpiresIn > 0) {
            return max($sessionTtl, $tokenExpiresIn);
        }

        return $sessionTtl;
    }

    /**
     * @param list<string>|string|null $scopesJsonOrList
     */
    public static function scopesIncludeAdminFromStored(mixed $scopesJsonOrList): bool
    {
        if (is_string($scopesJsonOrList)) {
            $trimmed = trim($scopesJsonOrList);
            if ($trimmed === '') {
                return false;
            }
            if ($trimmed[0] === '[') {
                $decoded = json_decode($trimmed, true);

                return is_array($decoded) && self::scopesIncludeAdmin($decoded);
            }

            return self::scopesIncludeAdmin(array_map('trim', explode(',', $trimmed)));
        }

        return is_array($scopesJsonOrList) && self::scopesIncludeAdmin($scopesJsonOrList);
    }
}
