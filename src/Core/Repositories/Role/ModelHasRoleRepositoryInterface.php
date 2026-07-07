<?php

declare(strict_types=1);

namespace App\Core\Repositories\Role;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ModelHasRoleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Assign a role to a model
     */
    public function assignRole(int $modelId, string $modelType, int $roleId): bool;

    /**
     * Get all roles for a specific model
     */
    public function getRolesForModel(int $modelId, string $modelType): array;

    /**
     * Remove all roles for a specific model
     */
    public function removeAllRoles(int $modelId, string $modelType): bool;
    public function findByModelHasRole(array $data): ?bool;

    /**
     * Upsert role for a model (insert or update)
     */
    public function upsertRole(array $userRoles, int $modelId = null): bool;
} 