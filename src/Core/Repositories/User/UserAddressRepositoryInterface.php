<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\User\UserAddress;

interface UserAddressRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all user addresses with pagination
     *
     * @param int $siteId
     * @param int $userId
     * @param int $start
     * @param int $limit
     * @return array{list: array<UserAddress>, total: int}
     */
    public function getAll(int $siteId, int $userId, int $start, int $limit): array;

    /**
     * Get a single user address by ID
     *
     * @param int $userAddressId
     * @param int $userId
     * @return UserAddress|null
     */
    public function get(int $userAddressId, int $userId): ?UserAddress;

    
} 