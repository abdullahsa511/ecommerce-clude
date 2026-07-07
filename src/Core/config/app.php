<?php

declare(strict_types=1);

use function App\Core\System\utils\env;

/**
 * Application Configuration
 * 
 * This file contains configuration options for the Application.
 * You can customize these settings based on your environment and requirements.
 *
 * Uses {@see env()} so values match the same .env / $_ENV loading as the HTTP Kernel.
 * Entrypoints (e.g. public/index.php) must load `functions.php` before the Kernel loads config.
 */

return [
    'cache' => [
        'driver' => 'Redis',
        'host' => (string) env('REDIS_HOST', 'mvc.redis'),
        'port' => (int) env('REDIS_PORT', 6379),
        'password' => env('REDIS_PASSWORD', null),
        'prefix' => (string) env('REDIS_PREFIX', 'krost.'),
        'expire' => 3600,
        'timeout' => (float) env('REDIS_TIMEOUT', 1.5),
        'read_timeout' => (float) env('REDIS_READ_TIMEOUT', 1.5),
        'retry_attempts' => (int) env('REDIS_RETRY_ATTEMPTS', 0),
        'persistent' => filter_var(env('REDIS_PERSISTENT', false), FILTER_VALIDATE_BOOL),
        'circuit_breaker_seconds' => (int) env('REDIS_CIRCUIT_BREAKER_SECONDS', 30),
    ],
    /**
     * Server-side login session (Redis) for web + cookie-authenticated API flows.
     * OAuth access tokens may still expire sooner; refresh_token in session is used when possible.
     */
    'auth_session' => [
        'ttl_seconds' => (int) env('AUTH_SESSION_TTL', 86400),
        'cookie_name' => (string) env('AUTH_SESSION_COOKIE', 'krost_auth_sid'),
        'redis_namespace' => (string) env('AUTH_SESSION_REDIS_NS', 'auth_sess'),
        /** Shared across subdomains (e.g. .krost.com.au) for admin_access_token on admin + API hosts. */
        'cookie_domain' => (string) env('AUTH_COOKIE_DOMAIN', ''),
    ],
    /**
     * CSRF tokens: stored in Redis (not PHP session). Browser binding via HttpOnly cookie.
     */
    'csrf' => [
        'ttl_seconds' => (int) env('CSRF_TOKEN_TTL', 3600),
        'client_cookie_name' => (string) env('CSRF_CLIENT_COOKIE', 'krost_csrf_cid'),
        'redis_namespace' => (string) env('CSRF_REDIS_NS', 'csrf'),
    ],
    /**
     * Google reCAPTCHA v3 (score-based). Leave keys empty to disable verification in local dev.
     */
    'recaptcha' => [
        'site_key' => (string) env('RECAPTCHA_SITE_KEY', ''),
        'secret_key' => (string) env('RECAPTCHA_SECRET_KEY', ''),
        'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
        'action_contact' => (string) env('RECAPTCHA_ACTION', 'contact_submit'),
        'action_service' => (string) env('RECAPTCHA_ACTION_SERVICE', 'service_request'),
        'action_project' => (string) env('RECAPTCHA_ACTION_PROJECT', 'project_submission'),
        'action_booking' => (string) env('RECAPTCHA_ACTION_BOOKING', 'showroom_booking'),
        'action_catalogue' => (string) env('RECAPTCHA_ACTION_CATALOGUE', 'catalogue_request'),
    ],
]; 