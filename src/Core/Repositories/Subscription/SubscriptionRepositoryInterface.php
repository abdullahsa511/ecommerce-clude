<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Subscription\Subscription;

interface SubscriptionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all subscriptions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int | null $languageId = null, int $start = 0, int $limit = 10): array;

     /**
     * Find a subscription by their email address.
     *
     * @param string $email
     * @return Subscription|null
     */
    public function findByEmail(string $email): ?Subscription;


    /**
     * Get a single subscription by ID
     * 
     * @param int $subscriptionId
     * @return array|null
     */
    public function get(int $subscriptionId): ?array;

    public function createSubscription(array $data): array;

    
    public function subscribeEmail(string $email);

    public function getSubscribeRequests(): array;
} 