<?php

namespace App\Core\Repositories\User;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface UserWishlistRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all products in user's wishlist
     * 
     * @param int $userId
     * @param int $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int $userId, int $languageId, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a specific product from user's wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @param int $languageId
     * @return array|null
     */
    public function get(int $userId, int $productId, int $languageId): ?array;

    /**
     * Add a product to user's wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return int
     */
    public function add(int $userId, int $productId): int;

    /**
     * Delete products from user's wishlist
     * 
     * @param int $userId
     * @param array|null $productIds
     * @return bool
     */
    public function delete(int $userId, ?array $productIds = null): bool;
}