<?php

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\ReturnStatus;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class ReturnStatusRepository extends BaseRepository implements ReturnStatusRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'return_status', ReturnStatus::class);
    }

    /**
     * Get all return statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['return_status.*']);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

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
     * Get a single return status by ID
     * 
     * @param int $returnStatusId
     * @return array|null
     */
    public function get(int $returnStatusId): ?array
    {
        $result = $this->model->find($returnStatusId);
        return $result ? $result->findAll() : null;
    }

    
} 