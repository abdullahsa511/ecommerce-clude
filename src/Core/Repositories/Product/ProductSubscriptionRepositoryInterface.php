<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductSubscriptionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product subscriptions
     * @param int $languageId
     * @param int|null $productId
     * @param int|null $subscriptionPlanId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(
        int $languageId,
        ?int $productId = null,
        ?int $subscriptionPlanId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get product subscription by ID
     * @param int $productSubscriptionId
     * @return array|null
     */
    public function get(int $productSubscriptionId): ?array;

    
} 