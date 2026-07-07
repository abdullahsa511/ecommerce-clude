<?php

declare(strict_types=1);

namespace App\Core\Repositories\Instagram;

use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductContent;
use App\Core\Models\Product\ProductInstagram;
use App\Core\Models\Product\ProductInstagramData;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class ProductInstagramRepository extends BaseRepository implements ProductInstagramRepositoryInterface
{
    private Product $product;
    private ProductContent $productContent;

    public function __construct(PDO $db, ProductInstagram $productInstagram, Product $product, ProductContent $productContent)
    {
        parent::__construct($db, 'product_instagram', ProductInstagram::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productContent = $productContent;
        $this->productContent->setDb($db);
    }

    public function getPostsForProduct(string $slug): array
    {
        $productContent = $this->productContent->where('slug', '=', $slug)->first();
        if (!$productContent) {
            $product = $this->product->where('product_code', '=', $slug)->first();
            if (!$product) {
                return ['title' => '', 'items' => []];
            }
            $productId = (int) $product->data->product_id;
            $title = (string) ($product->data->product_code ?? $slug);
        } else {
            $productId = (int) $productContent->data->product_id;
            $title = (string) ($productContent->data->title ?? $slug);
        }

        return [
            'title' => $title,
            'items' => $this->getByProductId($productId),
        ];
    }

    public function getByProductId(int $productId): array
    {
        $rows = $this->model
            ->where('product_id', '=', $productId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('product_instagram_id', 'ASC')
            ->findAll(false);

        return array_map(fn(array $row) => $this->formatPost($row), $rows);
    }

    public function createLink(ProductInstagramData $data): ProductInstagram
    {
        $payload = $this->preparePayload($data);
        $created = $this->model->create($payload);

        if (!$created) {
            throw new \RuntimeException('Failed to create product Instagram link.');
        }

        return $created;
    }

    public function updateLink(ProductInstagramData $data): ?ProductInstagram
    {
        if ($data->product_instagram_id === null) {
            return null;
        }

        $existing = $this->model->find($data->product_instagram_id);
        if (!$existing) {
            return null;
        }

        return $existing->update($this->preparePayload($data));
    }

    public function deleteLink(int $productInstagramId): bool
    {
        return $this->model->delete($productInstagramId);
    }

    public function findLinkForProduct(int $productId, int $productInstagramId): ?ProductInstagram
    {
        $row = $this->model
            ->where('product_instagram_id', '=', $productInstagramId)
            ->where('product_id', '=', $productId)
            ->first();

        return $row ?: null;
    }

    public function syncProductLinks(int $productId, array $links, string $defaultProductUrl = ''): array
    {
        $saved = [];

        foreach ($links as $index => $link) {
            if (!is_array($link)) {
                continue;
            }

            $data = new ProductInstagramData(array_merge($link, [
                'product_id' => $productId,
                'product_url' => $link['product_url'] ?? $defaultProductUrl,
                'sort_order' => $link['sort_order'] ?? ($index + 1),
            ]));

            if ($data->instagram_url === '') {
                continue;
            }

            if ($data->shortcode === null && $data->instagram_url !== '') {
                $data->shortcode = $this->extractShortcode($data->instagram_url);
            }

            if ($data->product_instagram_id) {
                $updated = $this->updateLink($data);
                if ($updated) {
                    $saved[] = $this->formatPost((array) $updated->data);
                }
                continue;
            }

            $existing = null;
            if ($data->instagram_media_id) {
                $this->model->clearQuery();
                $existing = $this->model
                    ->where('product_id', '=', $productId)
                    ->where('instagram_media_id', '=', $data->instagram_media_id)
                    ->first();
            }

            if ($existing) {
                $data->product_instagram_id = (int) $existing->data->product_instagram_id;
                $updated = $this->updateLink($data);
                if ($updated) {
                    $saved[] = $this->formatPost((array) $updated->data);
                }
                continue;
            }

            $created = $this->createLink($data);
            $saved[] = $this->formatPost((array) $created->data);
        }

        $keptIds = array_values(array_filter(array_map(
            static fn(array $item): int => (int) ($item['product_instagram_id'] ?? 0),
            $saved
        )));

        $deletedIds = $this->deleteLinksNotInList($productId, $keptIds);

        return [
            'items' => $saved,
            'deleted_ids' => $deletedIds,
        ];
    }

    public function reorderProductLinks(int $productId, array $items): array
    {
        $linkIds = $this->normalizeReorderItems($items);

        if ($linkIds === []) {
            throw new \InvalidArgumentException('At least one product_instagram_id is required to reorder.');
        }

        $reordered = [];

        foreach ($linkIds as $index => $linkId) {
            $existing = $this->findLinkForProduct($productId, $linkId);
            if (!$existing) {
                throw new \RuntimeException("Instagram link {$linkId} was not found for product {$productId}.");
            }

            $updated = $existing->update(['sort_order' => $index + 1]);
            if ($updated) {
                $reordered[] = $this->formatPost((array) $updated->data);
            }
        }

        return $reordered;
    }

    /**
     * @param array<int, int|string|array<string, mixed>>|array{links?: array, product_instagram_ids?: array<int, int>} $items
     * @return array<int, int>
     */
    private function normalizeReorderItems(array $items): array
    {
        if (isset($items['product_instagram_ids']) && is_array($items['product_instagram_ids'])) {
            return array_values(array_filter(array_map(
                static fn(mixed $id): int => (int) $id,
                $items['product_instagram_ids']
            )));
        }

        if (isset($items['links']) && is_array($items['links'])) {
            $items = $items['links'];
        }

        $linkIds = [];

        foreach (array_values($items) as $item) {
            if (is_int($item) || (is_string($item) && is_numeric($item))) {
                $linkIds[] = (int) $item;
                continue;
            }

            if (is_array($item) && isset($item['product_instagram_id'])) {
                $linkIds[] = (int) $item['product_instagram_id'];
            }
        }

        return array_values(array_filter($linkIds));
    }

    /**
     * @param array<int, int> $keptIds
     * @return array<int, int>
     */
    private function deleteLinksNotInList(int $productId, array $keptIds): array
    {
        $this->model->clearQuery();
        $existingRows = $this->model
            ->where('product_id', '=', $productId)
            ->findAll(false);

        $deletedIds = [];

        foreach ($existingRows as $row) {
            $id = (int) ($row['product_instagram_id'] ?? 0);
            if ($id === 0 || in_array($id, $keptIds, true)) {
                continue;
            }

            if ($this->deleteLink($id)) {
                $deletedIds[] = $id;
            }
        }

        return $deletedIds;
    }

    private function preparePayload(ProductInstagramData $data): array
    {
        $payload = $data->toArray();

        if (($payload['shortcode'] ?? null) === null && !empty($payload['instagram_url'])) {
            $payload['shortcode'] = $this->extractShortcode((string) $payload['instagram_url']);
        }

        return $payload;
    }

    private function formatPost(array $row): array
    {
        $instagramUrl = trim((string) ($row['instagram_url'] ?? ''));
        $thumbnailUrl = trim((string) ($row['thumbnail_url'] ?? ''));

        if ($thumbnailUrl === '' && $instagramUrl !== '') {
            $thumbnailUrl = $this->getInstagramThumbnail($instagramUrl);
        }

        return [
            'product_instagram_id' => (int) ($row['product_instagram_id'] ?? 0),
            'product_id' => (int) ($row['product_id'] ?? 0),
            'product_url' => trim((string) ($row['product_url'] ?? '')),
            'instagram_url' => $instagramUrl,
            'instagram_media_id' => $row['instagram_media_id'] ?? null,
            'thumbnail_url' => $thumbnailUrl,
            'thumbnail' => $thumbnailUrl,
            'caption' => $row['caption'] ?? null,
            'shortcode' => $row['shortcode'] ?? $this->extractShortcode($instagramUrl),
            'hashtag' => $row['hashtag'] ?? null,
            'media_type' => $row['media_type'] ?? null,
            'sort_order' => (int) ($row['sort_order'] ?? 0),
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    private function extractShortcode(string $url): ?string
    {
        if (preg_match('#instagram\.com/(?:p|reel|tv)/([A-Za-z0-9_-]+)#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getInstagramThumbnail(string $instagramUrl): string
    {
        if ($instagramUrl === '') {
            return '';
        }

        $oembedUrl = 'https://api.instagram.com/oembed?url=' . urlencode($instagramUrl);
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'user_agent' => 'Mozilla/5.0 (compatible; KrostEcommerce/1.0)',
            ],
        ]);

        try {
            $response = @file_get_contents($oembedUrl, false, $context);
            if ($response === false) {
                return '';
            }

            $data = json_decode($response, true);
            return is_array($data) ? (string) ($data['thumbnail_url'] ?? '') : '';
        } catch (\Throwable) {
            return '';
        }
    }
}
