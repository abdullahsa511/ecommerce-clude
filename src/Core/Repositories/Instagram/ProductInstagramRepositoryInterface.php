<?php

declare(strict_types=1);

namespace App\Core\Repositories\Instagram;

use App\Core\Models\Product\ProductInstagram;
use App\Core\Models\Product\ProductInstagramData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductInstagramRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return array{title: string, items: array<int, array<string, mixed>>}
     */
    public function getPostsForProduct(string $slug): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getByProductId(int $productId): array;

    public function createLink(ProductInstagramData $data): ProductInstagram;

    public function updateLink(ProductInstagramData $data): ?ProductInstagram;

    public function deleteLink(int $productInstagramId): bool;

    public function findLinkForProduct(int $productId, int $productInstagramId): ?ProductInstagram;

    /**
     * Replace all Instagram links for a product with the provided list.
     * Creates/updates links in the payload and deletes any existing links not included.
     *
     * @param array<int, array<string, mixed>> $links
     * @return array{items: array<int, array<string, mixed>>, deleted_ids: array<int, int>}
     */
    public function syncProductLinks(int $productId, array $links, string $defaultProductUrl = ''): array;

    /**
     * @param array<int, int|string|array<string, mixed>>|array{links?: array, product_instagram_ids?: array<int, int>} $items
     * @return array<int, array<string, mixed>>
     */
    public function reorderProductLinks(int $productId, array $items): array;
}
