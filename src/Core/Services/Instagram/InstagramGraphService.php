<?php

declare(strict_types=1);

namespace App\Core\Services\Instagram;

use RuntimeException;

use function App\Core\System\utils\env;

class InstagramGraphService
{
    private const MEDIA_FIELDS = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,children{media_url,thumbnail_url,media_type}';

    private InstagramGraphClient $client;

    private ?InstagramTokenService $tokenService;

    public function __construct(?InstagramGraphClient $client = null, ?InstagramTokenService $tokenService = null)
    {
        $this->client = $client ?? new InstagramGraphClient();
        $this->tokenService = $tokenService;
    }

    public function isConfigured(): bool
    {
        if ($this->useMock()) {
            return true;
        }

        return $this->accessToken() !== '' && $this->businessAccountId() !== '';
    }

    public function getStatus(): array
    {
        $status = [
            'configured' => $this->isConfigured(),
            'mock' => $this->useMock(),
            'api_version' => $this->client->apiVersion(),
            'business_account_id' => $this->businessAccountId() !== '' ? $this->businessAccountId() : null,
            'page_id' => $this->pageId() !== '' ? $this->pageId() : null,
            'token' => null,
        ];

        if (!$this->useMock() && $this->accessToken() !== '' && $this->tokenService() !== null) {
            try {
                $debug = $this->tokenService()->debugToken($this->accessToken());
                $status['token'] = [
                    'is_valid' => (bool) ($debug['is_valid'] ?? false),
                    'type' => $debug['type'] ?? null,
                    'expires_at' => isset($debug['expires_at']) ? (int) $debug['expires_at'] : null,
                    'data_access_expires_at' => isset($debug['data_access_expires_at'])
                        ? (int) $debug['data_access_expires_at']
                        : null,
                ];
            } catch (\Throwable) {
                $status['token'] = ['is_valid' => false];
            }
        }

        return $status;
    }

    private function tokenService(): ?InstagramTokenService
    {
        return $this->tokenService;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function searchHashtags(string $query): array
    {
        $tag = $this->normalizeHashtag($query);
        if ($tag === '') {
            return [];
        }

        if ($this->useMock()) {
            return [[
                'id' => 'mock_hashtag_' . md5($tag),
                'name' => $tag,
            ]];
        }

        $response = $this->request('/ig_hashtag_search', [
            'user_id' => $this->businessAccountId(),
            'q' => $tag,
        ]);

        return array_map(function (array $item) {
            return [
                'id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['name'] ?? ''),
            ];
        }, $response['data'] ?? []);
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, paging: array<string, mixed>}
     */
    public function getHashtagMedia(string $hashtagId, string $feed = 'top_media', ?string $after = null, ?string $hashtag = null): array
    {
        $feed = in_array($feed, ['top_media', 'recent_media'], true) ? $feed : 'top_media';

        if ($this->useMock()) {
            return [
                'items' => $this->mockMediaItems($hashtag ?? 'krostfurniture'),
                'paging' => [],
            ];
        }

        $params = [
            'user_id' => $this->businessAccountId(),
            'fields' => self::MEDIA_FIELDS,
            'limit' => 24,
        ];

        if ($after) {
            $params['after'] = $after;
        }

        $response = $this->request('/' . rawurlencode($hashtagId) . '/' . $feed, $params);

        $items = array_map(
            fn(array $item) => $this->normalizeMediaItem($item, $hashtag),
            $response['data'] ?? []
        );

        return [
            'items' => $items,
            'paging' => $response['paging'] ?? [],
        ];
    }

    /**
     * @return array{items: array<int, array<string, mixed>>, paging: array<string, mixed>}
     */
    public function getAccountMedia(?string $after = null, int $limit = 24): array
    {
        if ($this->useMock()) {
            return [
                'items' => $this->mockMediaItems('account'),
                'paging' => [],
            ];
        }

        $params = [
            'fields' => self::MEDIA_FIELDS,
            'limit' => max(1, min($limit, 50)),
        ];

        if ($after) {
            $params['after'] = $after;
        }

        $response = $this->request('/' . rawurlencode($this->businessAccountId()) . '/media', $params);

        return [
            'items' => array_map(
                fn(array $item) => $this->normalizeMediaItem($item),
                $response['data'] ?? []
            ),
            'paging' => $response['paging'] ?? [],
        ];
    }

    public function normalizeMediaItem(array $item, ?string $hashtag = null): array
    {
        $permalink = (string) ($item['permalink'] ?? '');
        $thumbnail = (string) ($item['thumbnail_url'] ?? '');
        $mediaUrl = (string) ($item['media_url'] ?? '');

        if ($thumbnail === '' && isset($item['children']['data'][0])) {
            $child = $item['children']['data'][0];
            $thumbnail = (string) ($child['thumbnail_url'] ?? $child['media_url'] ?? '');
        }

        if ($thumbnail === '' && $mediaUrl !== '') {
            $thumbnail = $mediaUrl;
        }

        return [
            'instagram_media_id' => (string) ($item['id'] ?? ''),
            'instagram_url' => $permalink,
            'thumbnail_url' => $thumbnail,
            'caption' => (string) ($item['caption'] ?? ''),
            'shortcode' => $this->extractShortcode($permalink),
            'media_type' => (string) ($item['media_type'] ?? ''),
            'hashtag' => $hashtag ? $this->normalizeHashtag($hashtag) : null,
            'timestamp' => (string) ($item['timestamp'] ?? ''),
        ];
    }

    private function request(string $path, array $query = []): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Instagram Graph API is not configured.');
        }

        if ($this->useMock()) {
            return ['data' => []];
        }

        return $this->client->get($path, $query, $this->accessToken());
    }

    private function useMock(): bool
    {
        return filter_var(env('INSTAGRAM_MOCK') ?: 'false', FILTER_VALIDATE_BOOL);
    }

    private function accessToken(): string
    {
        return trim((string) (env('INSTAGRAM_ACCESS_TOKEN') ?: ''));
    }

    private function businessAccountId(): string
    {
        return trim((string) (env('INSTAGRAM_BUSINESS_ACCOUNT_ID') ?: ''));
    }

    private function pageId(): string
    {
        return trim((string) (env('INSTAGRAM_PAGE_ID') ?: ''));
    }

    private function normalizeHashtag(string $value): string
    {
        return ltrim(trim($value), '#');
    }

    private function extractShortcode(string $url): ?string
    {
        if (preg_match('#instagram\.com/(?:p|reel|tv)/([A-Za-z0-9_-]+)#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mockMediaItems(string $hashtag): array
    {
        $tag = $this->normalizeHashtag($hashtag);

        return [
            [
                'instagram_media_id' => 'mock_media_1',
                'instagram_url' => 'https://www.instagram.com/p/mockpost1/',
                'thumbnail_url' => '/img/product-detail/insta-1.png',
                'caption' => 'Mock Instagram post for #' . $tag,
                'shortcode' => 'mockpost1',
                'media_type' => 'IMAGE',
                'hashtag' => $tag,
                'timestamp' => gmdate('c'),
            ],
            [
                'instagram_media_id' => 'mock_media_2',
                'instagram_url' => 'https://www.instagram.com/p/mockpost2/',
                'thumbnail_url' => '/img/product-detail/insta-2.png',
                'caption' => 'Another mock post for #' . $tag,
                'shortcode' => 'mockpost2',
                'media_type' => 'IMAGE',
                'hashtag' => $tag,
                'timestamp' => gmdate('c'),
            ],
        ];
    }
}
