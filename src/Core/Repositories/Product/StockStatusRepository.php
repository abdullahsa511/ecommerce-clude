<?php

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\StockStatus;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class StockStatusRepository extends BaseRepository implements StockStatusRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'stock_status', StockStatus::class);
    }

    /**
     * Get all stock statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['stock_status.*']);

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
     * Get a single stock status by ID
     * 
     * @param int $stockStatusId
     * @return array|null
     */
    public function get(int $stockStatusId): ?array
    {
        $result = $this->model->find($stockStatusId);
        return $result ? $result->findAll() : null;
    }

    
} 