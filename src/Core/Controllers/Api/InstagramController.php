<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Product\ProductInstagramData;
use App\Core\Repositories\Instagram\ProductInstagramRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Services\Instagram\InstagramGraphService;
use App\Core\Services\Instagram\InstagramTokenService;
use RuntimeException;

class InstagramController extends ApiController
{
    public function __construct(
        private InstagramGraphService $instagramGraphService,
        private InstagramTokenService $instagramTokenService,
        private ProductInstagramRepositoryInterface $productInstagramRepository,
        private ProductRepositoryInterface $productRepository
    ) {
        parent::__construct();
    }

    public function status(Request $request): Response
    {
        return $this->renderResponse($this->instagramGraphService->getStatus());
    }

    public function listPages(Request $request): Response
    {
        $shortLivedToken = trim((string) (
            $request->input('short_lived_token')
            ?? $request->query('short_lived_token', '')
        ));

        if ($shortLivedToken === '') {
            return $this->renderError(422, 'short_lived_token is required.');
        }

        try {
            $longLived = $this->instagramTokenService->exchangeForLongLivedToken($shortLivedToken);
            $pages = $this->instagramTokenService->getPageAccounts($longLived['access_token']);

            return $this->renderResponse([
                'long_lived_user_token_expires_in' => $longLived['expires_in'],
                'pages' => $pages,
            ]);
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function resolvePageToken(Request $request): Response
    {
        $shortLivedToken = trim((string) (
            $request->input('short_lived_token')
            ?? $request->query('short_lived_token', '')
        ));

        if ($shortLivedToken === '') {
            return $this->renderError(422, 'short_lived_token is required.');
        }

        $pageId = $request->input('page_id') ?? $request->query('page_id');
        $pageId = is_string($pageId) && $pageId !== '' ? $pageId : null;

        try {
            return $this->renderResponse(
                $this->instagramTokenService->resolvePageCredentials($shortLivedToken, $pageId)
            );
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function debugToken(Request $request): Response
    {
        $token = trim((string) (
            $request->input('access_token')
            ?? $request->query('access_token', '')
        ));

        if ($token === '') {
            $token = trim((string) (getenv('INSTAGRAM_ACCESS_TOKEN') ?: ''));
        }

        if ($token === '') {
            return $this->renderError(422, 'access_token is required.');
        }

        try {
            return $this->renderResponse([
                'token_debug' => $this->instagramTokenService->debugToken($token),
            ]);
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function searchHashtags(Request $request): Response
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return $this->renderError(422, 'Hashtag query is required.');
        }

        try {
            return $this->renderResponse([
                'items' => $this->instagramGraphService->searchHashtags($query),
            ]);
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function hashtagPosts(Request $request, string $hashtagId): Response
    {
        $feed = (string) $request->query('feed', 'top_media');
        $after = $request->query('after');
        $hashtag = $request->query('hashtag');

        try {
            return $this->renderResponse(
                $this->instagramGraphService->getHashtagMedia(
                    $hashtagId,
                    $feed,
                    is_string($after) ? $after : null,
                    is_string($hashtag) ? $hashtag : null
                )
            );
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function accountPosts(Request $request): Response
    {
        $after = $request->query('after');
        $limit = (int) $request->query('limit', 24);

        try {
            return $this->renderResponse(
                $this->instagramGraphService->getAccountMedia(
                    is_string($after) ? $after : null,
                    $limit
                )
            );
        } catch (RuntimeException $e) {
            return $this->renderError(502, $e->getMessage());
        }
    }

    public function productLinks(Request $request, int $product_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        return $this->renderResponse([
            'product_id' => $product_id,
            'items' => $this->productInstagramRepository->getByProductId($product_id),
        ]);
    }

    public function createProductLink(Request $request, int $product_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        $payload = $this->extractLinkPayload($request);
        if ($payload === null) {
            return $this->renderError(422, 'Instagram link data is required.');
        }

        $payload['product_id'] = $product_id;
        $payload['product_url'] = $payload['product_url'] ?? $this->resolveProductUrl($product_id);

        if (!empty($payload['instagram_media_id']) && empty($payload['thumbnail_url'])) {
            $payload = array_merge($payload, $this->fillMediaDefaults($payload));
        }

        try {
            $created = $this->productInstagramRepository->createLink(new ProductInstagramData($payload));
            return $this->renderResponse($this->formatLinkResponse($created));
        } catch (\Throwable $e) {
            return $this->renderError(500, 'Failed to create Instagram link: ' . $e->getMessage());
        }
    }

    public function updateProductLink(Request $request, int $product_id, int $link_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        $existing = $this->productInstagramRepository->findLinkForProduct($product_id, $link_id);
        if (!$existing) {
            return $this->renderError(404, 'Instagram link not found');
        }

        $payload = $this->extractLinkPayload($request);
        if ($payload === null) {
            return $this->renderError(422, 'Instagram link data is required.');
        }

        $payload['product_instagram_id'] = $link_id;
        $payload['product_id'] = $product_id;

        try {
            $updated = $this->productInstagramRepository->updateLink(new ProductInstagramData($payload));
            if (!$updated) {
                return $this->renderError(500, 'Failed to update Instagram link');
            }

            return $this->renderResponse($this->formatLinkResponse($updated));
        } catch (\Throwable $e) {
            return $this->renderError(500, 'Failed to update Instagram link: ' . $e->getMessage());
        }
    }

    public function deleteProductLink(Request $request, int $product_id, int $link_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        $existing = $this->productInstagramRepository->findLinkForProduct($product_id, $link_id);
        if (!$existing) {
            return $this->renderError(404, 'Instagram link not found');
        }

        if (!$this->productInstagramRepository->deleteLink($link_id)) {
            return $this->renderError(500, 'Failed to delete Instagram link');
        }

        return $this->renderResponse([
            'message' => 'Instagram link deleted successfully',
            'product_instagram_id' => $link_id,
        ]);
    }

    public function syncProductLinks(Request $request, int $product_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        $links = $request->input('links');
        if (!is_array($links)) {
            return $this->renderError(422, 'Links must be an array.');
        }

        try {
            $result = $this->productInstagramRepository->syncProductLinks(
                $product_id,
                $links,
                $this->resolveProductUrl($product_id)
            );

            return $this->renderResponse([
                'product_id' => $product_id,
                'items' => $result['items'],
                'deleted_ids' => $result['deleted_ids'],
                'deleted_count' => count($result['deleted_ids']),
            ]);
        } catch (\Throwable $e) {
            return $this->renderError(500, 'Failed to sync Instagram links: ' . $e->getMessage());
        }
    }

    public function reorderProductLinks(Request $request, int $product_id): Response
    {
        if (!$this->productExists($product_id)) {
            return $this->renderError(404, 'Product not found');
        }

        $payload = $request->input('links') ?? $request->input('product_instagram_ids') ?? $request->all();
        if (!is_array($payload) || $payload === []) {
            return $this->renderError(422, 'Reorder payload is required.');
        }

        try {
            $items = $this->productInstagramRepository->reorderProductLinks($product_id, $payload);

            return $this->renderResponse([
                'product_id' => $product_id,
                'items' => $items,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->renderError(422, $e->getMessage());
        } catch (\Throwable $e) {
            return $this->renderError(500, 'Failed to reorder Instagram links: ' . $e->getMessage());
        }
    }

    private function productExists(int $product_id): bool
    {
        return (bool) $this->productRepository->getProductById($product_id);
    }

    private function resolveProductUrl(int $product_id): string
    {
        $product = $this->productRepository->getProductById($product_id);
        if (!$product) {
            return '';
        }

        $slug = $product->data->slug ?? $product->data->product_code ?? '';
        if ($slug === '') {
            return '';
        }

        return '/products/' . ltrim((string) $slug, '/');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractLinkPayload(Request $request): ?array
    {
        $payload = $request->input('product_instagram')
            ?? $request->input('instagram_link')
            ?? $request->input('link');

        if (is_array($payload)) {
            return $payload;
        }

        $all = $request->all();
        if (isset($all['instagram_url']) || isset($all['instagram_media_id'])) {
            return $all;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function fillMediaDefaults(array $payload): array
    {
        if (!empty($payload['instagram_url'])) {
            return $this->instagramGraphService->normalizeMediaItem([
                'id' => $payload['instagram_media_id'] ?? '',
                'permalink' => $payload['instagram_url'],
                'thumbnail_url' => $payload['thumbnail_url'] ?? '',
                'caption' => $payload['caption'] ?? '',
                'media_type' => $payload['media_type'] ?? '',
            ], is_string($payload['hashtag'] ?? null) ? $payload['hashtag'] : null);
        }

        return [];
    }

    private function formatLinkResponse(object $model): array
    {
        $data = (array) $model->data;
        return [
            'product_instagram_id' => (int) ($data['product_instagram_id'] ?? 0),
            'product_id' => (int) ($data['product_id'] ?? 0),
            'product_url' => (string) ($data['product_url'] ?? ''),
            'instagram_url' => (string) ($data['instagram_url'] ?? ''),
            'instagram_media_id' => $data['instagram_media_id'] ?? null,
            'thumbnail_url' => $data['thumbnail_url'] ?? null,
            'caption' => $data['caption'] ?? null,
            'shortcode' => $data['shortcode'] ?? null,
            'hashtag' => $data['hashtag'] ?? null,
            'media_type' => $data['media_type'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
