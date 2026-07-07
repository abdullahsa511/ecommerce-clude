<?php

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\ReturnReason;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class ReturnReasonRepository extends BaseRepository implements ReturnReasonRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'return_reason', ReturnReason::class);
    }

    /**
     * Get all return reasons with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['return_reason.*']);

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
     * Get a single return reason by ID
     * 
     * @param int $returnReasonId
     * @return array|null
     */
    public function get(int $returnReasonId): ?array
    {
        $result = $this->model->find($returnReasonId);
        return $result ? $result->findAll() : null;
    }

    
} 