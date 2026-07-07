<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductContentRevisionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product content revisions with pagination
     *
     * @param int|null $productId Product ID
     * @param int|null $languageId Language ID
     * @param string|null $createdAt Created at timestamp
     * @param bool $includeContent Whether to include content in the result
     * @param int|null $start Start offset for pagination
     * @param int|null $limit Limit for pagination
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $productId = null,
        ?int $languageId = null,
        ?string $createdAt = null,
        bool $includeContent = false,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a single product content revision
     *
     * @param int $productId Product ID
     * @param int $languageId Language ID
     * @param string $createdAt Created at timestamp
     * @return array|null
     */
    public function get(int $productId, int $languageId, string $createdAt): ?array;

} 