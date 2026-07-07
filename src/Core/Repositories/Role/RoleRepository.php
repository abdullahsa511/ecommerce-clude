<?php

namespace App\Core\Repositories\Role;

use App\Core\Models\Admin\Role;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\RoleDataValidation;
use PDO;
use League\Csv\Reader;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'role', Role::class);
    }

    /**
     * Get all roles with optional pagination
     * 
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['role.*']);

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get a single role by ID
     * 
     * @param int $roleId
     * @return array|null
     */
    public function get(int $roleId): ?array
    {
        $result = $this->model->find($roleId);
        return $result ? $result->findAll() : null;
    }

}
