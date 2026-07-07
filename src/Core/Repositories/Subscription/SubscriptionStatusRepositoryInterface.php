<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface SubscriptionStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all subscription statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single subscription status by ID
     * 
     * @param int $subscriptionStatusId
     * @return array|null
     */
    public function get(int $subscriptionStatusId): ?array;

    
} 