<?php

namespace App\Core\Repositories\Role;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all roles with optional pagination
     * 
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $start = null, ?int $limit = null): array;

    /**
     * Get a single role by ID
     * 
     * @param int $roleId
     * @return array|null
     */
    public function get(int $roleId): ?array;

    
} 