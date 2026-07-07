<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SubscriptionPlanContentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all subscriptions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int $languageId = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single subscription by ID
     * 
     * @param int $subscriptionId
     * @return array|null
     */
    public function get(int $subscriptionId): ?array;

    
} 