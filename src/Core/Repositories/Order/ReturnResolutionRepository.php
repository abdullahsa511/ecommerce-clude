<?php

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\ReturnResolution;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class ReturnResolutionRepository extends BaseRepository implements ReturnResolutionRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'return_resolution', ReturnResolution::class);
    }

    /**
     * Get all return resolutions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['return_resolution.*']);

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
     * Get a single return resolution by ID
     * 
     * @param int $returnResolutionId
     * @return array|null
     */
    public function get(int $returnResolutionId): ?array
    {
        $result = $this->model->find($returnResolutionId);
        return $result ? $result->findAll() : null;
    }

} 