<?php

declare(strict_types=1);

namespace App\Core\Repositories\Role;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Role\ModelHasRole;
use PDO;

class ModelHasRoleRepository extends BaseRepository implements ModelHasRoleRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'model_has_role', ModelHasRole::class);
    }

    /**
     * Assign a role to a model
     */
    public function assignRole(int $modelId, string $modelType, int $roleId): bool
    {
        try {
            $data = [
                'model_id' => $modelId,
                'model_type' => $modelType,
                'role_id' => $roleId
            ];

            $this->model->create($data);
            return true;
        } catch (\Exception $e) {
            error_log('Error assigning role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all roles for a specific model
     */
    public function getRolesForModel(int $modelId, string $modelType): array
    {
        try {
            return $this->model
                ->select(['role_id'])
                ->where('model_id', '=', $modelId)
                ->where('model_type', '=', $modelType)
                ->findAll();
        } catch (\Exception $e) {
            error_log('Error getting roles for model: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Remove all roles for a specific model
     */
    public function removeAllRoles(int $modelId, string $modelType): bool
    {
        try {
            $conditions = [
                'model_id' => $modelId,
                'model_type' => $modelType
            ];

            $affectedRows = $this->model->deleteWhere($conditions);
            return $affectedRows > 0;
        } catch (\Exception $e) {
            error_log('Error removing all roles: ' . $e->getMessage());
            return false;
        }
    }

    public function findByModelHasRole(array $data): ?bool
    {
        $result = $this->model->select(['role_id', 'model_id', 'model_type'])
            ->where('model_id', '=', $data['model_id'])
            ->where('model_type', '=', $data['model_type'])
            ->first();
        if(!$result){
            return false;
        }
        return true;
    }

    /**
     * Upsert role for a model (insert or update)
     */
    public function upsertRole(array $userRoles, int $modelId = null): bool
    {
        if($modelId){
           $this->removeAllRoles($modelId, 'user');
        }
        if(empty($userRoles)){
            return false;
        }
        $this->model->upsert($userRoles, ['role_id', 'model_id', 'model_type']);
        return true;
    }
}