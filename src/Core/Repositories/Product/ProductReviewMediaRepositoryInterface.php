<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductReviewMediaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product review media
     * @param int $languageId
     * @param int|null $siteId
     * @param int|null $productId
     * @param int|null $productReviewId
     * @param int|null $status
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(
        int $languageId,
        ?int $siteId = null,
        ?int $productId = null,
        ?int $productReviewId = null,
        ?int $status = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get product review media by ID
     * @param int $productReviewMediaId
     * @return array|null
     */
    public function get(int $productReviewMediaId): ?array;

    
} 