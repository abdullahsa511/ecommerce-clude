<?php

declare(strict_types=1);

namespace App\Core\Services\Instagram;

use RuntimeException;

use function App\Core\System\utils\env;

class InstagramTokenService
{
    private InstagramGraphClient $client;

    public function __construct(?InstagramGraphClient $client = null)
    {
        $this->client = $client ?? new InstagramGraphClient();
    }

    /**
     * Exchange a short-lived user token for a long-lived user token (~60 days).
     *
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function exchangeForLongLivedToken(string $shortLivedToken): array
    {
        $appId = $this->appId();
        $appSecret = $this->appSecret();

        if ($appId === '' || $appSecret === '') {
            throw new RuntimeException('INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET are required for token exchange.');
        }

        $response = $this->client->get('/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => trim($shortLivedToken),
        ]);

        if (empty($response['access_token'])) {
            throw new RuntimeException('Long-lived token exchange did not return an access token.');
        }

        return [
            'access_token' => (string) $response['access_token'],
            'token_type' => (string) ($response['token_type'] ?? 'bearer'),
            'expires_in' => (int) ($response['expires_in'] ?? 0),
        ];
    }

    /**
     * @return array<int, array{
     *   page_id: string,
     *   page_name: string,
     *   page_access_token: string,
     *   instagram_business_account_id: string|null
     * }>
     */
    public function getPageAccounts(string $userAccessToken): array
    {
        $response = $this->client->get('/me/accounts', [
            'fields' => 'id,name,access_token,instagram_business_account',
        ], trim($userAccessToken));

        $pages = [];

        foreach ($response['data'] ?? [] as $page) {
            if (!is_array($page)) {
                continue;
            }

            $instagramAccount = $page['instagram_business_account'] ?? null;
            $instagramBusinessAccountId = is_array($instagramAccount)
                ? (string) ($instagramAccount['id'] ?? '')
                : '';

            $pages[] = [
                'page_id' => (string) ($page['id'] ?? ''),
                'page_name' => (string) ($page['name'] ?? ''),
                'page_access_token' => (string) ($page['access_token'] ?? ''),
                'instagram_business_account_id' => $instagramBusinessAccountId !== '' ? $instagramBusinessAccountId : null,
            ];
        }

        return $pages;
    }

    /**
     * Production flow:
     * 1. Short-lived user token -> long-lived user token
     * 2. /me/accounts -> page access token + instagram business account id
     *
     * @return array{
     *   page_id: string,
     *   page_name: string,
     *   instagram_business_account_id: string,
     *   access_token: string,
     *   long_lived_user_token: string,
     *   long_lived_user_token_expires_in: int,
     *   env: array<string, string>,
     *   token_debug: array<string, mixed>
     * }
     */
    public function resolvePageCredentials(string $shortLivedToken, ?string $pageId = null): array
    {
        $longLived = $this->exchangeForLongLivedToken($shortLivedToken);
        $pages = $this->getPageAccounts($longLived['access_token']);

        if ($pages === []) {
            throw new RuntimeException('No Facebook Pages were returned for this token.');
        }

        $selectedPage = $this->selectPage($pages, $pageId);

        if ($selectedPage['page_access_token'] === '') {
            throw new RuntimeException('Selected page does not have an access token.');
        }

        if ($selectedPage['instagram_business_account_id'] === null) {
            throw new RuntimeException(
                'Selected page is not linked to an Instagram Business account. Link Instagram to the Facebook Page first.'
            );
        }

        $tokenDebug = $this->debugToken($selectedPage['page_access_token']);

        return [
            'page_id' => $selectedPage['page_id'],
            'page_name' => $selectedPage['page_name'],
            'instagram_business_account_id' => $selectedPage['instagram_business_account_id'],
            'access_token' => $selectedPage['page_access_token'],
            'long_lived_user_token' => $longLived['access_token'],
            'long_lived_user_token_expires_in' => $longLived['expires_in'],
            'env' => [
                'INSTAGRAM_ACCESS_TOKEN' => $selectedPage['page_access_token'],
                'INSTAGRAM_BUSINESS_ACCOUNT_ID' => $selectedPage['instagram_business_account_id'],
                'INSTAGRAM_PAGE_ID' => $selectedPage['page_id'],
            ],
            'token_debug' => $tokenDebug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function debugToken(string $inputToken): array
    {
        $appToken = $this->appAccessToken();

        if ($appToken === '') {
            throw new RuntimeException('INSTAGRAM_APP_ID and INSTAGRAM_APP_SECRET are required to debug tokens.');
        }

        $response = $this->client->get('/debug_token', [
            'input_token' => trim($inputToken),
        ], $appToken);

        return is_array($response['data'] ?? null) ? $response['data'] : [];
    }

    private function selectPage(array $pages, ?string $pageId): array
    {
        if ($pageId !== null && $pageId !== '') {
            foreach ($pages as $page) {
                if ($page['page_id'] === $pageId) {
                    return $page;
                }
            }

            throw new RuntimeException('The requested page_id was not found in /me/accounts.');
        }

        foreach ($pages as $page) {
            if ($page['instagram_business_account_id'] !== null) {
                return $page;
            }
        }

        return $pages[0];
    }

    private function appId(): string
    {
        return trim((string) (env('INSTAGRAM_APP_ID') ?: ''));
    }

    private function appSecret(): string
    {
        return trim((string) (env('INSTAGRAM_APP_SECRET') ?: ''));
    }

    private function appAccessToken(): string
    {
        $appId = $this->appId();
        $appSecret = $this->appSecret();

        if ($appId === '' || $appSecret === '') {
            return '';
        }

        return $appId . '|' . $appSecret;
    }
}
