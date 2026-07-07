<?php

namespace App\Core\Repositories\User;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface UserFailedLoginRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all failed login entries with optional filtering and pagination
     * 
     * @param int|null $start
     * @param int|null $limit
     * @param int|null $userId
     * @param int|null $count
     * @param string|null $updatedAt
     * @return array
     */
    public function getAll(
        ?int $start = null,
        ?int $limit = null,
        ?int $userId = null,
        ?int $count = null,
        ?string $updatedAt = null
    ): array;

    /**
     * Get user failed login information with various filters
     * 
     * @param int|null $userId
     * @param string|null $updatedAt
     * @param int|null $count
     * @param string|null $username
     * @param string|null $email
     * @param int|null $status
     * @param int|null $roleId
     * @return array|null
     */
    public function get(
        ?int $userId = null,
        ?string $updatedAt = null,
        ?int $count = null,
        ?string $username = null,
        ?string $email = null,
        ?int $status = null,
        ?int $roleId = null
    ): ?array;

    /**
     * Log a failed login attempt
     * 
     * @param int|null $userId
     * @param string|null $username
     * @param string $updatedAt
     * @param string $lastIp
     * @return int
     */
    public function logFailed(
        ?int $userId,
        ?string $username,
        string $updatedAt,
        string $lastIp
    ): int;

} 