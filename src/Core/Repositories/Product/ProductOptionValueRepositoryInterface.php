<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductOptionValueRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product option values
     * @param int $languageId
     * @param int|null $optionId
     * @param int|null $productId
     * @param array|null $productOptionValueIds
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(
        int $languageId,
        ?int $optionId = null,
        ?int $productId = null,
        ?array $productOptionValueIds = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get product option value by ID
     * @param int $productOptionValueId
     * @param int $languageId
     * @return array|null
     */
    public function get(int $productOptionValueId, int $languageId): ?array;

    
} 